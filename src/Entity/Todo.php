<?php

namespace Duamel\Todo\Entity;

/**
 * Class Todo
 * @package Duamel\Todo\Entity
 * Class is a container for todo
 */
class Todo
{
    /** @var int */
    public $id;
    /** @var int */
    public $owner;
    /** @var string */
    public $message;
    /** @var bool */
    public $isCompleted;
    /** @var int */
    public $date;

    /**
     * Todo constructor.
     * @param int $owner
     * @param string $message
     * @param bool $isCompleted
     */
    public function __construct($owner, $message, $isCompleted = false)
    {
        $this->owner = $owner;
        $this->message = $message;
        $this->isCompleted = $isCompleted;
        $this->date = time();
    }

    /**
     * @param int $id
     * @return $this
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return bool
     */
    public function validate()
    {
        return !empty($this->owner) && !empty($this->message);
    }
}
