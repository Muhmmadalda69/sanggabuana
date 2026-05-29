<?php

namespace App\Http\Controllers;

use App\Models\Destination;
use App\Models\PendingRegistration;
use App\Models\Visitor;
use App\Services\MidtransService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class PaymentController extends Controller
{
    protected $midtrans;

    public function __construct(MidtransService $midtrans)
    {
        $this->midtrans = $midtrans;
    }

    public function pay(Request $request, $tempToken)
    {
        $pending = PendingRegistration::where('temp_token', $tempToken)
            ->where('status', 'pending')
            ->firstOrFail();

        if ($pending->expires_at->isPast()) {
            $pending->update(['status' => 'expired']);
            return redirect()->route('destination.register.date', $pending->slug)
                ->with('error', 'Sesi registrasi telah kadaluwarsa. Silakan daftar ulang.');
        }

        // If snap_token already exists, show payment page
        if ($pending->snap_token) {
            return view('payment.index', [
                'snapToken' => $pending->snap_token,
                'pending' => $pending,
            ]);
        }

        $orderId = 'REG-' . substr($pending->temp_token, 0, 20) . '-' . time();

        $leaderEmail = $pending->form_data['leader']['email'] ?? '';
        if (empty($leaderEmail) || !filter_var($leaderEmail, FILTER_VALIDATE_EMAIL)) {
            $leaderEmail = 'guest_' . $pending->temp_token . '@wisatasanggabuana.com';
        }

        $customerDetails = $this->midtrans->buildCustomerDetails(
            $pending->form_data['leader']['name'] ?? 'Guest',
            $leaderEmail,
        );

        $items = [];
        $ticketTotal = (int) $pending->form_data['total_amount'];

        if (!empty($pending->form_data['items'])) {
            foreach ($pending->form_data['items'] as $i => $item) {
                $items[] = [
                    'id' => 'ITEM-' . ($i + 1),
                    'price' => (int) $item['price'],
                    'quantity' => $item['quantity'],
                    'name' => $item['name'],
                ];
            }
        } else {
            // Non-member-details: single ticket
            $items[] = [
                'id' => 'TICKET',
                'price' => $ticketTotal,
                'quantity' => 1,
                'name' => 'Tiket ' . ($pending->form_data['leader']['name'] ?? '') . ' (' . $pending->destination->name . ')',
            ];
        }

            // Parse payment method details from form or database fallback
            $adminFee = 0;
            $paymentMethodDetails = $request->input('payment_method_details');
            $paymentMethodType = '';
            
            if ($paymentMethodDetails) {
                $details = json_decode($paymentMethodDetails, true);
                if ($details) {
                    $pending->payment_method = $details['name'] ?? $pending->payment_method;
                    $paymentMethodType = $details['group'] ?? '';
                }
            }
            
            // If paymentMethodType is empty (e.g., redirected GET request), determine it from database
            if (empty($paymentMethodType) && !empty($pending->payment_method)) {
                $paymentMethodType = $this->midtrans->getPaymentGroup($pending->payment_method);
            }
            
            // Calculate admin fee based on payment method group
            $paymentGroup = $paymentMethodType ?: 'QRIS';
            $feeConfig = $this->midtrans->getPaymentFees($paymentGroup);
            if ($feeConfig['type'] === 'fix') {
                $adminFee = $feeConfig['amount'];
            } else {
                $adminFee = (int) round($ticketTotal * ($feeConfig['percentage'] ?? 0.02));
            }
            
            $paymentMethod = $request->input('payment_method') ?: $pending->payment_method ?: 'qris';

            $grossAmount = $ticketTotal + $adminFee;

            // Update admin fee item based on selected payment method
            $items[] = [
                'id'        => 'ADMIN-FEE',
                'price'      => $adminFee,
                'quantity'   => 1,
                'name'       => 'Biaya Admin ' . ($pending->payment_method ?? 'Pembayaran'),
            ];

            $transaction = $this->midtrans->buildTransactionDetails($orderId, $grossAmount, $items, $paymentMethodType);
            $transaction['customer_details'] = $customerDetails;
            
            // Configure enabled payments based on method type and specific method code
            $enabledPayments = [];
            $paymentType = 'credit_card';
            
            // Map user-facing codes to Midtrans Snap enabled_payments codes
            // Note: 'qris' standalone doesn't work in Sandbox — use 'other_qris' + 'gopay'
            $midtransChannelMap = [
                'bca'       => ['enabled' => ['bca_va'],               'type' => 'bank_transfer'],
                'bni'       => ['enabled' => ['bni_va'],               'type' => 'bank_transfer'],
                'bri'       => ['enabled' => ['bri_va'],               'type' => 'bank_transfer'],
                'permata'   => ['enabled' => ['permata_va'],           'type' => 'bank_transfer'],
                'mandiri'   => ['enabled' => ['echannel'],             'type' => 'bank_transfer'],
                'qris'      => ['enabled' => ['other_qris'],           'type' => 'qris'],
                'gopay'     => ['enabled' => ['gopay'],                'type' => 'gopay'],
                'shopeepay' => ['enabled' => ['shopeepay'],            'type' => 'shopeepay'],
                'alfamart'  => ['enabled' => ['alfamart'],             'type' => 'cstore'],
            ];
            
            $methodCode = strtolower($paymentMethod);
            
            if (isset($midtransChannelMap[$methodCode])) {
                $channel = $midtransChannelMap[$methodCode];
                $enabledPayments = $channel['enabled'];
                $paymentType = $channel['type'];
                
                // Add bank_transfer details for VA (except Mandiri which uses echannel)
                if ($paymentMethodType === 'VA' && $methodCode !== 'mandiri') {
                    $transaction['bank_transfer'] = ['bank' => $methodCode];
                }
                // Add cstore details for convenience stores
                if ($paymentMethodType === 'ALFAMART') {
                    $transaction['cstore'] = ['store' => $methodCode];
                }
            } else {
                // Fallback: enable all channels for the group
                switch ($paymentMethodType) {
                    case 'VA':
                        $enabledPayments = ['bca_va', 'bni_va', 'bri_va', 'permata_va', 'echannel'];
                        $paymentType = 'bank_transfer';
                        break;
                    case 'QRIS':
                        $enabledPayments = ['other_qris', 'gopay'];
                        $paymentType = 'qris';
                        break;
                    case 'EWALLET':
                        $enabledPayments = ['gopay', 'shopeepay'];
                        $paymentType = 'gopay';
                        break;
                    case 'ALFAMART':
                        $enabledPayments = ['alfamart'];
                        $paymentType = 'cstore';
                        break;
                    default:
                        $enabledPayments = ['bca_va', 'bni_va', 'bri_va', 'permata_va', 'echannel', 'other_qris', 'gopay', 'shopeepay', 'alfamart'];
                        $paymentType = 'bank_transfer';
                }
            }
            
            $transaction['enabled_payments'] = $enabledPayments;
            $transaction['payment_type'] = $paymentType;
            
            $transaction['callbacks'] = [
                'finish' => route('payment.finish', $pending->temp_token),
                'error' => route('payment.finish', $pending->temp_token),
            ];

            $result = $this->midtrans->createSnapTransaction($transaction);

        if (!$result['success']) {
            return redirect()->route('destination.register.date', $pending->slug)
                ->with('error', 'Gagal membuat transaksi pembayaran: ' . ($result['message'] ?? 'Unknown error'));
        }

        $pending->transaction_id = $orderId;
        $pending->snap_token = $result['snap_token'];
        $pending->save();

        return view('payment.index', [
            'snapToken' => $result['snap_token'],
            'pending' => $pending,
        ]);
    }

    public function finish($tempToken)
    {
        $pending = PendingRegistration::where('temp_token', $tempToken)->firstOrFail();

        // If already completed, find created visitors
        if ($pending->status === 'completed') {
            $group = Visitor::where('group_id', $pending->transaction_id)->get();
            if ($group->isNotEmpty()) {
                return view('payment.finish', [
                    'visitor' => $group->first(),
                    'group' => $group,
                    'totalAmount' => $group->sum('total_price'),
                    'destination' => $pending->destination,
                ]);
            }
        }

        // Check transaction status with Midtrans
        if ($pending->transaction_id) {
            $status = $this->midtrans->checkTransactionStatus($pending->transaction_id);
            if ($status) {
                $paymentStatus = $this->midtrans->getPaymentStatus($status);
                if ($paymentStatus === 'success') {
                    $this->createVisitorsFromPending($pending, $status);
                } elseif (in_array($paymentStatus, ['failed', 'expired'])) {
                    $pending->update(['status' => $paymentStatus]);
                }
            }
        }

        // After processing, check if completed
        if ($pending->status === 'completed') {
            $group = Visitor::where('group_id', $pending->transaction_id)->get();
            if ($group->isNotEmpty()) {
                return view('payment.finish', [
                    'visitor' => $group->first(),
                    'group' => $group,
                    'totalAmount' => $group->sum('total_price'),
                    'destination' => $pending->destination,
                ]);
            }
        }

        // Return expired/failed/pending view
        $dummyVisitor = new Visitor();
        $dummyVisitor->payment_token = $pending->temp_token;
        $dummyVisitor->payment_method = $pending->payment_method;
        $dummyVisitor->payment_status = $pending->status;

        return view('payment.finish', [
            'visitor' => $dummyVisitor,
            'group' => collect(),
            'totalAmount' => $pending->form_data['total_amount'] ?? 0,
            'destination' => $pending->destination,
        ]);
    }

    public function success($tempToken)
    {
        return redirect()->route('payment.finish', $tempToken);
    }

    public function changeMethod(Request $request, $tempToken)
    {
        $pending = PendingRegistration::where('temp_token', $tempToken)
            ->where('status', 'pending')
            ->firstOrFail();

        $validated = $request->validate([
            'payment_method' => 'required|string|in:bca,bni,bri,mandiri,permata,qris,gopay,shopeepay,alfamart',
        ]);

        $pending->payment_method = $validated['payment_method'];
        $pending->snap_token = null;
        $pending->transaction_id = null;
        $pending->save();

        return redirect()->route('payment.pay', $pending->temp_token);
    }

    public function notificationHandler(Request $request)
    {
        try {
            $notification = $this->midtrans->handleNotification();
            $orderId = $notification->order_id;
            $transactionStatus = $notification->transaction_status;
            $fraudStatus = $notification->fraud_status;
            $transactionId = $notification->transaction_id;
            $settlementAt = $notification->settlement_time ?? null;

            Log::info('Midtrans notification received', [
                'order_id' => $orderId,
                'transaction_status' => $transactionStatus,
                'fraud_status' => $fraudStatus,
            ]);

            // Extract temp_token from order_id (REG-{temp_token}-{timestamp})
            $parts = explode('-', $orderId);
            if (count($parts) < 3 || $parts[0] !== 'REG') {
                Log::warning('Invalid order_id format: ' . $orderId);
                return response('OK', 200);
            }
            $tempToken = $parts[1]; // UUID is the second part

            $pending = PendingRegistration::where('temp_token', $tempToken)->first();
            if (!$pending) {
                return response('OK', 200);
            }

            $paymentStatus = match (true) {
                $transactionStatus === 'capture' && $fraudStatus === 'accept' => 'success',
                $transactionStatus === 'settlement' => 'success',
                $transactionStatus === 'pending' => 'pending',
                $transactionStatus === 'deny' => 'failed',
                $transactionStatus === 'cancel' => 'failed',
                $transactionStatus === 'expire' => 'expired',
                default => 'pending',
            };

            if ($paymentStatus === 'success') {
                $this->createVisitorsFromPending($pending, $notification->getResponse());
            } elseif (in_array($paymentStatus, ['failed', 'expired'])) {
                $pending->update(['status' => $paymentStatus]);
            }

            return response('OK', 200);
        } catch (\Exception $e) {
            Log::error('Midtrans notification error: ' . $e->getMessage());
            return response('OK', 200);
        }
    }

    public function status($tempToken)
    {
        $pending = PendingRegistration::where('temp_token', $tempToken)->first();

        if (!$pending) {
            return response()->json(['status' => 'not_found']);
        }

        return response()->json([
            'payment_status' => $pending->status === 'completed' ? 'success' : $pending->status,
            'status' => $pending->status,
        ]);
    }

    private function createVisitorsFromPending(PendingRegistration $pending, $midtransResponse)
    {
        if ($pending->status === 'completed') {
            return;
        }

        $destination = $pending->destination;
        $formData = $pending->form_data;
        $visitDate = $pending->visit_date->toDateString();

        $groupId = $pending->transaction_id;
        $todayStr = Carbon::now()->format('Ymd');
        $maxTicket = Visitor::whereDate('created_at', Carbon::today())
            ->where('ticket_no', 'like', 'TKT-' . $todayStr . '-%')
            ->max('ticket_no');
        $ticketCounter = $maxTicket ? (int) substr($maxTicket, -4) : 0;

        $leader = $formData['leader'];

        if (!empty($formData['has_member_details'])) {
            // Create leader
            $leaderAge = (int) $leader['age'];
            $leaderGender = $leader['gender'] ?? 'L';
            $ticketNo = 'TKT-' . $todayStr . '-' . str_pad(++$ticketCounter, 4, '0', STR_PAD_LEFT);

            $leaderQtyMale = 0; $leaderQtyFemale = 0; $leaderQtyKids = 0;
            if ($leaderAge < 5) { $leaderQtyKids = 1; }
            elseif ($leaderGender === 'L') { $leaderQtyMale = 1; }
            else { $leaderQtyFemale = 1; }

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
                'price' => (int) ($formData['items'][0]['price'] ?? $destination->price),
                'total_price' => (int) ($formData['items'][0]['price'] ?? $destination->price),
                'payment_method' => $pending->payment_method,
                'payment_status' => 'success',
                'payment_details' => json_encode($midtransResponse),
                'status' => 'pending',
            ], $pending->visitor_account_id ? ['visitor_account_id' => $pending->visitor_account_id] : []));

            // Create members
            foreach ($formData['members'] ?? [] as $i => $member) {
                $ticketNo = 'TKT-' . $todayStr . '-' . str_pad(++$ticketCounter, 4, '0', STR_PAD_LEFT);

                $qtyMale = 0; $qtyFemale = 0; $qtyKids = 0;
                $age = (int) $member['age'];
                $isChild = !empty($member['is_child']) && $member['is_child'] == '1';

                if ($isChild) { $qtyKids = 1; }
                elseif (($member['gender'] ?? 'L') === 'L') { $qtyMale = 1; }
                else { $qtyFemale = 1; }

                $memberPrice = $isChild && $destination->kids_discount
                    ? (int) round($destination->price * (1 - $destination->kids_discount / 100))
                    : (int) $destination->price;

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

            $ticketNo = 'TKT-' . $todayStr . '-' . str_pad(++$ticketCounter, 4, '0', STR_PAD_LEFT);
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
                'price' => (int) $destination->price,
                'total_price' => (int) $formData['total_amount'],
                'payment_method' => $pending->payment_method,
                'payment_status' => 'success',
                'payment_details' => json_encode($midtransResponse),
                'status' => 'pending',
            ], $pending->visitor_account_id ? ['visitor_account_id' => $pending->visitor_account_id] : []));
        }

        $pending->update(['status' => 'completed']);
    }

    public function paymentMethods()
    {
        return response()->json(
            (new \App\Services\MidtransService())->getPaymentMethods()
        );
    }

    public function cancel($tempToken)
    {
        $pending = PendingRegistration::where('temp_token', $tempToken)
            ->where('status', 'pending')
            ->firstOrFail();

        // Only the owner can cancel
        if ($pending->visitor_account_id !== Auth::guard('visitor')->id()) {
            abort(403);
        }

        // Try to cancel Midtrans transaction if exists
        if ($pending->transaction_id) {
            try {
                $this->midtrans->cancelTransaction($pending->transaction_id);
            } catch (\Exception $e) {
                Log::warning('Midtrans cancel failed: ' . $e->getMessage());
            }
        }

        $pending->update(['status' => 'failed']);

        return redirect()->route('visitor.riwayat')
            ->with('success', 'Transaksi berhasil dibatalkan.');
    }
}
