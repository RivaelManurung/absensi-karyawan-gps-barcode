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
        // Check and add columns to attendances table
        $attendanceColumns = DB::select("SHOW COLUMNS FROM attendances");
        $existingAttendanceColumns = array_column($attendanceColumns, 'Field');
        
        Schema::table('attendances', function (Blueprint $table) use ($existingAttendanceColumns) {
            // Location tracking fields
            if (!in_array('check_in_latitude', $existingAttendanceColumns)) {
                $table->decimal('check_in_latitude', 10, 7)->nullable();
            }
            if (!in_array('check_in_longitude', $existingAttendanceColumns)) {
                $table->decimal('check_in_longitude', 10, 7)->nullable();
            }
            if (!in_array('check_out_latitude', $existingAttendanceColumns)) {
                $table->decimal('check_out_latitude', 10, 7)->nullable();
            }
            if (!in_array('check_out_longitude', $existingAttendanceColumns)) {
                $table->decimal('check_out_longitude', 10, 7)->nullable();
            }
            
            // Accuracy tracking
            if (!in_array('check_in_accuracy', $existingAttendanceColumns)) {
                $table->decimal('check_in_accuracy', 8, 2)->nullable();
            }
            if (!in_array('check_out_accuracy', $existingAttendanceColumns)) {
                $table->decimal('check_out_accuracy', 8, 2)->nullable();
            }
            
            // Distance from barcode location
            if (!in_array('check_in_distance', $existingAttendanceColumns)) {
                $table->decimal('check_in_distance', 8, 2)->nullable();
            }
            if (!in_array('check_out_distance', $existingAttendanceColumns)) {
                $table->decimal('check_out_distance', 8, 2)->nullable();
            }
            
            // Work duration in minutes
            if (!in_array('work_duration', $existingAttendanceColumns)) {
                $table->integer('work_duration')->nullable();
            }
            
            // File attachment
            if (!in_array('attachment_path', $existingAttendanceColumns)) {
                $table->string('attachment_path')->nullable();
            }
        });
        
        // Check and add columns to barcodes table
        $barcodeColumns = DB::select("SHOW COLUMNS FROM barcodes");
        $existingBarcodeColumns = array_column($barcodeColumns, 'Field');
        
        Schema::table('barcodes', function (Blueprint $table) use ($existingBarcodeColumns) {
            // Enhanced barcode fields
            if (!in_array('radius', $existingBarcodeColumns)) {
                $table->integer('radius')->default(50);
            }
            if (!in_array('address', $existingBarcodeColumns)) {
                $table->string('address')->nullable();
            }
            if (!in_array('is_active', $existingBarcodeColumns)) {
                $table->boolean('is_active')->default(true);
            }
            if (!in_array('allowed_shifts', $existingBarcodeColumns)) {
                $table->json('allowed_shifts')->nullable();
            }
            if (!in_array('valid_from', $existingBarcodeColumns)) {
                $table->time('valid_from')->nullable();
            }
            if (!in_array('valid_until', $existingBarcodeColumns)) {
                $table->time('valid_until')->nullable();
            }
        });
        
        // Add foreign keys if they don't exist
        $foreignKeys = DB::select("
            SELECT CONSTRAINT_NAME 
            FROM information_schema.KEY_COLUMN_USAGE 
            WHERE TABLE_SCHEMA = DATABASE() 
            AND TABLE_NAME = 'attendances' 
            AND CONSTRAINT_NAME LIKE '%_foreign'
        ");
        
        $existingForeignKeys = array_column($foreignKeys, 'CONSTRAINT_NAME');
        
        if (in_array('approved_by', $existingAttendanceColumns) && !in_array('attendances_approved_by_foreign', $existingForeignKeys)) {
            Schema::table('attendances', function (Blueprint $table) {
                $table->foreign('approved_by')->references('id')->on('users')->onDelete('set null');
            });
        }
        
        if (in_array('rejected_by', $existingAttendanceColumns) && !in_array('attendances_rejected_by_foreign', $existingForeignKeys)) {
            Schema::table('attendances', function (Blueprint $table) {
                $table->foreign('rejected_by')->references('id')->on('users')->onDelete('set null');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            // Drop foreign keys first
            try {
                $table->dropForeign(['approved_by']);
            } catch (Exception $e) {
                // Ignore if doesn't exist
            }
            
            try {
                $table->dropForeign(['rejected_by']);
            } catch (Exception $e) {
                // Ignore if doesn't exist
            }
            
            // Drop columns
            $columnsToCheck = [
                'check_in_latitude',
                'check_in_longitude', 
                'check_out_latitude',
                'check_out_longitude',
                'check_in_accuracy',
                'check_out_accuracy',
                'check_in_distance',
                'check_out_distance',
                'work_duration',
                'attachment_path'
            ];
            
            foreach ($columnsToCheck as $column) {
                if (Schema::hasColumn('attendances', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
        
        Schema::table('barcodes', function (Blueprint $table) {
            $columnsToCheck = [
                'radius',
                'address',
                'is_active',
                'allowed_shifts',
                'valid_from',
                'valid_until'
            ];
            
            foreach ($columnsToCheck as $column) {
                if (Schema::hasColumn('barcodes', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
