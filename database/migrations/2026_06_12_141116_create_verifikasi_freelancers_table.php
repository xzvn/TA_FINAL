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
        Schema::create('verifikasi_freelancers', function (Blueprint $table) {
            $table->id();

            $table->foreignId('id_freelancer')
                ->unique()
                ->constrained('users')
                ->cascadeOnDelete();

            $table->string('email_kampus')->unique();
            $table->string('universitas');
            $table->string('program_studi')->nullable();

            $table->string('file_ktm');
            $table->text('catatan_admin')->nullable();

            $table->enum('status_verifikasi', [
                'pending',
                'approved',
                'rejected'
            ])->default('pending');

            $table->timestamp('tanggal_pengajuan')->nullable();
            $table->timestamp('tanggal_verifikasi')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('verifikasi_freelancers');
    }
};
