@extends('lecturer.layouts.app')

@section('content')
    <div class="questions-page">
        <section class="questions-hero">
            <div>
                <p class="questions-kicker">Questions</p>
                <h1 class="questions-title">Susun soal per challenge</h1>
            </div>

            <div class="questions-hero-actions">
                <form method="GET" action="{{ route('lecturer.questions.index') }}" class="questions-filter-form">
                    <label for="challenge_id" class="questions-filter-label">Filter Challenge</label>
                    <select name="challenge_id" id="challenge_id" onchange="this.form.submit()" class="questions-filter-select">
                        <option value="">Semua Challenge</option>
                        @foreach ($challenges as $challenge)
                            <option value="{{ $challenge->id }}" {{ (string) $selectedChallenge === (string) $challenge->id ? 'selected' : '' }}>
                                {{ $challenge->section?->order ?? '-' }}. {{ $challenge->title }}
                            </option>
                        @endforeach
                    </select>
                </form>

                <a href="{{ route('lecturer.questions.create', ['challenge_id' => request('challenge_id')]) }}" class="questions-primary-btn">
                    Tambah Soal
                </a>
            </div>
        </section>

        @if (session('success'))
            <div class="questions-alert success">{{ session('success') }}</div>
        @endif

        @if (session('error'))
            <div class="questions-alert error">{{ session('error') }}</div>
        @endif

        <section class="questions-card bg-white">
            <div class="questions-card-head">
                <div>
                    <p class="questions-card-kicker">Daftar</p>
                    <h2 class="questions-card-title">Soal aktif</h2>
                </div>

                <input type="text" id="searchInput" placeholder="Cari deskripsi atau pertanyaan..."
                    class="questions-search-input" onkeyup="filterQuestions()">
            </div>

            <div class="overflow-x-auto">
                <table class="questions-table">
                    <thead>
                        <tr>
                            <th>Challenge</th>
                            <th>Deskripsi</th>
                            <th>Tipe</th>
                            <th>Pertanyaan</th>
                            <th>Preview</th>
                            <th>Score / EXP</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($questions as $question)
                            <tr class="question-row">
                                <td>
                                    <div class="questions-name-wrap">
                                        <strong>{{ $question->challenge->title }}</strong>
                                        <span>{{ $question->challenge->section->name ?? 'Tanpa section' }}</span>
                                    </div>
                                </td>
                                <td class="question-desc">
                                    {{ Str::limit($question->description ?: '-', 70) }}
                                </td>
                                <td>
                                    @php
                                        $typeMap = [
                                            'multiple_choice' => 'Pilihan Ganda',
                                            'true_false' => 'Benar / Salah',
                                            'essay' => 'Esai',
                                        ];
                                    @endphp
                                    <span class="questions-pill type">
                                        {{ $typeMap[$question->type] ?? ucfirst($question->type) }}
                                    </span>
                                </td>
                                <td class="question-text">
                                    {{ Str::limit($question->question_text, 90) }}
                                </td>
                                <td>
                                    @if ($question->question_image)
                                        <img src="{{ asset('storage/' . $question->question_image) }}" alt="Question Image"
                                            class="questions-thumb">
                                    @else
                                        <span class="questions-pill soft">Tanpa gambar</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="questions-score-wrap">
                                        <span class="questions-pill score">{{ $question->score }} score</span>
                                        <span class="questions-pill exp">{{ $question->exp }} exp</span>
                                    </div>
                                </td>
                                <td>
                                    <div class="questions-actions">
                                        <a href="{{ route('lecturer.questions.show', $question->id) }}" class="questions-btn info">Info</a>
                                        <a href="{{ route('lecturer.questions.edit', $question->id) }}" class="questions-btn warn">Edit</a>
                                        <form action="{{ route('lecturer.questions.destroy', $question->id) }}" method="POST"
                                            onsubmit="return confirm('Hapus soal ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="questions-btn danger">Hapus</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr id="noDataRow">
                                <td colspan="7">
                                    <div class="questions-empty">
                                        Belum ada soal pada daftar ini. Tambahkan soal pertama untuk mulai membangun challenge.
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                        <tr id="noResultsRow" style="display: none;">
                            <td colspan="7">
                                <div class="questions-empty">Tidak ada soal yang cocok dengan pencarian.</div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="questions-pagination">
                {{ $questions->appends(['challenge_id' => request('challenge_id')])->links('pagination::tailwind') }}
            </div>
        </section>
    </div>

    <style>
        .questions-page {
            max-width: 1250px;
            margin: 0 auto;
        }

        .questions-hero {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            gap: 20px;
            margin-bottom: 24px;
            padding: 28px;
            border-radius: 30px;
            border: 1px solid rgba(255, 228, 236, 0.14);
            background: rgba(74, 19, 39, 0.78);
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.22);
        }

        .questions-kicker {
            margin: 0;
            font-size: 12px;
            letter-spacing: 0.34em;
            text-transform: uppercase;
            color: rgba(255, 228, 236, 0.75);
        }

        .questions-title {
            margin: 12px 0 0;
            font-size: 42px;
            line-height: 1.15;
            color: #fff;
            font-weight: 700;
        }

        .questions-copy {
            margin: 14px 0 0;
            max-width: 760px;
            color: rgba(255, 240, 244, 0.76);
            line-height: 1.8;
        }

        .questions-hero-actions {
            display: flex;
            align-items: flex-end;
            gap: 14px;
            flex-wrap: wrap;
        }

        .questions-filter-form {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .questions-filter-label {
            color: rgba(255, 240, 244, 0.76);
            font-size: 13px;
            font-weight: 600;
        }

        .questions-filter-select {
            min-width: 260px;
            padding: 13px 16px;
            border-radius: 16px;
            border: 1px solid rgba(255, 228, 236, 0.18);
            background: rgba(255, 255, 255, 0.08);
            color: #fff;
        }

        .questions-filter-select option {
            color: #1f2937;
        }

        .questions-primary-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 14px 20px;
            border-radius: 18px;
            background: linear-gradient(90deg, #c0265f, #ec4899);
            color: #fff;
            font-weight: 700;
            text-decoration: none;
            white-space: nowrap;
            box-shadow: 0 16px 30px rgba(190, 24, 93, 0.25);
        }

        .questions-alert {
            margin-bottom: 16px;
            padding: 14px 18px;
            border-radius: 18px;
            font-weight: 600;
        }

        .questions-alert.success {
            background: rgba(220, 252, 231, 0.96);
            color: #166534;
        }

        .questions-alert.error {
            background: rgba(254, 226, 226, 0.96);
            color: #991b1b;
        }

        .questions-card {
            padding: 24px;
            border-radius: 30px;
        }

        .questions-card-head {
            display: flex;
            align-items: flex-end;
            justify-content: space-between;
            gap: 16px;
            margin-bottom: 18px;
        }

        .questions-card-kicker {
            margin: 0;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.3em;
            color: #be185d;
        }

        .questions-card-title {
            margin: 8px 0 0;
            color: #1f2937;
            font-size: 30px;
            font-weight: 700;
        }

        .questions-search-input {
            min-width: 280px;
            padding: 13px 16px;
            border-radius: 16px;
            border: 1px solid #f0b6c9;
            box-sizing: border-box;
            color: #1f2937;
        }

        .questions-table {
            width: 100%;
            border-collapse: collapse;
        }

        .questions-table thead th {
            padding: 14px 16px;
            text-align: left;
            background: linear-gradient(90deg, #9f1d4f, #d9467a);
            color: #fff;
            font-size: 12px;
            letter-spacing: 0.18em;
            text-transform: uppercase;
        }

        .questions-table thead th:first-child {
            border-top-left-radius: 18px;
        }

        .questions-table thead th:last-child {
            border-top-right-radius: 18px;
        }

        .questions-table tbody td {
            padding: 18px 16px;
            border-bottom: 1px solid #f3e8ef;
            color: #334155;
            vertical-align: middle;
        }

        .questions-table tbody tr:hover {
            background: #fff7fa;
        }

        .questions-name-wrap {
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        .questions-name-wrap strong {
            color: #1f2937;
            font-size: 15px;
        }

        .questions-name-wrap span {
            color: #94a3b8;
            font-size: 13px;
        }

        .questions-pill {
            display: inline-flex;
            align-items: center;
            padding: 8px 12px;
            border-radius: 999px;
            font-weight: 700;
            font-size: 12px;
        }

        .questions-pill.type {
            background: #fff1f6;
            color: #be185d;
        }

        .questions-pill.soft {
            background: #f8fafc;
            color: #64748b;
        }

        .questions-pill.score {
            background: #fff7ed;
            color: #c2410c;
        }

        .questions-pill.exp {
            background: #fdf4ff;
            color: #a21caf;
        }

        .questions-thumb {
            width: 76px;
            height: 76px;
            object-fit: cover;
            border-radius: 16px;
            box-shadow: 0 10px 20px rgba(15, 23, 42, 0.12);
        }

        .questions-score-wrap {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .questions-actions {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 8px;
        }

        .questions-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 10px 14px;
            border-radius: 14px;
            border: 0;
            text-decoration: none;
            color: #fff;
            font-weight: 700;
            cursor: pointer;
        }

        .questions-btn.info {
            background: #0ea5e9;
        }

        .questions-btn.warn {
            background: #f59e0b;
        }

        .questions-btn.danger {
            background: #ef4444;
        }

        .questions-empty {
            padding: 24px;
            text-align: center;
            color: #64748b;
        }

        .questions-pagination {
            margin-top: 18px;
        }

        @media (max-width: 768px) {
            .questions-hero,
            .questions-card-head {
                flex-direction: column;
                align-items: stretch;
            }

            .questions-title {
                font-size: 32px;
            }

            .questions-card {
                padding: 18px;
            }

            .questions-filter-select,
            .questions-search-input {
                min-width: 100%;
            }
        }
    </style>

    <script>
        function filterQuestions() {
            const input = document.getElementById("searchInput");
            const filter = input.value.toLowerCase();
            const rows = document.querySelectorAll(".question-row");
            const noResultsRow = document.getElementById("noResultsRow");
            let anyVisible = false;

            rows.forEach(row => {
                const desc = row.querySelector(".question-desc")?.textContent.toLowerCase() || '';
                const text = row.querySelector(".question-text")?.textContent.toLowerCase() || '';
                const isMatch = desc.includes(filter) || text.includes(filter);

                row.style.display = isMatch ? "" : "none";

                if (isMatch) {
                    anyVisible = true;
                }
            });

            if (noResultsRow) {
                noResultsRow.style.display = anyVisible ? "none" : "";
            }
        }
    </script>
@endsection
