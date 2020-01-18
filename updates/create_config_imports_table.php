<?php namespace Waka\ImportExport\Updates;

use Schema;
use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

class CreateConfigImportsTable extends Migration
{
    public function up()
    {
        Schema::create('waka_importexport_config_imports', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->string('name');
            $table->string('model');
            $table->boolean('unique_care')->default(true);
            $table->boolean('unique_action')->default(false);
            $table->string('unique_key')->nullable();
            $table->string('unique_column')->nullable();
            $table->integer('type_id')->unsigned();
            $table->text('column_list');
            $table->boolean('use_batch')->default(false);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('waka_importexport_config_imports');
    }
}
