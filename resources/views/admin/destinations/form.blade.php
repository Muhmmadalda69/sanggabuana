@extends('layouts.admin')

@section('title', $destination->exists ? 'Edit Destinasi' : 'Tambah Destinasi')

@section('content')
<div class="max-w-4xl">
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-100 flex items-center justify-between bg-gray-50/50">
            <h2 class="font-bold text-gray-800 text-lg flex items-center gap-2">
                <i data-lucide="{{ $destination->exists ? 'edit-3' : 'plus-circle' }}" class="w-5 h-5 text-forest-600"></i>
                {{ $destination->exists ? 'Edit Destinasi: ' . $destination->name : 'Tambah Destinasi Baru' }}
            </h2>
            <a href="{{ route('admin.destinations.index') }}" class="text-sm font-medium text-gray-500 hover:text-gray-700 flex items-center gap-1">
                <i data-lucide="arrow-left" class="w-4 h-4"></i> Kembali
            </a>
        </div>

        <form action="{{ $destination->exists ? route('admin.destinations.update', $destination->id) : route('admin.destinations.store') }}" method="POST" enctype="multipart/form-data" class="p-6">
            @csrf
            @if($destination->exists)
                @method('PUT')
            @endif

            <div class="space-y-8">
                {{-- Basic Info --}}
                <div>
                    <h3 class="text-sm font-bold text-gray-900 uppercase tracking-wider mb-4 pb-2 border-b border-gray-100">Informasi Dasar</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nama Destinasi <span class="text-red-500">*</span></label>
                            <input type="text" name="name" value="{{ old('name', $destination->name) }}" required class="w-full rounded-xl border-gray-300 shadow-sm focus:border-forest-500 focus:ring-forest-500 px-4 py-2 border">
                        </div>
                        
                        <div class="col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi Singkat</label>
                            <textarea name="short_description" rows="2" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-forest-500 focus:ring-forest-500 px-4 py-2 border">{{ old('short_description', $destination->short_description) }}</textarea>
                            <p class="text-xs text-gray-500 mt-1">Tampil di card daftar destinasi.</p>
                        </div>
                        
                        <div class="col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Deskripsi Lengkap</label>
                            <textarea name="description" id="summernote" rows="6" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-forest-500 focus:ring-forest-500 px-4 py-2 border">{{ old('description', $destination->description) }}</textarea>
                        </div>
                    </div>
                </div>

                {{-- Detail Info --}}
                <div>
                    <h3 class="text-sm font-bold text-gray-900 uppercase tracking-wider mb-4 pb-2 border-b border-gray-100">Detail Spesifik</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Lokasi</label>
                            <input type="text" name="location" value="{{ old('location', $destination->location) }}" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-forest-500 focus:ring-forest-500 px-4 py-2 border" placeholder="Contoh: Desa Tegalwaru">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Ketinggian</label>
                            <input type="text" name="altitude" value="{{ old('altitude', $destination->altitude) }}" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-forest-500 focus:ring-forest-500 px-4 py-2 border" placeholder="Contoh: 1.291 mdpl">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Hari Operasional</label>
                            <input type="text" name="operational_days" value="{{ old('operational_days', $destination->operational_days) }}" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-forest-500 focus:ring-forest-500 px-4 py-2 border" placeholder="Contoh: Senin - Minggu">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Jam Operasional</label>
                            <input type="text" name="operational_hours" value="{{ old('operational_hours', $destination->operational_hours) }}" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-forest-500 focus:ring-forest-500 px-4 py-2 border" placeholder="Contoh: 08:00 - 17:00">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Harga Tiket (Rp)</label>
                            <input type="number" name="price" value="{{ old('price', $destination->price ? (int)$destination->price : 0) }}" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-forest-500 focus:ring-forest-500 px-4 py-2 border">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Kuota Harian <span class="text-xs text-gray-400 font-normal">(Kosongkan = tidak terbatas)</span></label>
                            <input type="number" name="daily_quota" min="1" value="{{ old('daily_quota', $destination->daily_quota) }}" placeholder="Contoh: 100" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-forest-500 focus:ring-forest-500 px-4 py-2 border">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Diskon Anak-anak <span class="text-xs text-gray-400 font-normal">(% dari harga tiket, kosongkan = tidak ada diskon)</span></label>
                            <div class="relative">
                                <input type="number" name="kids_discount" min="0" max="100" value="{{ old('kids_discount', $destination->kids_discount) }}" placeholder="Contoh: 50" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-forest-500 focus:ring-forest-500 px-4 py-2 pr-10 border">
                                <span class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 font-bold text-sm pointer-events-none">%</span>
                            </div>
                            <p class="text-xs text-gray-400 mt-1">Misal: isi 50 → anak-anak bayar 50% dari harga tiket.</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Estimasi Waktu/Durasi</label>
                            <input type="text" name="duration" value="{{ old('duration', $destination->duration) }}" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-forest-500 focus:ring-forest-500 px-4 py-2 border" placeholder="Contoh: 4-5 jam">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Urutan Tampil</label>
                            <input type="number" name="sort_order" value="{{ old('sort_order', $destination->sort_order ?? 0) }}" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-forest-500 focus:ring-forest-500 px-4 py-2 border">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Garis Lintang (Latitude)</label>
                            <input type="text" name="latitude" value="{{ old('latitude', $destination->latitude) }}" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-forest-500 focus:ring-forest-500 px-4 py-2 border" placeholder="Contoh: -6.7275">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Garis Bujur (Longitude)</label>
                            <input type="text" name="longitude" value="{{ old('longitude', $destination->longitude) }}" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-forest-500 focus:ring-forest-500 px-4 py-2 border" placeholder="Contoh: 107.0394">
                        </div>
                    </div>
                </div>

                {{-- Konfigurasi Input Loket (POS) --}}
                @if(auth()->user()->isSuperAdmin() || auth()->user()->isAdmin())
                <div>
                    <h3 class="text-sm font-bold text-gray-900 uppercase tracking-wider mb-4 pb-2 border-b border-gray-100 flex items-center gap-2">
                        <i data-lucide="sliders-horizontal" class="w-4 h-4 text-forest-600"></i>
                        Konfigurasi Input Formulir Loket (POS)
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="bg-gray-50/50 p-4 rounded-xl border border-gray-100 flex flex-col justify-between">
                            <label class="flex items-start gap-3 cursor-pointer">
                                <input type="checkbox" name="has_community" value="1" {{ old('has_community', $destination->has_community) ? 'checked' : '' }} class="w-5 h-5 mt-0.5 rounded text-forest-600 focus:ring-forest-500 border-gray-300">
                                <div>
                                    <div class="font-semibold text-gray-900 text-sm">Input Komunitas</div>
                                    <div class="text-xs text-gray-500 mt-1">Aktifkan isian "Nama Komunitas (Opsional)" pada form loket destinasi ini.</div>
                                </div>
                            </label>
                        </div>

                        <div class="bg-gray-50/50 p-4 rounded-xl border border-gray-100 flex flex-col justify-between">
                            <label class="flex items-start gap-3 cursor-pointer">
                                <input type="checkbox" name="has_purpose" value="1" {{ old('has_purpose', $destination->has_purpose) ? 'checked' : '' }} class="w-5 h-5 mt-0.5 rounded text-forest-600 focus:ring-forest-500 border-gray-300">
                                <div>
                                    <div class="font-semibold text-gray-900 text-sm">Tujuan Kunjungan</div>
                                    <div class="text-xs text-gray-500 mt-1">Aktifkan dropdown "Tujuan Kunjungan" (Hiking, Trail Run, Ziarah).</div>
                                </div>
                            </label>
                        </div>

                        <div class="bg-gray-50/50 p-4 rounded-xl border border-gray-100 flex flex-col justify-between">
                            <label class="flex items-start gap-3 cursor-pointer">
                                <input type="checkbox" name="has_member_details" value="1" {{ old('has_member_details', $destination->has_member_details) ? 'checked' : '' }} class="w-5 h-5 mt-0.5 rounded text-forest-600 focus:ring-forest-500 border-gray-300">
                                <div>
                                    <div class="font-semibold text-gray-900 text-sm">Detail Anggota</div>
                                    <div class="text-xs text-gray-500 mt-1">Aktifkan penginputan detail nama, alamat, email, usia, dan jenis kelamin untuk setiap anggota (satu tiket per orang).</div>
                                </div>
                            </label>
                        </div>

                        <div class="bg-gray-50/50 p-4 rounded-xl border border-gray-100 flex flex-col justify-between">
                            <label class="flex items-start gap-3 cursor-pointer">
                                <input type="checkbox" name="has_online_registration" value="1" {{ old('has_online_registration', $destination->has_online_registration) ? 'checked' : '' }} class="w-5 h-5 mt-0.5 rounded text-forest-600 focus:ring-forest-500 border-gray-300">
                                <div>
                                    <div class="font-semibold text-gray-900 text-sm">Registrasi Online</div>
                                    <div class="text-xs text-gray-500 mt-1">Aktifkan QR Code dan tombol registrasi online mandiri di halaman detail destinasi.</div>
                                </div>
                            </label>
                        </div>
                    </div>
                </div>
                @endif

                {{-- Kontak --}}
                <div>
                    <h3 class="text-sm font-bold text-gray-900 uppercase tracking-wider mb-4 pb-2 border-b border-gray-100 flex items-center justify-between">
                        <span class="flex items-center gap-2">
                            <i data-lucide="phone" class="w-4 h-4 text-forest-600"></i>
                            Kontak & Sosial Media Wisata
                        </span>
                        <button type="button" id="btn-add-contact" class="text-xs bg-forest-50 text-forest-700 hover:bg-forest-100 font-semibold px-3 py-1.5 rounded-lg flex items-center gap-1 transition-colors border border-forest-100">
                            <i data-lucide="plus" class="w-3.5 h-3.5"></i> Tambah Kontak
                        </button>
                    </h3>
                    
                    <div id="contacts-container" class="space-y-3">
                        @php
                            $contacts = old('contacts', $destination->contacts ?? []);
                        @endphp
                        
                        @forelse($contacts as $index => $contact)
                            <div class="contact-row grid grid-cols-1 md:grid-cols-12 gap-4 items-center bg-gray-50/50 p-4 rounded-xl border border-gray-100 transition-all duration-200">
                                <div class="md:col-span-4">
                                    <label class="block text-xs font-medium text-gray-500 mb-1">Platform</label>
                                    <select name="contacts[{{ $index }}][platform]" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-forest-500 focus:ring-forest-500 px-4 py-2 border bg-white">
                                        <option value="whatsapp" {{ ($contact['platform'] ?? '') == 'whatsapp' ? 'selected' : '' }}>WhatsApp</option>
                                        <option value="instagram" {{ ($contact['platform'] ?? '') == 'instagram' ? 'selected' : '' }}>Instagram</option>
                                        <option value="tiktok" {{ ($contact['platform'] ?? '') == 'tiktok' ? 'selected' : '' }}>TikTok</option>
                                        <option value="facebook" {{ ($contact['platform'] ?? '') == 'facebook' ? 'selected' : '' }}>Facebook</option>
                                        <option value="youtube" {{ ($contact['platform'] ?? '') == 'youtube' ? 'selected' : '' }}>YouTube</option>
                                        <option value="phone" {{ ($contact['platform'] ?? '') == 'phone' ? 'selected' : '' }}>Telepon/HP</option>
                                        <option value="email" {{ ($contact['platform'] ?? '') == 'email' ? 'selected' : '' }}>Email</option>
                                        <option value="website" {{ ($contact['platform'] ?? '') == 'website' ? 'selected' : '' }}>Website</option>
                                        <option value="other" {{ ($contact['platform'] ?? '') == 'other' ? 'selected' : '' }}>Lainnya</option>
                                    </select>
                                </div>
                                <div class="md:col-span-7">
                                    <label class="block text-xs font-medium text-gray-500 mb-1">Username / No. HP / Link</label>
                                    <input type="text" name="contacts[{{ $index }}][value]" value="{{ $contact['value'] ?? '' }}" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-forest-500 focus:ring-forest-500 px-4 py-2 border bg-white" placeholder="Contoh: 08123456789 atau @wisatasanggabuana">
                                </div>
                                <div class="md:col-span-1 flex justify-end md:justify-center pt-5">
                                    <button type="button" class="btn-remove-contact text-red-500 hover:text-red-700 bg-red-50 hover:bg-red-100 p-2.5 rounded-xl border border-red-100 transition-colors" title="Hapus Kontak">
                                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                                    </button>
                                </div>
                            </div>
                        @empty
                            <div class="no-contacts-alert text-center py-8 bg-gray-50/50 rounded-xl border border-dashed border-gray-200">
                                <i data-lucide="phone-off" class="w-8 h-8 text-gray-400 mx-auto mb-2"></i>
                                <p class="text-sm font-medium text-gray-500">Belum ada kontak yang ditambahkan.</p>
                                <p class="text-xs text-gray-400 mt-0.5">Klik tombol "Tambah Kontak" di atas untuk menambahkan.</p>
                            </div>
                        @endforelse
                    </div>
                </div>

                {{-- Media & Status --}}
                <div>
                    <h3 class="text-sm font-bold text-gray-900 uppercase tracking-wider mb-4 pb-2 border-b border-gray-100">Media & Status</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Foto Utama</label>
                            @if($destination->image)
                                <div class="mb-4">
                                    <img src="{{ $destination->image_url }}" alt="Preview" class="w-full h-48 object-cover rounded-xl border border-gray-200">
                                </div>
                            @endif
                            <input type="file" name="image" accept="image/*" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-forest-50 file:text-forest-700 hover:file:bg-forest-100">
                            <p class="text-xs text-gray-500 mt-2">Format: JPG, PNG, WEBP. Maks: 5MB.</p>
                        </div>
                        
                        <div class="space-y-4">
                            <div class="bg-gray-50 p-4 rounded-xl border border-gray-100">
                                <label class="flex items-center gap-3 cursor-pointer">
                                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', $destination->exists ? $destination->is_active : true) ? 'checked' : '' }} class="w-5 h-5 rounded text-forest-600 focus:ring-forest-500 border-gray-300">
                                    <div>
                                        <div class="font-medium text-gray-900">Aktifkan Destinasi</div>
                                        <div class="text-sm text-gray-500">Tampilkan destinasi ini di website publik.</div>
                                    </div>
                                </label>
                            </div>
                            
                            <div class="bg-gray-50 p-4 rounded-xl border border-gray-100">
                                <label class="flex items-center gap-3 cursor-pointer">
                                    <input type="checkbox" name="is_featured" value="1" {{ old('is_featured', $destination->is_featured) ? 'checked' : '' }} class="w-5 h-5 rounded text-forest-600 focus:ring-forest-500 border-gray-300">
                                    <div>
                                        <div class="font-medium text-gray-900">Jadikan Unggulan</div>
                                        <div class="text-sm text-gray-500">Tampilkan di halaman depan (Home).</div>
                                    </div>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-8 pt-6 border-t border-gray-100 flex items-center justify-end gap-3">
                <a href="{{ route('admin.destinations.index') }}" class="px-6 py-2.5 bg-white border border-gray-300 text-gray-700 font-medium rounded-xl hover:bg-gray-50 transition-colors">Batal</a>
                <button type="submit" class="px-6 py-2.5 bg-forest-600 text-white font-medium rounded-xl hover:bg-forest-700 transition-colors shadow-sm flex items-center gap-2">
                    <i data-lucide="save" class="w-4 h-4"></i> Simpan Destinasi
                </button>
            </div>
        </form>
    </div>
