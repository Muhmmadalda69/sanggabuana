@extends('layouts.app')

@section('title', 'Status Pembayaran - Wisata Sanggabuana')

@push('styles')
@if($visitor->payment_status === 'success')
<style>
    @media print {
        body * { visibility: hidden; }
        #receipt-area, #receipt-area * { visibility: visible; }
        #receipt-area { position: absolute; left: 0; top: 0; width: 100%; }
        .no-print { display: none !important; }
    }
</style>
@endif
@endpush

@section('content')
<div class="pt-24 pb-16 min-h-screen bg-forest-50/50">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        @if($visitor->payment_status === 'success')
        <div class="bg-gradient-to-r from-emerald-600 to-forest-700 rounded-3xl p-8 md:p-12 shadow-md text-white mb-8 relative overflow-hidden" style="background: linear-gradient(135deg, #059669 0%, #15803d 100%);">
            <div class="absolute right-0 top-0 opacity-10 pointer-events-none transform translate-x-12 -translate-y-12">
                <i data-lucide="check-circle-2" class="w-64 h-64"></i>
            </div>
            <div class="relative z-10 text-center">
                <div class="w-20 h-20 bg-white/20 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i data-lucide="check-circle-2" class="w-12 h-12 text-white"></i>
                </div>
                <h1 class="text-3xl md:text-4xl font-black text-white tracking-tight leading-none mb-3">Pembayaran Berhasil!</h1>
                <p class="text-emerald-100 text-sm md:text-base max-w-2xl font-medium mx-auto">Tiket Anda telah aktif. Silakan simpan QR Code di bawah untuk masuk.</p>
            </div>
        </div>
        @elseif($visitor->payment_status === 'pending')
        <div class="bg-gradient-to-r from-amber-500 to-orange-600 rounded-3xl p-8 md:p-12 shadow-md text-white mb-8 relative overflow-hidden">
            <div class="absolute right-0 top-0 opacity-10 pointer-events-none transform translate-x-12 -translate-y-12">
                <i data-lucide="clock" class="w-64 h-64"></i>
            </div>
            <div class="relative z-10 text-center">
                <div class="w-20 h-20 bg-white/20 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i data-lucide="clock" class="w-12 h-12 text-white"></i>
                </div>
                <h1 class="text-3xl md:text-4xl font-black text-white tracking-tight leading-none mb-3">Pembayaran Sedang Diproses</h1>
                <p class="text-amber-100 text-sm md:text-base max-w-2xl font-medium mx-auto">Pembayaran Anda sedang menunggu konfirmasi. Tiket akan aktif setelah pembayaran terverifikasi.</p>
                <div class="no-print flex flex-wrap justify-center gap-3 mt-6">
                    <a href="{{ route('payment.pay', $visitor->payment_token) }}" class="inline-flex items-center gap-2 px-6 py-3 bg-white text-amber-700 font-bold rounded-xl hover:bg-amber-50 transition-all text-sm">
                        <i data-lucide="credit-card" class="w-4 h-4"></i> Lanjutkan Pembayaran
                    </a>
                    <button type="button" onclick="document.getElementById('modal-change-method').classList.remove('hidden')" class="inline-flex items-center gap-2 px-6 py-3 bg-white/20 text-white font-bold rounded-xl hover:bg-white/30 transition-all text-sm border border-white/30">
                        <i data-lucide="refresh-cw" class="w-4 h-4"></i> Ganti Metode Pembayaran
                    </button>
                </div>
            </div>
        </div>
        @elseif($visitor->payment_status === 'expired')
        <div class="bg-gradient-to-r from-gray-500 to-gray-700 rounded-3xl p-8 md:p-12 shadow-md text-white mb-8 relative overflow-hidden">
            <div class="absolute right-0 top-0 opacity-10 pointer-events-none transform translate-x-12 -translate-y-12">
                <i data-lucide="clock" class="w-64 h-64"></i>
            </div>
            <div class="relative z-10 text-center">
                <div class="w-20 h-20 bg-white/20 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i data-lucide="clock" class="w-12 h-12 text-white"></i>
                </div>
                <h1 class="text-3xl md:text-4xl font-black text-white tracking-tight leading-none mb-3">Sesi Kadaluwarsa</h1>
                <p class="text-gray-100 text-sm md:text-base max-w-2xl font-medium mx-auto">Sesi pembayaran telah kadaluwarsa. Silakan daftar ulang.</p>
                <div class="no-print flex flex-wrap justify-center gap-3 mt-6">
                    <a href="{{ route('destination.register.date', $destination->slug ?? '') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-white text-gray-700 font-bold rounded-xl hover:bg-gray-50 transition-all text-sm">
                        <i data-lucide="refresh-ccw" class="w-4 h-4"></i> Daftar Ulang
                    </a>
                </div>
            </div>
        </div>
        @else
        <div class="bg-gradient-to-r from-red-500 to-red-700 rounded-3xl p-8 md:p-12 shadow-md text-white mb-8 relative overflow-hidden">
            <div class="absolute right-0 top-0 opacity-10 pointer-events-none transform translate-x-12 -translate-y-12">
                <i data-lucide="x-circle" class="w-64 h-64"></i>
            </div>
            <div class="relative z-10 text-center">
                <div class="w-20 h-20 bg-white/20 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i data-lucide="x-circle" class="w-12 h-12 text-white"></i>
                </div>
                <h1 class="text-3xl md:text-4xl font-black text-white tracking-tight leading-none mb-3">Pembayaran Gagal</h1>
                <p class="text-red-100 text-sm md:text-base max-w-2xl font-medium mx-auto">Pembayaran Anda tidak berhasil. Silakan coba lagi.</p>
                <div class="no-print flex flex-wrap justify-center gap-3 mt-6">
                    <a href="{{ route('payment.pay', $visitor->payment_token) }}" class="inline-flex items-center gap-2 px-6 py-3 bg-white text-red-700 font-bold rounded-xl hover:bg-red-50 transition-all text-sm">
                        <i data-lucide="refresh-ccw" class="w-4 h-4"></i> Coba Lagi
                    </a>
                    <button type="button" onclick="document.getElementById('modal-change-method').classList.remove('hidden')" class="inline-flex items-center gap-2 px-6 py-3 bg-white/20 text-white font-bold rounded-xl hover:bg-white/30 transition-all text-sm border border-white/30">
                        <i data-lucide="refresh-cw" class="w-4 h-4"></i> Ganti Metode Pembayaran
                    </button>
                </div>
            </div>
        </div>
        @endif

        @if($visitor->payment_status === 'success')
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
                <a href="{{ route('home') }}" class="ml-3 px-6 py-3 bg-white border border-gray-200 text-gray-700 font-bold rounded-xl hover:bg-gray-50 transition-all text-sm inline-flex items-center gap-2">
                    Kembali ke Beranda
                </a>
            </div>
        </div>
        @endif
    </div>
