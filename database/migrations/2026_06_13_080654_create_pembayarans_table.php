<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pembayarans', function (Blueprint $table) {
            $table->id();

            $table->foreignId('id_pesanan')
                ->unique()
                ->constrained('pesanans')
                ->cascadeOnDelete();

            $table->string('order_id')->nullable()->unique();
            $table->string('transaction_id')->nullable();
            $table->string('payment_type')->nullable();

            $table->decimal('gross_amount', 12, 2);

            $table->string('transaction_status')->default('pending');
            $table->string('fraud_status')->nullable();

            $table->enum('status_escrow', [
                'belum_ditahan',
                'ditahan',
                'dicairkan',
                'dikembalikan'
            ])->default('belum_ditahan');

            $table->string('snap_token')->nullable();

            $table->timestamp('tanggal_bayar')->nullable();
            $table->timestamp('tanggal_release')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pembayarans');
    }
};
