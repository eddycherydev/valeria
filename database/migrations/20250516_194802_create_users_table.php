<?php
use Core\Lucid\Schema;
use Core\Lucid\Blueprint;



return new class {
    public function up() {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('email', 150);
            $table->string('name', 150);
            $table->string('password', 255);
            $table->timestamps();
        });
    }

    public function down() {
        Blueprint::drop('users');
    }
};