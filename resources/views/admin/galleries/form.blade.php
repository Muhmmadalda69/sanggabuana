@extends('layouts.admin')

@section('title', $gallery->exists ? 'Edit Foto Galeri' : 'Tambah Foto Galeri')

@section('content')
<div class="max-w-2xl">
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-100 flex items-center justify-between bg-gray-50/50">
            <h2 class="font-bold text-gray-800 text-lg flex items-center gap-2">
                <i data-lucide="{{ $gallery->exists ? 'edit-3' : 'image-plus' }}" class="w-5 h-5 text-forest-600"></i>
                {{ $gallery->exists ? 'Edit Foto' : 'Upload Foto Baru' }}
            </h2>
            <a href="{{ route('admin.galleries.index') }}" class="text-sm font-medium text-gray-500 hover:text-gray-700 flex items-center gap-1">
                <i data-lucide="arrow-left" class="w-4 h-4"></i> Kembali
            </a>
        </div>

        <form action="{{ $gallery->exists ? route('admin.galleries.update', $gallery->id) : route('admin.galleries.store') }}" method="POST" enctype="multipart/form-data" class="p-6">
            @csrf
            @if($gallery->exists)
                @method('PUT')
            @endif

            <div class="space-y-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Foto <span class="text-red-500">*</span></label>
                    @if($gallery->image)
                        <div class="mb-4">
                            <img src="{{ $gallery->image_url }}" alt="Preview" class="w-full h-48 object-cover rounded-xl border border-gray-200">
                        </div>
                    @endif
                    <input type="file" name="image" {{ !$gallery->exists ? 'required' : '' }} accept="image/*" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-forest-50 file:text-forest-700 hover:file:bg-forest-100">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Judul Foto</label>
                    <input type="text" name="title" value="{{ old('title', $gallery->title) }}" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-forest-500 focus:ring-forest-500 px-4 py-2 border">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Keterangan / Caption</label>
                    <textarea name="caption" rows="3" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-forest-500 focus:ring-forest-500 px-4 py-2 border">{{ old('caption', $gallery->caption) }}</textarea>
                </div>

                <div class="grid grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Terkait Destinasi?</label>
                        <select name="destination_id" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-forest-500 focus:ring-forest-500 px-4 py-2 border bg-white">
                            <option value="">-- Umum / Tidak ada --</option>
                            @foreach($destinations as $dest)
                                <option value="{{ $dest->id }}" {{ old('destination_id', $gallery->destination_id) == $dest->id ? 'selected' : '' }}>
                                    {{ $dest->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Urutan Tampil</label>
                        <input type="number" name="sort_order" value="{{ old('sort_order', $gallery->sort_order ?? 0) }}" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-forest-500 focus:ring-forest-500 px-4 py-2 border">
                    </div>
                </div>

                <div class="bg-gray-50 p-4 rounded-xl border border-gray-100">
                    <label class="flex items-center gap-3 cursor-pointer">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', $gallery->exists ? $gallery->is_active : true) ? 'checked' : '' }} class="w-5 h-5 rounded text-forest-600 focus:ring-forest-500 border-gray-300">
                        <div>
                            <div class="font-medium text-gray-900">Tampilkan Foto</div>
                            <div class="text-sm text-gray-500">Tampilkan foto ini di halaman galeri website.</div>
                        </div>
                    </label>
                </div>
            </div>

            <div class="mt-8 pt-6 border-t border-gray-100 flex items-center justify-end gap-3">
                <a href="{{ route('admin.galleries.index') }}" class="px-6 py-2.5 bg-white border border-gray-300 text-gray-700 font-medium rounded-xl hover:bg-gray-50 transition-colors">Batal</a>
                <button type="submit" class="px-6 py-2.5 bg-forest-600 text-white font-medium rounded-xl hover:bg-forest-700 transition-colors shadow-sm flex items-center gap-2">
                    <i data-lucide="save" class="w-4 h-4"></i> Simpan Foto
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
