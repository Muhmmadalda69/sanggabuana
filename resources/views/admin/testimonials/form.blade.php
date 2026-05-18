@extends('layouts.admin')

@section('title', $testimonial->exists ? 'Edit Testimoni' : 'Tambah Testimoni')

@section('content')
<div class="max-w-2xl">
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-100 flex items-center justify-between bg-gray-50/50">
            <h2 class="font-bold text-gray-800 text-lg flex items-center gap-2">
                <i data-lucide="{{ $testimonial->exists ? 'edit-3' : 'plus-circle' }}" class="w-5 h-5 text-forest-600"></i>
                {{ $testimonial->exists ? 'Edit Testimoni' : 'Tambah Testimoni Baru' }}
            </h2>
            <a href="{{ route('admin.testimonials.index') }}" class="text-sm font-medium text-gray-500 hover:text-gray-700 flex items-center gap-1">
                <i data-lucide="arrow-left" class="w-4 h-4"></i> Kembali
            </a>
        </div>

        <form action="{{ $testimonial->exists ? route('admin.testimonials.update', $testimonial->id) : route('admin.testimonials.store') }}" method="POST" enctype="multipart/form-data" class="p-6">
            @csrf
            @if($testimonial->exists)
                @method('PUT')
            @endif

            <div class="space-y-6">
                <div class="grid grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap <span class="text-red-500">*</span></label>
                        <input type="text" name="name" value="{{ old('name', $testimonial->name) }}" required class="w-full rounded-xl border-gray-300 shadow-sm focus:border-forest-500 focus:ring-forest-500 px-4 py-2 border">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Peran / Profesi</label>
                        <input type="text" name="role" value="{{ old('role', $testimonial->role) }}" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-forest-500 focus:ring-forest-500 px-4 py-2 border" placeholder="Contoh: Travel Blogger">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Pesan Testimoni <span class="text-red-500">*</span></label>
                    <textarea name="message" rows="4" required class="w-full rounded-xl border-gray-300 shadow-sm focus:border-forest-500 focus:ring-forest-500 px-4 py-2 border">{{ old('message', $testimonial->message) }}</textarea>
                </div>

                <div class="grid grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Rating <span class="text-red-500">*</span></label>
                        <select name="rating" required class="w-full rounded-xl border-gray-300 shadow-sm focus:border-forest-500 focus:ring-forest-500 px-4 py-2 border bg-white">
                            @for($i = 5; $i >= 1; $i--)
                                <option value="{{ $i }}" {{ old('rating', $testimonial->rating ?? 5) == $i ? 'selected' : '' }}>
                                    {{ $i }} Bintang
                                </option>
                            @endfor
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Urutan Tampil</label>
                        <input type="number" name="sort_order" value="{{ old('sort_order', $testimonial->sort_order ?? 0) }}" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-forest-500 focus:ring-forest-500 px-4 py-2 border">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Foto / Avatar (Opsional)</label>
                    @if($testimonial->avatar && file_exists(public_path('storage/'.$testimonial->avatar)))
                        <div class="mb-4">
                            <img src="{{ $testimonial->avatar_url }}" alt="Preview" class="w-20 h-20 object-cover rounded-full border border-gray-200">
                        </div>
                    @endif
                    <input type="file" name="avatar" accept="image/*" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-forest-50 file:text-forest-700 hover:file:bg-forest-100">
                    <p class="text-xs text-gray-500 mt-2">Rasio 1:1 disarankan. Jika kosong, akan menggunakan inisial nama.</p>
                </div>

                <div class="bg-gray-50 p-4 rounded-xl border border-gray-100">
                    <label class="flex items-center gap-3 cursor-pointer">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', $testimonial->exists ? $testimonial->is_active : true) ? 'checked' : '' }} class="w-5 h-5 rounded text-forest-600 focus:ring-forest-500 border-gray-300">
                        <div>
                            <div class="font-medium text-gray-900">Tampilkan Testimoni</div>
                            <div class="text-sm text-gray-500">Tampilkan di halaman depan website.</div>
                        </div>
                    </label>
                </div>
            </div>

            <div class="mt-8 pt-6 border-t border-gray-100 flex items-center justify-end gap-3">
                <a href="{{ route('admin.testimonials.index') }}" class="px-6 py-2.5 bg-white border border-gray-300 text-gray-700 font-medium rounded-xl hover:bg-gray-50 transition-colors">Batal</a>
                <button type="submit" class="px-6 py-2.5 bg-forest-600 text-white font-medium rounded-xl hover:bg-forest-700 transition-colors shadow-sm flex items-center gap-2">
                    <i data-lucide="save" class="w-4 h-4"></i> Simpan Testimoni
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
