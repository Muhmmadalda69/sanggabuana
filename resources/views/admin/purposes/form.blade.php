@extends('layouts.admin')

@section('title', $purpose->exists ? 'Edit Tujuan Kunjungan' : 'Tambah Tujuan Kunjungan Baru')

@section('content')
<div class="mb-6">
    <a href="{{ route('admin.purposes.index') }}" class="inline-flex items-center gap-2 text-sm text-gray-500 hover:text-forest-600 transition-colors font-medium">
        <i data-lucide="arrow-left" class="w-4 h-4"></i> Kembali ke Master Tujuan Kunjungan
    </a>
</div>

<div class="max-w bg-white rounded-2xl border border-gray-100 shadow-sm p-6 sm:p-8">
    <form action="{{ $purpose->exists ? route('admin.purposes.update', $purpose->id) : route('admin.purposes.store') }}" method="POST" class="space-y-6">
        @csrf
        @if($purpose->exists)
            @method('PUT')
        @endif

        <div class="space-y-6">
            {{-- Name --}}
            <div>
                <label for="name" class="block text-sm font-semibold text-gray-700 mb-2">Nama Tujuan Kunjungan</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-gray-400">
                        <i data-lucide="compass" class="w-5 h-5"></i>
                    </span>
                    <input type="text" name="name" id="name" value="{{ old('name', $purpose->name) }}" required placeholder="Contoh: Hiking, Trail Run, Ziarah..." class="w-full pl-12 pr-4 py-2.5 bg-gray-50/50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:border-forest-500 focus:bg-white transition-all">
                </div>
                @error('name')
                    <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- is_all_destinations (Checkbox) --}}
            <div class="bg-gray-50/50 p-4 rounded-xl border border-gray-100">
                <label class="flex items-start gap-3 cursor-pointer">
                    <input type="checkbox" name="is_all_destinations" id="is_all_destinations" value="1" {{ old('is_all_destinations', $purpose->exists ? $purpose->is_all_destinations : true) ? 'checked' : '' }} class="w-5 h-5 mt-0.5 rounded text-forest-600 focus:ring-forest-500 border-gray-300">
                    <div>
                        <div class="font-semibold text-gray-900 text-sm">Tampilkan di Semua Destinasi (All)</div>
                        <div class="text-xs text-gray-500 mt-1">Apabila diceklis, tujuan kunjungan ini otomatis aktif untuk seluruh destinasi yang ada dan yang akan dibuat.</div>
                    </div>
                </label>
            </div>

            {{-- Destination Mappings (Checklist) --}}
            <div id="destinations-mapping-container" class="space-y-3 hidden">
                <label class="block text-sm font-semibold text-gray-700">Pilih Destinasi Wisata</label>
                <p class="text-xs text-gray-400">Pilih destinasi mana saja yang dapat menampilkan opsi tujuan kunjungan ini.</p>
                
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3 pt-2">
                    @foreach($destinations as $dest)
                        @php
                            $isMapped = $purpose->exists && $purpose->destinations->contains($dest->id);
                        @endphp
                        <label class="flex items-center gap-3 p-3 bg-gray-50/30 border border-gray-150 rounded-xl hover:bg-gray-50 transition-colors cursor-pointer select-none">
                            <input type="checkbox" name="destinations[]" value="{{ $dest->id }}" {{ (is_array(old('destinations')) && in_array($dest->id, old('destinations'))) || $isMapped ? 'checked' : '' }} class="w-4 h-4 rounded text-forest-600 focus:ring-forest-500 border-gray-300">
                            <span class="text-xs font-semibold text-gray-750">{{ $dest->name }}</span>
                        </label>
                    @endforeach
                </div>
                @error('destinations')
                    <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div class="pt-4 border-t border-gray-100 flex items-center justify-end gap-3">
            <a href="{{ route('admin.purposes.index') }}" class="px-5 py-2.5 border border-gray-200 rounded-xl font-semibold text-sm text-gray-600 hover:bg-gray-50 transition-colors">
                Batal
            </a>
            <button type="submit" class="inline-flex items-center gap-2 bg-forest-600 hover:bg-forest-700 text-white px-6 py-2.5 rounded-xl font-semibold text-sm transition-colors shadow-sm">
                <i data-lucide="save" class="w-4.5 h-4.5"></i> Simpan Tujuan Kunjungan
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const allDestinationsCheckbox = document.getElementById('is_all_destinations');
        const mappingContainer = document.getElementById('destinations-mapping-container');

        function toggleMappingContainer() {
            if (allDestinationsCheckbox.checked) {
                mappingContainer.classList.add('hidden');
            } else {
                mappingContainer.classList.remove('hidden');
            }
        }

        // Initialize state
        toggleMappingContainer();

        // Listen to changes
        allDestinationsCheckbox.addEventListener('change', toggleMappingContainer);
    });
</script>
@endpush
@endsection
