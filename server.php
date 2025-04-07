<?php
require __DIR__ . '/vendor/autoload.php';

use React\EventLoop\Loop;
use React\MySQL\Factory;
use App\Database;
use App\Server;

// Configuración de la base de datos (formato URI para ReactPHP)
$host = 'localhost';
$port = 3306;
$user = 'root';
$password = '';
$dbname = 'Restaurante';

// URI para ReactPHP MySQL: usuario:contraseña@host:puerto/basedatos
$uri = "$user:$password@$host:$port/$dbname";

// Obtener el loop de eventos
$loop = Loop::get();

try {
    // Crear conexión a la base de datos
    $factory = new Factory($loop);
    $db = $factory->createLazyConnection($uri);

    echo "Conectando a la base de datos...\n";

    // Iniciar el servidor HTTP
    $server = new Server($loop, $db);
    $server->run();

    echo "Servidor event-driven iniciado en http://127.0.0.1:8000\n";
    echo "Presiona Ctrl+C para detener el servidor\n";
} catch (Exception $e) {
    echo 'Error: ' . $e->getMessage() . PHP_EOL;
    exit(1);
}

// Ejecutar el loop de eventos
$loop->run();
