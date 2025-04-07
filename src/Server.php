<?php

namespace App;

use React\EventLoop\LoopInterface;
use React\Http\HttpServer;
use React\Socket\SocketServer;
use React\MySQL\ConnectionInterface;
use App\Controllers\DataController;
use App\Models\ReservaModel;
use App\Router;

class Server
{
    private $loop;
    private $db;

    /**
     * Constructor del servidor
     * 
     * @param LoopInterface $loop Loop de eventos de ReactPHP
     * @param ConnectionInterface $db Conexión a la base de datos
     */
    public function __construct(LoopInterface $loop, ConnectionInterface $db)
    {
        $this->loop = $loop;
        $this->db = $db;
    }

    /**
     * Inicia el servidor HTTP
     */
    public function run()
    {
        // Crear modelo y controladores
        $reservaModel = new ReservaModel($this->db);
        $dataController = new DataController($reservaModel);

        // Crear router con controladores
        $router = new Router($dataController);

        // Crear servidor HTTP
        $httpServer = new HttpServer($router);

        // Crear socket y escuchar en el puerto 8080
        $socket = new SocketServer('127.0.0.1:8000');
        $httpServer->listen($socket);

        echo "Servidor ejecutándose en http://127.0.0.1:8000\n";
    }
}
