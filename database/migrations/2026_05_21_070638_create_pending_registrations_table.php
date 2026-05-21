<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pending_registrations', function (Blueprint $table) {
            $table->id();
            $table->string('temp_token')->unique();
            $table->foreignId('destination_id')->constrained('destinations')->onDelete('cascade');
            $table->string('slug');
            $table->date('visit_date');
            $table->json('form_data');
            $table->string('payment_method')->default('Transfer');
            $table->string('snap_token')->nullable();
            $table->string('transaction_id')->nullable();
            $table->string('status')->default('pending'); // pending, completed, expired, failed
            $table->timestamp('expires_at');
            $table->timestamps();

            $table->index('temp_token');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pending_registrations');
    }
};
