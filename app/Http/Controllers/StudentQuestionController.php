<?php

namespace App\Http\Controllers;

use App\Models\Achievement;
use App\Models\ChallengeResult;
use App\Models\Question;
use App\Models\StudentAnswer;
use Illuminate\Http\Request;

class StudentQuestionController extends Controller
{
    public function show($challenge_id)
    {
        $user = auth()->user();

        $lastAttempt = ChallengeResult::where('user_id', $user->id)
            ->where('challenge_id', $challenge_id)
            ->max('attempt_number');

        $newAttemptNumber = $lastAttempt ? $lastAttempt + 1 : 1;

        ChallengeResult::create([
            'user_id' => $user->id,
            'challenge_id' => $challenge_id,
            'attempt_number' => $newAttemptNumber,
            'total_score' => 0,
            'total_exp' => 0,
            'correct_answers' => 0,
            'wrong_answers' => 0,
        ]);

        $questions = Question::where('challenge_id', $challenge_id)
            ->orderBy('id')
            ->pluck('id')
            ->toArray();

        session([
            'challenge_questions' => $questions,
            'current_question_index' => 0,
            'current_attempt' => $newAttemptNumber,
        ]);

        return redirect()->route('student.next.question', ['challenge_id' => $challenge_id]);
    }

    public function resumeQuestion($challenge_id)
    {
        return redirect()->route('student.next.question', ['challenge_id' => $challenge_id]);
    }

    public function checkAnswer(Request $request)
    {
        $request->validate([
            'question_id' => 'required|exists:questions,id',
            'selected_answer' => 'required',
        ]);

        $question = Question::with('answers')->findOrFail($request->question_id);
        $selectedAnswer = $request->selected_answer;

        if ($question->type === 'true_false') {
            $correctAnswer = $question->answers->firstWhere('is_correct', true);
            $isCorrect = $correctAnswer
                && strcasecmp(trim((string) $selectedAnswer), trim((string) $correctAnswer->answer)) === 0;

            $answerId = $question->answers
                ->firstWhere('answer', strtolower($selectedAnswer) === 'true' ? 'True' : 'False')
                ?->id;
        } else {
            $answer = $question->answers->firstWhere('id', (int) $selectedAnswer);
            $answerId = $answer?->id;
            $isCorrect = (bool) $answer?->is_correct;
        }

        $usedHelp = $this->resetExistingQuestionAttempt($question);

        $challengeResult = $this->recordSingleAnswer($question, $isCorrect, [
            'answer_id' => $answerId,
            'selected_answer' => (string) $selectedAnswer,
            'answer_text' => null,
            'used_help' => $usedHelp,
        ]);

        return response()->json($this->buildAnswerPayload($question, $challengeResult, $isCorrect));
    }

    public function checkMultiple(Request $request)
    {
        $request->validate([
            'question_id' => 'required|exists:questions,id',
            'selected_answers' => 'required|array|min:1',
        ]);

        $question = Question::with('answers')->findOrFail($request->question_id);
        $selectedAnswers = collect($request->selected_answers)->map(fn($id) => (int) $id)->sort()->values();
        $correctAnswers = $question->answers->where('is_correct', true)->pluck('id')->sort()->values();
        $isCorrect = $selectedAnswers->toArray() === $correctAnswers->toArray();

        $usedHelp = $this->resetExistingQuestionAttempt($question);
        $challengeResult = null;

        foreach ($selectedAnswers as $answerId) {
            $challengeResult = $this->recordSingleAnswer($question, $isCorrect, [
                'answer_id' => $answerId,
                'selected_answer' => (string) $answerId,
                'answer_text' => null,
                'used_help' => $usedHelp,
            ], $challengeResult === null);
        }

        return response()->json($this->buildAnswerPayload($question, $challengeResult, $isCorrect));
    }

