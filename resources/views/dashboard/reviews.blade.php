@extends('layouts.app')
@section('title', 'Ulasan Saya')

@section('content')
<div style="max-width:960px;margin:40px auto;padding:0 16px">

    {{-- Header --}}
    <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;margin-bottom:24px">
        <div>
            <h1 style="font-size:24px;font-weight:800;margin:0;display:flex;align-items:center;gap:10px">
                <i class="fa-solid fa-star" style="color:#F4CD0B"></i> Ulasan Saya
            </h1>
            <p style="font-size:14px;color:#888;margin:6px 0 0">
                Semua rating dan ulasan yang pernah Anda berikan
            </p>
        </div>
        <a href="{{ route('dashboard') }}"
           style="padding:8px 16px;border:2px solid #222;border-radius:10px;font-size:13px;font-weight:700;color:#222;text-decoration:none;background:#f5f5f5;display:inline-flex;align-items:center;gap:6px">
            <i class="fa-solid fa-arrow-left"></i> Kembali ke Dashboard
        </a>
    </div>

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

    {{-- Daftar Ulasan --}}
    @forelse($reviews as $review)
    <div class="review-card" id="review-{{ $review->id }}"
         style="background:#fff;border:3px solid #222;border-radius:16px;box-shadow:6px 6px 0 #222;padding:24px;margin-bottom:16px;transition:all .15s"
         onmouseover="this.style.boxShadow='4px 4px 0 #222'"
         onmouseout="this.style.boxShadow='6px 6px 0 #222'">

        {{-- Header: Avatar + Nama + Rating --}}
        <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;margin-bottom:14px">
            <div style="display:flex;align-items:center;gap:12px">
                <div style="width:42px;height:42px;border-radius:50%;background:#0d5c45;color:#fff;display:flex;align-items:center;justify-content:center;font-weight:800;font-size:15px;flex-shrink:0;border:2px solid #222">
                    {{ strtoupper(substr($review->user->name ?? 'U', 0, 1)) }}
                </div>
                <div>
                    <div style="font-size:14px;font-weight:700">{{ $review->user->name ?? 'Pengunjung' }}</div>
                    <div style="font-size:11px;color:#aaa">
                        <i class="fa-solid fa-clock" style="margin-right:4px"></i>
                        {{ $review->created_at->format('d M Y, H:i') }}
                    </div>
                </div>
            </div>
            <div class="review-rating-display" style="font-size:18px;color:#F4CD0B;letter-spacing:2px">
                {{ str_repeat('★', $review->rating) }}{{ str_repeat('☆', 5 - $review->rating) }}
            </div>
        </div>

        {{-- Comment --}}
        <div class="review-comment-display">
        @if($review->comment)
        <div style="font-size:14px;color:#444;line-height:1.7;margin-bottom:14px;padding:14px 16px;background:#f9f9f9;border-radius:10px;border:1px solid #eee">
            <i class="fa-solid fa-quote-left" style="color:#ccc;margin-right:6px;font-size:12px"></i>
            {{ $review->comment }}
        </div>
        @endif
        </div>

        {{-- Footer: Links + Actions --}}
        <div style="display:flex;flex-wrap:wrap;gap:10px;padding-top:14px;border-top:2px solid #eee">
            <div style="display:flex;flex-wrap:wrap;gap:10px;flex:1">
            @if($review->destination)
            <a href="{{ route('destinasi.show', $review->destination) }}"
               style="display:inline-flex;align-items:center;gap:6px;padding:6px 14px;background:#e8f5e9;border:2px solid #2e7d32;border-radius:20px;font-size:12px;font-weight:700;color:#2e7d32;text-decoration:none">
                <i class="fa-solid fa-location-dot"></i>
                {{ $review->destination->nama }}
            </a>
            @endif
            @if($review->guide)
            <a href="{{ route('pemandu.show', $review->guide) }}"
               style="display:inline-flex;align-items:center;gap:6px;padding:6px 14px;background:#e3f2fd;border:2px solid #1565c0;border-radius:20px;font-size:12px;font-weight:700;color:#1565c0;text-decoration:none">
                <i class="fa-solid fa-compass"></i>
                {{ $review->guide->user->name ?? 'Pemandu' }}
            </a>
            @endif
            <span style="display:inline-flex;align-items:center;gap:6px;padding:6px 14px;background:#f5f5f5;border:2px solid #ddd;border-radius:20px;font-size:12px;font-weight:600;color:#888">
                <i class="fa-regular fa-calendar"></i>
                {{ $review->created_at->format('M Y') }}
            </span>
            </div>

            {{-- Action Buttons --}}
            <div style="display:flex;gap:8px;align-items:center">
                <button onclick="openEditModal({{ $review->id }}, {{ $review->rating }}, {{ json_encode($review->comment) }})"
                        style="padding:6px 14px;border:2px solid #1565c0;border-radius:8px;background:#e3f2fd;color:#1565c0;font-size:12px;font-weight:700;cursor:pointer;display:inline-flex;align-items:center;gap:5px;font-family:inherit">
                    <i class="fa-solid fa-pen"></i> Edit
                </button>
                <form method="POST" action="{{ route('reviews.destroy', $review) }}" style="margin:0;display:inline" id="delete-form-{{ $review->id }}">
                    @csrf @method('DELETE')
                    <button type="submit"
                            onclick="return openConfirm(this, 'Hapus Ulasan', 'Yakin ingin menghapus ulasan ini?', 'danger', 'Ya, Hapus', 'confirm-btn-danger', 'delete-form-{{ $review->id }}')"
                            style="padding:6px 14px;border:2px solid #c62828;border-radius:8px;background:#fff;color:#c62828;font-size:12px;font-weight:700;cursor:pointer;display:inline-flex;align-items:center;gap:5px;font-family:inherit">
                        <i class="fa-solid fa-trash"></i> Hapus
                    </button>
                </form>
            </div>
        </div>
    </div>
    @empty
    {{-- Empty State --}}
    <div style="background:#fff;border:3px solid #222;border-radius:16px;box-shadow:6px 6px 0 #222;padding:48px 24px;text-align:center">
        <div style="font-size:48px;color:#ddd;margin-bottom:16px">
            <i class="fa-solid fa-star"></i>
        </div>
        <h2 style="font-size:18px;font-weight:800;color:#555;margin:0 0 8px">Belum Ada Ulasan</h2>
        <p style="font-size:14px;color:#888;margin:0 0 24px;max-width:400px;margin-left:auto;margin-right:auto">
            Anda belum memberikan ulasan apapun. Selesaikan tour dengan pemandu untuk bisa memberi rating dan ulasan!
        </p>
        <div style="display:flex;gap:12px;justify-content:center;flex-wrap:wrap">
            <a href="{{ route('destinasi.index') }}"
               style="padding:12px 24px;background:#0d5c45;color:#fff;border:2px solid #222;border-radius:12px;font-size:14px;font-weight:700;text-decoration:none;display:inline-flex;align-items:center;gap:8px;box-shadow:3px 3px 0 #222">
                <i class="fa-solid fa-compass"></i> Jelajahi Destinasi
            </a>
            <a href="{{ route('pemandu.index') }}"
               style="padding:12px 24px;background:#fff;color:#222;border:2px solid #222;border-radius:12px;font-size:14px;font-weight:700;text-decoration:none;display:inline-flex;align-items:center;gap:8px">
                <i class="fa-solid fa-user"></i> Temukan Pemandu
            </a>
        </div>
    </div>
    @endforelse

    {{-- Summary Card --}}
    @if($reviews->count() > 0)
    <div style="background:#fff;border:3px solid #222;border-radius:16px;box-shadow:6px 6px 0 #222;padding:20px 24px;margin-top:16px;display:flex;flex-wrap:wrap;align-items:center;justify-content:space-between;gap:12px">
        <div style="display:flex;align-items:center;gap:12px">
            <div style="width:44px;height:44px;border-radius:12px;background:#0d5c45;color:#fff;display:flex;align-items:center;justify-content:center;font-size:20px">
                <i class="fa-solid fa-chart-simple"></i>
            </div>
            <div>
                <div style="font-size:12px;color:#888">Total Ulasan</div>
                <div style="font-size:20px;font-weight:800">{{ $reviews->count() }} ulasan</div>
            </div>
        </div>
        <div style="display:flex;align-items:center;gap:12px">
            <div style="width:44px;height:44px;border-radius:12px;background:#F4CD0B;color:#fff;display:flex;align-items:center;justify-content:center;font-size:20px">
                <i class="fa-solid fa-star"></i>
            </div>
            <div>
                <div style="font-size:12px;color:#888">Rating Rata-rata</div>
                <div style="font-size:20px;font-weight:800">
                    {{ number_format($reviews->avg('rating'), 1) }}
                    <span style="color:#F4CD0B;font-size:14px">★</span>
                </div>
            </div>
        </div>
        <a href="{{ route('dashboard') }}"
           style="padding:10px 20px;background:#0d5c45;color:#fff;border:2px solid #222;border-radius:10px;font-size:13px;font-weight:700;text-decoration:none;display:inline-flex;align-items:center;gap:6px;box-shadow:2px 2px 0 #222">
            <i class="fa-solid fa-arrow-left"></i> Dashboard
        </a>
    </div>
    @endif

