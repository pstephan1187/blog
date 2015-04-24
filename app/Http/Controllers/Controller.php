<?php namespace App\Http\Controllers;

use App\Post;
use App\Page;
use Laravel\Lumen\Routing\Controller as BaseController;

class Controller extends BaseController {
    
    public function index(){
	    return view('layouts/home');
    }
    
    public function post($slug){
	    $Post = Post::find($slug);
	    return view('layouts/post')->with([
	    	'page_type' => 'post',
	    	'Post' => $Post,
	    	'page_title' => $Post->title
	    ]);
    }
    
    public function page($slug){
	    $Page = Page::find($slug);
	    return view('layouts/page')->with([
	    	'Page' => $Page,
	    	'page_title' => $Page->title
	    ]);
    }
    
}
