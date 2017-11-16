<?php

namespace Dewep\Parsers;

class YamlTest extends \Codeception\Test\Unit
{

    /**
     * @var \UnitTester
     */
    protected $tester;

    public function testParsersYaml()
    {
        $fileYaml = codecept_data_dir() . '/data.yml';

        $data = Yaml::read($fileYaml, codecept_output_dir());

        $this->tester->assertArrayHasKey('name', $data);

        $this->tester->assertEquals('test', $data['name']);

        array_map("unlink", glob(codecept_output_dir() . '*.yml.json'));
    }

}
