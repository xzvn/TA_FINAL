@extends('layouts.freelancer')

@section('title', 'Verifikasi PIN - JasaKampus')
@section('page-title', 'Verifikasi PIN')

@section('content')
@php
$expiredAt = session('freelancer_profile_update.expired_at');
$expiresAtMs = $expiredAt ? \Carbon\Carbon::parse($expiredAt)->timestamp * 1000 : now()->timestamp * 1000;
$serverNowMs = now()->timestamp * 1000;
@endphp

<div class="max-w-xl mx-auto bg-white rounded-xl border border-slate-200 shadow-sm p-6">
    <div class="mb-6 text-center">
        <div class="w-16 h-16 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center mx-auto text-3xl font-bold">
            ✉
        </div>

        <h1 class="text-2xl font-bold text-slate-900 mt-4">
            Verifikasi Perubahan Profil
        </h1>

        <p class="text-sm text-slate-500 mt-2">
            Masukkan PIN 6 digit yang sudah dikirim ke email akun freelancer kamu.
        </p>
    </div>

    @if (session('success'))
    <div class="mb-5 p-4 rounded-xl bg-green-50 text-green-700 text-sm font-semibold">
        {{ session('success') }}
    </div>
    @endif

    @if ($errors->any())
    <div class="mb-5 p-4 rounded-xl bg-red-50 text-red-700 text-sm">
        @foreach ($errors->all() as $error)
        <p>{{ $error }}</p>
        @endforeach
    </div>
    @endif

    <form id="pinForm" action="{{ route('freelancer.profile.verify') }}" method="POST" class="space-y-5">
        @csrf

        <div>
            <label class="block text-sm font-semibold text-slate-700 mb-3">
                PIN Verifikasi
            </label>

            <input type="hidden" name="pin" id="pinValue" value="{{ old('pin') }}">

            <div class="grid grid-cols-6 gap-3">
                @for ($i = 0; $i < 6; $i++)
                    <input
                    type="text"
                    maxlength="1"
                    inputmode="numeric"
                    pattern="[0-9]*"
                    value="{{ old('pin') ? substr(old('pin'), $i, 1) : '' }}"
                    class="pin-box h-14 rounded-xl border border-slate-300 text-center text-2xl font-bold text-slate-800 focus:border-blue-500 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    @endfor
            </div>
        </div>

        <div class="flex justify-between gap-3">
            <a href="{{ route('freelancer.profile.index') }}"
                class="px-5 py-3 rounded-xl border border-slate-300 text-slate-700 font-bold hover:bg-slate-50">
                Batal
            </a>

            <button type="submit" id="submitPinButton"
                class="px-5 py-3 rounded-xl bg-blue-600 text-white font-bold hover:bg-blue-700">
                Verifikasi & Simpan
            </button>
        </div>
    </form>

    <p class="text-xs text-slate-400 mt-5 text-center">
        PIN berlaku selama
        <span
            id="pinCountdown"
            class="font-bold text-blue-600"
            data-expires-at="{{ $expiresAtMs }}"
            data-server-now="{{ $serverNowMs }}">
            10:00
        </span>
    </p>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('pinForm');
        const pinValue = document.getElementById('pinValue');
        const boxes = Array.from(document.querySelectorAll('.pin-box'));
        const countdown = document.getElementById('pinCountdown');
        const submitButton = document.getElementById('submitPinButton');

        if (form && pinValue && boxes.length) {
            boxes.find(box => !box.value)?.focus();

            boxes.forEach((box, index) => {
                box.addEventListener('input', function() {
                    this.value = this.value.replace(/\D/g, '');

                    if (this.value && index < boxes.length - 1) {
                        boxes[index + 1].focus();
                    }

                    updateHiddenPin();
                });

                box.addEventListener('keydown', function(event) {
                    if (event.key === 'Backspace' && !this.value && index > 0) {
                        boxes[index - 1].focus();
                    }
                });

                box.addEventListener('paste', function(event) {
                    event.preventDefault();

                    const pasted = (event.clipboardData || window.clipboardData)
                        .getData('text')
                        .replace(/\D/g, '')
                        .slice(0, 6);

                    pasted.split('').forEach((char, i) => {
                        if (boxes[i]) boxes[i].value = char;
                    });

                    updateHiddenPin();
                    boxes[Math.min(pasted.length, boxes.length - 1)].focus();
                });
            });

            function updateHiddenPin() {
                pinValue.value = boxes.map(box => box.value).join('');
            }

            form.addEventListener('submit', function(event) {
                updateHiddenPin();

                if (pinValue.value.length !== 6) {
                    event.preventDefault();
                    alert('Masukkan PIN 6 digit terlebih dahulu.');
                    boxes.find(box => !box.value)?.focus();
                }
            });
        }

        if (countdown) {
            const expiresAt = Number(countdown.dataset.expiresAt);
            const serverNow = Number(countdown.dataset.serverNow);
            const clientStart = Date.now();

            function currentServerTime() {
                return serverNow + (Date.now() - clientStart);
            }

            function updateCountdown() {
                const distance = expiresAt - currentServerTime();

                if (distance <= 0) {
                    countdown.textContent = '00:00';
                    countdown.classList.remove('text-blue-600');
                    countdown.classList.add('text-red-600');

                    if (submitButton) {
                        submitButton.disabled = true;
                        submitButton.textContent = 'PIN Kedaluwarsa';
                        submitButton.classList.add('opacity-60', 'cursor-not-allowed');
                    }

                    clearInterval(timer);
                    return;
                }

                const minutes = Math.floor(distance / 1000 / 60);
                const seconds = Math.floor((distance / 1000) % 60);

                countdown.textContent =
                    String(minutes).padStart(2, '0') + ':' + String(seconds).padStart(2, '0');
            }

            updateCountdown();
            const timer = setInterval(updateCountdown, 1000);
        }
    });
</script>
@endpush