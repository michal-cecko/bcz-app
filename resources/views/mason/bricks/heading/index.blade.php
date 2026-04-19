@php
 $tag = $level ?? 'h2';
 $classes = match($tag) {
 'h2' => 'text-3xl font-bold text-white font-display tracking-wide',
 'h3' => 'text-2xl font-bold text-white font-display tracking-wide',
 'h4' => 'text-xl font-semibold text-white font-display tracking-wide',
 default => 'text-3xl font-bold text-white font-display tracking-wide',
 };
@endphp

<{{ $tag }} class="{{ $classes }}">{{ brick_trans($text ?? []) }}</{{ $tag }}>