</div>

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/summernote@0.9.0/dist/summernote-lite.min.css" rel="stylesheet">
<style>
    .note-editor.note-frame {
        border: 1px solid #e5e7eb !important;
        border-radius: 16px !important;
        overflow: hidden !important;
        box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05) !important;
        font-family: inherit !important;
    }
    .note-editor .note-toolbar {
        background-color: #f9fafb !important;
        border-bottom: 1px solid #e5e7eb !important;
        padding: 8px !important;
    }
    .note-btn {
        border: 1px solid #e5e7eb !important;
        background-color: #ffffff !important;
        border-radius: 6px !important;
        padding: 4px 8px !important;
        color: #374151 !important;
    }
    .note-btn:hover {
        background-color: #f3f4f6 !important;
    }
    .note-modal-content {
        border-radius: 16px !important;
        overflow: hidden !important;
        border: none !important;
        box-shadow: 0 20px 25px -5px rgb(0 0 0 / 0.1), 0 8px 10px -6px rgb(0 0 0 / 0.1) !important;
    }
    .note-modal-header {
        border-bottom: 1px solid #e5e7eb !important;
        padding: 16px !important;
    }
    .note-modal-body {
        padding: 16px !important;
    }
    .note-modal-footer {
        border-top: 1px solid #e5e7eb !important;
        padding: 16px !important;
    }
    .note-form-label {
        font-weight: 500 !important;
        color: #374151 !important;
        margin-bottom: 4px !important;
    }
    .note-input {
        border: 1px solid #d1d5db !important;
        border-radius: 8px !important;
        padding: 6px 12px !important;
        width: 100% !important;
    }
    .note-input:focus {
        border-color: #15803d !important;
        outline: none !important;
    }
