<?php

class SessionTest extends PHPUnit_Framework_TestCase
{
    public function setUp() {
        global $mysql_session_table;
        mysql_session_open(null, null);
        mysql_query("DELETE FROM $mysql_session_table");
        mysql_session_close();
    }

    public function tearDown() {
        global $mysql_session_table;
        mysql_session_open(null, null);
        mysql_query("DELETE FROM $mysql_session_table");
        mysql_session_close();
    }

    public function testSessionWR() {

        for ($i = 0; $i < 10; $i++) {
            $this->assertTrue(mysql_session_open(null, null));

            $sessionvars = array("test_data" => "data #$i");
            $sessiondata = serialize($sessionvars);
            $sessionid = md5("testtest$i");

            // Start a new session
            $this->assertTrue(mysql_session_write($sessionid, $sessiondata));
            $result = mysql_session_read($sessionid);
            $this->assertEquals($result, $sessiondata);
            $this->assertTrue(mysql_session_close());

            // Re-open the session
            $this->assertTrue(mysql_session_open(null, null));
            $result = mysql_session_read($sessionid);
            $this->assertEquals($result, $sessiondata);
            $this->assertTrue(mysql_session_write($sessionid, $sessiondata));
            $this->assertTrue(mysql_session_close());
        }

    }

    public function testSessionWRBig() {
        $bigvalue = "";
        for ($i = 0; $i < 100000; $i++) {
            $bigvalue .= 'x';
        }

        for ($i = 0; $i < 10; $i++) {
            $this->assertTrue(mysql_session_open(null, null));

            $sessionvars = array("test_data" => $bigvalue . " data #$i");
            $sessiondata = serialize($sessionvars);
            $sessionid = md5("testtest$i");

            // Start a new session
            $this->assertTrue(mysql_session_write($sessionid, $sessiondata));
            $result = mysql_session_read($sessionid);
            $this->assertEquals($result, $sessiondata);
            $this->assertTrue(mysql_session_close());

            // Re-open the session
            $this->assertTrue(mysql_session_open(null, null));
            $result = mysql_session_read($sessionid);
            $this->assertEquals($result, $sessiondata);
            $this->assertTrue(mysql_session_write($sessionid, $sessiondata));
            $this->assertTrue(mysql_session_close());
        }
    }

    public function testNonexistentSession() {
            $this->assertTrue(mysql_session_open(null, null));
            $this->assertEquals(mysql_session_read("asdalsidasd"), "");
            $this->assertTrue(mysql_session_close());
    }
}
