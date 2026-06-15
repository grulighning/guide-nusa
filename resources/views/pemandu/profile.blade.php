@extends('layouts.app')
@section('title', 'Profil Saya')

@section('content')

{{-- Header --}}
<div style="background:linear-gradient(135deg,#0d5c45,#1a8a6a);padding:20px 24px;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px">
    <div style="display:flex;align-items:center;gap:12px">
        <a href="{{ route('pemandu.dashboard') }}" style="color:#fff;font-size:14px;font-weight:600;text-decoration:none;display:flex;align-items:center;gap:6px;padding:6px 12px;background:rgba(255,255,255,0.15);border-radius:8px;border:1px solid rgba(255,255,255,0.3)">
            <i class="fa-solid fa-arrow-left"></i> Dashboard
        </a>
        <span style="color:#fff;font-size:18px;font-weight:800;margin-left:4px">Profil Saya</span>
    </div>
    <a href="{{ route('pemandu.show', $pemandu) }}" target="_blank" style="color:#fff;font-size:13px;font-weight:600;text-decoration:none;display:inline-flex;align-items:center;gap:6px;padding:8px 16px;background:rgba(255,255,255,0.2);border-radius:8px;border:1px solid rgba(255,255,255,0.3)">
        <i class="fa-solid fa-eye"></i> Lihat Halaman Publik
    </a>
</div>

