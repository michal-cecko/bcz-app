@extends('layouts.public')

@php
    $seoLocale = app()->getLocale();
    $pageOgImage = $page->getFirstMediaUrl('featured_image');
@endphp

@section('title', ($page->getTranslation('meta_title', $seoLocale) ?: $page->getTranslation('title', $seoLocale)) . ' | BCZ Club')
@section('meta_description', seo_description($page->getTranslation('meta_description', $seoLocale)))
@if ($pageOgImage)
    @section('og_image', $pageOgImage)
@endif
@section('og_type', $page->slug === '/' ? 'website' : 'article')

@section('content')
    {!! $renderedContent !!}
@endsection
