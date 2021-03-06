<?php
return [
    'btns' => [
        'exportChildExcel' => [
            'label' => 'waka.importexport::lang.global.btn_export',
            'class' => 'btn-secondary',
            'ajaxCaller' => 'onExportChildPopupForm',
            'ajaxInlineCaller' => 'onExportChildContentForm',
            'icon' => 'oc-icon-file-excel-o',
        ],
        'exportExcel' => [
            'label' => 'waka.importexport::lang.global.btn_export',
            'class' => 'btn-secondary',
            'ajaxCaller' => 'onExportPopupForm',
            'icon' => 'oc-icon-file-excel-o',
        ],
        'importChildExcel' => [
            'label' => 'waka.importexport::lang.global.btn_import',
            'class' => 'btn-secondary',
            'ajaxCaller' => 'onImportChildPopupForm',
            'ajaxInlineCaller' => 'onImportChilContentdForm',
            'icon' => 'oc-icon-file-excel-o',
        ],
         'importExcel' => [
            'label' => 'waka.importexport::lang.global.btn_import',
            'class' => 'btn-secondary',
            'ajaxCaller' => 'onImportPopupForm',
            'icon' => 'oc-icon-file-excel-o',
        ],
    ],

];
