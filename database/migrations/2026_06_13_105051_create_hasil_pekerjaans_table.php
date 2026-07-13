<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hasil_pekerjaans', function (Blueprint $table) {
            $table->id();

            $table->foreignId('id_pesanan')
                ->unique()
                ->constrained('pesanans')
                ->cascadeOnDelete();

            $table->text('catatan')->nullable();
            $table->string('file_hasil');

            $table->enum('status_hasil', [
                'menunggu_review',
                'disetujui',
                'revisi'
            ])->default('menunggu_review');

            $table->timestamp('tanggal_upload')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hasil_pekerjaans');
    }
};
