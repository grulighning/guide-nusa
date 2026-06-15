@extends('layouts.app')
@section('title', 'Destinasi Saya')

@section('content')
<div style="max-width:960px;margin:40px auto;padding:0 16px">

    {{-- Header --}}
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:32px;flex-wrap:wrap;gap:12px">
        <div>
            <h1 style="font-size:22px;font-weight:800;margin:0">Destinasi Saya</h1>
            <p style="font-size:13px;color:#888;margin:4px 0 0">{{ $destinasis->count() }} destinasi</p>
        </div>
        <a href="{{ route('pemandu.destinasi.create') }}" style="background:#0d5c45;color:#fff;padding:10px 20px;border-radius:10px;font-weight:700;font-size:13px;text-decoration:none;border:3px solid #222;box-shadow:3px 3px 0 #222;display:inline-flex;align-items:center;gap:6px">
            <i class="fa-solid fa-plus"></i> Tambah Destinasi
        </a>
    </div>

    {{-- Grid --}}
    @if($destinasis->count())
    <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:20px">
        @foreach($destinasis as $dest)
        <div style="background:#fff;border:3px solid #222;border-radius:16px;box-shadow:5px 5px 0 #222;overflow:hidden">
            {{-- Foto --}}
            <div class="dest-owner-img" style="height:180px;background:{{ $dest->warna_bg }};display:flex;align-items:center;justify-content:center;font-size:48px;position:relative;overflow:hidden">
                @if($dest->thumbnail)
                <img src="{{ $dest->thumbnail }}" alt="{{ $dest->nama }}">
                @else
                <span class="dest-emoji-overlay" style="text-shadow:0 2px 8px rgba(0,0,0,0.3)">{{ $dest->emoji }}</span>
                @endif

                {{-- Badge jumlah foto --}}
                @if(!empty($dest->fotos) && count($dest->fotos) > 1)
                <div style="position:absolute;bottom:8px;right:8px;background:rgba(0,0,0,0.6);color:#fff;font-size:11px;padding:3px 8px;border-radius:6px;font-weight:600">
                    <i class="fa-solid fa-images" style="margin-right:4px"></i>{{ count($dest->fotos) }}
                </div>
                @endif
            </div>

            {{-- Body --}}
            <div style="padding:16px">
                <div style="font-size:16px;font-weight:800;margin-bottom:4px">{{ $dest->nama }}</div>
                <div style="font-size:12px;color:#888;margin-bottom:8px">
                    <i class="fa-solid fa-location-dot" style="margin-right:4px"></i>{{ $dest->lokasi }}
                    @if($dest->kabupaten) · {{ $dest->kabupaten }} @endif
                </div>
                <div style="display:flex;gap:6px;margin-bottom:12px;flex-wrap:wrap">
                    <span style="font-size:11px;padding:3px 10px;background:#e8f5e9;border:2px solid #2e7d32;border-radius:20px;font-weight:600;color:#2e7d32">{{ $dest->kategori }}</span>
                    <span style="font-size:11px;padding:3px 10px;background:#fff3e0;border:2px solid #e65100;border-radius:20px;font-weight:600;color:#e65100">{{ $dest->jumlah_pemandu_aktif }} pemandu aktif</span>
                </div>

                {{-- Action buttons --}}
                <div style="display:flex;gap:8px;margin-top:12px">
                    <a href="{{ route('pemandu.destinasi.edit', $dest) }}"
                       style="flex:1;text-align:center;padding:8px 0;border:2px solid #222;border-radius:8px;font-size:12px;font-weight:700;color:#222;text-decoration:none;background:#f5f5f5">
                        <i class="fa-solid fa-pen"></i> Edit
                    </a>
                    <form method="POST" action="{{ route('pemandu.destinasi.destroy', $dest) }}"
                          onsubmit="return confirm('Yakin hapus destinasi {{ $dest->nama }}?')"
                          style="flex:1">
                        @csrf @method('DELETE')
                        <button type="submit"
                                style="width:100%;padding:8px 0;border:2px solid #c62828;border-radius:8px;font-size:12px;font-weight:700;color:#c62828;background:#fff;cursor:pointer">
                            <i class="fa-solid fa-trash"></i> Hapus
                        </button>
                    </form>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @else
    <div style="text-align:center;padding:64px 16px;background:#fff;border:3px solid #222;border-radius:16px;box-shadow:5px 5px 0 #222">
        <div style="font-size:64px;margin-bottom:16px">🗺️</div>
        <h2 style="font-size:18px;font-weight:800;margin:0 0 8px">Belum Ada Destinasi</h2>
        <p style="font-size:13px;color:#888;margin:0 0 20px">Tambahkan destinasi wisata pertama Anda sekarang.</p>
        <a href="{{ route('pemandu.destinasi.create') }}" style="background:#0d5c45;color:#fff;padding:12px 24px;border-radius:10px;font-weight:700;font-size:14px;text-decoration:none;border:3px solid #222;box-shadow:4px 4px 0 #222;display:inline-flex;align-items:center;gap:8px">
            <i class="fa-solid fa-plus"></i> Tambah Destinasi
        </a>
    </div>
    @endif

    {{-- Link ke dashboard --}}
    <div style="text-align:center;margin-top:24px">
        <a href="{{ route('pemandu.dashboard') }}" style="font-size:13px;color:#0d5c45;font-weight:600;text-decoration:none">
            <i class="fa-solid fa-arrow-left" style="margin-right:4px"></i> Kembali ke Dashboard
        </a>
    </div>
</div>
@endsection
