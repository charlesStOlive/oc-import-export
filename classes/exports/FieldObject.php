<?php namespace Waka\ImportExport\Classes\Exports;
use Yaml;
use System\Models\File;
use Config;

Class FieldObject {

    public $column;
    public $key;
    public $values;
    //
    public $hasRelation;
    public $model;
    public $modelAttribute;
    //
    private $type;
    private $rowArray;

    const RELATION = 'relation';
    const SELECT = 'select';
    const TYPEDATE = "typedate";
    const COLUMN = "column";

    public function __construct($key, $values) 
    {
        $this->values = $values; 
        $this->key = $key;
        $this->column = array_key_exists(self::COLUMN, $values) ? $values[self::COLUMN] : null;
    }
    
    public function getRelation() {
        return $this->checkOption(self::RELATION);
    }
    public function getSelect() {
        return $this->checkOption(self::SELECT);
    }
    public function getTypeDate() {
        return $this->checkOption(self::TYPEDATE);
    }
    public function setValue($modelAttributes) {
        $data = $modelAttributes[$this->key]; 
        $relation = $this->getRelation();
        $select = $this->getSelect();
        if($relation && $select) {
            $data = $modelAttributes[$relation][$select];
        }
        return $data;
    }
    private function checkOption($test) {
        return array_key_exists($test, $this->values) ? $this->values[$test] : false;
    }
}