<?php

return [
    'reset_password' => [
        'subject' => 'Obnova hesla',
        'greeting' => 'Ahoj :name,',
        'intro' => 'Dostáváš tento e-mail, protože jsme přijali žádost o obnovu hesla pro tvůj účet.',
        'cta' => 'Obnovit heslo',
        'expire' => 'Odkaz na obnovu hesla je platný :count minut.',
        'fallback' => 'Pokud tlačítko nefunguje, zkopíruj tuto adresu do prohlížeče:',
        'ignore' => 'Pokud jsi o obnovu hesla nežádal/a, můžeš tento e-mail ignorovat.',
    ],

    'registration_confirmation' => [
        'subject' => 'Potvrzení registrace — :title',
        'heading' => 'Děkujeme za registraci',
        'greeting' => 'Dobrý den:name,',
        'body' => 'děkujeme za vaši registraci na :type: :title. Vaši přihlášku jsme úspěšně přijali.',
        'type' => [
            'training' => 'trénink',
            'event' => 'akci',
        ],
        'payment_heading' => 'Platba za registraci',
        'payment_body' => 'Pro dokončení registrace je třeba uhradit platbu:',
        'payment_amount_label' => 'Částka k úhradě',
        'payment_cta' => 'Zaplatit :amount',
        'payment_disclaimer' => 'Kliknutím na tlačítko budete přesměrováni na zabezpečenou platební stránku.',
        'new_user_heading' => 'Mimochodem — vytvořili jsme vám účet',
        'new_user_body' => 'Abyste měli přehled o svých registracích, platbách a aktivitách, vytvořili jsme vám účet. Po přihlášení si můžete doplnit profil a nastavit heslo.',
        'new_user_cta' => 'Přihlásit se',
        'new_user_link_validity' => 'Tento odkaz je platný 7 dní.',
        'signoff' => 'Děkujeme,',
        'signature' => 'BCZ Club',
    ],
];
