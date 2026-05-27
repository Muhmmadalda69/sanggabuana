@extends('layouts.app')

@section('content')

{{-- Hero Section --}}
<section id="beranda" class="relative w-full min-h-screen flex items-center py-20 overflow-hidden">
    {{-- Background Image & Gradient --}}
    <div class="absolute inset-0 z-0">
        <img src="{{ App\Models\Setting::get('hero_background', 'https://images.unsplash.com/photo-1542224566-6e85f2e6772f?q=80&w=2000&auto=format&fit=crop') }}" alt="Gunung Sanggabuana" class="w-full h-full object-cover animate-float-slow transform scale-105">
        <div class="absolute inset-0 hero-gradient"></div>
    </div>

    {{-- Decorational Elements --}}
    <div class="absolute inset-0 z-0 overflow-hidden pointer-events-none">
        <div class="absolute top-1/4 left-10 w-32 h-32 bg-forest-400/20 rounded-full blur-3xl"></div>
        <div class="absolute bottom-1/4 right-10 w-48 h-48 bg-forest-300/20 rounded-full blur-3xl"></div>
    </div>

    <div class="relative z-10 w-full px-4 sm:px-6 lg:px-8">
        <div class="grid lg:grid-cols-2 gap-12 items-center">
            <div class="scroll-animate text-center lg:text-left">
                <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full glass-card text-forest-100 text-sm font-medium mb-6">
                    <span class="w-2 h-2 rounded-full bg-forest-400 animate-pulse"></span>
                    Wisata Alam Terbaik Karawang
                </div>
                <h1 class="text-4xl md:text-6xl font-bold text-white leading-tight mb-6 tracking-tight">
                    {{ App\Models\Setting::get('hero_title', 'Jelajahi Keajaiban Alam Gunung Sanggabuana') }}
                </h1>
                <p class="text-lg md:text-xl text-forest-100/90 mb-10 max-w-2xl mx-auto lg:mx-0 leading-relaxed">
                    {{ App\Models\Setting::get('hero_subtitle', 'Temukan keindahan tersembunyi di jantung Karawang. Pendakian menakjubkan, air terjun spektakuler, dan pengalaman alam yang tak terlupakan menanti Anda.') }}
                </p>
                
                <div class="flex flex-col sm:flex-row items-center justify-center lg:justify-start gap-4">
                    <a href="#destinasi" class="w-full sm:w-auto px-8 py-4 bg-white text-forest-900 font-bold rounded-xl hover:bg-forest-50 transition-all shadow-xl hover:shadow-2xl hover:-translate-y-1 text-center flex items-center justify-center gap-2 group">
                        Jelajahi Sekarang
                        <i data-lucide="arrow-right" class="w-5 h-5 group-hover:translate-x-1 transition-transform"></i>
                    </a>
                    <a href="#tentang" class="w-full sm:w-auto px-8 py-4 glass-card text-white font-bold rounded-xl hover:bg-white/20 transition-all text-center flex items-center justify-center gap-2">
                        <i data-lucide="play-circle" class="w-5 h-5"></i>
                        Tonton Video
                    </a>
                </div>

                {{-- Stats --}}
                <div class="grid grid-cols-3 gap-6 mt-16 pt-8 border-t border-white/10">
                    <div>
                        <div class="text-3xl font-bold text-white mb-1"><span class="counter" data-target="{{ App\Models\Destination::count() }}">0</span>+</div>
                        <div class="text-forest-200 text-sm">Destinasi Alam</div>
                    </div>
                    <div>
                        <div class="text-3xl font-bold text-white mb-1"><span class="counter" data-target="1291">0</span></div>
                        <div class="text-forest-200 text-sm">MDPL Ketinggian</div>
                    </div>
                    <div>
                        <div class="text-3xl font-bold text-white mb-1"><span class="counter" data-target="{{ $annualVisitors ?? 0 }}">0</span>+</div>
                        <div class="text-forest-200 text-sm">Pengunjung/Tahun</div>
                    </div>
                </div>
            </div>

            {{-- Image Card on Hero --}}
            <div class="hidden lg:block relative scroll-animate stagger-2">
                <div class="absolute inset-0 bg-gradient-to-tr from-forest-500/30 to-transparent rounded-[2rem] transform translate-x-6 translate-y-6"></div>
                <img src="{{ App\Models\Setting::get('hero_image', 'https://images.unsplash.com/photo-1542224566-6e85f2e6772f?q=80&w=800&auto=format&fit=crop') }}" alt="Pemandangan Sanggabuana" class="relative z-10 w-full h-[600px] object-cover rounded-[2rem] shadow-2xl border-4 border-white/10 animate-float">
                
                {{-- Floating Badge --}}
                <div class="absolute top-10 -left-10 z-20 glass-card-light rounded-2xl p-4 shadow-xl flex items-center gap-4 animate-float-delayed" id="weather-badge">
                    <div class="w-12 h-12 rounded-full bg-forest-100 flex items-center justify-center text-forest-600" id="weather-icon-container">
                        <i data-lucide="sun" class="w-6 h-6" id="weather-icon"></i>
                    </div>
                    <div>
                        <div class="font-bold text-forest-950 flex items-center gap-1.5" id="weather-title-container">
                            <span id="weather-text">Cuaca Cerah</span>
                            <span id="weather-temp" class="text-xs font-semibold px-2 py-0.5 bg-forest-100 text-forest-700 rounded-full">26°C</span>
                        </div>
                        <div class="text-sm text-forest-600" id="weather-desc">Cocok untuk pendakian</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

