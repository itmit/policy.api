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
            $table->uuid('uuid');
            $table->string('FIO');
            $table->string('place_of_work')->nullable();
            $table->string('position')->nullable();
            $table->bigInteger('category')->unsigned();
            $table->string('birthdate')->nullable(); // дата рождения

            $table->integer('likes')->default(0);
            $table->integer('dislikes')->default(0);
            $table->integer('neutrals')->default(0);

            $table->string('photo')->nullable();
            $table->timestamps();
            $table->softDeletes();

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
