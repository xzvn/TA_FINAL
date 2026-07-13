<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pesanans', function (Blueprint $table) {
            $table->id();

            $table->foreignId('id_customer')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->foreignId('id_freelancer')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->foreignId('id_jasa')
                ->constrained('jasa')
                ->cascadeOnDelete();

            $table->text('deskripsi_kebutuhan');
            $table->string('file_requirement')->nullable();

            $table->decimal('total_harga', 12, 2);

            $table->enum('status_pesanan', [
                'menunggu_pembayaran',
                'dibayar',
                'diproses',
                'menunggu_approve',
                'revisi',
                'selesai',
                'dibatalkan',
                'dispute'
            ])->default('menunggu_pembayaran');

            $table->unsignedTinyInteger('jumlah_revisi')->default(0);
            $table->unsignedTinyInteger('batas_revisi')->default(3);

            $table->timestamp('tanggal_pesan')->nullable();
            $table->date('deadline')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pesanans');
    }
};
