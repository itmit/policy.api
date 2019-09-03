<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSusliksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('susliks', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('place_of_work');
            $table->string('position');
            $table->bigInteger('category');

            $table->integer('like');
            $table->integer('dislike');
            $table->integer('neutral');

            $table->string('photo')->nullable();
            $table->timestamps();

            $table->foreign('category')->references('id')->on('susliks_categories')
                ->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('susliks');
    }
}
