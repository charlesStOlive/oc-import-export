<?php namespace Waka\ImportExport\Updates;

use Winter\Storm\Database\Schema\Blueprint;
use Winter\Storm\Database\Updates\Migration;
use Schema;

class CreateConfigExportsTableU103 extends Migration
{
    public function up()
    {
        Schema::table('waka_importexport_config_exports', function (Blueprint $table) {
            $table->text('relation')->nullable();
        });
    }

    public function down()
    {
        Schema::table('waka_importexport_config_exports', function (Blueprint $table) {
            $table->dropColumn('relation');
        });
    }
}
