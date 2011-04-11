<?php
require_once 'system.php';

define('confFile','tests/config.inc.php');

class SystemTest extends PHPUnit_Framework_TestCase {
    var $sys;
    public function setUp() {
        $this->sys=new System;
    }
    public function testGetValue() {
        $expected=true;
        $_POST['test1']=$expected;        
        $result=$this->sys->getValue('test1');
        $this->assertTrue($result==$expected);
    }
}
