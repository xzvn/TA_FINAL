<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('revisis', function (Blueprint $table) {
            $table->id();

            $table->foreignId('id_pesanan')
                ->constrained('pesanans')
                ->cascadeOnDelete();

            $table->text('catatan_revisi');

            $table->enum('status_revisi', [
                'diajukan',
                'diproses',
                'selesai'
            ])->default('diajukan');

            $table->timestamp('tanggal_revisi')->nullable();
            $table->timestamp('tanggal_selesai')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('revisis');
    }
};
