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
        Excel::import(new $configImport->type->class, plugins_path('waka/crsm/updates/excels/contacts.xlsx'));
        return Redirect::refresh();
    }
    public function onImportPopupForm() {
        $model = post('model');
        $this->vars['options'] = $this->loadDropdownImportValue($model);
        $this->vars['ImportPopupWidget'] = $this->ImportPopupWidget;
        $this->vars['model'] = $model;
        return $this->makePartial('$/waka/importexport/behaviors/excelimport/_popup.htm');
    }
    public function loadDropdownImportValue($model) {
        return ConfigImport::where('model', '=', $model)->lists('name', 'id');
    }
 
    public function onImportValidation() {
        $data = $this->ImportPopupWidget->getSaveData();
        $sessionKey = \Input::get('_session_key');
        $iel = new \Waka\ImportExport\Models\ImportExportLog;
        $iel->fill($data);
        $iel->logeable_type = post('logeable_type');
        $iel->logeable_id = post('logeable_id');
        $file = $iel
            ->excel_file()
            ->withDeferred($sessionKey) // how to get this session key dynamically?
            ->first();
        //le fichier est maintenant prêt à être traité. 
        $configImportId = post('logeable_id');
        $configImport = ConfigImport::find($configImportId);
        Session::put('excel.configImportId', $configImportId);
        Excel::import(new $configImport->type->class, $file->getDiskPath());
        return Redirect::refresh();
    }

    public function createImportPopupWidget() {
        $config = $this->makeConfig('$/waka/importexport/models/importexportlog/fields_popup.yaml');
        $config->alias = 'importformWidget';
        $config->arrayName = 'import_array';
        $config->model = new \Waka\ImportExport\Models\ImportExportLog;
        $widget = $this->makeWidget('Backend\Widgets\Form', $config);
        $widget->bindToController();
        return $widget;
    }
}