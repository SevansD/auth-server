<?php

namespace Duamel\Todo;

use Psr\Container\ContainerInterface;

class AjaxController
{

    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container->get('database');
    }

    public function create()
    {
    }

    public function update()
    {
    }

    public function get()
    {
    }

    public function getAll()
    {
    }

    public function delete()
    {
    }
}
