@extends('layouts.app')
@section('title', 'Paket Tour')

@section('content')
<div class="page-content">

    {{-- Stats Bar --}}
    <div class="stats-bar">
        <div class="stat-card">
            <div class="stat-card-val">{{ $stats['paket_aktif'] }}</div>
            <div class="stat-card-lbl">Paket Aktif</div>
        </div>
        <div class="stat-card">
            <div class="stat-card-val">{{ $stats['tour_selesai'] }}</div>
            <div class="stat-card-lbl">Tour Selesai</div>
        </div>
        <div class="stat-card">
            <div class="stat-card-val">Rp{{ number_format($stats['harga_mulai'] / 1000, 0) }}k</div>
            <div class="stat-card-lbl">Mulai Dari</div>
        </div>
        <div class="stat-card">
            <div class="stat-card-val">{{ $stats['kepuasan'] }}%</div>
            <div class="stat-card-lbl">Kepuasan</div>
        </div>
    </div>

    {{-- Title + CTA --}}
    <div class="page-title-row">
        <div class="page-title">Paket Tour</div>
        @auth
            @if(auth()->user()->isPemandu())
            <a href="{{ route('paket.create') }}" class="btn-primary">+ Buat Paket Tour</a>
            @endif
        @endauth
    </div>

    {{-- Filter --}}
    <form method="GET" action="{{ route('paket.index') }}">
        <div class="filter-bar">
            @foreach($durasiList as $d)
            <button type="submit" name="durasi" value="{{ strtolower($d) }}"
                class="filter-chip {{ strtolower($durasi) === strtolower($d) || ($durasi === 'semua' && $d === 'Semua') ? 'active' : '' }}">
                {{ $d }}
            </button>
            @endforeach

            <div class="search-box">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#888" stroke-width="2">
                    <circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/>
                </svg>
                <input type="text" name="cari" placeholder="Cari paket..."
                    value="{{ $keyword }}" onchange="this.form.submit()">
            </div>
        </div>
    </form>

    {{-- Grid --}}
    <div class="paket-grid">
        @forelse($pakets as $paket)
        <div class="paket-card {{ $paket->is_featured ? 'featured' : '' }}">
            @if($paket->badge)
            <div class="paket-badge">{{ $paket->badge }}</div>
            @endif

            <div class="paket-header">
                <div class="paket-title">{{ $paket->nama }}</div>
                <div>
                    <div class="paket-price">{{ $paket->harga_format }}</div>
                    <div class="paket-price-sub">per orang</div>
                </div>
            </div>

            <div class="paket-desc">{{ $paket->deskripsi }}</div>

            <div class="paket-info-row">
                <div class="paket-info-item"><i class="fa-solid fa-clock" style="margin-right:4px"></i>{{ $paket->jam_mulai }}–{{ $paket->jam_selesai }}</div>
                <div class="paket-info-item"><i class="fa-solid fa-users" style="margin-right:4px"></i>Maks {{ $paket->max_peserta }} orang</div>
                <div class="paket-info-item"><i class="fa-solid fa-calendar-days" style="margin-right:4px"></i>{{ $paket->durasi }}</div>
            </div>

            @if($paket->destinasis->count())
            <div class="paket-stops-title">Destinasi</div>
            <div class="stop-chips">
                @foreach($paket->destinasis as $d)
                <span class="stop-chip">{{ $d->nama }}</span>
                @endforeach
            </div>
            @endif

            @auth
                @if(auth()->user()->isWisatawan())
                <form method="POST" action="{{ route('paket.booking', $paket) }}">
                    @csrf
                    <button type="submit" class="btn-book">Booking Sekarang</button>
                </form>
                @endif
            @else
            <a href="{{ route('login') }}" class="btn-book" style="display:block;text-align:center;text-decoration:none">
                Masuk untuk Booking
            </a>
            @endauth

            @auth
                @if(auth()->user()->id === $paket->user_id)
                <div style="display:flex;gap:8px;margin-top:8px">
                    <a href="{{ route('paket.edit', $paket) }}" class="btn-detail" style="text-align:center;text-decoration:none">Edit</a>
                    <form method="POST" action="{{ route('paket.destroy', $paket) }}" onsubmit="return confirm('Hapus paket ini?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn-detail" style="color:#e74c3c;border-color:#e74c3c">Hapus</button>
                    </form>
                </div>
                @endif
            @endauth
        </div>
        @empty
        <div style="grid-column:1/-1;text-align:center;padding:48px 0;color:#888">
            <div style="font-size:48px;margin-bottom:12px"><i class="fa-solid fa-map"></i></div>
            <div style="font-size:15px">Belum ada paket tour tersedia.</div>
        </div>
        @endforelse
    </div>

</div>
@endsection
