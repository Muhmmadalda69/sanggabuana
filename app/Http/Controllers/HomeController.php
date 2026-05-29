<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use App\Models\Destination;
use App\Models\Gallery;
use App\Models\Page;
use App\Models\PendingRegistration;
use App\Models\Setting;
use App\Models\Testimonial;
use App\Models\Visitor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class HomeController extends Controller
{
    public function index()
    {
        $featuredDestinations = Destination::active()->featured()
            ->orderBy('sort_order')->limit(6)->get();
        $destinations = Destination::active()
            ->orderBy('sort_order')->limit(8)->get();
        $galleries = Gallery::where('is_active', true)
            ->orderBy('sort_order')->limit(8)->get();
        $testimonials = Testimonial::active()
            ->orderBy('sort_order')->limit(6)->get();
        $annualVisitors = Visitor::whereYear('checked_in_at', now()->year)->sum('qty_total');

        return view('landing', compact(
            'featuredDestinations', 'destinations', 'galleries', 'testimonials', 'annualVisitors'
        ));
    }

    public function destination($slug)
    {
        $destination = Destination::where('slug', $slug)->active()->firstOrFail();
        $galleries = $destination->galleries()->where('is_active', true)->orderBy('sort_order')->get();
        $relatedDestinations = Destination::active()
            ->where('id', '!=', $destination->id)
            ->limit(3)->get();

        return view('destination-detail', compact('destination', 'galleries', 'relatedDestinations'));
    }

    public function destinations()
    {
        $destinations = Destination::active()->orderBy('sort_order')->paginate(9);
        return view('destinations', compact('destinations'));
    }

    public function contact(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:20',
            'subject' => 'required|string|max:255',
            'message' => 'required|string|max:2000',
        ]);

        Contact::create($validated);

        return back()->with('success', 'Pesan Anda telah terkirim! Kami akan segera menghubungi Anda.');
    }

    public function page($slug)
    {
        $page = Page::where('slug', $slug)->active()->firstOrFail();
        return view('page', compact('page'));
    }

    public function storeTestimonial(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'role' => 'nullable|string|max:255',
            'rating' => 'required|integer|min:1|max:5',
            'message' => 'required|string|max:1000',
        ]);

        $validated['role'] = $request->filled('role') ? $request->role : 'Pengunjung';
        $validated['is_active'] = false;
        $validated['is_read'] = false;
        
        // Auto-increment sort_order to place new items at the bottom
        $nextSortOrder = (int) Testimonial::max('sort_order') + 1;
        $validated['sort_order'] = $nextSortOrder;

        Testimonial::create($validated);

        return back()->with('success', 'Terima kasih! Ulasan Anda telah berhasil dikirim dan menunggu persetujuan (kurasi) dari admin.');
    }

    public function registerDatePicker($slug)
    {
        $destination = Destination::where('slug', $slug)->active()->firstOrFail();
        if (!$destination->has_online_registration) {
            abort(403, 'Registrasi online tidak diaktifkan untuk destinasi ini.');
        }
        // Visitor must be logged in
        if (!Auth::guard('visitor')->check()) {
            session(['url.intended' => url()->current()]);
            return redirect()->route('visitor.login')
                ->with('info', 'Silakan login atau daftar akun terlebih dahulu untuk melakukan registrasi online.');
        }
        return view('destination-register-date', compact('destination'));
    }

    public function quotaApi(Request $request, $slug)
    {
        $destination = Destination::where('slug', $slug)->active()->firstOrFail();
        $date = $request->input('date', today()->toDateString());

        $booked = Visitor::where('destination_id', $destination->id)
            ->where('visit_date', $date)
            ->whereIn('status', ['pending', 'in'])
            ->sum('qty_total');

        $quota = $destination->daily_quota;
        $remaining = $quota ? max(0, $quota - $booked) : null;

        return response()->json([
            'date' => $date,
            'quota' => $quota,
            'booked' => $booked,
            'remaining' => $remaining,
            'is_full' => $quota ? $booked >= $quota : false,
        ]);
    }

    public function quotaMonth(Request $request, $slug)
    {
        $destination = Destination::where('slug', $slug)->active()->firstOrFail();
        $year  = (int) $request->input('year',  now()->year);
        $month = (int) $request->input('month', now()->month);

        $from = \Carbon\Carbon::create($year, $month, 1)->startOfMonth()->toDateString();
        $to   = \Carbon\Carbon::create($year, $month, 1)->endOfMonth()->toDateString();

        // One query — group by visit_date
        $rows = Visitor::where('destination_id', $destination->id)
            ->whereBetween('visit_date', [$from, $to])
            ->whereIn('status', ['pending', 'in'])
            ->selectRaw('visit_date, SUM(qty_total) as booked')
            ->groupBy('visit_date')
            ->pluck('booked', 'visit_date');

        $quota = $destination->daily_quota;
        $result = [];
        foreach ($rows as $date => $booked) {
            $result[$date] = [
                'booked'    => (int) $booked,
                'remaining' => $quota ? max(0, $quota - $booked) : null,
                'is_full'   => $quota ? $booked >= $quota : false,
            ];
        }

        return response()->json([
            'quota'  => $quota,
            'month'  => $result,
        ]);
    }

    public function registerForm($slug, $date)
    {
        $destination = Destination::where('slug', $slug)->active()->firstOrFail();
        if (!$destination->has_online_registration) {
            abort(403, 'Registrasi online tidak diaktifkan untuk destinasi ini.');
        }

        // Visitor must be logged in
        if (!Auth::guard('visitor')->check()) {
            session(['url.intended' => url()->current()]);
            return redirect()->route('visitor.login')
                ->with('info', 'Silakan login atau daftar akun terlebih dahulu untuk melakukan registrasi online.');
        }

        // Validate date
        try {
            $visitDate = Carbon::createFromFormat('Y-m-d', $date);
            if ($visitDate->isPast() && !$visitDate->isToday()) {
                return redirect()->route('destination.register.date', $slug)->with('error', 'Tanggal kunjungan tidak valid.');
            }
        } catch (\Exception $e) {
            return redirect()->route('destination.register.date', $slug);
        }

        // Check quota
        if ($destination->daily_quota) {
            $booked = Visitor::where('destination_id', $destination->id)
                ->where('visit_date', $date)
                ->whereIn('status', ['pending', 'in'])
                ->sum('qty_total');
            if ($booked >= $destination->daily_quota) {
                return redirect()->route('destination.register.date', $slug)->with('error', 'Kuota untuk tanggal ' . Carbon::parse($date)->translatedFormat('d F Y') . ' sudah penuh.');
            }
        }

        return view('destination-register', compact('destination', 'date'));
    }

    public function registerStore(Request $request, $slug, $date)
    {
        $destination = Destination::where('slug', $slug)->active()->firstOrFail();
        if (!$destination->has_online_registration) {
            abort(403, 'Registrasi online tidak diaktifkan untuk destinasi ini.');
        }

        // Validate visit date
        try {
            $visitDate = Carbon::createFromFormat('Y-m-d', $date)->toDateString();
        } catch (\Exception $e) {
            return redirect()->route('destination.register.date', $slug)->with('error', 'Tanggal tidak valid.');
        }

        // Build validation rules
        if ($destination->has_member_details) {
            $rules = [
                'name' => 'required|string|max:255',
                'leader_gender' => 'required|string|in:L,P',
                'leader_address' => 'required|string|max:500',
                'leader_email' => 'nullable|email|max:255',
                'leader_age' => 'required|integer|min:1|max:120',
                'members' => 'nullable|array',
                'members.*.name' => 'required|string|max:255',
                'members.*.email' => 'nullable|email|max:255',
                'members.*.age' => 'required|integer|min:1|max:120',
                'members.*.gender' => 'required|string|in:L,P',
                'members.*.address' => 'required|string|max:500',
                'address_type' => 'required|string|in:lokal,indonesia,mancanegara',
                'province' => 'required|string|max:255',
                'city' => 'required|string|max:255',
                'payment_method' => 'required|string|in:Tunai,Transfer,bca,bni,bri,mandiri,permata,qris,gopay,shopeepay,dana,grab_pay,alfamart,indomaret',
            ];

            if ($destination->has_purpose) {
                $rules['purpose'] = 'required|string|in:Hiking,Trail Run,Jiarah';
                if ($request->input('purpose') === 'Hiking') {
                    $rules['camping_duration'] = 'required|integer|min:1';
                }
            }

            $request->validate($rules);

            $members = $request->input('members');
            $requestedQty = 1 + count($members ?? []);

            // Check quota
            if ($destination->daily_quota) {
                $booked = Visitor::where('destination_id', $destination->id)
                    ->where('visit_date', $visitDate)
                    ->whereIn('status', ['pending', 'in'])
                    ->sum('qty_total');
                if (($booked + $requestedQty) > $destination->daily_quota) {
                    $remaining = max(0, $destination->daily_quota - $booked);
                    return redirect()->route('destination.register.date', $slug)
                        ->with('error', "Kuota tersisa {$remaining} orang, tidak cukup untuk {$requestedQty} orang.");
                }
            }

            $totalAmount = $destination->price;
            $items = [
                [
                    'name' => $request->input('name'),
                    'price' => (int) $destination->price,
                    'quantity' => 1,
                ]
            ];
            foreach ($members ?? [] as $member) {
                $isChild = !empty($member['is_child']) && $member['is_child'] == '1';
                $memberPrice = $isChild && $destination->kids_discount
                    ? (int) round($destination->price * (1 - $destination->kids_discount / 100))
                    : (int) $destination->price;
                $totalAmount += $memberPrice;
                $items[] = [
                    'name' => $member['name'],
                    'price' => $memberPrice,
                    'quantity' => 1,
                ];
            }

            $formData = [
                'has_member_details' => true,
                'leader' => [
                    'name' => $request->input('name'),
                    'email' => $request->input('leader_email'),
                    'age' => (int) $request->input('leader_age'),
                    'gender' => $request->input('leader_gender'),
                    'address' => $request->input('leader_address'),
                    'address_type' => $request->input('address_type'),
                    'province' => $request->input('province'),
                    'city' => $request->input('city'),
                    'community' => $request->input('community'),
                ],
                'members' => $members ?? [],
                'items' => $items,
                'total_amount' => $totalAmount,
            ];

            if ($destination->has_purpose) {
                $formData['leader']['purpose'] = $request->input('purpose');
                $formData['leader']['camping_duration'] = ($request->input('purpose') === 'Hiking') ? $request->input('camping_duration') : null;
            }
        } else {
            $rules = [
                'name' => 'required|string|max:255',
                'leader_gender' => 'required|string|in:L,P',
                'leader_address' => 'required|string|max:500',
                'leader_email' => 'nullable|email|max:255',
                'leader_age' => 'required|integer|min:1|max:120',
                'address_type' => 'required|string|in:lokal,indonesia,mancanegara',
                'province' => 'required|string|max:255',
                'city' => 'required|string|max:255',
                'qty_male' => 'required|integer|min:0',
                'qty_female' => 'required|integer|min:0',
                'qty_kids' => 'required|integer|min:0',
                'payment_method' => 'required|string|in:Tunai,Transfer,bca,bni,bri,mandiri,permata,qris,gopay,shopeepay,dana,grab_pay,alfamart,indomaret',
                'avg_age' => 'required|integer|min:1|max:100',
            ];

            if ($destination->has_purpose) {
                $rules['purpose'] = 'required|string|in:Hiking,Trail Run,Jiarah';
                if ($request->input('purpose') === 'Hiking') {
                    $rules['camping_duration'] = 'required|integer|min:1';
                }
            }

            $request->validate($rules);

            $qtyMale = $request->input('qty_male');
            $qtyFemale = $request->input('qty_female');
            $qtyKids = $request->input('qty_kids');
            $leaderGender = $request->input('leader_gender');

            if ($leaderGender === 'L') {
                $qtyMale += 1;
            } else {
                $qtyFemale += 1;
            }

            $qtyTotal = $qtyMale + $qtyFemale + $qtyKids;

            // Check quota
            if ($destination->daily_quota) {
                $booked = Visitor::where('destination_id', $destination->id)
                    ->where('visit_date', $visitDate)
                    ->whereIn('status', ['pending', 'in'])
                    ->sum('qty_total');
                if (($booked + $qtyTotal) > $destination->daily_quota) {
                    $remaining = max(0, $destination->daily_quota - $booked);
                    return redirect()->route('destination.register.date', $slug)
                        ->with('error', "Kuota tersisa {$remaining} orang, tidak cukup untuk {$qtyTotal} orang.");
                }
            }

            $totalAmount = $destination->price * $qtyTotal;

            $formData = [
                'has_member_details' => false,
                'leader' => [
                    'name' => $request->input('name'),
                    'email' => $request->input('leader_email'),
                    'age' => (int) $request->input('leader_age'),
                    'gender' => $leaderGender,
                    'address' => $request->input('leader_address'),
                    'address_type' => $request->input('address_type'),
                    'province' => $request->input('province'),
                    'city' => $request->input('city'),
                    'community' => $request->input('community'),
                    'avg_age' => (int) $request->input('avg_age'),
                ],
                'qty_male' => $qtyMale,
                'qty_female' => $qtyFemale,
                'qty_kids' => $qtyKids,
                'qty_total' => $qtyTotal,
                'total_amount' => $totalAmount,
            ];

            if ($destination->has_purpose) {
                $formData['leader']['purpose'] = $request->input('purpose');
                $formData['leader']['camping_duration'] = ($request->input('purpose') === 'Hiking') ? $request->input('camping_duration') : null;
            }
        }

        // Save to pending_registrations
        $pending = PendingRegistration::create([
            'visitor_account_id' => Auth::guard('visitor')->id(),
            'destination_id' => $destination->id,
            'slug' => $slug,
            'visit_date' => $visitDate,
            'form_data' => $formData,
            'payment_method' => $request->input('payment_method'),
        ]);

        return redirect()->route('payment.pay', $pending->temp_token);
    }
}
