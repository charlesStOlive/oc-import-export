<?php namespace Waka\ImportExport\Classes;
use Yaml;
use stdClass;

Class YamlExcel {

    private $importer;
    public $model;
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
        //traitement des fields classique
        $fields = $rows['fields'];
        foreach($fields as $key => $value) {
            $this->fieldObjects[$key] = new FieldObject($key, $value);
        }
        if(array_key_exists('relation_fields', $rows)) {
            $this->hasRelations = true;
            $this->relations = $this->getRelations($rows['relation_fields']);
        } else {

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