@extends('layouts.app')

@section('title', 'Detail Tiket - Wisata Sanggabuana')

@push('styles')
<style>
    @media print {
        body * { visibility: hidden; }
        #receipt-area, #receipt-area * { visibility: visible; }
        #receipt-area { position: absolute; left: 0; top: 0; width: 100%; }
        .no-print { display: none !important; }
    }
</style>
@endpush

@section('content')
<div class="pt-24 pb-16 min-h-screen bg-forest-50/50">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="no-print flex items-center gap-4 mb-6">
            <a href="{{ route('visitor.tiket-saya') }}" class="p-2 bg-white rounded-xl shadow-sm border border-forest-100">
                <i data-lucide="arrow-left" class="w-5 h-5 text-gray-600"></i>
            </a>
            <h1 class="text-2xl font-black text-forest-900">Detail Tiket</h1>
        </div>

        <div id="receipt-area" class="space-y-6">
            @foreach($group as $ticket)
            <div class="relative overflow-hidden rounded-3xl shadow-md border border-green-200/60 bg-white">
                <div class="bg-gradient-to-r from-emerald-600 to-forest-700 px-6 py-4 flex items-center justify-between" style="background: linear-gradient(135deg, #059669 0%, #15803d 100%);">
                    <div class="flex items-center gap-3">
                        <i data-lucide="check-circle-2" class="w-5 h-5 text-white"></i>
                        <div>
                            <h4 class="font-extrabold text-white text-xs tracking-wide uppercase">Tiket Aktif</h4>
                            <p class="text-green-100 text-[10px]">E-Ticket resmi {{ $destination->name ?? '' }}</p>
                        </div>
                    </div>
                </div>

                <div class="p-6 space-y-4">
                    <div class="text-center pb-4 border-b border-dashed border-gray-150">
                        <span class="text-[10px] text-gray-400 uppercase tracking-widest font-semibold">Nomor Tiket</span>
                        <div class="font-black text-gray-900 text-xl tracking-tight mt-1">{{ $ticket->ticket_no }}</div>
                    </div>

                    <div class="space-y-2.5 text-xs text-gray-600">
                        <div class="flex justify-between">
                            <span class="text-gray-400">Pengunjung:</span>
                            <strong class="text-gray-800">{{ $ticket->name }}</strong>
                        </div>
                        @if($ticket->email)
                        <div class="flex justify-between">
                            <span class="text-gray-400">Email:</span>
                            <strong class="text-gray-800">{{ $ticket->email }}</strong>
                        </div>
                        @endif
                        <div class="flex justify-between">
                            <span class="text-gray-400">Destinasi:</span>
                            <strong class="text-gray-800">{{ $destination->name ?? '' }}</strong>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-400">Tanggal Kunjungan:</span>
                            <strong class="text-gray-800">{{ \Carbon\Carbon::parse($ticket->visit_date)->translatedFormat('d F Y') }}</strong>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-400">Metode Bayar:</span>
                            <strong class="text-gray-800">{{ $ticket->payment_method }}</strong>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-400">Status:</span>
                            <strong class="text-green-600">LUNAS</strong>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-400">Total Harga:</span>
                            <strong class="text-forest-700 font-extrabold">Rp {{ number_format($ticket->total_price, 0, ',', '.') }}</strong>
                        </div>
                    </div>

                    <div class="flex flex-col items-center justify-center pt-4 border-t border-dashed border-gray-150 gap-2">
                        <div class="w-32 h-32 p-2 bg-white border border-gray-150 rounded-xl shadow-sm">
                            <img src="https://api.qrserver.com/v1/create-qr-code/?size=300x300&data={{ urlencode($ticket->ticket_no) }}&margin=5"
                                 alt="QR Code {{ $ticket->ticket_no }}"
                                 class="w-full h-full object-contain">
                        </div>
                        <span class="text-[9px] text-gray-400 font-bold uppercase tracking-widest text-center mt-1">Simpan QR Code ini untuk masuk</span>
                    </div>
                </div>
            </div>
            @endforeach

            <div class="no-print text-center">
                <button type="button" onclick="window.print()" class="px-6 py-3 bg-forest-600 hover:bg-forest-700 text-white font-bold rounded-xl transition-all shadow-md text-sm inline-flex items-center gap-2">
                    <i data-lucide="printer" class="w-4 h-4"></i> Cetak Tiket
                </button>
                <a href="{{ route('visitor.tiket-saya') }}" class="ml-3 px-6 py-3 bg-white border border-gray-200 text-gray-700 font-bold rounded-xl hover:bg-gray-50 transition-all text-sm inline-flex items-center gap-2">
                    Kembali
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
