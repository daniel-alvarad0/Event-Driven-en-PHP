<?php

namespace App\Controllers;

use React\Http\Message\Response;
use React\Promise\PromiseInterface;
use App\Models\ReservaModel;

class DataController
{
    private $reservaModel;

    public function __construct(ReservaModel $reservaModel)
    {
        $this->reservaModel = $reservaModel;
    }

    // Obtener todas las reservas
    public function getAll(): PromiseInterface
    {
        return $this->reservaModel->getAll()
            ->then(function ($reservas) {
                return new Response(
                    200,
                    ['Content-Type' => 'application/json'],
                    json_encode($reservas)
                );
            })
            ->then(null, function (\Exception $error) {
                return new Response(
                    500,
                    ['Content-Type' => 'application/json'],
                    json_encode(['error' => 'Error al obtener las reservas: ' . $error->getMessage()])
                );
            });
    }

    // Obtener una reserva por ID
    public function getById(array $params): PromiseInterface
    {
        $id = (int) $params['id'];

        return $this->reservaModel->getById($id)
            ->then(function ($reserva) {
                if (empty($reserva)) {
                    return new Response(
                        404,
                        ['Content-Type' => 'application/json'],
                        json_encode(['error' => 'Reserva no encontrada'])
                    );
                }

                return new Response(
                    200,
                    ['Content-Type' => 'application/json'],
                    json_encode($reserva)
                );
            })
            ->then(null, function (\Exception $error) {
                return new Response(
                    500,
                    ['Content-Type' => 'application/json'],
                    json_encode(['error' => 'Error al obtener la reserva: ' . $error->getMessage()])
                );
            });
    }

    // Crear una nueva reserva
    public function create(array $params): PromiseInterface
    {
        $data = $params['data'] ?? [];

        // Validar datos
        if (empty($data['nombre']) || empty($data['fecha']) || empty($data['hora']) || empty($data['personas'])) {
            return \React\Promise\resolve(new Response(
                400,
                ['Content-Type' => 'application/json'],
                json_encode(['error' => 'Datos incompletos. Se requiere nombre, fecha, hora y número de personas.'])
            ));
        }

        return $this->reservaModel->create($data)
            ->then(function ($result) {
                return new Response(
                    201,
                    ['Content-Type' => 'application/json'],
                    json_encode([
                        'message' => 'Reserva creada exitosamente',
                        'id' => $result->insertId
                    ])
                );
            })
            ->then(null, function (\Exception $error) {
                return new Response(
                    500,
                    ['Content-Type' => 'application/json'],
                    json_encode(['error' => 'Error al crear la reserva: ' . $error->getMessage()])
                );
            });
    }

    // Actualizar una reserva existente
    public function update(array $params): PromiseInterface
    {
        $id = (int) $params['id'];
        $data = $params['data'] ?? [];

        // Validar datos
        if (empty($data)) {
            return \React\Promise\resolve(new Response(
                400,
                ['Content-Type' => 'application/json'],
                json_encode(['error' => 'No se proporcionaron datos para actualizar'])
            ));
        }

        return $this->reservaModel->update($id, $data)
            ->then(function ($result) use ($id) {
                if ($result->affectedRows === 0) {
                    return new Response(
                        404,
                        ['Content-Type' => 'application/json'],
                        json_encode(['error' => 'Reserva no encontrada o sin cambios'])
                    );
                }

                return new Response(
                    200,
                    ['Content-Type' => 'application/json'],
                    json_encode([
                        'message' => 'Reserva actualizada exitosamente',
                        'id' => $id
                    ])
                );
            })
            ->then(null, function (\Exception $error) {
                return new Response(
                    500,
                    ['Content-Type' => 'application/json'],
                    json_encode(['error' => 'Error al actualizar la reserva: ' . $error->getMessage()])
                );
            });
    }

    // Eliminar una reserva
    public function delete(array $params): PromiseInterface
    {
        $id = (int) $params['id'];

        return $this->reservaModel->delete($id)
            ->then(function ($result) {
                if ($result->affectedRows === 0) {
                    return new Response(
                        404,
                        ['Content-Type' => 'application/json'],
                        json_encode(['error' => 'Reserva no encontrada'])
                    );
                }

                return new Response(
                    200,
                    ['Content-Type' => 'application/json'],
                    json_encode(['message' => 'Reserva eliminada exitosamente'])
                );
            })
            ->then(null, function (\Exception $error) {
                return new Response(
                    500,
                    ['Content-Type' => 'application/json'],
                    json_encode(['error' => 'Error al eliminar la reserva: ' . $error->getMessage()])
                );
            });
    }
}
