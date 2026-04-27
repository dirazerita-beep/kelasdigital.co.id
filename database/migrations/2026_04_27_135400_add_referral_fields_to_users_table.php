<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['admin', 'member'])->default('member')->after('password');
            $table->foreignId('referrer_id')->nullable()->after('role')->constrained('users')->nullOnDelete();
            $table->decimal('balance', 12, 2)->default(0)->after('referrer_id');
            $table->string('avatar')->nullable()->after('balance');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['referrer_id']);
            $table->dropColumn(['role', 'referrer_id', 'balance', 'avatar']);
        });
    }
};
