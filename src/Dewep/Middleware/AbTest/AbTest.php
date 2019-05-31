<?php declare(strict_types=1);

namespace Dewep\Middleware\AbTest;

use Dewep\Config;
use Dewep\Middleware\Auth\Cookies;
use Dewep\Middleware\BaseClass;

class AbTest extends BaseClass
{
    const TEST = 'abT';
    const ACTION = 'abA';

    /** @var string|null */
    public $testId;
    /** @var int */
    public $actionId = 0;

    /**
     * @param array $params
     *
     * @return mixed|void
     */
    public function before(array $params)
    {
        $this->setParams($params);

        $this->testId = Cookies::getData(self::TEST);
        $this->actionId = (int)Cookies::getData(self::ACTION);

        if (
            $this->testId === null &&
            $this->build()
        ) {
            Cookies::setData(self::TEST, $this->testId);
            Cookies::setData(self::ACTION, $this->actionId);
        }
    }

    /**
     *
     */
    protected function build(): bool
    {
        $tests = Config::get('abtests', []);
        if (empty($tests)) {
            return false;
        }

        $testsHelper = new TestsHelper($tests);

        foreach ($testsHelper->getTests() as $test) {

        }

        return true;
    }

    /**
     * @param array $params
     *
     * @return mixed|void
     */
    public function after(array $params)
    {

    }
}
