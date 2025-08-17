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
        Schema::create('supplies', function (Blueprint $table) {
            $table->id('itemID');
            $table->string('name');
            $table->text('description')->nullable();
            $table->dateTime('acquired_at');
            $table->string('estimated_life')->nullable();
            $table->decimal('unit_cost', 15, 2);
            $table->integer('quantity');
            $table->decimal('amount', 15, 2);
            $table->integer('minimum_stock')->default(0);
            $table->date('last_restock_date')->nullable();
            $table->foreignId('category_id')->constrained('categories', 'categoryID');
            $table->unsignedBigInteger('department_id')->nullable();
            $table->string('fund_code');
            $table->string('pp_sub_account');
            $table->string('gl_code');
            $table->foreignId('added_by')->constrained('users');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('supplies');
    }
}; 