<?php
/**
* Class to handle config file
* @package evofw
* @version 1.0
*/
class Config
{
    private static $confArray = array();
    private static $instance = null;
    private static $name = null;
    /**
    * @param string $file Config file to parse
    * @param string $name Game setting in the config file
    * @return object Instance of the config class
    */
    public static function init($file, $name='default') {
        if(!$file) {
            throw new Exception("No valid config file supplied");
        }
        if (self::$instance == null) {
            self::$instance = new self;
        }
        include($file);
        self::$confArray = $config;
        self::$name = $name;
        return self::$instance;
    }
    /**
    * Function to get config settings
    * @param string $key Key to get the value for
    * @return mixed Value for the provided key or all values if the key is null
    */
    public static function get($key=null) {
        if (!$key) {
            return self::$confArray[self::$name];
        }
        else {
            return self::$confArray[self::$name][$key];
        }
    }
}
