<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();

            $table->foreignId('id_pesanan')
                ->unique()
                ->constrained('pesanans')
                ->cascadeOnDelete();

            $table->foreignId('id_jasa')
                ->constrained('jasa')
                ->cascadeOnDelete();

            $table->foreignId('id_customer')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->foreignId('id_freelancer')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->unsignedTinyInteger('rating');
            $table->text('ulasan')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};
