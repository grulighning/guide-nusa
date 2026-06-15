<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'role', // 'pemandu' | 'wisatawan'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
        ];
    }

    // ── Helpers ────────────────────────────────────────────────
    public function isPemandu(): bool
    {
        return $this->role === 'pemandu';
    }

    public function isWisatawan(): bool
    {
        return $this->role === 'wisatawan';
    }

    // ── Relasi ─────────────────────────────────────────────────
    public function pemandu()
    {
        return $this->hasOne(Pemandu::class);
    }

    public function paketTours()
    {
        return $this->hasMany(PaketTour::class);
    }

    public function pemanduBookings()
    {
        return $this->hasMany(PemanduBooking::class, 'wisatawan_id');
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    // ── Chat ───────────────────────────────────────────────────
    /**
     * Pesan yang dikirim oleh user ini.
     */
    public function sentMessages()
    {
        return $this->hasMany(ChatMessage::class, 'sender_id');
    }

    /**
     * Pesan yang diterima oleh user ini.
     */
    public function receivedMessages()
    {
        return $this->hasMany(ChatMessage::class, 'receiver_id');
    }
}
