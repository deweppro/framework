<?php

namespace Dewep;

class ConfigTest extends \Codeception\Test\Unit
{

    /**
     * @var \UnitTester
     */
    protected $tester;

    public function testConfig()
    {
        $this->tester->assertEmpty(Config::all());

        $config = [
            'db' => 'mysql'
        ];
        Config::append($config);

        $this->tester->assertNotEmpty(Config::all());

        $this->tester->assertEquals('mysql', Config::get('db'));

        $this->tester->assertEmpty(Config::get('php'));
    }

}
