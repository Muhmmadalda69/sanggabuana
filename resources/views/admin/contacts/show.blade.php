@extends('layouts.admin')

@section('title', 'Baca Pesan')

@section('content')
<div class="max-w-3xl">
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-100 flex items-center justify-between bg-gray-50/50">
            <h2 class="font-bold text-gray-800 text-lg flex items-center gap-2">
                <i data-lucide="mail-open" class="w-5 h-5 text-forest-600"></i>
                Detail Pesan
            </h2>
            <div class="flex items-center gap-3">
                <form action="{{ route('admin.contacts.destroy', $contact->id) }}" method="POST" onsubmit="return confirm('Hapus pesan ini?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="text-sm font-medium text-red-500 hover:text-red-700 flex items-center gap-1">
                        <i data-lucide="trash-2" class="w-4 h-4"></i> Hapus
                    </button>
                </form>
                <div class="w-px h-4 bg-gray-300"></div>
                <a href="{{ route('admin.contacts.index') }}" class="text-sm font-medium text-gray-500 hover:text-gray-700 flex items-center gap-1">
                    <i data-lucide="arrow-left" class="w-4 h-4"></i> Kembali
                </a>
            </div>
        </div>

        <div class="p-6 md:p-8">
            <div class="flex items-start justify-between mb-8 pb-6 border-b border-gray-100">
                <div class="flex items-center gap-4">
                    <div class="w-14 h-14 rounded-full bg-forest-100 text-forest-600 flex items-center justify-center font-bold text-xl">
                        {{ strtoupper(substr($contact->name, 0, 1)) }}
                    </div>
                    <div>
                        <h3 class="font-bold text-gray-900 text-lg">{{ $contact->name }}</h3>
                        <div class="flex flex-col sm:flex-row sm:items-center gap-1 sm:gap-4 text-sm text-gray-500 mt-1">
                            <a href="mailto:{{ $contact->email }}" class="flex items-center gap-1 hover:text-forest-600 transition-colors">
                                <i data-lucide="mail" class="w-4 h-4"></i> {{ $contact->email }}
                            </a>
                            @if($contact->phone)
                                <span class="hidden sm:inline text-gray-300">•</span>
                                <a href="tel:{{ $contact->phone }}" class="flex items-center gap-1 hover:text-forest-600 transition-colors">
                                    <i data-lucide="phone" class="w-4 h-4"></i> {{ $contact->phone }}
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="text-right text-sm text-gray-500">
                    <div>{{ $contact->created_at->format('d M Y') }}</div>
                    <div>{{ $contact->created_at->format('H:i') }} WIB</div>
                </div>
            </div>

            <div class="mb-6">
                <h4 class="text-sm font-bold text-gray-500 uppercase tracking-wider mb-2">Subjek</h4>
                <div class="text-xl font-bold text-gray-900">{{ $contact->subject }}</div>
            </div>

            <div>
                <h4 class="text-sm font-bold text-gray-500 uppercase tracking-wider mb-3">Isi Pesan</h4>
                <div class="bg-gray-50 rounded-xl text-gray-800 px-6 py-4 leading-relaxed border border-gray-100">
                    {{ $contact->message }}
                </div>
            </div>

            <div class="mt-8 flex gap-3">
                <a href="mailto:{{ $contact->email }}?subject=Balasan: {{ $contact->subject }}" class="inline-flex items-center gap-2 px-6 py-2.5 bg-forest-600 text-white font-medium rounded-xl hover:bg-forest-700 transition-colors shadow-sm">
                    <i data-lucide="reply" class="w-4 h-4"></i> Balas via Email
                </a>
                @if($contact->phone)
                @php 
                    $waPhone = preg_replace('/[^0-9]/', '', $contact->phone);
                    if(str_starts_with($waPhone, '0')) $waPhone = '62' . substr($waPhone, 1);
                @endphp
                <a href="https://wa.me/{{ $waPhone }}" target="_blank" class="inline-flex items-center gap-2 px-6 py-2.5 bg-[#25D366] text-white font-medium rounded-xl hover:bg-[#128C7E] transition-colors shadow-sm">
                    <i data-lucide="message-circle" class="w-4 h-4"></i> Balas via WhatsApp
                </a>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
