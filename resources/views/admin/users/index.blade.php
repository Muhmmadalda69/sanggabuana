@extends('layouts.admin')

@section('title', 'Manajemen Pengguna (RBAC)')

@section('content')
<div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
    <div>
        <p class="text-sm text-gray-500">Kelola hak akses administrator dan akun kasir destinasi wisata secara tersentralisasi.</p>
    </div>
    <div>
        <a href="{{ route('admin.users.create') }}" class="inline-flex items-center gap-2 bg-forest-600 hover:bg-forest-700 text-white px-4 py-2.5 rounded-xl font-medium text-sm transition-colors shadow-sm">
            <i data-lucide="user-plus" class="w-4 h-4"></i> Tambah Pengguna
        </a>
    </div>
</div>

<div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-50/75 border-b border-gray-100 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                    <th class="px-6 py-4">Nama Pengguna</th>
                    <th class="px-6 py-4">Alamat Email</th>
                    <th class="px-6 py-4">Peran (Role)</th>
                    <th class="px-6 py-4">Tugas Destinasi</th>
                    <th class="px-6 py-4 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 text-sm text-gray-600">
                @forelse($users as $usr)
                <tr class="hover:bg-gray-50/50 transition-colors">
                    <td class="px-6 py-4 font-semibold text-gray-800">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-full bg-forest-50 text-forest-600 flex items-center justify-center font-bold">
                                {{ strtoupper(substr($usr->name, 0, 1)) }}
                            </div>
                            <div>
                                <span>{{ $usr->name }}</span>
                                @if($usr->id === Auth::id())
                                    <span class="ml-2 text-xs font-medium px-2 py-0.5 rounded-full bg-forest-100 text-forest-700">Anda</span>
                                @endif
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4">{{ $usr->email }}</td>
                    <td class="px-6 py-4">
                        @if($usr->isSuperAdmin())
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-purple-100 text-purple-700">
                                <i data-lucide="shield-alert" class="w-3.5 h-3.5"></i> Superadmin
                            </span>
                        @elseif($usr->isAdmin())
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-700">
                                <i data-lucide="user-cog" class="w-3.5 h-3.5"></i> Admin
                            </span>
                        @elseif($usr->isKasir())
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-700">
                                <i data-lucide="calculator" class="w-3.5 h-3.5"></i> Kasir
                            </span>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        @if($usr->isKasir() && $usr->destination)
                            <span class="font-medium text-gray-800 flex items-center gap-1.5">
                                <i data-lucide="map-pin" class="w-4 h-4 text-forest-500"></i>
                                {{ $usr->destination->name }}
                            </span>
                        @elseif($usr->isKasir())
                            <span class="text-xs text-red-500 italic">Belum ditentukan</span>
                        @else
                            <span class="text-xs text-gray-400 font-medium uppercase tracking-wider">Akses Semua</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex items-center justify-end gap-2">
                            <a href="{{ route('admin.users.edit', $usr->id) }}" class="p-1.5 rounded-lg text-gray-500 hover:text-forest-600 hover:bg-gray-100 transition-all" title="Edit Pengguna">
                                <i data-lucide="edit-3" class="w-4.5 h-4.5"></i>
                            </a>
                            @if($usr->id !== Auth::id())
                                <form action="{{ route('admin.users.destroy', $usr->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus pengguna {{ $usr->name }} ini? Tindakan ini tidak dapat dibatalkan.')" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-1.5 rounded-lg text-gray-500 hover:text-red-600 hover:bg-gray-100 transition-all" title="Hapus Pengguna">
                                        <i data-lucide="trash-2" class="w-4.5 h-4.5"></i>
                                    </button>
                                </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-12 text-center text-gray-400">
                        <i data-lucide="users" class="w-12 h-12 mx-auto mb-3 text-gray-300"></i>
                        <p class="font-medium text-base">Belum ada pengguna terdaftar.</p>
                        <p class="text-xs mt-1 text-gray-400">Silakan tambahkan pengguna baru dengan mengklik tombol Tambah Pengguna.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    @if($users->hasPages())
    <div class="px-6 py-4 border-t border-gray-100">
        {{ $users->links() }}
    </div>
    @endif
</div>
@endsection
