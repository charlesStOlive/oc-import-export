<?php namespace Waka\ImportExport\Updates;

//use Excel;
use Seeder;
use DB;
use Waka\ImportExport\Models\Type;
use System\Models\File;;
// use Waka\Crsm\Classes\CountryImport;



class SeedTables extends Seeder
{
    public function run()
    {
        $import = Type::create([
            'name'   => 'Import model',
            'import' => true,
            'class' => 'Waka\\ImportExport\\Classes\\Imports\\ImportModel'
        ]);
        $import = Type::create([
            'name'   => 'Export model',
            'import' => false,
            'class' => 'Waka\\ImportExport\\Classes\\Exports\\ExportModel'
        ]);
        $import = Type::create([
            'name'   => 'Import model with picture',
            'import' => true,
            'class' => 'Waka\\ImportExport\\Classes\\Imports\\ImportModelPicture'
        ]);
        
        $sql = plugins_path('waka/importexport/updates/sql/waka_importexport_config_exports.sql');
        DB::unprepared(file_get_contents($sql));

        $sql = plugins_path('waka/importexport/updates/sql/waka_importexport_config_imports.sql');
        DB::unprepared(file_get_contents($sql));

        
    }
}