<div class="pmnd-detail-body">

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
    @if($errors->any())
    <div style="background:#fff3e0;border:2px solid #e65100;border-radius:10px;padding:12px 16px;margin-bottom:16px;font-size:13px;font-weight:600;color:#e65100;display:flex;align-items:center;gap:8px">
        <i class="fa-solid fa-triangle-exclamation"></i>
        <ul style="margin:0;padding-left:16px">
            @foreach($errors->all() as $err)
            <li>{{ $err }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    {{-- Profile Card --}}
    <div class="pmnd-profile-card">
        <div class="pmnd-big-avatar" style="background:{{ $pemandu->warna_avatar ?? '#0d5c45' }}">{{ $pemandu->inisial }}</div>
        <div class="pmnd-profile-info">
            <div class="pmnd-profile-name">{{ $pemandu->user->name }}</div>
            <div class="pmnd-profile-tags">
                @foreach(explode(',', $pemandu->spesialisasi) as $chip)
                <span class="pmnd-profile-tag">{{ trim($chip) }}</span>
                @endforeach
            </div>
            <div class="pmnd-profile-meta">
                <span><i class="fa-solid fa-location-dot" style="margin-right:6px"></i>Sumatera Barat</span>
                <span><i class="fa-solid fa-language" style="margin-right:6px"></i>Indonesia, Minang</span>
                <span><i class="fa-solid fa-calendar-days" style="margin-right:6px"></i>{{ $pemandu->pengalaman_tahun }} Tahun Pengalaman</span>
            </div>
        </div>
        <div class="pmnd-big-rating">
            <span class="star">★</span>{{ number_format($pemandu->rating, 1) }}
        </div>
    </div>

    {{-- Stats + Ketersediaan (Editable) --}}
    <div class="detail-grid">
        <div class="detail-section-card">
            <div class="detail-section-title"><i class="fa-solid fa-chart-column" style="margin-right:6px"></i>Statistik</div>
            <div class="stat-boxes">
                <div class="stat-box">
                    <div class="stat-val">{{ number_format($pemandu->rating, 1) }}</div>
                    <div class="stat-lbl">Rating</div>
                </div>
                <div class="stat-box">
                    <div class="stat-val">{{ $pemandu->jumlah_tour }}</div>
                    <div class="stat-lbl">Tour Selesai</div>
                </div>
                <div class="stat-box" style="position:relative">
                    <div class="stat-val" id="pengalaman-val">{{ $pemandu->pengalaman_tahun }}th</div>
                    <div class="stat-lbl">Pengalaman</div>
                    <button onclick="openPengalamanModal()"
                            style="position:absolute;top:-4px;right:-4px;width:22px;height:22px;border-radius:50%;border:2px solid #0d5c45;background:#0d5c45;color:#fff;font-size:11px;cursor:pointer;display:flex;align-items:center;justify-content:center;padding:0;font-family:inherit">
                        <i class="fa-solid fa-pen"></i>
                    </button>
                </div>
            </div>
        </div>

        {{-- Ketersediaan (Editable) --}}
        <div class="detail-section-card" style="border:3px solid #0d5c45">
            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:14px">
                <div class="detail-section-title" style="margin-bottom:0">
                    <i class="fa-solid fa-calendar-days" style="margin-right:6px;color:#0d5c45"></i>Ketersediaan Saya
                </div>
                <button onclick="openEditModal()" style="padding:8px 16px;background:#0d5c45;color:#fff;border:2px solid #222;border-radius:8px;font-size:12px;font-weight:700;cursor:pointer;box-shadow:2px 2px 0 #222">
                    <i class="fa-solid fa-pen-to-square" style="margin-right:6px"></i> Edit
                </button>
            </div>

            {{-- Tampilan Hari Tersedia --}}
            <div style="font-size:12px;font-weight:700;color:#555;margin-bottom:8px">Hari Tersedia:</div>
            <div style="display:flex;gap:6px;flex-wrap:wrap;margin-bottom:12px">
                @php
                    $hariNama = ['Sen','Sel','Rab','Kam','Jum','Sab','Min'];
                    $avail = $pemandu->ketersediaan ?? [];
                @endphp
                @foreach($hariNama as $i => $h)
                @php $checked = in_array($i, $avail); @endphp
                <span style="display:flex;align-items:center;gap:5px;padding:6px 12px;border-radius:20px;font-size:12px;font-weight:700;
                    {{ $checked ? 'background:#0d5c45;color:#fff;border:2px solid #0d5c45;' : 'background:#f5f5f5;color:#888;border:2px solid #ddd;' }}">
                    {{ $h }} @if($checked) ✓ @else ✗ @endif
                </span>
                @endforeach
            </div>

            @php $tanggalList = $pemandu->getTanggalTersediaList(); @endphp
            @if(count($tanggalList) > 0)
            <div style="font-size:12px;font-weight:700;color:#555;margin-bottom:8px">Tanggal Spesifik:</div>
            <div style="display:flex;gap:8px;flex-wrap:wrap;margin-bottom:8px">
                @foreach($tanggalList as $tgl)
                <span style="display:flex;align-items:center;gap:4px;padding:4px 8px 4px 12px;background:#e8f5e9;border:1px solid #0d5c45;border-radius:20px;font-size:11px;font-weight:600;color:#0d5c45">
                    {{ \Carbon\Carbon::parse($tgl)->format('d M Y') }}
                </span>
                @endforeach
            </div>
            @endif

            <div class="avail-legend" style="margin-top:8px">
                <div class="legend-item"><div class="legend-dot" style="background:#0d5c45"></div>Tersedia</div>
                <div class="legend-item"><div class="legend-dot" style="background:#e0e0e0"></div>Tidak Tersedia</div>
            </div>
        </div>
    </div>

    {{-- Paket Tour + Ulasan --}}
    <div class="detail-grid" style="margin-top:16px">

        <div class="detail-section-card">
            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:14px">
                <div class="detail-section-title" style="margin-bottom:0"><i class="fa-solid fa-map-location-dot" style="margin-right:6px"></i>Paket Tour Saya</div>
                <a href="{{ route('paket.create') }}" style="padding:6px 14px;background:#0d5c45;color:#fff;border:2px solid #222;border-radius:8px;font-size:11px;font-weight:700;text-decoration:none;box-shadow:2px 2px 0 #222">
                    <i class="fa-solid fa-plus"></i> Tambah
                </a>
            </div>
            @forelse($pemandu->paketTours as $paket)
            <div class="jadwal-item">
                <span class="jadwal-time">{{ $paket->jam_mulai }} – {{ $paket->jam_selesai }}</span>
                <div class="jadwal-info">
                    <div class="jadwal-name">{{ $paket->nama }}</div>
                    <div class="jadwal-loc">
                        {{ $paket->destinasis->pluck('nama')->implode(', ') }}
                    </div>
                    <div style="display:flex;gap:6px;align-items:center;flex-wrap:wrap">
                        <span class="jadwal-badge badge-{{ $paket->status === 'aktif' ? 'berjalan' : 'terjadwal' }}">
                            {{ ucfirst($paket->status) }}
                        </span>
                        <span style="font-size:12px;font-weight:700;color:#0d5c45">{{ $paket->harga_format }}</span>
                        <a href="{{ route('paket.edit', $paket) }}" style="color:#888;font-size:12px;text-decoration:none">
                            <i class="fa-solid fa-pen-to-square"></i>
                        </a>
                    </div>
                </div>
            </div>
            @empty
            <p style="font-size:13px;color:#aaa">Belum ada paket tour. <a href="{{ route('paket.create') }}" style="color:#0d5c45;font-weight:700">Buat sekarang</a></p>
            @endforelse
        </div>

        <div class="detail-section-card">
            <div class="detail-section-title"><i class="fa-solid fa-star" style="margin-right:6px"></i>Ulasan Wisatawan</div>

            {{-- Old ulasan (Ulasan model) --}}
            @foreach($pemandu->ulasans->take(4) as $ulasan)
            <div class="ulasan-item">
                <div class="ulasan-header">
                    <div class="ulasan-name">{{ $ulasan->nama_wisatawan }}</div>
                    <div class="ulasan-stars">{{ str_repeat('★', (int)$ulasan->rating) }}</div>
                </div>
                <div class="ulasan-text">{{ $ulasan->komentar }}</div>
                <div class="ulasan-meta">
                    <span>{{ $ulasan->destinasi }}</span>
                    <span>{{ $ulasan->created_at->format('M Y') }}</span>
                </div>
            </div>
            @endforeach

            {{-- New reviews (Review model) --}}
            @forelse($reviews->take(4) as $review)
            <div class="ulasan-item">
                <div class="ulasan-header">
                    <div class="ulasan-name">{{ $review->user->name ?? 'Pengunjung' }}</div>
                    <div class="ulasan-stars" style="color:#F4CD0B">{{ str_repeat('★', $review->rating) }}{{ str_repeat('☆', 5 - $review->rating) }}</div>
                </div>
                @if($review->comment)
                <div class="ulasan-text">{{ $review->comment }}</div>
                @endif
                <div class="ulasan-meta">
                    <span>{{ $review->destination?->nama ?? 'Destinasi' }}</span>
                    <span>{{ $review->created_at->format('M Y') }}</span>
                </div>
            </div>
            @empty
                @if($pemandu->ulasans->count() === 0)
                <p style="font-size:13px;color:#aaa">Belum ada ulasan.</p>
                @endif
            @endforelse
        </div>

    </div>

</div>

{{-- ===== MODAL EDIT PENGALAMAN ===== --}}
<div id="pengalamanModal" style="display:none;position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.5);z-index:9999;align-items:center;justify-content:center;padding:20px;box-sizing:border-box">
    <div style="background:#fff;border-radius:14px;width:100%;max-width:400px;padding:24px;box-shadow:0 10px 30px rgba(0,0,0,0.3)">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px">
            <h2 style="font-size:18px;font-weight:800;margin:0;display:flex;align-items:center;gap:8px">
                <i class="fa-solid fa-calendar-days" style="color:#0d5c45"></i>
                Edit Pengalaman
            </h2>
            <button type="button" onclick="closePengalamanModal()" style="border:none;background:transparent;font-size:24px;cursor:pointer;color:#888;padding:0 8px">
                &times;
            </button>
        </div>

        <form method="POST" action="{{ route('pemandu.profile.update') }}">
            @csrf
            @method('PUT')

            <div style="margin-bottom:20px">
                <label style="display:block;font-size:13px;font-weight:600;margin-bottom:8px">Tahun Pengalaman</label>
                <select name="pengalaman_tahun"
                        style="width:100%;padding:12px;border:2px solid #222;border-radius:10px;font-size:14px;font-family:inherit;background:#fff;cursor:pointer">
                    @for($i = 0; $i <= 50; $i++)
                    <option value="{{ $i }}" {{ $pemandu->pengalaman_tahun == $i ? 'selected' : '' }}>{{ $i }} tahun</option>
                    @endfor
                </select>
            </div>

            <div style="display:flex;gap:12px">
                <button type="button" onclick="closePengalamanModal()"
                        style="flex:1;padding:12px;background:#f5f5f5;color:#333;border:2px solid #ddd;border-radius:10px;font-size:14px;font-weight:700;cursor:pointer;font-family:inherit">
                    Batal
                </button>
                <button type="submit"
                        style="flex:2;padding:12px;background:#0d5c45;color:#fff;border:2px solid #222;border-radius:10px;font-size:14px;font-weight:700;cursor:pointer;font-family:inherit;box-shadow:2px 2px 0 #222;display:inline-flex;align-items:center;justify-content:center;gap:6px">
                    <i class="fa-solid fa-floppy-disk"></i> Simpan
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ===== MODAL EDIT KETERSEDIAAN (sama seperti di dashboard) ===== --}}
<div id="editModal" style="display:none;position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.5);z-index:9999;align-items:center;justify-content:center;padding:20px;box-sizing:border-box">
    <div style="background:#fff;border-radius:14px;width:100%;max-width:600px;max-height:90vh;overflow-y:auto;padding:24px;box-shadow:0 10px 30px rgba(0,0,0,0.3)">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px">
            <h2 style="font-size:18px;font-weight:800;margin:0">
                <i class="fa-solid fa-calendar-days" style="margin-right:6px;color:#0d5c45"></i>
                Edit Ketersediaan
            </h2>
            <button type="button" id="closeModalBtn" style="border:none;background:transparent;font-size:24px;cursor:pointer;color:#888;padding:0 8px">
                &times;
            </button>
        </div>

        <form method="POST" action="{{ route('pemandu.ketersediaan.update') }}" id="avail-form">
            @csrf
            @method('PUT')

            {{-- Pola Hari Mingguan --}}
            <div style="font-size:14px;font-weight:700;color:#555;margin-bottom:12px">Pilih Hari Tersedia (Senin-Minggu):</div>
            <div id="day-toggles-container" style="display:flex;gap:8px;flex-wrap:wrap;margin-bottom:24px">
                @php
                    $hariNama = ['Sen','Sel','Rab','Kam','Jum','Sab','Min'];
                    $avail = is_array($pemandu->ketersediaan) ? $pemandu->ketersediaan : [];
                @endphp
                @foreach($hariNama as $i => $h)
                @php $checked = in_array($i, $avail); @endphp
                <div class="day-toggle-btn" data-day-index="{{ $i }}" style="display:flex;align-items:center;gap:5px;padding:8px 16px;border-radius:20px;cursor:pointer;font-size:14px;font-weight:700;user-select:none;transition:all .15s;
                    {{ $checked ? 'background:#0d5c45;color:#fff;border:2px solid #0d5c45;' : 'background:#fff;color:#888;border:2px solid #ddd;' }}">
                    <input type="checkbox" name="ketersediaan[]" value="{{ $i }}"
                           {{ $checked ? 'checked' : '' }}
                           style="position:absolute;opacity:0;pointer-events:none;width:0;height:0;">
                    {{ $h }}
                </div>
                @endforeach
            </div>

            {{-- Tanggal Spesifik --}}
            <div style="font-size:14px;font-weight:700;color:#555;margin-bottom:12px">Tanggal Spesifik Tersedia:</div>
            <div style="display:flex;gap:8px;flex-wrap:wrap;margin-bottom:16px" id="tanggal-list">
                @php $tanggalList = $pemandu->getTanggalTersediaList(); @endphp
                @forelse($tanggalList as $tgl)
                <div style="display:flex;align-items:center;gap:4px;padding:6px 10px 6px 14px;background:#e8f5e9;border:1px solid #0d5c45;border-radius:20px;font-size:12px;font-weight:600;color:#0d5c45">
                    <span>{{ \Carbon\Carbon::parse($tgl)->format('d M Y') }}</span>
                    <input type="hidden" name="tanggal_tersedia[]" value="{{ $tgl }}">
                    <button type="button" class="remove-tanggal-btn"
                            style="border:none;background:transparent;color:#c62828;cursor:pointer;font-size:16px;padding:0 4px">&times;</button>
                </div>
                @empty
                <div style="font-size:13px;color:#aaa" id="no-tanggal">Belum ada tanggal spesifik. Tambahkan di bawah.</div>
                @endforelse
            </div>

            {{-- Input tambah tanggal --}}
            <div style="display:flex;gap:8px;align-items:center;flex-wrap:wrap;margin-bottom:24px">
                <input type="date" id="tambah-tanggal" min="{{ date('Y-m-d') }}"
                       style="padding:8px 12px;border:2px solid #222;border-radius:8px;font-size:13px;font-family:inherit;flex:1;min-width:180px">
                <button type="button" id="tambah-tanggal-btn"
                        style="padding:8px 16px;background:#0d5c45;color:#fff;border:2px solid #222;border-radius:8px;font-size:13px;font-weight:700;cursor:pointer">
                    <i class="fa-solid fa-plus"></i> Tambah Tanggal
                </button>
            </div>

            <div style="display:flex;gap:12px">
                <button type="button" id="cancelModalBtn" style="flex:1;padding:12px;background:#f5f5f5;color:#333;border:2px solid #ddd;border-radius:10px;font-size:14px;font-weight:700;cursor:pointer">
                    Batal
                </button>
                <button type="submit" style="flex:2;padding:12px;background:#0d5c45;color:#fff;border:2px solid #222;border-radius:10px;font-size:14px;font-weight:700;cursor:pointer;box-shadow:2px 2px 0 #222">
                    <i class="fa-solid fa-floppy-disk" style="margin-right:6px"></i> Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ===== SCRIPT untuk Modal Pengalaman ===== --}}
