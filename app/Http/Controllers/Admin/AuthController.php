<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use App\Models\Destination;
use App\Models\Gallery;
use App\Models\Page;
use App\Models\Setting;
use App\Models\Testimonial;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function showLogin()
    {
        // Self-healing migration & user seeding trigger on login page load (disabled in production)
        if (!app()->environment('production')) {
            try {
                if (!\Illuminate\Support\Facades\Schema::hasTable('visitors')) {
                    \Illuminate\Support\Facades\DB::table('migrations')
                        ->where('migration', '2026_05_18_190000_create_visitors_table')
                        ->delete();
                    \Illuminate\Support\Facades\Artisan::call('migrate', ['--force' => true]);
                }

                if (\App\Models\User::count() === 0) {
                    \App\Models\User::create([
                        'name' => 'Super Administrator',
                        'email' => 'superadmin@sanggabuana.com',
                        'password' => \Illuminate\Support\Facades\Hash::make('superadmin123'),
                        'role' => 'superadmin',
                    ]);
                    \App\Models\User::create([
                        'name' => 'Administrator',
                        'email' => 'admin@sanggabuana.com',
                        'password' => \Illuminate\Support\Facades\Hash::make('admin123'),
                        'role' => 'admin',
                    ]);

                    // Create default Cashiers for all destinations
                    foreach (\App\Models\Destination::all() as $dest) {
                        \App\Models\User::create([
                            'name' => 'Kasir ' . $dest->name,
                            'email' => 'kasir.' . $dest->slug . '@sanggabuana.com',
                            'password' => \Illuminate\Support\Facades\Hash::make('kasir123'),
                            'role' => 'kasir',
                            'destination_id' => $dest->id,
                        ]);
                    }
                }
            } catch (\Exception $e) {
                // Ignore database connection issues during migration setup
            }
        }

        if (session('admin_authenticated') && \Illuminate\Support\Facades\Auth::check()) {
            return redirect()->route('admin.dashboard');
        }
        return view('admin.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $credentials = $request->only('email', 'password');

        if (\Illuminate\Support\Facades\Auth::attempt($credentials)) {
            $user = \Illuminate\Support\Facades\Auth::user();
            
            session([
                'admin_authenticated' => true,
                'admin_name' => $user->name,
                'admin_role' => $user->role,
                'admin_destination_id' => $user->destination_id
            ]);

            if ($user->isKasir()) {
                return redirect()->route('admin.dashboard')->with('success', 'Selamat datang, Kasir ' . ($user->destination ? $user->destination->name : '') . '!');
            }

            return redirect()->route('admin.dashboard')->with('success', 'Selamat datang, ' . $user->name . '!');
        }

        return back()->withErrors(['email' => 'Email atau password tidak valid.'])->withInput();
    }

    public function logout()
    {
        \Illuminate\Support\Facades\Auth::logout();
        session()->forget(['admin_authenticated', 'admin_name', 'admin_role', 'admin_destination_id']);
        return redirect()->route('admin.login')->with('success', 'Anda telah keluar.');
    }
}
