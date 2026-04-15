<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'CT-Game') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <link rel="icon" type="image/png" href="{{ asset('favicon-ctg.png') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        :root {
            --guest-shell: linear-gradient(135deg, #3a0918 0%, #58142e 38%, #6c1b3a 100%);
            --guest-panel: rgba(84, 19, 42, .84);
            --guest-panel-soft: rgba(255, 255, 255, .08);
            --guest-border: rgba(255, 228, 236, .14);
            --guest-text: #fff7fb;
            --guest-muted: rgba(255, 236, 242, .74);
            --guest-card: rgba(255, 250, 252, .96);
            --guest-ink: #1f2937;
            --guest-accent: linear-gradient(90deg, #c0265f, #ec4899);
        }

        * { box-sizing: border-box; }
        body {
            margin: 0;
            min-height: 100vh;
            font-family: 'Figtree', 'Segoe UI', sans-serif;
            background:
                radial-gradient(circle at top left, rgba(236, 72, 153, .18), transparent 28%),
                radial-gradient(circle at bottom right, rgba(251, 146, 60, .12), transparent 24%),
                var(--guest-shell);
            color: var(--guest-text);
        }

        .guest-shell {
            min-height: 100vh;
            display: grid;
            place-items: center;
            padding: 32px 18px;
        }

        .guest-grid {
            width: 100%;
            max-width: 1140px;
            display: grid;
            grid-template-columns: 1.05fr .95fr;
            gap: 24px;
            align-items: stretch;
        }

        .guest-brand-panel {
            position: relative;
            overflow: hidden;
            padding: 36px;
            border-radius: 36px;
            border: 1px solid var(--guest-border);
            background: var(--guest-panel);
            box-shadow: 0 28px 70px rgba(0, 0, 0, .28);
        }

        .guest-brand-panel::before,
        .guest-brand-panel::after {
            content: '';
            position: absolute;
            border-radius: 999px;
            background: rgba(255, 255, 255, .05);
            filter: blur(4px);
        }

        .guest-brand-panel::before {
            width: 220px;
            height: 220px;
            top: -80px;
            right: -60px;
        }

        .guest-brand-panel::after {
            width: 160px;
            height: 160px;
            bottom: -50px;
            left: -30px;
        }

        .guest-brand-inner {
            position: relative;
            z-index: 1;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            min-height: 100%;
        }

        .guest-brand-badge {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            width: fit-content;
            padding: 12px 16px;
            border-radius: 999px;
            background: rgba(255, 255, 255, .08);
            border: 1px solid rgba(255, 255, 255, .1);
            color: var(--guest-muted);
            font-size: 12px;
            letter-spacing: .28em;
            text-transform: uppercase;
        }

        .guest-brand-title {
            margin: 26px 0 0;
            font-size: clamp(40px, 6vw, 68px);
            line-height: .95;
            font-weight: 800;
            letter-spacing: -.03em;
            color: #fff;
        }

        .guest-brand-copy {
            margin: 18px 0 0;
            max-width: 520px;
            color: var(--guest-muted);
            line-height: 1.85;
            font-size: 16px;
        }

        .guest-brand-points {
            display: grid;
            gap: 14px;
            margin-top: 28px;
        }

        .guest-brand-point {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 14px 16px;
            border-radius: 20px;
            background: rgba(255, 255, 255, .07);
            color: var(--guest-text);
        }

        .guest-brand-point span {
            width: 34px;
            height: 34px;
            border-radius: 12px;
            display: grid;
            place-items: center;
            background: rgba(255, 255, 255, .12);
            font-size: 14px;
            font-weight: 700;
        }

        .guest-card {
            padding: 28px;
            border-radius: 36px;
            background: var(--guest-card);
            color: var(--guest-ink);
            border: 1px solid rgba(255, 255, 255, .34);
            box-shadow: 0 28px 70px rgba(0, 0, 0, .18);
        }

        .guest-form-shell {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .guest-card a {
            color: #be185d;
        }

        .guest-auth-footer {
            margin-top: 18px;
            color: rgba(255, 236, 242, .62);
            text-align: center;
            font-size: 13px;
        }

        @media (max-width: 960px) {
            .guest-grid {
                grid-template-columns: 1fr;
            }

            .guest-brand-panel {
                padding: 28px;
            }

            .guest-card {
                padding: 22px;
            }
        }
    </style>
</head>

<body>
    <div class="guest-shell">
        <div class="guest-grid">
            <section class="guest-brand-panel">
                <div class="guest-brand-inner">
                    <div>
                        <div class="guest-brand-badge">
                            <img src="{{ asset('favicon-ctg.png') }}" alt="CTG" style="width:22px;height:22px;object-fit:contain;">
                            CTG Platform
                        </div>

                        <h1 class="guest-brand-title">Belajar<br>lebih hidup.</h1>
                        <p class="guest-brand-copy">
                            Masuk ke workspace untuk mengelola tantangan, memantau progres, dan menjalankan pembelajaran dengan nuansa gamifikasi yang konsisten.
                        </p>

                        <div class="guest-brand-points">
                            <div class="guest-brand-point"><span>01</span> Progress yang jelas</div>
                            <div class="guest-brand-point"><span>02</span> Bantuan dan pembahasan</div>
                            <div class="guest-brand-point"><span>03</span> Dashboard terhubung</div>
                        </div>
                    </div>

                    <div style="margin-top:28px; color:rgba(255,236,242,.62); font-size:13px;">
                        &copy; {{ date('Y') }} {{ config('app.name', 'CT-Game') }}
                    </div>
                </div>
            </section>

            <section class="guest-card">
                <div class="guest-form-shell">
                    {{ $slot }}
                </div>
            </section>
        </div>
    </div>
</body>

</html>
