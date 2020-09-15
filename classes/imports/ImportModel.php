<?php namespace Waka\ImportExport\Classes\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ImportModel implements ToCollection, WithHeadingRow
{
    public $parser;

    public function __construct($configImport)
    {
        $this->parser = new YamlExcel($configImport);
    }
    public function collection(Collection $rows)
    {
        $parser = $this->parser;
        foreach ($rows as $row) {
            $model = $parser->getModelOrNew($row);
            foreach ($parser->fieldObjects as $fieldObject) {
                $model[$fieldObject->key] = $fieldObject->getValue($row);
            }
            if ($parser->hasRelations) {
                foreach ($parser->relations as $relation) {
                    $relatedModelId = $relation->getRelationId($row);
                    $relatedModel = $relation->model::find($relatedModelId) ?? new $relation->model;
                    $cloudis = [];
                    foreach ($relation->fieldObjects as $fieldObject) {
                        //Traitement spécial pour cloudi.
                        if ($fieldObject->getModelFileType() == 'cloudi') {
                            array_push($cloudis, $fieldObject->key);
                        }
                        $relatedModel->{$fieldObject->key} = $fieldObject->getValue($row);
                    }
                    if ($relatedModelId && !$relation->unique_update) {
                        // Si la relation existe mais qu'il n' y a pas de demande d'update, On saute le doublon.
                    } else {
                        $relatedModel->save();
                        if (count($cloudis)) {
                            $relatedModel->uploadToCloudinary($cloudis);
                        }

                    }
                    $model[$relation->name] = $relatedModel;
                }
            }
            if ($parser->isUpdate && !$parser->unique_update) {
                // Si la relation existe mais qu'il n' y a pas de demande d'update, On saute le doublon.
            } elseif (!$parser->isUpdate && !$parser->unique_create) {
                // Si la relation n'existe pas et que la création est interdite, on saute.
            } else {
                $model->save();

            }
            //trace_log("FIN EXPORT");
            return \Redirect::refresh();
        }
    }

    // public function registerEvents(): array
    // {
    //     return [
    //         // Handle by a closure.

    //         BeforeImport::class => function (BeforeImport $event) {
    //             $configImportId = Session::pull('excel.configImportId');
    //             $configImport = ConfigImport::find($configImportId);
    //             $this->parser = new YamlExcel($configImport);
    //         },
    //     ];
    // }
}
