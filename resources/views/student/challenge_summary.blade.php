@php
    use Carbon\Carbon;

    $start = Carbon::parse($challengeResult->created_at);
    $end = Carbon::parse($challengeResult->ended_at ?? now());
    $duration = $start->diff($end);
    $pesan =
        $challengeResult->correct_answers > $challengeResult->wrong_answers
            ? $motivasiBenar[array_rand($motivasiBenar)]
            : $motivasiSalah[array_rand($motivasiSalah)];
@endphp

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="{{ asset('favicon-ctg.png') }}">
    <title>Challenge Summary</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="min-h-screen bg-gradient-to-br from-[#240812] via-[#451127] to-[#66203d] flex items-center justify-center p-4">
    <div class="w-full max-w-3xl bg-[#fff8f8] shadow-2xl rounded-3xl p-8 border border-rose-200/40">
        <div class="text-center">
            <p class="text-sm uppercase tracking-[0.3em] text-sky-600 font-semibold">Challenge Selesai</p>
            <h1 class="text-3xl font-bold mt-2">Lanjutkan dengan review pembahasan</h1>
            <p class="mt-3 text-slate-500">{{ $pesan }}</p>
        </div>

        <div class="grid md:grid-cols-2 gap-4 mt-8">
            <div class="rounded-2xl bg-slate-50 p-4 border border-slate-200">
                <p class="text-sm text-slate-500">Durasi</p>
                <p class="text-2xl font-bold text-slate-900">{{ $duration->format('%i menit %s detik') }}</p>
            </div>
            <div class="rounded-2xl bg-slate-50 p-4 border border-slate-200">
                <p class="text-sm text-slate-500">Hasil</p>
                <p class="text-2xl font-bold text-slate-900">{{ $challengeResult->correct_answers }} benar / {{ $challengeResult->wrong_answers }} salah</p>
            </div>
        </div>

        <div class="grid md:grid-cols-2 gap-4 mt-4">
            <div class="rounded-2xl bg-emerald-50 p-4 border border-emerald-200">
                <p class="text-sm text-emerald-700">Score</p>
                <p class="text-3xl font-bold text-emerald-700">{{ $challengeResult->total_score }}</p>
            </div>
            <div class="rounded-2xl bg-sky-50 p-4 border border-sky-200">
                <p class="text-sm text-sky-700">EXP</p>
                <p class="text-3xl font-bold text-sky-700">{{ $challengeResult->total_exp }}</p>
            </div>
        </div>

        <div class="mt-8 flex flex-wrap justify-center gap-3">
            <a href="{{ route('student.review', ['challenge' => $challengeResult->challenge_id, 'attempt' => $challengeResult->attempt_number]) }}"
                class="rounded-2xl bg-slate-900 text-white px-5 py-3 font-semibold hover:bg-slate-800 transition">
                Lihat Pembahasan dan Jawaban
            </a>
            <a href="{{ route('student.start.question', ['challenge_id' => $challengeResult->challenge_id]) }}"
                class="rounded-2xl bg-sky-600 text-white px-5 py-3 font-semibold hover:bg-sky-700 transition">
                Coba Lagi
            </a>
            <a href="{{ route('student.mission.index') }}"
                class="rounded-2xl border border-slate-300 text-slate-700 px-5 py-3 font-semibold hover:bg-slate-50 transition">
                Kembali ke Misi
            </a>
        </div>
    </div>
</body>

</html>