@php
    $videoType = App\Models\Setting::get('about_video_type', 'link');
    $videoUrl = $videoType === 'upload' 
        ? App\Models\Setting::get('about_video_file') 
        : App\Models\Setting::get('about_video_link', 'https://www.youtube.com/watch?v=dQw4w9WgXcQ');
        
    // Parse YouTube and Google Drive URLs for embed compatibility
    $isYoutube = false;
    $isGoogleDrive = false;
    $embedUrl = $videoUrl;
    
    if ($videoUrl) {
        if (preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/ ]{11})/i', $videoUrl, $match)) {
            $isYoutube = true;
            $videoId = $match[1];
            $embedUrl = "https://www.youtube.com/embed/" . $videoId . "?autoplay=1&rel=0";
        } elseif (preg_match('/drive\.google\.com\/(?:file\/d\/|open\?id=)([^&?\/ ]+)/i', $videoUrl, $match)) {
            $isGoogleDrive = true;
            $driveId = $match[1];
            $embedUrl = "https://drive.google.com/file/d/" . $driveId . "/preview";
        }
    }

    // Decode About Features JSON list
    $aboutFeatures = json_decode(App\Models\Setting::get('about_features', '[]'), true) ?: [];
    if (empty($aboutFeatures)) {
        $aboutFeatures = [
            ['title' => 'Aman & Nyaman', 'desc' => 'Jalur terkelola baik'],
            ['title' => 'Spot Foto', 'desc' => 'Pemandangan indah']
        ];
    }
@endphp

