<?php

return [
    'reset_password' => [
        'subject' => 'Obnova hesla',
        'greeting' => 'Ahoj :name,',
        'intro' => 'Dostávaš tento e-mail, lebo sme prijali žiadosť o obnovu hesla pre tvoj účet.',
        'cta' => 'Obnoviť heslo',
        'expire' => 'Odkaz na obnovu hesla je platný :count minút.',
        'fallback' => 'Ak tlačidlo nefunguje, skopíruj túto adresu do prehliadača:',
        'ignore' => 'Ak si o obnovu hesla nežiadal/a, môžeš tento e-mail ignorovať.',
    ],

    'registration_confirmation' => [
        'subject' => 'Potvrdenie registrácie — :title',
        'heading' => 'Ďakujeme za registráciu',
        'greeting' => 'Dobrý deň:name,',
        'body' => 'ďakujeme za vašu registráciu na :type: :title. Vašu prihlášku sme úspešne prijali.',
        'type' => [
            'training' => 'tréning',
            'event' => 'podujatie',
        ],
        'payment_heading' => 'Platba za registráciu',
        'payment_body' => 'Pre dokončenie registrácie je potrebné uhradiť platbu:',
        'payment_amount_label' => 'Suma na úhradu',
        'payment_cta' => 'Zaplatiť :amount',
        'payment_disclaimer' => 'Kliknutím na tlačidlo budete presmerovaný na zabezpečenú platobnú stránku.',
        'new_user_heading' => 'Mimochodom — vytvorili sme vám účet',
        'new_user_body' => 'Aby ste mali prehľad o vašich registráciách, platbách a aktivitách, vytvorili sme vám účet. Po prihlásení si môžete doplniť profil a nastaviť heslo.',
        'new_user_cta' => 'Prihlásiť sa',
        'new_user_link_validity' => 'Tento odkaz je platný 7 dní.',
        'signoff' => 'Ďakujeme,',
        'signature' => 'BCZ Club',
    ],
];
