@extends('layouts.app')

@section('title', 'Akun Saya - Wisata Sanggabuana')

@section('content')
<div class="pt-24 pb-16 min-h-screen bg-forest-50/50">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-gradient-to-r from-forest-800 to-forest-950 rounded-3xl p-8 md:p-12 shadow-md text-white mb-8 relative overflow-hidden" style="background: linear-gradient(135deg, #15803d 0%, #14532d 100%);">
            <div class="absolute right-0 top-0 opacity-10 pointer-events-none transform translate-x-12 -translate-y-12">
                <i data-lucide="user" class="w-64 h-64"></i>
            </div>
            <div class="relative z-10">
                <h1 class="text-3xl md:text-4xl font-black text-white tracking-tight leading-none mb-2">Halo, {{ $account->name }}!</h1>
                <p class="text-green-100 text-sm md:text-base max-w-2xl font-medium">Selamat datang di akun Anda.</p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
            <div class="bg-white rounded-3xl p-6 shadow-sm border border-forest-100">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-amber-50 text-amber-600 rounded-2xl flex items-center justify-center">
                        <i data-lucide="clock" class="w-6 h-6"></i>
                    </div>
                    <div>
                        <p class="text-2xl font-black text-gray-900">{{ $pendingCount }}</p>
                        <p class="text-xs text-gray-500">Menunggu Pembayaran</p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-3xl p-6 shadow-sm border border-forest-100">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-green-50 text-green-600 rounded-2xl flex items-center justify-center">
                        <i data-lucide="ticket" class="w-6 h-6"></i>
                    </div>
                    <div>
                        <p class="text-2xl font-black text-gray-900">{{ $ticketCount }}</p>
                        <p class="text-xs text-gray-500">Tiket Aktif</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <a href="{{ route('visitor.riwayat') }}" class="bg-white rounded-3xl p-6 shadow-sm border border-forest-100 hover:border-forest-300 transition-all flex items-center gap-4">
                <div class="w-12 h-12 bg-forest-50 text-forest-600 rounded-2xl flex items-center justify-center">
                    <i data-lucide="receipt" class="w-6 h-6"></i>
                </div>
                <div>
                    <h3 class="font-bold text-gray-900">Riwayat Transaksi</h3>
                    <p class="text-xs text-gray-500">Lihat semua transaksi Anda</p>
                </div>
                <i data-lucide="chevron-right" class="w-5 h-5 text-gray-400 ml-auto"></i>
            </a>
            <a href="{{ route('visitor.tiket-saya') }}" class="bg-white rounded-3xl p-6 shadow-sm border border-forest-100 hover:border-forest-300 transition-all flex items-center gap-4">
                <div class="w-12 h-12 bg-forest-50 text-forest-600 rounded-2xl flex items-center justify-center">
                    <i data-lucide="ticket" class="w-6 h-6"></i>
                </div>
                <div>
                    <h3 class="font-bold text-gray-900">Tiket Saya</h3>
                    <p class="text-xs text-gray-500">Lihat tiket yang sudah aktif</p>
                </div>
                <i data-lucide="chevron-right" class="w-5 h-5 text-gray-400 ml-auto"></i>
            </a>
        </div>
    </div>
</div>
@endsection
