@extends('layouts.admin')

@section('title', 'Akun Pengunjung')

@section('content')
<div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
    <div class="p-6 border-b border-gray-100 flex items-center justify-between">
        <div>
            <h2 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                <i data-lucide="users" class="w-5 h-5 text-forest-600"></i>
                Daftar Akun Pengunjung
            </h2>
            <p class="text-xs text-gray-400 mt-0.5">Kelola akun pengunjung yang mendaftar melalui website.</p>
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead>
                <tr class="bg-gray-50 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                    <th class="px-6 py-3">Nama</th>
                    <th class="px-6 py-3">Email</th>
                    <th class="px-6 py-3">Telepon</th>
                    <th class="px-6 py-3">Status</th>
                    <th class="px-6 py-3">Daftar</th>
                    <th class="px-6 py-3">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 text-sm">
                @forelse($accounts as $account)
                <tr class="hover:bg-gray-50/50">
                    <td class="px-6 py-4 font-semibold text-gray-700">{{ $account->name }}</td>
                    <td class="px-6 py-4 text-gray-500">{{ $account->email }}</td>
                    <td class="px-6 py-4 text-gray-500">{{ $account->phone ?? '-' }}</td>
                    <td class="px-6 py-4">
                        @if($account->status === 'active')
                            <span class="px-2.5 py-1 bg-green-50 text-green-700 rounded-full text-xs font-semibold">Aktif</span>
                        @elseif($account->status === 'pending')
                            <span class="px-2.5 py-1 bg-amber-50 text-amber-700 rounded-full text-xs font-semibold">Menunggu</span>
                        @else
                            <span class="px-2.5 py-1 bg-red-50 text-red-700 rounded-full text-xs font-semibold">Diblokir</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-xs text-gray-400">{{ $account->created_at->translatedFormat('d M Y, H:i') }}</td>
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-2">
                            @if($account->status === 'pending')
                            <form action="{{ route('admin.visitor-accounts.activate', $account) }}" method="POST">
                                @csrf
                                <button type="submit" class="px-3 py-1.5 bg-forest-600 hover:bg-forest-700 text-white text-xs font-semibold rounded-lg transition-all">
                                    Aktifkan
                                </button>
                            </form>
                            @endif
                            @if($account->status === 'active')
                            <form action="{{ route('admin.visitor-accounts.ban', $account) }}" method="POST" onsubmit="return confirm('Nonaktifkan akun {{ $account->name }}?')">
                                @csrf
                                <button type="submit" class="px-3 py-1.5 bg-red-500 hover:bg-red-600 text-white text-xs font-semibold rounded-lg transition-all">
                                    Nonaktifkan
                                </button>
                            </form>
                            @endif
                            @if($account->status === 'banned')
                            <form action="{{ route('admin.visitor-accounts.activate', $account) }}" method="POST">
                                @csrf
                                <button type="submit" class="px-3 py-1.5 bg-forest-600 hover:bg-forest-700 text-white text-xs font-semibold rounded-lg transition-all">
                                    Aktifkan Kembali
                                </button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-12 text-center text-sm text-gray-400">
                        <i data-lucide="users" class="w-10 h-10 mx-auto text-gray-300 mb-3"></i>
                        Belum ada akun pengunjung.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
