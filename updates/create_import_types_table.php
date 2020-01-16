<?php namespace Waka\ImportExport\Updates;

use Schema;
use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

class CreateImportTypesTable extends Migration
{
    public function up()
    {
        Schema::create('waka_importexport_import_types', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->increments('name');
            $table->boolean('import');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('waka_importexport_import_types');
    }
}
