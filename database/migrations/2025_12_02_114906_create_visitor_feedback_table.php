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
        Schema::create('visitor_feedback', function (Blueprint $table) {
            $table->id();
            $table->foreignId('visitor_id')->constrained('visitors')->onDelete('cascade');
            $table->integer('rating')->nullable(); // 1-5 stars
            $table->text('comments')->nullable();
            $table->json('survey_responses')->nullable(); // For structured survey data
            $table->string('feedback_type')->default('general'); // general, service, facility, staff
            $table->boolean('is_anonymous')->default(false);
            $table->string('visitor_email')->nullable(); // If not anonymous
            $table->boolean('is_public')->default(false); // For displaying on dashboard
            $table->timestamps();

            $table->index('visitor_id');
            $table->index('rating');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('visitor_feedback');
    }
};