<script>
function openPengalamanModal() {
    document.getElementById('pengalamanModal').style.display = 'flex';
    document.body.style.overflow = 'hidden';
}
function closePengalamanModal() {
    document.getElementById('pengalamanModal').style.display = 'none';
    document.body.style.overflow = '';
}
document.addEventListener('DOMContentLoaded', function() {
    var m = document.getElementById('pengalamanModal');
    if (m) m.addEventListener('click', function(e) { if (e.target === this) closePengalamanModal(); });
});
</script>

{{-- ===== SCRIPT untuk Ketersediaan ===== --}}
<script>
(function() {
    // ── Modal open/close ──
    var modal = document.getElementById('editModal');

    window.openEditModal = function() {
        modal.style.display = 'flex';
        document.body.style.overflow = 'hidden';
    };

    window.closeEditModal = function() {
        modal.style.display = 'none';
        document.body.style.overflow = '';
    };

    // Close modal on background click
    modal.addEventListener('click', function(e) {
        if (e.target === modal) closeEditModal();
    });

    // Close buttons
    document.getElementById('closeModalBtn').addEventListener('click', closeEditModal);
    document.getElementById('cancelModalBtn').addEventListener('click', closeEditModal);

    // ── Day toggle buttons ──
    var dayButtons = document.querySelectorAll('.day-toggle-btn');
    dayButtons.forEach(function(btn) {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();

            var checkbox = this.querySelector('input[type="checkbox"]');
            checkbox.checked = !checkbox.checked;

            if (checkbox.checked) {
                this.style.background = '#0d5c45';
                this.style.color = '#fff';
                this.style.borderColor = '#0d5c45';
            } else {
                this.style.background = '#fff';
                this.style.color = '#888';
                this.style.borderColor = '#ddd';
            }
        });
    });

    // ── Remove tanggal buttons ──
    document.getElementById('tanggal-list').addEventListener('click', function(e) {
        if (e.target.classList.contains('remove-tanggal-btn') || e.target.closest('.remove-tanggal-btn')) {
            var btn = e.target.closest('.remove-tanggal-btn') || e.target;
            btn.parentElement.remove();
        }
    });

    // ── Tambah tanggal ──
    document.getElementById('tambah-tanggal-btn').addEventListener('click', function() {
        var input = document.getElementById('tambah-tanggal');
        if (!input.value) return;

        var list = document.getElementById('tanggal-list');
        var noEl = document.getElementById('no-tanggal');
        if (noEl) noEl.remove();

        // Cek duplikat
        var existingInputs = list.querySelectorAll('input[name="tanggal_tersedia[]"]');
        for (var i = 0; i < existingInputs.length; i++) {
            if (existingInputs[i].value === input.value) {
                alert('Tanggal sudah ada dalam daftar.');
                return;
            }
        }

        // Format tanggal untuk tampilan
        var dateObj = new Date(input.value + 'T00:00:00');
        var options = { day: 'numeric', month: 'short', year: 'numeric' };
        var formattedDate = dateObj.toLocaleDateString('id-ID', options);

        // Tambah elemen ke daftar
        var div = document.createElement('div');
        div.style.cssText = 'display:flex;align-items:center;gap:4px;padding:6px 10px 6px 14px;background:#e8f5e9;border:1px solid #0d5c45;border-radius:20px;font-size:12px;font-weight:600;color:#0d5c45';
        div.innerHTML = '<span>' + formattedDate + '</span>' +
            '<input type="hidden" name="tanggal_tersedia[]" value="' + input.value + '">' +
            '<button type="button" class="remove-tanggal-btn" style="border:none;background:transparent;color:#c62828;cursor:pointer;font-size:16px;padding:0 4px">&times;</button>';
        list.appendChild(div);

        // Reset input
        input.value = '';
    });
})();
</script>

@endsection
