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
        Schema::table('chats', function (Blueprint $table) {
            $table->index(['id_jasa', 'id_customer', 'id_freelancer'], 'chats_conversation_index');
            $table->index('pengirim_id');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::table('chats', function (Blueprint $table) {
            $table->dropIndex('chats_conversation_index');
            $table->dropIndex(['pengirim_id']);
            $table->dropIndex(['created_at']);
        });
    }
};
