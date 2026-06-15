@extends('layouts.app')
@section('title', 'Buat Paket Tour')

@section('content')
<div class="page-content">
    <div class="page-title-row" style="margin-bottom:20px">
        <div class="page-title">Buat Paket Tour</div>
        <a href="{{ route('paket.index') }}" class="btn-secondary">← Kembali</a>
    </div>

    @if($errors->any())
    <div class="flash-error" style="margin-bottom:16px">
        <strong>Ada kesalahan:</strong>
        <ul style="margin:8px 0 0 16px">
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form method="POST" action="{{ route('paket.store') }}">
        @csrf

        {{-- Info Dasar --}}
        <div class="form-card">
            <div class="form-section-title"><i class="fa-solid fa-clipboard-list" style="margin-right:6px"></i>Informasi Paket</div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Nama Paket</label>
                    <input class="form-input" type="text" name="nama" value="{{ old('nama') }}"
                        placeholder="Contoh: Jejak Budaya Minang" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Durasi</label>
                    <select class="form-select" name="durasi" required>
                        <option value="">Pilih Durasi</option>
                        <option value="1 Hari" {{ old('durasi') === '1 Hari' ? 'selected' : '' }}>1 Hari</option>
                        <option value="2 Hari" {{ old('durasi') === '2 Hari' ? 'selected' : '' }}>2 Hari</option>
                        <option value="Weekend" {{ old('durasi') === 'Weekend' ? 'selected' : '' }}>Weekend (2D1N)</option>
                        <option value="3 Hari" {{ old('durasi') === '3 Hari' ? 'selected' : '' }}>3 Hari</option>
                    </select>
                </div>
            </div>
            <div class="form-row-full">
                <div class="form-group">
                    <label class="form-label">Deskripsi</label>
                    <textarea class="form-textarea" name="deskripsi"
                        placeholder="Ceritakan detail paket wisata ini...">{{ old('deskripsi') }}</textarea>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Harga (Rp)</label>
                    <input class="form-input" type="number" name="harga" value="{{ old('harga') }}"
                        placeholder="200000" min="0" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Maks. Peserta</label>
                    <input class="form-input" type="number" name="max_peserta" value="{{ old('max_peserta') }}"
                        placeholder="15" min="1" required>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Jam Mulai</label>
                    <input class="form-input" type="time" name="jam_mulai" value="{{ old('jam_mulai', '07:00') }}" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Jam Selesai</label>
                    <input class="form-input" type="time" name="jam_selesai" value="{{ old('jam_selesai', '18:00') }}" required>
                </div>
            </div>
        </div>

        {{-- Pilih Destinasi --}}
        <div class="form-card">
            <div class="form-section-title"><i class="fa-solid fa-location-dot" style="margin-right:6px"></i>Pilih Destinasi</div>
            <div class="dest-checkboxes">
                @foreach($destinasis as $dest)
                <label class="dest-checkbox {{ in_array($dest->id, old('destinasi_ids', [])) ? 'selected' : '' }}">
                    <input type="checkbox" name="destinasi_ids[]" value="{{ $dest->id }}"
                        {{ in_array($dest->id, old('destinasi_ids', [])) ? 'checked' : '' }}
                        onchange="this.closest('.dest-checkbox').classList.toggle('selected', this.checked)">
                    <span class="dest-checkbox-label">{{ $dest->emoji }} {{ $dest->nama }}</span>
                </label>
                @endforeach
            </div>
        </div>

        {{-- Pilih Pemandu --}}
        <div class="form-card">
            <div class="form-section-title"><i class="fa-solid fa-compass" style="margin-right:6px"></i>Pilih Pemandu</div>
            <div class="guide-select-grid">
                @foreach($pemandus as $p)
                <label class="guide-option {{ old('pemandu_id') == $p->id ? 'selected' : '' }}">
                    <input type="radio" name="pemandu_id" value="{{ $p->id }}"
                        {{ old('pemandu_id') == $p->id ? 'checked' : '' }}
                        onchange="document.querySelectorAll('.guide-option').forEach(el=>el.classList.remove('selected'));this.closest('.guide-option').classList.add('selected')"
                        style="display:none">
                    <div class="guide-option-avatar" style="background:{{ $p->warna_avatar }}">{{ $p->inisial }}</div>
                    <div class="guide-option-info">
                        <div class="guide-option-name">{{ $p->user->name }}</div>
                        <div class="guide-option-spec">{{ $p->spesialisasi }} · <span class="star">★</span>{{ number_format($p->rating, 1) }}</div>
                    </div>
                </label>
                @endforeach
            </div>
        </div>

        {{-- Jadwal --}}
        <div class="form-card">
            <div class="form-section-title"><i class="fa-solid fa-calendar-days" style="margin-right:6px"></i>Jadwal Keberangkatan</div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Tanggal Mulai</label>
                    <input class="form-input" type="date" name="tanggal_mulai"
                        value="{{ old('tanggal_mulai') }}" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Tanggal Berakhir</label>
                    <input class="form-input" type="date" name="tanggal_selesai"
                        value="{{ old('tanggal_selesai') }}" required>
                </div>
            </div>
            <div class="form-row-full">
                <div class="form-group">
                    <label class="form-label">Catatan Tambahan</label>
                    <textarea class="form-textarea" name="catatan"
                        placeholder="Informasi tambahan untuk peserta: perlengkapan yang dibawa, titik kumpul, dll...">{{ old('catatan') }}</textarea>
                </div>
            </div>
            <div class="form-actions">
                <a href="{{ route('paket.index') }}" class="btn-cancel">Batal</a>
                <button type="submit" class="btn-submit">✓ Simpan Paket Tour</button>
            </div>
        </div>

    </form>
</div>
@endsection
