@php
    $schemaLocale = app()->getLocale();
    $schemaImage = $event->getFirstMediaUrl('detail_image') ?: $event->getFirstMediaUrl('card_image') ?: asset('images/og-default.png');
    $eventSchema = array_filter([
        '@context' => 'https://schema.org',
        '@type' => 'Event',
        'name' => $event->getTranslation('title', $schemaLocale),
        'description' => seo_description($event->getTranslation('card_description', $schemaLocale)),
        'startDate' => $event->date?->toIso8601String(),
        'endDate' => $event->date_end?->toIso8601String(),
        'image' => $schemaImage,
        'eventStatus' => 'https://schema.org/EventScheduled',
        'eventAttendanceMode' => 'https://schema.org/OfflineEventAttendanceMode',
        'location' => $event->place_name ? array_filter([
            '@type' => 'Place',
            'name' => $event->place_name,
            'address' => $event->place_address,
        ]) : null,
        'url' => url()->current(),
    ], fn ($value) => $value !== null && $value !== '');
@endphp
@push('schema')
    <script type="application/ld+json">
        @json($eventSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)
    </script>
@endpush
