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
        $this->container = $container;
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
            $newTodo = new Todo($this->container->get('owner'), $params['message'], FALSE);
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
            if (!isset($params['id'])) {
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
            $todos = $this->manager->getAll($this->container->get('owner'));
            return $response->body(json_encode(['items' => $todos]));
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
    public function delete($request, $response)
    {
        try {
            $id = $request->id;
            if (!isset($id)) {
                throw new \Exception('', 400);
            }
            $this->manager->delete($id);
            return $response->code(200);
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
    public function markAsCompleted($request, $response)
    {
        try {
            $id = $request->param('id');
            if (!isset($id)) {
                throw new \Exception('', 400);
            }
            $this->manager->markAsCompleted($id, $this->container->get('owner'));
            return $response->code(200);
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
    public function markAsUnCompleted($request, $response)
    {
        try {
            $id = $request->param('id');
            if (!isset($id)) {
                throw new \Exception('', 400);
            }
            $this->manager->markAsUnCompleted($id, $this->container->get('owner'));
            return $response->code(200);
        } catch (\Exception $e) {
            return $response->code($e->getCode());
        }
    }
}
