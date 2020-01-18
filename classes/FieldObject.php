<?php namespace Waka\ImportExport\Classes;
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

    const RELATION_KEY = 'relationKey';
    const SLUG = 'slugFrom';
    const CONSTANTE = 'constante';
    const COLUMN = 'column';
    const SRCTYPE = 'src_type';
    const MODELTYPE = 'model_type';
    const RELATIONKEY = 'relation_key';

    public function __construct($key, $values) 
    {
        $this->values = $values; 
        $this->key = $key;
        if(array_key_exists(self::COLUMN, $values)) {
            $this->column = $values[self::COLUMN];
        }
    }
    
    public function getSlug() {
        return $this->checkOption(self::SLUG);
    }
    public function getConstante() {
        return $this->checkOption(self::CONSTANTE);
    }
    public function getSrcType() {
        return $this->checkOption(self::SRCTYPE);
    }
    public function getModelFileType() {
        return $this->checkOption(self::MODELTYPE);
    }
    public function getRelationKey() {
        return $this->checkOption(self::RELATIONKEY);
    }
    public function getValue($columns) {
        // trace_log("____________Get Value________");
        // trace_log($columns);
        $slug = $this->getSlug();
        if($slug) return str_slug($columns[$slug]);

        $relation = $this->getRelationKey();
        if($relation) {
            $modelRelation = new $this->values['model'];
            trace_log($relation.' '.$columns[$this->column]);
            $modelRelation = $modelRelation::where($relation, '=', $columns[$this->column])->first();
            // $test = \Waka\Crsm\Models\Client::where($relation, '=', $columns[$this->column])->first();
            // trace_log($test->id);
            
            if($modelRelation) {
                return $modelRelation->id;
            } else {
                return null;
            }
        }

        $srcType = $this->getSrcType();
        if($srcType == 'media') {
            $url =  $url = url(Config::get('cms.storage.media.path')).$columns[$this->column];
        } elseif($srcType == 'url') {
            $url = $columns[$this->column];
        }
        $file = $this->getModelFileType();
        if($file == 'file' || $file == 'cloudi' ) {
            $file = new File;
            $file->fromUrl($url);
            return $file;
        }
        



        $constante = $this->getConstante();
        if($constante) return $constante;

        return $columns[$this->column];
    }
    private function checkOption($test) {
        if(array_key_exists($test, $this->values)) {
            return $this->values[$test];
        } else {
            return false;
        }

    }
}