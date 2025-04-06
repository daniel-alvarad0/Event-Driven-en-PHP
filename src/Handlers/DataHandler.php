<?php
namespace App\Handlers;

use Psr\Http\Message\ServerRequestInterface;
use React\Http\Message\Response;
use App\Database\DB;
use React\EventLoop\LoopInterface;

class DataHandler
{
    public static function handle(ServerRequestInterface $request, LoopInterface $loop)
    {
        $method = $request->getMethod();
        $conn = (new DB($loop))->getConnection();

        switch ($method) {
            case 'GET':
                return self::getReservas($conn);
            case 'POST':
                return self::crearReserva($request, $conn);
            case 'PUT':
                return self::actualizarReserva($request, $conn);
            case 'DELETE':
                return self::eliminarReserva($request, $conn);
            default:
                return new Response(405, ['Content-Type' => 'text/plain'], 'Método no permitido');
        }
    }

    private static function getReservas($conn)
    {
        return $conn->query('SELECT * FROM reservas')
            ->then(function ($result) {
                return new Response(
                    200,
                    ['Content-Type' => 'application/json'],
                    json_encode($result->resultRows)
                );
            }, function ($error) {
                return new Response(500, [], 'Error al obtener reservas');
            });
    }

    private static function crearReserva($request, $conn)
    {
        $params = json_decode((string) $request->getBody(), true);
        if (!$params || !isset($params['nombre'], $params['fecha'], $params['hora'], $params['personas'])) {
            return new Response(400, [], 'Datos incompletos');
        }

        $sql = 'INSERT INTO reservas (nombre, fecha, hora, personas) VALUES (?, ?, ?, ?)';
        return $conn->query($sql, [$params['nombre'], $params['fecha'], $params['hora'], $params['personas']])
            ->then(fn() => new Response(201, [], 'Reserva creada'))
            ->otherwise(fn() => new Response(500, [], 'Error al crear reserva'));
    }

    private static function actualizarReserva($request, $conn)
    {
        $params = json_decode((string) $request->getBody(), true);
        if (!$params || !isset($params['id'], $params['nombre'], $params['fecha'], $params['hora'], $params['personas'])) {
            return new Response(400, [], 'Datos incompletos');
        }

        $sql = 'UPDATE reservas SET nombre = ?, fecha = ?, hora = ?, personas = ? WHERE id = ?';
        return $conn->query($sql, [
            $params['nombre'],
            $params['fecha'],
            $params['hora'],
            $params['personas'],
            $params['id']
        ])
        ->then(fn() => new Response(200, [], 'Reserva actualizada'))
        ->otherwise(fn() => new Response(500, [], 'Error al actualizar reserva'));
    }

    private static function eliminarReserva($request, $conn)
    {
        $params = json_decode((string) $request->getBody(), true);
        if (!$params || !isset($params['id'])) {
            return new Response(400, [], 'ID no proporcionado');
        }

        return $conn->query('DELETE FROM reservas WHERE id = ?', [$params['id']])
            ->then(fn() => new Response(200, [], 'Reserva eliminada'))
            ->otherwise(fn() => new Response(500, [], 'Error al eliminar reserva'));
    }
}
