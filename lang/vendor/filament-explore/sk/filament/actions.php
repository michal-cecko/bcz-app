<?php

return [
    'upload_action' => [
        'label' => 'Nahrať',
        'modal_heading' => 'Nahrať súbory',
        'modal_description' => 'Pretiahnite alebo kliknite pre nahratie nových položiek do knižnice.',
        'modal_cancel_action_label' => 'Zavrieť',
        'modal_submit_action_label' => 'Uložiť súbory',
        'success_notification_title' => '{1}:count súbor bol úspešne nahraný|[2,4]:count súbory boli úspešne nahrané|[5,*]:count súborov bolo úspešne nahraných',
        'schema' => [
            'files' => [
                'label' => 'Nahrať súbor',
                'below_content' => [
                    'text' => 'Môžete nahrať súbory do veľkosti :maxFileSizeFormatted.',
                ],
                'validation_messages' => [
                    'required' => 'Prosím, vyberte alebo pretiahnite súbor na nahratie.',
                ],
            ],
        ],
    ],
    'create_folder_action' => [
        'label' => 'Vytvoriť priečinok',
        'modal_submit_action_label' => 'Uložiť priečinok',
        'success_notification_title' => 'Priečinok :folderName bol úspešne vytvorený',
        'schema' => [
            'name' => [
                'label' => 'Názov priečinka',
                'below_content' => [
                    'text' => [
                        'allows_directory_separator_in_folder_name' => [
                            'true' => 'Pre vytvorenie podpriečinkov prejdite do priečinka.',
                            'false' => 'Pomocou `:directorySeparator` môžete vytvárať podpriečinky.',
                        ],
                    ],
                ],
            ],
        ],
    ],
    'delete_bulk_action' => [
        'label' => 'Odstrániť',
        'modal_heading' => 'Odstrániť 1 položku|Odstrániť :count položiek',
        'modal_description' => [
            'with_folders' => '{1} Obsah vybraného priečinka bude natrvalo odstránený. Naozaj to chcete urobiť?|[2,*] Obsah vybraných :count priečinkov bude natrvalo odstránený. Naozaj to chcete urobiť?',
            'without_folders' => 'Naozaj chcete natrvalo odstrániť tieto položky?',
        ],
        'modal_submit_action_label' => '{0} Odstrániť 0 položiek|{1} Odstrániť 1 položku|[2,*] Odstrániť :count položiek',
        'success_notification_title' => '{0} Žiadne položky neboli odstránené|{1} 1 položka bola úspešne odstránená|[2,*] :count položiek bolo úspešne odstránených',
    ],
    'move_bulk_action' => [
        'label' => 'Presunúť',
        'modal_heading' => 'Presunúť 1 položku|Presunúť :count položiek',
        'modal_submit_action_label' => [
            'root' => '{0} Presunúť 0 položiek|{1} Presunúť 1 položku|[2,*] Presunúť :count položiek',
            'folder' => '{0} Presunúť 0 položiek do :displayName|{1} Presunúť 1 položku do :displayName|[2,*] Presunúť :count položiek do :displayName',
        ],
        'success_notification_title' => '{0} Žiadne položky neboli presunuté|{1} 1 položka bola úspešne presunutá|[2,*] :count položiek bolo úspešne presunutých',
    ],
    'move_action' => [
        'label' => 'Presunúť',
        'modal_heading' => 'Presunúť :displayName',
        'modal_submit_action_label' => [
            'root' => 'Presunúť do koreňového priečinka',
            'folder' => 'Presunúť do :displayName',
        ],
        'success_notification_title' => ':displayName bol úspešne presunutý',
        'failure_notification_title' => [
            'existing_target_folder_file' => 'Súbor s názvom :basename už existuje v :targetFolder',
            'root' => 'koreňový priečinok',
        ],
    ],
    'create_folder_bulk_action_bulk_action' => [
        'label' => 'Nový priečinok z výberu',
        'modal_heading' => 'Nový priečinok z výberu',
        'modal_submit_action_label' => '{0} Vytvoriť priečinok a presunúť 0 položiek|{1} Vytvoriť priečinok a presunúť 1 položku|[2,*] Vytvoriť priečinok a presunúť :count položiek',
        'success_notification_title' => 'Priečinok :folderName bol úspešne vytvorený s :count presunutými položkami',
        'schema' => [
            'name' => [
                'label' => 'Názov priečinka',
            ],
        ],
    ],
    'delete_action' => [
        'label' => 'Odstrániť',
        'modal_heading' => [
            'file' => 'Odstrániť súbor :displayName',
            'folder' => 'Odstrániť priečinok :displayName',
        ],
        'modal_description' => [
            'file' => 'Naozaj chcete odstrániť tento súbor?',
            'folder' => 'Súbory v priečinku nebudú odstránené, ale presunuté do aktuálneho priečinka. Naozaj chcete odstrániť tento priečinok?',
        ],
        'success_notification_title' => [
            'file' => 'Súbor :displayName bol úspešne odstránený',
            'folder' => [
                'delete_content' => 'Priečinok :displayName bol úspešne odstránený vrátane celého obsahu',
                'preserve_content' => 'Priečinok :displayName bol úspešne odstránený',
            ],
        ],
        'schema' => [
            'delete_content' => [
                'label' => 'Odstrániť celý obsah priečinka',
                'below_content' => [
                    'text' => [
                        'true' => 'Upozornenie: toto odstráni všetky súbory v priečinku vrátane podpriečinkov. Túto akciu nie je možné vrátiť späť.',
                    ],
                ],
            ],
        ],
    ],
    'download_action' => [
        'label' => 'Stiahnuť',
    ],
    'preview_action' => [
        'label' => 'Náhľad',
        'modal_cancel_action_label' => 'Zavrieť',
        'extra_modal_footer_actions' => [
            'preview' => [
                'label' => 'Otvoriť v novej karte',
            ],
        ],
    ],
    'view_action' => [
        'label' => 'Zobraziť',
        'modal_cancel_action_label' => 'Zavrieť',
    ],
    'rename_action' => [
        'label' => 'Premenovať',
        'modal_heading' => 'Premenovať :displayName',
        'modal_submit_action_label' => 'Uložiť názov',
        'success_notification_title' => ':displayName bol úspešne premenovaný',
        'schema' => [
            'name' => [
                'label' => 'Názov',
                'helper_text' => 'Aktuálny názov: :displayName',
            ],
        ],
    ],
    'replace_action' => [
        'label' => 'Nahradiť',
        'modal_heading' => 'Nahradiť :displayName',
        'modal_description' => 'Nahrajte nový súbor, ktorý nahradí existujúci. Odkazy na súbor zostanú nezmenené.',
        'modal_cancel_action_label' => 'Zrušiť',
        'modal_submit_action_label' => 'Nahradiť súbor',
        'success_notification_title' => ':displayName bol úspešne nahradený',
        'schema' => [
            'file' => [
                'label' => 'Náhradný súbor',
                'below_content' => [
                    'text' => 'Môžete nahrať súbor do veľkosti :maxFileSizeFormatted.',
                ],
                'validation_messages' => [
                    'required' => 'Prosím, vyberte alebo pretiahnite súbor na nahratie.',
                ],
            ],
        ],
    ],
    'select_file_action' => [
        'label' => [
            'singular' => 'Vybrať súbor',
            'plural' => 'Vybrať súbory',
        ],
        'modal_heading' => [
            'singular' => 'Vybrať súbor',
            'plural' => 'Vybrať súbory',
        ],
        'modal_description' => [
            'min_files_one_max_files_null' => 'Vyberte aspoň jeden súbor',
            'min_files_one_max_files_non_null' => 'Vyberte 1 až :maxFiles súborov',
            'min_files_plural_max_files_null' => 'Vyberte aspoň :minFiles súborov',
            'min_files_one_max_files_one' => null,
            'min_files_equals_max_files' => 'Vyberte presne :minFilesMaxFiles súborov',
        ],
        'modal_submit_action_label' => 'Vybrať',
        'modal_cancel_action_label' => 'Zrušiť',
        'success_notification_title' => '{1}1 súbor vybraný|[2,4]:count súbory vybrané|[5,*]:count súborov vybraných',
        'validation' => [
            'min_files' => '{1} Musíte vybrať aspoň :minFiles súbor|[2,*] Musíte vybrať aspoň :minFiles súborov',
            'max_files' => '{1} Nemôžete vybrať viac ako :maxFiles súbor|[2,*] Nemôžete vybrať viac ako :maxFiles súborov',
        ],
    ],
    'duplicate_action' => [
        'label' => 'Duplikovať',
        'modal_heading' => 'Duplikovať :displayName',
        'modal_submit_action_label' => 'Duplikovať',
        'success_notification_title' => ':displayName bol úspešne duplikovaný',
        'schema' => [
            'name' => [
                'label' => 'Nový názov súboru',
                'helper_text' => 'Pôvodný súbor: :displayName',
            ],
        ],
    ],
    'duplicate_bulk_action' => [
        'label' => 'Duplikovať',
        'success_notification_title' => '{0} Žiadne súbory neboli duplikované|{1} 1 súbor bol úspešne duplikovaný|[2,*] :count súborov bolo úspešne duplikovaných',
    ],
];
