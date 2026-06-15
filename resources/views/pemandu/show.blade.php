@extends('layouts.app')
@section('title', $pemandu->user->name)

@section('content')

{{-- Header hijau --}}
<div class="pmnd-detail-header">
    <a href="{{ route('pemandu.index') }}" class="pmnd-detail-back">← Kembali ke Daftar Pemandu</a>
    <div class="pmnd-detail-actions">
        @auth
        @if(auth()->user()->isWisatawan())
        <form method="POST" action="{{ route('pemandu.booking', $pemandu) }}" style="display:inline">
            @csrf
            <button type="submit" class="btn-login"><i class="fa-solid fa-calendar-check" style="margin-right:6px"></i>Booking</button>
        </form>
        <a href="{{ route('chat.show', $pemandu->user) }}" class="btn-signin" style="text-decoration:none;display:inline-flex;align-items:center;gap:6px">
            <i class="fa-solid fa-comment-dots"></i>Hubungi
        </a>
        @elseif(auth()->user()->isPemandu())
        <a href="{{ route('chat.show', $pemandu->user) }}" class="btn-signin" style="text-decoration:none;display:inline-flex;align-items:center;gap:6px">
            <i class="fa-solid fa-comment-dots"></i>Hubungi
        </a>
        @endif
        @endauth
    </div>
</div>

