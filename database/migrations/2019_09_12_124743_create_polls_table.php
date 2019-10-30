<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePollsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('polls', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->uuid('uuid');
            $table->string('name');
            $table->text('description')->nullable();
            $table->bigInteger('category')->unsigned();
            $table->string('link')->nullable();
            $table->timestamp('start_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('end_at')->nullable()->default(NULL);

            $table->timestamps();

            $table->foreign('category_id')->references('id')->on('poll_categories');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('polls');
    }
}
