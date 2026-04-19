<?php

return [
    'generation' => [
        'action_label' => 'Vygenerovat battle',
        'modal_heading' => 'Vygenerovat battle',
        'modal_description_overwrite' => 'Přepíše :count existujících battle.',
        'modal_description_new' => 'Vytvoří battle pro toto kolo z předchozího kola.',
        'submit_label' => 'Vygenerovat',
        'pairing_strategy_label' => 'Strategie párování',
        'success_title' => 'Battle vygenerovány',
        'success_body' => 'Vytvořeno :count battle pro kolo „:round".',
        'failed_title' => 'Generování selhalo',
        'auto_success_title' => 'Battle vygenerovány',
        'auto_success_body' => 'Kolo „:round" automaticky dostalo :count battle.',
        'regenerate_success_body' => 'Kolo „:round" dostalo :count nových battle.',
        'regenerate_failed_title' => 'Regenerace selhala',
    ],
    'stale' => [
        'title' => 'Pořadí postupujících se změnilo',
        'body' => 'Battle v kole „:round" jsou založeny na starém pořadí. Doporučujeme je regenerovat.',
        'banner_title' => 'Battle jsou zastaralé',
        'banner_body_with_prev' => 'Pořadí postupujících z kola „:prev" se změnilo. Doporučujeme battle regenerovat, aby odrážely aktuální postupující.',
        'banner_body_generic' => 'Doporučujeme battle regenerovat, aby odrážely aktuální postupující.',
        'regenerate_action' => 'Regenerovat battle',
        'dismiss_action' => 'Ignorovat',
    ],
    'errors' => [
        'invalid_advancement_type' => 'Battle lze generovat pouze pro kola typu „Vítěz battlu".',
        'invalid_competitor_count' => 'Počet soutěžících (:count) musí být dělitelný dvojnásobkem velikosti týmu (:team_size × 2 = :slots).',
        'already_exists' => 'Kolo již má :count battle. Pro regeneraci použijte přepsání.',
        'insufficient_competitors' => 'Kolo potřebuje :need soutěžících, ale z předchozího kola je dostupných jen :have.',
        'missing_competitor_count' => 'Počet soutěžících není nastaven a nelze jej odvodit z předchozího kola.',
        'third_place_requires_battle_source' => 'Battle o 3. místo vyžaduje předchozí battle kolo.',
        'third_place_requires_two_sources' => 'Battle o 3. místo vyžaduje, aby předchozí kolo mělo alespoň 2 battle (semifinále).',
        'third_place_needs_complete_winners' => 'Nejprve je třeba dokončit všechny battle v předchozím kole (nastavit vítěze).',
        'third_place_unresolved_battle' => 'Battle #:bracket v předchozím kole nemá vítěze (remíza nebo chybějící hlasování). Přidejte další kolo nebo upravte bodování.',
    ],
];
