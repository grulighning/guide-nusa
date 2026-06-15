@extends('layouts.app')
@section('title', 'Destinasi Wisata')

@section('content')
<div class="page-content">

    {{-- Stats Bar --}}
    <div class="stats-bar">
        <div class="stat-card">
            <div class="stat-card-val">{{ $totalDestiasi }}</div>
            <div class="stat-card-lbl">Total Destinasi</div>
        </div>
        <div class="stat-card">
            <div class="stat-card-val">{{ $totalKabupaten }}</div>
            <div class="stat-card-lbl">Kabupaten</div>
        </div>
        <div class="stat-card">
            <div class="stat-card-val">{{ $totalPemandu }}</div>
            <div class="stat-card-lbl">Pemandu Aktif</div>
        </div>
        <div class="stat-card">
            <div class="stat-card-val">{{ $ratingRataRata }}★</div>
            <div class="stat-card-lbl">Rating Rata-rata</div>
        </div>
    </div>

    {{-- Title + Filter --}}
    <div class="page-title-row">
        <div class="page-title">Destinasi Wisata</div>
        @auth
            @if(auth()->user()->isPemandu())
            <a href="{{ route('pemandu.destinasi.create') }}"
               style="background:#0d5c45;color:#fff;padding:10px 18px;border-radius:10px;font-weight:800;font-size:13px;text-decoration:none;border:2px solid #0a4635;display:inline-flex;align-items:center;gap:7px">
                <i class="fa-solid fa-plus"></i> Tambah Destinasi
            </a>
            @endif
        @endauth
    </div>

    <form method="GET" action="{{ route('destinasi.index') }}">
        <div class="filter-bar">
            @foreach($kategoriList as $k)
            <button type="submit" name="kategori" value="{{ strtolower($k) }}"
                class="filter-chip {{ strtolower($kategori) === strtolower($k) || ($kategori === 'semua' && $k === 'Semua') ? 'active' : '' }}">
                {{ $k }}
            </button>
            @endforeach

            <div class="search-box">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#888" stroke-width="2">
                    <circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/>
                </svg>
                <input type="text" name="cari" placeholder="Cari destinasi..."
                    value="{{ $keyword }}" onchange="this.form.submit()">
            </div>
        </div>
    </form>

    {{-- Grid --}}
    <div class="dest-grid-full">
        @forelse($destinasis as $dest)
        @php
            $jumlahPemanduAktif = $dest->jumlah_pemandu_aktif;
            $bolehKelolaDestinasi = auth()->check()
                && auth()->user()->isPemandu()
                && optional(auth()->user()->pemandu)->id === $dest->pemandu_id;
        @endphp
        <div class="dest-card-full" style="position:relative;overflow:hidden">
        <a href="{{ route('destinasi.show', $dest) }}" style="text-decoration:none;color:inherit;display:block">
            <div class="dest-img-full" style="background:{{ $dest->warna_bg }}">
                @if($dest->thumbnail)
                <img src="{{ $dest->thumbnail }}" alt="{{ $dest->nama }}">
                @else
                <span class="dest-emoji-overlay" style="text-shadow:0 2px 8px rgba(0,0,0,0.4)">{{ $dest->emoji }}</span>
                @endif
            </div>
            <div class="dest-body-full">
                <div class="dest-name-full">{{ $dest->nama }}</div>
                <div class="dest-loc"><i class="fa-solid fa-location-dot" style="margin-right:4px"></i>{{ $dest->lokasi }}</div>
                <div class="dest-tags">
                    <span class="tag">{{ $dest->kategori }}</span>
                </div>
                <div class="dest-meta-full">
                    <span style="font-size:11px;color:#555">
                        <i class="fa-solid fa-user" style="margin-right:2px"></i>
                        {{ $jumlahPemanduAktif }}+ pemandu aktif
                    </span>
                    <span class="rating"><span class="star">★</span>{{ number_format($dest->rating, 1) }}</span>
                </div>
            </div>
        </a>
            @if($bolehKelolaDestinasi)
            <div style="display:flex;gap:8px;padding:0 16px 16px">
                <a href="{{ route('pemandu.destinasi.edit', $dest) }}"
                   style="flex:1;text-align:center;padding:8px 0;border:2px solid #222;border-radius:8px;font-size:12px;font-weight:700;color:#222;text-decoration:none;background:#f5f5f5">
                    <i class="fa-solid fa-pen"></i> Edit
                </a>
                <form method="POST" action="{{ route('pemandu.destinasi.destroy', $dest) }}"
                      onsubmit="return confirm('Yakin hapus destinasi {{ $dest->nama }}?')"
                      style="flex:1;margin:0">
                    @csrf @method('DELETE')
                    <button type="submit"
                            style="width:100%;padding:8px 0;border:2px solid #c62828;border-radius:8px;font-size:12px;font-weight:700;color:#c62828;background:#fff;cursor:pointer">
                        <i class="fa-solid fa-trash"></i> Hapus
                    </button>
                </form>
            </div>
            @endif
        </div>
        @empty
        <div style="grid-column:1/-1;text-align:center;padding:48px 0;color:#888">
            <div style="font-size:48px;margin-bottom:12px"><i class="fa-solid fa-mountain"></i></div>
            <div style="font-size:15px">Destinasi tidak ditemukan.</div>
        </div>
        @endforelse
    </div>

</div>
@endsection
