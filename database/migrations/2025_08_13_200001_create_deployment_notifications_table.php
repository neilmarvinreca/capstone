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
        Schema::create('deployment_notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->unsignedBigInteger('deployed_item_id');
            $table->text('message');
            $table->boolean('is_read')->default(false);
            $table->string('type')->default('deployment');
            $table->json('data')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            $table->foreign('deployed_item_id')->references('deployedID')->on('deployed_items')->onDelete('cascade');
            $table->index(['user_id', 'is_read']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('deployment_notifications');
    }
};
