<?php
// ============================================================
// FILE: app/Http/Controllers/Auth/RegisteredUserController.php
// GANTI file ini dari bawaan Breeze untuk handle role & phone
// ============================================================

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Pemandu;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    public function create(): View
    {
        return view('auth.register');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'first_name' => ['required', 'string', 'max:100'],
            'last_name'  => ['nullable', 'string', 'max:100'],
            'email'      => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'phone'      => ['nullable', 'string', 'max:20'],
            'password'   => ['required', 'confirmed', Rules\Password::defaults()],
            'role'       => ['required', 'in:pemandu,wisatawan'],
        ]);

        $name = trim($request->first_name . ' ' . $request->last_name);

        $user = User::create([
            'name'     => $name,
            'email'    => $request->email,
            'phone'    => $request->phone,
            'role'     => $request->role,
            'password' => Hash::make($request->password),
        ]);

        // Kalau daftar sebagai pemandu, buat profil pemandu otomatis
        if ($user->role === 'pemandu') {
            $inisial = strtoupper(
                substr($request->first_name, 0, 1) .
                substr($request->last_name ?? $request->first_name, 0, 1)
            );
            $warna = ['#0d5c45', '#1a5276', '#1e8449', '#7d3c98', '#b7950b', '#1f618d'];

            Pemandu::create([
                'user_id'          => $user->id,
                'spesialisasi'     => 'Alam',
                'pengalaman_tahun' => 0,
                'rating'           => 0,
                'jumlah_tour'      => 0,
                'warna_avatar'     => $warna[array_rand($warna)],
                'inisial'          => $inisial,
            ]);
        }

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('dashboard', absolute: false));
    }
}
