<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('deployed_items', function (Blueprint $table) {
            $table->string('qr_code_image')->nullable()->after('qrCode');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('deployed_items', function (Blueprint $table) {
            $table->dropColumn('qr_code_image');
        });
    }
};
