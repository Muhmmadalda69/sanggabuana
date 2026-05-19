<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('destinations', function (Blueprint $table) {
            $table->boolean('has_community')->default(false)->after('is_active');
            $table->boolean('has_purpose')->default(false)->after('has_community');
            $table->boolean('has_gender_details')->default(false)->after('has_purpose');
        });
    }

    public function down(): void
    {
        Schema::table('destinations', function (Blueprint $table) {
            $table->dropColumn(['has_community', 'has_purpose', 'has_gender_details']);
        });
    }
};
