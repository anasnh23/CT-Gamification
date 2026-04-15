@extends('student.layouts.app')

@section('content')
    <div class="animate-fadeIn">
        <div class="max-w-6xl mx-auto px-4 py-10">
            @if (session('success'))
                <div class="mb-6 rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-emerald-700 shadow-sm">
                    {{ session('success') }}
                </div>
            @endif

            <div class="grid gap-6 lg:grid-cols-[1.45fr_1fr]">
                <section class="rounded-[28px] border border-rose-200/40 bg-[#fff8f8] p-6 text-slate-900 shadow-xl">
                    <div class="flex flex-col gap-6 md:flex-row md:items-center">
                        <div class="shrink-0">
                            <img src="{{ asset('storage/' . ($user->profile_photo ?? 'profile_photos/default.webp')) }}"
                                alt="Foto profil"
                                class="h-28 w-28 rounded-full border-4 border-pink-200 object-cover shadow-lg">
                        </div>

                        <div class="flex-1">
                            <p class="text-sm font-semibold uppercase tracking-[0.25em] text-rose-500">Profil Mahasiswa</p>
                            <h1 class="mt-2 text-3xl font-bold">{{ $user->name }}</h1>
                            <p class="mt-2 text-slate-500">{{ $user->email }}</p>

                            <div class="mt-4 grid gap-3 sm:grid-cols-2">
                                <div class="rounded-2xl bg-rose-50 px-4 py-3">
                                    <p class="text-xs uppercase tracking-[0.2em] text-rose-400">NIM</p>
                                    <p class="mt-1 font-semibold text-slate-800">{{ $student->nim ?: '-' }}</p>
                                </div>
                                <div class="rounded-2xl bg-sky-50 px-4 py-3">
                                    <p class="text-xs uppercase tracking-[0.2em] text-sky-500">Peringkat Mingguan</p>
                                    <p class="mt-1 font-semibold text-slate-800">#{{ $weeklyRank }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="shrink-0">
                            <a href="{{ route('student.profile.edit') }}"
                                class="inline-flex items-center rounded-2xl bg-gradient-to-r from-pink-600 to-rose-500 px-5 py-3 font-semibold text-white shadow-lg transition hover:scale-[1.02] hover:shadow-pink-300/30">
                                Edit Profil
                            </a>
                        </div>
                    </div>
                </section>

                <section class="rounded-[28px] border border-pink-200/25 bg-[#4a1327] p-6 text-white shadow-xl">
                    <p class="text-sm font-semibold uppercase tracking-[0.25em] text-pink-200/80">Rank dan EXP</p>
                    <h2 class="mt-2 text-2xl font-bold">{{ $currentRank?->name ?? 'Belum Ada Rank' }}</h2>
                    <p class="mt-2 text-sm text-rose-100/80">
                        EXP {{ number_format($student->exp) }}
                        @if ($currentRank)
                            / {{ number_format($currentRank->max_exp) }}
                        @endif
                    </p>

                    <div class="mt-5 h-3 overflow-hidden rounded-full bg-white/15">
                        <div class="h-3 rounded-full bg-gradient-to-r from-amber-300 via-pink-300 to-rose-300"
                            style="width: {{ $expProgress }}%"></div>
                    </div>

                    <div class="mt-6 grid grid-cols-2 gap-4">
                        <div class="rounded-2xl bg-white/10 px-4 py-4">
                            <p class="text-xs uppercase tracking-[0.2em] text-pink-100/70">Total Score</p>
                            <p class="mt-2 text-2xl font-bold">{{ number_format($student->total_score) }}</p>
                        </div>
                        <div class="rounded-2xl bg-white/10 px-4 py-4">
                            <p class="text-xs uppercase tracking-[0.2em] text-pink-100/70">Streak</p>
                            <p class="mt-2 text-2xl font-bold">{{ $student->streak }} hari</p>
                        </div>
                    </div>
                </section>
            </div>

            <div class="mt-6 grid gap-6 xl:grid-cols-[1.25fr_1fr]">
                <div class="space-y-6">
                    <section class="rounded-[28px] border border-rose-200/40 bg-[#fff8f8] p-6 text-slate-900 shadow-xl">
                        <div class="flex items-center justify-between gap-4">
                            <div>
                                <p class="text-sm font-semibold uppercase tracking-[0.25em] text-rose-500">Ringkasan Akademik</p>
                                <h2 class="mt-2 text-2xl font-bold">Informasi Mahasiswa</h2>
                            </div>
                        </div>

                        <div class="mt-6 grid gap-4 md:grid-cols-2 xl:grid-cols-3">
                            <div class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-4">
                                <p class="text-xs uppercase tracking-[0.2em] text-slate-400">Program Studi</p>
                                <p class="mt-2 font-semibold">{{ $student->prodi ?: '-' }}</p>
                            </div>
                            <div class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-4">
                                <p class="text-xs uppercase tracking-[0.2em] text-slate-400">Semester</p>
                                <p class="mt-2 font-semibold">{{ $student->semester ?: '-' }}</p>
                            </div>
                            <div class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-4">
                                <p class="text-xs uppercase tracking-[0.2em] text-slate-400">Kelas</p>
                                <p class="mt-2 font-semibold">{{ $student->class ?: '-' }}</p>
                            </div>
                            <div class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-4">
                                <p class="text-xs uppercase tracking-[0.2em] text-slate-400">No. Telepon</p>
                                <p class="mt-2 font-semibold">{{ $student->phone_number ?: '-' }}</p>
                            </div>
                            <div class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-4">
                                <p class="text-xs uppercase tracking-[0.2em] text-slate-400">Agama</p>
                                <p class="mt-2 font-semibold">{{ $student->religion ?: '-' }}</p>
                            </div>
                            <div class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-4">
                                <p class="text-xs uppercase tracking-[0.2em] text-slate-400">Jenis Kelamin</p>
                                <p class="mt-2 font-semibold">{{ $student->gender ?: '-' }}</p>
                            </div>
                        </div>

                        <div class="mt-4 rounded-2xl border border-slate-200 bg-slate-50 px-4 py-4">
                            <p class="text-xs uppercase tracking-[0.2em] text-slate-400">Alamat</p>
                            <p class="mt-2 leading-7 text-slate-700">{{ $student->address ?: 'Alamat belum diisi.' }}</p>
                        </div>
                    </section>

                    <section class="rounded-[28px] border border-rose-200/40 bg-[#fff8f8] p-6 text-slate-900 shadow-xl">
                        <p class="text-sm font-semibold uppercase tracking-[0.25em] text-rose-500">Progres Belajar</p>
                        <h2 class="mt-2 text-2xl font-bold">Status Misi Saat Ini</h2>

                        <div class="mt-6 grid gap-4 md:grid-cols-2">
                            <div class="rounded-2xl bg-gradient-to-br from-rose-50 to-pink-50 p-5 border border-rose-200">
                                <p class="text-xs uppercase tracking-[0.2em] text-rose-400">Section Aktif</p>
                                <p class="mt-2 text-xl font-bold">
                                    {{ $student->currentSection?->name ? 'Section ' . $student->currentSection->order . ' - ' . $student->currentSection->name : 'Belum ada section aktif' }}
                                </p>
                            </div>
                            <div class="rounded-2xl bg-gradient-to-br from-sky-50 to-blue-50 p-5 border border-sky-200">
                                <p class="text-xs uppercase tracking-[0.2em] text-sky-500">Mission Aktif</p>
                                <p class="mt-2 text-xl font-bold">
                                    {{ $currentChallenge?->title ?? 'Belum ada mission aktif' }}
                                </p>
                                <p class="mt-2 text-sm text-slate-500">
                                    {{ $currentChallenge?->section?->name ? 'Masih berada di ' . $currentChallenge->section->name : 'Pilih mission dari halaman missions untuk mulai belajar.' }}
                                </p>
                            </div>
                        </div>

                        <div class="mt-4 grid gap-4 sm:grid-cols-3">
                            <div class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-4">
                                <p class="text-xs uppercase tracking-[0.2em] text-slate-400">Mission Selesai</p>
                                <p class="mt-2 text-2xl font-bold">{{ $completedChallengesCount }} / {{ $totalChallengesCount }}</p>
                            </div>
                            <div class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-4">
                                <p class="text-xs uppercase tracking-[0.2em] text-slate-400">Section Terbuka</p>
                                <p class="mt-2 text-2xl font-bold">{{ $unlockedSectionsCount }}</p>
                            </div>
                            <div class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-4">
                                <p class="text-xs uppercase tracking-[0.2em] text-slate-400">Section Tuntas</p>
                                <p class="mt-2 text-2xl font-bold">{{ $completedSectionsCount }}</p>
                            </div>
                        </div>
                    </section>

                    <section class="rounded-[28px] border border-rose-200/40 bg-[#fff8f8] p-6 text-slate-900 shadow-xl">
                        <div class="flex items-center justify-between gap-4">
                            <div>
                                <p class="text-sm font-semibold uppercase tracking-[0.25em] text-rose-500">Achievements</p>
                                <h2 class="mt-2 text-2xl font-bold">Pencapaian Belajar</h2>
                            </div>
                            <div class="rounded-2xl bg-rose-50 px-4 py-3 text-right">
                                <p class="text-xs uppercase tracking-[0.2em] text-rose-400">Terbuka</p>
                                <p class="mt-1 font-semibold">{{ count($unlockedAchievementIds) }} / {{ $allAchievements->count() }}</p>
                            </div>
                        </div>

                        <div class="mt-6 grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-4">
                            @foreach ($allAchievements as $achievement)
                                @php
                                    $pivotData = $student->achievements->firstWhere('id', $achievement->id)?->pivot;
                                    $isUnlocked = !is_null($pivotData);
                                    $unlockedAt = $pivotData?->unlocked_at
                                        ? \Carbon\Carbon::parse($pivotData->unlocked_at)->translatedFormat('d F Y, H:i')
                                        : null;
                                @endphp

                                <button type="button"
                                    onclick='showModal(@json(asset("storage/" . $achievement->icon)), @json($achievement->name), @json($achievement->description), {{ $isUnlocked ? "true" : "false" }}, @json($unlockedAt ?? ""))'
                                    class="rounded-2xl border p-4 text-center transition hover:scale-[1.03] {{ $isUnlocked ? 'border-pink-200 bg-gradient-to-br from-white to-rose-50 shadow-sm' : 'border-slate-200 bg-slate-100 opacity-60 grayscale' }}">
                                    <img src="{{ asset('storage/' . $achievement->icon) }}" alt="{{ $achievement->name }}"
                                        class="mx-auto h-16 w-16 object-contain">
                                    <p class="mt-3 text-sm font-semibold text-slate-700">{{ $achievement->name }}</p>
                                </button>
                            @endforeach
                        </div>
                    </section>
                </div>

                <section class="rounded-[28px] border border-pink-200/25 bg-[#4a1327] p-6 text-white shadow-xl">
                    <p class="text-sm font-semibold uppercase tracking-[0.25em] text-pink-200/80">Leaderboard</p>
                    <h2 class="mt-2 text-2xl font-bold">Papan Skor Mingguan</h2>
                    <p class="mt-2 text-sm text-rose-100/75">Posisi ditentukan dari weekly score mahasiswa.</p>

                    <div class="mt-6 space-y-3">
                        @foreach ($leaderboard as $index => $entry)
                            @php
                                $isCurrentUser = $entry->name === $user->name;
                            @endphp
                            <div
                                class="flex items-center gap-3 rounded-2xl border px-4 py-3 {{ $isCurrentUser ? 'border-amber-300/70 bg-amber-100 text-slate-900' : 'border-white/10 bg-white/10 text-white' }}">
                                <div class="w-8 text-center text-sm font-bold">{{ $index + 1 }}</div>
                                <img src="{{ asset('storage/' . $entry->profile_photo) }}" alt="{{ $entry->name }}"
                                    class="h-11 w-11 rounded-full border border-white/30 object-cover">
                                <div class="min-w-0 flex-1">
                                    <p class="truncate font-semibold">{{ $entry->name }}</p>
                                    <p class="text-xs {{ $isCurrentUser ? 'text-slate-600' : 'text-rose-100/70' }}">
                                        {{ $isCurrentUser ? 'Anda' : 'Mahasiswa' }}
                                    </p>
                                </div>
                                <div class="text-right">
                                    <p class="text-sm font-bold">{{ number_format($entry->weekly_score) }}</p>
                                    <p class="text-xs {{ $isCurrentUser ? 'text-slate-600' : 'text-rose-100/70' }}">pts</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </section>
            </div>
        </div>
    </div>

    <div id="achievementModal"
        class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 px-4">
        <div class="relative w-full max-w-md rounded-[28px] border border-pink-200/35 bg-[#fff8f8] p-6 text-slate-900 shadow-2xl">
            <button type="button" onclick="closeModal()"
                class="absolute right-4 top-4 flex h-9 w-9 items-center justify-center rounded-full bg-rose-100 text-rose-600 transition hover:bg-rose-200">
                &times;
            </button>

            <div class="flex justify-center">
                <div class="flex h-24 w-24 items-center justify-center rounded-full border-4 border-pink-200 bg-white shadow-inner">
                    <img id="modalIcon" src="" alt="Achievement Icon" class="h-16 w-16 object-contain">
                </div>
            </div>

            <h3 id="modalName" class="mt-5 text-center text-2xl font-bold"></h3>
            <p id="modalDescription" class="mt-3 text-center leading-7 text-slate-600"></p>
            <div id="modalStatus"
                class="mx-auto mt-5 inline-flex rounded-full bg-rose-100 px-4 py-2 text-sm font-semibold text-rose-700">
            </div>
        </div>
    </div>

    <audio id="hover-sound" src="{{ asset('sfx/hover.mp3') }}"></audio>
    <audio id="click-sound" src="{{ asset('sfx/click.mp3') }}"></audio>

    <script>
        function showModal(icon, name, description, isUnlocked, unlockedAt) {
            document.getElementById('modalIcon').src = icon;
            document.getElementById('modalName').innerText = name;
            document.getElementById('modalDescription').innerText = description;
            document.getElementById('modalStatus').innerText = isUnlocked ?
                `Dibuka pada: ${unlockedAt}` :
                'Belum terbuka';

            const modal = document.getElementById('achievementModal');
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }

        function closeModal() {
            const modal = document.getElementById('achievementModal');
            modal.classList.remove('flex');
            modal.classList.add('hidden');
        }

        function playHoverSound() {
            const audio = document.getElementById("hover-sound");
            if (audio) {
                audio.currentTime = 0;
                audio.play();
            }
        }

        document.addEventListener("DOMContentLoaded", function() {
            document.querySelectorAll(".hover-sfx").forEach((el) => {
                el.addEventListener("mouseenter", playHoverSound);
            });
        });
    </script>
@endsection
