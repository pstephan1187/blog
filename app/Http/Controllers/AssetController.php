<?php namespace App\Http\Controllers;

use Cache;
use Laravel\Lumen\Routing\Controller as BaseController;
use Intervention\Image\ImageManagerStatic as Image;

class AssetController extends BaseController {
    
    public function image($dimensions, $src){
	    
	    //if no 'x' is found in $dimensions (ie. 400) then image is square
	    $w = $dimensions;
	    $h = $dimensions;
	    
	    
	    //if 'x' is found (ie. 200x400), get the dimensions
	    if(strpos($dimensions, 'x') !== false){
		    $w = substr($dimensions, 0, strpos($dimensions, 'x'));
		    $h = substr($dimensions, strpos($dimensions, 'x') + 1);
	    }
	    
	    
	    //if fill == true, image will cover dimensions, otherwise, it will fit inside
		$fill		 = isset($_GET['fill']) && $_GET['fill'] == 'true' ? true : false;
		$clear_cache = isset($_GET['clear_cache']) && $_GET['clear_cache'] == 'true' ? true : false;
		
		
		//return the image
		try{
			Image::configure(array('driver' => 'gd'));
			$cache_key = $src.'_'.$w.'_'.$h.'_'.($fill ? 'true' : 'false');
			
			if($clear_cache){
				Cache::forget($cache_key);
			}
			
			$image = Cache::get($cache_key, function() use($src, $w, $h, $fill, $cache_key, $clear_cache) {
			
				$image = Image::make(base_path().'/public/img/'.$src);
				
				if($w || $h){
					
					if($fill){
						$image = $image->fit($w, $h);
					}else{
						$image = $image->resize($w, $h, function ($constraint) {
						    $constraint->aspectRatio();
						});
					}
					
				}
				
				if($image && !$clear_cache){
					Cache::forever($cache_key, (string) $image->encode());
				}
				
				return $image;
			});
						
			echo Image::make($image)->response();
		}catch(\Exception $e){
			echo $e->getMessage();
		}
		
		die();
    }
    
}
