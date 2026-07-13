<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('disputes', function (Blueprint $table) {
            $table->id();

            $table->foreignId('id_pesanan')
                ->unique()
                ->constrained('pesanans')
                ->cascadeOnDelete();

            $table->foreignId('id_customer')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->foreignId('id_freelancer')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->text('alasan_dispute');
            $table->string('bukti_dispute')->nullable();

            $table->enum('status_dispute', [
                'pending',
                'diproses',
                'refund',
                'lanjutkan_pesanan',
                'ditolak',
                'selesai'
            ])->default('pending');

            $table->text('keputusan_admin')->nullable();

            $table->timestamp('tanggal_pengajuan')->nullable();
            $table->timestamp('tanggal_diproses')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('disputes');
    }
};
