<?php
use Core\Lucid\Schema;
use Core\Lucid\Blueprint;

return new class {
    public function up(): void {
        Schema::create('nombre_de_tabla', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->timestamps();
        });
    }

    public function down(): void {
        Blueprint::drop('nombre_de_tabla');
    }
};
