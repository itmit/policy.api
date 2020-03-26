<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('email')->unique()->nullable();
            $table->string('phone')->unique()->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->text('photo')->nullable();; // путь к фото
            $table->string('name')->nullable();; // ФИО
            $table->string('city')->nullable();; // город
            $table->string('field_of_activity')->nullable();; // сфера деятельности
            $table->string('organization')->nullable();; // организация 
            $table->string('position')->nullable();; // должность
            $table->string('link')->nullable();; // ссылка
            $table->date('birthday')->nullable();; // дата рождения

            $table->enum('sex', ['мужской', 'женский'])->nullable();; // пол
            $table->enum('education', ['высшее или неполное высшее', 'среднее (профессиональное)', 'среднее (полное)', 'среднее (общее)', 'начальное'])->nullable();; // образование
            $table->string('region')->nullable();; // регион
            $table->string('city_type')->nullable();; // тип нас. пункта

            $table->string('uid')->unique(); // id пользователя из приложения
            $table->boolean('is_admin')->default(0); // id пользователя из приложения
            $table->rememberToken();
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
        Schema::dropIfExists('users');
    }
}
