<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('visitors', function (Blueprint $table) {
            $table->string('transaction_id')->nullable()->after('total_price')->unique();
            $table->string('payment_status')->default('pending')->after('transaction_id');
            $table->text('payment_details')->nullable()->after('payment_status');
            $table->string('snap_token')->nullable()->after('payment_details');
            $table->timestamp('payment_settlement_at')->nullable()->after('snap_token');
        });
    }

    public function down(): void
    {
        Schema::table('visitors', function (Blueprint $table) {
            $table->dropColumn(['transaction_id', 'payment_status', 'payment_details', 'snap_token', 'payment_settlement_at']);
        });
    }
};
