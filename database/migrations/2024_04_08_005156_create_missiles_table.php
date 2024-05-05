<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Exécutez les migrations.
     */
    public function up(): void
    {
        Schema::create('missiles', function (Blueprint $table) {
            $table->id();
            $table->string('coordonnee', 4);
            $table->integer('resultat')->nullable();
            $table->unsignedBigInteger('partie_id');
            $table->timestamps();

            $table->foreign('partie_id')->references('id')->on('parties')->onDelete('cascade');
        });
    }

    /**
     * Renverse les migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('missiles');
    }
};
