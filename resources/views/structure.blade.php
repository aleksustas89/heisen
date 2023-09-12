@extends('main')

@section('seo_title', $structure->seo_title ?? $structure->name)
@section('seo_description', $structure->seo_description)
@section('seo_keywords', $structure->seo_keywords)

@section('content')

<div class="static-page">
    {!! $structure->text !!}
</div>

@endsection