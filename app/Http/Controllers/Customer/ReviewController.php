<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Pesanan;
use App\Models\Review;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Services\CloudinaryService;


class ReviewController extends Controller
{
    private function authorizeCustomer(Request $request, Pesanan $pesanan): void
    {
        $user = $request->user();

        abort_if(! $user || $user->role !== 'customer', 403);
        abort_if($pesanan->id_customer !== $user->id, 403);
    }

    public function create(Request $request, Pesanan $pesanan): View
    {
        $this->authorizeCustomer($request, $pesanan);

        abort_if(
            $pesanan->status_pesanan !== 'selesai',
            403,
            'Review hanya bisa diberikan setelah pesanan selesai.'
        );

        $pesanan->load([
            'jasa.freelancer',
            'freelancer',
            'review',
        ]);

        return view('customer.review.create', compact('pesanan'));
    }

    public function store(Request $request, Pesanan $pesanan): RedirectResponse
    {
        $this->authorizeCustomer($request, $pesanan);

        abort_if(
            $pesanan->status_pesanan !== 'selesai',
            403,
            'Review hanya bisa diberikan setelah pesanan selesai.'
        );

        $request->validate([
            'rating_kualitas' => ['required', 'integer', 'min:1', 'max:5'],
            'rating_komunikasi' => ['required', 'integer', 'min:1', 'max:5'],
            'rating_waktu' => ['required', 'integer', 'min:1', 'max:5'],
            'rating_profesionalisme' => ['required', 'integer', 'min:1', 'max:5'],
            'ulasan' => ['nullable', 'string', 'max:1000'],
            'rekomendasi' => ['nullable', 'accepted'],
            'foto_review' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],

        ]);

        $fotoPath = null;

        if ($request->hasFile('foto_review')) {
            $fotoPath = CloudinaryService::uploadImage(
                $request->file('foto_review'),
                'jasakampus/review'
            );
        }

        $ratingOverall = round((
            $request->rating_kualitas +
            $request->rating_komunikasi +
            $request->rating_waktu +
            $request->rating_profesionalisme
        ) / 4);

        Review::updateOrCreate(
            [
                'id_pesanan' => $pesanan->id,
            ],
            [
                'id_jasa' => $pesanan->id_jasa,
                'id_customer' => $pesanan->id_customer,
                'id_freelancer' => $pesanan->id_freelancer,
                'rating' => $ratingOverall,
                'rating_kualitas' => $request->rating_kualitas,
                'rating_komunikasi' => $request->rating_komunikasi,
                'rating_waktu' => $request->rating_waktu,
                'rating_profesionalisme' => $request->rating_profesionalisme,
                'ulasan' => $request->ulasan,
                'rekomendasi' => $request->boolean('rekomendasi'),
                'foto_review' => $fotoPath,
            ]
        );



        return redirect()
            ->route('customer.order.show', $pesanan->id)
            ->with('success', 'Ulasan berhasil dikirim.');
    }


    public function index(Request $request): View
    {
        $user = $request->user();

        abort_if(! $user || $user->role !== 'customer', 403);

        $reviews = Review::with([
            'pesanan.jasa.freelancer',
            'freelancer',
        ])
            ->where('id_customer', $user->id)
            ->latest()
            ->get();

        return view('customer.review.index', compact('reviews'));
    }
}
