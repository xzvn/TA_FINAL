<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0">

    <title>
        @yield('title', 'Customer Dashboard - JasaKampus')
    </title>

    @vite([
    'resources/css/app.css',
    'resources/js/app.js',
    ])

    @stack('head')
</head>

<body
    class="{{ (auth()->user()->theme ?? 'light') === 'dark'
        ? 'theme-dark'
        : 'theme-light' }}
        min-h-screen bg-[#f4f7fb] text-slate-800">

    @php
    $user = auth()->user();

    $isDarkTheme =
    ($user->theme ?? 'light') === 'dark';

    $sidebarBaseClass = $isDarkTheme
    ? 'bg-white border-r border-slate-200 text-slate-900'
    : 'bg-slate-900 border-r border-slate-800 text-white';

    $sidebarTitleClass = $isDarkTheme
    ? 'text-slate-900'
    : 'text-white';

    $sidebarSubtitleClass = $isDarkTheme
    ? 'text-slate-500'
    : 'text-slate-300';

    $sidebarBorderClass = $isDarkTheme
    ? 'border-slate-200'
    : 'border-slate-800';

    $sidebarInactiveClass = $isDarkTheme
    ? 'text-slate-700 hover:bg-slate-100'
    : 'text-slate-300 hover:bg-slate-800 hover:text-white';

    $sidebarSectionClass = $isDarkTheme
    ? 'text-slate-400'
    : 'text-slate-500';

    $sidebarCloseClass = $isDarkTheme
    ? 'bg-slate-100 text-slate-700 hover:bg-slate-200'
    : 'bg-white/10 text-white hover:bg-white/20';

    $logoutClass = $isDarkTheme
    ? 'text-red-600 hover:bg-red-50'
    : 'text-red-300 hover:bg-red-500/10 hover:text-red-200';
    @endphp

    <div class="flex min-h-screen flex-col">

        {{-- HEADER --}}
        <header
            class="sticky top-0 z-50 flex h-16 items-center border-b border-slate-200 bg-white px-5">

            <div class="flex w-full items-center gap-4">

                {{-- TOMBOL SIDEBAR --}}
                <button
                    type="button"
                    id="sidebarToggle"
                    aria-label="Buka menu"
                    class="inline-flex h-10 w-10 items-center justify-center rounded-xl border border-slate-200 text-slate-700 transition hover:bg-slate-100">

                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="h-6 w-6"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                        stroke-width="2">

                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M4 6h16M4 12h16M4 18h16">
                        </path>
                    </svg>
                </button>

                {{-- LOGO --}}
                <a
                    href="{{ route('dashboard') }}"
                    class="whitespace-nowrap text-lg font-extrabold text-blue-700">

                    JasaKampus
                </a>

                {{-- SEARCH BAR --}}
                <form
                    action="{{ route('dashboard') }}"
                    method="GET"
                    class="mx-auto hidden max-w-3xl flex-1 md:block">

                    <input
                        type="text"
                        name="search"
                        value="{{ request('search') }}"
                        placeholder="Cari layanan berdasarkan judul atau kategori..."
                        class="w-full rounded-xl border border-slate-300 px-5 py-2.5 text-sm text-slate-700 focus:border-blue-500 focus:ring-blue-500">

                    <input
                        type="hidden"
                        name="kategori"
                        value="{{ request('kategori') }}">

                    <input
                        type="hidden"
                        name="sort"
                        value="{{ request('sort', 'terlaris') }}">
                </form>

                {{-- MENU KANAN --}}
                <div class="ml-auto flex items-center gap-4">

                    {{-- FAVORIT --}}
                    <a
                        href="{{ route('customer.favorite.index') }}"
                        title="Favorit Saya"
                        class="relative flex h-10 w-10 items-center justify-center rounded-full border border-slate-200 text-slate-600 transition hover:bg-red-50 hover:text-red-500">

                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="h-5 w-5"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                            stroke-width="1.8">

                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M21 8.25c0-2.485-2.1-4.5-4.688-4.5-1.934 0-3.597 1.126-4.312 2.733C11.285 4.876 9.622 3.75 7.688 3.75 5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z">
                            </path>
                        </svg>
                    </a>

                    {{-- NOTIFIKASI --}}
                    <x-notifikasi-bell />

                    {{-- PROFIL --}}
                    <a
                        href="{{ route('customer.profile.index') }}"
                        class="group flex items-center gap-3">

                        <div
                            class="flex h-10 w-10 items-center justify-center overflow-hidden rounded-full bg-blue-600 font-bold text-white shadow-sm transition group-hover:ring-4 group-hover:ring-blue-100">

                            @if ($user->foto_profil)
                            <img
                                src="{{ \App\Services\CloudinaryService::mediaUrl($user->foto_profil) }}"
                                alt="Foto Profil"
                                class="h-full w-full object-cover"
                                onerror="this.onerror=null;this.src='{{ asset('images/placeholder-avatar.svg') }}';">
                            @else
                            {{ strtoupper(
                                    substr(
                                        $user->nama ?? $user->email,
                                        0,
                                        1
                                    )
                                ) }}
                            @endif
                        </div>

                        <div class="hidden leading-tight md:block">
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
            class="fixed inset-0 z-40 hidden bg-slate-900/50">
        </div>

        {{-- SIDEBAR --}}
        <aside
            id="sidebar"
            class="fixed inset-y-0 left-0 z-50 flex w-72 -translate-x-full transform flex-col shadow-sm transition-transform duration-300 ease-in-out {{ $sidebarBaseClass }}">

            {{-- HEADER SIDEBAR --}}
            <div
                class="flex h-24 shrink-0 items-center justify-between border-b px-6 {{ $sidebarBorderClass }}">

                <div>
                    <h1
                        class="text-2xl font-bold leading-tight {{ $sidebarTitleClass }}">

                        JasaKampus
                    </h1>

                    <p
                        class="mt-1 text-sm {{ $sidebarSubtitleClass }}">

                        Menu Customer
                    </p>
                </div>

                <button
                    type="button"
                    id="sidebarClose"
                    aria-label="Tutup menu"
                    class="inline-flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl transition {{ $sidebarCloseClass }}">

                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="h-6 w-6"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                        stroke-width="2">

                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M6 18L18 6M6 6l12 12">
                        </path>
                    </svg>
                </button>
            </div>

            {{-- MENU --}}
            <div
                class="flex-1 overflow-y-auto overscroll-contain px-4 py-5">

                {{-- MENU UTAMA --}}
                <div class="mb-6">
                    <p
                        class="mb-3 px-3 text-xs font-semibold uppercase tracking-wider {{ $sidebarSectionClass }}">

                        Menu Utama
                    </p>

                    <nav class="space-y-2">

                        {{-- BERANDA --}}
                        <a
                            href="{{ route('dashboard') }}"
                            class="sidebar-link flex items-center gap-3 rounded-xl px-4 py-3 text-sm font-semibold transition
                            {{ request()->routeIs('dashboard')
                                ? 'bg-blue-600 text-white shadow-sm'
                                : $sidebarInactiveClass }}">

                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="h-5 w-5"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                                stroke-width="1.8">

                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="M3 12l9-9 9 9M5 10v10h14V10">
                                </path>
                            </svg>

                            <span>Beranda</span>
                        </a>

                        {{-- PESANAN --}}
                        <a
                            href="{{ route('customer.order.index') }}"
                            class="sidebar-link flex items-center gap-3 rounded-xl px-4 py-3 text-sm font-semibold transition
                            {{ request()->routeIs('customer.order.*')
                                ? 'bg-blue-600 text-white shadow-sm'
                                : $sidebarInactiveClass }}">

                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="h-5 w-5"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                                stroke-width="1.8">

                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="M6 7h12l-1 13H7L6 7zM9 7a3 3 0 016 0">
                                </path>
                            </svg>

                            <span>Pesanan</span>
                        </a>

                        {{-- PESAN --}}
                        <a
                            href="{{ route('customer.chat.index') }}"
                            class="sidebar-link flex items-center gap-3 rounded-xl px-4 py-3 text-sm font-semibold transition
                            {{ request()->routeIs('customer.chat.*')
                                ? 'bg-blue-600 text-white shadow-sm'
                                : $sidebarInactiveClass }}">

                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="h-5 w-5"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                                stroke-width="1.8">

                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="M8 10h8M8 14h5M21 12a8 8 0 01-8 8H7l-4 3v-6a8 8 0 1118-5z">
                                </path>
                            </svg>

                            <span>Pesan</span>
                        </a>

                        {{-- PEMBAYARAN --}}
                        <a
                            href="{{ route('customer.payment.index') }}"
                            class="sidebar-link flex items-center gap-3 rounded-xl px-4 py-3 text-sm font-semibold transition
                            {{ request()->routeIs('customer.payment.*')
                                ? 'bg-blue-600 text-white shadow-sm'
                                : $sidebarInactiveClass }}">

                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="h-5 w-5"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                                stroke-width="1.8">

                                <rect
                                    x="3"
                                    y="6"
                                    width="18"
                                    height="12"
                                    rx="2"
                                    ry="2">
                                </rect>

                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="M3 10h18">
                                </path>
                            </svg>

                            <span>Pembayaran</span>
                        </a>
                    </nav>
                </div>

                {{-- AKTIVITAS --}}
                <div class="mb-6">
                    <p
                        class="mb-3 px-3 text-xs font-semibold uppercase tracking-wider {{ $sidebarSectionClass }}">

                        Aktivitas
                    </p>

                    <nav class="space-y-2">

                        {{-- ULASAN --}}
                        <a
                            href="{{ route('customer.review.index') }}"
                            class="sidebar-link flex items-center gap-3 rounded-xl px-4 py-3 text-sm font-semibold transition
                            {{ request()->routeIs('customer.review.*')
                                || request()->routeIs('customer.order.review.*')
                                    ? 'bg-blue-600 text-white shadow-sm'
                                    : $sidebarInactiveClass }}">

                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="h-5 w-5"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                                stroke-width="1.8">

                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="M12 3l2.7 5.47 6.03.88-4.36 4.25 1.03 6-5.4-2.84-5.4 2.84 1.03-6-4.36-4.25 6.03-.88L12 3z">
                                </path>
                            </svg>

                            <span>Ulasan</span>
                        </a>

                        {{-- PROGRESS --}}
                        <a
                            href="{{ route('customer.progress.index') }}"
                            class="sidebar-link flex items-center gap-3 rounded-xl px-4 py-3 text-sm font-semibold transition
                            {{ request()->routeIs('customer.progress.*')
                                ? 'bg-blue-600 text-white shadow-sm'
                                : $sidebarInactiveClass }}">

                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="h-5 w-5"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                                stroke-width="1.8">

                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="M4 19V5m0 14h16M8 16v-5m4 5V8m4 8v-3">
                                </path>
                            </svg>

                            <span>Progress</span>
                        </a>
                    </nav>
                </div>

                {{-- AKUN --}}
                <div>
                    <p
                        class="mb-3 px-3 text-xs font-semibold uppercase tracking-wider {{ $sidebarSectionClass }}">

                        Akun
                    </p>

                    <nav class="space-y-2">

                        {{-- PROFIL --}}
                        <a
                            href="{{ route('customer.profile.index') }}"
                            class="sidebar-link flex items-center gap-3 rounded-xl px-4 py-3 text-sm font-semibold transition
                            {{ request()->routeIs('customer.profile.*')
                                ? 'bg-blue-600 text-white shadow-sm'
                                : $sidebarInactiveClass }}">

                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="h-5 w-5"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                                stroke-width="1.8">

                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="M12 12a4 4 0 100-8 4 4 0 000 8zm-7 8a7 7 0 0114 0">
                                </path>
                            </svg>

                            <span>Profil Customer</span>
                        </a>
                    </nav>
                </div>
            </div>

            {{-- LOGOUT --}}
            <div
                class="border-t p-4 {{ $sidebarBorderClass }}">

                <form
                    method="POST"
                    action="{{ route('logout') }}">

                    @csrf

                    <button
                        type="submit"
                        class="flex w-full items-center gap-3 rounded-xl px-4 py-3 text-sm font-semibold transition {{ $logoutClass }}">

                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="h-5 w-5"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                            stroke-width="1.8">

                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M15 12H3m0 0l4-4m-4 4l4 4M21 5v14a2 2 0 01-2 2h-6">
                            </path>
                        </svg>

                        <span>Logout</span>
                    </button>
                </form>
            </div>
        </aside>

        {{-- MAIN CONTENT --}}
        <main class="flex-1">
            <div class="min-h-[calc(100vh-64px)]">

                {{-- FLASH SUCCESS GLOBAL --}}
                @if (session('success'))
                <div
                    class="mx-6 mt-6 rounded-xl border border-green-200 bg-green-100 px-5 py-4 text-green-700">

                    {{ session('success') }}
                </div>
                @endif

                @yield('content')
            </div>

            {{-- FOOTER --}}
            <footer
                class="border-t border-slate-200 bg-white px-8 py-8">

                <div
                    class="grid grid-cols-1 gap-8 text-sm md:grid-cols-4">

                    <div>
                        <h3 class="text-lg font-bold text-blue-700">
                            JasaKampus
                        </h3>

                        <p
                            class="mt-3 leading-relaxed text-slate-500">

                            Memberdayakan mahasiswa untuk membuka
                            peluang jasa, membangun portofolio, dan
                            bertransaksi dengan aman.
                        </p>
                    </div>

                    <div>
                        <h4 class="mb-3 font-bold text-slate-800">
                            PASAR
                        </h4>

                        <p class="mb-2 text-slate-500">
                            Grafis & Desain
                        </p>

                        <p class="mb-2 text-slate-500">
                            Pemrograman Digital
                        </p>

                        <p class="mb-2 text-slate-500">
                            Penulisan & Terjemahan
                        </p>

                        <p class="text-slate-500">
                            Video & Animasi
                        </p>
                    </div>

                    <div>
                        <h4 class="mb-3 font-bold text-slate-800">
                            TENTANG
                        </h4>

                        <p class="mb-2 text-slate-500">
                            Karier
                        </p>

                        <p class="mb-2 text-slate-500">
                            Pers & Berita
                        </p>

                        <p class="mb-2 text-slate-500">
                            Kemitraan
                        </p>

                        <p class="text-slate-500">
                            Kebijakan Privasi
                        </p>
                    </div>

                    <div>
                        <h4 class="mb-3 font-bold text-slate-800">
                            KOMUNITAS
                        </h4>

                        <p class="mb-2 text-slate-500">
                            Acara
                        </p>

                        <p class="mb-2 text-slate-500">
                            Blog
                        </p>

                        <p class="mb-2 text-slate-500">
                            Forum
                        </p>

                        <p class="text-slate-500">
                            Afiliasi
                        </p>
                    </div>
                </div>

                <div
                    class="mt-8 flex flex-col gap-4 border-t border-slate-100 pt-5 text-xs text-slate-500 sm:flex-row sm:items-center sm:justify-between">

                    <p>
                        © {{ now()->year }} JasaKampus Inc.
                        Hak cipta dilindungi undang-undang.
                    </p>
                </div>
            </footer>
        </main>
    </div>

    {{-- SCRIPT DARI HALAMAN --}}
    @stack('scripts')

    {{-- SCRIPT SIDEBAR --}}
    <script>
        document.addEventListener(
            'DOMContentLoaded',
            function() {
                const sidebarToggle =
                    document.getElementById(
                        'sidebarToggle'
                    );

                const sidebarClose =
                    document.getElementById(
                        'sidebarClose'
                    );

                const sidebar =
                    document.getElementById(
                        'sidebar'
                    );

                const overlay =
                    document.getElementById(
                        'sidebarOverlay'
                    );

                if (
                    !sidebar ||
                    !overlay ||
                    !sidebarToggle
                ) {
                    return;
                }

                function lockPageScroll() {
                    document.documentElement
                        .classList
                        .add('overflow-hidden');

                    document.body
                        .classList
                        .add('overflow-hidden');
                }

                function unlockPageScroll() {
                    document.documentElement
                        .classList
                        .remove('overflow-hidden');

                    document.body
                        .classList
                        .remove('overflow-hidden');
                }

                function openSidebar() {
                    sidebar.classList.remove(
                        '-translate-x-full'
                    );

                    overlay.classList.remove(
                        'hidden'
                    );

                    lockPageScroll();
                }

                function closeSidebar() {
                    sidebar.classList.add(
                        '-translate-x-full'
                    );

                    overlay.classList.add(
                        'hidden'
                    );

                    unlockPageScroll();
                }

                sidebarToggle.addEventListener(
                    'click',
                    function() {
                        if (
                            sidebar.classList.contains(
                                '-translate-x-full'
                            )
                        ) {
                            openSidebar();
                        } else {
                            closeSidebar();
                        }
                    }
                );

                if (sidebarClose) {
                    sidebarClose.addEventListener(
                        'click',
                        closeSidebar
                    );
                }

                overlay.addEventListener(
                    'click',
                    closeSidebar
                );

                document.addEventListener(
                    'keydown',
                    function(event) {
                        if (event.key === 'Escape') {
                            closeSidebar();
                        }
                    }
                );

                sidebar
                    .querySelectorAll(
                        '.sidebar-link'
                    )
                    .forEach(function(link) {
                        link.addEventListener(
                            'click',
                            closeSidebar
                        );
                    });
            }
        );
    </script>
</body>

</html>