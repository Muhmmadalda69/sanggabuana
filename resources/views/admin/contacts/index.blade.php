@extends('layouts.admin')

@section('title', 'Pesan Pengunjung')

@section('content')
<div class="bg-white rounded-2xl shadow-sm border border-gray-100">
    <div class="px-6 py-5 border-b border-gray-100 flex items-center justify-between">
        <h2 class="font-bold text-gray-800 text-lg flex items-center gap-2">
            <i data-lucide="inbox" class="w-5 h-5 text-forest-600"></i>
            Kotak Masuk
        </h2>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-50/50">
                    <th class="px-6 py-4 font-semibold text-gray-500 text-sm border-b border-gray-100 w-12"></th>
                    <th class="px-6 py-4 font-semibold text-gray-500 text-sm border-b border-gray-100">Pengirim</th>
                    <th class="px-6 py-4 font-semibold text-gray-500 text-sm border-b border-gray-100">Subjek</th>
                    <th class="px-6 py-4 font-semibold text-gray-500 text-sm border-b border-gray-100">Tanggal</th>
                    <th class="px-6 py-4 font-semibold text-gray-500 text-sm border-b border-gray-100 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($contacts as $contact)
                <tr class="hover:bg-gray-50/50 transition-colors {{ !$contact->is_read ? 'bg-forest-50/30' : '' }}">
                    <td class="px-6 py-4 text-center">
                        @if(!$contact->is_read)
                            <div class="w-2.5 h-2.5 rounded-full bg-forest-500 mx-auto" title="Belum dibaca"></div>
                        @else
                            <i data-lucide="check-check" class="w-4 h-4 text-gray-300 mx-auto" title="Sudah dibaca"></i>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        <div class="font-bold text-gray-800 {{ !$contact->is_read ? 'text-forest-900' : '' }}">{{ $contact->name }}</div>
                        <div class="text-sm text-gray-500 flex items-center gap-1">
                            <i data-lucide="mail" class="w-3 h-3"></i> {{ $contact->email }}
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="font-medium text-gray-800 {{ !$contact->is_read ? 'text-forest-900' : '' }} mb-1">{{ $contact->subject }}</div>
                        <div class="text-sm text-gray-500 truncate max-w-xs">{{ $contact->message }}</div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="text-sm text-gray-800">{{ $contact->created_at->format('d M Y') }}</div>
                        <div class="text-xs text-gray-500">{{ $contact->created_at->format('H:i') }} WIB</div>
                    </td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex items-center justify-end gap-2">
                            <a href="{{ route('admin.contacts.show', $contact->id) }}" class="p-2 text-gray-400 hover:text-blue-600 bg-white border border-gray-200 rounded-lg shadow-sm hover:shadow transition-all" title="Baca Pesan">
                                <i data-lucide="eye" class="w-4 h-4"></i>
                            </a>
                            <form action="{{ route('admin.contacts.destroy', $contact->id) }}" method="POST" class="inline" onsubmit="return confirm('Hapus pesan ini?')">
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
                        <i data-lucide="inbox" class="w-12 h-12 mx-auto text-gray-300 mb-3"></i>
                        <p class="text-lg font-medium text-gray-600">Kotak masuk kosong</p>
                        <p class="text-sm">Belum ada pesan dari pengunjung.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    @if($contacts->hasPages())
    <div class="px-6 py-4 border-t border-gray-100">
        {{ $contacts->links() }}
    </div>
    @endif
</div>
@endsection
