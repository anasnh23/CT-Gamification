@extends('lecturer.layouts.app')

@section('content')
    <div class="challenge-form-page">
        <section class="challenge-form-hero">
            <p class="challenge-form-kicker">Perbarui Mission</p>
            <h1 class="challenge-form-title">Edit challenge {{ $challenge->title }}</h1>
            <p class="challenge-form-copy">
                Ubah section tujuan atau perbarui judul challenge agar lebih sesuai dengan struktur pembelajaran yang sedang Anda bangun.
            </p>
        </section>

        @if ($errors->any())
            <div class="challenge-form-alert">
                <strong>Data belum bisa diperbarui.</strong>
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('lecturer.challenges.update', $challenge->id) }}" method="POST" class="challenge-form-card bg-white">
            @csrf
            @method('PUT')

            <div class="challenge-form-grid">
                <div>
                    <label for="section_id" class="challenge-form-label">Section Tujuan</label>
                    <select name="section_id" id="section_id" class="challenge-form-input" required>
                        <option value="">Pilih section</option>
                        @foreach ($sections as $section)
                            <option value="{{ $section->id }}" {{ (string) old('section_id', $challenge->section_id) === (string) $section->id ? 'selected' : '' }}>
                                {{ $section->order }} - {{ $section->name }}
                            </option>
                        @endforeach
                    </select>
                    <p class="challenge-form-hint">Pindahkan challenge ke section lain bila struktur materi berubah.</p>
                </div>

                <div>
                    <label for="title" class="challenge-form-label">Judul Challenge</label>
                    <input type="text" name="title" id="title" value="{{ old('title', $challenge->title) }}" class="challenge-form-input" required>
                    <p class="challenge-form-hint">Gunakan judul yang singkat, jelas, dan mewakili jenis tantangan.</p>
                </div>
            </div>

            <div class="challenge-form-summary">
                <div class="challenge-summary-box">
                    <span>Total EXP Saat Ini</span>
                    <strong>{{ $challenge->total_exp }}</strong>
                    <small>Nilai ini akan berubah otomatis jika ada soal yang ditambah atau diperbarui.</small>
                </div>
                <div class="challenge-summary-box">
                    <span>Total Score Saat Ini</span>
                    <strong>{{ $challenge->total_score }}</strong>
                    <small>Skor challenge mengikuti jumlah skor dari seluruh soal yang terhubung.</small>
                </div>
            </div>

            <div class="challenge-form-actions">
                <a href="{{ route('lecturer.challenges.index') }}" class="challenge-form-btn neutral">Kembali</a>
                <button type="submit" class="challenge-form-btn primary">Update Challenge</button>
            </div>
        </form>
    </div>

    <style>
        .challenge-form-page {
            max-width: 980px;
            margin: 0 auto;
        }

        .challenge-form-hero {
            margin-bottom: 24px;
            padding: 28px;
            border-radius: 30px;
            border: 1px solid rgba(255, 228, 236, 0.14);
            background: rgba(74, 19, 39, 0.78);
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.22);
        }

        .challenge-form-kicker {
            margin: 0;
            font-size: 12px;
            letter-spacing: 0.34em;
            text-transform: uppercase;
            color: rgba(255, 228, 236, 0.75);
        }

        .challenge-form-title {
            margin: 12px 0 0;
            color: #fff;
            font-size: 40px;
            font-weight: 700;
        }

        .challenge-form-copy {
            margin: 14px 0 0;
            color: rgba(255, 240, 244, 0.76);
            line-height: 1.8;
            max-width: 760px;
        }

        .challenge-form-alert {
            margin-bottom: 16px;
            padding: 16px 18px;
            border-radius: 18px;
            background: rgba(254, 226, 226, 0.96);
            color: #991b1b;
        }

        .challenge-form-alert ul {
            margin: 10px 0 0 18px;
        }

        .challenge-form-card {
            padding: 24px;
            border-radius: 30px;
        }

        .challenge-form-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 20px;
        }

        .challenge-form-label {
            display: block;
            margin-bottom: 10px;
            color: #334155;
            font-weight: 700;
        }

        .challenge-form-input {
            width: 100%;
            padding: 14px 16px;
            border-radius: 16px;
            border: 1px solid #f0b6c9;
            box-sizing: border-box;
            color: #1f2937;
        }

        .challenge-form-hint {
            margin: 8px 0 0;
            color: #94a3b8;
            font-size: 13px;
            line-height: 1.6;
        }

        .challenge-form-summary {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 16px;
            margin-top: 22px;
        }

        .challenge-summary-box {
            padding: 18px;
            border-radius: 22px;
            background: #fff7fa;
            border: 1px solid #f5c3d6;
        }

        .challenge-summary-box span {
            display: block;
            color: #be185d;
            font-size: 13px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.18em;
        }

        .challenge-summary-box strong {
            display: block;
            margin-top: 10px;
            color: #1f2937;
            font-size: 22px;
        }

        .challenge-summary-box small {
            display: block;
            margin-top: 8px;
            color: #64748b;
            line-height: 1.6;
        }

        .challenge-form-actions {
            display: flex;
            justify-content: space-between;
            gap: 14px;
            margin-top: 24px;
        }

        .challenge-form-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 14px 18px;
            border-radius: 16px;
            text-decoration: none;
            font-weight: 700;
            border: 0;
            cursor: pointer;
        }

        .challenge-form-btn.neutral {
            background: #64748b;
            color: #fff;
        }

        .challenge-form-btn.primary {
            background: linear-gradient(90deg, #c0265f, #ec4899);
            color: #fff;
        }

        @media (max-width: 768px) {
            .challenge-form-grid,
            .challenge-form-summary {
                grid-template-columns: 1fr;
            }

            .challenge-form-title {
                font-size: 32px;
            }

            .challenge-form-actions {
                flex-direction: column;
            }
        }
    </style>
@endsection
