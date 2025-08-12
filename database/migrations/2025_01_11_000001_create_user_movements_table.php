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
        Schema::create('user_movements', function (Blueprint $table) {
            $table->id();
            $table->string('user_id'); // Match users table ULID type
            $table->decimal('latitude', 10, 8);
            $table->decimal('longitude', 11, 8);
            $table->decimal('accuracy', 8, 2)->nullable();
            $table->string('activity_type')->default('unknown'); // checkin, checkout, movement
            $table->timestamp('recorded_at');
            $table->json('additional_data')->nullable(); // Store extra GPS data
            $table->timestamps();
            
            $table->index(['user_id', 'recorded_at']);
            $table->index('activity_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_movements');
    }
};
