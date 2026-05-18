@extends('layouts.admin')

@section('title', 'Pengaturan Website')

@section('content')
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="px-6 py-5 border-b border-gray-100 flex items-center justify-between bg-gray-50/50">
        <h2 class="font-bold text-gray-800 text-lg flex items-center gap-2">
            <i data-lucide="settings" class="w-5 h-5 text-forest-600"></i>
            Konfigurasi Umum
        </h2>
    </div>

    <form action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data" class="flex flex-col md:flex-row">
        @csrf
        
        {{-- Tabs Sidebar --}}
        <div class="w-full md:w-64 border-r border-gray-100 p-4 flex flex-col gap-1 bg-gray-50/30">
            @foreach($settings as $group => $groupSettings)
                <button type="button" class="tab-btn w-full text-left px-4 py-3 rounded-xl font-medium text-sm transition-all {{ $loop->first ? 'bg-forest-50 text-forest-700 shadow-sm border border-forest-100' : 'text-gray-600 hover:bg-gray-100' }}" data-target="group-{{ $group }}">
                    {{ ucfirst($group) }}
                </button>
            @endforeach
        </div>
        
        {{-- Tabs Content --}}
        <div class="flex-1 p-6">
            @foreach($settings as $group => $groupSettings)
                <div id="group-{{ $group }}" class="tab-content {{ !$loop->first ? 'hidden' : '' }}">
                    <h3 class="text-xl font-bold text-gray-800 mb-6 pb-4 border-b border-gray-100 capitalize">Pengaturan {{ $group }}</h3>
                    
                    <div class="space-y-6 max-w-2xl">
                        @foreach($groupSettings as $setting)
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">{{ $setting->label ?? ucwords(str_replace('_', ' ', $setting->key)) }}</label>
                                
                                @if($setting->type === 'textarea')
                                    <textarea name="settings[{{ $setting->key }}]" rows="4" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-forest-500 focus:ring-forest-500 px-4 py-2 border">{{ $setting->value }}</textarea>
                                @elseif($setting->type === 'image')
                                    <div class="flex items-start gap-4">
                                        @if($setting->value)
                                            <div class="w-24 h-24 rounded-xl overflow-hidden border border-gray-200 bg-gray-50 flex-shrink-0 shadow-sm">
                                                <img src="{{ $setting->value }}" alt="{{ $setting->label }}" class="w-full h-full object-cover">
                                            </div>
                                        @endif
                                        <div class="flex-1">
                                            <input type="file" name="settings_files[{{ $setting->key }}]" accept="image/*" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-forest-50 file:text-forest-700 hover:file:bg-forest-100 cursor-pointer">
                                            <p class="text-xs text-gray-400 mt-2">Pilih file gambar untuk mengganti. Format yang didukung: JPG, JPEG, PNG, WEBP.</p>
                                        </div>
                                    </div>
                                @elseif($setting->type === 'select')
                                    <select name="settings[{{ $setting->key }}]" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-forest-500 focus:ring-forest-500 px-4 py-2 border bg-white">
                                        @if($setting->key === 'about_video_type')
                                            <option value="link" {{ $setting->value === 'link' ? 'selected' : '' }}>Link URL (YouTube / Google Drive / Direct Link)</option>
                                            <option value="upload" {{ $setting->value === 'upload' ? 'selected' : '' }}>Unggah File Video</option>
                                        @elseif($setting->key === 'weather_mode')
                                            <option value="auto" {{ $setting->value === 'auto' ? 'selected' : '' }}>Otomatis (Mengikuti Prakiraan Cuaca Internet)</option>
                                            <option value="manual" {{ $setting->value === 'manual' ? 'selected' : '' }}>Manual / Darurat (Override Menggunakan Input di Bawah)</option>
                                        @elseif($setting->key === 'weather_manual_icon')
                                            <option value="alert-triangle" {{ $setting->value === 'alert-triangle' ? 'selected' : '' }}>Segitiga Peringatan ⚠️ (Bahaya / Tutup)</option>
                                            <option value="x-circle" {{ $setting->value === 'x-circle' ? 'selected' : '' }}>Tanda X Merah ❌ (Tutup)</option>
                                            <option value="info" {{ $setting->value === 'info' ? 'selected' : '' }}>Informasi ℹ️ (Pengumuman)</option>
                                            <option value="sun" {{ $setting->value === 'sun' ? 'selected' : '' }}>Matahari ☀️ (Cerah)</option>
                                            <option value="cloud-rain" {{ $setting->value === 'cloud-rain' ? 'selected' : '' }}>Awan Hujan 🌧️ (Hujan)</option>
                                        @endif
                                    </select>
                                @elseif($setting->type === 'video')
                                    <div class="flex flex-col gap-2">
                                        @if($setting->value)
                                            <div class="w-full max-w-md rounded-xl overflow-hidden border border-gray-200 bg-gray-50 p-2 shadow-sm mb-2">
                                                <video src="{{ $setting->value }}" controls class="w-full rounded-lg max-h-48 object-cover"></video>
                                                <p class="text-[10px] text-gray-500 mt-1 truncate px-2 font-mono">Berkas: {{ basename($setting->value) }}</p>
                                            </div>
                                        @endif
                                        <input type="file" name="settings_files[{{ $setting->key }}]" accept="video/*" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-forest-50 file:text-forest-700 hover:file:bg-forest-100 cursor-pointer">
                                        <p class="text-xs text-gray-400 mt-2">Pilih file video untuk diunggah. Format yang didukung: MP4, WebM, OGG (Maks. 50MB).</p>
                                    </div>
                                @elseif($setting->type === 'features')
                                    <div class="space-y-4 font-sans" id="features-container">
                                        @php
                                            $features = json_decode($setting->value, true) ?: [];
                                        @endphp
                                        
                                        <div id="features-list" class="space-y-3">
                                            @foreach($features as $index => $feature)
                                                <div class="feature-row flex items-center gap-4 bg-gray-50/50 p-4 rounded-xl border border-gray-100">
                                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 flex-1">
                                                        <div>
                                                            <label class="block text-xs font-bold text-gray-500 mb-1">Judul Fitur (Kiri)</label>
                                                            <input type="text" name="settings[{{ $setting->key }}][{{ $index }}][title]" value="{{ $feature['title'] }}" placeholder="Contoh: Aman & Nyaman" class="w-full rounded-xl border-gray-200 shadow-sm focus:border-forest-500 focus:ring-forest-500 text-sm px-4 py-2 border bg-white">
                                                        </div>
                                                        <div>
                                                            <label class="block text-xs font-bold text-gray-500 mb-1">Deskripsi Fitur (Tengah)</label>
                                                            <input type="text" name="settings[{{ $setting->key }}][{{ $index }}][desc]" value="{{ $feature['desc'] }}" placeholder="Contoh: Jalur terkelola baik" class="w-full rounded-xl border-gray-200 shadow-sm focus:border-forest-500 focus:ring-forest-500 text-sm px-4 py-2 border bg-white">
                                                        </div>
                                                        <div>
                                                            <label class="block text-xs font-bold text-gray-500 mb-1">Ikon Lucide (Kanan) <a href="https://lucide.dev/icons" target="_blank" class="text-forest-600 underline font-normal ml-1">Galeri ↗</a></label>
                                                            <input type="text" name="settings[{{ $setting->key }}][{{ $index }}][icon]" value="{{ $feature['icon'] ?? ($index % 2 === 0 ? 'shield-check' : 'camera') }}" placeholder="Contoh: shield-check, camera, map-pin" class="w-full rounded-xl border-gray-200 shadow-sm focus:border-forest-500 focus:ring-forest-500 text-sm px-4 py-2 border bg-white">
                                                        </div>
                                                    </div>
                                                    <button type="button" class="remove-feature-btn self-end w-10 h-10 rounded-xl bg-red-50 hover:bg-red-100 flex items-center justify-center text-red-600 transition-colors border border-red-100 cursor-pointer">
                                                        <i data-lucide="trash-2" class="w-5 h-5"></i>
                                                    </button>
                                                </div>
                                            @endforeach
                                        </div>
                                        
                                        <button type="button" id="add-feature-btn" class="mt-2 inline-flex items-center gap-2 px-4 py-2.5 bg-forest-50 text-forest-700 hover:bg-forest-100 text-sm font-semibold rounded-xl border border-forest-100 transition-all cursor-pointer">
                                            <i data-lucide="plus" class="w-4 h-4"></i>
                                            Tambah Fitur Baru
                                        </button>
                                    </div>
                                @else
                                    <input type="text" name="settings[{{ $setting->key }}]" value="{{ $setting->value }}" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-forest-500 focus:ring-forest-500 px-4 py-2 border">
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
            
            <div class="mt-8 pt-6 border-t border-gray-100">
                <button type="submit" class="px-6 py-2.5 bg-forest-600 text-white font-medium rounded-xl hover:bg-forest-700 transition-colors shadow-sm flex items-center gap-2">
                    <i data-lucide="save" class="w-4 h-4"></i> Simpan Pengaturan
                </button>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
    document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            // Remove active state from all buttons
            document.querySelectorAll('.tab-btn').forEach(b => {
                b.className = 'tab-btn w-full text-left px-4 py-3 rounded-xl font-medium text-sm transition-all text-gray-600 hover:bg-gray-100';
            });
            
            // Add active state to clicked button
            btn.className = 'tab-btn w-full text-left px-4 py-3 rounded-xl font-medium text-sm transition-all bg-forest-50 text-forest-700 shadow-sm border border-forest-100';
            
            // Hide all content
            document.querySelectorAll('.tab-content').forEach(content => {
                content.classList.add('hidden');
            });
            
            // Show target content
            const targetId = btn.getAttribute('data-target');
            document.getElementById(targetId).classList.remove('hidden');
        });
    });

    // Dynamic About Features Row Builder
    const featuresList = document.getElementById('features-list');
    const addFeatureBtn = document.getElementById('add-feature-btn');
    
    if (featuresList && addFeatureBtn) {
        let featureIndex = {{ count(json_decode($settings->get('about')?->firstWhere('key', 'about_features')?->value ?? '[]', true) ?: []) }};
        
        addFeatureBtn.addEventListener('click', () => {
            const row = document.createElement('div');
            row.className = 'feature-row flex items-center gap-4 bg-gray-50/50 p-4 rounded-xl border border-gray-100';
            row.innerHTML = `
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 flex-1">
                    <div>
                        <label class="block text-xs font-bold text-gray-500 mb-1">Judul Fitur (Kiri)</label>
                        <input type="text" name="settings[about_features][${featureIndex}][title]" placeholder="Contoh: Aman & Nyaman" class="w-full rounded-xl border-gray-200 shadow-sm focus:border-forest-500 focus:ring-forest-500 text-sm px-4 py-2 border bg-white">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-500 mb-1">Deskripsi Fitur (Tengah)</label>
                        <input type="text" name="settings[about_features][${featureIndex}][desc]" placeholder="Contoh: Jalur terkelola baik" class="w-full rounded-xl border-gray-200 shadow-sm focus:border-forest-500 focus:ring-forest-500 text-sm px-4 py-2 border bg-white">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-500 mb-1">Ikon Lucide (Kanan) <a href="https://lucide.dev/icons" target="_blank" class="text-forest-600 underline font-normal ml-1">Galeri ↗</a></label>
                        <input type="text" name="settings[about_features][${featureIndex}][icon]" placeholder="Contoh: shield-check, camera, map-pin" class="w-full rounded-xl border-gray-200 shadow-sm focus:border-forest-500 focus:ring-forest-500 text-sm px-4 py-2 border bg-white">
                    </div>
                </div>
                <button type="button" class="remove-feature-btn self-end w-10 h-10 rounded-xl bg-red-50 hover:bg-red-100 flex items-center justify-center text-red-600 transition-colors border border-red-100 cursor-pointer">
                    <i data-lucide="trash-2" class="w-5 h-5"></i>
                </button>
            `;
            featuresList.appendChild(row);
            featureIndex++;
            
            // Re-initialize Lucide icons for the newly added button
            if (typeof lucide !== 'undefined') {
                lucide.createIcons();
            }
        });
        
        // Event delegation for removal
        featuresList.addEventListener('click', (e) => {
            const removeBtn = e.target.closest('.remove-feature-btn');
            if (removeBtn) {
                const row = removeBtn.closest('.feature-row');
                if (row) {
                    row.remove();
                }
            }
        });
    }
</script>
@endpush
@endsection
