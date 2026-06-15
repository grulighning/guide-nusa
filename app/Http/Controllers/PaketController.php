<?php

namespace App\Http\Controllers;

use App\Models\Destinasi;
use App\Models\PaketTour;
use App\Models\PemanduBooking;
use App\Models\Pemandu;
use Illuminate\Http\Request;
use App\Models\Review;
use Illuminate\Support\Arr;

class PaketController extends Controller
{
    public function index(Request $request)
    {
        $durasi  = $request->query('durasi', 'semua');
        $keyword = $request->query('cari', '');

        $pakets = PaketTour::with(['destinasis', 'pemandu.user'])
            ->aktif()
            ->durasi($durasi)
            ->when($keyword, fn ($q) => $q->where('nama', 'like', "%{$keyword}%"))
            ->orderByDesc('is_featured')
            ->get();

        // Hitung tingkat kepuasan dari rating review pemandu yang memiliki paket aktif (1-5 bintang -> %)
        $guideIds  = PaketTour::aktif()->pluck('pemandu_id')->filter()->unique();
        $avgRating = $guideIds->isNotEmpty()
            ? Review::whereIn('guide_id', $guideIds)->avg('rating')
            : null;
        $kepuasan  = $avgRating ? round(($avgRating / 5) * 100) : 0;

        $stats = [
            'paket_aktif'  => PaketTour::aktif()->count(),
            'tour_selesai' => PaketTour::where('status', 'selesai')->count(),
            'harga_mulai'  => PaketTour::aktif()->min('harga'),
            'kepuasan'     => $kepuasan,
        ];

        $durasiList = ['Semua', '1 Hari', '2 Hari', 'Weekend', 'Grup'];

        return view('paket.index', compact('pakets', 'stats', 'durasi', 'keyword', 'durasiList'));
    }

    public function create()
    {
        $destinasis = Destinasi::orderBy('nama')->get();
        $pemandus = Pemandu::with('user')->orderByDesc('rating')->get();

        return view('paket.create', compact('destinasis', 'pemandus'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama'            => 'required|string|max:255',
            'deskripsi'       => 'required|string',
            'harga'           => 'required|integer|min:0',
            'durasi'          => 'required|string',
            'jam_mulai'       => 'required',
            'jam_selesai'     => 'required',
            'max_peserta'     => 'required|integer|min:1',
            'pemandu_id'      => 'required|exists:pemandus,id',
            'destinasi_ids'   => 'required|array|min:1',
            'destinasi_ids.*' => 'exists:destinasis,id',
            'tanggal_mulai'   => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'catatan'         => 'nullable|string',
        ]);

        $paket = PaketTour::create([
            ...Arr::except($validated, ['destinasi_ids']),
            'user_id' => auth()->id(),
            'status'  => 'aktif',
        ]);

        $paket->destinasis()->sync($validated['destinasi_ids']);

        return redirect()->route('paket.index')
            ->with('success', 'Paket tour berhasil dibuat!');
    }

    public function edit(PaketTour $paket)
    {
        abort_if(auth()->id() !== $paket->user_id, 403, 'Anda tidak berhak mengedit paket ini.');

        $destinasis = Destinasi::orderBy('nama')->get();
        $pemandus = Pemandu::with('user')->orderByDesc('rating')->get();
        $selectedDestinasi = $paket->destinasis->pluck('id')->toArray();

        return view('paket.edit', compact('paket', 'destinasis', 'pemandus', 'selectedDestinasi'));
    }

    public function update(Request $request, PaketTour $paket)
    {
        abort_if(auth()->id() !== $paket->user_id, 403, 'Anda tidak berhak mengubah paket ini.');

        $validated = $request->validate([
            'nama'            => 'required|string|max:255',
            'deskripsi'       => 'required|string',
            'harga'           => 'required|integer|min:0',
            'durasi'          => 'required|string',
            'jam_mulai'       => 'required',
            'jam_selesai'     => 'required',
            'max_peserta'     => 'required|integer|min:1',
            'pemandu_id'      => 'required|exists:pemandus,id',
            'destinasi_ids'   => 'required|array|min:1',
            'destinasi_ids.*' => 'exists:destinasis,id',
            'tanggal_mulai'   => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'catatan'         => 'nullable|string',
        ]);

        $paket->update(Arr::except($validated, ['destinasi_ids']));
        $paket->destinasis()->sync($validated['destinasi_ids']);

        return redirect()->route('paket.index')
            ->with('success', 'Paket tour berhasil diperbarui!');
    }

    public function destroy(PaketTour $paket)
    {
        abort_if(auth()->id() !== $paket->user_id, 403, 'Anda tidak berhak menghapus paket ini.');

        $paket->delete();

        return redirect()->route('paket.index')
            ->with('success', 'Paket tour berhasil dihapus.');
    }

    public function booking(Request $request, PaketTour $paket)
    {
        abort_unless(auth()->user()->isWisatawan(), 403, 'Hanya wisatawan yang bisa booking paket.');

        if ($paket->pemandu_id) {
            PemanduBooking::create([
                'pemandu_id'      => $paket->pemandu_id,
                'wisatawan_id'    => auth()->id(),
                'paket_tour_id'   => $paket->id,
                'tanggal_booking' => $paket->tanggal_mulai,
                'jumlah_peserta'  => 1,
                'catatan'         => 'Booking dari paket tour: ' . $paket->nama,
                'status'          => 'menunggu',
            ]);
        }

        return redirect()->back()->with('success', 'Booking berhasil! Pemandu terkait akan melihat pesanan ini di dashboardnya.');
    }
}
