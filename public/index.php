<?php
error_reporting(E_ALL);
use Ratchet\Server\IoServer;
use Duamel\Todo\WebSocketController;
use Medoo\Medoo;
use Klein\Klein;
require __DIR__ . '/../vendor/autoload.php';
$builder = new \DI\ContainerBuilder();
$container = $builder->build();
$database = new Medoo([
    'database_type' => 'mysql',
    'database_name' => 'todo',
    'server' => 'localhost',
    'username' => 'root',
    'password' => 'passwd',
]);
$container->set('database', $database);
$router = new Klein();

$ajaxController = new \Duamel\Todo\AjaxController($container);
$router->with('/api', function () use ($router, $ajaxController) {
    $router->respond('GET','/get/[:id]', [$ajaxController, 'get']);
    $router->respond('GET', '/getAll', [$ajaxController, 'getAll']);
    $router->respond('POST', '/create', [$ajaxController, 'create']);
    $router->respond('POST', '/update', [$ajaxController, 'update']);
    $router->respond('DELETE', '/delete/[:id]', [$ajaxController, 'delete']);
});

$router->respond('GET', '/install', function() use ($container) {
    if (file_exists(__DIR__ . '/../.installed')) {
        return;
    }
    /** @var Medoo $database */
    $database = $container->get('database');
    $database->exec('CREATE TABLE todo (
                              id          INT  NOT NULL AUTO_INCREMENT  PRIMARY KEY,
                              owner       INT  NOT NULL,
                              message     TEXT,
                              isCompleted BOOL NOT NULL,
                              date        TIMESTAMP     DEFAULT CURRENT_TIMESTAMP
                              )');
    $database->exec('CREATE INDEX owner ON todo (owner)');
    file_put_contents(__DIR__ . '/../.installed', '');
});
$router->dispatch();