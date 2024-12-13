<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('filapress_user_roles', function (Blueprint $table) {
            $table->unsignedBigInteger('filapress_role_id');
            $table->unsignedBigInteger('user_id');
            $table->foreign('filapress_role_id')->references('id')->on('filapress_roles')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('filapress_user_roles');
    }
};
