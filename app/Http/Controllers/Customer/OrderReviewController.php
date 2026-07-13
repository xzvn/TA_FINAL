<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Pesanan;
use App\Models\ProgressPekerjaan;
use App\Models\Revisi;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Services\NotifikasiService;



class OrderReviewController extends Controller
{
    private function authorizeCustomer(Request $request, Pesanan $pesanan): void
    {
        $user = $request->user();

        abort_if(! $user || $user->role !== 'customer', 403);
        abort_if($pesanan->id_customer !== $user->id, 403);
    }

    public function approve(Request $request, Pesanan $pesanan): RedirectResponse
    {
        $this->authorizeCustomer($request, $pesanan);

        $pesanan->load(['hasilPekerjaan', 'pembayaran']);

        abort_if(
            $pesanan->status_pesanan !== 'menunggu_approve',
            403,
            'Pesanan belum dapat disetujui.'
        );

        abort_if(
            ! $pesanan->hasilPekerjaan,
            403,
            'Hasil akhir belum tersedia.'
        );

        $pesanan->hasilPekerjaan->update([
            'status_hasil' => 'disetujui',
        ]);

        $progressTertinggi = $pesanan->progressPekerjaans()
            ->max('persentase_progress') ?? 0;

        if ($progressTertinggi < 100) {
            ProgressPekerjaan::create([
                'id_pesanan' => $pesanan->id,
                'judul_progress' => 'Pekerjaan selesai',
                'deskripsi_progress' => 'Customer telah menyetujui hasil akhir. Progress proyek otomatis menjadi 100%.',
                'persentase_progress' => 100,
                'file_progress' => $pesanan->hasilPekerjaan->file_hasil ?? null,
                'tanggal_upload' => now(),
            ]);
        }

        if ($pesanan->pembayaran) {
            $pesanan->pembayaran->update([
                'status_escrow' => 'dicairkan',
                'tanggal_release' => now(),
            ]);
        }



        $pesanan->update([
            'status_pesanan' => 'selesai',
        ]);


        NotifikasiService::kirim(
            $pesanan->id_freelancer,
            'Pesanan Disetujui',
            'Customer telah menyetujui hasil pekerjaan untuk pesanan #' . $pesanan->id . '. Dana escrow telah dicairkan.',
            'pembayaran',
            route('freelancer.pesanan.show', $pesanan->id, false)
        );

        return redirect()
            ->route('customer.order.show', $pesanan->id)
            ->with('success', 'Pekerjaan berhasil disetujui. Progress proyek menjadi 100%, pesanan selesai, dan dana escrow dicairkan.');
    }

    public function revision(Request $request, Pesanan $pesanan): RedirectResponse
    {
        $this->authorizeCustomer($request, $pesanan);

        $pesanan->load(['hasilPekerjaan', 'progressPekerjaans']);

        abort_if(
            $pesanan->jumlah_revisi >= $pesanan->batas_revisi,
            403,
            'Batas revisi sudah habis.'
        );

        $revisiTerbuka = Revisi::where('id_pesanan', $pesanan->id)
            ->whereIn('status_revisi', ['diajukan', 'diproses'])
            ->exists();

        abort_if(
            $revisiTerbuka,
            403,
            'Masih ada revisi yang belum diselesaikan.'
        );

        $request->validate([
            'catatan_revisi' => ['required', 'string', 'min:10'],
            'id_progress' => ['nullable', 'exists:progress_pekerjaans,id'],
        ]);

        /*
        |--------------------------------------------------------------------------
        | Revisi Hasil Akhir
        |--------------------------------------------------------------------------
        */
        if ($pesanan->status_pesanan === 'menunggu_approve') {
            abort_if(
                ! $pesanan->hasilPekerjaan,
                403,
                'Hasil akhir belum tersedia.'
            );

            Revisi::create([
                'id_pesanan' => $pesanan->id,
                'id_progress' => null,
                'jenis_revisi' => 'hasil_akhir',
                'persentase_progress' => null,
                'catatan_revisi' => $request->catatan_revisi,
                'status_revisi' => 'diajukan',
                'tanggal_revisi' => now(),
            ]);

            $pesanan->hasilPekerjaan->update([
                'status_hasil' => 'revisi',
            ]);
            $pesanan->update([
                'status_pesanan' => 'revisi',
                'jumlah_revisi' => $pesanan->jumlah_revisi + 1,
            ]);

            NotifikasiService::kirim(
                $pesanan->id_freelancer,
                'Permintaan Revisi Hasil Akhir',
                'Customer meminta revisi hasil akhir untuk pesanan #' . $pesanan->id . '.',
                'revisi',
                route('freelancer.pesanan.show', $pesanan->id, false)
            );

            return redirect()
                ->route('customer.order.show', $pesanan->id)
                ->with('success', 'Permintaan revisi hasil akhir berhasil dikirim.');
        }

        /*
        |--------------------------------------------------------------------------
        | Revisi Progress
        |--------------------------------------------------------------------------
        */
        abort_if(
            $pesanan->status_pesanan !== 'diproses',
            403,
            'Revisi progress hanya bisa diajukan saat pekerjaan sedang diproses.'
        );

        if ($request->filled('id_progress')) {
            $progress = ProgressPekerjaan::where('id_pesanan', $pesanan->id)
                ->where('id', $request->id_progress)
                ->first();
        } else {
            $progress = $pesanan->progressPekerjaans()
                ->latest()
                ->first();
        }

        abort_if(
            ! $progress,
            403,
            'Progress belum tersedia.'
        );

        Revisi::create([
            'id_pesanan' => $pesanan->id,
            'id_progress' => $progress->id,
            'jenis_revisi' => 'progress',
            'persentase_progress' => $progress->persentase_progress,
            'catatan_revisi' => $request->catatan_revisi,
            'status_revisi' => 'diajukan',
            'tanggal_revisi' => now(),
        ]);

        $pesanan->update([
            'status_pesanan' => 'revisi',
            'jumlah_revisi' => $pesanan->jumlah_revisi + 1,
        ]);

        NotifikasiService::kirim(
            $pesanan->id_freelancer,
            'Permintaan Revisi Progress',
            'Customer meminta revisi progress untuk pesanan #' . $pesanan->id . '.',
            'revisi',
            route('freelancer.pesanan.show', $pesanan->id, false)
        );

        return redirect()
            ->route('customer.order.show', $pesanan->id)
            ->with('success', 'Permintaan revisi progress berhasil dikirim ke freelancer.');
    }
}
