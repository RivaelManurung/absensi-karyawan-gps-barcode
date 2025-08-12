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
        // Check if tables already exist before creating
        if (!Schema::hasTable('user_movements')) {
            Schema::create('user_movements', function (Blueprint $table) {
                $table->id();
                $table->string('user_id'); // Match users table ULID type
                $table->decimal('latitude', 10, 7);
                $table->decimal('longitude', 10, 7);
                $table->decimal('accuracy', 8, 2)->nullable();
                $table->timestamp('recorded_at');
                $table->string('device_info')->nullable();
                $table->json('additional_data')->nullable(); // for storing extra sensor data
                $table->timestamps();
                
                // No foreign key constraint - just index for performance
                $table->index(['user_id', 'recorded_at']);
                $table->index(['latitude', 'longitude']);
            });
        }

        if (!Schema::hasTable('attendance_logs')) {
            Schema::create('attendance_logs', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('attendance_id');
                $table->string('action'); // check_in, check_out, update, approve, reject
                $table->json('old_data')->nullable();
                $table->json('new_data')->nullable();
                $table->string('performed_by'); // Match users table ULID type
                $table->string('ip_address')->nullable();
                $table->string('user_agent')->nullable();
                $table->text('notes')->nullable();
                $table->timestamps();
                
                $table->foreign('attendance_id')->references('id')->on('attendances')->onDelete('cascade');
                // No foreign key constraint for performed_by - just index
                $table->index(['attendance_id', 'action']);
                $table->index(['performed_by', 'created_at']);
            });
        }

        if (!Schema::hasTable('geofence_violations')) {
            Schema::create('geofence_violations', function (Blueprint $table) {
                $table->id();
                $table->string('user_id'); // Match users table ULID type
                $table->unsignedBigInteger('barcode_id')->nullable();
                $table->string('violation_type'); // distance, time, accuracy, suspicious_movement
                $table->decimal('latitude', 10, 7);
                $table->decimal('longitude', 10, 7);
                $table->decimal('distance', 8, 2)->nullable();
                $table->json('violation_data'); // additional context
                $table->enum('severity', ['low', 'medium', 'high', 'critical'])->default('medium');
                $table->boolean('is_resolved')->default(false);
                $table->text('notes')->nullable();
                $table->timestamps();
                
                // No foreign key constraint for user_id - just index
                $table->foreign('barcode_id')->references('id')->on('barcodes')->onDelete('set null');
                $table->index(['user_id', 'violation_type']);
                $table->index(['severity', 'is_resolved']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('geofence_violations');
        Schema::dropIfExists('attendance_logs');
        Schema::dropIfExists('user_movements');
    }
};
