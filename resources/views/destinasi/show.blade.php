@extends('layouts.app')
@section('title', $destinasi->nama)

@section('content')
<div style="max-width:960px;margin:40px auto;padding:0 16px">
    @php
        $bolehKelolaDestinasi = auth()->check()
            && auth()->user()->isPemandu()
            && optional(auth()->user()->pemandu)->id === $destinasi->pemandu_id;
    @endphp

    {{-- Breadcrumb --}}
    <div style="margin-bottom:24px">
        <a href="{{ route('destinasi.index') }}" style="font-size:13px;color:#0d5c45;font-weight:600;text-decoration:none">
            <i class="fa-solid fa-arrow-left" style="margin-right:4px"></i> Kembali ke Destinasi
        </a>
    </div>

    {{-- Hero --}}
    <div style="background:#fff;border:3px solid #222;border-radius:16px;box-shadow:6px 6px 0 #222;overflow:hidden;margin-bottom:24px">
        {{-- Cover Image or Gradient --}}
        <div class="dest-hero-img" style="height:280px;background:{{ $destinasi->warna_bg }};display:flex;align-items:center;justify-content:center;font-size:72px;position:relative;overflow:hidden">
            @if($destinasi->thumbnail)
            <img src="{{ $destinasi->thumbnail }}" alt="{{ $destinasi->nama }}">
            @else
            <span class="dest-emoji-overlay" style="text-shadow:0 4px 12px rgba(0,0,0,0.4);font-size:80px">{{ $destinasi->emoji }}</span>
            @endif
        </div>

        {{-- Info --}}
        <div style="padding:24px">
            <div style="display:flex;align-items:flex-start;justify-content:space-between;flex-wrap:wrap;gap:12px;margin-bottom:16px">
                <div>
                    <h1 style="font-size:24px;font-weight:800;margin:0">{{ $destinasi->nama }}</h1>
                    <div style="font-size:14px;color:#888;margin-top:4px">
                        <i class="fa-solid fa-location-dot" style="margin-right:4px"></i>{{ $destinasi->lokasi }}
                        @if($destinasi->kabupaten) · {{ $destinasi->kabupaten }} @endif
                    </div>
                </div>
                <div style="text-align:right">
                    <div style="font-size:28px;font-weight:800;color:#222">
                        <span class="star" style="font-size:24px">★</span> {{ number_format($destinasi->rating, 1) }}
                    </div>
                    <div style="font-size:12px;color:#888;margin-top:2px">{{ $destinasi->jumlah_pemandu_aktif }} pemandu aktif</div>
                </div>
            </div>

            {{-- Tags --}}
            <div style="display:flex;gap:8px;flex-wrap:wrap;margin-bottom:16px">
                <span style="font-size:12px;padding:4px 14px;background:#e8f5e9;border:2px solid #2e7d32;border-radius:20px;font-weight:600;color:#2e7d32">{{ $destinasi->kategori }}</span>
                <span style="font-size:12px;padding:4px 14px;background:#fff3e0;border:2px solid #e65100;border-radius:20px;font-weight:600;color:#e65100">
                    <i class="fa-solid fa-user" style="margin-right:4px"></i>{{ $destinasi->jumlah_pemandu_aktif }} Pemandu Aktif
                </span>
                @if(!empty($destinasi->fotos) && count($destinasi->fotos) > 0)
                <span style="font-size:12px;padding:4px 14px;background:#e3f2fd;border:2px solid #1565c0;border-radius:20px;font-weight:600;color:#1565c0">
                    <i class="fa-solid fa-images" style="margin-right:4px"></i>{{ count($destinasi->fotos) }} Foto
                </span>
                @endif
            </div>

            @if($bolehKelolaDestinasi)
            <div style="display:flex;gap:10px;flex-wrap:wrap;margin-bottom:16px">
                <a href="{{ route('pemandu.destinasi.edit', $destinasi) }}"
                   style="min-width:140px;text-align:center;padding:9px 14px;border:2px solid #222;border-radius:8px;font-size:13px;font-weight:800;color:#222;text-decoration:none;background:#f5f5f5">
                    <i class="fa-solid fa-pen"></i> Edit Destinasi
                </a>
                <form method="POST" action="{{ route('pemandu.destinasi.destroy', $destinasi) }}"
                      onsubmit="return confirm('Yakin hapus destinasi {{ $destinasi->nama }}?')"
                      style="margin:0">
                    @csrf @method('DELETE')
                    <button type="submit"
                            style="min-width:140px;padding:9px 14px;border:2px solid #c62828;border-radius:8px;font-size:13px;font-weight:800;color:#c62828;background:#fff;cursor:pointer">
                        <i class="fa-solid fa-trash"></i> Hapus Destinasi
                    </button>
                </form>
            </div>
            @endif

            {{-- Deskripsi --}}
            @if($destinasi->deskripsi)
            <div style="font-size:14px;line-height:1.7;color:#444;margin-bottom:16px;padding:16px;background:#f9f9f9;border-radius:10px;border:1px solid #eee">
                {{ $destinasi->deskripsi }}
            </div>
            @endif
        </div>
    </div>

    {{-- Galeri Foto --}}
    @if(!empty($destinasi->fotos) && count($destinasi->fotos) > 0)
    <div style="background:#fff;border:3px solid #222;border-radius:16px;box-shadow:6px 6px 0 #222;padding:24px;margin-bottom:24px">
        <h2 style="font-size:16px;font-weight:800;margin:0 0 16px">
            <i class="fa-solid fa-images" style="margin-right:6px"></i>Galeri Foto
        </h2>
        <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(200px,1fr));gap:12px">
            @foreach($destinasi->fotos as $foto)
            <a href="{{ \Illuminate\Support\Facades\Storage::url($foto) }}" target="_blank"
               style="display:block;height:180px;border:2px solid #222;border-radius:12px;overflow:hidden;background:#f5f5f5">
                <img src="{{ \Illuminate\Support\Facades\Storage::url($foto) }}"
                     alt="Foto {{ $destinasi->nama }}"
                     style="width:100%;height:100%;object-fit:cover;display:block">
            </a>
            @endforeach
        </div>
    </div>
    @endif

    {{-- Pemandu Aktif --}}
    <div style="background:#fff;border:3px solid #222;border-radius:16px;box-shadow:6px 6px 0 #222;padding:24px;margin-bottom:24px">
        <h2 style="font-size:16px;font-weight:800;margin:0 0 16px">
            <i class="fa-solid fa-compass" style="margin-right:6px"></i>Pemandu Aktif di Destinasi Ini
            <span style="font-size:12px;font-weight:600;color:#888;margin-left:6px">({{ $pemanduAktif->count() }})</span>
        </h2>

        @if($pemanduAktif->count())
        <div style="display:grid;gap:12px">
            @foreach($pemanduAktif as $p)
            <div style="display:flex;align-items:center;gap:14px;padding:12px 16px;border:2px solid #e0e0e0;border-radius:12px;background:#fafafa;transition:all .15s"
                 onmouseover="this.style.borderColor='#0d5c45';this.style.background='#f0faf6'"
                 onmouseout="this.style.borderColor='#e0e0e0';this.style.background='#fafafa'">
                <a href="{{ route('pemandu.show', $p) }}" style="text-decoration:none;display:flex;align-items:center;gap:14px;flex:1;min-width:0;color:inherit">
                    <div style="width:44px;height:44px;border-radius:50%;background:{{ $p->warna_avatar }};color:#fff;display:flex;align-items:center;justify-content:center;font-weight:800;font-size:16px;flex-shrink:0;border:2px solid #222">
                        {{ $p->inisial }}
                    </div>
                    <div style="flex:1;min-width:0">
                        <div style="font-size:14px;font-weight:700;color:#222">{{ $p->user->name }}</div>
                        <div style="font-size:12px;color:#888;margin-top:2px">
                            {{ $p->spesialisasi }} · {{ $p->pengalaman_tahun }} th pengalaman
                        </div>
                    </div>
                    <div style="text-align:right;flex-shrink:0">
                        <div style="font-size:14px;font-weight:700;color:#F4CD0B">
                            <span class="star">★</span>{{ number_format($p->rating, 1) }}
                        </div>
                        <div style="font-size:11px;color:#888">{{ $p->jumlah_tour }} tour</div>
                        <div class="day-row" style="margin-top:6px">
                            @php $avail = is_array($p->ketersediaan) ? $p->ketersediaan : []; @endphp
                            @foreach(['S','S','R','K','J','S','M'] as $di => $day)
                            @php $tersedia = in_array($di, $avail); @endphp
                            <div class="day-dot {{ $tersedia ? 'avail' : 'busy' }}"
                                 title="{{ $tersedia ? 'Tersedia' : 'Tidak tersedia' }}">
                                {{ $day }}
                            </div>
                            @endforeach
                        </div>
                    </div>
                </a>
                @auth
                @if(auth()->user()->isWisatawan())
                <a href="{{ route('bookings.create', ['destination' => $destinasi->id, 'guide' => $p->id]) }}"
                   style="padding:8px 16px;border:2px solid #0d5c45;border-radius:8px;background:#0d5c45;color:#fff;font-size:12px;font-weight:700;text-decoration:none;white-space:nowrap;display:inline-flex;align-items:center;gap:5px">
                    <i class="fa-solid fa-calendar-check"></i> Booking
                </a>
                @endif
                @endauth
            </div>
            @endforeach
        </div>
        @else
        <div style="text-align:center;padding:24px 0;color:#888;font-size:13px">
            <i class="fa-solid fa-user-slash" style="font-size:24px;display:block;margin-bottom:8px"></i>
            Belum ada pemandu yang terdaftar aktif di destinasi ini.
        </div>
        @endif
    </div>

    {{-- Review Form --}}
    @auth
    @if(auth()->user()->isWisatawan() && $userReviewable)
    <div style="background:#fff;border:3px solid #222;border-radius:16px;box-shadow:6px 6px 0 #222;padding:24px;margin-bottom:24px">
        <h2 style="font-size:16px;font-weight:800;margin:0 0 16px">
            <i class="fa-solid fa-star" style="margin-right:6px"></i>Beri Rating & Ulasan
        </h2>

        @if($existingReview)
        <div style="text-align:center;padding:16px;background:#e8f5e9;border:2px solid #2e7d32;border-radius:12px;color:#2e7d32;font-size:14px;font-weight:600">
            <i class="fa-solid fa-check-circle" style="margin-right:6px"></i>
            Anda sudah memberikan ulasan untuk destinasi ini. Terima kasih!
        </div>
        @else
        <form method="POST" action="{{ route('reviews.store') }}">
            @csrf
            <input type="hidden" name="destination_id" value="{{ $destinasi->id }}">
            <input type="hidden" name="guide_id" value="{{ $userGuideReviewable }}">

            {{-- Star Rating (pure JS) --}}
            <div style="margin-bottom:20px">
                <label style="display:block;font-size:13px;font-weight:600;margin-bottom:8px">Rating</label>
                <div class="star-rating" style="display:flex;gap:6px;direction:ltr;font-size:34px">
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
            <div style="margin-bottom:20px">
                <label style="display:block;font-size:13px;font-weight:600;margin-bottom:8px">Komentar</label>
                <textarea name="comment" rows="4" placeholder="Bagaimana pengalaman Anda di destinasi ini?"
                          style="width:100%;padding:12px;border:2px solid #222;border-radius:10px;font-size:13px;resize:vertical;box-sizing:border-box">{{ old('comment') }}</textarea>
                @error('comment') <div style="font-size:12px;color:#c62828;margin-top:4px">{{ $message }}</div> @enderror
            </div>

            <button type="submit"
                    style="width:100%;padding:12px;background:#0d5c45;color:#fff;border:2px solid #222;border-radius:12px;font-size:14px;font-weight:700;cursor:pointer;display:inline-flex;align-items:center;justify-content:center;gap:8px">
                <i class="fa-solid fa-paper-plane"></i> Kirim Ulasan
            </button>
        </form>
        @endif
    </div>
    @endif
    @endauth

    {{-- Ulasan Pengunjung --}}
    <div style="background:#fff;border:3px solid #222;border-radius:16px;box-shadow:6px 6px 0 #222;padding:24px;margin-bottom:24px">
        <h2 style="font-size:16px;font-weight:800;margin:0 0 16px">
            <i class="fa-solid fa-comments" style="margin-right:6px"></i>Ulasan Pengunjung
            <span style="font-size:12px;font-weight:600;color:#888;margin-left:6px">({{ $reviews->count() }})</span>
        </h2>

        @if($reviews->count())
        <div style="display:grid;gap:14px">
            @foreach($reviews as $review)
            <div style="padding:16px;background:#f9f9f9;border:1px solid #eee;border-radius:12px">
                <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:8px">
                    <div style="display:flex;align-items:center;gap:10px">
                        <div style="width:36px;height:36px;border-radius:50%;background:#0d5c45;color:#fff;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:13px">
                            {{ strtoupper(substr($review->user->name ?? 'U', 0, 1)) }}
                        </div>
                        <div>
                            <div style="font-size:13px;font-weight:700">{{ $review->user->name ?? 'Pengunjung' }}</div>
                            <div style="font-size:11px;color:#aaa">{{ $review->created_at->format('d M Y') }}</div>
                        </div>
                    </div>
                    <div style="font-size:16px;color:#F4CD0B;letter-spacing:2px">
                        {{ str_repeat('★', $review->rating) }}{{ str_repeat('☆', 5 - $review->rating) }}
                    </div>
                </div>
                @if($review->comment)
                <div style="font-size:13px;color:#555;line-height:1.6">{{ $review->comment }}</div>
                @endif
            </div>
            @endforeach
        </div>
        @else
        <div style="text-align:center;padding:24px 0;color:#888;font-size:13px">
            <i class="fa-solid fa-star" style="font-size:24px;display:block;margin-bottom:8px"></i>
            Belum ada ulasan untuk destinasi ini.
        </div>
        @endif
    </div>

</div>
@endsection
