<?php

namespace Dewep\Middleware\AbTest;

/**
 * Class ActionsModel
 *
 * @package Dewep\Middleware\AbTest
 */
class ActionsModel
{
    /** @var int */
    protected $id = 0;
    /** @var string */
    protected $description = '';

    /**
     * ActionsModel constructor.
     *
     * @param array $action
     */
    public function __construct(array $action)
    {
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     *
     * @return ActionsModel
     */
    public function setId(int $id): ActionsModel
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @param string $description
     *
     * @return ActionsModel
     */
    public function setDescription(string $description): ActionsModel
    {
        $this->description = $description;

        return $this;
    }


}
