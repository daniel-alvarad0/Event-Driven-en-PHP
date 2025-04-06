<?php
require __DIR__ . '/../vendor/autoload.php';

use React\Http\HttpServer;
use React\EventLoop\Factory;
use React\Socket\SocketServer;
use Psr\Http\Message\ServerRequestInterface;

require_once __DIR__ . '/Router.php';

$loop = React\EventLoop\Factory::create();

$server = new HttpServer(function (ServerRequestInterface $request) {
    return Router::route($request);
});

$socket = new SocketServer('127.0.0.1:8080', [], $loop);
$server->listen($socket);

echo "Servidor corriendo en http://127.0.0.1:8080\n";
$loop->run();
