<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('filapress_images', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('collection', 48)->index();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('filename');
            $table->string('disk');
            $table->string('path');
            $table->string('mime_type');
            $table->integer('width');
            $table->integer('height');
            $table->string('alt');
            $table->integer('filesize');
            $table->jsonb('formats')->nullable();
            $table->timestamps();
        });

        Schema::create('filapress_images_usage', function (Blueprint $table) {
            $table->uuid('image_id');
            $table->string('usage_type');
            $table->string('usage_id', 48);
            $table->foreign('image_id')->references('id')->on('filapress_images')->onDelete('cascade');
            $table->primary(['image_id', 'usage_type', 'usage_id']);

        });
    }

    public function down(): void
    {
        Schema::dropIfExists('filapress_images');
    }
};
