<?php

return [
    'generation' => [
        'action_label' => 'Generate battles',
        'modal_heading' => 'Generate battles',
        'modal_description_overwrite' => 'Will overwrite :count existing battles.',
        'modal_description_new' => 'Will create battles for this round from the previous round.',
        'submit_label' => 'Generate',
        'pairing_strategy_label' => 'Pairing strategy',
        'success_title' => 'Battles generated',
        'success_body' => 'Created :count battles for round ":round".',
        'failed_title' => 'Generation failed',
        'auto_success_title' => 'Battles generated',
        'auto_success_body' => 'Round ":round" automatically received :count battles.',
        'regenerate_success_body' => 'Round ":round" got :count fresh battles.',
        'regenerate_failed_title' => 'Regeneration failed',
    ],
    'stale' => [
        'title' => 'Advancing competitors changed',
        'body' => 'Battles in round ":round" are based on outdated ranking. Regeneration is recommended.',
        'banner_title' => 'Battles are outdated',
        'banner_body_with_prev' => 'Advancing competitors from round ":prev" have changed. Regenerate the battles to reflect the current advancers.',
        'banner_body_generic' => 'Regenerate the battles to reflect the current advancers.',
        'regenerate_action' => 'Regenerate battles',
        'dismiss_action' => 'Dismiss',
    ],
    'errors' => [
        'invalid_advancement_type' => 'Battles can only be generated for rounds of type "Battle winner".',
        'invalid_competitor_count' => 'Competitor count (:count) must be divisible by team size × 2 (:team_size × 2 = :slots).',
        'already_exists' => 'Round already has :count battles. Use overwrite to regenerate.',
        'insufficient_competitors' => 'Round needs :need competitors, but only :have are available from the previous round.',
        'missing_competitor_count' => 'Competitor count is not set and cannot be derived from the previous round.',
        'third_place_requires_battle_source' => '3rd place battle requires a previous battle round.',
        'third_place_requires_two_sources' => '3rd place battle requires the previous round to have at least 2 battles (a semifinal).',
        'third_place_needs_complete_winners' => 'Finish all battles in the previous round (set winners) before generating.',
        'third_place_unresolved_battle' => 'Battle #:bracket in the previous round has no winner (tie or missing votes). Add another cycle or adjust the scoring.',
    ],
];
