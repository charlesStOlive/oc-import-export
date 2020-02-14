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
        $model = post('model');
        Session::put('modelImportExportLog.targetModel', $model);
        $this->vars['ExportPopupWidget'] = $this->ExportPopupWidget;
        $this->vars['model'] = $model;
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
        return Excel::download(new $configExport->type->class, 'test.xlsx');

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
