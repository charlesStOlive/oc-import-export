<?php namespace Waka\ImportExport\Behaviors;

use Backend\Classes\ControllerBehavior;
use October\Rain\Exception\ApplicationException;
use Waka\ImportExport\Models\ConfigImport;
use Flash;
use Lang;
use Redirect;
use Session;
use Excel;
class ExcelImport extends ControllerBehavior
{
    protected $ImportPopupWidget;

	public function __construct($controller)
    {
        parent::__construct($controller);
        $this->ImportPopupWidget = $this->createImportPopupWidget();
    }
    public function onImport() {
        $configImportId = 1;
        $configImport = ConfigImport::find($configImportId);
        Session::put('excel.configImportId', $configImportId);
        Excel::import(new $configImport->type->class, plugins_path('waka/crsm/updates/excels/maj_contact.xlsx'));
        return Redirect::refresh();
    }
    public function onImportPopupForm() {
        $model = post('model');
        Session::put('modelImportExportLog.targetModel', $model);
        $this->vars['ImportPopupWidget'] = $this->ImportPopupWidget;
        $this->vars['model'] = $model;
        return $this->makePartial('$/waka/importexport/behaviors/excelimport/_popup.htm');
    }
 
    public function onImportValidation() {
        $data = $this->ImportPopupWidget->getSaveData();
        $sessionKey = \Input::get('_session_key');
        $iel = new \Waka\ImportExport\Models\ImportExportLog;
        $iel->fill($data);
        $file = $iel
            ->excel_file()
            ->withDeferred($sessionKey) // how to get this session key dynamically?
            ->first();
        //le fichier est maintenant prêt à être traité. 
        //$iel->save();
        $configImportId = $data['logeable_id'];
        // trace_log(post('logeable_id'));
        // trace_log($data);

        $configImport = ConfigImport::find($configImportId);
        Session::put('excel.configImportId', $configImportId);
        Excel::import(new $configImport->type->class, $file->getDiskPath());
        return Redirect::refresh();
    }

    public function createImportPopupWidget() {
        $config = $this->makeConfig('$/waka/importexport/models/importexportlog/fields_popup_import.yaml');
        $config->alias = 'importformWidget';
        $config->arrayName = 'import_array';
        $config->model = new \Waka\ImportExport\Models\ImportExportLog;
        $widget = $this->makeWidget('Backend\Widgets\Form', $config);
        $widget->bindToController();
        return $widget;
    }
}