<?php

namespace App\Observers;

use App\Models\Achievement;
use App\Models\ChallengeResult;
use App\Models\Section;
use App\Models\StudentAnswer;
use Illuminate\Support\Facades\Log;

class ChallengeResultObserver
{
    public function saved(ChallengeResult $result)
    {
        $user = $result->user;
        $student = $user->student()->with('achievements')->first();

        if (! $result->ended_at) {
            return;
        }

        $completedResults = ChallengeResult::where('user_id', $user->id)
            ->whereNotNull('ended_at')
            ->get();
        $completedChallengeIds = $completedResults->pluck('challenge_id')->unique();

        $result->load('challenge');
        $firstTime = $completedChallengeIds->count() >= 1;
        self::unlockIf($student, 'first_mission', $firstTime);

        self::unlockIf($student, 'three_missions', $completedChallengeIds->count() >= 3);

        $sections = Section::with('challenges')->get();
        $completedSections = $sections->filter(function ($section) use ($completedChallengeIds) {
            return $section->challenges->isNotEmpty()
                && $section->challenges->every(fn($challenge) => $completedChallengeIds->contains($challenge->id));
        });
        self::unlockIf($student, 'first_section', $completedSections->count() >= 1);

        $isPerfect = $result->total_score === $result->challenge?->total_score && $result->wrong_answers === 0;
        self::unlockIf($student, 'perfect_mission', $isPerfect);

        $guidedSuccess = StudentAnswer::where('user_id', $user->id)
            ->where('challenge_id', $result->challenge_id)
            ->where('attempt_number', $result->attempt_number)
            ->where('used_help', true)
            ->where('is_correct', true)
            ->exists();
        self::unlockIf($student, 'guided_success', $guidedSuccess);

        self::unlockIf($student, 'three_day_streak', (int) $student->streak >= 3);

        Log::info('Score check', [
            'user_id' => $user->id,
            'total_score' => $result->total_score,
            'max_score' => $result->challenge?->total_score,
            'isPerfect' => $isPerfect
        ]);
    }

    private static function unlockIf($student, $achievementCode, $condition)
    {
        if (!$condition) return;

        $achievement = Achievement::where('code', $achievementCode)->first();
        if ($achievement && !$student->achievements->contains($achievement->id)) {
            $student->achievements()->attach($achievement->id, [
                'unlocked_at' => now(),
            ]);
        }
    }
}
