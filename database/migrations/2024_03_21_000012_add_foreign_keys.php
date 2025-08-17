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
        // Add foreign key constraint for users.department_id
        Schema::table('users', function (Blueprint $table) {
            $table->foreign('department_id')->references('departmentID')->on('departments')->onDelete('set null');
        });

        // Add foreign key constraint for departments.accountableper
        Schema::table('departments', function (Blueprint $table) {
            $table->foreign('accountableper')->references('id')->on('users')->onDelete('set null');
        });

        // Add foreign key constraint for supplies.department_id
        Schema::table('supplies', function (Blueprint $table) {
            $table->foreign('department_id')->references('departmentID')->on('departments')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop foreign key constraints
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['department_id']);
        });

        Schema::table('departments', function (Blueprint $table) {
            $table->dropForeign(['accountableper']);
        });

        Schema::table('supplies', function (Blueprint $table) {
            $table->dropForeign(['department_id']);
        });
    }
};
