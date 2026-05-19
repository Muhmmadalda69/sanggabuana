@extends('layouts.admin')

@section('title', 'POS Loket Penjualan Tiket')

@push('styles')
<style>
    /* Custom TomSelect styling to match Tailwind UI */
    .ts-wrapper.single .ts-control {
        background-color: #ffffff !important; /* bg-white */
        border: 1px solid #e5e7eb !important; /* border-gray-200 */
        border-radius: 0.75rem !important; /* rounded-xl */
        padding: 0.625rem 0.75rem 0.625rem 2.5rem !important; /* py-2.5 pl-10 pr-3 */
        font-size: 0.875rem; /* text-sm */
        min-height: 44px;
        box-shadow: none;
        transition: all 0.2s;
    }
    .ts-wrapper.single.focus .ts-control {
        border-color: #10b981; /* focus:border-forest-500 */
        box-shadow: 0 0 0 1px #10b981;
    }
    .ts-wrapper.disabled .ts-control {
        background-color: #f3f4f6 !important; /* bg-gray-100 */
        opacity: 0.7;
        cursor: not-allowed;
    }
    .ts-wrapper.single .ts-control input {
        font-size: 0.875rem;
    }
    .ts-dropdown {
        border-radius: 0.75rem;
        border-color: #e5e7eb;
        box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
        font-size: 0.875rem;
        margin-top: 4px;
        overflow: hidden;
    }
    .ts-dropdown .option {
        padding: 8px 12px;
    }
    .ts-dropdown .option.active {
        background-color: #ecfdf5; /* bg-forest-50 */
        color: #065f46; /* text-forest-800 */
    }
    /* Add absolute icons for TomSelect containers manually */
    .ts-icon-container {
        position: absolute;
        top: 0;
        bottom: 0;
        left: 0;
        padding-left: 1rem;
        display: flex;
        align-items: center;
        color: #9ca3af;
        z-index: 10;
        pointer-events: none;
    }
</style>
@endpush

@section('content')
<div class="mb-8 bg-gradient-to-br from-forest-700 to-forest-900 rounded-2xl p-6 sm:p-8 text-white shadow-md relative overflow-hidden" style="background: linear-gradient(135deg, #15803d 0%, #14532d 100%);">
    <div class="absolute right-0 bottom-0 opacity-10 pointer-events-none transform translate-x-12 translate-y-12">
        <i data-lucide="mountain" class="w-96 h-96"></i>
    </div>
    
    <div class="relative z-10">
        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-white/20 text-white mb-4">
            <span class="w-1.5 h-1.5 rounded-full bg-green-400 animate-pulse"></span>
            Tugas Loket Aktif: {{ $destination->name }}
        </span>
        <h2 class="text-2xl sm:text-3xl font-extrabold mb-2" style="border-left:none; padding-left:0; color:white; margin:0;">POS Tiket Destinasi</h2>
        <p class="max-w-xl text-sm leading-relaxed" style="color: #dcfce7; margin-top: 8px; margin-bottom: 0;">
            Formulir loket pendaftaran masuk otomatis beradaptasi berdasarkan destinasi wisata aktif Anda.
        </p>
    </div>
</div>

