<?php namespace Waka\ImportExport;

use Backend;
use Event;
use Lang;
use System\Classes\PluginBase;

/**
 * ImportExport Plugin Information File
 */
class Plugin extends PluginBase
{
    /**
     * @var array Plugin dependencies
     */
    public $require = ['Waka.Utils'];

    /**
     * Returns information about this plugin.
     *
     * @return array
     */
    public function pluginDetails()
    {
        return [
            'name' => 'ImportExport',
            'description' => 'No description provided yet...',
            'author' => 'Waka',
            'icon' => 'icon-leaf',
        ];
    }

    /**
     * Register method, called when the plugin is first registered.
     *
     * @return void
     */
    public function register()
    {
    }

    /**
     * Boot method, called right before the request route.
     *
     * @return array
     */
    public function boot()
    {
       //A conserver en exemple //
        // Event::listen('backend.update.prod', function ($controller) {
        //     if (get_class($controller) == 'Waka\ImportExport\Controllers\Imports') {
        //         return;
        //     }

        //     if (in_array('Waka.ImportExport.Behaviors.ExcelImport', $controller->implement)) {
        //         $data = [
        //             'model' => $modelClass = str_replace('\\', '\\\\', get_class($controller->formGetModel())),
        //             'modelId' => $controller->formGetModel()->id,
        //         ];
        //         return View::make('waka.importexport::excelimport_child_popup')->withData($data);;
        //     }
        // });
    }

    /**
     * Registers any front-end components implemented in this plugin.
     *
     * @return array
     */
    public function registerComponents()
    {
        return []; // Remove this line to activate

        return [
            'Waka\ImportExport\Components\MyComponent' => 'myComponent',
        ];
    }

    /**
     * Registers any back-end permissions used by this plugin.
     *
     * @return array
     */
    public function registerPermissions()
    {
        return [
            'waka.importexport.imp.admin' => [
                'tab' => 'Waka - Import Export',
                'label' => 'Administrateur des imports',
            ],
            'waka.importexport.imp.user' => [
                'tab' => 'Waka - Import Export',
                'label' => 'Importe des données avec des restrictions',
            ],
            'waka.importexport.exp.admin' => [
                'tab' => 'Waka - Import Export',
                'label' => 'Administrateur des exports',
            ],
            'waka.importexport.exp.user' => [
                'tab' => 'Waka - Import Export',
                'label' => 'Exporte des données avec des restrictions',
            ],
        ];
    }

    /**
     * Registers back-end navigation items for this plugin.
     *
     * @return array
     */
    public function registerNavigation()
    {
        return [];
    }
    public function registerSettings()
    {

        return [
            'imports_exports' => [
                'label' => Lang::get('waka.importexport::lang.menu.impexp_title'),
                'description' => Lang::get('waka.importexport::lang.menu.impexp_description'),
                'category' => Lang::get('waka.utils::lang.menu.settings_category_model'),
                'url' => Backend::url('waka/importexport/imports/index/imports'),
                'icon' => 'icon-table',
                'permissions' => ['waka.importexport.admin'],
                'order' => 70,
            ],
        ];
    }
}
