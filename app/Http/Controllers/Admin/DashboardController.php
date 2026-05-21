<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use App\Models\Destination;
use App\Models\Gallery;
use App\Models\Testimonial;
use App\Models\Visitor;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    private function ensureSchemaIsUpToDate()
    {
        // In production, migrations are handled by deployment scripts
        if (app()->environment('production')) {
            return;
        }

        try {
            if (!\Illuminate\Support\Facades\Schema::hasTable('visitors')) {
                \Illuminate\Support\Facades\Artisan::call('migrate', ['--force' => true]);
            }

            if (\App\Models\Visitor::count() === 0) {
                \Illuminate\Support\Facades\Artisan::call('db:seed', ['--force' => true]);
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::warning('Database setup check skipped or failed: ' . $e->getMessage());
        }
    }

    public function index(Request $request)
    {
        $this->ensureSchemaIsUpToDate();

        $user = Auth::user();
        $isKasir = $user->isKasir();
        
        // Destination check for cashier
        $destination = null;
        if ($isKasir) {
            $destination = $user->destination;
            if (!$destination) {
                return view('admin.dashboard_kasir_fallback');
            }
        }

        // Apply filters
        $query = Visitor::query();

        // 1. Destination Filter
        if ($isKasir) {
            $query->where('destination_id', $destination->id);
        } elseif ($request->filled('destination_id')) {
            $query->where('destination_id', $request->destination_id);
            $destination = Destination::find($request->destination_id);
        }

        // 2. Date Filter (perhari, perbulan, pertahun)
        $timeFilter = $request->input('filter', 'month'); // default: month
        if ($timeFilter === 'day') {
            $query->whereDate('checked_in_at', Carbon::today());
        } elseif ($timeFilter === 'month') {
            $query->whereMonth('checked_in_at', Carbon::now()->month)
                  ->whereYear('checked_in_at', Carbon::now()->year);
        } elseif ($timeFilter === 'year') {
            $query->whereYear('checked_in_at', Carbon::now()->year);
        } elseif ($timeFilter === 'year_range') {
            $startYear = $request->input('start_year', Carbon::now()->year - 1);
            $endYear = $request->input('end_year', Carbon::now()->year);
            $query->whereYear('checked_in_at', '>=', $startYear)
                  ->whereYear('checked_in_at', '<=', $endYear);
        }

        // Retrieve statistics data
        $visitorsData = $query->get();

        $totalVisitors = $visitorsData->sum('qty_total');
        $totalRevenue = $visitorsData->sum('total_price');
        $averageAge = round($visitorsData->avg('avg_age') ?: 0, 1);
        $activeInside = $visitorsData->where('status', 'in')->sum('qty_total');

        // Genders & Kids
        $totalMale = $visitorsData->sum('qty_male');
        $totalFemale = $visitorsData->sum('qty_female');
        $totalKids = $visitorsData->sum('qty_kids');
        
        // Payment Methods Grouping
        $paymentStats = $visitorsData->groupBy('payment_method')->map(function ($group) {
            return [
                'count' => $group->count(),
                'total' => $group->sum('total_price'),
                'qty' => $group->sum('qty_total')
            ];
        });

        // Purpose Statistics (Only purposes other than 'Normal')
        $purposeStats = $visitorsData->where('purpose', '!=', 'Normal')->groupBy('purpose')->map(function ($group) {
            return [
                'count' => $group->count(),
                'qty' => $group->sum('qty_total')
            ];
        });

        // City, Province & Country Statistics
        $cityStats = $visitorsData->whereNotNull('city')->where('city', '!=', '')
            ->groupBy(function ($visitor) {
                $city = trim($visitor->city);
                if (in_array(strtolower($city), ['pangkalan', 'tegalwaru'])) {
                    return 'Lokal';
                }
                return $city;
            })->map(function ($group) {
                return [
                    'count' => $group->count(),
                    'qty' => $group->sum('qty_total')
                ];
            })->sortByDesc('qty')->take(5);

        $provinceStats = $visitorsData->whereNotNull('province')->where('province', '!=', '')
            ->where('address_type', '!=', 'mancanegara')
            ->groupBy('province')->map(function ($group) {
                return [
                    'count' => $group->count(),
                    'qty' => $group->sum('qty_total')
                ];
            })->sortByDesc('qty')->take(5);

        $countryStats = $visitorsData->groupBy(function ($visitor) {
            $type = $visitor->address_type;
            if ($type === 'lokal' || $type === 'indonesia' || $type === 'nusantara') {
                return 'Indonesia';
            }
            $country = trim($visitor->province);
            return $country ?: 'Mancanegara';
        })->map(function ($group) {
            return [
                'count' => $group->count(),
                'qty' => $group->sum('qty_total')
            ];
        })->sortByDesc('qty')->take(5);

        // Trend over time (grouped by date/hour for charts)
        $trendStats = $visitorsData->sortBy('checked_in_at')->groupBy(function ($visitor) use ($timeFilter) {
            if ($timeFilter === 'day') {
                return $visitor->checked_in_at->format('H:00');
            } elseif ($timeFilter === 'month') {
                return $visitor->checked_in_at->format('d M');
            } elseif ($timeFilter === 'year_range') {
                return $visitor->checked_in_at->format('Y');
            } else {
                return $visitor->checked_in_at->format('M Y');
            }
        })->map(function ($group) {
            return [
                'qty' => $group->sum('qty_total'),
                'revenue' => $group->sum('total_price')
            ];
        });

        // Province Coordinate Mapping for Map Visualization
        $provinceCoords = [
            'aceh' => [-5.55, 95.32], 'sumatera utara' => [2.12, 99.54], 'sumatera barat' => [-0.74, 100.80],
            'riau' => [0.51, 101.45], 'jambi' => [-1.61, 103.61], 'sumatera selatan' => [-3.32, 104.91],
            'bengkulu' => [-3.80, 102.26], 'lampung' => [-4.56, 105.41], 'kepulauan bangka belitung' => [-2.74, 106.44],
            'kepulauan riau' => [3.95, 108.14], 'dki jakarta' => [-6.21, 106.85], 'jawa barat' => [-6.91, 107.61],
            'jawa tengah' => [-7.15, 110.14], 'di yogyakarta' => [-7.80, 110.36], 'jawa timur' => [-7.54, 112.24],
            'banten' => [-6.41, 106.13], 'bali' => [-8.41, 115.19], 'nusa tenggara barat' => [-8.65, 117.36],
            'nusa tenggara timur' => [-8.66, 121.08], 'kalimantan barat' => [-0.26, 109.34],
            'kalimantan tengah' => [-1.68, 113.38], 'kalimantan selatan' => [-3.09, 115.28],
            'kalimantan timur' => [1.69, 116.42], 'kalimantan utara' => [3.07, 116.04],
            'sulawesi utara' => [0.62, 123.97], 'sulawesi tengah' => [-1.43, 121.45],
            'sulawesi selatan' => [-3.67, 119.97], 'sulawesi tenggara' => [-4.14, 122.17],
            'gorontalo' => [0.69, 122.45], 'sulawesi barat' => [-2.84, 119.23],
            'maluku' => [-3.24, 130.15], 'maluku utara' => [1.57, 127.81],
            'papua' => [-4.27, 138.08], 'papua barat' => [-1.34, 133.17],
            'papua selatan' => [-6.52, 140.72], 'papua tengah' => [-3.59, 137.08],
            'papua pegunungan' => [-4.08, 138.95], 'papua barat daya' => [-1.90, 131.52],
        ];

        // Build map data: group by city for Choropleth map
        $mapData = $visitorsData->whereNotNull('city')->where('city', '!=', '')
            ->groupBy(function ($visitor) {
                $city = strtolower(trim($visitor->city));
                // Map local districts to Karawang for map visualization
                if (in_array($city, ['pangkalan', 'tegalwaru', 'lokal'])) {
                    return 'karawang';
                }
                // Handle Jakarta prefix/suffix if needed, but usually exact match or partial match works
                return $city;
            })->map(function ($group, $key) {
                $name = $group->first()->city;
                if (in_array(strtolower($name), ['pangkalan', 'tegalwaru', 'lokal'])) {
                    $name = 'Karawang';
                }
                return [
                    'name' => ucfirst($name),
                    'count' => $group->count(),
                    'qty' => $group->sum('qty_total'),
                    'revenue' => $group->sum('total_price'),
                ];
            })->filter()->values();

        // Build world map data: group by country (province field for mancanegara, otherwise Indonesia)
        $worldMapData = $visitorsData->groupBy(function ($visitor) {
            $type = strtolower($visitor->address_type ?? '');
            if (in_array($type, ['lokal', 'indonesia', 'nusantara']) || empty($type)) {
                return 'indonesia';
            }
            return strtolower(trim($visitor->province ?: 'mancanegara'));
        })->map(function ($group, $key) {
            return [
                'name' => ucfirst($key),
                'count' => $group->count(),
                'qty' => $group->sum('qty_total'),
                'revenue' => $group->sum('total_price'),
            ];
        })->filter()->values();

        // Get destinations list for admin dropdown
        $destinations = Destination::all();

        return view('admin.dashboard', compact(
            'destination',
            'isKasir',
            'totalVisitors',
            'totalRevenue',
            'averageAge',
            'activeInside',
            'totalMale',
            'totalFemale',
            'totalKids',
            'paymentStats',
            'purposeStats',
            'cityStats',
            'provinceStats',
            'countryStats',
            'mapData',
            'worldMapData',
            'trendStats',
            'timeFilter',
            'destinations'
        ));
    }

    public function realtimeStats(Request $request)
    {
        $user = Auth::user();
        $isKasir = $user->isKasir();
        
        $destination = null;
        if ($isKasir) {
            $destination = $user->destination;
        } elseif ($request->filled('destination_id')) {
            $destination = Destination::find($request->destination_id);
        }

        $query = Visitor::query();

        if ($isKasir) {
            $query->where('destination_id', $destination->id);
        } elseif ($request->filled('destination_id')) {
            $query->where('destination_id', $request->destination_id);
        }

        $timeFilter = $request->input('filter', 'month');
        if ($timeFilter === 'day') {
            $query->whereDate('checked_in_at', Carbon::today());
        } elseif ($timeFilter === 'month') {
            $query->whereMonth('checked_in_at', Carbon::now()->month)
                  ->whereYear('checked_in_at', Carbon::now()->year);
        } elseif ($timeFilter === 'year') {
            $query->whereYear('checked_in_at', Carbon::now()->year);
        } elseif ($timeFilter === 'year_range') {
            $startYear = $request->input('start_year', Carbon::now()->year - 1);
            $endYear = $request->input('end_year', Carbon::now()->year);
            $query->whereYear('checked_in_at', '>=', $startYear)
                  ->whereYear('checked_in_at', '<=', $endYear);
        }

        $visitorsData = $query->get();

        $totalVisitors = $visitorsData->sum('qty_total');
        $totalRevenue = $visitorsData->sum('total_price');
        $averageAge = round($visitorsData->avg('avg_age') ?: 0, 1);
        $activeInside = $visitorsData->where('status', 'in')->sum('qty_total');

        $totalMale = $visitorsData->sum('qty_male');
        $totalFemale = $visitorsData->sum('qty_female');
        $totalKids = $visitorsData->sum('qty_kids');

        return response()->json([
            'total_visitors' => $totalVisitors,
            'total_visitors_formatted' => number_format($totalVisitors, 0, ',', '.'),
            'total_revenue' => $totalRevenue,
            'total_revenue_formatted' => 'Rp ' . number_format($totalRevenue, 0, ',', '.'),
            'average_age' => $averageAge,
            'active_inside' => $activeInside,
            'active_inside_formatted' => number_format($activeInside, 0, ',', '.'),
            'total_male' => $totalMale,
            'total_female' => $totalFemale,
            'total_kids' => $totalKids,
        ]);
    }

    public function posQuota(Request $request)
    {
        $user = Auth::user();
        $destination = $user->isKasir() ? $user->destination : Destination::find($request->input('destination_id'));
        if (!$destination) return response()->json(['error' => 'Not found'], 404);

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
            'booked' => (int) $booked,
            'remaining' => $remaining,
            'is_full' => $quota ? $booked >= $quota : false,
        ]);
    }

    public function posIndex()
    {
        $this->ensureSchemaIsUpToDate();
        $user = Auth::user();
        if (!$user->isKasir() && !$user->isSuperadmin()) {
            abort(403, 'Hanya Kasir atau Superadmin yang dapat mengakses POS Tiket.');
        }

        $destination = null;
        if ($user->isKasir()) {
            $destination = $user->destination;
        } else {
            $destination = Destination::first();
        }

        if (!$destination) {
            return view('admin.dashboard_kasir_fallback');
        }

        // Retrieve last 10 transactions grouped by group_id
        $recentGroups = Visitor::where('destination_id', $destination->id)
            ->whereNotNull('group_id')
            ->orderByDesc('checked_in_at')
            ->get()
            ->groupBy('group_id')
            ->take(10);

        return view('admin.pos', compact('destination', 'recentGroups'));
    }

    public function posStore(Request $request)
    {
        $this->ensureSchemaIsUpToDate();
        $user = Auth::user();
        if (!$user->isKasir() && !$user->isSuperadmin()) {
            abort(403, 'Aksi tidak diizinkan.');
        }

        $destinationId = $request->input('destination_id');
        $destination = Destination::findOrFail($destinationId);

        // Security check: cashier can only process ticket for their own destination
        if ($user->isKasir() && $user->destination_id !== $destination->id) {
            abort(403, 'Anda hanya dapat memproses tiket untuk destinasi tugas Anda.');
        }

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
                'members.*.address_type' => 'required|string|in:lokal,indonesia,mancanegara',
                'members.*.province' => 'required|string|max:255',
                'members.*.city' => 'required|string|max:255',
                'address_type' => 'required|string|in:lokal,indonesia,mancanegara',
                'province' => 'required|string|max:255',
                'city' => 'required|string|max:255',
                'payment_method' => 'required|string|in:Tunai,QRIS,Transfer',
                'price' => 'required|numeric|min:0',
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

            // Generate one group_id for the entire rombongan
            $groupId = 'GRP-' . Carbon::now()->format('Ymd') . '-' . strtoupper(Str::random(6));

            // Base ticket counter — use max existing sequence number to avoid gaps/duplicates
            $todayStr = Carbon::now()->format('Ymd');
            $maxTicket = Visitor::whereDate('created_at', Carbon::today())
                ->where('ticket_no', 'like', 'TKT-' . $todayStr . '-%')
                ->max('ticket_no');
            $ticketCounter = $maxTicket ? (int) substr($maxTicket, -4) : 0;

            // Create ticket for the leader (penanggung jawab) first
            $leaderAge = (int) $request->input('leader_age');
            $leaderGender = $request->input('leader_gender');
            $leaderTicketNo = 'TKT-' . $todayStr . '-' . str_pad(++$ticketCounter, 4, '0', STR_PAD_LEFT);

            $leaderQtyMale = 0; $leaderQtyFemale = 0; $leaderQtyKids = 0;
            if ($leaderAge < 5) { $leaderQtyKids = 1; }
            elseif ($leaderGender === 'L') { $leaderQtyMale = 1; }
            else { $leaderQtyFemale = 1; }

            $leaderCombinedAddress = $request->input('leader_address') . ', ' . $request->input('city') . ', ' . $request->input('province');

            $leaderVisitor = Visitor::create([
                'destination_id' => $destination->id,
                'group_id' => $groupId,
                'visit_date' => Carbon::today()->toDateString(),
                'ticket_no' => $leaderTicketNo,
                'name' => $request->input('name'),
                'email' => $request->input('leader_email'),
                'age' => $leaderAge,
                'address' => $leaderCombinedAddress,
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
                'price' => (int) $request->input('price'),
                'total_price' => (int) $request->input('price'),
                'payment_method' => $request->input('payment_method'),
                'status' => 'in',
                'checked_in_at' => Carbon::now(),
            ]);
            $createdTicketIds[] = $leaderVisitor->id;

            foreach ($members ?? [] as $index => $member) {
                $ticketNo = 'TKT-' . $todayStr . '-' . str_pad(++$ticketCounter, 4, '0', STR_PAD_LEFT);

                // Determine demographic count based on age, gender, and is_child flag
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

                // Apply kids discount if applicable
                $basePrice = (int) $request->input('price');
                $memberPrice = $isChild && $destination->kids_discount
                    ? (int) round($basePrice * (1 - $destination->kids_discount / 100))
                    : $basePrice;

                $combinedAddress = $member['address'] . ', ' . ($member['city'] ?? $request->input('city')) . ', ' . ($member['province'] ?? $request->input('province'));

                $visitor = Visitor::create([
                    'destination_id' => $destination->id,
                    'group_id' => $groupId,
                    'visit_date' => Carbon::today()->toDateString(),
                    'ticket_no' => $ticketNo,
                    'name' => $member['name'],
                    'email' => $member['email'],
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
                    'status' => 'in',
                    'checked_in_at' => Carbon::now(),
                ]);

                $createdTicketIds[] = $visitor->id;
            }

            return redirect()->route('admin.pos.index')
                ->with('success', count($createdTicketIds) . ' tiket berhasil diproses!')
                ->with('print_group_id', $groupId)
                ->with('print_ticket_ids', $createdTicketIds);
        }

        // Validation based on destination
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
            'price' => 'required|numeric|min:0',
            'avg_age' => 'required|integer|min:1|max:100',
        ];

        if ($destination->has_purpose) {
            $rules['purpose'] = 'required|string|in:Hiking,Trail Run,Jiarah';
            if ($request->input('purpose') === 'Hiking') {
                $rules['camping_duration'] = 'required|integer|min:1';
            }
        }

        $request->validate($rules);

        // Calculate totals
        $qtyMale = $request->input('qty_male');
        $qtyFemale = $request->input('qty_female');
        $qtyKids = $request->input('qty_kids');
        $leaderGender = $request->input('leader_gender');

        // Tambah penanggung jawab ke kalkulasi gender yang sesuai
        if ($leaderGender === 'L') {
            $qtyMale += 1;
        } else {
            $qtyFemale += 1;
        }
        
        // Penanggung jawab sudah masuk hitungan male/female
        $qtyTotal = $qtyMale + $qtyFemale + $qtyKids;

        // Generate unique ticket number: TKT-YYYYMMDD-XXXX
        $todayStr = Carbon::now()->format('Ymd');
        $maxTicket = Visitor::whereDate('created_at', Carbon::today())
            ->where('ticket_no', 'like', 'TKT-' . $todayStr . '-%')
            ->max('ticket_no');
        $seq = $maxTicket ? (int) substr($maxTicket, -4) : 0;
        $ticketNo = 'TKT-' . $todayStr . '-' . str_pad($seq + 1, 4, '0', STR_PAD_LEFT);

        // Combine address for backward compatibility and to store leader address
        $combinedAddress = $request->input('leader_address') . ', ' . $request->input('city') . ', ' . $request->input('province');

        $visitor = Visitor::create([
            'destination_id' => $destination->id,
            'group_id' => $ticketNo,
            'visit_date' => Carbon::today()->toDateString(),
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
            'price' => (int) $request->input('price'),
            'total_price' => (int) $request->input('price') * $qtyTotal,
            'payment_method' => $request->input('payment_method'),
            'status' => 'in', // checked in by default when bought
            'checked_in_at' => Carbon::now(),
        ]);

        return redirect()->route('admin.pos.index')
            ->with('success', 'Tiket ' . $ticketNo . ' berhasil diproses!')
            ->with('print_ticket_id', $visitor->id);
    }

    public function monitoringIndex(Request $request)
    {
        $this->ensureSchemaIsUpToDate();
        $user = Auth::user();
        if (!$user->isKasir() && !$user->isSuperadmin()) {
            abort(403, 'Aksi tidak diizinkan.');
        }

        $destination = null;
        if ($user->isKasir()) {
            $destination = $user->destination;
        } else {
            $destination = Destination::first();
        }

        if (!$destination) {
            return view('admin.dashboard_kasir_fallback');
        }

        // Fetch visitors grouped by group_id
        $query = Visitor::where('destination_id', $destination->id);

        // Optional search filter
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('ticket_no', 'like', "%{$search}%")
                  ->orWhere('group_id', 'like', "%{$search}%")
                  ->orWhere('community', 'like', "%{$search}%");
            });
        }

        // Optional status filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Optional visit_date filter
        if ($request->filled('visit_date')) {
            $query->where('visit_date', $request->visit_date);
        }

        // Get unique groups sorted by visit_date desc, then checked_in_at desc
        $allGroupIds = (clone $query)->whereNotNull('group_id')
            ->orderByRaw('COALESCE(visit_date, DATE(checked_in_at)) DESC')
            ->orderByDesc('checked_in_at')
            ->pluck('group_id')
            ->unique()
            ->values();

        // Paginate group IDs manually
        $perPage = 15;
        $currentPage = $request->input('page', 1);
        $pagedGroupIds = $allGroupIds->slice(($currentPage - 1) * $perPage, $perPage)->values();

        // Load all visitors for these groups
        $groupedVisitors = Visitor::where('destination_id', $destination->id)
            ->whereIn('group_id', $pagedGroupIds)
            ->orderBy('checked_in_at')
            ->get()
            ->groupBy('group_id');

        // Build paginator
        $visitors = new \Illuminate\Pagination\LengthAwarePaginator(
            $pagedGroupIds,
            $allGroupIds->count(),
            $perPage,
            $currentPage,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return view('admin.monitoring', compact('destination', 'visitors', 'groupedVisitors'));
    }

    public function monitoringCheckout(Visitor $visitor)
    {
        $user = Auth::user();
        if (!$user->isKasir() && !$user->isSuperadmin()) {
            abort(403, 'Aksi tidak diizinkan.');
        }

        // Security check: cashier can only check out visitor of their own destination
        if ($user->isKasir() && $visitor->destination_id !== $user->destination_id) {
            abort(403, 'Anda hanya dapat mengubah status pengunjung untuk destinasi tugas Anda.');
        }

        if ($visitor->status !== 'in') {
            return redirect()->back()->with('error', 'Pengunjung sudah keluar sebelumnya.');
        }

        $visitor->update([
            'status' => 'out',
            'checked_out_at' => Carbon::now(),
        ]);

        return redirect()->back()->with('success', 'Pengunjung ' . $visitor->name . ' (' . $visitor->ticket_no . ') berhasil keluar dari lokasi.');
    }

    public function monitoringGroupCheckout(Request $request, string $groupId)
    {
        $user = Auth::user();
        if (!$user->isKasir() && !$user->isSuperadmin()) {
            abort(403, 'Aksi tidak diizinkan.');
        }

        $members = Visitor::where('group_id', $groupId)->where('status', 'in')->get();

        if ($members->isEmpty()) {
            return redirect()->back()->with('error', 'Semua anggota rombongan sudah keluar.');
        }

        // Security check
        if ($user->isKasir()) {
            $members->each(function($v) use ($user) {
                if ($v->destination_id !== $user->destination_id) abort(403);
            });
        }

        Visitor::where('group_id', $groupId)->where('status', 'in')->update([
            'status' => 'out',
            'checked_out_at' => Carbon::now(),
        ]);

        return redirect()->back()->with('success', 'Rombongan ' . $groupId . ' (' . $members->count() . ' orang) berhasil check-out.');
    }

    public function monitoringUpdateStatus(Request $request, Visitor $visitor)
    {
        $user = Auth::user();
        if (!$user->isKasir() && !$user->isSuperadmin()) {
            abort(403, 'Aksi tidak diizinkan.');
        }
        if ($user->isKasir() && $visitor->destination_id !== $user->destination_id) {
            abort(403);
        }

        $newStatus = $request->input('status');
        if (!in_array($newStatus, ['pending', 'in', 'out'])) {
            return response()->json(['error' => 'Status tidak valid.'], 422);
        }

        $data = ['status' => $newStatus];
        if ($newStatus === 'in' && !$visitor->checked_in_at) {
            $data['checked_in_at'] = Carbon::now();
        }
        if ($newStatus === 'out' && !$visitor->checked_out_at) {
            $data['checked_out_at'] = Carbon::now();
        }

        $visitor->update($data);

        return response()->json(['success' => true, 'status' => $newStatus]);
    }

    public function monitoringPartialCheckout(Request $request)
    {
        $user = Auth::user();
        if (!$user->isKasir() && !$user->isSuperadmin()) {
            abort(403, 'Aksi tidak diizinkan.');
        }

        $visitorIds = $request->input('visitor_ids', []);
        if (empty($visitorIds)) {
            return redirect()->back()->with('error', 'Pilih minimal satu anggota untuk di-checkout.');
        }

        $visitors = Visitor::whereIn('id', $visitorIds)->where('status', 'in')->get();

        if ($user->isKasir()) {
            foreach ($visitors as $v) {
                if ($v->destination_id !== $user->destination_id) abort(403);
            }
        }

        Visitor::whereIn('id', $visitorIds)->where('status', 'in')->update([
            'status' => 'out',
            'checked_out_at' => Carbon::now(),
        ]);

        return redirect()->back()->with('success', $visitors->count() . ' anggota berhasil di-checkout.');
    }

    public function unreadCount()
    {
        $recentContacts = Contact::latest()->limit(5)->get()->map(function($contact) {
            return [
                'id' => $contact->id,
                'name' => $contact->name,
                'subject' => $contact->subject,
                'message' => Str::limit($contact->message, 80),
                'is_read' => $contact->is_read,
                'created_time' => $contact->created_at->diffForHumans(),
                'show_url' => route('admin.contacts.show', $contact->id),
                'avatar_letter' => strtoupper(substr($contact->name, 0, 1)),
            ];
        });

        return response()->json([
            'unread_contacts' => Contact::unread()->count(),
            'unread_testimonials' => Testimonial::where('is_read', false)->count(),
            'total_contacts' => Contact::count(),
            'total_testimonials' => Testimonial::count(),
            'recent_contacts' => $recentContacts,
        ]);
    }
}
