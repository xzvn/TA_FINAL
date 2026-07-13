@extends('layouts.admin')

@section('title', 'Settings Admin - JasaKampus')

@section('content')
<div class="space-y-6">
    <div>
        <h1 class="text-3xl font-bold text-slate-900">
            Settings
        </h1>
        <p class="text-sm text-slate-500 mt-1">
            Informasi konfigurasi dasar sistem JasaKampus.
        </p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
            <p class="text-xs text-slate-400 uppercase font-semibold">
                Nama Aplikasi
            </p>
            <h3 class="text-xl font-bold text-slate-900 mt-2">
                {{ config('app.name') }}
            </h3>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
            <p class="text-xs text-slate-400 uppercase font-semibold">
                Environment
            </p>
            <h3 class="text-xl font-bold text-slate-900 mt-2">
                {{ config('app.env') }}
            </h3>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
            <p class="text-xs text-slate-400 uppercase font-semibold">
                Mail Driver
            </p>
            <h3 class="text-xl font-bold text-slate-900 mt-2">
                {{ config('mail.default') }}
            </h3>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
            <p class="text-xs text-slate-400 uppercase font-semibold">
                Database
            </p>
            <h3 class="text-xl font-bold text-slate-900 mt-2">
                {{ config('database.default') }}
            </h3>
        </div>
    </div>
    <x-theme-setting />

    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
        <h2 class="text-lg font-bold text-slate-900">
            Catatan
        </h2>

        <p class="text-sm text-slate-500 mt-2 leading-relaxed">
            Halaman ini digunakan untuk menampilkan informasi konfigurasi dasar sistem.
            Untuk demo, admin dapat melihat status environment, mail, dan database yang digunakan.
        </p>
    </div>
</div>
@endsection