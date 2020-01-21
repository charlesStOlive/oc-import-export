<?php namespace Waka\ImportExport\Classes\Imports;
use Yaml;
use stdClass;

Class YamlExcel {

    private $importer;
    public $model;
    public $unique_update;
    public $unique_create;
    public $unique_column;
    public $unique_key;
    public $isUpdate;

    public $hasRelations;

    public $fieldObjects;
    public $relations;


    public function __construct($config) 
    {
       $this->importer = $config;
       $this->model = $config->model;
       $this->parse($config->column_list);


    }

    public function parse($yaml) {
        $rows = Yaml::parse($yaml);
        $baseModel = $rows['base'];
        $this->unique_create = $baseModel['unique_create'] ?? true;
        $this->unique_update = $baseModel['unique_update'] ?? false;
        $this->unique_column = $baseModel['unique_column'] ?? false;
        $this->unique_key = $baseModel['unique_key'] ?? false;
        trace_log("ok parse");
        //traitement des fields classique
        $fields = $baseModel['fields'];
        foreach($fields as $key => $value) {
            $this->fieldObjects[$key] = new FieldObject($key, $value);
        }
        if(array_key_exists('relation_fields', $rows)) {
            $this->hasRelations = true;
            $this->relations = $this->getRelations($rows['relation_fields']);
        }
        
    }
    public function getModelOrNew($columns) {
            // le this update est dangereux a retravaillé. 
            $this->isUpdate = false;
            trace_log("On recherche le model");
            $excelUniqueColumn = $columns[$this->unique_column] ?? false;
            $findModel;
            trace_log("Valeur recherché : ".$excelUniqueColumn." : ".$this->unique_key);
            if($excelUniqueColumn) {
                $findModel = $this->model::where($this->unique_key, '=', $excelUniqueColumn)->first();
                if($findModel) {
                    $this->isUpdate = true;
                    return  $findModel; 
                } else {
                    return new $this->model;
                }
            } else {
                return new $this->model;
            }
        
    }

    public function getRelations($relation_rows) {
        $relations;
        foreach($relation_rows as $key => $row) {
            $relation = new RelationObject($key, $row);
            $relations[$key] = $relation;
        }
        return $relations;
    }
}