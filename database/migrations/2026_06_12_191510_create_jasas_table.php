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
        Schema::create('jasa', function (Blueprint $table) {
            $table->id();

            $table->foreignId('id_freelancer')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->string('nama_jasa');
            $table->string('kategori');
            $table->text('deskripsi');
            $table->decimal('harga', 12, 2);
            $table->string('estimasi_pengerjaan');
            $table->string('thumbnail')->nullable();

            $table->enum('status_jasa', [
                'pending',
                'active',
                'inactive',
                'rejected'
            ])->default('pending');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jasa');
    }
};
