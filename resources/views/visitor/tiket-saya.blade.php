@extends('layouts.app')

@section('title', 'Tiket Saya - Wisata Sanggabuana')

@section('content')
<div class="pt-24 pb-16 min-h-screen bg-forest-50/50">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center gap-4 mb-6">
            <a href="{{ route('visitor.dashboard') }}" class="p-2 bg-white rounded-xl shadow-sm border border-forest-100">
                <i data-lucide="arrow-left" class="w-5 h-5 text-gray-600"></i>
            </a>
            <h1 class="text-2xl font-black text-forest-900">Tiket Saya</h1>
        </div>

        @if($groups->isEmpty())
            <div class="bg-white rounded-3xl p-12 shadow-sm border border-forest-100 text-center">
                <div class="w-16 h-16 bg-gray-100 text-gray-400 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i data-lucide="ticket" class="w-8 h-8"></i>
                </div>
                <h3 class="font-bold text-gray-700">Belum Ada Tiket</h3>
                <p class="text-sm text-gray-500 mt-1">Anda belum memiliki tiket aktif.</p>
            </div>
        @else
            <div class="space-y-6">
                @foreach($groups as $groupId => $visitors)
                    @php $first = $visitors->first(); @endphp
                    <div class="bg-white rounded-3xl shadow-sm border border-forest-100 overflow-hidden">
                        <div class="p-6 border-b border-forest-50">
                            <div class="flex items-start justify-between">
                                <div>
                                    <h3 class="font-bold text-gray-900">{{ $first->destination->name ?? 'Unknown' }}</h3>
                                    <p class="text-xs text-gray-500">
                                        {{ \Carbon\Carbon::parse($first->visit_date)->translatedFormat('d F Y') }}
                                    </p>
                                </div>
                                <span class="px-2.5 py-0.5 bg-green-50 text-green-700 rounded-full text-xs font-semibold">Aktif</span>
                            </div>
                        </div>

                        <div class="divide-y divide-gray-50">
                            @foreach($visitors as $visitor)
                                <div class="px-6 py-3 flex items-center gap-3 text-sm">
                                    <div class="w-8 h-8 bg-forest-50 text-forest-600 rounded-lg flex items-center justify-center">
                                        <i data-lucide="user" class="w-4 h-4"></i>
                                    </div>
                                    <div>
                                        <p class="font-medium text-gray-800">{{ $visitor->name }}</p>
                                        <p class="text-xs text-gray-500">{{ $visitor->identity_type }}: {{ $visitor->identity_number }}</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="px-6 py-3 bg-gray-50/50 flex items-center justify-between text-xs text-gray-500">
                            <span>Total: Rp {{ number_format($visitors->sum('total_price'), 0, ',', '.') }} &middot; {{ $visitors->count() }} tiket</span>
                            <a href="{{ route('visitor.tiket-detail', $groupId) }}" class="inline-flex items-center gap-1 text-forest-600 hover:text-forest-700 font-semibold transition-colors">
                                Lihat Tiket <i data-lucide="eye" class="w-3.5 h-3.5"></i>
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
@endsection
