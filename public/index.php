<?php
use Ratchet\Server\IoServer;
use Duamel\Todo\WebSocketController;
use Medoo\Medoo;
use Klein\Klein;
define('APP_SECRET_KEY', '! golf PARK LAPTOP tokyo 3 FRUIT SKYPE PARK drip # 2 + BESTBUY % 2');
require __DIR__ . '/../vendor/autoload.php';
if (!file_exists(__DIR__ . '/../.installed')) {
    echo "Service isn't installed. Run /install";
}
$builder = new \DI\ContainerBuilder();
$container = $builder->build();
$database = new Medoo([
    'database_type' => 'mysql',
    'database_name' => 'todo',
    'server' => 'localhost',
    'username' => 'root',
    'password' => 'forkpoons',
]);
$container->set('database', $database);
$router = new Klein();
$router->respond('*', function($request, $response) use ($container) {
    /** @var \Klein\Request $request */
    /** @var \Klein\Response $response */
    $params = $request->paramsGet();
    var_dump($_SERVER); die;
    if (empty($params['token']) || $params['userId'] || $params['userName']) {
        $response->body('Empty headers');
        return $response->code(401);
    }
    $hash = hash(
        'sha256',
        $params['userId'] . $params['userName'] . APP_SECRET_KEY
    );
    if ($params['token'] == $hash) {
        $container->set('owner', $params['userId']);
        $container->set('userName', $params['userName']);
    } else {
       // $response->body('Incorrect password');
       // return $response->code(401);
    }
});

$ajaxController = new \Duamel\Todo\AjaxController($container);
$router->with('/api', function () use ($router, $ajaxController) {
    $router->respond('GET','/get/[:id]', [$ajaxController, 'get']);
    $router->respond('GET', '/getAll', [$ajaxController, 'getAll']);
    $router->respond('POST', '/create', [$ajaxController, 'create']);
    $router->respond('POST', '/update', [$ajaxController, 'update']);
    $router->respond('DELETE', '/delete/[:id]', [$ajaxController, 'delete']);
    $router->respond('POST', '/markAsCompleted', [$ajaxController, 'markAsCompleted']);
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