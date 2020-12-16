<?php namespace Waka\ImportExport\Classes\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Waka\ImportExport\Models\Export;

class ExportsImport implements ToCollection, WithHeadingRow
{
    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            $export = new Export();
            $export->id = $row['id'] ?? null;
            $export->name = $row['name'] ?? null;
            $export->data_source_id = $row['data_source_id'] ?? null;
            $export->is_editable = $row['is_editable'] ?? null;
            $export->export_model_class = $row['export_model_class'] ?? null;
            $export->for_relation = $row['for_relation'] ?? null;
            $export->relation = $row['relation'] ?? null;
            $export->column_list = $row['column_list'] ?? null;
            $export->comment = $row['comment'] ?? null;
            $export->is_scope = $row['is_scope'] ?? null;
            $export->scopes = json_decode($row['scopes'] ?? null);
            $export->use_batch = $row['use_batch'] ?? null;
            $export->save();
        }
    }
}
