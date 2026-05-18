@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6 mb-8">
    {{-- Stat Cards --}}
    <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm">
        <div class="flex items-center justify-between mb-4">
            <div class="w-12 h-12 rounded-xl bg-forest-50 flex items-center justify-center text-forest-600">
                <i data-lucide="map" class="w-6 h-6"></i>
            </div>
            <div class="text-sm font-medium text-gray-500">Destinasi</div>
        </div>
        <div class="text-3xl font-bold text-gray-800">{{ $stats['destinations'] }}</div>
    </div>
    
    <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm">
        <div class="flex items-center justify-between mb-4">
            <div class="w-12 h-12 rounded-xl bg-blue-50 flex items-center justify-center text-blue-600">
                <i data-lucide="image" class="w-6 h-6"></i>
            </div>
            <div class="text-sm font-medium text-gray-500">Galeri Foto</div>
        </div>
        <div class="text-3xl font-bold text-gray-800">{{ $stats['galleries'] }}</div>
    </div>
    
    <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm relative overflow-hidden" id="unread-contacts-card">
        <div id="unread-indicator-strip" class="absolute top-0 right-0 w-2 h-full bg-red-500 {{ $stats['unread_contacts'] > 0 ? '' : 'hidden' }}"></div>
        <div class="flex items-center justify-between mb-4">
            <div class="w-12 h-12 rounded-xl bg-orange-50 flex items-center justify-center text-orange-600">
                <i data-lucide="mail" class="w-6 h-6"></i>
            </div>
            <div class="text-sm font-medium text-gray-500">Pesan Masuk</div>
        </div>
        <div class="flex items-end gap-3">
            <div class="text-3xl font-bold text-gray-800" id="total-contacts-count">{{ $stats['contacts'] }}</div>
            <div class="text-sm font-medium text-red-500 mb-1 {{ $stats['unread_contacts'] > 0 ? '' : 'hidden' }}" id="unread-contacts-subtext">
                <span id="unread-contacts-count">{{ $stats['unread_contacts'] }}</span> belum dibaca
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm">
        <div class="flex items-center justify-between mb-4">
            <div class="w-12 h-12 rounded-xl bg-yellow-50 flex items-center justify-center text-yellow-600">
                <i data-lucide="message-square" class="w-6 h-6"></i>
            </div>
            <div class="text-sm font-medium text-gray-500">Testimoni</div>
        </div>
        <div class="text-3xl font-bold text-gray-800" id="total-testimonials-count">{{ $stats['testimonials'] }}</div>
    </div>
    
    <div class="bg-gradient-to-br from-forest-500 to-forest-700 rounded-2xl p-6 border border-forest-600 shadow-md text-white flex flex-col justify-between">
        <div>
            <div class="text-forest-100 text-sm font-medium mb-1">Status Web</div>
            <div class="font-bold text-lg flex items-center gap-2">
                <span class="w-2.5 h-2.5 rounded-full bg-green-400 animate-pulse"></span>
                Online
            </div>
        </div>
        <a href="{{ route('home') }}" target="_blank" class="mt-4 bg-white/20 hover:bg-white/30 transition-colors px-4 py-2 rounded-lg text-sm font-medium flex items-center justify-center gap-2">
            Lihat Website <i data-lucide="external-link" class="w-4 h-4"></i>
        </a>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    {{-- Recent Contacts --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden flex flex-col">
        <div class="px-6 py-5 border-b border-gray-100 flex items-center justify-between">
            <h3 class="font-bold text-gray-800 flex items-center gap-2">
                <i data-lucide="mail" class="w-5 h-5 text-gray-400"></i>
                Pesan Terbaru
            </h3>
            <a href="{{ route('admin.contacts.index') }}" class="text-sm text-forest-600 hover:text-forest-800 font-medium">Lihat Semua</a>
        </div>
        <div class="flex-1 overflow-y-auto p-0" id="recent-contacts-container">
            @if($recentContacts->count() > 0)
                <div class="divide-y divide-gray-100" id="recent-contacts-list">
                    @foreach($recentContacts as $contact)
                    <a href="{{ route('admin.contacts.show', $contact->id) }}" class="flex items-start gap-4 p-5 hover:bg-gray-50 transition-colors {{ !$contact->is_read ? 'bg-forest-50/50' : '' }}">
                        <div class="w-10 h-10 rounded-full bg-gray-100 flex items-center justify-center text-gray-500 font-bold shrink-0">
                            {{ substr($contact->name, 0, 1) }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center justify-between mb-1">
                                <h4 class="font-bold text-sm text-gray-800 truncate {{ !$contact->is_read ? 'text-forest-900' : '' }}">{{ $contact->name }}</h4>
                                <span class="text-xs text-gray-400 whitespace-nowrap">{{ $contact->created_at->diffForHumans() }}</span>
                            </div>
                            <p class="text-sm text-gray-600 font-medium truncate mb-1">{{ $contact->subject }}</p>
                            <p class="text-xs text-gray-500 truncate">{{ $contact->message }}</p>
                        </div>
                        @if(!$contact->is_read)
                            <div class="w-2.5 h-2.5 rounded-full bg-forest-500 shrink-0 mt-1.5"></div>
                        @endif
                    </a>
                    @endforeach
                </div>
            @else
                <div class="p-8 text-center text-gray-500" id="recent-contacts-empty">
                    <i data-lucide="inbox" class="w-12 h-12 mx-auto text-gray-300 mb-3"></i>
                    <p>Belum ada pesan masuk.</p>
                </div>
            @endif
        </div>
    </div>
    
    {{-- Recent Destinations --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden flex flex-col">
        <div class="px-6 py-5 border-b border-gray-100 flex items-center justify-between">
            <h3 class="font-bold text-gray-800 flex items-center gap-2">
                <i data-lucide="map-pin" class="w-5 h-5 text-gray-400"></i>
                Destinasi Terbaru
            </h3>
            <a href="{{ route('admin.destinations.index') }}" class="text-sm text-forest-600 hover:text-forest-800 font-medium">Kelola</a>
        </div>
        <div class="flex-1 p-0">
            @if($recentDestinations->count() > 0)
                <div class="divide-y divide-gray-100">
                    @foreach($recentDestinations as $dest)
                    <div class="flex items-center gap-4 p-5 hover:bg-gray-50 transition-colors">
                        <img src="{{ $dest->image_url }}" alt="{{ $dest->name }}" class="w-16 h-16 rounded-lg object-cover border border-gray-200">
                        <div class="flex-1 min-w-0">
                            <h4 class="font-bold text-gray-800 mb-1 truncate">{{ $dest->name }}</h4>
                            <div class="flex items-center gap-3 text-xs">
                                <span class="px-2 py-1 rounded bg-gray-100 text-gray-600"><i data-lucide="clock" class="w-3 h-3 inline mr-1"></i>{{ $dest->operational_hours ?? '-' }}</span>
                                <span class="text-gray-500"><i data-lucide="map-pin" class="w-3 h-3 inline mr-1"></i>{{ $dest->altitude }}</span>
                            </div>
                        </div>
                        <div class="text-right">
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium {{ $dest->is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600' }}">
                                <span class="w-1.5 h-1.5 rounded-full {{ $dest->is_active ? 'bg-green-500' : 'bg-gray-400' }}"></span>
                                {{ $dest->is_active ? 'Aktif' : 'Draft' }}
                            </span>
                        </div>
                    </div>
                    @endforeach
                </div>
            @else
                <div class="p-8 text-center text-gray-500">
                    <p>Belum ada destinasi yang ditambahkan.</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
