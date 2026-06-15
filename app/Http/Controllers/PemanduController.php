<?php

namespace App\Http\Controllers;

use App\Models\PemanduBooking;
use App\Models\Pemandu;
use App\Models\Review;
use Illuminate\Http\Request;

class PemanduController extends Controller
{
    public function dashboard()
    {
        $pemandu = auth()->user()->pemandu;

        if (! $pemandu) {
            return redirect()->route('dashboard')->with('error', 'Akun pemandu tidak ditemukan.');
        }

        $bookings = $pemandu->bookings()
            ->with(['wisatawan', 'paketTour.destinasis'])
            ->latest()
            ->get();

        // Data ketersediaan untuk dashboard
        $bookedDates = $pemandu->getBookedDates();

        return view('pemandu.dashboard', compact('pemandu', 'bookings', 'bookedDates'));
    }

    public function profile()
    {
        $pemandu = auth()->user()->pemandu;

        if (! $pemandu) {
            return redirect()->route('dashboard')->with('error', 'Akun pemandu tidak ditemukan.');
        }

        $pemandu->load(['user', 'paketTours.destinasis', 'ulasans']);

        // Load reviews from Review model
        $reviews = Review::with('user')
            ->where('guide_id', $pemandu->id)
            ->latest()
            ->get();

        return view('pemandu.profile', compact('pemandu', 'reviews'));
    }

    public function index(Request $request)
    {
        $keyword      = $request->query('cari', '');
        $spesialisasi = $request->query('spesialisasi', 'semua');

        $pemandus = Pemandu::with('user')
            ->when($keyword, fn($q) => $q->whereHas('user', fn($u) =>
                $u->where('name', 'like', "%{$keyword}%")
            ))
            ->when($spesialisasi !== 'semua', fn($q) =>
                $q->where('spesialisasi', 'like', "%{$spesialisasi}%")
            )
            ->orderByDesc('rating')
            ->get();

        return view('pemandu.index', compact('pemandus', 'keyword', 'spesialisasi'));
    }

    public function show(Pemandu $pemandu)
    {
        $pemandu->load(['user', 'paketTours.destinasis', 'ulasans']);

        // Load reviews from Review model
        $reviews = Review::with('user')
            ->where('guide_id', $pemandu->id)
            ->latest()
            ->get();

        // Check if current user can review this guide
        $userReviewable = false;
        $userCompletedBooking = null;
        $existingReview = null;
        $reviewDestinasi = null;

        if (auth()->check() && auth()->user()->isWisatawan()) {
            $completedBooking = PemanduBooking::where('pemandu_id', $pemandu->id)
                ->where('wisatawan_id', auth()->id())
                ->where('status', 'selesai')
                ->with('paketTour.destinasis')
                ->latest()
                ->first();

            if ($completedBooking) {
                $userCompletedBooking = $completedBooking;
                $userReviewable = true;

                // Cari destinasi: prioritas dari paketTour, fallback ke destinasi pemandu
                $destinasi = $completedBooking->paketTour?->destinasis?->first()
                    ?? $pemandu->destinasiAktif()->first()
                    ?? $pemandu->destinasis()->first();

                if ($destinasi) {
                    $reviewDestinasi = $destinasi;
                    $existingReview = Review::where('user_id', auth()->id())
                        ->where('guide_id', $pemandu->id)
                        ->where('destination_id', $destinasi->id)
                        ->first();
                }
            }
        }

        return view('pemandu.show', compact(
            'pemandu',
            'reviews',
            'userReviewable',
            'userCompletedBooking',
            'existingReview',
            'reviewDestinasi'
        ));
    }

    public function booking(Request $request, Pemandu $pemandu)
    {
        abort_unless(auth()->user()->isWisatawan(), 403, 'Hanya wisatawan yang bisa memesan pemandu.');

        $validated = $request->validate([
            'tanggal_booking' => 'nullable|date|after_or_equal:today',
            'jumlah_peserta'  => 'nullable|integer|min:1|max:100',
            'catatan'         => 'nullable|string|max:1000',
        ]);

        // ── Validasi: tanggal booking harus sesuai hari ketersediaan pemandu ──
        if (! empty($validated['tanggal_booking'])) {
            $bookingDate = $validated['tanggal_booking'];

            // Cek apakah hari (day-of-week) termasuk dalam ketersediaan
            if (! $pemandu->isDateAvailable($bookingDate)) {
                $hariNames = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];
                $dayOfWeek = (int) date('w', strtotime($bookingDate));
                $hariNama  = $hariNames[$dayOfWeek === 0 ? 6 : $dayOfWeek - 1];

                return redirect()->back()
                    ->withInput()
                    ->withErrors(['tanggal_booking' => "Pemandu tidak tersedia pada hari {$hariNama}. Silakan pilih tanggal lain yang sesuai dengan jadwal ketersediaan pemandu."]);
            }

            // Cek apakah tanggal sudah di-booking (double-booking prevention)
            $existingBooking = PemanduBooking::where('pemandu_id', $pemandu->id)
                ->where('tanggal_booking', $bookingDate)
                ->whereIn('status', ['menunggu', 'dikonfirmasi', 'menunggu_konfirmasi_selesai'])
                ->exists();

            if ($existingBooking) {
                return redirect()->back()
                    ->withInput()
                    ->withErrors(['tanggal_booking' => 'Tanggal ini sudah memiliki booking. Silakan pilih tanggal lain.']);
            }
        }

