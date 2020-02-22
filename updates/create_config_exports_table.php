<?php namespace Waka\ImportExport\Updates;

use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;
use Schema;

class CreateConfigExportsTable extends Migration
{
    public function up()
    {
        Schema::create('waka_importexport_config_exports', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->string('name');
            $table->string('model');
            $table->string('import_model_class')->nullable();
            $table->boolean('is_editable')->default(true);
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
