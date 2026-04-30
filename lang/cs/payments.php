<?php

return [
    'bank_transfer' => [
        'details_title' => 'Údaje pro platbu',
        'iban' => 'IBAN:',
        'variable_symbol' => 'Variabilní symbol:',
        'amount' => 'Částka:',
        'recipient' => 'Příjemce:',
        'note' => 'Poznámka:',
        'scan_qr' => 'Naskenuj QR kód',
        'pay_by_square' => 'Pay by square',
        'qr_platba_sepa' => 'CZ účet',
        'instructions_title' => 'Důležité pokyny',
        'instruction_use_vs' => 'Použijte správný variabilní symbol pro automatické přiřazení platby.',
        'instruction_processing' => 'Platba může trvat 1-2 pracovní dny v závislosti na vaší bance.',
        'instruction_confirmation' => 'Po přiřazení platby dostanete potvrzení na email.',
        'instruction_membership_activation' => 'Členství bude aktivováno po připsání (1–3 prac. dny).',
        'instruction_registration_confirmation' => 'Registrace bude potvrzena po připsání platby (1–3 prac. dny).',
        'amount_to_pay' => 'Částka k úhradě:',
    ],
    'cash' => [
        'instructions_title' => 'Platba v hotovosti',
        'amount_label' => 'Částka:',
        'instruction' => 'Platbu předejte trenérovi na nejbližším tréninku.',
    ],
    'method' => [
        'gopay' => 'Kartou (GoPay)',
        'gopay_subtitle' => 'Platba na účet týmu',
        'bank_transfer' => 'Bankovní převod',
        'bank_transfer_subtitle' => 'Platba na účet týmu',
        'cash' => 'Hotovost',
        'cash_subtitle' => 'V hotovosti',
        'select_label' => 'Způsob platby:',
    ],
    'gopay' => [
        'pay_button' => 'Zaplatit :amount',
        'failed' => 'Platba se nezdařila. Zkuste to znovu.',
        'success_title' => 'Platba byla úspěšně zpracována!',
        'success_body' => 'Vaše členství bylo aktivováno.',
    ],
    'open_payment_page' => 'Otevřít platbu',
    'bank_account_override' => [
        'helper_text' => 'Pokud je prázdné, použije se výchozí IBAN týmu: :default',
        'recipient_helper_text' => 'Pokud je prázdné, použije se výchozí název příjemce týmu: :default',
    ],
];
