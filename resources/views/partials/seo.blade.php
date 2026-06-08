@php
    $seoSiteName = __('seo.site_name');
    $seoRawTitle = trim($__env->yieldContent('title', $seoSiteName));
    $seoTitle = $seoRawTitle !== '' ? $seoRawTitle : $seoSiteName;
    $seoDescription = trim($__env->yieldContent('meta_description'));
    $seoDescription = $seoDescription !== '' ? $seoDescription : __('seo.default_description');
    $seoImage = trim($__env->yieldContent('og_image'));
    $seoImage = $seoImage !== '' ? $seoImage : seo_default_og_image();
    $seoType = trim($__env->yieldContent('og_type')) ?: 'website';
    $seoUrl = url()->current();
    $seoLocale = app()->getLocale();
    $seoAlternateLocales = array_diff(['sk', 'en', 'cs'], [$seoLocale]);
    $seoOrganization = [
        '@context' => 'https://schema.org',
        '@type' => 'Organization',
        'name' => $seoSiteName,
        'url' => url('/'),
        'logo' => asset('images/bcz-logo.png'),
        'description' => __('seo.default_description'),
    ];
@endphp
<meta name="robots" content="@yield('robots', 'index, follow')">
<meta name="description" content="{{ $seoDescription }}">

{{-- Open Graph --}}
<meta property="og:site_name" content="{{ $seoSiteName }}">
<meta property="og:type" content="{{ $seoType }}">
<meta property="og:title" content="{{ $seoTitle }}">
<meta property="og:description" content="{{ $seoDescription }}">
<meta property="og:url" content="{{ $seoUrl }}">
<meta property="og:image" content="{{ $seoImage }}">
<meta property="og:image:alt" content="{{ $seoTitle }}">
<meta property="og:locale" content="{{ seo_og_locale($seoLocale) }}">
@foreach ($seoAlternateLocales as $altLocale)
    <meta property="og:locale:alternate" content="{{ seo_og_locale($altLocale) }}">
@endforeach

{{-- Twitter Card --}}
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="{{ $seoTitle }}">
<meta name="twitter:description" content="{{ $seoDescription }}">
<meta name="twitter:image" content="{{ $seoImage }}">

{{-- Organization structured data --}}
<script type="application/ld+json">
    @json($seoOrganization, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)
</script>

@stack('schema')
