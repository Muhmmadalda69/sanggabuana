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
    /* Member row TomSelect: match h-[34px] inputs */
    .member-row .ts-wrapper.single .ts-control {
        min-height: 34px !important;
        height: 34px !important;
        padding: 0 0.5rem 0 2rem !important;
        font-size: 0.75rem !important;
        display: flex;
        align-items: center;
    }
    .member-row .ts-wrapper.single .ts-control input {
        font-size: 0.75rem !important;
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
    {{-- Quota Info Bar (realtime) --}}
    @if($destination->daily_quota)
    <div id="quota-bar-container" class="bg-white rounded-2xl border border-gray-100 shadow-sm px-6 py-4 flex flex-col sm:flex-row sm:items-center gap-4">
        <div class="flex items-center gap-3 flex-1">
            <div class="w-10 h-10 rounded-xl bg-forest-50 flex items-center justify-center shrink-0">
                <i data-lucide="users" class="w-5 h-5 text-forest-600"></i>
            </div>
            <div>
                <div class="text-xs text-gray-500 font-medium">Kuota Hari Ini — {{ now()->translatedFormat('d F Y') }}</div>
                <div class="text-sm font-bold text-gray-800">
                    <span id="quota-booked">...</span> / {{ $destination->daily_quota }} pengunjung
                    <span id="quota-status-badge" class="ml-2 text-[10px] font-bold px-2 py-0.5 rounded-full"></span>
                </div>
            </div>
        </div>
        <div class="flex-1 max-w-xs">
            <div class="h-2.5 bg-gray-100 rounded-full overflow-hidden">
                <div id="quota-progress" class="h-full rounded-full transition-all duration-700 bg-forest-500" style="width:0%"></div>
            </div>
            <div class="flex justify-between text-[10px] text-gray-400 mt-1">
                <span id="quota-remaining-label">Memuat...</span>
                <span>Kuota: {{ $destination->daily_quota }}</span>
            </div>
        </div>
    </div>
    @endif

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
            <input type="hidden" id="ticket-kids-discount" value="{{ $destination->kids_discount ?? 0 }}">
            
            {{-- Form Fields Responsive Grid --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    {{-- Name Penanggung Jawab (Always visible) --}}
                    <div>
                        <label for="name" class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-2">Nama Penanggung Jawab</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-gray-400">
                                <i data-lucide="user" class="w-4.5 h-4.5"></i>
                            </span>
                            <input type="text" name="name" id="name" required placeholder="Masukkan nama" class="w-full pl-12 pr-3 py-2.5 bg-white border border-gray-200 rounded-xl text-sm focus:outline-none focus:border-forest-500 transition-all">
                        </div>
                    </div>

                    {{-- Jenis Kelamin Penanggung Jawab (Always visible) --}}
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

                    {{-- Email Penanggung Jawab (Always visible) --}}
                    <div>
                        <label for="leader_email" class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-2">Email Penanggung Jawab <span class="text-[10px] text-gray-400 normal-case">(Opsional)</span></label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-gray-400">
                                <i data-lucide="mail" class="w-4.5 h-4.5"></i>
                            </span>
                            <input type="email" name="leader_email" id="leader_email" placeholder="Masukkan email aktif" class="w-full pl-12 pr-3 py-2.5 bg-white border border-gray-200 rounded-xl text-sm focus:outline-none focus:border-forest-500 transition-all">
                        </div>
                    </div>

                    {{-- Usia Penanggung Jawab (Always visible) --}}
                    <div>
                        <label for="leader_age" class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-2">Usia Penanggung Jawab</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-gray-400">
                                <i data-lucide="calendar" class="w-4.5 h-4.5"></i>
                            </span>
                            <input type="number" name="leader_age" id="leader_age" required min="1" max="120" placeholder="Usia (tahun)" class="w-full pl-12 pr-3 py-2.5 bg-white border border-gray-200 rounded-xl text-sm focus:outline-none focus:border-forest-500 transition-all">
                        </div>
                    </div>

                    {{-- Alamat Lengkap Penanggung Jawab (Always visible) --}}
                    <div>
                        <label for="leader_address" class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-2">Alamat Lengkap Penanggung Jawab</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-gray-400">
                                <i data-lucide="map-pin" class="w-4.5 h-4.5"></i>
                            </span>
                            <input type="text" name="leader_address" id="leader_address" required placeholder="Masukkan alamat jalan/RT/RW" class="w-full pl-12 pr-3 py-2.5 bg-white border border-gray-200 rounded-xl text-sm focus:outline-none focus:border-forest-500 transition-all">
                        </div>
                    </div>

                @if($destination->has_member_details)
                {{-- Leader Province/City (member mode) --}}
                <div class="col-span-1 md:col-span-2 lg:col-span-3">
                    <div class="bg-[#f0fdf4] border border-green-100 rounded-xl p-4">
                        <p class="text-[10px] font-bold text-[#16a34a] uppercase tracking-wider mb-4 m-0">Asal Daerah — Penanggung Jawab</p>
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-2">Kategori Wilayah</label>
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400 pointer-events-none">
                                        <i data-lucide="globe" class="w-4 h-4"></i>
                                    </span>
                                    <select name="address_type" id="leader_address_type" required class="w-full pl-9 pr-7 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:border-forest-500 transition-all appearance-none cursor-pointer bg-white">
                                        <option value="lokal">Lokal (Warga Sekitar)</option>
                                        <option value="indonesia" selected>Indonesia (Domestik)</option>
                                        <option value="mancanegara">Mancanegara (Luar Negeri)</option>
                                    </select>
                                    <span class="absolute inset-y-0 right-0 pr-2 flex items-center text-gray-400 pointer-events-none">
                                        <i data-lucide="chevron-down" class="w-4 h-4"></i>
                                    </span>
                                </div>
                            </div>
                            <div>
                                <label id="lbl_province" class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-2">Provinsi</label>
                                <div class="relative">
                                    <div class="ts-icon-container"><i data-lucide="map" class="w-4.5 h-4.5"></i></div>
                                    <input type="hidden" name="province" id="province_hidden">
                                    <select id="province" placeholder="Pilih Provinsi..."></select>
                                </div>
                            </div>
                            <div>
                                <label id="lbl_city" class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-2">Kota / Kabupaten</label>
                                <div class="relative">
                                    <div class="ts-icon-container"><i data-lucide="building" class="w-4.5 h-4.5"></i></div>
                                    <input type="hidden" name="city" id="city_hidden">
                                    <select id="city_input" placeholder="Pilih Kota..."></select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                @if($destination->has_member_details)
                    {{-- Dynamic Member Details Section (Full Width) --}}
                    <div class="col-span-1 md:col-span-2 lg:col-span-3 border border-gray-200/80 rounded-2xl p-5 sm:p-6 bg-gray-50/20 shadow-sm space-y-6">
                        <div class="flex items-center justify-between border-b border-gray-200 pb-3">
                            <div>
                                <h4 class="font-bold text-gray-800 text-sm uppercase tracking-wider" style="margin:0;">Daftar Anggota Rombongan</h4>
                                <p class="text-xs text-gray-400 font-normal mt-1" style="margin:0;">Isi informasi lengkap untuk masing-masing anggota (selain penanggung jawab). Satu tiket akan dicetak per orang.</p>
                            </div>
                            <button type="button" id="btn-add-member" class="text-xs bg-forest-600 hover:bg-forest-700 text-white font-semibold px-4 py-2 rounded-xl flex items-center gap-1.5 transition-all shadow-sm">
                                <i data-lucide="plus" class="w-4 h-4"></i> Tambah Anggota
                            </button>
                        </div>

                        <div id="members-container" class="space-y-4">
                            {{-- Rows appended here via JS --}}
                        </div>
                    </div>
                @endif

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

                    </div>
                    <div id="camping-duration-container" class="hidden transition-all duration-300">
                        <label for="camping_duration" class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-2">Lama Camping (Malam)</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-gray-400">
                                <i data-lucide="moon" class="w-4.5 h-4.5"></i>
                            </span>
                            <input type="number" name="camping_duration" id="camping_duration" min="1" value="1" class="w-full pl-12 pr-3 py-2.5 bg-white border border-gray-200 rounded-xl text-sm focus:outline-none focus:border-forest-500 transition-all" placeholder="Contoh: 1">
                        </div>
                    </div>
                @endif

                {{-- Kategori Wilayah (non-member mode only) --}}
                @if(!$destination->has_member_details)
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
                @endif

                @if(!$destination->has_member_details)
                {{-- Provinsi / Negara (non-member mode) --}}
                <div>
                    <label id="lbl_province" for="province" class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-2">Provinsi</label>
                    <div class="relative">
                        <div class="ts-icon-container"><i data-lucide="map" class="w-4.5 h-4.5"></i></div>
                        <input type="hidden" name="province" id="province_hidden">
                        <select id="province" placeholder="Pilih Provinsi..."></select>
                    </div>
                </div>

                {{-- Kota / Kecamatan (non-member mode) --}}
                <div>
                    <label id="lbl_city" for="city_input" class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-2">Kota / Kabupaten</label>
                    <div class="relative">
                        <div class="ts-icon-container"><i data-lucide="building" class="w-4.5 h-4.5"></i></div>
                        <input type="hidden" name="city" id="city_hidden">
                        <select id="city_input" placeholder="Pilih Kota..."></select>
                    </div>
                </div>
                @endif

                @if(!$destination->has_member_details)
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
                @endif

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
        {{-- E-Ticket Receipt Visualizer after group sale --}}
        @if(session('print_group_id'))
            @php
                $printedGroup = App\Models\Visitor::where('group_id', session('print_group_id'))->orderBy('id')->get();
                $groupLeader = $printedGroup->first();
            @endphp
            @if($groupLeader)
                <div id="receipt-visualizer" class="relative overflow-hidden rounded-2xl shadow-lg border border-emerald-200/60">
                    <div class="bg-gradient-to-r from-emerald-600 to-forest-700 px-6 py-4 flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-white/20 flex items-center justify-center">
                                <i data-lucide="check-circle-2" class="w-6 h-6 text-white"></i>
                            </div>
                            <div>
                                <h4 class="font-extrabold text-white text-sm">{{ $printedGroup->count() }} Tiket Rombongan Berhasil Diproses!</h4>
                                <p class="text-emerald-100 text-[11px] mt-0.5">Rombongan: {{ session('print_group_id') }} — {{ $destination->name }}</p>
                            </div>
                        </div>
                        <button type="button" onclick="document.getElementById('receipt-visualizer').style.opacity='0';setTimeout(()=>document.getElementById('receipt-visualizer').remove(),300)" class="w-8 h-8 rounded-full bg-white/10 hover:bg-white/20 flex items-center justify-center text-white transition-all">
                            <i data-lucide="x" class="w-4 h-4"></i>
                        </button>
                    </div>
                    <div class="bg-white px-6 py-4 divide-y divide-gray-100">
                        @foreach($printedGroup as $pt)
                            <div class="py-3 flex items-center justify-between gap-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 p-1 bg-white border border-gray-100 rounded-lg shadow-sm shrink-0">
                                        <img src="https://api.qrserver.com/v1/create-qr-code/?size=100x100&data={{ urlencode($pt->ticket_no) }}&margin=2" class="w-full h-full object-contain">
                                    </div>
                                    <div>
                                        <div class="font-bold text-gray-800 text-xs">{{ $pt->name }}</div>
                                        <div class="text-[10px] text-gray-400">{{ $pt->ticket_no }}</div>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <div class="text-[10px] text-gray-400">{{ $pt->age }} thn · {{ $pt->gender ?? ($pt->qty_male > 0 ? 'L' : 'P') }}</div>
                                    <div class="text-xs font-bold text-forest-700">Rp {{ number_format($pt->total_price, 0, ',', '.') }}</div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div class="bg-gray-50 border-t border-dashed border-gray-200 px-6 py-3 flex items-center justify-between">
                        <span class="text-[10px] text-gray-500 font-semibold">Total: Rp {{ number_format($printedGroup->sum('total_price'), 0, ',', '.') }} · {{ $printedGroup->count() }} Orang</span>
                        <button type="button" onclick="window.print()" class="text-[10px] text-forest-600 hover:text-forest-800 font-bold flex items-center gap-1">
                            <i data-lucide="printer" class="w-3 h-3"></i> Cetak Ulang
                        </button>
                    </div>
                </div>
            @endif
        @elseif(session('print_ticket_id'))
            @php $printedTicket = App\Models\Visitor::find(session('print_ticket_id')); @endphp
            @if($printedTicket)
                <div id="receipt-visualizer" class="relative overflow-hidden rounded-2xl shadow-lg border border-emerald-200/60">
                    <div class="bg-gradient-to-r from-emerald-600 to-forest-700 px-6 py-4 flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-white/20 flex items-center justify-center">
                                <i data-lucide="check-circle-2" class="w-6 h-6 text-white"></i>
                            </div>
                            <div>
                                <h4 class="font-extrabold text-white text-sm">Tiket Berhasil Dicetak!</h4>
                                <p class="text-emerald-100 text-[11px] mt-0.5">{{ $printedTicket->ticket_no }} — {{ $destination->name }}</p>
                            </div>
                        </div>
                        <button type="button" onclick="document.getElementById('receipt-visualizer').style.opacity='0';setTimeout(()=>document.getElementById('receipt-visualizer').remove(),300)" class="w-8 h-8 rounded-full bg-white/10 hover:bg-white/20 flex items-center justify-center text-white transition-all">
                            <i data-lucide="x" class="w-4 h-4"></i>
                        </button>
                    </div>
                    <div class="bg-white px-6 py-4">
                        <div class="grid grid-cols-2 gap-x-6 gap-y-2 text-xs">
                            <div><span class="text-gray-400 text-[10px] uppercase font-semibold">Pengunjung</span><div class="font-bold mt-0.5">{{ $printedTicket->name }}</div></div>
                            <div><span class="text-gray-400 text-[10px] uppercase font-semibold">No. Tiket</span><div class="font-bold mt-0.5">{{ $printedTicket->ticket_no }}</div></div>
                            <div><span class="text-gray-400 text-[10px] uppercase font-semibold">Total Rombongan</span><div class="font-bold mt-0.5">{{ $printedTicket->qty_total }} Orang</div></div>
                            <div><span class="text-gray-400 text-[10px] uppercase font-semibold">Total Bayar</span><div class="font-bold text-forest-700 mt-0.5">Rp {{ number_format($printedTicket->total_price, 0, ',', '.') }}</div></div>
                        </div>
                    </div>
                    <div class="bg-gray-50 border-t border-dashed border-gray-200 px-6 py-3 flex justify-end">
                        <button type="button" onclick="window.print()" class="text-[10px] text-forest-600 hover:text-forest-800 font-bold flex items-center gap-1">
                            <i data-lucide="printer" class="w-3 h-3"></i> Cetak Ulang
                        </button>
                    </div>
                </div>
            @endif
        @endif

        {{-- Transactions Table (per rombongan) --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden flex flex-col">
            <div class="px-6 py-5 border-b border-gray-100 flex items-center justify-between">
                <h3 class="font-bold text-gray-800 flex items-center gap-2" style="margin:0;">
                    <i data-lucide="receipt" class="w-5 h-5 text-gray-400"></i>
                    Log Transaksi Penjualan Terakhir
                </h3>
                <span class="text-xs font-semibold px-2.5 py-1 bg-gray-100 text-gray-500 rounded-full">10 Rombongan Terakhir</span>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50/50 border-b border-gray-100 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            <th class="px-6 py-4">ID Rombongan</th>
                            <th class="px-6 py-4">Penanggung Jawab</th>
                            <th class="px-6 py-4">Jumlah & Demografi</th>
                            <th class="px-6 py-4">Total Bayar</th>
                            <th class="px-6 py-4">Metode</th>
                            <th class="px-6 py-4">Waktu</th>
                            <th class="px-6 py-4 text-center">Status</th>
                            <th class="px-6 py-4 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-xs font-medium text-gray-600">
                        @forelse($recentGroups as $groupId => $tickets)
                            @php
                                $leader = $tickets->first();
                                $totalPay = $tickets->sum('total_price');
                                $allOut = $tickets->every(fn($t) => $t->status === 'out');
                                $anyIn = $tickets->contains(fn($t) => $t->status === 'in');
                            @endphp
                            <tr class="hover:bg-gray-50/50 transition-colors">
                                <td class="px-6 py-4 font-bold text-gray-800 text-[11px]">{{ $groupId }}</td>
                                <td class="px-6 py-4">
                                    <div class="font-bold text-gray-700">{{ $leader->name }}</div>
                                    <div class="text-[10px] text-gray-400">{{ $leader->province }}, {{ $leader->city }}</div>
                                </td>
                                @php
                                    $grpTotal = $tickets->sum('qty_total');
                                    $grpL = $tickets->sum('qty_male');
                                    $grpP = $tickets->sum('qty_female');
                                    $grpA = $tickets->sum('qty_kids');
                                @endphp
                                <td class="px-6 py-4">
                                    <div class="font-bold text-gray-700">{{ $grpTotal }} Orang</div>
                                    <div class="flex items-center gap-2 mt-0.5 text-[10px]">
                                        <span class="text-blue-600 font-bold">L: {{ $grpL }}</span>
                                        <span class="text-pink-600 font-bold">P: {{ $grpP }}</span>
                                        @if($grpA > 0)<span class="text-amber-600 font-bold">A: {{ $grpA }}</span>@endif
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-forest-700 font-bold">Rp {{ number_format($totalPay, 0, ',', '.') }}</td>
                                <td class="px-6 py-4">
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold {{ $leader->payment_method === 'QRIS' ? 'bg-blue-50 text-blue-700' : ($leader->payment_method === 'Transfer' ? 'bg-amber-50 text-amber-700' : 'bg-green-50 text-green-700') }}">
                                        {{ $leader->payment_method }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-gray-400">{{ $leader->created_at->diffForHumans() }}</td>
                                <td class="px-6 py-4 text-center">
                                    @if($anyIn)
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-green-100 text-green-700">
                                            <span class="w-1 h-1 rounded-full bg-green-500 animate-pulse"></span> Di Dalam
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-gray-100 text-gray-400">
                                            <span class="w-1 h-1 rounded-full bg-gray-400"></span> Keluar
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <button type="button"
                                        onclick="showGroupDetailModal({{ json_encode($tickets->map(fn($t) => ['ticket_no'=>$t->ticket_no,'name'=>$t->name,'age'=>$t->age,'province'=>$t->province,'city'=>$t->city,'status'=>$t->status,'total_price'=>$t->total_price,'qty_male'=>$t->qty_male,'qty_female'=>$t->qty_female,'qty_kids'=>$t->qty_kids,'qty_total'=>$t->qty_total])->values()) }}, '{{ $groupId }}', '{{ $leader->name }}', '{{ $destination->name }}', '{{ $leader->payment_method }}', {{ $tickets->sum('qty_total') }}, {{ $totalPay }})"
                                        class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg text-[10px] font-bold bg-forest-50 text-forest-700 hover:bg-forest-100 border border-forest-100 transition-all cursor-pointer">
                                        <i data-lucide="users" class="w-3 h-3"></i> Detail
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
            const kidsDiscount = parseInt(document.getElementById('ticket-kids-discount')?.value || '0');
            const kidsPrice = kidsDiscount > 0 ? Math.round(currentPrice * (1 - kidsDiscount / 100)) : currentPrice;

            const hasMemberDetails = {{ $destination->has_member_details ? 'true' : 'false' }};
            if (hasMemberDetails) {
                // Count leader (full price) + each member (check is_child)
                const rows = document.querySelectorAll('.member-row');
                qty = rows.length + 1;
                let total = currentPrice; // leader always full price
                rows.forEach(row => {
                    const isChild = row.querySelector('select[name*="[is_child]"]')?.value === '1';
                    total += isChild ? kidsPrice : currentPrice;
                });
                if (gunungCalcTotal) gunungCalcTotal.innerText = qty;
                const calcBadge = document.getElementById('gunung-calc-total-badge');
                if (calcBadge) calcBadge.innerText = qty;
                posTotalPay.innerText = formatRupiah(total);
                return;
            } else {
                const male = qtyMaleInput ? (parseInt(qtyMaleInput.value) || 0) : 0;
                const female = qtyFemaleInput ? (parseInt(qtyFemaleInput.value) || 0) : 0;
                const kids = qtyKidsInput ? (parseInt(qtyKidsInput.value) || 0) : 0;
                qty = male + female + kids + 1;
                const total = (male + female + 1) * currentPrice + kids * kidsPrice;
                if (gunungCalcTotal) gunungCalcTotal.innerText = qty;
                const calcBadge = document.getElementById('gunung-calc-total-badge');
                if (calcBadge) calcBadge.innerText = qty;
                posTotalPay.innerText = formatRupiah(total);
                return;
            }
        }

        // TomSelect instances
        let tsProvince = null;
        let tsCity = null;

        // Init TomSelect for Province
        if (provinceInput) {
            tsProvince = new TomSelect(provinceInput, {
                create: true,
                sortField: { field: "text", direction: "asc" },
                placeholder: 'Pilih Provinsi / Negara...',
                onChange: function(value) {
                    // Sync to hidden input
                    const hiddenProvince = document.getElementById('province_hidden');
                    if (hiddenProvince) hiddenProvince.value = value || '';

                    // In member mode, use leader_address_type; otherwise use address_type
                    const leaderTypeEl = document.getElementById('leader_address_type');
                    const type = leaderTypeEl ? leaderTypeEl.value : (addressTypeSelect ? addressTypeSelect.value : 'indonesia');
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
                placeholder: 'Pilih Kota...',
                onChange: function(value) {
                    // Sync to hidden input
                    const hiddenCity = document.getElementById('city_hidden');
                    if (hiddenCity) hiddenCity.value = value || '';
                }
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
            if (!addressTypeSelect || !tsProvince || !tsCity) return;
            const type = addressTypeSelect.value;

            tsProvince.clear();
            tsCity.clear();
            tsProvince.clearOptions();
            tsCity.clearOptions();

            if (type === 'lokal') {
                lblProvince.innerText = 'Provinsi';
                lblCity.innerText = 'Kecamatan (Lokal)';

                tsProvince.addOption({value: 'Jawa Barat', text: 'Jawa Barat'});
                tsProvince.setValue('Jawa Barat'); // triggers onChange → sets hidden
                tsProvince.enable();
                tsProvince.wrapper.style.pointerEvents = 'none';
                tsProvince.wrapper.style.opacity = '0.7';

                tsCity.addOption({value: 'Pangkalan', text: 'Kecamatan Pangkalan'});
                tsCity.addOption({value: 'Tegalwaru', text: 'Kecamatan Tegalwaru'});
                tsCity.enable();
                tsCity.wrapper.style.pointerEvents = '';
                tsCity.wrapper.style.opacity = '';

            } else if (type === 'indonesia' || type === 'nusantara') {
                lblProvince.innerText = 'Provinsi';
                lblCity.innerText = 'Kota / Kabupaten';

                tsProvince.enable();
                tsProvince.wrapper.style.pointerEvents = '';
                tsProvince.wrapper.style.opacity = '';
                tsCity.enable();
                tsCity.wrapper.style.pointerEvents = '';
                tsCity.wrapper.style.opacity = '';
                loadProvinces();

            } else if (type === 'mancanegara') {
                lblProvince.innerText = 'Negara Asal';
                lblCity.innerText = 'Kota Asal';

                tsProvince.enable();
                tsProvince.wrapper.style.pointerEvents = '';
                tsProvince.wrapper.style.opacity = '';
                tsCity.enable();
                tsCity.wrapper.style.pointerEvents = '';
                tsCity.wrapper.style.opacity = '';
                loadCountries();
            }
        }

        function handleLeaderAddressTypeChange() {
            const leaderTypeEl = document.getElementById('leader_address_type');
            if (!leaderTypeEl || !tsProvince || !tsCity) return;
            const type = leaderTypeEl.value;

            const hiddenProvince = document.getElementById('province_hidden');
            const hiddenCity = document.getElementById('city_hidden');

            if (lblProvince) lblProvince.innerText = type === 'mancanegara' ? 'Negara Asal' : 'Provinsi';
            if (lblCity) lblCity.innerText = type === 'lokal' ? 'Kecamatan (Lokal)' : (type === 'mancanegara' ? 'Kota Asal' : 'Kota / Kabupaten');

            tsProvince.clear(); tsCity.clear();
            tsProvince.clearOptions(); tsCity.clearOptions();
            if (hiddenProvince) hiddenProvince.value = '';
            if (hiddenCity) hiddenCity.value = '';

            if (type === 'lokal') {
                tsProvince.addOption({ value: 'Jawa Barat', text: 'Jawa Barat' });
                tsProvince.setValue('Jawa Barat'); // triggers onChange → sets hidden
                tsProvince.enable(); // keep enabled so value submits via hidden input
                tsProvince.wrapper.style.pointerEvents = 'none';
                tsProvince.wrapper.style.opacity = '0.7';
                tsCity.addOption({ value: 'Pangkalan', text: 'Kecamatan Pangkalan' });
                tsCity.addOption({ value: 'Tegalwaru', text: 'Kecamatan Tegalwaru' });
                tsCity.refreshOptions(false);
                tsCity.enable();
                tsCity.wrapper.style.pointerEvents = '';
                tsCity.wrapper.style.opacity = '';
            } else if (type === 'indonesia') {
                tsProvince.enable();
                tsProvince.wrapper.style.pointerEvents = '';
                tsProvince.wrapper.style.opacity = '';
                tsCity.enable();
                tsCity.wrapper.style.pointerEvents = '';
                tsCity.wrapper.style.opacity = '';
                loadProvinces();
            } else if (type === 'mancanegara') {
                tsProvince.enable();
                tsProvince.wrapper.style.pointerEvents = '';
                tsProvince.wrapper.style.opacity = '';
                tsCity.enable();
                tsCity.wrapper.style.pointerEvents = '';
                tsCity.wrapper.style.opacity = '';
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
        const leaderAddressTypeEl = document.getElementById('leader_address_type');
        if (leaderAddressTypeEl) leaderAddressTypeEl.addEventListener('change', handleLeaderAddressTypeChange);
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
        handleLeaderAddressTypeChange();
        handlePurposeChange();
    });
</script>

@if($destination->has_member_details)
    @include('partials.member_row_js')
@endif

@include('admin.partials.ticket_modal')

{{-- Group Detail Modal --}}
<div id="group-detail-modal" class="fixed inset-0 z-50 hidden items-center justify-center p-4" style="background:rgba(0,0,0,0.5);">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl max-h-[90vh] flex flex-col overflow-hidden">
        <div class="bg-gradient-to-r from-forest-600 to-forest-800 px-6 py-4 flex items-center justify-between shrink-0">
            <div>
                <h4 class="font-extrabold text-white text-sm" id="gdm-title">Detail Rombongan</h4>
                <p class="text-green-100 text-[11px] mt-0.5" id="gdm-subtitle"></p>
            </div>
            <button type="button" onclick="closeGroupDetailModal()" class="w-8 h-8 rounded-full bg-white/10 hover:bg-white/20 flex items-center justify-center text-white transition-all">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>
        <div class="px-6 py-4 border-b border-gray-100 grid grid-cols-3 gap-4 shrink-0 text-xs">
            <div><span class="text-gray-400 uppercase font-semibold text-[10px]">Penanggung Jawab</span><div class="font-bold text-gray-800 mt-0.5" id="gdm-leader"></div></div>
            <div><span class="text-gray-400 uppercase font-semibold text-[10px]">Metode Bayar</span><div class="font-bold text-gray-800 mt-0.5" id="gdm-method"></div></div>
            <div><span class="text-gray-400 uppercase font-semibold text-[10px]">Total Bayar</span><div class="font-bold text-forest-700 mt-0.5" id="gdm-total"></div></div>
        </div>
        <div class="overflow-y-auto flex-1">
            <table class="w-full text-xs text-left">
                <thead class="bg-gray-50 sticky top-0">
                    <tr class="text-[10px] font-bold text-gray-500 uppercase tracking-wider border-b border-gray-100">
                        <th class="px-4 py-3">No. Tiket</th>
                        <th class="px-4 py-3">Nama</th>
                        <th class="px-4 py-3">Usia</th>
                        <th class="px-4 py-3">Jenis Kelamin / Demografi</th>
                        <th class="px-4 py-3">Asal</th>
                        <th class="px-4 py-3">Harga</th>
                        <th class="px-4 py-3 text-center">Status</th>
                        <th class="px-4 py-3 text-center">Tiket</th>
                    </tr>
                </thead>
                <tbody id="gdm-members" class="divide-y divide-gray-100 text-gray-700"></tbody>
            </table>
        </div>
    </div>
</div>

<script>
function showGroupDetailModal(members, groupId, leader, destination, method, count, total) {
    document.getElementById('gdm-title').textContent = 'Rombongan: ' + groupId;
    document.getElementById('gdm-subtitle').textContent = destination + ' · ' + count + ' Orang';
    document.getElementById('gdm-leader').textContent = leader;
    document.getElementById('gdm-method').textContent = method;
    document.getElementById('gdm-total').textContent = 'Rp ' + total.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');

    const tbody = document.getElementById('gdm-members');
    tbody.innerHTML = members.map((m, i) => `
        <tr class="hover:bg-gray-50/50">
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
            <td class="px-4 py-3 text-forest-700 font-bold">Rp ${(m.total_price||0).toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.')}</td>
            <td class="px-4 py-3 text-center">
                ${m.status === 'in'
                    ? '<span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-green-100 text-green-700"><span class="w-1 h-1 rounded-full bg-green-500"></span> Di Dalam</span>'
                    : '<span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-gray-100 text-gray-400">Keluar</span>'
                }
            </td>
            <td class="px-4 py-3 text-center">
                <button type="button" class="gdm-ticket-btn inline-flex items-center gap-1 px-2 py-1 rounded-lg text-[10px] font-bold bg-forest-50 text-forest-700 hover:bg-forest-100 border border-forest-100 transition-all"
                    data-index="${i}">
                    <i data-lucide="ticket" class="w-3 h-3"></i> Tiket
                </button>
            </td>
        </tr>
    `).join('');

    // Ticket button handler — use index to access members array directly
    tbody.querySelectorAll('.gdm-ticket-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const m = members[parseInt(this.dataset.index)];
            const price = 'Rp ' + (m.total_price||0).toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
            showTicketModal(m.ticket_no, m.name, destination, m.qty_total||count, method, price, '—', m.status, '', '', '', leader, m.qty_male, m.qty_female, m.qty_kids);
        });
    });

    const modal = document.getElementById('group-detail-modal');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
    if (window.lucide) window.lucide.createIcons();
}

function closeGroupDetailModal() {
    const modal = document.getElementById('group-detail-modal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
}

document.getElementById('group-detail-modal').addEventListener('click', function(e) {
    if (e.target === this) closeGroupDetailModal();
});
</script>

@if($destination->daily_quota)
<script>
(function() {
    const QUOTA = {{ $destination->daily_quota }};
    const API = '{{ route("admin.pos.quota") }}?destination_id={{ $destination->id }}&date={{ now()->toDateString() }}';

    function updateQuota() {
        fetch(API, { headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content } })
            .then(r => r.json())
            .then(data => {
                const booked = data.booked || 0;
                const remaining = data.remaining ?? (QUOTA - booked);
                const pct = Math.min(100, Math.round((booked / QUOTA) * 100));

                document.getElementById('quota-booked').textContent = booked;
                document.getElementById('quota-remaining-label').textContent = remaining + ' sisa';
                document.getElementById('quota-progress').style.width = pct + '%';

                const bar = document.getElementById('quota-progress');
                const badge = document.getElementById('quota-status-badge');
                if (pct >= 100) {
                    bar.className = 'h-full rounded-full transition-all duration-700 bg-red-500';
                    badge.textContent = 'PENUH';
                    badge.className = 'ml-2 text-[10px] font-bold px-2 py-0.5 rounded-full bg-red-100 text-red-700';
                } else if (pct >= 75) {
                    bar.className = 'h-full rounded-full transition-all duration-700 bg-amber-500';
                    badge.textContent = remaining + ' sisa';
                    badge.className = 'ml-2 text-[10px] font-bold px-2 py-0.5 rounded-full bg-amber-100 text-amber-700';
                } else {
                    bar.className = 'h-full rounded-full transition-all duration-700 bg-forest-500';
                    badge.textContent = remaining + ' sisa';
                    badge.className = 'ml-2 text-[10px] font-bold px-2 py-0.5 rounded-full bg-green-100 text-green-700';
                }
            }).catch(() => {});
    }

    updateQuota();
    setInterval(updateQuota, 15000); // refresh every 15s
})();
</script>
@endif

@endsection
