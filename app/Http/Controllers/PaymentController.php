<?php

namespace App\Http\Controllers;

use App\Models\Destination;
use App\Models\PendingRegistration;
use App\Models\Visitor;
use App\Services\BookingService;
use App\Services\MidtransService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    protected $midtrans;
    protected $booking;

    public function __construct(MidtransService $midtrans, BookingService $booking)
    {
        $this->midtrans = $midtrans;
        $this->booking = $booking;
    }

    public function pay(Request $request, $tempToken)
    {
        // 1. Try to handle online PendingRegistration
        $pending = PendingRegistration::where('temp_token', $tempToken)
            ->where('status', 'pending')
            ->first();

        if ($pending) {
            // Strict authorization check: Only the visitor who created it can view/pay
            if ($pending->visitor_account_id && $pending->visitor_account_id !== Auth::guard('visitor')->id()) {
                abort(403, 'Aksi tidak diizinkan.');
            }

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
                    'isVisitorDirect' => false,
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
                $items[] = [
                    'id' => 'TICKET',
                    'price' => $ticketTotal,
                    'quantity' => 1,
                    'name' => 'Tiket ' . ($pending->form_data['leader']['name'] ?? '') . ' (' . $pending->destination->name . ')',
                ];
            }

            $paymentMethod = $request->input('payment_method') ?: $pending->payment_method ?: 'qris';
            $paymentMethodDetails = $request->input('payment_method_details');
            $paymentMethodType = '';

            if ($paymentMethodDetails) {
                $details = json_decode($paymentMethodDetails, true);
                if ($details) {
                    $pending->payment_method = $details['name'] ?? $pending->payment_method;
                    $paymentMethodType = $details['group'] ?? '';
                }
            }

            if (empty($paymentMethodType) && !empty($pending->payment_method)) {
                $paymentMethodType = $this->midtrans->getPaymentGroup($pending->payment_method);
            }

            $paymentGroup = $paymentMethodType ?: 'QRIS';
            $feeConfig = $this->midtrans->getPaymentFees($paymentGroup);
            if ($feeConfig['type'] === 'fix') {
                $adminFee = $feeConfig['amount'];
            } else {
                $adminFee = (int) round($ticketTotal * ($feeConfig['percentage'] ?? 0.02));
            }

            $grossAmount = $ticketTotal + $adminFee;
            $items[] = [
                'id' => 'ADMIN-FEE',
                'price' => $adminFee,
                'quantity' => 1,
                'name' => 'Biaya Admin ' . ($pending->payment_method ?? 'Pembayaran'),
            ];

            $transaction = $this->midtrans->buildTransactionDetails($orderId, $grossAmount, $items, $paymentMethodType);
            $transaction['customer_details'] = $customerDetails;

            $enabledPayments = [];
            $paymentType = 'credit_card';

            $midtransChannelMap = [
                'bca' => ['enabled' => ['bca_va'], 'type' => 'bank_transfer'],
                'bni' => ['enabled' => ['bni_va'], 'type' => 'bank_transfer'],
                'bri' => ['enabled' => ['bri_va'], 'type' => 'bank_transfer'],
                'permata' => ['enabled' => ['permata_va'], 'type' => 'bank_transfer'],
                'mandiri' => ['enabled' => ['echannel'], 'type' => 'bank_transfer'],
                'qris' => ['enabled' => ['other_qris'], 'type' => 'qris'],
                'gopay' => ['enabled' => ['gopay'], 'type' => 'gopay'],
                'shopeepay' => ['enabled' => ['shopeepay'], 'type' => 'shopeepay'],
                'alfamart' => ['enabled' => ['alfamart'], 'type' => 'cstore'],
            ];

            $methodCode = strtolower($paymentMethod);
            if (isset($midtransChannelMap[$methodCode])) {
                $channel = $midtransChannelMap[$methodCode];
                $enabledPayments = $channel['enabled'];
                $paymentType = $channel['type'];

                if ($paymentMethodType === 'VA' && $methodCode !== 'mandiri') {
                    $transaction['bank_transfer'] = ['bank' => $methodCode];
                }
                if ($paymentMethodType === 'ALFAMART') {
                    $transaction['cstore'] = ['store' => $methodCode];
                }
            } else {
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
                'isVisitorDirect' => false,
            ]);
        }

        // 2. Try to handle offline POS direct Visitor booking
        $visitor = Visitor::where('payment_token', $tempToken)
            ->where('payment_status', 'pending')
            ->first();

        if ($visitor) {
            $group = Visitor::where('group_id', $visitor->group_id)->get();
            $firstVisitor = $group->first();

            // If snap_token already exists, show payment page
            if ($firstVisitor->snap_token) {
                return view('payment.index', [
                    'snapToken' => $firstVisitor->snap_token,
                    'pending' => $firstVisitor,
                    'isVisitorDirect' => true,
                ]);
            }

            $orderId = 'REG-' . substr($visitor->payment_token, 0, 20) . '-' . time();
            $leaderEmail = $firstVisitor->email ?: '';
            if (empty($leaderEmail) || !filter_var($leaderEmail, FILTER_VALIDATE_EMAIL)) {
                $leaderEmail = 'guest_' . $visitor->payment_token . '@wisatasanggabuana.com';
            }

            $customerDetails = $this->midtrans->buildCustomerDetails(
                $firstVisitor->name ?: 'Guest',
                $leaderEmail,
            );

            $items = [];
            $ticketTotal = (int) $group->sum('total_price');

            foreach ($group as $v) {
                $items[] = [
                    'id' => $v->ticket_no,
                    'price' => (int) $v->price,
                    'quantity' => (int) $v->qty_total,
                    'name' => 'Tiket ' . $v->name . ' (' . $v->destination->name . ')',
                ];
            }

            $paymentMethod = $request->input('payment_method') ?: $firstVisitor->payment_method ?: 'qris';
            $paymentMethodDetails = $request->input('payment_method_details');
            $paymentMethodType = '';

            if ($paymentMethodDetails) {
                $details = json_decode($paymentMethodDetails, true);
                if ($details) {
                    $paymentMethod = $details['name'] ?? $paymentMethod;
                    $paymentMethodType = $details['group'] ?? '';
                }
            }

            if (empty($paymentMethodType)) {
                $paymentMethodType = $this->midtrans->getPaymentGroup($paymentMethod);
            }

            $paymentGroup = $paymentMethodType ?: 'QRIS';
            $feeConfig = $this->midtrans->getPaymentFees($paymentGroup);
            if ($feeConfig['type'] === 'fix') {
                $adminFee = $feeConfig['amount'];
            } else {
                $adminFee = (int) round($ticketTotal * ($feeConfig['percentage'] ?? 0.02));
            }

            $grossAmount = $ticketTotal + $adminFee;
            $items[] = [
                'id' => 'ADMIN-FEE',
                'price' => $adminFee,
                'quantity' => 1,
                'name' => 'Biaya Admin ' . $paymentMethod,
            ];

            $transaction = $this->midtrans->buildTransactionDetails($orderId, $grossAmount, $items, $paymentMethodType);
            $transaction['customer_details'] = $customerDetails;

            $enabledPayments = [];
            $paymentType = 'credit_card';

            $midtransChannelMap = [
                'bca' => ['enabled' => ['bca_va'], 'type' => 'bank_transfer'],
                'bni' => ['enabled' => ['bni_va'], 'type' => 'bank_transfer'],
                'bri' => ['enabled' => ['bri_va'], 'type' => 'bank_transfer'],
                'permata' => ['enabled' => ['permata_va'], 'type' => 'bank_transfer'],
                'mandiri' => ['enabled' => ['echannel'], 'type' => 'bank_transfer'],
                'qris' => ['enabled' => ['other_qris'], 'type' => 'qris'],
                'gopay' => ['enabled' => ['gopay'], 'type' => 'gopay'],
                'shopeepay' => ['enabled' => ['shopeepay'], 'type' => 'shopeepay'],
                'alfamart' => ['enabled' => ['alfamart'], 'type' => 'cstore'],
            ];

            $methodCode = strtolower($paymentMethod);
            if (isset($midtransChannelMap[$methodCode])) {
                $channel = $midtransChannelMap[$methodCode];
                $enabledPayments = $channel['enabled'];
                $paymentType = $channel['type'];

                if ($paymentMethodType === 'VA' && $methodCode !== 'mandiri') {
                    $transaction['bank_transfer'] = ['bank' => $methodCode];
                }
                if ($paymentMethodType === 'ALFAMART') {
                    $transaction['cstore'] = ['store' => $methodCode];
                }
            } else {
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
                'finish' => route('payment.finish', $visitor->payment_token),
                'error' => route('payment.finish', $visitor->payment_token),
            ];

            $result = $this->midtrans->createSnapTransaction($transaction);
            if (!$result['success']) {
                return redirect()->route('admin.pos.index')
                    ->with('error', 'Gagal membuat transaksi pembayaran: ' . ($result['message'] ?? 'Unknown error'));
            }

            Visitor::where('group_id', $visitor->group_id)->update([
                'transaction_id' => $orderId,
                'snap_token' => $result['snap_token'],
                'payment_method' => $paymentMethod,
            ]);

            return view('payment.index', [
                'snapToken' => $result['snap_token'],
                'pending' => $firstVisitor,
                'isVisitorDirect' => true,
            ]);
        }

        abort(404, 'Transaksi tidak ditemukan.');
    }

    public function finish($tempToken)
    {
        // 1. Check online PendingRegistration flow
        $pending = PendingRegistration::where('temp_token', $tempToken)->first();
        if ($pending) {
            // Strict authorization check: Only the visitor who created it can view
            if ($pending->visitor_account_id && $pending->visitor_account_id !== Auth::guard('visitor')->id()) {
                abort(403, 'Aksi tidak diizinkan.');
            }

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

            if ($pending->transaction_id) {
                $status = $this->midtrans->checkTransactionStatus($pending->transaction_id);
                if ($status) {
                    $paymentStatus = $this->midtrans->getPaymentStatus($status);
                    if ($paymentStatus === 'success') {
                        $this->booking->createVisitorsFromPending($pending, $status);
                    } elseif (in_array($paymentStatus, ['failed', 'expired'])) {
                        $pending->update(['status' => $paymentStatus]);
                    }
                }
            }

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

        // 2. Check offline POS direct Visitor booking flow
        $visitor = Visitor::where('payment_token', $tempToken)->first();
        if ($visitor) {
            $group = Visitor::where('group_id', $visitor->group_id)->get();
            $firstVisitor = $group->first();

            if ($firstVisitor->payment_status === 'success') {
                return view('payment.finish', [
                    'visitor' => $firstVisitor,
                    'group' => $group,
                    'totalAmount' => $group->sum('total_price'),
                    'destination' => $firstVisitor->destination,
                ]);
            }

            if ($firstVisitor->transaction_id) {
                $status = $this->midtrans->checkTransactionStatus($firstVisitor->transaction_id);
                if ($status) {
                    $paymentStatus = $this->midtrans->getPaymentStatus($status);
                    if ($paymentStatus === 'success') {
                        $this->booking->completePayment($tempToken, (array) $status);
                        // Refresh group collection
                        $group = Visitor::where('group_id', $visitor->group_id)->get();
                        $firstVisitor = $group->first();
                    } elseif (in_array($paymentStatus, ['failed', 'expired'])) {
                        Visitor::where('group_id', $visitor->group_id)->update(['payment_status' => $paymentStatus]);
                    }
                }
            }

            return view('payment.finish', [
                'visitor' => $firstVisitor,
                'group' => $group,
                'totalAmount' => $group->sum('total_price'),
                'destination' => $firstVisitor->destination,
            ]);
        }

        abort(404, 'Transaksi tidak ditemukan.');
    }

    public function success($tempToken)
    {
        return redirect()->route('payment.finish', $tempToken);
    }

    public function changeMethod(Request $request, $tempToken)
    {
        $validated = $request->validate([
            'payment_method' => 'required|string|in:bca,bni,bri,mandiri,permata,qris,gopay,shopeepay,alfamart',
        ]);

        $pending = PendingRegistration::where('temp_token', $tempToken)
            ->where('status', 'pending')
            ->first();

        if ($pending) {
            if ($pending->visitor_account_id && $pending->visitor_account_id !== Auth::guard('visitor')->id()) {
                abort(403, 'Aksi tidak diizinkan.');
            }

            $pending->payment_method = $validated['payment_method'];
            $pending->snap_token = null;
            $pending->transaction_id = null;
            $pending->save();

            return redirect()->route('payment.pay', $pending->temp_token);
        }

        $visitor = Visitor::where('payment_token', $tempToken)
            ->where('payment_status', 'pending')
            ->first();

        if ($visitor) {
            Visitor::where('group_id', $visitor->group_id)->update([
                'payment_method' => $validated['payment_method'],
                'snap_token' => null,
                'transaction_id' => null,
            ]);

            return redirect()->route('payment.pay', $visitor->payment_token);
        }

        abort(404, 'Transaksi tidak ditemukan.');
    }

    public function notificationHandler(Request $request)
    {
        try {
            $notification = $this->midtrans->handleNotification();
            
            // SECURITY HARDENING: Verify webhook request signature
            if (!$this->midtrans->verifySignature($notification)) {
                Log::warning('Midtrans spoofed notification attempt detected and blocked.', [
                    'order_id' => $notification->order_id ?? null,
                ]);
                return response('Invalid Signature', 400);
            }

            $orderId = $notification->order_id;
            $transactionStatus = $notification->transaction_status;
            $fraudStatus = $notification->fraud_status;

            Log::info('Midtrans secure notification received', [
                'order_id' => $orderId,
                'transaction_status' => $transactionStatus,
                'fraud_status' => $fraudStatus,
            ]);

            $parts = explode('-', $orderId);
            if (count($parts) < 3 || $parts[0] !== 'REG') {
                Log::warning('Invalid order_id format: ' . $orderId);
                return response('OK', 200);
            }
            $tempToken = $parts[1];

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
                $this->booking->completePayment($tempToken, (array) $notification->getResponse());
            } else {
                // Try updating pending registration
                $pending = PendingRegistration::where('temp_token', $tempToken)->first();
                if ($pending) {
                    $pending->update(['status' => $paymentStatus]);
                } else {
                    // Try updating direct visitors
                    $visitor = Visitor::where('payment_token', $tempToken)->first();
                    if ($visitor) {
                        Visitor::where('group_id', $visitor->group_id)->update(['payment_status' => $paymentStatus]);
                    }
                }
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
        if ($pending) {
            return response()->json([
                'payment_status' => $pending->status === 'completed' ? 'success' : $pending->status,
                'status' => $pending->status,
            ]);
        }

        $visitor = Visitor::where('payment_token', $tempToken)->first();
        if ($visitor) {
            return response()->json([
                'payment_status' => $visitor->payment_status,
                'status' => $visitor->payment_status,
            ]);
        }

        return response()->json(['status' => 'not_found']);
    }

    public function paymentMethods()
    {
        return response()->json(
            $this->midtrans->getPaymentMethods()
        );
    }

    public function cancel($tempToken)
    {
        $pending = PendingRegistration::where('temp_token', $tempToken)
            ->where('status', 'pending')
            ->first();

        if ($pending) {
            if ($pending->visitor_account_id !== Auth::guard('visitor')->id()) {
                abort(403, 'Aksi tidak diizinkan.');
            }

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

        $visitor = Visitor::where('payment_token', $tempToken)
            ->where('payment_status', 'pending')
            ->first();

        if ($visitor) {
            // Cashier/POS visitor cancel is only allowed by superadmin or assigned cashier
            $user = Auth::user();
            if (!$user || (!$user->isSuperAdmin() && $user->destination_id !== $visitor->destination_id)) {
                abort(403, 'Aksi tidak diizinkan.');
            }

            if ($visitor->transaction_id) {
                try {
                    $this->midtrans->cancelTransaction($visitor->transaction_id);
                } catch (\Exception $e) {
                    Log::warning('Midtrans cancel failed: ' . $e->getMessage());
                }
            }

            Visitor::where('group_id', $visitor->group_id)->update(['payment_status' => 'failed']);

            return redirect()->route('admin.monitoring.index')
                ->with('success', 'Transaksi pembayaran POS berhasil dibatalkan.');
        }

        abort(404, 'Transaksi tidak ditemukan.');
    }
}
