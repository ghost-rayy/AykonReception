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
        Schema::table('appointments', function (Blueprint $table) {
            if (!Schema::hasColumn('appointments', 'recurring_appointment_id')) {
                $table->unsignedBigInteger('recurring_appointment_id')->nullable()->after('user_id');
            }
            if (!Schema::hasColumn('appointments', 'visitor_email')) {
                $table->string('visitor_email')->nullable()->after('visitor_phone');
            }
            if (!Schema::hasColumn('appointments', 'duration_minutes')) {
                $table->integer('duration_minutes')->default(60)->after('appointment_time');
            }
            if (!Schema::hasColumn('appointments', 'notes')) {
                $table->text('notes')->nullable()->after('purpose');
            }
            if (!Schema::hasColumn('appointments', 'original_appointment_time')) {
                $table->timestamp('original_appointment_time')->nullable()->after('appointment_time');
            }
            if (!Schema::hasColumn('appointments', 'rescheduled_at')) {
                $table->timestamp('rescheduled_at')->nullable()->after('original_appointment_time');
            }
            if (!Schema::hasColumn('appointments', 'rescheduled_by')) {
                $table->unsignedBigInteger('rescheduled_by')->nullable()->after('rescheduled_at');
            }
        });

        // Add foreign key constraints separately if tables exist
        if (Schema::hasTable('recurring_appointments') && Schema::hasColumn('appointments', 'recurring_appointment_id')) {
            $foreignKeys = DB::select("
                SELECT CONSTRAINT_NAME 
                FROM information_schema.KEY_COLUMN_USAGE 
                WHERE TABLE_SCHEMA = DATABASE() 
                AND TABLE_NAME = 'appointments' 
                AND COLUMN_NAME = 'recurring_appointment_id' 
                AND REFERENCED_TABLE_NAME IS NOT NULL
            ");
            
            if (empty($foreignKeys)) {
                Schema::table('appointments', function (Blueprint $table) {
                    $table->foreign('recurring_appointment_id')->references('id')->on('recurring_appointments')->onDelete('cascade');
                });
            }
        }

        if (Schema::hasTable('users') && Schema::hasColumn('appointments', 'rescheduled_by')) {
            $foreignKeys = DB::select("
                SELECT CONSTRAINT_NAME 
                FROM information_schema.KEY_COLUMN_USAGE 
                WHERE TABLE_SCHEMA = DATABASE() 
                AND TABLE_NAME = 'appointments' 
                AND COLUMN_NAME = 'rescheduled_by' 
                AND REFERENCED_TABLE_NAME IS NOT NULL
            ");
            
            if (empty($foreignKeys)) {
                Schema::table('appointments', function (Blueprint $table) {
                    $table->foreign('rescheduled_by')->references('id')->on('users')->onDelete('set null');
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            if (Schema::hasColumn('appointments', 'recurring_appointment_id')) {
                $table->dropForeign(['recurring_appointment_id']);
            }
            if (Schema::hasColumn('appointments', 'rescheduled_by')) {
                $table->dropForeign(['rescheduled_by']);
            }
            
            $columnsToDrop = [];
            if (Schema::hasColumn('appointments', 'recurring_appointment_id')) $columnsToDrop[] = 'recurring_appointment_id';
            if (Schema::hasColumn('appointments', 'visitor_email')) $columnsToDrop[] = 'visitor_email';
            if (Schema::hasColumn('appointments', 'duration_minutes')) $columnsToDrop[] = 'duration_minutes';
            if (Schema::hasColumn('appointments', 'notes')) $columnsToDrop[] = 'notes';
            if (Schema::hasColumn('appointments', 'original_appointment_time')) $columnsToDrop[] = 'original_appointment_time';
            if (Schema::hasColumn('appointments', 'rescheduled_at')) $columnsToDrop[] = 'rescheduled_at';
            if (Schema::hasColumn('appointments', 'rescheduled_by')) $columnsToDrop[] = 'rescheduled_by';
            
            if (!empty($columnsToDrop)) {
                $table->dropColumn($columnsToDrop);
            }
        });
    }
};
