<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PemanduBooking extends Model
{
    use HasFactory;

    protected $fillable = [
        'pemandu_id',
        'wisatawan_id',
        'paket_tour_id',
        'tanggal_booking',
        'jumlah_peserta',
        'catatan',
        'status',
    ];

    protected $casts = [
        'tanggal_booking' => 'date',
        'jumlah_peserta' => 'integer',
    ];

    public function pemandu()
    {
        return $this->belongsTo(Pemandu::class);
    }

    public function wisatawan()
    {
        return $this->belongsTo(User::class, 'wisatawan_id');
    }

    public function paketTour()
    {
        return $this->belongsTo(PaketTour::class);
    }
}
