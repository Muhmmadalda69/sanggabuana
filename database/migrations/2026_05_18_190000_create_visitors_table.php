<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('visitors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('destination_id')->constrained('destinations')->onDelete('cascade');
            $table->string('ticket_no')->unique();
            $table->string('name');
            $table->string('address')->nullable();
            $table->string('address_type')->default('indonesia'); // lokal, indonesia, mancanegara
            $table->string('city')->nullable();
            $table->string('province')->nullable();
            $table->string('community')->nullable();
            $table->string('purpose')->nullable()->default('Normal'); // Hiking, Trail Run, Jiarah, Normal
            $table->integer('camping_duration')->nullable()->default(null);
            $table->integer('qty_male')->default(0);
            $table->integer('qty_female')->default(0);
            $table->integer('qty_kids')->default(0);
            $table->integer('qty_total')->default(1);
            $table->integer('avg_age')->default(25);
            $table->integer('price')->default(0);
            $table->integer('total_price')->default(0);
            $table->string('payment_method')->default('Tunai');
            $table->string('status')->default('in'); // 'in' = Masuk, 'out' = Keluar
            $table->timestamp('checked_in_at')->useCurrent();
            $table->timestamp('checked_out_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('visitors');
    }
};
