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
    'comon' => [
        'name' => "Nom de l'import",
        'model' => "Model cible",
        'config' => 'Configuration',
        'type' => "Type d'import",
        'type_placeholder' => "Choisissez le type d'import",
        'use_batch' => 'Utilisation du batch',
        "unique_care" => "Vérifier les clés unique",
        "unique_care_comment" => "Permet de modifier le comportement de l'importateur",
        "unique_action" => "Updater le modèle",
        "unique_action_comment" => "Updater le modèle avec les infos contenu dans la feuille excel, sinon les champs seront ignorés",
        "unique_key" => "Clé unique du modèle à surveiller",
        "unique_column" => "Colonne Excel contenant la clé",
    ],
    'type' => [
        'name' => "Intitulé de la class d'import",
        'import' => "Est ce un import ?",
        'class' => 'Chemin de la classe',
    ],
];
