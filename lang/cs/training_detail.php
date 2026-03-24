<?php

return [
    'breadcrumb_home' => 'DOMOV',
    'breadcrumb_trainings' => 'TRÉNINKY',

    'about_label' => 'O TRÉNINKU',
    'about_title' => "CO TĚ ČEKÁ\nNA TRÉNINKU?",

    'details_title' => 'DETAILY TRÉNINKU',
    'detail_category' => 'Kategorie',
    'detail_age_group' => 'Věková skupina',
    'detail_gender' => 'Pohlaví',
    'all_genders' => 'Obě pohlaví',
    'detail_day' => 'Den',
    'detail_time' => 'Čas',
    'detail_place' => 'Místo',
    'detail_city' => 'Město',
    'detail_price' => 'Cena',
    'pricing_free' => 'Zdarma',
    'pricing_membership' => 'Vyžaduje členství',

    'capacity_label' => 'Aktuální kapacita',
    'capacity_spots' => 'míst',
    'capacity_full' => 'Plný',
    'capacity_remaining' => 'Zbývá už jen :count volné místo!|Zbývají už jen :count volná místa!|Zbývá už jen :count volných míst!',

    'location_label' => 'LOKACE',
    'location_title' => 'KDE NÁS NAJDEŠ',
    'location_address' => 'Adresa',
    'location_meeting_title' => 'Místo setkání',
    'location_open_maps' => 'Otevřít v Google Maps',

    'coach_label' => 'VEDENÍ TRÉNINKU',
    'coach_title' => 'TVŮJ TRENÉR',

    'gallery_label' => 'Z TRÉNINKU',
    'gallery_title' => 'GALERIE',
    'gallery_subtitle' => 'Podívej se, jak tento trénink vypadá v akci',

    'form_label' => 'REGISTRACE',
    'form_title' => 'PŘIHLAS SE NA TRÉNINK',
    'form_subtitle' => 'Vyplň formulář a my se ti ozveme s potvrzením',
    'form_name' => 'Jméno',
    'form_surname' => 'Příjmení',
    'form_email' => 'Email',
    'form_email_placeholder' => 'tvuj@email.cz',
    'form_phone' => 'Telefon',
    'form_phone_placeholder' => '+420 XXX XXX XXX',
    'form_submit' => 'ODESLAT PŘIHLÁŠKU',
    'form_submitting' => 'ODESÍLÁM...',
    'form_consent' => 'Odesláním souhlasíš se zpracováním osobních údajů.',
    'form_success_title' => 'DĚKUJEME!',
    'form_success_message' => 'Tvoje přihláška byla úspěšně odeslána. Ozveme se ti s potvrzením.',

    // Registration states
    'registration_not_yet_open' => 'Registrace ještě nejsou otevřené',
    'registration_closed' => 'Registrace jsou uzavřené',
    'registration_opens_at' => 'Registrace se otevře :date',

    'already_registered_title' => 'Už jsi registrovaný',
    'already_registered_message' => 'Už jsi registrovaný na tento trénink.',

    'free_approved_message' => 'Tvoje registrace byla automaticky schválena. Těšíme se na tebe!',
    'membership_valid_message' => 'Tvoje členství je platné a registrace byla schválena. Těšíme se na tebe!',

    'membership_needed_title' => 'Vyžaduje se členství',
    'membership_needed_message' => 'Tento trénink vyžaduje aktivní členství v týmu. Pro pokračování vyber způsob platby.',

    'payment_needed_title' => 'Platba za trénink',
    'payment_needed_message' => 'Tvoje registrace byla přijata. Pro schválení je potřeba uhradit :price.',

    // State labels
    'state_registered' => 'STAV: ÚSPĚŠNĚ ZAREGISTROVÁN',
    'state_payment_success' => 'STAV: PLATBA ÚSPĚŠNÁ',
    'state_membership_needed' => 'STAV: CHYBÍ AKTIVNÍ ČLENSTVÍ',
    'state_payment_needed' => 'STAV: ČEKÁ SE NA PLATBU',

    'payment_success_title' => 'Platba přijata!',
    'payment_success_message' => 'Tvoje platba za trénink byla úspěšně zpracována. Registrace je potvrzena, uvidíme se na tréninku!',
    'payment_confirmation_email' => 'Potvrzení o platbě bylo odesláno na váš email.',

    'dr_training' => 'Trénink',
    'dr_date' => 'Datum',
    'dr_location' => 'Místo',
    'dr_amount' => 'Částka',
    'dr_payment_method' => 'Způsob platby',
    'dr_membership' => 'Členství',
    'membership_active' => 'Aktivní',
    'membership_not_required' => 'Nevyžaduje se',

    // Payment methods
    'payment_method_label' => 'Platební metoda',
    'payment_gopay' => 'Platba kartou',
    'payment_gopay_desc' => 'Okamžitá platba přes GoPay',
    'payment_bank_transfer' => 'Bankovní převod',
    'payment_bank_transfer_desc' => 'QR kód a IBAN po výběru',
    'payment_cash' => 'Hotovost',
    'payment_cash_desc' => 'Platba na místě nebo kontaktujte tým',
    'payment_cash_instructions' => 'Platbu v hotovosti je možné uhradit přímo na místě nebo kontaktujte tým pro více informací.',

    // Payment buttons
    'pay_button' => 'Zaplatit :price',
    'show_payment_details' => 'Zobrazit platební údaje',
    'contact_team' => 'Kontaktujte tým',
    'payment_auto_approve_note' => 'Po zaplacení budeš automaticky registrován na tento trénink.',

    // Season info
    'season_remaining' => 'za zbytek sezóny',
    'season_prorated_note' => 'Poměrná cena za zbývající měsíce aktuální sezóny',

    // Validation errors
    'error_email_exists' => 'Účet s touto emailovou adresou již existuje. <a href="/login" class="underline text-bcz-red hover:text-red-400">Přihlaste se</a>.',
    'error_phone_exists' => 'Telefonní číslo je již přiřazeno k jinému účtu.',

    'days' => [
        'monday' => 'Pondělí',
        'tuesday' => 'Úterý',
        'wednesday' => 'Středa',
        'thursday' => 'Čtvrtek',
        'friday' => 'Pátek',
        'saturday' => 'Sobota',
        'sunday' => 'Neděle',
    ],
];
