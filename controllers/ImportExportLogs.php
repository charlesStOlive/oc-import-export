<?php namespace Waka\ImportExport\Controllers;

use BackendMenu;
use Backend\Classes\Controller;

/**
 * Import Export Logs Back-end Controller
 */
class ImportExportLogs extends Controller
{
    public $implement = [
        'Backend.Behaviors.FormController',
        'Backend.Behaviors.ListController'
    ];

    public $formConfig = 'config_form.yaml';
    public $listConfig = 'config_list.yaml';

    public function __construct()
    {
        parent::__construct();

        //BackendMenu::setContext('Waka.ImportExport', 'importexport', 'importexportlogs');
        BackendMenu::setContext('October.System', 'system', 'settings');
        SettingsManager::setContext('Waka.ImportExport', 'importexportlogs');
    }
}