    public function checkEssayAnswer(Request $request)
    {
        $request->validate([
            'question_id' => 'required|exists:questions,id',
            'answer' => 'required|string',
        ]);

        $user = auth()->user();
        $student = $user->student;
        $question = Question::with('answers')->findOrFail($request->question_id);
        $attemptNumber = session('current_attempt');
        $student->updateRank();

        $correctAnswer = $question->answers->firstWhere('is_correct', true);

        if (! $correctAnswer) {
            return response()->json([
                'error' => 'No correct answer found for this question.',
            ], 400);
        }

        $submittedAnswer = $this->normalizeText($request->answer);
        $correctAnswerText = $this->normalizeText($correctAnswer->answer);
        $isCorrect = $submittedAnswer === $correctAnswerText || levenshtein($submittedAnswer, $correctAnswerText) <= 2;
        $usedHelp = $this->resetExistingQuestionAttempt($question);

        StudentAnswer::create([
            'user_id' => $user->id,
            'question_id' => $question->id,
            'challenge_id' => $question->challenge_id,
            'attempt_number' => $attemptNumber,
            'selected_answer' => $request->answer,
            'answer_text' => $request->answer,
            'is_correct' => $isCorrect,
            'used_help' => $usedHelp,
            'help_requested_at' => $usedHelp ? now() : null,
        ]);

        $challengeResult = $this->updateChallengeResult($question, $isCorrect);

        return response()->json($this->buildAnswerPayload($question, $challengeResult, $isCorrect));
    }

    public function requestHelp(Request $request)
    {
        $request->validate([
            'question_id' => 'required|exists:questions,id',
        ]);

        $user = auth()->user();
        $question = Question::findOrFail($request->question_id);
        $attemptNumber = session('current_attempt');

        StudentAnswer::where('user_id', $user->id)
            ->where('question_id', $question->id)
            ->where('attempt_number', $attemptNumber)
            ->update([
                'used_help' => true,
                'help_requested_at' => now(),
            ]);

        session()->put("question_help_used.{$question->id}", true);

        return response()->json([
            'help_text' => $question->help_text ?: 'Belum ada bantuan khusus untuk soal ini.',
        ]);
    }

    public function nextQuestion($challenge_id)
    {
        $questions = session('challenge_questions', []);
        $index = session('current_question_index', 0);
        $attemptNumber = session('current_attempt');
        $user = auth()->user();
        $student = $user->student;

        if ($index >= count($questions)) {
            $student->load('ranks');

            $challengeResult = ChallengeResult::where('user_id', $user->id)
                ->where('challenge_id', $challenge_id)
                ->where('attempt_number', $attemptNumber)
                ->first();

            if ($challengeResult) {
                $challengeResult->ended_at = now();
                $challengeResult->save();

                $previousBestResult = ChallengeResult::where('user_id', $user->id)
                    ->where('challenge_id', $challenge_id)
                    ->where('attempt_number', '<', $attemptNumber)
                    ->orderByDesc('total_score')
                    ->orderByDesc('total_exp')
                    ->first();

                $previousHighestScore = $previousBestResult?->total_score ?? 0;
                $previousHighestExp = $previousBestResult?->total_exp ?? 0;

                if ($challengeResult->total_score > $previousHighestScore || $challengeResult->total_exp > $previousHighestExp) {
                    $scoreDelta = max(0, $challengeResult->total_score - $previousHighestScore);
                    $expDelta = max(0, $challengeResult->total_exp - $previousHighestExp);

                    $student->increment('weekly_score', $scoreDelta);
                    $student->increment('total_score', $scoreDelta);

                    if ($expDelta > 0) {
                        $student->increment('exp', $expDelta);
                    }
                }

                $rankUpdate = $student->updateRank();
                if ($rankUpdate['rank_changed']) {
                    $achievement = Achievement::where('code', 'rank_up')->first();
                    if ($achievement && ! $student->achievements()->where('achievement_id', $achievement->id)->exists()) {
                        $student->achievements()->attach($achievement->id, [
                            'unlocked_at' => now(),
                        ]);
                    }

                    return redirect()->route('student.rank.up', [
                        'challenge_id' => $challenge_id,
                        'attempt_number' => $attemptNumber,
                    ]);
                }
            }

            return redirect()->route('student.challenge.summary', [
                'challenge_id' => $challenge_id,
                'attempt_number' => $attemptNumber,
            ]);
        }

        $question = Question::with('answers')->findOrFail($questions[$index]);
        session(['current_question_index' => $index + 1]);

        $progress = (($index + 1) / max(count($questions), 1)) * 100;

        return view('student.question', compact('question', 'progress'));
    }

