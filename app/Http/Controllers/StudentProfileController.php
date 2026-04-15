<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Achievement;
use App\Models\Challenge;
use App\Models\ChallengeResult;
use App\Models\Section;
use App\Models\Student;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class StudentProfileController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $student = Student::with(['user', 'ranks', 'currentSection', 'achievements'])
            ->where('user_id', $user->id)
            ->firstOrFail();
        $this->ensureProfilePhotoPubliclyAvailable($user);

        $currentChallenge = $student->current_challenge_id
            ? Challenge::with('section')->find($student->current_challenge_id)
            : null;
        $allAchievements = Achievement::all();
        $unlockedAchievementIds = $student->achievements->pluck('id')->toArray();
        $completedChallengeIds = ChallengeResult::where('user_id', $user->id)
            ->whereNotNull('ended_at')
            ->distinct()
            ->pluck('challenge_id');
        $completedChallengesCount = $completedChallengeIds->count();
        $totalChallengesCount = Challenge::count();
        $orderedSections = Section::with('challenges')->orderBy('order')->get();
        $completedSectionsCount = $orderedSections->filter(function ($section) use ($completedChallengeIds) {
            return $section->challenges->isNotEmpty()
                && $section->challenges->every(fn($challenge) => $completedChallengeIds->contains($challenge->id));
        })->count();

        $unlockedSectionsCount = $orderedSections->isNotEmpty() ? 1 : 0;
        foreach ($orderedSections as $section) {
            if ($section->order === 1) {
                continue;
            }

            $previousSection = $orderedSections->firstWhere('order', $section->order - 1);
            if (! $previousSection || $previousSection->challenges->isEmpty()) {
                $unlockedSectionsCount++;
                continue;
            }

            $allPreviousCompleted = $previousSection->challenges->every(
                fn($challenge) => $completedChallengeIds->contains($challenge->id)
            );

            if ($allPreviousCompleted) {
                $unlockedSectionsCount++;
            } else {
                break;
            }
        }

        $leaderboard = Student::join('users', 'students.user_id', '=', 'users.id')
            ->orderByDesc('students.weekly_score')
            ->select('students.weekly_score', 'users.name', 'users.profile_photo')
            ->limit(10)
            ->get();

        $weeklyRank = Student::where('weekly_score', '>', (int) $student->weekly_score)->count() + 1;

        $currentRank = $student->current_rank;
        $minExp = $currentRank?->min_exp ?? 0;
        $maxExp = $currentRank?->max_exp ?? 100;
        $expRange = max($maxExp - $minExp, 1);
        $expProgress = min(100, max(0, (($student->exp - $minExp) / $expRange) * 100));

        return view('student.profile.index', compact(
            'user',
            'student',
            'currentChallenge',
            'leaderboard',
            'weeklyRank',
            'currentRank',
            'expProgress',
            'allAchievements',
            'unlockedAchievementIds',
            'completedChallengesCount',
            'totalChallengesCount',
            'completedSectionsCount',
            'unlockedSectionsCount'
        ));
    }

    /**
     * Menampilkan Halaman Edit Profil Mahasiswa.
     */
    public function edit()
    {
        $user = Auth::user();
        $student = $user->student;
        $this->ensureProfilePhotoPubliclyAvailable($user);

        return view('student.profile.edit', compact('user', 'student'));
    }

    /**
     * Update Profil oleh Mahasiswa.
     */
    public function update(Request $request)
    {
        $user = auth()->user();
        $student = $user->student;

        // Validasi Data
        $validatedData = $request->validate([
            'address' => 'nullable|string|max:255',
            'birth_date' => 'nullable|date',
            'religion' => 'nullable|string|in:Islam,Protestan,Katolik,Hindu,Buddha,Konghucu,Lainnya',
            'gender' => 'nullable|string|in:Laki-laki,Perempuan',
            'phone_number' => 'nullable|string|max:20',
            'prodi' => 'nullable|string|in:Sistem Informasi Bisnis,Teknik Informatika',
            'semester' => 'nullable|integer|min:1|max:8',
            'profile_photo' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048', // Maks 2MB
        ]);

        // ✅ **Jika Ada Upload Foto Baru**
        if ($request->hasFile('profile_photo')) {
            if ($user->profile_photo && $user->profile_photo !== 'profile_photos/default.webp') {
                Storage::disk('public')->delete($user->profile_photo);
                $publicPhotoPath = public_path('storage/' . $user->profile_photo);
                if (file_exists($publicPhotoPath)) {
                    @unlink($publicPhotoPath);
                }
            }

            $file = $request->file('profile_photo');
            $filename = Str::random(40) . '.' . $file->getClientOriginalExtension();
            $path = 'profile_photos/' . $filename;

            Storage::disk('public')->putFileAs('profile_photos', $file, $filename);

            $publicDirectory = public_path('storage/profile_photos');
            if (! is_dir($publicDirectory)) {
                mkdir($publicDirectory, 0777, true);
            }
            copy(storage_path('app/public/' . $path), $publicDirectory . DIRECTORY_SEPARATOR . $filename);

            $user->profile_photo = $path;
        }

        // ✅ **Jika User Menghapus Foto**
        if ($request->input('delete_photo') == "1") {
            if ($user->profile_photo !== 'profile_photos/default.webp') {
                Storage::disk('public')->delete($user->profile_photo);
            }
            $user->profile_photo = 'profile_photos/default.webp';
        }

        $user->save();
        $student->update($validatedData);

        return redirect()->route('student.profile.index')->with('success', 'Profile updated successfully!');
    }

    protected function ensureProfilePhotoPubliclyAvailable($user): void
    {
        $photoPath = $user->profile_photo;

        if (blank($photoPath) || $photoPath === 'profile_photos/default.webp') {
            return;
        }

        $storagePath = storage_path('app/public/' . $photoPath);
        $publicPath = public_path('storage/' . $photoPath);

        if (file_exists($publicPath) || ! file_exists($storagePath)) {
            return;
        }

        $directory = dirname($publicPath);
        if (! is_dir($directory)) {
            mkdir($directory, 0777, true);
        }

        copy($storagePath, $publicPath);
    }
}
