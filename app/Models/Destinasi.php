<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Destinasi extends Model
{
    use HasFactory;

    protected $fillable = [
        'pemandu_id',
        'nama',
        'lokasi',
        'kabupaten',
        'kategori',     // Alam, Budaya, Sejarah, Petualangan, Pantai
        'emoji',
        'warna_bg',
        'rating',
        'jumlah_pemandu',
        'deskripsi',
        'gambar_url',
        'fotos',
    ];

    protected $casts = [
        'fotos' => 'array',
    ];

    // ── Relasi ─────────────────────────────────────────────────
    public function paketTours()
    {
        return $this->belongsToMany(PaketTour::class, 'paket_destinasi');
    }

    public function pemandu()
    {
        return $this->belongsTo(Pemandu::class, 'pemandu_id');
    }

    public function reviews()
    {
        return $this->hasMany(Review::class, 'destination_id');
    }

    /**
     * Pemandu aktif yang melayani destinasi ini (via pivot destination_guide).
     */
    public function pemanduAktif()
    {
        return $this->belongsToMany(Pemandu::class, 'destination_guide', 'destination_id', 'guide_id');
    }

    /**
     * Hitung jumlah pemandu aktif secara real-time dari pivot.
     * Jika menggunakan withCount, pakai nilai dari situ.
     */
    public function getJumlahPemanduAktifAttribute(): int
    {
        if (array_key_exists('jumlah_pemandu_aktif_count', $this->attributes)) {
            return (int) $this->attributes['jumlah_pemandu_aktif_count'];
        }
        return $this->relationLoaded('pemanduAktif')
            ? $this->pemanduAktif->count()
            : $this->pemanduAktif()->count();
    }

    // ── Accessor / Mutator ─────────────────────────────────────
    public function getThumbnailAttribute(): ?string
    {
        $fotos = $this->fotos ?? [];
        if (! empty($fotos)) {
            return \Illuminate\Support\Facades\Storage::url($fotos[0]);
        }
        if ($this->gambar_url) {
            return $this->gambar_url;
        }
        return null;
    }

    // ── Scope filter kategori ──────────────────────────────────
    public function scopeKategori($query, string $kategori)
    {
        if ($kategori && $kategori !== 'semua') {
            return $query->whereRaw('LOWER(kategori) = ?', [strtolower($kategori)]);
        }
        return $query;
    }

    public function scopeCari($query, string $keyword)
    {
        return $query->where('nama', 'like', "%{$keyword}%")
                     ->orWhere('lokasi', 'like', "%{$keyword}%");
    }

    /**
     * Scope: destinasi milik pemandu tertentu.
     */
    public function scopeMilikPemandu($query, $pemanduId)
    {
        return $query->where('pemandu_id', $pemanduId);
    }
}
