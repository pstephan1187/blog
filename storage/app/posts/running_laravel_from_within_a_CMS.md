>> How To Run Laravel In Parallel With Another CMS Or PHP Framework

Laravel is a great framework. It offers so many great tools out of the box and is written in such a way that it just makes programming fun. Unfortunately, there are cases in which we cannot use it, or have a project already built that has a budget that doesn't allow for a rebuild.

For the latter, there is hope. Now, I want to preface this by saying that I don't necessarily support this because it's bad practice and it leads to severe performance issues. However, in rare cases, it can be useful. I am going to show you how you can run Laravel in conjunction with another CMS or framework.

## Why Would I Do That?

The reasons someone might legitimately try to do this is numerous. In my case, I am trying to convert a large number of websites, each built on an individual instance of a CMS, to a centralized Laravel-based CMS. The transition will be slow, but if I could somehow get the sites to utilize Laravel now, I could build the new CMS under the current websites without much trouble.

## The Concept

Essentially, what we want to do is replicate the process that occurs when a request is made to Laravel's `public/index.php` file. The big difference being that we control when to send a request to Laravel. So let's see what happens.

If we look at the `index.php` file we will see a few things:

* Composer's autoloader file is included
* Laravel's app bootstrap file is included
* The HTTP kernel is instantiated
* The request is handled
* The app is killed

The app bootstrap file really only does two things. It

* Sets up the kernel, and it
* Sets up the Exception handler

The kernel's main job is running any bootstrap files and routing the request. Once that is done, we can do any needed clean up and end the app.

## Implementing The Basics

So how do we get this all done? Well, the basics are pretty simple, but you can add your own logic to the process to make fit your needs more.

Firstly we need to create a class that will encapsulate the Laravel app for us. You can store this wherever it will be easily accessible to your CMS/Framework (This does not need to be accessible by composer or Laravel itself):

```php
<?php

class Laravel {

}
```

We will want to include a constructor that will instantiate Laravel. Since the `app` object requires a base path, so will our class (or you could include it in a class constant or whatnot). We will also bootstrap the app:

```php
private $bootstrappers = [
	'Illuminate\Foundation\Bootstrap\DetectEnvironment',
	'Illuminate\Foundation\Bootstrap\LoadConfiguration',
	'Illuminate\Foundation\Bootstrap\ConfigureLogging',
	'Illuminate\Foundation\Bootstrap\HandleExceptions',
	'Illuminate\Foundation\Bootstrap\RegisterFacades',
	'Illuminate\Foundation\Bootstrap\RegisterProviders',
	'Illuminate\Foundation\Bootstrap\BootProviders',
];

public function __construct($base_path){
	require_once $base_path.'/bootstrap/autoload.php';
	$this->app     = new \Illuminate\Foundation\Application($base_path);
	$this->request = \Illuminate\Http\Request::capture();

	$this->app->singleton(
		'Illuminate\Contracts\Debug\ExceptionHandler',
		'Illuminate\Foundation\Exceptions\Handler'
	);
				
	$this->app->instance('request', $this->request);
	$this->app->bootstrapWith($this->bootstrappers);
}
```

At this point, we would have access to all autoloaded classes and anything attached to Laravel's service container. However, laravel cannot handle any routes yet and no middleware is being run. If that is all you need, you can skip ahead to "Hooking It All Up".

If you want to handle routes or need to run the middleware, then we need to boot up the kernel:

```php
public function handleRequest(){
	$this->kernel = $this->app->make('Torch\Http\Kernel');
	$this->request = Illuminate\Http\Request::capture();
	
	try{
		$this->response = $this->kernel->handle($this->request);
		$this->response->send();
		$this->kernel->terminate($this->request, $this->response);
		die();
	}catch(Exception $e){
		error_log('Request not captured by CMS/Framework or Laravel');
		dd($e);
	}
	
}
```

## Hooking It All Up

Here comes the magical part. Now we want to actually have our CSM/Framework hook into our Laravel app at will. Where you would do this depends completely on what CMS/Framework you are using and your use case. But no matter where it is done, if you want access to any of Laravel's features all you need to do is:

```php
$laravel = new Laravel('path/to/laravel/installation');
```

And now you can

```php
$cache = Cache::put(...);

//OR

$request = $laravel->make('request');

//OR

$base_path = base_path();

//OR

$user = User::find(1);
```

Typically you would only want Laravel to handle route requests when your CMS/Framework 404's. You can do something like this:

```php
//your CMS/Framework 404 event handler:
onPageNotFound(){
	try{
		$laravel->handleRequest();
	}catch(\Exception $e){
		return response('Page Not Found', 404);
	}
}
```

And that's it. I hope this is a help, but again, I must mention, this process should be avoided unless absolutely necessary.