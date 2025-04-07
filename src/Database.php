<?php

namespace App;

use React\MySQL\Factory;
use React\MySQL\ConnectionInterface;
use React\EventLoop\LoopInterface;

class Database
{
    private $connection;

    /**
     * Crea una instancia de la clase Database
     * 
     * @param string $uri URI de conexión a la base de datos (formato user:pass@host:port/dbname)
     * @param LoopInterface|null $loop Loop de eventos de ReactPHP
     */
    public function __construct(string $uri, LoopInterface $loop = null)
    {
        $factory = new Factory($loop);
        $this->connection = $factory->createLazyConnection($uri);
    }

    /**
     * Obtiene la conexión a la base de datos
     * 
     * @return ConnectionInterface
     */
    public function getConnection(): ConnectionInterface
    {
        return $this->connection;
    }
}
