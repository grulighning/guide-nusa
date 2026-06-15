@extends('layouts.app')
@section('title', 'Dashboard Pemandu')

@section('content')
<div style="max-width:900px;margin:40px auto;padding:0 16px">

    {{-- Header --}}
    <div style="display:flex;align-items:center;gap:16px;margin-bottom:32px">
        <div style="width:56px;height:56px;border-radius:50%;background:{{ $pemandu->warna_avatar ?? '#0d5c45' }};display:flex;align-items:center;justify-content:center;font-size:20px;font-weight:800;color:#fff;flex-shrink:0">
            {{ $pemandu->inisial }}
        </div>
        <div>
            <h1 style="font-size:22px;font-weight:800;margin:0">Halo, {{ $pemandu->user->name }}</h1>
            <p style="font-size:13px;color:#888;margin:4px 0 0">{{ $bookings->count() }} booking masuk · {{ $bookings->where('status', 'menunggu')->count() }} menunggu konfirmasi</p>
        </div>
    </div>

    {{-- Stat cards --}}
    <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:16px;margin-bottom:32px">
        <div style="background:#fff;border:3px solid #222;border-radius:14px;box-shadow:4px 4px 0 #222;padding:20px;text-align:center">
            <div style="font-size:28px;font-weight:800;color:#0d5c45">{{ $bookings->where('status', 'menunggu')->count() }}</div>
            <div style="font-size:12px;color:#888;font-weight:600;margin-top:4px">Menunggu</div>
        </div>
        <div style="background:#fff;border:3px solid #222;border-radius:14px;box-shadow:4px 4px 0 #222;padding:20px;text-align:center">
            <div style="font-size:28px;font-weight:800;color:#1e88e5">{{ $bookings->where('status', 'dikonfirmasi')->count() }}</div>
            <div style="font-size:12px;color:#888;font-weight:600;margin-top:4px">Dikonfirmasi</div>
        </div>
        <div style="background:#fff;border:3px solid #222;border-radius:14px;box-shadow:4px 4px 0 #222;padding:20px;text-align:center">
            <div style="font-size:28px;font-weight:800;color:#43a047">{{ $bookings->where('status', 'selesai')->count() }}</div>
            <div style="font-size:12px;color:#888;font-weight:600;margin-top:4px">Selesai</div>
        </div>
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

    {{-- Debug Info --}}
    @if(auth()->check())
    <div style="background:#f0f0f0;border:1px solid #ccc;padding:10px;margin-bottom:10px;border-radius:8px;font-size:12px">
        Debug: User logged in: {{ auth()->user()->name }} (Role: {{ auth()->user()->role }}) | Pemandu exists: {{ auth()->user()->pemandu ? 'Ya' : 'Tidak' }}
    </div>
    @endif

    {{-- Ringkasan Ketersediaan --}}
    <div style="background:#fff;border:3px solid #0d5c45;border-radius:14px;box-shadow:4px 4px 0 #222;padding:20px;margin-bottom:20px">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:14px">
            <div style="font-size:14px;font-weight:800">
                <i class="fa-solid fa-calendar-days" style="margin-right:6px;color:#0d5c45"></i>
                Ketersediaan Saya
            </div>
            <button onclick="openEditModal()" style="padding:8px 16px;background:#0d5c45;color:#fff;border:2px solid #222;border-radius:8px;font-size:12px;font-weight:700;cursor:pointer;box-shadow:2px 2px 0 #222">
                <i class="fa-solid fa-pen-to-square" style="margin-right:6px"></i> Edit Ketersediaan
            </button>
        </div>

        {{-- Tampilan Status Ketersediaan --}}
        <div style="font-size:12px;font-weight:700;color:#555;margin-bottom:8px">Hari Tersedia:</div>
        <div style="display:flex;gap:6px;flex-wrap:wrap;margin-bottom:16px">
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
        <div style="display:flex;gap:8px;flex-wrap:wrap">
            @foreach($tanggalList as $tgl)
            <span style="display:flex;align-items:center;gap:4px;padding:4px 8px 4px 12px;background:#e8f5e9;border:1px solid #0d5c45;border-radius:20px;font-size:11px;font-weight:600;color:#0d5c45">
                {{ \Carbon\Carbon::parse($tgl)->format('d M Y') }}
            </span>
            @endforeach
        </div>
        @endif
    </div>

    {{-- Modal Edit Ketersediaan --}}
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

    {{-- ===== INLINE SCRIPT untuk Ketersediaan ===== --}}
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


    {{-- Daftar Booking --}}
    <div style="background:#fff;border:3px solid #222;border-radius:16px;box-shadow:6px 6px 0 #222;overflow:hidden">
    <div style="padding:20px 24px;border-bottom:3px solid #222;background:#fafafa;display:flex;align-items:center;justify-content:space-between;gap:12px;flex-wrap:wrap">
            <h2 style="font-size:16px;font-weight:800;margin:0">Semua Pesanan Masuk</h2>
            <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap">
                <a href="{{ route('pemandu.destinasi.index') }}" style="font-size:12px;font-weight:700;color:#0d5c45;text-decoration:none;display:inline-flex;align-items:center;gap:4px">
                    <i class="fa-solid fa-map-pin"></i> Kelola Destinasi
                </a>
                <a href="{{ route('pemandu.destinasi.create') }}" style="font-size:12px;font-weight:800;color:#fff;background:#0d5c45;text-decoration:none;display:inline-flex;align-items:center;gap:5px;padding:8px 12px;border:2px solid #222;border-radius:8px;box-shadow:2px 2px 0 #222">
                    <i class="fa-solid fa-plus"></i> Tambah Destinasi
                </a>
            </div>
        </div>

        <div style="padding:16px 24px">
            @forelse($bookings as $booking)
            <div style="display:flex;gap:14px;align-items:flex-start;padding:16px 0;{{ !$loop->first ? 'border-top:2px solid #eee' : '' }}">
                {{-- Avatar wisatawan --}}
                <div style="width:44px;height:44px;border-radius:12px;background:#0d5c45;display:flex;align-items:center;justify-content:center;font-size:16px;font-weight:700;color:#fff;flex-shrink:0">
                    {{ strtoupper(substr($booking->wisatawan->name ?? 'W', 0, 1)) }}
                </div>

                {{-- Info --}}
                <div style="flex:1;min-width:0">
                    <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap">
                        <strong style="font-size:15px">{{ $booking->wisatawan->name ?? 'Wisatawan' }}</strong>
                        <span style="font-size:11px;padding:2px 10px;border-radius:20px;font-weight:600;
                            {{ $booking->status === 'menunggu' ? 'background:#fff3e0;color:#e65100;border:2px solid #e65100' : '' }}
                            {{ $booking->status === 'dikonfirmasi' ? 'background:#e3f2fd;color:#1565c0;border:2px solid #1565c0' : '' }}
                            {{ $booking->status === 'selesai' ? 'background:#e8f5e9;color:#2e7d32;border:2px solid #2e7d32' : '' }}
                            {{ $booking->status === 'dibatalkan' ? 'background:#fbe9e7;color:#c62828;border:2px solid #c62828' : '' }}
                            {{ $booking->status === 'menunggu_konfirmasi_selesai' ? 'background:#fff8e1;color:#f57f17;border:2px solid #f57f17' : '' }}">
                            {{ $booking->status === 'menunggu_konfirmasi_selesai' ? 'Menunggu konfirmasi wisatawan' : ucfirst($booking->status) }}
                        </span>
                    </div>

                    <div style="font-size:12px;color:#888;margin-top:6px;display:flex;gap:16px;flex-wrap:wrap">
                        @if($booking->paketTour)
                        <span><i class="fa-solid fa-map-pin" style="margin-right:4px"></i>
                            @foreach($booking->paketTour->destinasis as $d)
                                {{ $d->nama }}@if(!$loop->last), @endif
                            @endforeach
                        </span>
                        @endif
                        <span><i class="fa-solid fa-users" style="margin-right:4px"></i>{{ $booking->jumlah_peserta }} peserta</span>
                        <span><i class="fa-solid fa-calendar-days" style="margin-right:4px"></i>
                            {{ $booking->tanggal_booking ? $booking->tanggal_booking->format('d M Y') : 'Fleksibel' }}
                        </span>
                    </div>

                    @if($booking->catatan)
                    <div style="margin-top:8px;padding:8px 12px;background:#f5f5f5;border-radius:8px;font-size:12px;color:#555;border:1px solid #ddd">
                        <i class="fa-solid fa-quote-left" style="margin-right:4px;color:#888"></i>{{ $booking->catatan }}
                    </div>
                    @endif
                </div>

                {{-- Aksi --}}
                <div style="display:flex;flex-direction:column;align-items:flex-end;gap:8px;white-space:nowrap;flex-shrink:0">
                    <div style="font-size:11px;color:#aaa">
                        {{ $booking->created_at->diffForHumans() }}
                    </div>
                    @if($booking->status === 'menunggu')
                    <div style="display:flex;gap:6px;flex-wrap:wrap;justify-content:flex-end">
                        <form method="POST" action="{{ route('pemandu.bookings.confirm', $booking) }}" style="margin:0" id="confirm-form-{{ $booking->id }}">
                            @csrf @method('PATCH')
                            <button type="submit"
                                    style="padding:8px 14px;border:2px solid #0d5c45;border-radius:8px;background:#0d5c45;color:#fff;font-size:12px;font-weight:800;cursor:pointer;box-shadow:2px 2px 0 #222"
                                    data-nama="{{ $booking->wisatawan->name }}"
                                    onclick="return openConfirm(this, 'Konfirmasi Pesanan', 'Terima pesanan dari ', 'info', 'Ya, Konfirmasi', 'confirm-btn-primary', 'confirm-form-{{ $booking->id }}')">
                                <i class="fa-solid fa-check"></i> Konfirmasi
                            </button>
                        </form>
                        <form method="POST" action="{{ route('pemandu.bookings.reject', $booking) }}" style="margin:0" id="reject-form-{{ $booking->id }}">
                            @csrf @method('PATCH')
                            <button type="submit"
                                    style="padding:8px 14px;border:2px solid #c62828;border-radius:8px;background:#fff;color:#c62828;font-size:12px;font-weight:800;cursor:pointer"
                                    data-nama="{{ $booking->wisatawan->name }}"
                                    onclick="return openConfirm(this, 'Tolak Pesanan', 'Tolak pesanan dari ', 'danger', 'Ya, Tolak', 'confirm-btn-danger', 'reject-form-{{ $booking->id }}')">
                                <i class="fa-solid fa-xmark"></i> Tolak Pesanan
                            </button>
                        </form>
                    </div>
                    @endif
                                        @if($booking->status === 'dikonfirmasi')
                    <form method="POST" action="{{ route('pemandu.bookings.complete', $booking) }}" style="margin:0" id="complete-form-{{ $booking->id }}">
                        @csrf @method('PATCH')
                        <button type="submit"
                                style="padding:8px 14px;border:2px solid #f57f17;border-radius:8px;background:#fff8e1;color:#f57f17;font-size:12px;font-weight:800;cursor:pointer"
                                onclick="return openConfirm(this, 'Selesaikan Tour', 'Tandai tour ini sebagai selesai? Wisatawan akan diminta mengonfirmasi.', 'warning', 'Ya, Selesaikan', 'confirm-btn-warning', 'complete-form-{{ $booking->id }}')">
                            <i class="fa-solid fa-flag-checkered"></i> Selesaikan Tour
                        </button>
                    </form>
                    @endif
                </div>
            </div>
            @empty
            <div style="text-align:center;padding:48px 16px">
                <div style="font-size:48px;margin-bottom:12px">📭</div>
                <p style="font-size:15px;font-weight:600;color:#555;margin:0">Belum ada pesanan masuk</p>
                <p style="font-size:13px;color:#888;margin:4px 0 0">Pesanan dari wisatawan akan muncul di sini.</p>
            </div>
            @endforelse
        </div>
    </div>

</div>
@endsection