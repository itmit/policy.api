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
            $table->text('photo'); // путь к фото
            $table->string('name'); // ФИО
            $table->string('city'); // город
            $table->string('field_of_activity'); // сфера деятельности
            $table->string('organization'); // организация 
            $table->string('position'); // должность
            $table->string('link'); // ссылка
            $table->date('birthday'); // дата рождения

            $table->enum('sex', ['мужской', 'женский']); // пол
            $table->enum('education', ['высшее или неполное высшее', 'среднее (профессиональное)', 'среднее (полное)', 'среднее (общее) или ниже']); // образование
            $table->string('region'); // регион
            $table->string('city_type'); // тип нас. пункта

            $table->string('uid')->unique(); // id пользователя из приложения
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