<div class="pmnd-detail-body">

    {{-- Profile Card --}}
    <div class="pmnd-profile-card">
        <div class="pmnd-big-avatar" style="background:{{ $pemandu->warna_avatar ?? '#f5a623' }}">{{ $pemandu->inisial }}</div>
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

    {{-- Review Form (after completed tour) --}}
    @auth
    @if(auth()->user()->isWisatawan() && $userReviewable && $userCompletedBooking)
    <div class="detail-section-card" style="margin-bottom:16px">
        <div class="detail-section-title">
            <i class="fa-solid fa-star" style="margin-right:6px"></i>Beri Rating & Ulasan untuk {{ $pemandu->user->name }}
        </div>

        @if($existingReview)
        <div style="text-align:center;padding:16px;background:#e8f5e9;border:2px solid #2e7d32;border-radius:12px;color:#2e7d32;font-size:14px;font-weight:600">
            <i class="fa-solid fa-check-circle" style="margin-right:6px"></i>
            Anda sudah memberikan ulasan untuk tour ini. Terima kasih!
        </div>
        @elseif($reviewDestinasi)
        <div style="font-size:13px;color:#666;margin-bottom:16px">
            Beri rating dan ulasan untuk pengalaman tour Anda
            di <strong>{{ $reviewDestinasi->nama }}</strong>
            bersama <strong>{{ $pemandu->user->name }}</strong>.
        </div>
        <form method="POST" action="{{ route('reviews.store') }}">
            @csrf
            <input type="hidden" name="destination_id" value="{{ $reviewDestinasi->id }}">
            <input type="hidden" name="guide_id" value="{{ $pemandu->id }}">

            {{-- Star Rating --}}
            <div style="margin-bottom:16px">
                <label style="display:block;font-size:13px;font-weight:600;margin-bottom:8px">Rating</label>
                <div class="star-rating" style="display:flex;gap:6px;font-size:34px">
                    @for($i = 1; $i <= 5; $i++)
                    <span data-value="{{ $i }}"
                          style="cursor:pointer;color:#ddd;transition:color .15s"
                          onclick="starClick({{ $i }})"
                          onmouseover="starHover({{ $i }})"
                          onmouseout="starReset()">
                        ★
                    </span>
                    @endfor
                </div>
                <input type="hidden" name="rating" id="rating-input" value="" required>
                <div id="rating-text" style="font-size:12px;color:#888;margin-top:4px">Klik bintang untuk memberi rating</div>
                @error('rating') <div style="font-size:12px;color:#c62828;margin-top:4px">{{ $message }}</div> @enderror
            </div>

            {{-- Comment --}}
            <div style="margin-bottom:16px">
                <label style="display:block;font-size:13px;font-weight:600;margin-bottom:8px">Komentar</label>
                <textarea name="comment" rows="4" placeholder="Bagaimana pengalaman tour Anda dengan pemandu ini?"
                          style="width:100%;padding:12px;border:2px solid #222;border-radius:10px;font-size:13px;resize:vertical;box-sizing:border-box;">{{ old('comment') }}</textarea>
                @error('comment') <div style="font-size:12px;color:#c62828;margin-top:4px">{{ $message }}</div> @enderror
            </div>

            <button type="submit"
                    style="width:100%;padding:12px;background:#0d5c45;color:#fff;border:2px solid #222;border-radius:12px;font-size:14px;font-weight:700;cursor:pointer;display:inline-flex;align-items:center;justify-content:center;gap:8px">
                <i class="fa-solid fa-paper-plane"></i> Kirim Ulasan
            </button>
        </form>
        @else
        <div style="text-align:center;padding:16px;background:#fff8e1;border:2px solid #f57f17;border-radius:12px;color:#f57f17;font-size:13px">
            <i class="fa-solid fa-info-circle" style="margin-right:6px"></i>
            Mohon maaf, data destinasi tidak ditemukan untuk booking ini.
        </div>
        @endif
    </div>
    @endif
    @endauth

    {{-- Stats + Ketersediaan --}}
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
                <div class="stat-box">
                    <div class="stat-val">{{ $pemandu->pengalaman_tahun }}th</div>
                    <div class="stat-lbl">Pengalaman</div>
                </div>
            </div>
        </div>

        <div class="detail-section-card">
            <div class="detail-section-title">
                <i class="fa-solid fa-calendar-days" style="margin-right:6px"></i>Ketersediaan
            </div>
            <div class="avail-grid">
                @php
                    $hari = ['Sen','Sel','Rab','Kam','Jum','Sab','Min'];
                    $avail = $pemandu->ketersediaan ?? [];
                @endphp
                @foreach($hari as $i => $h)
                    @php $tersedia = in_array($i, $avail); @endphp
                    <div class="avail-day">
                        <div class="avail-day-label" style="{{ $tersedia ? 'color:#0d5c45;font-weight:700' : 'color:#bbb' }}">
                            {{ $h }}
                        </div>
                        <div class="avail-dot {{ $tersedia ? 'avail' : 'busy' }}">
                            {{ $tersedia ? '✓' : '–' }}
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="avail-legend">
                <div class="legend-item"><div class="legend-dot" style="background:#0d5c45"></div>Tersedia</div>
                <div class="legend-item"><div class="legend-dot" style="background:#e0e0e0"></div>Tidak Tersedia</div>
            </div>

            {{-- Tanggal spesifik tersedia (manual) --}}
            @php $tglTersedia = $pemandu->getTanggalTersediaList(); @endphp
            @if(!empty($tglTersedia))
            <div style="margin-top:14px;padding-top:12px;border-top:2px solid #eee">
                <div style="font-size:12px;font-weight:700;color:#0d5c45;margin-bottom:8px">
                    <i class="fa-solid fa-calendar-check" style="margin-right:4px"></i>
                    Tanggal Spesifik Tersedia
                </div>
                <div style="display:flex;gap:6px;flex-wrap:wrap">
                    @foreach($tglTersedia as $tgl)
                    <span style="padding:4px 10px;background:#e8f5e9;border:1px solid #0d5c45;border-radius:6px;font-size:11px;font-weight:600;color:#0d5c45">
                        {{ \Carbon\Carbon::parse($tgl)->format('d M Y') }}
                    </span>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
    </div>

    {{-- Paket Tour + Ulasan --}}
    <div class="detail-grid" style="margin-top:16px">

        <div class="detail-section-card">
            <div class="detail-section-title"><i class="fa-solid fa-map-location-dot" style="margin-right:6px"></i>Paket Tour Aktif</div>
            @forelse($pemandu->paketTours as $paket)
            <div class="jadwal-item">
                <span class="jadwal-time">{{ $paket->jam_mulai }} – {{ $paket->jam_selesai }}</span>
                <div class="jadwal-info">
                    <div class="jadwal-name">{{ $paket->nama }}</div>
                    <div class="jadwal-loc">
                        {{ $paket->destinasis->pluck('nama')->implode(', ') }}
                    </div>
                    <span class="jadwal-badge badge-{{ $paket->status === 'aktif' ? 'berjalan' : 'terjadwal' }}">
                        {{ ucfirst($paket->status) }}
                    </span>
                </div>
            </div>
            @empty
            <p style="font-size:13px;color:#aaa">Belum ada paket aktif.</p>
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
@endsection
