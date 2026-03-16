@php
    $imageUrl = $image ?? '';
    if ($imageUrl && !str_starts_with($imageUrl, 'http')) {
        $imageUrl = asset('storage/' . $imageUrl);
    }

    $resolvedLink = \App\Services\LinkResolver::resolve([
        'link_type' => $image_link_type ?? null,
        'link_model_id' => $image_link_model_id ?? null,
        'link_url' => $image_link_url ?? null,
    ]);
@endphp
@if($imageUrl)
    <table role="presentation" cellpadding="0" cellspacing="0" width="100%" style="margin: 16px 0;">
        <tr>
            <td align="center">
                @if($resolvedLink)
                    <a href="{{ $resolvedLink }}" target="_blank">
                @endif
                <img src="{{ $imageUrl }}" alt="{{ $alt ?? '' }}" style="max-width: 100%; height: auto; border-radius: 8px; display: block;" />
                @if($resolvedLink)
                    </a>
                @endif
            </td>
        </tr>
    </table>
@endif
