@extends('layouts.admin')

@section('title', $user->exists ? 'Edit Pengguna' : 'Tambah Pengguna Baru')

@section('content')
<div class="mb-6">
    <a href="{{ route('admin.users.index') }}" class="inline-flex items-center gap-2 text-sm text-gray-500 hover:text-forest-600 transition-colors font-medium">
        <i data-lucide="arrow-left" class="w-4 h-4"></i> Kembali ke Daftar Pengguna
    </a>
</div>

<div class="max-w bg-white rounded-2xl border border-gray-100 shadow-sm p-6 sm:p-8">
    <form action="{{ $user->exists ? route('admin.users.update', $user->id) : route('admin.users.store') }}" method="POST" class="space-y-6">
        @csrf
        @if($user->exists)
            @method('PUT')
        @endif

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
            {{-- Name --}}
            <div class="sm:col-span-2">
                <label for="name" class="block text-sm font-semibold text-gray-700 mb-2">Nama Lengkap</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-gray-400">
                        <i data-lucide="user" class="w-5 h-5"></i>
                    </span>
                    <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" required placeholder="Masukkan nama lengkap..." class="w-full pl-12 pr-4 py-2.5 bg-gray-50/50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:border-forest-500 focus:bg-white transition-all">
                </div>
            </div>

            {{-- Email --}}
            <div class="sm:col-span-2">
                <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">Alamat Email</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-gray-400">
                        <i data-lucide="mail" class="w-5 h-5"></i>
                    </span>
                    <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" required placeholder="contoh: nama@sanggabuana.com" class="w-full pl-12 pr-4 py-2.5 bg-gray-50/50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:border-forest-500 focus:bg-white transition-all">
                </div>
            </div>

            {{-- Role --}}
            <div>
                <label for="role" class="block text-sm font-semibold text-gray-700 mb-2">Peran (Role)</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-gray-400">
                        <i data-lucide="shield" class="w-5 h-5"></i>
                    </span>
                    <select name="role" id="role" required class="w-full pl-12 pr-4 py-2.5 bg-gray-50/50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:border-forest-500 focus:bg-white transition-all appearance-none cursor-pointer">
                        <option value="admin" {{ old('role', $user->role) === 'admin' ? 'selected' : '' }}>Admin</option>
                        <option value="superadmin" {{ old('role', $user->role) === 'superadmin' ? 'selected' : '' }}>Superadmin</option>
                        <option value="kasir" {{ old('role', $user->role) === 'kasir' ? 'selected' : '' }}>Kasir</option>
                    </select>
                    <span class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-gray-400 pointer-events-none">
                        <i data-lucide="chevron-down" class="w-4 h-4"></i>
                    </span>
                </div>
            </div>

            {{-- Destination (Only shown for cashier role) --}}
            <div id="destination-container" class="hidden">
                <label for="destination_id" class="block text-sm font-semibold text-gray-700 mb-2">Tugas Destinasi Wisata</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-gray-400">
                        <i data-lucide="map-pin" class="w-5 h-5"></i>
                    </span>
                    <select name="destination_id" id="destination_id" class="w-full pl-12 pr-4 py-2.5 bg-gray-50/50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:border-forest-500 focus:bg-white transition-all appearance-none cursor-pointer">
                        <option value="">-- Pilih Destinasi --</option>
                        @foreach($destinations as $dest)
                            <option value="{{ $dest->id }}" {{ old('destination_id', $user->destination_id) == $dest->id ? 'selected' : '' }}>{{ $dest->name }}</option>
                        @endforeach
                    </select>
                    <span class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-gray-400 pointer-events-none">
                        <i data-lucide="chevron-down" class="w-4 h-4"></i>
                    </span>
                </div>
            </div>

            {{-- Password --}}
            <div>
                <label for="password" class="block text-sm font-semibold text-gray-700 mb-2">
                    Password 
                    @if($user->exists)
                        <span class="text-xs font-normal text-gray-400">(Kosongkan jika tidak diubah)</span>
                    @endif
                </label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-gray-400">
                        <i data-lucide="lock" class="w-5 h-5"></i>
                    </span>
                    <input type="password" name="password" id="password" {{ $user->exists ? '' : 'required' }} placeholder="Minimal 6 karakter..." class="w-full pl-12 pr-10 py-2.5 bg-gray-50/50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:border-forest-500 focus:bg-white transition-all">
                    <button type="button" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600 focus:outline-none toggle-password" data-target="password">
                        <i data-lucide="eye" class="w-5 h-5"></i>
                    </button>
                </div>
            </div>

            {{-- Password Confirmation --}}
            <div>
                <label for="password_confirmation" class="block text-sm font-semibold text-gray-700 mb-2">Ulangi Password</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-gray-400">
                        <i data-lucide="key" class="w-5 h-5"></i>
                    </span>
                    <input type="password" name="password_confirmation" id="password_confirmation" {{ $user->exists ? '' : 'required' }} placeholder="Ketik ulang password..." class="w-full pl-12 pr-10 py-2.5 bg-gray-50/50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:border-forest-500 focus:bg-white transition-all">
                    <button type="button" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600 focus:outline-none toggle-password" data-target="password_confirmation">
                        <i data-lucide="eye" class="w-5 h-5"></i>
                    </button>
                </div>
            </div>
        </div>

        <div class="pt-4 border-t border-gray-100 flex items-center justify-end gap-3">
            <a href="{{ route('admin.users.index') }}" class="px-5 py-2.5 border border-gray-200 rounded-xl font-semibold text-sm text-gray-600 hover:bg-gray-50 transition-colors">
                Batal
            </a>
            <button type="submit" class="inline-flex items-center gap-2 bg-forest-600 hover:bg-forest-700 text-white px-6 py-2.5 rounded-xl font-semibold text-sm transition-colors shadow-sm">
                <i data-lucide="save" class="w-4.5 h-4.5"></i> Simpan Pengguna
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const roleSelect = document.getElementById('role');
        const destinationContainer = document.getElementById('destination-container');
        const destinationSelect = document.getElementById('destination_id');

        function toggleDestinationVisibility() {
            if (roleSelect.value === 'kasir') {
                destinationContainer.classList.remove('hidden');
                destinationSelect.setAttribute('required', 'required');
            } else {
                destinationContainer.classList.add('hidden');
                destinationSelect.removeAttribute('required');
                destinationSelect.value = '';
            }
        }

        // Run immediately to handle old form input or existing values
        toggleDestinationVisibility();

        // Listen for changes
        roleSelect.addEventListener('change', toggleDestinationVisibility);
    });
</script>
@endpush
@endsection
