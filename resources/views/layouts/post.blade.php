@extends('template')



@section('metahead')
<script>
	ga('send', 'event', 'Posts', 'Visit', '{{$Post->title}}');
</script>
@stop



@section('header_image', '/img/post-images/'.$Post->image)



@section('head')
    <h1>
	    {{$Post->title}}<br />
	    <small>{{ $Post->subheading }}</small>
	</h1>
    <span class="meta">Posted by Patrick Stephan on {!! date("F j, Y", $Post->last_modified) !!}</span>
@stop



@section('content')
	
	{!! $Post->content !!}
	
@stop