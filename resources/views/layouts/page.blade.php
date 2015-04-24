@extends('template')

@section('header_image', '/img/page-images/'.$Page->image)

@section('head')
	<h1>{{ $Page->title }}</h1>
	<hr class="small">
	<span class="subheading">{{ $Page->subheading }}</span>
@stop

@section('content')
	{!! $Page->content !!}
@stop