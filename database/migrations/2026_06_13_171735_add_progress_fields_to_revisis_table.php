<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('revisis', function (Blueprint $table) {
            $table->foreignId('id_progress')
                ->nullable()
                ->after('id_pesanan')
                ->constrained('progress_pekerjaans')
                ->nullOnDelete();

            $table->enum('jenis_revisi', [
                'progress',
                'hasil_akhir'
            ])->default('progress')->after('id_progress');

            $table->unsignedTinyInteger('persentase_progress')
                ->nullable()
                ->after('jenis_revisi');
        });
    }

    public function down(): void
    {
        Schema::table('revisis', function (Blueprint $table) {
            $table->dropForeign(['id_progress']);
            $table->dropColumn([
                'id_progress',
                'jenis_revisi',
                'persentase_progress',
            ]);
        });
    }
};
