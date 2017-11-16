<?php

namespace Dewep\Parsers;

class ResponseTest extends \Codeception\Test\Unit
{

    /**
     * @var \UnitTester
     */
    protected $tester;

    /*
     *
     */

    public function testParsersResponseJson()
    {
        $result = Response::json(['name' => 'test']);
        $this->tester->assertNotEmpty($result);
        $this->tester->assertEquals('{"name":"test"}', $result);
    }

    public function testParsersResponseHtml()
    {
        $result = Response::html(['name' => 'test']);
        $this->tester->assertNotEmpty($result);
        $this->tester->assertEquals("<html><test>name</test></html>\n", $result);
    }

    public function testParsersResponseXml()
    {
        $result = Response::xml(['name' => 'test']);
        $this->tester->assertNotEmpty($result);
        $this->tester->assertEquals("<?xml version=\"1.0\"?>\n<root><test>name</test></root>\n",
                $result);
    }

}