{{-- Tentang Section --}}
<section id="tentang" class="py-24 bg-forest-50 relative overflow-hidden">
    <div class="w-full px-4 sm:px-6 lg:px-8">
        <div class="grid md:grid-cols-2 gap-16 items-center">
            <div class="relative scroll-animate w-full rounded-[2rem] overflow-hidden shadow-2xl bg-forest-950 border-4 border-white/80 group" style="height: 380px;">
                {{-- Cover Poster Image --}}
                <div id="video-cover" class="absolute inset-0 z-20 transition-all duration-500 ease-in-out">
                    @if($isYoutube)
                        <img src="https://img.youtube.com/vi/{{ $videoId }}/maxresdefault.jpg" onerror="this.src='https://img.youtube.com/vi/{{ $videoId }}/hqdefault.jpg'" alt="Video Cover" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700">
                    @elseif($isGoogleDrive)
                        <img src="https://images.unsplash.com/photo-1433086966358-54859d0ed716?q=80&w=1200&auto=format&fit=crop" alt="Video Cover" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700">
                    @else
                        <video src="{{ $videoUrl }}" preload="metadata" muted playsinline class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700"></video>
                    @endif
                    <div class="absolute inset-0 bg-gradient-to-t from-forest-950/40 via-transparent to-transparent"></div>
                    
                    {{-- Pulsing Play Button --}}
                    <button id="play-inline-video" class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-20 h-20 bg-white rounded-full flex items-center justify-center shadow-2xl animate-pulse hover:scale-110 active:scale-95 transition-transform duration-300 z-30 cursor-pointer">
                        <div class="w-14 h-14 rounded-full bg-forest-600 flex items-center justify-center text-white">
                            <i data-lucide="play" class="w-5 h-5 ml-1"></i>
                        </div>
                    </button>
                </div>

                {{-- The Video Element (starts hidden) --}}
                <div id="video-player-container" class="absolute inset-0 z-10 hidden w-full h-full">
                    {{-- Iframe for YouTube / Google Drive --}}
                    <iframe id="inline-iframe" class="w-full h-full hidden" frameborder="0" allow="autoplay; encrypted-media; picture-in-picture" allowfullscreen></iframe>
                    
                    {{-- Native HTML5 Video Player --}}
                    <video id="inline-html5" class="w-full h-full object-contain hidden" controls></video>
                    
                    {{-- Mini Close/Stop Button Overlay --}}
                    <button id="stop-inline-video" class="absolute top-4 right-4 z-40 w-8 h-8 rounded-full bg-black/60 hover:bg-black/80 flex items-center justify-center text-white transition-all border border-white/10 hover:scale-105 active:scale-95">
                        <i data-lucide="x" class="w-4 h-4"></i>
                    </button>
                </div>
            </div>
            
            <div class="scroll-animate stagger-2">
                <div class="text-forest-600 font-bold uppercase tracking-wider text-sm mb-2 flex items-center gap-2">
                    <i data-lucide="leaf" class="w-4 h-4"></i> Tentang Kami
                </div>
                <h2 class="text-3xl md:text-4xl font-bold text-forest-950 mb-6">Menjaga Keasrian, Menyajikan Keindahan</h2>
                <div class="section-title-line mb-8"></div>
                <div class="prose prose-forest text-forest-700 mb-8">
                    {!! App\Models\Setting::get('about_text', '<p>Gunung Sanggabuana merupakan kawasan wisata alam yang terletak di Kabupaten Karawang. Kami berkomitmen menjaga kelestarian alam sambil memberikan pengalaman wisata terbaik.</p>') !!}
                </div>
                
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 mb-8">
                    @foreach($aboutFeatures as $index => $feature)
                        <div class="flex items-start gap-3">
                            <div class="w-10 h-10 rounded-lg bg-forest-100 flex items-center justify-center text-forest-600 shrink-0">
                                <i data-lucide="{{ $feature['icon'] ?? ($index % 2 === 0 ? 'shield-check' : 'camera') }}" class="w-5 h-5"></i>
                            </div>
                            <div>
                                <div class="font-bold text-forest-950">{{ $feature['title'] }}</div>
                                <div class="text-sm text-forest-600">{{ $feature['desc'] }}</div>
                            </div>
                        </div>
                    @endforeach
                </div>
                
                <a href="{{ route('page.show', 'tentang-kami') }}" class="inline-flex items-center gap-2 font-semibold text-forest-600 hover:text-forest-800 transition-colors">
                    Baca Selengkapnya
                    <i data-lucide="arrow-right" class="w-4 h-4"></i>
                </a>
            </div>
        </div>
    </div>
</section>

{{-- Featured Destinations --}}
<section id="destinasi" class="py-24 bg-white relative">
    <div class="w-full px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-3xl mx-auto mb-16 scroll-animate">
            <div class="text-forest-600 font-bold uppercase tracking-wider text-sm mb-2 flex items-center justify-center gap-2">
                <i data-lucide="map" class="w-4 h-4"></i> Destinasi Unggulan
            </div>
            <h2 class="text-3xl md:text-5xl font-bold text-forest-950 mb-6">Eksplorasi Keajaiban Sanggabuana</h2>
            <div class="section-title-line mx-auto mb-6"></div>
            <p class="text-forest-600 text-lg">Pilih petualangan Anda dari berbagai destinasi menakjubkan yang kami tawarkan, mulai dari puncak gunung hingga air terjun tersembunyi.</p>
        </div>

        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
            @foreach($featuredDestinations as $index => $dest)
            <div class="destination-card bg-white rounded-3xl overflow-hidden shadow-xl border border-forest-50 scroll-animate stagger-{{ ($index % 3) + 1 }}">
                <div class="relative h-64 overflow-hidden">
                    <img src="{{ $dest->image_url }}" alt="{{ $dest->name }}" class="w-full h-full object-cover destination-image">
                    <div class="absolute top-4 right-4 bg-white/90 backdrop-blur px-3 py-1 rounded-full text-xs font-bold text-forest-800 flex items-center gap-1 shadow-lg">
                        <i data-lucide="trending-up" class="w-3 h-3"></i> Populer
                    </div>
                    <div class="absolute bottom-0 left-0 w-full h-1/2 bg-gradient-to-t from-forest-950/80 to-transparent"></div>
                    <div class="absolute bottom-4 left-4 text-white">
                        <div class="flex items-center gap-2 text-sm font-medium">
                            <i data-lucide="map-pin" class="w-4 h-4"></i>
                            {{ $dest->altitude }}
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
                    <div class="flex items-center justify-between pt-4 border-t border-forest-50">
                        <div class="text-forest-900 font-bold">
                            Rp {{ number_format($dest->price, 0, ',', '.') }}<span class="text-xs text-forest-500 font-normal">/orang</span>
                        </div>
                        <a href="{{ route('destination.detail', $dest->slug) }}" class="w-10 h-10 rounded-full bg-forest-50 hover:bg-forest-600 hover:text-white flex items-center justify-center text-forest-600 transition-colors">
                            <i data-lucide="arrow-right" class="w-5 h-5"></i>
                        </a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <div class="text-center mt-12 scroll-animate stagger-4">
            <a href="{{ route('destinations') }}" class="inline-flex items-center gap-2 px-8 py-4 bg-forest-50 text-forest-700 font-bold rounded-xl hover:bg-forest-100 transition-colors border border-forest-100">
                Lihat Semua Destinasi
                <i data-lucide="arrow-right" class="w-5 h-5"></i>
            </a>
        </div>
    </div>
