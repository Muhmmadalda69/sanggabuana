@extends('layouts.admin')

@section('title', 'Destinasi Wisata')

@section('content')
<div class="bg-white rounded-2xl shadow-sm border border-gray-100">
    <div class="px-6 py-5 border-b border-gray-100 flex items-center justify-between">
        <h2 class="font-bold text-gray-800 text-lg">Daftar Destinasi</h2>
        <a href="{{ route('admin.destinations.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-forest-600 text-white font-medium rounded-xl hover:bg-forest-700 transition-colors text-sm">
            <i data-lucide="plus" class="w-4 h-4"></i> Tambah Destinasi
        </a>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-50/50">
                    <th class="px-6 py-4 font-semibold text-gray-500 text-sm border-b border-gray-100">Info Destinasi</th>
                    <th class="px-6 py-4 font-semibold text-gray-500 text-sm border-b border-gray-100">Lokasi / Ketinggian</th>
                    <th class="px-6 py-4 font-semibold text-gray-500 text-sm border-b border-gray-100">Waktu Operasional</th>
                    <th class="px-6 py-4 font-semibold text-gray-500 text-sm border-b border-gray-100">Status</th>
                    <th class="px-6 py-4 font-semibold text-gray-500 text-sm border-b border-gray-100 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($destinations as $dest)
                <tr class="hover:bg-gray-50/50 transition-colors">
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-4">
                            <img src="{{ $dest->image_url }}" alt="{{ $dest->name }}" class="w-16 h-16 rounded-xl object-cover border border-gray-200">
                            <div>
                                <div class="font-bold text-gray-800 mb-1 flex items-center gap-2">
                                    {{ $dest->name }}
                                    @if($dest->is_featured)
                                        <i data-lucide="star" class="w-3.5 h-3.5 text-yellow-500 fill-yellow-500" title="Featured"></i>
                                    @endif
                                </div>
                                <div class="text-sm text-gray-500">Rp {{ number_format($dest->price, 0, ',', '.') }}</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="text-sm text-gray-800 mb-1">{{ $dest->location ?? '-' }}</div>
                        <div class="text-xs text-gray-500 flex items-center gap-1">
                            <i data-lucide="mountain" class="w-3 h-3"></i> {{ $dest->altitude ?? '-' }}
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="text-sm text-gray-800 mb-1 flex items-center gap-1"><i data-lucide="calendar" class="w-3 h-3 text-gray-400"></i> {{ $dest->operational_days ?? '-' }}</div>
                        <div class="text-xs text-gray-500 flex items-center gap-1"><i data-lucide="clock" class="w-3 h-3 text-gray-400"></i> {{ $dest->operational_hours ?? '-' }}</div>
                    </td>
                    <td class="px-6 py-4">
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium {{ $dest->is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600' }}">
                            <span class="w-1.5 h-1.5 rounded-full {{ $dest->is_active ? 'bg-green-500' : 'bg-gray-400' }}"></span>
                            {{ $dest->is_active ? 'Aktif' : 'Draft' }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex items-center justify-end gap-2">
                            <a href="{{ route('destination.detail', $dest->slug) }}" target="_blank" class="p-2 text-gray-400 hover:text-blue-600 bg-white border border-gray-200 rounded-lg shadow-sm hover:shadow transition-all" title="Lihat">
                                <i data-lucide="eye" class="w-4 h-4"></i>
                            </a>
                            <a href="{{ route('admin.destinations.edit', $dest->id) }}" class="p-2 text-gray-400 hover:text-orange-600 bg-white border border-gray-200 rounded-lg shadow-sm hover:shadow transition-all" title="Edit">
                                <i data-lucide="edit-2" class="w-4 h-4"></i>
                            </a>
                            <form action="{{ route('admin.destinations.destroy', $dest->id) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus destinasi ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-2 text-gray-400 hover:text-red-600 bg-white border border-gray-200 rounded-lg shadow-sm hover:shadow transition-all" title="Hapus">
                                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                        <i data-lucide="map" class="w-12 h-12 mx-auto text-gray-300 mb-3"></i>
                        <p class="text-lg font-medium text-gray-600 mb-1">Belum ada destinasi</p>
                        <p class="text-sm mb-4">Tambahkan destinasi pertama Anda untuk ditampilkan di website.</p>
                        <a href="{{ route('admin.destinations.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-forest-50 text-forest-600 font-medium rounded-xl hover:bg-forest-100 transition-colors text-sm">
                            Tambah Destinasi
                        </a>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    @if($destinations->hasPages())
    <div class="px-6 py-4 border-t border-gray-100">
        {{ $destinations->links() }}
    </div>
    @endif
</div>
@endsection
