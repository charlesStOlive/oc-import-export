<?php namespace Waka\ImportExport\Classes\Imports;

use Maatwebsite\Excel\Concerns\ToCollection;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\BeforeImport;
use Session;
use \Waka\ImportExport\Classes\YamlExcel;
use \Waka\ImportExport\Models\ConfigImport;

use Waka\Crsm\Models\Contact;

class ImportModel implements ToCollection, WithHeadingRow, WithEvents
{
    public $parser;
    public function collection(Collection $rows)
    {
        $parser = $this->parser;
        foreach ($rows as $row) 
        {
            $newModel = new $parser->model;
            foreach($parser->fieldObjects as $fieldObject) {
                //trace_log($fieldObject->key.' : '.$fieldObject->getValue($row));
                $newModel[$fieldObject->key] = $fieldObject->getValue($row);
            }
            if($parser->hasRelations) {
                foreach($parser->relations as $relation) {
                    $relatedModel;
                    $relatedModelId =  $relation->getRelationId($row);
                    if($relatedModelId) {
                        $relatedModel = $relation->model::find($relatedModelId);
                    }  else {
                        $relatedModel = new $relation->model;
                    }
                    $cloudis = [];
                    foreach($relation->fieldObjects as $fieldObject) {
                        //Traitement spécial pour cloudi. 
                        if($fieldObject->getModelFileType() == 'cloudi') {
                            array_push($cloudis, $fieldObject->key);
                        }
                            $relatedModel->{$fieldObject->key} = $fieldObject->getValue($row);
                        
                    }
                    if($relatedModelId && !$relation->unique_update) {
                        // Si la relation existe mais qu'il n' y a pas de demande d'update, On saute le doublon. 
                    } else {
                        $relatedModel->save();
                        if(count($cloudis)) $relatedModel->uploadToCloudinary($cloudis);
                    }
                    $newModel[$relation->name] = $relatedModel;
                }
            }
            $newModel->save();
            trace_log("FIN EXPORT");
        }
    }

    public function registerEvents(): array
    {
        return [
            // Handle by a closure.
            
            BeforeImport::class => function(BeforeImport $event) {
                $configImportId = Session::pull('excel.configImportId');
                $configImport = ConfigImport::find($configImportId);
                $this->parser = new YamlExcel($configImport);
            },         
        ];
    }
}