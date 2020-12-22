<?php namespace Waka\ImportExport\Classes\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Waka\ImportExport\Models\Import;

class ImportsImport implements ToCollection, WithHeadingRow
{
    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            $import = new Import();
            $import->id = $row['id'] ?? null;
            $import->name = $row['name'] ?? null;
            $import->data_source = $row['data_source'] ?? null;
            $import->is_editable = $row['is_editable'] ?? null;
            $import->import_model_class = $row['import_model_class'] ?? null;
            $import->for_relation = $row['for_relation'] ?? null;
            $import->relation = $row['relation'] ?? null;
            $import->column_list = $row['column_list'] ?? null;
            $import->comment = $row['comment'] ?? null;
            $import->is_scope = $row['is_scope'] ?? null;
            $import->scopes = json_decode($row['scopes'] ?? null);
            $import->use_batch = $row['use_batch'] ?? null;
            $import->save();
        }
    }
}
