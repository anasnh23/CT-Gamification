@extends('admin.layouts.app')

@section('content')
    <div class="admin-users-page">
        <section class="admin-users-hero admin-surface">
            <div>
                <p class="admin-users-kicker">Users</p>
                <h1 class="admin-users-title">Atur akun admin, dosen, dan mahasiswa</h1>
            </div>
            <div class="admin-users-actions">
                <input type="text" id="search" placeholder="Cari nama, email, atau role..." class="admin-users-search" onkeyup="filterUsers()">
                <a href="{{ route('admin.users.create') }}" class="admin-users-primary">Tambah User</a>
            </div>
        </section>

        @if (session('success'))
            <div class="admin-users-alert">{{ session('success') }}</div>
        @endif

        <section class="admin-card-white admin-users-card">
            <div class="admin-users-head">
                <div>
                    <p class="admin-users-head-kicker">Daftar</p>
                    <h2 class="admin-users-head-title">Akun terdaftar</h2>
                </div>
                <form method="GET" action="{{ route('admin.users.index') }}" class="admin-users-perpage">
                    <label for="perPage">Tampilkan</label>
                    <select name="perPage" id="perPage" onchange="this.form.submit()">
                        <option value="10" {{ $perPage == 10 ? 'selected' : '' }}>10</option>
                        <option value="20" {{ $perPage == 20 ? 'selected' : '' }}>20</option>
                        <option value="50" {{ $perPage == 50 ? 'selected' : '' }}>50</option>
                        <option value="100" {{ $perPage == 100 ? 'selected' : '' }}>100</option>
                        <option value="all" {{ $perPage == 'all' ? 'selected' : '' }}>Semua</option>
                    </select>
                </form>
            </div>

            <div class="overflow-x-auto">
                <table class="admin-users-table">
                    <thead>
                        <tr>
                            <th>Profil</th>
                            <th>Nama</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="user-table">
                        @forelse ($users as $user)
                            <tr>
                                <td>
                                    <img src="{{ $user->profile_photo ? asset('storage/' . $user->profile_photo) : asset('images/default-avatar.png') }}"
                                        alt="{{ $user->name }}" class="admin-users-avatar">
                                </td>
                                <td>
                                    <div class="admin-users-name">
                                        <strong>{{ $user->name }}</strong>
                                        <span>{{ $user->student?->nim ?? 'Bukan mahasiswa' }}</span>
                                    </div>
                                </td>
                                <td>{{ $user->email }}</td>
                                <td>
                                    <span class="admin-users-pill">{{ $user->roles->pluck('name')->join(', ') }}</span>
                                </td>
                                <td>
                                    <div class="admin-users-row-actions">
                                        <a href="{{ route('admin.users.show', $user->id) }}" class="row-btn warn">Detail</a>
                                        <a href="{{ route('admin.users.edit', $user->id) }}" class="row-btn secondary">Edit</a>
                                        <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" onsubmit="return confirm('Hapus user ini?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="row-btn danger">Hapus</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr id="noDataRow">
                                <td colspan="5"><div class="admin-users-empty">Belum ada data user.</div></td>
                            </tr>
                        @endforelse
                        <tr id="noResultsRow" style="display:none;">
                            <td colspan="5"><div class="admin-users-empty">Tidak ada user yang cocok dengan pencarian.</div></td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="admin-users-pagination">
                {{ $users->links('pagination::tailwind') }}
            </div>
        </section>
    </div>

    <style>
        .admin-users-page { max-width: 1220px; margin: 0 auto; }
        .admin-users-hero { display:flex; justify-content:space-between; align-items:flex-end; gap:20px; padding:28px; border-radius:30px; margin-bottom:24px; }
        .admin-users-kicker, .admin-users-head-kicker { margin:0; font-size:12px; letter-spacing:.32em; text-transform:uppercase; color:rgba(255,236,242,.72); }
        .admin-users-title { margin:12px 0 0; font-size:42px; line-height:1.15; color:#fff; }
        .admin-users-copy { margin:14px 0 0; max-width:760px; color:rgba(255,236,242,.75); line-height:1.8; }
        .admin-users-actions { display:flex; flex-wrap:wrap; gap:14px; align-items:flex-end; }
        .admin-users-search { min-width:280px; padding:14px 16px; border-radius:16px; border:1px solid rgba(255,228,236,.18); background:rgba(255,255,255,.08); color:#fff; }
        .admin-users-search::placeholder { color:rgba(255,236,242,.58); }
        .admin-users-primary { display:inline-flex; align-items:center; justify-content:center; padding:14px 20px; border-radius:18px; background:var(--admin-accent); color:#fff; text-decoration:none; font-weight:700; }
        .admin-users-alert { margin-bottom:16px; padding:14px 18px; border-radius:18px; background:rgba(220,252,231,.95); color:#166534; font-weight:700; }
        .admin-users-card { padding:24px; border-radius:30px; }
        .admin-users-head { display:flex; justify-content:space-between; align-items:center; gap:18px; margin-bottom:18px; }
        .admin-users-head-title { margin:8px 0 0; font-size:30px; color:#1f2937; }
        .admin-users-perpage { display:flex; align-items:center; gap:10px; color:#64748b; }
        .admin-users-perpage select { padding:10px 12px; border-radius:12px; border:1px solid #e7cad6; color:#1f2937; }
        .admin-users-table { width:100%; border-collapse:collapse; }
        .admin-users-table thead th { padding:14px 16px; text-align:left; background:linear-gradient(90deg,#9f1d4f,#d9467a); color:#fff; font-size:12px; letter-spacing:.18em; text-transform:uppercase; }
        .admin-users-table tbody td { padding:18px 16px; border-bottom:1px solid #f3e8ef; color:#334155; vertical-align:middle; }
        .admin-users-table tbody tr:hover { background:#fff7fa; }
        .admin-users-avatar { width:52px; height:52px; object-fit:cover; border-radius:999px; background:#fff; }
        .admin-users-name { display:flex; flex-direction:column; gap:6px; }
        .admin-users-name strong { color:#1f2937; }
        .admin-users-name span { color:#94a3b8; font-size:13px; }
        .admin-users-pill { display:inline-flex; padding:8px 12px; border-radius:999px; background:#fff1f6; color:#be185d; font-size:12px; font-weight:700; }
        .admin-users-row-actions { display:flex; justify-content:center; gap:8px; flex-wrap:wrap; }
        .row-btn { display:inline-flex; align-items:center; justify-content:center; padding:10px 14px; border-radius:14px; border:0; color:#fff; text-decoration:none; font-weight:700; cursor:pointer; }
        .row-btn.warn { background:#f59e0b; }
        .row-btn.secondary { background:#7c3aed; }
        .row-btn.danger { background:#e11d48; }
        .admin-users-empty { padding:24px; text-align:center; color:#64748b; }
        .admin-users-pagination { margin-top:18px; }
        @media (max-width:768px) {
            .admin-users-hero, .admin-users-head { flex-direction:column; align-items:stretch; }
            .admin-users-title { font-size:32px; }
            .admin-users-search { min-width:100%; }
        }
    </style>

    <script>
        function filterUsers() {
            const input = document.getElementById('search').value.toLowerCase();
            const rows = document.querySelectorAll('#user-table tr');
            const noResultsRow = document.getElementById('noResultsRow');
            let anyVisible = false;

            rows.forEach(row => {
                if (row.id === 'noResultsRow' || row.id === 'noDataRow') return;
                const text = row.textContent.toLowerCase();
                const isMatch = text.includes(input);
                row.style.display = isMatch ? '' : 'none';
                if (isMatch) anyVisible = true;
            });

            if (noResultsRow) noResultsRow.style.display = anyVisible ? 'none' : '';
        }
    </script>
@endsection
