<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title') - Admin Sanggabuana</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,200..800;1,200..800&display=swap" rel="stylesheet">
    
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.min.js"></script>
    <!-- SweetAlert2 CDN -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <!-- FontAwesome 6 Icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    
    <!-- TomSelect -->
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
    
    <!-- jsQR for QR Code Scanning -->
    <script src="https://cdn.jsdelivr.net/npm/jsqr@1.4.0/dist/jsQR.js"></script>
    
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
            background-color: rgba(15, 23, 42, 0.65) !important;
            backdrop-filter: blur(4px) !important;
            -webkit-backdrop-filter: blur(4px) !important;
            display: none !important;
            align-items: center !important;
            justify-content: center !important;
            transition: all 0.3s ease !important;
            pointer-events: auto !important;
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
        .spinner-ring {
            position: relative !important;
            width: 64px !important;
            height: 64px !important;
        }
        .spinner-ring::before {
            content: '' !important;
            position: absolute !important;
            inset: 0 !important;
            border-radius: 50% !important;
            border: 4px solid #f1f5f9 !important;
        }
        .spinner-ring::after {
            content: '' !important;
            position: absolute !important;
            inset: 0 !important;
            border-radius: 50% !important;
            border: 4px solid transparent !important;
            border-top-color: #059669 !important;
            border-left-color: #059669 !important;
            animation: spin-custom 0.8s linear infinite !important;
        }
        @keyframes spin-custom {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }
    </style>
