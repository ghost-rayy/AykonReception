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
        Schema::create('recurring_appointments', function (Blueprint $table) {
            $table->id();
            $table->string('visitor_name');
            $table->string('visitor_phone');
            $table->string('visitor_email')->nullable();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('purpose');
            $table->text('notes')->nullable();
            $table->time('appointment_time'); // Time of day (e.g., 10:00 AM)
            $table->integer('duration_minutes')->default(60);
            $table->enum('recurrence_type', ['daily', 'weekly', 'monthly', 'yearly'])->default('weekly');
            $table->integer('recurrence_interval')->default(1); // Every X days/weeks/months
            $table->json('recurrence_days')->nullable(); // For weekly: [1,3,5] = Mon, Wed, Fri
            $table->date('start_date');
            $table->date('end_date')->nullable(); // Null = no end date
            $table->integer('occurrences')->nullable(); // Number of occurrences, null = unlimited
            $table->enum('status', ['active', 'paused', 'completed', 'canceled'])->default('active');
            $table->timestamps();

            $table->index(['user_id', 'status']);
            $table->index('start_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('recurring_appointments');
    }
};
