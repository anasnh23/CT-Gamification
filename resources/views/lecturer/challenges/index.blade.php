@extends('lecturer.layouts.app')

@section('content')
    <div class="challenges-page">
        <section class="challenges-hero">
            <div>
                <p class="challenges-kicker">Challenges</p>
                <h1 class="challenges-title">Atur challenge per section</h1>
            </div>

            <div class="challenges-hero-actions">
                <form method="GET" action="{{ route('lecturer.challenges.index') }}" class="challenges-filter-form">
                    <label for="section_id" class="challenges-filter-label">Filter Section</label>
                    <select name="section_id" id="section_id" onchange="this.form.submit()" class="challenges-filter-select">
                        <option value="">Semua Section</option>
                        @foreach ($sections as $section)
                            <option value="{{ $section->id }}" {{ (string) $sectionSearch === (string) $section->id ? 'selected' : '' }}>
                                {{ $section->order }} - {{ $section->name }}
                            </option>
                        @endforeach
                    </select>
                </form>

                <a href="{{ route('lecturer.challenges.create', ['section_id' => request('section_id')]) }}" class="challenges-primary-btn">
                    Tambah Challenge
                </a>
            </div>
        </section>

        @if (session('success'))
            <div class="challenges-alert success">{{ session('success') }}</div>
        @endif

        @if (session('error'))
            <div class="challenges-alert error">{{ session('error') }}</div>
        @endif

        <section class="challenges-card bg-white">
            <div class="challenges-card-head">
                <div>
                    <p class="challenges-card-kicker">Daftar</p>
                    <h2 class="challenges-card-title">Challenge aktif</h2>
                </div>
                <p class="challenges-card-note">EXP dan score otomatis.</p>
            </div>

            <div class="overflow-x-auto">
                <table class="challenges-table">
                    <thead>
                        <tr>
                            <th>Section</th>
                            <th>Judul Challenge</th>
                            <th>Jumlah Soal</th>
                            <th>Total EXP</th>
                            <th>Total Score</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($challenges as $challenge)
                            <tr>
                                <td>
                                    <span class="challenges-pill">
                                        {{ $challenge->section->order ?? '-' }}. {{ $challenge->section->name ?? 'No Section' }}
                                    </span>
                                </td>
                                <td>
                                    <div class="challenges-name-wrap">
                                        <strong>{{ $challenge->title }}</strong>
                                        <span>Challenge #{{ $challenge->id }}</span>
                                    </div>
                                </td>
                                <td>
                                    <span class="challenges-pill soft">{{ $challenge->questions_count }} soal</span>
                                </td>
                                <td class="challenges-metric">{{ $challenge->total_exp }}</td>
                                <td class="challenges-metric">{{ $challenge->total_score }}</td>
                                <td>
                                    <div class="challenges-actions">
                                        <a href="{{ route('lecturer.challenges.edit', $challenge->id) }}" class="challenges-btn warn">Edit</a>
                                        <a href="{{ route('lecturer.questions.index', ['challenge_id' => $challenge->id]) }}" class="challenges-btn accent">Kelola Soal</a>
                                        <form action="{{ route('lecturer.challenges.destroy', $challenge->id) }}" method="POST"
                                            onsubmit="return confirm('Hapus challenge ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="challenges-btn danger">Hapus</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6">
                                    <div class="challenges-empty">
                                        Belum ada challenge.
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="challenges-pagination">
                {{ $challenges->appends(['section_id' => request('section_id')])->links('pagination::tailwind') }}
            </div>
        </section>
    </div>

    <style>
        .challenges-page {
            max-width: 1200px;
            margin: 0 auto;
        }

        .challenges-hero {
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

        .challenges-kicker {
            margin: 0;
            font-size: 12px;
            letter-spacing: 0.34em;
            text-transform: uppercase;
            color: rgba(255, 228, 236, 0.75);
        }

        .challenges-title {
            margin: 12px 0 0;
            font-size: 42px;
            line-height: 1.15;
            color: #fff;
            font-weight: 700;
        }

        .challenges-copy {
            margin: 14px 0 0;
            max-width: 760px;
            color: rgba(255, 240, 244, 0.76);
            line-height: 1.8;
        }

        .challenges-hero-actions {
            display: flex;
            align-items: flex-end;
            gap: 14px;
            flex-wrap: wrap;
        }

        .challenges-filter-form {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .challenges-filter-label {
            color: rgba(255, 240, 244, 0.76);
            font-size: 13px;
            font-weight: 600;
        }

        .challenges-filter-select {
            min-width: 240px;
            padding: 13px 16px;
            border-radius: 16px;
            border: 1px solid rgba(255, 228, 236, 0.18);
            background: rgba(255, 255, 255, 0.08);
            color: #fff;
        }

        .challenges-filter-select option {
            color: #1f2937;
        }

        .challenges-primary-btn {
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

        .challenges-alert {
            margin-bottom: 16px;
            padding: 14px 18px;
            border-radius: 18px;
            font-weight: 600;
        }

        .challenges-alert.success {
            background: rgba(220, 252, 231, 0.96);
            color: #166534;
        }

        .challenges-alert.error {
            background: rgba(254, 226, 226, 0.96);
            color: #991b1b;
        }

        .challenges-card {
            padding: 24px;
            border-radius: 30px;
        }

        .challenges-card-head {
            display: flex;
            align-items: flex-end;
            justify-content: space-between;
            gap: 16px;
            margin-bottom: 18px;
        }

        .challenges-card-kicker {
            margin: 0;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.3em;
            color: #be185d;
        }

        .challenges-card-title {
            margin: 8px 0 0;
            color: #1f2937;
            font-size: 30px;
            font-weight: 700;
        }

        .challenges-card-note {
            margin: 0;
            color: #64748b;
            font-size: 14px;
        }

        .challenges-table {
            width: 100%;
            border-collapse: collapse;
        }

        .challenges-table thead th {
            padding: 14px 16px;
            text-align: left;
            background: linear-gradient(90deg, #9f1d4f, #d9467a);
            color: #fff;
            font-size: 12px;
            letter-spacing: 0.18em;
            text-transform: uppercase;
        }

        .challenges-table thead th:first-child {
            border-top-left-radius: 18px;
        }

        .challenges-table thead th:last-child {
            border-top-right-radius: 18px;
        }

        .challenges-table tbody td {
            padding: 18px 16px;
            border-bottom: 1px solid #f3e8ef;
            color: #334155;
            vertical-align: middle;
        }

        .challenges-table tbody tr:hover {
            background: #fff7fa;
        }

        .challenges-pill {
            display: inline-flex;
            align-items: center;
            padding: 8px 12px;
            border-radius: 999px;
            background: #fff1f6;
            color: #be185d;
            font-weight: 700;
            font-size: 13px;
        }

        .challenges-pill.soft {
            background: #f5f3ff;
            color: #7c3aed;
        }

        .challenges-name-wrap {
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        .challenges-name-wrap strong {
            color: #1f2937;
            font-size: 16px;
        }

        .challenges-name-wrap span {
            color: #94a3b8;
            font-size: 13px;
        }

        .challenges-metric {
            font-weight: 700;
            color: #4a1327;
        }

        .challenges-actions {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 8px;
        }

        .challenges-btn {
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

        .challenges-btn.warn {
            background: #f59e0b;
        }

        .challenges-btn.accent {
            background: #a21caf;
        }

        .challenges-btn.danger {
            background: #ef4444;
        }

        .challenges-empty {
            padding: 24px;
            text-align: center;
            color: #64748b;
        }

        .challenges-pagination {
            margin-top: 18px;
        }

        @media (max-width: 768px) {
            .challenges-hero,
            .challenges-card-head {
                flex-direction: column;
                align-items: stretch;
            }

            .challenges-title {
                font-size: 32px;
            }

            .challenges-card {
                padding: 18px;
            }

            .challenges-filter-select {
                min-width: 100%;
            }
        }
    </style>
@endsection