</head>
<body class="font-sans antialiased bg-gray-50 text-gray-900">
    <!-- Global Loading Overlay -->
    <div id="global-loading-overlay">
        <div class="loading-card">
            <div class="spinner-ring"></div>
            <div>
                <h4 style="font-weight: 800; color: #1e293b; font-size: 14px; margin: 0; font-family: 'Plus Jakarta Sans', sans-serif;">Sedang Memproses...</h4>
            </div>
        </div>
    </div>

    <div class="flex h-screen overflow-hidden">
        {{-- Mobile Sidebar Backdrop --}}
        <div id="sidebar-backdrop" class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm z-20 hidden md:hidden"></div>

        {{-- Sidebar --}}
        <aside id="admin-sidebar" class="fixed inset-y-0 left-0 w-64 bg-white border-r border-gray-200 z-30 transform -translate-x-full md:translate-x-0 md:static flex flex-col transition-transform duration-300 ease-in-out">
            <div class="h-16 flex items-center justify-between px-6 border-b border-gray-200">
                <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-forest-600 flex items-center justify-center">
                        <i data-lucide="mountain" class="w-4 h-4 text-white"></i>
                    </div>
                    <span class="font-bold text-gray-800 text-lg">Admin Panel</span>
                </a>
                <button id="close-sidebar-btn" class="md:hidden text-gray-500 hover:text-gray-700">
                    <i data-lucide="x" class="w-6 h-6"></i>
                </button>
            </div>
            
            <div class="flex-1 overflow-y-auto py-4 px-3 space-y-1">
                @if(Auth::user()->isKasir())
                    {{-- Kasir Sidebar Navigation --}}
                    <div class="pt-2 pb-2 px-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Kasir Panel</div>
                    
                    <a href="{{ route('admin.dashboard') }}" class="admin-sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium text-gray-600 {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                        <i data-lucide="bar-chart-2" class="w-5 h-5"></i> Data Statistik
                    </a>

                    <a href="{{ route('admin.pos.index') }}" class="admin-sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium text-gray-600 {{ request()->routeIs('admin.pos.index') ? 'active' : '' }}">
                        <i data-lucide="ticket" class="w-5 h-5"></i> POS Tiket
                    </a>

                    <a href="{{ route('admin.monitoring.index') }}" class="admin-sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium text-gray-600 {{ request()->routeIs('admin.monitoring.index') ? 'active' : '' }}">
                        <i data-lucide="users" class="w-5 h-5"></i> Monitoring Pengunjung
                    </a>
                    
                    @if(Auth::user()->destination_id)
                        <a href="{{ route('admin.destinations.edit', Auth::user()->destination_id) }}" class="admin-sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium text-gray-600 {{ request()->routeIs('admin.destinations.edit') ? 'active' : '' }}">
                            <i data-lucide="mountain" class="w-5 h-5"></i> Destinasi Saya
                        </a>
                    @endif
                @else
                    {{-- Superadmin & Admin Sidebar Navigation --}}
                    <a href="{{ route('admin.dashboard') }}" class="admin-sidebar-link flex items-center justify-between px-3 py-2.5 rounded-lg text-sm font-medium text-gray-600 {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                        <div class="flex items-center gap-3">
                            <i data-lucide="layout-dashboard" class="w-5 h-5"></i> Dashboard
                        </div>
                        <span id="dashboard-badge-container">
                            @php 
                                $totalUnread = App\Models\Contact::unread()->count() + App\Models\Testimonial::where('is_read', false)->count(); 
                            @endphp
                            @if($totalUnread > 0)
                                <span class="w-2.5 h-2.5 bg-red-500 rounded-full animate-pulse"></span>
                            @endif
                        </span>
                    </a>
                    
                    <div class="pt-4 pb-2 px-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Konten Wisata</div>
                    
                    <a href="{{ route('admin.destinations.index') }}" class="admin-sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium text-gray-600 {{ request()->routeIs('admin.destinations.*') ? 'active' : '' }}">
                        <i data-lucide="map" class="w-5 h-5"></i> Destinasi
                    </a>
                    
                    <a href="{{ route('admin.galleries.index') }}" class="admin-sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium text-gray-600 {{ request()->routeIs('admin.galleries.*') ? 'active' : '' }}">
                        <i data-lucide="image" class="w-5 h-5"></i> Galeri
                    </a>
                    
                    <div class="pt-4 pb-2 px-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Interaksi</div>
                    
                    <a href="{{ route('admin.contacts.index') }}" class="admin-sidebar-link flex items-center justify-between px-3 py-2.5 rounded-lg text-sm font-medium text-gray-600 {{ request()->routeIs('admin.contacts.*') ? 'active' : '' }}">
                        <div class="flex items-center gap-3">
                            <i data-lucide="mail" class="w-5 h-5"></i> Pesan
                        </div>
                        <span id="contacts-badge-container">
                            @php $unread = App\Models\Contact::unread()->count(); @endphp
                            @if($unread > 0)
                                <span class="bg-red-500 text-white text-xs font-bold px-2 py-0.5 rounded-full">{{ $unread }}</span>
                            @endif
                        </span>
                    </a>
                    
                    <a href="{{ route('admin.testimonials.index') }}" class="admin-sidebar-link flex items-center justify-between px-3 py-2.5 rounded-lg text-sm font-medium text-gray-600 {{ request()->routeIs('admin.testimonials.*') ? 'active' : '' }}">
                        <div class="flex items-center gap-3">
                            <i data-lucide="message-square" class="w-5 h-5"></i> Testimoni
                        </div>
                        <span id="testimonials-badge-container">
                            @php $unreadTesti = App\Models\Testimonial::where('is_read', false)->count(); @endphp
                            @if($unreadTesti > 0)
                                <span class="bg-red-500 text-white text-xs font-bold px-2 py-0.5 rounded-full">{{ $unreadTesti }}</span>
                            @endif
                        </span>
                    </a>
                    
                    <div class="pt-4 pb-2 px-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Sistem</div>
                    
                    <a href="{{ route('admin.pages.index') }}" class="admin-sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium text-gray-600 {{ request()->routeIs('admin.pages.*') ? 'active' : '' }}">
                        <i data-lucide="file-text" class="w-5 h-5"></i> Halaman CMS
                    </a>
                    
                    <a href="{{ route('admin.settings.index') }}" class="admin-sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium text-gray-600 {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}">
                        <i data-lucide="settings" class="w-5 h-5"></i> Pengaturan
                    </a>
                    
                    @if(Auth::user()->isSuperAdmin())
                        <a href="{{ route('admin.users.index') }}" class="admin-sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium text-gray-600 {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                            <i data-lucide="shield-check" class="w-5 h-5"></i> Kelola Pengguna
                        </a>

                        <a href="{{ route('admin.purposes.index') }}" class="admin-sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium text-gray-600 {{ request()->routeIs('admin.purposes.*') ? 'active' : '' }}">
                            <i data-lucide="compass" class="w-5 h-5"></i> Master Tujuan
                        </a>
                    @endif

                    {{-- Visitor Accounts -- visible to admin/superadmin --}}
                    @if(Auth::user()->isAdmin() || Auth::user()->isSuperAdmin())
                    <a href="{{ route('admin.visitor-accounts.index') }}" class="admin-sidebar-link flex items-center justify-between px-3 py-2.5 rounded-lg text-sm font-medium text-gray-600 {{ request()->routeIs('admin.visitor-accounts.*') ? 'active' : '' }}">
                        <div class="flex items-center gap-3">
                            <i data-lucide="users" class="w-5 h-5"></i> Akun Pengunjung
                        </div>
                        @php $pendingAccounts = App\Models\VisitorAccount::where('status', 'pending')->count(); @endphp
                        @if($pendingAccounts > 0)
                            <span class="bg-amber-500 text-white text-xs font-bold px-2 py-0.5 rounded-full">{{ $pendingAccounts }}</span>
                        @endif
                    </a>
                    @endif
                @endif
            </div>
            
            <div class="p-4 border-t border-gray-200">
                <form action="{{ route('admin.logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium text-red-600 hover:bg-red-50 transition-colors">
                        <i data-lucide="log-out" class="w-5 h-5"></i> Keluar
                    </button>
                </form>
            </div>
        </aside>

        {{-- Main Content --}}
        <main class="flex-1 flex flex-col h-full overflow-hidden bg-gray-50/50">
            {{-- Header --}}
            <header class="h-16 bg-white border-b border-gray-200 flex items-center justify-between px-6 z-10">
                <div class="flex items-center gap-4">
                    <button id="mobile-menu-btn" class="md:hidden text-gray-500 hover:text-gray-700">
                        <i data-lucide="menu" class="w-6 h-6"></i>
                    </button>
                    <h1 class="text-xl font-bold text-gray-800">@yield('title')</h1>
                </div>
                
                <div class="flex items-center gap-4">
                    <a href="{{ route('home') }}" target="_blank" class="text-sm text-forest-600 font-medium hover:text-forest-800 flex items-center gap-2">
                        <i data-lucide="external-link" class="w-4 h-4"></i> Lihat Web
                    </a>
                    <div class="w-px h-6 bg-gray-200"></div>
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full bg-forest-100 flex items-center justify-center text-forest-600 font-bold text-sm">
                            {{ strtoupper(substr(Auth::user()->name ?? 'A', 0, 1)) }}
                        </div>
                        <span class="text-sm font-medium text-gray-700 hidden sm:block">{{ Auth::user()->name ?? 'Administrator' }}</span>
                    </div>
                </div>
            </header>
            
            {{-- Content Area --}}
            <div class="flex-1 overflow-y-auto p-6">
                {{-- SweetAlert2 Flash Alerts --}}
                @if(session('success'))
                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: "{{ session('success') }}",
                            showConfirmButton: false,
                            timer: 3000,
                            timerProgressBar: true,
                            toast: true,
                            position: 'top-end',
                            background: '#ffffff',
                            iconColor: '#10b981',
                            customClass: {
                                popup: 'rounded-xl shadow-lg border border-gray-100'
                            }
                        });
                    });
                </script>
                @endif
                
                @if(session('error'))
                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        Swal.fire({
                            icon: 'error',
                            title: 'Terjadi Kesalahan!',
                            text: "{{ session('error') }}",
                            confirmButtonColor: '#059669', // Forest Green
                            background: '#ffffff',
                            customClass: {
                                popup: 'rounded-2xl shadow-xl border border-gray-100',
                                confirmButton: 'px-6 py-2.5 bg-forest-600 hover:bg-forest-700 rounded-xl font-bold text-sm text-white'
                            }
                        });
                    });
                </script>
                @endif
                
                @if($errors->any())
                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        Swal.fire({
                            icon: 'error',
                            title: 'Validasi Gagal!',
                            html: `<ul class="text-left list-disc list-inside text-sm text-gray-600 space-y-1">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                   </ul>`,
                            confirmButtonColor: '#059669', // Forest Green
                            background: '#ffffff',
                            customClass: {
                                popup: 'rounded-2xl shadow-xl border border-gray-100',
                                confirmButton: 'px-6 py-2.5 bg-forest-600 hover:bg-forest-700 rounded-xl font-bold text-sm text-white'
                            }
                        });
                    });
                </script>
                @endif

                @yield('content')
            </div>
        </main>
    </div>

    <script>
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

        // Mobile Sidebar Toggle
        const mobileMenuBtn = document.getElementById('mobile-menu-btn');
        const closeSidebarBtn = document.getElementById('close-sidebar-btn');
        const adminSidebar = document.getElementById('admin-sidebar');
        const sidebarBackdrop = document.getElementById('sidebar-backdrop');

        function toggleSidebar() {
            if (adminSidebar) {
                adminSidebar.classList.toggle('-translate-x-full');
                adminSidebar.classList.toggle('translate-x-0');
            }
            if (sidebarBackdrop) {
                sidebarBackdrop.classList.toggle('hidden');
            }
        }

        if (mobileMenuBtn) {
            mobileMenuBtn.addEventListener('click', toggleSidebar);
        }

        if (closeSidebarBtn) {
            closeSidebarBtn.addEventListener('click', toggleSidebar);
        }

        if (sidebarBackdrop) {
            sidebarBackdrop.addEventListener('click', toggleSidebar);
        }

        // Realtime Polling for Unread Messages & Testimonials
        function checkUnreadCounts() {
            fetch('{{ route("admin.notifications.unread") }}')
                .then(response => response.json())
                .then(data => {
                    // Update Dashboard Badge
                    const dashboardContainer = document.getElementById('dashboard-badge-container');
                    if (dashboardContainer) {
                        const totalUnread = data.unread_contacts + data.unread_testimonials;
                        if (totalUnread > 0) {
                            dashboardContainer.innerHTML = '<span class="w-2.5 h-2.5 bg-red-500 rounded-full animate-pulse"></span>';
                        } else {
                            dashboardContainer.innerHTML = '';
                        }
                    }

                    // Update Contacts (Pesan) Badge
                    const contactsContainer = document.getElementById('contacts-badge-container');
                    if (contactsContainer) {
                        if (data.unread_contacts > 0) {
                            contactsContainer.innerHTML = `<span class="bg-red-500 text-white text-xs font-bold px-2 py-0.5 rounded-full">${data.unread_contacts}</span>`;
                        } else {
                            contactsContainer.innerHTML = '';
                        }
                    }

                    // Update Testimonials Badge
                    const testimonialsContainer = document.getElementById('testimonials-badge-container');
                    if (testimonialsContainer) {
                        if (data.unread_testimonials > 0) {
                            testimonialsContainer.innerHTML = `<span class="bg-red-500 text-white text-xs font-bold px-2 py-0.5 rounded-full">${data.unread_testimonials}</span>`;
                        } else {
                            testimonialsContainer.innerHTML = '';
                        }
                    }

                    // Live Update Dashboard View Elements
                    const totalContactsCount = document.getElementById('total-contacts-count');
                    if (totalContactsCount) {
                        // Update stats counts
                        totalContactsCount.innerText = data.total_contacts;
                        document.getElementById('total-testimonials-count').innerText = data.total_testimonials;
                        
                        const unreadContactsCount = document.getElementById('unread-contacts-count');
                        if (unreadContactsCount) {
                            unreadContactsCount.innerText = data.unread_contacts;
                        }

                        const unreadStrip = document.getElementById('unread-indicator-strip');
                        const unreadSubtext = document.getElementById('unread-contacts-subtext');
                        if (data.unread_contacts > 0) {
                            if (unreadStrip) unreadStrip.classList.remove('hidden');
                            if (unreadSubtext) unreadSubtext.classList.remove('hidden');
                        } else {
                            if (unreadStrip) unreadStrip.classList.add('hidden');
                            if (unreadSubtext) unreadSubtext.classList.add('hidden');
                        }

                        // Update recent contacts list in dashboard
                        const listContainer = document.getElementById('recent-contacts-container');
                        if (listContainer) {
                            if (data.recent_contacts && data.recent_contacts.length > 0) {
                                let html = '<div class="divide-y divide-gray-100" id="recent-contacts-list">';
                                data.recent_contacts.forEach(contact => {
                                    const unreadClass = !contact.is_read ? 'bg-forest-50/50' : '';
                                    const fontClass = !contact.is_read ? 'text-forest-900' : '';
                                    const dotHtml = !contact.is_read ? '<div class="w-2.5 h-2.5 rounded-full bg-forest-500 shrink-0 mt-1.5"></div>' : '';
                                    
                                    html += `
                                        <a href="${contact.show_url}" class="flex items-start gap-4 p-5 hover:bg-gray-50 transition-colors ${unreadClass}">
                                            <div class="w-10 h-10 rounded-full bg-gray-100 flex items-center justify-center text-gray-500 font-bold shrink-0">
                                                ${contact.avatar_letter}
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <div class="flex items-center justify-between mb-1">
                                                    <h4 class="font-bold text-sm text-gray-800 truncate ${fontClass}">${contact.name}</h4>
                                                    <span class="text-xs text-gray-400 whitespace-nowrap">${contact.created_time}</span>
                                                </div>
                                                <p class="text-sm text-gray-600 font-medium truncate mb-1">${contact.subject}</p>
                                                <p class="text-xs text-gray-500 truncate">${contact.message}</p>
                                            </div>
                                            ${dotHtml}
                                        </a>
                                    `;
                                });
                                html += '</div>';
                                listContainer.innerHTML = html;
                            } else {
                                listContainer.innerHTML = `
                                    <div class="p-8 text-center text-gray-500" id="recent-contacts-empty">
                                        <i data-lucide="inbox" class="w-12 h-12 mx-auto text-gray-300 mb-3"></i>
                                        <p>Belum ada pesan masuk.</p>
                                    </div>
                                `;
                            }
                            // Re-init Lucide icons for any dynamic elements
                            lucide.createIcons();
                        }
                    }
                })
                .catch(err => console.error('Error fetching unread counts:', err));
        }

        // Run immediately on page load
        checkUnreadCounts();

        // Poll every 5 seconds (5000ms)
        setInterval(checkUnreadCounts, 5000);

        // Global SweetAlert2 confirm dialog interceptor for forms with confirm dialogues
        document.addEventListener('DOMContentLoaded', function() {
            const interceptConfirm = function(form) {
                if (form && !form.dataset.swalAttached) {
                    form.dataset.swalAttached = 'true';
                    const originalOnSubmit = form.getAttribute('onsubmit');
                    let message = 'Apakah Anda yakin ingin menghapus data ini?';
                    if (originalOnSubmit) {
                        const match = originalOnSubmit.match(/confirm\(['"](.*?)['"]\)/);
                        if (match && match[1]) {
                            message = match[1];
                        }
                    }
                    form.removeAttribute('onsubmit');
                    
                    // Determine if this is a checkout action instead of a deletion
                    const isCheckout = message.toLowerCase().includes('check-out') || 
                                       message.toLowerCase().includes('checkout') || 
                                       message.toLowerCase().includes('keluar');

                    form.addEventListener('submit', function(e) {
                        e.preventDefault();
                        Swal.fire({
                            title: isCheckout ? 'Konfirmasi Check Out' : 'Konfirmasi Hapus',
                            text: message,
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: isCheckout ? '#059669' : '#ef4444', // Forest Green for checkout, Red for delete
                            cancelButtonColor: '#6b7280', // Soft Gray
                            confirmButtonText: isCheckout ? 'Ya, Check Out!' : 'Ya, Hapus!',
                            cancelButtonText: 'Batal',
                            background: '#ffffff',
                            color: '#1f2937',
                            iconColor: isCheckout ? '#059669' : '#f59e0b',
                            reverseButtons: true,
                            customClass: {
                                popup: 'rounded-2xl shadow-xl border border-gray-100',
                                confirmButton: 'px-5 py-2.5 rounded-xl font-semibold text-sm',
                                cancelButton: 'px-5 py-2.5 rounded-xl font-semibold text-sm'
                            }
                        }).then((result) => {
                            if (result.isConfirmed) {
                                form.submit();
                            }
                        });
                    });
                }
            };

            // Intercept pre-existing forms
            document.querySelectorAll('form[onsubmit*="confirm"]').forEach(interceptConfirm);

            // Dynamically intercept clicked forms (covers Ajax or dynamic loads)
            document.addEventListener('click', function(e) {
                const deleteBtn = e.target.closest('form[onsubmit*="confirm"] button, form[onsubmit*="confirm"] input[type="submit"]');
                if (deleteBtn) {
                    const form = deleteBtn.closest('form');
                    if (form && !form.dataset.swalAttached) {
                        interceptConfirm(form);
                        form.requestSubmit ? form.requestSubmit() : form.submit();
                    }
                }
            });
        });
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
        };

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
    </script>
</body>
</html>
