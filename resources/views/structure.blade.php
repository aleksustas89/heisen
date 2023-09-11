@extends('main')

@section('seo_title', $structure->seo_title)
@section('seo_description', $structure->seo_description)
@section('seo_keywords', $structure->seo_keywords)

@section('content')

{!! $structure->text !!}

@endsection