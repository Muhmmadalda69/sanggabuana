@extends('layouts.admin')

@section('title', 'Galeri Foto')

@section('content')
<div class="bg-white rounded-2xl shadow-sm border border-gray-100">
    <div class="px-6 py-5 border-b border-gray-100 flex items-center justify-between">
        <h2 class="font-bold text-gray-800 text-lg">Daftar Foto Galeri</h2>
        <a href="{{ route('admin.galleries.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-forest-600 text-white font-medium rounded-xl hover:bg-forest-700 transition-colors text-sm">
            <i data-lucide="plus" class="w-4 h-4"></i> Tambah Foto
        </a>
    </div>

    <div class="p-6">
        @if($galleries->count() > 0)
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                @foreach($galleries as $gallery)
                <div class="group relative rounded-xl overflow-hidden shadow-sm border border-gray-100 bg-gray-50/50 hover:shadow-md transition-shadow">
                    <div class="aspect-[4/3] relative">
                        <img src="{{ $gallery->image_url }}" alt="{{ $gallery->title }}" class="w-full h-full object-cover">
                        <div class="absolute inset-0 bg-gray-900/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center gap-3 backdrop-blur-sm">
                            <a href="{{ route('admin.galleries.edit', $gallery->id) }}" class="w-10 h-10 rounded-full bg-white text-forest-600 flex items-center justify-center hover:bg-forest-50 hover:scale-110 transition-all shadow-lg">
                                <i data-lucide="edit-2" class="w-5 h-5"></i>
                            </a>
                            <form action="{{ route('admin.galleries.destroy', $gallery->id) }}" method="POST" class="inline" onsubmit="return confirm('Hapus foto ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="w-10 h-10 rounded-full bg-white text-red-600 flex items-center justify-center hover:bg-red-50 hover:scale-110 transition-all shadow-lg">
                                    <i data-lucide="trash-2" class="w-5 h-5"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                    <div class="p-4">
                        <h3 class="font-bold text-gray-800 text-sm truncate mb-1">{{ $gallery->title ?: 'Tanpa Judul' }}</h3>
                        @if($gallery->destination)
                            <div class="text-xs text-forest-600 font-medium flex items-center gap-1 mb-2">
                                <i data-lucide="map-pin" class="w-3 h-3"></i> {{ $gallery->destination->name }}
                            </div>
                        @else
                            <div class="text-xs text-gray-400 mb-2">Umum</div>
                        @endif
                        <div class="flex items-center justify-between text-xs mt-3 pt-3 border-t border-gray-100">
                            <span class="text-gray-500">Urutan: {{ $gallery->sort_order }}</span>
                            <span class="inline-flex items-center gap-1 {{ $gallery->is_active ? 'text-green-600' : 'text-gray-400' }}">
                                <span class="w-1.5 h-1.5 rounded-full {{ $gallery->is_active ? 'bg-green-500' : 'bg-gray-400' }}"></span>
                                {{ $gallery->is_active ? 'Aktif' : 'Draft' }}
                            </span>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            
            @if($galleries->hasPages())
            <div class="mt-8">
                {{ $galleries->links() }}
            </div>
            @endif
        @else
            <div class="text-center py-16 text-gray-500">
                <i data-lucide="image" class="w-12 h-12 mx-auto text-gray-300 mb-3"></i>
                <p class="text-lg font-medium text-gray-600 mb-1">Belum ada foto galeri</p>
                <p class="text-sm mb-4">Tambahkan foto pertama untuk ditampilkan di website.</p>
                <a href="{{ route('admin.galleries.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-forest-50 text-forest-600 font-medium rounded-xl hover:bg-forest-100 transition-colors text-sm">
                    Tambah Foto
                </a>
            </div>
        @endif
    </div>
</div>
@endsection
