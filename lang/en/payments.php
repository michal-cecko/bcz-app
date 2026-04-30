<?php

return [
    'bank_transfer' => [
        'details_title' => 'Payment details',
        'iban' => 'IBAN:',
        'variable_symbol' => 'Variable symbol:',
        'amount' => 'Amount:',
        'recipient' => 'Recipient:',
        'note' => 'Note:',
        'scan_qr' => 'Scan the QR code',
        'pay_by_square' => 'Pay by square',
        'qr_platba_sepa' => 'CZ účet',
        'instructions_title' => 'Important instructions',
        'instruction_use_vs' => 'Use the correct variable symbol so we can match your payment automatically.',
        'instruction_processing' => 'The transfer may take 1-2 business days depending on your bank.',
        'instruction_confirmation' => 'You will receive a confirmation email once we match the payment.',
        'instruction_membership_activation' => 'Membership will be activated once the payment clears (1–3 business days).',
        'instruction_registration_confirmation' => 'Registration will be confirmed once the payment clears (1–3 business days).',
        'amount_to_pay' => 'Amount due:',
    ],
    'cash' => [
        'instructions_title' => 'Cash payment',
        'amount_label' => 'Amount:',
        'instruction' => 'Hand the payment to the coach at your next training.',
    ],
    'method' => [
        'gopay' => 'Card (GoPay)',
        'gopay_subtitle' => 'Payment to the team account',
        'bank_transfer' => 'Bank transfer',
        'bank_transfer_subtitle' => 'Payment to the team account',
        'cash' => 'Cash',
        'cash_subtitle' => 'In cash',
        'select_label' => 'Payment method:',
    ],
    'gopay' => [
        'pay_button' => 'Pay :amount',
        'failed' => 'Payment failed. Please try again.',
        'success_title' => 'Payment processed successfully!',
        'success_body' => 'Your membership has been activated.',
    ],
    'open_payment_page' => 'Open payment',
    'bank_account_override' => [
        'helper_text' => 'If empty, the team default IBAN will be used: :default',
        'recipient_helper_text' => 'If empty, the team default recipient name will be used: :default',
    ],
];
