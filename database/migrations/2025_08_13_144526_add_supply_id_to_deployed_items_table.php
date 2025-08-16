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
            $table->unsignedBigInteger('supply_id')->nullable()->after('departmentID');
            $table->foreign('supply_id')->references('itemID')->on('supplies')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('deployed_items', function (Blueprint $table) {
            $table->dropForeign(['supply_id']);
            $table->dropColumn('supply_id');
        });
    }
};
