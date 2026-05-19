@extends('layouts.admin')

@section('title', 'Tugas Kasir Tertunda')

@section('content')
<div class="max-w-md mx-auto my-12 text-center bg-white rounded-2xl border border-gray-100 shadow-sm p-8">
    <div class="w-16 h-16 bg-amber-50 text-amber-500 rounded-full flex items-center justify-center mx-auto mb-6">
        <i data-lucide="alert-triangle" class="w-8 h-8"></i>
    </div>
    
    <h2 class="text-xl font-bold text-gray-800 mb-3" style="border-left:none; padding-left:0; justify-content:center;">Destinasi Belum Ditugaskan</h2>
    
    <p class="text-sm text-gray-500 mb-6 leading-relaxed">
        Akun kasir Anda saat ini belum ditugaskan ke destinasi wisata mana pun. Untuk dapat mulai melakukan penjualan tiket, silakan hubungi <strong>Super Administrator</strong> Anda untuk mengaitkan akun Anda dengan salah satu destinasi wisata di panel pengguna.
    </p>

    <div class="bg-gray-50 rounded-xl p-4 text-xs text-gray-500 flex flex-col gap-2 text-left">
        <div class="font-semibold text-gray-700 flex items-center gap-1.5 mb-1">
            <i data-lucide="help-circle" class="w-4 h-4 text-gray-400"></i> Informasi Kontak Superadmin
        </div>
        <div><strong>Email Resmi:</strong> superadmin@sanggabuana.com</div>
        <div><strong>Hubungi Via WA:</strong> Pengaturan Global &gt; Telepon Kontak</div>
    </div>
</div>
@endsection
