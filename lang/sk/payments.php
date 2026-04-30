<?php

return [
    'bank_transfer' => [
        'details_title' => 'Údaje pre platbu',
        'iban' => 'IBAN:',
        'variable_symbol' => 'Variabilný symbol:',
        'amount' => 'Suma:',
        'recipient' => 'Príjemca:',
        'note' => 'Poznámka:',
        'scan_qr' => 'Naskenuj QR kód',
        'pay_by_square' => 'Pay by Square',
        'qr_platba_sepa' => 'QR Platba / SEPA',
        'instructions_title' => 'Dôležité pokyny',
        'instruction_use_vs' => 'Použite správny variabilný symbol pre automatické priradenie platby.',
        'instruction_processing' => 'Platba môže trvať 1-2 pracovné dni v závislosti od vašej banky.',
        'instruction_confirmation' => 'Po priradení platby dostanete potvrdenie na email.',
        'instruction_membership_activation' => 'Členstvo bude aktivované po pripísaní (1–3 prac. dni).',
        'instruction_registration_confirmation' => 'Registrácia bude potvrdená po pripísaní platby (1–3 prac. dni).',
        'amount_to_pay' => 'Suma k úhrade:',
    ],
    'cash' => [
        'instructions_title' => 'Platba v hotovosti',
        'amount_label' => 'Suma:',
        'instruction' => 'Platbu odovzdajte trénerovi na najbližšom tréningu.',
    ],
    'method' => [
        'gopay' => 'Kartou (GoPay)',
        'gopay_subtitle' => 'Platba na účet tímu',
        'bank_transfer' => 'Bankový prevod',
        'bank_transfer_subtitle' => 'Platba na účet tímu',
        'cash' => 'Hotovosť',
        'cash_subtitle' => 'V hotovosti',
        'select_label' => 'Spôsob platby:',
    ],
    'gopay' => [
        'pay_button' => 'Zaplatiť :amount',
        'failed' => 'Platba sa nepodarila. Skúste to znova.',
        'success_title' => 'Platba bola úspešne spracovaná!',
        'success_body' => 'Vaše členstvo bolo aktivované.',
    ],
    'open_payment_page' => 'Otvoriť platbu',
    'bank_account_override' => [
        'helper_text' => 'Ak prázdne, použije sa predvolený IBAN tímu: :default',
        'recipient_helper_text' => 'Ak prázdne, použije sa predvolený názov príjemcu tímu: :default',
    ],
];
