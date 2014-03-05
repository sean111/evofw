<?php
/**
* It can be anywhere or anything... even your dog Todo!
* @package evofw
* @version 1.0
*/
class Morph
{
    /**
    * @param array $array Values to place into the class using key=>val
    */
    public function  __construct($array = null)
    {
        if ($array) {
            foreach($array as $key => $val) {
                $this->{$key} = $val;
            }
        }
    }
    /**
    * Function to set values
    * @param string $name Name of the variable
    * @param mixed $value Value to be set
    */
    public function __set($name, $value)
    {
        $this->{$name} = $value;
    }
    /**
    * Function to get values
    * @param string $name Name of the variable to get
    * @return mixed Value for the variable name passed
    */
    public function __get($name)
    {
        if (isset($this->{$name})) {
            return $this->{$name};
        }
        else {
            return null;
        }
    }
    /**
    * Check if a value is set
    * @param string $name Name of the variable
    * @return bool If the value is set or not
    */
    public function __isset($name)
    {
        return isset($this->{$name});
    }
    /**
    * Unset a variable
    * @param string $name Name of the variable to unset
    */
    public function  __unset($name)
    {
        unset($this->{$name});
    }
}