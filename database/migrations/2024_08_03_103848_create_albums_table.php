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
        Schema::create('albums', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('summary')->nullable();
            $table->unsignedInteger('pages')->nullable();
            $table->text('cover')->nullable();
            $table->string('isbn')->nullable();
            $table->text('comment')->nullable();
            $table->boolean('read')->default(false);
            $table->boolean('complete')->default(false);
            $table->foreignId('serie_id')->nullable();
            $table->unsignedInteger('serie_issue')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('albums');
    }
};
