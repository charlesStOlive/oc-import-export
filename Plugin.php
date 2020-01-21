<?php namespace Waka\ImportExport;

use Backend;
use System\Classes\PluginBase;
use Lang;
use Event;
use View;

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
            'name'        => 'ImportExport',
            'description' => 'No description provided yet...',
            'author'      => 'Waka',
            'icon'        => 'icon-leaf'
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
        Event::listen('backend.top.index', function($controller) {
            if(in_array('Waka.ImportExport.Behaviors.ExcelImport', $controller->implement)) {
                $data = [
                    'model' => $modelClass = str_replace('\\', '\\\\', $controller->listGetConfig()->modelClass),
                    //'modelId' => $controller->formGetModel()->id
                ];
                return View::make('waka.importexport::excelimport_popup')->withData($data);;
            }
        });
        Event::listen('backend.top.index', function($controller) {
            if(in_array('Waka.ImportExport.Behaviors.ExcelExport', $controller->implement)) {
                $data = [
                    'model' => $modelClass = str_replace('\\', '\\\\', $controller->listGetConfig()->modelClass),
                    //'modelId' => $controller->formGetModel()->id
                ];
                return View::make('waka.importexport::excelexport_popup')->withData($data);;
            }
        });

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
        return []; // Remove this line to activate

        return [
            'waka.importexport.some_permission' => [
                'tab' => 'ImportExport',
                'label' => 'Some permission'
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
        return[];

    }
    public function registerSettings()
    {

        return [
            'import' => [
                'label'       => Lang::get('waka.importexport::lang.menu.imports_title'),
                'description' => Lang::get('waka.importexport::lang.menu.imports_description'),
                'category'    => Lang::get('waka.importexport::lang.menu.category'),
                'url'         => Backend::url('waka/importexport/configimports'),
                'icon'        => 'icon-caret-square-o-down',
                'permissions' => ['waka.importexport.*'],
                'order'       => 500,
            ],
            'export' => [
                'label'       => Lang::get('waka.importexport::lang.menu.exports_title'),
                'description' => Lang::get('waka.importexport::lang.menu.exports_description'),
                'category'    => Lang::get('waka.importexport::lang.menu.category'),
                'url'         => Backend::url('waka/importexport/configexports'),
                'icon'        => 'icon-caret-square-o-up',
                'permissions' => ['waka.importexport.*'],
                'order'       => 500,
            ],
            'type' => [
                'label'       => Lang::get('waka.importexport::lang.menu.types_title'),
                'description' => Lang::get('waka.importexport::lang.menu.types_description'),
                'category'    => Lang::get('waka.importexport::lang.menu.category'),
                'url'         => Backend::url('waka/importexport/types'),
                'icon'        => 'icon-file-code-o',
                'permissions' => ['waka.importexport.*'],
                'order'       => 500,
            ],
            'logs' => [
                'label'       => Lang::get('waka.importexport::lang.menu.logs_title'),
                'description' => Lang::get('waka.importexport::lang.menu.logs_description'),
                'category'    => Lang::get('waka.importexport::lang.menu.category'),
                'url'         => Backend::url('waka/importexport/types'),
                'icon'        => 'icon-terminal',
                'permissions' => ['waka.importexport.*'],
                'order'       => 500,
            ],
        ];
    }
}
