<?php namespace Waka\ImportExport\Classes\Imports;

use Waka\Utils\Classes\DataSource;
use Yaml;

class YamlExcel
{
    use \Waka\Utils\Classes\Traits\StringRelation;

    private $importer;
    public $model;
    public $unique_update;
    public $unique_create;
    public $unique_column;
    public $unique_key;
    public $isUpdate;
    public $parent_id;

    public $hasRelations;

    public $fieldObjects;
    public $relations;
    public $relationKeyName;

    public function __construct($config, $parentId = null)
    {
        $this->importer = $config;
        $this->parentId = $parentId;
        $ds = new DataSource($config->data_source);
        $this->setFinalModels($ds->class);
        //$this->model = $config->model;
        $this->parse($config->column_list);
    }

    public function setFinalModels($model)
    {
        if ($this->parentId && !$this->importer->relation) {
            throw new \ApplicationException("Erreur configuration l'import d'un modèle enfant nécessite un parentId et une relation");
        }
        if (!$this->parentId && $this->importer->relation) {
            throw new \ApplicationException("Erreur configuration l'import d'un modèle enfant nécessite un parentId et une relation");
        }
        if ($this->parentId && $this->importer->relation) {
            $model = $model::find($this->parentId);
            $relatedModel = $this->getStringRequestRelation($model, $this->importer->relation);
            $this->model = $relatedModel->getRelated();
            $this->relationKeyName = $relatedModel->getForeignKeyName();
        } else {
            $this->model = $model;
        }
    }

    public function parse($yaml)
    {
        $rows = Yaml::parse($yaml);
        $baseModel = $rows['base'];
        $this->unique_create = $baseModel['unique_create'] ?? true;
        $this->unique_update = $baseModel['unique_update'] ?? false;
        $this->unique_column = $baseModel['unique_column'] ?? false;
        $this->unique_key = $baseModel['unique_key'] ?? false;
        //trace_log("ok parse");
        //traitement des fields classique

        $fields = $baseModel['fields'];
        foreach ($fields as $key => $value) {
            $this->fieldObjects[$key] = new FieldObject($key, $value);
        }
        if (array_key_exists('relation_fields', $rows)) {
            $this->hasRelations = true;
            $this->relations = $this->getRelations($rows['relation_fields']);
        }
    }

    public function import($rows)
    {
        foreach ($rows as $row) {
            $model = $this->getModelOrNew($row);
            foreach ($this->fieldObjects as $fieldObject) {
                $model[$fieldObject->key] = $fieldObject->getValue($row);
            }
            if ($this->hasRelations) {
                foreach ($this->relations as $relation) {
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
            if ($this->isUpdate && !$this->unique_update) {
                // Si la relation existe mais qu'il n' y a pas de demande d'update, On saute le doublon.
            } elseif (!$this->isUpdate && !$this->unique_create) {
                // Si la relation n'existe pas et que la création est interdite, on saute.
            } else {
                if ($this->relationKeyName) {
                    $model[$this->relationKeyName] = $this->parentId;
                }
                $model->save();
            }
        }
    }

    public function getModelOrNew($columns)
    {
        // le this update est dangereux a retravailler.
        $this->isUpdate = false;
        //trace_log("On recherche le model");
        $excelUniqueColumn = $columns[$this->unique_column] ?? false;
        $findModel;
        //trace_log("Valeur recherché : ".$excelUniqueColumn." : ".$this->unique_key);
        if ($excelUniqueColumn) {
            $findModel = $this->model::where($this->unique_key, '=', $excelUniqueColumn)->first();
            if ($findModel) {
                $this->isUpdate = true;
                return $findModel;
            } else {
                return new $this->model;
            }
        } else {
            return new $this->model;
        }
    }

    public function getRelations($relation_rows)
    {
        $relations;
        foreach ($relation_rows as $key => $row) {
            $relation = new RelationObject($key, $row);
            $relations[$key] = $relation;
        }
        return $relations;
    }
}
