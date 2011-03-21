<?php
Class Morph {
    public function  __construct($array=null) {
        if($array) {
            foreach($array as $key=>$val) {
                $this->{$key}=$val;
            }
        }
    }
    public function __set($name, $value) {
        $this->{$name}=$value;
    }
    public function __get($name) {
        if(isset($this->{$name})) {
            return $this->{$name};
        }
        else {
            return null;
        }
    }
    public function __isset($name) {
        return isset($this->{$name});
    }
    public function  __unset($name) {
        unset($this->{$name});
    }
}
?>