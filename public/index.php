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
header('Access-Control-Allow-Origin: *');
$builder = new \DI\ContainerBuilder();
$container = $builder->build();
$database = new Medoo([
    'database_type' => 'mysql',
    'database_name' => 'todo',
    'server' => 'localhost',
    'username' => '',
    'password' => '',
]);
$container->set('database', $database);
$router = new Klein();

$router->respond('OPTIONS', null, function($request, $response) {
    /** @var \Klein\Response $response */
    $response->header('Access-Control-Allow-Methods', 'GET, POST, DELETE, OPTIONS');
    $response->header('Access-Control-Allow-Headers', 'Content-Type, Access-Control-Allow-Headers, Authorization, X-UserId, X-UserName, X-Token');
    $response->send();
    return true;
});

$router->respond(array('POST','GET'), '*', function($request, $response) use ($container) {
    /** @var \Klein\Request $request */
    /** @var \Klein\Response $response */
    $params = $request->headers();
    if (empty($params['X-Token']) || empty($params['X-UserId']) || empty($params['X-UserName'])) {
        $response->body('Empty credentials');
        $response->code(401);
        $response->send();
        return false;
    }
    $hash = hash(
        'sha256',
        $params['X-UserId'] . $params['X-UserName'] . APP_SECRET_KEY
    );
    if ($params['X-Token'] == $hash) {
        $container->set('owner', $params['X-UserId']);
        $container->set('userName', $params['X-UserName']);
    } else {
       $response->body('Incorrect password');
       $response->code(401);
       $response->send();
       return false;
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
    $router->respond('POST', '/markAsUnCompleted', [$ajaxController, 'markAsUnCompleted']);
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