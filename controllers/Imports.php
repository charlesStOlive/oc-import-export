<?php namespace Waka\ImportExport\Controllers;

use BackendMenu;
use Backend\Classes\Controller;
use System\Classes\SettingsManager;

/**
 * Import Back-end Controller
 */
class Imports extends Controller
{
    public $implement = [
        'Backend.Behaviors.FormController',
        'Backend.Behaviors.ListController',
        'Waka.Utils.Behaviors.DuplicateModel',

    ];

    public $formConfig = 'config_form.yaml';
    public $duplicateConfig = 'config_duplicate.yaml';
    public $listConfig = [
        'imports' => 'config_imports_list.yaml',
        'exports' => 'config_exports_list.yaml',
    ];

    public function __construct()
    {
        parent::__construct();
        BackendMenu::setContext('October.System', 'system', 'settings');
        SettingsManager::setContext('Waka.ImportExport', 'imports_exports');
    }

    public function index($tab = null)
    {
        $this->asExtension('ListController')->index();
        $this->bodyClass = 'compact-container';
        $this->vars['activeTab'] = $tab ?: 'templates';
    }
}
