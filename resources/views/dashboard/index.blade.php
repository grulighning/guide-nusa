@extends('layouts.app')
@section('title', 'Dashboard')

@section('content')

{{-- ===== HERO ===== --}}
<div class="hero-section">
    <div class="hero-card">
        <h1>Temukan Pemandu<br>Wisata Terbaik</h1>
        <p>Jelajahi keindahan Nusantara bersama pemandu lokal berpengalaman.<br>Dari Sabang sampai Merauke, kami siap menemanimu.</p>
        <div class="hero-btns">
            <a href="{{ route('destinasi.index') }}" class="btn-primary">Jelajahi Destinasi</a>
            <a href="{{ route('pemandu.index') }}" class="btn-secondary">Temukan Pemandu</a>
        </div>
    </div>
</div>

@auth
    @if(auth()->user()->isPemandu())
    <div class="section-header">
        <h2>Pesanan Masuk</h2>
        <span class="section-lihat">{{ $jumlahBookingMenunggu }} menunggu</span>
    </div>
    <div class="bottom-card" style="margin-bottom:24px">
        <div class="bottom-card-header">
            <h3><i class="fa-solid fa-bell" style="margin-right:6px"></i>Wisatawan yang Memesan Jasa Anda</h3>
        </div>
        <div class="bottom-card-body">
            @forelse($bookingPemandu as $booking)
            <div class="tour-item" style="display:flex;gap:12px;align-items:flex-start">
                <div class="guide-avatar" style="background:#0d5c45;flex:0 0 38px">
                    {{ strtoupper(substr($booking->wisatawan->name ?? 'W', 0, 1)) }}
                </div>
                <div style="flex:1;min-width:0">
                    <div class="tour-name">{{ $booking->wisatawan->name ?? 'Wisatawan' }}</div>
                    <div class="tour-meta">
                        <span style="font-weight:700;
                            {{ $booking->status === 'menunggu' ? 'color:#e65100' : '' }}
                            {{ $booking->status === 'dikonfirmasi' ? 'color:#1565c0' : '' }}
                            {{ $booking->status === 'selesai' ? 'color:#2e7d32' : '' }}
                            {{ $booking->status === 'dibatalkan' ? 'color:#c62828' : '' }}
                            {{ $booking->status === 'menunggu_konfirmasi_selesai' ? 'color:#f57f17' : '' }}">
                            <span class="dot {{ $booking->status === 'dibatalkan' ? 'dot-gray' : ($booking->status === 'menunggu_konfirmasi_selesai' ? 'dot-gray' : 'dot-green') }}"></span>
                            {{ $booking->status === 'menunggu_konfirmasi_selesai' ? 'Menunggu konfirmasi wisatawan' : ucfirst($booking->status) }}
                        </span>
                        <span><i class="fa-solid fa-users" style="margin-right:4px"></i>{{ $booking->jumlah_peserta }} peserta</span>
                        <span><i class="fa-solid fa-calendar-days" style="margin-right:4px"></i>{{ $booking->tanggal_booking ? $booking->tanggal_booking->format('d M Y') : 'Tanggal fleksibel' }}</span>
                    </div>
                    @if($booking->paketTour)
                    <div class="guide-spec" style="margin-top:4px">
                        Paket: {{ $booking->paketTour->nama }}
                    </div>
                    @endif
                    @if($booking->catatan)
                    <div class="paket-desc" style="margin-top:6px">{{ $booking->catatan }}</div>
                    @endif
                </div>
                <div style="display:flex;flex-direction:column;align-items:flex-end;gap:8px;white-space:nowrap;flex-shrink:0">
                    <div class="guide-rating">{{ $booking->created_at->diffForHumans() }}</div>
                    @if($booking->status === 'menunggu')
                    <div style="display:flex;gap:6px;flex-wrap:wrap;justify-content:flex-end">
                        <form method="POST" action="{{ route('pemandu.bookings.confirm', $booking) }}" style="margin:0" id="dash-confirm-{{ $booking->id }}">
                            @csrf @method('PATCH')
                            <button type="submit"
                                    style="padding:7px 12px;border:2px solid #0d5c45;border-radius:8px;background:#0d5c45;color:#fff;font-size:12px;font-weight:800;cursor:pointer;box-shadow:2px 2px 0 #222"
                                    data-nama="{{ $booking->wisatawan->name }}"
                                    onclick="return openConfirm(this, 'Konfirmasi Pesanan', 'Terima pesanan dari ', 'info', 'Ya, Konfirmasi', 'confirm-btn-primary', 'dash-confirm-{{ $booking->id }}')">
                                <i class="fa-solid fa-check"></i> Konfirmasi
                            </button>
                        </form>
                        <form method="POST" action="{{ route('pemandu.bookings.reject', $booking) }}" style="margin:0" id="dash-reject-{{ $booking->id }}">
                            @csrf @method('PATCH')
                            <button type="submit"
                                    style="padding:7px 12px;border:2px solid #c62828;border-radius:8px;background:#fff;color:#c62828;font-size:12px;font-weight:800;cursor:pointer"
                                    data-nama="{{ $booking->wisatawan->name }}"
                                    onclick="return openConfirm(this, 'Tolak Pesanan', 'Tolak pesanan dari ', 'danger', 'Ya, Tolak', 'confirm-btn-danger', 'dash-reject-{{ $booking->id }}')">
                                <i class="fa-solid fa-xmark"></i> Tolak Pesanan
                            </button>
                        </form>
                    </div>
                    @endif
                    @if($booking->status === 'dikonfirmasi')
                    <form method="POST" action="{{ route('pemandu.bookings.complete', $booking) }}" style="margin:0" id="dash-complete-{{ $booking->id }}">
                        @csrf @method('PATCH')                            <button type="submit"
                                    style="padding:7px 12px;border:2px solid #f57f17;border-radius:8px;background:#fff8e1;color:#f57f17;font-size:12px;font-weight:800;cursor:pointer"
                                    onclick="return openConfirm(this, 'Selesaikan Tour', 'Tandai tour ini sebagai selesai? Wisatawan akan diminta mengonfirmasi.', 'warning', 'Ya, Selesaikan', 'confirm-btn-warning', 'dash-complete-{{ $booking->id }}')">
                            <i class="fa-solid fa-flag-checkered"></i> Selesaikan Tour
                        </button>
                    </form>
                    @endif
                </div>
            </div>
            @empty
            <p style="font-size:13px;color:#888;text-align:center;padding:16px 0">Belum ada wisatawan yang memesan jasa Anda.</p>
            @endforelse
        </div>
    </div>
    @endif

    @if(auth()->user()->isWisatawan())
    <div class="section-header">
        <h2>Status Pesanan Saya</h2>
        <span class="section-lihat">{{ $bookingWisatawan->count() }} pesanan</span>
    </div>
    <div class="bottom-card" style="margin-bottom:24px">
        <div class="bottom-card-header">
            <h3><i class="fa-solid fa-clipboard-check" style="margin-right:6px"></i>Informasi Konfirmasi dari Pemandu</h3>
        </div>
        <div class="bottom-card-body">
            @forelse($bookingWisatawan as $booking)
            <div class="tour-item" style="display:flex;gap:12px;align-items:flex-start">
                <div class="guide-avatar" style="background:{{ $booking->pemandu->warna_avatar ?? '#0d5c45' }};flex:0 0 38px">
                    {{ $booking->pemandu->inisial ?? strtoupper(substr($booking->pemandu->user->name ?? 'P', 0, 1)) }}
                </div>
                <div style="flex:1;min-width:0">
                    <div class="tour-name">{{ $booking->pemandu->user->name ?? 'Pemandu' }}</div>
                    <div class="tour-meta">
                        <span style="font-weight:700;
                            {{ $booking->status === 'menunggu' ? 'color:#e65100' : '' }}
                            {{ $booking->status === 'dikonfirmasi' ? 'color:#1565c0' : '' }}
                            {{ $booking->status === 'selesai' ? 'color:#2e7d32' : '' }}
                            {{ $booking->status === 'dibatalkan' ? 'color:#c62828' : '' }}
                            {{ $booking->status === 'menunggu_konfirmasi_selesai' ? 'color:#f57f17' : '' }}">
                            <span class="dot {{ in_array($booking->status, ['dibatalkan', 'menunggu', 'menunggu_konfirmasi_selesai']) ? 'dot-gray' : 'dot-green' }}"></span>
                            {{ $booking->status === 'dikonfirmasi' ? 'Diterima' : ($booking->status === 'dibatalkan' ? 'Ditolak' : ($booking->status === 'menunggu_konfirmasi_selesai' ? 'Menunggu konfirmasi Anda' : ucfirst($booking->status))) }}
                        </span>
                        <span><i class="fa-solid fa-users" style="margin-right:4px"></i>{{ $booking->jumlah_peserta }} peserta</span>
                        <span><i class="fa-solid fa-calendar-days" style="margin-right:4px"></i>{{ $booking->tanggal_booking ? $booking->tanggal_booking->format('d M Y') : 'Tanggal fleksibel' }}</span>
                    </div>
                    @if($booking->paketTour)
                    <div class="guide-spec" style="margin-top:4px">
                        Paket: {{ $booking->paketTour->nama }}
                    </div>
                    @endif
                    <div class="paket-desc" style="margin-top:6px">
                        @if($booking->status === 'dikonfirmasi')
                            Pesanan Anda diterima. Silakan lanjutkan komunikasi dengan pemandu untuk detail perjalanan.
                        @elseif($booking->status === 'dibatalkan')
                            Pesanan Anda ditolak oleh pemandu. Anda dapat memilih pemandu atau paket lain.
                        @elseif($booking->status === 'menunggu_konfirmasi_selesai')
                            Pemandu telah menandai tour selesai. Silakan konfirmasi jika tour benar-benar telah selesai.
                        @else
                            Pesanan Anda sedang menunggu konfirmasi dari pemandu.
                        @endif
                    </div>
                </div>
                <div style="display:flex;flex-direction:column;align-items:flex-end;gap:8px;white-space:nowrap;flex-shrink:0">
                    <div class="guide-rating">{{ $booking->updated_at->diffForHumans() }}</div>
                    @if($booking->status === 'menunggu_konfirmasi_selesai')
                    <form method="POST" action="{{ route('user.bookings.confirm-complete', $booking) }}" style="margin:0" id="user-complete-{{ $booking->id }}">
                        @csrf @method('PATCH')
                        <button type="submit"
                                style="padding:7px 12px;border:2px solid #2e7d32;border-radius:8px;background:#2e7d32;color:#fff;font-size:12px;font-weight:800;cursor:pointer;box-shadow:2px 2px 0 #222"
                                data-nama="{{ $booking->pemandu->user->name }}"
                                onclick="return openConfirm(this, 'Konfirmasi Selesai', 'Konfirmasi bahwa tour bersama ', 'success', 'Ya, Selesai', 'confirm-btn-primary', 'user-complete-{{ $booking->id }}')">
                            <i class="fa-solid fa-check-circle"></i> Konfirmasi Selesai
                        </button>
                    </form>
                    @endif
                </div>
            </div>
            @empty
            <p style="font-size:13px;color:#888;text-align:center;padding:16px 0">Belum ada pesanan. Pilih pemandu atau paket tour untuk mulai booking.</p>
            @endforelse
        </div>
    </div>

    {{-- Ulasan Saya (preview) --}}
    <div class="section-header">
        <h2>Ulasan Saya</h2>
        <a href="{{ route('dashboard.reviews') }}" class="section-lihat">
            Lihat Semua @if($userReviews->count() > 2)({{ $userReviews->count() }})@endif →
        </a>
    </div>
    <div class="bottom-section" style="margin-bottom:24px">
        @forelse($userReviews as $review)
        <div class="bottom-card" style="flex:1;min-width:250px">
            <div class="bottom-card-body" style="padding:16px">
                <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:10px">
                    <div style="display:flex;align-items:center;gap:8px">
                        <div style="width:28px;height:28px;border-radius:50%;background:{{ $review->guide->warna_avatar ?? '#0d5c45' }};color:#fff;display:flex;align-items:center;justify-content:center;font-weight:800;font-size:11px;flex-shrink:0;border:1px solid #222">
                            {{ $review->guide->inisial ?? strtoupper(substr($review->guide->user->name ?? 'P', 0, 1)) }}
                        </div>
                        <div style="font-size:12px;font-weight:700">{{ $review->guide->user->name ?? 'Pemandu' }}</div>
                    </div>
                    <div style="font-size:13px;color:#F4CD0B;letter-spacing:1px">
                        {{ str_repeat('★', $review->rating) }}{{ str_repeat('☆', 5 - $review->rating) }}
                    </div>
                </div>
                @if($review->comment)
                <div style="font-size:12px;color:#555;line-height:1.6;margin-bottom:8px;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden">
                    {{ $review->comment }}
                </div>
                @endif
                <div style="font-size:11px;color:#aaa;display:flex;align-items:center;gap:8px">
                    @if($review->destination)
                    <span style="color:#0d5c45;font-weight:600">{{ $review->destination->nama }}</span>
                    <span>·</span>
                    @endif
                    <span>{{ $review->created_at->format('d M Y') }}</span>
                </div>
            </div>
        </div>
        @empty
        <div class="bottom-card" style="flex:1">
            <div class="bottom-card-body" style="text-align:center;padding:24px 16px;color:#888;font-size:13px">
                <i class="fa-solid fa-star" style="font-size:20px;display:block;margin-bottom:8px;color:#ddd"></i>
                Belum ada ulasan. Selesaikan tour untuk memberi rating!
            </div>
        </div>
        @endforelse
    </div>
    @endif
