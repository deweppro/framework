<?php

namespace Dewep\Middleware\AbTest;

/**
 * Class TestModel
 *
 * @package Dewep\Middleware\AbTest
 */
class TestModel
{
    /** @var string */
    protected $name = '';
    /** @var int */
    protected $rate = 0;
    /** @var ActionsModel[] */
    protected $actions = [];
    /** @var bool */
    protected $active = true;
    /** @var bool */
    protected $closed = true;

    /**
     * TestModel constructor.
     *
     * @param array $test
     */
    public function __construct(array $test)
    {

    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     *
     * @return TestModel
     */
    public function setName(string $name): TestModel
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return int
     */
    public function getRate(): int
    {
        return $this->rate;
    }

    /**
     * @param int $rate
     *
     * @return TestModel
     */
    public function setRate(int $rate): TestModel
    {
        $this->rate = $rate;

        return $this;
    }

    /**
     * @return array
     */
    public function getActions(): array
    {
        return $this->actions;
    }

    /**
     * @param array $actions
     *
     * @return TestModel
     */
    public function setActions(array $actions): TestModel
    {
        $this->actions = $actions;

        return $this;
    }

    /**
     * @return bool
     */
    public function isActive(): bool
    {
        return $this->active;
    }

    /**
     * @param bool $active
     *
     * @return TestModel
     */
    public function setActive(bool $active): TestModel
    {
        $this->active = $active;

        return $this;
    }

    /**
     * @return bool
     */
    public function isClosed(): bool
    {
        return $this->closed;
    }

    /**
     * @param bool $closed
     *
     * @return TestModel
     */
    public function setClosed(bool $closed): TestModel
    {
        $this->closed = $closed;

        return $this;
    }


}
