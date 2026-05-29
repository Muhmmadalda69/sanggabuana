<?php

namespace App\Services;

use Midtrans\Config;
use Midtrans\Snap;
use Midtrans\Transaction;
use Midtrans\Notification;

class MidtransService
{
    public function __construct()
    {
        Config::$serverKey = config('midtrans.server_key');
        Config::$clientKey = config('midtrans.client_key');
        Config::$isProduction = config('midtrans.is_production');
        Config::$isSanitized = config('midtrans.is_sanitized');
        Config::$is3ds = config('midtrans.is_3ds');
    }

    public function createSnapTransaction(array $params): array
    {
        try {
            $snapToken = Snap::getSnapToken($params);
            return [
                'success' => true,
                'snap_token' => $snapToken,
                'redirect_url' => null,
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    public function handleNotification(): object
    {
        return new Notification();
    }

    public function checkTransactionStatus(string $orderId): ?object
    {
        try {
            return Transaction::status($orderId);
        } catch (\Exception $e) {
            return null;
        }
    }

    public function cancelTransaction(string $orderId): ?object
    {
        try {
            return Transaction::cancel($orderId);
        } catch (\Exception $e) {
            return null;
        }
    }

    public function getTransactionId(object $status): ?string
    {
        return $status->transaction_id ?? null;
    }

    public function getPaymentStatus(object $status): string
    {
        $transactionStatus = $status->transaction_status ?? '';
        $fraudStatus = $status->fraud_status ?? '';

        return match (true) {
            $transactionStatus === 'capture' && $fraudStatus === 'accept' => 'success',
            $transactionStatus === 'settlement' => 'success',
            $transactionStatus === 'pending' => 'pending',
            $transactionStatus === 'deny' => 'failed',
            $transactionStatus === 'cancel' => 'failed',
            $transactionStatus === 'expire' => 'expired',
            default => 'pending',
        };
    }

    public function buildCustomerDetails(string $name, ?string $email, string $phone = ''): array
    {
        $validEmail = $email;
        if (empty($validEmail) || !filter_var($validEmail, FILTER_VALIDATE_EMAIL)) {
            $validEmail = 'guest@wisatasanggabuana.com';
        }
        return [
            'first_name' => $name,
            'email' => $validEmail,
            'phone' => $phone,
        ];
    }

    public function buildTransactionDetails(string $orderId, int $grossAmount, array $items = [], string $paymentMethod = null): array
    {
        return [
            'transaction_details' => [
                'order_id'       => $orderId,
                'gross_amount'   => $grossAmount,
            ],
            'item_details'    => $items,
            'custom_expiry' => [
                'order_time' => date('Y-m-d H:i:s T'),
                'expiry_duration' => 1,
                'unit' => 'day'
            ],
            'customer_details' => [
                'first_name' => 'Customer',
                'email' => 'customer@wisatasanggabuana.com',
                'phone' => ''
            ],
        ];
    }
    
    public function getPaymentFees(string $paymentType): array
    {
        $type = strtolower($paymentType);
        $fees = [
            'bank_transfer' => [
                'type' => 'fix',
                'amount' => 10000,
                'percentage' => 0
            ],
            'va' => [
                'type' => 'fix',
                'amount' => 10000,
                'percentage' => 0
            ],
            'qris' => [
                'type' => 'percentage',
                'amount' => 0,
                'percentage' => 0.02
            ],
            'ewallet' => [
                'type' => 'percentage',
                'amount' => 0,
                'percentage' => 0.05
            ],
            'cstore' => [
                'type' => 'fix',
                'amount' => 10000,
                'percentage' => 0
            ],
            'alfamart' => [
                'type' => 'fix',
                'amount' => 10000,
                'percentage' => 0
            ]
        ];
        
        return $fees[$type] ?? ['type' => 'percentage', 'amount' => 0, 'percentage' => 0];
    }
    
    public function getPaymentGroup(string $paymentMethod): string
    {
        $method = strtolower($paymentMethod);
        
        $groups = [
            'bca' => 'VA',
            'bni' => 'VA',
            'bri' => 'VA',
            'mandiri' => 'VA',
            'permata' => 'VA',
            'qris' => 'QRIS',
            'gopay' => 'EWALLET',
            'shopeepay' => 'EWALLET',
            'dana' => 'EWALLET',
            'grab_pay' => 'EWALLET',
            'alfamart' => 'ALFAMART',
            'indomaret' => 'ALFAMART',
        ];
        
        return $groups[$method] ?? 'OTHERS';
    }
    
    public function getPaymentMethods(): array
    {
        return [
            'VA' => [
                'name' => 'Virtual Account',
                'methods' => [
                    ['code' => 'bca', 'name' => 'BCA Virtual Account', 'icon' => '/images/payment/bca.svg'],
                    ['code' => 'bni', 'name' => 'BNI Virtual Account', 'icon' => '/images/payment/bni.svg'],
                    ['code' => 'bri', 'name' => 'BRI Virtual Account', 'icon' => '/images/payment/bri.svg'],
                    ['code' => 'mandiri', 'name' => 'Mandiri Virtual Account', 'icon' => '/images/payment/mandiri.svg'],
                    ['code' => 'permata', 'name' => 'Permata Virtual Account', 'icon' => '/images/payment/permata.svg']
                ],
                'fee' => $this->getPaymentFees('VA')
            ],
            'QRIS' => [
                'name' => 'QRIS',
                'methods' => [
                    ['code' => 'qris', 'name' => 'QRIS', 'icon' => '/images/payment/qris.svg']
                ],
                'fee' => $this->getPaymentFees('qris')
            ],
            'EWALLET' => [
                'name' => 'E-Money',
                'methods' => [
                    ['code' => 'gopay', 'name' => 'GoPay', 'icon' => '/images/payment/gopay.svg'],
                    ['code' => 'shopeepay', 'name' => 'ShopeePay', 'icon' => '/images/payment/shopeepay.svg']
                ],
                'fee' => $this->getPaymentFees('ewallet')
            ],
            'ALFAMART' => [
                'name' => 'Convenience Store',
                'methods' => [
                    ['code' => 'alfamart', 'name' => 'Alfamart', 'icon' => '/images/payment/alfamart.svg']
                ],
                'fee' => $this->getPaymentFees('alfamart')
            ]
        ];
    }
}
