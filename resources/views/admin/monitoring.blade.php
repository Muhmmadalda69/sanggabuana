@extends('layouts.admin')

@section('title', 'Monitoring Pengunjung')

@section('content')
<div class="mb-8 bg-white rounded-2xl border border-gray-100 shadow-sm p-6 sm:p-8">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 border-b border-gray-100 pb-5 mb-6">
        <div>
            <h2 class="text-xl font-bold text-gray-800 flex items-center gap-2 mb-1" style="margin:0; border-left:none; padding-left:0;">
                <i data-lucide="eye" class="w-6 h-6 text-forest-600"></i>
                Monitoring Rombongan Aktif
            </h2>
            <p class="text-xs text-gray-500">Pantau rombongan wisatawan di <strong>{{ $destination->name }}</strong> dan kelola gerbang keluar masuk.</p>
        </div>
        <div class="flex items-center gap-3">
            <span class="px-4 py-2 rounded-xl bg-green-50 border border-green-100 text-xs font-semibold text-green-700 flex items-center gap-1.5">
                <span class="w-2 h-2 rounded-full bg-green-500 animate-pulse"></span>
                {{ App\Models\Visitor::where('destination_id', $destination->id)->where('status', 'in')->sum('qty_total') }} Pengunjung Aktif Di Dalam
            </span>
        </div>
    </div>

        {{-- Filter & Search --}}
    <form action="{{ route('admin.monitoring.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-4 gap-4 mb-6">
        <div class="relative sm:col-span-1">
            <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-gray-400">
                <i data-lucide="search" class="w-4.5 h-4.5"></i>
            </span>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama, no. tiket, grup..." class="w-full pl-12 pr-3 py-2 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:border-forest-500 transition-colors">
        </div>
        <div class="relative">
            <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-gray-400">
                <i data-lucide="calendar" class="w-4.5 h-4.5"></i>
            </span>
            <input type="date" name="visit_date" value="{{ request('visit_date') }}" class="w-full pl-12 pr-3 py-2 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:border-forest-500 transition-colors">
        </div>
        <div class="relative">
            <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-gray-400">
                <i data-lucide="filter" class="w-4.5 h-4.5"></i>
            </span>
            <select name="status" onchange="this.form.submit()" class="w-full pl-12 pr-8 py-2 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:border-forest-500 transition-colors appearance-none cursor-pointer">
                <option value="">-- Semua Status --</option>
                <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="in" {{ request('status') === 'in' ? 'selected' : '' }}>Aktif (Di Dalam)</option>
                <option value="out" {{ request('status') === 'out' ? 'selected' : '' }}>Selesai (Sudah Keluar)</option>
            </select>
            <span class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 pointer-events-none">
                <i data-lucide="chevron-down" class="w-4 h-4"></i>
            </span>
        </div>
        <div class="flex gap-2">
            <button type="submit" class="flex-1 bg-forest-600 hover:bg-forest-700 text-white font-semibold text-sm px-4 py-2 rounded-xl transition-colors flex items-center justify-center gap-1.5">
                <i data-lucide="search" class="w-4 h-4"></i> Terapkan
            </button>
            @if(request('search') || request('status') || request('visit_date'))
                <a href="{{ route('admin.monitoring.index') }}" class="px-4 py-2 border border-gray-200 hover:bg-gray-50 rounded-xl text-gray-500 text-sm flex items-center justify-center gap-1.5 transition-colors font-semibold">
                    <i data-lucide="rotate-ccw" class="w-4 h-4"></i> Reset
                </a>
            @endif
        </div>
    </form>

    {{-- Rombongan Table --}}
    <div class="overflow-x-auto border border-gray-100 rounded-2xl">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-50 border-b border-gray-100 text-xs font-bold text-gray-500 uppercase tracking-wider">
                    <th class="px-6 py-4">ID Rombongan</th>
                    <th class="px-6 py-4">Penanggung Jawab</th>
                    <th class="px-6 py-4">Komunitas &amp; Tujuan</th>
                    <th class="px-6 py-4">Jumlah</th>
                    <th class="px-6 py-4">Tgl Kunjungan</th>
                    <th class="px-6 py-4">Check-In</th>
                    <th class="px-6 py-4 text-center">Status</th>
                    <th class="px-6 py-4 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 text-xs font-semibold text-gray-600">
                @forelse($visitors as $groupId)
                    @php
                        $tickets = $groupedVisitors[$groupId] ?? collect();
                        $leader = $tickets->first();
                        if (!$leader) continue;
                        $anyIn = $tickets->contains(fn($t) => $t->status === 'in');
                        $allOut = !$anyIn;
                        $checkedOutAt = $allOut ? $tickets->max('checked_out_at') : null;
                    @endphp
                    <tr class="hover:bg-gray-50/50 transition-colors">
                        {{-- Group ID --}}
                        <td class="px-6 py-4">
                            <div class="font-extrabold text-gray-800 text-[11px]">{{ $groupId }}</div>
                            <div class="text-[10px] text-gray-400 mt-0.5">{{ $tickets->count() }} tiket · {{ $tickets->sum('qty_total') }} orang</div>
                        </td>

                        {{-- Leader --}}
                        <td class="px-6 py-4">
                            <div class="font-extrabold text-gray-700 text-sm">{{ $leader->name }}</div>
                            <div class="text-[10px] text-gray-400 mt-0.5 flex items-center gap-1">
                                <i data-lucide="map-pin" class="w-3 h-3"></i>
                                {{ implode(', ', array_filter([$leader->city, $leader->province])) ?: $leader->address }}
                            </div>
                        </td>

                        {{-- Community & Purpose --}}
                        <td class="px-6 py-4">
                            @if($leader->community)
                                <div class="text-gray-700 font-bold flex items-center gap-1">
                                    <i data-lucide="users-2" class="w-3.5 h-3.5 text-gray-400"></i> {{ $leader->community }}
                                </div>
                            @else
                                <div class="text-gray-400 font-normal italic">Bukan Komunitas</div>
                            @endif
                            @if($leader->purpose && $leader->purpose !== 'Normal')
                                <div class="mt-1 text-[10px] font-bold text-forest-600 flex items-center gap-1">
                                    <i data-lucide="mountain" class="w-3 h-3"></i> {{ $leader->purpose }}
                                    @if($leader->camping_duration) · {{ $leader->camping_duration }} malam @endif
                                </div>
                            @else
                                <div class="mt-1 text-[10px] text-gray-400 font-normal">Kunjungan Normal</div>
                            @endif
                        </td>

                        {{-- Count + Demografi --}}
                        @php
                            $totalOrang = $tickets->sum('qty_total');
                            $totalL = $tickets->sum('qty_male');
                            $totalP = $tickets->sum('qty_female');
                            $totalA = $tickets->sum('qty_kids');
                        @endphp
                        <td class="px-6 py-4">
                            <div class="font-black text-gray-700 text-sm">{{ $totalOrang }} Orang</div>
                            <div class="flex items-center gap-2 mt-1 text-[10px]">
                                <span class="text-blue-600 font-bold">L: {{ $totalL }}</span>
                                <span class="text-pink-600 font-bold">P: {{ $totalP }}</span>
                                @if($totalA > 0)<span class="text-amber-600 font-bold">A: {{ $totalA }}</span>@endif
                            </div>
                        </td>

                        {{-- Check-In --}}
                        <td class="px-6 py-4 text-gray-400">
                            @if($leader->visit_date)
                                <div class="font-bold text-amber-700 text-xs">{{ \Carbon\Carbon::parse($leader->visit_date)->format('d/M/Y') }}</div>
                                <div class="text-[10px] text-gray-400">Tgl Kunjungan</div>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-gray-400">
                            @if($leader->checked_in_at)
                                <div>{{ $leader->checked_in_at->format('d/M/Y') }}</div>
                                <div class="text-[10px] font-bold text-gray-500 mt-0.5">{{ $leader->checked_in_at->format('H:i') }} WIB</div>
                            @else
                                <span class="text-[10px] text-gray-400 italic">Belum masuk</span>
                            @endif
                        </td>

                        {{-- Status --}}
                        @php
                            $hasPending = $tickets->contains(fn($t) => $t->status === 'pending');
                            $outAt = $tickets->max('checked_out_at');
                            $dur = ($allOut && $outAt && $leader->checked_in_at) ? \Carbon\Carbon::parse($outAt)->diffInMinutes($leader->checked_in_at) : 0;
                            $h = floor($dur/60); $m = $dur%60;
                        @endphp                        <td class="px-6 py-4 text-center">
                            @if($hasPending && !$anyIn)
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-bold bg-amber-100 text-amber-700">
                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span> Pending
                                </span>
                            @elseif($anyIn)
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-bold bg-green-100 text-green-700">
                                    <span class="w-1.5 h-1.5 rounded-full bg-green-500 animate-pulse"></span> Di Dalam
                                </span>
                            @else
                                <div class="flex flex-col items-center gap-1">
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-bold bg-gray-100 text-gray-400">
                                        <i data-lucide="check" class="w-3 h-3 text-green-500"></i> Selesai
                                    </span>
                                    @if($dur > 0)<span class="text-[9px] text-gray-400">{{ $h > 0 ? "{$h}j " : '' }}{{ $m }}mnt</span>@endif
                                </div>
                            @endif
                        </td>
                        {{-- Actions --}}
                        <td class="px-6 py-4 text-center">
                            <button type="button"
                                onclick="showGroupModal({{ json_encode($tickets->map(fn($t) => ['id'=>$t->id,'ticket_no'=>$t->ticket_no,'name'=>$t->name,'age'=>$t->age,'city'=>$t->city,'province'=>$t->province,'status'=>$t->status,'total_price'=>$t->total_price,'checked_in_at'=>$t->checked_in_at?->format('d M Y, H:i'),'checked_out_at'=>$t->checked_out_at?->format('d M Y, H:i'),'qty_male'=>$t->qty_male,'qty_female'=>$t->qty_female,'qty_kids'=>$t->qty_kids,'qty_total'=>$t->qty_total])->values()) }}, '{{ $groupId }}', '{{ addslashes($leader->name) }}', '{{ addslashes($destination->name) }}', '{{ $leader->payment_method }}', {{ $tickets->sum('qty_total') }}, {{ $tickets->sum('total_price') }})"
                                class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg text-[10px] font-bold bg-forest-50 text-forest-700 hover:bg-forest-100 border border-forest-100 transition-all">
                                <i data-lucide="users" class="w-3 h-3"></i> Detail
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center py-12 text-gray-400">
                            <i data-lucide="inbox" class="w-12 h-12 mx-auto text-gray-300 mb-3"></i>
                            <p class="font-medium text-sm">Tidak ada data rombongan yang cocok.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">{{ $visitors->links() }}</div>
