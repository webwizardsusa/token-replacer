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
        Schema::create('oembeds', function (Blueprint $table) {
            $table->uuid('id');
            $table->string('url_hash', 64)->unique();
            $table->string('url');
            $table->string('provider');
            $table->string('title')->nullable();
            $table->json('data');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('oembeds');
    }
};
