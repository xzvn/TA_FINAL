@extends('layouts.customer')

@section('title', 'Ulasan - JasaKampus')

@section('content')
<div class="max-w-7xl mx-auto">
    <div class="grid grid-cols-1 xl:grid-cols-12 gap-6">

        {{-- LEFT CONTENT --}}
        <div class="xl:col-span-9 space-y-6">

            {{-- HEADER --}}
            <div>
                <p class="text-sm text-slate-500">
                    Pesanan / #AL-{{ str_pad($pesanan->id, 4, '0', STR_PAD_LEFT) }} / Penilaian
                </p>
                <h1 class="text-2xl font-bold text-slate-900 mt-2">
                    Berikan Penilaian & Ulasan
                </h1>
                <p class="text-sm text-slate-500 mt-1">
                    Bagikan pengalaman Anda untuk membantu komunitas JasaKampus.
                </p>
            </div>

            {{-- ORDER SUMMARY --}}
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5">
                <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                    <div class="flex items-start gap-4">
                        <div class="w-20 h-20 bg-slate-100 rounded-xl flex items-center justify-center text-slate-400 text-xs">
                            Jasa
                        </div>

                        <div>
                            <h2 class="font-bold text-slate-900">
                                {{ $pesanan->jasa->nama_jasa ?? '-' }}
                            </h2>

                            <p class="text-sm text-slate-500 mt-1">
                                {{ $pesanan->created_at->format('d M Y') }} · #AL-{{ str_pad($pesanan->id, 4, '0', STR_PAD_LEFT) }}
                            </p>

                            <span class="inline-block mt-3 px-3 py-1 bg-green-100 text-green-700 rounded-full text-xs font-bold">
                                Proyek Selesai
                            </span>
                        </div>
                    </div>

                    <div class="text-right">
                        <p class="text-xs text-slate-500">Total Pembayaran</p>
                        <p class="text-xl font-bold text-blue-600 mt-1">
                            Rp {{ number_format($pesanan->total_harga, 0, ',', '.') }}
                        </p>
                    </div>
                </div>
            </div>

            {{-- FREELANCER CARD --}}
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5">
                <div class="flex items-center gap-4">
                    <div class="w-16 h-16 rounded-full bg-slate-100 flex items-center justify-center text-xl font-bold text-blue-600">
                        {{ strtoupper(substr($pesanan->freelancer->nama ?? 'F', 0, 1)) }}
                    </div>

                    <div class="flex-1">
                        <div class="flex items-center gap-2 flex-wrap">
                            <h3 class="font-bold text-slate-900">
                                {{ $pesanan->freelancer->nama ?? 'Freelancer' }}
                            </h3>

                            <span class="px-3 py-1 bg-purple-100 text-purple-700 rounded-full text-xs font-bold">
                                Freelancer Terverifikasi
                            </span>
                        </div>

                        <p class="text-sm text-slate-500 mt-1">
                            {{ $pesanan->jasa->nama_jasa ?? '-' }}
                        </p>
                    </div>
                </div>
            </div>

            {{-- FORM REVIEW --}}
            <form method="POST"
                action="{{ route('customer.order.review.store', $pesanan->id) }}"
                enctype="multipart/form-data"
                class="space-y-6">
                @csrf

                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
                    <h3 class="font-bold text-slate-900 text-lg">
                        Penilaian Detail
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                        @php
                        $ratings = [
                        'rating_kualitas' => 'Kualitas Kerja',
                        'rating_komunikasi' => 'Komunikasi',
                        'rating_waktu' => 'Waktu Pengiriman',
                        'rating_profesionalisme' => 'Profesionalisme',
                        ];
                        @endphp

                        @foreach ($ratings as $name => $label)
                        @php
                        $nilaiLama = old($name, $pesanan->review->$name ?? 0);
                        @endphp

                        <div class="review-rating" data-input="{{ $name }}">
                            <div class="flex items-center justify-between mb-2">
                                <label class="block text-sm font-semibold text-slate-700">
                                    {{ $label }}
                                </label>

                                <span class="text-sm font-bold text-blue-600 rating-value">
                                    {{ $nilaiLama ?: '0.0' }}
                                </span>
                            </div>

                            <input type="hidden"
                                name="{{ $name }}"
                                value="{{ $nilaiLama }}"
                                required>

                            <div class="flex items-center gap-2">
                                @for ($i = 1; $i <= 5; $i++)
                                    <button type="button"
                                    class="star-btn text-3xl leading-none transition {{ $i <= $nilaiLama ? 'text-yellow-400' : 'text-slate-300' }}"
                                    data-value="{{ $i }}">
                                    ★
                                    </button>
                                    @endfor
                            </div>

                            @error($name)
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        @endforeach
                    </div>
                </div>

                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
                    <div class="flex items-center justify-between">
                        <h3 class="font-bold text-slate-900 text-lg">
                            Ulasan Tertulis
                        </h3>

                        <span class="text-xs text-slate-400">
                            Maksimal 1000 karakter
                        </span>
                    </div>

                    <textarea name="ulasan"
                        rows="6"
                        maxlength="1000"
                        class="mt-4 w-full rounded-xl border-slate-300 focus:border-blue-500 focus:ring-blue-500"
                        placeholder="Ceritakan pengalaman Anda bekerja dengan freelancer ini...">{{ old('ulasan', $pesanan->review->ulasan ?? '') }}</textarea>

                    @error('ulasan')
                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                    @enderror

                    <label class="inline-flex items-start gap-3 mt-5">
                        <input type="checkbox"
                            name="rekomendasi"
                            value="1"
                            {{ old('rekomendasi', $pesanan->review->rekomendasi ?? false) ? 'checked' : '' }}
                            class="rounded border-slate-300 text-blue-600 focus:ring-blue-500 mt-1">

                        <span class="text-sm text-slate-600">
                            Saya merekomendasikan freelancer ini kepada pelanggan lain.
                        </span>
                    </label>
                </div>

                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
                    <h3 class="font-bold text-slate-900 text-lg">
                        Unggah Foto Hasil Kerja
                    </h3>

                    <p class="text-sm text-slate-500 mt-1">
                        Opsional. Foto ini akan membantu customer lain melihat hasil pekerjaan.
                    </p>

                    <div class="mt-5 border-2 border-dashed border-slate-300 rounded-2xl p-8 text-center bg-slate-50">
                        <div class="text-4xl mb-3">☁️</div>

                        <p class="text-sm font-semibold text-slate-700">
                            Pilih file JPG atau PNG
                        </p>

                        <p class="text-xs text-slate-400 mt-1">
                            Maksimal ukuran file 5MB.
                        </p>

                        <input type="file"
                            name="foto_review"
                            id="foto_review"
                            accept="image/png,image/jpeg,image/jpg"
                            class="mt-5 w-full rounded-xl border border-slate-300 bg-white p-3 text-sm">

                        <div id="preview-wrapper" class="hidden mt-5">
                            <img id="preview-image"
                                src=""
                                alt="Preview Foto Review"
                                class="mx-auto max-h-56 rounded-xl border border-slate-200">
                        </div>
                    </div>

                    @error('foto_review')
                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center justify-end gap-3">
                    <a href="{{ route('customer.order.show', $pesanan->id) }}"
                        class="px-5 py-3 text-slate-600 font-semibold hover:text-slate-900">
                        Lewati
                    </a>

                    <button type="submit"
                        class="px-6 py-3 bg-blue-600 text-white rounded-xl font-bold hover:bg-blue-700">
                        Kirim Ulasan
                    </button>
                </div>
            </form>

            <script>
                document.querySelectorAll('.review-rating').forEach(function(wrapper) {
                    const input = wrapper.querySelector('input[type="hidden"]');
                    const valueText = wrapper.querySelector('.rating-value');
                    const stars = wrapper.querySelectorAll('.star-btn');

                    function setRating(value) {
                        input.value = value;
                        valueText.textContent = value + '.0';

                        stars.forEach(function(star) {
                            const starValue = Number(star.dataset.value);

                            if (starValue <= value) {
                                star.classList.remove('text-slate-300');
                                star.classList.add('text-yellow-400');
                            } else {
                                star.classList.remove('text-yellow-400');
                                star.classList.add('text-slate-300');
                            }
                        });
                    }

                    stars.forEach(function(star) {
                        star.addEventListener('click', function() {
                            setRating(Number(star.dataset.value));
                        });
                    });
                });

                const fotoInput = document.getElementById('foto_review');
                const previewWrapper = document.getElementById('preview-wrapper');
                const previewImage = document.getElementById('preview-image');

                if (fotoInput) {
                    fotoInput.addEventListener('change', function() {
                        const file = this.files[0];

                        if (!file) {
                            previewWrapper.classList.add('hidden');
                            return;
                        }

                        previewImage.src = URL.createObjectURL(file);
                        previewWrapper.classList.remove('hidden');
                    });
                }
            </script>
        </div>

        {{-- RIGHT SIDEBAR --}}
        <div class="xl:col-span-3 space-y-6">

            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5">
                <h3 class="font-bold text-slate-900">
                    Pratinjau Ulasan Anda
                </h3>

                <div class="mt-4 flex items-start gap-3">
                    <div class="w-12 h-12 rounded-full bg-slate-100 flex items-center justify-center font-bold text-blue-600">
                        {{ strtoupper(substr(Auth::user()->nama, 0, 1)) }}
                    </div>

                    <div>
                        <p class="font-semibold text-slate-900">
                            {{ Auth::user()->nama }}
                        </p>
                        <p class="text-sm text-slate-500">
                            Customer
                        </p>
                    </div>
                </div>

                <div class="mt-4 p-4 bg-slate-50 rounded-xl border border-slate-200">
                    <p class="text-sm text-slate-500">
                        Ulasan Anda akan tampil pada profil freelancer dan detail jasa.
                    </p>
                </div>
            </div>

            <div class="bg-purple-50 rounded-2xl border border-purple-200 shadow-sm p-5">
                <h3 class="font-bold text-purple-800">
                    Aman & Transparan
                </h3>
                <p class="text-sm text-purple-700 mt-2 leading-relaxed">
                    Ulasan Anda membantu menjaga integritas marketplace dan membantu customer lain memilih freelancer yang tepat.
                </p>
            </div>

            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5">
                <h3 class="font-bold text-slate-900">
                    Bantuan Cepat
                </h3>

                <div class="mt-4 space-y-3">
                    <div class="rounded-xl bg-blue-50 border border-blue-100 px-4 py-3 text-sm text-blue-700 leading-relaxed">
                        Berikan ulasan sesuai pengalaman Anda terhadap hasil pekerjaan freelancer.
                        Rating dan komentar akan membantu customer lain menilai kualitas layanan.
                    </div>

                    <a href="{{ route('customer.order.index') }}"
                        class="block text-sm font-bold text-blue-600 hover:underline">
                        Kembali ke Riwayat Pesanan
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection