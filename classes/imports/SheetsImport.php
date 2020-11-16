<?php

namespace Waka\ImportExport\Classes\Imports;

use Db;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Waka\Utils\Models\Settings;

class SheetsImport implements WithMultipleSheets
{

    public function sheets(): array
    {
        $settingImports = Settings::get('start_imports');
        $configArray = \Config::get('waka.wconfig::start_data');

        trace_log($configArray);

        $sheetsToImport = [];
        foreach ($settingImports as $key) {
            $sheetsToImport[$key] = new $configArray[$key]['class'];
            if ($configArray[$key]['truncate'] ?? false) {
                Db::table($configArray[$key]['table'])->truncate();
            }

        }
        trace_log($sheetsToImport);
        return $sheetsToImport;
    }
}
