@extends('layouts.freelancer')

@section('title', 'Upload Hasil Akhir - JasaKampus')
@section('page-title', 'Upload Hasil Akhir')

@section('content')
    <div class="max-w-5xl mx-auto">
        <a href="{{ route('freelancer.pesanan.show', $pesanan->id) }}"
           class="inline-block mb-6 text-blue-600 font-semibold hover:underline">
            ← Kembali ke Detail Pesanan
        </a>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2 bg-white rounded-xl border border-slate-200 shadow-sm p-6">
                <h1 class="text-2xl font-bold text-slate-900">
                    Upload Hasil Akhir
                </h1>

                <p class="text-sm text-slate-500 mt-1">
                    Unggah file hasil akhir pekerjaan untuk direview oleh customer.
                </p>

                <form method="POST"
                      action="{{ route('freelancer.hasil.store', $pesanan->id) }}"
                      enctype="multipart/form-data"
                      class="mt-6 space-y-5">
                    @csrf

                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">
                            Catatan untuk Customer
                        </label>

                        <textarea name="catatan"
                                  rows="5"
                                  class="w-full rounded-lg border-slate-300 focus:border-blue-500 focus:ring-blue-500"
                                  placeholder="Contoh: Berikut hasil akhir pekerjaan. File sudah termasuk versi final dan source file.">{{ old('catatan') }}</textarea>

                        @error('catatan')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">
                            File Hasil Akhir
                        </label>

                        <input type="file"
                               name="file_hasil"
                               class="w-full rounded-lg border border-slate-300 p-3 text-sm"
                               required>

                        <p class="text-xs text-slate-500 mt-1">
                            Format: jpg, png, pdf, doc, docx, zip, rar. Maksimal 10MB.
                        </p>

                        @error('file_hasil')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <button type="submit"
                            class="w-full px-5 py-3 bg-blue-600 text-white rounded-lg font-semibold hover:bg-blue-700">
                        Kirim Hasil Akhir
                    </button>
                </form>
            </div>

            <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6 h-fit">
                <h2 class="text-lg font-bold text-slate-900">
                    Ringkasan Pesanan
                </h2>

                <div class="mt-5 space-y-4">
                    <div>
                        <p class="text-xs text-slate-500">Order</p>
                        <p class="font-semibold text-slate-900">
                            #{{ $pesanan->id }}
                        </p>
                    </div>

                    <div>
                        <p class="text-xs text-slate-500">Customer</p>
                        <p class="font-semibold text-slate-900">
                            {{ $pesanan->customer->nama ?? '-' }}
                        </p>
                    </div>

                    <div>
                        <p class="text-xs text-slate-500">Jasa</p>
                        <p class="font-semibold text-slate-900">
                            {{ $pesanan->jasa->nama_jasa ?? '-' }}
                        </p>
                    </div>

                    <div>
                        <p class="text-xs text-slate-500">Status</p>
                        <p class="font-semibold text-blue-600">
                            {{ str_replace('_', ' ', strtoupper($pesanan->status_pesanan)) }}
                        </p>
                    </div>

                    <div class="p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                        <p class="text-sm text-yellow-700">
                            Setelah hasil akhir dikirim, customer dapat menyetujui pekerjaan atau meminta revisi.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection