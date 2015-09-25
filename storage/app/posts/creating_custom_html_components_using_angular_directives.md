>> Creating custom HTML components

<a href="https://angularjs.org/" target="_blank">AngularJS</a> is a powerful Javascript framework that includes features like two-way data binding, templating, and form validation. But one thing that AngularJS allows you to do that can really help in the development of UIs is a feature called *directives*. Directives allow you to create custom HTML components like `<confirm>Isn't this cool?</confirm>` or `<tooltip>Check it!</tooltip>`. Directives can be attributes, classes or elements/tags. Using elements allows us to create modular UI components for our web apps and that is what we will be covering today.

## Setup
Getting started we will need to have a basic HTML document setup. I have pulled in the Twitter Bootstrap CSS and the AngularJS Frameworks via CDN for simplicity:

```html
<!DOCTYPE html>
<html ng-app="app">
<head>
	<title>Directives Test</title>

	<link rel="stylesheet" type="text/css" href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css">
</head>
<body>
	

	<script src="https://cdnjs.cloudflare.com/ajax/libs/angular.js/1.3.15/angular.min.js"></script>
</body>
</html>
```

We will need to initialize AngularJS by adding the `ng-app` attribute to the `<html>` element:

```html
<html ng-app="app">
```

We will also need to setup our Angular app:

```html
<script>
	var App = angular.module('app', []);
</script>
```

## Writing A Simple Directive

We will start off with something very simple: a static alert box. We can do this pretty easily by setting up a very simple directive. To declare a directive, we use the `directive` method on our Angular module. The first argument should be the name of our directive. The second argument should be a function that returns an object. This object can have a number of different properties, but the one we are concerned about at this moment is the `template` property. This is how we would declare our super simple directive:

```javascript
App.directive('alert', function(){
	return {
		template: '<div class="alert alert-info">Yo! This is an alert</div>'
	};
});
```

And adding the element to our page:

```html
<alert />
```

We should be able to see our basic alert on our page now. Our HTML document should look something like this so far:

```html
<!DOCTYPE html>
<html ng-app="app">
<head>
	<title>Directives Test</title>

	<link rel="stylesheet" type="text/css" href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css">
</head>
<body>
	
	<alert />

	<script src="https://cdnjs.cloudflare.com/ajax/libs/angular.js/1.3.15/angular.min.js"></script>
	<script>
		var App = angular.module('app', []);
		App.directive('alert', function(){
			return {
				template: '<div class="alert alert-info">Yo! This is an alert</div>'
			};
		});
	</script>
</body>
</html>
```

## Custom Content

This is great and all, but this isn't really gonna do us a lot of good. We need to be able to control the text in the alert, we need to be able to control the type of the alert (success, danger, info, etc), we need to be able to make it closable, we need to... Woah there! One step at a time. The ability to create great things comes from learning simple things one step at a time.

Ok, so to be able to determine the alert text, we want to be able to do something like this, right?

```html
<alert>Hey! I'm an alert!</alert>
```

To do that we will need to use the **transclude** property of the directive and tell the template where our content should show up. We will add `transclude: true` to our directive object and then add a 'nb-transclude' property to the element we want to hold our content. The element that will hold our content will need to be empty, so we will need to remove the 'Yo! This is an alert' from our alert. We should have something like this:

```html
<alert>Hey! I'm an alert!</alert>

<script src="https://cdnjs.cloudflare.com/ajax/libs/angular.js/1.3.15/angular.min.js"></script>
<script>
	var App = angular.module('app', []);
	App.directive('alert', function(){
		return {
			transclude: true,
			template: '<div class="alert alert-info" ng-transclude></div>'
		};
	});
</script>
```

Pretty cool, right? Well, we are just getting started. We still need to control the context of the alert.

## Adding Attributes

We are going to need some additional functionality added to our `<alert>` component to make it really useful. We don't want to have to create `<alert-info>`, `<alert-success>`, etc components. We just want to create one and control it's type. We can do that using attributes. We will need to map those attributes to the correct spot within our component and we can do that using the directive's **link** property. According to the documentation:

> Directives that want to modify the DOM typically use the link option. link takes a function with the following signature, function link(scope, element, attrs) { ... } where:
> 
> * scope is an Angular scope object.
> * element is the jqLite-wrapped element that this directive matches.
> * attrs is a hash object with key-value pairs of normalized attribute names and their corresponding attribute values.

With that in mind, we want our alert to look something like this:

```html
<alert type="success">Hey! I'm an alert!</alert>
```

To do that, we will want to map the `type` attribute to the class of our component. We sill need something like this:

```javascript
App.directive('alert', function(){
	return {
		transclude: true,
		template: '<div class="alert alert-{{type}}" ng-transclude></div>',
		link: function($scope, element, attributes){
			$scope.type = attributes.type;
		}
	};
});
```

Great, It works! The only problem is that if we leave out the `type` attribute, the alert breaks. We don't want that, so let's include a sensible default:

```javascript
$scope.type = attributes.type || 'info';
```

Beautiful! Let's test it out by adding several different types of alerts to our page just to check out the coolness:

```html
<alert>Hey! I'm a normal alert!</alert>
<alert type="success">Yay! I'm bring glad tidings!</alert>
<alert type="danger">Uh oh! Something's wrong :(</alert>
```

