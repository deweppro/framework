<?php

namespace Dewep;

class ContainerTest extends \Codeception\Test\Unit
{

    /**
     * @var \UnitTester
     */
    protected $tester;

    protected function _before()
    {

    }

    protected function _after()
    {

    }

    // tests
    public function testMe()
    {
        $all = Container::all();
        $this->tester->assertEmpty($all);
    }

}