</div>

{{-- Group Detail Modal --}}
<div id="monitoring-group-modal" class="fixed inset-0 z-50 hidden items-center justify-center p-4" style="background:rgba(0,0,0,0.5);">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl max-h-[90vh] flex flex-col overflow-hidden">
        {{-- Header --}}
        <div class="bg-gradient-to-r from-forest-600 to-forest-800 px-6 py-4 flex items-center justify-between shrink-0">
            <div>
                <h4 class="font-extrabold text-white text-sm" id="mgm-title">Detail Rombongan</h4>
                <p class="text-green-100 text-[11px] mt-0.5" id="mgm-subtitle"></p>
            </div>
            <button type="button" onclick="closeGroupModal()" class="w-8 h-8 rounded-full bg-white/10 hover:bg-white/20 flex items-center justify-center text-white transition-all">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>

        {{-- Summary bar --}}
        <div class="px-6 py-3 border-b border-gray-100 grid grid-cols-3 gap-4 shrink-0 text-xs bg-gray-50/50">
            <div><span class="text-gray-400 uppercase font-semibold text-[10px]">Penanggung Jawab</span><div class="font-bold text-gray-800 mt-0.5" id="mgm-leader"></div></div>
            <div><span class="text-gray-400 uppercase font-semibold text-[10px]">Metode Bayar</span><div class="font-bold text-gray-800 mt-0.5" id="mgm-method"></div></div>
            <div><span class="text-gray-400 uppercase font-semibold text-[10px]">Total Bayar</span><div class="font-bold text-forest-700 mt-0.5" id="mgm-total"></div></div>
        </div>

        {{-- Member table --}}
        <div class="overflow-y-auto flex-1">
            <table class="w-full text-xs text-left">
                <thead class="bg-gray-50 sticky top-0 z-10">
                    <tr class="text-[10px] font-bold text-gray-500 uppercase tracking-wider border-b border-gray-100">
                        <th class="px-4 py-3">
                            <input type="checkbox" id="mgm-check-all" class="rounded" title="Pilih semua">
                        </th>
                            <th class="px-4 py-3">No. Tiket</th>
                            <th class="px-4 py-3">Nama</th>
                            <th class="px-4 py-3">Usia</th>
                            <th class="px-4 py-3">Jenis Kelamin / Demografi</th>
                            <th class="px-4 py-3">Asal</th>
                            <th class="px-4 py-3 text-center">Check-In</th>
                            <th class="px-4 py-3 text-center">Check-Out</th>
                            <th class="px-4 py-3 text-center">Status</th>
                            <th class="px-4 py-3 text-center">Ubah Status</th>
                            <th class="px-4 py-3 text-center">Tiket</th>
                        </tr>
                    </thead>
                    <tbody id="mgm-members" class="divide-y divide-gray-100 text-gray-700"></tbody>
                </table>
        </div>

        {{-- Footer actions --}}
        <div class="px-6 py-3 border-t border-gray-100 flex items-center justify-between gap-3 shrink-0 bg-gray-50/50">
            <span class="text-[10px] text-gray-400" id="mgm-selected-count">0 dipilih</span>
            <div class="flex items-center gap-2">
                <select id="mgm-bulk-status" class="text-xs border border-gray-200 rounded-xl px-3 py-2 bg-white focus:outline-none focus:border-forest-500 cursor-pointer">
                    <option value="pending">Pending</option>
                    <option value="in">Di Dalam</option>
                    <option value="out">Keluar</option>
                </select>
                <button type="button" onclick="closeGroupModal()" class="px-4 py-2 text-xs font-semibold text-gray-500 border border-gray-200 rounded-xl hover:bg-gray-50 transition-colors">
                    Tutup
                </button>
                <button type="button" id="mgm-bulk-btn" onclick="submitBulkStatus(false)"
                    class="px-4 py-2 text-xs font-bold text-white bg-forest-600 hover:bg-forest-700 rounded-xl transition-colors flex items-center gap-1.5 disabled:opacity-40 disabled:cursor-not-allowed"
                    disabled>
                    <i data-lucide="refresh-cw" class="w-3.5 h-3.5"></i>
                    <span id="mgm-bulk-label">Ubah Dipilih</span>
                </button>
                <button type="button" id="mgm-bulk-all-btn" onclick="submitBulkStatus(true)"
                    class="px-4 py-2 text-xs font-bold text-white bg-gray-600 hover:bg-gray-700 rounded-xl transition-colors flex items-center gap-1.5">
                    <i data-lucide="users" class="w-3.5 h-3.5"></i> Ubah Semua
                </button>
            </div>
        </div>
    </div>
