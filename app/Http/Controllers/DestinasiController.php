<?php

namespace App\Http\Controllers;

use App\Models\Destinasi;
use App\Models\PemanduBooking;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DestinasiController extends Controller
{
    public function index(Request $request)
    {
        $kategori = $request->query('kategori', 'semua');
        $keyword = $request->query('cari', '');

        $destinasis = Destinasi::withCount('pemanduAktif as jumlah_pemandu_aktif_count')
            ->kategori($kategori)
            ->when($keyword, fn ($q) => $q->cari($keyword))
            ->orderByDesc('rating')
            ->get();

        $totalDestiasi = Destinasi::count();
        $ratingRataRata = number_format(Destinasi::avg('rating'), 1);
        $totalKabupaten = Destinasi::distinct('kabupaten')->count('kabupaten');
        $totalPemandu = DB::table('destination_guide')
            ->distinct('guide_id')
            ->count('guide_id');

        $kategoriList = ['Semua', 'Alam', 'Budaya', 'Sejarah', 'Petualangan', 'Pantai'];

        return view('destinasi.index', compact(
            'destinasis',
            'kategori',
            'keyword',
            'totalPemandu',
            'totalDestiasi',
            'ratingRataRata',
            'totalKabupaten',
            'kategoriList'
        ));
    }

    public function show(Destinasi $destinasi)
    {
        $destinasi->loadCount('pemanduAktif as jumlah_pemandu_aktif_count');

        $pemanduAktif = $destinasi->pemanduAktif()
            ->with('user')
            ->get();

        // Load reviews with user
        $reviews = Review::with('user')
            ->where('destination_id', $destinasi->id)
            ->latest()
            ->get();

        // Check if user has completed a booking at this destination
        $userReviewable = false;
        $userGuideReviewable = null; // the guide_id from completed booking
        $existingReview = null;

        if (auth()->check() && auth()->user()->isWisatawan()) {
            // Cari booking selesai: prioritas via paketTour, fallback via guide yg terdaftar di destinasi ini
            $completedBooking = PemanduBooking::where('wisatawan_id', auth()->id())
                ->where('status', 'selesai')
                ->where(function ($q) use ($destinasi) {
                    $q->whereHas('paketTour.destinasis', fn ($q) => $q->where('destinasis.id', $destinasi->id))
                      ->orWhereHas('pemandu.destinasiAktif', fn ($q) => $q->where('destinasis.id', $destinasi->id))
                      ->orWhereHas('pemandu.destinasis', fn ($q) => $q->where('id', $destinasi->id));
                })
                ->latest()
                ->first();

            if ($completedBooking) {
                $userReviewable = true;
                $userGuideReviewable = $completedBooking->pemandu_id;

                // Check if user already reviewed this destination with this guide
                $existingReview = Review::where('user_id', auth()->id())
                    ->where('destination_id', $destinasi->id)
                    ->where('guide_id', $completedBooking->pemandu_id)
                    ->first();
            }
        }

        return view('destinasi.show', compact(
            'destinasi',
            'pemanduAktif',
            'reviews',
            'userReviewable',
            'userGuideReviewable',
            'existingReview'
        ));
    }
}
