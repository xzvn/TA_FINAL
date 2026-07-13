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
        Schema::create('chats', function (Blueprint $table) {
            $table->id();

            $table->foreignId('id_jasa')
                ->constrained('jasa')
                ->cascadeOnDelete();

            $table->foreignId('id_customer')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->foreignId('id_freelancer')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->foreignId('pengirim_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->text('pesan');
            $table->string('lampiran')->nullable();
            $table->timestamp('waktu_kirim')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chats');
    }
};
