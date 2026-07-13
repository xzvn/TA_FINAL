<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'Freelancer Panel - JasaKampus')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="{{ (auth()->user()->theme ?? 'light') === 'dark' ? 'theme-dark' : 'theme-light' }} bg-[#f6f8fb] text-slate-800 min-h-screen">
    @php
    $user = auth()->user();
    $isDarkTheme = ($user->theme ?? 'light') === 'dark';

    $sidebarBaseClass = $isDarkTheme
    ? 'bg-white border-r border-slate-200 text-slate-900'
    : 'bg-slate-900 border-r border-slate-800 text-white';

    $sidebarTitleClass = $isDarkTheme ? 'text-slate-900' : 'text-white';
    $sidebarSubtitleClass = $isDarkTheme ? 'text-slate-500' : 'text-slate-300';
    $sidebarBorderClass = $isDarkTheme ? 'border-slate-200' : 'border-slate-800';

    $sidebarInactiveClass = $isDarkTheme
    ? 'text-slate-700 hover:bg-slate-100'
    : 'text-slate-300 hover:bg-slate-800 hover:text-white';

    $logoutClass = $isDarkTheme
    ? 'text-red-600 hover:bg-red-50'
    : 'text-red-300 hover:bg-red-500/10 hover:text-red-200';

    $profileUrl = \Illuminate\Support\Facades\Route::has('freelancer.profile.index')
    ? route('freelancer.profile.index')
    : '#';


    $resolveRoute = function ($routes) {
    foreach ($routes as $route) {
    if (\Illuminate\Support\Facades\Route::has($route)) {
    return route($route);
    }
    }

    return '#';
    };

    $isActive = function ($patterns) {
    foreach ($patterns as $pattern) {
    if (request()->routeIs($pattern)) {
    return true;
    }
    }

    return false;
    };
    @endphp

    {{-- OVERLAY MOBILE --}}
    <div id="sidebarOverlay"
        class="fixed inset-0 bg-slate-900/50 z-30 hidden">
    </div>


    {{-- SIDEBAR --}}
    <aside id="sidebar"
        class="fixed inset-y-0 left-0 z-40 w-72 flex flex-col transform -translate-x-full transition-transform duration-300 ease-in-out shadow-sm {{ $sidebarBaseClass }}">
        {{-- Header Sidebar --}}
        <div class="h-24 px-6 flex items-center justify-between border-b {{ $sidebarBorderClass }} shrink-0">
            <div>
                <h1 class="text-2xl font-bold leading-tight {{ $sidebarTitleClass }}">
                    JasaKampus
                </h1>

                <p class="text-sm mt-1 {{ $sidebarSubtitleClass }}">
                    Panel Freelancer
                </p>
            </div>

            <button type="button"
                id="sidebarClose"
                class="inline-flex items-center justify-center w-11 h-11 rounded-2xl transition shrink-0
            {{ $isDarkTheme ? 'bg-slate-100 text-slate-700 hover:bg-slate-200' : 'bg-white/10 text-white hover:bg-white/20' }}">
                <svg xmlns="http://www.w3.org/2000/svg"
                    class="w-6 h-6"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                    stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        {{-- Menu --}}
        <div class="flex-1 overflow-y-auto overscroll-contain px-4 py-5">

            <div class="mb-6">
                <p class="px-3 text-xs font-semibold uppercase tracking-wider mb-3
                {{ $isDarkTheme ? 'text-slate-400' : 'text-slate-500' }}">
                    Menu Utama
                </p>

                <nav class="space-y-2">
                    {{-- Dashboard --}}
                    <a href="{{ $resolveRoute(['dashboard']) }}"
                        class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold transition
                    {{ $isActive(['dashboard']) ? 'bg-blue-600 text-white shadow-sm' : $sidebarInactiveClass }}">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M3 12l9-9 9 9M5 10v10h14V10" />
                        </svg>
                        <span>Dashboard</span>
                    </a>

                    {{-- Jasa Saya --}}
                    <a href="{{ $resolveRoute(['freelancer.jasa.index']) }}"
                        class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold transition
                    {{ $isActive(['freelancer.jasa.*']) ? 'bg-blue-600 text-white shadow-sm' : $sidebarInactiveClass }}">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M4 7h16M4 12h16M4 17h10" />
                        </svg>
                        <span>Jasa Saya</span>
                    </a>

                    {{-- Chat --}}
                    <a href="{{ $resolveRoute(['freelancer.chat.index']) }}"
                        class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold transition
                    {{ $isActive(['freelancer.chat.*']) ? 'bg-blue-600 text-white shadow-sm' : $sidebarInactiveClass }}">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M8 10h8M8 14h5M21 12a8 8 0 01-8 8H7l-4 3v-6a8 8 0 1118-5z" />
                        </svg>
                        <span>Pesan</span>
                    </a>
                </nav>
            </div>

            <div class="mb-6">
                <p class="px-3 text-xs font-semibold uppercase tracking-wider mb-3
                {{ $isDarkTheme ? 'text-slate-400' : 'text-slate-500' }}">
                    Manajemen Proyek
                </p>

                <nav class="space-y-2">
                    {{-- Proyek --}}
                    <a href="{{ $resolveRoute(['freelancer.pesanan.index', 'freelancer.project.index']) }}"
                        class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold transition
                    {{ $isActive(['freelancer.pesanan.*', 'freelancer.project.*', 'freelancer.progress.*', 'freelancer.hasil.*']) ? 'bg-blue-600 text-white shadow-sm' : $sidebarInactiveClass }}">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M3 7a2 2 0 012-2h5l2 2h7a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V7z" />
                        </svg>
                        <span>Proyek</span>
                    </a>

                    {{-- Earnings --}}
                    <a href="{{ $resolveRoute(['freelancer.earnings.index', 'freelancer.earning.index']) }}"
                        class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold transition
                    {{ $isActive(['freelancer.earnings.*', 'freelancer.earning.*']) ? 'bg-blue-600 text-white shadow-sm' : $sidebarInactiveClass }}">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M4 7h16v10H4V7zm3 3h.01M17 14h.01M12 12a2 2 0 100-4 2 2 0 000 4z" />
                        </svg>
                        <span>Pendapatan</span>
                    </a>

                    {{-- Portofolio --}}
                    <a href="{{ $resolveRoute(['freelancer.portfolio.index']) }}"
                        class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold transition
                    {{ $isActive(['freelancer.portfolio.*']) ? 'bg-blue-600 text-white shadow-sm' : $sidebarInactiveClass }}">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M4 5h16v14H4V5zm3 10l3-3 3 3 2-2 2 2" />
                        </svg>
                        <span>Portofolio</span>
                    </a>
                </nav>
            </div>

            <div>
                <p class="px-3 text-xs font-semibold uppercase tracking-wider mb-3
                {{ $isDarkTheme ? 'text-slate-400' : 'text-slate-500' }}">
                    Akun
                </p>

                <nav class="space-y-2">
                    {{-- Profil --}}
                    <a href="{{ $resolveRoute(['freelancer.profile.index']) }}"
                        class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold transition
                    {{ $isActive(['freelancer.profile.*']) ? 'bg-blue-600 text-white shadow-sm' : $sidebarInactiveClass }}">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M12 12a4 4 0 100-8 4 4 0 000 8zm-7 8a7 7 0 0114 0" />
                        </svg>
                        <span>Profil Freelancer</span>
                    </a>
                </nav>
            </div>
        </div>

        {{-- Logout --}}
        <div class="px-4 py-5 border-t {{ $sidebarBorderClass }}">
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

                <h2 class="text-lg font-bold text-slate-900">
                    @yield('page-title', 'Dashboard Freelancer')
                </h2>
            </div>

            <div class="flex items-center gap-4">
                <div class="hidden sm:block text-right">
                    <p class="text-sm font-semibold text-slate-800">
                        {{ $user->nama }}
                    </p>
                    <p class="text-xs text-slate-500">
                        Freelancer
                    </p>
                </div>

                <x-notifikasi-bell />

                <a href="{{ $profileUrl }}"
                    class="w-10 h-10 rounded-full bg-blue-600 flex items-center justify-center text-white font-bold overflow-hidden hover:ring-4 hover:ring-blue-100 transition"
                    title="Profil Freelancer">
                    @if ($user->foto_profil)
                    <img src="{{ \App\Services\CloudinaryService::mediaUrl($user->foto_profil) }}"
                        alt="Foto Profil"
                        class="w-full h-full object-cover">
                    @else
                    {{ strtoupper(substr($user->nama ?? $user->email, 0, 1)) }}
                    @endif
                </a>
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

            sidebar.querySelectorAll('.sidebar-link').forEach(function(link) {
                link.addEventListener('click', closeSidebar);
            });
        });
    </script>
    @stack('scripts')
</body>

</html>