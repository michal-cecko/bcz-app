<?php

return [
    'generation' => [
        'action_label' => 'Vygenerovať battle',
        'modal_heading' => 'Vygenerovať battle',
        'modal_description_overwrite' => 'Prepíše :count existujúcich battle.',
        'modal_description_new' => 'Vytvorí battle pre toto kolo z predchádzajúceho kola.',
        'submit_label' => 'Vygenerovať',
        'pairing_strategy_label' => 'Stratégia párovania',
        'success_title' => 'Battle vygenerované',
        'success_body' => 'Vytvorených :count battle pre kolo „:round".',
        'failed_title' => 'Generácia zlyhala',
        'auto_success_title' => 'Battle vygenerované',
        'auto_success_body' => 'Kolo „:round" automaticky dostalo :count battle.',
        'regenerate_success_body' => 'Kolo „:round" dostalo :count nových battle.',
        'regenerate_failed_title' => 'Regenerácia zlyhala',
    ],
    'stale' => [
        'title' => 'Poradie postupujúcich sa zmenilo',
        'body' => 'Battle v kole „:round" sú založené na starom poradí. Odporúčame ich regenerovať.',
        'banner_title' => 'Battle sú zastarané',
        'banner_body_with_prev' => 'Poradie postupujúcich z kola „:prev" sa zmenilo. Odporúčame battle regenerovať, aby odrážali aktuálnych postupujúcich.',
        'banner_body_generic' => 'Odporúčame battle regenerovať, aby odrážali aktuálnych postupujúcich.',
        'regenerate_action' => 'Regenerovať battle',
        'dismiss_action' => 'Ignorovať',
    ],
    'errors' => [
        'invalid_advancement_type' => 'Battle sa dajú generovať len pre kolá typu „Víťaz battlu".',
        'invalid_competitor_count' => 'Počet súťažiacich (:count) musí byť deliteľný dvojnásobkom veľkosti tímu (:team_size × 2 = :slots).',
        'already_exists' => 'Kolo už má :count battle. Pre regeneráciu použite prepísanie.',
        'insufficient_competitors' => 'Kolo potrebuje :need súťažiacich, ale z predchádzajúceho kola je dostupných len :have.',
        'missing_competitor_count' => 'Počet súťažiacich nie je nastavený a nedá sa odvodiť z predchádzajúceho kola.',
        'third_place_requires_battle_source' => 'Battle o 3. miesto vyžaduje predchádzajúce battle kolo.',
        'third_place_requires_two_sources' => 'Battle o 3. miesto vyžaduje, aby predchádzajúce kolo malo aspoň 2 battle (semifinále).',
        'third_place_needs_complete_winners' => 'Najprv treba dokončiť všetky battle v predchádzajúcom kole (nastaviť víťazov).',
        'third_place_unresolved_battle' => 'Battle #:bracket v predchádzajúcom kole nemá víťaza (remíza alebo chýbajúce hlasovanie). Pridajte ďalšie kolo alebo upravte bodovanie.',
    ],
];
