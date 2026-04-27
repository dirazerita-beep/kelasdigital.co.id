<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('title', 200);
            $table->string('slug', 200)->unique();
            $table->text('description');
            $table->decimal('price', 12, 2);
            $table->string('thumbnail')->nullable();
            $table->enum('type', ['course', 'software', 'mixed']);
            $table->enum('status', ['active', 'draft'])->default('draft');
            $table->decimal('commission_rate', 5, 2)->default(30.00);
            $table->string('preview_youtube_id', 50)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
