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
            $table->string('name');
            $table->integer('number')->unique();
            $table->string('place_of_work');
            $table->string('position');
            $table->bigInteger('category');
            
            // $table->date('birthday'); // дата рождения

            // $table->enum('sex', ['мужской', 'женский']); // пол
            // $table->enum('education', ['высшее или неполное высшее', 'среднее (профессиональное)', 'среднее (полное)', 'среднее (общее) или ниже']); // образование
            // $table->string('region'); // регион

            $table->integer('likes')->default(0);
            $table->integer('dislikes')->default(0);
            $table->integer('neutrals')->default(0);

            $table->string('link')->nullable();

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
