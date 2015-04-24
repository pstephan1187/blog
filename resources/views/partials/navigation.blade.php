<nav class="navbar navbar-default navbar-custom navbar-fixed-top">
    <div class="container-fluid">
        
        <div class="navbar-header page-scroll">
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="/">Patrick Stephan</a>
        </div>

        
        <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
            <ul class="nav navbar-nav navbar-right">
                <li>
                    <a href="/">Home</a>
                </li>
                @foreach(App\Page::all() as $Page)
	                <li>
	                    <a href="/{!! $Page->slug.'.html' !!}">{{ $Page->title }}</a>
	                </li>
                @endforeach
            </ul>
        </div>
        
    </div>
    
</nav>
