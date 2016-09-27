<?php

if (!class_exists('View')) {
    require_once 'view.php';
}

if (!class_exists('Config')) {
    require_once 'config.php';
}
define('confFile', 'tests/config.inc.php');

class ViewTest extends PHPUnit_Framework_TestCase
{
    public $view;

    public function setUp()
    {
        Config::init(confFile);
        $this->view = View::init();
    }

    public function tearDown()
    {
        unset($this->view);
    }

    public function testLoad()
    {
        try {
            $this->view->load('testview');
            $this->assertTrue(true);
        } catch (Exception $e) {
            $this->assertFalse(true, $e->getMessage());
        }
    }

    public function testBind()
    {
        try {
            $a = true;
            $this->view->bind('test', $a);
            $this->assertTrue(true);
        } catch (Exception $e) {
            $this->assertFalse(true, $e->getMessage());
        }
    }

    public function testRender()
    {
        try {
            View::init('testview')->render();
            $this->assertTrue(true);
        } catch (Exception $e) {
            $this->assertFalse(true, $e->getMessage());
        }
    }
}
