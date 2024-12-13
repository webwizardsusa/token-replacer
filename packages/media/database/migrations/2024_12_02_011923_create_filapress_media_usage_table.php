<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('filapress_media_usage', function (Blueprint $table) {
            $table->uuid('media_id');
            $table->string('usage_type');
            $table->string('usage_id', 36);
            $table->primary(['media_id', 'usage_type', 'usage_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('filapress_media_usage');
    }
};
