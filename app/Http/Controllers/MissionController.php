<?php

namespace App\Http\Controllers;

use App\Models\Challenge;
use App\Models\ChallengeResult;
use App\Models\Rank;
use App\Models\Section;
use Illuminate\Http\Request;

class MissionController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $student = $user->student;
        $student->loadMissing('ranks');

        $rank = $student->current_rank?->name ?? 'Unranked';

        $sections = Section::with(['challenges' => function ($query) {
            $query->withCount('questions')->orderBy('id');
        }])->orderBy('order')->get();

        $completedChallengeIds = ChallengeResult::query()
            ->where('user_id', $user->id)
            ->whereNotNull('ended_at')
            ->pluck('challenge_id')
            ->unique()
            ->values()
            ->all();

        $previousSectionCompleted = true;

        $sections->transform(function ($section) use (&$previousSectionCompleted, $completedChallengeIds) {
            $section->is_unlocked = $previousSectionCompleted;

            $previousChallengeCompleted = $section->is_unlocked;
            $allChallengesCompleted = true;

            $section->challenges->transform(function ($challenge) use (&$previousChallengeCompleted, &$allChallengesCompleted, $completedChallengeIds) {
                $challenge->is_completed = in_array($challenge->id, $completedChallengeIds, true);
                $challenge->is_unlocked = $previousChallengeCompleted;

                if (! $challenge->is_completed) {
                    $allChallengesCompleted = false;
                }

                $previousChallengeCompleted = $challenge->is_completed;

                return $challenge;
            });

            $section->is_completed = $allChallengesCompleted && $section->challenges->isNotEmpty();
            $previousSectionCompleted = $section->is_completed;

            return $section;
        });

        $allRanks = Rank::orderBy('min_exp')->get();

        return view('student.mission.index', compact('student', 'rank', 'sections', 'allRanks'));
    }

    public function show($id)
    {
        $user = auth()->user();
        $challenge = Challenge::withCount('questions')->findOrFail($id);

        if (! $this->isChallengeUnlockedForUser($user->id, $challenge)) {
            return response()->json([
                'message' => 'Mission ini masih terkunci. Selesaikan mission sebelumnya terlebih dahulu.',
            ], 403);
        }

        $latestAttemptNumber = ChallengeResult::where('user_id', $user->id)
            ->where('challenge_id', $id)
            ->max('attempt_number');

        $latestResult = null;
        $isPerfect = false;

        if ($latestAttemptNumber) {
            $latestResult = ChallengeResult::where('user_id', $user->id)
                ->where('challenge_id', $id)
                ->where('attempt_number', $latestAttemptNumber)
                ->first();

            $isPerfect = $latestResult && $latestResult->correct_answers == $challenge->questions_count;
        }

        return response()->json([
            'id' => $challenge->id,
            'title' => $challenge->title,
            'question_count' => $challenge->questions_count,
            'exp' => $challenge->total_exp,
            'score' => $challenge->total_score,
            'attempt_number' => $latestAttemptNumber ?? 0,
            'is_perfect' => $isPerfect,
        ]);
    }

    public function showChallenge($id)
    {
        return $this->show($id);
    }

    public function startChallenge(Request $request)
    {
        $user = auth()->user();
        $student = $user->student;
        $challengeId = $request->input('challenge_id');

        if (! $student || ! $challengeId) {
            return response()->json(['message' => 'Invalid request'], 400);
        }

        $challenge = Challenge::find($challengeId);
        if (! $challenge) {
            return response()->json(['message' => 'Challenge not found'], 404);
        }

        if (! $this->isChallengeUnlockedForUser($user->id, $challenge)) {
            return response()->json([
                'message' => 'Mission ini masih terkunci. Selesaikan mission sebelumnya terlebih dahulu.',
            ], 403);
        }

        $today = now()->toDateString();
        $lastStreakDate = $student->last_played;

        if ($lastStreakDate === $today) {
        } elseif ($lastStreakDate === now()->subDay()->toDateString()) {
            $student->increment('streak');
        } else {
            $student->streak = 1;
        }

        $student->last_played = $today;
        $student->current_challenge_id = $challenge->id;
        $student->current_section_id = $challenge->section_id;
        $student->save();

        return response()->json([
            'message' => 'Challenge and section updated',
            'streak' => $student->streak,
        ]);
    }

    public function checkLives()
    {
        $user = auth()->user();

        return response()->json([
            'lives' => $user->student->lives,
            'next_life_at' => $user->student->next_life_at,
        ]);
    }

    protected function isChallengeUnlockedForUser(int $userId, Challenge $challenge): bool
    {
        $section = Section::with(['challenges' => function ($query) {
            $query->orderBy('id');
        }])->findOrFail($challenge->section_id);

        if (! $this->isSectionUnlockedForUser($userId, $section->id)) {
            return false;
        }

        $previousChallenge = $section->challenges
            ->where('id', '<', $challenge->id)
            ->sortByDesc('id')
            ->first();

        if (! $previousChallenge) {
            return true;
        }

        return ChallengeResult::where('user_id', $userId)
            ->where('challenge_id', $previousChallenge->id)
            ->whereNotNull('ended_at')
            ->exists();
    }

    protected function isSectionUnlockedForUser(int $userId, int $sectionId): bool
    {
        $section = Section::orderBy('order')->findOrFail($sectionId);
        $previousSection = Section::with(['challenges' => function ($query) {
            $query->orderBy('id');
        }])->where('order', '<', $section->order)
            ->orderByDesc('order')
            ->first();

        if (! $previousSection) {
            return true;
        }

        if ($previousSection->challenges->isEmpty()) {
            return true;
        }

        foreach ($previousSection->challenges as $challenge) {
            $completed = ChallengeResult::where('user_id', $userId)
                ->where('challenge_id', $challenge->id)
                ->whereNotNull('ended_at')
                ->exists();

            if (! $completed) {
                return false;
            }
        }

        return true;
    }
}
