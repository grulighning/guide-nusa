{{--
    FILE: resources/views/auth/register.blade.php
    GANTI file register bawaan Breeze dengan ini
    untuk menambahkan role selector (Pemandu / Wisatawan)
--}}
@extends('layouts.app')
@section('title', 'Daftar Akun')

@section('content')
<div class="auth-page">
    <div class="auth-card">
        <div class="auth-logo"><div class="auth-logo-text">GUIDE NUSA</div></div>
        <div class="auth-title">Buat Akun Baru</div>
        <div class="auth-subtitle">Bergabung dan mulai kelola perjalanan wisatamu</div>

        <form method="POST" action="{{ route('register') }}">
            @csrf

            {{-- Role Selector --}}
            <div style="margin-bottom:14px">
                <div style="font-size:12px;font-weight:700;color:#444;text-transform:uppercase;letter-spacing:0.4px;margin-bottom:8px">
                    Daftar Sebagai
                </div>
                <div class="role-select">
                    <label class="role-option {{ old('role', 'pemandu') === 'pemandu' ? 'selected' : '' }}">
                        <input type="radio" name="role" value="pemandu"
                            {{ old('role', 'pemandu') === 'pemandu' ? 'checked' : '' }}
                            style="display:none"
                            onchange="document.querySelectorAll('.role-option').forEach(el=>el.classList.remove('selected'));this.closest('.role-option').classList.add('selected')">
                        <div class="role-option-icon"><i class="fa-solid fa-compass"></i></div>
                        <div class="role-option-label">Pemandu</div>
                        <div class="role-option-sub">Tawarkan jasa tur</div>
                    </label>
                    <label class="role-option {{ old('role') === 'wisatawan' ? 'selected' : '' }}">
                        <input type="radio" name="role" value="wisatawan"
                            {{ old('role') === 'wisatawan' ? 'checked' : '' }}
                            style="display:none"
                            onchange="document.querySelectorAll('.role-option').forEach(el=>el.classList.remove('selected'));this.closest('.role-option').classList.add('selected')">
                        <div class="role-option-icon"><i class="fa-solid fa-suitcase"></i></div>
                        <div class="role-option-label">Wisatawan</div>
                        <div class="role-option-sub">Cari paket wisata</div>
                    </label>
                </div>
                @error('role')
                <span style="font-size:12px;color:#c0392b">{{ $message }}</span>
                @enderror
            </div>

            {{-- Nama --}}
            <div class="form-row" style="margin-bottom:14px">
                <div class="auth-form-group" style="margin-bottom:0">
                    <label class="auth-label">Nama Depan</label>
                    <input class="auth-input" type="text" name="first_name"
                        value="{{ old('first_name') }}" placeholder="Bima" required>
                    @error('first_name')
                    <span style="font-size:12px;color:#c0392b">{{ $message }}</span>
                    @enderror
                </div>
                <div class="auth-form-group" style="margin-bottom:0">
                    <label class="auth-label">Nama Belakang</label>
                    <input class="auth-input" type="text" name="last_name"
                        value="{{ old('last_name') }}" placeholder="Wijaya">
                </div>
            </div>

            {{-- Email --}}
            <div class="auth-form-group">
                <label class="auth-label">Email</label>
                <input class="auth-input" type="email" name="email"
                    value="{{ old('email') }}" placeholder="bima@email.com" required>
                @error('email')
                <span style="font-size:12px;color:#c0392b">{{ $message }}</span>
                @enderror
            </div>

            {{-- Phone --}}
            <div class="auth-form-group">
                <label class="auth-label">No. HP / WhatsApp</label>
                <input class="auth-input" type="tel" name="phone"
                    value="{{ old('phone') }}" placeholder="+62 812-xxxx-xxxx">
            </div>

            {{-- Password --}}
            <div class="auth-form-group">
                <label class="auth-label">Password</label>
                <input class="auth-input" type="password" name="password"
                    placeholder="Min. 8 karakter" required>
                @error('password')
                <span style="font-size:12px;color:#c0392b">{{ $message }}</span>
                @enderror
            </div>
            <div class="auth-form-group">
                <label class="auth-label">Konfirmasi Password</label>
                <input class="auth-input" type="password" name="password_confirmation"
                    placeholder="Ulangi password" required>
            </div>

            <button type="submit" class="btn-auth">Buat Akun</button>
        </form>

        <div class="auth-footer">
            Sudah punya akun?
            <a href="{{ route('login') }}" style="color:#0d5c45;font-weight:700">Masuk di sini</a>
        </div>
    </div>
</div>

@push('styles')
<style>
.auth-page { display:flex; align-items:center; justify-content:center; min-height:calc(100vh - 60px); background:#f0f2f1; padding:32px; }
.auth-card { background:#fff; border-radius:16px; padding:36px 32px; width:100%; max-width:440px; box-shadow:0 4px 24px rgba(0,0,0,0.08); }
.auth-logo { text-align:center; margin-bottom:20px; }
.auth-logo-text { display:inline-block; background:#0d5c45; color:#fff; font-size:16px; font-weight:800; padding:8px 20px; border-radius:8px; letter-spacing:1px; }
.auth-title { font-size:22px; font-weight:800; color:#1a1a1a; margin-bottom:6px; text-align:center; }
.auth-subtitle { font-size:13px; color:#888; text-align:center; margin-bottom:20px; }
.auth-form-group { display:flex; flex-direction:column; gap:5px; margin-bottom:14px; }
.auth-label { font-size:12px; font-weight:700; color:#444; text-transform:uppercase; letter-spacing:0.4px; }
.auth-input { border:1.5px solid #ddd; border-radius:8px; padding:10px 12px; font-size:14px; font-family:inherit; outline:none; transition:border-color 0.15s; }
.auth-input:focus { border-color:#0d5c45; }
.btn-auth { width:100%; background:#0d5c45; color:#fff; border:none; padding:12px; border-radius:8px; font-size:14px; font-weight:700; cursor:pointer; font-family:inherit; margin-top:8px; margin-bottom:16px; transition:all 0.15s; }
.btn-auth:hover { background:#0a4a38; }
.auth-footer { text-align:center; font-size:13px; color:#888; }
.role-select { display:grid; grid-template-columns:1fr 1fr; gap:8px; }
.role-option { border:1.5px solid #ddd; border-radius:10px; padding:12px; text-align:center; cursor:pointer; transition:all 0.15s; display:block; }
.role-option.selected { border-color:#0d5c45; background:#e8f5f0; }
.role-option-icon { font-size:24px; margin-bottom:4px; }
.role-option-label { font-size:14px; font-weight:700; color:#1a1a1a; }
.role-option-sub { font-size:11px; color:#888; }
.form-row { display:grid; grid-template-columns:1fr 1fr; gap:12px; }
</style>
@endpush

@endsection
