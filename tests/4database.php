<?php

class DatabaseTest extends PHPUnit_Framework_TestCase
{
    public function testSelect()
    {
        $expected = 'seanb@eshark.net';
        Database::select('users', 'email', ['id' => 1]);
        $res = Database::results();
        $result = $res[0]['email'];
        $this->assertTrue($result == $expected);
    }
}
