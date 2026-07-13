<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use App\Services\CloudinaryService;

class ProfileController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();

        abort_if(! $user || $user->role !== 'customer', 403);

        return view('customer.profile.index', compact('user'));
    }

    public function update(Request $request): RedirectResponse
    {
        $user = $request->user();

        abort_if(! $user || $user->role !== 'customer', 403);

        $data = $request->validate([
            'nama' => ['required', 'string', 'max:100'],
            'email' => [
                'required',
                'email',
                'max:150',
                Rule::unique('users', 'email')->ignore($user->id),
            ],
            'no_hp' => ['nullable', 'string', 'max:20'],
            'alamat' => ['nullable', 'string', 'max:255'],
            'foto_profil' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        $tempFotoProfil = null;

        if ($request->hasFile('foto_profil')) {
            $emailFolder = str_replace(['@', '.'], '_', strtolower($data['email']));

            $tempFotoProfil = CloudinaryService::uploadImage(
                $request->file('foto_profil'),
                'jasakampus/customer/' . $emailFolder . '/profile'
            );
        }

        $pin = (string) random_int(100000, 999999);



        session([
            'customer_profile_update' => [
                'data' => [
                    'nama' => $data['nama'],
                    'email' => $data['email'],
                    'no_hp' => $data['no_hp'] ?? null,
                    'alamat' => $data['alamat'] ?? null,
                ],
                'temp_foto_profil' => $tempFotoProfil,
                'pin' => $pin,
                'expired_at' => now()->addMinutes(10)->toDateTimeString(),
            ],
        ]);

        Mail::raw(
            "Kode PIN verifikasi perubahan profil JasaKampus kamu adalah: {$pin}\n\nKode ini berlaku selama 10 menit.\n\nJika kamu tidak merasa mengubah profil, abaikan email ini.",
            function ($message) use ($user) {
                $message->to($user->email, $user->nama ?? null)
                    ->subject('PIN Verifikasi Perubahan Profil - JasaKampus');
            }
        );

        return redirect()

            ->route('customer.profile.verify.form')
            ->with('success', 'PIN verifikasi telah dikirim ke email kamu.' . $user->email);
    }

    public function verifyForm(Request $request): View
    {
        $user = $request->user();

        abort_if(! $user || $user->role !== 'customer', 403);

        abort_if(! session()->has('customer_profile_update'), 404);

        return view('customer.profile.verify-pin');
    }

    public function verify(Request $request): RedirectResponse
    {
        $user = $request->user();

        abort_if(! $user || $user->role !== 'customer', 403);

        $request->validate([
            'pin' => ['required', 'digits:6'],
        ]);

        $pending = session('customer_profile_update');

        if (! $pending) {
            return redirect()
                ->route('customer.profile.index')
                ->withErrors(['pin' => 'Tidak ada perubahan profil yang perlu diverifikasi.']);
        }

        if (now()->greaterThan($pending['expired_at'])) {
            session()->forget('customer_profile_update');

            return redirect()
                ->route('customer.profile.index')
                ->withErrors(['pin' => 'PIN sudah kedaluwarsa. Silakan ubah profil kembali.']);
        }

        if ($request->pin !== $pending['pin']) {
            return back()
                ->withErrors(['pin' => 'PIN yang kamu masukkan salah.'])
                ->withInput();
        }
        $data = $pending['data'];

        if (! empty($pending['temp_foto_profil'])) {
            $data['foto_profil'] = $pending['temp_foto_profil'];
        }

        $user->update($data);

        session()->forget('customer_profile_update');

        return redirect()
            ->route('customer.profile.index')
            ->with('success', 'Profil berhasil diperbarui setelah verifikasi PIN.');
    }
}
