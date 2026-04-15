<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\ChallengeResult;
use App\Models\StudentAnswer;
use App\Models\Challenge;

class LecturerStudentController extends Controller
{
    /**
     * Display a listing of the students.
     */
    public function index()
    {
        $students = Student::with(['user', 'ranks'])->orderByDesc('total_score')->paginate(10);
        return view('lecturer.students.index', compact('students'));
    }

    /**
     * Show the form for creating a new student.
     */
    public function create()
    {
        return view('lecturer.students.create');
    }

    /**
     * Store a newly created student in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'nim' => 'required|string|unique:students,nim',
            'exp' => 'required|integer|min:0',
            'prodi' => 'nullable|string|max:25',
            'semester' => 'nullable|integer|min:1|max:14',
            'class' => 'nullable|string|max:10',
        ]);

        // Create User
        $user = User::create([
            'name' => trim($request->name),
            'email' => $request->email,
            'password' => Hash::make('password123'), // Default password
        ]);
        $user->assignRole('student');

        $student = Student::create([
            'user_id' => $user->id,
            'nim' => $request->nim,
            'exp' => $request->exp,
            'prodi' => $request->prodi,
            'semester' => $request->semester,
            'class' => $request->class,
        ]);
        $student->load('ranks');
        $student->updateRank();

        return redirect()->route('lecturer.students.index')->with('success', 'Mahasiswa berhasil ditambahkan.');
    }

    public function show(Student $student)
    {
        $student->load(['user', 'ranks', 'currentChallenge', 'currentSection']);

        $results = ChallengeResult::where('user_id', $student->user_id)
            ->with('challenge')
            ->orderBy('attempt_number')
            ->get();

        return view('lecturer.students.show', compact('student', 'results'));
    }

    public function detailResult(Student $student, Challenge $challenge, $attempt)
    {
        $answers = StudentAnswer::with(['question', 'selectedAnswer'])
            ->where('user_id', $student->user_id)
            ->where('challenge_id', $challenge->id)
            ->where('attempt_number', $attempt)
            ->get()
            ->groupBy('question_id');

        $result = ChallengeResult::where('user_id', $student->user_id)
            ->where('challenge_id', $challenge->id)
            ->where('attempt_number', $attempt)
            ->first();

        return view('lecturer.students.detail_result', compact('student', 'challenge', 'attempt', 'result', 'answers'));
    }
    /**
     * Show the form for editing the specified student.
     */
    public function edit(Student $student)
    {
        return view('lecturer.students.edit', compact('student'));
    }

    /**
     * Update the specified student in storage.
     */
    public function update(Request $request, Student $student)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $student->user_id,
            'nim' => 'required|string|unique:students,nim,' . $student->id,
            'exp' => 'required|integer|min:0',
            'prodi' => 'nullable|string|max:25',
            'semester' => 'nullable|integer|min:1|max:14',
            'class' => 'nullable|string|max:10',
        ]);

        // Update User
        $student->user->update([
            'name' => trim($request->name),
            'email' => $request->email,
        ]);

        $student->update([
            'nim' => $request->nim,
            'exp' => $request->exp,
            'prodi' => $request->prodi,
            'semester' => $request->semester,
            'class' => $request->class,
        ]);
        $student->load('ranks');
        $student->updateRank();

        return redirect()->route('lecturer.students.index')->with('success', 'Mahasiswa berhasil diperbarui.');
    }

    /**
     * Remove the specified student from storage.
     */
    public function destroy(Student $student)
    {
        $student->user->delete(); // Delete related user
        $student->delete();       // Delete student

        return redirect()->route('lecturer.students.index')->with('success', 'Mahasiswa berhasil dihapus.');
    }
}
