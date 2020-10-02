<?php namespace Waka\ImportExport\Behaviors;

use Backend\Classes\ControllerBehavior;
use Excel;
use Lang;
use Redirect;
use Session;
use Waka\ImportExport\Models\ConfigExport;
use Waka\Utils\Classes\DataSource;

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
        //liste des requêtes filtrées
        $lists = $this->controller->makeLists();
        $widget = $lists[0] ?? reset($lists);
        $query = $widget->prepareQuery();
        $results = $query->get();

        $checkedIds = post('checked');

        $countCheck = null;
        if (is_countable($checkedIds)) {
            $countCheck = count($checkedIds);
        }

        //
        Session::put('modelImportExportLog.listId', $results->lists('id'));
        Session::put('modelImportExportLog.checkedIds', $checkedIds);
        //
        $model = post('model');

        $ds = new DataSource($model, 'class');
        $options = $ds->getPartialIndexOptions('Waka\ImportExport\Models\ConfigExport');

        $this->ExportPopupWidget->getField('logeable_id')->options = $options;
        $this->vars['ExportPopupWidget'] = $this->ExportPopupWidget;
        $this->vars['controllerUrl'] = $ds->controller;
        $this->vars['model'] = $model;
        $this->vars['all'] = $model::count();
        $this->vars['filtered'] = $query->count();
        $this->vars['countCheck'] = $countCheck;

        return $this->makePartial('$/waka/importexport/behaviors/excelexport/_popup.htm');
    }

    public function onExportValidation()
    {
        $errors = $this->CheckValidation(\Input::all());
        if ($errors) {
            throw new \ValidationException(['error' => $errors]);
        }

        $controllerUrl = post('controllerUrl') . '/';
        $exportType = post('exportType');
        $configExportId = post('export_array.logeable_id');

        //return Redirect::to('backend/waka/crsm/regions/makeexcel/' . $configExportId . '/' . $exportType);
        return Redirect::to('backend\\' . $controllerUrl . 'makeexcel/' . $configExportId . '/' . $exportType);
    }

    public function CheckValidation($inputs)
    {
        $rules = [
            'export_array.logeable_id' => 'required',
            'exportType' => 'required',
        ];

        $messages = [
            'export_array.logeable_id.required' => Lang::get("waka.importexport::lang.errors.logeable_id"),
            'exportType.wakaPdfId' => Lang::get("waka.importexport::lang.errors.exportType"),
        ];

        $validator = \Validator::make($inputs, $rules, $messages);

        if ($validator->fails()) {
            return $validator->messages()->first();
        } else {
            return false;
        }
    }
    public function makeTempexcel($id, $exportType)
    {

        $configExportId = 1;
        $configExport = ConfigExport::find($configExportId);
        Session::put('excel.configExportId', $configExportId);
        return Excel::download(new $configExport->type->class, str_slug($configExport->name));
    }

    public function makeexcel($configExportId, $exportType)
    {
        $configExport = ConfigExport::find($configExportId);
        //trace_log(str_slug($configExport->name) . 'xlsx');
        //Gestion de la liste avec la session
        $listId = null;
        if ($exportType == 'filtered') {
            $listId = Session::get('modelImportExportLog.listId');
            Session::forget('modelImportExportLog.checkedIds');

        } elseif ($exportType == 'checked') {
            $listId = Session::get('modelImportExportLog.checkedIds');

        }
        Session::forget('modelImportExportLog.listId');
        Session::forget('modelImportExportLog.checkedIds');

        if ($configExport->is_editable) {
            return Excel::download(new \Waka\ImportExport\Classes\Exports\ExportModel($configExport, $listId), str_slug($configExport->name) . '.xlsx');
        } else {
            if (!$configExport->import_model_class) {
                throw new \SystemException('import_model_class manqunt dans configexport');
            }
            $classExcel = new \ReflectionClass($configExport->import_model_class);

            return Excel::download($classExcel->newInstanceArgs([$listId]), str_slug($configExport->name) . '.xlsx');
        }
        //return Excel::download(new $configExport->type->class, 'test.xlsx');

    }

    public function createExportPopupWidget()
    {
        $config = $this->makeConfig('$/waka/importexport/models/importexportlog/fields_popup_export.yaml');
        $config->alias = 'exportExcelformWidget';
        $config->arrayName = 'export_array';
        $config->model = new \Waka\ImportExport\Models\ImportExportLog;
        $widget = $this->makeWidget('Backend\Widgets\Form', $config);
        $widget->bindToController();
        return $widget;
    }
}