    public function challengeSummary($challenge_id, $attempt_number)
    {
        $totalQuestions = Question::where('challenge_id', $challenge_id)->count();

        $motivasiBenar = [
            'Mantap, kamu makin paham pola berpikirnya.',
            'Kerja bagus, ritme belajarmu sudah bagus.',
            'Bagus, pertahankan fokus seperti ini.',
            'Keren, logikamu mulai konsisten.',
        ];

        $motivasiSalah = [
            'Tidak apa-apa, lanjut review pembahasannya lalu coba lagi.',
            'Bagian yang sulit justru yang paling bagus untuk dipelajari.',
            'Tenang, lihat bantuan dan pembahasan supaya langkahnya makin jelas.',
            'Salah sekali bukan masalah, yang penting paham cara berpikirnya.',
        ];

        $user = auth()->user();
        $challengeResult = ChallengeResult::where('user_id', $user->id)
            ->where('challenge_id', $challenge_id)
            ->where('attempt_number', $attempt_number)
            ->firstOrFail();

        $isPerfect = $challengeResult->correct_answers == $totalQuestions;
        $user->student->updateRank();

        return view('student.challenge_summary', compact(
            'challengeResult',
            'motivasiBenar',
            'motivasiSalah',
            'isPerfect'
        ));
    }

    public function updateLives()
    {
        $student = auth()->user()->student;

        if ($student->lives > 0) {
            $student->decrement('lives');
        }

        return response()->json(['lives' => $student->lives]);
    }

    public function exitChallenge(Request $request)
    {
        $user = auth()->user();
        $challenge_id = $request->challenge_id;

        $latestAttempt = ChallengeResult::where('user_id', $user->id)
            ->where('challenge_id', $challenge_id)
            ->max('attempt_number');

        if (! $latestAttempt) {
            return response()->json(['success' => false, 'message' => 'Tidak ada attempt yang bisa dihapus.']);
        }

        $challengeResult = ChallengeResult::where('user_id', $user->id)
            ->where('challenge_id', $challenge_id)
            ->where('attempt_number', $latestAttempt)
            ->first();

        if ($challengeResult) {
            $challengeResult->delete();
        }

        StudentAnswer::where('user_id', $user->id)
            ->where('challenge_id', $challenge_id)
            ->where('attempt_number', $latestAttempt)
            ->delete();

        $todayAttemptCount = ChallengeResult::where('user_id', $user->id)
            ->where('challenge_id', $challenge_id)
            ->whereDate('created_at', now()->toDateString())
            ->count();

        if ($todayAttemptCount < 1) {
            $student = $user->student;
            $student->streak = max(0, $student->streak - 1);
            $student->save();
        }

        return response()->json(['success' => true, 'message' => 'Challenge exited and progress deleted.']);
    }

    public function checkLives()
    {
        $user = auth()->user();

        return response()->json([
            'lives' => $user->student->lives,
            'next_life_at' => $user->student->next_life_at,
        ]);
    }