</style>
@endpush

@push('scripts')
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/summernote@0.9.0/dist/summernote-lite.min.js"></script>
<script>
    $(document).ready(function() {
        $('#summernote').summernote({
            placeholder: 'Tulis deskripsi lengkap destinasi wisata di sini...',
            tabsize: 2,
            height: 400,
            toolbar: [
                ['style', ['style']],
                ['font', ['bold', 'underline', 'strikethrough', 'clear']],
                ['fontname', ['fontname']],
                ['color', ['color']],
                ['para', ['ul', 'ol', 'paragraph']],
                ['table', ['table']],
                ['insert', ['link', 'picture', 'video', 'hr']],
                ['view', ['fullscreen', 'codeview', 'help']]
            ],
            styleTags: [
                'p', 'blockquote', 'pre', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6'
            ]
        });

        // Contact Dynamic Management
        let contactIndex = {{ count($contacts) }};
        
        $('#btn-add-contact').on('click', function() {
            $('.no-contacts-alert').remove();
            
            const newRow = `
                <div class="contact-row grid grid-cols-1 md:grid-cols-12 gap-4 items-center bg-gray-50/50 p-4 rounded-xl border border-gray-100 opacity-0 transform translate-y-2 transition-all duration-300">
                    <div class="md:col-span-4">
                        <label class="block text-xs font-medium text-gray-500 mb-1">Platform</label>
                        <select name="contacts[${contactIndex}][platform]" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-forest-500 focus:ring-forest-500 px-4 py-2 border bg-white">
                            <option value="whatsapp">WhatsApp</option>
                            <option value="instagram">Instagram</option>
                            <option value="tiktok">TikTok</option>
                            <option value="facebook">Facebook</option>
                            <option value="youtube">YouTube</option>
                            <option value="phone">Telepon/HP</option>
                            <option value="email">Email</option>
                            <option value="website">Website</option>
                            <option value="other">Lainnya</option>
                        </select>
                    </div>
                    <div class="md:col-span-7">
                        <label class="block text-xs font-medium text-gray-500 mb-1">Username / No. HP / Link</label>
                        <input type="text" name="contacts[${contactIndex}][value]" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-forest-500 focus:ring-forest-500 px-4 py-2 border bg-white" placeholder="Contoh: 08123456789 atau @wisatasanggabuana">
                    </div>
                    <div class="md:col-span-1 flex justify-end md:justify-center pt-5">
                        <button type="button" class="btn-remove-contact text-red-500 hover:text-red-700 bg-red-50 hover:bg-red-100 p-2.5 rounded-xl border border-red-100 transition-colors" title="Hapus Kontak">
                            <i data-lucide="trash-2" class="w-4 h-4"></i>
                        </button>
                    </div>
                </div>
            `;
            
            const $row = $(newRow);
            $('#contacts-container').append($row);
            
            // Animate fade-in and slide-up
            setTimeout(() => {
                $row.removeClass('opacity-0 translate-y-2');
            }, 10);

            contactIndex++;
            if (typeof lucide !== 'undefined') {
                lucide.createIcons();
            }
        });
        
        $('#contacts-container').on('click', '.btn-remove-contact', function() {
            const $row = $(this).closest('.contact-row');
            
            // Animate out
            $row.addClass('opacity-0 scale-95 transition-all duration-200');
            setTimeout(() => {
                $row.remove();
                
                if ($('#contacts-container').children('.contact-row').length === 0) {
                    $('#contacts-container').append(`
                        <div class="no-contacts-alert text-center py-8 bg-gray-50/50 rounded-xl border border-dashed border-gray-200">
                            <i data-lucide="phone-off" class="w-8 h-8 text-gray-400 mx-auto mb-2"></i>
                            <p class="text-sm font-medium text-gray-500">Belum ada kontak yang ditambahkan.</p>
                            <p class="text-xs text-gray-400 mt-0.5">Klik tombol "Tambah Kontak" di atas untuk menambahkan.</p>
                        </div>
                    `);
                    if (typeof lucide !== 'undefined') {
                        lucide.createIcons();
                    }
                }
            }, 200);
        });
    });
</script>
@endpush
@endsection
