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
        Schema::create('attendance_logs', function (Blueprint $table) {
            $table->id();
            $table->string('user_id'); // Match users table ULID type
            $table->unsignedBigInteger('attendance_id')->nullable();
            $table->string('action'); // checkin_attempt, checkout_attempt, validation_failed
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->decimal('accuracy', 8, 2)->nullable();
            $table->boolean('is_successful')->default(false);
            $table->string('failure_reason')->nullable();
            $table->json('device_info')->nullable(); // Store device and browser info
            $table->timestamps();
            
            $table->index(['user_id', 'action']);
            $table->index('is_successful');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendance_logs');
    }
};
