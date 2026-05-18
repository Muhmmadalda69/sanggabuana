@extends('layouts.admin')

@section('title', $page->exists ? 'Edit Halaman' : 'Tambah Halaman')

@section('content')
<div class="max-w-4xl">
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-100 flex items-center justify-between bg-gray-50/50">
            <h2 class="font-bold text-gray-800 text-lg flex items-center gap-2">
                <i data-lucide="{{ $page->exists ? 'edit-3' : 'file-plus' }}" class="w-5 h-5 text-forest-600"></i>
                {{ $page->exists ? 'Edit Halaman: ' . $page->title : 'Buat Halaman Baru' }}
            </h2>
            <a href="{{ route('admin.pages.index') }}" class="text-sm font-medium text-gray-500 hover:text-gray-700 flex items-center gap-1">
                <i data-lucide="arrow-left" class="w-4 h-4"></i> Kembali
            </a>
        </div>

        <form action="{{ $page->exists ? route('admin.pages.update', $page->id) : route('admin.pages.store') }}" method="POST" class="p-6">
            @csrf
            @if($page->exists)
                @method('PUT')
            @endif

            <div class="space-y-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Judul Halaman <span class="text-red-500">*</span></label>
                    <input type="text" name="title" value="{{ old('title', $page->title) }}" required class="w-full rounded-xl border-gray-300 shadow-sm focus:border-forest-500 focus:ring-forest-500 px-4 py-2 border text-lg font-semibold">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Konten Halaman</label>
                    <textarea name="content" id="summernote" rows="15" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-forest-500 focus:ring-forest-500 px-4 py-3 border text-sm leading-relaxed">{{ old('content', $page->content) }}</textarea>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Meta Deskripsi (Untuk SEO)</label>
                    <input type="text" name="meta_description" value="{{ old('meta_description', $page->meta_description) }}" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-forest-500 focus:ring-forest-500 px-4 py-2 border">
                </div>

                <div class="bg-gray-50 p-4 rounded-xl border border-gray-100">
                    <label class="flex items-center gap-3 cursor-pointer">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', $page->exists ? $page->is_active : true) ? 'checked' : '' }} class="w-5 h-5 rounded text-forest-600 focus:ring-forest-500 border-gray-300">
                        <div>
                            <div class="font-medium text-gray-900">Terbitkan Halaman</div>
                            <div class="text-sm text-gray-500">Halaman dapat diakses oleh publik.</div>
                        </div>
                    </label>
                </div>
            </div>

            <div class="mt-8 pt-6 border-t border-gray-100 flex items-center justify-end gap-3">
                <a href="{{ route('admin.pages.index') }}" class="px-6 py-2.5 bg-white border border-gray-300 text-gray-700 font-medium rounded-xl hover:bg-gray-50 transition-colors">Batal</a>
                <button type="submit" class="px-6 py-2.5 bg-forest-600 text-white font-medium rounded-xl hover:bg-forest-700 transition-colors shadow-sm flex items-center gap-2">
                    <i data-lucide="save" class="w-4 h-4"></i> Simpan Halaman
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
            placeholder: 'Tulis konten halaman secara lengkap di sini...',
            tabsize: 2,
            height: 450,
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
    });
</script>
@endpush
@endsection
