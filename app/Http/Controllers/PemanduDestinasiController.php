<?php

namespace App\Http\Controllers;

use App\Models\Destinasi;
use App\Models\Pemandu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PemanduDestinasiController extends Controller
{
    /**
     * Daftar destinasi milik pemandu yang login.
     */
    public function index()
    {
        $pemandu = auth()->user()->pemandu;

        if (! $pemandu) {
            return redirect()->route('dashboard')->with('error', 'Akun pemandu tidak ditemukan.');
        }

        $destinasis = $pemandu->destinasis()
            ->withCount('pemanduAktif as jumlah_pemandu_aktif_count')
            ->latest()
            ->get();

        return view('pemandu.destinasi.index', compact('destinasis', 'pemandu'));
    }

    /**
     * Form tambah destinasi.
     */
    public function create()
    {
        $kategoriList = ['Alam', 'Budaya', 'Sejarah', 'Petualangan', 'Pantai'];
        $emojiList = [
            '🏔️' => 'Gunung',
            '🏝️' => 'Pulau',
            '🏯' => 'Kuil',
            '🌊' => 'Pantai',
            '🌋' => 'Gunung Berapi',
            '🌲' => 'Hutan',
            '🏛️' => 'Sejarah',
            '🎭' => 'Budaya',
            '🐠' => 'Laut',
            '🌺' => 'Alam',
        ];

        return view('pemandu.destinasi.create', compact('kategoriList', 'emojiList'));
    }

    /**
     * Simpan destinasi baru beserta foto.
     */
    public function store(Request $request)
    {
        $pemandu = auth()->user()->pemandu;
        if (! $pemandu) {
            return back()->with('error', 'Akun pemandu tidak ditemukan.');
        }

        $validated = $request->validate([
            'nama'       => 'required|string|max:255',
            'lokasi'     => 'required|string|max:255',
            'kabupaten'  => 'nullable|string|max:100',
            'kategori'   => 'required|in:Alam,Budaya,Sejarah,Petualangan,Pantai',
            'emoji'      => 'nullable|string|max:10',
            'deskripsi'  => 'nullable|string',
            'fotos.*'    => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
        ]);

        $fotos = [];

        // Upload multiple photos
        if ($request->hasFile('fotos')) {
            foreach ($request->file('fotos') as $foto) {
                $path = $foto->store('destinasi-fotos', 'public');
                if ($path) {
                    $fotos[] = $path;
                }
            }
        }

        $validated['fotos'] = $fotos;
        $validated['pemandu_id'] = $pemandu->id;
        $validated['warna_bg'] = $request->warna_bg ?? $this->randomWarnaBg();
        $validated['rating'] = 0;
        $validated['jumlah_pemandu'] = 1;

        $destinasi = Destinasi::create($validated);

        // Daftarkan pemandu sebagai pemandu aktif di destinasi ini
        $destinasi->pemanduAktif()->syncWithoutDetaching([$pemandu->id]);

        return redirect()->route('pemandu.destinasi.index')
            ->with('success', 'Destinasi berhasil ditambahkan!');
    }

    /**
     * Form edit destinasi.
     */
    public function edit(Destinasi $destinasi)
    {
        $pemandu = auth()->user()->pemandu;

        if (! $pemandu || $destinasi->pemandu_id !== $pemandu->id) {
            abort(403, 'Anda tidak memiliki akses ke destinasi ini.');
        }

        $kategoriList = ['Alam', 'Budaya', 'Sejarah', 'Petualangan', 'Pantai'];
        $emojiList = [
            '🏔️' => 'Gunung',
            '🏝️' => 'Pulau',
            '🏯' => 'Kuil',
            '🌊' => 'Pantai',
            '🌋' => 'Gunung Berapi',
            '🌲' => 'Hutan',
            '🏛️' => 'Sejarah',
            '🎭' => 'Budaya',
            '🐠' => 'Laut',
            '🌺' => 'Alam',
        ];

        return view('pemandu.destinasi.edit', compact('destinasi', 'kategoriList', 'emojiList'));
    }

    /**
     * Update destinasi.
     */
    public function update(Request $request, Destinasi $destinasi)
    {
        $pemandu = auth()->user()->pemandu;

        if (! $pemandu || $destinasi->pemandu_id !== $pemandu->id) {
            abort(403, 'Anda tidak memiliki akses ke destinasi ini.');
        }

        $validated = $request->validate([
            'nama'       => 'required|string|max:255',
            'lokasi'     => 'required|string|max:255',
            'kabupaten'  => 'nullable|string|max:100',
            'kategori'   => 'required|in:Alam,Budaya,Sejarah,Petualangan,Pantai',
            'emoji'      => 'nullable|string|max:10',
            'deskripsi'  => 'nullable|string',
            'fotos.*'    => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'hapus_fotos' => 'nullable|string',
        ]);

        $fotos = $destinasi->fotos ?? [];

        // Hapus foto yang dipilih
        if ($request->hapus_fotos) {
            $hapusList = explode(',', $request->hapus_fotos);
            foreach ($hapusList as $fotoToDelete) {
                $fotoToDelete = trim($fotoToDelete);
                if (($key = array_search($fotoToDelete, $fotos)) !== false) {
                    Storage::disk('public')->delete($fotoToDelete);
                    unset($fotos[$key]);
                }
            }
        }

        // Upload foto baru
        if ($request->hasFile('fotos')) {
            foreach ($request->file('fotos') as $foto) {
                $path = $foto->store('destinasi-fotos', 'public');
                if ($path) {
                    $fotos[] = $path;
                }
            }
        }

        $validated['fotos'] = array_values($fotos);
        $destinasi->update($validated);

        return redirect()->route('pemandu.destinasi.index')
            ->with('success', 'Destinasi berhasil diperbarui!');
    }

    /**
     * Hapus destinasi beserta foto-fotonya.
     */
    public function destroy(Destinasi $destinasi)
    {
        $pemandu = auth()->user()->pemandu;

        if (! $pemandu || $destinasi->pemandu_id !== $pemandu->id) {
            abort(403, 'Anda tidak memiliki akses ke destinasi ini.');
        }

        // Hapus semua foto dari storage
        $fotos = $destinasi->fotos ?? [];
        foreach ($fotos as $foto) {
            Storage::disk('public')->delete($foto);
        }

        // Lepas semua relasi pemandu aktif dari pivot
        $destinasi->pemanduAktif()->detach();

        $destinasi->delete();

        return redirect()->route('pemandu.destinasi.index')
            ->with('success', 'Destinasi berhasil dihapus.');
    }

    /**
     * Generate warna background acak untuk destinasi.
     */
    private function randomWarnaBg(): string
    {
        $colors = [
            'linear-gradient(135deg,#1b5e20,#388e3c)',
            'linear-gradient(135deg,#0d47a1,#1976d2)',
            'linear-gradient(135deg,#4a148c,#7b1fa2)',
            'linear-gradient(135deg,#e65100,#f57c00)',
            'linear-gradient(135deg,#b71c1c,#d32f2f)',
            'linear-gradient(135deg,#004d40,#00897b)',
            'linear-gradient(135deg,#311b92,#512da8)',
            'linear-gradient(135deg,#1a237e,#283593)',
        ];
        return $colors[array_rand($colors)];
    }
}
