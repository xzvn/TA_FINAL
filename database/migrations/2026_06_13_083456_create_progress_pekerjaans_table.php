<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('progress_pekerjaans', function (Blueprint $table) {
            $table->id();

            $table->foreignId('id_pesanan')
                ->constrained('pesanans')
                ->cascadeOnDelete();

            $table->string('judul_progress');
            $table->text('deskripsi_progress')->nullable();
            $table->unsignedTinyInteger('persentase_progress')->default(0);
            $table->string('file_progress')->nullable();
            $table->timestamp('tanggal_upload')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('progress_pekerjaans');
    }
};
