<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Create a new table with the desired structure
        Schema::create('departments_new', function (Blueprint $table) {
            $table->id('departmentID');
            $table->string('locationcode')->unique();
            $table->string('officename');
            $table->unsignedBigInteger('accountableper');
            $table->string('description');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('accountableper')->references('id')->on('users')->onDelete('cascade');
        });

        // Copy data from old table to new table
        DB::statement('INSERT INTO departments_new (locationcode, officename, accountableper, description, created_at, updated_at, deleted_at) SELECT locationcode, officename, accountableper, description, created_at, updated_at, deleted_at FROM departments');

        // Drop the old table
        Schema::drop('departments');

        // Rename the new table
        Schema::rename('departments_new', 'departments');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Create a new table with the old structure
        Schema::create('departments_old', function (Blueprint $table) {
            $table->string('departmentID')->primary();
            $table->string('locationcode')->unique();
            $table->string('officename');
            $table->unsignedBigInteger('accountableper');
            $table->string('description');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('accountableper')->references('id')->on('users')->onDelete('cascade');
        });

        // Copy data back (we'll generate new string IDs)
        DB::statement('INSERT INTO departments_old (departmentID, locationcode, officename, accountableper, description, created_at, updated_at, deleted_at) SELECT CONCAT("DEPT-", id), locationcode, officename, accountableper, description, created_at, updated_at, deleted_at FROM departments');

        // Drop the new table
        Schema::drop('departments');

        // Rename the old table
        Schema::rename('departments_old', 'departments');
    }
};