@endauth

{{-- ===== DESTINASI POPULER ===== --}}
<div class="section-header">
    <h2>Destinasi Populer</h2>
    <a href="{{ route('destinasi.index') }}" class="section-lihat">Lihat Semua →</a>
</div>
<div class="dest-grid">
    @foreach($destinasiPopuler as $dest)
    <a href="{{ route('destinasi.index', ['kategori' => strtolower($dest->kategori)]) }}"
       class="dest-card" style="text-decoration:none">
        <div class="dest-img-placeholder" style="background:{{ $dest->warna_bg }}">
            @if($dest->thumbnail)
            <img src="{{ $dest->thumbnail }}" alt="{{ $dest->nama }}">
            @else
            <span class="dest-emoji-overlay">{{ $dest->emoji }}</span>
            @endif
        </div>
    <div class="dest-card-body">
        <div class="dest-card-name">{{ $dest->nama }}</div>
        <div class="dest-card-meta">
            <span class="pemandu-count">{{ $dest->jumlah_pemandu_aktif }}+ pemandu</span>
            <span class="rating"><span class="star">★</span>{{ number_format($dest->rating, 1) }}</span>
        </div>
    </div>
    </a>
    @endforeach
</div>

{{-- ===== BOTTOM SECTION: Tour Aktif + Pemandu Terbaik ===== --}}
<div class="bottom-section">

    {{-- Tour Aktif --}}
    <div class="bottom-card">
        <div class="bottom-card-header">
            <h3><i class="fa-solid fa-map-location-dot" style="margin-right:6px"></i>Tour Aktif</h3>
        </div>
        <div class="bottom-card-body">
            @forelse($tourAktif as $tour)
            <div class="tour-item">
                <div class="tour-name">{{ $tour->nama }}</div>
                <div class="tour-meta">
                    <span>
                        <span class="dot {{ $tour->status === 'aktif' ? 'dot-green' : 'dot-gray' }}"></span>
                        {{ ucfirst($tour->status) }}
                    </span>
                    <span><i class="fa-solid fa-calendar-days" style="margin-right:4px"></i>{{ $tour->durasi }}</span>
                    <span><i class="fa-solid fa-users" style="margin-right:4px"></i>Maks {{ $tour->max_peserta }}</span>
                </div>
                <div class="avatar-row">
                    @foreach($tour->destinasis->take(3) as $d)
                    <div class="avatar" style="background:#0d5c45;font-size:8px;width:auto;padding:0 6px;border-radius:8px">
                        {{ Str::limit($d->nama, 10) }}
                    </div>
                    @endforeach
                </div>
            </div>
            @empty
            <p style="font-size:13px;color:#888;text-align:center;padding:16px 0">Belum ada tour aktif.</p>
            @endforelse
        </div>
    </div>

    {{-- Pemandu Terbaik --}}
    <div class="bottom-card">
        <div class="bottom-card-header">
            <h3><i class="fa-solid fa-compass" style="margin-right:6px"></i>Pemandu Terbaik</h3>
        </div>
        <div class="bottom-card-body">
            @forelse($pemanduTerbaik as $pemandu)
            <a href="{{ route('pemandu.show', $pemandu) }}" class="guide-item" style="text-decoration:none">
                <div class="guide-avatar" style="background:{{ $pemandu->warna_avatar }}">
                    {{ $pemandu->inisial }}
                </div>
                <div class="guide-info">
                    <div class="guide-name">{{ $pemandu->user->name }}</div>
                    <div class="guide-spec">{{ $pemandu->spesialisasi }}</div>
                    <div class="guide-exp">
                        <span class="dot dot-gray"></span>
                        {{ $pemandu->pengalaman_tahun }} Tahun Pengalaman
                    </div>
                </div>
                <div class="guide-rating"><span class="star">★</span>{{ number_format($pemandu->rating, 1) }}</div>
            </a>
            @empty
            <p style="font-size:13px;color:#888;text-align:center;padding:16px 0">Belum ada pemandu.</p>
            @endforelse
        </div>
    </div>

</div>

@endsection
