@extends('layouts.admin')

@section('title', 'Halaman CMS')

@section('content')
<div class="bg-white rounded-2xl shadow-sm border border-gray-100">
    <div class="px-6 py-5 border-b border-gray-100 flex items-center justify-between">
        <h2 class="font-bold text-gray-800 text-lg">Daftar Halaman</h2>
        <a href="{{ route('admin.pages.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-forest-600 text-white font-medium rounded-xl hover:bg-forest-700 transition-colors text-sm">
            <i data-lucide="plus" class="w-4 h-4"></i> Tambah Halaman
        </a>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-50/50">
                    <th class="px-6 py-4 font-semibold text-gray-500 text-sm border-b border-gray-100">Judul Halaman</th>
                    <th class="px-6 py-4 font-semibold text-gray-500 text-sm border-b border-gray-100">URL / Slug</th>
                    <th class="px-6 py-4 font-semibold text-gray-500 text-sm border-b border-gray-100">Status</th>
                    <th class="px-6 py-4 font-semibold text-gray-500 text-sm border-b border-gray-100 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($pages as $page)
                <tr class="hover:bg-gray-50/50 transition-colors">
                    <td class="px-6 py-4">
                        <div class="font-bold text-gray-800">{{ $page->title }}</div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="text-sm text-gray-500 flex items-center gap-2">
                            <span class="bg-gray-100 px-2 py-1 rounded text-gray-600">/halaman/{{ $page->slug }}</span>
                            <a href="{{ route('page.show', $page->slug) }}" target="_blank" class="text-forest-600 hover:text-forest-800" title="Buka URL">
                                <i data-lucide="external-link" class="w-3.5 h-3.5"></i>
                            </a>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium {{ $page->is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600' }}">
                            <span class="w-1.5 h-1.5 rounded-full {{ $page->is_active ? 'bg-green-500' : 'bg-gray-400' }}"></span>
                            {{ $page->is_active ? 'Aktif' : 'Draft' }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex items-center justify-end gap-2">
                            <a href="{{ route('admin.pages.edit', $page->id) }}" class="p-2 text-gray-400 hover:text-orange-600 bg-white border border-gray-200 rounded-lg shadow-sm hover:shadow transition-all" title="Edit">
                                <i data-lucide="edit-2" class="w-4 h-4"></i>
                            </a>
                            <form action="{{ route('admin.pages.destroy', $page->id) }}" method="POST" class="inline" onsubmit="return confirm('Hapus halaman ini?')">
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
                    <td colspan="4" class="px-6 py-12 text-center text-gray-500">
                        <i data-lucide="file-text" class="w-12 h-12 mx-auto text-gray-300 mb-3"></i>
                        <p class="text-lg font-medium text-gray-600">Belum ada halaman</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    @if($pages->hasPages())
    <div class="px-6 py-4 border-t border-gray-100">
        {{ $pages->links() }}
    </div>
    @endif
</div>
@endsection
