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
        Schema::create('visitor_preregistrations', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('phone');
            $table->string('email')->nullable();
            $table->string('company')->nullable();
            $table->string('purpose');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // Staff to visit
            $table->foreignId('category_id')->nullable()->constrained('visitor_categories')->onDelete('set null');
            $table->timestamp('expected_arrival_time');
            $table->text('notes')->nullable();
            $table->string('status')->default('pending'); // pending, confirmed, canceled, completed
            $table->string('qr_code')->unique()->nullable(); // For quick check-in
            $table->timestamp('checked_in_at')->nullable();
            $table->foreignId('checked_in_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();

            $table->index('qr_code');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('visitor_preregistrations');
    }
};
