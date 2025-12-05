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
        Schema::table('visitors', function (Blueprint $table) {
            if (!Schema::hasColumn('visitors', 'category_id')) {
                $table->unsignedBigInteger('category_id')->nullable()->after('user_id');
            }
            if (!Schema::hasColumn('visitors', 'email')) {
                $table->string('email')->nullable()->after('phone');
            }
            if (!Schema::hasColumn('visitors', 'company')) {
                $table->string('company')->nullable()->after('email');
            }
            if (!Schema::hasColumn('visitors', 'internal_notes')) {
                $table->text('internal_notes')->nullable()->after('notes');
            }
            if (!Schema::hasColumn('visitors', 'wait_start_time')) {
                $table->timestamp('wait_start_time')->nullable()->after('check_in_time');
            }
            if (!Schema::hasColumn('visitors', 'wait_duration_minutes')) {
                $table->integer('wait_duration_minutes')->nullable()->after('wait_start_time');
            }
            if (!Schema::hasColumn('visitors', 'is_vip')) {
                $table->boolean('is_vip')->default(false)->after('status');
            }
            if (!Schema::hasColumn('visitors', 'visitor_type')) {
                $table->string('visitor_type')->default('regular')->after('is_vip'); // regular, contractor, vendor, guest
            }
        });

        // Add foreign key constraint separately if it doesn't exist
        if (Schema::hasColumn('visitors', 'category_id') && Schema::hasTable('visitor_categories')) {
            $foreignKeys = DB::select("
                SELECT CONSTRAINT_NAME 
                FROM information_schema.KEY_COLUMN_USAGE 
                WHERE TABLE_SCHEMA = DATABASE() 
                AND TABLE_NAME = 'visitors' 
                AND COLUMN_NAME = 'category_id' 
                AND REFERENCED_TABLE_NAME IS NOT NULL
            ");
            
            if (empty($foreignKeys)) {
                Schema::table('visitors', function (Blueprint $table) {
                    $table->foreign('category_id')->references('id')->on('visitor_categories')->onDelete('set null');
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('visitors', function (Blueprint $table) {
            // Check if foreign key exists before dropping
            $foreignKeys = DB::select("
                SELECT CONSTRAINT_NAME 
                FROM information_schema.KEY_COLUMN_USAGE 
                WHERE TABLE_SCHEMA = DATABASE() 
                AND TABLE_NAME = 'visitors' 
                AND COLUMN_NAME = 'category_id' 
                AND REFERENCED_TABLE_NAME IS NOT NULL
            ");
            
            if (!empty($foreignKeys)) {
                $table->dropForeign(['category_id']);
            }
        });

        Schema::table('visitors', function (Blueprint $table) {
            $columnsToDrop = [];
            if (Schema::hasColumn('visitors', 'category_id')) $columnsToDrop[] = 'category_id';
            if (Schema::hasColumn('visitors', 'email')) $columnsToDrop[] = 'email';
            if (Schema::hasColumn('visitors', 'company')) $columnsToDrop[] = 'company';
            if (Schema::hasColumn('visitors', 'internal_notes')) $columnsToDrop[] = 'internal_notes';
            if (Schema::hasColumn('visitors', 'wait_start_time')) $columnsToDrop[] = 'wait_start_time';
            if (Schema::hasColumn('visitors', 'wait_duration_minutes')) $columnsToDrop[] = 'wait_duration_minutes';
            if (Schema::hasColumn('visitors', 'is_vip')) $columnsToDrop[] = 'is_vip';
            if (Schema::hasColumn('visitors', 'visitor_type')) $columnsToDrop[] = 'visitor_type';
            
            if (!empty($columnsToDrop)) {
                $table->dropColumn($columnsToDrop);
            }
        });
    }
};
