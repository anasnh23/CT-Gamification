@extends('lecturer.layouts.app')

@section('content')
    <div class="section-form-page">
        <section class="section-form-hero">
            <p class="section-form-kicker">Sections</p>
            <h1 class="section-form-title">Buat section pembelajaran</h1>
        </section>

        @if ($errors->any())
            <div class="section-form-alert">
                <strong>Data belum bisa disimpan.</strong>
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('lecturer.sections.store') }}" method="POST" class="section-form-card bg-white">
            @csrf

            <div class="section-form-grid">
                <div>
                    <label for="order" class="section-form-label">Urutan Section</label>
                    <input type="number" name="order" id="order" value="{{ old('order') }}" class="section-form-input" required>
                    <p class="section-form-hint">Gunakan angka unik.</p>
                </div>

                <div>
                    <label for="name" class="section-form-label">Nama Section</label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}" class="section-form-input" required>
                    <p class="section-form-hint">Contoh: Dasar Logika.</p>
                </div>
            </div>

            <div class="section-form-actions">
                <a href="{{ route('lecturer.sections.index') }}" class="section-form-btn neutral">Kembali</a>
                <button type="submit" class="section-form-btn primary">Simpan Section</button>
            </div>
        </form>
    </div>

    <style>
        .section-form-page {
            max-width: 960px;
            margin: 0 auto;
        }

        .section-form-hero {
            margin-bottom: 24px;
            padding: 28px;
            border-radius: 30px;
            border: 1px solid rgba(255, 228, 236, 0.14);
            background: rgba(74, 19, 39, 0.78);
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.22);
        }

        .section-form-kicker {
            margin: 0;
            font-size: 12px;
            letter-spacing: 0.34em;
            text-transform: uppercase;
            color: rgba(255, 228, 236, 0.75);
        }

        .section-form-title {
            margin: 12px 0 0;
            color: #fff;
            font-size: 40px;
            font-weight: 700;
        }

        .section-form-copy {
            margin: 14px 0 0;
            color: rgba(255, 240, 244, 0.76);
            line-height: 1.8;
            max-width: 760px;
        }

        .section-form-alert {
            margin-bottom: 16px;
            padding: 16px 18px;
            border-radius: 18px;
            background: rgba(254, 226, 226, 0.96);
            color: #991b1b;
        }

        .section-form-alert ul {
            margin: 10px 0 0 18px;
        }

        .section-form-card {
            padding: 24px;
            border-radius: 30px;
        }

        .section-form-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 20px;
        }

        .section-form-label {
            display: block;
            margin-bottom: 10px;
            color: #334155;
            font-weight: 700;
        }

        .section-form-input {
            width: 100%;
            padding: 14px 16px;
            border-radius: 16px;
            border: 1px solid #f0b6c9;
            box-sizing: border-box;
            color: #1f2937;
        }

        .section-form-hint {
            margin: 8px 0 0;
            color: #94a3b8;
            font-size: 13px;
            line-height: 1.6;
        }

        .section-form-actions {
            display: flex;
            justify-content: space-between;
            gap: 14px;
            margin-top: 24px;
        }

        .section-form-btn {
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

        .section-form-btn.neutral {
            background: #64748b;
            color: #fff;
        }

        .section-form-btn.primary {
            background: linear-gradient(90deg, #c0265f, #ec4899);
            color: #fff;
        }

        @media (max-width: 768px) {
            .section-form-grid {
                grid-template-columns: 1fr;
            }

            .section-form-title {
                font-size: 32px;
            }

            .section-form-actions {
                flex-direction: column;
            }
        }
    </style>
@endsection
