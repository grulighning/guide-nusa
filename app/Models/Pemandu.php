<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pemandu extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'spesialisasi',   // Contoh: Fotografi, Alam, Kuliner, Sejarah
        'pengalaman_tahun',
        'rating',
        'jumlah_tour',
        'warna_avatar',
        'inisial',
        'bio',
        'ketersediaan',      // JSON: [0,1,2,...] index hari (0=Sen...6=Min)
        'tanggal_tersedia',   // JSON: ['2026-06-20', '2026-06-25', ...]
    ];

    protected $casts = [
        'ketersediaan'    => 'array',
        'tanggal_tersedia' => 'array',
    ];

    // ── Relasi ─────────────────────────────────────────────────
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function paketTours()
    {
        return $this->hasMany(PaketTour::class);
    }

    public function ulasans()
    {
        return $this->hasMany(Ulasan::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class, 'guide_id');
    }

    public function bookings()
    {
        return $this->hasMany(PemanduBooking::class);
    }

    /**
     * Destinasi yang dikelola oleh pemandu ini (milik sendiri).
     */
    public function destinasis()
    {
        return $this->hasMany(Destinasi::class, 'pemandu_id');
    }

    /**
     * Destinasi di mana pemandu ini terdaftar sebagai pemandu aktif (via pivot).
     */
    public function destinasiAktif()
    {
        return $this->belongsToMany(Destinasi::class, 'destination_guide', 'guide_id', 'destination_id');
    }

    // ── Helper: cek apakah hari tertentu tersedia ────────────
    /**
     * Cek apakah tanggal tertentu termasuk dalam ketersediaan pemandu.
     * Pengecekan:
     * 1. Jika ada tanggal_tersedia spesifik, cek apakah tanggal ada di daftar.
     * 2. Jika tidak, cek berdasarkan pola hari mingguan (ketersediaan).
     *
     * @param string|\Carbon\Carbon $date
     * @return bool
     */
    public function isDateAvailable($date): bool
    {
        $dateStr = $date instanceof \Carbon\Carbon ? $date->format('Y-m-d') : date('Y-m-d', strtotime($date));

        // Prioritas 1: Cek tanggal spesifik (tanggal_tersedia)
        $tanggalTersedia = $this->tanggal_tersedia ?? [];
        if (is_array($tanggalTersedia) && count($tanggalTersedia) > 0) {
            return in_array($dateStr, $tanggalTersedia);
        }

        // Prioritas 2: Cek pola hari mingguan (ketersediaan)
        if (empty($this->ketersediaan) || !is_array($this->ketersediaan)) {
            return false;
        }

        $dayOfWeek = (int) date('w', strtotime($dateStr));
        $ourIndex  = $dayOfWeek === 0 ? 6 : $dayOfWeek - 1;

        return in_array($ourIndex, array_map('intval', $this->ketersediaan));
    }

    /**
     * Ambil daftar tanggal yang sudah di-booking (dikonfirmasi atau menunggu) untuk pemandu ini.
     *
     * @return \Illuminate\Support\Collection
     */
    public function getBookedDates()
    {
        return $this->bookings()
            ->whereIn('status', ['menunggu', 'dikonfirmasi', 'menunggu_konfirmasi_selesai'])
            ->whereNotNull('tanggal_booking')
            ->pluck('tanggal_booking')
            ->map(fn($d) => $d instanceof \Carbon\Carbon ? $d->format('Y-m-d') : $d);
    }

    /**
     * Ambil daftar tanggal tersedia yang sudah diformat (Y-m-d).
     *
     * @return array
     */
    public function getTanggalTersediaList(): array
    {
        $list = $this->tanggal_tersedia ?? [];
        if (!is_array($list)) {
            return [];
        }
        return array_map(function ($d) {
            return $d instanceof \Carbon\Carbon ? $d->format('Y-m-d') : $d;
        }, $list);
    }

    // ── Accessor ───────────────────────────────────────────────
    public function getNamaAttribute(): string
    {
        return $this->user->name ?? '';
    }
}
