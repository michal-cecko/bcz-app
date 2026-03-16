@php
    $borderColor = match($color ?? 'blue') {
        'blue' => '#2563eb',
        'green' => '#16a34a',
        'yellow' => '#eab308',
        'red' => '#dc2626',
        default => '#2563eb',
    };
    $bgColor = match($color ?? 'blue') {
        'blue' => '#eff6ff',
        'green' => '#f0fdf4',
        'yellow' => '#fefce8',
        'red' => '#fef2f2',
        default => '#eff6ff',
    };
@endphp
<table role="presentation" cellpadding="0" cellspacing="0" width="100%" style="margin: 16px 0;">
    <tr>
        <td style="border-left: 4px solid {{ $borderColor }}; background-color: {{ $bgColor }}; padding: 16px 20px; border-radius: 0 8px 8px 0;">
            <div style="font-family: 'DM Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; font-size: 15px; line-height: 1.5; color: #374151;">
                {!! $content ?? '' !!}
            </div>
        </td>
    </tr>
</table>
