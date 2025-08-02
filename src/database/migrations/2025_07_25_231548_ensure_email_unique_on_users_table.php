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
        // Create a code to make an email unique in the users table
        Schema::table('users', function (Blueprint $table) {
            // Ensure the email column is unique
            $table->string('email')->unique()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove the unique constraint on the email column
        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique(['email']);
        });
    }
};
