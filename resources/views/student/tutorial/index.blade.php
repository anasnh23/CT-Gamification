@extends('student.layouts.app')

@section('content')
    <div class="animate-fadeIn">
        <div class="mx-auto max-w-6xl px-4 py-10">
            <div class="grid gap-6 lg:grid-cols-[1.15fr_0.85fr]">
                <section class="rounded-[32px] border border-pink-200/20 bg-[#4a1327] p-8 text-white shadow-2xl">
                    <p class="text-sm font-semibold uppercase tracking-[0.35em] text-pink-200/75">Tutorial Belajar</p>
                    <h1 class="mt-3 text-4xl font-bold leading-tight">Pelajari alur sistem sebelum mulai mengerjakan mission</h1>
                    <p class="mt-4 max-w-2xl text-rose-100/80 leading-7">
                        Tutorial ini dirancang seperti panduan misi. Mahasiswa bisa memahami cara bermain, cara belajar dari
                        bantuan, dan cara membaca progres tanpa harus bingung dengan terlalu banyak aturan sekaligus.
                    </p>

                    <div class="mt-8 grid gap-4 sm:grid-cols-3">
                        <div class="rounded-2xl bg-white/10 p-4">
                            <p class="text-xs uppercase tracking-[0.2em] text-pink-100/70">Langkah 1</p>
                            <p class="mt-2 text-lg font-semibold">Pilih Mission</p>
                            <p class="mt-2 text-sm text-rose-100/75">Mulai dari mission yang terang atau sudah terbuka.</p>
                        </div>
                        <div class="rounded-2xl bg-white/10 p-4">
                            <p class="text-xs uppercase tracking-[0.2em] text-pink-100/70">Langkah 2</p>
                            <p class="mt-2 text-lg font-semibold">Kerjakan Soal</p>
                            <p class="mt-2 text-sm text-rose-100/75">Jawab soal satu per satu dan gunakan bantuan bila perlu.</p>
                        </div>
                        <div class="rounded-2xl bg-white/10 p-4">
                            <p class="text-xs uppercase tracking-[0.2em] text-pink-100/70">Langkah 3</p>
                            <p class="mt-2 text-lg font-semibold">Review Hasil</p>
                            <p class="mt-2 text-sm text-rose-100/75">Pelajari pembahasan agar strategi berpikir makin kuat.</p>
                        </div>
                    </div>
                </section>

                <section class="rounded-[32px] border border-rose-200/40 bg-[#fff8f8] p-7 text-slate-900 shadow-xl">
                    <p class="text-sm font-semibold uppercase tracking-[0.25em] text-rose-500">Peta Panduan</p>
                    <div class="mt-5 space-y-4">
                        <button type="button" onclick="showTutorialTab('mission')"
                            class="tutorial-tab w-full rounded-2xl border border-pink-200 bg-gradient-to-r from-white to-rose-50 px-5 py-4 text-left transition hover:scale-[1.01]"
                            data-tab="mission">
                            <p class="text-sm font-semibold uppercase tracking-[0.2em] text-rose-400">Modul 1</p>
                            <p class="mt-1 text-xl font-bold">Memulai Mission</p>
                            <p class="mt-2 text-sm text-slate-500">Memahami mission, unlock, dan popup detail challenge.</p>
                        </button>

                        <button type="button" onclick="showTutorialTab('question')"
                            class="tutorial-tab w-full rounded-2xl border border-slate-200 bg-white px-5 py-4 text-left transition hover:scale-[1.01]"
                            data-tab="question">
                            <p class="text-sm font-semibold uppercase tracking-[0.2em] text-sky-500">Modul 2</p>
                            <p class="mt-1 text-xl font-bold">Mengerjakan Soal</p>
                            <p class="mt-2 text-sm text-slate-500">Pilihan ganda, esai, bantuan, dan jawab ulang.</p>
                        </button>

                        <button type="button" onclick="showTutorialTab('profile')"
                            class="tutorial-tab w-full rounded-2xl border border-slate-200 bg-white px-5 py-4 text-left transition hover:scale-[1.01]"
                            data-tab="profile">
                            <p class="text-sm font-semibold uppercase tracking-[0.2em] text-amber-500">Modul 3</p>
                            <p class="mt-1 text-xl font-bold">Profil dan Progres</p>
                            <p class="mt-2 text-sm text-slate-500">Cara membaca rank, score, streak, dan achievement.</p>
                        </button>
                    </div>
                </section>
            </div>

            <div class="mt-8 space-y-6">
                <section id="tutorial-mission" class="tutorial-panel rounded-[32px] border border-rose-200/40 bg-[#fff8f8] p-7 text-slate-900 shadow-xl">
                    <div class="flex flex-col gap-5 lg:flex-row lg:items-start lg:justify-between">
                        <div>
                            <p class="text-sm font-semibold uppercase tracking-[0.25em] text-rose-500">Modul 1</p>
                            <h2 class="mt-2 text-3xl font-bold">Cara Memulai Mission</h2>
                            <p class="mt-3 max-w-3xl text-slate-600 leading-7">
                                Halaman mission adalah pusat progres belajar. Di sana mahasiswa melihat section yang sedang
                                terbuka, mission yang sudah selesai, dan mission berikutnya yang siap dikerjakan.
                            </p>
                        </div>
                        <a href="{{ route('student.mission.index') }}"
                            class="inline-flex items-center rounded-2xl bg-gradient-to-r from-pink-600 to-rose-500 px-5 py-3 font-semibold text-white shadow-lg transition hover:scale-[1.02]">
                            Buka Missions
                        </a>
                    </div>

                    <div class="mt-6 grid gap-4 md:grid-cols-3">
                        <div class="rounded-2xl border border-slate-200 bg-slate-50 p-5">
                            <p class="text-xs uppercase tracking-[0.2em] text-slate-400">Status 1</p>
                            <p class="mt-2 text-xl font-bold">Mission Terang</p>
                            <p class="mt-2 text-sm leading-6 text-slate-600">Mission yang terang berarti sudah terbuka dan bisa mulai dikerjakan.</p>
                        </div>
                        <div class="rounded-2xl border border-slate-200 bg-slate-50 p-5">
                            <p class="text-xs uppercase tracking-[0.2em] text-slate-400">Status 2</p>
                            <p class="mt-2 text-xl font-bold">Mission Selesai</p>
                            <p class="mt-2 text-sm leading-6 text-slate-600">Mission yang selesai menunjukkan progres belajar yang sudah dituntaskan.</p>
                        </div>
                        <div class="rounded-2xl border border-slate-200 bg-slate-50 p-5">
                            <p class="text-xs uppercase tracking-[0.2em] text-slate-400">Status 3</p>
                            <p class="mt-2 text-xl font-bold">Mission Terkunci</p>
                            <p class="mt-2 text-sm leading-6 text-slate-600">Mission terkunci akan terbuka setelah mission sebelumnya benar-benar selesai.</p>
                        </div>
                    </div>

                    <div class="mt-6 rounded-[28px] border border-pink-200 bg-gradient-to-r from-rose-50 to-pink-50 p-6">
                        <p class="text-sm font-semibold uppercase tracking-[0.2em] text-rose-500">Alur Singkat</p>
                        <ol class="mt-4 space-y-3 text-slate-700">
                            <li>1. Klik mission yang sudah terbuka.</li>
                            <li>2. Baca ringkasan challenge pada popup detail mission.</li>
                            <li>3. Tekan tombol mulai untuk masuk ke soal.</li>
                            <li>4. Setelah semua soal selesai, kembali ke mission untuk membuka progres berikutnya.</li>
                        </ol>
                    </div>
                </section>

                <section id="tutorial-question" class="tutorial-panel hidden rounded-[32px] border border-rose-200/40 bg-[#fff8f8] p-7 text-slate-900 shadow-xl">
                    <p class="text-sm font-semibold uppercase tracking-[0.25em] text-sky-500">Modul 2</p>
                    <h2 class="mt-2 text-3xl font-bold">Cara Mengerjakan Soal</h2>
                    <p class="mt-3 max-w-3xl text-slate-600 leading-7">
                        Bagian ini menjelaskan alur menjawab soal dengan konsep belajar bertahap. Sistem tidak hanya menilai,
                        tetapi juga membantu mahasiswa memahami strategi penyelesaiannya.
                    </p>

                    <div class="mt-6 grid gap-4 xl:grid-cols-2">
                        <div class="rounded-[28px] border border-slate-200 bg-slate-50 p-6">
                            <p class="text-sm font-semibold uppercase tracking-[0.2em] text-slate-400">Jenis Soal</p>
                            <div class="mt-4 space-y-4">
                                <div class="rounded-2xl border border-slate-200 bg-white p-4">
                                    <p class="font-semibold">Pilihan Ganda Satu Jawaban</p>
                                    <p class="mt-2 text-sm text-slate-600">Pilih satu opsi yang paling tepat, lalu sistem langsung memeriksa hasilnya.</p>
                                </div>
                                <div class="rounded-2xl border border-slate-200 bg-white p-4">
                                    <p class="font-semibold">Pilihan Ganda Banyak Jawaban</p>
                                    <p class="mt-2 text-sm text-slate-600">Pilih beberapa opsi, lalu tekan tombol submit untuk mengecek kombinasi jawaban.</p>
                                </div>
                                <div class="rounded-2xl border border-slate-200 bg-white p-4">
                                    <p class="font-semibold">Essay</p>
                                    <p class="mt-2 text-sm text-slate-600">Tulis jawaban dalam bentuk teks, lalu sistem mencocokkan dengan kunci jawaban.</p>
                                </div>
                                <div class="rounded-2xl border border-slate-200 bg-white p-4">
                                    <p class="font-semibold">True / False</p>
                                    <p class="mt-2 text-sm text-slate-600">Pilih apakah pernyataan benar atau salah.</p>
                                </div>
                            </div>
                        </div>

                        <div class="rounded-[28px] border border-pink-200 bg-gradient-to-br from-rose-50 to-pink-50 p-6">
                            <p class="text-sm font-semibold uppercase tracking-[0.2em] text-rose-500">Strategi Belajar</p>
                            <div class="mt-4 space-y-4">
                                <div class="rounded-2xl bg-white/80 p-4 border border-rose-100">
                                    <p class="font-semibold">1. Jawab dulu</p>
                                    <p class="mt-2 text-sm text-slate-600">Mahasiswa mencoba menjawab berdasarkan pemahaman sendiri.</p>
                                </div>
                                <div class="rounded-2xl bg-white/80 p-4 border border-rose-100">
                                    <p class="font-semibold">2. Jika salah, buka bantuan</p>
                                    <p class="mt-2 text-sm text-slate-600">Bantuan memberi petunjuk langkah, bukan langsung membocorkan jawaban.</p>
                                </div>
                                <div class="rounded-2xl bg-white/80 p-4 border border-rose-100">
                                    <p class="font-semibold">3. Jawab ulang</p>
                                    <p class="mt-2 text-sm text-slate-600">Setelah membaca bantuan, mahasiswa bisa mencoba lagi pada soal yang sama.</p>
                                </div>
                                <div class="rounded-2xl bg-white/80 p-4 border border-rose-100">
                                    <p class="font-semibold">4. Review pembahasan</p>
                                    <p class="mt-2 text-sm text-slate-600">Setelah challenge selesai, pembahasan lengkap dapat dibuka dari halaman review.</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-6 grid gap-4 md:grid-cols-2">
                        <div class="rounded-2xl bg-emerald-50 border border-emerald-200 p-5">
                            <p class="text-sm font-semibold uppercase tracking-[0.2em] text-emerald-600">Jawaban Benar</p>
                            <p class="mt-3 text-sm leading-6 text-slate-700">Score dan EXP challenge akan bertambah, lalu mahasiswa bisa lanjut ke soal berikutnya.</p>
                        </div>
                        <div class="rounded-2xl bg-amber-50 border border-amber-200 p-5">
                            <p class="text-sm font-semibold uppercase tracking-[0.2em] text-amber-600">Jawaban Belum Tepat</p>
                            <p class="mt-3 text-sm leading-6 text-slate-700">Mahasiswa bisa membuka bantuan, memahami petunjuknya, lalu mencoba menjawab ulang.</p>
                        </div>
                    </div>
                </section>

                <section id="tutorial-profile" class="tutorial-panel hidden rounded-[32px] border border-rose-200/40 bg-[#fff8f8] p-7 text-slate-900 shadow-xl">
                    <div class="flex flex-col gap-5 lg:flex-row lg:items-start lg:justify-between">
                        <div>
                            <p class="text-sm font-semibold uppercase tracking-[0.25em] text-amber-500">Modul 3</p>
                            <h2 class="mt-2 text-3xl font-bold">Memahami Profil dan Progres</h2>
                            <p class="mt-3 max-w-3xl text-slate-600 leading-7">
                                Halaman profil membantu mahasiswa membaca perkembangan belajarnya secara lebih nyata, mulai dari
                                identitas, rank, score, mission aktif, sampai achievement yang sudah terbuka.
                            </p>
                        </div>
                        <a href="{{ route('student.profile.index') }}"
                            class="inline-flex items-center rounded-2xl border border-slate-300 bg-white px-5 py-3 font-semibold text-slate-700 transition hover:bg-slate-50">
                            Buka Profil
                        </a>
                    </div>

                    <div class="mt-6 grid gap-4 md:grid-cols-2 xl:grid-cols-4">
                        <div class="rounded-2xl border border-slate-200 bg-slate-50 p-5">
                            <p class="text-xs uppercase tracking-[0.2em] text-slate-400">Rank dan EXP</p>
                            <p class="mt-2 text-lg font-bold">Progres Level</p>
                            <p class="mt-2 text-sm text-slate-600">Menunjukkan posisi rank mahasiswa dan seberapa dekat ke rank berikutnya.</p>
                        </div>
                        <div class="rounded-2xl border border-slate-200 bg-slate-50 p-5">
                            <p class="text-xs uppercase tracking-[0.2em] text-slate-400">Total Score</p>
                            <p class="mt-2 text-lg font-bold">Hasil Belajar</p>
                            <p class="mt-2 text-sm text-slate-600">Menggambarkan akumulasi performa terbaik dari mission yang sudah diselesaikan.</p>
                        </div>
                        <div class="rounded-2xl border border-slate-200 bg-slate-50 p-5">
                            <p class="text-xs uppercase tracking-[0.2em] text-slate-400">Streak</p>
                            <p class="mt-2 text-lg font-bold">Konsistensi</p>
                            <p class="mt-2 text-sm text-slate-600">Menunjukkan kebiasaan belajar mahasiswa dari hari ke hari.</p>
                        </div>
                        <div class="rounded-2xl border border-slate-200 bg-slate-50 p-5">
                            <p class="text-xs uppercase tracking-[0.2em] text-slate-400">Achievement</p>
                            <p class="mt-2 text-lg font-bold">Reward Pembelajaran</p>
                            <p class="mt-2 text-sm text-slate-600">Badge penghargaan untuk progres, strategi belajar, dan konsistensi.</p>
                        </div>
                    </div>

                    <div class="mt-6 rounded-[28px] border border-sky-200 bg-gradient-to-r from-sky-50 to-white p-6">
                        <p class="text-sm font-semibold uppercase tracking-[0.2em] text-sky-500">Tips Membaca Profil</p>
                        <ul class="mt-4 space-y-3 text-sm leading-6 text-slate-700">
                            <li>1. Gunakan total score untuk melihat perkembangan performa terbaik.</li>
                            <li>2. Gunakan rank dan EXP untuk membaca perkembangan level belajar.</li>
                            <li>3. Gunakan achievement untuk melihat kebiasaan belajar yang sudah tercapai.</li>
                            <li>4. Gunakan mission aktif untuk kembali ke progres terakhir dengan lebih cepat.</li>
                        </ul>
                    </div>
                </section>
            </div>
        </div>

        <audio id="hover-sound" src="{{ asset('sfx/hover.mp3') }}"></audio>
        <audio id="click-sound" src="{{ asset('sfx/click.mp3') }}"></audio>
    </div>

    <style>
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: scale(0.97);
            }

            to {
                opacity: 1;
                transform: scale(1);
            }
        }

        .animate-fadeIn {
            animation: fadeIn 0.45s ease-out;
        }
    </style>

    <script>
        function showTutorialTab(tabName) {
            document.querySelectorAll('.tutorial-panel').forEach((panel) => {
                panel.classList.add('hidden');
            });

            document.querySelectorAll('.tutorial-tab').forEach((tab) => {
                tab.classList.remove('border-pink-200', 'bg-gradient-to-r', 'from-white', 'to-rose-50');
                tab.classList.add('border-slate-200', 'bg-white');
            });

            document.getElementById(`tutorial-${tabName}`).classList.remove('hidden');

            const activeTab = document.querySelector(`.tutorial-tab[data-tab="${tabName}"]`);
            if (activeTab) {
                activeTab.classList.remove('border-slate-200', 'bg-white');
                activeTab.classList.add('border-pink-200', 'bg-gradient-to-r', 'from-white', 'to-rose-50');
            }

            playClick();
        }

        function playClick() {
            const audio = document.getElementById("click-sound");
            if (audio) {
                audio.currentTime = 0;
                audio.play();
            }
        }

        function playHoverSound() {
            const audio = document.getElementById("hover-sound");
            if (audio) {
                audio.currentTime = 0;
                audio.play();
            }
        }

        document.addEventListener("DOMContentLoaded", function() {
            document.querySelectorAll(".tutorial-tab").forEach((el) => {
                el.addEventListener("mouseenter", playHoverSound);
            });
        });
    </script>
@endsection
