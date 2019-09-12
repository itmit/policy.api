<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSuslikRatingHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Schema::create('suslik_rating_histories', function (Blueprint $table) {
        //     $table->bigIncrements('id');
        //     $table->bigInteger('from_suslik');
        //     $table->bigInteger('whom_suslik');
        //     $table->enum('type', ['likes', 'dislikes', 'neutrals']);
        //     $table->timestamps();

            // $table->foreign('from_suslik')->references('id')->on('susliks')
            //     ->onUpdate('cascade');

            // $table->foreign('whom_suslik')->references('id')->on('susliks')
            //     ->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('suslik_rating_histories');
    }
}
