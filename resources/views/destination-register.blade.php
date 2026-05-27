@extends('layouts.app')

@section('title', 'Registrasi Tiket Online ' . $destination->name . ' - Wisata Sanggabuana')

@push('styles')
    <!-- TomSelect -->
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.css" rel="stylesheet">
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        .ts-control {
            border-radius: 0.75rem !important;
            padding: 0.625rem 1rem !important;
            border-color: rgb(229, 231, 235) !important;
            font-size: 0.875rem !important;
        }

        .navbar-scroll,
        #navbar {
            background-color: #14532d !important;
        }

        /* TomSelect icon container */
        .ts-icon-container {
            position: absolute;
            top: 0;
            bottom: 0;
            left: 0;
            padding-left: 0.75rem;
            display: flex;
            align-items: center;
            color: #9ca3af;
            z-index: 10;
            pointer-events: none;
        }

        /* Leader province/city TomSelect */
        #province+.ts-wrapper .ts-control,
        #city_input+.ts-wrapper .ts-control {
            padding-left: 2.25rem !important;
        }

        /* Member row TomSelect sizing */
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

        .member-row .ts-wrapper.single.focus .ts-control {
            border-color: #10b981;
            box-shadow: 0 0 0 1px #10b981;
        }

        .ts-wrapper.single .ts-control {
            background-color: #ffffff !important;
            border: 1px solid #e5e7eb !important;
            border-radius: 0.5rem !important;
            box-shadow: none;
            transition: all 0.2s;
        }

        .ts-wrapper.single.focus .ts-control {
            border-color: #10b981 !important;
            box-shadow: 0 0 0 1px #10b981 !important;
        }

        .ts-dropdown {
            border-radius: 0.75rem;
            border-color: #e5e7eb;
            box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1);
            font-size: 0.875rem;
            margin-top: 4px;
            overflow: hidden;
        }

        .ts-dropdown .option.active {
            background-color: #ecfdf5;
            color: #065f46;
        }
    </style>
@endpush

