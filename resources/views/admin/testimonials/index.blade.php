@extends('layouts.admin')

@section('title', 'Testimoni')

@section('content')
<div class="bg-white rounded-2xl shadow-sm border border-gray-100">
    <div class="px-6 py-5 border-b border-gray-100 flex items-center justify-between">
        <h2 class="font-bold text-gray-800 text-lg">Daftar Testimoni</h2>
        <a href="{{ route('admin.testimonials.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-forest-600 text-white font-medium rounded-xl hover:bg-forest-700 transition-colors text-sm">
            <i data-lucide="plus" class="w-4 h-4"></i> Tambah Testimoni
        </a>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-50/50">
                    <th class="px-6 py-4 font-semibold text-gray-500 text-sm border-b border-gray-100">Pengunjung</th>
                    <th class="px-6 py-4 font-semibold text-gray-500 text-sm border-b border-gray-100">Rating & Pesan</th>
                    <th class="px-6 py-4 font-semibold text-gray-500 text-sm border-b border-gray-100 w-24">Urutan</th>
                    <th class="px-6 py-4 font-semibold text-gray-500 text-sm border-b border-gray-100 w-28">Status</th>
                    <th class="px-6 py-4 font-semibold text-gray-500 text-sm border-b border-gray-100 text-right w-28">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($testimonials as $testi)
                <tr class="hover:bg-gray-50/50 transition-colors">
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <img src="{{ $testi->avatar_url }}" alt="{{ $testi->name }}" class="w-10 h-10 rounded-full object-cover bg-gray-100">
                            <div>
                                <div class="font-bold text-gray-800">{{ $testi->name }}</div>
                                <div class="text-xs text-gray-500">{{ $testi->role ?? '-' }}</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-1 text-yellow-400 mb-1">
                            @for($i = 0; $i < 5; $i++)
                                <i data-lucide="star" class="w-3.5 h-3.5 {{ $i < $testi->rating ? 'fill-current' : 'text-gray-300' }}"></i>
                            @endfor
                        </div>
                        <div class="text-sm text-gray-600 line-clamp-2 italic">"{{ $testi->message }}"</div>
                    </td>
                    <td class="px-6 py-4">
                        <span class="text-sm font-medium text-gray-700 bg-gray-100 px-2 py-1 rounded">{{ $testi->sort_order }}</span>
                    </td>
                    <td class="px-6 py-4">
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium {{ $testi->is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600' }}">
                            <span class="w-1.5 h-1.5 rounded-full {{ $testi->is_active ? 'bg-green-500' : 'bg-gray-400' }}"></span>
                            {{ $testi->is_active ? 'Aktif' : 'Draft' }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex items-center justify-end gap-2">
                            <form action="{{ route('admin.testimonials.toggle', $testi->id) }}" method="POST" class="inline">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="p-2 text-gray-400 hover:text-{{ $testi->is_active ? 'amber' : 'green' }}-600 bg-white border border-gray-200 rounded-lg shadow-sm hover:shadow transition-all" title="{{ $testi->is_active ? 'Sembunyikan' : 'Tampilkan (Setujui)' }}">
                                    <i data-lucide="{{ $testi->is_active ? 'eye-off' : 'check-circle' }}" class="w-4 h-4"></i>
                                </button>
                            </form>
                            <a href="{{ route('admin.testimonials.edit', $testi->id) }}" class="p-2 text-gray-400 hover:text-orange-600 bg-white border border-gray-200 rounded-lg shadow-sm hover:shadow transition-all" title="Edit">
                                <i data-lucide="edit-2" class="w-4 h-4"></i>
                            </a>
                            <form action="{{ route('admin.testimonials.destroy', $testi->id) }}" method="POST" class="inline" onsubmit="return confirm('Hapus testimoni ini?')">
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
                        <i data-lucide="message-square" class="w-12 h-12 mx-auto text-gray-300 mb-3"></i>
                        <p class="text-lg font-medium text-gray-600 mb-1">Belum ada testimoni</p>
                        <p class="text-sm mb-4">Tambahkan testimoni untuk meningkatkan kepercayaan pengunjung.</p>
                        <a href="{{ route('admin.testimonials.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-forest-50 text-forest-600 font-medium rounded-xl hover:bg-forest-100 transition-colors text-sm">
                            Tambah Testimoni
                        </a>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    @if($testimonials->hasPages())
    <div class="px-6 py-4 border-t border-gray-100">
        {{ $testimonials->links() }}
    </div>
    @endif
</div>
@endsection
