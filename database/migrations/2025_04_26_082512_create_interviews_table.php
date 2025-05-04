<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('interviews', function (Blueprint $table) {
            $table->id();
            $table->string('nom')->nullable();
            $table->integer('nbrPersonne')->nullable();
            $table->integer('nbrFemme')->nullable();
            $table->integer('age02')->nullable();
            $table->integer('age310')->nullable();
            $table->integer('age10plus')->nullable();
            $table->decimal('depense', 10, 2)->nullable();
            $table->decimal('budget', 10, 2)->nullable();
            $table->string('loyer')->nullable();
            $table->decimal('montantLoyer', 10, 2)->nullable();
            $table->string('ecolage')->nullable();
            $table->integer('nbrEcolage')->nullable();
            $table->integer('depPrimaire')->nullable();
            $table->integer('depSecondaire')->nullable();
            $table->integer('depTertaire')->nullable();
            $table->timestamps();
        });
    }
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('interviews');
    }
};
