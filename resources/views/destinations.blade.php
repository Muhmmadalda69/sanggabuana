@extends('layouts.app')

@section('title', 'Semua Destinasi Wisata Sanggabuana')

@section('content')
{{-- Page Header --}}
<div class="pt-28 pb-16 bg-forest-950 relative overflow-hidden">
    <div class="absolute inset-0 opacity-20">
        <img src="https://images.unsplash.com/photo-1542224566-6e85f2e6772f?q=80&w=2000&auto=format&fit=crop" class="w-full h-full object-cover">
        <div class="absolute inset-0 bg-gradient-to-t from-forest-950 to-transparent"></div>
    </div>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 text-center">
        <h1 class="text-4xl md:text-5xl font-bold text-white mb-4 animate-fade-up">Destinasi Wisata</h1>
        <p class="text-forest-200 text-lg max-w-2xl mx-auto animate-fade-up" style="animation-delay: 0.1s">Temukan dan jelajahi berbagai keindahan alam yang ditawarkan oleh Gunung Sanggabuana.</p>
    </div>
</div>

<div class="py-16 bg-forest-50 min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        @if($destinations->count() > 0)
            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8 mb-12">
                @foreach($destinations as $dest)
                <div class="destination-card bg-white rounded-3xl overflow-hidden shadow-xl border border-forest-50">
                    <div class="relative h-64 overflow-hidden">
                        <img src="{{ $dest->image_url }}" alt="{{ $dest->name }}" class="w-full h-full object-cover destination-image">
                        @if($dest->is_featured)
                        <div class="absolute top-4 right-4 bg-white/90 backdrop-blur px-3 py-1 rounded-full text-xs font-bold text-forest-800 flex items-center gap-1 shadow-lg">
                            <i data-lucide="trending-up" class="w-3 h-3 text-yellow-500"></i> Populer
                        </div>
                        @endif
                        <div class="absolute bottom-0 left-0 w-full h-1/2 bg-gradient-to-t from-forest-950/80 to-transparent"></div>
                        <div class="absolute bottom-4 left-4 text-white">
                            <div class="flex items-center gap-2 text-sm font-medium">
                                <i data-lucide="mountain" class="w-4 h-4 text-forest-300"></i>
                                {{ $dest->altitude ?? 'Ketinggian bervariasi' }}
                            </div>
                        </div>
                    </div>
                    <div class="p-6">
                        <div class="flex justify-between items-start mb-4">
                            <h3 class="text-xl font-bold text-forest-950">{{ $dest->name }}</h3>
                            <div class="px-2 py-1 rounded-md text-xs font-bold bg-forest-100 text-forest-700 flex items-center gap-1">
                                <i data-lucide="clock" class="w-3 h-3"></i> {{ $dest->operational_hours ?? '-' }}
                            </div>
                        </div>
                        <p class="text-forest-600 text-sm mb-6 line-clamp-2">
                            {{ $dest->short_description }}
                        </p>
                        
                        <div class="grid grid-cols-2 gap-4 mb-6">
                            <div class="flex items-center gap-2 text-sm text-forest-700">
                                <i data-lucide="map-pin" class="w-4 h-4 text-forest-400"></i>
                                <span class="truncate">{{ $dest->location ?? 'Karawang' }}</span>
                            </div>
                            <div class="flex items-center gap-2 text-sm text-forest-700">
                                <i data-lucide="clock" class="w-4 h-4 text-forest-400"></i>
                                <span class="truncate">{{ $dest->duration ?? '-' }}</span>
                            </div>
                        </div>

                        <div class="flex items-center justify-between pt-4 border-t border-forest-50">
                            <div class="text-forest-900 font-bold">
                                Rp {{ number_format($dest->price, 0, ',', '.') }}<span class="text-xs text-forest-500 font-normal">/orang</span>
                            </div>
                            <a href="{{ route('destination.detail', $dest->slug) }}" class="inline-flex items-center gap-2 px-4 py-2 bg-forest-50 hover:bg-forest-600 hover:text-white rounded-xl text-forest-600 transition-colors text-sm font-bold">
                                Detail <i data-lucide="arrow-right" class="w-4 h-4"></i>
                            </a>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            
            {{-- Pagination --}}
            @if($destinations->hasPages())
                <div class="flex justify-center mt-12">
                    {{ $destinations->links() }}
                </div>
            @endif
        @else
            <div class="text-center py-20">
                <div class="w-24 h-24 bg-white rounded-full flex items-center justify-center mx-auto mb-6 shadow-sm">
                    <i data-lucide="map-x" class="w-12 h-12 text-forest-300"></i>
                </div>
                <h2 class="text-2xl font-bold text-forest-950 mb-2">Belum Ada Destinasi</h2>
                <p class="text-forest-600">Saat ini belum ada destinasi yang dipublikasikan.</p>
                <a href="{{ route('home') }}" class="inline-block mt-6 px-6 py-3 bg-forest-600 text-white rounded-xl hover:bg-forest-700 transition-colors font-medium">Kembali ke Beranda</a>
            </div>
        @endif
    </div>
</div>
@endsection