Woah! It broke! What happened? Well, all three alert boxes are using the same scope, so they are confused about which one gets what class. To fix this, we need to isolate the scope. This is very simple. All we need to do is add a `scope` property to our directive with an empty object. This tells the directive to use it's own scope instead of the global scope:

```javascript
App.directive('alert', function(){
	return {
		transclude: true,
		scope: {},
		template: '<div class="alert alert-{{type}}" ng-transclude></div>',
		link: function($scope, element, attributes){
			$scope.type = attributes.type || 'info';
		}
	};
});
```

Looking good. Many times, we may want our alert to be able to be closed. We don't always want all of our alerts to show all the time. How can we do that? Well, we can use attributes again, but we want to use a special type of attribute called a **flag**.

## Flag Attributes

A flag is an attribute that doesn't have a value, it's just on or off, true or false. the `ng-transclude` attribute is a flag. It doesn't have a value, it just tells you "yeah, this element has this attribute". How can we do that?

In our link function, if we check the `typeof` our attributes, they are `string`s, but if an attribute isn't there, it's `undefined`. So we can do something like this:

```javascript
$scope.closable = typeof attributes.closable === 'undefined' ? false : true;
```

We can shorten that down to:

```javascript
$scope.closable = !(typeof attributes.closable === 'undefined');
```

Then we can update our template to include a close button:

```javascript
template: '<div class="alert alert-{{type}}" ng-transclude><button ng-if="closable" type="button" class="close"><span>&times;</span></button></div>',
```

Now the only problem here, is that the button is inside the `ng-transclude` element, and that just won't work. So we will need to move the `ng-transclude` to a span tag inside the alert box:

```javascript
template: '<div class="alert alert-{{type}}"><span ng-transclude></span><button ng-if="closable" type="button" class="close"><span>&times;</span></button></div>',
```

And let's add an alert to make sure it works:

```html
<alert closable>Hey! I'm a closable alert!</alert>
```

Well, the button is there but it's not doing anything. Let's fix that...

## Directives and DOM Manipulation

Remember the documentation for the **link** property of the directive? It stated that the

> element is the jqLite-wrapped element that this directive matches.

Angular includes and lite version of jQuery that they have dubbed **jqLite**. If jQuery is included on your site, then Angular will implement the full version of jQuery instead of their lite version. You can read more about their implementation <a href="https://docs.angularjs.org/api/ng/function/angular.element" target="_blank">here</a>.

That being said, we will need to add an onclick handler to that button and have that handler remove the element from the page. Pretty simple. We need to create the handler inside the link function first:

```javascript
$scope.close = function(){
	element.remove();
}
```

And add the `ng-click="close()"` event to the button:

```javascript
template: '<div class="alert alert-{{type}}"><span ng-transclude></span><button ng-if="closable" ng-click="close()" type="button" class="close"><span>&times;</span></button></div>',
```

## Bonus: Two-Way Data Binding

By default, if you were to try to control the alert's `type` via two-way binding, it won't work. By default, directives won't listen for updates. Luckily, Angular gives us an easy way to listen for changes, thereby allowing two-way binding.

First, we need to add an input so that we can test:

```html
<alert type="{{alert_type}}">Check it! You can control my context!</alert>

<input ng-model="alert_type">
```

Obviously, this doesn't work, so how do we make it work? Angular provides an `$observe` method on the `attributes` argument in the `link` method. Inside our `link` method, we can implement `attributes.$observe()`. The first argument is the property in the `attributes` object to watch and the second argument is a function that processes the value of that property. It takes one argument which is the value of the property that we are observing.

To get the `type` attribute of our `alert` componenent to update like we want, we will need to add something like this to our link method:

```javascript
attributes.$observe('type', function(val){
	$scope.type = val;
});
```

That should update the `type` attribute as expected.

## Conclusion

AngularJS offers some very powerful features and directives do not disappoint. Being able to define our own HTML components and use them throughout our app make them a great way to produce clean, modular code that is easy to read and update.

Here is the full source code for the project we worked on:

```html
<!DOCTYPE html>
<html ng-app="app">
<head>
	<title>Directives Test</title>

	<link rel="stylesheet" type="text/css" href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css">
</head>
<body>
	
	<alert>Hey! I'm a normal alert!</alert>
	<alert type="success">Yay! I'm bring glad tidings!</alert>
	<alert type="danger">Uh oh! Something's wrong :(</alert>
	<alert closable>Hey! I'm a closable alert!</alert>
	<alert type="{{alert_type}}">Check it! You can control my context!</alert>

	<input ng-model="alert_type">

	<script src="https://cdnjs.cloudflare.com/ajax/libs/angular.js/1.3.15/angular.min.js"></script>
	<script>
		var App = angular.module('app', []);
		App.directive('alert', function(){
			return {
				transclude: true,
				scope: {},
				template: '<div class="alert alert-{{type}}"><span ng-transclude></span><button ng-if="closable" ng-click="close()" type="button" class="close"><span>&times;</span></button></div>',
				link: function($scope, element, attributes){
					$scope.type = attributes.type || 'info';
					$scope.closable = !(typeof attributes.closable === 'undefined');
					$scope.close = function(){
						element.remove();
					}
					attributes.$observe('type', function(val){
						$scope.type = val;
					});
				}
			};
		});
	</script>
</body>
</html>
```