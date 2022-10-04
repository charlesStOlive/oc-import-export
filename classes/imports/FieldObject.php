<?php namespace Waka\ImportExport\Classes\Imports;

use Config;
use System\Models\File;

class FieldObject
{
    public $column;
    public $key;
    public $values;
    //
    public $hasRelation;
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

    public function __construct($key, $values, $parentModel = null)
    {
        $this->values = $values;
        $this->key = $key;
        //trace_log("__construct");
        //trace_log($key);
        //trace_log($values);
        //trace_log(self::COLUMN);
        $this->column = array_key_exists(self::COLUMN, $values) ? $values[self::COLUMN] : null;
        $this->type = 'normal';
    }

    public function getSlug()
    {
        return $this->checkOption(self::SLUG);
    }
    public function getConstante()
    {
        return $this->checkOption(self::CONSTANTE);
    }
    public function getSrcType()
    {
        return $this->checkOption(self::SRCTYPE);
    }
    public function getModelFileType()
    {
        return $this->checkOption(self::MODELTYPE);
    }
    public function getRelationKey()
    {
        return $this->checkOption(self::RELATIONKEY);
    }
    public function getValue($columns)
    {
        $slug = $this->getSlug();
        //trace_log($slug);
        if ($slug) {
            return str_slug($columns[$slug]);
        }

        $relation = $this->getRelationKey();
        if ($relation) {
            $modelRelation = new $this->values['model'];
            $modelRelation = $modelRelation::where($relation, '=', $columns[$this->column])->first();
            return $modelRelation->id ?? null;
        }

        $srcType = $this->getSrcType();
        if ($srcType == 'media') {
            $url = $url = url(Config::get('cms.storage.media.path')) . $columns[$this->column];
        } elseif ($srcType == 'url') {
            $url = $columns[$this->column];
        }
        $file = $this->getModelFileType();
        if ($file == 'file' || $file == 'cloudi') {
            $file = new File;
            $file->fromUrl($url);
            return $file;
        }
        $constante = $this->getConstante();
        if ($constante) {
            return $constante;
        }

        return $columns[$this->column];
    }
    private function checkOption($test)
    {
        return array_key_exists($test, $this->values) ? $this->values[$test] : false;
    }
}
