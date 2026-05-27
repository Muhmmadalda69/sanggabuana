<?php

namespace App\Http\Controllers;

use App\Models\PendingRegistration;
use App\Models\Visitor;
use App\Models\VisitorAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class VisitorAuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::guard('visitor')->check()) {
            return redirect()->route('visitor.dashboard');
        }
        return view('visitor.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $account = VisitorAccount::where('email', $credentials['email'])->first();

        if (!$account || !Hash::check($credentials['password'], $account->password)) {
            return back()->withErrors([
                'email' => 'Email atau password salah.',
            ])->onlyInput('email');
        }

        if ($account->status === 'pending') {
            return back()->with('info', 'Akun Anda masih menunggu aktivasi oleh admin. Silakan coba lagi nanti.')
                ->onlyInput('email');
        }

        if ($account->status === 'banned') {
            return back()->with('error', 'Akun Anda telah dinonaktifkan. Silakan hubungi admin.')
                ->onlyInput('email');
        }

        Auth::guard('visitor')->login($account, $request->filled('remember'));
        $request->session()->regenerate();

        $intended = session()->pull('url.intended');
        if ($intended) {
            return redirect($intended);
        }

        return redirect()->route('visitor.dashboard');
    }

    public function showRegister()
    {
        if (Auth::guard('visitor')->check()) {
            return redirect()->route('visitor.dashboard');
        }
        return view('visitor.register');
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:visitor_accounts,email',
            'phone' => 'nullable|string|max:20',
            'password' => 'required|string|min:8|confirmed|regex:/[a-zA-Z]/|regex:/[0-9]/',
        ], [
            'password.min' => 'Password minimal harus 8 karakter.',
            'password.regex' => 'Password harus mengandung minimal satu huruf dan satu angka.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
        ]);

        $account = VisitorAccount::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'password' => Hash::make($validated['password']),
            'status' => 'active',
        ]);

        // Auto login after registration
        Auth::guard('visitor')->login($account);
        $request->session()->regenerate();

        return redirect()->route('visitor.dashboard')
            ->with('success', 'Pendaftaran berhasil! Akun Anda telah aktif dan Anda otomatis masuk.');
    }

    public function logout(Request $request)
    {
        Auth::guard('visitor')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }

    public function dashboard()
    {
        $account = Auth::guard('visitor')->user();

        // Get pending registrations
        $pendingCount = PendingRegistration::where('visitor_account_id', $account->id)
            ->where('status', 'pending')
            ->count();

        // Get visitors (successful tickets)
        $ticketCount = Visitor::where('visitor_account_id', $account->id)->count();

        return view('visitor.dashboard', [
            'account' => $account,
            'pendingCount' => $pendingCount,
            'ticketCount' => $ticketCount,
        ]);
    }

    public function riwayat()
    {
        $account = Auth::guard('visitor')->user();

        // Get pending registrations
        $pendings = PendingRegistration::where('visitor_account_id', $account->id)
            ->where('status', '!=', 'completed')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($p) {
                return [
                    'id' => $p->id,
                    'type' => 'pending',
                    'status' => $p->status,
                    'destination' => $p->destination->name ?? 'Unknown',
                    'visit_date' => $p->visit_date,
                    'total_amount' => $p->form_data['total_amount'] ?? 0,
                    'created_at' => $p->created_at,
                    'temp_token' => $p->temp_token,
                ];
            });

        // Get successful visitors (group by transaction/group_id)
        $visitors = Visitor::where('visitor_account_id', $account->id)
            ->orderBy('created_at', 'desc')
            ->get()
            ->groupBy('group_id')
            ->map(function ($group) {
                $first = $group->first();
                return [
                    'id' => $first->id,
                    'type' => 'completed',
                    'status' => 'success',
                    'destination' => $first->destination->name ?? 'Unknown',
                    'visit_date' => $first->visit_date,
                    'total_amount' => $group->sum('total_price'),
                    'created_at' => $first->created_at,
                    'ticket_count' => $group->count(),
                    'group_id' => $first->group_id,
                ];
            });

        // Merge and sort: pending (pending, expired, failed) first, then completed
        $allTransactions = collect($pendings)->merge($visitors)->sortByDesc(function ($item) {
            // Sort: pending first, then paid, then failed/expired
            $order = ['pending' => 3, 'success' => 2, 'failed' => 1, 'expired' => 0];
            return $order[$item['status']] ?? 99;
        });

        return view('visitor.riwayat', [
            'account' => $account,
            'transactions' => $allTransactions,
        ]);
    }

    public function viewTiket($groupId)
    {
        $account = Auth::guard('visitor')->user();

        $visitors = Visitor::where('visitor_account_id', $account->id)
            ->where('group_id', $groupId)
            ->where('payment_status', 'success')
            ->get();

        if ($visitors->isEmpty()) {
            abort(404);
        }

        $first = $visitors->first();
        $destination = $first->destination;

        return view('visitor.tiket-detail', [
            'group' => $visitors,
            'first' => $first,
            'destination' => $destination,
        ]);
    }

    public function tiketSaya()
    {
        $account = Auth::guard('visitor')->user();

        $visitors = Visitor::where('visitor_account_id', $account->id)
            ->where('payment_status', 'success')
            ->orderBy('created_at', 'desc')
            ->get()
            ->groupBy('group_id');

        return view('visitor.tiket-saya', [
            'account' => $account,
            'groups' => $visitors,
        ]);
    }
}
