@extends('student.layouts.app')

@section('content')
    <div class="animate-fadeIn">
        <div class="mx-auto max-w-6xl px-4 py-10">
            <div class="grid gap-6 lg:grid-cols-[1.1fr_0.9fr]">
                <section class="rounded-[32px] border border-pink-200/20 bg-[#4a1327] p-8 text-white shadow-2xl">
                    <p class="text-sm font-semibold uppercase tracking-[0.35em] text-pink-200/75">Riwayat Belajar</p>
                    <h1 class="mt-3 text-4xl font-bold leading-tight">Lihat jejak mission yang sudah pernah kamu selesaikan</h1>
                    <p class="mt-4 max-w-2xl text-rose-100/80 leading-7">
                        Halaman ini membantu mahasiswa melihat hasil mission sebelumnya, membandingkan attempt, dan membuka
                        kembali pembahasan dari challenge yang sudah pernah dikerjakan.
                    </p>

                    <div class="mt-8 grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                        <div class="rounded-2xl bg-white/10 p-4">
                            <p class="text-xs uppercase tracking-[0.2em] text-pink-100/70">Mission Selesai</p>
                            <p class="mt-2 text-2xl font-bold">{{ $summary['completed_missions'] }}</p>
                        </div>
                        <div class="rounded-2xl bg-white/10 p-4">
                            <p class="text-xs uppercase tracking-[0.2em] text-pink-100/70">Total Attempt</p>
                            <p class="mt-2 text-2xl font-bold">{{ $summary['total_attempts'] }}</p>
                        </div>
                        <div class="rounded-2xl bg-white/10 p-4">
                            <p class="text-xs uppercase tracking-[0.2em] text-pink-100/70">Skor Terbaik</p>
                            <p class="mt-2 text-2xl font-bold">{{ number_format($summary['best_score']) }}</p>
                        </div>
                        <div class="rounded-2xl bg-white/10 p-4">
                            <p class="text-xs uppercase tracking-[0.2em] text-pink-100/70">Rata-rata Skor</p>
                            <p class="mt-2 text-2xl font-bold">{{ number_format($summary['average_score'], 1) }}</p>
                        </div>
                    </div>
                </section>

                <section class="rounded-[32px] border border-rose-200/40 bg-[#fff8f8] p-7 text-slate-900 shadow-xl">
                    <p class="text-sm font-semibold uppercase tracking-[0.25em] text-rose-500">Highlight</p>
                    <h2 class="mt-2 text-2xl font-bold">Best Result per Mission</h2>

                    <div class="mt-6 space-y-4 max-h-[400px] overflow-y-auto pr-2">
                        @forelse ($bestResults as $result)
                            <div class="rounded-2xl border border-slate-200 bg-slate-50 p-5">
                                <p class="text-xs uppercase tracking-[0.2em] text-slate-400">
                                    {{ $result->challenge?->section?->name ?? 'Section' }}
                                </p>
                                <h3 class="mt-2 text-lg font-bold">{{ $result->challenge?->title ?? 'Mission' }}</h3>
                                <div class="mt-4 grid grid-cols-3 gap-3 text-sm">
                                    <div class="rounded-xl bg-white p-3 border border-slate-200">
                                        <p class="text-slate-400">Score</p>
                                        <p class="mt-1 font-bold">{{ $result->total_score }}</p>
                                    </div>
                                    <div class="rounded-xl bg-white p-3 border border-slate-200">
                                        <p class="text-slate-400">EXP</p>
                                        <p class="mt-1 font-bold">{{ $result->total_exp }}</p>
                                    </div>
                                    <div class="rounded-xl bg-white p-3 border border-slate-200">
                                        <p class="text-slate-400">Attempt</p>
                                        <p class="mt-1 font-bold">#{{ $result->attempt_number }}</p>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="rounded-2xl border border-slate-200 bg-slate-50 p-5 text-slate-600">
                                Belum ada mission yang selesai. Kerjakan mission pertama untuk mulai membangun riwayat belajar.
                            </div>
                        @endforelse
                    </div>
                </section>
            </div>

            <section class="mt-8 rounded-[32px] border border-rose-200/40 bg-[#fff8f8] p-7 text-slate-900 shadow-xl">
                <div class="flex flex-col gap-3 md:flex-row md:items-end md:justify-between">
                    <div>
                        <p class="text-sm font-semibold uppercase tracking-[0.25em] text-rose-500">Daftar Attempt</p>
                        <h2 class="mt-2 text-3xl font-bold">Attempt Terbaru</h2>
                    </div>
                    <p class="text-sm text-slate-500">Urut dari yang paling baru selesai dikerjakan.</p>
                </div>

                <div class="mt-6 overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200">
                        <thead>
                            <tr class="text-left text-xs uppercase tracking-[0.2em] text-slate-400">
                                <th class="pb-4 pr-4">Mission</th>
                                <th class="pb-4 pr-4">Section</th>
                                <th class="pb-4 pr-4">Attempt</th>
                                <th class="pb-4 pr-4">Hasil</th>
                                <th class="pb-4 pr-4">Waktu</th>
                                <th class="pb-4 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200">
                            @forelse ($results as $result)
                                <tr class="align-top">
                                    <td class="py-4 pr-4">
                                        <p class="font-semibold">{{ $result->challenge?->title ?? 'Mission' }}</p>
                                        <p class="mt-1 text-sm text-slate-500">
                                            {{ $result->challenge?->questions_count ?? 0 }} soal
                                        </p>
                                    </td>
                                    <td class="py-4 pr-4 text-sm text-slate-600">
                                        Section {{ $result->challenge?->section?->order ?? '-' }} -
                                        {{ $result->challenge?->section?->name ?? '-' }}
                                    </td>
                                    <td class="py-4 pr-4 text-sm font-semibold text-slate-700">
                                        #{{ $result->attempt_number }}
                                    </td>
                                    <td class="py-4 pr-4">
                                        <div class="flex flex-wrap gap-2 text-sm">
                                            <span class="rounded-full bg-emerald-50 px-3 py-1 font-semibold text-emerald-700">
                                                Score {{ $result->total_score }}
                                            </span>
                                            <span class="rounded-full bg-sky-50 px-3 py-1 font-semibold text-sky-700">
                                                EXP {{ $result->total_exp }}
                                            </span>
                                            <span class="rounded-full bg-rose-50 px-3 py-1 font-semibold text-rose-700">
                                                {{ $result->correct_answers }} benar / {{ $result->wrong_answers }} salah
                                            </span>
                                        </div>
                                    </td>
                                    <td class="py-4 pr-4 text-sm text-slate-600">
                                        {{ optional($result->ended_at)->format('d M Y, H:i') }}
                                    </td>
                                    <td class="py-4 text-right">
                                        <a href="{{ route('student.review', ['challenge' => $result->challenge_id, 'attempt' => $result->attempt_number]) }}"
                                            class="inline-flex rounded-2xl bg-gradient-to-r from-pink-600 to-rose-500 px-4 py-2 text-sm font-semibold text-white shadow transition hover:scale-[1.02]">
                                            Review
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="py-8 text-center text-slate-500">
                                        Belum ada attempt yang selesai. Mulai mission pertama untuk melihat riwayat belajar.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>
        </div>
    </div>
@endsection
