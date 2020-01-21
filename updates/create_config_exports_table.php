<?php namespace Waka\ImportExport\Updates;

use Schema;
use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

class CreateConfigExportsTable extends Migration
{
    public function up()
    {
        Schema::create('waka_importexport_config_exports', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->string('name');
            $table->string('model');
            $table->integer('type_id')->unsigned();
            $table->text('column_list');
            $table->text('comment')->nullable();
            $table->boolean('use_batch')->default(0);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('waka_importexport_config_exports');
    }
}
