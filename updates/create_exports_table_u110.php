<?php namespace Waka\ImportExport\Updates;

use Winter\Storm\Database\Schema\Blueprint;
use Winter\Storm\Database\Updates\Migration;
use Schema;

class CreateExportsTableU110 extends Migration
{
    public function up()
    {
        Schema::table('waka_importexport_exports', function (Blueprint $table) {
            $table->dropColumn('is_scope');
            $table->dropColumn('scopes');
            $table->string('state')->default('Actif');
        });
    }

    public function down()
    {
        Schema::table('waka_importexport_exports', function (Blueprint $table) {
            $table->boolean('is_scope')->nullable();
            $table->text('scopes')->nullable();
            $table->dropColumn('state');
        });
    }
}