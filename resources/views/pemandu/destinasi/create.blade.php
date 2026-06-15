@extends('layouts.app')
@section('title', 'Tambah Destinasi')

@section('content')
<div style="max-width:640px;margin:40px auto;padding:0 16px">

    <div style="display:flex;align-items:center;gap:12px;margin-bottom:24px">
        <a href="{{ route('pemandu.destinasi.index') }}" style="font-size:18px;color:#222;text-decoration:none">
            <i class="fa-solid fa-arrow-left"></i>
        </a>
        <h1 style="font-size:22px;font-weight:800;margin:0">Tambah Destinasi</h1>
    </div>

    <form method="POST" action="{{ route('pemandu.destinasi.store') }}" enctype="multipart/form-data"
          style="background:#fff;border:3px solid #222;border-radius:16px;box-shadow:6px 6px 0 #222;padding:28px 24px">
        @csrf

        {{-- Nama --}}
        <div style="margin-bottom:20px">
            <label style="font-size:13px;font-weight:700;display:block;margin-bottom:6px">Nama Destinasi</label>
            <input type="text" name="nama" value="{{ old('nama') }}" required maxlength="255"
                   style="width:100%;padding:10px 14px;border:2px solid #222;border-radius:10px;font-size:14px;box-sizing:border-box">
            @error('nama') <div style="font-size:12px;color:#c62828;margin-top:4px">{{ $message }}</div> @enderror
        </div>

        {{-- Lokasi + Kabupaten --}}
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:20px">
            <div>
                <label style="font-size:13px;font-weight:700;display:block;margin-bottom:6px">Lokasi</label>
                <input type="text" name="lokasi" value="{{ old('lokasi') }}" required maxlength="255"
                       style="width:100%;padding:10px 14px;border:2px solid #222;border-radius:10px;font-size:14px;box-sizing:border-box">
                @error('lokasi') <div style="font-size:12px;color:#c62828;margin-top:4px">{{ $message }}</div> @enderror
            </div>
            <div>
                <label style="font-size:13px;font-weight:700;display:block;margin-bottom:6px">Kabupaten</label>
                <input type="text" name="kabupaten" value="{{ old('kabupaten') }}" maxlength="100"
                       style="width:100%;padding:10px 14px;border:2px solid #222;border-radius:10px;font-size:14px;box-sizing:border-box">
            </div>
        </div>

        {{-- Kategori + Emoji --}}
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:20px">
            <div>
                <label style="font-size:13px;font-weight:700;display:block;margin-bottom:6px">Kategori</label>
                <select name="kategori" required
                        style="width:100%;padding:10px 14px;border:2px solid #222;border-radius:10px;font-size:14px;background:#fff;box-sizing:border-box">
                    @foreach($kategoriList as $k)
                    <option value="{{ $k }}" {{ old('kategori') === $k ? 'selected' : '' }}>{{ $k }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label style="font-size:13px;font-weight:700;display:block;margin-bottom:6px">Emoji</label>
                <select name="emoji"
                        style="width:100%;padding:10px 14px;border:2px solid #222;border-radius:10px;font-size:14px;background:#fff;box-sizing:border-box">
                    @foreach($emojiList as $emoji => $label)
                    <option value="{{ $emoji }}" {{ old('emoji') === $emoji ? 'selected' : '' }}>{{ $emoji }} {{ $label }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        {{-- Deskripsi --}}
        <div style="margin-bottom:20px">
            <label style="font-size:13px;font-weight:700;display:block;margin-bottom:6px">Deskripsi</label>
            <textarea name="deskripsi" rows="4"
                      style="width:100%;padding:10px 14px;border:2px solid #222;border-radius:10px;font-size:14px;resize:vertical;box-sizing:border-box">{{ old('deskripsi') }}</textarea>
        </div>

        {{-- Foto --}}
        <div style="margin-bottom:24px">
            <label style="font-size:13px;font-weight:700;display:block;margin-bottom:6px">Foto Destinasi</label>
            <div style="border:2px dashed #aaa;border-radius:12px;padding:20px;text-align:center;background:#fafafa;cursor:pointer"
                 onclick="document.getElementById('fotoInput').click()">
                <div style="font-size:32px;margin-bottom:8px">📷</div>
                <div style="font-size:13px;color:#888;font-weight:600">Klik untuk upload foto</div>
                <div style="font-size:11px;color:#aaa;margin-top:4px">Bisa pilih beberapa foto sekaligus (max 5MB per foto)</div>
                <input type="file" name="fotos[]" id="fotoInput" multiple accept="image/*"
                       style="display:none" onchange="previewFotos(this)">
            </div>

            {{-- Preview --}}
            <div id="fotoPreview" style="display:flex;gap:10px;margin-top:12px;flex-wrap:wrap"></div>
            @error('fotos.*') <div style="font-size:12px;color:#c62828;margin-top:4px">{{ $message }}</div> @enderror
        </div>

        {{-- Submit --}}
        <button type="submit"
                style="width:100%;padding:14px;background:#0d5c45;color:#fff;border:3px solid #222;border-radius:12px;font-size:15px;font-weight:800;box-shadow:4px 4px 0 #222;cursor:pointer">
            <i class="fa-solid fa-plus" style="margin-right:6px"></i> Tambah Destinasi
        </button>
    </form>
</div>

@push('scripts')
<script>
function previewFotos(input) {
    const container = document.getElementById('fotoPreview');
    container.innerHTML = '';
    if (input.files) {
        Array.from(input.files).forEach((file, i) => {
            const reader = new FileReader();
            reader.onload = function(e) {
                const div = document.createElement('div');
                div.style.cssText = 'width:90px;height:90px;border:2px solid #222;border-radius:10px;overflow:hidden;position:relative';
                div.innerHTML = `<img src="${e.target.result}" style="width:100%;height:100%;object-fit:cover">
                    <div style="position:absolute;bottom:0;left:0;right:0;background:rgba(0,0,0,0.6);color:#fff;font-size:10px;text-align:center;padding:2px">${(file.size/1024/1024).toFixed(1)}MB</div>`;
                container.appendChild(div);
            };
            reader.readAsDataURL(file);
        });
    }
}
</script>
@endpush
@endsection
