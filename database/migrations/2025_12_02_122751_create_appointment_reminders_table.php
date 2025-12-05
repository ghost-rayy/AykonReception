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
        Schema::create('appointment_reminders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('appointment_id')->constrained('appointments')->onDelete('cascade');
            $table->enum('type', ['email', 'sms'])->default('email');
            $table->integer('remind_before_hours')->default(24); // Hours before appointment
            $table->timestamp('scheduled_at')->nullable(); // When to send the reminder
            $table->timestamp('sent_at')->nullable(); // When reminder was actually sent
            $table->enum('status', ['pending', 'sent', 'failed'])->default('pending');
            $table->text('message')->nullable(); // Custom message
            $table->text('error_message')->nullable(); // If sending failed
            $table->timestamps();

            $table->index(['appointment_id', 'status']);
            $table->index('scheduled_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appointment_reminders');
    }
};
