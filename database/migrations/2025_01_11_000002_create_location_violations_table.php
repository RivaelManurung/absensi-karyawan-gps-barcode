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
        Schema::create('location_violations', function (Blueprint $table) {
            $table->id();
            $table->string('user_id'); // Match users table ULID type
            $table->string('violation_type'); // distance_exceeded, suspicious_movement, accuracy_low
            $table->decimal('latitude', 10, 8);
            $table->decimal('longitude', 11, 8);
            $table->decimal('distance_from_location', 8, 2)->nullable();
            $table->decimal('accuracy', 8, 2)->nullable();
            $table->string('severity')->default('medium'); // low, medium, high
            $table->text('description')->nullable();
            $table->json('metadata')->nullable(); // Store additional violation data
            $table->boolean('is_resolved')->default(false);
            $table->timestamp('resolved_at')->nullable();
            $table->timestamps();
            
            $table->index(['user_id', 'violation_type']);
            $table->index('severity');
            $table->index('is_resolved');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('location_violations');
    }
};
