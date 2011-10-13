<?php
require_once 'Twig/Autoloader.php';
class TwigView {
    private $_data=array();
    private $_file;
    private $twig;
    public static function init($name=null) {
        Twig_Autoloader::register();
        return new TwigView($name);
    }
    public function __construct($name=null) {        
        if($name) {            
            $this->load($name);
        }                        
    }
    public function load($name) {
        $path=Config::get('path');
        $view=$path.'/views/'.$name;        
        if(!$name || !is_file($view)) {
            throw new Exception("No valid template supplied");
        }
        $loader=new Twig_Loader_Filesystem($path.'/views/');
        $this->twig=new Twig_Environment($loader);
        $this->_file=$name;
    }
    public function bind($key, $value) {
        $this->_data[$key]=$value;
        return $this;
    }
    public function render() {        
        print $this->twig->render($this->_file, $this->_data);
    }
}
?>
