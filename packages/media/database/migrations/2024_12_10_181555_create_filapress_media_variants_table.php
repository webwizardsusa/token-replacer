<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('filapress_media_variants', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name', 48)->index();
            $table->uuid('media_id');
            $table->string('disk');
            $table->string('path');
            $table->integer('width')->default(0);
            $table->integer('height')->default(0);
            $table->string('mime', 32)->nullable();
            $table->integer('filesize')->default(0);
            $table->jsonb('sizes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('filapress_media_variants');
    }
};
