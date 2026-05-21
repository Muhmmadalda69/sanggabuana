@extends('layouts.app')

@section('title', 'Riwayat Transaksi - Wisata Sanggabuana')

@section('content')
<div class="pt-24 pb-16 min-h-screen bg-forest-50/50">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center gap-4 mb-6">
            <a href="{{ route('visitor.dashboard') }}" class="p-2 bg-white rounded-xl shadow-sm border border-forest-100">
                <i data-lucide="arrow-left" class="w-5 h-5 text-gray-600"></i>
            </a>
            <h1 class="text-2xl font-black text-forest-900">Riwayat Transaksi</h1>
        </div>

        @if($transactions->isEmpty())
            <div class="bg-white rounded-3xl p-12 shadow-sm border border-forest-100 text-center">
                <div class="w-16 h-16 bg-gray-100 text-gray-400 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i data-lucide="receipt" class="w-8 h-8"></i>
                </div>
                <h3 class="font-bold text-gray-700">Belum Ada Transaksi</h3>
                <p class="text-sm text-gray-500 mt-1">Anda belum melakukan transaksi apapun.</p>
            </div>
        @else
            <div class="space-y-4">
                @foreach($transactions as $trx)
                    <div class="bg-white rounded-3xl p-6 shadow-sm border border-forest-100">
                        <div class="flex items-start justify-between">
                            <div class="space-y-1">
                                <div class="flex items-center gap-2">
                                    @if($trx['type'] === 'pending' && $trx['status'] === 'pending')
                                        <span class="px-2.5 py-0.5 bg-amber-50 text-amber-700 rounded-full text-xs font-semibold">Menunggu</span>
                                    @elseif($trx['type'] === 'pending' && $trx['status'] === 'expired')
                                        <span class="px-2.5 py-0.5 bg-red-50 text-red-700 rounded-full text-xs font-semibold">Kedaluwarsa</span>
                                    @elseif($trx['type'] === 'pending' && $trx['status'] === 'failed')
                                        <span class="px-2.5 py-0.5 bg-red-50 text-red-700 rounded-full text-xs font-semibold">Gagal</span>
                                    @else
                                        <span class="px-2.5 py-0.5 bg-green-50 text-green-700 rounded-full text-xs font-semibold">Berhasil</span>
                                    @endif
                                </div>
                                <h3 class="font-bold text-gray-900">{{ $trx['destination'] }}</h3>
                                <p class="text-xs text-gray-500">
                                    {{ \Carbon\Carbon::parse($trx['visit_date'])->translatedFormat('d F Y') }}
                                </p>
                            </div>
                            <div class="text-right">
                                <p class="font-bold text-gray-900">Rp {{ number_format($trx['total_amount'], 0, ',', '.') }}</p>
                                <p class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($trx['created_at'])->diffForHumans() }}</p>
                            </div>
                        </div>

                        @if($trx['type'] === 'pending' && $trx['status'] === 'pending')
                            <div class="mt-4 pt-4 border-t border-gray-100 flex items-center gap-2">
                                <a href="{{ route('payment.pay', $trx['temp_token']) }}" class="text-xs font-semibold text-white bg-forest-600 hover:bg-forest-700 px-4 py-2 rounded-xl transition-all">
                                    Bayar Sekarang
                                </a>
                                <form action="{{ route('payment.cancel', $trx['temp_token']) }}" method="POST" onsubmit="return confirm('Batalkan transaksi ini?')">
                                    @csrf
                                    <button type="submit" class="text-xs font-semibold text-red-600 bg-red-50 hover:bg-red-100 px-4 py-2 rounded-xl transition-all">
                                        Batal
                                    </button>
                                </form>
                            </div>
                        @endif

                        @if(isset($trx['ticket_count']))
                            <div class="mt-4 pt-4 border-t border-gray-100 flex items-center gap-2 text-xs text-gray-500">
                                <i data-lucide="ticket" class="w-4 h-4"></i>
                                {{ $trx['ticket_count'] }} tiket
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
@endsection
