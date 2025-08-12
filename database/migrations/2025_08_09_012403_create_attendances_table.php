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
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignUlid('user_id')->constrained('users');
            $table->foreignId('barcode_id')->nullable()->constrained('barcodes');
            $table->date('date')->nullable();
            $table->time('time_in')->nullable(); // absensi masuk
            $table->time('time_out')->nullable(); // absensi keluar
            $table->foreignId('shift_id')->nullable()->constrained('shifts');
            $table->double('latitude')->nullable(); // lokasi absensi sumbu Y
            $table->double('longitude')->nullable(); // lokasi absensi sumbu X
            $table->foreignId('status_id')->nullable()->constrained('statuses'); // referensi ke tabel status
            $table->string('request_type')->nullable(); // jenis pengajuan yang diminta user (sick, excused, leave, etc)
            $table->string('note')->nullable(); // keterangan
            $table->string('attachment')->nullable(); // lampiran
            
            // Kolom untuk approval
            $table->timestamp('approved_at')->nullable(); // waktu disetujui
            $table->foreignUlid('approved_by')->nullable()->constrained('users'); // disetujui oleh admin
            $table->timestamp('rejected_at')->nullable(); // waktu ditolak
            $table->foreignUlid('rejected_by')->nullable()->constrained('users'); // ditolak oleh admin
            $table->string('rejection_reason')->nullable(); // alasan penolakan
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};