</div>

<script>
let _mgmGroupId = null;

function showGroupModal(members, groupId, leader, destination, method, count, total) {
    _mgmGroupId = groupId;
    document.getElementById('mgm-title').textContent = 'Rombongan: ' + groupId;
    document.getElementById('mgm-subtitle').textContent = destination + ' · ' + count + ' Orang';
    document.getElementById('mgm-leader').textContent = leader;
    document.getElementById('mgm-method').textContent = method;
    document.getElementById('mgm-total').textContent = 'Rp ' + total.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');

    const tbody = document.getElementById('mgm-members');
    tbody.innerHTML = members.map((m, i) => {
        const isIn = m.status === 'in';
        const isPending = m.status === 'pending';
        const isOut = m.status === 'out';
        const canCheckbox = true; // all rows selectable for status change
        const statusBadge = isIn
            ? `<span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-green-100 text-green-700"><span class="w-1 h-1 rounded-full bg-green-500 animate-pulse"></span> Di Dalam</span>`
            : isPending
                ? `<span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-700"><span class="w-1 h-1 rounded-full bg-amber-500 animate-pulse"></span> Pending</span>`
                : `<span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-gray-100 text-gray-400">Keluar</span>`;

        const statusOptions = ['pending','in','out'].map(s =>
            `<option value="${s}" ${m.status === s ? 'selected' : ''}>${s === 'pending' ? 'Pending' : s === 'in' ? 'Di Dalam' : 'Keluar'}</option>`
        ).join('');

        return `
        <tr class="hover:bg-gray-50/50 transition-colors" id="mgm-row-${m.id}">
            <td class="px-4 py-3">
                ${canCheckbox ? `<input type="checkbox" name="visitor_ids[]" value="${m.id}" class="mgm-cb rounded" onchange="updateCheckoutBtn()">` : '<span class="w-4 h-4 block"></span>'}
            </td>
            <td class="px-4 py-3 font-bold text-gray-800">${m.ticket_no}</td>
            <td class="px-4 py-3 font-semibold">${m.name}</td>
            <td class="px-4 py-3">${m.age ?? '-'} thn</td>
            <td class="px-4 py-3">
                ${m.qty_total > 1
                    ? `<div class="flex gap-1.5 text-[10px] font-bold">
                        <span class="text-blue-600">L:${m.qty_male}</span>
                        <span class="text-pink-600">P:${m.qty_female}</span>
                        ${m.qty_kids > 0 ? `<span class="text-amber-600">A:${m.qty_kids}</span>` : ''}
                       </div>
                       <div class="text-[10px] text-gray-400">${m.qty_total} orang</div>`
                    : `<span class="text-[10px] font-bold ${m.qty_male > 0 ? 'text-blue-600' : 'text-pink-600'}">${m.qty_male > 0 ? 'Laki-laki' : 'Perempuan'}</span>`
                }
            </td>
            <td class="px-4 py-3 text-gray-500">${[m.city, m.province].filter(Boolean).join(', ') || '-'}</td>
            <td class="px-4 py-3 text-center text-gray-500">${m.checked_in_at || '-'}</td>
            <td class="px-4 py-3 text-center">
                ${m.checked_out_at
                    ? `<span class="text-gray-500">${m.checked_out_at}</span>`
                    : `<span class="text-[10px] text-gray-400 italic">—</span>`
                }
            </td>
            <td class="px-4 py-3 text-center">${statusBadge}</td>
            <td class="px-4 py-3 text-center">
                <div class="flex items-center gap-1 justify-center">
                    <form class="mgm-status-form flex items-center gap-1" data-visitor-id="${m.id}">
                        <select class="mgm-status-select text-[10px] border border-gray-200 rounded-lg px-1.5 py-1 bg-white focus:outline-none focus:border-forest-500 cursor-pointer">
                            ${statusOptions}
                        </select>
                        <button type="submit" class="px-2 py-1 text-[10px] font-bold bg-forest-600 hover:bg-forest-700 text-white rounded-lg transition-colors">
                            Ubah
                        </button>
                    </form>
                </div>
            </td>
            <td class="px-4 py-3 text-center">
                <button type="button" class="mgm-ticket-btn inline-flex items-center gap-1 px-2 py-1 rounded-lg text-[10px] font-bold bg-forest-50 text-forest-700 hover:bg-forest-100 border border-forest-100 transition-all"
                    data-index="${i}">
                    <i data-lucide="ticket" class="w-3 h-3"></i> Tiket
                </button>
            </td>
        </tr>`;
    }).join('');

    // Status change handler
    tbody.querySelectorAll('.mgm-status-form').forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            const visitorId = this.dataset.visitorId;
            const newStatus = this.querySelector('.mgm-status-select').value;
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content
                || document.querySelector('input[name="_token"]')?.value;

            fetch(`/admin/monitoring/${visitorId}/status`, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                },
                body: JSON.stringify({ status: newStatus })
            }).then(res => {
                if (res.ok || res.redirected) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Status diperbarui',
                        text: 'Status tiket berhasil diubah ke ' + (newStatus === 'pending' ? 'Pending' : newStatus === 'in' ? 'Di Dalam' : 'Keluar'),
                        timer: 1500,
                        showConfirmButton: false,
                    }).then(() => window.location.reload());
                } else {
                    Swal.fire({ icon: 'error', title: 'Gagal', text: 'Terjadi kesalahan saat mengubah status.' });
                }
            }).catch(() => {
                Swal.fire({ icon: 'error', title: 'Gagal', text: 'Tidak dapat terhubung ke server.' });
            });
        });
    });

    // Ticket button handler — use index to access members array directly (avoids escaping issues)
    tbody.querySelectorAll('.mgm-ticket-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const m = members[parseInt(this.dataset.index)];
            const price = 'Rp ' + (m.total_price||0).toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
            showTicketModal(m.ticket_no, m.name, destination, m.qty_total||count, method, price, m.checked_in_at, m.status, '', '', '', leader, m.qty_male, m.qty_female, m.qty_kids);
        });
    });

    // Check-all toggle
    const checkAll = document.getElementById('mgm-check-all');
    checkAll.checked = false;
    checkAll.onchange = function() {
        document.querySelectorAll('.mgm-cb').forEach(cb => cb.checked = this.checked);
        updateCheckoutBtn();
    };

    updateCheckoutBtn();

    const modal = document.getElementById('monitoring-group-modal');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
    if (window.lucide) window.lucide.createIcons();
}

