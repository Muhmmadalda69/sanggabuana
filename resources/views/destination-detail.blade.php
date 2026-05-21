@extends('layouts.app')

@section('title', $destination->name . ' - Wisata Sanggabuana')

@section('content')
{{-- Hero Section --}}
<div class="relative h-[60vh] min-h-[400px] flex items-end pb-16">
    <div class="absolute inset-0 z-0">
        <img src="{{ $destination->image_url }}" alt="{{ $destination->name }}" class="w-full h-full object-cover">
        <div class="absolute inset-0 bg-gradient-to-t from-forest-950 via-forest-950/60 to-transparent"></div>
    </div>
    
    <div class="relative z-10 w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <a href="{{ route('destinations') }}" class="inline-flex items-center gap-2 text-white/80 hover:text-white mb-6 transition-colors animate-fade-up">
            <i data-lucide="arrow-left" class="w-4 h-4"></i> Kembali ke Daftar Destinasi
        </a>
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 animate-fade-up" style="animation-delay: 0.1s">
            <div>
                <div class="flex items-center gap-3 mb-3">
                    <span class="px-3 py-1 bg-white/20 backdrop-blur rounded-full text-white text-xs font-bold border border-white/20 flex items-center gap-1">
                        <i data-lucide="clock" class="w-3 h-3"></i> {{ $destination->operational_hours ?? '-' }}
                    </span>
                    @if($destination->is_featured)
                        <span class="px-3 py-1 bg-yellow-500/90 backdrop-blur rounded-full text-white text-xs font-bold flex items-center gap-1">
                            <i data-lucide="star" class="w-3 h-3 fill-current"></i> Unggulan
                        </span>
                    @endif
                </div>
                <h1 class="text-4xl md:text-6xl font-bold text-white mb-2">{{ $destination->name }}</h1>
                <p class="text-forest-200 text-lg flex items-center gap-2">
                    <i data-lucide="map-pin" class="w-5 h-5"></i> {{ $destination->location ?? 'Karawang, Jawa Barat' }}
                </p>
            </div>
            <div class="bg-white/10 backdrop-blur border border-white/20 p-4 rounded-2xl md:text-right">
                <div class="text-forest-200 text-sm mb-1">Harga Tiket Masuk</div>
                <div class="text-3xl font-bold text-white">Rp {{ number_format($destination->price, 0, ',', '.') }}<span class="text-sm font-normal text-forest-200">/org</span></div>
            </div>
        </div>
    </div>
</div>

