<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('favorites', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_customer')->constrained('users')->cascadeOnDelete();
            $table->foreignId('id_jasa')->constrained('jasa')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['id_customer', 'id_jasa']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('favorites');
    }
};
