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
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('cascade'); // Null = all receptionists
            $table->string('type'); // appointment_reminder, visitor_arrival, staff_status_change, overdue_checkout, vip_visitor
            $table->string('title');
            $table->text('message');
            $table->string('icon')->nullable(); // Bootstrap icon class
            $table->string('color')->default('info'); // info, warning, danger, success, primary
            $table->morphs('notifiable'); // Polymorphic relation (appointment, visitor, staff, etc.)
            $table->json('data')->nullable(); // Additional data
            $table->boolean('is_read')->default(false);
            $table->timestamp('read_at')->nullable();
            $table->timestamp('expires_at')->nullable(); // Auto-dismiss after this time
            $table->timestamps();

            $table->index(['user_id', 'is_read']);
            $table->index(['type', 'is_read']);
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
