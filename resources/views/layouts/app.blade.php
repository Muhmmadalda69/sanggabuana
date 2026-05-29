<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="{{ App\Models\Setting::get('site_description', 'Wisata Gunung Sanggabuana - Jelajahi Keindahan Alam Pegunungan') }}">
    <meta name="keywords" content="wisata gunung, sanggabuana, karawang, jawa barat, pendakian, air terjun, alam">
    <title>@yield('title', App\Models\Setting::get('site_name', 'Wisata Gunung Sanggabuana'))</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,200..800;1,200..800&display=swap" rel="stylesheet">

    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.min.js"></script>
    
    <!-- FontAwesome 6 Icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
    
    <!-- NProgress for Global Loading Bar -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/nprogress/0.2.0/nprogress.min.css">
    <style>
        #nprogress .bar {
            background: #059669 !important;
            height: 3px !important;
        }
        #nprogress .peg {
            box-shadow: 0 0 10px #059669, 0 0 5px #059669 !important;
        }
        #nprogress .spinner-icon {
            border-top-color: #059669 !important;
            border-left-color: #059669 !important;
        }

        /* Premium Global Loading Overlay styles */
        #global-loading-overlay {
            position: fixed !important;
            top: 0 !important;
            left: 0 !important;
            right: 0 !important;
            bottom: 0 !important;
            z-index: 999999 !important;
            background-color: rgba(15, 23, 42, 0.65) !important; /* Slate 900 semi-transparent */
            backdrop-filter: blur(4px) !important;
            -webkit-backdrop-filter: blur(4px) !important;
            display: none !important;
            align-items: center !important;
            justify-content: center !important;
            transition: all 0.3s ease !important;
            pointer-events: auto !important; /* Block all clicks underneath */
        }
        #global-loading-overlay.active {
            display: flex !important;
        }
        .loading-card {
            background: #ffffff !important;
            border-radius: 24px !important;
            padding: 32px !important;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25) !important;
            border: 1px solid rgba(241, 245, 249, 0.8) !important;
            max-width: 320px !important;
            width: 90% !important;
            text-align: center !important;
            display: flex !important;
            flex-direction: column !important;
            align-items: center !important;
            gap: 16px !important;
            animation: loaderScaleIn 0.3s cubic-bezier(0.34, 1.56, 0.64, 1) !important;
        }
        @keyframes loaderScaleIn {
            from { transform: scale(0.9); opacity: 0; }
            to { transform: scale(1); opacity: 1; }
        }
        .mountain-svg {
            width: 80px !important;
            height: 64px !important;
            animation: mountainPulse 2s ease-in-out infinite !important;
        }
        .mountain-path {
            fill: none !important;
            stroke: #059669 !important;
            stroke-width: 3 !important;
            stroke-linecap: round !important;
            stroke-linejoin: round !important;
            stroke-dasharray: 0 !important;
            stroke-dashoffset: 0 !important;
            animation: drawMountain 2.5s ease-in-out infinite !important;
        }
        .mountain-path-bg {
            fill: none !important;
            stroke: #d1fae5 !important;
            stroke-width: 3 !important;
            stroke-linecap: round !important;
            stroke-linejoin: round !important;
            opacity: 0.3 !important;
        }
        @keyframes drawMountain {
            0% {
                stroke-dasharray: 0 300;
                opacity: 1;
            }
            20% {
                stroke-dasharray: 60 300;
            }
            40% {
                stroke-dasharray: 120 300;
            }
            60% {
                stroke-dasharray: 180 300;
            }
            80% {
                stroke-dasharray: 240 300;
            }
            100% {
                stroke-dasharray: 300 300;
                opacity: 1;
            }
        }
        @keyframes mountainPulse {
            0%, 100% {
                transform: scale(1);
            }
            50% {
                transform: scale(1.05);
            }
        }
        .loading-dots .dot {
            width: 6px !important;
            height: 6px !important;
            background: #059669 !important;
            border-radius: 50% !important;
            display: inline-block !important;
            animation: dotBounce 1.4s infinite ease-in-out both !important;
        }
        @keyframes dotBounce {
            0%, 80%, 100% {
                transform: scale(0);
                opacity: 0.5;
            }
            40% {
                transform: scale(1);
                opacity: 1;
            }
        }
    </style>
