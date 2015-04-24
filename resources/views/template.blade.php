<!DOCTYPE html>
<html lang="en">

@include('partials/header')

<body class="{{$page_type or 'site'}}">

	@include('partials/navigation')
	
	@if(!isset($page_type) || $page_type !== 'post')
	    <header class="intro-header" style="background-image: url('@yield('header_image')')">
	        <div class="container">
	            <div class="row">
	                <div class="col-lg-8 col-lg-offset-2 col-md-10 col-md-offset-1">
	                    <div class="{{$page_type or 'site'}}-heading">
		                    @yield('head')
	                    </div>
	                </div>
	            </div>
	        </div>
	    </header>
	@else
        <div class="container">
            <div class="row">
                <div class="col-lg-8 col-lg-offset-2 col-md-10 col-md-offset-1">
                    <div class="{{$page_type or 'site'}}-heading">
	                    @yield('head')
                    </div>
                </div>
            </div>
        </div>
    @endif


    <div class="container">
        <div class="row">
            <div class="col-lg-8 col-lg-offset-2 col-md-10 col-md-offset-1" id="content">
	            @yield('content')
            </div>
        </div>
    </div>

	@include('partials/footer')
</body>

</html>