</section>

{{-- Banner Ajakan --}}
<section class="py-20 relative bg-forest-950 overflow-hidden">
    <div class="absolute inset-0 opacity-20">
        <img src="https://images.unsplash.com/photo-1448375240586-882707db888b?q=80&w=2000&auto=format&fit=crop" class="w-full h-full object-cover">
    </div>
    <div class="relative w-full px-4 text-center scroll-animate">
        <h2 class="text-3xl md:text-5xl font-bold text-white mb-6">Siap Memulai Petualangan Anda?</h2>
        <p class="text-forest-200 text-lg md:text-xl mb-10 max-w-2xl mx-auto">Kami siap membantu Anda merencanakan perjalanan tak terlupakan ke Sanggabuana. Hubungi tim kami untuk info lebih lanjut.</p>
        <a href="#kontak" class="inline-block px-10 py-4 bg-white text-forest-900 font-bold rounded-xl hover:bg-forest-50 transition-all shadow-xl hover:-translate-y-1">
            Hubungi Tim Kami
        </a>
    </div>
</section>

{{-- Gallery Section --}}
<section id="galeri" class="py-24 bg-forest-50">
    <div class="w-full px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-3xl mx-auto mb-16 scroll-animate">
            <div class="text-forest-600 font-bold uppercase tracking-wider text-sm mb-2 flex items-center justify-center gap-2">
                <i data-lucide="image" class="w-4 h-4"></i> Galeri
            </div>
            <h2 class="text-3xl md:text-5xl font-bold text-forest-950 mb-6">Potret Keindahan Alam</h2>
            <div class="section-title-line mx-auto mb-6"></div>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            @foreach($galleries as $index => $gallery)
            <div class="gallery-item relative overflow-hidden rounded-2xl aspect-square scroll-animate stagger-{{ ($index % 4) + 1 }}">
                <img src="{{ $gallery->image_url }}" alt="{{ $gallery->title }}" class="w-full h-full object-cover">
                <div class="gallery-overlay absolute inset-0 bg-gradient-to-t from-forest-950/90 via-forest-950/40 to-transparent flex flex-col justify-end p-6">
                    <h4 class="text-white font-bold text-lg mb-1">{{ $gallery->title }}</h4>
                    <p class="text-forest-200 text-sm">{{ $gallery->caption }}</p>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- Testimonials --}}