</div>

{{-- ===== EDIT REVIEW MODAL ===== --}}
<div id="edit-review-modal" style="display:none;position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.5);z-index:9999;align-items:center;justify-content:center;padding:20px;box-sizing:border-box">
    <div style="background:#fff;border-radius:14px;width:100%;max-width:500px;max-height:90vh;overflow-y:auto;padding:24px;box-shadow:0 10px 30px rgba(0,0,0,0.3)">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px">
            <h2 style="font-size:18px;font-weight:800;margin:0;display:flex;align-items:center;gap:8px">
                <i class="fa-solid fa-pen" style="color:#1565c0"></i>
                Edit Ulasan
            </h2>
            <button type="button" onclick="closeEditModal()" style="border:none;background:transparent;font-size:24px;cursor:pointer;color:#888;padding:0 8px">
                &times;
            </button>
        </div>

        <form method="POST" action="" id="edit-review-form">
            @csrf
            @method('PUT')

            {{-- Star Rating --}}
            <div style="margin-bottom:20px">
                <label style="display:block;font-size:13px;font-weight:600;margin-bottom:8px">Rating</label>
                <div class="edit-star-rating" style="display:flex;gap:6px;font-size:34px">
                    @for($i = 1; $i <= 5; $i++)
                    <span data-value="{{ $i }}"
                          style="cursor:pointer;color:#ddd;transition:color .15s"
                          onclick="editStarClick({{ $i }})"
                          onmouseover="editStarHover({{ $i }})"
                          onmouseout="editStarReset()">
                        ★
                    </span>
                    @endfor
                </div>
                <input type="hidden" name="rating" id="edit-rating-input" value="" required>
                <div id="edit-rating-text" style="font-size:12px;color:#888;margin-top:4px">Klik bintang untuk memberi rating</div>
            </div>

            {{-- Comment --}}
            <div style="margin-bottom:20px">
                <label style="display:block;font-size:13px;font-weight:600;margin-bottom:8px">Komentar</label>
                <textarea name="comment" id="edit-comment-input" rows="4" placeholder="Bagaimana pengalaman Anda?"
                          style="width:100%;padding:12px;border:2px solid #222;border-radius:10px;font-size:13px;resize:vertical;box-sizing:border-box"></textarea>
            </div>

            <div style="display:flex;gap:12px">
                <button type="button" onclick="closeEditModal()"
                        style="flex:1;padding:12px;background:#f5f5f5;color:#333;border:2px solid #ddd;border-radius:10px;font-size:14px;font-weight:700;cursor:pointer;font-family:inherit">
                    Batal
                </button>
                <button type="submit"
                        style="flex:2;padding:12px;background:#1565c0;color:#fff;border:2px solid #222;border-radius:10px;font-size:14px;font-weight:700;cursor:pointer;font-family:inherit;box-shadow:2px 2px 0 #222;display:inline-flex;align-items:center;justify-content:center;gap:6px">
                    <i class="fa-solid fa-floppy-disk"></i> Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ===== SCRIPT for Edit Modal ===== --}}
