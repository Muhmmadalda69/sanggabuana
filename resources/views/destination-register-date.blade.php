@extends('layouts.app')

@section('title', 'Pilih Tanggal Kunjungan — ' . $destination->name)

@push('styles')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        .navbar-scroll, #navbar { background-color: #14532d !important; }
        .date-btn { transition: all 0.2s; }
        .date-btn:disabled { opacity: 0.45; cursor: not-allowed; }
        .date-btn.selected { background: #15803d; color: white; border-color: #15803d; }
    </style>
@endpush

@section('content')
<div class="pt-24 pb-16 min-h-screen bg-forest-50/50">
    <div class="max-w-2xl mx-auto px-4 sm:px-6">

        {{-- Header --}}
        <div class="bg-gradient-to-r from-forest-800 to-forest-950 rounded-3xl p-8 shadow-md text-white mb-8 relative overflow-hidden" style="background: linear-gradient(135deg, #15803d 0%, #14532d 100%);">
            <div class="absolute right-0 top-0 opacity-10 pointer-events-none transform translate-x-8 -translate-y-8">
                <i data-lucide="calendar" class="w-48 h-48"></i>
            </div>
            <div class="relative z-10">
                <a href="{{ route('destination.detail', $destination->slug) }}" class="inline-flex items-center gap-2 text-green-200 hover:text-white mb-4 text-xs font-bold transition-colors">
                    <i data-lucide="arrow-left" class="w-3.5 h-3.5"></i> Kembali ke Destinasi
                </a>
                <h1 class="text-2xl font-black text-white mb-1">Pilih Tanggal Kunjungan</h1>
                <p class="text-green-100 text-sm">{{ $destination->name }}</p>
            </div>
        </div>

        {{-- Date Picker Card --}}
        <div class="bg-white rounded-3xl p-6 sm:p-8 shadow-sm border border-forest-100">
            <h2 class="text-base font-bold text-gray-800 mb-1 flex items-center gap-2">
                <i data-lucide="calendar-days" class="w-5 h-5 text-forest-600"></i>
                Pilih Tanggal Kedatangan
            </h2>
            <p class="text-xs text-gray-400 mb-6">Pilih tanggal yang tersedia. Tanggal dengan kuota penuh tidak dapat dipilih.</p>

            {{-- Month navigation --}}
            <div class="flex items-center justify-between mb-4">
                <button type="button" id="prev-month" class="w-8 h-8 rounded-lg border border-gray-200 hover:bg-gray-50 flex items-center justify-center text-gray-500 transition-colors">
                    <i data-lucide="chevron-left" class="w-4 h-4"></i>
                </button>
                <span id="month-label" class="font-bold text-gray-800 text-sm"></span>
                <button type="button" id="next-month" class="w-8 h-8 rounded-lg border border-gray-200 hover:bg-gray-50 flex items-center justify-center text-gray-500 transition-colors">
                    <i data-lucide="chevron-right" class="w-4 h-4"></i>
                </button>
            </div>

            {{-- Calendar grid --}}
            <div class="grid grid-cols-7 gap-1 mb-2">
                @foreach(['Min','Sen','Sel','Rab','Kam','Jum','Sab'] as $d)
                    <div class="text-center text-[10px] font-bold text-gray-400 uppercase py-1">{{ $d }}</div>
                @endforeach
            </div>
            <div id="calendar-grid" class="grid grid-cols-7 gap-1 mb-6"></div>

            {{-- Quota info --}}
            <div id="quota-info" class="hidden bg-forest-50 border border-forest-100 rounded-xl px-4 py-3 text-sm mb-6">
                <div class="flex items-center justify-between">
                    <span class="text-gray-600 font-medium" id="quota-date-label"></span>
                    <span id="quota-badge" class="font-bold text-forest-700"></span>
                </div>
                <div id="quota-bar-wrap" class="mt-2 h-2 bg-gray-200 rounded-full overflow-hidden">
                    <div id="quota-bar" class="h-full bg-forest-500 rounded-full transition-all duration-500" style="width:0%"></div>
                </div>
            </div>

            {{-- Proceed button --}}
            <a id="btn-proceed" href="#" class="block w-full py-3 bg-forest-600 hover:bg-forest-700 text-white rounded-xl font-bold text-sm text-center transition-all shadow-sm opacity-50 pointer-events-none">
                Lanjut ke Formulir Registrasi
            </a>

            {{-- Legend --}}
            <div class="flex items-center gap-4 mt-4 text-[10px] text-gray-400 justify-center">
                <span class="flex items-center gap-1"><span class="w-3 h-3 rounded bg-forest-600 inline-block"></span> Dipilih</span>
                <span class="flex items-center gap-1"><span class="w-3 h-3 rounded bg-red-100 border border-red-200 inline-block"></span> Penuh</span>
                <span class="flex items-center gap-1"><span class="w-3 h-3 rounded bg-gray-100 border border-gray-200 inline-block"></span> Tidak Tersedia</span>
            </div>
        </div>

    </div>
</div>
@endsection

@push('scripts')
<script>
const SLUG = '{{ $destination->slug }}';
const QUOTA = {{ $destination->daily_quota ?? 'null' }};
const REGISTER_BASE = '{{ url("/destinasi/{$destination->slug}/registrasi") }}';
const QUOTA_MONTH_API = '{{ route("destination.quota.month", $destination->slug) }}';

let currentYear, currentMonth, selectedDate = null;
const monthCache = {}; // key: "YYYY-M" => { quota, month: { date: {booked,remaining,is_full} } }

function today() {
    const d = new Date(); d.setHours(0,0,0,0); return d;
}
function pad(n) { return String(n).padStart(2,'0'); }
function dateStr(y, m, d) { return `${y}-${pad(m+1)}-${pad(d)}`; }

async function fetchMonth(year, month) {
    const key = `${year}-${month}`;
    if (monthCache[key]) return monthCache[key];
    try {
        const res = await fetch(`${QUOTA_MONTH_API}?year=${year}&month=${month + 1}`);
        const data = await res.json();
        monthCache[key] = data;
        return data;
    } catch { return { quota: QUOTA, month: {} }; }
}

async function renderCalendar(year, month) {
    currentYear = year; currentMonth = month;
    const months = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
    document.getElementById('month-label').textContent = `${months[month]} ${year}`;

    const grid = document.getElementById('calendar-grid');
    grid.innerHTML = '<div class="col-span-7 text-center py-4 text-xs text-gray-400">Memuat...</div>';

    const mData = await fetchMonth(year, month);
    const monthQuota = mData.quota;
    const dayData = mData.month || {};

    const firstDay = new Date(year, month, 1).getDay();
    const daysInMonth = new Date(year, month + 1, 0).getDate();
    const todayDate = today();

    let html = '';
    for (let i = 0; i < firstDay; i++) html += '<div></div>';

    for (let d = 1; d <= daysInMonth; d++) {
        const ds = dateStr(year, month, d);
        const cellDate = new Date(year, month, d);
        const isPast = cellDate < todayDate;
        const qd = dayData[ds] || { booked: 0, remaining: monthQuota, is_full: false };
        const isFull = monthQuota ? qd.is_full : false;
        const isSelected = ds === selectedDate;

        let cls = 'date-btn w-full aspect-square rounded-xl text-xs font-bold border flex flex-col items-center justify-center gap-0.5 ';
        let disabled = false;

        if (isPast) {
            cls += 'bg-gray-50 border-gray-100 text-gray-300 cursor-not-allowed';
            disabled = true;
        } else if (isFull) {
            cls += 'bg-red-50 border-red-200 text-red-400 cursor-not-allowed';
            disabled = true;
        } else if (isSelected) {
            cls += 'bg-forest-600 border-forest-600 text-white selected';
        } else {
            cls += 'bg-white border-gray-200 text-gray-700 hover:border-forest-400 hover:bg-forest-50 cursor-pointer';
        }

        const remaining = monthQuota ? qd.remaining : null;
        const subLabel = isFull
            ? '<span class="text-[8px] leading-none">Penuh</span>'
            : (remaining !== null && remaining <= 10 ? `<span class="text-[8px] leading-none text-amber-600">${remaining} sisa</span>` : '');

        html += `<button type="button" class="${cls}" ${disabled ? 'disabled' : ''} data-date="${ds}">
            ${d}${subLabel}
        </button>`;
    }

    grid.innerHTML = html;
    grid.querySelectorAll('.date-btn:not(:disabled)').forEach(btn => {
        btn.addEventListener('click', () => selectDate(btn.dataset.date, dayData, monthQuota));
    });

    // Re-highlight selected if still in same month
    if (selectedDate) {
        const sel = grid.querySelector(`[data-date="${selectedDate}"]`);
        if (sel && !sel.disabled) {
            sel.classList.add('selected', 'bg-forest-600', 'border-forest-600', 'text-white');
            sel.classList.remove('bg-white', 'border-gray-200', 'text-gray-700');
        }
    }
}

function selectDate(date, dayData, monthQuota) {
    selectedDate = date;
    const qd = dayData[date] || { booked: 0, remaining: monthQuota, is_full: false };

    // Update calendar highlight
    document.querySelectorAll('.date-btn').forEach(b => {
        if (b.dataset.date === date) {
            b.classList.add('selected', 'bg-forest-600', 'border-forest-600', 'text-white');
            b.classList.remove('bg-white', 'border-gray-200', 'text-gray-700', 'hover:border-forest-400', 'hover:bg-forest-50');
        } else if (!b.disabled) {
            b.classList.remove('selected', 'bg-forest-600', 'border-forest-600', 'text-white');
            b.classList.add('bg-white', 'border-gray-200', 'text-gray-700', 'hover:border-forest-400', 'hover:bg-forest-50');
        }
    });

    // Show quota info
    const infoEl = document.getElementById('quota-info');
    const d = new Date(date + 'T00:00:00');
    const days = ['Minggu','Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'];
    const months = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
    document.getElementById('quota-date-label').textContent = `${days[d.getDay()]}, ${d.getDate()} ${months[d.getMonth()]} ${d.getFullYear()}`;

    if (monthQuota) {
        infoEl.classList.remove('hidden');
        document.getElementById('quota-bar-wrap').classList.remove('hidden');
        const pct = Math.min(100, Math.round(((qd.booked || 0) / monthQuota) * 100));
        document.getElementById('quota-badge').textContent = `${qd.remaining ?? monthQuota} / ${monthQuota} sisa`;
        document.getElementById('quota-bar').style.width = pct + '%';
        document.getElementById('quota-bar').className = `h-full rounded-full transition-all duration-500 ${pct >= 100 ? 'bg-red-500' : pct >= 75 ? 'bg-amber-500' : 'bg-forest-500'}`;
    } else {
        infoEl.classList.remove('hidden');
        document.getElementById('quota-badge').textContent = 'Tidak ada batas kuota';
        document.getElementById('quota-bar-wrap').classList.add('hidden');
    }

    // Enable proceed button
    const btn = document.getElementById('btn-proceed');
    btn.href = `${REGISTER_BASE}/${date}`;
    btn.classList.remove('opacity-50', 'pointer-events-none');
}

// Month navigation
document.getElementById('prev-month').addEventListener('click', () => {
    let m = currentMonth - 1, y = currentYear;
    if (m < 0) { m = 11; y--; }
    const t = today();
    if (y < t.getFullYear() || (y === t.getFullYear() && m < t.getMonth())) return;
    renderCalendar(y, m);
});
document.getElementById('next-month').addEventListener('click', () => {
    let m = currentMonth + 1, y = currentYear;
    if (m > 11) { m = 0; y++; }
    renderCalendar(y, m);
});

// Init — prefetch current month immediately
const t = today();
renderCalendar(t.getFullYear(), t.getMonth());

@if (session('error'))
    Swal.fire({
        icon: 'error',
        title: 'Gagal',
        text: "{{ session('error') }}",
        confirmButtonColor: '#059669',
        background: '#ffffff',
        customClass: {
            popup: 'rounded-2xl shadow-xl border border-gray-100',
            confirmButton: 'px-6 py-2.5 bg-forest-600 hover:bg-forest-750 rounded-xl font-bold text-sm text-white'
        }
    });
@endif
</script>
@endpush
