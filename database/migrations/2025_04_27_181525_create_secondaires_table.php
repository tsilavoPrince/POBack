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
        Schema::create('secondaires', function (Blueprint $table) {
            $table->id();
            $table->string('nom')->nullable();
            $table->decimal('credit', 10, 2)->nullable();
            $table->decimal('entretien', 10, 2)->nullable();
            $table->decimal('aide', 10, 2)->nullable();
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
        Schema::dropIfExists('secondaires');
    }
};
