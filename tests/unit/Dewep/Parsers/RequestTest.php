<?php

namespace Dewep\Parsers;

class RequestTest extends \Codeception\Test\Unit
{

    /**
     * @var \UnitTester
     */
    protected $tester;

    public function testParsersRequestJson()
    {
        $file = codecept_data_dir() . '/data.json';

        //-- валидный json
        $data = file_get_contents($file);

        $result = Request::json($data);

        $this->tester->assertNotEmpty($result);
        $this->tester->assertArrayHasKey('name', $result);
        $this->tester->assertEquals('test', $result['name']);

        //-- не валидный json
        $data = file_get_contents($file);

        $result = Request::json($data . 'ERROR');

        $this->tester->assertNull($result);
    }

    public function testParsersRequestUrl()
    {
        $file = codecept_data_dir() . '/data.txt';

        //-- валидная ссылка
        $data = file_get_contents($file);

        $result = Request::url($data);

        $this->tester->assertNotEmpty($result);
        $this->tester->assertArrayHasKey('name', $result);
        $this->tester->assertEquals('test', $result['name']);

        //-- не валидная ссылка
        $result = Request::url('');

        $this->tester->assertNull($result);
    }

    public function testParsersRequestXml()
    {
        $file = codecept_data_dir() . '/data.xml';

        //-- валидный json
        $data = file_get_contents($file);

        $result = Request::xml($data);

        $this->tester->assertNotEmpty($result);
        $this->tester->assertInternalType('object', $result);
        $this->tester->assertEquals('test', $result->name);

        //-- не валидный json
        $data = file_get_contents($file);

        $result = Request::xml($data . 'ERROR');

        $this->tester->assertNull($result);
    }

}
