@extends('layouts.app')
@section('title', 'Edit Paket Tour')

@section('content')
<div class="page-content">
    <div class="page-title-row" style="margin-bottom:20px">
        <div class="page-title">Edit Paket Tour</div>
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

    <form method="POST" action="{{ route('paket.update', $paket) }}">
        @csrf
        @method('PUT')

        <div class="form-card">
            <div class="form-section-title"><i class="fa-solid fa-clipboard-list" style="margin-right:6px"></i>Informasi Paket</div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Nama Paket</label>
                    <input class="form-input" type="text" name="nama" value="{{ old('nama', $paket->nama) }}" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Durasi</label>
                    <select class="form-select" name="durasi" required>
                        <option value="">Pilih Durasi</option>
                        @foreach(['1 Hari', '2 Hari', 'Weekend', '3 Hari', 'Grup'] as $durasi)
                        <option value="{{ $durasi }}" {{ old('durasi', $paket->durasi) === $durasi ? 'selected' : '' }}>
                            {{ $durasi }}
                        </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="form-row-full">
                <div class="form-group">
                    <label class="form-label">Deskripsi</label>
                    <textarea class="form-textarea" name="deskripsi" required>{{ old('deskripsi', $paket->deskripsi) }}</textarea>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Harga (Rp)</label>
                    <input class="form-input" type="number" name="harga" min="0" value="{{ old('harga', $paket->harga) }}" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Maks. Peserta</label>
                    <input class="form-input" type="number" name="max_peserta" min="1" value="{{ old('max_peserta', $paket->max_peserta) }}" required>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Jam Mulai</label>
                    <input class="form-input" type="time" name="jam_mulai" value="{{ old('jam_mulai', $paket->jam_mulai) }}" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Jam Selesai</label>
                    <input class="form-input" type="time" name="jam_selesai" value="{{ old('jam_selesai', $paket->jam_selesai) }}" required>
                </div>
            </div>
        </div>

        <div class="form-card">
            <div class="form-section-title"><i class="fa-solid fa-location-dot" style="margin-right:6px"></i>Pilih Destinasi</div>
            <div class="dest-checkboxes">
                @foreach($destinasis as $dest)
                <label class="dest-checkbox {{ in_array($dest->id, old('destinasi_ids', $selectedDestinasi ?? [])) ? 'selected' : '' }}">
                    <input type="checkbox" name="destinasi_ids[]" value="{{ $dest->id }}"
                        {{ in_array($dest->id, old('destinasi_ids', $selectedDestinasi ?? [])) ? 'checked' : '' }}
                        onchange="this.closest('.dest-checkbox').classList.toggle('selected', this.checked)">
                    <span class="dest-checkbox-label">{{ $dest->emoji }} {{ $dest->nama }}</span>
                </label>
                @endforeach
            </div>
        </div>

        <div class="form-card">
            <div class="form-section-title"><i class="fa-solid fa-compass" style="margin-right:6px"></i>Pilih Pemandu</div>
            <div class="guide-select-grid">
                @foreach($pemandus as $p)
                <label class="guide-option {{ old('pemandu_id', $paket->pemandu_id) == $p->id ? 'selected' : '' }}">
                    <input type="radio" name="pemandu_id" value="{{ $p->id }}"
                        {{ old('pemandu_id', $paket->pemandu_id) == $p->id ? 'checked' : '' }}
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

        <div class="form-card">
            <div class="form-section-title"><i class="fa-solid fa-calendar-days" style="margin-right:6px"></i>Jadwal Keberangkatan</div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Tanggal Mulai</label>
                    <input class="form-input" type="date" name="tanggal_mulai" value="{{ old('tanggal_mulai', optional($paket->tanggal_mulai)->format('Y-m-d')) }}" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Tanggal Berakhir</label>
                    <input class="form-input" type="date" name="tanggal_selesai" value="{{ old('tanggal_selesai', optional($paket->tanggal_selesai)->format('Y-m-d')) }}" required>
                </div>
            </div>

            <div class="form-row-full">
                <div class="form-group">
                    <label class="form-label">Catatan Tambahan</label>
                    <textarea class="form-textarea" name="catatan">{{ old('catatan', $paket->catatan) }}</textarea>
                </div>
            </div>

            <div class="form-actions">
                <a href="{{ route('paket.index') }}" class="btn-cancel">Batal</a>
                <button type="submit" class="btn-submit">✓ Simpan Perubahan</button>
            </div>
        </div>
    </form>
</div>
@endsection
