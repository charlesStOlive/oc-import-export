<?php

return [
    'menu' => [
        'imports_title' => 'Import',
        'imports_description' => 'Gestion des imports',
        'exports_title' => 'Export',
        'exports_description' => 'Gestion des exports',
        'types_title' => "Types",
        'types_description' => "Gestion des types d'import ou export",
        'logs_title' => "Logs",
        'logs_description' => "Liste de tous les imports et exports",
        'category' => 'Wakaari Import',
    ],
    'popup' => [
        "all" => "Tout",
        "filtered" => "Liste filtrée",
        "checked" => "Lignes cochées",
        "choose_type" => "Que voulez vous exporter ?",
    ],
    'comon' => [
        'name' => "Nom de l'import",
        'model' => "Model cible",
        'config' => 'Configuration',
        'type' => "Type d'import",
        'type_placeholder' => "Choisissez le type d'import",
        'use_batch' => 'Utilisation du batch',
        'comment' => 'Information',
        'is_editable' => 'Editable ?',
        'import_model_class' => "Chemin de la classe d'import",
    ],
    'user' => [
        'first_name' => "Prénom",
        'last_name' => "Nom",
        'role' => "Role",

    ],
    'global' => [
        'export_title' => "Exporter vers Excel",
        'import_title' => "Importer depuis Excel",
        'btn_import' => "Import Excel",
        'btn_export' => "Export Excel",
    ],
    'type' => [
        'name' => "Intitulé de la class d'import",
        'import' => "Est ce un import ?",
        'class' => 'Chemin de la classe',
    ],
    'importexportlog' => [
        'logeable' => 'Choisissez un type',
        'logeable_placeholder' => '--Choisissez--',
        'excel_file' => 'Importer une feuille excel',
        "use_queue" => "Utilisr les taches serveurs.",
        "use_queue_com" => "Fortement consillé si le fichier dépasse 250Ko",
    ],
    'errors' => [
        'logeable_id' => "Vous devez choisir un export",
        "exportType" => "Le type d'export est manquant",
    ],
];
