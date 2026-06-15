<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaketTour extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'pemandu_id',
        'nama',
        'deskripsi',
        'harga',
        'durasi',           // Contoh: '1 Hari', '2 Hari'
        'jam_mulai',
        'jam_selesai',
        'max_peserta',
        'tanggal_mulai',
        'tanggal_selesai',
        'catatan',
        'status',           // aktif | nonaktif | selesai
        'is_featured',
        'badge',            // Contoh: '⭐ Terpopuler'
    ];

    protected $casts = [
        'tanggal_mulai'  => 'date',
        'tanggal_selesai'=> 'date',
        'is_featured'    => 'boolean',
        'harga'          => 'integer',
    ];

    // ── Relasi ─────────────────────────────────────────────────
    public function pemandu()
    {
        return $this->belongsTo(Pemandu::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function destinasis()
    {
        return $this->belongsToMany(Destinasi::class, 'paket_destinasi');
    }

    public function bookings()
    {
        return $this->hasMany(PemanduBooking::class);
    }

    // ── Scope ──────────────────────────────────────────────────
    public function scopeAktif($query)
    {
        return $query->where('status', 'aktif');
    }

    public function scopeDurasi($query, string $durasi)
    {
        if ($durasi && $durasi !== 'semua') {
            return $query->where('durasi', 'like', "%{$durasi}%");
        }
        return $query;
    }

    // ── Accessor ───────────────────────────────────────────────
    public function getHargaFormatAttribute(): string
    {
        return 'Rp' . number_format($this->harga / 1000, 0, ',', '.') . 'k';
    }
}
