<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Services\CloudinaryService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Throwable;

class ProfileController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();

        abort_if(
            ! $user || $user->role !== 'customer',
            403
        );

        return view(
            'customer.profile.index',
            compact('user')
        );
    }

    public function update(
        Request $request
    ): RedirectResponse {
        $user = $request->user();

        abort_if(
            ! $user || $user->role !== 'customer',
            403
        );

        /*
         * Rapikan format email sebelum validasi.
         */
        $request->merge([
            'email' => strtolower(
                trim(
                    (string) $request->input('email')
                )
            ),
        ]);

        $data = $request->validate([
            'nama' => [
                'required',
                'string',
                'max:100',
            ],

            'email' => [
                'required',
                'email',
                'max:150',
                Rule::unique(
                    'users',
                    'email'
                )->ignore($user->id),
            ],

            'no_hp' => [
                'nullable',
                'string',
                'max:20',
            ],

            'alamat' => [
                'nullable',
                'string',
                'max:255',
            ],

            'foto_profil' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:2048',
            ],

            'theme' => [
                'required',
                'in:light,dark',
            ],
        ]);

        $tempFotoProfil = null;

        try {
            if ($request->hasFile('foto_profil')) {
                $emailFolder = str_replace(
                    ['@', '.', '+'],
                    '_',
                    strtolower($data['email'])
                );

                $tempFotoProfil =
                    CloudinaryService::uploadImage(
                        $request->file('foto_profil'),
                        'jasakampus/customer/' .
                            $emailFolder .
                            '/profile'
                    );
            }

            $pin = (string) random_int(
                100000,
                999999
            );

            session([
                'customer_profile_update' => [
                    'data' => [
                        'nama' =>
                        $data['nama'],

                        'email' =>
                        $data['email'],

                        'no_hp' =>
                        $data['no_hp']
                            ?? null,

                        'alamat' =>
                        $data['alamat']
                            ?? null,

                        'theme' =>
                        $data['theme'],
                    ],

                    'temp_foto_profil' =>
                    $tempFotoProfil,

                    /*
                     * Jangan menyimpan PIN
                     * dalam bentuk teks biasa.
                     */
                    'pin_hash' =>
                    Hash::make($pin),

                    'attempts' =>
                    0,

                    'expired_at' =>
                    now()
                        ->addMinutes(10)
                        ->toIso8601String(),
                ],
            ]);

            /*
             * PIN dikirim ke email akun saat ini
             * untuk mengesahkan perubahan profil,
             * termasuk ketika email akan diganti.
             */
            Mail::raw(
                implode("\n", [
                    'Kode PIN verifikasi perubahan profil JasaKampus kamu adalah:',
                    '',
                    $pin,
                    '',
                    'Kode ini berlaku selama 10 menit.',
                    '',
                    'Jika kamu tidak merasa mengubah profil, abaikan email ini.',
                ]),
                function ($message) use ($user): void {
                    $message
                        ->to(
                            $user->email,
                            $user->nama ?? null
                        )
                        ->subject(
                            'PIN Verifikasi Perubahan Profil - JasaKampus'
                        );
                }
            );
        } catch (Throwable $exception) {
            session()->forget(
                'customer_profile_update'
            );

            Log::error(
                'Gagal memproses perubahan profil customer.',
                [
                    'user_id' =>
                    $user->id,

                    'email' =>
                    $user->email,

                    'message' =>
                    $exception->getMessage(),
                ]
            );

            return back()
                ->withInput()
                ->withErrors([
                    'profile' =>
                    'Perubahan profil belum dapat diproses. Silakan coba kembali.',
                ]);
        }

        return redirect()
            ->route(
                'customer.profile.verify.form'
            )
            ->with(
                'success',
                'PIN verifikasi telah dikirim ke email akun saat ini: ' .
                    $user->email
            );
    }

    public function verifyForm(
        Request $request
    ): View {
        $user = $request->user();

        abort_if(
            ! $user || $user->role !== 'customer',
            403
        );

        abort_if(
            ! session()->has(
                'customer_profile_update'
            ),
            404
        );

        return view(
            'customer.profile.verify-pin'
        );
    }

    public function verify(
        Request $request
    ): RedirectResponse {
        $user = $request->user();

        abort_if(
            ! $user || $user->role !== 'customer',
            403
        );

        $request->validate([
            'pin' => [
                'required',
                'digits:6',
            ],
        ]);

        $pending = session(
            'customer_profile_update'
        );

        if (! is_array($pending)) {
            return redirect()
                ->route(
                    'customer.profile.index'
                )
                ->withErrors([
                    'pin' =>
                    'Tidak ada perubahan profil yang perlu diverifikasi.',
                ]);
        }

        try {
            $expiredAt = Carbon::parse(
                $pending['expired_at'] ?? null
            );
        } catch (Throwable) {
            session()->forget(
                'customer_profile_update'
            );

            return redirect()
                ->route(
                    'customer.profile.index'
                )
                ->withErrors([
                    'pin' =>
                    'Data verifikasi tidak valid. Silakan ubah profil kembali.',
                ]);
        }

        if (now()->greaterThan($expiredAt)) {
            session()->forget(
                'customer_profile_update'
            );

            return redirect()
                ->route(
                    'customer.profile.index'
                )
                ->withErrors([
                    'pin' =>
                    'PIN sudah kedaluwarsa. Silakan ubah profil kembali.',
                ]);
        }

        $attempts = (int) (
            $pending['attempts'] ?? 0
        );

        if ($attempts >= 5) {
            session()->forget(
                'customer_profile_update'
            );

            return redirect()
                ->route(
                    'customer.profile.index'
                )
                ->withErrors([
                    'pin' =>
                    'Batas percobaan PIN telah tercapai. Silakan ubah profil kembali.',
                ]);
        }

        $pinHash = (string) (
            $pending['pin_hash'] ?? ''
        );

        $pinIsValid =
            $pinHash !== '' &&
            Hash::check(
                (string) $request->input('pin'),
                $pinHash
            );

        if (! $pinIsValid) {
            $newAttempts =
                $attempts + 1;

            if ($newAttempts >= 5) {
                session()->forget(
                    'customer_profile_update'
                );

                return redirect()
                    ->route(
                        'customer.profile.index'
                    )
                    ->withErrors([
                        'pin' =>
                        'Batas percobaan PIN telah tercapai. Silakan ubah profil kembali.',
                    ]);
            }

            $pending['attempts'] =
                $newAttempts;

            session([
                'customer_profile_update' =>
                $pending,
            ]);

            $remainingAttempts =
                5 - $newAttempts;

            return back()
                ->withErrors([
                    'pin' =>
                    'PIN yang kamu masukkan salah. Sisa percobaan: ' .
                        $remainingAttempts .
                        '.',
                ])
                ->withInput();
        }

        $profileData =
            $pending['data'] ?? null;

        if (
            ! is_array($profileData) ||
            empty($profileData['nama']) ||
            empty($profileData['email'])
        ) {
            session()->forget(
                'customer_profile_update'
            );

            return redirect()
                ->route(
                    'customer.profile.index'
                )
                ->withErrors([
                    'profile' =>
                    'Data perubahan profil tidak valid. Silakan isi profil kembali.',
                ]);
        }

        try {
            DB::transaction(
                function () use (
                    $user,
                    $profileData,
                    $pending
                ): void {
                    $user->nama =
                        $profileData['nama'];

                    $user->email =
                        $profileData['email'];

                    $user->no_hp =
                        $profileData['no_hp']
                        ?? null;

                    $user->alamat =
                        $profileData['alamat']
                        ?? null;

                    $user->theme =
                        $profileData['theme']
                        ?? 'light';

                    if (
                        ! empty($pending['temp_foto_profil'])
                    ) {
                        $user->foto_profil =
                            $pending['temp_foto_profil'];
                    }

                    $user->save();
                },
                3
            );
        } catch (Throwable $exception) {
            session()->forget(
                'customer_profile_update'
            );

            Log::error(
                'Gagal menyimpan profil customer setelah verifikasi PIN.',
                [
                    'user_id' =>
                    $user->id,

                    'message' =>
                    $exception->getMessage(),
                ]
            );

            return redirect()
                ->route(
                    'customer.profile.index'
                )
                ->withErrors([
                    'profile' =>
                    'Profil belum berhasil disimpan. Silakan isi perubahan kembali.',
                ]);
        }

        session()->forget(
            'customer_profile_update'
        );

        return redirect()
            ->route(
                'customer.profile.index'
            )
            ->with(
                'success',
                'Profil berhasil diperbarui setelah verifikasi PIN.'
            );
    }
}
