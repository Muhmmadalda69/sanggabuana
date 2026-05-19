@extends('layouts.admin')

@section('title', 'Monitoring Pengunjung')

@section('content')
<div class="mb-8 bg-white rounded-2xl border border-gray-100 shadow-sm p-6 sm:p-8">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 border-b border-gray-100 pb-5 mb-6">
        <div>
            <h2 class="text-xl font-bold text-gray-800 flex items-center gap-2 mb-1" style="margin:0; border-left:none; padding-left:0;">
                <i data-lucide="eye" class="w-6 h-6 text-forest-600"></i>
                Monitoring Pengunjung Aktif
            </h2>
            <p class="text-xs text-gray-500">
                Pantau wisatawan yang sedang berada di lokasi **{{ $destination->name }}** dan kelola gerbang keluar masuk.
            </p>
        </div>
        
        {{-- Stats Counter pills --}}
        <div class="flex items-center gap-3">
            <span class="px-4 py-2 rounded-xl bg-green-50 border border-green-100 text-xs font-semibold text-green-700 flex items-center gap-1.5">
                <span class="w-2 h-2 rounded-full bg-green-500 animate-pulse"></span>
                {{ App\Models\Visitor::where('destination_id', $destination->id)->where('status', 'in')->sum('qty_total') }} Pengunjung Aktif Di Dalam
            </span>
        </div>
    </div>

    {{-- Filter & Search Toolbar --}}
    <form action="{{ route('admin.monitoring.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
        {{-- Search input --}}
        <div class="relative sm:col-span-1">
            <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-gray-400">
                <i data-lucide="search" class="w-4.5 h-4.5"></i>
            </span>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama, no. tiket, komunitas..." class="w-full pl-12 pr-3 py-2 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:border-forest-500 transition-colors">
        </div>

        {{-- Status Filter --}}
        <div class="relative">
            <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-gray-400">
                <i data-lucide="filter" class="w-4.5 h-4.5"></i>
            </span>
            <select name="status" onchange="this.form.submit()" class="w-full pl-12 pr-8 py-2 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:border-forest-500 transition-colors appearance-none cursor-pointer">
                <option value="">-- Semua Status --</option>
                <option value="in" {{ request('status') === 'in' ? 'selected' : '' }}>Aktif (Di Dalam)</option>
                <option value="out" {{ request('status') === 'out' ? 'selected' : '' }}>Selesai (Sudah Keluar)</option>
            </select>
            <span class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 pointer-events-none">
                <i data-lucide="chevron-down" class="w-4 h-4"></i>
            </span>
        </div>

        {{-- Action Buttons --}}
        <div class="flex gap-2">
            <button type="submit" class="flex-1 bg-forest-600 hover:bg-forest-700 text-white font-semibold text-sm px-4 py-2 rounded-xl transition-colors flex items-center justify-center gap-1.5">
                <i data-lucide="search" class="w-4 h-4"></i> Terapkan
            </button>
            @if(request('search') || request('status'))
                <a href="{{ route('admin.monitoring.index') }}" class="px-4 py-2 border border-gray-200 hover:bg-gray-50 rounded-xl text-gray-500 text-sm flex items-center justify-center gap-1.5 transition-colors font-semibold" title="Reset Pencarian">
                    <i data-lucide="rotate-ccw" class="w-4 h-4"></i> Reset
                </a>
            @endif
        </div>
    </form>

    {{-- Visitors Monitoring Table --}}
    <div class="overflow-x-auto border border-gray-100 rounded-2xl">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-50 border-b border-gray-100 text-xs font-bold text-gray-500 uppercase tracking-wider">
                    <th class="px-6 py-4">No. Tiket</th>
                    <th class="px-6 py-4">Rincian Pengunjung</th>
                    <th class="px-6 py-4">Komunitas &amp; Tujuan</th>
                    <th class="px-6 py-4">Demografi</th>
                    <th class="px-6 py-4">Total Rombongan</th>
                    <th class="px-6 py-4">Waktu Check-In</th>
                    <th class="px-6 py-4">Waktu Check-Out</th>
                    <th class="px-6 py-4 text-center">Aksi Gerbang</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 text-xs font-semibold text-gray-600">
                @forelse($visitors as $visitor)
                    <tr class="hover:bg-gray-50/50 transition-colors">
                        {{-- Ticket No --}}
                        <td class="px-6 py-4 font-extrabold text-gray-800">
                            <div>{{ $visitor->ticket_no }}</div>
                            <button type="button" 
                                onclick="showTicketModal('{{ $visitor->ticket_no }}', '{{ $visitor->name }}', '{{ $destination->name }}', '{{ $visitor->qty_total }}', '{{ $visitor->payment_method }}', 'Rp {{ number_format($visitor->total_price, 0, ',', '.') }}', '{{ $visitor->checked_in_at->format('d M Y, H:i') }}', '{{ $visitor->status }}', '{{ $visitor->community ?? '' }}', '{{ ($visitor->purpose && $visitor->purpose !== 'Normal') ? $visitor->purpose : '' }}', '{{ $visitor->camping_duration ?? '' }}')"
                                class="inline-flex items-center gap-1 mt-1.5 px-2 py-0.5 rounded text-[10px] font-bold bg-forest-50 text-forest-700 hover:bg-forest-100 border border-forest-100/50 transition-all cursor-pointer">
                                <i data-lucide="ticket" class="w-3 h-3"></i> Lihat Tiket
                            </button>
                        </td>
                        
                        {{-- Visitor Details --}}
                        <td class="px-6 py-4">
                            <div class="font-extrabold text-gray-700 text-sm">{{ $visitor->name }}</div>
                            <div class="text-[10px] text-gray-400 mt-0.5 flex items-center gap-1">
                                <i data-lucide="map-pin" class="w-3 h-3"></i> {{ $visitor->address }}
                            </div>
                        </td>

                        {{-- Community & Purpose --}}
                        <td class="px-6 py-4">
                            @if($visitor->community)
                                <div class="text-gray-700 font-bold flex items-center gap-1">
                                    <i data-lucide="users-2" class="w-3.5 h-3.5 text-gray-400"></i> {{ $visitor->community }}
                                </div>
                            @else
                                <div class="text-gray-400 font-normal italic">Bukan Komunitas</div>
                            @endif
                            
                            @if($visitor->purpose && $visitor->purpose !== 'Normal')
                                <div class="mt-1 text-[10px] font-bold text-forest-600 flex items-center gap-1">
                                    <i data-lucide="mountain" class="w-3 h-3 text-forest-500"></i> {{ $visitor->purpose }}
                                </div>
                            @else
                                <div class="mt-1 text-[10px] text-gray-400 font-normal">Kunjungan Normal</div>
                            @endif
                        </td>

                        {{-- Gender stats & avg age --}}
                        <td class="px-6 py-4">
                            @if($visitor->qty_male > 0 || $visitor->qty_female > 0 || $visitor->qty_kids > 0)
                                <div class="flex items-center gap-2 text-gray-500">
                                    <span class="flex items-center gap-0.5 text-blue-600 font-bold" title="Laki-laki (Dewasa)">L: {{ $visitor->qty_male }}</span>
                                    <span class="flex items-center gap-0.5 text-pink-600 font-bold" title="Perempuan (Dewasa)">P: {{ $visitor->qty_female }}</span>
                                    <span class="flex items-center gap-0.5 text-amber-600 font-bold" title="Anak-anak">A: {{ $visitor->qty_kids }}</span>
                                </div>
                            @else
                                <div class="text-gray-400 font-normal italic">Tidak Didetailkan</div>
                            @endif
                            <div class="text-[10px] text-gray-400 font-medium mt-0.5">Rata-rata Usia: {{ $visitor->avg_age }} Thn</div>
                        </td>

                        {{-- Total Qty --}}
                        <td class="px-6 py-4 font-black text-gray-700 text-sm">{{ $visitor->qty_total }} Orang</td>

                        {{-- Checked In At --}}
                        <td class="px-6 py-4 text-gray-400">
                            <div>{{ $visitor->checked_in_at->format('d/M/Y') }}</div>
                            <div class="text-[10px] font-bold text-gray-500 mt-0.5">{{ $visitor->checked_in_at->format('H:i') }} WIB</div>
                        </td>

                        {{-- Checked Out At --}}
                        <td class="px-6 py-4 text-gray-400">
                            @if($visitor->checked_out_at)
                                <div>{{ $visitor->checked_out_at->format('d/M/Y') }}</div>
                                <div class="text-[10px] font-bold text-gray-500 mt-0.5">{{ $visitor->checked_out_at->format('H:i') }} WIB</div>
                            @else
                                <span class="px-2 py-0.5 rounded bg-green-50 text-green-700 font-bold text-[10px] inline-flex items-center gap-1">
                                    <span class="w-1 h-1 rounded-full bg-green-500 animate-pulse"></span> Di Dalam Lokasi
                                </span>
                            @endif
                        </td>

                        {{-- Action Checkout button --}}
                        <td class="px-6 py-4 text-center">
                            @if($visitor->status === 'in')
                                <form action="{{ route('admin.monitoring.checkout', $visitor->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin memproses check-out pengunjung {{ $visitor->name }} ini?')" class="inline">
                                    @csrf
                                    <button type="submit" class="inline-flex items-center gap-1 bg-red-50 hover:bg-red-100 text-red-600 hover:text-red-700 px-3 py-1.5 rounded-xl font-bold text-[11px] transition-colors border border-red-100">
                                        <i data-lucide="log-out" class="w-3.5 h-3.5"></i> Check Out
                                    </button>
                                </form>
                            @else
                                @php
                                    $duration = $visitor->checked_out_at ? $visitor->checked_out_at->diffInMinutes($visitor->checked_in_at) : 0;
                                    $hours = floor($duration / 60);
                                    $mins = $duration % 60;
                                    $durationStr = $hours > 0 ? "{$hours} jam {$mins} mnt" : "{$mins} mnt";
                                @endphp
                                <div class="flex flex-col items-center">
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-xl text-[10px] font-extrabold bg-gray-100 text-gray-400">
                                        <i data-lucide="check" class="w-3.5 h-3.5 text-green-500"></i> Selesai
                                    </span>
                                    <span class="text-[9px] text-gray-400 mt-1 font-semibold">Durasi: {{ $durationStr }}</span>
                                </div>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center py-12 text-gray-400">
                            <i data-lucide="inbox" class="w-12 h-12 mx-auto text-gray-300 mb-3"></i>
                            <p class="font-medium text-sm">Tidak ada data pengunjung yang cocok dengan filter pencarian.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination links --}}
    <div class="mt-6">
        {{ $visitors->links() }}
    </div>
</div>

@include('admin.partials.ticket_modal')
@endsection