@section('content')
    <div class="pt-24 pb-16 min-h-screen bg-forest-50/50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- Header Banner --}}
            <div class="bg-gradient-to-r from-forest-800 to-forest-950 rounded-3xl p-8 md:p-12 shadow-md text-white mb-8 relative overflow-hidden animate-fade-in"
                style="background: linear-gradient(135deg, #15803d 0%, #14532d 100%);">
                <div class="absolute right-0 top-0 opacity-10 pointer-events-none transform translate-x-12 -translate-y-12">
                    <i data-lucide="ticket" class="w-64 h-64"></i>
                </div>
                <div class="relative z-10">
                    <a href="{{ route('destination.detail', $destination->slug) }}"
                        class="inline-flex items-center gap-2 text-green-200 hover:text-white mb-4 text-xs font-bold transition-colors">
                        <i data-lucide="arrow-left" class="w-3.5 h-3.5"></i> Kembali ke Destinasi
                    </a>
                    <h1 class="text-3xl md:text-5xl font-black text-white tracking-tight leading-none mb-3">Registrasi
                        Online</h1>
                    <p class="text-green-100 text-sm md:text-base max-w-2xl font-medium">Beli tiket masuk ke destinasi
                        <strong>{{ $destination->name }}</strong> secara mandiri. Isi formulir pendaftaran di bawah ini.
                    </p>
                </div>
            </div>

            {{-- Main Grid --}}
            <div class="grid lg:grid-cols-12 gap-8 items-start">

                {{-- Form Column --}}
                <div class="lg:col-span-8">

                    {{-- Form Card --}}
                    <div class="bg-white rounded-3xl p-6 sm:p-8 shadow-sm border border-forest-100">
                        <h2
                            class="text-xl font-bold text-forest-950 mb-2 flex items-center gap-2 pb-4 border-b border-forest-50">
                            <i data-lucide="clipboard-edit" class="w-5 h-5 text-forest-600"></i> Formulir Data Pengunjung
                        </h2>
                        <div
                            class="flex items-center gap-2 mb-6 text-sm text-forest-700 bg-forest-50 rounded-xl px-4 py-2.5 border border-forest-100">
                            <i data-lucide="calendar-check" class="w-4 h-4 text-forest-600 shrink-0"></i>
                            <span>Tanggal Kunjungan:
                                <strong>{{ \Carbon\Carbon::parse($date)->translatedFormat('l, d F Y') }}</strong></span>
                            <a href="{{ route('destination.register.date', $destination->slug) }}"
                                class="ml-auto text-[10px] text-forest-500 hover:text-forest-700 font-semibold underline">Ubah</a>
                        </div>

                        <form action="{{ route('destination.register.store', [$destination->slug, $date]) }}" method="POST"
                            id="online-register-form">
                            @csrf
                            <input type="hidden" id="ticket-base-price" value="{{ $destination->price }}">
                            <input type="hidden" name="destination_id" value="{{ $destination->id }}">

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">

                                {{-- Name Penanggung Jawab (Always visible) --}}
                                <div>
                                    <label for="name"
                                        class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-2">Nama
                                        Penanggung Jawab</label>
                                    <div class="relative">
                                        <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-gray-400">
                                            <i data-lucide="user" class="w-4.5 h-4.5"></i>
                                        </span>
                                        <input type="text" name="name" id="name" required
                                            placeholder="Masukkan nama penanggung jawab"
                                            class="w-full pl-12 pr-3 py-2.5 bg-white border border-gray-200 rounded-xl text-sm focus:outline-none focus:border-forest-500 transition-all">
                                    </div>
                                </div>

                                {{-- Jenis Kelamin Penanggung Jawab (Always visible) --}}
                                <div>
                                    <label for="leader_gender"
                                        class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-2">Jenis
                                        Kelamin</label>
                                    <div class="relative">
                                        <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-gray-400">
                                            <i data-lucide="users" class="w-4.5 h-4.5"></i>
                                        </span>
                                        <select name="leader_gender" id="leader_gender" required
                                            class="w-full pl-12 pr-8 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:border-forest-500 transition-all appearance-none cursor-pointer bg-white">
                                            <option value="L">Laki-laki</option>
                                            <option value="P">Perempuan</option>
                                        </select>
                                        <span
                                            class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 pointer-events-none">
                                            <i data-lucide="chevron-down" class="w-4 h-4"></i>
                                        </span>
                                    </div>
                                </div>

                                {{-- Alamat Lengkap Penanggung Jawab (Always visible) --}}
                                <div class="md:col-span-2">
                                    <label for="leader_address"
                                        class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-2">Alamat
                                        Lengkap Penanggung Jawab</label>
                                    <div class="relative">
                                        <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-gray-400">
                                            <i data-lucide="map-pin" class="w-4.5 h-4.5"></i>
                                        </span>
                                        <input type="text" name="leader_address" id="leader_address" required
                                            placeholder="Masukkan alamat jalan/RT/RW"
                                            class="w-full pl-12 pr-3 py-2.5 bg-white border border-gray-200 rounded-xl text-sm focus:outline-none focus:border-forest-500 transition-all">
                                    </div>
                                </div>

                                {{-- Email Penanggung Jawab (Always visible) --}}
                                <div>
                                    <label for="leader_email"
                                        class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-2">Email
                                        Penanggung Jawab <span
                                            class="text-[10px] text-gray-400 normal-case">(Opsional)</span></label>
                                    <div class="relative">
                                        <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-gray-400">
                                            <i data-lucide="mail" class="w-4.5 h-4.5"></i>
                                        </span>
                                        <input type="email" name="leader_email" id="leader_email"
                                            placeholder="Masukkan email aktif"
                                            class="w-full pl-12 pr-3 py-2.5 bg-white border border-gray-200 rounded-xl text-sm focus:outline-none focus:border-forest-500 transition-all">
                                    </div>
                                </div>

                                {{-- Usia Penanggung Jawab (Always visible) --}}
                                <div>
                                    <label for="leader_age"
                                        class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-2">Usia
                                        Penanggung Jawab</label>
                                    <div class="relative">
                                        <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-gray-400">
                                            <i data-lucide="calendar" class="w-4.5 h-4.5"></i>
                                        </span>
                                        <input type="number" name="leader_age" id="leader_age" required min="1"
                                            max="120" placeholder="Usia (tahun)"
                                            class="w-full pl-12 pr-3 py-2.5 bg-white border border-gray-200 rounded-xl text-sm focus:outline-none focus:border-forest-500 transition-all">
                                    </div>
                                </div>

                                @if ($destination->has_member_details)
                                    {{-- Leader Province/City (member mode) — same as POS --}}
                                    <div class="col-span-1 md:col-span-2">
                                        <div class="bg-[#f0fdf4] border border-green-100 rounded-xl p-4">
                                            <p
                                                class="text-[10px] font-bold text-[#16a34a] uppercase tracking-wider mb-4 m-0">
                                                Asal Daerah — Penanggung Jawab</p>
                                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                                                <div>
                                                    <label
                                                        class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-2">Kategori
                                                        Wilayah</label>
                                                    <div class="relative">
                                                        <span
                                                            class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400 pointer-events-none">
                                                            <i data-lucide="globe" class="w-4 h-4"></i>
                                                        </span>
                                                        <select name="address_type" id="leader_address_type" required
                                                            class="w-full pl-9 pr-7 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:border-forest-500 transition-all appearance-none cursor-pointer bg-white">
                                                            <option value="lokal">Lokal (Warga Sekitar)</option>
                                                            <option value="indonesia" selected>Indonesia (Domestik)
                                                            </option>
                                                            <option value="mancanegara">Mancanegara (Luar Negeri)</option>
                                                        </select>
                                                        <span
                                                            class="absolute inset-y-0 right-0 pr-2 flex items-center text-gray-400 pointer-events-none">
                                                            <i data-lucide="chevron-down" class="w-4 h-4"></i>
                                                        </span>
                                                    </div>
                                                </div>
                                                <div>
                                                    <label id="lbl_province"
                                                        class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-2">Provinsi</label>
                                                    <div class="relative">
                                                        <div class="ts-icon-container"><i data-lucide="map"
                                                                class="w-4 h-4"></i></div>
                                                        <input type="hidden" name="province" id="province_hidden">
                                                        <select id="province" placeholder="Pilih Provinsi..."></select>
                                                    </div>
                                                </div>
                                                <div>
                                                    <label id="lbl_city"
                                                        class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-2">Kota
                                                        / Kabupaten</label>
                                                    <div class="relative">
                                                        <div class="ts-icon-container"><i data-lucide="building"
                                                                class="w-4 h-4"></i></div>
                                                        <input type="hidden" name="city" id="city_hidden">
                                                        <select id="city_input" placeholder="Pilih Kota..."></select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Dynamic Member Details Section (Full Width) --}}
                                    <div
                                        class="col-span-1 md:col-span-2 border border-gray-200/80 rounded-2xl p-5 bg-gray-50/20 shadow-sm space-y-6">
                                        <div class="flex items-center justify-between border-b border-gray-200 pb-3">
                                            <div>
                                                <h4 class="font-bold text-forest-900 text-sm uppercase tracking-wider"
                                                    style="margin:0;">Daftar Anggota Rombongan</h4>
                                                <p class="text-[11px] text-gray-400 font-normal mt-1" style="margin:0;">
                                                    Isi data selain penanggung jawab. Satu tiket akan dicetak untuk setiap
                                                    anggota.</p>
                                            </div>
                                            <button type="button" id="btn-add-member"
                                                class="text-xs bg-forest-600 hover:bg-forest-700 text-white font-semibold px-4 py-2 rounded-xl flex items-center gap-1.5 transition-all shadow-sm">
                                                <i data-lucide="plus" class="w-4 h-4"></i> Tambah Anggota
                                            </button>
                                        </div>
                                        <div id="members-container" class="space-y-4"></div>
                                    </div>
                                @endif

                                {{-- Community (Dynamic) --}}
                                @if ($destination->has_community)
                                    <div class="col-span-1 md:col-span-2">
                                        <label for="community"
                                            class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-2">Nama
                                            Komunitas / Instansi</label>
                                        <div class="relative">
                                            <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-gray-400">
                                                <i data-lucide="building" class="w-4.5 h-4.5"></i>
                                            </span>
                                            <input type="text" name="community" id="community"
                                                placeholder="Contoh: Mapala Sanggabuana, Universitas Karawang"
                                                class="w-full pl-12 pr-3 py-2.5 bg-white border border-gray-200 rounded-xl text-sm focus:outline-none focus:border-forest-500 transition-all">
                                        </div>
                                    </div>
                                @endif

                                {{-- Purpose (Dynamic) --}}
                                @if ($destination->has_purpose)
                                    <div class="col-span-1">
                                        <label for="purpose"
                                            class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-2">Tujuan
                                            Kunjungan</label>
                                        <div class="relative">
                                            <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-gray-400">
                                                <i data-lucide="compass" class="w-4.5 h-4.5"></i>
                                            </span>
                                            <select name="purpose" id="purpose" required
                                                class="w-full pl-12 pr-8 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:border-forest-500 transition-all appearance-none cursor-pointer bg-white">
                                                <option value="Hiking">Hiking / Camping</option>
                                                <option value="Trail Run">Trail Run</option>
                                                <option value="Jiarah">Ziarah</option>
                                            </select>
                                            <span
                                                class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 pointer-events-none">
                                                <i data-lucide="chevron-down" class="w-4 h-4"></i>
                                            </span>
                                        </div>
                                    </div>

                                    {{-- Camping Duration (Dynamic nested) --}}
                                    <div class="col-span-1" id="camping-duration-container">
                                        <label for="camping_duration"
                                            class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-2">Lama
                                            Camping <span
                                                class="text-[10px] text-gray-400 font-normal">(Malam)</span></label>
                                        <div class="relative">
                                            <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-gray-400">
                                                <i data-lucide="moon" class="w-4.5 h-4.5"></i>
                                            </span>
                                            <input type="number" name="camping_duration" id="camping_duration"
                                                value="1" min="1" required
                                                class="w-full pl-12 pr-3 py-2.5 bg-white border border-gray-200 rounded-xl text-sm focus:outline-none focus:border-forest-500 transition-all font-bold text-center">
                                        </div>
                                    </div>
                                @endif

                                {{-- Address Category (non-member mode only) --}}
                                @if (!$destination->has_member_details)
                                    <div>
                                        <label for="address_type"
                                            class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-2">Kategori
                                            Wilayah Asal</label>
                                        <div class="relative">
                                            <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-gray-400">
                                                <i data-lucide="globe" class="w-4.5 h-4.5"></i>
                                            </span>
                                            <select name="address_type" id="address_type" required
                                                class="w-full pl-12 pr-8 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:border-forest-500 transition-all appearance-none cursor-pointer bg-white">
                                                <option value="lokal">Lokal (Kec. Tegalwaru, Karawang)</option>
                                                <option value="indonesia" selected>Domestik / Indonesia</option>
                                                <option value="mancanegara">Mancanegara / Asing</option>
                                            </select>
                                            <span
                                                class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 pointer-events-none">
                                                <i data-lucide="chevron-down" class="w-4 h-4"></i>
                                            </span>
                                        </div>
                                    </div>

                                    {{-- Province --}}
                                    <div>
                                        <label for="province" id="lbl_province"
                                            class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-2">Provinsi</label>
                                        <input type="hidden" name="province" id="province_hidden">
                                        <select id="province" required class="w-full"></select>
                                    </div>

                                    {{-- City --}}
                                    <div class="col-span-1 md:col-span-2">
                                        <label for="city_input" id="lbl_city"
                                            class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-2">Kota
                                            / Kabupaten</label>
                                        <input type="hidden" name="city" id="city_hidden">
                                        <select id="city_input" required class="w-full"></select>
                                    </div>
                                @endif

                                @if (!$destination->has_member_details)
                                    {{-- Group Average Age --}}
                                    <div>
                                        <label for="avg_age"
                                            class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-2">Rata-rata
                                            Usia</label>
                                        <div class="relative">
                                            <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-gray-400">
                                                <i data-lucide="calendar" class="w-4.5 h-4.5"></i>
                                            </span>
                                            <input type="number" name="avg_age" id="avg_age" value="25"
                                                min="5" max="99" required
                                                class="w-full pl-12 pr-3 py-2.5 bg-white border border-gray-200 rounded-xl text-sm focus:outline-none focus:border-forest-500 transition-all text-center font-bold">
                                        </div>
                                    </div>

                                    {{-- Detail Jumlah Rombongan (Accordion) --}}
                                    <div
                                        class="col-span-1 md:col-span-2 border border-gray-150 rounded-2xl overflow-hidden shadow-sm transition-all duration-300">
                                        <button type="button" id="toggle-rombongan-accordion"
                                            class="w-full flex items-center justify-between p-4 bg-gray-50/60 hover:bg-gray-100/60 transition-colors focus:outline-none select-none">
                                            <div class="flex items-center gap-2 text-left">
                                                <div>
                                                    <h4 class="font-bold text-gray-800 text-xs uppercase tracking-wider mb-0.5"
                                                        style="margin: 0;">
                                                        Detail Jumlah Rombongan
                                                    </h4>
                                                    <p
                                                        class="text-[10px] text-gray-400 font-normal normal-case leading-none my-0.5">
                                                        Penanggung jawab otomatis terhitung
                                                    </p>
                                                    <div class="flex items-center mt-2">
                                                        <span
                                                            class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-forest-50 text-forest-700 border border-forest-100">
                                                            <span>Total:</span>
                                                            <span id="gunung-calc-total-badge">1</span>
                                                            <span>Orang</span>
                                                        </span>
                                                        <i id="accordion-chevron" data-lucide="chevron-down"
                                                            class="w-4 h-4 text-gray-400 transition-transform duration-300 ml-1"></i>
                                                    </div>
                                                </div>
                                            </div>
                                        </button>

                                        <div id="rombongan-accordion-content"
                                            class="max-h-0 overflow-hidden transition-all duration-300 ease-in-out bg-white"
                                            style="max-height: 0px;">
                                            <div class="p-4 flex flex-col gap-4 border-t border-gray-100">
                                                <div class="grid grid-cols-3 gap-4">
                                                    <div>
                                                        <label for="qty_male"
                                                            class="block text-[10px] font-bold text-gray-500 uppercase tracking-wider mb-1">Laki-laki</label>
                                                        <input type="number" name="qty_male" id="qty_male"
                                                            value="0" min="0" required
                                                            class="w-full py-2 bg-white border border-gray-200 rounded-xl text-sm font-bold text-center focus:outline-none focus:border-forest-500">
                                                    </div>
                                                    <div>
                                                        <label for="qty_female"
                                                            class="block text-[10px] font-bold text-gray-500 uppercase tracking-wider mb-1">Perempuan</label>
                                                        <input type="number" name="qty_female" id="qty_female"
                                                            value="0" min="0" required
                                                            class="w-full py-2 bg-white border border-gray-200 rounded-xl text-sm font-bold text-center focus:outline-none focus:border-forest-500">
                                                    </div>
                                                    <div>
                                                        <label for="qty_kids"
                                                            class="block text-[10px] font-bold text-gray-500 uppercase tracking-wider mb-1">Anak-anak</label>
                                                        <input type="number" name="qty_kids" id="qty_kids"
                                                            value="0" min="0" required
                                                            class="w-full py-2 bg-white border border-gray-200 rounded-xl text-sm font-bold text-center focus:outline-none focus:border-forest-500">
                                                    </div>
                                                </div>
                                                <div
                                                    class="bg-gray-50 border border-gray-200 rounded-xl px-4 py-2 text-xs text-gray-500 flex justify-between items-center">
                                                    <span>Total Pengunjung:</span>
                                                    <span class="font-extrabold text-forest-750 text-sm"><span
                                                            id="gunung-calc-total">1</span> Orang</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                {{-- Payment Method --}}
                                <div>
                                    <label for="payment-method"
                                        class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-2">Metode
                                        Pembayaran</label>
                                    <div class="relative">
                                        <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-gray-400">
                                            <i data-lucide="wallet" class="w-4.5 h-4.5"></i>
                                        </span>
                                        <select name="payment_method" id="payment-method"
                                            class="w-full pl-12 pr-8 py-2.5 bg-white border border-gray-200 rounded-xl text-sm focus:outline-none focus:border-forest-500 transition-all appearance-none cursor-pointer">
                                            <option value="Transfer">QRIS (Midtrans)</option>
                                        </select>
                                        <span
                                            class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 pointer-events-none">
                                            <i data-lucide="chevron-down" class="w-4 h-4"></i>
                                        </span>
                                    </div>
                                </div>

                                {{-- Read-only Ticket Price --}}
                                <div>
                                    <label
                                        class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-2">Harga
                                        Per Tiket (HTM)</label>
                                    <div class="relative">
                                        <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-gray-500">
                                            <i data-lucide="banknote" class="w-4.5 h-4.5"></i>
                                        </span>
                                        <input type="text" disabled
                                            value="Rp {{ number_format($destination->price, 0, ',', '.') }}"
                                            class="w-full pl-12 pr-3 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm font-bold text-gray-600">
                                    </div>
                                </div>

                            </div>

                            {{-- Total Pay Summary and Submit --}}
                            <div class="mt-8 pt-6 border-t border-forest-50 space-y-4">
                                <div class="bg-gray-50 border border-gray-100 rounded-2xl p-4 space-y-2.5">
                                    <div class="flex justify-between text-xs text-gray-500">
                                        <span>Total Harga Tiket:</span>
                                        <span class="font-bold text-gray-800" id="raw-total-tickets">Rp 0</span>
                                    </div>
                                    <div class="flex justify-between text-xs text-gray-500">
                                        <span>Biaya Admin QRIS (2%):</span>
                                        <span class="font-bold text-gray-800" id="admin-fee-amount">Rp 0</span>
                                    </div>
                                </div>
                                <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                                    <div class="text-center sm:text-left">
                                        <span class="text-xs text-forest-500 font-medium">Estimasi Pembayaran (Total)</span>
                                        <h3 class="text-2xl font-black text-forest-900 tracking-tight" id="pos-total-pay">Rp 0</h3>
                                    </div>
                                    <button type="submit"
                                        class="w-full sm:w-auto px-8 py-3.5 bg-forest-600 hover:bg-forest-750 text-white font-bold rounded-xl transition-all shadow-md hover:shadow-forest-500/20 flex items-center justify-center gap-2">
                                        <i data-lucide="credit-card" class="w-5 h-5"></i> Proses Registrasi & Bayar
                                    </button>
                                </div>
                            </div>

                        </form>
                    </div>
                </div>

                {{-- Ticket/Receipt Column --}}
                <div class="lg:col-span-4 space-y-6">

                    {{-- Receipt Visualizer --}}
                    @if (session('print_ticket_ids') || session('print_ticket_id'))
                        @php
                            $ticketIds = session('print_ticket_ids')
                                ? session('print_ticket_ids')
                                : [session('print_ticket_id')];
                            $printedTickets = App\Models\Visitor::whereIn('id', $ticketIds)->get();
                        @endphp
                        @if ($printedTickets->isNotEmpty())
                            <div id="receipt-visualizer" class="space-y-6 animate-fade-in">
                                @foreach ($printedTickets as $index => $printedTicket)
                                    <div
                                        class="relative overflow-hidden rounded-3xl shadow-md border border-green-200/60 bg-white">
                                        {{-- Success Banner --}}
                                        <div class="bg-gradient-to-r from-emerald-600 to-forest-700 px-6 py-4 flex items-center justify-between"
                                            style="background: linear-gradient(135deg, #059669 0%, #15803d 100%);">
                                            <div class="flex items-center gap-3">
                                                <i data-lucide="check-circle-2" class="w-5 h-5 text-white"></i>
                                                <div>
                                                    <h4 class="font-extrabold text-white text-xs tracking-wide uppercase">
                                                        Tiket Aktif! ({{ $index + 1 }}/{{ count($printedTickets) }})</h4>
                                                    <p class="text-green-100 text-[10px]">E-Ticket resmi
                                                        {{ $destination->name }}</p>
                                                </div>
                                            </div>
                                        </div>

                                        {{-- Details --}}
                                        <div class="p-6 space-y-4">
                                            <div class="text-center pb-4 border-b border-dashed border-gray-150">
                                                <span
                                                    class="text-[10px] text-gray-400 uppercase tracking-widest font-semibold">Nomor
                                                    Tiket</span>
                                                <div class="font-black text-gray-900 text-xl tracking-tight mt-1">
                                                    {{ $printedTicket->ticket_no }}</div>
                                            </div>

                                            <div class="space-y-2.5 text-xs text-gray-600">
                                                <div class="flex justify-between">
                                                    <span class="text-gray-400">Pengunjung:</span>
                                                    <strong class="text-gray-800">{{ $printedTicket->name }}</strong>
                                                </div>
                                                @if ($printedTicket->email)
                                                    <div class="flex justify-between">
                                                        <span class="text-gray-400">Email:</span>
                                                        <strong class="text-gray-800">{{ $printedTicket->email }}</strong>
                                                    </div>
                                                @endif
                                                @if ($printedTicket->age)
                                                    <div class="flex justify-between">
                                                        <span class="text-gray-400">Usia:</span>
                                                        <strong class="text-gray-800">{{ $printedTicket->age }}
                                                            Tahun</strong>
                                                    </div>
                                                @endif
                                                <div class="flex justify-between">
                                                    <span class="text-gray-400">Destinasi:</span>
                                                    <strong class="text-gray-800">{{ $destination->name }}</strong>
                                                </div>
                                                <div class="flex justify-between">
                                                    <span class="text-gray-400">Metode Bayar:</span>
                                                    <strong
                                                        class="text-gray-800">{{ $printedTicket->payment_method }}</strong>
                                                </div>
                                                <div class="flex justify-between">
                                                    <span class="text-gray-400">Total Harga:</span>
                                                    <strong class="text-forest-700 font-extrabold">Rp
                                                        {{ number_format($printedTicket->total_price, 0, ',', '.') }}</strong>
                                                </div>
                                            </div>

                                            {{-- QR Code --}}
                                            <div
                                                class="flex flex-col items-center justify-center pt-4 border-t border-dashed border-gray-150 gap-2">
                                                <div
                                                    class="w-32 h-32 p-2 bg-white border border-gray-150 rounded-xl shadow-sm">
                                                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=300x300&data={{ urlencode($printedTicket->ticket_no) }}&margin=5"
                                                        alt="QR Code {{ $printedTicket->ticket_no }}"
                                                        class="w-full h-full object-contain">
                                                </div>
                                                <span
                                                    class="text-[9px] text-gray-400 font-bold uppercase tracking-widest text-center mt-1">Simpan
                                                    QR Code ini untuk masuk</span>
                                            </div>
                                        </div>

                                        {{-- Print button --}}
                                        <div
                                            class="bg-gray-50 border-t border-dashed border-gray-150 px-6 py-3 text-center">
                                            <button type="button" onclick="window.print()"
                                                class="text-xs text-forest-600 hover:text-forest-800 font-bold flex items-center justify-center gap-1.5 mx-auto transition-colors">
                                                <i data-lucide="printer" class="w-3.5 h-3.5"></i> Cetak Tiket / PDF
                                            </button>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    @else
                        {{-- Default Info Card --}}
                        <div class="bg-white rounded-3xl p-6 shadow-sm border border-forest-100 text-center space-y-4">
                            <div
                                class="w-14 h-14 bg-forest-50 text-forest-600 rounded-full flex items-center justify-center mx-auto">
                                <i data-lucide="info" class="w-7 h-7"></i>
                            </div>
                            <h3 class="font-bold text-forest-900">Petunjuk Registrasi</h3>
                            <p class="text-xs text-forest-600 leading-relaxed text-left">
                                1. Isi data pengunjung secara lengkap dan benar.<br>
                                2. Tentukan jumlah rombongan atau detail data per anggota sesuai tipe formulir.<br>
                                3. Pilih metode pembayaran online.<br>
                                4. Klik <strong>Proses Registrasi</strong>. Tiket elektronik Anda beserta QR Code akan
                                muncul seketika di bagian ini setelah transaksi berhasil diproses. Silakan simpan / cetak
                                tiket tersebut.
                            </p>
                        </div>
                    @endif

                </div>

            </div>

        </div>
    </div>
