<?php
require_once 'twig/Twig.php';
class Twig {
    private $_data=array();
    private $_file;
    private $twig;
    public static function init($name=null) {
        return new Twig($name);
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
        $this->twig=new Twig_Enviroment($loader);
        $this->_file=$view;
    }
    public function bind($key, &$value) {
        $this->_data[$key]=&$value;
        //print "Bound $key to ".$value."<br>";
        return $this;
    }
    public function render() {
        $this->twig->render($this->_file, $this->_data);
    }
}
?>
