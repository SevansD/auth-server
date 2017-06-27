<?php

namespace Duamel\Todo;

use Duamel\Todo\Entity\Todo;
use Duamel\Todo\Entity\TodoManager;
use Psr\Container\ContainerInterface;

class AjaxController
{

    private $container;

    private $manager;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container->get('database');
        $this->manager = new TodoManager($container);
    }

    /**
     * @param \Klein\Request $request
     * @param \Klein\AbstractResponse $response
     *
     * @return mixed
     */
    public function create($request, $response)
    {
        try {
            $params = $request->paramsPost();
            $newTodo = new Todo($params['owner'], $params['message'], FALSE);
            $newTodo->id = $this->manager->insert($newTodo);
            $response->code(201);
            return $response->body(json_encode($newTodo));
        } catch (\Exception $e) {
            return $response->code(403);
        }
    }

    /**
     * @param \Klein\Request $request
     * @param \Klein\AbstractResponse $response
     *
     * @return mixed
     */
    public function update($request, $response)
    {
        try {
            $params = $request->paramsPost();
            if (empty($params['id'])) {
                throw new \Exception('', 400);
            }
            $todo = $this->manager->read($params['id']);
            if (empty($todo)) {
                throw new \Exception('', 404);
            }
            if (!empty($params['message']) && $params['message'] != $todo->message) {
                $todo->message = $params['message'];
                if (isset($params['isCompleted'])) {
                    $todo->isCompleted = $params['isCompleted'];
                }
                $this->manager->update($todo);
                return $response->code(200);
            }
            throw new \Exception('', 400);
        } catch (\Exception $e) {
            return $response->code($e->getCode());
        }
    }

    /**
     * @param \Klein\Request $request
     * @param \Klein\AbstractResponse $response
     *
     * @return mixed
     */
    public function get($request, $response)
    {
        try {
            $todo = $this->manager->read($request->id);
            return $response->body(json_encode($todo));
        } catch (\Exception $e) {
            return $response->code(404);
        }
    }

    /**
     * @param \Klein\Request $request
     * @param \Klein\AbstractResponse $response
     *
     * @return mixed
     */
    public function getAll($request, $response)
    {
        try {
            $todos = $this->manager->getAll($request->owner);
            return $response->body(json_encode($todos));
        } catch (\Exception $e) {
            return $response->code(404);
        }
    }

    public function delete($request, $response)
    {
        try {

        } catch (\Exception $e) {

        }
    }
}
