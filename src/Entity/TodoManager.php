<?php

namespace Duamel\Todo\Entity;

use Psr\Container\ContainerInterface;

class TodoManager
{

    /** @var \Medoo\Medoo */
    private $database;

    public function __construct(ContainerInterface $container)
    {
        $this->database = $container->get('database');
    }

    /**
     * @param \Duamel\Todo\Entity\Todo $entity
     *
     * @return int|null
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
    }


    public function delete($entity)
    {
        $this->database->delete('todo', 'id = ' . $entity->id);
    }

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
        $data = $this->database->select('todo', '*', 'owner = ' . $owner);
        $all = [];
        foreach ($data as $todo) {
            $all[] = (new Todo($todo['owner'], $todo['message'], $todo['isCompleted']))
                ->setId($todo['id']);
        }
        return $all;
    }
}
