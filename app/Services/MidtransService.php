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

    public function buildTransactionDetails(string $orderId, int $grossAmount, array $items = []): array
    {
        return [
            'transaction_details' => [
                'order_id' => $orderId,
                'gross_amount' => $grossAmount,
            ],
            'item_details' => $items,
            'enabled_payments' => ['gopay'],
        ];
    }
}
