@extends('admin.layouts.app')

@section('content')
    <div class="admin-students-page">
        <section class="admin-students-hero admin-surface">
            <div>
                <p class="admin-students-kicker">Students</p>
                <h1 class="admin-students-title">Pantau dan atur data mahasiswa</h1>
            </div>
            <div class="admin-students-actions">
                <input type="text" id="search" name="search" value="{{ request('search') }}" placeholder="Cari nama, email, program, atau kelas..." class="admin-students-search" onkeyup="filterStudents()">
                <a href="{{ route('admin.students.create') }}" class="admin-students-primary">Tambah Mahasiswa</a>
            </div>
        </section>

        @if (session('success'))
            <div class="admin-students-alert">{{ session('success') }}</div>
        @endif

        <section class="admin-card-white admin-students-card">
            <div class="admin-students-head">
                <div>
                    <p class="admin-students-head-kicker">Daftar</p>
                    <h2 class="admin-students-head-title">Mahasiswa aktif</h2>
                </div>
                <div class="admin-students-toolbar">
                    <a href="{{ route('admin.students.index', ['perPage' => request('perPage')]) }}" class="toolbar-btn reset">Reset</a>
                    <form method="GET" action="{{ route('admin.students.index') }}" class="admin-students-perpage">
                        <label for="perPage">Tampilkan</label>
                        <select name="perPage" id="perPage" onchange="this.form.submit()">
                            <option value="10" {{ request('perPage') == 10 ? 'selected' : '' }}>10</option>
                            <option value="20" {{ request('perPage') == 20 ? 'selected' : '' }}>20</option>
                            <option value="50" {{ request('perPage') == 50 ? 'selected' : '' }}>50</option>
                            <option value="100" {{ request('perPage') == 100 ? 'selected' : '' }}>100</option>
                            <option value="all" {{ request('perPage') == 'all' ? 'selected' : '' }}>Semua</option>
                        </select>
                    </form>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="admin-students-table">
                    <thead>
                        <tr>
                            <th>Profil</th>
                            <th>Mahasiswa</th>
                            <th>Program</th>
                            <th>Kelas</th>
                            <th>Semester</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="students-table-body">
                        @forelse ($students as $student)
                            <tr class="student-row">
                                <td>
                                    <img src="{{ $student->user->profile_photo ? asset('storage/' . $student->user->profile_photo) : asset('images/default-avatar.png') }}"
                                        alt="{{ $student->user->name }}" class="admin-students-avatar">
                                </td>
                                <td>
                                    <div class="admin-students-name">
                                        <strong>{{ $student->user->name }}</strong>
                                        <span>{{ $student->user->email }}</span>
                                    </div>
                                </td>
                                <td>{{ $student->prodi ?? '-' }}</td>
                                <td>{{ $student->class ?? '-' }}</td>
                                <td>{{ $student->semester ?? '-' }}</td>
                                <td>
                                    <div class="admin-students-row-actions">
                                        <a href="{{ route('admin.students.show', $student->id) }}" class="row-btn warn">Detail</a>
                                        <a href="{{ route('admin.students.edit', $student->id) }}" class="row-btn secondary">Edit</a>
                                        <form action="{{ route('admin.students.destroy', $student->id) }}" method="POST" onsubmit="return confirm('Hapus mahasiswa ini?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="row-btn danger">Hapus</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr id="noDataRow">
                                <td colspan="6"><div class="admin-students-empty">Belum ada data mahasiswa.</div></td>
                            </tr>
                        @endforelse
                        <tr id="noResultsRow" style="display:none;">
                            <td colspan="6"><div class="admin-students-empty">Tidak ada mahasiswa yang cocok dengan pencarian.</div></td>
                        </tr>
                    </tbody>
                </table>
            </div>

            @if ($pagination)
                <div class="admin-students-pagination">
                    {{ $students->links('pagination::tailwind') }}
                </div>
            @endif
        </section>
    </div>

    <style>
        .admin-students-page { max-width: 1220px; margin: 0 auto; }
        .admin-students-hero { display:flex; justify-content:space-between; align-items:flex-end; gap:20px; padding:28px; border-radius:30px; margin-bottom:24px; }
        .admin-students-kicker, .admin-students-head-kicker { margin:0; font-size:12px; letter-spacing:.32em; text-transform:uppercase; color:rgba(255,236,242,.72); }
        .admin-students-title { margin:12px 0 0; font-size:42px; line-height:1.15; color:#fff; }
        .admin-students-copy { margin:14px 0 0; max-width:760px; color:rgba(255,236,242,.75); line-height:1.8; }
        .admin-students-actions { display:flex; gap:14px; flex-wrap:wrap; align-items:flex-end; }
        .admin-students-search { min-width:300px; padding:14px 16px; border-radius:16px; border:1px solid rgba(255,228,236,.18); background:rgba(255,255,255,.08); color:#fff; }
        .admin-students-search::placeholder { color:rgba(255,236,242,.58); }
        .admin-students-primary { display:inline-flex; align-items:center; justify-content:center; padding:14px 20px; border-radius:18px; background:var(--admin-accent); color:#fff; text-decoration:none; font-weight:700; }
        .admin-students-alert { margin-bottom:16px; padding:14px 18px; border-radius:18px; background:rgba(220,252,231,.95); color:#166534; font-weight:700; }
        .admin-students-card { padding:24px; border-radius:30px; }
        .admin-students-head { display:flex; justify-content:space-between; align-items:center; gap:18px; margin-bottom:18px; }
        .admin-students-head-title { margin:8px 0 0; font-size:30px; color:#1f2937; }
        .admin-students-toolbar { display:flex; align-items:center; gap:12px; flex-wrap:wrap; }
        .toolbar-btn.reset { display:inline-flex; align-items:center; justify-content:center; padding:12px 14px; border-radius:14px; background:#64748b; color:#fff; text-decoration:none; font-weight:700; }
        .admin-students-perpage { display:flex; align-items:center; gap:10px; color:#64748b; }
        .admin-students-perpage select { padding:10px 12px; border-radius:12px; border:1px solid #e7cad6; color:#1f2937; }
        .admin-students-table { width:100%; border-collapse:collapse; }
        .admin-students-table thead th { padding:14px 16px; text-align:left; background:linear-gradient(90deg,#9f1d4f,#d9467a); color:#fff; font-size:12px; letter-spacing:.18em; text-transform:uppercase; }
        .admin-students-table tbody td { padding:18px 16px; border-bottom:1px solid #f3e8ef; color:#334155; vertical-align:middle; }
        .admin-students-table tbody tr:hover { background:#fff7fa; }
        .admin-students-avatar { width:52px; height:52px; object-fit:cover; border-radius:999px; background:#fff; }
        .admin-students-name { display:flex; flex-direction:column; gap:6px; }
        .admin-students-name strong { color:#1f2937; }
        .admin-students-name span { color:#94a3b8; font-size:13px; }
        .admin-students-row-actions { display:flex; justify-content:center; gap:8px; flex-wrap:wrap; }
        .row-btn { display:inline-flex; align-items:center; justify-content:center; padding:10px 14px; border-radius:14px; border:0; color:#fff; text-decoration:none; font-weight:700; cursor:pointer; }
        .row-btn.warn { background:#f59e0b; }
        .row-btn.secondary { background:#7c3aed; }
        .row-btn.danger { background:#e11d48; }
        .admin-students-empty { padding:24px; text-align:center; color:#64748b; }
        .admin-students-pagination { margin-top:18px; }
        @media (max-width:768px) { .admin-students-hero, .admin-students-head { flex-direction:column; align-items:stretch; } .admin-students-title { font-size:32px; } .admin-students-search { min-width:100%; } }
    </style>

    <script>
        function filterStudents() {
            const input = document.getElementById('search').value.toLowerCase();
            const rows = document.querySelectorAll('.student-row');
            const noResultsRow = document.getElementById('noResultsRow');
            let anyVisible = false;

            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                const isMatch = text.includes(input);
                row.style.display = isMatch ? '' : 'none';
                if (isMatch) anyVisible = true;
            });

            if (noResultsRow) noResultsRow.style.display = anyVisible ? 'none' : '';
        }
    </script>
@endsection
