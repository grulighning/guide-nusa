@extends('layouts.app')
@section('title', 'Pesan dengan ' . $user->name)

@section('content')
<div style="max-width:800px;margin:0 auto;padding:0 16px;height:calc(100vh - 180px);display:flex;flex-direction:column">

    {{-- Header --}}
    <div style="display:flex;align-items:center;gap:12px;padding:16px 0;border-bottom:3px solid #222;margin-bottom:0;flex-shrink:0;background:#fff;position:sticky;top:0;z-index:10">
        <a href="{{ route('chat.index') }}" style="font-size:18px;color:#888;text-decoration:none;display:flex;align-items:center;padding:6px;border-radius:8px;transition:background .15s"
           onmouseover="this.style.background='#f0f0f0'" onmouseout="this.style.background='transparent'">
            <i class="fa-solid fa-arrow-left"></i>
        </a>

        @php
            $role = $user->role === 'pemandu' ? 'P' : 'W';
            $bgColor = $user->role === 'pemandu' && $user->pemandu
                ? ($user->pemandu->warna_avatar ?? '#0d5c45')
                : '#1565c0';
            $initials = $user->role === 'pemandu' && $user->pemandu
                ? $user->pemandu->inisial
                : strtoupper(substr($user->name, 0, 2));
        @endphp
        <div style="width:44px;height:44px;border-radius:12px;background:{{ $bgColor }};display:flex;align-items:center;justify-content:center;font-size:15px;font-weight:800;color:#fff;flex-shrink:0">
            {{ $initials }}
        </div>

        <div>
            <div style="font-size:16px;font-weight:800;color:#1a1a1a">{{ $user->name }}</div>
            <div style="font-size:12px;color:#888;display:flex;align-items:center;gap:6px">
                <span style="font-weight:600;padding:1px 6px;border-radius:4px;background:{{ $user->role === 'pemandu' ? '#e8f5e9' : '#e3f2fd' }};color:{{ $user->role === 'pemandu' ? '#0d5c45' : '#1565c0' }};font-size:10px">
                    {{ $user->role === 'pemandu' ? 'Pemandu' : 'Wisatawan' }}
                </span>
                @if($user->phone)
                <span>{{ $user->phone }}</span>
                @endif
                <span>{{ $user->email }}</span>
            </div>
        </div>
    </div>

    {{-- Flash Messages --}}
    @if(session('success'))
    <div style="background:#e8f5e9;border:2px solid #2e7d32;border-radius:10px;padding:10px 14px;margin:12px 0;font-size:13px;font-weight:600;color:#2e7d32;display:flex;align-items:center;gap:8px;flex-shrink:0">
        <i class="fa-solid fa-check-circle"></i> {{ session('success') }}
    </div>
    @endif
    @if(session('error'))
    <div style="background:#fbe9e7;border:2px solid #c62828;border-radius:10px;padding:10px 14px;margin:12px 0;font-size:13px;font-weight:600;color:#c62828;display:flex;align-items:center;gap:8px;flex-shrink:0">
        <i class="fa-solid fa-xmark-circle"></i> {{ session('error') }}
    </div>
    @endif

    {{-- Messages Area --}}
    <div id="chat-messages" style="flex:1;overflow-y:auto;padding:16px 0;display:flex;flex-direction:column;gap:8px;scroll-behavior:smooth">
        @forelse($messages as $msg)
        @php $isMine = $msg->sender_id === auth()->id(); @endphp
        <div style="display:flex;{{ $isMine ? 'justify-content:flex-end' : 'justify-content:flex-start' }};padding:0 4px">
            <div style="max-width:75%;padding:10px 16px;border-radius:16px;font-size:13px;line-height:1.6;word-wrap:break-word;
                {{ $isMine
                    ? 'background:#0d5c45;color:#fff;border-bottom-right-radius:4px;'
                    : 'background:#f0f0f0;color:#1a1a1a;border-bottom-left-radius:4px;'
                }}">
                <div>{{ $msg->message }}</div>
                <div style="font-size:10px;margin-top:4px;opacity:0.7;display:flex;align-items:center;gap:6px;{{ $isMine ? 'justify-content:flex-end' : 'justify-content:flex-start' }}">
                    <span>{{ $msg->created_at->format('H:i') }}</span>
                    @if($isMine)
                        <span>{{ $msg->is_read ? '✓✓ Dibaca' : '✓' }}</span>
                    @endif
                </div>
            </div>
        </div>
        @empty
        <div style="text-align:center;padding:60px 20px;color:#aaa">
            <div style="font-size:40px;margin-bottom:12px"><i class="fa-solid fa-comment-dots"></i></div>
            <div style="font-size:14px;font-weight:600;color:#888">Belum ada pesan</div>
            <div style="font-size:12px;margin-top:4px">Kirim pesan pertama untuk memulai percakapan</div>
        </div>
        @endforelse
    </div>

    {{-- Form Kirim Pesan --}}
    <div style="flex-shrink:0;padding:12px 0 16px;border-top:2px solid #eee;background:#fff">
        <form method="POST" action="{{ route('chat.store', $user) }}" style="display:flex;gap:10px;align-items:flex-end">
            @csrf
            <div style="flex:1;position:relative">
                <textarea name="message" rows="2" placeholder="Ketik pesan..." required
                          style="width:100%;padding:12px 16px;border:2px solid #222;border-radius:14px;font-size:13px;font-family:inherit;resize:none;box-sizing:border-box;outline:none;transition:border-color .15s"
                          onfocus="this.style.borderColor='#0d5c45'"
                          onblur="this.style.borderColor='#222'"
                          oninput="autoResize(this)">{{ old('message') }}</textarea>
                @error('message')
                <div style="font-size:11px;color:#c62828;margin-top:4px">{{ $message }}</div>
                @enderror
            </div>
            <button type="submit"
                    style="padding:12px 20px;background:#0d5c45;color:#fff;border:2px solid #222;border-radius:14px;font-size:14px;font-weight:700;cursor:pointer;display:inline-flex;align-items:center;gap:8px;box-shadow:2px 2px 0 #222;transition:all .15s;white-space:nowrap"
                    onmouseover="this.style.background='#1a8a6a'"
                    onmouseout="this.style.background='#0d5c45'">
                <i class="fa-solid fa-paper-plane"></i>
                <span style="display:none" class="send-text-md">Kirim</span>
            </button>
        </form>
    </div>

</div>

<script>
// Auto-resize textarea
function autoResize(el) {
    el.style.height = 'auto';
    el.style.height = Math.min(el.scrollHeight, 150) + 'px';
}

// Scroll to bottom on load
document.addEventListener('DOMContentLoaded', function() {
    var container = document.getElementById('chat-messages');
    if (container) {
        container.scrollTop = container.scrollHeight;

        // Auto-resize any existing textarea
        document.querySelectorAll('textarea[name="message"]').forEach(function(el) {
            autoResize(el);
        });
    }
});
</script>

{{-- Style untuk di layar besar --}}
<style>
@media (min-width: 600px) {
    .send-text-md { display: inline !important; }
}
</style>
@endsection
