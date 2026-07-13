<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notifikasi', function (Blueprint $table) {
            $table->id();

            $table->foreignId('id_user')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->string('judul');
            $table->text('pesan');

            $table->enum('tipe', [
                'order',
                'progress',
                'revisi',
                'hasil',
                'pembayaran',
                'withdrawal',
                'dispute',
                'system'
            ])->default('system');

            $table->string('url')->nullable();

            $table->boolean('dibaca')->default(false);
            $table->timestamp('dibaca_pada')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifikasi');
    }
};
