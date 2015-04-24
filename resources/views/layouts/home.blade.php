@extends('template')

@section('header_image', 'img/home-bg.jpg')

@section('head')
	<h1>Patrick Stephan</h1>
    <hr class="small">
    <span class="subheading">Notes and Surmisings on Web Development</span>
@stop

@section('content')
	
	@foreach(App\Post::all() as $Post)
		
	    <div class="post-preview">
	        <a href="post/{!! $Post->slug.'.html' !!}">
	            <h2 class="post-title">
	                {!! $Post->title !!}
	            </h2>
	            <h3 class="post-subtitle">
	                {!! $Post->subheading !!}
	            </h3>
	        </a>
	        <p class="post-meta">Posted by Patrick Stephan on {!! date("F j, Y", $Post->last_modified) !!}</p>
	    </div>
	    <hr>
	@endforeach
	
	
    <!-- Pager -->
<!--
    <ul class="pager">
        <li class="next">
            <a href="#">Older Posts &rarr;</a>
        </li>
    </ul>
-->
@stop