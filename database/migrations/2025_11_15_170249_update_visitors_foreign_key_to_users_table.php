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
        Schema::table('visitors', function (Blueprint $table) {
            // Drop the old foreign key constraint to staff table (constraint name from original migration)
            $table->dropForeign(['staff_id']); // Original constraint name

            // Add new foreign key constraint to users table
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('visitors', function (Blueprint $table) {
            // Drop the foreign key to users table
            $table->dropForeign(['user_id']);

            // Add back the foreign key to staff table
            $table->foreign('user_id')->references('id')->on('staff')->onDelete('cascade');
        });
    }
};
