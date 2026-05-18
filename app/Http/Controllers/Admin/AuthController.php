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
        if (session('admin_authenticated')) {
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

        // Simple admin authentication - in production use proper auth
        if ($request->email === 'admin@sanggabuana.com' && $request->password === 'admin123') {
            session(['admin_authenticated' => true, 'admin_name' => 'Administrator']);
            return redirect()->route('admin.dashboard')->with('success', 'Selamat datang, Administrator!');
        }

        return back()->withErrors(['email' => 'Email atau password tidak valid.'])->withInput();
    }

    public function logout()
    {
        session()->forget(['admin_authenticated', 'admin_name']);
        return redirect()->route('admin.login')->with('success', 'Anda telah keluar.');
    }
}
