<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Favorite;
use App\Models\Jasa;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FavoriteController extends Controller
{
    private function authorizeCustomer(Request $request): void
    {
        $user = $request->user();

        abort_if(! $user || $user->role !== 'customer', 403);
    }

    public function index(Request $request): View
    {
        $this->authorizeCustomer($request);

        $favorites = Favorite::with([
            'jasa.freelancer',
            'jasa.reviews',
        ])
            ->where('id_customer', $request->user()->id)
            ->latest()
            ->get();

        return view('customer.favorite.index', compact('favorites'));
    }

    public function toggle(Request $request, Jasa $jasa): RedirectResponse
    {
        $this->authorizeCustomer($request);

        abort_if($jasa->status_jasa !== 'active', 404);

        $favorite = Favorite::where('id_customer', $request->user()->id)
            ->where('id_jasa', $jasa->id)
            ->first();

        if ($favorite) {
            $favorite->delete();

            return back()->with('success', 'Jasa berhasil dihapus dari favorite.');
        }

        Favorite::create([
            'id_customer' => $request->user()->id,
            'id_jasa' => $jasa->id,
        ]);

        return back()->with('success', 'Jasa berhasil ditambahkan ke favorite.');
    }
}
