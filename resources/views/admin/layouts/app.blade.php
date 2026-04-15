<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Workspace</title>
    @vite('resources/css/app.css')
    <link rel="icon" type="image/png" href="{{ asset('favicon-ctg.png') }}">
    <style>
        :root {
            --admin-shell: linear-gradient(135deg, #3f0a1c 0%, #5b1731 42%, #691b39 100%);
            --admin-panel: rgba(83, 20, 43, .9);
            --admin-panel-soft: rgba(112, 39, 67, .76);
            --admin-border: rgba(255, 228, 236, .14);
            --admin-text: #fff7fb;
            --admin-muted: rgba(255, 236, 242, .72);
            --admin-accent: linear-gradient(90deg, #c0265f, #ec4899);
            --admin-card: #fffafc;
            --admin-ink: #1f2937;
        }

        * { box-sizing: border-box; }
        html, body { margin: 0; min-height: 100%; font-family: 'Poppins', 'Segoe UI', sans-serif; background: var(--admin-shell); color: var(--admin-text); }
        body { overflow-x: hidden; }
        .admin-shell { min-height: 100vh; background: var(--admin-shell); }
        .admin-sidebar { position: fixed; inset: 0 auto 0 0; width: 320px; background: linear-gradient(180deg, rgba(92,18,45,.96), rgba(73,16,37,.98)); border-right: 1px solid var(--admin-border); display: flex; flex-direction: column; z-index: 60; transition: width .25s ease, transform .25s ease; }
        .admin-sidebar.collapsed { width: 92px; }
        .admin-sidebar.scrolled { overflow-y: auto; overflow-x: hidden; }
        .admin-main { margin-left: 320px; min-height: 100vh; transition: margin-left .25s ease; }
        .admin-main.expanded { margin-left: 92px; }
        .admin-main-inner { padding: 32px; }
        .admin-surface { background: rgba(92, 22, 47, .58); border: 1px solid var(--admin-border); box-shadow: 0 24px 60px rgba(0,0,0,.18); }
        .admin-card-white { background: var(--admin-card); color: var(--admin-ink); box-shadow: 0 24px 60px rgba(0,0,0,.12); }
        .admin-scrollbar::-webkit-scrollbar { width: 8px; }
        .admin-scrollbar::-webkit-scrollbar-thumb { background: rgba(255,255,255,.18); border-radius: 999px; }
        .admin-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .admin-sidebar .hide-when-collapsed { transition: opacity .18s ease, width .18s ease; }
        .admin-sidebar.collapsed .hide-when-collapsed { opacity: 0; width: 0; overflow: hidden; pointer-events: none; }
        .admin-sidebar.collapsed .compact-center { justify-content: center !important; }
        .admin-sidebar.collapsed .compact-stack { padding-left: 10px !important; padding-right: 10px !important; }
        .admin-sidebar.collapsed .compact-hide { display: none !important; }
        .admin-sidebar.collapsed .nav-link-admin { justify-content: center; padding-left: 0 !important; padding-right: 0 !important; }
        .admin-sidebar.collapsed .nav-badge-admin { width: 46px !important; height: 46px !important; border-radius: 16px !important; font-size: 13px !important; }
        .admin-sidebar.collapsed .nav-link-admin { border-radius: 22px !important; padding-top: 14px !important; padding-bottom: 14px !important; min-height: 74px !important; }
        .admin-sidebar.collapsed .sidebar-brand-admin { width: 34px !important; height: 34px !important; }
        .admin-sidebar.collapsed .sidebar-toggle-admin { font-size: 28px !important; }
        .admin-sidebar.collapsed .logout-admin { border-radius: 20px !important; padding: 14px 12px !important; min-height: 60px !important; }
        @media (max-width: 1024px) {
            .admin-sidebar { transform: translateX(-100%); transition: transform .25s ease; }
            .admin-sidebar.open { transform: translateX(0); }
            .admin-main { margin-left: 0; }
            .admin-main-inner { padding: 20px; }
        }
    </style>
</head>

<body>
    <div class="admin-shell">
        @include('admin.layouts.sidebar')
        <div class="admin-main" id="adminMain">
            @include('admin.layouts.navbar')
            <main class="admin-main-inner">
                @yield('content')
            </main>
        </div>
    </div>

    <script>
        function toggleAdminSidebar() {
            const sidebar = document.getElementById('adminSidebar');
            const main = document.getElementById('adminMain');
            if (!sidebar) return;
            if (window.innerWidth <= 1024) {
                sidebar.classList.toggle('open');
                return;
            }
            sidebar.classList.toggle('collapsed');
            if (main) main.classList.toggle('expanded');
        }

        document.addEventListener('click', function(event) {
            const sidebar = document.getElementById('adminSidebar');
            const toggle = document.getElementById('adminSidebarToggle');
            if (!sidebar || window.innerWidth > 1024) return;
            if (sidebar.contains(event.target) || (toggle && toggle.contains(event.target))) return;
            sidebar.classList.remove('open');
        });
    </script>
</body>

</html>
