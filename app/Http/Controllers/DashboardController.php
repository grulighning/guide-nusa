<?php

namespace App\Http\Controllers;

use App\Models\Destinasi;
use App\Models\PaketTour;
use App\Models\Pemandu;
use App\Models\Review;
use Illuminate\Support\Collection;

class DashboardController extends Controller
{
    public function index()
    {
        $bookingPemandu = new Collection();
        $bookingWisatawan = new Collection();
        $jumlahBookingMenunggu = 0;

        if (auth()->check() && auth()->user()->isPemandu() && auth()->user()->pemandu) {
            $pemanduLogin = auth()->user()->pemandu;

            $bookingPemandu = $pemanduLogin->bookings()
                ->with(['wisatawan', 'paketTour.destinasis'])
                ->latest()
                ->take(8)
                ->get();

            $jumlahBookingMenunggu = $pemanduLogin->bookings()
                ->where('status', 'menunggu')
                ->count();
        }

        if (auth()->check() && auth()->user()->isWisatawan()) {
            $bookingWisatawan = auth()->user()->pemanduBookings()
                ->with(['pemandu.user', 'paketTour.destinasis'])
                ->latest()
                ->take(8)
                ->get();

            $userReviews = auth()->user()->reviews()
                ->with(['destination', 'guide.user'])
                ->latest()
                ->take(3)
                ->get();
        } else {
            $userReviews = new Collection();
        }

        $destinasiPopuler = Destinasi::withCount('pemanduAktif as jumlah_pemandu_aktif_count')
            ->orderByDesc('rating')
            ->take(4)
            ->get();

        $tourAktif = PaketTour::with(['destinasis', 'pemandu.user'])
            ->aktif()
            ->orderByDesc('is_featured')
            ->take(3)
            ->get();

        $pemanduTerbaik = Pemandu::with('user')
            ->orderByDesc('rating')
            ->take(3)
            ->get();

        return view('dashboard.index', compact(
            'destinasiPopuler',
            'tourAktif',
            'pemanduTerbaik',
            'bookingPemandu',
            'bookingWisatawan',
            'userReviews',
            'jumlahBookingMenunggu'
        ));
    }

    public function myReviews()
    {
        $reviews = Review::with(['user', 'destination', 'guide.user'])
            ->where('user_id', auth()->id())
            ->latest()
            ->get();

        return view('dashboard.reviews', compact('reviews'));
    }
}
