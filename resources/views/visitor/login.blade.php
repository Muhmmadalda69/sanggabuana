@extends('layouts.app')

@section('title', 'Login Pengunjung - Wisata Sanggabuana')

@section('content')
<div class=" pt-24 pb-16 min-h-screen bg-forest-50/50 flex items-center">
    <div class="max-w-md mx-auto px-4 sm:px-6 lg:px-8 w-full">
        <div class="bg-white rounded-3xl p-8 shadow-sm border border-forest-100">
            <div class="text-center mb-8">
                <div class="w-16 h-16 bg-forest-50 text-forest-600 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i data-lucide="user" class="w-8 h-8"></i>
                </div>
                <h1 class="text-2xl font-black text-forest-900">Masuk Akun</h1>
                <p class="text-sm text-gray-500 mt-1">Masuk untuk melanjutkan registrasi online</p>
            </div>

            <form method="POST" action="{{ route('visitor.login.post') }}">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label for="email" class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-2">Email</label>
                        <input type="email" name="email" id="email" value="{{ old('email') }}" required autofocus
                            class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-xl text-sm focus:outline-none focus:border-forest-500 transition-all @error('email') border-red-400 @enderror">
                        @error('email')
                            <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="password" class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-2">Password</label>
                        <div class="relative">
                            <input type="password" name="password" id="password" required
                                class="w-full pl-4 pr-10 py-2.5 bg-white border border-gray-200 rounded-xl text-sm focus:outline-none focus:border-forest-500 transition-all">
                            <button type="button" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600 focus:outline-none toggle-password" data-target="password">
                                <i data-lucide="eye" class="w-5 h-5"></i>
                            </button>
                        </div>
                    </div>
                    <div class="flex items-center justify-between">
                        <label class="flex items-center gap-2 text-xs text-gray-600">
                            <input type="checkbox" name="remember" class="rounded border-gray-300 text-forest-600 focus:ring-forest-500">
                            Ingat saya
                        </label>
                    </div>
                    <button type="submit" class="w-full py-3 bg-forest-600 hover:bg-forest-700 text-white font-bold rounded-xl transition-all text-sm">
                        Masuk
                    </button>
                </div>
            </form>

            <p class="text-center text-xs text-gray-500 mt-6">
                Belum punya akun?
                <a href="{{ route('visitor.register') }}" class="text-forest-600 font-semibold hover:underline">Daftar di sini</a>
            </p>
        </div>
    </div>
</div>
@endsection
