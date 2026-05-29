@extends('layouts.app')

@section('title', 'Pembayaran Tiket - Wisata Sanggabuana')

@section('content')
<div class="pt-24 pb-16 min-h-screen bg-forest-50/50">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-gradient-to-r from-forest-800 to-forest-950 rounded-3xl p-8 md:p-12 shadow-md text-white mb-8 relative overflow-hidden" style="background: linear-gradient(135deg, #15803d 0%, #14532d 100%);">
            <div class="absolute right-0 top-0 opacity-10 pointer-events-none transform translate-x-12 -translate-y-12">
                <i data-lucide="credit-card" class="w-64 h-64"></i>
            </div>
            <div class="relative z-10">
                <h1 class="text-3xl md:text-4xl font-black text-white tracking-tight leading-none mb-3">Pembayaran Tiket</h1>
                <p class="text-green-100 text-sm md:text-base max-w-2xl font-medium">Selesaikan pembayaran Anda melalui metode yang tersedia.</p>
            </div>
        </div>

        <div class="bg-white rounded-3xl p-6 sm:p-8 shadow-sm border border-forest-100 mb-6">
            <h3 class="text-lg font-bold text-forest-950 mb-4 flex items-center gap-2">
                <i data-lucide="receipt" class="w-5 h-5 text-forest-600"></i> Ringkasan Pesanan
            </h3>
            <div class="divide-y divide-gray-100">
                @php
                    $items = $pending->form_data['items'] ?? [];
                    $ticketTotal = (int) ($pending->form_data['total_amount'] ?? 0);
                    
                    $paymentMethod = $pending->payment_method ?? 'qris';
                    $midtransService = new \App\Services\MidtransService();
                    $paymentGroup = $midtransService->getPaymentGroup($paymentMethod);
                    $feeConfig = $midtransService->getPaymentFees($paymentGroup);
                    
                    if ($feeConfig['type'] === 'fix') {
                        $adminFee = $feeConfig['amount'];
                        $feeLabel = 'Biaya Admin ' . strtoupper($paymentMethod);
                    } else {
                        $adminFee = (int) round($ticketTotal * ($feeConfig['percentage'] ?? 0.02));
                        $feeLabel = 'Biaya Admin ' . strtoupper($paymentMethod) . ' (' . (($feeConfig['percentage'] ?? 0.02) * 100) . '%)';
                    }
                    
                    $grandTotal = $ticketTotal + $adminFee;
                @endphp
                @forelse($items as $item)
                <div class="py-3 flex items-center justify-between">
                    <div>
                        <div class="font-semibold text-gray-800 text-sm">{{ $item['name'] }}</div>
                    </div>
                    <div class="text-sm font-bold text-forest-700">Rp {{ number_format($item['price'] * $item['quantity'], 0, ',', '.') }}</div>
                </div>
                @empty
                <div class="py-3 flex items-center justify-between">
                    <div class="font-semibold text-gray-800 text-sm">{{ $pending->form_data['leader']['name'] ?? 'Tiket' }}</div>
                    <div class="text-sm font-bold text-forest-700">Rp {{ number_format($ticketTotal, 0, ',', '.') }}</div>
                </div>
                @endforelse
            </div>
            <div class="mt-3 pt-3 border-t border-gray-100">
                <div class="flex justify-between items-center text-sm text-gray-600 mb-1">
                    <span>Subtotal Tiket</span>
                    <span class="font-semibold">Rp {{ number_format($ticketTotal, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between items-center text-sm text-gray-600 mb-1">
                    <span>{{ $feeLabel }}</span>
                    <span class="font-semibold">Rp {{ number_format($adminFee, 0, ',', '.') }}</span>
                </div>
            </div>
            <div class="mt-4 pt-4 border-t-2 border-dashed border-gray-200 flex justify-between items-center">
                <span class="text-base font-bold text-gray-800">Total Pembayaran</span>
                <span class="text-xl font-black text-forest-700">Rp {{ number_format($grandTotal, 0, ',', '.') }}</span>
            </div>
            <div id="snap-container" class="flex justify-center">
                <button id="pay-button" class="px-8 py-4 bg-forest-600 hover:bg-forest-700 text-white font-bold rounded-xl transition-all shadow-md hover:shadow-forest-500/20 text-base flex items-center gap-3">
                    <i data-lucide="credit-card" class="w-5 h-5"></i>
                    Bayar
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('midtrans.client_key') }}"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const payButton = document.getElementById('pay-button');
        const snapToken = '{{ $snapToken }}';

        payButton.addEventListener('click', function(e) {
            e.preventDefault();

            snap.pay(snapToken, {
                onSuccess: function(result) {
                    window.location.href = '{{ route("payment.finish", $pending->temp_token) }}';
                },
                onPending: function(result) {
                    window.location.href = '{{ route("payment.finish", $pending->temp_token) }}';
                },
                onError: function(result) {
                    alert('Pembayaran gagal! Silakan coba lagi.');
                },
                onClose: function() {
                    // User closed the popup without finishing
                }
            });
        });
    });
</script>
@endpush
