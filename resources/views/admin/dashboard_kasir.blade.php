@extends('layouts.admin')

@section('title', 'Dashboard Kasir')

@section('content')
<div class="mb-8 bg-gradient-to-br from-forest-700 to-forest-900 rounded-2xl p-6 sm:p-8 text-white shadow-md relative overflow-hidden" style="background: linear-gradient(135deg, #15803d 0%, #14532d 100%);">
    <div class="absolute right-0 bottom-0 opacity-10 pointer-events-none transform translate-x-12 translate-y-12">
        <i data-lucide="mountain" class="w-96 h-96"></i>
    </div>
    
    <div class="relative z-10">
        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-white/20 text-white mb-4">
            <span class="w-1.5 h-1.5 rounded-full bg-green-400 animate-pulse"></span>
            Tugas Aktif: {{ $destination->name }}
        </span>
        <h2 class="text-2xl sm:text-3xl font-extrabold mb-2" style="border-left:none; padding-left:0; color:white; margin:0;">Selamat Datang, Kasir {{ Auth::user()->name }}!</h2>
        <p class="max-w-xl text-sm leading-relaxed" style="color: #dcfce7; margin-top: 8px; margin-bottom: 0;">
            Kelola penjualan tiket masuk dan pantau arus pengunjung di **{{ $destination->name }}** secara langsung dari panel ini.
        </p>
    </div>
</div>

