<?php namespace Waka\ImportExport\Updates;

use Winter\Storm\Database\Schema\Blueprint;
use Winter\Storm\Database\Updates\Migration;
use Schema;

class CreateImportsTableU110 extends Migration
{
    public function up()
    {
        Schema::table('waka_importexport_imports', function (Blueprint $table) {
            $table->string('state')->default('Actif');
            $table->dropColumn('is_scope');
            $table->dropColumn('scopes');
        });
    }

    public function down()
    {
        Schema::table('waka_importexport_imports', function (Blueprint $table) {
            $table->dropColumn('state');
            $table->boolean('is_scope')->nullable();
            $table->text('scopes')->nullable();
        });
    }
}