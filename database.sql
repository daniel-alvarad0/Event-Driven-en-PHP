CREATE DATABASE Restaurante;

USE Restaurante;

CREATE TABLE reservas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    fecha DATE NOT NULL,
    hora TIME NOT NULL,
    personas INT NOT NULL
);

INSERT INTO reservas (nombre, fecha, hora, personas) 
VALUES ('Carlos López', '2025-04-10', '19:30:00', 4);

INSERT INTO reservas (nombre, fecha, hora, personas) 
VALUES ('María Fernández', '2025-04-11', '20:00:00', 2);

INSERT INTO reservas (nombre, fecha, hora, personas) 
VALUES ('José Ramírez', '2025-04-12', '18:45:00', 6);

INSERT INTO reservas (nombre, fecha, hora, personas) 
VALUES ('Ana Castillo', '2025-04-13', '21:00:00', 3);

INSERT INTO reservas (nombre, fecha, hora, personas) 
VALUES ('Luis Méndez', '2025-04-14', '19:00:00', 5);