<section id="testimoni" class="py-24 bg-white">
    <div class="w-full px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-3xl mx-auto mb-16 scroll-animate">
            <div class="text-forest-600 font-bold uppercase tracking-wider text-sm mb-2 flex items-center justify-center gap-2">
                <i data-lucide="star" class="w-4 h-4"></i> Ulasan Pengunjung
            </div>
            <h2 class="text-3xl md:text-5xl font-bold text-forest-950 mb-6">Apa Kata Mereka?</h2>
            <div class="section-title-line mx-auto mb-6"></div>
            <p class="text-forest-600 text-lg">Suara dan pengalaman langsung dari para pengunjung Sanggabuana yang telah menikmati keindahan alam kami.</p>
        </div>

        <div class="grid md:grid-cols-3 gap-8 mb-16">
            @foreach($testimonials as $index => $testi)
            <div class="testimonial-card bg-forest-50 p-8 rounded-3xl relative scroll-animate stagger-{{ ($index % 3) + 1 }}">
                <div class="absolute -top-6 right-8 text-forest-200">
                    <svg width="60" height="60" viewBox="0 0 24 24" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                        <path d="M14.017 21L16.41 14.425C16.5913 13.9427 16.6841 13.4332 16.684 12.92V7H21.05V13.884L18.497 21H14.017ZM4 21L6.393 14.425C6.57434 13.9427 6.66714 13.4332 6.667 12.92V7H11.033V13.884L8.48 21H4Z"/>
                    </svg>
                </div>
                <div class="flex items-center gap-1 text-yellow-400 mb-6">
                    @for($i = 0; $i < $testi->rating; $i++)
                    <i data-lucide="star" class="w-5 h-5 fill-current"></i>
                    @endfor
                </div>
                <p class="text-forest-700 text-lg mb-8 italic">"{{ $testi->message }}"</p>
                <div class="flex items-center gap-4">
                    <img src="{{ $testi->avatar_url }}" alt="{{ $testi->name }}" class="w-12 h-12 rounded-full object-cover">
                    <div>
                        <h4 class="font-bold text-forest-950">{{ $testi->name }}</h4>
                        <div class="text-sm text-forest-500">{{ $testi->role }}</div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <div class="mt-20 bg-forest-950 rounded-3xl p-8 sm:p-12 text-white relative overflow-hidden scroll-animate">
            <div class="absolute top-0 right-0 w-64 h-64 bg-forest-800 rounded-full blur-3xl opacity-50 -translate-y-1/2 translate-x-1/2"></div>
            
            <div class="relative z-10 grid md:grid-cols-2 gap-12 items-center">
                <div>
                    <h3 class="text-3xl font-bold mb-4">Punya Pengalaman Menarik?</h3>
                    <p class="text-forest-200 mb-8">Bagikan momen dan pengalaman tak terlupakan Anda selama mengunjungi wisata alam Sanggabuana. Ulasan Anda sangat berarti bagi kami dan pengunjung lainnya.</p>
                    
                    @if(session('success') && session('success') == 'Terima kasih! Ulasan Anda telah berhasil dikirim dan menunggu persetujuan (kurasi) dari admin.')
                    <div class="bg-green-500/20 border border-green-500 text-green-100 px-4 py-3 rounded-xl flex items-start gap-3 mb-6">
                        <i data-lucide="check-circle" class="w-5 h-5 shrink-0 mt-0.5"></i>
                        <div>{{ session('success') }}</div>
                    </div>
                    @endif
                    
                    @if(session('error') && session('error') == 'Gagal mengirim ulasan, mohon cek kembali inputan Anda.')
                    <div class="bg-red-500/20 border border-red-500 text-red-100 px-4 py-3 rounded-xl flex items-start gap-3 mb-6">
                        <i data-lucide="alert-circle" class="w-5 h-5 shrink-0 mt-0.5"></i>
                        <div>{{ session('error') }}</div>
                    </div>
                    @endif
                </div>
                
                <div class="glass-card rounded-2xl p-6 sm:p-8 border border-white/10">
                    <form action="{{ route('testimonials.store') }}" method="POST">
                        @csrf
                        <div class="space-y-4">
                            <div>
                                <label class="block text-forest-200 text-sm font-medium mb-2">Nama Anda</label>
                                <input type="text" name="name" required class="w-full bg-forest-900/50 border border-forest-700 text-white rounded-xl px-4 py-3 focus:outline-none focus:border-forest-400 focus:ring-1 focus:ring-forest-400 transition-colors" placeholder="Masukkan nama Anda">
                            </div>
                            <div>
                                <label class="block text-forest-200 text-sm font-medium mb-2">Peran / Profesi <span class="text-xs text-forest-400">(Opsional)</span></label>
                                <input type="text" name="role" class="w-full bg-forest-900/50 border border-forest-700 text-white rounded-xl px-4 py-3 focus:outline-none focus:border-forest-400 focus:ring-1 focus:ring-forest-400 transition-colors" placeholder="Pendaki, Vloger, dll">
                            </div>
                            <div>
                                <label class="block text-forest-200 text-sm font-medium mb-2">Bintang (1-5)</label>
                                <select name="rating" required class="w-full bg-forest-900/50 border border-forest-700 text-white rounded-xl px-4 py-3 focus:outline-none focus:border-forest-400 focus:ring-1 focus:ring-forest-400 transition-colors appearance-none">
                                    <option value="5" class="text-gray-900">5 Bintang - Sangat Baik</option>
                                    <option value="4" class="text-gray-900">4 Bintang - Baik</option>
                                    <option value="3" class="text-gray-900">3 Bintang - Cukup</option>
                                    <option value="2" class="text-gray-900">2 Bintang - Kurang</option>
                                    <option value="1" class="text-gray-900">1 Bintang - Sangat Kurang</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-forest-200 text-sm font-medium mb-2">Isi Ulasan</label>
                                <textarea name="message" rows="3" required class="w-full bg-forest-900/50 border border-forest-700 text-white rounded-xl px-4 py-3 focus:outline-none focus:border-forest-400 focus:ring-1 focus:ring-forest-400 transition-colors" placeholder="Ceritakan pengalaman Anda..."></textarea>
                            </div>
                            <button type="submit" class="w-full py-4 bg-gradient-to-r from-forest-400 to-forest-600 text-white font-bold rounded-xl hover:from-forest-500 hover:to-forest-700 transition-all flex items-center justify-center gap-2 btn-glow mt-4">
                                Kirim Ulasan
                                <i data-lucide="send" class="w-5 h-5"></i>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- Contact Section --}}
