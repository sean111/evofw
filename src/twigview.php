<?php
/**
* Wrapper class for the Twig templating engine (Module)
* @package evofw
* @version 1.0
*/
class TwigView
{
    private $_data = array();
    private $_file;
    private $twig;
    /**
    * Initialize the TwigView
    * @param string $name Name of the instance
    * @return object TwigView instance for name
    */
    public static function init($name = null)
    {
        Twig_Autoloader::register();
        return new TwigView($name);
    }
    /**
    * @param string $name Name of the instance
    */
    public function __construct($name = null)
    {
        require_once 'Twig/Autoloader.php';
        if ($name) {
            $this->load($name);
        }
    }
    /**
    * Load a provided file
    * @param string $name Name of the template to load
    */
    public function load($name)
    {
        $path = Config::get('path');
        $view = $path.'/views/'.$name;
        if (!$name || !is_file($view)) {
            throw new Exception("No valid template supplied");
        }
        $loader=new Twig_Loader_Filesystem($path.'/views/');
        $this->twig = new Twig_Environment($loader);
        $this->_file = $name;
    }
    /**
    * Bind the supplied key and value to the template
    * @param mixed $key Name for the value
    * @param mixed $value Value for the variable
    * @return object Returns the object for chaining
    */
    public function bind($key, $value)
    {
        $this->_data[$key] = $value;
        return $this;
    }
    /**
    * Render the template file
    */
    public function render()
    {
        print $this->twig->render($this->_file, $this->_data);
    }
}
