<?php
class View {
    private $_data=array();
    private $_view;
    public static function init($name=null) {
        return new View($name);
    }
    public function __construct($name=null) {
        if($name) {
            $this->load($name);
        }        
    }
    public function load($name) {
        $path=Config::get('path');
        $view=$path.'/views/'.$name.".php";        
        if(!$name || !is_file($view)) {
            throw new Exception("No valid view supplied");
        }
        $this->_view=$view;
    }
    public function bind($key, &$value) {
        $this->_data[$key]=&$value;
        //print "Bound $key to ".$value."<br>";
        return $this;
    }
    public function render() {
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
?>
