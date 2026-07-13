<?php

namespace App\Http\Controllers\Freelancer;

use App\Http\Controllers\Controller;
use App\Models\HasilPekerjaan;
use App\Models\Pesanan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Models\Revisi;
use App\Models\Notifikasi;
use App\Services\NotifikasiService;


class HasilPekerjaanController extends Controller
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
            ! in_array($pesanan->status_pesanan, ['diproses', 'revisi']),
            403,
            'Hasil akhir hanya bisa diunggah saat pesanan sedang diproses atau revisi.'
        );

        $pesanan->load(['customer', 'jasa', 'hasilPekerjaan']);

        return view('freelancer.hasil.create', compact('pesanan'));
    }

    public function store(Request $request, Pesanan $pesanan): RedirectResponse
    {
        $this->authorizeFreelancer($request, $pesanan);

        abort_if(
            ! in_array($pesanan->status_pesanan, ['diproses', 'revisi']),
            403,
            'Hasil akhir hanya bisa diunggah saat pesanan sedang diproses atau revisi.'
        );

        $request->validate([
            'catatan' => ['nullable', 'string'],
            'file_hasil' => ['required', 'file', 'mimes:jpg,jpeg,png,pdf,doc,docx,zip,rar', 'max:10240'],
        ]);

        $emailFolder = str_replace(['@', '.'], '_', strtolower($request->user()->email));

        $fileHasilPath = CloudinaryService::uploadFile(
            $request->file('file_hasil'),
            'jasakampus/freelancer/' . $emailFolder . '/hasil'
        );

        HasilPekerjaan::updateOrCreate(
            [
                'id_pesanan' => $pesanan->id,
            ],
            [
                'catatan' => $request->catatan,
                'file_hasil' => $fileHasilPath,
                'status_hasil' => 'menunggu_review',
                'tanggal_upload' => now(),
            ]
        );

        Notifikasi::create([
            'id_user' => $pesanan->id_customer,
            'judul' => 'Hasil Akhir Telah Dikirim',
            'pesan' => 'Freelancer telah mengirim hasil akhir untuk pesanan #' . $pesanan->id . '. Silakan periksa dan berikan persetujuan atau revisi.',
            'tipe' => 'hasil',
            'url' => route('customer.order.show', $pesanan->id),
        ]);


        $revisiTerbuka = Revisi::where('id_pesanan', $pesanan->id)
            ->whereIn('status_revisi', ['diajukan', 'diproses'])
            ->where(function ($query) {
                $query->where('jenis_revisi', 'hasil_akhir')
                    ->orWhere(function ($q) {
                        $q->where('jenis_revisi', 'progress')
                            ->where('persentase_progress', '>=', 100);
                    });
            })
            ->latest()
            ->first();

        if ($revisiTerbuka) {
            $revisiTerbuka->update([
                'status_revisi' => 'selesai',
                'tanggal_selesai' => now(),
            ]);
        }

        $revisiHasilAkhir = Revisi::where('id_pesanan', $pesanan->id)
            ->where('jenis_revisi', 'hasil_akhir')
            ->whereIn('status_revisi', ['diajukan', 'diproses'])
            ->latest()
            ->first();

        if ($revisiHasilAkhir) {
            $revisiHasilAkhir->update([
                'status_revisi' => 'selesai',
                'tanggal_selesai' => now(),
            ]);
        }

        $pesanan->update([
            'status_pesanan' => 'menunggu_approve',
        ]);

        NotifikasiService::kirim(
            $pesanan->id_customer,
            'Hasil Akhir Telah Dikirim',
            'Freelancer telah mengirim hasil akhir untuk pesanan #' . $pesanan->id . '. Silakan periksa hasil pekerjaan.',
            'hasil',
            route('customer.order.show', $pesanan->id, false)
        );
        if ($pesanan->status_pesanan === 'revisi') {
            Revisi::where('id_pesanan', $pesanan->id)
                ->where('status_revisi', 'diajukan')
                ->latest()
                ->first()
                ?->update([
                    'status_revisi' => 'selesai',
                    'tanggal_selesai' => now(),
                ]);
        }

        return redirect()
            ->route('freelancer.progress.create', $pesanan->id)
            ->with('success', 'Hasil akhir berhasil diunggah. Menunggu persetujuan customer.');
    }
}
