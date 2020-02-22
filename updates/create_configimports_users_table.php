<?php namespace Waka\ImportExport\Updates;

use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;
use Schema;

class CreateConfigimportsUsersTable extends Migration
{
    public function up()
    {
        Schema::create('waka_configimports_users', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->integer('config_import_id')->unsigned();
            $table->integer('user_id')->unsigned();
            $table->primary(['config_import_id', 'user_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('waka_configimports_users');
    }
}
