<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('withdrawals', function (Blueprint $table) {
            $table->id();

            $table->foreignId('id_freelancer')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->decimal('jumlah_pencairan', 12, 2);

            $table->string('nama_bank');
            $table->string('nomor_rekening');
            $table->string('nama_pemilik_rekening');

            $table->enum('status_withdrawal', [
                'pending',
                'approved',
                'rejected'
            ])->default('pending');

            $table->text('catatan_admin')->nullable();

            $table->timestamp('tanggal_pengajuan')->nullable();
            $table->timestamp('tanggal_diproses')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('withdrawals');
    }
};
