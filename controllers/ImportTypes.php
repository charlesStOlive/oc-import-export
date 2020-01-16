<?php namespace Waka\ImportExport\Controllers;

use BackendMenu;
use Backend\Classes\Controller;

/**
 * Import Types Back-end Controller
 */
class ImportTypes extends Controller
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

        //BackendMenu::setContext('Waka.ImportExport', 'importexport', 'importtypes');
        BackendMenu::setContext('October.System', 'system', 'settings');
        SettingsManager::setContext('Waka.ImportExport', 'importtypes');
    }
}
