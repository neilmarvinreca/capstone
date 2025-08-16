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
        // Get all deployed items with deployedID = 0
        $items = \DB::table('deployed_items')->where('deployedID', 0)->get();
        
        // Update each item with a new unique ID
        foreach ($items as $item) {
            $newId = 'DP-' . strtoupper(uniqid());
            \DB::table('deployed_items')
                ->where('deployedID', 0)
                ->limit(1)
                ->update(['deployedID' => $newId]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
