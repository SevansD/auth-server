<?php

namespace Duamel\Todo\Entity;

use Psr\Container\ContainerInterface;

class Todo
{
    public $id;
    public $owner;
    public $message;
    public $isCompleted;
    public $date;

    /** @var \Medoo\Medoo */
    private $database;

    public function __construct(ContainerInterface $container, $owner, $message, $isCompleted = false)
    {
        $this->database = $container->get('database');
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

    public function save()
    {
        if (empty($this->id)) {
            $this->database->insert('users', [
                'id' => null,
                'owner' => $this->owner,
                'message' => $this->message,
                'isCompleted' => $this->isCompleted,
                'date' => $this->date,
            ]);
        } else {
            $this->database->update(
                'users',
                    [
                'owner' => $this->owner,
                'message' => $this->message,
                'isCompleted' => $this->isCompleted,
                'date' => $this->date,
                ],
                'id = ' . $this->id
                );
        }
    }

    public function delete()
    {
        $this->database->delete('users', 'id = ' . $this->id);
    }

    public function read()
    {
        if (empty($id)) {
            return;
        }
        $data = $this->database->get('users', '*', 'id = ' . $this->id);
        $this->owner = $data['owner'];
        $this->message = $data['mesage'];
        $this->isCompleted = $data['isCompleted'];
        $this->date = $data['date'];
    }

    /**
     * @param int $owner
     * @param ContainerInterface $container
     *
     * @return $this[]
     */
    public static function getAll($owner, $container)
    {
        $data = $container->get('database')->select('users', '*', 'owner = ' . $owner);
        $all = [];
        foreach ($data as $todo) {
            $all[] = (new self($container, $todo['owner'], $todo['message'], $todo['isCompleted']))->setId($todo['id']);
        }
        return $all;
    }
}