@endsection

@push('scripts')
    <!-- TomSelect JS -->
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const basePrice = parseInt(document.getElementById('ticket-base-price').value);
            const posTotalPay = document.getElementById('pos-total-pay');

            const qtyMaleInput = document.getElementById('qty_male');
            const qtyFemaleInput = document.getElementById('qty_female');
            const qtyKidsInput = document.getElementById('qty_kids');
            const gunungCalcTotal = document.getElementById('gunung-calc-total');

            // Address category elements
            const addressTypeSelect = document.getElementById('address_type');
            const leaderAddressTypeEl = document.getElementById('leader_address_type');
            const provinceSelect = document.getElementById('province');
            const citySelect = document.getElementById('city_input');
            const lblProvince = document.getElementById('lbl_province');
            const lblCity = document.getElementById('lbl_city');

            let tsProvince = null;
            let tsCity = null;

            // Init TomSelect for Province
            if (provinceSelect) {
                tsProvince = new TomSelect(provinceSelect, {
                    create: true,
                    sortField: {
                        field: "text",
                        direction: "asc"
                    },
                    placeholder: 'Pilih Provinsi / Negara...',
                    onChange: function(value) {
                        const hiddenP = document.getElementById('province_hidden');
                        if (hiddenP) hiddenP.value = value || '';
                        const activeType = leaderAddressTypeEl ? leaderAddressTypeEl.value : (
                            addressTypeSelect ? addressTypeSelect.value : 'indonesia');
                        if (activeType === 'indonesia') {
                            const option = this.options[value];
                            if (option && option.id) loadCities(option.id);
                        } else if (activeType === 'mancanegara' && value) {
                            loadWorldCities(value);
                        }
                    }
                });
            }

            // Init TomSelect for City
            if (citySelect) {
                tsCity = new TomSelect(citySelect, {
                    create: true,
                    sortField: {
                        field: "text",
                        direction: "asc"
                    },
                    placeholder: 'Pilih Kota...',
                    onChange: function(val) {
                        const hiddenC = document.getElementById('city_hidden');
                        if (hiddenC) hiddenC.value = val || '';
                        calculatePOS();
                    }
                });
            }

            function formatRupiah(number) {
                return 'Rp ' + number.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
            }

            function calculatePOS() {
                let qty = 1;
                const kidsDiscount = {{ $destination->kids_discount ?? 0 }};
                const kidsPrice = kidsDiscount > 0 ? Math.round(basePrice * (1 - kidsDiscount / 100)) : basePrice;
                let ticketTotal = 0;

                const hasMemberDetails = {{ $destination->has_member_details ? 'true' : 'false' }};
                if (hasMemberDetails) {
                    const rows = document.querySelectorAll('.member-row');
                    qty = rows.length + 1;
                    ticketTotal = basePrice; // leader full price
                    rows.forEach(row => {
                        const isChild = row.querySelector('select[name*="[is_child]"]')?.value === '1';
                        ticketTotal += isChild ? kidsPrice : basePrice;
                    });
                    if (gunungCalcTotal) gunungCalcTotal.innerText = qty;
                    const calcBadge = document.getElementById('gunung-calc-total-badge');
                    if (calcBadge) calcBadge.innerText = qty;
                } else {
                    const male = qtyMaleInput ? (parseInt(qtyMaleInput.value) || 0) : 0;
                    const female = qtyFemaleInput ? (parseInt(qtyFemaleInput.value) || 0) : 0;
                    const kids = qtyKidsInput ? (parseInt(qtyKidsInput.value) || 0) : 0;
                    qty = male + female + kids + 1;
                    ticketTotal = (male + female + 1) * basePrice + kids * kidsPrice;
                    if (gunungCalcTotal) gunungCalcTotal.innerText = qty;
                    const calcBadge = document.getElementById('gunung-calc-total-badge');
                    if (calcBadge) calcBadge.innerText = qty;
                }

                // Calculate 2% Admin Fee
                const adminFee = Math.round(ticketTotal * 0.02);
                const finalTotal = ticketTotal + adminFee;

                const rawTotalEl = document.getElementById('raw-total-tickets');
                if (rawTotalEl) rawTotalEl.innerText = formatRupiah(ticketTotal);

                const adminFeeEl = document.getElementById('admin-fee-amount');
                if (adminFeeEl) adminFeeEl.innerText = formatRupiah(adminFee);

                posTotalPay.innerText = formatRupiah(finalTotal);
            }
            // Expose to global so the member_row_js partial can call it
            window.calculatePOS = calculatePOS;

            // Fetch Functions
            async function loadProvinces() {
                tsProvince.clearOptions();
                tsProvince.addOption({
                    value: '',
                    text: 'Sedang memuat...'
                });
                tsProvince.disable();
                try {
                    const res = await fetch('/data/wilayah/indonesia/provinces.json');
                    const data = await res.json();
                    tsProvince.clearOptions();
                    data.forEach(p => tsProvince.addOption({
                        value: p.name,
                        text: p.name,
                        id: p.id
                    }));
                    tsProvince.enable();
                } catch (e) {
                    tsProvince.enable();
                }
            }

            async function loadCities(provinceId) {
                tsCity.clearOptions();
                tsCity.addOption({
                    value: '',
                    text: 'Sedang memuat...'
                });
                tsCity.disable();
                try {
                    const res = await fetch(`/data/wilayah/indonesia/regencies/${provinceId}.json`);
                    const data = await res.json();
                    tsCity.clearOptions();
                    data.forEach(c => tsCity.addOption({
                        value: c.name,
                        text: c.name
                    }));
                    tsCity.enable();
                } catch (e) {
                    tsCity.enable();
                }
            }

            async function loadCountries() {
                tsProvince.clearOptions();
                tsProvince.addOption({
                    value: '',
                    text: 'Sedang memuat...'
                });
                tsProvince.disable();
                try {
                    const res = await fetch('/data/wilayah/countries.json');
                    const json = await res.json();
                    tsProvince.clearOptions();
                    if (json) json.forEach(c => tsProvince.addOption({
                        value: c.name,
                        text: c.name
                    }));
                    tsProvince.enable();
                    tsCity.clearOptions();
                    tsCity.enable();
                } catch (e) {
                    tsProvince.enable();
                }
            }

            async function loadWorldCities(countryName) {
                tsCity.clearOptions();
                tsCity.addOption({
                    value: '',
                    text: 'Sedang memuat...'
                });
                tsCity.disable();
                try {
                    const res = await fetch('https://countriesnow.space/api/v0.1/countries/cities', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            country: countryName
                        })
                    });
                    const json = await res.json();
                    tsCity.clearOptions();
                    if (!json.error && json.data) {
                        json.data.forEach(city => {
                            tsCity.addOption({
                                value: city,
                                text: city
                            });
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
                const hiddenP = document.getElementById('province_hidden');
                const hiddenC = document.getElementById('city_hidden');

                tsProvince.clear();
                tsCity.clear();
                tsProvince.clearOptions();
                tsCity.clearOptions();
                if (hiddenP) hiddenP.value = '';
                if (hiddenC) hiddenC.value = '';

                if (type === 'lokal') {
                    if (lblProvince) lblProvince.innerText = 'Provinsi';
                    if (lblCity) lblCity.innerText = 'Kecamatan (Lokal)';
                    tsProvince.addOption({
                        value: 'Jawa Barat',
                        text: 'Jawa Barat'
                    });
                    tsProvince.setValue('Jawa Barat');
                    tsProvince.enable();
                    tsProvince.wrapper.style.pointerEvents = 'none';
                    tsProvince.wrapper.style.opacity = '0.7';
                    tsCity.addOption({
                        value: 'Pangkalan',
                        text: 'Kecamatan Pangkalan'
                    });
                    tsCity.addOption({
                        value: 'Tegalwaru',
                        text: 'Kecamatan Tegalwaru'
                    });
                    tsCity.enable();
                    tsCity.wrapper.style.pointerEvents = '';
                    tsCity.wrapper.style.opacity = '';
                } else if (type === 'indonesia') {
                    if (lblProvince) lblProvince.innerText = 'Provinsi';
                    if (lblCity) lblCity.innerText = 'Kota / Kabupaten';
                    tsProvince.enable();
                    tsProvince.wrapper.style.pointerEvents = '';
                    tsProvince.wrapper.style.opacity = '';
                    tsCity.enable();
                    tsCity.wrapper.style.pointerEvents = '';
                    tsCity.wrapper.style.opacity = '';
                    loadProvinces();
                } else if (type === 'mancanegara') {
                    if (lblProvince) lblProvince.innerText = 'Negara Asal';
                    if (lblCity) lblCity.innerText = 'Kota Asal';
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
                if (!leaderAddressTypeEl || !tsProvince || !tsCity) return;
                const type = leaderAddressTypeEl.value;
                const hiddenP = document.getElementById('province_hidden');
                const hiddenC = document.getElementById('city_hidden');

                if (lblProvince) lblProvince.innerText = type === 'mancanegara' ? 'Negara Asal' : 'Provinsi';
                if (lblCity) lblCity.innerText = type === 'lokal' ? 'Kecamatan (Lokal)' : (type === 'mancanegara' ?
                    'Kota Asal' : 'Kota / Kabupaten');

                tsProvince.clear();
                tsCity.clear();
                tsProvince.clearOptions();
                tsCity.clearOptions();
                if (hiddenP) hiddenP.value = '';
                if (hiddenC) hiddenC.value = '';

                if (type === 'lokal') {
                    tsProvince.addOption({
                        value: 'Jawa Barat',
                        text: 'Jawa Barat'
                    });
                    tsProvince.setValue('Jawa Barat');
                    tsProvince.enable();
                    tsProvince.wrapper.style.pointerEvents = 'none';
                    tsProvince.wrapper.style.opacity = '0.7';
                    tsCity.addOption({
                        value: 'Pangkalan',
                        text: 'Kecamatan Pangkalan'
                    });
                    tsCity.addOption({
                        value: 'Tegalwaru',
                        text: 'Kecamatan Tegalwaru'
                    });
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
            if (addressTypeSelect) addressTypeSelect.addEventListener('change', handleAddressTypeChange);
            if (leaderAddressTypeEl) leaderAddressTypeEl.addEventListener('change', handleLeaderAddressTypeChange);
            if (purposeSelect) purposeSelect.addEventListener('change', handlePurposeChange);

            // Accordion Toggle for Detail Jumlah Rombongan
            const accordionToggle = document.getElementById('toggle-rombongan-accordion');
            const accordionContent = document.getElementById('rombongan-accordion-content');
            const accordionChevron = document.getElementById('accordion-chevron');

            if (accordionToggle && accordionContent) {
                accordionToggle.addEventListener('click', function() {
                    const isCollapsed = accordionContent.style.maxHeight === '0px' || accordionContent.style
                        .maxHeight === '';
                    if (isCollapsed) {
                        accordionContent.style.maxHeight = accordionContent.scrollHeight + "px";
                        if (accordionChevron) accordionChevron.style.transform = "rotate(180deg)";
                    } else {
                        accordionContent.style.maxHeight = "0px";
                        if (accordionChevron) accordionChevron.style.transform = "rotate(0deg)";
                    }
                });

                const subInputs = accordionContent.querySelectorAll('input');
                subInputs.forEach(input => {
                    input.addEventListener('input', function() {
                        if (accordionContent.style.maxHeight !== '0px' && accordionContent.style
                            .maxHeight !== '') {
                            setTimeout(() => {
                                accordionContent.style.maxHeight = accordionContent
                                    .scrollHeight + "px";
                            }, 50);
                        }
                    });
                });
            }

            // Dynamic Member Rows Management — handled by shared partial below
            // Run initial calculations and set locks
            calculatePOS();
            handleAddressTypeChange();
            handleLeaderAddressTypeChange();
            handlePurposeChange();

            // Flash Messages Alerts using SweetAlert2
            @if (session('success'))
                Swal.fire({
                    icon: 'success',
                    title: 'Registrasi Berhasil!',
                    text: "{{ session('success') }}",
                    confirmButtonColor: '#059669',
                    background: '#ffffff',
                    customClass: {
                        popup: 'rounded-2xl shadow-xl border border-gray-100',
                        confirmButton: 'px-6 py-2.5 bg-forest-600 hover:bg-forest-750 rounded-xl font-bold text-sm text-white'
                    }
                });
            @endif

            @if ($errors->any())
                Swal.fire({
                    icon: 'error',
                    title: 'Kesalahan Validasi',
                    html: `<ul class="text-left list-disc list-inside text-xs text-gray-600 space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                           </ul>`,
                    confirmButtonColor: '#059669',
                    background: '#ffffff',
                    customClass: {
                        popup: 'rounded-2xl shadow-xl border border-gray-100',
                        confirmButton: 'px-6 py-2.5 bg-forest-600 hover:bg-forest-750 rounded-xl font-bold text-sm text-white'
                    }
                });
            @endif
        });
    </script>
    @if ($destination->has_member_details)
        @include('partials.member_row_js')
    @endif
@endpush
