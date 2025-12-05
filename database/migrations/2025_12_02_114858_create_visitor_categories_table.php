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
        Schema::create('visitor_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // VIP, Regular, Contractor, Vendor, Guest, etc.
            $table->string('slug')->unique();
            $table->string('color')->default('#0099ff'); // For UI display
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('priority')->default(0); // For sorting
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('visitor_categories');
    }
};
