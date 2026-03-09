@extends('layouts.public')

@section('title', $page->getTranslation('meta_title', app()->getLocale()) ?: $page->getTranslation('title', app()->getLocale()))

@section('content')
    {!! $renderedContent !!}
@endsection