<script>
let editStarRatingValue = 0;

function editStarElements() {
    return document.querySelectorAll('.edit-star-rating span[data-value]');
}

function editStarClick(value) {
    editStarRatingValue = (editStarRatingValue === value) ? 0 : value;
    document.getElementById('edit-rating-input').value = editStarRatingValue;
    updateEditStarDisplay();
}

function editStarHover(value) {
    editStarElements().forEach(function(star) {
        var v = parseInt(star.getAttribute('data-value'));
        star.style.color = v <= value ? '#F4CD0B' : '#ddd';
    });
}

function editStarReset() {
    updateEditStarDisplay();
}

function updateEditStarDisplay() {
    editStarElements().forEach(function(star) {
        var v = parseInt(star.getAttribute('data-value'));
        star.style.color = v <= editStarRatingValue ? '#F4CD0B' : '#ddd';
    });
    var text = document.getElementById('edit-rating-text');
    if (text) {
        text.textContent = editStarRatingValue ? editStarRatingValue + ' dari 5 bintang' : 'Klik bintang untuk memberi rating';
    }
}

function openEditModal(reviewId, currentRating, currentComment) {
    var modal = document.getElementById('edit-review-modal');

    // Set form action
    var form = document.getElementById('edit-review-form');
    form.action = '/reviews/' + reviewId;

    // Set rating
    editStarRatingValue = currentRating;
    document.getElementById('edit-rating-input').value = currentRating;
    updateEditStarDisplay();

    // Set comment
    document.getElementById('edit-comment-input').value = currentComment || '';

    // Show modal
    modal.style.display = 'flex';
    document.body.style.overflow = 'hidden';
}

function closeEditModal() {
    var modal = document.getElementById('edit-review-modal');
    modal.style.display = 'none';
    document.body.style.overflow = '';
}

// Close modal on background click
document.addEventListener('DOMContentLoaded', function() {
    var modal = document.getElementById('edit-review-modal');
    if (modal) {
        modal.addEventListener('click', function(e) {
            if (e.target === this) closeEditModal();
        });
    }
});
</script>

@endsection
