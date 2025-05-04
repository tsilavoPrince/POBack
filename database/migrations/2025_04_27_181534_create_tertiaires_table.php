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
        Schema::create('tertiaires', function (Blueprint $table) {
            $table->id();
            $table->string('nom')->nullable();
            $table->decimal('loisir', 10, 2)->nullable();
            $table->decimal('vaccance', 10, 2)->nullable();
            $table->decimal('autres', 10, 2)->nullable();
            $table->decimal('fety', 10, 2)->nullable();
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
        Schema::dropIfExists('tertiaires');
    }
};
