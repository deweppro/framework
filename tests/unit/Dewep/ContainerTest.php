<?php

namespace Dewep;

class Test
{

    public static function hello()
    {
        return 'hello';
    }

}

class ContainerTest extends \Codeception\Test\Unit
{

    /**
     * @var \UnitTester
     */
    protected $tester;

    // tests
    public function testContainer()
    {
        //--
        $this->tester->assertEmpty(Container::all());

        //--
        Container::set('hello', '\Dewep\Test::hello');

        //--
        $this->tester->assertNotEmpty(Container::all());

        //--
        $hello1 = Container::get('hello1');
        $this->tester->assertEmpty($hello1);

        //--
        $this->tester->assertEquals('hello', Container::get('hello'));

        //--
        Container::exist('hello',
                function () {
            return 'not hello';
        });
        $this->tester->assertEquals('hello', Container::get('hello'));

        //--
        Container::remove('hello');
        $this->tester->assertEmpty(Container::get('hello'));

        //--
        Container::exist('hello',
                function () {
            return 'not hello';
        });
        $this->tester->assertEquals('not hello', Container::get('hello'));

        //--
        Container::reset();
        $this->tester->assertEmpty(Container::all());
    }

}
