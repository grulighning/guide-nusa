@extends('layouts.app')
@section('title', 'Edit Destinasi')

@section('content')
<div style="max-width:640px;margin:40px auto;padding:0 16px">

    <div style="display:flex;align-items:center;gap:12px;margin-bottom:24px">
        <a href="{{ route('pemandu.destinasi.index') }}" style="font-size:18px;color:#222;text-decoration:none">
            <i class="fa-solid fa-arrow-left"></i>
        </a>
        <h1 style="font-size:22px;font-weight:800;margin:0">Edit Destinasi</h1>
    </div>

    <form method="POST" action="{{ route('pemandu.destinasi.update', $destinasi) }}" enctype="multipart/form-data"
          style="background:#fff;border:3px solid #222;border-radius:16px;box-shadow:6px 6px 0 #222;padding:28px 24px">
        @csrf @method('PUT')

        {{-- Nama --}}
        <div style="margin-bottom:20px">
            <label style="font-size:13px;font-weight:700;display:block;margin-bottom:6px">Nama Destinasi</label>
            <input type="text" name="nama" value="{{ old('nama', $destinasi->nama) }}" required maxlength="255"
                   style="width:100%;padding:10px 14px;border:2px solid #222;border-radius:10px;font-size:14px;box-sizing:border-box">
            @error('nama') <div style="font-size:12px;color:#c62828;margin-top:4px">{{ $message }}</div> @enderror
        </div>

        {{-- Lokasi + Kabupaten --}}
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:20px">
            <div>
                <label style="font-size:13px;font-weight:700;display:block;margin-bottom:6px">Lokasi</label>
                <input type="text" name="lokasi" value="{{ old('lokasi', $destinasi->lokasi) }}" required maxlength="255"
                       style="width:100%;padding:10px 14px;border:2px solid #222;border-radius:10px;font-size:14px;box-sizing:border-box">
                @error('lokasi') <div style="font-size:12px;color:#c62828;margin-top:4px">{{ $message }}</div> @enderror
            </div>
            <div>
                <label style="font-size:13px;font-weight:700;display:block;margin-bottom:6px">Kabupaten</label>
                <input type="text" name="kabupaten" value="{{ old('kabupaten', $destinasi->kabupaten) }}" maxlength="100"
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
                    <option value="{{ $k }}" {{ (old('kategori', $destinasi->kategori) === $k) ? 'selected' : '' }}>{{ $k }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label style="font-size:13px;font-weight:700;display:block;margin-bottom:6px">Emoji</label>
                <select name="emoji"
                        style="width:100%;padding:10px 14px;border:2px solid #222;border-radius:10px;font-size:14px;background:#fff;box-sizing:border-box">
                    @foreach($emojiList as $emoji => $label)
                    <option value="{{ $emoji }}" {{ (old('emoji', $destinasi->emoji) === $emoji) ? 'selected' : '' }}>{{ $emoji }} {{ $label }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        {{-- Deskripsi --}}
        <div style="margin-bottom:20px">
            <label style="font-size:13px;font-weight:700;display:block;margin-bottom:6px">Deskripsi</label>
            <textarea name="deskripsi" rows="4"
                      style="width:100%;padding:10px 14px;border:2px solid #222;border-radius:10px;font-size:14px;resize:vertical;box-sizing:border-box">{{ old('deskripsi', $destinasi->deskripsi) }}</textarea>
        </div>

        {{-- Foto existing --}}
        <div style="margin-bottom:16px">
            <label style="font-size:13px;font-weight:700;display:block;margin-bottom:8px">Foto Saat Ini</label>
            @if(!empty($destinasi->fotos) && count($destinasi->fotos))
            <div style="display:flex;gap:10px;flex-wrap:wrap" id="existingFotos">
                @foreach($destinasi->fotos as $foto)
                <div class="foto-item" data-path="{{ $foto }}" style="width:100px;height:100px;border:2px solid #222;border-radius:10px;overflow:hidden;position:relative">
                    <img src="{{ \Illuminate\Support\Facades\Storage::url($foto) }}" style="width:100%;height:100%;object-fit:cover">
                    <button type="button" onclick="hapusFoto(this, '{{ $foto }}')"
                            style="position:absolute;top:4px;right:4px;width:22px;height:22px;border-radius:50%;border:2px solid #c62828;background:#fff;color:#c62828;font-size:12px;cursor:pointer;display:flex;align-items:center;justify-content:center;font-weight:700;line-height:1">
                        ×
                    </button>
                </div>
                @endforeach
            </div>
            <input type="hidden" name="hapus_fotos" id="hapusFotosInput" value="">
            @else
            <div style="font-size:12px;color:#888;padding:12px;background:#f5f5f5;border-radius:8px;text-align:center">Belum ada foto.</div>
            @endif
        </div>

        {{-- Upload foto baru --}}
        <div style="margin-bottom:24px">
            <label style="font-size:13px;font-weight:700;display:block;margin-bottom:6px">Tambah Foto Baru</label>
            <div style="border:2px dashed #aaa;border-radius:12px;padding:20px;text-align:center;background:#fafafa;cursor:pointer"
                 onclick="document.getElementById('fotoInput').click()">
                <div style="font-size:32px;margin-bottom:8px">📷</div>
                <div style="font-size:13px;color:#888;font-weight:600">Klik untuk upload foto tambahan</div>
                <div style="font-size:11px;color:#aaa;margin-top:4px">Bisa pilih beberapa foto (max 5MB per foto)</div>
                <input type="file" name="fotos[]" id="fotoInput" multiple accept="image/*"
                       style="display:none" onchange="previewFotos(this)">
            </div>
            <div id="fotoPreview" style="display:flex;gap:10px;margin-top:12px;flex-wrap:wrap"></div>
            @error('fotos.*') <div style="font-size:12px;color:#c62828;margin-top:4px">{{ $message }}</div> @enderror
        </div>

        {{-- Submit --}}
        <button type="submit"
                style="width:100%;padding:14px;background:#0d5c45;color:#fff;border:3px solid #222;border-radius:12px;font-size:15px;font-weight:800;box-shadow:4px 4px 0 #222;cursor:pointer">
            <i class="fa-solid fa-floppy-disk" style="margin-right:6px"></i> Simpan Perubahan
        </button>
    </form>
</div>

@push('scripts')
<script>
function previewFotos(input) {
    const container = document.getElementById('fotoPreview');
    container.innerHTML = '';
    if (input.files) {
        Array.from(input.files).forEach((file) => {
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

function hapusFoto(btn, path) {
    const item = btn.closest('.foto-item');
    item.style.opacity = '0.3';
    item.style.pointerEvents = 'none';

    const input = document.getElementById('hapusFotosInput');
    const paths = input.value ? input.value.split(',') : [];
    paths.push(path);
    input.value = paths.join(',');

    // Add a "dihapus" label
    const label = document.createElement('div');
    label.style.cssText = 'position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);background:rgba(198,40,40,0.85);color:#fff;font-size:10px;font-weight:700;padding:2px 8px;border-radius:4px;white-space:nowrap';
    label.textContent = 'Dihapus';
    item.appendChild(label);
}
</script>
@endpush
@endsection
