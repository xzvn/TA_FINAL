<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Chat;
use App\Models\Jasa;
use App\Models\Pesanan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Services\CloudinaryService;

class OrderController extends Controller
{
    private function authorizeCustomer(Request $request): void
    {
        $user = $request->user();

        abort_if(! $user || $user->role !== 'customer', 403);
    }

    private function ensureSudahChat(Request $request, Jasa $jasa): void
    {
        $sudahChat = Chat::where('id_jasa', $jasa->id)
            ->where('id_customer', $request->user()->id)
            ->where('id_freelancer', $jasa->id_freelancer)
            ->exists();

        abort_if(! $sudahChat, 403, 'Anda harus chat freelancer terlebih dahulu sebelum melakukan order.');
    }

    public function create(Request $request, Jasa $jasa): View
    {
        $this->authorizeCustomer($request);
        $this->ensureSudahChat($request, $jasa);

        abort_if($jasa->status_jasa !== 'active', 404);

        $jasa->load('freelancer');

        return view('customer.order.create', compact('jasa'));
    }

    public function store(Request $request, Jasa $jasa): RedirectResponse
    {
        $this->authorizeCustomer($request);
        $this->ensureSudahChat($request, $jasa);

        abort_if($jasa->status_jasa !== 'active', 404);

        $request->validate([
            'deskripsi_kebutuhan' => ['required', 'string', 'min:10'],
            'file_requirement' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf,doc,docx,zip', 'max:5120'],
        ]);

        $fileRequirementPath = null;

        if ($request->hasFile('file_requirement')) {
            $emailFolder = str_replace(['@', '.'], '_', strtolower($request->user()->email));

            $fileRequirementPath = CloudinaryService::uploadFile(
                $request->file('file_requirement'),
                'jasakampus/customer/' . $emailFolder . '/order'
            );
        }

        $pesanan = Pesanan::create([
            'id_customer' => $request->user()->id,
            'id_freelancer' => $jasa->id_freelancer,
            'id_jasa' => $jasa->id,
            'deskripsi_kebutuhan' => $request->deskripsi_kebutuhan,
            'file_requirement' => $fileRequirementPath,
            'total_harga' => $jasa->harga,
            'status_pesanan' => 'menunggu_pembayaran',
            'jumlah_revisi' => 0,
            'batas_revisi' => 3,
            'tanggal_pesan' => now(),
        ]);

        return redirect()
            ->route('customer.payment.show', $pesanan->id)
            ->with('success', 'Order berhasil dibuat. Silakan lanjut ke pembayaran.');
    }

    public function show(Request $request, Pesanan $pesanan): View
    {
        $this->authorizeCustomer($request);

        abort_if($pesanan->id_customer !== $request->user()->id, 403);

        $pesanan->load([
            'jasa.freelancer',
            'freelancer',
            'pembayaran',
            'progressPekerjaans',
            'hasilPekerjaan',
            'revisis',
            'review',
            'dispute',
        ]);

        return view('customer.order.show', compact('pesanan'));
    }

    public function index(Request $request): View
    {
        $this->authorizeCustomer($request);

        $request->validate([
            'status_pesanan' => ['nullable', 'in:menunggu_pembayaran,dibayar,diproses,menunggu_approve,revisi,selesai,dibatalkan,dispute'],
            'tanggal_mulai' => ['nullable', 'date'],
            'tanggal_selesai' => ['nullable', 'date', 'after_or_equal:tanggal_mulai'],
        ]);

        $statusPesanan = $request->status_pesanan;

        $tanggalMulai = $request->filled('tanggal_mulai')
            ? Carbon::parse($request->tanggal_mulai)->startOfDay()
            : null;

        $tanggalSelesai = $request->filled('tanggal_selesai')
            ? Carbon::parse($request->tanggal_selesai)->endOfDay()
            : null;

        $query = Pesanan::with(['jasa.freelancer', 'pembayaran', 'progressPekerjaans'])
            ->where('id_customer', $request->user()->id);

        if ($statusPesanan) {
            $query->where('status_pesanan', $statusPesanan);
        }

        if ($tanggalMulai && $tanggalSelesai) {
            $query->whereBetween('tanggal_pesan', [$tanggalMulai, $tanggalSelesai]);
        } elseif ($tanggalMulai) {
            $query->where('tanggal_pesan', '>=', $tanggalMulai);
        } elseif ($tanggalSelesai) {
            $query->where('tanggal_pesan', '<=', $tanggalSelesai);
        }

        $pesanans = $query
            ->latest('tanggal_pesan')
            ->get();

        return view('customer.order.index', compact(
            'pesanans',
            'statusPesanan',
            'tanggalMulai',
            'tanggalSelesai'
        ));
    }
}