</head>
<body class="font-sans antialiased bg-forest-50 text-forest-950">
    <!-- Global Loading Overlay -->
    <div id="global-loading-overlay">
        <div class="loading-card">
            <svg class="mountain-svg" viewBox="0 0 80 64" xmlns="http://www.w3.org/2000/svg">
                <!-- Background path (faint outline) -->
                <polyline class="mountain-path-bg" points="4,60 30,12 40,24 56,8 76,60 4,60"/>
                <!-- Animated drawing path -->
                <polyline class="mountain-path" points="4,60 30,12 40,24 56,8 76,60 4,60"/>
            </svg>
            <div style="display: flex; align-items: center; justify-content: center; gap: 4px; margin-top: 4px;">
                <!-- <h4 style="font-weight: 800; color: #1e293b; font-size: 14px; margin: 0; font-family: 'Plus Jakarta Sans', sans-serif; display: inline;">Sedang Memproses</h4> -->
                <span class="loading-dots" style="display: inline-flex; gap: 4px; margin-left: 2px;">
                    <span class="dot" style="animation-delay: 0s;"></span>
                    <span class="dot" style="animation-delay: 0.2s;"></span>
                    <span class="dot" style="animation-delay: 0.4s;"></span>
                </span>
            </div>
        </div>
    </div>

    {{-- Navigation --}}
    <nav id="navbar" class="fixed top-0 left-0 right-0 z-50 transition-all duration-500 {{ request()->routeIs('home') ? 'bg-transparent' : 'navbar-scroll' }}">
        <div class="w-full px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-20">
                {{-- Logo --}}
                <a href="{{ route('home') }}" class="flex items-center gap-3 group">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-forest-400 to-forest-600 flex items-center justify-center shadow-lg group-hover:shadow-forest-500/30 transition-shadow">
                        <i data-lucide="mountain" class="w-5 h-5 text-white"></i>
                    </div>
                    <div>
                        <span class="text-white font-bold text-lg tracking-tight">Sanggabuana</span>
                        <span class="hidden sm:block text-forest-300 text-xs -mt-1">Mountain Tourism</span>
                    </div>
                </a>

                {{-- Desktop Menu --}}
                <div class="hidden md:flex items-center gap-1">
                    <a href="{{ route('home') }}#beranda" class="px-4 py-2 text-white/80 hover:text-white text-sm font-medium rounded-lg hover:bg-white/10 transition-all">Beranda</a>
                    <a href="{{ route('home') }}#destinasi" class="px-4 py-2 text-white/80 hover:text-white text-sm font-medium rounded-lg hover:bg-white/10 transition-all">Destinasi</a>
                    <a href="{{ route('home') }}#tentang" class="px-4 py-2 text-white/80 hover:text-white text-sm font-medium rounded-lg hover:bg-white/10 transition-all">Tentang</a>
                    <a href="{{ route('home') }}#galeri" class="px-4 py-2 text-white/80 hover:text-white text-sm font-medium rounded-lg hover:bg-white/10 transition-all">Galeri</a>
                    <a href="{{ route('home') }}#testimoni" class="px-4 py-2 text-white/80 hover:text-white text-sm font-medium rounded-lg hover:bg-white/10 transition-all">Testimoni</a>
                    <a href="{{ route('home') }}#kontak" class="px-4 py-2 text-white/80 hover:text-white text-sm font-medium rounded-lg hover:bg-white/10 transition-all">
                    {{-- <a href="{{ route('home') }}#testimoni" class="ml-2 px-5 py-2.5 bg-gradient-to-r from-forest-500 to-forest-600 text-white text-sm font-semibold rounded-xl hover:from-forest-400 hover:to-forest-500 transition-all shadow-lg hover:shadow-forest-500/30 btn-glow"> --}}
                        Hubungi Kami
                    </a>
                    {{-- Visitor Account --}}
                    @auth('visitor')
                    <div class="relative ml-2 group">
                        <button class="flex items-center gap-2 px-4 py-2 text-white bg-white/10 hover:bg-white/20 text-sm font-semibold rounded-xl transition-all">
                            <i data-lucide="user" class="w-4 h-4"></i>
                            <span>{{ Auth::guard('visitor')->user()->name }}</span>
                            <i data-lucide="chevron-down" class="w-3 h-3"></i>
                        </button>
                        <div class="absolute right-0 mt-2 w-48 bg-white rounded-2xl shadow-lg border border-gray-100 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 py-2 z-50">
                            <a href="{{ route('visitor.dashboard') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-700 hover:bg-forest-50 transition-colors">
                                <i data-lucide="layout-dashboard" class="w-4 h-4 text-forest-600"></i>
                                Dashboard
                            </a>
                            <a href="{{ route('visitor.riwayat') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-700 hover:bg-forest-50 transition-colors">
                                <i data-lucide="receipt" class="w-4 h-4 text-forest-600"></i>
                                Riwayat Transaksi
                            </a>
                            <a href="{{ route('visitor.tiket-saya') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-700 hover:bg-forest-50 transition-colors">
                                <i data-lucide="ticket" class="w-4 h-4 text-forest-600"></i>
                                Tiket Saya
                            </a>
                            <hr class="my-2 border-gray-100">
                            <form method="POST" action="{{ route('visitor.logout') }}">
                                @csrf
                                <button type="submit" class="flex items-center gap-3 px-4 py-2.5 text-sm text-red-600 hover:bg-red-50 transition-colors w-full">
                                    <i data-lucide="log-out" class="w-4 h-4"></i>
                                    Keluar
                                </button>
                            </form>
                        </div>
                    </div>
                    @else
                    <a href="{{ route('visitor.login') }}" class="ml-2 px-5 py-2.5 bg-gradient-to-r from-forest-500 to-forest-600 text-white text-sm font-semibold rounded-xl hover:from-forest-400 hover:to-forest-500 transition-all shadow-lg hover:shadow-forest-500/30 btn-glow">Masuk</a>
                    @endauth
                </div>

                {{-- Mobile Menu Button --}}
                <button id="mobile-menu-btn" class="md:hidden p-2 text-white rounded-lg hover:bg-white/10 transition-all">
                    <i data-lucide="menu" class="w-6 h-6" id="menu-icon"></i>
                </button>
            </div>

            {{-- Mobile Menu --}}
            <div id="mobile-menu" class="hidden md:hidden pb-4">
                <div class="glass-card rounded-2xl p-4 mt-2 mobile-menu-enter">
                    <a href="{{ route('home') }}#beranda" class="block px-4 py-3 text-white/80 hover:text-white text-sm font-medium rounded-lg hover:bg-white/10 transition-all">Beranda</a>
                    <a href="{{ route('home') }}#destinasi" class="block px-4 py-3 text-white/80 hover:text-white text-sm font-medium rounded-lg hover:bg-white/10 transition-all">Destinasi</a>
                    <a href="{{ route('home') }}#tentang" class="block px-4 py-3 text-white/80 hover:text-white text-sm font-medium rounded-lg hover:bg-white/10 transition-all">Tentang</a>
                    <a href="{{ route('home') }}#galeri" class="block px-4 py-3 text-white/80 hover:text-white text-sm font-medium rounded-lg hover:bg-white/10 transition-all">Galeri</a>
                    <a href="{{ route('home') }}#testimoni" class="block px-4 py-3 text-white/80 hover:text-white text-sm font-medium rounded-lg hover:bg-white/10 transition-all">Testimoni</a>
                    <a href="#kontak" class="block mb-3 px-5 py-3 bg-gradient-to-r from-forest-500 to-forest-600 text-white text-sm font-semibold rounded-xl text-center">Hubungi Kami</a>
                    <hr class="border-white/10 mb-3">
                    @auth('visitor')
                    <div class="text-white/60 text-xs uppercase tracking-wider px-4 mb-2">Akun</div>
                    <a href="{{ route('visitor.dashboard') }}" class="block px-4 py-3 text-white/80 hover:text-white text-sm font-medium rounded-lg hover:bg-white/10 transition-all">
                        <i data-lucide="layout-dashboard" class="w-4 h-4 inline mr-2"></i>Dashboard
                    </a>
                    <a href="{{ route('visitor.riwayat') }}" class="block px-4 py-3 text-white/80 hover:text-white text-sm font-medium rounded-lg hover:bg-white/10 transition-all">
                        <i data-lucide="receipt" class="w-4 h-4 inline mr-2"></i>Riwayat Transaksi
                    </a>
                    <a href="{{ route('visitor.tiket-saya') }}" class="block px-4 py-3 text-white/80 hover:text-white text-sm font-medium rounded-lg hover:bg-white/10 transition-all">
                        <i data-lucide="ticket" class="w-4 h-4 inline mr-2"></i>Tiket Saya
                    </a>
                    <form method="POST" action="{{ route('visitor.logout') }}" class="mt-2">
                        @csrf
                        <button type="submit" class="block w-full px-4 py-3 text-red-300 hover:text-red-200 text-sm font-medium rounded-lg hover:bg-white/10 transition-all text-left">
                            <i data-lucide="log-out" class="w-4 h-4 inline mr-2"></i>Keluar
                        </button>
                    </form>
                    @else
                    <div class="text-white/60 text-xs uppercase tracking-wider px-4 mb-2">Akun</div>
                    <a href="{{ route('visitor.login') }}" class="block px-4 py-3 text-white/80 hover:text-white text-sm font-medium rounded-lg hover:bg-white/10 transition-all">
                        <i data-lucide="log-in" class="w-4 h-4 inline mr-2"></i>Masuk
                    </a>
                    <a href="{{ route('visitor.register') }}" class="block mt-2 px-5 py-3 bg-white text-forest-800 text-sm font-semibold rounded-xl text-center hover:bg-forest-50 transition-all">
                        Daftar
                    </a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    @yield('content')

    {{-- Footer --}}
    <footer class="bg-forest-950 text-white pt-20 pb-8">
        <div class="w-full px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-12 mb-16">
                {{-- Brand --}}
                <div class="lg:col-span-1">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-forest-400 to-forest-600 flex items-center justify-center">
                            <i data-lucide="mountain" class="w-5 h-5 text-white"></i>
                        </div>
                        <div>
                            <span class="font-bold text-lg">Sanggabuana</span>
                            <span class="block text-forest-400 text-xs -mt-1">Mountain Tourism</span>
                        </div>
                    </div>
                    <p class="text-forest-300 text-sm leading-relaxed mb-6">
                        {{ App\Models\Setting::get('site_description', 'Menjelajahi keindahan alam pegunungan Sanggabuana.') }}
                    </p>
                    <div class="flex gap-3">
                        <a href="{{ App\Models\Setting::get('social_instagram', '#') }}" target="_blank" class="w-10 h-10 rounded-xl bg-forest-800/50 hover:bg-forest-700 text-forest-300 hover:text-pink-400 flex items-center justify-center transition-all duration-300 transform hover:-translate-y-0.5">
                            <i class="fa-brands fa-instagram text-lg"></i>
                        </a>
                        <a href="{{ App\Models\Setting::get('social_facebook', '#') }}" target="_blank" class="w-10 h-10 rounded-xl bg-forest-800/50 hover:bg-forest-700 text-forest-300 hover:text-blue-400 flex items-center justify-center transition-all duration-300 transform hover:-translate-y-0.5">
                            <i class="fa-brands fa-facebook-f text-base"></i>
                        </a>
                        <a href="{{ App\Models\Setting::get('social_youtube', '#') }}" target="_blank" class="w-10 h-10 rounded-xl bg-forest-800/50 hover:bg-forest-700 text-forest-300 hover:text-red-500 flex items-center justify-center transition-all duration-300 transform hover:-translate-y-0.5">
                            <i class="fa-brands fa-youtube text-base"></i>
                        </a>
                    </div>
                </div>

                {{-- Quick Links --}}
                <div>
                    <h4 class="font-semibold text-sm uppercase tracking-wider mb-6 text-forest-300">Navigasi</h4>
                    <ul class="space-y-3">
                        <li><a href="#beranda" class="text-forest-400 hover:text-white text-sm transition-colors flex items-center gap-2"><i data-lucide="chevron-right" class="w-3 h-3"></i>Beranda</a></li>
                        <li><a href="#destinasi" class="text-forest-400 hover:text-white text-sm transition-colors flex items-center gap-2"><i data-lucide="chevron-right" class="w-3 h-3"></i>Destinasi</a></li>
                        <li><a href="#tentang" class="text-forest-400 hover:text-white text-sm transition-colors flex items-center gap-2"><i data-lucide="chevron-right" class="w-3 h-3"></i>Tentang Kami</a></li>
                        <li><a href="#galeri" class="text-forest-400 hover:text-white text-sm transition-colors flex items-center gap-2"><i data-lucide="chevron-right" class="w-3 h-3"></i>Galeri</a></li>
                        <li><a href="#kontak" class="text-forest-400 hover:text-white text-sm transition-colors flex items-center gap-2"><i data-lucide="chevron-right" class="w-3 h-3"></i>Kontak</a></li>
                    </ul>
                </div>

                {{-- Destinations --}}
                <div>
                    <h4 class="font-semibold text-sm uppercase tracking-wider mb-6 text-forest-300">Destinasi</h4>
                    <ul class="space-y-3">
                        @php $footerDest = App\Models\Destination::active()->limit(5)->get(); @endphp
                        @foreach($footerDest as $fd)
                        <li><a href="{{ route('destination.detail', $fd->slug) }}" class="text-forest-400 hover:text-white text-sm transition-colors flex items-center gap-2"><i data-lucide="chevron-right" class="w-3 h-3"></i>{{ $fd->name }}</a></li>
                        @endforeach
                    </ul>
                </div>

                {{-- Contact Info --}}
                <div>
                    <h4 class="font-semibold text-sm uppercase tracking-wider mb-6 text-forest-300">Kontak</h4>
                    <ul class="space-y-4">
                        <li class="flex items-start gap-3">
                            <i data-lucide="map-pin" class="w-4 h-4 text-forest-400 mt-0.5 shrink-0"></i>
                            <span class="text-forest-400 text-sm">{{ App\Models\Setting::get('contact_address', '-') }}</span>
                        </li>
                        <li class="flex items-center gap-3">
                            <i data-lucide="phone" class="w-4 h-4 text-forest-400 shrink-0"></i>
                            <span class="text-forest-400 text-sm">{{ App\Models\Setting::get('contact_phone', '-') }}</span>
                        </li>
                        <li class="flex items-center gap-3">
                            <i data-lucide="mail" class="w-4 h-4 text-forest-400 shrink-0"></i>
                            <span class="text-forest-400 text-sm">{{ App\Models\Setting::get('contact_email', '-') }}</span>
                        </li>
                        <li class="flex items-center gap-3">
                            <i data-lucide="clock" class="w-4 h-4 text-forest-400 shrink-0"></i>
                            <span class="text-forest-400 text-sm">{{ App\Models\Setting::get('open_hours', '-') }}</span>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="border-t border-forest-800 pt-8 flex flex-col md:flex-row items-center justify-between gap-4">
                <p class="text-forest-500 text-sm">&copy; {{ date('Y') }} {{ App\Models\Setting::get('site_name', 'Wisata Sanggabuana') }}. All rights reserved.</p>
                <div class="flex gap-6">
                    @php $footerPages = App\Models\Page::active()->get(); @endphp
                    @foreach($footerPages as $fp)
                    <a href="{{ route('page.show', $fp->slug) }}" class="text-forest-500 hover:text-white text-sm transition-colors">{{ $fp->title }}</a>
                    @endforeach
                </div>
            </div>
        </div>
    </footer>

    {{-- WhatsApp Float Button --}}
    <a href="https://wa.me/{{ App\Models\Setting::get('contact_whatsapp', '6281234567890') }}" target="_blank"
       class="fixed bottom-6 right-6 z-50 w-14 h-14 rounded-full bg-green-500 hover:bg-green-600 shadow-xl hover:shadow-green-500/40 flex items-center justify-center transition-all hover:scale-110 pulse-ring">
        <i data-lucide="message-circle" class="w-6 h-6 text-white"></i>
    </a>

    <script>
        // Initialize Lucide icons
        lucide.createIcons();

        // Toggle Password Visibility
        document.querySelectorAll('.toggle-password').forEach(button => {
            button.addEventListener('click', function() {
                const targetId = this.getAttribute('data-target');
                const input = targetId ? document.getElementById(targetId) : this.closest('.relative').querySelector('input');
                
                if (input) {
                    if (input.type === 'password') {
                        input.type = 'text';
                        this.innerHTML = '<i data-lucide="eye-off" class="w-5 h-5"></i>';
                    } else {
                        input.type = 'password';
                        this.innerHTML = '<i data-lucide="eye" class="w-5 h-5"></i>';
                    }
                    if (window.lucide) {
                        lucide.createIcons();
                    }
                }
            });
        });

        // Navbar scroll effect
        const navbar = document.getElementById('navbar');
        const isHomePage = @json(request()->routeIs('home'));

        if (isHomePage) {
            const handleScroll = () => {
                if (window.scrollY > 50) {
                    navbar.classList.add('navbar-scroll');
                } else {
                    navbar.classList.remove('navbar-scroll');
                }
            };
            window.addEventListener('scroll', handleScroll);
            handleScroll();
        }

        // Mobile menu toggle
        const mobileMenuBtn = document.getElementById('mobile-menu-btn');
        const mobileMenu = document.getElementById('mobile-menu');
        const menuIcon = document.getElementById('menu-icon');

        if (mobileMenuBtn) {
            mobileMenuBtn.addEventListener('click', () => {
                mobileMenu.classList.toggle('hidden');
                const isOpen = !mobileMenu.classList.contains('hidden');
                menuIcon.setAttribute('data-lucide', isOpen ? 'x' : 'menu');
                lucide.createIcons();
            });
        }

        // Close mobile menu on link click
        document.querySelectorAll('#mobile-menu a').forEach(link => {
            link.addEventListener('click', () => {
                mobileMenu.classList.add('hidden');
                menuIcon.setAttribute('data-lucide', 'menu');
                lucide.createIcons();
            });
        });

        // Intersection Observer for scroll animations
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('animate-fade-up');
                    observer.unobserve(entry.target);
                }
            });
        }, observerOptions);

        document.querySelectorAll('.scroll-animate').forEach(el => {
            observer.observe(el);
        });

        // Counter animation
        function animateCounter(element, target) {
            let current = 0;
            const increment = target / 60;
            const timer = setInterval(() => {
                current += increment;
                if (current >= target) {
                    current = target;
                    clearInterval(timer);
                }
                element.textContent = Math.floor(current).toLocaleString('id-ID');
            }, 30);
        }

        const counterObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const target = parseInt(entry.target.dataset.target);
                    animateCounter(entry.target, target);
                    counterObserver.unobserve(entry.target);
                }
            });
        }, { threshold: 0.5 });

        document.querySelectorAll('.counter').forEach(el => counterObserver.observe(el));
    </script>

    {{-- SweetAlert2 for flash messages --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        @if(session('success'))
            Swal.fire({ icon: 'success', title: 'Berhasil!', text: '{{ session('success') }}', confirmButtonColor: '#15803d' });
        @endif
        @if(session('error'))
            Swal.fire({ icon: 'error', title: 'Oops!', text: '{{ session('error') }}', confirmButtonColor: '#dc2626' });
        @endif
        @if(session('info'))
            Swal.fire({ icon: 'info', title: 'Informasi', text: '{{ session('info') }}', confirmButtonColor: '#15803d' });
        @endif
    </script>
    @stack('scripts')
    
    <!-- NProgress JS and Global Progress Bar Logic -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/nprogress/0.2.0/nprogress.min.js"></script>
    <script>
        window.showLoading = function() {
            const overlay = document.getElementById('global-loading-overlay');
            if (overlay) {
                overlay.classList.add('active');
            }
            NProgress.start();
        };

        window.hideLoading = function() {
            const overlay = document.getElementById('global-loading-overlay');
            if (overlay) {
                overlay.classList.remove('active');
            }
            NProgress.done();
            NProgress.remove();
        };

        // Hide loading when page is fully loaded
        document.addEventListener('DOMContentLoaded', function() {
            window.hideLoading();
        });

        window.addEventListener('load', function() {
            window.hideLoading();
        });

        // Start loader on page unload (actual full-page navigation)
        window.addEventListener('beforeunload', function() {
            window.showLoading();
        });

        // Start loader on form submission (if not prevented)
        document.addEventListener('submit', function(e) {
            if (!e.defaultPrevented) {
                window.showLoading();
            }
        });

        // Intercept link clicks — only for actual page navigations, NOT section anchors
        document.addEventListener('click', function(e) {
            const link = e.target.closest('a');
            if (!link) return;
            
            const href = link.getAttribute('href');
            const target = link.getAttribute('target');
            
            // Skip non-navigation links
            if (!href) return;
            if (target === '_blank') return;
            if (href.startsWith('javascript:') || href.startsWith('mailto:') || href.startsWith('tel:')) return;
            if (link.hasAttribute('data-no-loader')) return;
            
            // Skip any link that contains a hash (section anchor)
            if (href.startsWith('#')) return;
            if (href.includes('#')) {
                try {
                    const linkUrl = new URL(href, window.location.origin);
                    // Same page with different hash = section scroll, skip
                    if (linkUrl.pathname === window.location.pathname && linkUrl.origin === window.location.origin) {
                        return;
                    }
                } catch(ex) { /* malformed URL, let it pass */ }
            }
            
            window.showLoading();
        });
        
        // Auto-hide loading if stuck for more than 5 seconds
        setTimeout(function() {
            window.hideLoading();
        }, 5000);
    </script>
</body>
</html>
