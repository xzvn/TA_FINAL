<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Dispute;
use App\Models\Pesanan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Services\NotifikasiService;
use App\Services\CloudinaryService;


class DisputeController extends Controller
{
    private function authorizeCustomer(Request $request, Pesanan $pesanan): void
    {
        $user = $request->user();

        abort_if(! $user || $user->role !== 'customer', 403);
        abort_if($pesanan->id_customer !== $user->id, 403);
    }

    public function store(Request $request, Pesanan $pesanan): RedirectResponse
    {
        $this->authorizeCustomer($request, $pesanan);

        $pesanan->load('dispute');

        abort_if($pesanan->status_pesanan === 'selesai', 403, 'Pesanan yang sudah selesai tidak dapat diajukan dispute.');
        abort_if($pesanan->status_pesanan === 'dibatalkan', 403, 'Pesanan yang sudah dibatalkan tidak dapat diajukan dispute.');
        abort_if($pesanan->status_pesanan === 'dispute', 403, 'Pesanan ini sudah dalam proses dispute.');
        abort_if($pesanan->jumlah_revisi < $pesanan->batas_revisi, 403, 'Dispute hanya dapat diajukan setelah batas revisi tercapai.');
        abort_if($pesanan->dispute, 403, 'Dispute untuk pesanan ini sudah pernah diajukan.');

        $request->validate([
            'alasan_dispute' => ['required', 'string', 'min:10'],
            'bukti_dispute' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp,pdf,doc,docx', 'max:5120'],
        ]);

        $buktiPath = null;

        if ($request->hasFile('bukti_dispute')) {
            $buktiPath = CloudinaryService::uploadFile(
                $request->file('bukti_dispute'),
                'jasakampus/dispute'
            );
        }

        Dispute::create([
            'id_pesanan' => $pesanan->id,
            'id_customer' => $pesanan->id_customer,
            'id_freelancer' => $pesanan->id_freelancer,
            'alasan_dispute' => $request->alasan_dispute,
            'bukti_dispute' => $buktiPath,
            'status_dispute' => 'pending',
            'tanggal_pengajuan' => now(),
        ]);

        $pesanan->update([
            'status_pesanan' => 'dispute',
        ]);

        NotifikasiService::kirimKeAdmin(
            'Dispute Baru Masuk',
            'Customer mengajukan dispute untuk pesanan #' . $pesanan->id . '.',
            'dispute',
            route('admin.disputes.index', [], false)
        );

        NotifikasiService::kirim(
            $pesanan->id_freelancer,
            'Pesanan Masuk Dispute',
            'Customer mengajukan dispute untuk pesanan #' . $pesanan->id . '. Admin akan meninjau pesanan ini.',
            'dispute',
            route('freelancer.pesanan.show', $pesanan->id, false)
        );

        return redirect()
            ->route('customer.order.show', $pesanan->id)
            ->with('success', 'Dispute berhasil diajukan. Admin akan meninjau pesanan ini.');
    }
}
