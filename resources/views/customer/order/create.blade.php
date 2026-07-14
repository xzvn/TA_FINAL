@extends('layouts.customer')

@section('title', 'Buat Order - JasaKampus')

@section('content')
<section class="px-6 py-6">
    <div class="mb-6">
        <p class="text-xs text-slate-500">
            Pasar Jasa / {{ $jasa->kategori }} / Buat Order
        </p>

        <h1 class="text-2xl md:text-3xl font-bold text-slate-900 mt-2">
            Buat Order
        </h1>

        <p class="text-sm text-slate-500 mt-1">
            Lengkapi kebutuhan proyek sebelum melanjutkan ke pembayaran escrow.
        </p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

        {{-- LEFT CONTENT --}}
        <div class="lg:col-span-8 space-y-6">

            {{-- SERVICE SUMMARY --}}
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
                <div class="flex flex-col md:flex-row gap-5">
                    <div class="w-full md:w-40 h-32 rounded-xl overflow-hidden bg-slate-100 shrink-0">
                        @if ($jasa->thumbnail)
                        <img src="{{ \App\Services\CloudinaryService::mediaUrl($jasa->thumbnail) }}"
                            alt="{{ $jasa->nama_jasa }}"
                            onerror="this.onerror=null;this.src='{{ asset('images/placeholder-service.svg') }}';"
                            class="w-full h-full object-cover">
                        @else
                        <div class="w-full h-full bg-gradient-to-br from-blue-100 to-slate-200 flex items-center justify-center">
                            <span class="text-4xl">🖼️</span>
                        </div>
                        @endif
                    </div>

                    <div class="flex-1">
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="px-3 py-1 bg-blue-100 text-blue-700 rounded-full text-xs font-bold">
                                {{ $jasa->kategori }}
                            </span>

                            <span class="px-3 py-1 bg-green-100 text-green-700 rounded-full text-xs font-bold">
                                Freelancer Terverifikasi
                            </span>

                            <span class="px-3 py-1 bg-yellow-100 text-yellow-700 rounded-full text-xs font-bold">
                                ★ 5.0
                            </span>
                        </div>

                        <h2 class="text-xl md:text-2xl font-bold text-slate-900 mt-3">
                            {{ $jasa->nama_jasa }}
                        </h2>

                        <div class="flex items-center gap-3 mt-4">
                            <div class="w-9 h-9 rounded-full bg-blue-600 text-white flex items-center justify-center font-bold">
                                {{ strtoupper(substr($jasa->freelancer->nama ?? 'F', 0, 1)) }}
                            </div>

                            <div>
                                <p class="text-sm font-semibold text-slate-900">
                                    {{ $jasa->freelancer->nama ?? 'Freelancer' }}
                                </p>
                                <p class="text-xs text-slate-500">
                                    {{ $jasa->freelancer->verifikasiFreelancer->universitas ?? 'Freelancer Terverifikasi' }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- FORM --}}
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
                <h2 class="text-xl font-bold text-slate-900">
                    Detail Kebutuhan Proyek
                </h2>

                <p class="text-sm text-slate-500 mt-1">
                    Jelaskan kebutuhan proyek dengan detail agar freelancer dapat mengerjakan sesuai permintaan.
                </p>

                <form method="POST"
                    action="{{ route('customer.order.store', $jasa->id) }}"
                    enctype="multipart/form-data"
                    class="mt-6 space-y-6">
                    @csrf

                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">
                            Deskripsi Kebutuhan
                        </label>

                        <textarea name="deskripsi_kebutuhan"
                            rows="8"
                            class="w-full rounded-xl border-slate-300 focus:border-blue-500 focus:ring-blue-500 text-sm"
                            placeholder="Contoh: Saya membutuhkan desain logo untuk brand makanan. Warna utama biru dan putih, konsep minimalis, file akhir PNG dan PDF..."
                            required>{{ old('deskripsi_kebutuhan') }}</textarea>

                        <p class="text-xs text-slate-500 mt-2">
                            Jelaskan tujuan proyek, konsep, referensi, deadline, dan output akhir yang diharapkan.
                        </p>

                        @error('deskripsi_kebutuhan')
                        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">
                            File Requirement / Brief
                        </label>

                        <div class="border-2 border-dashed border-slate-300 rounded-2xl p-6 bg-slate-50">
                            <div class="flex items-start gap-4">
                                <div class="w-12 h-12 rounded-xl bg-blue-100 text-blue-600 flex items-center justify-center text-xl">
                                    📎
                                </div>

                                <div class="flex-1">
                                    <input type="file"
                                        name="file_requirement"
                                        class="w-full rounded-xl border border-slate-300 bg-white p-3 text-sm">

                                    <p class="text-xs text-slate-500 mt-3">
                                        Opsional. Format: JPG, PNG, PDF, DOC, DOCX, ZIP. Maksimal 5MB.
                                    </p>
                                </div>
                            </div>
                        </div>

                        @error('file_requirement')
                        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="p-5 bg-yellow-50 border border-yellow-200 rounded-2xl">
                        <h3 class="font-bold text-yellow-800">
                            Catatan Penting
                        </h3>

                        <p class="text-sm text-yellow-700 mt-1">
                            Dana akan ditahan sementara oleh sistem escrow setelah pembayaran berhasil. Dana baru dicairkan ke freelancer setelah pekerjaan disetujui customer.
                        </p>
                    </div>

                    <div class="flex flex-col md:flex-row gap-3">
                        <a href="{{ route('customer.jasa.show', $jasa->id) }}"
                            class="md:w-1/3 text-center px-5 py-3 bg-slate-100 text-slate-700 rounded-xl font-bold hover:bg-slate-200">
                            Kembali
                        </a>

                        <button type="submit"
                            class="md:w-2/3 px-5 py-3 bg-blue-600 text-white rounded-xl font-bold hover:bg-blue-700">
                            Buat Order
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- RIGHT SIDEBAR --}}
        <aside class="lg:col-span-4">
            <div class="lg:sticky lg:top-24 space-y-6">

                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
                    <div class="flex items-center justify-between">
                        <p class="text-sm text-slate-500">
                            Total Pembayaran
                        </p>

                        <span class="px-3 py-1 bg-blue-100 text-blue-700 rounded-full text-xs font-bold">
                            Escrow
                        </span>
                    </div>

                    <p class="text-3xl font-bold text-blue-600 mt-3">
                        Rp {{ number_format($jasa->harga, 0, ',', '.') }}
                    </p>

                    <div class="mt-6 space-y-4 text-sm">
                        <div class="flex items-start justify-between gap-4">
                            <span class="text-slate-500">Nama Jasa</span>
                            <span class="font-semibold text-slate-900 text-right max-w-[180px]">
                                {{ $jasa->nama_jasa }}
                            </span>
                        </div>

                        <div class="flex items-center justify-between">
                            <span class="text-slate-500">Freelancer</span>
                            <span class="font-semibold text-slate-900">
                                {{ $jasa->freelancer->nama ?? '-' }}
                            </span>
                        </div>

                        <div class="flex items-center justify-between">
                            <span class="text-slate-500">Estimasi</span>
                            <span class="font-semibold text-slate-900">
                                {{ $jasa->estimasi_pengerjaan }}
                            </span>
                        </div>

                        <div class="flex items-center justify-between">
                            <span class="text-slate-500">Revisi</span>
                            <span class="font-semibold text-slate-900">
                                Maksimal 3 kali
                            </span>
                        </div>

                        <div class="flex items-center justify-between">
                            <span class="text-slate-500">Pembayaran</span>
                            <span class="font-semibold text-slate-900">
                                Ditahan escrow
                            </span>
                        </div>

                        <div class="border-t border-slate-100 pt-4 flex items-center justify-between">
                            <span class="text-slate-900 font-bold">
                                Subtotal
                            </span>

                            <span class="text-blue-600 font-bold">
                                Rp {{ number_format($jasa->harga, 0, ',', '.') }}
                            </span>
                        </div>
                    </div>

                    <div class="mt-6 p-4 bg-blue-50 border border-blue-200 rounded-2xl">
                        <p class="text-sm text-blue-700">
                            Setelah order dibuat, kamu akan diarahkan ke halaman detail order untuk melakukan pembayaran.
                        </p>
                    </div>
                </div>

                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
                    <h3 class="font-bold text-slate-900">
                        Alur Setelah Order
                    </h3>

                    <div class="mt-5 space-y-5">
                        <div class="flex gap-3">
                            <div class="w-8 h-8 rounded-full bg-blue-600 text-white flex items-center justify-center text-xs font-bold shrink-0">
                                1
                            </div>

                            <div>
                                <p class="font-semibold text-slate-900 text-sm">
                                    Buat Order
                                </p>
                                <p class="text-xs text-slate-500">
                                    Customer mengisi kebutuhan proyek.
                                </p>
                            </div>
                        </div>

                        <div class="flex gap-3">
                            <div class="w-8 h-8 rounded-full bg-blue-600 text-white flex items-center justify-center text-xs font-bold shrink-0">
                                2
                            </div>

                            <div>
                                <p class="font-semibold text-slate-900 text-sm">
                                    Pembayaran Escrow
                                </p>
                                <p class="text-xs text-slate-500">
                                    Dana ditahan sementara oleh sistem.
                                </p>
                            </div>
                        </div>

                        <div class="flex gap-3">
                            <div class="w-8 h-8 rounded-full bg-blue-600 text-white flex items-center justify-center text-xs font-bold shrink-0">
                                3
                            </div>

                            <div>
                                <p class="font-semibold text-slate-900 text-sm">
                                    Freelancer Mengerjakan
                                </p>
                                <p class="text-xs text-slate-500">
                                    Progress pekerjaan dapat dipantau.
                                </p>
                            </div>
                        </div>

                        <div class="flex gap-3">
                            <div class="w-8 h-8 rounded-full bg-green-600 text-white flex items-center justify-center text-xs font-bold shrink-0">
                                4
                            </div>

                            <div>
                                <p class="font-semibold text-slate-900 text-sm">
                                    Approve & Selesai
                                </p>
                                <p class="text-xs text-slate-500">
                                    Dana dicairkan setelah pekerjaan disetujui.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </aside>
</section>
@endsection