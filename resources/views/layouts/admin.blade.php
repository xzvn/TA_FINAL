<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'Admin Dashboard - JasaKampus')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <!-- <style>
        body.theme-dark {
            background: #0f172a !important;
            color: #e5e7eb !important;
        }

        body.theme-dark main {
            background: #0f172a !important;
        }

        body.theme-dark header {
            background: #111827 !important;
            border-color: #1e293b !important;
        }

        body.theme-dark #sidebar {
            background: #ffffff !important;
            color: #0f172a !important;
        }

        body.theme-dark #sidebar h1,
        body.theme-dark #sidebar p,
        body.theme-dark #sidebar span {
            color: #0f172a !important;
        }

        body.theme-dark #sidebar a {
            color: #0f172a !important;
        }

        body.theme-dark #sidebar a:hover {
            background: #e0ecff !important;
            color: #1d4ed8 !important;
        }

        body.theme-dark #sidebar .bg-blue-600 {
            background: #2563eb !important;
            color: #ffffff !important;
        }

        body.theme-dark #sidebar .bg-blue-600 span {
            color: #ffffff !important;
        }

        body.theme-dark .bg-white,
        body.theme-dark .theme-card {
            background: #1e293b !important;
            border-color: #334155 !important;
        }

        body.theme-dark .text-slate-900,
        body.theme-dark .text-slate-800,
        body.theme-dark .theme-text {
            color: #f8fafc !important;
        }

        body.theme-dark .text-slate-700,
        body.theme-dark .text-slate-600,
        body.theme-dark .text-slate-500,
        body.theme-dark .theme-muted {
            color: #cbd5e1 !important;
        }

        body.theme-dark input,
        body.theme-dark select,
        body.theme-dark textarea {
            background: #0f172a !important;
            color: #f8fafc !important;
            border-color: #475569 !important;
        }

        body.theme-dark input::placeholder {
            color: #94a3b8 !important;
        }
    </style> -->
</head>

<body class="{{ auth()->user()->theme === 'dark' ? 'theme-dark' : 'theme-light' }} bg-[#f6f8fb] text-slate-800 min-h-screen">

    {{-- OVERLAY MOBILE --}}
    <div id="sidebarOverlay"
        class="fixed inset-0 bg-slate-900/50 z-30 hidden">
    </div>

    {{-- SIDEBAR --}}
    @php
    $isDarkTheme = auth()->user()->theme === 'dark';

    $sidebarBaseClass = $isDarkTheme
    ? 'bg-white border-r border-slate-200 text-slate-900'
    : 'bg-slate-900 border-r border-slate-800 text-white';

    $sidebarTitleClass = $isDarkTheme ? 'text-slate-900' : 'text-white';
    $sidebarSubtitleClass = $isDarkTheme ? 'text-slate-500' : 'text-slate-300';
    $sidebarBorderClass = $isDarkTheme ? 'border-slate-200' : 'border-slate-800';
    $sidebarSectionClass = $isDarkTheme ? 'text-slate-400' : 'text-slate-500';

    $sidebarInactiveClass = $isDarkTheme
    ? 'text-slate-700 hover:bg-slate-100'
    : 'text-slate-300 hover:bg-slate-800 hover:text-white';

    $sidebarCloseClass = $isDarkTheme
    ? 'bg-slate-100 text-slate-700 hover:bg-slate-200'
    : 'bg-white/10 text-white hover:bg-white/20';

    $logoutClass = $isDarkTheme
    ? 'text-red-600 hover:bg-red-50'
    : 'text-red-300 hover:bg-red-500/10 hover:text-red-200';
    @endphp
    <aside id="sidebar"
        class="fixed inset-y-0 left-0 z-40 w-[290px] transform -translate-x-full transition-transform duration-300 ease-in-out flex flex-col shadow-sm {{ $sidebarBaseClass }}">
        {{-- Header Sidebar --}}
        <div class="h-24 px-6 flex items-center justify-between border-b {{ $sidebarBorderClass }} shrink-0">
            <div>
                <h1 class="text-2xl font-bold leading-tight {{ $sidebarTitleClass }}">
                    JasaKampus
                </h1>

                <p class="text-sm mt-1 {{ $sidebarSubtitleClass }}">
                    Menu Admin
                </p>
            </div>

            <button type="button"
                id="sidebarClose"
                class="inline-flex items-center justify-center w-12 h-12 rounded-2xl transition shrink-0 {{ $sidebarCloseClass }}">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        {{-- Menu --}}
        <div class="flex-1 overflow-y-auto overscroll-contain px-4 py-5">
            <div class="mb-6">
                <p class="px-3 text-xs font-semibold uppercase tracking-wider mb-3 {{ $sidebarSectionClass }}">
                    Menu Utama
                </p>

                <nav class="space-y-2">
                    {{-- Dashboard --}}
                    <a href="{{ route('dashboard') }}"
                        class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold transition
                    {{ request()->routeIs('dashboard') ? 'bg-blue-600 text-white shadow-sm' : $sidebarInactiveClass }}">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M3 12l9-9 9 9M5 10v10h14V10" />
                        </svg>
                        <span>Dashboard</span>
                    </a>

                    {{-- Verifikasi --}}
                    <a href="{{ route('admin.verifikasi.freelancer') }}"
                        class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold transition
                    {{ request()->routeIs('admin.verifikasi.*') ? 'bg-blue-600 text-white shadow-sm' : $sidebarInactiveClass }}">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M9 12l2 2 4-4M12 3l7 4v5c0 5-3.5 8-7 9-3.5-1-7-4-7-9V7l7-4z" />
                        </svg>
                        <span>Verifikasi Freelancer</span>
                    </a>

                    {{-- Pencairan Dana --}}
                    <a href="{{ route('admin.withdrawals.index') }}"
                        class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold transition
                    {{ request()->routeIs('admin.withdrawals.*') ? 'bg-blue-600 text-white shadow-sm' : $sidebarInactiveClass }}">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M3 10l9-6 9 6M5 10v9h14v-9M9 14h6" />
                        </svg>
                        <span>Pencairan Dana</span>
                    </a>

                    {{-- Kelola Pengguna --}}
                    <a href="{{ route('admin.users.index') }}"
                        class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold transition
                    {{ request()->routeIs('admin.users.*') ? 'bg-blue-600 text-white shadow-sm' : $sidebarInactiveClass }}">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M17 20a4 4 0 00-8 0M12 12a4 4 0 100-8 4 4 0 000 8zm7 8a4 4 0 00-3-3.87M20 8a3 3 0 11-6 0M4 20a4 4 0 013-3.87M4 8a3 3 0 106 0" />
                        </svg>
                        <span>Kelola Pengguna</span>
                    </a>

                    {{-- Pembayaran --}}
                    <a href="{{ route('admin.transactions.index') }}"
                        class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold transition
                    {{ request()->routeIs('admin.transactions.*') ? 'bg-blue-600 text-white shadow-sm' : $sidebarInactiveClass }}">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <rect x="3" y="6" width="18" height="12" rx="2" ry="2"></rect>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18" />
                        </svg>
                        <span>Pembayaran</span>
                    </a>

                    {{-- Aduan --}}
                    <a href="{{ route('admin.disputes.index') }}"
                        class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold transition
                    {{ request()->routeIs('admin.disputes.*') ? 'bg-blue-600 text-white shadow-sm' : $sidebarInactiveClass }}">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M12 9v4m0 4h.01M10.29 3.86l-7 12.14A2 2 0 005 19h14a2 2 0 001.71-3l-7-12.14a2 2 0 00-3.42 0z" />
                        </svg>
                        <span>Aduan</span>
                    </a>

                    {{-- Kelola Jasa --}}
                    <a href="{{ route('admin.jasa.index') }}"
                        class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold transition
                    {{ request()->routeIs('admin.jasa.*') ? 'bg-blue-600 text-white shadow-sm' : $sidebarInactiveClass }}">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M14.7 6.3l3 3m-9.4 9.4H5v-3.3l8.8-8.8a2.121 2.121 0 013 3L8.3 18.7z" />
                        </svg>
                        <span>Kelola Jasa</span>
                    </a>
                </nav>
            </div>

            <div>
                <p class="px-3 text-xs font-semibold uppercase tracking-wider mb-3 {{ $sidebarSectionClass }}">
                    Akun
                </p>

                <nav class="space-y-2">
                    {{-- Profil Admin --}}
                    <a href="{{ route('admin.profile.index') }}"
                        class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold transition
                    {{ request()->routeIs('admin.profile.*') ? 'bg-blue-600 text-white shadow-sm' : $sidebarInactiveClass }}">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M12 12a4 4 0 100-8 4 4 0 000 8zm-7 8a7 7 0 0114 0" />
                        </svg>
                        <span>Profil Admin</span>
                    </a>
                </nav>
            </div>
        </div>

        {{-- Logout --}}
        <div class="p-4 border-t {{ $sidebarBorderClass }}">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                    class="w-full flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold transition {{ $logoutClass }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M15 12H3m0 0l4-4m-4 4l4 4M21 5v14a2 2 0 01-2 2h-6" />
                    </svg>
                    <span>Logout</span>
                </button>
            </form>
        </div>
    </aside>

    {{-- MAIN --}}
    <main class="min-h-screen w-full">

        {{-- TOPBAR --}}
        <header class="h-16 bg-white border-b border-slate-200 flex items-center justify-between px-4 md:px-8">
            <div class="flex items-center gap-3">
                <button type="button"
                    id="sidebarToggle"
                    class="inline-flex items-center justify-center w-11 h-11 rounded-xl bg-white border border-slate-200 text-slate-700 hover:bg-slate-50">
                    <svg xmlns="http://www.w3.org/2000/svg"
                        class="w-6 h-6"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                        stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>

                <h2 class="font-bold text-slate-900 hidden sm:block">
                    Admin Panel
                </h2>
            </div>

            <div class="flex items-center gap-4">
                <div class="relative hidden md:block">
                    <input type="text"
                        placeholder="Cari data..."
                        class="w-72 rounded-lg border-slate-300 text-sm pl-10 pr-4 py-2 focus:ring-blue-500 focus:border-blue-500">
                    <span class="absolute left-3 top-2 text-slate-400">⌕</span>
                </div>

                <x-notifikasi-bell />

                <div class="w-9 h-9 rounded-full bg-slate-600 flex items-center justify-center text-white font-bold">
                    {{ strtoupper(substr(Auth::user()->nama, 0, 1)) }}
                </div>
            </div>
        </header>

        {{-- CONTENT --}}
        <section class="p-4 md:p-8">
            @yield('content')
        </section>

    </main>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebarOverlay');
            const toggle = document.getElementById('sidebarToggle');
            const closeBtn = document.getElementById('sidebarClose');

            if (!sidebar || !overlay || !toggle) {
                return;
            }

            function lockPageScroll() {
                document.documentElement.classList.add('overflow-hidden');
                document.body.classList.add('overflow-hidden');
            }

            function unlockPageScroll() {
                document.documentElement.classList.remove('overflow-hidden');
                document.body.classList.remove('overflow-hidden');
            }

            function openSidebar() {
                sidebar.classList.remove('-translate-x-full');
                overlay.classList.remove('hidden');
                lockPageScroll();
            }

            function closeSidebar() {
                sidebar.classList.add('-translate-x-full');
                overlay.classList.add('hidden');
                unlockPageScroll();
            }

            toggle.addEventListener('click', function() {
                if (sidebar.classList.contains('-translate-x-full')) {
                    openSidebar();
                } else {
                    closeSidebar();
                }
            });

            if (closeBtn) {
                closeBtn.addEventListener('click', closeSidebar);
            }

            overlay.addEventListener('click', closeSidebar);

            document.addEventListener('keydown', function(event) {
                if (event.key === 'Escape') {
                    closeSidebar();
                }
            });

            sidebar.querySelectorAll('a').forEach(function(link) {
                link.addEventListener('click', closeSidebar);
            });
        });
    </script>
</body>

</html>