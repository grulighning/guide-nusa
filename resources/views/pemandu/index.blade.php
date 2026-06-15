@extends('layouts.app')
@section('title', 'Daftar Pemandu')

@section('content')
<div class="page-content">

    <div class="page-title-row">
        <div class="page-title">Pemandu Wisata</div>
    </div>

    {{-- Search --}}
    <form method="GET" action="{{ route('pemandu.index') }}">
        <div class="filter-bar">
            @foreach(['Semua', 'Alam', 'Budaya', 'Fotografi', 'Kuliner', 'Sejarah'] as $s)
            <button type="submit" name="spesialisasi" value="{{ strtolower($s) }}"
                class="filter-chip {{ $spesialisasi === strtolower($s) || ($spesialisasi === 'semua' && $s === 'Semua') ? 'active' : '' }}">
                {{ $s }}
            </button>
            @endforeach

            <div class="search-box">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#888" stroke-width="2">
                    <circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/>
                </svg>
                <input type="text" name="cari" placeholder="Cari pemandu..."
                    value="{{ $keyword }}" onchange="this.form.submit()">
            </div>
        </div>
    </form>

    {{-- List --}}
    <div class="pemandu-list-full">
        @forelse($pemandus as $pemandu)
        <a href="{{ route('pemandu.show', $pemandu) }}" class="pemandu-card-full" style="text-decoration:none;color:inherit">
            <div class="pmnd-avatar" style="background:{{ $pemandu->warna_avatar }}">
                {{ $pemandu->inisial }}
            </div>
            <div class="pmnd-info">
                <div class="pmnd-name">{{ $pemandu->user->name }}</div>
                <div class="pmnd-spec">{{ $pemandu->spesialisasi }}</div>
                <div class="pmnd-chips">
                    @foreach(explode(',', $pemandu->spesialisasi) as $chip)
                    <span class="pmnd-chip">{{ trim($chip) }}</span>
                    @endforeach
                </div>
            </div>
            <div class="pmnd-right">
                <div class="pmnd-rating">
                    <span class="star">★</span>{{ number_format($pemandu->rating, 1) }}
                </div>
                <div class="pmnd-tours">{{ $pemandu->jumlah_tour }} tour selesai</div>
                <div class="day-row">
                    @php
                        $hariList = ['S','S','R','K','J','S','M'];
                        $availIndices = is_array($pemandu->ketersediaan) ? $pemandu->ketersediaan : [];
                    @endphp
                    @foreach($hariList as $i => $day)
                    @php $avail = in_array($i, $availIndices); @endphp
                    <div class="day-dot {{ $avail ? 'avail' : 'busy' }}"
                         title="{{ $avail ? 'Tersedia' : 'Tidak tersedia' }}">
                        {{ $day }}
                    </div>
                    @endforeach
                </div>
            </div>
        </a>
        @empty
        <div style="text-align:center;padding:48px 0;color:#888">
            <div style="font-size:48px;margin-bottom:12px"><i class="fa-solid fa-compass"></i></div>
            <div style="font-size:15px">Pemandu tidak ditemukan.</div>
        </div>
        @endforelse
    </div>

</div>
@endsection
