<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>JasaKampus - Marketplace Jasa Mahasiswa</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-[#f8fafc] text-slate-900">

    {{-- HEADER --}}
    <header class="h-20 bg-white border-b border-slate-200 flex items-center justify-between px-8">
        <a href="{{ url('/') }}" class="text-3xl font-extrabold text-blue-700">
            JasaKampus
        </a>

        <div class="flex flex-wrap items-center justify-end gap-3">
            <a href="{{ route('login.customer') }}"
                class="px-5 py-3 rounded-xl border border-blue-600 text-blue-600 font-bold hover:bg-blue-50 transition">
                Login Customer
            </a>

            <a href="{{ route('login.freelancer') }}"
                class="px-5 py-3 rounded-xl border border-slate-900 text-slate-900 font-bold hover:bg-slate-100 transition">
                Login Freelancer
            </a>

            <a href="{{ route('register') }}"
                class="px-5 py-3 rounded-xl bg-blue-600 text-white font-bold hover:bg-blue-700 transition">
                Daftar Customer
            </a>

            <a href="{{ route('freelancer.register') }}"
                class="px-5 py-3 rounded-xl bg-slate-900 text-white font-bold hover:bg-slate-800 transition">
                Daftar Freelancer
            </a>
        </div>
    </header>

    {{-- HERO --}}
    <main class="px-8 py-20">
        <section class="max-w-7xl mx-auto">

            {{-- TEXT CONTENT --}}
            <div class="max-w-5xl">
                <div class="inline-flex px-6 py-3 rounded-full bg-blue-100 text-blue-700 font-bold mb-10">
                    Platform Jasa Mahasiswa Terverifikasi
                </div>

                <h1 class="text-5xl lg:text-6xl font-extrabold text-slate-950 leading-tight">
                    Hubungkan Kebutuhan Anda dengan Talenta Mahasiswa Terbaik
                </h1>

                <p class="mt-8 text-xl text-slate-600 leading-relaxed max-w-4xl">
                    Temukan mahasiswa berbakat untuk membantu desain grafis, pemrograman,
                    penulisan, riset, editing video, dan berbagai kebutuhan proyek lainnya.
                </p>

                <div class="mt-10 flex flex-wrap gap-4">
                    <a href="{{ route('login.customer') }}"
                        class="px-7 py-4 rounded-xl border border-blue-600 text-blue-600 font-bold hover:bg-blue-50 transition">
                        Login Customer
                    </a>

                    <a href="{{ route('login.freelancer') }}"
                        class="px-7 py-4 rounded-xl border border-slate-900 text-slate-900 font-bold hover:bg-slate-100 transition">
                        Login Freelancer
                    </a>

                    <a href="{{ route('register') }}"
                        class="px-7 py-4 rounded-xl bg-blue-600 text-white font-bold hover:bg-blue-700 transition">
                        Daftar Customer
                    </a>

                    <a href="{{ route('freelancer.register') }}"
                        class="px-7 py-4 rounded-xl bg-slate-900 text-white font-bold hover:bg-slate-800 transition">
                        Daftar Freelancer
                    </a>
                </div>
            </div>

            {{-- JASA TERBAIK --}}
            <div class="mt-16 bg-white rounded-3xl border border-slate-200 shadow-xl p-6">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h2 class="text-2xl font-extrabold text-slate-900">
                            Jasa Terbaik
                        </h2>
                        <p class="text-sm text-slate-500 mt-1">
                            Layanan terbaik berdasarkan rating dari customer.
                        </p>
                    </div>

                    <div class="flex items-center gap-3">
                        <button type="button"
                            id="slideLeft"
                            class="w-10 h-10 rounded-full border border-slate-300 text-slate-600 hover:bg-blue-50 hover:text-blue-600 transition">
                            ‹
                        </button>

                        <button type="button"
                            id="slideRight"
                            class="w-10 h-10 rounded-full border border-slate-300 text-slate-600 hover:bg-blue-50 hover:text-blue-600 transition">
                            ›
                        </button>

                        <a href="{{ route('login.customer') }}"
                            class="text-sm font-bold text-blue-600 hover:underline">
                            Lihat Semua
                        </a>
                    </div>
                </div>

                @if ($jasaLanding->count() > 0)
                <div id="jasaSlider"
                    class="overflow-x-auto scroll-smooth pb-4"
                    style="scrollbar-width: thin;">

                    <div class="flex gap-6 min-w-max">
                        @foreach ($jasaLanding as $item)
                        <div class="w-80 bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden hover:shadow-md transition shrink-0">
                            <div class="h-44 bg-slate-100 overflow-hidden">
                                @if ($item->thumbnail)
                                <img src="{{ \App\Services\CloudinaryService::mediaUrl($item->thumbnail) }}"
                                    alt="{{ $item->nama_jasa }}"
                                    class="w-full h-full object-cover">
                                @else
                                <div class="w-full h-full flex items-center justify-center text-4xl bg-slate-100">
                                    🖼️
                                </div>
                                @endif
                            </div>

                            <div class="p-5">
                                <div class="flex items-start justify-between gap-3">
                                    <div class="min-w-0">
                                        <h3 class="font-extrabold text-slate-900 text-lg leading-snug line-clamp-2 min-h-[56px]">
                                            {{ $item->nama_jasa }}
                                        </h3>

                                        <p class="text-sm text-slate-500 mt-1 truncate">
                                            {{ $item->freelancer->nama ?? 'Freelancer' }}
                                        </p>
                                    </div>

                                    @php
                                    $rating = $item->rating_rata_rata ?? 0;
                                    $totalReview = $item->reviews_count ?? 0;
                                    @endphp

                                    <div class="text-sm font-bold text-blue-600 shrink-0">
                                        ★ {{ number_format((float) $rating, 1) }}
                                        <span class="text-xs text-slate-400">
                                            ({{ $totalReview }})
                                        </span>
                                    </div>
                                </div>

                                <div class="flex flex-wrap gap-2 mt-4">
                                    <span class="px-3 py-1 rounded-lg bg-blue-100 text-blue-700 text-xs font-bold uppercase">
                                        {{ $item->kategori }}
                                    </span>

                                    <span class="px-3 py-1 rounded-lg bg-purple-100 text-purple-700 text-xs font-bold uppercase">
                                        Terverifikasi
                                    </span>
                                </div>

                                <div class="mt-5 pt-4 border-t border-slate-100 flex items-end justify-between gap-3">
                                    <div>
                                        <p class="text-xs text-slate-500">
                                            Estimasi
                                        </p>
                                        <p class="text-sm text-slate-500 mt-1">
                                            {{ $item->estimasi_pengerjaan }}
                                        </p>
                                    </div>

                                    <p class="text-xl font-extrabold text-blue-600">
                                        Rp {{ number_format($item->harga, 0, ',', '.') }}
                                    </p>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @else
                <div class="h-72 rounded-2xl bg-gradient-to-br from-blue-100 to-purple-100 flex items-center justify-center text-center p-8">
                    <div>
                        <div class="text-5xl mb-4">⭐</div>
                        <h3 class="text-xl font-extrabold text-slate-800">
                            Belum ada jasa dengan rating
                        </h3>
                        <p class="text-sm text-slate-500 mt-2">
                            Jasa terbaik akan tampil setelah customer memberikan rating.
                        </p>
                    </div>
                </div>
                @endif
            </div>
        </section>
    </main>
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
                <h4 class="font-bold text-slate-800 mb-3">
                    PASAR
                </h4>
                <p class="text-slate-500 mb-2">Grafis & Desain</p>
                <p class="text-slate-500 mb-2">Pemrograman Digital</p>
                <p class="text-slate-500 mb-2">Penulisan & Terjemahan</p>
                <p class="text-slate-500">Video & Animasi</p>
            </div>

            <div>
                <h4 class="font-bold text-slate-800 mb-3">
                    TENTANG
                </h4>
                <p class="text-slate-500 mb-2">Karier</p>
                <p class="text-slate-500 mb-2">Pers & Berita</p>
                <p class="text-slate-500 mb-2">Kemitraan</p>
                <p class="text-slate-500">Kebijakan Privasi</p>
            </div>

            <div>
                <h4 class="font-bold text-slate-800 mb-3">
                    KOMUNITAS
                </h4>
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
    @guest
    <div id="rolePopup"
        class="fixed inset-0 z-[9999] bg-slate-900/60 backdrop-blur-sm flex items-center justify-center px-4">

        <div class="bg-white w-full max-w-md rounded-3xl shadow-2xl border border-slate-200 overflow-hidden">
            <div class="p-8 text-center">

                <div class="w-16 h-16 mx-auto rounded-2xl bg-blue-100 text-blue-700 flex items-center justify-center text-3xl mb-5">
                    🎓
                </div>

                <h2 class="text-2xl font-extrabold text-slate-900">
                    Masuk ke JasaKampus
                </h2>

                <p class="text-sm text-slate-500 mt-3 leading-relaxed">
                    Pilih jenis akun yang ingin kamu gunakan untuk melanjutkan ke platform.
                </p>

                <div class="mt-7 grid grid-cols-1 gap-4">
                    <a href="{{ route('login.customer') }}"
                        class="w-full px-5 py-4 rounded-2xl bg-blue-600 text-white font-bold hover:bg-blue-700 transition flex items-center justify-center gap-3">
                        <span>🛒</span>
                        <span>Login sebagai Customer</span>
                    </a>

                    <a href="{{ route('login.freelancer') }}"
                        class="w-full px-5 py-4 rounded-2xl bg-slate-900 text-white font-bold hover:bg-slate-800 transition flex items-center justify-center gap-3">
                        <span>💼</span>
                        <span>Login sebagai Freelancer</span>
                    </a>
                </div>

                <div class="mt-6 flex items-center justify-center gap-3 text-sm">
                    <a href="{{ route('register') }}"
                        class="text-blue-600 font-semibold hover:underline">
                        Daftar Customer
                    </a>

                    <span class="text-slate-300">|</span>

                    <a href="{{ route('freelancer.register') }}"
                        class="text-blue-600 font-semibold hover:underline">
                        Daftar Freelancer
                    </a>
                </div>

                <button type="button"
                    id="closeRolePopup"
                    class="mt-6 text-sm text-slate-400 hover:text-slate-700 font-medium">
                    Lihat halaman dulu
                </button>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const popup = document.getElementById('rolePopup');
            const closeButton = document.getElementById('closeRolePopup');

            if (closeButton && popup) {
                closeButton.addEventListener('click', function() {
                    popup.classList.add('hidden');
                });
            }
        });
    </script>
    @endguest

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const slider = document.getElementById('jasaSlider');
            const left = document.getElementById('slideLeft');
            const right = document.getElementById('slideRight');

            if (!slider || !left || !right) {
                return;
            }

            left.addEventListener('click', function() {
                slider.scrollBy({
                    left: -360,
                    behavior: 'smooth'
                });
            });

            right.addEventListener('click', function() {
                slider.scrollBy({
                    left: 360,
                    behavior: 'smooth'
                });
            });
        });
    </script>
</body>

</html>