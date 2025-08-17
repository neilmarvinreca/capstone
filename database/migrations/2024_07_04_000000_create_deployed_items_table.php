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
        Schema::create('deployed_items', function (Blueprint $table) {
            $table->id('deployedID');
            $table->string('itemName');
            $table->text('itemDescription')->nullable();
            $table->dateTime('dateAcquired');
            $table->decimal('cost', 15, 2);
            $table->string('itemCategory');
            $table->string('qrCode')->unique();
            $table->unsignedBigInteger('departmentID');
            $table->date('dateDeployed');
            $table->string('status');
            $table->text('remarks')->nullable();
            $table->integer('quantity')->default(1);
            $table->string('condition')->default('new');
            $table->unsignedBigInteger('supply_id')->nullable();
            $table->string('purpose')->nullable();
            $table->unsignedBigInteger('deployed_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('departmentID')->references('departmentID')->on('departments')->onDelete('cascade');
            $table->foreign('supply_id')->references('itemID')->on('supplies')->onDelete('set null');
            $table->foreign('deployed_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('deployed_items');
    }
}; 