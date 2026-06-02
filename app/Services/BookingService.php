<?php

namespace App\Services;

use App\Models\Destination;
use App\Models\PendingRegistration;
use App\Models\Visitor;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class BookingService
{
    /**
     * Generate a unique ticket number with safe sequence lock to prevent race conditions.
     */
    public function generateTicketNumber(): string
    {
        $todayStr = Carbon::now()->format('Ymd');
        
        // Lock table row selection for sequential generation safety
        $maxTicket = Visitor::whereDate('created_at', Carbon::today())
            ->where('ticket_no', 'like', 'TKT-' . $todayStr . '-%')
            ->lockForUpdate()
            ->max('ticket_no');
            
        $seq = $maxTicket ? (int) substr($maxTicket, -4) : 0;
        
        return 'TKT-' . $todayStr . '-' . str_pad($seq + 1, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Validate daily quota for a destination.
     */
    public function checkQuota(Destination $destination, string $visitDate, int $requestedQty): bool
    {
        if (!$destination->daily_quota) {
            return true;
        }

        $booked = Visitor::where('destination_id', $destination->id)
            ->where('visit_date', $visitDate)
            ->whereIn('status', ['pending', 'in'])
            ->sum('qty_total');

        return ($booked + $requestedQty) <= $destination->daily_quota;
    }

    /**
     * Create Visitor models from online PendingRegistration model in a safe transaction.
     */
    public function createVisitorsFromPending(PendingRegistration $pending, $midtransResponse): void
    {
        if ($pending->status === 'completed') {
            return;
        }

        DB::transaction(function () use ($pending, $midtransResponse) {
            $destination = $pending->destination;
            $formData = $pending->form_data;
            $visitDate = $pending->visit_date->toDateString();
            $groupId = $pending->transaction_id;
            $leader = $formData['leader'];

            $purposeName = $leader['purpose'] ?? 'Normal';
            $dbPurposeName = $purposeName === 'Jiarah' ? 'Ziarah' : $purposeName;
            $purp = $destination->active_purposes->firstWhere('name', $dbPurposeName);
            $baseTicketPrice = ($purp && $purp->pivot && $purp->pivot->has_custom_price) ? (int)$purp->pivot->custom_price : (int)$destination->price;

            $duration = 1;
            if (in_array(strtolower($purposeName), ['camping'])) {
                $duration = (int) ($leader['camping_duration'] ?? 1);
                if ($duration < 1) $duration = 1;
            }
            $baseTicketPrice = $baseTicketPrice * $duration;

            if (!empty($formData['has_member_details'])) {
                // Create leader
                $leaderAge = (int) $leader['age'];
                $leaderGender = $leader['gender'] ?? 'L';
                $ticketNo = $this->generateTicketNumber();

                $leaderQtyMale = 0; $leaderQtyFemale = 0; $leaderQtyKids = 0;
                if ($leaderAge < 5) {
                    $leaderQtyKids = 1;
                } elseif ($leaderGender === 'L') {
                    $leaderQtyMale = 1;
                } else {
                    $leaderQtyFemale = 1;
                }

                $combinedAddress = $leader['address'] . ', ' . ($leader['city'] ?? '') . ', ' . ($leader['province'] ?? '');

                Visitor::create(array_merge([
                    'destination_id' => $destination->id,
                    'group_id' => $groupId,
                    'visit_date' => $visitDate,
                    'ticket_no' => $ticketNo,
                    'name' => $leader['name'],
                    'email' => $leader['email'] ?? null,
                    'age' => $leaderAge,
                    'address' => $combinedAddress,
                    'address_type' => $leader['address_type'] ?? 'indonesia',
                    'city' => $leader['city'] ?? '',
                    'province' => $leader['province'] ?? '',
                    'community' => $leader['community'] ?? '',
                    'purpose' => $leader['purpose'] ?? 'Normal',
                    'camping_duration' => $leader['camping_duration'] ?? null,
                    'qty_male' => $leaderQtyMale,
                    'qty_female' => $leaderQtyFemale,
                    'qty_kids' => $leaderQtyKids,
                    'qty_total' => 1,
                    'avg_age' => $leaderAge,
                    'price' => (int) ($formData['items'][0]['price'] ?? $baseTicketPrice),
                    'total_price' => (int) ($formData['items'][0]['price'] ?? $baseTicketPrice),
                    'payment_method' => $pending->payment_method,
                    'payment_status' => 'success',
                    'payment_details' => json_encode($midtransResponse),
                    'status' => 'pending',
                ], $pending->visitor_account_id ? ['visitor_account_id' => $pending->visitor_account_id] : []));

                // Create members
                foreach ($formData['members'] ?? [] as $member) {
                    $ticketNo = $this->generateTicketNumber();

                    $qtyMale = 0; $qtyFemale = 0; $qtyKids = 0;
                    $age = (int) $member['age'];
                    $isChild = !empty($member['is_child']) && $member['is_child'] == '1';

                    if ($isChild) {
                        $qtyKids = 1;
                    } elseif (($member['gender'] ?? 'L') === 'L') {
                        $qtyMale = 1;
                    } else {
                        $qtyFemale = 1;
                    }

                    $memberPrice = $isChild && $destination->kids_discount
                        ? (int) round($baseTicketPrice * (1 - $destination->kids_discount / 100))
                        : (int) $baseTicketPrice;

                    $combinedAddress = $member['address'] . ', ' . ($member['city'] ?? $leader['city'] ?? '') . ', ' . ($member['province'] ?? $leader['province'] ?? '');

                    Visitor::create(array_merge([
                        'destination_id' => $destination->id,
                        'group_id' => $groupId,
                        'visit_date' => $visitDate,
                        'ticket_no' => $ticketNo,
                        'name' => $member['name'],
                        'email' => $member['email'] ?? null,
                        'age' => $age,
                        'address' => $combinedAddress,
                        'address_type' => $member['address_type'] ?? $leader['address_type'] ?? 'indonesia',
                        'city' => $member['city'] ?? $leader['city'] ?? '',
                        'province' => $member['province'] ?? $leader['province'] ?? '',
                        'community' => $leader['community'] ?? '',
                        'purpose' => $leader['purpose'] ?? 'Normal',
                        'camping_duration' => $leader['camping_duration'] ?? null,
                        'qty_male' => $qtyMale,
                        'qty_female' => $qtyFemale,
                        'qty_kids' => $qtyKids,
                        'qty_total' => 1,
                        'avg_age' => $age,
                        'price' => $memberPrice,
                        'total_price' => $memberPrice,
                        'payment_method' => $pending->payment_method,
                        'payment_status' => 'success',
                        'payment_details' => json_encode($midtransResponse),
                        'status' => 'pending',
                        'checked_in_at' => null,
                    ], $pending->visitor_account_id ? ['visitor_account_id' => $pending->visitor_account_id] : []));
                }
            } else {
                $qtyMale = (int) ($formData['qty_male'] ?? 0);
                $qtyFemale = (int) ($formData['qty_female'] ?? 0);
                $qtyKids = (int) ($formData['qty_kids'] ?? 0);
                $qtyTotal = (int) ($formData['qty_total'] ?? 1);

                $ticketNo = $this->generateTicketNumber();
                $combinedAddress = $leader['address'] . ', ' . ($leader['city'] ?? '') . ', ' . ($leader['province'] ?? '');

                Visitor::create(array_merge([
                    'destination_id' => $destination->id,
                    'group_id' => $groupId,
                    'visit_date' => $visitDate,
                    'ticket_no' => $ticketNo,
                    'name' => $leader['name'],
                    'email' => $leader['email'] ?? null,
                    'age' => (int) ($leader['age'] ?? 25),
                    'address' => $combinedAddress,
                    'address_type' => $leader['address_type'] ?? 'indonesia',
                    'city' => $leader['city'] ?? '',
                    'province' => $leader['province'] ?? '',
                    'community' => $leader['community'] ?? '',
                    'purpose' => $leader['purpose'] ?? 'Normal',
                    'camping_duration' => $leader['camping_duration'] ?? null,
                    'qty_male' => $qtyMale,
                    'qty_female' => $qtyFemale,
                    'qty_kids' => $qtyKids,
                    'qty_total' => $qtyTotal,
                    'avg_age' => (int) ($leader['avg_age'] ?? 25),
                    'price' => (int) $baseTicketPrice,
                    'total_price' => (int) $formData['total_amount'],
                    'payment_method' => $pending->payment_method,
                    'payment_status' => 'success',
                    'payment_details' => json_encode($midtransResponse),
                    'status' => 'pending',
                ], $pending->visitor_account_id ? ['visitor_account_id' => $pending->visitor_account_id] : []));
            }

            $pending->update(['status' => 'completed']);
        });
    }

    /**
     * Create direct Visitor models for cashier POS bookings in a transaction.
     */
    public function createTicketsDirectly(array $data, Destination $destination, string $paymentMethod, ?string $paymentToken): array
    {
        return DB::transaction(function () use ($data, $destination, $paymentMethod, $paymentToken) {
            $groupId = 'GRP-' . Carbon::now()->format('Ymd') . '-' . strtoupper(Str::random(6));
            
            $status = $paymentMethod === 'Tunai' ? 'in' : 'pending';
            $paymentStatus = $paymentMethod === 'Tunai' ? 'success' : 'pending';
            $checkedInAt = $paymentMethod === 'Tunai' ? Carbon::now() : null;
            
            $createdTicketIds = [];

            if ($destination->has_member_details) {
                // Leader creation
                $leaderAge = (int) $data['leader_age'];
                $leaderGender = $data['leader_gender'];
                $leaderTicketNo = $this->generateTicketNumber();

                $leaderQtyMale = 0; $leaderQtyFemale = 0; $leaderQtyKids = 0;
                if ($leaderAge < 5) {
                    $leaderQtyKids = 1;
                } elseif ($leaderGender === 'L') {
                    $leaderQtyMale = 1;
                } else {
                    $leaderQtyFemale = 1;
                }

                $leaderCombinedAddress = $data['leader_address'] . ', ' . $data['city'] . ', ' . $data['province'];

                $duration = 1;
                if ($destination->has_purpose && in_array(strtolower($data['purpose'] ?? ''), ['camping'])) {
                    $duration = (int) ($data['camping_duration'] ?? 1);
                    if ($duration < 1) $duration = 1;
                }
                $unitPrice = (int) $data['price'];
                $leaderPrice = $unitPrice * $duration;

                $leaderVisitor = Visitor::create([
                    'destination_id' => $destination->id,
                    'group_id' => $groupId,
                    'visit_date' => Carbon::today()->toDateString(),
                    'ticket_no' => $leaderTicketNo,
                    'name' => $data['name'],
                    'email' => $data['leader_email'] ?? null,
                    'age' => $leaderAge,
                    'address' => $leaderCombinedAddress,
                    'address_type' => $data['address_type'],
                    'city' => $data['city'],
                    'province' => $data['province'],
                    'community' => $data['community'] ?? null,
                    'purpose' => $destination->has_purpose ? ($data['purpose'] ?? 'Normal') : 'Normal',
                    'camping_duration' => ($destination->has_purpose && in_array(strtolower($data['purpose'] ?? ''), ['camping'])) ? $data['camping_duration'] : null,
                    'qty_male' => $leaderQtyMale,
                    'qty_female' => $leaderQtyFemale,
                    'qty_kids' => $leaderQtyKids,
                    'qty_total' => 1,
                    'avg_age' => $leaderAge,
                    'price' => $leaderPrice,
                    'total_price' => $leaderPrice,
                    'payment_method' => $paymentMethod,
                    'payment_token' => $paymentToken,
                    'payment_status' => $paymentStatus,
                    'status' => $status,
                    'checked_in_at' => $checkedInAt,
                ]);
                $createdTicketIds[] = $leaderVisitor->id;

                // Members creation
                foreach ($data['members'] ?? [] as $member) {
                    $ticketNo = $this->generateTicketNumber();

                    $qtyMale = 0; $qtyFemale = 0; $qtyKids = 0;
                    $age = (int) $member['age'];
                    $isChild = !empty($member['is_child']) && $member['is_child'] == '1';

                    if ($isChild) {
                        $qtyKids = 1;
                    } elseif (($member['gender'] ?? 'L') === 'L') {
                        $qtyMale = 1;
                    } else {
                        $qtyFemale = 1;
                    }

                    $basePrice = $leaderPrice;
                    $memberPrice = $isChild && $destination->kids_discount
                        ? (int) round($basePrice * (1 - $destination->kids_discount / 100))
                        : $basePrice;

                    $combinedAddress = $member['address'] . ', ' . ($member['city'] ?? $data['city']) . ', ' . ($member['province'] ?? $data['province']);

                    $visitor = Visitor::create([
                        'destination_id' => $destination->id,
                        'group_id' => $groupId,
                        'visit_date' => Carbon::today()->toDateString(),
                        'ticket_no' => $ticketNo,
                        'name' => $member['name'],
                        'email' => $member['email'] ?? null,
                        'age' => $age,
                        'address' => $combinedAddress,
                        'address_type' => $member['address_type'] ?? $data['address_type'],
                        'city' => $member['city'] ?? $data['city'],
                        'province' => $member['province'] ?? $data['province'],
                        'community' => $data['community'] ?? null,
                        'purpose' => $destination->has_purpose ? ($data['purpose'] ?? 'Normal') : 'Normal',
                        'camping_duration' => ($destination->has_purpose && in_array(strtolower($data['purpose'] ?? ''), ['camping'])) ? $data['camping_duration'] : null,
                        'qty_male' => $qtyMale,
                        'qty_female' => $qtyFemale,
                        'qty_kids' => $qtyKids,
                        'qty_total' => 1,
                        'avg_age' => $age,
                        'price' => $memberPrice,
                        'total_price' => $memberPrice,
                        'payment_method' => $paymentMethod,
                        'payment_token' => $paymentToken,
                        'payment_status' => $paymentStatus,
                        'status' => $status,
                        'checked_in_at' => $checkedInAt,
                    ]);
                    $createdTicketIds[] = $visitor->id;
                }
            } else {
                $qtyMale = (int) ($data['qty_male'] ?? 0);
                $qtyFemale = (int) ($data['qty_female'] ?? 0);
                $qtyKids = (int) ($data['qty_kids'] ?? 0);
                $leaderGender = $data['leader_gender'] ?? 'L';

                if ($leaderGender === 'L') {
                    $qtyMale += 1;
                } else {
                    $qtyFemale += 1;
                }

                $qtyTotal = $qtyMale + $qtyFemale + $qtyKids;
                
                $duration = 1;
                if ($destination->has_purpose && in_array(strtolower($data['purpose'] ?? ''), ['camping'])) {
                    $duration = (int) ($data['camping_duration'] ?? 1);
                    if ($duration < 1) $duration = 1;
                }
                $basePrice = (int) $data['price'] * $duration;

                $kidsPrice = $destination->kids_discount > 0 ? (int) round($basePrice * (1 - $destination->kids_discount / 100)) : $basePrice;
                $totalPrice = ($qtyMale + $qtyFemale) * $basePrice + $qtyKids * $kidsPrice;
                $ticketNo = $this->generateTicketNumber();
                $combinedAddress = $data['leader_address'] . ', ' . $data['city'] . ', ' . $data['province'];

                $visitor = Visitor::create([
                    'destination_id' => $destination->id,
                    'group_id' => $ticketNo,
                    'visit_date' => Carbon::today()->toDateString(),
                    'ticket_no' => $ticketNo,
                    'name' => $data['name'],
                    'email' => $data['leader_email'] ?? null,
                    'age' => (int) $data['leader_age'],
                    'address' => $combinedAddress,
                    'address_type' => $data['address_type'],
                    'city' => $data['city'],
                    'province' => $data['province'],
                    'community' => $data['community'] ?? null,
                    'purpose' => $destination->has_purpose ? ($data['purpose'] ?? 'Normal') : 'Normal',
                    'camping_duration' => ($destination->has_purpose && in_array(strtolower($data['purpose'] ?? ''), ['camping'])) ? $data['camping_duration'] : null,
                    'qty_male' => $qtyMale,
                    'qty_female' => $qtyFemale,
                    'qty_kids' => $qtyKids,
                    'qty_total' => $qtyTotal,
                    'avg_age' => (int) $data['avg_age'],
                    'price' => $basePrice,
                    'total_price' => $totalPrice,
                    'payment_method' => $paymentMethod,
                    'payment_token' => $paymentToken,
                    'payment_status' => $paymentStatus,
                    'status' => $status,
                    'checked_in_at' => $checkedInAt,
                ]);
                $groupId = $ticketNo;
                $createdTicketIds[] = $visitor->id;
            }

            return [
                'group_id' => $groupId,
                'ticket_ids' => $createdTicketIds,
            ];
        });
    }

    /**
     * Unify completing payments for both PendingRegistration and Visitor groups.
     */
    public function completePayment(string $tempToken, $paymentDetails): bool
    {
        // 1. Try online flow (PendingRegistration)
        $pending = PendingRegistration::where('temp_token', $tempToken)->first();
        if ($pending) {
            if ($pending->status !== 'completed') {
                $this->createVisitorsFromPending($pending, $paymentDetails);
            }
            return true;
        }

        // 2. Try offline POS flow (Visitor)
        $visitors = Visitor::where('payment_token', $tempToken)->get();
        if ($visitors->isNotEmpty()) {
            $groupId = $visitors->first()->group_id;
            
            DB::transaction(function () use ($groupId, $paymentDetails) {
                Visitor::where('group_id', $groupId)->update([
                    'payment_status' => 'success',
                    'payment_details' => json_encode($paymentDetails),
                    'status' => 'in',
                    'checked_in_at' => Carbon::now(),
                ]);
            });
            return true;
        }

        return false;
    }
}
