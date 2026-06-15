{{--
    FILE: resources/views/auth/reset-password.blade.php
    Disamakan dengan style halaman login/forgot password
--}}
@extends('layouts.app')
@section('title', 'Reset Password')

@section('content')
<div class="auth-page">
    <div class="auth-card">
        <div class="auth-logo">
            <div class="auth-logo-text">GUIDE NUSA</div>
        </div>

        <div class="auth-title">Reset Password</div>
        <div class="auth-subtitle">Buat password baru untuk akun Anda</div>

        <x-auth-session-status class="auth-session-status" :status="session('status')" />

        <form method="POST" action="{{ route('password.store') }}">
            @csrf

            <input type="hidden" name="token" value="{{ $request->route('token') }}">

            <div class="auth-form-group">
                <label class="auth-label" for="email">Email</label>
                <input
                    id="email"
                    class="auth-input"
                    type="email"
                    name="email"
                    value="{{ old('email', $request->email) }}"
                    placeholder="bima@email.com"
                    required
                    autofocus
                    autocomplete="username"
                >
                @error('email')
                    <span class="auth-error">{{ $message }}</span>
                @enderror
            </div>

            <div class="auth-form-group">
                <label class="auth-label" for="password">Password Baru</label>
                <input
                    id="password"
                    class="auth-input"
                    type="password"
                    name="password"
                    placeholder="Masukkan password baru"
                    required
                    autocomplete="new-password"
                >
                @error('password')
                    <span class="auth-error">{{ $message }}</span>
                @enderror
            </div>

            <div class="auth-form-group">
                <label class="auth-label" for="password_confirmation">Konfirmasi Password</label>
                <input
                    id="password_confirmation"
                    class="auth-input"
                    type="password"
                    name="password_confirmation"
                    placeholder="Ulangi password baru"
                    required
                    autocomplete="new-password"
                >
                @error('password_confirmation')
                    <span class="auth-error">{{ $message }}</span>
                @enderror
            </div>

            <button type="submit" class="btn-auth">Reset Password</button>
        </form>

        <div class="auth-footer">
            Kembali ke halaman
            <a href="{{ route('login') }}" style="color:#0d5c45;font-weight:700">login</a>
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
.auth-session-status { margin-bottom:14px; font-size:13px; font-weight:600; color:#0d5c45; background:#e8f5f0; border-left:4px solid #0d5c45; padding:10px 12px; border-radius:8px; }
.auth-form-group { display:flex; flex-direction:column; gap:5px; margin-bottom:14px; }
.auth-label { font-size:12px; font-weight:700; color:#444; text-transform:uppercase; letter-spacing:0.4px; }
.auth-input { border:1.5px solid #ddd; border-radius:8px; padding:10px 12px; font-size:14px; font-family:inherit; outline:none; transition:border-color 0.15s; background:#fff; }
.auth-input:focus { border-color:#0d5c45; }
.auth-error { font-size:12px; color:#c0392b; }
.btn-auth { width:100%; background:#0d5c45; color:#fff; border:none; padding:12px; border-radius:8px; font-size:14px; font-weight:700; cursor:pointer; font-family:inherit; margin-top:8px; margin-bottom:16px; transition:all 0.15s; }
.btn-auth:hover { background:#0a4a38; }
.auth-footer { text-align:center; font-size:13px; color:#888; }
@media (max-width: 480px) {
    .auth-page { padding:20px; }
    .auth-card { padding:28px 20px; border-radius:14px; }
    .auth-title { font-size:20px; }
}
</style>
@endpush
@endsection
