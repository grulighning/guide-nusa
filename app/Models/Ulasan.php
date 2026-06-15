<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ulasan extends Model
{
    use HasFactory;

    protected $fillable = [
        'pemandu_id',
        'user_id',
        'nama_wisatawan',
        'rating',
        'komentar',
        'destinasi',
    ];

    protected $casts = [
        'rating' => 'integer',
    ];

    public function pemandu()
    {
        return $this->belongsTo(Pemandu::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
