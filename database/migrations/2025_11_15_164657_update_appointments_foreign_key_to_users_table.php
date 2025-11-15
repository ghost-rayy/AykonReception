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
        Schema::table('appointments', function (Blueprint $table) {
            // Drop the existing foreign key constraint
            $table->dropForeign(['staff_id']);

            // Rename column from staff_id to user_id
            $table->renameColumn('staff_id', 'user_id');

            // Add new foreign key constraint to users table
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            // Drop the foreign key to users
            $table->dropForeign(['user_id']);

            // Rename column back to staff_id
            $table->renameColumn('user_id', 'staff_id');

            // Add back the foreign key to staff table
            $table->foreign('staff_id')->references('id')->on('staff')->onDelete('cascade');
        });
    }
};
