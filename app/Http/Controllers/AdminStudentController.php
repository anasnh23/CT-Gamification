<?php

namespace App\Http\Controllers;

use App\Models\Challenge;
use App\Models\Student;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class AdminStudentController extends Controller
{
    public function index(Request $request)
    {
        $query = Student::with(['user', 'ranks'])
            ->join('users', 'students.user_id', '=', 'users.id')
            ->select('students.*', 'users.name as user_name', 'users.email as user_email');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('users.name', 'like', "%{$search}%")
                    ->orWhere('users.email', 'like', "%{$search}%")
                    ->orWhere('students.prodi', 'like', "%{$search}%")
                    ->orWhere('students.class', 'like', "%{$search}%")
                    ->orWhere('students.semester', 'like', "%{$search}%");
            });
        }

        $sortField = $request->get('sort', 'students.created_at');
        $sortOrder = $request->get('order', 'desc');
        $allowedFields = ['user_name', 'user_email', 'prodi', 'class', 'semester', 'students.created_at'];

        if (in_array($sortField, $allowedFields, true)) {
            $query->orderBy($sortField, $sortOrder);
        }

        $perPage = $request->get('perPage', 10);

        if ($perPage === 'all') {
            $students = $query->get();
            $pagination = false;
        } else {
            $students = $query->paginate((int) $perPage);
            $pagination = true;
        }

        return view('admin.students.index', compact('students', 'sortField', 'sortOrder', 'perPage', 'pagination'));
    }

    public function show(Student $student)
    {
        $student->load(['user', 'ranks', 'currentChallenge', 'currentSection']);

        return view('admin.students.show', compact('student'));
    }

    public function create()
    {
        return view('admin.students.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'profile_photo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'nim' => 'required|string|unique:students,nim',
            'address' => 'nullable|string',
            'birth_date' => 'nullable|date',
            'religion' => 'required|string|in:Islam,Protestan,Katolik,Hindu,Buddha,Konghucu,Lainnya',
            'gender' => 'required|string|in:Laki-laki,Perempuan',
            'phone_number' => 'nullable|string|max:15',
            'prodi' => 'required|string|in:Sistem Informasi Bisnis,Teknik Informatika',
            'semester' => 'required|integer|min:1|max:8',
            'class' => 'required|string|max:50',
        ]);

        $profilePhotoPath = null;
        if ($request->hasFile('profile_photo')) {
            $profilePhotoPath = $request->file('profile_photo')->store('profile_photos', 'public');
        }

        $user = User::create([
            'name' => trim($request->name),
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'profile_photo' => $profilePhotoPath,
        ]);

        $user->assignRole('student');

        $student = Student::create([
            'user_id' => $user->id,
            'nim' => $request->nim,
            'address' => $request->address,
            'birth_date' => $request->birth_date,
            'religion' => $request->religion,
            'gender' => $request->gender,
            'phone_number' => $request->phone_number,
            'prodi' => $request->prodi,
            'semester' => $request->semester,
            'class' => $request->class,
        ]);

        $student->load('ranks');
        $student->updateRank();

        return redirect()->route('admin.students.index')->with('success', 'Mahasiswa berhasil ditambahkan.');
    }

    public function edit(Student $student)
    {
        $student->load(['user', 'ranks']);
        $challenges = Challenge::orderBy('title')->get();

        return view('admin.students.edit', compact('student', 'challenges'));
    }

    public function update(Request $request, Student $student)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $student->user_id,
            'nim' => 'required|string|unique:students,nim,' . $student->id,
            'birth_date' => 'nullable|date',
            'religion' => 'required|string|in:Islam,Protestan,Katolik,Hindu,Buddha,Konghucu,Lainnya',
            'gender' => 'required|string|in:Laki-laki,Perempuan',
            'phone_number' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'prodi' => 'required|string|in:Sistem Informasi Bisnis,Teknik Informatika',
            'semester' => 'required|integer|min:1|max:8',
            'class' => 'nullable|string|max:50',
            'streak' => 'required|integer|min:0',
            'exp' => 'required|integer|min:0',
            'weekly_score' => 'required|integer|min:0',
            'total_score' => 'required|integer|min:0',
            'current_challenge_id' => 'nullable|exists:challenges,id',
            'profile_photo' => 'nullable|image|mimes:jpg,png,jpeg,webp|max:2048',
        ]);

        $user = $student->user;
        $user->update([
            'name' => trim($request->name),
            'email' => $request->email,
        ]);

        if ($request->delete_photo === '1') {
            if ($user->profile_photo && $user->profile_photo !== 'profile_photos/default.webp') {
                Storage::disk('public')->delete($user->profile_photo);
            }
            $user->update(['profile_photo' => 'profile_photos/default.webp']);
        } elseif ($request->hasFile('profile_photo')) {
            if ($user->profile_photo && Storage::disk('public')->exists($user->profile_photo) && $user->profile_photo !== 'profile_photos/default.webp') {
                Storage::disk('public')->delete($user->profile_photo);
            }

            $photoPath = $request->file('profile_photo')->store('profile_photos', 'public');
            $user->update(['profile_photo' => $photoPath]);
        }

        $student->update([
            'nim' => $request->nim,
            'birth_date' => $request->birth_date,
            'religion' => $request->religion,
            'gender' => $request->gender,
            'phone_number' => $request->phone_number,
            'address' => $request->address,
            'prodi' => $request->prodi,
            'semester' => $request->semester,
            'class' => $request->class,
            'streak' => $request->streak,
            'exp' => $request->exp,
            'weekly_score' => $request->weekly_score,
            'total_score' => $request->total_score,
            'current_challenge_id' => $request->current_challenge_id,
        ]);

        $student->load('ranks');
        $student->updateRank();

        return redirect()->route('admin.students.index')->with('success', 'Mahasiswa berhasil diperbarui.');
    }

    public function destroy(Student $student)
    {
        if ($student->user->profile_photo && Storage::disk('public')->exists($student->user->profile_photo)) {
            Storage::disk('public')->delete($student->user->profile_photo);
        }

        $student->user->delete();
        $student->delete();

        return redirect()->route('admin.students.index')->with('success', 'Mahasiswa berhasil dihapus.');
    }
}
