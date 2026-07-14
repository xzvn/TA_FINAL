<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'Customer Dashboard - JasaKampus')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="{{ (auth()->user()->theme ?? 'light') === 'dark' ? 'theme-dark' : 'theme-light' }} bg-[#f4f7fb] text-slate-800 min-h-screen">
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

    $sidebarSectionClass = $isDarkTheme ? 'text-slate-400' : 'text-slate-500';

    $sidebarCloseClass = $isDarkTheme
    ? 'bg-slate-100 text-slate-700 hover:bg-slate-200'
    : 'bg-white/10 text-white hover:bg-white/20';

    $logoutClass = $isDarkTheme
    ? 'text-red-600 hover:bg-red-50'
    : 'text-red-300 hover:bg-red-500/10 hover:text-red-200';
    @endphp

    <div class="min-h-screen flex flex-col">

        {{-- HEADER --}}
        <header class="h-16 bg-white border-b border-slate-200 flex items-center px-5 sticky top-0 z-50">
            <div class="flex items-center gap-4 w-full">

                {{-- Tombol Sidebar --}}
                <button
                    type="button"
                    id="sidebarToggle"
                    class="inline-flex items-center justify-center w-10 h-10 rounded-xl border border-slate-200 text-slate-700 hover:bg-slate-100 transition"
                    aria-label="Buka menu">
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

                {{-- Logo --}}
                <a href="{{ route('dashboard') }}" class="text-lg font-extrabold text-blue-700 whitespace-nowrap">
                    JasaKampus
                </a>

                {{-- Search Bar --}}
                <form action="{{ route('dashboard') }}" method="GET" class="flex-1 max-w-3xl mx-auto hidden md:block">
                    <input
                        type="text"
                        name="search"
                        value="{{ request('search') }}"
                        placeholder="Cari layanan berdasarkan judul atau kategori..."
                        class="w-full rounded-xl border border-slate-300 px-5 py-2.5 text-sm text-slate-700 focus:border-blue-500 focus:ring-blue-500">

                    <input type="hidden" name="kategori" value="{{ request('kategori') }}">
                    <input type="hidden" name="sort" value="{{ request('sort', 'terlaris') }}">
                </form>

                {{-- Right Menu --}}
                <div class="flex items-center gap-4 ml-auto">

                    <a href="{{ route('customer.favorite.index') }}"
                        class="relative w-10 h-10 rounded-full border border-slate-200 flex items-center justify-center text-slate-600 hover:bg-red-50 hover:text-red-500 transition"
                        title="Favorite Saya">
                        <svg xmlns="http://www.w3.org/2000/svg"
                            class="w-5 h-5"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                            stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M21 8.25c0-2.485-2.1-4.5-4.688-4.5-1.934 0-3.597 1.126-4.312 2.733C11.285 4.876 9.622 3.75 7.688 3.75 5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z" />
                        </svg>
                    </a>

                    <x-notifikasi-bell />

                    {{-- Profil --}}
                    <a href="{{ route('customer.profile.index') }}"
                        class="flex items-center gap-3 group">

                        <div class="w-10 h-10 rounded-full bg-blue-600 text-white flex items-center justify-center overflow-hidden font-bold shadow-sm group-hover:ring-4 group-hover:ring-blue-100 transition">
                            @if ($user->foto_profil)
                            <img src="{{ \App\Services\CloudinaryService::mediaUrl($user->foto_profil) }}"
                                alt="Foto Profil"
                                onerror="this.onerror=null;this.src='{{ asset('images/placeholder-avatar.svg') }}';"
                                class="w-full h-full object-cover">
                            @else
                            {{ strtoupper(substr($user->nama ?? $user->email, 0, 1)) }}
                            @endif
                        </div>

                        <div class="hidden md:block leading-tight">
                            <p class="text-sm font-bold text-slate-800">
                                {{ $user->nama ?? 'Customer' }}
                            </p>
                            <p class="text-xs text-slate-400">
                                Profil
                            </p>
                        </div>
                    </a>
                </div>
            </div>
        </header>

        {{-- OVERLAY SIDEBAR --}}
        <div
            id="sidebarOverlay"
            class="fixed inset-0 bg-slate-900/50 z-40 hidden">
        </div>

        {{-- SIDEBAR --}}
        <aside
            id="sidebar"
            class="fixed inset-y-0 left-0 z-50 w-72 flex flex-col transform -translate-x-full transition-transform duration-300 ease-in-out shadow-sm {{ $sidebarBaseClass }}">

            {{-- Header Sidebar --}}
            <div class="h-24 px-6 flex items-center justify-between border-b {{ $sidebarBorderClass }} shrink-0">
                <div>
                    <h1 class="text-2xl font-bold leading-tight {{ $sidebarTitleClass }}">
                        JasaKampus
                    </h1>

                    <p class="text-sm mt-1 {{ $sidebarSubtitleClass }}">
                        Menu Customer
                    </p>
                </div>

                <button
                    type="button"
                    id="sidebarClose"
                    class="inline-flex items-center justify-center w-11 h-11 rounded-2xl transition shrink-0 {{ $sidebarCloseClass }}"
                    aria-label="Tutup menu">
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
                    <p class="px-3 text-xs font-semibold uppercase tracking-wider mb-3 {{ $sidebarSectionClass }}">
                        Menu Utama
                    </p>

                    <nav class="space-y-2">
                        {{-- Beranda --}}
                        <a href="{{ url('/dashboard') }}"
                            class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold transition
                            {{ request()->routeIs('dashboard') ? 'bg-blue-600 text-white shadow-sm' : $sidebarInactiveClass }}">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M3 12l9-9 9 9M5 10v10h14V10" />
                            </svg>
                            <span>Beranda</span>
                        </a>

                        {{-- Pesanan --}}
                        <a href="{{ route('customer.order.index') }}"
                            class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold transition
                            {{ request()->routeIs('customer.order.*') ? 'bg-blue-600 text-white shadow-sm' : $sidebarInactiveClass }}">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M6 7h12l-1 13H7L6 7zM9 7a3 3 0 016 0" />
                            </svg>
                            <span>Pesanan</span>
                        </a>

                        {{-- Pesan --}}
                        <a href="{{ route('customer.chat.index') }}"
                            class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold transition
                            {{ request()->routeIs('customer.chat.*') ? 'bg-blue-600 text-white shadow-sm' : $sidebarInactiveClass }}">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M8 10h8M8 14h5M21 12a8 8 0 01-8 8H7l-4 3v-6a8 8 0 1118-5z" />
                            </svg>
                            <span>Pesan</span>
                        </a>

                        {{-- Pembayaran --}}
                        <a href="{{ route('customer.payment.index') }}"
                            class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold transition
                            {{ request()->routeIs('customer.payment.*') ? 'bg-blue-600 text-white shadow-sm' : $sidebarInactiveClass }}">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                <rect x="3" y="6" width="18" height="12" rx="2" ry="2"></rect>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18" />
                            </svg>
                            <span>Pembayaran</span>
                        </a>
                    </nav>
                </div>

                <div class="mb-6">
                    <p class="px-3 text-xs font-semibold uppercase tracking-wider mb-3 {{ $sidebarSectionClass }}">
                        Aktivitas
                    </p>

                    <nav class="space-y-2">
                        {{-- Ulasan --}}
                        <a href="{{ route('customer.review.index') }}"
                            class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold transition
                            {{ request()->routeIs('customer.review.*') || request()->routeIs('customer.order.review.*') ? 'bg-blue-600 text-white shadow-sm' : $sidebarInactiveClass }}">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M12 3l2.7 5.47 6.03.88-4.36 4.25 1.03 6-5.4-2.84-5.4 2.84 1.03-6-4.36-4.25 6.03-.88L12 3z" />
                            </svg>
                            <span>Ulasan</span>
                        </a>

                        {{-- Progress --}}
                        <a href="{{ route('customer.progress.index') }}"
                            class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold transition
                            {{ request()->routeIs('customer.progress.*') ? 'bg-blue-600 text-white shadow-sm' : $sidebarInactiveClass }}">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M4 19V5m0 14h16M8 16v-5m4 5V8m4 8v-3" />
                            </svg>
                            <span>Progress</span>
                        </a>
                    </nav>
                </div>

                <div>
                    <p class="px-3 text-xs font-semibold uppercase tracking-wider mb-3 {{ $sidebarSectionClass }}">
                        Akun
                    </p>

                    <nav class="space-y-2">
                        {{-- Profil --}}
                        <a href="{{ route('customer.profile.index') }}"
                            class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold transition
                            {{ request()->routeIs('customer.profile.*') ? 'bg-blue-600 text-white shadow-sm' : $sidebarInactiveClass }}">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M12 12a4 4 0 100-8 4 4 0 000 8zm-7 8a7 7 0 0114 0" />
                            </svg>
                            <span>Profil Customer</span>
                        </a>
                    </nav>
                </div>
            </div>

            {{-- Logout --}}
            <div class="p-4 border-t {{ $sidebarBorderClass }}">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <button
                        type="submit"
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

        {{-- MAIN CONTENT --}}
        <main class="flex-1">
            <div class="min-h-[calc(100vh-64px)]">
                @if (session('success'))
                <div class="mx-6 mt-6 px-5 py-4 bg-green-100 text-green-700 rounded-xl border border-green-200">
                    {{ session('success') }}
                </div>
                @endif

                @yield('content')
            </div>

            {{-- FOOTER --}}
            <footer class="bg-white border-t border-slate-200 px-8 py-8">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-8 text-sm">
                    <div>
                        <h3 class="font-bold text-blue-700 text-lg">
                            JasaKampus
                        </h3>
                        <p class="text-slate-500 mt-3 leading-relaxed">
                            Memberdayakan mahasiswa untuk membuka peluang jasa,
                            membangun portofolio, dan bertransaksi dengan aman.
                        </p>
                    </div>

                    <div>
                        <h4 class="font-bold text-slate-800 mb-3">PASAR</h4>
                        <p class="text-slate-500 mb-2">Grafis & Desain</p>
                        <p class="text-slate-500 mb-2">Pemrograman Digital</p>
                        <p class="text-slate-500 mb-2">Penulisan & Terjemahan</p>
                        <p class="text-slate-500">Video & Animasi</p>
                    </div>

                    <div>
                        <h4 class="font-bold text-slate-800 mb-3">TENTANG</h4>
                        <p class="text-slate-500 mb-2">Karier</p>
                        <p class="text-slate-500 mb-2">Pers & Berita</p>
                        <p class="text-slate-500 mb-2">Kemitraan</p>
                        <p class="text-slate-500">Kebijakan Privasi</p>
                    </div>

                    <div>
                        <h4 class="font-bold text-slate-800 mb-3">KOMUNITAS</h4>
                        <p class="text-slate-500 mb-2">Acara</p>
                        <p class="text-slate-500 mb-2">Blog</p>
                        <p class="text-slate-500 mb-2">Forum</p>
                        <p class="text-slate-500">Afiliasi</p>
                    </div>
                </div>

                <div class="mt-8 pt-5 border-t border-slate-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 text-xs text-slate-500">
                    <p>© 2024 JasaKampus Inc. Hak cipta dilindungi undang-undang.</p>
                </div>
            </footer>
        </main>
    </div>

    @stack('scripts')

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const sidebarToggle = document.getElementById('sidebarToggle');
            const sidebarClose = document.getElementById('sidebarClose');
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebarOverlay');

            if (!sidebar || !overlay || !sidebarToggle) {
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

            sidebarToggle.addEventListener('click', function() {
                if (sidebar.classList.contains('-translate-x-full')) {
                    openSidebar();
                } else {
                    closeSidebar();
                }
            });

            if (sidebarClose) {
                sidebarClose.addEventListener('click', closeSidebar);
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

</body>

</html>