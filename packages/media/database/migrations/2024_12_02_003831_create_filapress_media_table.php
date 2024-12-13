<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('filapress_media', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('type', 24)->index();
            $table->string('collection', 48)->index()->nullable();
            $table->boolean('status')->default(true);
            $table->string('title');
            $table->string('filename')->nullable();
            $table->string('disk')->nullable();
            $table->string('path')->nullable();
            $table->string('thumbnail_disk')->nullable();
            $table->string('thumbnail_path')->nullable();
            $table->integer('width')->default(0);
            $table->integer('height')->default(0);
            $table->string('mime', 32)->nullable();
            $table->integer('filesize')->default(0);
            $table->jsonb('data')->nullable();
            $table->jsonb('sizes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('filapress_media');
    }
};
