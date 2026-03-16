@php
    $tag = $level ?? 'h2';
    $styles = match($tag) {
        'h1' => 'font-size: 28px; font-weight: 700; margin: 0 0 16px;',
        'h2' => 'font-size: 24px; font-weight: 700; margin: 0 0 12px;',
        'h3' => 'font-size: 20px; font-weight: 600; margin: 0 0 10px;',
        default => 'font-size: 24px; font-weight: 700; margin: 0 0 12px;',
    };
@endphp
<{{ $tag }} style="font-family: 'DM Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; {{ $styles }} color: #1A1A1A; text-align: {{ $alignment ?? 'left' }};">{{ $text ?? '' }}</{{ $tag }}>
