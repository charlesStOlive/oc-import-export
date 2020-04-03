<?php namespace Waka\ImportExport\Classes\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;
use Maatwebsite\Excel\Events\BeforeExport;
use Session;
use \Waka\ImportExport\Models\ConfigExport;

class ExportModel implements FromCollection, WithEvents, WithStrictNullComparison, WithHeadings
{
    public $parser;

    public function headings(): array
    {
        return $this->parser->headers();
    }

    public function collection()
    {
        return $this->parser->export();
    }
    public function registerEvents(): array
    {
        return [
            // Handle by a closure.
            BeforeExport::class => function (BeforeExport $event) {
                $configExportId = Session::pull('excel.configExportId');
                $configExport = ConfigExport::find($configExportId);
                $listId = Session::pull('modelImportExportLog.listId');
                //trace_log("listId in export model");
                //trace_log($listId);
                $this->parser = new YamlExcel($configExport, $listId);
            },
        ];
    }
}
