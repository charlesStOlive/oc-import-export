<?php namespace Waka\ImportExport\Classes\Exports;

use Illuminate\Support\Collection;
use Waka\Utils\Classes\DataSource;
use Yaml;

class YamlExcel
{
    use \Waka\Utils\Classes\Traits\StringRelation;

    private $importer;
    public $model;
    private $excelCollection;
    public $excelHeaders;
    private $listId;
    private $parentId;

    public function __construct($config, $listId = null, $parentId = null)
    {
        $this->importer = $config;
        $ds = new DataSource($config->data_source);
        $this->model = $ds->class;
        $this->parentId = $parentId;
        $this->config = $config->column_list;
        $this->listId = $listId;
    }

    private function prepareVars()
    {
        $fields = $this->getConfig();
        $models = $this->getFinalModels();
        //trace_log($models->toArray());

        $excelArray = new Collection();
        foreach ($models as $model) {
            $fieldObjects = [];
            foreach ($fields as $key => $value) {
                $column = new FieldObject($key, $value);
                $fieldObjects[$key] = $column->setValue($model);
            }
            $excelArray->push($fieldObjects);
        }
        return $excelArray;
    }
    public function getConfig()
    {
        $config = Yaml::parse($this->config);
        $baseModel = $config['base'];
        //traitement des fields classique
        return $baseModel['fields'];

    }
    public function getFinalModels()
    {
        if ($this->parentId && !$this->importer->relation) {
            throw new ApplicationException("Erreur configuration l'extraction d'un modèle enfant nécessite un parentId et une relation");
        }
        if ($this->parentId && $this->importer->relation) {
            $this->model = $this->model::find($this->parentId);
            $finalModel = $this->getStringRequestRelation($this->model, $this->importer->relation);
            return $finalModel->get();
        } else {
            if ($this->listId) {
                return $this->model::whereIn('id', $this->listId)->get();
            } else {
                return $this->model::get();
            }

        }

    }
    public function export()
    {
        $this->excelCollection = $this->prepareVars();
        return $this->excelCollection;
    }
    public function headers()
    {
        $rows = $this->getConfig();
        return array_pluck($rows, 'column');
    }

}
