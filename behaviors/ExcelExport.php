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

        $dataSource = $this->getDataSourceFromModel($model);
        //trace_log($dataSource->name);
        $options = $dataSource->getPartialIndexOptions('Waka\ImportExport\Models\ConfigExport');

        $this->ExportPopupWidget->getField('logeable_id')->options = $options;
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
        $exportType = post('exportType');
        $configExportId = $data['logeable_id'];
        return Redirect::to('backend/waka/crsm/contacts/makeexcel/' . $configExportId . '/' . $exportType);
    }
    public function makeTempexcel($id, $exportType)
    {
        $configExportId = 1;
        $configExport = ConfigExport::find($configExportId);
        Session::put('excel.configExportId', $configExportId);
        return Excel::download(new $configExport->type->class, 'test.xlsx');
    }

    public function makeexcel($configExportId, $exportType)
    {
        $configExport = ConfigExport::find($configExportId);

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
            return Excel::download(new \Waka\ImportExport\Classes\Exports\ExportModel($configExport, $listId), 'test.xlsx');
        } else {
            if (!$configExport->import_model_class) {
                throw new \SystemException('import_model_class manqunt dans configexport');
            }
            $classExcel = new \ReflectionClass($configExport->import_model_class);

            return Excel::download($classExcel->newInstanceArgs([$listId]), 'test.xlsx');
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

    public function getDataSourceFromModel(String $model)
    {
        $modelClassDecouped = explode('\\', $model);
        $modelClassName = array_pop($modelClassDecouped);
        return \Waka\Utils\Models\DataSource::where('model', '=', $modelClassName)->first();
    }
}
