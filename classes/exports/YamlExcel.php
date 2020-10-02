<?php namespace Waka\ImportExport\Classes\Exports;

use Illuminate\Support\Collection;
use Waka\Utils\Classes\DataSource;
use Yaml;

class YamlExcel
{

    private $importer;
    public $model;
    private $excelCollection;
    public $excelHeaders;
    private $listId;

    public function __construct($config, $listId = null)
    {
        $this->importer = $config;
        $this->model = DataSource::find($config->data_source_id)->class;
        $this->config = $config->column_list;
        $this->listId = $listId;
    }

    private function prepareVars()
    {
        $fields = $this->getConfig();

        $models;
        if ($this->listId) {
            $models = $this->model::whereIn('id', $this->listId)->get();
        } else {
            $models = $this->model::get();
        }
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
        $rows = Yaml::parse($this->config);
        $baseModel = $rows['base'];
        //traitement des fields classique
        return $baseModel['fields'];

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
