@extends('layouts.app')

@section('title', 'Daftar Akun - Wisata Sanggabuana')

@section('content')
<div class="pt-24 pb-16 min-h-screen bg-forest-50/50 flex items-center">
    <div class="max-w-md mx-auto px-4 sm:px-6 lg:px-8 w-full">
        <div class="bg-white rounded-3xl p-8 shadow-sm border border-forest-100">
            <div class="text-center mb-8">
                <div class="w-16 h-16 bg-forest-50 text-forest-600 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i data-lucide="user-plus" class="w-8 h-8"></i>
                </div>
                <h1 class="text-2xl font-black text-forest-900">Daftar Akun</h1>
                <p class="text-sm text-gray-500 mt-1">Buat akun untuk registrasi online</p>
            </div>

            <form method="POST" action="{{ route('visitor.register.post') }}">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label for="name" class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-2">Nama Lengkap</label>
                        <input type="text" name="name" id="name" value="{{ old('name') }}" required
                            class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-xl text-sm focus:outline-none focus:border-forest-500 transition-all">
                    </div>
                    <div>
                        <label for="email" class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-2">Email</label>
                        <input type="email" name="email" id="email" value="{{ old('email') }}" required
                            class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-xl text-sm focus:outline-none focus:border-forest-500 transition-all @error('email') border-red-400 @enderror">
                        @error('email')
                            <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="phone" class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-2">Nomor HP <span class="text-gray-400 font-normal">(opsional)</span></label>
                        <input type="text" name="phone" id="phone" value="{{ old('phone') }}"
                            class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-xl text-sm focus:outline-none focus:border-forest-500 transition-all">
                    </div>
                    <div>
                        <label for="password" class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-2">Password</label>
                        <input type="password" name="password" id="password" required minlength="6"
                            class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-xl text-sm focus:outline-none focus:border-forest-500 transition-all">
                    </div>
                    <div>
                        <label for="password_confirmation" class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-2">Konfirmasi Password</label>
                        <input type="password" name="password_confirmation" id="password_confirmation" required
                            class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-xl text-sm focus:outline-none focus:border-forest-500 transition-all">
                    </div>
                    <button type="submit" class="w-full py-3 bg-forest-600 hover:bg-forest-700 text-white font-bold rounded-xl transition-all text-sm">
                        Daftar
                    </button>
                </div>
            </form>

            <p class="text-center text-xs text-gray-500 mt-6">
                Sudah punya akun?
                <a href="{{ route('visitor.login') }}" class="text-forest-600 font-semibold hover:underline">Masuk di sini</a>
            </p>
        </div>
    </div>
</div>
@endsection
