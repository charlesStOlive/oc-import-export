<?php namespace Waka\ImportExport\Controllers;

use BackendMenu;
use Backend\Classes\Controller;

/**
 * Config Imports Back-end Controller
 */
class ConfigImports extends Controller
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

        //BackendMenu::setContext('Waka.ImportExport', 'importexport', 'configimports');
        BackendMenu::setContext('October.System', 'system', 'settings');
        SettingsManager::setContext('Waka.ImportExport', 'configimports');
    }
}
