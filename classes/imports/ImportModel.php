<?php namespace Waka\ImportExport\Classes\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ImportModel implements ToCollection, WithHeadingRow
{
    public $parser;

    public function __construct($configImport, $parentId = null)
    {
        //trace_log($parentId);
        $this->parser = new YamlExcel($configImport, $parentId);
    }
    public function collection(Collection $rows)
    {
        $this->parser->import($rows);
        return \Redirect::refresh();
    }
}
