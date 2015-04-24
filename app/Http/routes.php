<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

$app->get('/', 'App\Http\Controllers\Controller@index');
$app->get('about', 'App\Http\Controllers\Controller@about');
$app->get('post/{post}.html', 'App\Http\Controllers\Controller@post');
$app->get('images/{dimensions}/{src}', 'App\Http\Controllers\AssetController@image');

$app->get('/{page}.html', 'App\Http\Controllers\Controller@page');