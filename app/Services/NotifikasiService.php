<?php

namespace App\Services;

use App\Models\Notifikasi;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Throwable;

class NotifikasiService
{
    public static function kirim(
        int|array $idUser,
        string $judul,
        string $pesan,
        string $tipe = 'system',
        ?string $url = null,
        bool $kirimEmail = true
    ): void {
        foreach ((array) $idUser as $userId) {
            if (! $userId) {
                continue;
            }

            Notifikasi::create([
                'id_user' => $userId,
                'judul' => $judul,
                'pesan' => $pesan,
                'tipe' => $tipe,
                'url' => $url,
                'dibaca' => false,
            ]);

            if ($kirimEmail) {
                self::kirimEmail($userId, $judul, $pesan, $url);
            }
        }
    }

    public static function kirimKeAdmin(
        string $judul,
        string $pesan,
        string $tipe = 'system',
        ?string $url = null,
        bool $kirimEmail = true
    ): void {
        $adminIds = User::where('role', 'admin')
            ->pluck('id')
            ->toArray();

        self::kirim($adminIds, $judul, $pesan, $tipe, $url, $kirimEmail);
    }

    private static function kirimEmail(
        int $idUser,
        string $judul,
        string $pesan,
        ?string $url = null
    ): void {
        $user = User::find($idUser);

        if (! $user || ! $user->email) {
            return;
        }

        try {
            $isiEmail = $pesan;

            if ($url) {
                $link = str_starts_with($url, 'http')
                    ? $url
                    : url($url);

                $isiEmail .= "\n\nBuka detail:\n" . $link;
            }

            Mail::raw($isiEmail, function ($message) use ($user, $judul) {
                $message->to($user->email, $user->nama ?? $user->name ?? null)
                    ->subject($judul);
            });
        } catch (Throwable $e) {
            Log::warning('Gagal mengirim email notifikasi: ' . $e->getMessage());
        }
    }
}