{{-- Metrics Row --}}
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
    <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm flex items-center justify-between">
        <div>
            <div class="text-sm font-medium text-gray-500 mb-1">Harga Tiket Masuk</div>
            <div class="text-2xl font-bold text-gray-800">Rp {{ number_format($stats['price'], 0, ',', '.') }}</div>
            <div class="text-xs text-gray-400 mt-1">Status: {{ $destination->is_active ? 'Aktif di Web' : 'Draft/Nonaktif' }}</div>
        </div>
        <div class="w-12 h-12 rounded-xl bg-forest-50 flex items-center justify-center text-forest-600">
            <i data-lucide="ticket" class="w-6 h-6"></i>
        </div>
    </div>
    
    <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm flex items-center justify-between">
        <div>
            <div class="text-sm font-medium text-gray-500 mb-1">Total Tiket Terjual (Hari Ini)</div>
            <div class="text-2xl font-bold text-gray-800" id="total-tickets">{{ $stats['tickets_sold'] }}</div>
            <div class="text-xs text-green-500 mt-1 font-medium flex items-center gap-1">
                <i data-lucide="trending-up" class="w-3.5 h-3.5"></i> +12% dibanding kemarin
            </div>
        </div>
        <div class="w-12 h-12 rounded-xl bg-blue-50 flex items-center justify-center text-blue-600">
            <i data-lucide="users" class="w-6 h-6"></i>
        </div>
    </div>
    
    <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm flex items-center justify-between">
        <div>
            <div class="text-sm font-medium text-gray-500 mb-1">Total Pendapatan (Hari Ini)</div>
            <div class="text-2xl font-bold text-gray-800" id="total-revenue" data-base="{{ $stats['price'] }}">Rp {{ number_format($stats['revenue'], 0, ',', '.') }}</div>
            <div class="text-xs text-gray-400 mt-1">Estimasi kotor dari tiket terjual</div>
        </div>
        <div class="w-12 h-12 rounded-xl bg-emerald-50 flex items-center justify-center text-emerald-600">
            <i data-lucide="wallet" class="w-6 h-6"></i>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    {{-- Ticket Sales Simulator POS --}}
    <div class="lg:col-span-1 bg-white rounded-2xl border border-gray-100 shadow-sm p-6 flex flex-col h-fit">
        <h3 class="font-bold text-gray-800 flex items-center gap-2 mb-4" style="margin-top:0;">
            <i data-lucide="shopping-cart" class="w-5 h-5 text-gray-400"></i>
            Loket Penjualan Tiket
        </h3>
        
        <div class="space-y-4">
            <div>
                <label for="ticket-qty" class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-2">Jumlah Tiket (Orang)</label>
                <div class="flex items-center gap-2">
                    <button type="button" onclick="adjustQty(-1)" class="w-10 h-10 border border-gray-200 rounded-xl flex items-center justify-center text-gray-600 hover:bg-gray-50 transition-colors font-bold">-</button>
                    <input type="number" id="ticket-qty" value="1" min="1" max="100" class="flex-1 h-10 border border-gray-200 rounded-xl text-center text-sm font-bold focus:outline-none focus:border-forest-500" readonly>
                    <button type="button" onclick="adjustQty(1)" class="w-10 h-10 border border-gray-200 rounded-xl flex items-center justify-center text-gray-600 hover:bg-gray-50 transition-colors font-bold">+</button>
                </div>
            </div>
            
            <div>
                <label for="payment-method" class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-2">Metode Pembayaran</label>
                <select id="payment-method" class="w-full h-10 border border-gray-200 rounded-xl px-3 text-sm focus:outline-none focus:border-forest-500 cursor-pointer">
                    <option value="Tunai">Tunai / Cash</option>
                    <option value="Transfer">Transfer Bank</option>
                </select>
            </div>
            
            <div class="bg-gray-50 rounded-xl p-4 border border-gray-100 flex flex-col gap-2">
                <div class="flex items-center justify-between text-xs text-gray-500">
                    <span>Harga Satuan:</span>
                    <span>Rp {{ number_format($stats['price'], 0, ',', '.') }}</span>
                </div>
                <div class="flex items-center justify-between text-sm font-bold text-gray-800 border-t border-gray-200/80 pt-2">
                    <span>Total Bayar:</span>
                    <span id="pos-total-pay">Rp {{ number_format($stats['price'], 0, ',', '.') }}</span>
                </div>
            </div>
            
            <button type="button" onclick="processSale()" class="w-full bg-forest-600 hover:bg-forest-700 text-white h-11 rounded-xl font-semibold text-sm transition-colors shadow-sm flex items-center justify-center gap-2">
                <i data-lucide="printer" class="w-4.5 h-4.5"></i> Proses &amp; Cetak Tiket
            </button>
        </div>
    </div>
    
    {{-- Recent Transactions & Interactive Receipt Visualizer --}}
    <div class="lg:col-span-2 space-y-6">
        {{-- Receipt Viewer Panel (Dynamically shown on print) --}}
        <div id="receipt-visualizer" class="hidden bg-yellow-50/75 border border-yellow-200 rounded-2xl p-6 relative overflow-hidden transition-all duration-300">
            <div class="absolute -right-6 -top-6 w-24 h-24 bg-yellow-100 rounded-full opacity-50"></div>
            <div class="flex justify-between items-start gap-4 mb-4">
                <div>
                    <h4 class="font-extrabold text-sm text-yellow-800 uppercase tracking-wider flex items-center gap-2">
                        <i data-lucide="check-circle" class="w-4 h-4 text-green-600"></i> Tiket Berhasil Dicetak
                    </h4>
                    <p class="text-xs text-yellow-700 mt-1">E-Ticket resmi Wisata Sanggabuana</p>
                </div>
                <button type="button" onclick="closeReceipt()" class="text-yellow-600 hover:text-yellow-800 text-xs font-bold">&times; Tutup</button>
            </div>
            
            <div class="bg-white border-2 border-dashed border-yellow-200 rounded-xl p-4 flex flex-col sm:flex-row gap-4 justify-between items-center shadow-sm">
                <div class="space-y-1.5 text-center sm:text-left">
                    <div class="text-xs text-gray-400">NOMOR TIKET</div>
                    <div class="font-bold text-gray-800 text-base" id="rcpt-no">#TKT-20260518-001</div>
                    <div class="text-xs text-gray-600"><strong id="rcpt-qty">1</strong> Tiket Masuk &bull; <strong id="rcpt-method">Tunai</strong></div>
                    <div class="text-xs text-gray-500 font-medium">Destinasi: {{ $destination->name }}</div>
                </div>
                <div class="flex flex-col items-center shrink-0">
                    {{-- Simulated QR Code Box --}}
                    <div class="w-16 h-16 border border-gray-200 bg-gray-50 p-1 flex items-center justify-center rounded">
                        <div class="w-full h-full bg-cover" style="background-image: url('https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=TKT-SANGGABUANA-VALID-2026');"></div>
                    </div>
                    <span class="text-[9px] text-gray-400 mt-1 uppercase font-bold tracking-wider">Scan Check-In</span>
                </div>
            </div>
        </div>

        {{-- Transactions Table --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden flex flex-col">
            <div class="px-6 py-5 border-b border-gray-100 flex items-center justify-between">
                <h3 class="font-bold text-gray-800 flex items-center gap-2" style="margin:0;">
                    <i data-lucide="receipt" class="w-5 h-5 text-gray-400"></i>
                    Log Transaksi Penjualan Terakhir
                </h3>
                <span class="text-xs font-semibold px-2.5 py-1 bg-gray-100 text-gray-500 rounded-full">Hari Ini</span>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50/50 border-b border-gray-100 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            <th class="px-6 py-4">ID Transaksi</th>
                            <th class="px-6 py-4">Jumlah Tiket</th>
                            <th class="px-6 py-4">Total Bayar</th>
                            <th class="px-6 py-4">Metode</th>
                            <th class="px-6 py-4">Waktu</th>
                            <th class="px-6 py-4 text-right">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-xs font-medium text-gray-600" id="transactions-ledger">
                        {{-- Simulated records --}}
                        <tr class="hover:bg-gray-50/50 transition-colors">
                            <td class="px-6 py-4 font-bold text-gray-800">#TKT-20260518-0247</td>
                            <td class="px-6 py-4">2 Orang</td>
                            <td class="px-6 py-4 text-gray-800 font-bold">Rp {{ number_format($stats['price'] * 2, 0, ',', '.') }}</td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-0.5 rounded bg-blue-50 text-blue-700 font-bold">QRIS</span>
                            </td>
                            <td class="px-6 py-4 text-gray-400">10 Menit yang lalu</td>
                            <td class="px-6 py-4 text-right">
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-green-100 text-green-700">
                                    <span class="w-1 h-1 rounded-full bg-green-500"></span> Lunas
                                </span>
                            </td>
                        </tr>
                        <tr class="hover:bg-gray-50/50 transition-colors">
                            <td class="px-6 py-4 font-bold text-gray-800">#TKT-20260518-0246</td>
                            <td class="px-6 py-4">5 Orang</td>
                            <td class="px-6 py-4 text-gray-800 font-bold">Rp {{ number_format($stats['price'] * 5, 0, ',', '.') }}</td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-0.5 rounded bg-gray-100 text-gray-700 font-bold">Tunai</span>
                            </td>
                            <td class="px-6 py-4 text-gray-400">25 Menit yang lalu</td>
                            <td class="px-6 py-4 text-right">
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-green-100 text-green-700">
                                    <span class="w-1 h-1 rounded-full bg-green-500"></span> Lunas
                                </span>
                            </td>
                        </tr>
                        <tr class="hover:bg-gray-50/50 transition-colors">
                            <td class="px-6 py-4 font-bold text-gray-800">#TKT-20260518-0245</td>
                            <td class="px-6 py-4">1 Orang</td>
                            <td class="px-6 py-4 text-gray-800 font-bold">Rp {{ number_format($stats['price'], 0, ',', '.') }}</td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-0.5 rounded bg-amber-50 text-amber-700 font-bold">Transfer</span>
                            </td>
                            <td class="px-6 py-4 text-gray-400">1 Jam yang lalu</td>
                            <td class="px-6 py-4 text-right">
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-green-100 text-green-700">
                                    <span class="w-1 h-1 rounded-full bg-green-500"></span> Lunas
                                </span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
    const ticketQtyInput = document.getElementById('ticket-qty');
    const posTotalPay = document.getElementById('pos-total-pay');
    const totalTicketsElem = document.getElementById('total-tickets');
    const totalRevenueElem = document.getElementById('total-revenue');
    
    // Retrieve base ticket price
    const ticketPrice = parseInt(totalRevenueElem.getAttribute('data-base'));
    
    // Initial stats count
    let currentTicketsSold = parseInt(totalTicketsElem.innerText);
    let currentRevenue = currentTicketsSold * ticketPrice;

    function formatRupiah(number) {
        return 'Rp ' + number.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
    }

    function adjustQty(amount) {
        let currentQty = parseInt(ticketQtyInput.value);
        currentQty += amount;
        if (currentQty < 1) currentQty = 1;
        if (currentQty > 100) currentQty = 100;
        
        ticketQtyInput.value = currentQty;
        
        // Update POS total cost
        posTotalPay.innerText = formatRupiah(currentQty * ticketPrice);
    }

    function closeReceipt() {
        document.getElementById('receipt-visualizer').classList.add('hidden');
    }

    function processSale() {
        const qty = parseInt(ticketQtyInput.value);
        const paymentMethod = document.getElementById('payment-method').value;
        const totalCost = qty * ticketPrice;
        
        // Generate simulated ID
        const serialNum = currentTicketsSold + 1;
        const transactionId = `#TKT-20260518-0` + serialNum;
        
        // 1. Update overall dashboard metrics on-screen
        currentTicketsSold += qty;
        currentRevenue += totalCost;
        
        totalTicketsElem.innerText = currentTicketsSold;
        totalRevenueElem.innerText = formatRupiah(currentRevenue);
        
        // 2. Configure & open visual receipt
        document.getElementById('rcpt-no').innerText = transactionId;
        document.getElementById('rcpt-qty').innerText = qty + ' Orang';
        document.getElementById('rcpt-method').innerText = paymentMethod;
        
        const receiptBox = document.getElementById('receipt-visualizer');
        receiptBox.classList.remove('hidden');
        receiptBox.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        
        // 3. Prepend dynamic transaction record to the table list
        const ledgerTable = document.getElementById('transactions-ledger');
        const newRow = document.createElement('tr');
        newRow.className = 'bg-forest-50/30 hover:bg-forest-50/50 transition-colors animate-pulse';
        newRow.style.animationDuration = '1.5s';
        
        let methodBadgeClass = 'bg-gray-100 text-gray-700';
        if (paymentMethod === 'QRIS') methodBadgeClass = 'bg-blue-50 text-blue-700';
        if (paymentMethod === 'Transfer') methodBadgeClass = 'bg-amber-50 text-amber-700';
        
        newRow.innerHTML = `
            <td class="px-6 py-4 font-bold text-gray-800">${transactionId}</td>
            <td class="px-6 py-4">${qty} Orang</td>
            <td class="px-6 py-4 text-gray-800 font-bold">${formatRupiah(totalCost)}</td>
            <td class="px-6 py-4">
                <span class="px-2 py-0.5 rounded ${methodBadgeClass} font-bold">${paymentMethod}</span>
            </td>
            <td class="px-6 py-4 text-forest-600 font-semibold">Baru Saja</td>
            <td class="px-6 py-4 text-right">
                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-green-100 text-green-700">
                    <span class="w-1 h-1 rounded-full bg-green-500"></span> Lunas
                </span>
            </td>
        `;
        
        ledgerTable.insertBefore(newRow, ledgerTable.firstChild);
        
        // Reset POS form back to 1
        ticketQtyInput.value = 1;
        posTotalPay.innerText = formatRupiah(ticketPrice);
        
        // Remove animation pulse from table row after 3 seconds
        setTimeout(() => {
            newRow.classList.remove('animate-pulse');
            newRow.classList.remove('bg-forest-50/30');
        }, 3000);
    }
</script>
@endsection
