<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('visitors', function (Blueprint $table) {
            $table->foreignId('visitor_account_id')->nullable()->after('id')->constrained('visitor_accounts')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('visitors', function (Blueprint $table) {
            $table->dropForeign(['visitor_account_id']);
            $table->dropColumn('visitor_account_id');
        });
    }
};
