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
        Schema::table('attendances', function (Blueprint $table) {
            // Check and add location tracking fields
            if (!Schema::hasColumn('attendances', 'check_in_latitude')) {
                $table->decimal('check_in_latitude', 10, 7)->nullable()->after('time_out');
            }
            if (!Schema::hasColumn('attendances', 'check_in_longitude')) {
                $table->decimal('check_in_longitude', 10, 7)->nullable()->after('check_in_latitude');
            }
            if (!Schema::hasColumn('attendances', 'check_out_latitude')) {
                $table->decimal('check_out_latitude', 10, 7)->nullable()->after('check_in_longitude');
            }
            if (!Schema::hasColumn('attendances', 'check_out_longitude')) {
                $table->decimal('check_out_longitude', 10, 7)->nullable()->after('check_out_latitude');
            }
            
            // Check and add accuracy tracking
            if (!Schema::hasColumn('attendances', 'check_in_accuracy')) {
                $table->decimal('check_in_accuracy', 8, 2)->nullable()->after('check_out_longitude');
            }
            if (!Schema::hasColumn('attendances', 'check_out_accuracy')) {
                $table->decimal('check_out_accuracy', 8, 2)->nullable()->after('check_in_accuracy');
            }
            
            // Check and add distance tracking
            if (!Schema::hasColumn('attendances', 'check_in_distance')) {
                $table->decimal('check_in_distance', 8, 2)->nullable()->after('check_out_accuracy');
            }
            if (!Schema::hasColumn('attendances', 'check_out_distance')) {
                $table->decimal('check_out_distance', 8, 2)->nullable()->after('check_in_distance');
            }
            
            // Check and add work duration
            if (!Schema::hasColumn('attendances', 'work_duration')) {
                $table->integer('work_duration')->nullable()->after('check_out_distance');
            }
            
            // Check and add approval fields
            if (!Schema::hasColumn('attendances', 'approved_at')) {
                $table->timestamp('approved_at')->nullable()->after('work_duration');
            }
            if (!Schema::hasColumn('attendances', 'approved_by')) {
                $table->string('approved_by')->nullable()->after('approved_at'); // Use string to match ULID
            }
            if (!Schema::hasColumn('attendances', 'rejected_at')) {
                $table->timestamp('rejected_at')->nullable()->after('approved_by');
            }
            if (!Schema::hasColumn('attendances', 'rejected_by')) {
                $table->string('rejected_by')->nullable()->after('rejected_at'); // Use string to match ULID
            }
            if (!Schema::hasColumn('attendances', 'rejection_reason')) {
                $table->text('rejection_reason')->nullable()->after('rejected_by');
            }
            
            // Check and add attachment field
            if (!Schema::hasColumn('attendances', 'attachment_path')) {
                $table->string('attachment_path')->nullable()->after('rejection_reason');
            }
        });
        
        Schema::table('barcodes', function (Blueprint $table) {
            // Check and add enhanced barcode fields
            if (!Schema::hasColumn('barcodes', 'radius')) {
                $table->integer('radius')->default(50)->after('longitude'); // radius in meters
            }
            if (!Schema::hasColumn('barcodes', 'address')) {
                $table->string('address')->nullable()->after('radius');
            }
            if (!Schema::hasColumn('barcodes', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('address');
            }
            if (!Schema::hasColumn('barcodes', 'allowed_shifts')) {
                $table->json('allowed_shifts')->nullable()->after('is_active'); // for future shift management
            }
            if (!Schema::hasColumn('barcodes', 'valid_from')) {
                $table->time('valid_from')->nullable()->after('allowed_shifts');
            }
            if (!Schema::hasColumn('barcodes', 'valid_until')) {
                $table->time('valid_until')->nullable()->after('valid_from');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            // Drop columns if they exist
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
                'approved_at',
                'approved_by',
                'rejected_at',
                'rejected_by',
                'rejection_reason',
                'attachment_path'
            ];
            
            foreach ($columnsToCheck as $column) {
                if (Schema::hasColumn('attendances', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
        
        Schema::table('barcodes', function (Blueprint $table) {
            // Drop columns if they exist
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
