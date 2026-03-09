@php
    $tag = $level ?? 'h2';
    $classes = match($tag) {
        'h2' => 'text-3xl font-bold',
        'h3' => 'text-2xl font-semibold',
        'h4' => 'text-xl font-semibold',
        default => 'text-3xl font-bold',
    };
@endphp

<{{ $tag }} class="{{ $classes }}">{{ brick_trans($text ?? []) }}</{{ $tag }}>
