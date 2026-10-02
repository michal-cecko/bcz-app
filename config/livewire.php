<?php

/*
 * Only the keys we change. Livewire merges this file over its own config one level deep,
 * so "payload" must list all of its keys.
 */
return [
    'payload' => [
        'max_size' => 1024 * 1024,
        // Rich editor state in a repeater (registration_form_schema helper texts) is TipTap JSON:
        // a link inside a list item is already 17 levels deep. Livewire's default is 10.
        'max_nesting_depth' => 30,
        'max_calls' => 50,
        'max_components' => 200,
    ],
];