        PemanduBooking::create([
            'pemandu_id'      => $pemandu->id,
            'wisatawan_id'    => auth()->id(),
            'tanggal_booking' => $validated['tanggal_booking'] ?? null,
            'jumlah_peserta'  => $validated['jumlah_peserta'] ?? 1,
            'catatan'         => $validated['catatan'] ?? null,
            'status'          => 'menunggu',
        ]);

        return redirect()->route('pemandu.show', $pemandu)
            ->with('success', 'Booking pemandu berhasil dikirim. Pemandu akan melihat pesanan ini di dashboardnya.');
    }

    public function confirmBooking(PemanduBooking $booking)
    {
        $pemandu = auth()->user()->pemandu;

        abort_if(! $pemandu || $booking->pemandu_id !== $pemandu->id, 403, 'Anda tidak berhak mengonfirmasi booking ini.');
        abort_if($booking->status !== 'menunggu', 422, 'Booking ini sudah diproses.');

        $booking->update([
            'status' => 'dikonfirmasi',
        ]);

        $namaWisatawan = $booking->wisatawan->name ?? 'wisatawan';

        return back()
            ->with('success', "Pesanan dari {$namaWisatawan} berhasil dikonfirmasi.");
    }

    public function rejectBooking(PemanduBooking $booking)
    {
        $pemandu = auth()->user()->pemandu;

        abort_if(! $pemandu || $booking->pemandu_id !== $pemandu->id, 403, 'Anda tidak berhak menolak booking ini.');
        abort_if($booking->status !== 'menunggu', 422, 'Booking ini sudah diproses.');

        $booking->update([
            'status' => 'dibatalkan',
        ]);

        $namaWisatawan = $booking->wisatawan->name ?? 'wisatawan';

        return back()
            ->with('error', "Pesanan dari {$namaWisatawan} telah ditolak.");
    }

    public function completeBooking(PemanduBooking $booking)
    {
        $pemandu = auth()->user()->pemandu;

        abort_if(! $pemandu || $booking->pemandu_id !== $pemandu->id, 403, 'Anda tidak berhak menyelesaikan booking ini.');
        abort_if($booking->status !== 'dikonfirmasi', 422, 'Booking hanya bisa diselesaikan setelah dikonfirmasi.');

        $booking->update([
            'status' => 'menunggu_konfirmasi_selesai',
        ]);

        $namaWisatawan = $booking->wisatawan->name ?? 'wisatawan';

        return back()
            ->with('success', "Tour bersama {$namaWisatawan} sudah ditandai selesai. Menunggu konfirmasi dari wisatawan.");
    }

    public function updateProfile(Request $request)
    {
        $pemandu = auth()->user()->pemandu;

        if (! $pemandu) {
            abort(403, 'Akun pemandu tidak ditemukan.');
        }

        $validated = $request->validate([
            'pengalaman_tahun' => 'required|integer|min:0|max:100',
        ]);

        $pemandu->update([
            'pengalaman_tahun' => $validated['pengalaman_tahun'],
        ]);

        return redirect()->back()->with('success', 'Pengalaman berhasil diperbarui!');
    }

    public function updateKetersediaan(Request $request)
    {
        \Log::info('Update ketersediaan dipanggil', [
            'user_id' => auth()->id(),
            'request_data' => $request->all()
        ]);
        
        $pemandu = auth()->user()->pemandu;

        if (!$pemandu) {
            \Log::error('Pemandu tidak ditemukan untuk user: ' . auth()->id());
            abort(403, 'Akun pemandu tidak ditemukan.');
        }

        try {
            $validated = $request->validate([
                'ketersediaan'     => 'nullable|array',
                'ketersediaan.*'   => 'integer|min:0|max:6',
                'tanggal_tersedia' => 'nullable|array',
                'tanggal_tersedia.*' => 'date|after_or_equal:today',
            ]);

            \Log::info('Validasi berhasil', $validated);

            $pemandu->update([
                'ketersediaan'    => array_map('intval', $validated['ketersediaan'] ?? []),
                'tanggal_tersedia' => $validated['tanggal_tersedia'] ?? [],
            ]);

            \Log::info('Update berhasil', ['pemandu_id' => $pemandu->id]);

            return redirect()->back()
                ->with('success', 'Ketersediaan berhasil diperbarui.');
        } catch (\Exception $e) {
            \Log::error('Error update ketersediaan: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function userConfirmComplete(PemanduBooking $booking)
    {
        abort_if(! auth()->user()->isWisatawan(), 403, 'Hanya wisatawan yang bisa mengonfirmasi penyelesaian tour.');
        abort_if($booking->wisatawan_id !== auth()->id(), 403, 'Anda tidak berhak mengonfirmasi booking ini.');
        abort_if($booking->status !== 'menunggu_konfirmasi_selesai', 422, 'Booking ini sudah selesai atau belum ditandai selesai oleh pemandu.');

        $booking->update([
            'status' => 'selesai',
        ]);

        // Update jumlah_tour pemandu — hitung ulang dari semua booking selesai
        $totalSelesai = PemanduBooking::where('pemandu_id', $booking->pemandu_id)
            ->where('status', 'selesai')
            ->count();

        $booking->pemandu->update([
            'jumlah_tour' => $totalSelesai,
        ]);

        $namaPemandu = $booking->pemandu->user->name ?? 'Pemandu';

        return back()
            ->with('success', "Tour bersama {$namaPemandu} telah selesai. Terima kasih telah menggunakan layanan kami!");
    }
}