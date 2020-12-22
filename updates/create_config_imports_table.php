<?php namespace Waka\ImportExport\Updates;

use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;
use Schema;

class CreateConfigImportsTable extends Migration
{
    public function up()
    {
        Schema::create('waka_importexport_config_imports', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->string('name');
            $table->string('data_source')->nullable();
            $table->string('import_model_class')->nullable();
            $table->boolean('is_editable')->default(true);
            $table->text('column_list');
            $table->text('comment')->nullable();
            $table->string('is_scope')->nullable();
            $table->text('scopes')->nullable();
            $table->integer('link_export_id')->unsigned()->nullable();
            $table->boolean('use_batch')->default(false);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('waka_importexport_config_imports');
    }
}
