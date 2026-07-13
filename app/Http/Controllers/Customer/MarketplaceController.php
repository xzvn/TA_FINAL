<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Jasa;
use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Models\Chat;
use App\Models\Review;

class MarketplaceController extends Controller
{
    private function authorizeCustomer(Request $request): void
    {
        $user = $request->user();

        abort_if(! $user || $user->role !== 'customer', 403);
    }

    public function index(Request $request): View
    {
        $this->authorizeCustomer($request);

        $query = Jasa::with('freelancer')
            ->withAvg('reviews as rating_rata_rata', 'rating')
            ->withCount('reviews')
            ->where('status_jasa', 'active')
            ->whereHas('freelancer.verifikasiFreelancer', function ($q) {
                $q->where('status_verifikasi', 'approved');
            });

        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('nama_jasa', 'like', '%' . $search . '%')
                    ->orWhere('kategori', 'like', '%' . $search . '%')
                    ->orWhere('deskripsi', 'like', '%' . $search . '%');
            });
        }

        if ($request->filled('kategori')) {
            $query->where('kategori', $request->kategori);
        }

        $jasa = $query
            ->latest()
            ->get();

        return view('customer.dashboard', compact('jasa'));
    }
    public function show(Request $request, Jasa $jasa): View
    {
        $this->authorizeCustomer($request);

        abort_if($jasa->status_jasa !== 'active', 404);

        $jasa->load([
            'freelancer.verifikasiFreelancer',
            'freelancer.portofolios',
            'reviews.customer',
        ]);

        $ratingJasa = Review::where('id_jasa', $jasa->id)
            ->avg('rating');

        $totalReviewJasa = Review::where('id_jasa', $jasa->id)
            ->count();



        $sudahChat = Chat::where('id_jasa', $jasa->id)
            ->where('id_customer', $request->user()->id)
            ->where('id_freelancer', $jasa->id_freelancer)
            ->exists();

        return view('customer.detail-jasa', compact(
            'jasa',
            'sudahChat',
            'ratingJasa',
            'totalReviewJasa'
        ));
    }
}
