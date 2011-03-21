<?php
class Config {
	private static $confArray=array();
	private static $instance=null;
	private static $game=null;
	public static function init($file, $game='default') {
		if(!$file) {
			throw new Exception("No valid config file supplied");
		}
		if(self::$instance==null) {
			self::$instance=new self;
		}
		include($file);
		self::$confArray=$config;
		self::$game=$game;
		return self::$instance;
	}
	public static function get($key=null) {
		if(!$key) {
			return self::$confArray[self::$game];
		}
		else {
			return self::$confArray[self::$game][$key];
		}
	}
}
?>
