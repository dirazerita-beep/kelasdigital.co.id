<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_lessons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('section_id')->constrained('product_sections')->cascadeOnDelete();
            $table->string('title', 200);
            $table->enum('type', ['video', 'file', 'text']);
            $table->string('youtube_id', 50)->nullable();
            $table->string('gdrive_file_id', 100)->nullable();
            $table->text('content')->nullable();
            $table->integer('duration_minutes')->default(0);
            $table->integer('order_index')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_lessons');
    }
};
