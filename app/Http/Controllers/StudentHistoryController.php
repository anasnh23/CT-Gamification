<?php

namespace App\Http\Controllers;

use App\Models\ChallengeResult;

class StudentHistoryController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $student = $user->student;

        $results = ChallengeResult::with(['challenge' => function ($query) {
            $query->withCount('questions')->with('section');
        }])
            ->where('user_id', $user->id)
            ->whereNotNull('ended_at')
            ->orderByDesc('ended_at')
            ->orderByDesc('attempt_number')
            ->get();

        $bestResults = $results
            ->groupBy('challenge_id')
            ->map(fn($group) => $group->sortByDesc('total_score')->sortByDesc('total_exp')->first())
            ->values();

        $summary = [
            'completed_missions' => $results->pluck('challenge_id')->unique()->count(),
            'total_attempts' => $results->count(),
            'best_score' => $bestResults->max('total_score') ?? 0,
            'average_score' => round($bestResults->avg('total_score') ?? 0, 1),
        ];

        return view('student.history.index', compact('student', 'results', 'bestResults', 'summary'));
    }
}
