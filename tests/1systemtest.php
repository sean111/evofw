<?php
require_once 'src/system.php';

define('confFile','tests/config.inc.php');

class SystemTest extends PHPUnit_Framework_TestCase {
    var $sys;
    public function setUp() {
        $this->sys=new System;
    }
    public function tearDown() {
        unset($this->sys);
    }
    public function testGetValue() {
        $expected=true;
        $_POST['test1']=$expected;        
        $result=$this->sys->getValue('test1');
        $this->assertTrue($result==$expected);
    }
    public function testLoad() {
        try {
            $this->sys->load('test');
            $this->assertTrue(true);
        }
        catch(Exception $e) {
            $this->assertFalse(true, $e->getMessage());
        }
    }
    public function testLoadAction() {
        try {
            $this->sys->load('test','testing');
            $this->assertTrue(true);
        }
        catch(Exception $e) {
            $this->assertFalse(true, $e->getMessage());
        }
    }
}
