<?php

namespace App\Models;

use React\MySQL\ConnectionInterface;
use React\MySQL\QueryResult;
use React\Promise\PromiseInterface;

class ReservaModel
{
    private $db;

    public function __construct(ConnectionInterface $db)
    {
        $this->db = $db;
    }

    public function getAll(): PromiseInterface
    {
        return $this->db->query('SELECT * FROM reservas')
            ->then(function (QueryResult $result) {
                return $result->resultRows;
            });
    }

    public function getById(int $id): PromiseInterface
    {
        return $this->db->query('SELECT * FROM reservas WHERE id = ?', [$id])
            ->then(function (QueryResult $result) {
                return $result->resultRows[0] ?? null;
            });
    }

    public function create(array $data): PromiseInterface
    {
        return $this->db->query(
            'INSERT INTO reservas (nombre, fecha, hora, personas) VALUES (?, ?, ?, ?)',
            [$data['nombre'], $data['fecha'], $data['hora'], $data['personas']]
        );
    }

    public function update(int $id, array $data): PromiseInterface
    {
        // Construir la consulta dinámicamente basada en los campos proporcionados
        $updateFields = [];
        $params = [];

        if (isset($data['nombre'])) {
            $updateFields[] = 'nombre = ?';
            $params[] = $data['nombre'];
        }

        if (isset($data['fecha'])) {
            $updateFields[] = 'fecha = ?';
            $params[] = $data['fecha'];
        }

        if (isset($data['hora'])) {
            $updateFields[] = 'hora = ?';
            $params[] = $data['hora'];
        }

        if (isset($data['personas'])) {
            $updateFields[] = 'personas = ?';
            $params[] = $data['personas'];
        }

        // Si no hay campos para actualizar, devolver un error
        if (empty($updateFields)) {
            return \React\Promise\reject(new \Exception('No se proporcionaron campos válidos para actualizar'));
        }

        // Añadir el ID al final de los parámetros
        $params[] = $id;

        $query = 'UPDATE reservas SET ' . implode(', ', $updateFields) . ' WHERE id = ?';

        return $this->db->query($query, $params);
    }

    public function delete(int $id): PromiseInterface
    {
        return $this->db->query('DELETE FROM reservas WHERE id = ?', [$id]);
    }
}
