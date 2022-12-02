<?php namespace Waka\ImportExport\Updates;

use Winter\Storm\Database\Schema\Blueprint;
use Winter\Storm\Database\Updates\Migration;
use Schema;

class CreateExportsTable extends Migration
{
    public function up()
    {
        Schema::create('waka_importexport_exports', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->string('name');
            $table->string('data_source');
            $table->boolean('is_editable')->nullable();
            $table->string('export_model_class')->nullable();
            $table->boolean('for_relation')->default(0);
            $table->text('relation')->nullable();
            $table->text('column_list')->nullable();
            $table->text('comment')->nullable();
            $table->boolean('is_scope')->nullable();
            $table->text('scopes')->nullable();
            $table->boolean('use_batch')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('waka_importexport_exports');
    }
}