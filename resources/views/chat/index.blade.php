@extends('layouts.app')
@section('title', 'Pesan')

@section('content')
<div style="max-width:800px;margin:40px auto;padding:0 16px">

    <div style="margin-bottom:24px">
        <h1 style="font-size:22px;font-weight:800;margin:0;display:flex;align-items:center;gap:10px">
            <i class="fa-solid fa-comment-dots" style="color:#0d5c45"></i>
            Pesan
        </h1>
        <p style="font-size:13px;color:#888;margin:6px 0 0">
            Komunikasikan detail perjalanan dengan pemandu atau wisatawan
        </p>
    </div>

    {{-- Flash Messages --}}
    @if(session('success'))
    <div style="background:#e8f5e9;border:2px solid #2e7d32;border-radius:10px;padding:12px 16px;margin-bottom:16px;font-size:13px;font-weight:600;color:#2e7d32;display:flex;align-items:center;gap:8px">
        <i class="fa-solid fa-check-circle"></i> {{ session('success') }}
    </div>
    @endif
    @if(session('error'))
    <div style="background:#fbe9e7;border:2px solid #c62828;border-radius:10px;padding:12px 16px;margin-bottom:16px;font-size:13px;font-weight:600;color:#c62828;display:flex;align-items:center;gap:8px">
        <i class="fa-solid fa-xmark-circle"></i> {{ session('error') }}
    </div>
    @endif

    {{-- Daftar Percakapan --}}
    <div style="background:#fff;border:3px solid #222;border-radius:16px;box-shadow:6px 6px 0 #222;overflow:hidden">

        @forelse($conversations as $conv)
        @php
            $otherUser = $conv['user'];
            $lastMsg   = $conv['last_message'];
            $unread    = $conv['unread_count'];
        @endphp
        <a href="{{ route('chat.show', $otherUser) }}" style="text-decoration:none;color:inherit;display:block;transition:background .15s"
           onmouseover="this.style.background='#f9f9f9'"
           onmouseout="this.style.background='transparent'">
            <div style="display:flex;gap:14px;align-items:center;padding:16px 20px;{{ !$loop->first ? 'border-top:2px solid #eee' : '' }}">
                {{-- Avatar --}}
                @php
                    $role = $otherUser->role === 'pemandu' ? 'P' : 'W';
                    $bgColor = $otherUser->role === 'pemandu' ? ($otherUser->pemandu?->warna_avatar ?? '#0d5c45') : '#1565c0';
                    $initials = $otherUser->role === 'pemandu' && $otherUser->pemandu
                        ? $otherUser->pemandu->inisial
                        : strtoupper(substr($otherUser->name, 0, 2));
                @endphp
                <div style="width:48px;height:48px;border-radius:14px;background:{{ $bgColor }};display:flex;align-items:center;justify-content:center;font-size:16px;font-weight:800;color:#fff;flex-shrink:0;position:relative">
                    {{ $initials }}
                    @if($unread > 0)
                    <div style="position:absolute;top:-4px;right:-4px;width:22px;height:22px;border-radius:50%;background:#c62828;color:#fff;font-size:10px;font-weight:800;display:flex;align-items:center;justify-content:center;border:2px solid #fff">
                        {{ $unread > 9 ? '9+' : $unread }}
                    </div>
                    @endif
                </div>

                {{-- Info --}}
                <div style="flex:1;min-width:0">
                    <div style="display:flex;align-items:center;justify-content:space-between;gap:8px">
                        <div style="font-size:14px;font-weight:700;color:#1a1a1a;display:flex;align-items:center;gap:6px">
                            {{ $otherUser->name }}
                            <span style="font-size:10px;font-weight:600;padding:2px 8px;border-radius:20px;background:{{ $otherUser->role === 'pemandu' ? '#e8f5e9' : '#e3f2fd' }};color:{{ $otherUser->role === 'pemandu' ? '#0d5c45' : '#1565c0' }};border:1px solid currentColor">
                                {{ $otherUser->role === 'pemandu' ? 'Pemandu' : 'Wisatawan' }}
                            </span>
                        </div>
                        <div style="font-size:11px;color:#aaa;white-space:nowrap">
                            {{ $lastMsg->created_at->diffForHumans() }}
                        </div>
                    </div>
                    <div style="font-size:13px;color:#888;margin-top:4px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;max-width:100%;{{ $unread > 0 ? 'font-weight:700;color:#1a1a1a' : '' }}">
                        @if($lastMsg->sender_id === auth()->id())
                            <span style="color:#888">Anda: </span>
                        @endif
                        {{ Str::limit($lastMsg->message, 80) }}
                    </div>
                </div>
            </div>
        </a>
        @empty
        <div style="text-align:center;padding:60px 20px">
            <div style="font-size:56px;margin-bottom:16px;opacity:0.6">
                <i class="fa-solid fa-comment-dots" style="color:#0d5c45"></i>
            </div>
            <h2 style="font-size:18px;font-weight:800;margin:0 0 8px;color:#333">Belum ada percakapan</h2>
            <p style="font-size:13px;color:#888;margin:0;max-width:360px;margin:0 auto">
                @auth
                    @if(auth()->user()->isWisatawan())
                        Kunjungi halaman pemandu dan klik tombol "Hubungi" untuk memulai percakapan.
                    @else
                        Pesan dari wisatawan akan muncul di sini setelah mereka menghubungi Anda.
                    @endif
                @endauth
            </p>
            <div style="margin-top:20px;display:flex;gap:10px;justify-content:center;flex-wrap:wrap">
                <a href="{{ route('pemandu.index') }}" style="padding:10px 20px;background:#0d5c45;color:#fff;border:2px solid #222;border-radius:10px;font-size:13px;font-weight:700;text-decoration:none;display:inline-flex;align-items:center;gap:6px;box-shadow:2px 2px 0 #222">
                    <i class="fa-solid fa-compass"></i> Cari Pemandu
                </a>
                @if(auth()->user()->isPemandu())
                <a href="{{ route('pemandu.dashboard') }}" style="padding:10px 20px;background:#1565c0;color:#fff;border:2px solid #222;border-radius:10px;font-size:13px;font-weight:700;text-decoration:none;display:inline-flex;align-items:center;gap:6px;box-shadow:2px 2px 0 #222">
                    <i class="fa-solid fa-gauge-high"></i> Dashboard
                </a>
                @endif
            </div>
        </div>
        @endforelse
    </div>
</div>
@endsection
