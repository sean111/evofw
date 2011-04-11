<?php
if(!class_exists('Config')) {
    require_once 'config.php';
}
define('confFile','tests/config.inc.php');
class ConfigTest extends PHPUnit_Framework_TestCase {
    var $conf;
    public function setUp() {
        $this->conf=Config::init(confFile);
    }
    public function tearDown() {
        unset($this->conf);
    }
    public function testGet() {
        $result=$this->conf->get('testvar');
        $expected=42;
        $this->assertTrue($result===$expected);
    }
}
?>
