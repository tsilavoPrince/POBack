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
        Schema::create('primaires', function (Blueprint $table) {
            $table->id();
            $table->string('nom')->nullable();
            $table->decimal('nouriture', 10, 2)->nullable();
            $table->decimal('loyer', 10, 2)->nullable();
            $table->decimal('energie', 10, 2)->nullable();
            $table->decimal('sante', 10, 2)->nullable();
            $table->decimal('ecolage', 10, 2)->nullable();
            $table->decimal('vetement', 10, 2)->nullable();
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
        Schema::dropIfExists('primaires');
    }
};
