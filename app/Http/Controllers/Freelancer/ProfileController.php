<?php

namespace App\Http\Controllers\Freelancer;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use App\Services\CloudinaryService;




class ProfileController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();

        abort_if(! $user || $user->role !== 'freelancer', 403);

        $verifikasi = $user->verifikasiFreelancer;
        $portofolios = $user->portofolios()->latest()->get();

        return view('freelancer.profile.index', compact(
            'user',
            'verifikasi',
            'portofolios'
        ));
    }

    public function update(Request $request): RedirectResponse
    {
        $user = $request->user();

        abort_if(! $user || $user->role !== 'freelancer', 403);

        $data = $request->validate([
            'nama' => ['required', 'string', 'max:100'],
            'no_hp' => ['nullable', 'string', 'max:20'],
            'alamat' => ['nullable', 'string', 'max:255'],
            'foto_profil' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        $tempFotoProfil = null;

        if ($request->hasFile('foto_profil')) {
            $emailFolder = str_replace(['@', '.', '+'], '_', strtolower((string) $user->email));

            $tempFotoProfil = CloudinaryService::uploadImage(
                $request->file('foto_profil'),
                'jasakampus/freelancer/' . $emailFolder . '/profile'
            );
        }
        $pin = (string) random_int(100000, 999999);

        session([
            'freelancer_profile_update' => [
                'data' => [
                    'nama' => $data['nama'],
                    'no_hp' => $data['no_hp'] ?? null,
                    'alamat' => $data['alamat'] ?? null,
                ],
                'temp_foto_profil' => $tempFotoProfil,
                'pin' => $pin,
                'expired_at' => now()->addMinutes(10)->toDateTimeString(),
            ],
        ]);

        Mail::raw(
            "Kode PIN verifikasi perubahan profil freelancer JasaKampus kamu adalah: {$pin}\n\nKode ini berlaku selama 10 menit.\n\nJika kamu tidak merasa mengubah profil, abaikan email ini.",
            function ($message) use ($user) {
                $message->to($user->email, $user->nama ?? null)
                    ->subject('PIN Verifikasi Perubahan Profil Freelancer - JasaKampus');
            }
        );

        return redirect()
            ->route('freelancer.profile.verify.form')
            ->with('success', 'PIN verifikasi telah dikirim ke email: ' . $user->email);
    }

    public function verifyForm(Request $request): View
    {
        $user = $request->user();

        abort_if(! $user || $user->role !== 'freelancer', 403);

        abort_if(! session()->has('freelancer_profile_update'), 404);

        return view('freelancer.profile.verify-pin');
    }

    public function verify(Request $request): RedirectResponse
    {
        $user = $request->user();

        abort_if(! $user || $user->role !== 'freelancer', 403);

        $request->validate([
            'pin' => ['required', 'digits:6'],
        ]);

        $pending = session('freelancer_profile_update');

        if (! $pending) {
            return redirect()
                ->route('freelancer.profile.index')
                ->withErrors(['pin' => 'Tidak ada perubahan profil yang perlu diverifikasi.']);
        }

        if (now()->greaterThan($pending['expired_at'])) {
            session()->forget('freelancer_profile_update');
            return redirect()
                ->route('freelancer.profile.index')
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

        session()->forget('freelancer_profile_update');

        return redirect()
            ->route('freelancer.profile.index')
            ->with('success', 'Profil freelancer berhasil diperbarui setelah verifikasi PIN.');
    }

   
}