    protected function recordSingleAnswer(Question $question, bool $isCorrect, array $payload, bool $updateResult = true): ?ChallengeResult
    {
        $user = auth()->user();
        $attemptNumber = session('current_attempt');

        StudentAnswer::create([
            'user_id' => $user->id,
            'question_id' => $question->id,
            'challenge_id' => $question->challenge_id,
            'attempt_number' => $attemptNumber,
            'answer_id' => $payload['answer_id'] ?? null,
            'selected_answer' => $payload['selected_answer'] ?? null,
            'answer_text' => $payload['answer_text'] ?? null,
            'is_correct' => $isCorrect,
            'used_help' => $payload['used_help'] ?? false,
            'help_requested_at' => ($payload['used_help'] ?? false) ? now() : null,
        ]);

        if (! $updateResult) {
            return ChallengeResult::where('user_id', $user->id)
                ->where('challenge_id', $question->challenge_id)
                ->where('attempt_number', $attemptNumber)
                ->firstOrFail();
        }

        return $this->updateChallengeResult($question, $isCorrect);
    }

    protected function updateChallengeResult(Question $question, bool $isCorrect): ChallengeResult
    {
        $user = auth()->user();
        $student = $user->student;
        $challengeResult = ChallengeResult::where('user_id', $user->id)
            ->where('challenge_id', $question->challenge_id)
            ->where('attempt_number', session('current_attempt'))
            ->firstOrFail();

        if ($isCorrect) {
            $challengeResult->correct_answers += 1;
            $challengeResult->total_score += $question->score;
            $challengeResult->total_exp += $question->exp;
        } else {
            $challengeResult->wrong_answers += 1;
            if ($student->lives > 0) {
                $student->decrement('lives', 1);
                $student->refresh();
            }
        }

        $challengeResult->save();

        if ($student->lives < 5 && $student->next_life_at === null) {
            $student->next_life_at = now()->addMinutes(60);
            $student->save();
        }

        return $challengeResult;
    }

    protected function buildAnswerPayload(Question $question, ChallengeResult $challengeResult, bool $isCorrect): array
    {
        $student = auth()->user()->student;

        return [
            'is_correct' => $isCorrect,
            'correct' => $isCorrect,
            'total_score' => $challengeResult->total_score,
            'total_exp' => $challengeResult->total_exp,
            'correct_answers' => $challengeResult->correct_answers,
            'wrong_answers' => $challengeResult->wrong_answers,
            'lives' => $student->lives,
            'has_help' => ! blank($question->help_text),
        ];
    }

    protected function normalizeText(string $text): string
    {
        return preg_replace('/[^a-z0-9]/', '', strtolower($text));
    }

    protected function resetExistingQuestionAttempt(Question $question): bool
    {
        $user = auth()->user();
        $student = $user->student;
        $attemptNumber = session('current_attempt');

        $existingAnswers = StudentAnswer::where('user_id', $user->id)
            ->where('question_id', $question->id)
            ->where('challenge_id', $question->challenge_id)
            ->where('attempt_number', $attemptNumber)
            ->get();

        $usedHelp = session()->pull("question_help_used.{$question->id}", false)
            || $existingAnswers->contains(fn($answer) => $answer->used_help);

        if ($existingAnswers->isEmpty()) {
            return $usedHelp;
        }

        $previousWasCorrect = (bool) $existingAnswers->first()->is_correct;
        $challengeResult = ChallengeResult::where('user_id', $user->id)
            ->where('challenge_id', $question->challenge_id)
            ->where('attempt_number', $attemptNumber)
            ->first();

        if ($challengeResult) {
            if ($previousWasCorrect) {
                $challengeResult->correct_answers = max(0, $challengeResult->correct_answers - 1);
                $challengeResult->total_score = max(0, $challengeResult->total_score - $question->score);
                $challengeResult->total_exp = max(0, $challengeResult->total_exp - $question->exp);
            } else {
                $challengeResult->wrong_answers = max(0, $challengeResult->wrong_answers - 1);
                if ($student->lives < 5) {
                    $student->increment('lives');
                    $student->refresh();
                }
            }

            $challengeResult->save();
        }

        StudentAnswer::whereIn('result_id', $existingAnswers->pluck('result_id'))->delete();

        return $usedHelp;
    }
}