<section id="kontak" class="py-24 bg-forest-950 relative overflow-hidden">
    <div class="absolute top-0 right-0 w-1/2 h-full opacity-10 pointer-events-none">
        <svg viewBox="0 0 200 200" xmlns="http://www.w3.org/2000/svg" class="w-full h-full animate-spin" style="animation-duration: 40s;">
            <path fill="#ffffff" d="M44.7,-76.4C58.8,-69.2,71.8,-59.1,81.6,-46.3C91.4,-33.5,98,-18.1,97.7,-2.9C97.4,12.3,90.2,27.3,80.4,40.1C70.6,52.9,58.2,63.5,44.1,70.1C30,76.7,15,79.3,0.3,78.8C-14.4,78.3,-28.8,74.7,-42.6,67.8C-56.4,60.9,-69.6,50.7,-78.9,37.8C-88.2,24.9,-93.6,9.3,-92.4,-5.9C-91.2,-21.1,-83.4,-35.9,-73.2,-48.1C63,-60.3,-50.4,-69.9,-36.8,-77.3C-23.2,-84.7,-8.6,-89.9,3.5,-85.9C15.6,-81.9,30.6,-83.6,44.7,-76.4Z" transform="translate(100 100)" />
        </svg>
    </div>

    <div class="w-full px-4 sm:px-6 lg:px-8 relative z-10">
        <div class="grid lg:grid-cols-2 gap-16">
            <div class="scroll-animate text-white">
                <div class="text-forest-400 font-bold uppercase tracking-wider text-sm mb-2 flex items-center gap-2">
                    <i data-lucide="mail" class="w-4 h-4"></i> Hubungi Kami
                </div>
                <h2 class="text-3xl md:text-5xl font-bold mb-6">Punya Pertanyaan?</h2>
                <div class="w-20 h-2 bg-forest-500 rounded-full mb-8"></div>
                <p class="text-forest-200 text-lg mb-10">Jangan ragu untuk menghubungi kami jika Anda memiliki pertanyaan tentang wisata, reservasi, atau informasi lainnya seputar Sanggabuana.</p>

                <div class="space-y-6">
                    <div class="flex items-start gap-4">
                        <div class="w-12 h-12 rounded-xl bg-forest-800/50 flex items-center justify-center shrink-0">
                            <i data-lucide="map-pin" class="w-6 h-6 text-forest-400"></i>
                        </div>
                        <div>
                            <h4 class="font-bold text-lg mb-1">Lokasi Kami</h4>
                            <p class="text-forest-300">{{ App\Models\Setting::get('contact_address', 'Sanggabuana, Karawang') }}</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-4">
                        <div class="w-12 h-12 rounded-xl bg-forest-800/50 flex items-center justify-center shrink-0">
                            <i data-lucide="phone" class="w-6 h-6 text-forest-400"></i>
                        </div>
                        <div>
                            <h4 class="font-bold text-lg mb-1">Telepon</h4>
                            <p class="text-forest-300">{{ App\Models\Setting::get('contact_phone') }}</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-4">
                        <div class="w-12 h-12 rounded-xl bg-forest-800/50 flex items-center justify-center shrink-0">
                            <i data-lucide="mail" class="w-6 h-6 text-forest-400"></i>
                        </div>
                        <div>
                            <h4 class="font-bold text-lg mb-1">Email</h4>
                            <p class="text-forest-300">{{ App\Models\Setting::get('contact_email') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="scroll-animate stagger-2">
                <div class="glass-card rounded-3xl p-8 sm:p-10 border border-white/10">
                    <h3 class="text-2xl font-bold text-white mb-6">Kirim Pesan</h3>
                    
                    @if(session('success'))
                    <div class="bg-green-500/20 border border-green-500 text-green-100 px-4 py-3 rounded-xl mb-6 flex items-start gap-3">
                        <i data-lucide="check-circle" class="w-5 h-5 shrink-0 mt-0.5"></i>
                        <div>{{ session('success') }}</div>
                    </div>
                    @endif

                    <form action="{{ route('contact.store') }}" method="POST">
                        @csrf
                        <div class="grid grid-cols-2 gap-6 mb-6">
                            <div class="col-span-2 sm:col-span-1">
                                <label class="block text-forest-200 text-sm font-medium mb-2">Nama Lengkap</label>
                                <input type="text" name="name" required class="w-full bg-forest-900/50 border border-forest-700 text-white rounded-xl px-4 py-3 focus:outline-none focus:border-forest-400 focus:ring-1 focus:ring-forest-400 transition-colors" placeholder="Masukkan nama Anda">
                            </div>
                            <div class="col-span-2 sm:col-span-1">
                                <label class="block text-forest-200 text-sm font-medium mb-2">Email</label>
                                <input type="email" name="email" required class="w-full bg-forest-900/50 border border-forest-700 text-white rounded-xl px-4 py-3 focus:outline-none focus:border-forest-400 focus:ring-1 focus:ring-forest-400 transition-colors" placeholder="email@contoh.com">
                            </div>
                            <div class="col-span-2 sm:col-span-1">
                                <label class="block text-forest-200 text-sm font-medium mb-2">Nomor Telepon</label>
                                <input type="text" name="phone" class="w-full bg-forest-900/50 border border-forest-700 text-white rounded-xl px-4 py-3 focus:outline-none focus:border-forest-400 focus:ring-1 focus:ring-forest-400 transition-colors" placeholder="Opsional">
                            </div>
                            <div class="col-span-2 sm:col-span-1">
                                <label class="block text-forest-200 text-sm font-medium mb-2">Subjek</label>
                                <input type="text" name="subject" required class="w-full bg-forest-900/50 border border-forest-700 text-white rounded-xl px-4 py-3 focus:outline-none focus:border-forest-400 focus:ring-1 focus:ring-forest-400 transition-colors" placeholder="Tujuan pesan">
                            </div>
                            <div class="col-span-2">
                                <label class="block text-forest-200 text-sm font-medium mb-2">Pesan</label>
                                <textarea name="message" rows="4" required class="w-full bg-forest-900/50 border border-forest-700 text-white rounded-xl px-4 py-3 focus:outline-none focus:border-forest-400 focus:ring-1 focus:ring-forest-400 transition-colors" placeholder="Tuliskan pesan Anda di sini..."></textarea>
                            </div>
                        </div>
                        <button type="submit" class="w-full py-4 bg-gradient-to-r from-forest-400 to-forest-600 text-white font-bold rounded-xl hover:from-forest-500 hover:to-forest-700 transition-all flex items-center justify-center gap-2 btn-glow">
                            Kirim Pesan
                            <i data-lucide="send" class="w-5 h-5"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const playInlineBtn = document.getElementById('play-inline-video');
        const stopInlineBtn = document.getElementById('stop-inline-video');
        const videoCover = document.getElementById('video-cover');
        const videoPlayerContainer = document.getElementById('video-player-container');
        const inlineIframe = document.getElementById('inline-iframe');
        const inlineHtml5 = document.getElementById('inline-html5');
        
        const isEmbed = {{ ($isYoutube || $isGoogleDrive) ? 'true' : 'false' }};
        const videoSrc = "{{ $embedUrl }}";
        const directSrc = "{{ $videoUrl }}";

        if (playInlineBtn && videoPlayerContainer) {
            playInlineBtn.addEventListener('click', () => {
                // Fade out cover
                videoCover.classList.add('opacity-0', 'pointer-events-none');
                
                // Show player container
                videoPlayerContainer.classList.remove('hidden');

                if (isEmbed) {
                    inlineIframe.src = videoSrc;
                    inlineIframe.classList.remove('hidden');
                    inlineHtml5.classList.add('hidden');
                } else if (directSrc) {
                    inlineHtml5.src = directSrc;
                    inlineHtml5.classList.remove('hidden');
                    inlineIframe.classList.add('hidden');
                    inlineHtml5.load();
                    inlineHtml5.play();
                }
            });

            if (stopInlineBtn) {
                stopInlineBtn.addEventListener('click', () => {
                    // Hide player container
                    videoPlayerContainer.classList.add('hidden');
                    
                    // Stop video
                    inlineIframe.removeAttribute('src');
                    inlineHtml5.pause();
                    inlineHtml5.removeAttribute('src');
                    
                    // Fade in cover
                    videoCover.classList.remove('opacity-0', 'pointer-events-none');
                });
            }

            const weatherMode = '{{ App\Models\Setting::get('weather_mode', 'auto') }}';
            
            if (weatherMode === 'manual') {
                // Manual override! Directly set the override values and skip the weather fetch.
                const tempEl = document.getElementById('weather-temp');
                const textEl = document.getElementById('weather-text');
                const descEl = document.getElementById('weather-desc');
                const iconEl = document.getElementById('weather-icon');
                
                if (tempEl) tempEl.classList.add('hidden'); // Hide the temp badge in emergency manual override
                if (textEl) textEl.innerText = '{{ App\Models\Setting::get('weather_manual_status', 'Jalur Ditutup') }}';
                if (descEl) descEl.innerText = '{{ App\Models\Setting::get('weather_manual_desc', 'Ditutup sementara untuk pemulihan ekosistem hutan') }}';
                
                const iconName = '{{ App\Models\Setting::get('weather_manual_icon', 'alert-triangle') }}';
                if (iconEl) {
                    iconEl.setAttribute('data-lucide', iconName);
                    lucide.createIcons();
                }
                
                // Align manual styling exactly with the beautiful white automatic card layout!
                const badgeEl = document.getElementById('weather-badge');
                if (badgeEl) {
                    badgeEl.className = 'absolute top-10 -left-10 z-20 glass-card-light rounded-2xl p-4 shadow-xl flex items-center gap-4 animate-float-delayed';
                }
                const iconContainerEl = document.getElementById('weather-icon-container');
                if (iconContainerEl) {
                    if (['alert-triangle', 'x-circle'].includes(iconName)) {
                        iconContainerEl.className = 'w-12 h-12 rounded-full bg-red-50 flex items-center justify-center text-red-600 border border-red-100';
                    } else if (iconName === 'info') {
                        iconContainerEl.className = 'w-12 h-12 rounded-full bg-blue-50 flex items-center justify-center text-blue-600 border border-blue-100';
                    } else {
                        iconContainerEl.className = 'w-12 h-12 rounded-full bg-forest-100 flex items-center justify-center text-forest-600';
                    }
                }
                const titleContainerEl = document.getElementById('weather-title-container');
                if (titleContainerEl) {
                    titleContainerEl.className = 'font-bold text-forest-950 flex items-center gap-1.5';
                }
                if (descEl) {
                    descEl.className = 'text-sm text-forest-600';
                }
            } else {
                // Auto mode! Fetch live weather using dynamic coordinates
                const lat = '{{ App\Models\Setting::get('weather_latitude', '-6.505') }}';
                const lon = '{{ App\Models\Setting::get('weather_longitude', '107.218') }}';
                
                fetch(`https://api.open-meteo.com/v1/forecast?latitude=${lat}&longitude=${lon}&current=temperature_2m,weather_code`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.current) {
                            const temp = Math.round(data.current.temperature_2m);
                            const code = data.current.weather_code;
                            
                            let text = 'Cuaca Cerah';
                            let desc = 'Cocok untuk pendakian';
                            let icon = 'sun';
                            
                            if (code === 0) {
                                text = 'Cuaca Cerah';
                                desc = 'Cocok untuk pendakian';
                                icon = 'sun';
                            } else if ([1, 2, 3].includes(code)) {
                                text = 'Cerah Berawan';
                                desc = 'Nyaman untuk mendaki';
                                icon = 'cloud-sun';
                            } else if ([45, 48].includes(code)) {
                                text = 'Kabut Gunung';
                                desc = 'Gunakan jaket tebal';
                                icon = 'wind';
                            } else if ([51, 53, 55, 56, 57].includes(code)) {
                                text = 'Gerimis Ringan';
                                desc = 'Sedia jas hujan';
                                icon = 'cloud-drizzle';
                            } else if ([61, 63, 65, 66, 67, 80, 81, 82].includes(code)) {
                                text = 'Hujan Basah';
                                desc = 'Siapkan jas hujan & berhati-hati';
                                icon = 'cloud-rain';
                            } else if ([71, 73, 75, 77, 85, 86].includes(code)) {
                                text = 'Suhu Dingin';
                                desc = 'Gunakan pakaian hangat';
                                icon = 'snowflake';
                            } else if ([95, 96, 99].includes(code)) {
                                text = 'Hujan Petir';
                                desc = 'Tunda pendakian sementara';
                                icon = 'cloud-lightning';
                            }
                            
                            // Update DOM elements
                            const tempEl = document.getElementById('weather-temp');
                            const textEl = document.getElementById('weather-text');
                            const descEl = document.getElementById('weather-desc');
                            const iconEl = document.getElementById('weather-icon');
                            
                            if (tempEl) tempEl.innerText = `${temp}°C`;
                            if (textEl) textEl.innerText = text;
                            if (descEl) descEl.innerText = desc;
                            
                            if (iconEl) {
                                iconEl.setAttribute('data-lucide', icon);
                                lucide.createIcons(); // Re-render lucide icon
                            }
                        }
                    })
                    .catch(err => console.error('Error fetching mountain weather:', err));
            }
        }
    });
</script>
@endpush

@endsection
