<?php namespace App;

use Cache;
use Carbon;

class Asset {
	
	const CACHE_TIME = 1440;//one day in minutes
	
	public static function inject($file) {
		
		Cache::forget($file);
		
		$file_contents = Cache::get($file, function() use($file) {
			$file_contents = '';
			
			if(!file_exists($file)){
				$file_contents = self::getFileContents($file);
			}else{
				$file_contents = file_get_contents($file);
			}
			
			Cache::put($file, $file_contents, time() + self::CACHE_TIME);
			
			return $file_contents;
		});
		
		return $file_contents;
	}
	
	private static function getFileContents($file){
		$ch = curl_init();
		$timeout = 10;
		curl_setopt($ch, CURLOPT_URL, $file);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
		$file_contents = curl_exec($ch);
		curl_close($ch);
		return $file_contents;
	}
	
}