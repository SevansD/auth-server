<?php

namespace Duamel\Todo\Entity;
class Todo
{
    public $id;
    public $owner;
    public $message;
    public $isCompleted;
    public $date;

    public function __construct($owner, $message, $isCompleted = false)
    {
        $this->owner = $owner;
        $this->message = $message;
        $this->isCompleted = $isCompleted;
        $this->date = time();
    }

    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    public function validate()
    {
        return !empty($this->owner) && !empty($this->message);
    }
}
