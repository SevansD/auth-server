<?php

use Ratchet\Server\IoServer;
use Duamel\Todo\WebSocketController;
use Medoo\Medoo;
use MiladRahimi\PHPRouter\Router;
require __DIR__ . '/../vendor/autoload.php';
$builder = new \DI\ContainerBuilder();
$container = $builder->build();
$database = new Medoo([
    'database_type' => 'mysql',
    'database_name' => 'name',
    'server' => 'localhost',
    'username' => 'your_username',
    'password' => 'your_password',
]);
$container->set('database', $database);
$router = new Router;
$router->any('/websocket', function() use ($container) {
    $server = IoServer::factory(new WebSocketController($container), 8080);
    $server->run();
});

$ajaxController = new \Duamel\Todo\AjaxController($container);
$router->get('/api/get/{id}', 'AjaxController@get');
$router->get('/api/getAll', 'AjaxController@getAll');
$router->post('/api/create', 'AjaxController@create');
$router->map('put', '/api/update', 'AjaxController@update');
$router->map('delete', 'api/delete', 'AjaxController@delete');

$router->get('install', function() use ($container) {
    if (file_exists(__DIR__ . '/../.installed')) {
        return;
    }
    /** @var Medoo $database */
    $database = $container->get('database');
    $database->exec('CREATE TABLE users (
                              id          INT  NOT NULL AUTO_INCREMENT  PRIMARY KEY,
                              owner       INT  NOT NULL,
                              message     TEXT,
                              isCompleted BOOL NOT NULL,
                              date        DATETIME
                              )');
    $database->exec('CREATE INDEX owner ON users (owner)');
});
$router->dispatch();