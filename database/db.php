<?php
namespace App\Database;

use React\EventLoop\LoopInterface;
use React\MySQL\Factory;

class DB {
    private $connection;

    public function __construct(LoopInterface $loop) {
        $factory = new Factory($loop);

        // Datos de conexión a tu servidor MySQL desde phpMyAdmin
        $this->connection = $factory->createLazyConnection(
            'root:@127.0.0.1/restaurante'
        );
    }

    public function getConnection() {
        return $this->connection;
    }
}
