<?php namespace Waka\ImportExport\Classes\Imports;
use Yaml;


Class RelationObject {
    
    public $model;
    public $name;
    public $unique_key;
    public $unique_column;
    public $unique_update;
    public $update;
    public $fieldObjects;

    public function __construct($key, $value) 
    {
        $this->model = $value['model'];
        $this->name = $key;
        $this->unique_key = $value['unique_key'];
        $this->unique_column = $value['unique_column'];
        $this->unique_update = $value['unique_update'];
        $this->fieldObjects = [];
        foreach($value['fields'] as $key => $row) {
            $this->fieldObjects[$key] = new FieldObject($key, $row);
        }
    }

    public function getRelationId($columns) {
        $model = new $this->model;
        $model = $model::where($this->unique_key, '=', $columns[$this->unique_column]);
        return $model->count() > 0 ? $model->first()->id : false;
    }
    
    
}