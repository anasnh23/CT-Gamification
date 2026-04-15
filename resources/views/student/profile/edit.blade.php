@extends('student.layouts.app')

@section('content')
    <div class="max-w-5xl mx-auto px-4 py-10">
        <div class="rounded-[30px] border border-pink-200/25 bg-[#4a1327] p-8 text-white shadow-2xl">
            <div class="flex flex-col gap-3 md:flex-row md:items-end md:justify-between">
                <div>
                    <p class="text-sm font-semibold uppercase tracking-[0.25em] text-pink-200/80">Edit Profil</p>
                    <h1 class="mt-2 text-3xl font-bold">Perbarui Data Mahasiswa</h1>
                    <p class="mt-2 text-rose-100/80">Lengkapi data diri agar profil mahasiswa terlihat rapi dan informatif.</p>
                </div>

                <a href="{{ route('student.profile.index') }}"
                    class="inline-flex items-center rounded-2xl border border-white/15 bg-white/10 px-5 py-3 text-sm font-semibold text-white transition hover:bg-white/15">
                    Kembali ke Profil
                </a>
            </div>

            @if ($errors->any())
                <div class="mt-6 rounded-2xl border border-rose-300/40 bg-rose-950/30 px-5 py-4 text-rose-100">
                    <p class="font-semibold">Ada data yang perlu diperbaiki:</p>
                    <ul class="mt-2 space-y-1 text-sm">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('student.profile.update') }}" method="POST" enctype="multipart/form-data" class="mt-8">
                @csrf
                @method('PUT')

                <div class="grid gap-8 lg:grid-cols-[320px_1fr]">
                    <aside class="rounded-[26px] border border-white/10 bg-white/10 p-6">
                        <div class="flex flex-col items-center text-center">
                            <img id="profile-preview"
                                src="{{ asset('storage/' . ($student->user->profile_photo ?? 'profile_photos/default.webp')) }}"
                                alt="Foto profil"
                                class="h-36 w-36 rounded-full border-4 border-pink-200 object-cover shadow-lg">

                            <h2 class="mt-5 text-xl font-bold">{{ $user->name }}</h2>
                            <p class="mt-1 text-sm text-rose-100/75">{{ $user->email }}</p>

                            <div class="mt-6 flex flex-wrap justify-center gap-3">
                                <label for="profile_photo"
                                    class="cursor-pointer rounded-2xl bg-pink-600 px-4 py-2 font-semibold text-white transition hover:bg-pink-500">
                                    Ganti Foto
                                </label>

                                @if (($student->user->profile_photo ?? 'profile_photos/default.webp') !== 'profile_photos/default.webp')
                                    <button type="button" onclick="deleteProfilePhoto()"
                                        class="rounded-2xl border border-white/15 bg-white/10 px-4 py-2 font-semibold text-white transition hover:bg-white/15">
                                        Hapus Foto
                                    </button>
                                @endif
                            </div>

                            <input type="file" id="profile_photo" name="profile_photo" accept="image/*" class="hidden">
                            <input type="hidden" id="delete_photo" name="delete_photo" value="0">

                            <div class="mt-6 w-full rounded-2xl bg-black/10 px-4 py-4 text-left">
                                <p class="text-xs uppercase tracking-[0.2em] text-pink-200/70">Data Tetap</p>
                                <div class="mt-3 space-y-3 text-sm">
                                    <div>
                                        <p class="text-rose-100/65">NIM</p>
                                        <p class="font-semibold text-white">{{ $student->nim ?: '-' }}</p>
                                    </div>
                                    <div>
                                        <p class="text-rose-100/65">Nama</p>
                                        <p class="font-semibold text-white">{{ $user->name }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </aside>

                    <div class="rounded-[26px] border border-white/10 bg-white/10 p-6">
                        <div class="grid gap-5 md:grid-cols-2">
                            <div>
                                <label class="mb-2 block text-sm font-semibold text-pink-100">Alamat</label>
                                <input type="text" name="address" value="{{ old('address', $student->address) }}"
                                    class="w-full rounded-2xl border border-white/15 bg-white/10 px-4 py-3 text-white placeholder:text-rose-100/45 focus:border-pink-300 focus:outline-none">
                            </div>

                            <div>
                                <label class="mb-2 block text-sm font-semibold text-pink-100">Tanggal Lahir</label>
                                <input type="date" name="birth_date" value="{{ old('birth_date', $student->birth_date) }}"
                                    class="w-full rounded-2xl border border-white/15 bg-white/10 px-4 py-3 text-white focus:border-pink-300 focus:outline-none">
                            </div>

                            <div>
                                <label class="mb-2 block text-sm font-semibold text-pink-100">Agama</label>
                                <select name="religion"
                                    class="w-full rounded-2xl border border-white/15 bg-[#5a1830] px-4 py-3 text-white focus:border-pink-300 focus:outline-none">
                                    @foreach (['Islam', 'Protestan', 'Katolik', 'Hindu', 'Buddha', 'Konghucu', 'Lainnya'] as $religion)
                                        <option value="{{ $religion }}"
                                            {{ old('religion', $student->religion) == $religion ? 'selected' : '' }}>
                                            {{ $religion }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="mb-2 block text-sm font-semibold text-pink-100">Jenis Kelamin</label>
                                <select name="gender"
                                    class="w-full rounded-2xl border border-white/15 bg-[#5a1830] px-4 py-3 text-white focus:border-pink-300 focus:outline-none">
                                    <option value="Laki-laki" {{ old('gender', $student->gender) == 'Laki-laki' ? 'selected' : '' }}>
                                        Laki-laki
                                    </option>
                                    <option value="Perempuan" {{ old('gender', $student->gender) == 'Perempuan' ? 'selected' : '' }}>
                                        Perempuan
                                    </option>
                                </select>
                            </div>

                            <div>
                                <label class="mb-2 block text-sm font-semibold text-pink-100">Nomor Telepon</label>
                                <input type="text" name="phone_number" value="{{ old('phone_number', $student->phone_number) }}"
                                    class="w-full rounded-2xl border border-white/15 bg-white/10 px-4 py-3 text-white placeholder:text-rose-100/45 focus:border-pink-300 focus:outline-none">
                            </div>

                            <div>
                                <label class="mb-2 block text-sm font-semibold text-pink-100">Program Studi</label>
                                <select name="prodi"
                                    class="w-full rounded-2xl border border-white/15 bg-[#5a1830] px-4 py-3 text-white focus:border-pink-300 focus:outline-none">
                                    <option value="Sistem Informasi Bisnis"
                                        {{ old('prodi', $student->prodi) == 'Sistem Informasi Bisnis' ? 'selected' : '' }}>
                                        Sistem Informasi Bisnis
                                    </option>
                                    <option value="Teknik Informatika"
                                        {{ old('prodi', $student->prodi) == 'Teknik Informatika' ? 'selected' : '' }}>
                                        Teknik Informatika
                                    </option>
                                </select>
                            </div>

                            <div>
                                <label class="mb-2 block text-sm font-semibold text-pink-100">Semester</label>
                                <select name="semester"
                                    class="w-full rounded-2xl border border-white/15 bg-[#5a1830] px-4 py-3 text-white focus:border-pink-300 focus:outline-none">
                                    @for ($i = 1; $i <= 8; $i++)
                                        <option value="{{ $i }}" {{ old('semester', $student->semester) == $i ? 'selected' : '' }}>
                                            Semester {{ $i }}
                                        </option>
                                    @endfor
                                </select>
                            </div>
                        </div>

                        <div class="mt-8 flex flex-wrap gap-3">
                            <button type="submit"
                                class="rounded-2xl bg-gradient-to-r from-pink-600 to-rose-500 px-6 py-3 font-semibold text-white shadow-lg transition hover:scale-[1.02] hover:shadow-pink-300/30">
                                Simpan Perubahan
                            </button>
                            <a href="{{ route('student.profile.index') }}"
                                class="rounded-2xl border border-white/15 bg-white/10 px-6 py-3 font-semibold text-white transition hover:bg-white/15">
                                Batal
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.getElementById('profile_photo').addEventListener('change', function(event) {
            const file = event.target.files[0];
            if (!file) {
                return;
            }

            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('profile-preview').src = e.target.result;
                document.getElementById('delete_photo').value = "0";
            };
            reader.readAsDataURL(file);
        });

        function deleteProfilePhoto() {
            document.getElementById('delete_photo').value = "1";
            document.getElementById('profile-preview').src = "{{ asset('storage/profile_photos/default.webp') }}";
        }
    </script>
@endsection