function updateCheckoutBtn() {
    const checked = document.querySelectorAll('.mgm-cb:checked').length;
    const total = document.querySelectorAll('.mgm-cb').length;
    const btn = document.getElementById('mgm-bulk-btn');
    const label = document.getElementById('mgm-bulk-label');
    const countEl = document.getElementById('mgm-selected-count');

    countEl.textContent = checked + ' dari ' + total + ' dipilih';
    btn.disabled = checked === 0;
    label.textContent = 'Ubah Dipilih (' + checked + ')';
}

function submitBulkStatus(all) {
    const newStatus = document.getElementById('mgm-bulk-status').value;
    const statusLabel = newStatus === 'pending' ? 'Pending' : newStatus === 'in' ? 'Di Dalam' : 'Keluar';

    let ids = [];
    if (all) {
        document.querySelectorAll('.mgm-cb').forEach(cb => ids.push(cb.value));
    } else {
        document.querySelectorAll('.mgm-cb:checked').forEach(cb => ids.push(cb.value));
    }

    if (ids.length === 0) {
        Swal.fire({ icon: 'warning', title: 'Pilih anggota', text: 'Pilih minimal satu anggota.', timer: 1500, showConfirmButton: false });
        return;
    }

    Swal.fire({
        title: 'Konfirmasi Ubah Status',
        text: `Ubah status ${ids.length} anggota menjadi "${statusLabel}"?`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#15803d',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Ya, Ubah',
        cancelButtonText: 'Batal',
    }).then(result => {
        if (!result.isConfirmed) return;

        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
        Promise.all(ids.map(id =>
            fetch(`/admin/monitoring/${id}/status`, {
                method: 'PATCH',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
                body: JSON.stringify({ status: newStatus })
            })
        )).then(() => {
            Swal.fire({
                icon: 'success',
                title: 'Status diperbarui',
                text: `${ids.length} anggota berhasil diubah ke "${statusLabel}"`,
                timer: 1500,
                showConfirmButton: false,
            }).then(() => window.location.reload());
        }).catch(() => {
            Swal.fire({ icon: 'error', title: 'Gagal', text: 'Terjadi kesalahan.' });
        });
    });
}

function closeGroupModal() {
    const modal = document.getElementById('monitoring-group-modal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
}

document.getElementById('monitoring-group-modal').addEventListener('click', function(e) {
    if (e.target === this) closeGroupModal();
});
</script>

@include('admin.partials.ticket_modal')
@endsection
