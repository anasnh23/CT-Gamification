<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gamified Dashboard</title>

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">

    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="icon" type="image/png" href="{{ asset('storage/icons/game.png') }}">

    <!-- Custom CSS -->
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background:
                radial-gradient(circle at top, rgba(244, 114, 182, 0.22), transparent 34%),
                radial-gradient(circle at right top, rgba(251, 113, 133, 0.18), transparent 28%),
                linear-gradient(145deg, #220610 0%, #3b1021 46%, #5b1630 100%);
            color: white;
            overflow-x: hidden;
        }

        /* Efek Glow */
        .glow {
            box-shadow: 0px 0px 18px rgba(251, 191, 36, 0.28);
        }
    </style>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>

<body class="flex min-h-screen overflow-x-hidden">

    <!-- Sidebar -->
    @include('student.layouts.sidebar')

    <!-- Content Wrapper -->
    <div id="main-content" class="flex-1 transition-all duration-500 p-4 md:p-8 ml-24 md:ml-64 max-w-full overflow-x-hidden">
        @yield('content')
    </div>

    <!-- Sidebar Animation -->
    <script>
        $(document).ready(function() {
            const mobileQuery = window.matchMedia('(max-width: 767px)');
            let isCollapsed = mobileQuery.matches;

            function applySidebarState() {
                $('#sidebar').removeClass('w-24 w-64 md:w-24 md:w-64');
                $('#main-content').removeClass('ml-24 ml-64 md:ml-24 md:ml-64');
                $('.sidebar-link').removeClass('justify-center justify-start md:justify-start px-3 px-6');
                $('#logout-button').removeClass('justify-center justify-start md:justify-start px-3 px-4');
                $('.sidebar-badge').removeClass('mr-0 mr-3');

                if (isCollapsed) {
                    $('#sidebar').addClass('w-24 md:w-24');
                    $('#main-content').addClass('ml-24 md:ml-24');
                    $('.sidebar-text').addClass('hidden');
                    $('#sidebar-logo').addClass('justify-center');
                    $('.sidebar-link').addClass('justify-center px-3');
                    $('#logout-button').addClass('justify-center px-3');
                    $('.sidebar-badge').addClass('mr-0');
                } else {
                    $('#sidebar').addClass('w-64 md:w-64');
                    $('#main-content').addClass('ml-64 md:ml-64');
                    $('.sidebar-text').removeClass('hidden');
                    $('#sidebar-logo').removeClass('justify-center');
                    $('.sidebar-link').addClass('justify-start px-6 md:justify-start');
                    $('#logout-button').addClass('justify-start px-4 md:justify-start');
                    $('.sidebar-badge').addClass('mr-3');
                }

                $('#toggleSidebarIcon').toggleClass('rotate-180', !isCollapsed);
            }

            applySidebarState();

            mobileQuery.addEventListener('change', function(event) {
                isCollapsed = event.matches;
                applySidebarState();
            });

            $('#toggleSidebar').click(function() {
                isCollapsed = !isCollapsed;
                applySidebarState();
            });
        });
    </script>
    <!-- Gaming-Style Loading Overlay -->
    <div id="loadingOverlay"
        class="fixed inset-0 z-50 bg-[radial-gradient(circle_at_top,_rgba(244,114,182,0.18),_transparent_30%),linear-gradient(145deg,_rgba(27,5,8,0.96),_rgba(43,9,16,0.98),_rgba(18,2,4,0.99))] flex items-center justify-center hidden flex-col animate-fadeIn px-6">
        <div
            class="w-full max-w-md rounded-[2rem] border border-pink-200/20 bg-white/5 backdrop-blur-md px-8 py-10 text-center shadow-[0_20px_60px_rgba(0,0,0,0.45)]">
            <div
                class="mx-auto mb-5 flex h-16 w-16 items-center justify-center rounded-[1.4rem] border border-pink-200/20 bg-white/10 shadow-[0_0_35px_rgba(244,114,182,0.18)] loading-emblem">
                <img src="{{ asset('storage/icons/game.png') }}" alt="Loading mission" class="h-10 w-10 object-contain">
            </div>

            <div class="mx-auto mb-6 flex w-fit items-center gap-3">
                <span class="loading-node loading-node-1"></span>
                <span class="loading-link"></span>
                <span class="loading-node loading-node-2"></span>
                <span class="loading-link"></span>
                <span class="loading-node loading-node-3"></span>
            </div>

            <p class="text-xs font-semibold uppercase tracking-[0.38em] text-pink-200/80">Menyiapkan Mission</p>
            <h2 class="mt-3 text-2xl font-bold text-white loading-title">Tantangan sedang dimuat</h2>
            <p class="mt-3 text-sm leading-7 text-rose-100/75">
                Sistem sedang menyiapkan halaman berikutnya agar pengalaman belajar tetap halus.
            </p>

            <div class="mt-6 h-2 overflow-hidden rounded-full bg-white/10">
                <div class="loading-bar h-full rounded-full"></div>
            </div>

            <div class="mt-5 flex items-center justify-center gap-2 text-xs text-rose-100/65">
                <span class="loading-dot"></span>
                <span>Memuat progres dan tantangan</span>
                <span class="loading-dot loading-dot-delay"></span>
            </div>
        </div>
    </div>
    @if (session('show_tutorial_popup'))
        <div id="tutorialPopup" class="fixed inset-0 bg-black bg-opacity-60 flex items-center justify-center z-50">
            <div class="bg-[#4a1327] border border-rose-300/30 p-8 rounded-lg text-center shadow-2xl animate-pop w-96">
                <h2 class="text-2xl font-bold text-yellow-400 mb-4">📘 Welcome {{ explode(' ', $user->name)[0] }}!</h2>
                <p class="text-rose-100 mb-6">Yuk mulai dengan membaca tutorial penggunaan aplikasi terlebih dahulu!</p>
                <div class="flex justify-center gap-4">
                    <form action="{{ route('student.dismiss.tutorial') }}" method="POST">
                        @csrf
                        <button type="submit"
                            class="bg-pink-600 hover:bg-pink-500 text-white px-4 py-2 rounded font-semibold transition">
                            Lihat Tutorial
                        </button>
                    </form>
                    <button onclick="document.getElementById('tutorialPopup').remove()"
                        class="bg-[#6b2440] hover:bg-[#7c2b4b] text-white px-4 py-2 rounded font-semibold transition">Nanti
                        Saja</button>
                </div>
            </div>
        </div>
    @endif

    <!-- Custom Animations -->
    <style>
        @keyframes pop {
            0% {
                transform: scale(0.8);
                opacity: 0;
            }

            100% {
                transform: scale(1);
                opacity: 1;
            }
        }

        .animate-pop {
            animation: pop 0.4s ease-in-out;
        }

        @keyframes spinSlow {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        .animate-spin-slow {
            animation: spinSlow 3s linear infinite;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

        .animate-fadeIn {
            animation: fadeIn 0.5s ease-out;
        }

        @keyframes missionPulse {
            0%,
            100% {
                transform: scale(0.92);
                opacity: 0.4;
                box-shadow: 0 0 0 rgba(251, 113, 133, 0);
            }

            50% {
                transform: scale(1.08);
                opacity: 1;
                box-shadow: 0 0 20px rgba(251, 113, 133, 0.55);
            }
        }

        @keyframes missionBar {
            0% {
                transform: translateX(-100%);
            }

            100% {
                transform: translateX(220%);
            }
        }

        @keyframes emblemFloat {
            0%,
            100% {
                transform: translateY(0px) scale(1);
            }

            50% {
                transform: translateY(-4px) scale(1.03);
            }
        }

        @keyframes titleGlow {
            0%,
            100% {
                text-shadow: 0 0 0 rgba(255, 255, 255, 0);
            }

            50% {
                text-shadow: 0 0 18px rgba(244, 114, 182, 0.22);
            }
        }

        @keyframes blinkDot {
            0%,
            100% {
                opacity: 0.25;
                transform: scale(0.9);
            }

            50% {
                opacity: 1;
                transform: scale(1.15);
            }
        }

        .loading-node {
            width: 20px;
            height: 20px;
            border-radius: 9999px;
            background: linear-gradient(135deg, #fb7185, #ec4899);
            border: 2px solid rgba(255, 255, 255, 0.55);
            display: inline-block;
            animation: missionPulse 1.4s ease-in-out infinite;
        }

        .loading-node-2 {
            animation-delay: 0.2s;
        }

        .loading-node-3 {
            animation-delay: 0.4s;
        }

        .loading-link {
            width: 56px;
            height: 4px;
            border-radius: 9999px;
            background: linear-gradient(90deg, rgba(251, 113, 133, 0.25), rgba(255, 255, 255, 0.35), rgba(251, 113, 133, 0.25));
        }

        .loading-bar {
            width: 45%;
            background: linear-gradient(90deg, #fb7185, #f472b6, #fde68a);
            animation: missionBar 1.6s ease-in-out infinite;
        }

        .loading-emblem {
            animation: emblemFloat 2s ease-in-out infinite;
        }

        .loading-title {
            animation: titleGlow 1.8s ease-in-out infinite;
        }

        .loading-dot {
            width: 6px;
            height: 6px;
            border-radius: 9999px;
            background: #f9a8d4;
            display: inline-block;
            animation: blinkDot 1.2s ease-in-out infinite;
        }

        .loading-dot-delay {
            animation-delay: 0.3s;
        }
    </style>

    <script>
        window.addEventListener("DOMContentLoaded", () => {
            const links = document.querySelectorAll("a:not([target='_blank']):not([href^='#'])");
            const overlay = document.getElementById("loadingOverlay");

            links.forEach(link => {
                link.addEventListener("click", (e) => {
                    const href = link.getAttribute("href");
                    if (href && !href.startsWith("javascript:") && !link.classList.contains(
                            'no-loading')) {
                        overlay.classList.remove("hidden");
                    }
                });
            });
        });
    </script>
</body>

</html>
