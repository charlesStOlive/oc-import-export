<?php namespace Waka\ImportExport\Behaviors;

use Backend\Classes\ControllerBehavior;
use Excel;
use Lang;
use Redirect;
use Session;
use Waka\ImportExport\Models\Export;
use Waka\Utils\Classes\DataSource;

class ExcelExport extends ControllerBehavior
{
    protected $exportPopupWidget;

    public function __construct($controller)
    {
        parent::__construct($controller);
        $this->exportPopupWidget = $this->createExportPopupWidget();
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
        $modelClass = post('modelClass');

        $ds = new DataSource($modelClass, 'class');
        $options = $ds->getPartialIndexOptions('Waka\ImportExport\Models\Export');

        $this->exportPopupWidget->getField('logeable_id')->options = $options;
        $this->vars['exportPopupWidget'] = $this->exportPopupWidget;
        $this->vars['controllerUrl'] = $ds->controller;
        $this->vars['modelClass'] = $modelClass;
        $this->vars['countFiltered'] = $query->count();
        $this->vars['countCheck'] = $countCheck;

        
        if($options) {
            return $this->makePartial('$/waka/importexport/behaviors/excelexport/_popup.htm');
        } else {
            return $this->makePartial('$/waka/utils/views/_popup_no_model.htm');
        }
    }

    public function onExportChildPopupForm()
    {
        //liste des requêtes filtrées
        $modelClass = post('modelClass');
        $modelId = post('modelId');
        $ds = new DataSource($modelClass, 'class');
        $options = $ds->getPartialIndexOptions('Waka\ImportExport\Models\Export', true);
        $this->exportPopupWidget->getField('logeable_id')->options = $options;
        $this->vars['exportPopupWidget'] = $this->exportPopupWidget;
        $this->vars['controllerUrl'] = $ds->controller;
        $this->vars['modelId'] = $modelId;
        $this->vars['modelClass'] = $modelClass;
        if($options) {
            return  $this->makePartial('$/waka/importexport/behaviors/excelexport/_popup_child.htm');
        } else {
            return $this->makePartial('$/waka/utils/views/_popup_no_model.htm');
        }
    }

    public function onExportChildContentForm()
    {
        //liste des requêtes filtrées
        $modelClass = post('modelClass');
        $modelId = post('modelId');
        $ds = new DataSource($modelClass, 'class');
        $options = $ds->getPartialIndexOptions('Waka\ImportExport\Models\Export', true);
        $this->exportPopupWidget->getField('logeable_id')->options = $options;
        $this->vars['exportPopupWidget'] = $this->exportPopupWidget;
        $this->vars['controllerUrl'] = $ds->controller;
        $this->vars['modelId'] = $modelId;
        $this->vars['modelClass'] = $modelClass;
        if($options) {
            return ['#popupActionContent' => $this->makePartial('$/waka/importexport/behaviors/excelexport/_popup_child.htm')];
        } else {
            return ['#popupActionContent' => $this->makePartial('$/waka/utils/views/_popup_no_model.htm')];
        }
    }

    public function onExportValidation()
    {
        $errors = $this->CheckValidation(\Input::all());
        //trace_log(\Input::all());
        if ($errors) {
            throw new \ValidationException(['error' => $errors]);
        }

        $controllerUrl = post('controllerUrl') . '/';
        $exportType = post('lotType');
        $configExportId = post('export_array.logeable_id');

        return Redirect::to('backend\\' . $controllerUrl . 'makeexcel/' . $configExportId . '/' . $exportType);
    }

    public function onExportChildValidation()
    {
        $errors = $this->CheckChildValidation(\Input::all());
        if ($errors) {
            throw new \ValidationException(['error' => $errors]);
        }

        $controllerUrl = post('controllerUrl') . '/';
        $parentId = post('modelId');
        $exportType = 'child';
        $configExportId = post('export_array.logeable_id');

        return Redirect::to('backend\\' . $controllerUrl . 'makeexcel/' . $configExportId . '/' . $exportType . '/' . $parentId);
    }

    public function CheckValidation($inputs)
    {
        $rules = [
            'export_array.logeable_id' => 'required',
        ];

        $messages = [
            'export_array.logeable_id.required' => Lang::get("waka.importexport::lang.errors.logeable_id"),
            'logeable_type' => Lang::get("waka.importexport::lang.errors.exportType"),
        ];

        $validator = \Validator::make($inputs, $rules, $messages);

        if ($validator->fails()) {
            return $validator->messages()->first();
        } else {
            return false;
        }
    }
    public function CheckChildValidation($inputs)
    {
        $rules = [
            'export_array.logeable_id' => 'required',
        ];

        $messages = [
            'export_array.logeable_id.required' => Lang::get("waka.importexport::lang.errors.logeable_id"),
        ];

        $validator = \Validator::make($inputs, $rules, $messages);

        if ($validator->fails()) {
            return $validator->messages()->first();
        } else {
            return false;
        }
    }
    public function makeTempexcel($id, $exportType, $parentId = null)
    {

        $configExportId = 1;
        $configExport = Export::find($configExportId);
        Session::put('excel.configExportId', $configExportId);
        return Excel::download(new $configExport->type->class, str_slug($configExport->name));
    }

    public function makeexcel($configExportId, $exportType=null, $parentId = null)
    {
        //trace_log($exportType);
        $configExport = Export::find($configExportId);
        //trace_log(str_slug($configExport->name) . 'xlsx');
        //Gestion de la liste avec la session
        $listId = null;
        if ($exportType == 'filtered') {
            //La methode FILTERED est pour l'instant abandonnée.
            $listId = Session::get('modelImportExportLog.listId');
            Session::forget('modelImportExportLog.checkedIds');
        } elseif ($exportType == 'checked') {
            $listId = Session::get('modelImportExportLog.checkedIds');
        }
        Session::forget('modelImportExportLog.listId');
        Session::forget('modelImportExportLog.checkedIds');

        if ($configExport->is_editable) {
            return Excel::download(new \Waka\ImportExport\Classes\Exports\ExportModel($configExport, $listId, $parentId), str_slug($configExport->name) . '.xlsx');
        } else {
            if (!$configExport->export_model_class) {
                throw new \SystemException('import_model_class manqunt dans configexport');
            }
            $classExcel = new \ReflectionClass($configExport->export_model_class);

            return Excel::download($classExcel->newInstanceArgs([$listId, $parentId]), str_slug($configExport->name) . '.xlsx');
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
