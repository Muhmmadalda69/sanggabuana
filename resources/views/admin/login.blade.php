<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin - Sanggabuana</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,200..800;1,200..800&display=swap" rel="stylesheet">
    
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.min.js"></script>
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-forest-950 flex items-center justify-center min-h-screen relative">

    {{-- Background Decorations --}}
    <div class="absolute inset-0 z-0">
        <img src="https://images.unsplash.com/photo-1542224566-6e85f2e6772f?q=80&w=2000&auto=format&fit=crop" class="w-full h-full object-cover opacity-20">
        <div class="absolute inset-0 bg-gradient-to-t from-forest-950 via-forest-950/80 to-forest-900/50"></div>
    </div>

    <div class="relative z-10 w-full max-w-md px-4">
        <div class="text-center mb-8">
            <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-forest-400 to-forest-600 flex items-center justify-center shadow-xl shadow-forest-500/20 mx-auto mb-4 animate-float">
                <i data-lucide="mountain" class="w-8 h-8 text-white"></i>
            </div>
            <h1 class="text-3xl font-bold text-white mb-2">Sanggabuana Admin</h1>
            <p class="text-forest-200">Silakan login untuk mengelola konten wisata.</p>
        </div>

        <div class="glass-card rounded-3xl p-8 border border-white/10 shadow-2xl">
            @if(session('error'))
            <div class="bg-red-500/20 border border-red-500/50 text-red-200 px-4 py-3 rounded-xl mb-6 flex items-start gap-3">
                <i data-lucide="alert-circle" class="w-5 h-5 shrink-0 mt-0.5"></i>
                <div>{{ session('error') }}</div>
            </div>
            @endif
            
            @if($errors->any())
            <div class="bg-red-500/20 border border-red-500/50 text-red-200 px-4 py-3 rounded-xl mb-6 flex items-start gap-3">
                <i data-lucide="alert-circle" class="w-5 h-5 shrink-0 mt-0.5"></i>
                <div>
                    @foreach($errors->all() as $error)
                        <div class="text-sm">{{ $error }}</div>
                    @endforeach
                </div>
            </div>
            @endif

            <form action="{{ route('admin.login.post') }}" method="POST">
                @csrf
                <div class="space-y-5">
                    <div>
                        <label class="block text-forest-200 text-sm font-medium mb-2">Email</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <i data-lucide="mail" class="w-5 h-5 text-forest-400"></i>
                            </div>
                            <input type="email" name="email" value="{{ old('email') }}" required class="w-full bg-forest-900/50 border border-forest-700 text-white rounded-xl pl-12 pr-4 py-3.5 focus:outline-none focus:border-forest-400 focus:ring-1 focus:ring-forest-400 transition-colors" placeholder="admin@sanggabuana.com">
                        </div>
                    </div>
                    
                    <div>
                        <label class="block text-forest-200 text-sm font-medium mb-2">Password</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <i data-lucide="lock" class="w-5 h-5 text-forest-400"></i>
                            </div>
                            <input type="password" name="password" required class="w-full bg-forest-900/50 border border-forest-700 text-white rounded-xl pl-12 pr-4 py-3.5 focus:outline-none focus:border-forest-400 focus:ring-1 focus:ring-forest-400 transition-colors" placeholder="••••••••">
                        </div>
                    </div>

                    <button type="submit" class="w-full py-4 bg-gradient-to-r from-forest-400 to-forest-600 text-white font-bold rounded-xl hover:from-forest-500 hover:to-forest-700 transition-all flex items-center justify-center gap-2 btn-glow mt-6">
                        Masuk Dashboard
                        <i data-lucide="arrow-right" class="w-5 h-5"></i>
                    </button>
                </div>
            </form>
            
            <div class="mt-6 text-center text-forest-400 text-sm">
                Gunakan kredensial default: <br>
                Email: <strong>admin@sanggabuana.com</strong><br>
                Password: <strong>admin123</strong>
            </div>
        </div>
        
        <div class="text-center mt-8 text-forest-500 text-sm">
            &copy; {{ date('Y') }} Wisata Gunung Sanggabuana
        </div>
    </div>

    <script>
        lucide.createIcons();
    </script>
</body>
</html>
