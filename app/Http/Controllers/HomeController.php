<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use App\Models\Destination;
use App\Models\Gallery;
use App\Models\Page;
use App\Models\Setting;
use App\Models\Testimonial;
use App\Models\Visitor;
use Illuminate\Http\Request;
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

        // Check quota again before storing
        if ($destination->daily_quota) {
            $booked = Visitor::where('destination_id', $destination->id)
                ->where('visit_date', $visitDate)
                ->whereIn('status', ['pending', 'in'])
                ->sum('qty_total');
            if ($booked >= $destination->daily_quota) {
                return redirect()->route('destination.register.date', $slug)->with('error', 'Kuota untuk tanggal ini sudah penuh.');
            }
        }

        // Build validation rules similar to POS
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
                'payment_method' => 'required|string|in:Tunai,QRIS,Transfer',
            ];

            if ($destination->has_purpose) {
                $rules['purpose'] = 'required|string|in:Hiking,Trail Run,Jiarah';
                if ($request->input('purpose') === 'Hiking') {
                    $rules['camping_duration'] = 'required|integer|min:1';
                }
            }

            $request->validate($rules);

            $members = $request->input('members');
            $createdTicketIds = [];

            $groupId = 'GRP-' . Carbon::now()->format('Ymd') . '-' . strtoupper(\Illuminate\Support\Str::random(6));
            $todayStr = Carbon::now()->format('Ymd');
            $maxTicket = Visitor::whereDate('created_at', Carbon::today())
                ->where('ticket_no', 'like', 'TKT-' . $todayStr . '-%')
                ->max('ticket_no');
            $ticketCounter = $maxTicket ? (int) substr($maxTicket, -4) : 0;

            // Leader ticket
            $leaderAge = (int) $request->input('leader_age');
            $leaderGender = $request->input('leader_gender');
            $leaderTicketNo = 'TKT-' . $todayStr . '-' . str_pad(++$ticketCounter, 4, '0', STR_PAD_LEFT);
            $leaderQtyMale = 0; $leaderQtyFemale = 0; $leaderQtyKids = 0;
            if ($leaderAge < 5) { $leaderQtyKids = 1; }
            elseif ($leaderGender === 'L') { $leaderQtyMale = 1; }
            else { $leaderQtyFemale = 1; }

            $leaderVisitor = Visitor::create([
                'destination_id' => $destination->id,
                'group_id' => $groupId,
                'visit_date' => $visitDate,
                'ticket_no' => $leaderTicketNo,
                'name' => $request->input('name'),
                'email' => $request->input('leader_email'),
                'age' => $leaderAge,
                'address' => $request->input('leader_address') . ', ' . $request->input('city') . ', ' . $request->input('province'),
                'address_type' => $request->input('address_type'),
                'city' => $request->input('city'),
                'province' => $request->input('province'),
                'community' => $request->input('community'),
                'purpose' => $destination->has_purpose ? $request->input('purpose') : 'Normal',
                'camping_duration' => ($destination->has_purpose && $request->input('purpose') === 'Hiking') ? $request->input('camping_duration') : null,
                'qty_male' => $leaderQtyMale,
                'qty_female' => $leaderQtyFemale,
                'qty_kids' => $leaderQtyKids,
                'qty_total' => 1,
                'avg_age' => $leaderAge,
                'price' => $destination->price,
                'total_price' => $destination->price,
                'payment_method' => $request->input('payment_method'),
                'status' => 'pending',
                'checked_in_at' => null,
            ]);
            $createdTicketIds[] = $leaderVisitor->id;

            foreach ($members ?? [] as $index => $member) {
                $ticketNo = 'TKT-' . $todayStr . '-' . str_pad(++$ticketCounter, 4, '0', STR_PAD_LEFT);

                $qtyMale = 0;
                $qtyFemale = 0;
                $qtyKids = 0;

                $age = (int) $member['age'];
                $isChild = !empty($member['is_child']) && $member['is_child'] == '1';

                if ($isChild) {
                    $qtyKids = 1;
                } elseif ($member['gender'] === 'L') {
                    $qtyMale = 1;
                } else {
                    $qtyFemale = 1;
                }

                $memberPrice = $isChild && $destination->kids_discount
                    ? (int) round($destination->price * (1 - $destination->kids_discount / 100))
                    : (int) $destination->price;

                $combinedAddress = $member['address'] . ', ' . ($member['city'] ?? $request->input('city')) . ', ' . ($member['province'] ?? $request->input('province'));

                $visitor = Visitor::create([
                    'destination_id' => $destination->id,
                    'group_id' => $groupId,
                    'visit_date' => $visitDate,
                    'ticket_no' => $ticketNo,
                    'name' => $member['name'],
                    'email' => $member['email'] ?? null,
                    'age' => $age,
                    'address' => $combinedAddress,
                    'address_type' => $member['address_type'] ?? $request->input('address_type'),
                    'city' => $member['city'] ?? $request->input('city'),
                    'province' => $member['province'] ?? $request->input('province'),
                    'community' => $request->input('community'),
                    'purpose' => $destination->has_purpose ? $request->input('purpose') : 'Normal',
                    'camping_duration' => ($destination->has_purpose && $request->input('purpose') === 'Hiking') ? $request->input('camping_duration') : null,
                    'qty_male' => $qtyMale,
                    'qty_female' => $qtyFemale,
                    'qty_kids' => $qtyKids,
                    'qty_total' => 1,
                    'avg_age' => $age,
                    'price' => $memberPrice,
                    'total_price' => $memberPrice,
                    'payment_method' => $request->input('payment_method'),
                    'status' => 'pending',
                    'checked_in_at' => null,
                ]);

                $createdTicketIds[] = $visitor->id;
            }

            return redirect()->route('destination.register', [$slug, $visitDate])
                ->with('success', 'Registrasi online berhasil! Silakan simpan detail tiket Anda di bawah.')
                ->with('print_ticket_ids', $createdTicketIds);
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
                'payment_method' => 'required|string|in:Tunai,QRIS,Transfer',
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

            $todayStr = Carbon::now()->format('Ymd');
            $maxTicket = Visitor::whereDate('created_at', Carbon::today())
                ->where('ticket_no', 'like', 'TKT-' . $todayStr . '-%')
                ->max('ticket_no');
            $seq = $maxTicket ? (int) substr($maxTicket, -4) : 0;
            $ticketNo = 'TKT-' . $todayStr . '-' . str_pad($seq + 1, 4, '0', STR_PAD_LEFT);

            $combinedAddress = $request->input('leader_address') . ', ' . $request->input('city') . ', ' . $request->input('province');

            $visitor = Visitor::create([
                'destination_id' => $destination->id,
                'group_id' => $ticketNo,
                'visit_date' => $visitDate,
                'ticket_no' => $ticketNo,
                'name' => $request->input('name'),
                'email' => $request->input('leader_email'),
                'age' => $request->input('leader_age'),
                'address' => $combinedAddress,
                'address_type' => $request->input('address_type'),
                'city' => $request->input('city'),
                'province' => $request->input('province'),
                'community' => $request->input('community'),
                'purpose' => $destination->has_purpose ? $request->input('purpose') : 'Normal',
                'camping_duration' => ($destination->has_purpose && $request->input('purpose') === 'Hiking') ? $request->input('camping_duration') : null,
                'qty_male' => $qtyMale,
                'qty_female' => $qtyFemale,
                'qty_kids' => $qtyKids,
                'qty_total' => $qtyTotal,
                'avg_age' => $request->input('avg_age'),
                'price' => $destination->price,
                'total_price' => $destination->price * $qtyTotal,
                'payment_method' => $request->input('payment_method'),
                'status' => 'pending',
                'checked_in_at' => null,
            ]);

            return redirect()->route('destination.register', [$slug, $visitDate])
                ->with('success', 'Registrasi online berhasil! Silakan simpan detail tiket Anda di bawah.')
                ->with('print_ticket_id', $visitor->id);
        }
    }
}
