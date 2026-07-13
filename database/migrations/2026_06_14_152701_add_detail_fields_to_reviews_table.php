<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reviews', function (Blueprint $table) {
            $table->unsignedTinyInteger('rating_kualitas')->nullable()->after('rating');
            $table->unsignedTinyInteger('rating_komunikasi')->nullable()->after('rating_kualitas');
            $table->unsignedTinyInteger('rating_waktu')->nullable()->after('rating_komunikasi');
            $table->unsignedTinyInteger('rating_profesionalisme')->nullable()->after('rating_waktu');

            $table->boolean('rekomendasi')->default(false)->after('ulasan');
            $table->string('foto_review')->nullable()->after('rekomendasi');
        });
    }

    public function down(): void
    {
        Schema::table('reviews', function (Blueprint $table) {
            $table->dropColumn([
                'rating_kualitas',
                'rating_komunikasi',
                'rating_waktu',
                'rating_profesionalisme',
                'rekomendasi',
                'foto_review',
            ]);
        });
    }
};
