<?php namespace App;

use Storage;
use Illuminate\Support\Collection;
use League\CommonMark\CommonMarkConverter;


class Document {
	
	public static $folder;
	
	private $attributes = [];
	
	public function __construct($file_name){
		$markdown = new CommonMarkConverter();
		
		$subheading = '';
		$content_filtered = '';
		$content = '';
		$content_raw = Storage::get($file_name);
		$content_rows = preg_split('/[\r\n]{1,1}/', $content_raw);
		
		foreach($content_rows as $content_row){
			if(strpos($content_row, '>> ') === 0){
				$subheading .= substr($content_row, 3);
			}else{
				$content_filtered .= $content_row.PHP_EOL;
			}
		}
		
		$content = $markdown->convertToHtml($content_filtered);
		
		$file_image = str_replace('.md', '.jpg', substr($file_name, strrpos($file_name, '/') +1));
		
		$this->filename			= $file_name;
		$this->image			= $file_image;
		$this->slug				= $this->convertFilenameToSlug($file_name);
		$this->subheading		= $subheading;
		$this->title			= $this->convertFilenameToTitle($file_name);
		$this->last_modified	= Storage::lastModified($file_name);
		$this->content			= $content;
	}
	
	public static function all(){
		$posts = Storage::files(self::getClassFolder());
		$posts = Collection::make($posts)->map(function($post){
			return self::makeModel($post);
		});
		
		return $posts;
		
	}
	
	protected static function getClassFolder(){
		$class = get_called_class();
		return str_plural(strtolower(substr($class, strrpos($class, '\\') + 1)));
	}
	
	protected static function makeModel($file_name){
		return new static($file_name);
	}
	
	protected function convertFilenameToTitle($file_name){
		return ucwords(str_replace('_', ' ', substr(substr($file_name, strpos($file_name, '/') + 1), 0, -3)));
	}
	
	protected function convertFilenameToSlug($file_name){
		return str_replace('_', '-', substr(substr($file_name, strpos($file_name, '/') + 1), 0, -3));
	}
	
	public static function find($slug){
		$file_name = self::getClassFolder().'/'.str_replace('-', '_', $slug).'.md';
		
		return new static($file_name);
	}
	
	public function __get($key){
		return $this->attributes[$key];
	}
	
	public function __set($key, $value){
		$this->attributes[$key] = $value;
	}
	
}