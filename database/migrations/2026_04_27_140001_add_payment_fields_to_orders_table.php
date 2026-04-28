<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->enum('payment_method', ['midtrans', 'manual'])
                ->default('manual')
                ->after('status');
            $table->string('payment_proof', 255)->nullable()->after('payment_method');
            $table->enum('manual_status', ['waiting', 'confirmed', 'rejected'])
                ->nullable()
                ->after('payment_proof');
            $table->timestamp('whatsapp_sent_at')->nullable()->after('manual_status');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['payment_method', 'payment_proof', 'manual_status', 'whatsapp_sent_at']);
        });
    }
};
