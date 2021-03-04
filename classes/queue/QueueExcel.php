<?php namespace Waka\ImportExport\Classes\Queue;

use Event;
use Excel;
use Waka\ImportExport\Models\ConfigImport;

class QueueExcel
{
    public function import($job, $datas)
    {

        if ($job) {
            Event::fire('job.start.import', [$job, 'Import config ']);
        }

        $filePath = $datas['file_path'];
        $configImportId = $datas['configImportId'];
        $configImport = ConfigImport::find($configImportId);
        if ($configImport->is_editable) {
            Excel::import(new \Waka\ImportExport\Classes\Imports\ImportModel($configImport), $filePath);
        } else {
            if (!$configImport->import_model_class) {
                throw new \SystemException('import_model_class manqunt dans configexport');
            }
            Excel::import(new $configImport->import_model_class, $filePath);
        }

        if ($job) {
            Event::fire('job.end.import', [$job]);
            $job->delete();
        }
    }
    public function export($job, $data)
    {
    }
}
