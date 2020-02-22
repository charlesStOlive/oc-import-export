<?php namespace Waka\ImportExport\Controllers;

use BackendMenu;
use Backend\Classes\Controller;
use System\Classes\SettingsManager;

/**
 * Config Exports Back-end Controller
 */
class ConfigExports extends Controller
{
    public $implement = [
        'Backend.Behaviors.FormController',
        'Backend.Behaviors.ListController',
        'Backend.Behaviors.RelationController',
        'Waka.Utils.Behaviors.DuplicateModel',

    ];

    public $formConfig = 'config_form.yaml';
    public $listConfig = 'config_list.yaml';
    public $duplicateConfig = 'config_duplicate.yaml';
    public $relationConfig = 'config_relation.yaml';

    public function __construct()
    {
        parent::__construct();

        //BackendMenu::setContext('Waka.ImportExport', 'importexport', 'configexports');
        BackendMenu::setContext('October.System', 'system', 'settings');
        SettingsManager::setContext('Waka.ImportExport', 'configExports');
    }
}
