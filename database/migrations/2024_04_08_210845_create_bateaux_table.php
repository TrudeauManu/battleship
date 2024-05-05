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
        Schema::create('bateaux', function (Blueprint $table) {
            $table->id();
            $table->string('positions_porte_avions');
            $table->string('positions_cuirasse');
            $table->string('positions_destroyer');
            $table->string('positions_sous_marin');
            $table->string('positions_patrouilleur');
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
        Schema::dropIfExists('bateaux');
    }
};