<div class="bg-forest-50 py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid lg:grid-cols-3 gap-12">
            {{-- Main Content --}}
            <div class="lg:col-span-2">
                <div class="bg-white rounded-3xl p-8 shadow-sm border border-forest-100 mb-8 animate-fade-up">
                    <h2 class="text-2xl font-bold text-forest-950 mb-6 flex items-center gap-2">
                        <i data-lucide="info" class="w-6 h-6 text-forest-500"></i> Tentang Destinasi Ini
                    </h2>
                    
                    @if($destination->short_description)
                        <p class="text-lg text-forest-800 font-medium mb-6 leading-relaxed">{{ $destination->short_description }}</p>
                    @endif
                    
                    <div class="prose prose-forest prose-lg text-forest-700 max-w-none mb-8">
                        {!! $destination->description !!}
                    </div>
                </div>

                {{-- Gallery --}}
                @if($galleries->count() > 0)
                <div class="bg-white rounded-3xl p-8 shadow-sm border border-forest-100 animate-fade-up">
                    <h2 class="text-2xl font-bold text-forest-950 mb-6 flex items-center gap-2">
                        <i data-lucide="image" class="w-6 h-6 text-forest-500"></i> Galeri Foto
                    </h2>
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                        @foreach($galleries as $gallery)
                            <div class="aspect-square rounded-2xl overflow-hidden group relative">
                                <img src="{{ $gallery->image_url }}" alt="{{ $gallery->title }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                                @if($gallery->title || $gallery->caption)
                                    <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity flex flex-col justify-end p-4">
                                        <div class="text-white font-bold text-sm">{{ $gallery->title }}</div>
                                        <div class="text-gray-300 text-xs line-clamp-1">{{ $gallery->caption }}</div>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>

            {{-- Sidebar --}}
            <div class="space-y-8 animate-fade-up" style="animation-delay: 0.2s">
                {{-- Online Registration Card --}}
                @if($destination->has_online_registration)
                <div class="bg-gradient-to-br from-forest-700 to-forest-900 rounded-3xl p-6 shadow-md text-white relative overflow-hidden" style="background: linear-gradient(135deg, #15803d 0%, #14532d 100%);">
                    <div class="absolute right-0 bottom-0 opacity-10 pointer-events-none transform translate-x-4 translate-y-4">
                        <i data-lucide="qr-code" class="w-48 h-48"></i>
                    </div>
                    
                    <div class="relative z-10 text-center space-y-4">
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[10px] font-bold bg-white/20 text-white mb-2">
                            <span class="w-1.5 h-1.5 rounded-full bg-green-400 animate-pulse"></span>
                            Beli Tiket Online
                        </span>
                        
                        <h3 class="font-extrabold text-white text-xl tracking-wide" style="color:white; margin:0;">Registrasi Online</h3>
                        <p class="text-forest-100 text-xs leading-relaxed" style="color:#dcfce7; margin-top:8px;">Pindai QR Code atau klik tombol di bawah untuk registrasi dan membeli tiket masuk online.</p>
                        
                        {{-- QR Code --}}
                        <div class="mx-auto w-36 h-36 p-2 bg-white rounded-2xl shadow-sm flex items-center justify-center">
                            <img src="https://api.qrserver.com/v1/create-qr-code/?size=300x300&data={{ urlencode(route('destination.register.date', $destination->slug)) }}&margin=5" 
                                 alt="QR Code Registrasi {{ $destination->name }}" 
                                 class="w-full h-full object-contain">
                        </div>
                        
                        {{-- Button --}}
                        <a href="{{ route('destination.register.date', $destination->slug) }}" class="block w-full py-3 bg-white hover:bg-forest-50 text-forest-800 hover:text-forest-900 rounded-xl font-bold text-sm transition-all shadow-sm">
                            Registrasi Sekarang
                        </a>
                    </div>
                </div>
                @endif

                {{-- Quick Info --}}
                <div class="bg-white rounded-3xl p-6 shadow-sm border border-forest-100">
                    <h3 class="font-bold text-forest-950 text-lg mb-6 pb-4 border-b border-forest-50">Informasi Penting</h3>
                    
                    <div class="space-y-5">
                        <div class="flex items-start gap-4">
                            <div class="w-10 h-10 rounded-xl bg-forest-50 flex items-center justify-center text-forest-600 shrink-0">
                                <i data-lucide="mountain" class="w-5 h-5"></i>
                            </div>
                            <div>
                                <div class="text-sm text-forest-500">Ketinggian</div>
                                <div class="font-bold text-forest-900">{{ $destination->altitude ?? '-' }}</div>
                            </div>
                        </div>
                        <div class="flex items-start gap-4">
                            <div class="w-10 h-10 rounded-xl bg-forest-50 flex items-center justify-center text-forest-600 shrink-0">
                                <i data-lucide="clock" class="w-5 h-5"></i>
                            </div>
                            <div>
                                <div class="text-sm text-forest-500">Estimasi Waktu</div>
                                <div class="font-bold text-forest-900">{{ $destination->duration ?? '-' }}</div>
                            </div>
                        </div>
                        <div class="flex items-start gap-4">
                            <div class="w-10 h-10 rounded-xl bg-forest-50 flex items-center justify-center text-forest-600 shrink-0">
                                <i data-lucide="calendar" class="w-5 h-5"></i>
                            </div>
                            <div>
                                <div class="text-sm text-forest-500">Hari Operasional</div>
                                <div class="font-bold text-forest-900">{{ $destination->operational_days ?? '-' }}</div>
                            </div>
                        </div>
                        <div class="flex items-start gap-4">
                            <div class="w-10 h-10 rounded-xl bg-forest-50 flex items-center justify-center text-forest-600 shrink-0">
                                <i data-lucide="clock" class="w-5 h-5"></i>
                            </div>
                            <div>
                                <div class="text-sm text-forest-500">Jam Operasional</div>
                                <div class="font-bold text-forest-900">{{ $destination->operational_hours ?? '-' }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Contacts List --}}
                @if(!empty($destination->contacts) && is_array($destination->contacts) && count($destination->contacts) > 0)
                <div class="bg-white rounded-3xl p-6 shadow-sm border border-forest-100">
                    <h3 class="font-bold text-forest-950 text-lg mb-4 pb-4 border-b border-forest-50 flex items-center gap-2">
                        <i data-lucide="messages-square" class="w-5 h-5 text-forest-600"></i> Kontak Pengelola Wisata
                    </h3>
                    <p class="text-xs text-forest-600 mb-6 bg-forest-50/50 px-3 py-2.5 rounded-xl border border-forest-100 leading-relaxed">
                        Punya pertanyaan khusus atau ingin berkunjung ke <strong>{{ $destination->name }}</strong>? Hubungi langsung pihak pengelola di bawah ini:
                    </p>
                    
                    <div class="space-y-3">
                        @foreach($destination->contacts as $contact)
                            @php
                                $platform = $contact['platform'] ?? 'other';
                                $value = $contact['value'] ?? '';
                                
                                // Determine icon HTML, colors, label, and direct URL
                                $iconHtml = '<i class="fa-solid fa-link"></i>';
                                $colorClass = 'bg-slate-50 text-slate-700 hover:bg-slate-100 border border-slate-200';
                                $label = 'Lainnya';
                                $url = $value;
                                
                                switch($platform) {
                                    case 'whatsapp':
                                        $iconHtml = '<i class="fa-brands fa-whatsapp text-lg text-emerald-600"></i>';
                                        $colorClass = 'bg-emerald-50 text-emerald-700 hover:bg-emerald-100/80 border border-emerald-100';
                                        $label = 'WhatsApp';
                                        
                                        $cleanPhone = preg_replace('/[^0-9]/', '', $value);
                                        if (str_starts_with($cleanPhone, '0')) {
                                            $cleanPhone = '62' . substr($cleanPhone, 1);
                                        }
                                        $url = "https://wa.me/" . $cleanPhone . "?text=" . urlencode("Halo, saya ingin bertanya seputar wisata " . $destination->name);
                                        break;
                                        
                                    case 'instagram':
                                        $iconHtml = '<i class="fa-brands fa-instagram text-lg text-pink-600"></i>';
                                        $colorClass = 'bg-pink-50 text-pink-700 hover:bg-pink-100/80 border border-pink-100';
                                        $label = 'Instagram';
                                        
                                        $username = ltrim($value, '@');
                                        $url = "https://instagram.com/" . $username;
                                        break;
                                        
                                    case 'tiktok':
                                        $iconHtml = '<i class="fa-brands fa-tiktok text-base text-zinc-950"></i>';
                                        $colorClass = 'bg-zinc-50 text-zinc-800 hover:bg-zinc-100 border border-zinc-200';
                                        $label = 'TikTok';
                                        
                                        $username = ltrim($value, '@');
                                        $url = "https://tiktok.com/@" . $username;
                                        break;
                                        
                                    case 'facebook':
                                        $iconHtml = '<i class="fa-brands fa-facebook-f text-base text-blue-600"></i>';
                                        $colorClass = 'bg-blue-50 text-blue-700 hover:bg-blue-100/80 border border-blue-100';
                                        $label = 'Facebook';
                                        
                                        if (!str_starts_with($value, 'http')) {
                                            $url = "https://facebook.com/" . ltrim($value, '/');
                                        }
                                        break;
                                        
                                    case 'youtube':
                                        $iconHtml = '<i class="fa-brands fa-youtube text-base text-red-600"></i>';
                                        $colorClass = 'bg-red-50 text-red-700 hover:bg-red-100/80 border border-red-100';
                                        $label = 'YouTube';
                                        
                                        if (!str_starts_with($value, 'http')) {
                                            $url = "https://youtube.com/" . ltrim($value, '/');
                                        }
                                        break;
                                        
                                    case 'phone':
                                        $iconHtml = '<i class="fa-solid fa-phone text-sm text-teal-600"></i>';
                                        $colorClass = 'bg-teal-50 text-teal-700 hover:bg-teal-100/80 border border-teal-100';
                                        $label = 'Telepon/HP';
                                        
                                        $url = "tel:" . preg_replace('/[^0-9+]/', '', $value);
                                        break;
                                        
                                    case 'email':
                                        $iconHtml = '<i class="fa-solid fa-envelope text-sm text-slate-600"></i>';
                                        $colorClass = 'bg-slate-50 text-slate-700 hover:bg-slate-100/80 border border-slate-100';
                                        $label = 'Email';
                                        
                                        $url = "mailto:" . $value;
                                        break;
                                        
                                    case 'website':
                                        $iconHtml = '<i class="fa-solid fa-globe text-sm text-indigo-600"></i>';
                                        $colorClass = 'bg-indigo-50 text-indigo-700 hover:bg-indigo-100/80 border border-indigo-100';
                                        $label = 'Website';
                                        
                                        if (!str_starts_with($value, 'http')) {
                                            $url = "https://" . $value;
                                        }
                                        break;
                                }
                            @endphp
                            
                            <a href="{{ $url }}" target="_blank" class="flex items-center justify-between p-3.5 rounded-2xl {{ $colorClass }} transition-all duration-300 group shadow-sm border border-transparent">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-xl bg-white/80 flex items-center justify-center shrink-0 shadow-sm border border-black/5">
                                        {!! $iconHtml !!}
                                    </div>
                                    <div class="min-w-0">
                                        <div class="text-[10px] uppercase font-bold tracking-wider opacity-60">{{ $label }}</div>
                                        <div class="text-sm font-semibold truncate max-w-[160px]">{{ $value }}</div>
                                    </div>
                                </div>
                                <div class="w-7 h-7 rounded-lg bg-white/60 flex items-center justify-center text-current opacity-0 group-hover:opacity-100 transition-all duration-300 transform translate-x-1 group-hover:translate-x-0">
                                    <i data-lucide="arrow-up-right" class="w-4 h-4"></i>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
                @endif

                {{-- Related --}}
                @if($relatedDestinations->count() > 0)
                <div class="bg-white rounded-3xl p-6 shadow-sm border border-forest-100">
                    <h3 class="font-bold text-forest-950 text-lg mb-6 pb-4 border-b border-forest-50">Destinasi Lainnya</h3>
                    
                    <div class="space-y-4">
                        @foreach($relatedDestinations as $related)
                        <a href="{{ route('destination.detail', $related->slug) }}" class="flex items-center gap-4 group">
                            <img src="{{ $related->image_url }}" alt="{{ $related->name }}" class="w-16 h-16 rounded-xl object-cover">
                            <div>
                                <div class="font-bold text-forest-900 group-hover:text-forest-600 transition-colors line-clamp-1 mb-1">{{ $related->name }}</div>
                                <div class="text-xs text-forest-500"><i data-lucide="clock" class="w-3 h-3 inline mr-1"></i>{{ $related->operational_hours ?? '-' }} • Rp {{ number_format($related->price, 0, ',', '.') }}</div>
                            </div>
                        </a>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