<div class="flex flex-col gap-8">
    {{-- POS Input Form --}}
    <div class="w-full bg-white rounded-2xl border border-gray-100 shadow-sm p-6 sm:p-8 flex flex-col">
        <h3 class="font-bold text-gray-800 flex items-center gap-2 mb-6" style="margin-top:0;">
            <i data-lucide="shopping-cart" class="w-5 h-5 text-gray-400"></i>
            Registrasi Pengunjung Baru
        </h3>
        
        <form id="pos-ticket-form" action="{{ route('admin.pos.store') }}" method="POST" class="space-y-6">
            @csrf
            <input type="hidden" name="destination_id" value="{{ $destination->id }}">
            <input type="hidden" id="ticket-base-price" value="{{ (int) $destination->price }}">
            
            {{-- Form Fields Responsive Grid --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                {{-- Name Penanggung Jawab --}}
                <div>
                    <label for="name" class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-2">Nama Penanggung Jawab</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-gray-400">
                            <i data-lucide="user" class="w-4.5 h-4.5"></i>
                        </span>
                        <input type="text" name="name" id="name" required placeholder="Masukkan nama" class="w-full pl-12 pr-3 py-2.5 bg-white border border-gray-200 rounded-xl text-sm focus:outline-none focus:border-forest-500 transition-all">
                    </div>
                </div>

                {{-- Jenis Kelamin Penanggung Jawab --}}
                <div>
                    <label for="leader_gender" class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-2">Jenis Kelamin Penanggung Jawab</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-gray-400">
                            <i data-lucide="users" class="w-4.5 h-4.5"></i>
                        </span>
                        <select name="leader_gender" id="leader_gender" required class="w-full pl-12 pr-8 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:border-forest-500 transition-all appearance-none cursor-pointer bg-white">
                            <option value="L">Laki-laki</option>
                            <option value="P">Perempuan</option>
                        </select>
                        <span class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 pointer-events-none">
                            <i data-lucide="chevron-down" class="w-4 h-4"></i>
                        </span>
                    </div>
                </div>

                {{-- Community (Dynamic) --}}
                @if($destination->has_community)
                    <div>
                        <label for="community" class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-2">Nama Komunitas <span class="text-[10px] font-normal text-gray-400">(Opsional)</span></label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-gray-400">
                                <i data-lucide="users-round" class="w-4.5 h-4.5"></i>
                            </span>
                            <input type="text" name="community" id="community" placeholder="Contoh: KPA Rimba Raya" class="w-full pl-12 pr-3 py-2.5 bg-white border border-gray-200 rounded-xl text-sm focus:outline-none focus:border-forest-500 transition-all">
                        </div>
                    </div>
                @endif

                {{-- Purpose (Dynamic) --}}
                @if($destination->has_purpose)
                    <div>
                        <label for="purpose" class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-2">Tujuan Kunjungan</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-gray-400">
                                <i data-lucide="mountain" class="w-4.5 h-4.5"></i>
                            </span>
                            <select name="purpose" id="purpose" required class="w-full pl-12 pr-8 py-2.5 bg-white border border-gray-200 rounded-xl text-sm focus:outline-none focus:border-forest-500 transition-all appearance-none cursor-pointer">
                                <option value="Hiking">Hiking &amp; Camping</option>
                                <option value="Trail Run">Trail Running / Olahraga</option>
                                <option value="Jiarah">Wisata Religi / Ziarah</option>
                            </select>
                            <span class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 pointer-events-none">
                                <i data-lucide="chevron-down" class="w-4 h-4"></i>
                            </span>
                        </div>

                        <div id="camping-duration-container" class="mt-4 hidden transition-all duration-300">
                            <label for="camping_duration" class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-2">Lama Camping (Malam)</label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-gray-400">
                                    <i data-lucide="moon" class="w-4.5 h-4.5"></i>
                                </span>
                                <input type="number" name="camping_duration" id="camping_duration" min="1" value="1" class="w-full pl-12 pr-3 py-2.5 bg-white border border-gray-200 rounded-xl text-sm focus:outline-none focus:border-forest-500 transition-all" placeholder="Contoh: 1">
                            </div>
                        </div>
                    </div>
                @endif

                {{-- Kategori Wilayah --}}
                <div>
                    <label for="address_type" class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-2">Kategori Wilayah</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-gray-400">
                            <i data-lucide="globe" class="w-4.5 h-4.5"></i>
                        </span>
                        <select name="address_type" id="address_type" required class="w-full pl-12 pr-8 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:border-forest-500 transition-all appearance-none cursor-pointer bg-white">
                            <option value="lokal">Lokal (Warga Sekitar)</option>
                            <option value="indonesia" selected>Indonesia (Domestik)</option>
                            <option value="mancanegara">Mancanegara (Luar Negeri)</option>
                        </select>
                        <span class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 pointer-events-none">
                            <i data-lucide="chevron-down" class="w-4 h-4"></i>
                        </span>
                    </div>
                </div>

                {{-- Provinsi / Negara --}}
                <div>
                    <label id="lbl_province" for="province" class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-2">Provinsi</label>
                    <div class="relative">
                        <div class="ts-icon-container"><i data-lucide="map" class="w-4.5 h-4.5"></i></div>
                        <select name="province" id="province" required placeholder="Pilih Provinsi..."></select>
                    </div>
                </div>

                {{-- Kota / Kecamatan --}}
                <div>
                    <label id="lbl_city" for="city_input" class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-2">Kota / Kabupaten</label>
                    <div class="relative">
                        <div class="ts-icon-container"><i data-lucide="building" class="w-4.5 h-4.5"></i></div>
                        <select name="city" id="city_input" required placeholder="Pilih Kota..."></select>
                    </div>
                </div>

                {{-- Group Average Age --}}
                <div>
                    <label for="avg_age" class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-2">Rata-rata Usia Pengunjung</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-gray-400">
                            <i data-lucide="calendar" class="w-4.5 h-4.5"></i>
                        </span>
                        <input type="number" name="avg_age" id="avg_age" value="25" min="5" max="99" required class="w-full pl-12 pr-3 py-2.5 bg-white border border-gray-200 rounded-xl text-sm focus:outline-none focus:border-forest-500 transition-all text-center font-bold">
                    </div>
                </div>

                {{-- Detail Jumlah Rombongan (Accordion) --}}
                <div class="col-span-1 md:col-span-2 lg:col-span-3 border border-gray-150/70 rounded-2xl overflow-hidden shadow-sm transition-all duration-300">
                    <button type="button" id="toggle-rombongan-accordion" class="w-full flex items-center justify-between p-4 bg-gray-50/60 hover:bg-gray-100/60 transition-colors focus:outline-none select-none">
                        <div class="flex items-center gap-2 text-left">
                            <div>
                                <h4 class="font-bold text-gray-800 text-xs uppercase tracking-wider mb-0.5" style="margin: 0;">
                                    Detail Jumlah Rombongan
                                </h4>
                                <p class="text-xs text-gray-400 font-normal normal-case leading-none my-0.5">
                                    Penanggung jawab otomatis terhitung
                                </p>
                                <div class="flex items-center mt-2">
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-forest-50 text-forest-700 border border-forest-100">
                                        <span>Total:</span>
                                        <span id="gunung-calc-total-badge">1</span>
                                        <span>Orang</span>
                                    </span>
                                    <i id="accordion-chevron" data-lucide="chevron-down" class="w-4 h-4 text-gray-400 transition-transform duration-300"></i>
                                </div>
                            </div>
                        </div>
                    </button>

                    <div id="rombongan-accordion-content" class="max-h-0 overflow-hidden transition-all duration-300 ease-in-out bg-white" style="max-height: 0px;">
                        <div class="p-4 sm:p-5 border-t border-gray-100 flex flex-col gap-4">
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                                <div>
                                    <label for="qty_male" class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-2">Laki-laki <span class="text-[10px] text-gray-400 font-normal">(Dewasa)</span></label>
                                    <div class="relative">
                                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400 pointer-events-none">
                                            <i data-lucide="user" class="w-4 h-4 text-blue-500"></i>
                                        </span>
                                        <input type="number" name="qty_male" id="qty_male" value="0" min="0" required class="w-full pl-9 pr-3 py-2 bg-white border border-gray-200 rounded-xl text-sm font-bold text-center focus:outline-none focus:border-forest-500 transition-all">
                                    </div>
                                </div>

                                <div>
                                    <label for="qty_female" class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-2">Perempuan <span class="text-[10px] text-gray-400 font-normal">(Dewasa)</span></label>
                                    <div class="relative">
                                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400 pointer-events-none">
                                            <i data-lucide="user" class="w-4 h-4 text-pink-500"></i>
                                        </span>
                                        <input type="number" name="qty_female" id="qty_female" value="0" min="0" required class="w-full pl-9 pr-3 py-2 bg-white border border-gray-200 rounded-xl text-sm font-bold text-center focus:outline-none focus:border-forest-500 transition-all">
                                    </div>
                                </div>

                                <div>
                                    <label for="qty_kids" class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-2">Anak-anak <span class="text-[10px] text-gray-500 font-normal">(dibawah lima tahun)</span></label>
                                    <div class="relative">
                                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400 pointer-events-none">
                                            <i data-lucide="smile" class="w-4 h-4 text-amber-500"></i>
                                        </span>
                                        <input type="number" name="qty_kids" id="qty_kids" value="0" min="0" required class="w-full pl-9 pr-3 py-2 bg-white border border-gray-200 rounded-xl text-sm font-bold text-center focus:outline-none focus:border-forest-500 transition-all">
                                    </div>
                                </div>
                            </div>

                            {{-- Calculated total rombongan indicator --}}
                            <div class="bg-gray-50/50 border border-gray-200/80 rounded-xl px-4 py-2.5 text-xs sm:text-sm text-gray-500 flex justify-between items-center shadow-sm">
                                <span class="flex items-center gap-1.5 font-medium">
                                    <span class="w-2 h-2 rounded-full bg-forest-500 animate-pulse"></span>
                                    Total Rombongan (Laki-laki + Perempuan + Anak-anak + Penanggung Jawab):
                                </span>
                                <span class="font-extrabold text-forest-700 text-base"><span id="gunung-calc-total">1</span> Orang</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Payment Method --}}
                <div>
                    <label for="payment-method" class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-2">Metode Pembayaran</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-gray-400">
                            <i data-lucide="wallet" class="w-4.5 h-4.5"></i>
                        </span>
                        <select name="payment_method" id="payment-method" class="w-full pl-12 pr-8 py-2.5 bg-white border border-gray-200 rounded-xl text-sm focus:outline-none focus:border-forest-500 transition-all appearance-none cursor-pointer">
                            <option value="Tunai">Tunai / Cash</option>
                            <option value="QRIS">QRIS / LinkAja/Gopay/OVO</option>
                            <option value="Transfer">Transfer Bank</option>
                        </select>
                        <span class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 pointer-events-none">
                            <i data-lucide="chevron-down" class="w-4 h-4"></i>
                        </span>
                    </div>
                </div>
                
                {{-- Manual Price / HTM Override --}}
                <div>
                    <label for="price" class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-2">Harga Per Tiket (HTM) <span class="text-[9px] font-normal text-forest-600">(Dapat Diubah)</span></label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-gray-400 font-bold text-sm">Rp</span>
                        <input type="number" name="price" id="price" value="{{ (int) $destination->price }}" min="0" required class="w-full pl-12 pr-3 py-2.5 bg-white border border-gray-200 rounded-xl text-sm font-bold focus:outline-none focus:border-forest-500 transition-all">
                    </div>
                </div>
            </div>

            {{-- Summary & Checkout Bottom Row --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-center pt-6 border-t border-gray-100">
                {{-- Summary total pay --}}
                <div class="lg:col-span-2 bg-forest-50/30 rounded-xl p-4 border border-forest-100 flex flex-col gap-1">
                    <div class="flex items-center justify-between text-xs text-gray-500">
                        <span>Harga Tiket Satuan:</span>
                        <span class="font-semibold">Rp {{ number_format($destination->price, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex items-center justify-between text-sm font-black text-gray-800 border-t border-gray-200/80 pt-2">
                        <span>Total Estimasi Bayar:</span>
                        <span id="pos-total-pay" class="text-forest-700 text-lg">Rp {{ number_format($destination->price, 0, ',', '.') }}</span>
                    </div>
                </div>
                
                <button type="submit" class="lg:col-span-1 w-full bg-forest-600 hover:bg-forest-700 text-white h-12 rounded-xl font-bold text-sm transition-all shadow-md flex items-center justify-center gap-2">
                    <i data-lucide="printer" class="w-5 h-5"></i> Proses &amp; Cetak Tiket
                </button>
            </div>
        </form>
    </div>
    
    {{-- Recent Transactions List --}}
    <div class="w-full space-y-6">
        {{-- E-Ticket Receipt Visualizer (Shown after successful sale in PHP session) --}}
        @if(session('print_ticket_id'))
            @php
                $printedTicket = App\Models\Visitor::find(session('print_ticket_id'));
            @endphp
            @if($printedTicket)
                <div id="receipt-visualizer" class="relative overflow-hidden rounded-2xl shadow-lg border border-emerald-200/60 animate-fade-in">
                    {{-- Success Header Banner --}}
                    <div class="bg-gradient-to-r from-emerald-600 to-forest-700 px-6 py-4 flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-white/20 backdrop-blur-sm flex items-center justify-center">
                                <i data-lucide="check-circle-2" class="w-6 h-6 text-white"></i>
                            </div>
                            <div>
                                <h4 class="font-extrabold text-white text-sm tracking-wide">Tiket Berhasil Dicetak!</h4>
                                <p class="text-emerald-100 text-[11px] mt-0.5">E-Ticket resmi {{ $destination->name }}</p>
                            </div>
                        </div>
                        <button type="button" onclick="document.getElementById('receipt-visualizer').style.opacity='0';setTimeout(()=>document.getElementById('receipt-visualizer').remove(),300)" class="w-8 h-8 rounded-full bg-white/10 hover:bg-white/20 flex items-center justify-center text-white transition-all">
                            <i data-lucide="x" class="w-4 h-4"></i>
                        </button>
                    </div>
                    
                    {{-- Ticket Body --}}
                    <div class="bg-white px-6 py-6">
                        <div class="flex flex-col sm:flex-row gap-6 items-stretch">
                            {{-- Ticket Info --}}
                            <div class="flex-1 space-y-4">
                                <div class="flex mt-3 items-center gap-3">
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-100">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span> Aktif — Di Dalam Lokasi
                                    </span>
                                </div>
                                
                                <div>
                                    <div class="text-[10px] text-gray-400 font-semibold uppercase tracking-widest mb-1">Nomor Tiket</div>
                                    <div class="font-black text-gray-900 text-2xl tracking-tight">{{ $printedTicket->ticket_no }}</div>
                                </div>
                                
                                <div class="grid grid-cols-2 mb-3 gap-x-6 gap-y-3 text-xs pt-3 border-t border-dashed border-gray-200">
                                    <div>
                                        <span class="text-gray-400 text-[10px] uppercase tracking-wider font-semibold">Pengunjung</span>
                                        <div class="text-gray-800 font-bold mt-0.5">{{ $printedTicket->name }}</div>
                                    </div>
                                    <div>
                                        <span class="text-gray-400 text-[10px] uppercase tracking-wider font-semibold">Destinasi</span>
                                        <div class="text-gray-800 font-bold mt-0.5">{{ $destination->name }}</div>
                                    </div>
                                    <div>
                                        <span class="text-gray-400 text-[10px] uppercase tracking-wider font-semibold">Jumlah Rombongan</span>
                                        <div class="text-gray-800 font-bold mt-0.5">{{ $printedTicket->qty_total }} Orang</div>
                                    </div>
                                    <div>
                                        <span class="text-gray-400 text-[10px] uppercase tracking-wider font-semibold">Metode Bayar</span>
                                        <div class="text-gray-800 font-bold mt-0.5">{{ $printedTicket->payment_method }}</div>
                                    </div>
                                    <div>
                                        <span class="text-gray-400 text-[10px] uppercase tracking-wider font-semibold">Total Bayar</span>
                                        <div class="text-forest-700 font-extrabold mt-0.5">Rp {{ number_format($printedTicket->total_price, 0, ',', '.') }}</div>
                                    </div>
                                    <div>
                                        <span class="text-gray-400 text-[10px] uppercase tracking-wider font-semibold">Waktu Masuk</span>
                                        <div class="text-gray-800 font-bold mt-0.5">{{ $printedTicket->checked_in_at->format('d M Y, H:i') }}</div>
                                    </div>
                                    @if($printedTicket->community)
                                        <div class="col-span-2">
                                            <span class="text-gray-400 text-[10px] uppercase tracking-wider font-semibold">Komunitas</span>
                                            <div class="text-gray-800 font-bold mt-0.5">{{ $printedTicket->community }}</div>
                                        </div>
                                    @endif
                                    @if($printedTicket->purpose && $printedTicket->purpose !== 'Normal')
                                        <div>
                                            <span class="text-gray-400 text-[10px] uppercase tracking-wider font-semibold">Tujuan</span>
                                            <div class="text-gray-800 font-bold mt-0.5">{{ $printedTicket->purpose }}</div>
                                        </div>
                                    @endif
                                    @if($printedTicket->camping_duration)
                                        <div>
                                            <span class="text-gray-400 text-[10px] uppercase tracking-wider font-semibold">Lama Camping</span>
                                            <div class="text-gray-800 font-bold mt-0.5">{{ $printedTicket->camping_duration }} Malam</div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            
                            {{-- QR Code Section --}}
                            <div class="flex flex-col items-center justify-center shrink-0 sm:border-l border-dashed border-gray-200 sm:pl-6 pt-4 sm:pt-0 gap-3">
                                <div class="w-28 h-28 p-2 bg-white border-2 border-gray-100 rounded-xl shadow-sm">
                                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=300x300&data={{ urlencode($printedTicket->ticket_no) }}&margin=5" 
                                         alt="QR Code {{ $printedTicket->ticket_no }}" 
                                         class="w-full h-full object-contain"
                                         loading="eager">
                                </div>
                                <span class="text-[9px] text-gray-400 uppercase font-bold tracking-widest text-center">Pindai untuk<br>Verifikasi</span>
                            </div>
                        </div>
                    </div>
                    
                    {{-- Footer --}}
                    <div class="bg-gray-50 border-t border-dashed border-gray-200 px-6 py-3 flex items-center justify-between">
                        <span class="text-[10px] text-gray-400">Powered by Wisata Sanggabuana Digital Ticketing</span>
                        <button type="button" onclick="window.print()" class="text-[10px] text-forest-600 hover:text-forest-800 font-bold flex items-center gap-1 transition-colors">
                            <i data-lucide="printer" class="w-3 h-3"></i> Cetak Ulang
                        </button>
                    </div>
                </div>
            @endif
        @endif

        {{-- Transactions Table --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden flex flex-col">
            <div class="px-6 py-5 border-b border-gray-100 flex items-center justify-between">
                <h3 class="font-bold text-gray-800 flex items-center gap-2" style="margin:0;">
                    <i data-lucide="receipt" class="w-5 h-5 text-gray-400"></i>
                    Log Transaksi Penjualan Terakhir
                </h3>
                <span class="text-xs font-semibold px-2.5 py-1 bg-gray-100 text-gray-500 rounded-full">10 Terakhir</span>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50/50 border-b border-gray-100 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            <th class="px-6 py-4">No. Tiket</th>
                            <th class="px-6 py-4">Pengunjung / Rombongan</th>
                            <th class="px-6 py-4">Total Anggota</th>
                            <th class="px-6 py-4">Total Bayar</th>
                            <th class="px-6 py-4">Metode</th>
                            <th class="px-6 py-4">Waktu</th>
                            <th class="px-6 py-4 text-center">Status</th>
                            <th class="px-6 py-4 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-xs font-medium text-gray-600">
                        @forelse($recentTransactions as $tx)
                            <tr class="hover:bg-gray-50/50 transition-colors">
                                <td class="px-6 py-4 font-bold text-gray-800">{{ $tx->ticket_no }}</td>
                                <td class="px-6 py-4">
                                    <div class="font-bold text-gray-700">{{ $tx->name }}</div>
                                    <div class="text-[10px] text-gray-400">{{ $tx->address }}</div>
                                </td>
                                <td class="px-6 py-4">{{ $tx->qty_total }} Orang</td>
                                <td class="px-6 py-4 text-forest-700 font-bold">Rp {{ number_format($tx->total_price, 0, ',', '.') }}</td>
                                <td class="px-6 py-4">
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold 
                                        {{ $tx->payment_method === 'QRIS' ? 'bg-blue-50 text-blue-700' : ($tx->payment_method === 'Transfer' ? 'bg-amber-50 text-amber-700' : 'bg-green-50 text-green-700') }}">
                                        {{ $tx->payment_method }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-gray-400">{{ $tx->created_at->diffForHumans() }}</td>
                                <td class="px-6 py-4 text-center">
                                    @if($tx->status === 'in')
                                        <span class="w-20 inline-flex items-center justify-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-green-100 text-green-700">
                                            <span class="w-1 h-1 rounded-full bg-green-500 animate-pulse"></span> Di Dalam
                                        </span>
                                    @else
                                        <span class="w-20 inline-flex items-center justify-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-gray-100 text-gray-400">
                                            <span class="w-1 h-1 rounded-full bg-gray-400"></span> Keluar
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <button type="button" 
                                        onclick="showTicketModal('{{ $tx->ticket_no }}', '{{ $tx->name }}', '{{ $destination->name }}', '{{ $tx->qty_total }}', '{{ $tx->payment_method }}', 'Rp {{ number_format($tx->total_price, 0, ',', '.') }}', '{{ $tx->checked_in_at->format('d M Y, H:i') }}', '{{ $tx->status }}', '{{ $tx->community ?? '' }}', '{{ ($tx->purpose && $tx->purpose !== 'Normal') ? $tx->purpose : '' }}', '{{ $tx->camping_duration ?? '' }}')"
                                        class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg text-[10px] font-bold bg-forest-50 text-forest-700 hover:bg-forest-100 border border-forest-100 transition-all cursor-pointer">
                                        <i data-lucide="ticket" class="w-3 h-3"></i> Lihat
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-10 text-gray-400">
                                    <i data-lucide="info" class="w-10 h-10 mx-auto mb-2 text-gray-300"></i>
                                    Belum ada transaksi tiket hari ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const basePrice = parseInt(document.getElementById('ticket-base-price').value);
        const inputPrice = document.getElementById('price');
        const posTotalPay = document.getElementById('pos-total-pay');

        const qtyMaleInput = document.getElementById('qty_male');
        const qtyFemaleInput = document.getElementById('qty_female');
        const qtyKidsInput = document.getElementById('qty_kids');
        const gunungCalcTotal = document.getElementById('gunung-calc-total');

        // Address category elements
        const addressTypeSelect = document.getElementById('address_type');
        const provinceInput = document.getElementById('province');
        const cityInput = document.getElementById('city_input');
        const lblProvince = document.getElementById('lbl_province');
        const lblCity = document.getElementById('lbl_city');

        function formatRupiah(number) {
            return 'Rp ' + number.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
        }

        function calculatePOS() {
            let qty = 1;
            let currentPrice = parseInt(inputPrice.value) || 0;

            const male = parseInt(qtyMaleInput.value) || 0;
            const female = parseInt(qtyFemaleInput.value) || 0;
            const kids = parseInt(qtyKidsInput.value) || 0;
            
            qty = male + female + kids + 1; // + 1 for Penanggung Jawab
            
            if (gunungCalcTotal) {
                gunungCalcTotal.innerText = qty;
            }

            const calcBadge = document.getElementById('gunung-calc-total-badge');
            if (calcBadge) {
                calcBadge.innerText = qty;
            }

            // Estimate total
            const total = qty * currentPrice;
            posTotalPay.innerText = formatRupiah(total);
        }

        // TomSelect instances
        let tsProvince = null;
        let tsCity = null;

        // Init TomSelect for Province
        if (provinceInput) {
            tsProvince = new TomSelect(provinceInput, {
                create: true, // Allow manual typing if not found
                sortField: { field: "text", direction: "asc" },
                placeholder: 'Pilih Provinsi / Negara...',
                onChange: function(value) {
                    const type = addressTypeSelect.value;
                    if (type === 'indonesia') {
                        const option = this.options[value];
                        if (option && option.id) {
                            loadCities(option.id);
                        }
                    } else if (type === 'mancanegara') {
                        if (value) {
                            loadWorldCities(value);
                        }
                    }
                }
            });
        }

        // Init TomSelect for City
        if (cityInput) {
            tsCity = new TomSelect(cityInput, {
                create: true,
                sortField: { field: "text", direction: "asc" },
                placeholder: 'Pilih Kota...'
            });
        }

        // Fetch Functions
        async function loadProvinces() {
            tsProvince.clearOptions();
            tsProvince.addOption({value: '', text: 'Sedang memuat...'});
            tsProvince.disable();
            try {
                const res = await fetch('/data/wilayah/indonesia/provinces.json');
                const data = await res.json();
                tsProvince.clearOptions();
                data.forEach(p => {
                    tsProvince.addOption({value: p.name, text: p.name, id: p.id});
                });
                tsProvince.enable();
            } catch (e) {
                console.error(e);
                tsProvince.enable();
            }
        }

        async function loadCities(provinceId) {
            tsCity.clearOptions();
            tsCity.addOption({value: '', text: 'Sedang memuat...'});
            tsCity.disable();
            try {
                const res = await fetch(`/data/wilayah/indonesia/regencies/${provinceId}.json`);
                const data = await res.json();
                tsCity.clearOptions();
                data.forEach(c => {
                    tsCity.addOption({value: c.name, text: c.name});
                });
                tsCity.enable();
            } catch (e) {
                console.error(e);
                tsCity.enable();
            }
        }

        async function loadCountries() {
            tsProvince.clearOptions();
            tsProvince.addOption({value: '', text: 'Sedang memuat...'});
            tsProvince.disable();
            try {
                const res = await fetch('/data/wilayah/countries.json');
                const json = await res.json();
                tsProvince.clearOptions();
                if (json) {
                    json.forEach(c => {
                        tsProvince.addOption({value: c.name, text: c.name});
                    });
                }
                tsProvince.enable();
                tsCity.clearOptions();
                tsCity.enable();
            } catch (e) {
                console.error(e);
                tsProvince.enable();
            }
        }

        async function loadWorldCities(countryName) {
            tsCity.clearOptions();
            tsCity.addOption({value: '', text: 'Sedang memuat...'});
            tsCity.disable();
            try {
                const res = await fetch('https://countriesnow.space/api/v0.1/countries/cities', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ country: countryName })
                });
                const json = await res.json();
                tsCity.clearOptions();
                if (!json.error && json.data) {
                    json.data.forEach(city => {
                        tsCity.addOption({value: city, text: city});
                    });
                }
                tsCity.enable();
            } catch (e) {
                console.error(e);
                tsCity.enable();
            }
        }

        function handleAddressTypeChange() {
            if (!addressTypeSelect) return;
            const type = addressTypeSelect.value;

            tsProvince.clear();
            tsCity.clear();
            tsProvince.clearOptions();
            tsCity.clearOptions();

            if (type === 'lokal') {
                lblProvince.innerText = 'Provinsi';
                lblCity.innerText = 'Kecamatan (Lokal)';
                
                tsProvince.addOption({value: 'Jawa Barat', text: 'Jawa Barat'});
                tsProvince.setValue('Jawa Barat');
                tsProvince.disable();

                tsCity.addOption({value: 'Pangkalan', text: 'Kecamatan Pangkalan'});
                tsCity.addOption({value: 'Tegalwaru', text: 'Kecamatan Tegalwaru'});
                tsCity.enable();

            } else if (type === 'indonesia' || type === 'nusantara') {
                lblProvince.innerText = 'Provinsi';
                lblCity.innerText = 'Kota / Kabupaten';
                
                tsProvince.enable();
                tsCity.enable();
                loadProvinces();

            } else if (type === 'mancanegara') {
                lblProvince.innerText = 'Negara Asal';
                lblCity.innerText = 'Kota Asal';
                
                tsProvince.enable();
                tsCity.enable();
                loadCountries();
            }
        }

        // Purpose / Camping Duration elements
        const purposeSelect = document.getElementById('purpose');
        const campingDurationContainer = document.getElementById('camping-duration-container');
        const campingDurationInput = document.getElementById('camping_duration');

        function handlePurposeChange() {
            if (!purposeSelect || !campingDurationContainer) return;
            if (purposeSelect.value === 'Hiking') {
                campingDurationContainer.classList.remove('hidden');
                if (campingDurationInput) campingDurationInput.disabled = false;
            } else {
                campingDurationContainer.classList.add('hidden');
                if (campingDurationInput) campingDurationInput.disabled = true;
            }
        }

        // Add event listeners for dynamic calculation
        if (qtyMaleInput) qtyMaleInput.addEventListener('input', calculatePOS);
        if (qtyFemaleInput) qtyFemaleInput.addEventListener('input', calculatePOS);
        if (qtyKidsInput) qtyKidsInput.addEventListener('input', calculatePOS);
        if (inputPrice) inputPrice.addEventListener('input', calculatePOS);
        if (addressTypeSelect) addressTypeSelect.addEventListener('change', handleAddressTypeChange);
        if (purposeSelect) purposeSelect.addEventListener('change', handlePurposeChange);

        // Accordion Toggle for Detail Jumlah Rombongan
        const accordionToggle = document.getElementById('toggle-rombongan-accordion');
        const accordionContent = document.getElementById('rombongan-accordion-content');
        const accordionChevron = document.getElementById('accordion-chevron');

        if (accordionToggle && accordionContent) {
            accordionToggle.addEventListener('click', function() {
                const isCollapsed = accordionContent.style.maxHeight === '0px' || accordionContent.style.maxHeight === '';
                if (isCollapsed) {
                    accordionContent.style.maxHeight = accordionContent.scrollHeight + "px";
                    if (accordionChevron) accordionChevron.style.transform = "rotate(180deg)";
                } else {
                    accordionContent.style.maxHeight = "0px";
                    if (accordionChevron) accordionChevron.style.transform = "rotate(0deg)";
                }
            });

            // Adjust height if internal inputs change
            const subInputs = accordionContent.querySelectorAll('input');
            subInputs.forEach(input => {
                input.addEventListener('input', function() {
                    if (accordionContent.style.maxHeight !== '0px' && accordionContent.style.maxHeight !== '') {
                        setTimeout(() => {
                            accordionContent.style.maxHeight = accordionContent.scrollHeight + "px";
                        }, 50);
                    }
                });
            });
        }

        // Run initial calculations and set locks
        calculatePOS();
        handleAddressTypeChange();
        handlePurposeChange();
    });
</script>

@include('admin.partials.ticket_modal')
@endsection
