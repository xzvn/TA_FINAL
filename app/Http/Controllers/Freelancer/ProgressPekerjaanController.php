<?php

namespace App\Http\Controllers\Freelancer;

use App\Http\Controllers\Controller;
use App\Models\Pesanan;
use App\Models\ProgressPekerjaan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Models\Revisi;
use App\Models\Notifikasi;
use App\Services\NotifikasiService;


class ProgressPekerjaanController extends Controller
{
    private function authorizeFreelancer(Request $request, Pesanan $pesanan): void
    {
        $user = $request->user();

        abort_if(! $user || $user->role !== 'freelancer', 403);
        abort_if($pesanan->id_freelancer !== $user->id, 403);
    }

    public function create(Request $request, Pesanan $pesanan): View
    {
        $this->authorizeFreelancer($request, $pesanan);

        abort_if(
            ! in_array($pesanan->status_pesanan, [
                'dibayar',
                'diproses',
                'revisi',
                'menunggu_approve',
                'selesai',
            ]),
            403,
            'Halaman proyek hanya bisa dibuka untuk pesanan yang sudah dibayar.'
        );

        $pesanan->load(['customer', 'jasa', 'progressPekerjaans', 'revisis', 'hasilPekerjaan']);

        return view('freelancer.progress.create', compact('pesanan'));
    }

    public function store(Request $request, Pesanan $pesanan): RedirectResponse
    {
        $this->authorizeFreelancer($request, $pesanan);

        abort_if(
            ! in_array($pesanan->status_pesanan, ['dibayar', 'diproses', 'revisi']),
            403,
            'Progress hanya bisa diunggah untuk pesanan yang sudah dibayar.'
        );

        $request->validate([
            'judul_progress' => ['required', 'string', 'max:255'],
            'deskripsi_progress' => ['nullable', 'string'],
            'persentase_progress' => ['required', 'integer', 'min:1', 'max:100'],
            'file_progress' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf,doc,docx,zip', 'max:5120'],
        ]);

        $fileProgressPath = null;

        if ($request->hasFile('file_progress')) {
            $emailFolder = str_replace(['@', '.'], '_', strtolower($request->user()->email));

            $fileProgressPath = CloudinaryService::uploadFile(
                $request->file('file_progress'),
                'jasakampus/freelancer/' . $emailFolder . '/progress'
            );
        }

        $progress = ProgressPekerjaan::create([
            'id_pesanan' => $pesanan->id,
            'judul_progress' => $request->judul_progress,
            'deskripsi_progress' => $request->deskripsi_progress,
            'persentase_progress' => $request->persentase_progress,
            'file_progress' => $fileProgressPath,
            'tanggal_upload' => now(),
        ]);


        NotifikasiService::kirim(
            $pesanan->id_customer,
            'Progress Baru Diupload',
            'Freelancer telah mengupload progress terbaru untuk pesanan #' . $pesanan->id . '.',
            'progress',
            route('customer.order.show', $pesanan->id, false)
        );

        $revisiProgress = Revisi::where('id_pesanan', $pesanan->id)
            ->whereIn('status_revisi', ['diajukan', 'diproses'])
            ->where(function ($query) {
                $query->where('jenis_revisi', 'progress')
                    ->orWhereNull('jenis_revisi');
            })
            ->latest()
            ->first();

        if ($revisiProgress) {
            $revisiProgress->update([
                'status_revisi' => 'selesai',
                'tanggal_selesai' => now(),
            ]);
        }

        $pesanan->update([
            'status_pesanan' => 'diproses',
        ]);

        return redirect()
            ->route('freelancer.progress.create', $pesanan->id)
            ->with('success', 'Progress pekerjaan berhasil diunggah.');
    }
}
