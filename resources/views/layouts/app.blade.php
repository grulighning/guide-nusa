<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Guide Nusa') — Guide Nusa</title>

    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

    {{-- Vite assets --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @stack('styles')
</head>
<body>

{{-- ===== NAVBAR ===== --}}
<nav class="navbar">
    <a href="{{ route('dashboard') }}" class="nav-logo">GUIDE NUSA</a>

    <div class="nav-links">
        <a href="{{ route('dashboard') }}"
           class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            Dashboard
        </a>
        <a href="{{ route('destinasi.index') }}"
           class="nav-link {{ request()->routeIs('destinasi.*') ? 'active' : '' }}">
            Destinasi
        </a>
        <a href="{{ route('paket.index') }}"
           class="nav-link {{ request()->routeIs('paket.*') ? 'active' : '' }}">
            Paket Tour
        </a>
        <a href="{{ route('pemandu.index') }}"
           class="nav-link {{ request()->routeIs('pemandu.index') || request()->routeIs('pemandu.show') ? 'active' : '' }}">
            Pemandu
        </a>
        @auth
        @if(auth()->user()->isWisatawan())
        <a href="{{ route('dashboard.reviews') }}"
           class="nav-link {{ request()->routeIs('dashboard.reviews') ? 'active' : '' }}">
            <i class="fa-solid fa-star" style="margin-right:4px"></i>Ulasan Saya
        </a>
        @endif
        @if(auth()->user()->isPemandu())
        <a href="{{ route('pemandu.profile') }}"
           class="nav-link {{ request()->routeIs('pemandu.profile') ? 'active' : '' }}">
            <i class="fa-solid fa-user" style="margin-right:4px"></i>Profil Saya
        </a>
        @endif
        <a href="{{ route('chat.index') }}"
           class="nav-link {{ request()->routeIs('chat.*') ? 'active' : '' }}">
            <i class="fa-solid fa-comment-dots" style="margin-right:4px"></i>Pesan
            @php $unreadChat = \App\Http\Controllers\ChatController::unreadCount(); @endphp
            @if($unreadChat > 0)
            <span style="display:inline-flex;align-items:center;justify-content:center;width:20px;height:20px;border-radius:50%;background:#c62828;color:#fff;font-size:10px;font-weight:800;margin-left:4px">
                {{ $unreadChat > 9 ? '9+' : $unreadChat }}
            </span>
            @endif
        </a>
        @endauth
    </div>

    <div class="nav-actions">
        @guest
            <a href="{{ route('register') }}" class="btn-signin">Daftar</a>
            <a href="{{ route('login') }}" class="btn-login">Masuk</a>
        @else
            <span style="color:rgba(255,255,255,0.85);font-size:13px;margin-right:8px">
                <i class="fa-solid fa-hand-wave" style="margin-right:6px"></i>{{ auth()->user()->name }}
            </span>
            <form method="POST" action="{{ route('logout') }}" style="display:inline">
                @csrf
                <button type="submit" class="btn-signin">Keluar</button>
            </form>
        @endguest
    </div>
</nav>

{{-- ===== TOAST NOTIFICATION ===== --}}
<div class="toast-container" id="toast-container"></div>

{{-- ===== CONFIRM MODAL (pure JS, no Alpine) ===== --}}
<div id="confirm-modal-container" style="display:none;position:fixed;inset:0;z-index:8000;align-items:center;justify-content:center;padding:20px;background:rgba(0,0,0,0.5);backdrop-filter:blur(4px)"></div>

{{-- ===== CONTENT ===== --}}
@yield('content')


{{-- Global functions for modal & toast --}}
<script>
// Show confirmation modal - pure JS, no Alpine dependency
// Parameters: buttonEl, title, messagePrefix, icon, confirmText, confirmClass, formId
// The name comes from buttonEl.dataset.nama (safe, no quoting issues)
function openConfirm(btn, title, messagePrefix, icon, confirmText, confirmClass, formId) {
    const container = document.getElementById('confirm-modal-container');
    if (!container) return false;

    const nama = btn.dataset.nama || '';
    const message = messagePrefix + nama + (nama ? '?' : '');

    const iconMap = {
        warning: { bg: '#fff8e1', color: '#f57f17', icon: 'fa-triangle-exclamation' },
        success: { bg: '#e8f5e9', color: '#2e7d32', icon: 'fa-check-circle' },
        info: { bg: '#e3f2fd', color: '#1565c0', icon: 'fa-circle-info' },
        danger: { bg: '#fbe9e7', color: '#c62828', icon: 'fa-ban' }
    };
    const ic = iconMap[icon] || iconMap.warning;

    const confirmBg = confirmClass === 'confirm-btn-danger' ? '#c62828' :
                      (confirmClass === 'confirm-btn-warning' ? '#f57f17' : '#0d5c45');

    container.innerHTML = `
        <div style="background:#fff;border-radius:16px;max-width:420px;width:100%;box-shadow:0 20px 60px rgba(0,0,0,0.2);overflow:hidden;border:2px solid #222;animation:modalPop 0.3s cubic-bezier(0.22,1,0.36,1)">
            <div style="display:flex;align-items:center;gap:14px;padding:24px 24px 0">
                <div style="width:48px;height:48px;border-radius:14px;display:flex;align-items:center;justify-content:center;font-size:22px;flex-shrink:0;background:${ic.bg};color:${ic.color}">
                    <i class="fa-solid ${ic.icon}"></i>
                </div>
                <div style="font-size:17px;font-weight:800;color:#1a1a1a;line-height:1.3">${escapeHtml(title)}</div>
            </div>
            <div style="padding:12px 24px 0;font-size:14px;color:#666;line-height:1.6">${escapeHtml(message)}</div>
            <div style="display:flex;gap:10px;justify-content:flex-end;padding:20px 24px 24px">
                <button onclick="closeConfirmModal()"
                        style="padding:10px 22px;border-radius:10px;font-size:13px;font-weight:700;cursor:pointer;font-family:inherit;border:2px solid #ddd;background:#fff;color:#666;display:inline-flex;align-items:center;gap:6px">
                    <i class="fa-solid fa-xmark"></i> Batal
                </button>
                <button onclick="submitConfirmForm('${formId}')"
                        style="padding:10px 22px;border-radius:10px;font-size:13px;font-weight:700;cursor:pointer;font-family:inherit;border:2px solid ${confirmBg};background:${confirmBg};color:#fff;box-shadow:2px 2px 0 #222;display:inline-flex;align-items:center;gap:6px">
                    <i class="fa-solid fa-check"></i> ${escapeHtml(confirmText)}
                </button>
            </div>
        </div>
    `;

    container.style.display = 'flex';
    container.onclick = function(e) { if (e.target === this) closeConfirmModal(); };
    document.body.style.overflow = 'hidden';
    return false;
}

function closeConfirmModal() {
    const container = document.getElementById('confirm-modal-container');
    if (!container) return;
    container.style.display = 'none';
    container.innerHTML = '';
    document.body.style.overflow = '';
}

function submitConfirmForm(formId) {
    closeConfirmModal();
    if (formId) {
        document.getElementById(formId).submit();
    }
}

function showToast(title, message, type = 'success') {
    const container = document.getElementById('toast-container');
    if (!container) return;

    const toast = document.createElement('div');
    toast.className = 'toast toast-' + type;
    toast.innerHTML = `
        <div class="toast-icon">
            <i class="fa-solid ${type === 'success' ? 'fa-check' : 'fa-xmark'}"></i>
        </div>
        <div class="toast-body">
            <div class="toast-title">${escapeHtml(title)}</div>
            <div class="toast-message">${escapeHtml(message)}</div>
        </div>
        <button class="toast-close" onclick="this.closest('.toast').classList.add('removing'); setTimeout(() => this.closest('.toast').remove(), 300)">
            <i class="fa-solid fa-times"></i>
        </button>
        <div class="toast-progress"></div>
    `;
    container.appendChild(toast);

    setTimeout(() => {
        if (toast.parentNode) {
            toast.classList.add('removing');
            setTimeout(() => toast.remove(), 300);
        }
    }, 5000);
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

// Star Rating functions (used on destinasi show page)
let starRatingValue = 0;

function ratingStars() {
    return document.querySelectorAll('.star-rating span[data-value]');
}

function starClick(value) {
    starRatingValue = (starRatingValue === value) ? 0 : value;
    document.getElementById('rating-input').value = starRatingValue;
    updateStarDisplay();
}

function starHover(value) {
    ratingStars().forEach(function(star) {
        var v = parseInt(star.getAttribute('data-value'));
        star.style.color = v <= value ? '#F4CD0B' : '#ddd';
    });
}

function starReset() {
    updateStarDisplay();
}

function updateStarDisplay() {
    ratingStars().forEach(function(star) {
        var v = parseInt(star.getAttribute('data-value'));
        star.style.color = v <= starRatingValue ? '#F4CD0B' : '#ddd';
    });
    var text = document.getElementById('rating-text');
    if (text) {
        text.textContent = starRatingValue ? starRatingValue + ' dari 5 bintang' : 'Klik bintang untuk memberi rating';
    }
}

@if(session('success'))
    showToast('Berhasil', {!! json_encode(session('success')) !!}, 'success');
@endif
@if(session('error'))
    showToast('Gagal', {!! json_encode(session('error')) !!}, 'error');
@endif
</script>
@stack('scripts')
</body>
</html>