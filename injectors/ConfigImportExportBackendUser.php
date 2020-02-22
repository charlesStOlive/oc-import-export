<?php namespace Waka\ImportExport\Injectors;

use Backend\Controllers\Users as BackendUsers;
use Backend\Models\User as BackendUser;
use Event;

class ConfigImportExportBackendUser
{

    public static function inject()
    {

        BackendUser::extend(function ($model) {
            $model->belongsToMany['configexports'] = [
                'Waka\ImportExport\Models\ConfigExport',
                'table' => 'waka_configexports_users',
                'conditions' => 'is_editable = 1',
            ];
            $model->belongsToMany['configimports'] = [
                'Waka\ImportExport\Models\ConfigImport',
                'table' => 'waka_configimports_users',
                'conditions' => 'is_editable = 1',
            ];
        });

        BackendUsers::extend(function ($controller) {

            // Implement behavior if not already implemented
            if (!$controller->isClassExtendedWith('Backend.Behaviors.RelationController')) {
                $controller->implement[] = 'Backend.Behaviors.RelationController';
            }

            // Define property if not already defined
            if (!isset($controller->relationConfig)) {
                $controller->addDynamicProperty('relationConfig');
            }

            // Splice in configuration safely
            $myConfigPath = '$/waka/importexport/controllers/injectors/backenduser/config_relation.yaml';

            $controller->relationConfig = $controller->mergeConfig(
                $controller->relationConfig,
                $myConfigPath
            );

        });

        Event::listen('backend.form.extendFields', function ($widget) {

            // Only for the User controller
            if (!$widget->getController() instanceof BackendUsers) {
                return;
            }

            // Only for the User model
            if (!$widget->model instanceof BackendUser) {
                return;
            }

            if ($widget->model->hasAccess('waka.importexport.impexp.limited') || $widget->model->hasAccess('waka.importExport.imp')) {

                $widget->addTabFields([
                    'configimport' => [
                        'label' => 'Config Import',
                        'path' => '$/waka/importexport/controllers/injectors/backenduser/_field_configimports.htm',
                        'type' => 'partial',
                        'tab' => 'Config Import & Export',
                    ],
                ]);
            }

            if ($widget->model->hasAccess('waka.importexport.impexp.limited') || $widget->model->hasAccess('waka.importExport.exp')) {

                $widget->addTabFields([
                    'configexport' => [
                        'label' => 'Config Export',
                        'path' => '$/waka/importexport/controllers/injectors/backenduser/_field_configexports.htm',
                        'type' => 'partial',
                        'tab' => 'Config Import & Export',
                    ],
                ]);

            }

            $widget->addTabFields([
                '_comment' => [
                    'tab' => 'Config Import & Export',
                    'type' => 'commentfield',
                    'text' => "Si rien ne s'affiche, soit vous avez des droits pour tous les imports exports, soit vous n'avez aucun droit",
                ],
            ]);

            // Add an extra birthday field

        });

    }

}
