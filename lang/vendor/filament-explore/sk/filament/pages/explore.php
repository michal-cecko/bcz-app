<?php

return [
    'actions' => [
        'upload' => [
            'label' => 'Nahrať',
            'modal_heading' => 'Nahrať súbory',
            'modal_description' => 'Pretiahnite alebo kliknite pre nahratie nových položiek do knižnice.',
            'modal_cancel_action_label' => 'Zavrieť',
        ],
    ],
    'exploreForm' => [
        'components' => [
            'filters' => [
                'schema' => [
                    'search' => [
                        'label' => 'Hľadať',
                        'placeholder' => 'Hľadať súbory alebo priečinky podľa názvu',
                    ],
                    'tabs' => [
                        'label' => 'Filtre',
                    ],
                    'trigger' => [
                        'filters' => [
                            'label' => 'Filtrovať',
                        ],
                    ],
                    'sorters' => [
                        'label' => 'Zoradiť',
                    ],
                    'display_options' => [
                        'label' => 'Zobrazenie',
                        'actions' => [
                            'update_file_version_grid' => [
                                'label' => 'Mriežka',
                            ],
                            'update_file_version_row' => [
                                'label' => 'Zoznam',
                            ],
                            'toggle_file_is_file_extension_visible' => [
                                'label' => [
                                    'true' => 'Skryť prípony',
                                    'false' => 'Zobraziť prípony súborov',
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            'files' => [
                'empty_state' => [
                    'heading' => [
                        'not_found' => 'Žiadne súbory',
                        'not_found_with_modifications' => 'Nič nezodpovedá vášmu hľadaniu',
                        'unauthorized' => 'Nemáte oprávnenie zobraziť tento priečinok',
                    ],
                    'description' => [
                        'not_found' => 'Začnite nahraním vašej prvej položky.',
                        'not_found_with_modifications' => 'Skúste zmeniť filtre alebo hľadať iný výraz.',
                        'unauthorized' => 'Ak si myslíte, že je to chyba, kontaktujte administrátora.',
                    ],
                ],
            ],
            'selected_file' => [
                'empty_state_heading' => 'Zatiaľ nie je vybraný žiadny súbor',
                'schema' => [
                    'information' => [
                        'heading' => 'Informácie',
                        'schema' => [
                            'uploader' => [
                                'label' => 'Nahral',
                            ],
                            'created_at' => [
                                'label' => 'Nahrané',
                                'placeholder' => '-',
                            ],
                        ],
                    ],
                ],
            ],
        ],
    ],
];