</div>

{{-- Modal Ganti Metode Pembayaran --}}
<div id="modal-change-method" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm">
    <div class="bg-white rounded-3xl p-6 w-full max-w-md mx-4 shadow-2xl">
        <div class="flex items-center justify-between mb-5">
            <h3 class="font-bold text-gray-900 text-lg">Ganti Metode Pembayaran</h3>
            <button type="button" onclick="document.getElementById('modal-change-method').classList.add('hidden')" class="text-gray-400 hover:text-gray-600 transition-colors">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>
        <form action="{{ route('payment.change-method', $visitor->payment_token) }}" method="POST">
            @csrf
            <div class="space-y-3 mb-6">
                <label class="flex items-center gap-3 p-4 border border-gray-200 rounded-xl cursor-pointer hover:border-forest-500 transition-all has-[:checked]:bg-forest-50 has-[:checked]:border-forest-600">
                    <input type="radio" name="payment_method" value="Transfer" class="text-forest-600 focus:ring-forest-500" checked>
                    <div>
                        <span class="font-semibold text-gray-800 text-sm">Transfer / QRIS</span>
                        <p class="text-xs text-gray-400">Bayar via transfer virtual account atau scan QR</p>
                    </div>
                </label>
            </div>
            <div class="flex gap-3">
                <button type="button" onclick="document.getElementById('modal-change-method').classList.add('hidden')" class="w-full py-3 bg-gray-100 text-gray-700 font-bold rounded-xl hover:bg-gray-200 transition-all text-sm">
                    Batal
                </button>
                <button type="submit" class="w-full py-3 bg-forest-600 hover:bg-forest-700 text-white font-bold rounded-xl transition-all text-sm">
                    Simpan & Lanjutkan
                </button>
            </div>
        </form>
    </div>
</div>
<script>
    lucide.createIcons();
</script>
@endsection
