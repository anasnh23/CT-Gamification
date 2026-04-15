@extends('lecturer.layouts.app')

@section('content')
    <div class="students-page">
        <section class="students-hero">
            <div>
                <p class="students-kicker">Students</p>
                <h1 class="students-title">Lihat progres mahasiswa</h1>
            </div>

            <div class="students-hero-actions">
                <input type="text" id="searchInput" placeholder="Cari nama atau rank..." class="students-search-input" onkeyup="filterTable()">
                <a href="{{ route('lecturer.students.create') }}" class="students-primary-btn">Tambah Mahasiswa</a>
            </div>
        </section>

        @if (session('success'))
            <div class="students-alert success">{{ session('success') }}</div>
        @endif

        <section class="students-card bg-white">
            <div class="students-card-head">
                <div>
                    <p class="students-card-kicker">Daftar</p>
                    <h2 class="students-card-title">Progress mahasiswa aktif</h2>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="students-table">
                    <thead>
                        <tr>
                            <th>Mahasiswa</th>
                            <th>Rank</th>
                            <th>EXP</th>
                            <th>Streak</th>
                            <th>Weekly Score</th>
                            <th>Total Score</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($students as $student)
                            <tr class="student-row">
                                <td class="student-name">
                                    <div class="students-name-wrap">
                                        <strong>{{ $student->user->name }}</strong>
                                        <span>{{ $student->nim }}</span>
                                    </div>
                                </td>
                                <td class="student-level">
                                    <span class="students-pill rank">{{ $student->current_rank?->name ?? 'Belum ada rank' }}</span>
                                </td>
                                <td><span class="students-pill metric">{{ $student->exp }} EXP</span></td>
                                <td><span class="students-pill warm">{{ $student->streak }} hari</span></td>
                                <td class="students-metric">{{ $student->weekly_score }}</td>
                                <td class="students-metric">{{ $student->total_score }}</td>
                                <td>
                                    <div class="students-actions">
                                        <a href="{{ route('lecturer.students.show', $student->id) }}" class="students-btn warn">Detail</a>
                                        <a href="{{ route('lecturer.students.edit', $student->id) }}" class="students-btn secondary">Edit</a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr id="noDataRow">
                                <td colspan="7">
                                    <div class="students-empty">Belum ada mahasiswa pada daftar ini.</div>
                                </td>
                            </tr>
                        @endforelse
                        <tr id="noResultsRow" style="display: none;">
                            <td colspan="7">
                                <div class="students-empty">Tidak ada mahasiswa yang cocok dengan pencarian.</div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="students-pagination">
                {{ $students->links('pagination::tailwind') }}
            </div>
        </section>
    </div>

    <style>
        .students-page { max-width: 1200px; margin: 0 auto; }
        .students-hero { display:flex; justify-content:space-between; align-items:flex-end; gap:20px; margin-bottom:24px; padding:28px; border-radius:30px; border:1px solid rgba(255,228,236,.14); background:rgba(74,19,39,.78); box-shadow:0 20px 50px rgba(0,0,0,.22); }
        .students-kicker { margin:0; font-size:12px; letter-spacing:.34em; text-transform:uppercase; color:rgba(255,228,236,.75); }
        .students-title { margin:12px 0 0; font-size:42px; line-height:1.15; color:#fff; font-weight:700; }
        .students-copy { margin:14px 0 0; max-width:760px; color:rgba(255,240,244,.76); line-height:1.8; }
        .students-hero-actions { display:flex; align-items:flex-end; gap:14px; flex-wrap:wrap; }
        .students-search-input { min-width:280px; padding:13px 16px; border-radius:16px; border:1px solid rgba(255,228,236,.18); background:rgba(255,255,255,.08); color:#fff; }
        .students-search-input::placeholder { color:rgba(255,240,244,.62); }
        .students-primary-btn { display:inline-flex; align-items:center; justify-content:center; padding:14px 20px; border-radius:18px; background:linear-gradient(90deg,#c0265f,#ec4899); color:#fff; font-weight:700; text-decoration:none; white-space:nowrap; box-shadow:0 16px 30px rgba(190,24,93,.25); }
        .students-alert { margin-bottom:16px; padding:14px 18px; border-radius:18px; font-weight:600; }
        .students-alert.success { background:rgba(220,252,231,.96); color:#166534; }
        .students-card { padding:24px; border-radius:30px; }
        .students-card-head { margin-bottom:18px; }
        .students-card-kicker { margin:0; font-size:12px; text-transform:uppercase; letter-spacing:.3em; color:#be185d; }
        .students-card-title { margin:8px 0 0; color:#1f2937; font-size:30px; font-weight:700; }
        .students-table { width:100%; border-collapse:collapse; }
        .students-table thead th { padding:14px 16px; text-align:left; background:linear-gradient(90deg,#9f1d4f,#d9467a); color:#fff; font-size:12px; letter-spacing:.18em; text-transform:uppercase; }
        .students-table thead th:first-child { border-top-left-radius:18px; }
        .students-table thead th:last-child { border-top-right-radius:18px; }
        .students-table tbody td { padding:18px 16px; border-bottom:1px solid #f3e8ef; color:#334155; vertical-align:middle; }
        .students-table tbody tr:hover { background:#fff7fa; }
        .students-name-wrap { display:flex; flex-direction:column; gap:6px; }
        .students-name-wrap strong { color:#1f2937; font-size:16px; }
        .students-name-wrap span { color:#94a3b8; font-size:13px; }
        .students-pill { display:inline-flex; align-items:center; padding:8px 12px; border-radius:999px; font-weight:700; font-size:12px; }
        .students-pill.rank { background:#fff1f6; color:#be185d; }
        .students-pill.metric { background:#fdf4ff; color:#a21caf; }
        .students-pill.warm { background:#fff7ed; color:#c2410c; }
        .students-metric { font-weight:700; color:#4a1327; }
        .students-actions { display:flex; justify-content:center; gap:8px; }
        .students-btn { display:inline-flex; align-items:center; justify-content:center; padding:10px 14px; border-radius:14px; border:0; text-decoration:none; color:#fff; font-weight:700; cursor:pointer; }
        .students-btn.warn { background:#f59e0b; }
        .students-btn.secondary { background:#7c3aed; }
        .students-empty { padding:24px; text-align:center; color:#64748b; }
        .students-pagination { margin-top:18px; }
        @media (max-width:768px) {
            .students-hero { flex-direction:column; align-items:stretch; }
            .students-title { font-size:32px; }
            .students-search-input { min-width:100%; }
            .students-card { padding:18px; }
        }
    </style>

    <script>
        function filterTable() {
            const input = document.getElementById("searchInput");
            const filter = input.value.toLowerCase();
            const rows = document.querySelectorAll(".student-row");
            const noResultsRow = document.getElementById("noResultsRow");
            let anyVisible = false;

            rows.forEach(row => {
                const name = row.querySelector(".student-name")?.textContent.toLowerCase() || '';
                const level = row.querySelector(".student-level")?.textContent.toLowerCase() || '';
                const isMatch = name.includes(filter) || level.includes(filter);
                row.style.display = isMatch ? "" : "none";
                if (isMatch) anyVisible = true;
            });

            if (noResultsRow) noResultsRow.style.display = anyVisible ? "none" : "";
        }
    </script>
@endsection
