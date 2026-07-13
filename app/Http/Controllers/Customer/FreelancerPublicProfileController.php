<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Jasa;
use App\Models\Pesanan;
use App\Models\Portofolio;
use App\Models\Review;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FreelancerPublicProfileController extends Controller
{
    private function authorizeCustomer(Request $request): void
    {
        $user = $request->user();

        abort_if(! $user || $user->role !== 'customer', 403);
    }

    public function show(Request $request, User $freelancer): View
    {
        $this->authorizeCustomer($request);

        abort_if($freelancer->role !== 'freelancer', 404);

        $freelancer->load('verifikasiFreelancer');

        abort_if(
            optional($freelancer->verifikasiFreelancer)->status_verifikasi !== 'approved',
            404
        );

        $jasaAktif = Jasa::withAvg('reviews as rating_rata_rata', 'rating')
            ->withCount('reviews')
            ->where('id_freelancer', $freelancer->id)
            ->where('status_jasa', 'active')
            ->latest()
            ->take(6)
            ->get();

        $portofolios = Portofolio::where('id_freelancer', $freelancer->id)
            ->latest()
            ->take(6)
            ->get();

        $reviews = Review::with(['customer', 'jasa'])
            ->where('id_freelancer', $freelancer->id)
            ->latest()
            ->take(6)
            ->get();

        $ratingRataRata = Review::where('id_freelancer', $freelancer->id)
            ->avg('rating');

        $totalReview = Review::where('id_freelancer', $freelancer->id)
            ->count();

        $totalJasaAktif = Jasa::where('id_freelancer', $freelancer->id)
            ->where('status_jasa', 'active')
            ->count();

        $totalProyekSelesai = Pesanan::where('id_freelancer', $freelancer->id)
            ->where('status_pesanan', 'selesai')
            ->count();

        return view('customer.freelancer-profile', compact(
            'freelancer',
            'jasaAktif',
            'portofolios',
            'reviews',
            'ratingRataRata',
            'totalReview',
            'totalJasaAktif',
            'totalProyekSelesai'
        ));
    }
}