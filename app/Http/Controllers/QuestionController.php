<?php

namespace App\Http\Controllers;

use App\Models\Answer;
use App\Models\Challenge;
use App\Models\Question;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class QuestionController extends Controller
{
    public function index(Request $request)
    {
        $challenges = Challenge::with('section')->orderBy('section_id')->orderBy('title')->get();
        $selectedChallenge = $request->query('challenge_id');

        $questions = Question::query()
            ->with(['challenge.section', 'answers'])
            ->when($selectedChallenge, fn($query) => $query->where('challenge_id', $selectedChallenge))
            ->orderBy('challenge_id')
            ->orderBy('id')
            ->paginate(10);

        return view('lecturer.questions.index', compact('questions', 'challenges', 'selectedChallenge'));
    }

    public function show($id)
    {
        $question = Question::with('answers', 'challenge')->findOrFail($id);

        return view('lecturer.questions.show', compact('question'));
    }

    public function create(Request $request)
    {
        $challenges = Challenge::with('section')->orderBy('section_id')->orderBy('title')->get();
        $selectedChallengeId = $request->query('challenge_id');

        return view('lecturer.questions.create', compact('challenges', 'selectedChallengeId'));
    }

    public function store(Request $request)
    {
        $validated = $this->validateQuestion($request);

        $data = collect($validated)->only([
            'challenge_id',
            'type',
            'description',
            'question_text',
            'help_text',
            'explanation_text',
            'score',
            'exp',
        ])->toArray();
        $data['description'] = trim((string) ($data['description'] ?? ''));
        $data['question_text'] = trim((string) $data['question_text']);
        $data['help_text'] = trim((string) ($data['help_text'] ?? ''));
        $data['explanation_text'] = trim((string) ($data['explanation_text'] ?? ''));

        if ($request->hasFile('question_image')) {
            $data['question_image'] = $request->file('question_image')->store('questions', 'public');
        }

        $question = Question::create($data);
        $this->syncAnswers($request, $question);
        $question->challenge->recalculateTotals();

        return redirect()->route('lecturer.questions.index')->with('success', 'Soal berhasil dibuat.');
    }

    public function edit($id)
    {
        $question = Question::with('answers')->findOrFail($id);
        $challenges = Challenge::with('section')->orderBy('section_id')->orderBy('title')->get();

        return view('lecturer.questions.edit', compact('question', 'challenges'));
    }

    public function update(Request $request, Question $question)
    {
        $validated = $this->validateQuestion($request);

        $data = collect($validated)->only([
            'challenge_id',
            'type',
            'description',
            'question_text',
            'help_text',
            'explanation_text',
            'score',
            'exp',
        ])->toArray();
        $data['description'] = trim((string) ($data['description'] ?? ''));
        $data['question_text'] = trim((string) $data['question_text']);
        $data['help_text'] = trim((string) ($data['help_text'] ?? ''));
        $data['explanation_text'] = trim((string) ($data['explanation_text'] ?? ''));

        if ($request->boolean('delete_question_image') && $question->question_image) {
            Storage::disk('public')->delete($question->question_image);
            $data['question_image'] = null;
        }

        if ($request->hasFile('question_image')) {
            if ($question->question_image) {
                Storage::disk('public')->delete($question->question_image);
            }

            $data['question_image'] = $request->file('question_image')->store('questions', 'public');
        } elseif (! array_key_exists('question_image', $data)) {
            $data['question_image'] = $question->question_image;
        }

        $question->answers()->delete();
        $question->update($data);

        $this->syncAnswers($request, $question);
        $question->challenge->recalculateTotals();

        return redirect()->route('lecturer.questions.index')->with('success', 'Soal berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $question = Question::with('answers', 'challenge')->findOrFail($id);

        if ($question->question_image) {
            Storage::disk('public')->delete($question->question_image);
        }

        foreach ($question->answers as $answer) {
            if ($answer->answer_image) {
                Storage::disk('public')->delete($answer->answer_image);
            }
        }

        $challenge = $question->challenge;
        $question->answers()->delete();
        $question->delete();
        $challenge?->recalculateTotals();

        return redirect()->route('lecturer.questions.index')->with('success', 'Soal berhasil dihapus.');
    }

    protected function validateQuestion(Request $request): array
    {
        return $request->validate([
            'challenge_id' => 'required|exists:challenges,id',
            'type' => 'required|in:multiple_choice,true_false,essay',
            'description' => 'nullable|string',
            'question_text' => 'required|string',
            'help_text' => 'nullable|string',
            'explanation_text' => 'nullable|string',
            'score' => 'required|integer|min:0',
            'exp' => 'required|integer|min:0',
            'question_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
            'answers' => 'required_if:type,multiple_choice,essay|array',
            'answers.*' => 'nullable|string',
            'is_correct' => 'nullable|array',
            'answer_images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
            'correct_answer' => 'required_if:type,true_false|in:true,false',
        ]);
    }

    protected function syncAnswers(Request $request, Question $question): void
    {
        if ($request->type === 'true_false') {
            Answer::create([
                'question_id' => $question->id,
                'answer' => 'True',
                'is_correct' => $request->correct_answer === 'true',
            ]);

            Answer::create([
                'question_id' => $question->id,
                'answer' => 'False',
                'is_correct' => $request->correct_answer === 'false',
            ]);

            return;
        }

        if ($request->type === 'essay') {
            Answer::create([
                'question_id' => $question->id,
                'answer' => $request->answers[0] ?? '',
                'is_correct' => true,
            ]);

            return;
        }

        foreach ($request->answers as $index => $answerText) {
            if (blank($answerText) && ! $request->hasFile("answer_images.$index")) {
                continue;
            }

            $answerData = [
                'question_id' => $question->id,
                'answer' => $answerText,
                'is_correct' => isset($request->is_correct[$index]) && (string) $request->is_correct[$index] === '1',
            ];

            if ($request->hasFile("answer_images.$index")) {
                $answerData['answer_image'] = $request->file("answer_images.$index")->store('answers', 'public');
            } elseif (! empty($request->old_answer_images[$index])) {
                $answerData['answer_image'] = $request->old_answer_images[$index];
            }

            Answer::create($answerData);
        }
    }
}
