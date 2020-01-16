<?php namespace Waka\ImportExport\Updates;

use Schema;
use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

class CreateImportExportLogsTable extends Migration
{
    public function up()
    {
        Schema::create('waka_importexport_import_export_logs', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->text('comments');
            $table->text('results');
            $table->integer('backend_user_id');
            $table->integer('logeable_type');
            $table->integer('logeable_id');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('waka_importexport_import_export_logs');
    }
}
