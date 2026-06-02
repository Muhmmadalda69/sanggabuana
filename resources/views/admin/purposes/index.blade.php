@extends('layouts.admin')

@section('title', 'Master Tujuan Kunjungan')

@section('content')
<div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
    <div>
        <p class="text-sm text-gray-500">Kelola master tujuan kunjungan (seperti Hiking, Trail Run, Ziarah) serta penugasan ke masing-masing destinasi wisata secara fleksibel.</p>
    </div>
    <div>
        <a href="{{ route('admin.purposes.create') }}" class="inline-flex items-center gap-2 bg-forest-600 hover:bg-forest-700 text-white px-4 py-2.5 rounded-xl font-medium text-sm transition-colors shadow-sm">
            <i data-lucide="plus-circle" class="w-4 h-4"></i> Tambah Tujuan Kunjungan
        </a>
    </div>
</div>

<div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-50/75 border-b border-gray-100 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                    <th class="px-6 py-4">Tujuan Kunjungan</th>
                    <th class="px-6 py-4">Slug</th>
                    <th class="px-6 py-4">Tampilan Destinasi</th>
                    <th class="px-6 py-4 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 text-sm text-gray-600">
                @forelse($purposes as $purp)
                <tr class="hover:bg-gray-50/50 transition-colors">
                    <td class="px-6 py-4 font-semibold text-gray-800">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-full bg-forest-50 text-forest-600 flex items-center justify-center font-bold">
                                {{ strtoupper(substr($purp->name, 0, 1)) }}
                            </div>
                            <div>
                                <span>{{ $purp->name }}</span>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 font-mono text-xs text-gray-500">{{ $purp->slug }}</td>
                    <td class="px-6 py-4">
                        @if($purp->is_all_destinations)
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-700">
                                <i data-lucide="globe" class="w-3.5 h-3.5"></i> Semua Destinasi (All)
                            </span>
                        @else
                            <div class="flex flex-wrap gap-1.5 max-w-md">
                                @forelse($purp->destinations as $dest)
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium bg-forest-50 text-forest-700 border border-forest-100">
                                        <i data-lucide="map-pin" class="w-3 h-3 text-forest-500"></i>
                                        {{ $dest->name }}
                                    </span>
                                @empty
                                    <span class="text-xs text-red-500 italic">Belum dipetakan ke mana pun (Tidak Tampil)</span>
                                @endforelse
                            </div>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex items-center justify-end gap-2">
                            <a href="{{ route('admin.purposes.edit', $purp->id) }}" class="p-1.5 rounded-lg text-gray-500 hover:text-forest-600 hover:bg-gray-100 transition-all" title="Edit Tujuan">
                                <i data-lucide="edit-3" class="w-4.5 h-4.5"></i>
                            </a>
                            <form action="{{ route('admin.purposes.destroy', $purp->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus tujuan kunjungan {{ $purp->name }} ini? Tindakan ini akan menghapus pengaturan harga khusus tujuan ini pada semua destinasi.')" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-1.5 rounded-lg text-gray-500 hover:text-red-600 hover:bg-gray-100 transition-all" title="Hapus Tujuan">
                                    <i data-lucide="trash-2" class="w-4.5 h-4.5"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="px-6 py-12 text-center text-gray-400">
                        <i data-lucide="compass" class="w-12 h-12 mx-auto mb-3 text-gray-300"></i>
                        <p class="font-medium text-base">Belum ada tujuan kunjungan terdaftar.</p>
                        <p class="text-xs mt-1 text-gray-400">Silakan tambahkan tujuan baru dengan mengklik tombol Tambah Tujuan Kunjungan.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    @if($purposes->hasPages())
    <div class="px-6 py-4 border-t border-gray-100">
        {{ $purposes->links() }}
    </div>
    @endif
</div>
@endsection
