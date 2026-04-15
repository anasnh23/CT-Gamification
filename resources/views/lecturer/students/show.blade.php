@extends('lecturer.layouts.app')

@section('content')
    <div class="student-detail-page">
        <section class="student-detail-hero">
            <div class="student-detail-profile">
                <img src="{{ $student->user->profile_photo ? asset('storage/' . $student->user->profile_photo) : asset('images/default-avatar.png') }}"
                    alt="Profile Photo" class="student-detail-avatar">
                <div>
                    <p class="student-detail-kicker">Profil Mahasiswa</p>
                    <h1 class="student-detail-title">{{ $student->user->name }}</h1>
                    <p class="student-detail-copy">{{ $student->nim }} | {{ $student->prodi ?? 'Program studi belum diisi' }}</p>
                </div>
            </div>
            <div class="student-detail-hero-actions">
                <a href="{{ route('lecturer.students.edit', $student->id) }}" class="student-detail-edit">Edit Mahasiswa</a>
                <a href="{{ route('lecturer.students.index') }}" class="student-detail-back">Kembali ke daftar</a>
            </div>
        </section>

        <div class="student-detail-grid">
            <section class="student-detail-card bg-white">
                <h2 class="student-detail-card-title">Informasi Akademik</h2>
                <div class="student-detail-info-grid">
                    <div><span>NIM</span><strong>{{ $student->nim }}</strong></div>
                    <div><span>Program Studi</span><strong>{{ $student->prodi ?? '-' }}</strong></div>
                    <div><span>Semester</span><strong>{{ $student->semester ?? '-' }}</strong></div>
                    <div><span>Kelas</span><strong>{{ $student->class ?? '-' }}</strong></div>
                </div>
            </section>

            <section class="student-detail-card bg-white">
                <h2 class="student-detail-card-title">Progress Belajar</h2>
                <div class="student-detail-info-grid">
                    <div><span>Rank</span><strong>{{ $student->current_rank?->name ?? 'Belum ada rank' }}</strong></div>
                    <div><span>EXP</span><strong>{{ $student->exp }}</strong></div>
                    <div><span>Streak</span><strong>{{ $student->streak }} hari</strong></div>
                    <div><span>Weekly Score</span><strong>{{ $student->weekly_score }}</strong></div>
                    <div><span>Total Score</span><strong>{{ $student->total_score }}</strong></div>
                    <div><span>Last Played</span><strong>{{ $student->last_played ? \Carbon\Carbon::parse($student->last_played)->translatedFormat('d F Y') : 'Belum pernah' }}</strong></div>
                    <div><span>Current Challenge</span><strong>{{ $student->currentChallenge?->title ?? 'Tidak ada' }}</strong></div>
                    <div><span>Current Section</span><strong>{{ $student->currentSection?->name ?? 'Tidak ada' }}</strong></div>
                </div>
            </section>
        </div>

        <section class="student-detail-card bg-white student-results-card">
            <div class="student-results-head">
                <div>
                    <p class="student-results-kicker">Riwayat Attempt</p>
                    <h2 class="student-detail-card-title">Hasil challenge mahasiswa</h2>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="student-results-table">
                    <thead>
                        <tr>
                            <th>Challenge</th>
                            <th>Attempt</th>
                            <th>Score</th>
                            <th>EXP</th>
                            <th>Benar</th>
                            <th>Salah</th>
                            <th>Durasi</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($results as $result)
                            <tr>
                                <td>{{ $result->challenge->title ?? 'Challenge' }}</td>
                                <td>{{ $result->attempt_number }}</td>
                                <td>{{ $result->total_score }}</td>
                                <td>{{ $result->total_exp }}</td>
                                <td>{{ $result->correct_answers }}</td>
                                <td>{{ $result->wrong_answers }}</td>
                                <td>
                                    @php
                                        $start = \Carbon\Carbon::parse($result->created_at);
                                        $end = \Carbon\Carbon::parse($result->ended_at ?? now());
                                        $duration = $start->diff($end)->format('%h hr %i min %s sec');
                                    @endphp
                                    {{ $duration }}
                                </td>
                                <td>
                                    <a href="{{ route('lecturer.students.detail_result', ['student' => $student->id, 'challenge' => $result->challenge_id, 'attempt' => $result->attempt_number]) }}"
                                        class="student-detail-btn">Lihat Detail</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8">
                                    <div class="students-empty">Belum ada hasil challenge untuk mahasiswa ini.</div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </div>

    <style>
        .student-detail-page { max-width: 1200px; margin: 0 auto; }
        .student-detail-hero { display:flex; justify-content:space-between; align-items:center; gap:20px; margin-bottom:24px; padding:28px; border-radius:30px; border:1px solid rgba(255,228,236,.14); background: rgba(74,19,39,.78); box-shadow: 0 20px 50px rgba(0,0,0,.22); }
        .student-detail-profile { display:flex; align-items:center; gap:18px; }
        .student-detail-avatar { width:88px; height:88px; border-radius:999px; object-fit:cover; border:3px solid rgba(255,228,236,.3); background:#fff; }
        .student-detail-kicker { margin:0; font-size:12px; letter-spacing:.34em; text-transform:uppercase; color:rgba(255,228,236,.75); }
        .student-detail-title { margin:10px 0 0; color:#fff; font-size:40px; font-weight:700; }
        .student-detail-copy { margin:10px 0 0; color:rgba(255,240,244,.76); }
        .student-detail-hero-actions { display:flex; align-items:center; gap:12px; flex-wrap:wrap; }
        .student-detail-back,
        .student-detail-edit { display:inline-flex; align-items:center; justify-content:center; padding:14px 20px; border-radius:18px; color:#fff; font-weight:700; text-decoration:none; }
        .student-detail-back { background:rgba(255,255,255,.1); }
        .student-detail-edit { background:linear-gradient(90deg,#c0265f,#ec4899); box-shadow:0 16px 30px rgba(190,24,93,.22); }
        .student-detail-grid { display:grid; grid-template-columns: repeat(2, minmax(0,1fr)); gap:24px; margin-bottom:24px; }
        .student-detail-card { padding:24px; border-radius:30px; }
        .student-detail-card-title { margin:0 0 18px; color:#1f2937; font-size:28px; font-weight:700; }
        .student-detail-info-grid { display:grid; grid-template-columns: repeat(2, minmax(0,1fr)); gap:16px; }
        .student-detail-info-grid div { padding:16px; border-radius:18px; background:#fff7fa; }
        .student-detail-info-grid span { display:block; color:#94a3b8; font-size:12px; text-transform:uppercase; letter-spacing:.12em; margin-bottom:8px; }
        .student-detail-info-grid strong { color:#1f2937; font-size:15px; }
        .student-results-card { margin-top: 6px; }
        .student-results-head { margin-bottom:18px; }
        .student-results-kicker { margin:0; font-size:12px; text-transform:uppercase; letter-spacing:.3em; color:#be185d; }
        .student-results-table { width:100%; border-collapse:collapse; }
        .student-results-table thead th { padding:14px 16px; text-align:left; background:linear-gradient(90deg,#9f1d4f,#d9467a); color:#fff; font-size:12px; letter-spacing:.18em; text-transform:uppercase; }
        .student-results-table thead th:first-child { border-top-left-radius:18px; }
        .student-results-table thead th:last-child { border-top-right-radius:18px; }
        .student-results-table tbody td { padding:18px 16px; border-bottom:1px solid #f3e8ef; color:#334155; vertical-align:middle; }
        .student-results-table tbody tr:hover { background:#fff7fa; }
        .student-detail-btn { display:inline-flex; align-items:center; justify-content:center; padding:10px 14px; border-radius:14px; background:#f59e0b; color:#fff; font-weight:700; text-decoration:none; }
        @media (max-width:768px) {
            .student-detail-hero { flex-direction:column; align-items:stretch; }
            .student-detail-grid { grid-template-columns:1fr; }
            .student-detail-title { font-size:32px; }
            .student-detail-info-grid { grid-template-columns:1fr; }
        }
    </style>
@endsection
