@extends('admin.layouts.app')

@section('content')
    <div class="admin-form-page">
        <section class="admin-form-hero admin-surface">
            <p class="admin-form-kicker">Users</p>
            <h1 class="admin-form-title">Buat akun baru dengan role yang sesuai</h1>
        </section>

        @if ($errors->any())
            <div class="admin-form-alert">
                <strong>Data belum bisa disimpan.</strong>
                <ul>@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
            </div>
        @endif

        <form action="{{ route('admin.users.store') }}" method="POST" enctype="multipart/form-data" class="admin-card-white admin-form-card">
            @csrf
            <div class="admin-form-grid two">
                <div><label>Nama</label><input type="text" name="name" value="{{ old('name') }}" required></div>
                <div><label>Email</label><input type="email" name="email" value="{{ old('email') }}" required></div>
            </div>

            <div class="admin-form-grid two">
                <div><label>Password</label><input type="password" name="password" id="password" required></div>
                <div><label>Konfirmasi Password</label><input type="password" name="password_confirmation" id="password_confirmation" required></div>
            </div>

            <div class="admin-form-grid two">
                <div>
                    <label>Role</label>
                    <select name="role" id="role" onchange="toggleStudentFields()" required>
                        <option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>Admin</option>
                        <option value="lecturer" {{ old('role') === 'lecturer' ? 'selected' : '' }}>Lecturer</option>
                        <option value="student" {{ old('role') === 'student' ? 'selected' : '' }}>Student</option>
                    </select>
                </div>
                <div><label>Foto Profil</label><input type="file" name="profile_photo" accept="image/*"></div>
            </div>

            <div id="studentFields" class="admin-form-student" style="display:none;">
                <h2>Data Mahasiswa</h2>
                <div class="admin-form-grid two">
                    <div><label>NIM</label><input type="text" name="nim" value="{{ old('nim') }}"></div>
                    <div><label>No. Telepon</label><input type="text" name="phone_number" value="{{ old('phone_number') }}"></div>
                </div>
                <div class="admin-form-grid two">
                    <div><label>Tanggal Lahir</label><input type="date" name="birth_date" value="{{ old('birth_date') }}"></div>
                    <div>
                        <label>Agama</label>
                        <select name="religion">
                            @foreach (['Islam','Protestan','Katolik','Hindu','Buddha','Konghucu','Lainnya'] as $religion)
                                <option value="{{ $religion }}" {{ old('religion') === $religion ? 'selected' : '' }}>{{ $religion }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="admin-form-grid two">
                    <div>
                        <label>Jenis Kelamin</label>
                        <select name="gender">
                            <option value="Laki-laki" {{ old('gender') === 'Laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                            <option value="Perempuan" {{ old('gender') === 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                        </select>
                    </div>
                    <div><label>Alamat</label><input type="text" name="address" value="{{ old('address') }}"></div>
                </div>
                <div class="admin-form-grid three">
                    <div>
                        <label>Program Studi</label>
                        <select name="prodi">
                            <option value="Sistem Informasi Bisnis" {{ old('prodi') === 'Sistem Informasi Bisnis' ? 'selected' : '' }}>Sistem Informasi Bisnis</option>
                            <option value="Teknik Informatika" {{ old('prodi') === 'Teknik Informatika' ? 'selected' : '' }}>Teknik Informatika</option>
                        </select>
                    </div>
                    <div><label>Semester</label><input type="number" min="1" max="8" name="semester" value="{{ old('semester') }}"></div>
                    <div><label>Kelas</label><input type="text" name="class" value="{{ old('class') }}"></div>
                </div>
            </div>

            <div class="admin-form-actions">
                <a href="{{ route('admin.users.index') }}" class="btn-neutral">Kembali</a>
                <button type="submit" class="btn-primary">Simpan User</button>
            </div>
        </form>
    </div>

    <style>
        .admin-form-page { max-width: 1040px; margin: 0 auto; }
        .admin-form-hero { padding:28px; border-radius:30px; margin-bottom:24px; }
        .admin-form-kicker { margin:0; font-size:12px; letter-spacing:.32em; text-transform:uppercase; color:rgba(255,236,242,.72); }
        .admin-form-title { margin:12px 0 0; color:#fff; font-size:40px; }
        .admin-form-copy { margin:14px 0 0; color:rgba(255,236,242,.75); line-height:1.8; }
        .admin-form-alert { margin-bottom:16px; padding:16px 18px; border-radius:18px; background:rgba(254,226,226,.96); color:#991b1b; }
        .admin-form-card { padding:24px; border-radius:30px; }
        .admin-form-grid { display:grid; gap:20px; margin-bottom:20px; }
        .admin-form-grid.two { grid-template-columns:repeat(2,minmax(0,1fr)); }
        .admin-form-grid.three { grid-template-columns:repeat(3,minmax(0,1fr)); }
        .admin-form-card label { display:block; margin-bottom:10px; font-weight:700; color:#334155; }
        .admin-form-card input, .admin-form-card select { width:100%; padding:14px 16px; border-radius:16px; border:1px solid #f0b6c9; background:#fff; color:#1f2937; }
        .admin-form-student { margin-top:10px; padding:22px; border-radius:24px; background:#fff7fa; border:1px solid #f6d3e0; }
        .admin-form-student h2 { margin:0 0 18px; color:#9f1d4f; }
        .admin-form-actions { display:flex; justify-content:space-between; gap:14px; margin-top:24px; }
        .btn-primary, .btn-neutral { display:inline-flex; align-items:center; justify-content:center; padding:14px 18px; border-radius:16px; text-decoration:none; font-weight:700; border:0; cursor:pointer; color:#fff; }
        .btn-primary { background:var(--admin-accent); }
        .btn-neutral { background:#64748b; }
        @media (max-width:768px) { .admin-form-grid.two, .admin-form-grid.three { grid-template-columns:1fr; } .admin-form-actions { flex-direction:column; } .admin-form-title { font-size:32px; } }
    </style>

    <script>
        function toggleStudentFields() {
            const role = document.getElementById('role').value;
            document.getElementById('studentFields').style.display = role === 'student' ? 'block' : 'none';
        }
        toggleStudentFields();
    </script>
@endsection
