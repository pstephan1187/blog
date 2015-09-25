<?php namespace App\Http\Middleware;

use Closure;

class HtmlMinifyMiddleware {

	public function handle($request, Closure $next){
		$content = $next($request);
		
		$raw_content = (string) $content->getOriginalContent();
		$content->setContent(\App\HtmlMinify::minify($raw_content));
		
		return $content;
	}

}
