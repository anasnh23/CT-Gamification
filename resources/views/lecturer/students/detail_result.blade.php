@extends('lecturer.layouts.app')

@section('content')
    <div class="student-result-page">
        <section class="student-result-hero">
            <div>
                <p class="student-result-kicker">Detail Attempt</p>
                <h1 class="student-result-title">{{ $student->user->name }} - Attempt {{ $attempt }}</h1>
                <p class="student-result-copy">Tinjau jawaban mahasiswa per soal untuk melihat pola kesalahan dan pemahaman konsep pada challenge ini.</p>
            </div>
            <a href="{{ route('lecturer.students.show', $student->id) }}" class="student-result-back">Kembali ke profil mahasiswa</a>
        </section>

        <section class="student-result-summary bg-white">
            <div class="student-result-summary-box">
                <span>Challenge</span>
                <strong>{{ $challenge->title }}</strong>
            </div>
            <div class="student-result-summary-box">
                <span>Score</span>
                <strong>{{ $result->total_score }}</strong>
            </div>
            <div class="student-result-summary-box">
                <span>EXP</span>
                <strong>{{ $result->total_exp }}</strong>
            </div>
            <div class="student-result-summary-box">
                <span>Attempt</span>
                <strong>#{{ $result->attempt_number }}</strong>
            </div>
        </section>

        <section class="student-result-card bg-white">
            <div class="student-result-card-head">
                <div>
                    <p class="student-result-card-kicker">Jawaban Mahasiswa</p>
                    <h2 class="student-result-card-title">Ringkasan jawaban per soal</h2>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="student-result-table">
                    <thead>
                        <tr>
                            <th>Pertanyaan</th>
                            <th>Input Mahasiswa</th>
                            <th>Detail Jawaban</th>
                            <th>Hasil</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($answers as $questionId => $groupedAnswers)
                            <tr>
                                <td>{{ Str::limit($groupedAnswers->first()->question->question_text ?? 'Question not found', 120) }}</td>
                                <td>{{ $groupedAnswers->pluck('selected_answer')->join(', ') }}</td>
                                <td>{{ $groupedAnswers->pluck('selectedAnswer.answer')->filter()->join(', ') }}</td>
                                <td>
                                    @php
                                        $isAllCorrect = $groupedAnswers->every(fn($ans) => $ans->is_correct);
                                        $isAnyCorrect = $groupedAnswers->contains(fn($ans) => $ans->is_correct);
                                    @endphp

                                    @if ($isAllCorrect)
                                        <span class="student-result-pill success">Semua benar</span>
                                    @elseif ($isAnyCorrect)
                                        <span class="student-result-pill warn">Sebagian benar</span>
                                    @else
                                        <span class="student-result-pill danger">Salah</span>
                                    @endif
                                </td>
                                <td>
                                    @if ($groupedAnswers->first()->question)
                                        <a href="{{ route('lecturer.questions.show', $groupedAnswers->first()->question->id) }}" class="student-result-btn">
                                            Lihat Soal
                                        </a>
                                    @else
                                        <span class="text-gray-400">N/A</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5">
                                    <div class="students-empty">Belum ada jawaban untuk attempt ini.</div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </div>

    <style>
        .student-result-page { max-width: 1200px; margin: 0 auto; }
        .student-result-hero { display:flex; justify-content:space-between; align-items:flex-end; gap:20px; margin-bottom:24px; padding:28px; border-radius:30px; border:1px solid rgba(255,228,236,.14); background: rgba(74,19,39,.78); box-shadow: 0 20px 50px rgba(0,0,0,.22); }
        .student-result-kicker { margin:0; font-size:12px; letter-spacing:.34em; text-transform:uppercase; color:rgba(255,228,236,.75); }
        .student-result-title { margin:12px 0 0; color:#fff; font-size:40px; font-weight:700; }
        .student-result-copy { margin:14px 0 0; max-width:760px; color:rgba(255,240,244,.76); line-height:1.8; }
        .student-result-back { display:inline-flex; align-items:center; justify-content:center; padding:14px 20px; border-radius:18px; background:rgba(255,255,255,.1); color:#fff; font-weight:700; text-decoration:none; }
        .student-result-summary { display:grid; grid-template-columns:repeat(4,minmax(0,1fr)); gap:16px; padding:24px; border-radius:30px; margin-bottom:24px; }
        .student-result-summary-box { padding:18px; border-radius:20px; background:#fff7fa; }
        .student-result-summary-box span { display:block; color:#94a3b8; font-size:12px; text-transform:uppercase; letter-spacing:.12em; margin-bottom:10px; }
        .student-result-summary-box strong { color:#1f2937; font-size:24px; }
        .student-result-card { padding:24px; border-radius:30px; }
        .student-result-card-head { margin-bottom:18px; }
        .student-result-card-kicker { margin:0; font-size:12px; text-transform:uppercase; letter-spacing:.3em; color:#be185d; }
        .student-result-card-title { margin:8px 0 0; color:#1f2937; font-size:30px; font-weight:700; }
        .student-result-table { width:100%; border-collapse:collapse; }
        .student-result-table thead th { padding:14px 16px; text-align:left; background:linear-gradient(90deg,#9f1d4f,#d9467a); color:#fff; font-size:12px; letter-spacing:.18em; text-transform:uppercase; }
        .student-result-table thead th:first-child { border-top-left-radius:18px; }
        .student-result-table thead th:last-child { border-top-right-radius:18px; }
        .student-result-table tbody td { padding:18px 16px; border-bottom:1px solid #f3e8ef; color:#334155; vertical-align:middle; }
        .student-result-table tbody tr:hover { background:#fff7fa; }
        .student-result-pill { display:inline-flex; align-items:center; padding:8px 12px; border-radius:999px; font-weight:700; font-size:12px; }
        .student-result-pill.success { background:#dcfce7; color:#166534; }
        .student-result-pill.warn { background:#fff7ed; color:#c2410c; }
        .student-result-pill.danger { background:#fee2e2; color:#991b1b; }
        .student-result-btn { display:inline-flex; align-items:center; justify-content:center; padding:10px 14px; border-radius:14px; background:#0ea5e9; color:#fff; font-weight:700; text-decoration:none; }
        @media (max-width:768px) {
            .student-result-hero { flex-direction:column; align-items:stretch; }
            .student-result-title { font-size:32px; }
            .student-result-summary { grid-template-columns:1fr; }
        }
    </style>
@endsection
