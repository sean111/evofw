<?php
/**
* Class to handle basic views
* @package evofw
* @version 1.0
*/
class View
{
    private $_data = array();
    private $_view;
    /**
    * Function to intialize the View
    * @param string $name Name of the instance
    * @return object Instance
    */
    public static function init($name = null)
    {
        return new View($name);
    }
    /**
    * @param string $name Name of the instance
    */
    public function __construct($name = null)
    {
        if ($name) {
            $this->load($name);
        }        
    }
    /**
    * Load the view file
    * @param string $name Name of the view to load
    */
    public function load($name)
    {
        $path = Config::get('path');
        $view = $path.'/views/'.$name.".php";
        if (!$name || !is_file($view)) {
            throw new Exception("No valid view supplied");
        }
        $this->_view = $view;
    }
    /**
    * Function to bind value to the view
    * @param mixed $key Name of the value
    * @param mixed $value Value for the variable
    * @return object Returns it's self to allow chaining
    */
    public function bind($key, &$value)
    {
        $this->_data[$key] = &$value;
        //print "Bound $key to ".$value."<br>";
        return $this;
    }
    /**
    * Render the view
    */
    public function render()
    {
        extract($this->_data, EXTR_SKIP);       
        ob_start();
        try {
            include $this->_view;
        }
        catch(Exception $e) {
            ob_end_clean();
            throw $e;
        }
        return ob_end_flush();
    }
}
