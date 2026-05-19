@extends('layouts.admin')

@section('title', 'Data Statistik Pengunjung')

@section('content')
{{-- Filter Controls Header --}}
<div class="mb-8 bg-white rounded-2xl border border-gray-100 shadow-sm p-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
    <div>
        <h2 class="text-xl font-bold text-gray-800 flex items-center gap-2 mb-1" style="margin:0; border-left:none; padding-left:0;">
            <i data-lucide="bar-chart-3" class="w-6 h-6 text-forest-600 mr-2 shrink-0"></i>
            Statistik Pengunjung {{ $isKasir ? ' - ' . $destination->name : '' }}
        </h2>
        <p class="text-xs text-gray-500">
            @if($isKasir)
                Data real-time untuk destinasi tugas aktif Anda.
            @else
                Analisis data agregat untuk seluruh destinasi wisata Sanggabuana.
            @endif
        </p>
    </div>
    
    {{-- Filter Form --}}
    <form action="{{ route('admin.dashboard') }}" method="GET" class="flex flex-wrap items-center gap-3">
        {{-- Admin Destination Filter --}}
        @if(!$isKasir)
            <div class="flex items-center bg-gray-50 border border-gray-200 rounded-xl px-3 py-2 focus-within:border-forest-500 focus-within:ring-1 focus-within:ring-forest-500/10 transition-all duration-300">
                <i data-lucide="map-pin" class="w-4 h-4 text-gray-400 mr-2 shrink-0"></i>
                <select name="destination_id" onchange="this.form.submit()" class="bg-transparent border-none p-0 outline-none text-xs font-semibold text-gray-700 cursor-pointer appearance-none w-full focus:outline-none focus:ring-0">
                    <option value="">-- Semua Destinasi --</option>
                    @foreach($destinations as $dest)
                        <option value="{{ $dest->id }}" {{ request('destination_id') == $dest->id ? 'selected' : '' }}>{{ $dest->name }}</option>
                    @endforeach
                </select>
            </div>
        @endif

        {{-- Time Range Filter --}}
        <div class="flex items-center bg-gray-50 border border-gray-200 rounded-xl px-3 py-2 focus-within:border-forest-500 focus-within:ring-1 focus-within:ring-forest-500/10 transition-all duration-300">
            <i data-lucide="calendar" class="w-4 h-4 text-gray-400 mr-2 shrink-0"></i>
            <select name="filter" onchange="this.form.submit()" class="bg-transparent border-none p-0 outline-none text-xs font-semibold text-gray-700 cursor-pointer appearance-none w-full focus:outline-none focus:ring-0">
                <option value="day" {{ $timeFilter === 'day' ? 'selected' : '' }}>Hari Ini (Harian)</option>
                <option value="month" {{ $timeFilter === 'month' ? 'selected' : '' }}>Bulan Ini (Bulanan)</option>
                <option value="year" {{ $timeFilter === 'year' ? 'selected' : '' }}>Tahun Ini (Tahunan)</option>
                <option value="year_range" {{ $timeFilter === 'year_range' ? 'selected' : '' }}>Rentang Tahun</option>
            </select>
        </div>

        {{-- Year Range Picker (Only if filter is year_range) --}}
        @if($timeFilter === 'year_range')
            <div class="flex items-center gap-2">
                <div class="flex items-center bg-gray-50 border border-gray-200 rounded-xl px-3 py-2 focus-within:border-forest-500 focus-within:ring-1 focus-within:ring-forest-500/10 transition-all duration-300">
                    <span class="text-xs text-gray-400 mr-2">Dari:</span>
                    <select name="start_year" onchange="this.form.submit()" class="bg-transparent border-none p-0 outline-none text-xs font-semibold text-gray-700 cursor-pointer appearance-none focus:outline-none focus:ring-0">
                        @for($y = 2025; $y <= date('Y') + 1; $y++)
                            <option value="{{ $y }}" {{ request('start_year', date('Y') - 5) == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endfor
                    </select>
                </div>
                <span class="text-gray-400 text-xs">-</span>
                <div class="flex items-center bg-gray-50 border border-gray-200 rounded-xl px-3 py-2 focus-within:border-forest-500 focus-within:ring-1 focus-within:ring-forest-500/10 transition-all duration-300">
                    <span class="text-xs text-gray-400 mr-2">Sampai:</span>
                    <select name="end_year" onchange="this.form.submit()" class="bg-transparent border-none p-0 outline-none text-xs font-semibold text-gray-700 cursor-pointer appearance-none focus:outline-none focus:ring-0">
                        @for($y = 2025; $y <= date('Y') + 1; $y++)
                            <option value="{{ $y }}" {{ request('end_year', date('Y')) == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endfor
                    </select>
                </div>
            </div>
        @endif
        
        @if(request('destination_id') || request('filter'))
            <a href="{{ route('admin.dashboard') }}" class="w-fit whitespace-nowrap px-4 py-2 border border-gray-200 hover:bg-gray-50 text-red-600 rounded-xl text-xs flex items-center justify-center gap-1.5 font-bold transition-all duration-300 shadow-sm" title="Reset Filter">
                <i data-lucide="rotate-ccw" class="w-3.5 h-3.5 shrink-0"></i>
                Reset Filter
            </a>
        @endif
    </form>
</div>

{{-- SVG-powered Dynamic Visitation Chart --}}
<div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 mb-8">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h3 class="font-bold text-gray-800 flex items-center gap-2" style="margin:0; top:0;">
                <i data-lucide="trending-up" class="w-5 h-5 text-gray-400 mr-2 shrink-0"></i>
                Grafik Tren Pengunjung
            </h3>
            <p class="text-[10px] text-gray-400 mt-1">Representasi visual lonjakan dan kestabilan kunjungan wisatawan</p>
        </div>
        <span class="text-xs font-semibold px-3 py-1 rounded-full bg-forest-50 text-forest-600 flex items-center gap-1">
            <span class="w-1.5 h-1.5 bg-green-500 rounded-full animate-ping"></span>
            Filter: 
            @if($timeFilter === 'day')
                Hari Ini
            @elseif($timeFilter === 'month')
                Bulan Ini
            @elseif($timeFilter === 'year')
                Tahun Ini
            @elseif($timeFilter === 'year_range')
                Rentang {{ request('start_year', date('Y') - 5) }} - {{ request('end_year', date('Y')) }}
            @endif
        </span>
    </div>

    @if($trendStats->count() > 0)
        {{-- Chart.js powered visitation trend chart --}}
        <div class="w-full bg-gray-50/50 rounded-2xl p-6 border border-gray-100 relative h-80">
            <canvas id="trendChart" class="w-full h-full"></canvas>
        </div>
    @else
        <div class="text-center py-12 text-gray-400 bg-gray-50 rounded-2xl border border-gray-100">
            <i data-lucide="info" class="w-12 h-12 mx-auto text-gray-300 mb-3"></i>
            <p class="text-sm font-medium">Belum ada aktivitas kunjungan pada rentang waktu yang dipilih.</p>
        </div>
    @endif
</div>

{{-- Top Summary Stats Cards --}}
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    {{-- Card 1: Total Visitors --}}
    <div class="bg-white rounded-2xl border border-gray-100 p-6 shadow-sm relative overflow-hidden group hover:border-forest-400/60 hover:-translate-y-1.5 hover:shadow-lg hover:shadow-forest-100/40 transition-all duration-500 ease-out cursor-pointer">
        <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-36 h-36 bg-gradient-to-br from-forest-500/10 to-forest-600/5 rounded-full blur-xl opacity-0 group-hover:opacity-100 scale-50 group-hover:scale-125 transition-all duration-700 ease-out pointer-events-none z-0"></div>
        <div class="flex items-center gap-3.5 mb-4 relative z-10">
            <div class="w-12 h-12 rounded-xl bg-forest-50 text-forest-600 flex items-center justify-center shrink-0 group-hover:bg-forest-600 group-hover:text-white group-hover:scale-110 group-hover:rotate-6 group-hover:shadow-md group-hover:shadow-forest-500/20 transition-all duration-500 ease-out">
                <i data-lucide="users" class="w-6 h-6"></i>
            </div>
            <span class="text-xs pl-5 font-bold text-gray-400 group-hover:text-gray-500 uppercase tracking-wider transition-colors duration-300">Total Pengunjung</span>
        </div>
        <div class="relative z-10">
            <div id="stat-total-visitors" class="text-2xl sm:text-3xl font-extrabold text-gray-800 group-hover:text-gray-900 tracking-tight transition-colors duration-300">{{ number_format($totalVisitors, 0, ',', '.') }}</div>
            <div class="text-[10px] text-gray-400 mt-1">Orang yang telah terdaftar</div>
        </div>
    </div>

    {{-- Card 2: Total Revenue --}}
    <div class="bg-white rounded-2xl border border-gray-100 p-6 shadow-sm relative overflow-hidden group hover:border-emerald-400/60 hover:-translate-y-1.5 hover:shadow-lg hover:shadow-emerald-100/40 transition-all duration-500 ease-out cursor-pointer">
        <div class="absolute -right-10 -bottom-10 w-36 h-36 bg-gradient-to-br from-emerald-500/10 to-emerald-600/5 rounded-full blur-xl opacity-0 group-hover:opacity-100 scale-50 group-hover:scale-125 transition-all duration-700 ease-out pointer-events-none z-0"></div>
        <div class="flex items-center gap-3.5 mb-4 relative z-10">
            <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0 group-hover:bg-emerald-600 group-hover:text-white group-hover:scale-110 group-hover:rotate-6 group-hover:shadow-md group-hover:shadow-emerald-500/20 transition-all duration-500 ease-out">
                <i data-lucide="wallet" class="w-6 h-6"></i>
            </div>
            <span class="text-xs font-bold text-gray-400 group-hover:text-gray-500 uppercase tracking-wider transition-colors duration-300">Total Pendapatan</span>
        </div>
        <div class="relative z-10">
            <div id="stat-total-revenue" class="text-2xl sm:text-3xl font-extrabold text-gray-800 group-hover:text-gray-900 tracking-tight transition-colors duration-300">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</div>
            <div class="text-[10px] text-gray-400 mt-1">Dari penjualan tiket masuk</div>
        </div>
    </div>

    {{-- Card 3: Average Age --}}
    <div class="bg-white rounded-2xl border border-gray-100 p-6 shadow-sm relative overflow-hidden group hover:border-blue-400/60 hover:-translate-y-1.5 hover:shadow-lg hover:shadow-blue-100/40 transition-all duration-500 ease-out cursor-pointer">
        <div class="absolute -right-10 -bottom-10 w-36 h-36 bg-gradient-to-br from-blue-500/10 to-blue-600/5 rounded-full blur-xl opacity-0 group-hover:opacity-100 scale-50 group-hover:scale-125 transition-all duration-700 ease-out pointer-events-none z-0"></div>
        <div class="flex items-center gap-3.5 mb-4 relative z-10">
            <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center shrink-0 group-hover:bg-blue-600 group-hover:text-white group-hover:scale-110 group-hover:rotate-6 group-hover:shadow-md group-hover:shadow-blue-500/20 transition-all duration-500 ease-out">
                <i data-lucide="sparkles" class="w-6 h-6"></i>
            </div>
            <span class="text-xs font-bold text-gray-400 group-hover:text-gray-500 uppercase tracking-wider transition-colors duration-300">Rata-rata Usia</span>
        </div>
        <div class="relative z-10">
            <div class="text-2xl sm:text-3xl font-extrabold text-gray-800 group-hover:text-gray-900 tracking-tight transition-colors duration-300"><span id="stat-average-age">{{ $averageAge }}</span> <span class="text-sm font-semibold text-gray-400">Tahun</span></div>
            <div class="text-[10px] text-gray-400 mt-1">Usia mayoritas rombongan</div>
        </div>
    </div>

    {{-- Card 4: Checked-In Active --}}
    <div class="bg-white rounded-2xl border border-gray-100 p-6 shadow-sm relative overflow-hidden group hover:border-amber-400/60 hover:-translate-y-1.5 hover:shadow-lg hover:shadow-amber-100/40 transition-all duration-500 ease-out cursor-pointer">
        <div class="absolute -right-10 -bottom-10 w-36 h-36 bg-gradient-to-br from-amber-500/10 to-amber-600/5 rounded-full blur-xl opacity-0 group-hover:opacity-100 scale-50 group-hover:scale-125 transition-all duration-700 ease-out pointer-events-none z-0"></div>
        <div class="flex items-center gap-3.5 mb-4 relative z-10">
            <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center shrink-0 group-hover:bg-amber-600 group-hover:text-white group-hover:scale-110 group-hover:rotate-6 group-hover:shadow-md group-hover:shadow-amber-500/20 transition-all duration-500 ease-out">
                <span class="relative flex h-5 w-5 items-center justify-center">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-amber-400 opacity-75"></span>
                    <i data-lucide="compass" class="relative inline-flex w-5 h-5 text-amber-600 group-hover:text-white transition-colors duration-300"></i>
                </span>
            </div>
            <span class="text-xs font-bold text-gray-400 group-hover:text-gray-500 uppercase tracking-wider transition-colors duration-300">Di Dalam Lokasi</span>
        </div>
        <div class="relative z-10">
            <div class="text-2xl sm:text-3xl font-extrabold text-gray-800 group-hover:text-gray-900 tracking-tight transition-colors duration-300"><span id="stat-active-inside">{{ number_format($activeInside, 0, ',', '.') }}</span> <span class="text-sm font-semibold text-gray-400">Orang</span></div>
            <div class="text-[10px] text-gray-400 mt-1">Pengunjung aktif (belum checkout)</div>
        </div>
    </div>
</div>

{{-- Main Charts & Breakdown --}}
<div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6 mb-8">
    {{-- Gender Pie Chart --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 flex flex-col items-center">
        <h3 class="font-bold text-gray-800 text-sm w-full mb-3 flex items-center gap-2">
            <i data-lucide="users" class="w-4 h-4 text-blue-500"></i> Rasio Gender
        </h3>
        <div class="w-full h-48 relative">
            @if($totalMale > 0 || $totalFemale > 0)
                <canvas id="genderChart"></canvas>
            @else
                <div class="w-full h-full flex flex-col items-center justify-center text-gray-400">
                    <i data-lucide="pie-chart" class="w-8 h-8 mb-2 opacity-50"></i>
                    <span class="text-[10px]">Data tidak tersedia</span>
                </div>
            @endif
        </div>
    </div>

    {{-- Age Pie Chart (Dewasa vs Anak) --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 flex flex-col items-center">
        <h3 class="font-bold text-gray-800 text-sm w-full mb-3 flex items-center gap-2">
            <i data-lucide="baby" class="w-4 h-4 text-pink-500"></i> Kategori Usia
        </h3>
        <div class="w-full h-48 relative">
            @if(($totalMale + $totalFemale + $totalKids) > 0)
                <canvas id="ageChart"></canvas>
            @else
                <div class="w-full h-full flex flex-col items-center justify-center text-gray-400">
                    <i data-lucide="pie-chart" class="w-8 h-8 mb-2 opacity-50"></i>
                    <span class="text-[10px]">Data tidak tersedia</span>
                </div>
            @endif
        </div>
    </div>

    {{-- Purpose Pie Chart --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 flex flex-col items-center">
        <h3 class="font-bold text-gray-800 text-sm w-full mb-3 flex items-center gap-2">
            <i data-lucide="mountain" class="w-4 h-4 text-emerald-500"></i> Tujuan Kunjungan
        </h3>
        <div class="w-full h-48 relative">
            @if($purposeStats->count() > 0)
                <canvas id="purposeChart"></canvas>
            @else
                <div class="w-full h-full flex flex-col items-center justify-center text-gray-400">
                    <i data-lucide="mountain-snow" class="w-8 h-8 mb-2 opacity-50"></i>
                    <span class="text-[10px]">Data tidak tersedia</span>
                </div>
            @endif
        </div>
    </div>

    {{-- Payment Pie Chart --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 flex flex-col items-center">
        <h3 class="font-bold text-gray-800 text-sm w-full mb-3 flex items-center gap-2">
            <i data-lucide="credit-card" class="w-4 h-4 text-amber-500"></i> Metode Pembayaran
        </h3>
        <div class="w-full h-48 relative">
            @if($paymentStats->count() > 0)
                <canvas id="paymentChart"></canvas>
            @else
                <div class="w-full h-full flex flex-col items-center justify-center text-gray-400">
                    <i data-lucide="pie-chart" class="w-8 h-8 mb-2 opacity-50"></i>
                    <span class="text-[10px]">Data tidak tersedia</span>
                </div>
            @endif
        </div>
    </div>
</div>

{{-- City, Province and Country Analytics --}}
<div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-8 mb-8">
    {{-- Top Cities --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 flex flex-col">
        <h3 class="font-bold text-gray-800 flex items-center gap-2 mb-4" style="margin-top:0;">
            <i data-lucide="building" class="w-5 h-5 text-indigo-500 mr-2 shrink-0"></i>
            5 Kota / Kabupaten Terpopuler
        </h3>
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-gray-50 text-[10px] font-semibold text-gray-500 uppercase tracking-wider">
                        <th class="px-4 py-2.5 rounded-l-lg">Kota / Kabupaten</th>
                        <th class="px-4 py-2.5 text-center">Transaksi</th>
                        <th class="px-4 py-2.5 rounded-r-lg text-right">Total Rombongan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-sm">
                    @forelse($cityStats as $city => $stat)
                    <tr class="hover:bg-gray-50/50">
                        <td class="px-4 py-3 font-semibold text-gray-700">{{ $city }}</td>
                        <td class="px-4 py-3 text-gray-500 text-center">{{ number_format($stat['count']) }}</td>
                        <td class="px-4 py-3 font-bold text-indigo-600 text-right">{{ number_format($stat['qty']) }} <span class="text-[10px] font-normal text-gray-400">Orang</span></td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" class="px-4 py-6 text-center text-xs text-gray-400">Belum ada data kota pengunjung.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Top Provinces --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 flex flex-col">
        <h3 class="font-bold text-gray-800 flex items-center gap-2 mb-4" style="margin-top:0;">
            <i data-lucide="map" class="w-5 h-5 text-emerald-500 mr-2 shrink-0"></i>
            5 Provinsi Terpopuler
        </h3>
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-gray-50 text-[10px] font-semibold text-gray-500 uppercase tracking-wider">
                        <th class="px-4 py-2.5 rounded-l-lg">Provinsi</th>
                        <th class="px-4 py-2.5 text-center">Transaksi</th>
                        <th class="px-4 py-2.5 rounded-r-lg text-right">Total Rombongan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-sm">
                    @forelse($provinceStats as $province => $stat)
                    <tr class="hover:bg-gray-50/50">
                        <td class="px-4 py-3 font-semibold text-gray-700">{{ $province }}</td>
                        <td class="px-4 py-3 text-gray-500 text-center">{{ number_format($stat['count']) }}</td>
                        <td class="px-4 py-3 font-bold text-emerald-600 text-right">{{ number_format($stat['qty']) }} <span class="text-[10px] font-normal text-gray-400">Orang</span></td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" class="px-4 py-6 text-center text-xs text-gray-400">Belum ada data provinsi pengunjung.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Top Countries --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 flex flex-col">
        <h3 class="font-bold text-gray-800 flex items-center gap-2 mb-4" style="margin-top:0;">
            <i data-lucide="globe" class="w-5 h-5 text-amber-500 mr-2 shrink-0"></i>
            5 Negara Terpopuler
        </h3>
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-gray-50 text-[10px] font-semibold text-gray-500 uppercase tracking-wider">
                        <th class="px-4 py-2.5 rounded-l-lg">Negara</th>
                        <th class="px-4 py-2.5 text-center">Transaksi</th>
                        <th class="px-4 py-2.5 rounded-r-lg text-right">Total Rombongan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-sm">
                    @forelse($countryStats as $country => $stat)
                    <tr class="hover:bg-gray-50/50">
                        <td class="px-4 py-3 font-semibold text-gray-700">{{ $country }}</td>
                        <td class="px-4 py-3 text-gray-500 text-center">{{ number_format($stat['count']) }}</td>
                        <td class="px-4 py-3 font-bold text-amber-600 text-right">{{ number_format($stat['qty']) }} <span class="text-[10px] font-normal text-gray-400">Orang</span></td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" class="px-4 py-6 text-center text-xs text-gray-400">Belum ada data mancanegara.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Visitor Distribution Map --}}
<div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 mb-8">
    <div class="flex items-center justify-between mb-4">
        <div>
            <h3 class="font-bold text-gray-800 flex items-center gap-2" style="margin:0; top:0;">
                <i data-lucide="map-pin" class="w-5 h-5 text-forest-500 mr-2 shrink-0"></i>
                Peta Persebaran Pengunjung
            </h3>
            <p class="text-[10px] text-gray-400 mt-1">Visualisasi asal pengunjung berdasarkan kota/kabupaten di Indonesia</p>
        </div>
        <div class="flex items-center gap-2">
            <select id="map-region-filter" class="text-xs font-semibold px-3 py-1.5 rounded-xl border border-gray-200 bg-white text-gray-700 focus:ring-forest-500 focus:border-forest-500 outline-none cursor-pointer hover:bg-gray-50 transition-colors">
                <option value="indonesia">🇮🇩 Peta Indonesia (Kota)</option>
                <option value="world">🌎 Peta Dunia (Negara)</option>
            </select>
            <span class="text-xs font-semibold px-3 py-1 rounded-full bg-forest-50 text-forest-600 flex items-center gap-1">
                <span class="w-1.5 h-1.5 bg-green-500 rounded-full animate-ping"></span>
                <span id="map-area-count">{{ $mapData->count() }} Kota/Kab</span>
            </span>
        </div>
    </div>
    <div id="visitor-map" class="w-full rounded-xl border border-gray-100 overflow-hidden" style="height: 420px; z-index: 1;"></div>
    <div class="flex items-center gap-4 mt-3 text-[10px] text-gray-400">
        <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-sm" style="background: rgba(16,185,129,0.7); border: 1px solid #10b981;"></span> Warna hijau = Intensitas pengunjung</span>
        <span class="flex items-center gap-1.5"><i data-lucide="mouse-pointer-click" class="w-3 h-3"></i> Klik area untuk detail</span>
    </div>
</div>

{{-- Leaflet CSS --}}
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize Map
    const mapEl = document.getElementById('visitor-map');
    if (!mapEl) return;

    // Destinations Coordinate Data from database (filter to active destination if selected/cashier)
    const destinationsCoords = @json(
        ($destination 
            ? collect([$destination]) 
            : $destinations
        )->map(function($d) {
            return [
                'name' => $d->name,
                'latitude' => $d->latitude,
                'longitude' => $d->longitude
            ];
        })->filter(function($d) {
            return !empty($d['latitude']) && !empty($d['longitude']);
        })->values()
    );

    const mapCenter = destinationsCoords.length === 1 
        ? [destinationsCoords[0].latitude, destinationsCoords[0].longitude] 
        : [-2.5, 118.0];
    const defaultZoom = destinationsCoords.length === 1 ? 10 : 5;

    const map = L.map('visitor-map', {
        center: mapCenter,
        zoom: defaultZoom,
        zoomControl: true,
        scrollWheelZoom: true,
        attributionControl: false
    });

    // Beautiful CartoDB Voyager tile layer
    L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png', {
        maxZoom: 19,
        subdomains: 'abcd'
    }).addTo(map);

    // Add subtle attribution
    L.control.attribution({
        position: 'bottomright',
        prefix: false
    }).addAttribution('&copy; <a href="https://carto.com/">CARTO</a>').addTo(map);

    // Map Data from backend
    const mapData = @json($mapData);
    const worldMapData = @json($worldMapData ?? []);
    const combinedData = [...mapData, ...worldMapData];

    if (combinedData.length === 0) {
        // Show empty state overlay
        const emptyDiv = document.createElement('div');
        emptyDiv.style.cssText = 'position:absolute;inset:0;z-index:1000;display:flex;align-items:center;justify-content:center;background:rgba(255,255,255,0.85);border-radius:0.75rem;';
        emptyDiv.innerHTML = '<div class="text-center"><svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#9ca3af" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" style="margin:0 auto 8px;opacity:0.5;"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/></svg><p style="font-size:12px;color:#9ca3af;font-weight:500;">Belum ada data persebaran pengunjung</p></div>';
        mapEl.style.position = 'relative';
        mapEl.appendChild(emptyDiv);
        return;
    }

    // Calculate max qty for proportional coloring
    const maxQty = Math.max(...combinedData.map(d => d.qty));

    // Color scaling function (Choropleth logic)
    function getColor(d) {
        if (!d) return '#f9fafb'; // empty color
        const ratio = d / maxQty;
        return ratio > 0.8 ? '#047857' : // Emerald 700
               ratio > 0.6 ? '#059669' : // Emerald 600
               ratio > 0.4 ? '#10b981' : // Emerald 500
               ratio > 0.2 ? '#34d399' : // Emerald 400
                             '#6ee7b7';  // Emerald 300
    }

    // Fetch GeoJSON from raw github content (Indonesian Regencies/Cities)
    const geoJsonUrl = 'https://raw.githubusercontent.com/rifani/geojson-political-indonesia/master/IDN_adm_2_kabkota.json';
    const worldGeoJsonUrl = 'https://raw.githubusercontent.com/johan/world.geo.json/master/countries.geo.json';
    
    // Add loading indicator
    mapEl.style.position = 'relative';
    const loader = document.createElement('div');
    loader.className = 'map-loader';
    loader.style.cssText = 'position:absolute;inset:0;z-index:1000;display:flex;align-items:center;justify-content:center;background:rgba(255,255,255,0.7);border-radius:0.75rem;backdrop-filter:blur(2px);';
    loader.innerHTML = '<div class="animate-spin rounded-full h-8 w-8 border-b-2 border-forest-600"></div>';
    mapEl.appendChild(loader);

    let worldLayer = null;
    let indoLayer = null;
    let markerLayerGroup = L.layerGroup();
    const regionFilter = document.getElementById('map-region-filter');
    const areaCount = document.getElementById('map-area-count');

    function updateMapVisibility() {
        if (!regionFilter) return;
        const mode = regionFilter.value;
        
        if (mode === 'world') {
            if (indoLayer) map.removeLayer(indoLayer);
            if (worldLayer) {
                worldLayer.addTo(map);
                worldLayer.bringToFront();
            }
            map.setZoom(2);
            areaCount.innerHTML = `{{ $worldMapData->count() }} Negara`;
        } else {
            if (worldLayer) map.removeLayer(worldLayer);
            if (indoLayer) {
                indoLayer.addTo(map);
                indoLayer.bringToFront();
            }
            map.setZoom(defaultZoom);
            map.panTo(mapCenter);
            areaCount.innerHTML = `{{ $mapData->count() }} Kota/Kab`;
        }

        // Keep markers on top
        markerLayerGroup.addTo(map);
        if (markerLayerGroup.getLayers().length > 0) {
            markerLayerGroup.eachLayer(layer => {
                if (layer.getElement) {
                    const el = layer.getElement();
                    if (el) el.style.zIndex = 1000;
                }
            });
        }
    }

    if (regionFilter) {
        regionFilter.addEventListener('change', updateMapVisibility);
    }

    function findCountryMatch(countryName) {
        const geoName = countryName.toLowerCase().trim();
        
        // Dictionary of Indonesian translations to English GeoJSON names
        const countryTranslations = {
            'jepang': 'japan',
            'singapura': 'singapore',
            'jerman': 'germany',
            'belanda': 'netherlands',
            'prancis': 'france',
            'perancis': 'france',
            'cina': 'china',
            'tiongkok': 'china',
            'arab saudi': 'saudi arabia',
            'timor leste': 'timor-leste',
            'rusia': 'russia',
            'mesir': 'egypt',
            'spanyol': 'spain',
            'italia': 'italy',
            'turki': 'turkey'
        };

        return worldMapData.find(d => {
            let dbName = d.name.toLowerCase().trim();
            
            // Translate database name to English if exists in translations
            if (countryTranslations[dbName]) {
                dbName = countryTranslations[dbName];
            }
            
            if (dbName === geoName) return true;
            
            // USA synonyms
            if ((dbName.includes('united states') || dbName === 'usa' || dbName === 'us' || dbName.includes('amerika')) && 
                (geoName.includes('united states') || geoName.includes('america') || geoName === 'usa' || geoName === 'us')) {
                return true;
            }
            // UK synonyms
            if ((dbName.includes('united kingdom') || dbName === 'uk' || dbName.includes('inggris')) && 
                (geoName.includes('united kingdom') || geoName === 'gbr')) {
                return true;
            }
            // Korea synonyms
            if (dbName.includes('korea') && geoName.includes('korea')) {
                return true;
            }
            return false;
        });
    }

    // Fetch World Map first
    fetch(worldGeoJsonUrl)
        .then(res => res.json())
        .then(worldData => {
            worldLayer = L.geoJSON(worldData, {
                style: function (feature) {
                    const countryName = (feature.properties.name || '').toLowerCase();
                    const matchedData = findCountryMatch(countryName);
                    return {
                        fillColor: matchedData ? getColor(matchedData.qty) : 'transparent',
                        weight: matchedData ? 1.5 : 0.2,
                        opacity: matchedData ? 1 : 0.15,
                        color: matchedData ? '#ffffff' : 'transparent',
                        fillOpacity: matchedData ? 0.85 : 0
                    };
                },
                onEachFeature: function (feature, layer) {
                    const countryName = (feature.properties.name || '').toLowerCase();
                    const matchedData = findCountryMatch(countryName);
                    if (matchedData) {
                        const popupContent = `
                            <div style="font-family:'Inter',system-ui,sans-serif;min-width:180px;">
                                <div style="font-weight:800;font-size:13px;color:#1f2937;margin-bottom:6px;border-bottom:2px dashed #e5e7eb;padding-bottom:6px;">${matchedData.name}</div>
                                <div style="display:grid;grid-template-columns:1fr 1fr;gap:6px;">
                                    <div>
                                        <div style="font-size:9px;color:#9ca3af;text-transform:uppercase;letter-spacing:0.05em;font-weight:600;">Transaksi</div>
                                        <div style="font-size:14px;font-weight:800;color:#374151;">${matchedData.count.toLocaleString('id-ID')}</div>
                                    </div>
                                    <div>
                                        <div style="font-size:9px;color:#9ca3af;text-transform:uppercase;letter-spacing:0.05em;font-weight:600;">Rombongan</div>
                                        <div style="font-size:14px;font-weight:800;color:#10b981;">${matchedData.qty.toLocaleString('id-ID')} <span style="font-size:9px;font-weight:500;color:#9ca3af;">Orang</span></div>
                                    </div>
                                </div>
                                <div style="margin-top:6px;border-top:1px solid #f3f4f6;padding-top:6px;">
                                    <div style="font-size:9px;color:#9ca3af;text-transform:uppercase;letter-spacing:0.05em;font-weight:600;">Pendapatan</div>
                                    <div style="font-size:13px;font-weight:800;color:#059669;">Rp ${matchedData.revenue.toLocaleString('id-ID')}</div>
                                </div>
                            </div>
                        `;
                        layer.bindPopup(popupContent, { className: 'custom-popup', maxWidth: 250 });
                        layer.on({
                            mouseover: function(e) {
                                e.target.setStyle({ weight: 2, color: '#1f2937', fillOpacity: 0.95 });
                                e.target.bringToFront();
                            },
                            mouseout: function(e) {
                                e.target.setStyle({ weight: 1.5, color: '#ffffff', fillOpacity: 0.85 });
                            }
                        });
                    }
                }
            });
            
            // Now fetch Indonesia Map
            return fetch(geoJsonUrl);
        })
        .then(res => res.json())
        .then(data => {
            if(loader && loader.parentNode) mapEl.removeChild(loader);

            indoLayer = L.geoJSON(data, {
                style: function (feature) {
                    const name = (feature.properties.NAME_2 || '').toLowerCase();
                    const type = (feature.properties.TYPE_2 || '').toLowerCase();
                    const isKota = type.includes('kota') || type.includes('kotamadya');
                    const cleanFeatureName = name.replace('kota ', '').replace('kabupaten ', '').replace('kab. ', '').trim();
                    
                    const matchedData = mapData.find(d => {
                        const searchName = d.name.toLowerCase().trim();
                        const searchIsKota = searchName.includes('kota');
                        const searchIsKab = searchName.includes('kabupaten') || searchName.includes('kab ');
                        const cleanSearchName = searchName.replace('kota ', '').replace('kabupaten ', '').replace('kab ', '').trim();
                        
                        if (cleanSearchName === cleanFeatureName) {
                            if (searchIsKota && isKota) return true;
                            if (searchIsKab && !isKota) return true;
                            if (!searchIsKota && !searchIsKab) return true;
                        }
                        return false;
                    });

                    return {
                        fillColor: matchedData ? getColor(matchedData.qty) : '#f9fafb',
                        weight: matchedData ? 1.5 : 0.5,
                        opacity: 1,
                        color: matchedData ? '#ffffff' : '#e5e7eb',
                        fillOpacity: matchedData ? 0.8 : 0.4
                    };
                },
                onEachFeature: function (feature, layer) {
                    const name = (feature.properties.NAME_2 || '').toLowerCase();
                    const type = (feature.properties.TYPE_2 || '').toLowerCase();
                    const isKota = type.includes('kota') || type.includes('kotamadya');
                    const cleanFeatureName = name.replace('kota ', '').replace('kabupaten ', '').replace('kab. ', '').trim();
                    
                    const matchedData = mapData.find(d => {
                        const searchName = d.name.toLowerCase().trim();
                        const searchIsKota = searchName.includes('kota');
                        const searchIsKab = searchName.includes('kabupaten') || searchName.includes('kab ');
                        const cleanSearchName = searchName.replace('kota ', '').replace('kabupaten ', '').replace('kab ', '').trim();
                        
                        if (cleanSearchName === cleanFeatureName) {
                            if (searchIsKota && isKota) return true;
                            if (searchIsKab && !isKota) return true;
                            if (!searchIsKota && !searchIsKab) return true;
                        }
                        return false;
                    });

                    if (matchedData) {
                        const popupContent = `
                            <div style="font-family:'Inter',system-ui,sans-serif;min-width:180px;">
                                <div style="font-weight:800;font-size:13px;color:#1f2937;margin-bottom:6px;border-bottom:2px dashed #e5e7eb;padding-bottom:6px;">${matchedData.name}</div>
                                <div style="display:grid;grid-template-columns:1fr 1fr;gap:6px;">
                                    <div>
                                        <div style="font-size:9px;color:#9ca3af;text-transform:uppercase;letter-spacing:0.05em;font-weight:600;">Transaksi</div>
                                        <div style="font-size:14px;font-weight:800;color:#374151;">${matchedData.count.toLocaleString('id-ID')}</div>
                                    </div>
                                    <div>
                                        <div style="font-size:9px;color:#9ca3af;text-transform:uppercase;letter-spacing:0.05em;font-weight:600;">Rombongan</div>
                                        <div style="font-size:14px;font-weight:800;color:#10b981;">${matchedData.qty.toLocaleString('id-ID')} <span style="font-size:9px;font-weight:500;color:#9ca3af;">Orang</span></div>
                                    </div>
                                </div>
                                <div style="margin-top:6px;border-top:1px solid #f3f4f6;padding-top:6px;">
                                    <div style="font-size:9px;color:#9ca3af;text-transform:uppercase;letter-spacing:0.05em;font-weight:600;">Pendapatan</div>
                                    <div style="font-size:13px;font-weight:800;color:#059669;">Rp ${matchedData.revenue.toLocaleString('id-ID')}</div>
                                </div>
                            </div>
                        `;
                        layer.bindPopup(popupContent, { className: 'custom-popup', maxWidth: 250 });
                        layer.on({
                            mouseover: function(e) { e.target.setStyle({ weight: 2, color: '#1f2937', fillOpacity: 0.95 }); e.target.bringToFront(); },
                            mouseout: function(e) { e.target.setStyle({ weight: 1.5, color: '#ffffff', fillOpacity: 0.8 }); }
                        });
                    }
                }
            });

            // Add location markers dynamically
            const sgbIcon = L.divIcon({
                html: '<div style="width:14px;height:14px;background:#dc2626;border:3px solid #fff;border-radius:50%;box-shadow:0 2px 8px rgba(220,38,38,0.4);"></div>',
                className: '',
                iconSize: [14, 14],
                iconAnchor: [7, 7]
            });

            if (destinationsCoords.length > 0) {
                destinationsCoords.forEach(dest => {
                    L.marker([dest.latitude, dest.longitude], { icon: sgbIcon })
                        .bindPopup(`
                            <div style="font-family:Inter,system-ui,sans-serif;text-align:center;">
                                <div style="font-weight:800;font-size:12px;color:#dc2626;">📍 ${dest.name}</div>
                                <div style="font-size:10px;color:#9ca3af;margin-top:2px;">Destinasi Wisata</div>
                            </div>
                        `, { className: 'custom-popup' })
                        .addTo(markerLayerGroup);
                });
            } else {
                L.marker([-6.7275, 107.0394], { icon: sgbIcon })
                    .bindPopup('<div style="font-family:Inter,system-ui,sans-serif;text-align:center;"><div style="font-weight:800;font-size:12px;color:#dc2626;">📍 Wisata Sanggabuana</div><div style="font-size:10px;color:#9ca3af;margin-top:2px;">Lokasi Destinasi Wisata (Default)</div></div>', { className: 'custom-popup' })
                    .addTo(markerLayerGroup);
            }

            // Set initial map mode based on the dropdown
            updateMapVisibility();
        })
        .catch(err => {
            console.error('Error loading GeoJSON:', err);
            if(loader && loader.parentNode) mapEl.removeChild(loader);
        });
});
</script>

<style>
    .custom-popup .leaflet-popup-content-wrapper {
        border-radius: 12px !important;
        box-shadow: 0 10px 25px -5px rgba(0,0,0,0.12), 0 8px 10px -6px rgba(0,0,0,0.06) !important;
        border: 1px solid #e5e7eb !important;
        padding: 0 !important;
    }
    .custom-popup .leaflet-popup-content {
        margin: 12px 14px !important;
    }
    .custom-popup .leaflet-popup-tip {
        box-shadow: 0 3px 6px rgba(0,0,0,0.08) !important;
        border: 1px solid #e5e7eb !important;
    }
    .leaflet-control-zoom a {
        border-radius: 8px !important;
        border: 1px solid #e5e7eb !important;
        color: #374151 !important;
        font-weight: bold !important;
    }
    .leaflet-control-zoom {
        border: none !important;
        border-radius: 10px !important;
        overflow: hidden !important;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08) !important;
    }
</style>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Register the plugin globally
    Chart.register(ChartDataLabels);

    // Shared styling configuration
    const commonOptions = {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { position: 'bottom', labels: { boxWidth: 10, padding: 15, font: { size: 10, family: "'Inter', sans-serif" } } },
            datalabels: {
                color: '#ffffff',
                font: { weight: 'bold', size: 11, family: "'Inter', sans-serif" },
                formatter: (value, ctx) => {
                    let sum = 0;
                    let dataArr = ctx.chart.data.datasets[0].data;
                    dataArr.map(data => { sum += parseInt(data) || 0; });
                    if (sum === 0 || value === 0) return '';
                    return (value * 100 / sum).toFixed(0) + "%";
                }
            }
        },
        borderWidth: 0,
        hoverOffset: 4
    };

    // 0. Trend Mixed Chart (Bar + Line Curve)
    const ctxTrend = document.getElementById('trendChart');
    if (ctxTrend) {
        @php
            $trendLabels = $trendStats->keys()->toJson();
            $trendData = $trendStats->pluck('qty')->toJson();
            $trendRevenue = $trendStats->pluck('revenue')->toJson();
        @endphp

        new Chart(ctxTrend.getContext('2d'), {
            type: 'bar',
            data: {
                labels: {!! $trendLabels !!},
                datasets: [
                    {
                        type: 'line',
                        label: 'Tren Pengunjung',
                        data: {!! $trendData !!},
                        borderColor: '#059669', // Emerald 600
                        borderWidth: 3,
                        pointBackgroundColor: '#ffffff',
                        pointBorderColor: '#059669',
                        pointBorderWidth: 2,
                        pointRadius: 4,
                        pointHoverRadius: 6,
                        tension: 0.4, // Smooth curve!
                        fill: false,
                        datalabels: {
                            display: true,
                            anchor: 'end',
                            align: 'top',
                            color: '#047857',
                            offset: 4,
                            font: {
                                weight: 'bold',
                                size: 10,
                                family: "'Inter', sans-serif"
                            },
                        }
                    },
                    {
                        type: 'bar',
                        label: 'Total Pengunjung',
                        data: {!! $trendData !!},
                        backgroundColor: 'rgba(16, 185, 129, 0.12)', // Light green
                        borderColor: 'rgba(16, 185, 129, 0.25)',
                        borderWidth: 1,
                        borderRadius: 6,
                        barPercentage: 0.5,
                        datalabels: {
                            display: false
                        }
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: '#111827',
                        titleFont: { size: 12, family: "'Inter', sans-serif', weight: 'bold'" },
                        bodyFont: { size: 11, family: "'Inter', sans-serif" },
                        padding: 10,
                        cornerRadius: 8,
                        callbacks: {
                            label: function(context) {
                                const index = context.dataIndex;
                                const revenue = {!! $trendRevenue !!}[index];
                                return [
                                    `Pengunjung: ${context.parsed.y} Orang`,
                                    `Pendapatan: Rp ${revenue.toLocaleString('id-ID')}`
                                ];
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            font: { size: 10, family: "'Inter', sans-serif', weight: '600'" },
                            color: '#9ca3af'
                        }
                    },
                    y: {
                        grid: {
                            color: 'rgba(243, 244, 246, 0.6)'
                        },
                        ticks: {
                            font: { size: 10, family: "'Inter', sans-serif" },
                            color: '#9ca3af',
                            precision: 0
                        }
                    }
                }
            }
        });
    }

    // 1. Gender Pie Chart
    const ctxGender = document.getElementById('genderChart');
    if (ctxGender) {
        window.genderChartInstance = new Chart(ctxGender.getContext('2d'), {
            type: 'doughnut',
            data: {
                labels: ['Laki-laki', 'Perempuan'],
                datasets: [{
                    data: [{{ $totalMale }}, {{ $totalFemale }}],
                    backgroundColor: ['#3b82f6', '#ec4899'], // blue-500, pink-500
                    borderWidth: 2, borderColor: '#ffffff'
                }]
            },
            options: { ...commonOptions, cutout: '65%' }
        });
    }

    // 2. Age Pie Chart
    const ctxAge = document.getElementById('ageChart');
    if (ctxAge) {
        window.ageChartInstance = new Chart(ctxAge.getContext('2d'), {
            type: 'doughnut',
            data: {
                labels: ['Dewasa (>5 Tahun)', 'Anak-anak (<5 Tahun)'],
                datasets: [{
                    data: [{{ $totalMale + $totalFemale }}, {{ $totalKids }}],
                    backgroundColor: ['#10b981', '#f59e0b'], // emerald-500, amber-500
                    borderWidth: 2, borderColor: '#ffffff'
                }]
            },
            options: { ...commonOptions, cutout: '65%' }
        });
    }

    // 3. Purpose Pie Chart
    const ctxPurpose = document.getElementById('purposeChart');
    if (ctxPurpose) {
        @php
            $purposeLabels = $purposeStats->keys()->toJson();
            $purposeData = $purposeStats->pluck('qty')->toJson();
        @endphp
        new Chart(ctxPurpose.getContext('2d'), {
            type: 'pie',
            data: {
                labels: {!! $purposeLabels !!},
                datasets: [{
                    data: {!! $purposeData !!},
                    backgroundColor: ['#10b981', '#f97316', '#a855f7', '#3b82f6', '#ef4444'], // emerald, orange, purple, blue, red
                    borderWidth: 2, borderColor: '#ffffff'
                }]
            },
            options: commonOptions
        });
    }

    // 4. Payment Pie Chart
    const ctxPayment = document.getElementById('paymentChart');
    if (ctxPayment) {
        @php
            $paymentLabels = $paymentStats->keys()->toJson();
            $paymentData = $paymentStats->pluck('qty')->toJson();
        @endphp
        new Chart(ctxPayment.getContext('2d'), {
            type: 'pie',
            data: {
                labels: {!! $paymentLabels !!},
                datasets: [{
                    data: {!! $paymentData !!},
                    backgroundColor: ['#22c55e', '#3b82f6', '#6366f1', '#eab308'], // green, blue, indigo, yellow
                    borderWidth: 2, borderColor: '#ffffff'
                }]
            },
            options: commonOptions
        });
    }

    // Realtime Polling for Dashboard Statistics
    function pollDashboardStats() {
        // Build request URL with active filters
        const url = new URL('{{ route("admin.dashboard.realtime-stats") }}');
        const destSelect = document.querySelector('select[name="destination_id"]');
        const filterSelect = document.querySelector('select[name="filter"]');
        
        if (destSelect && destSelect.value) {
            url.searchParams.append('destination_id', destSelect.value);
        }
        if (filterSelect && filterSelect.value) {
            url.searchParams.append('filter', filterSelect.value);
            if (filterSelect.value === 'year_range') {
                const startYearSelect = document.querySelector('select[name="start_year"]');
                const endYearSelect = document.querySelector('select[name="end_year"]');
                if (startYearSelect && startYearSelect.value) {
                    url.searchParams.append('start_year', startYearSelect.value);
                }
                if (endYearSelect && endYearSelect.value) {
                    url.searchParams.append('end_year', endYearSelect.value);
                }
            }
        }

        fetch(url)
            .then(response => response.json())
            .then(data => {
                // Update top summary cards dynamically
                const totalVisitorsEl = document.getElementById('stat-total-visitors');
                if (totalVisitorsEl && totalVisitorsEl.innerText !== data.total_visitors_formatted) {
                    totalVisitorsEl.innerText = data.total_visitors_formatted;
                }

                const totalRevenueEl = document.getElementById('stat-total-revenue');
                if (totalRevenueEl && totalRevenueEl.innerText !== data.total_revenue_formatted) {
                    totalRevenueEl.innerText = data.total_revenue_formatted;
                }

                const averageAgeEl = document.getElementById('stat-average-age');
                if (averageAgeEl && averageAgeEl.innerText !== String(data.average_age)) {
                    averageAgeEl.innerText = data.average_age;
                }

                const activeInsideEl = document.getElementById('stat-active-inside');
                if (activeInsideEl && activeInsideEl.innerText !== data.active_inside_formatted) {
                    activeInsideEl.innerText = data.active_inside_formatted;
                }

                // Update Gender Doughnut Chart dynamically with animation
                if (window.genderChartInstance) {
                    window.genderChartInstance.data.datasets[0].data = [data.total_male, data.total_female];
                    window.genderChartInstance.update();
                }

                // Update Age Category Doughnut Chart dynamically with animation
                if (window.ageChartInstance) {
                    window.ageChartInstance.data.datasets[0].data = [data.total_male + data.total_female, data.total_kids];
                    window.ageChartInstance.update();
                }
            })
            .catch(err => console.error('Error polling dashboard stats:', err));
    }

    // Run polling every 5 seconds
    setInterval(pollDashboardStats, 5000);
});
</script>
@endsection
