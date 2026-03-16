@php
    $bgColor = match($color ?? 'primary') {
        'primary' => '#2563eb',
        'success' => '#16a34a',
        'danger' => '#dc2626',
        'warning' => '#ea580c',
        'purple' => '#9333ea',
        'pink' => '#db2777',
        'indigo' => '#4f46e5',
        'teal' => '#0d9488',
        'gray' => '#4b5563',
        default => '#2563eb',
    };

    $align = $alignment ?? 'left';

    $resolvedUrl = \App\Services\LinkResolver::resolve([
        'link_type' => $button_link_type ?? null,
        'link_model_id' => $button_link_model_id ?? null,
        'link_url' => $button_link_url ?? null,
    ]) ?? '#';
@endphp
<table role="presentation" cellpadding="0" cellspacing="0" width="100%" style="margin: 16px 0;">
    <tr>
        <td align="{{ $align }}">
            <table role="presentation" cellpadding="0" cellspacing="0">
                <tr>
                    <td style="border-radius: 10px; background-color: {{ $bgColor }};">
                        <a href="{{ $resolvedUrl }}" target="_blank" style="display: inline-block; padding: 10px 28px; font-family: 'DM Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; font-size: 14px; font-weight: 600; color: #ffffff; text-decoration: none; border-radius: 10px;">{{ $text ?? 'Kliknite sem' }}</a>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
