<?php

namespace Duamel\Todo\Entity;

use Psr\Container\ContainerInterface;

/**
 * Class TodoManager
 * @package Duamel\Todo\Entity
 * Class for save entity into storage
 */
class TodoManager
{

    /** @var \Medoo\Medoo */
    private $database;

    /**
     * TodoManager constructor.
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->database = $container->get('database');
    }

    /**
     * @param Todo $entity
     * @return int|string
     * @throws \Exception
     */
    public function insert($entity)
    {
        if ($entity->validate()) {
            $this->database->insert('todo', [
                'id' => NULL,
                'owner' => $entity->owner,
                'message' => $entity->message,
                'isCompleted' => $entity->isCompleted
            ]);
            return $this->database->id();
        }
        throw new \Exception();
    }

    /**
     * @param $entity
     *
     * @return Todo
     */
    public function update($entity)
    {
        $this->database->update(
            'todo',
            [
                'message' => $entity->message,
                'isCompleted' => $entity->isCompleted
            ],
            ['id' => $entity->id]
        );
        return $this->read($entity->id);
    }

    /**
     * @param $id
     */
    public function delete($id)
    {
        $this->database->delete('todo', ['id' => $id]);
    }

    /**
     * @param int $id
     * @return Todo
     * @throws \Exception
     */
    public function read($id)
    {
        if (empty($id)) {
            throw new \Exception();
        }
        $data = $this->database->get('todo', '*', ['id' => $id]);
        if ($data) {
            $entity = new Todo($data['owner'], $data['message'], $data['isCompleted']);
            $entity->setId($id);
            return $entity;
        }
        throw new \Exception();
    }

    /**
     * @param int $owner
     *
     * @return $this[]
     */
    public function getAll($owner)
    {
        $data = $this->database->select('todo', '*', ['todo.owner' => $owner]);
        $all = [];
        foreach ($data as $todo) {
            $all[] = (new Todo($todo['owner'], $todo['message'], $todo['isCompleted']))
                ->setId($todo['id']);
        }
        return $all;
    }

    /**
     * @param int $id
     * @param int $owner
     */
    public function markAsCompleted($id, $owner)
    {
        $this->database->update('todo', ['isCompleted' => true], ['id' => $id, 'owner' => $owner]);
    }

    /**
     * @param int $id
     * @param int $owner
     */
    public function markAsUnCompleted($id, $owner)
    {
        $this->database->update('todo', ['isCompleted' => false], ['id' => $id, 'owner' => $owner]);
    }
}
