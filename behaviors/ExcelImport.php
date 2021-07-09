<?php namespace Waka\ImportExport\Behaviors;

use Backend\Classes\ControllerBehavior;
use Excel;
use Redirect;
use Waka\ImportExport\Models\Import;
use Waka\Utils\Classes\DataSource;

class ExcelImport extends ControllerBehavior
{
    protected $ImportPopupWidget;

    public function __construct($controller)
    {
        parent::__construct($controller);
        $this->ImportPopupWidget = $this->createImportPopupWidget();
    }
    // public function onImport()
    // {
    //     $configImportId = 1;
    //     $configImport = Import::find($configImportId);
    //     Session::put('excel.configImportId', $configImportId);
    //     return Redirect::refresh();
    // }
    public function onImportPopupForm()
    {
        $modelClass = post('modelClass');

        $ds = new DataSource($modelClass, 'class');;
        $options = $ds->getPartialIndexOptions('Waka\ImportExport\Models\Import');

        $this->ImportPopupWidget->getField('logeable_id')->options = $options;
        $this->vars['ImportPopupWidget'] = $this->ImportPopupWidget;
        $this->vars['model'] = $modelClass;

        if($options) {
            return $this->makePartial('$/waka/importexport/behaviors/excelimport/_popup.htm');
        } else {
            return $this->makePartial('$/waka/utils/views/_popup_no_model.htm');
        }
        
    }

    public function onImportChildPopupForm()
    {
        $modelClass = post('modelClass');
        $modelId = post('modelId');

        $ds = new DataSource($modelClass, 'class');
        $options = $ds->getPartialIndexOptions('Waka\ImportExport\Models\Import', true);

        $this->ImportPopupWidget->getField('logeable_id')->options = $options;
        $this->vars['ImportPopupWidget'] = $this->ImportPopupWidget;
        $this->vars['modelClass'] = $modelClass;
        $this->vars['modelId'] = $modelId;

        if($options) {
            return $this->makePartial('$/waka/importexport/behaviors/excelimport/_popup_child.htm');
        } else {
            return $this->makePartial('$/waka/utils/views/_popup_no_model.htm');
        }
        
    }

    public function onImportValidation()
    {
        $data = $this->ImportPopupWidget->getSaveData();
        //trace_log($this->ImportPopupWidget->getSaveData());
        $sessionKey = \Input::get('_session_key');
        $iel = new \Waka\ImportExport\Models\ImportExportLog;
        $iel->fill($data);
        $file = $iel
            ->excel_file()
            ->withDeferred($sessionKey)
            ->first();
        //le fichier est maintenant prêt à être traité.
        //$iel->save();
        $configImportId = $data['logeable_id'];
        $useQueue = $data['use_queue'];
        // trace_log(post('logeable_id'));
        // trace_log($data);
        if ($useQueue) {
            //trace_log("queue");
            $datas = [
                'configImportId' => $configImportId,
                'file_path' => $file->getDiskPath(),
            ];
            $jobId = \Queue::push('\Waka\ImportExport\Classes\Queue\QueueExcel@import', $datas);
            \Event::fire('job.create.imp', [$jobId, 'Import en attente ']);
        } else {
            $configImport = Import::find($configImportId);
            if ($configImport->is_editable) {
                Excel::import(new \Waka\ImportExport\Classes\Imports\ImportModel($configImport), $file->getDiskPath());
            } else {
                if (!$configImport->import_model_class) {
                    throw new \SystemException('import_model_class manqunt dans configexport');
                }
                Excel::import(new $configImport->import_model_class, $file->getDiskPath());
            }
        }

        return Redirect::refresh();
    }

    public function onImportChildValidation()
    {
        $data = $this->ImportPopupWidget->getSaveData();
        //trace_log($this->ImportPopupWidget->getSaveData());
        $sessionKey = \Input::get('_session_key');
        $iel = new \Waka\ImportExport\Models\ImportExportLog;
        $iel->fill($data);
        $file = $iel
            ->excel_file()
            ->withDeferred($sessionKey)
            ->first();
        //le fichier est maintenant prêt à être traité.
        //$iel->save();
        $configImportId = $data['logeable_id'];
        $useQueue = $data['use_queue'];
        $parentId = post('modelId');
        // trace_log(post('logeable_id'));
        // trace_log($data);
        if ($useQueue) {
            //trace_log("queue");
            $datas = [
                'configImportId' => $configImportId,
                'file_path' => $file->getDiskPath(),
            ];
            $jobId = \Queue::push('\Waka\ImportExport\Classes\Queue\QueueExcel@import', $datas);
            \Event::fire('job.create.imp', [$jobId, 'Import en attente ']);
        } else {
            $configImport = Import::find($configImportId);
            if ($configImport->is_editable) {
                Excel::import(new \Waka\ImportExport\Classes\Imports\ImportModel($configImport, $parentId), $file->getDiskPath());
            } else {
                if (!$configImport->import_model_class) {
                    throw new \SystemException('import_model_class manqunt dans configexport');
                }
                $importClass = new \ReflectionClass($configImport->import_model_class);
                Excel::import($importClass->newInstanceArgs([$parentId]) , $file->getDiskPath());
            }
        }

        return Redirect::refresh();
    }

    public function createImportPopupWidget()
    {
        $config = $this->makeConfig('$/waka/importexport/models/importexportlog/fields_popup_import.yaml');
        $config->alias = 'importformWidget';
        $config->arrayName = 'import_array';
        $config->model = new \Waka\ImportExport\Models\ImportExportLog;
        $widget = $this->makeWidget('Backend\Widgets\Form', $config);
        $widget->bindToController();
        return $widget;
    }
}
