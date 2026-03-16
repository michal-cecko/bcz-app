@php
    $height = match($size ?? 'medium') {
        'small' => '10px',
        'medium' => '20px',
        'large' => '40px',
        default => '20px',
    };
@endphp
<div style="height: {{ $height }}; line-height: {{ $height }}; font-size: 1px;">&nbsp;</div>
