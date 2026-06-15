@extends('layouts.app')
@section('title', 'Checkout Booking')

@section('content')
<div style="max-width:720px;margin:40px auto;padding:0 16px">

    {{-- Breadcrumb --}}
    <a href="{{ route('pemandu.show', $guide) }}" style="display:inline-flex;align-items:center;gap:6px;font-size:13px;color:#0d5c45;text-decoration:none;margin-bottom:24px">
        <i class="fa-solid fa-arrow-left"></i> Kembali ke {{ $guide->user->name }}
    </a>

    {{-- Card --}}
    <div style="background:#fff;border:3px solid #222;border-radius:16px;box-shadow:6px 6px 0 #222;padding:32px">

        <h2 style="font-size:22px;font-weight:800;margin:0 0 4px">Checkout Booking</h2>
        <p style="font-size:13px;color:#888;margin:0 0 28px">Pastikan data di bawah sudah benar sebelum melanjutkan.</p>

        {{-- Ringkasan Destinasi --}}
        <div style="display:flex;align-items:center;gap:16px;padding:16px;background:#f5f5f5;border:2px solid #222;border-radius:12px;margin-bottom:16px">
            <div style="width:48px;height:48px;border-radius:12px;background:{{ $destination->warna_bg ?? '#0d5c45' }};display:flex;align-items:center;justify-content:center;font-size:24px;flex-shrink:0">
                {{ $destination->emoji ?? '🏔️' }}
            </div>
            <div>
                <div style="font-weight:700;font-size:15px">{{ $destination->nama }}</div>
                <div style="font-size:12px;color:#888">{{ $destination->lokasi }}</div>
            </div>
            <div style="margin-left:auto;display:flex;align-items:center;gap:4px;font-size:13px;font-weight:600">
                <span style="color:#F4CD0B">★</span> {{ number_format($destination->rating ?? 0, 1) }}
            </div>
        </div>

        {{-- Ringkasan Pemandu --}}
        <div style="display:flex;align-items:center;gap:16px;padding:16px;background:#f5f5f5;border:2px solid #222;border-radius:12px;margin-bottom:28px">
            <div style="width:48px;height:48px;border-radius:50%;background:{{ $guide->warna_avatar ?? '#0d5c45' }};display:flex;align-items:center;justify-content:center;font-size:16px;font-weight:800;color:#fff;flex-shrink:0">
                {{ $guide->inisial }}
            </div>
            <div>
                <div style="font-weight:700;font-size:15px">{{ $guide->user->name }}</div>
                <div style="font-size:12px;color:#888">{{ $guide->spesialisasi }} · {{ $guide->pengalaman_tahun }}th pengalaman</div>
            </div>
            <div style="margin-left:auto;display:flex;align-items:center;gap:4px;font-size:13px;font-weight:600">
                <span style="color:#F4CD0B">★</span> {{ number_format($guide->rating ?? 0, 1) }}
            </div>
        </div>

        {{-- Status Ketersediaan Pemandu --}}
        <div style="background:#f0faf6;border:2px solid #0d5c45;border-radius:12px;padding:16px;margin-bottom:24px">
            <div style="font-size:13px;font-weight:700;margin-bottom:10px">
                <i class="fa-solid fa-calendar-days" style="margin-right:6px;color:#0d5c45"></i>
                Jadwal Ketersediaan {{ $guide->user->name }}
            </div>

            {{-- Hari tersedia --}}
            <div style="display:flex;gap:6px;flex-wrap:wrap;margin-bottom:12px">
                @foreach($hari as $i => $h)
                @php $tersedia = is_array($guide->ketersediaan) && in_array($i, $guide->ketersediaan); @endphp
                <div style="padding:4px 10px;border-radius:20px;font-size:11px;font-weight:700;
                    {{ $tersedia ? 'background:#0d5c45;color:#fff;' : 'background:#e0e0e0;color:#999;' }}">
                    {{ $h }}
                </div>
                @endforeach
            </div>

            {{-- Tanggal spesifik tersedia --}}
            @php $tglTersedia = $guide->getTanggalTersediaList(); @endphp
            @if(!empty($tglTersedia))
            <div style="font-size:12px;font-weight:600;color:#0d5c45;margin-bottom:6px">
                <i class="fa-solid fa-calendar-check" style="margin-right:4px"></i>
                Tanggal spesifik tersedia:
            </div>
            <div style="display:flex;gap:6px;flex-wrap:wrap;margin-bottom:12px">
                @foreach($tglTersedia as $tgl)
                <span style="padding:3px 10px;background:#e8f5e9;border:1px solid #0d5c45;border-radius:6px;font-size:11px;font-weight:600;color:#0d5c45">
                    {{ \Carbon\Carbon::parse($tgl)->format('d M Y') }}
                </span>
                @endforeach
            </div>
            @endif

            {{-- Tanggal yang sudah di-booking --}}
            @if($bookedDates->isNotEmpty())
            <div style="font-size:12px;font-weight:600;color:#c62828;margin-bottom:6px">
                <i class="fa-solid fa-circle-exclamation" style="margin-right:4px"></i>
                Tanggal sudah ada booking (menunggu/dikonfirmasi):
            </div>
            <div style="display:flex;gap:6px;flex-wrap:wrap">
                @foreach($bookedDates as $bd)
                <span style="padding:3px 10px;background:#fbe9e7;border:1px solid #c62828;border-radius:6px;font-size:11px;font-weight:600;color:#c62828">
                    <i class="fa-solid fa-xmark"></i> {{ \Carbon\Carbon::parse($bd)->format('d M Y') }}
                </span>
                @endforeach
            </div>
            @endif
        </div>

        {{-- Form Booking --}}
        <form method="POST" action="{{ route('pemandu.booking', $guide) }}">
            @csrf

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:24px">
                <div>
                    <label style="display:block;font-size:12px;font-weight:600;margin-bottom:4px">Tanggal Booking</label>
                    <input type="date" name="tanggal_booking" min="{{ date('Y-m-d') }}"
                           style="width:100%;padding:10px 12px;border:2px solid #222;border-radius:10px;font-size:13px;box-sizing:border-box"
                           onchange="validateBookingDate(this)">
                    @error('tanggal_booking') <div style="font-size:12px;color:#c62828;margin-top:6px">{{ $message }}</div> @enderror
                </div>
                <div>
                    <label style="display:block;font-size:12px;font-weight:600;margin-bottom:4px">Jumlah Peserta</label>
                    <input type="number" name="jumlah_peserta" min="1" max="100" value="1"
                           style="width:100%;padding:10px 12px;border:2px solid #222;border-radius:10px;font-size:13px;box-sizing:border-box">
                </div>
            </div>

            <div id="date-feedback" style="display:none;padding:10px 14px;border-radius:10px;font-size:13px;font-weight:600;margin-bottom:16px"></div>

            <div style="margin-bottom:24px">
                <label style="display:block;font-size:12px;font-weight:600;margin-bottom:4px">Catatan (opsional)</label>
                <textarea name="catatan" rows="3" placeholder="Contoh: ingin trekking pagi, butuh pick-up di hotel..."
                          style="width:100%;padding:10px 12px;border:2px solid #222;border-radius:10px;font-size:13px;resize:vertical;box-sizing:border-box"></textarea>
            </div>

            <button type="submit" style="width:100%;padding:14px;background:#0d5c45;color:#fff;border:3px solid #222;border-radius:14px;font-size:15px;font-weight:700;cursor:pointer;box-shadow:4px 4px 0 #222">
                <i class="fa-solid fa-paper-plane" style="margin-right:8px"></i>Konfirmasi Booking
            </button>
        </form>

        @push('scripts')
        <script>
        // Data ketersediaan & booking untuk validasi client-side
        var availIndices = {!! json_encode(is_array($guide->ketersediaan) ? $guide->ketersediaan : []) !!};
        var bookedDates  = {!! json_encode($bookedDates) !!};
        var hariNama     = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];

        function validateBookingDate(input) {
            var feedback = document.getElementById('date-feedback');
            if (!input.value) {
                feedback.style.display = 'none';
                return;
            }

            var date = new Date(input.value + 'T00:00:00');
            var dayOfWeek = date.getDay(); // 0=Minggu,1=Senin,...
            var ourIndex  = dayOfWeek === 0 ? 6 : dayOfWeek - 1;

            // Cek ketersediaan hari
            if (!availIndices.includes(ourIndex)) {
                feedback.style.display = 'block';
                feedback.style.background = '#fbe9e7';
                feedback.style.border = '2px solid #c62828';
                feedback.style.color = '#c62828';
                feedback.innerHTML = '<i class="fa-solid fa-circle-exclamation" style="margin-right:6px"></i>Pemandu tidak tersedia pada hari <strong>' + hariNama[ourIndex] + '</strong>.';
                return;
            }

            // Cek double-booking
            var dateStr = input.value;
            if (bookedDates.includes(dateStr)) {
                feedback.style.display = 'block';
                feedback.style.background = '#fff8e1';
                feedback.style.border = '2px solid #f57f17';
                feedback.style.color = '#f57f17';
                feedback.innerHTML = '<i class="fa-solid fa-triangle-exclamation" style="margin-right:6px"></i>Tanggal ini sudah memiliki booking. Silakan pilih tanggal lain.';
                return;
            }

            // Valid
            feedback.style.display = 'block';
            feedback.style.background = '#e8f5e9';
            feedback.style.border = '2px solid #2e7d32';
            feedback.style.color = '#2e7d32';
            feedback.innerHTML = '<i class="fa-solid fa-check-circle" style="margin-right:6px"></i>Tanggal tersedia! Silakan lanjutkan booking.';
        }
        </script>
        @endpush

    </div>
</div>
@endsection
