<?php namespace Waka\ImportExport\Behaviors;

use Backend\Classes\ControllerBehavior;
use Excel;
use Redirect;
use Session;
use Waka\ImportExport\Models\ConfigExport;

class ExcelExport extends ControllerBehavior
{
    protected $ExportPopupWidget;

    public function __construct($controller)
    {
        parent::__construct($controller);
        $this->ExportPopupWidget = $this->createExportPopupWidget();
    }

    public function onExportPopupForm()
    {
        trace_log(post());
        //liste des requêtes filtrées
        $lists = $this->controller->makeLists();
        $widget = $lists[0] ?? reset($lists);
        $query = $widget->prepareQuery();
        $results = $query->get();
        trace_log($query->count());

        $checkedIds = post('checked');

        $countCheck = null;
        if(is_countable($checkedIds)) {
            $countCheck = count($checkedIds);
        }

       
        //
        Session::put('modelImportExportLog.listId', $results->lists('id'));
        Session::put('modelImportExportLog.checkedIds', $checkedIds);
        //
        $model = post('model');
        Session::put('modelImportExportLog.targetModel', $model);
        $this->vars['ExportPopupWidget'] = $this->ExportPopupWidget;
        $this->vars['model'] = $model;
        $this->vars['all'] = $model::count();
        $this->vars['filtered'] = $query->count();
        $this->vars['countCheck'] = $countCheck;
        
        return $this->makePartial('$/waka/importexport/behaviors/excelexport/_popup.htm');
    }

    public function onExportValidation()
    {
        $data = $this->ExportPopupWidget->getSaveData();
        // $sessionKey = \Input::get('_session_key');
        // $iel = new \Waka\ImportExport\Models\ImportExportLog;
        // $iel->fill($data);
        // $iel->logeable_type = post('logeable_type');
        $configExportId = $data['logeable_id'];
        // $file = $iel
        //     ->excel_file()
        //     ->withDeferred($sessionKey) // how to get this session key dynamically?
        //     ->first();
        //le fichier est maintenant prêt à être traité.
        //$configExportId = post('logeable_id');
        return Redirect::to('backend/waka/crsm/contacts/makeexcel/' . $configExportId);
    }
    public function makeTempexcel($id)
    {
        $configExportId = 1;
        $configExport = ConfigExport::find($configExportId);
        Session::put('excel.configExportId', $configExportId);
        return Excel::download(new $configExport->type->class, 'test.xlsx');
    }

    public function makeexcel($id)
    {
        $configExportId = $id;
        $configExport = ConfigExport::find($configExportId);
        Session::put('excel.configExportId', $configExportId);
        if ($configExport->is_editable) {
            return Excel::download(new \Waka\ImportExport\Classes\Exports\ExportModel, 'test.xlsx');
        } else {
            if (!$configExport->import_model_class) {
                throw new \SystemException('import_model_class manqunt dans configexport');
            }
            return Excel::download(new $configExport->import_model_class, 'test.xlsx');
        }
        //return Excel::download(new $configExport->type->class, 'test.xlsx');

    }

    public function createExportPopupWidget()
    {
        $config = $this->makeConfig('$/waka/importexport/models/importexportlog/fields_popup_export.yaml');
        $config->alias = 'exportformWidget';
        $config->arrayName = 'export_array';
        $config->model = new \Waka\ImportExport\Models\ImportExportLog;
        $widget = $this->makeWidget('Backend\Widgets\Form', $config);
        $widget->bindToController();
        return $widget;
    }
}
