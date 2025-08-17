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
        Schema::create('deployment_requests', function (Blueprint $table) {
            $table->id('requestID');
            $table->unsignedBigInteger('deployedID');
            $table->enum('requestType', ['transfer', 'status_change', 'maintenance', 'other']);
            $table->foreignId('requestBy')->constrained('users')->onDelete('cascade');
            $table->dateTime('requestDate');
            $table->foreignId('checkedBy')->nullable()->constrained('users')->onDelete('set null');
            $table->text('remarks')->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->timestamps();
            
            $table->foreign('deployedID')->references('deployedID')->on('deployed_items')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('deployment_requests');
    }
};
