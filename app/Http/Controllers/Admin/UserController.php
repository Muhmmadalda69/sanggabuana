<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Destination;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function index()
    {
        $users = User::with('destination')->orderBy('role')->orderBy('name')->paginate(10);
        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        $destinations = Destination::orderBy('name')->get();
        return view('admin.users.form', [
            'user' => new User(),
            'destinations' => $destinations
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
            'role' => 'required|string|in:superadmin,admin,kasir',
            'destination_id' => 'required_if:role,kasir|nullable|exists:destinations,id',
        ], [
            'name.required' => 'Nama wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.unique' => 'Email ini sudah terdaftar di sistem.',
            'password.required' => 'Password wajib diisi.',
            'password.min' => 'Password minimal terdiri dari 6 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
            'destination_id.required_if' => 'Destinasi wajib dipilih apabila role yang dipilih adalah Kasir.',
        ]);

        $validated['password'] = Hash::make($validated['password']);
        
        // If role is not cashier, clear destination association
        if ($validated['role'] !== 'kasir') {
            $validated['destination_id'] = null;
        }

        User::create($validated);

        return redirect()->route('admin.users.index')
            ->with('success', 'Pengguna baru berhasil ditambahkan!');
    }

    public function edit(User $user)
    {
        $destinations = Destination::orderBy('name')->get();
        return view('admin.users.form', compact('user', 'destinations'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:6|confirmed',
            'role' => 'required|string|in:superadmin,admin,kasir',
            'destination_id' => 'required_if:role,kasir|nullable|exists:destinations,id',
        ], [
            'name.required' => 'Nama wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.unique' => 'Email ini sudah terdaftar di sistem.',
            'password.min' => 'Password minimal terdiri dari 6 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
            'destination_id.required_if' => 'Destinasi wajib dipilih apabila role yang dipilih adalah Kasir.',
        ]);

        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        // Prevent admin from changing their own role to something else to avoid lockout
        if ($user->id === Auth::id()) {
            $validated['role'] = $user->role;
            $validated['destination_id'] = $user->destination_id;
        } else {
            // If role is not cashier, clear destination
            if ($validated['role'] !== 'kasir') {
                $validated['destination_id'] = null;
            }
        }

        $user->update($validated);

        // Update active session metadata if modifying self
        if ($user->id === Auth::id()) {
            session([
                'admin_name' => $user->name,
                'admin_role' => $user->role,
                'admin_destination_id' => $user->destination_id
            ]);
        }

        return redirect()->route('admin.users.index')
            ->with('success', 'Data pengguna berhasil diperbarui!');
    }

    public function destroy(User $user)
    {
        if ($user->id === Auth::id()) {
            return redirect()->route('admin.users.index')
                ->with('error', 'Anda tidak dapat menghapus akun Anda sendiri yang sedang aktif.');
        }

        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', 'Pengguna berhasil dihapus dari sistem!');
    }
}
