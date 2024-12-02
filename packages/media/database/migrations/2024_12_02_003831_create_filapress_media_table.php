<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('filapress_media', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('type', 24)->index();
            $table->string('collection', 48)->index();
            $table->boolean('status')->default(true);
            $table->string('title');
            $table->string('filename')->nullable();
            $table->string('disk')->nullable();
            $table->string('path')->nullable();
            $table->integer('width')->default(0);
            $table->integer('height')->default(0);
            $table->string('mime_type', 32)->nullable();
            $table->integer('filesize')->default(0);
            $table->string('thumb_disk')->nullable();
            $table->string('thumb_path')->nullable();
            $table->jsonb('extra')->nullable();
            $table->jsonb('variations')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('filapress_media');
    }
};
