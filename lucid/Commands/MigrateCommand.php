<?php
namespace Lucid\Commands;

use Lucid\Schema;

class MigrateCommand {
    public function handle(): void {
        Schema::create('users', function($table) {
            $table->increments('id')
                ->string('email', 150)
                ->string('password', 255);
        });
        echo "Migración completada.\n";
    }
}
