create database rr_hh;

use rr_hh;

-- Tabla departamento (independiente)
CREATE TABLE departamento (
    id_d INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(60) NOT NULL UNIQUE,
    ubicacion VARCHAR(100) NOT NULL
);

-- Tabla empleado (con FK a departamento)
CREATE TABLE empleado (
    id_e INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    apellido VARCHAR(100) NOT NULL,
    fecha_nacimiento DATE NOT NULL,
    edad INT GENERATED ALWAYS AS (TIMESTAMPDIFF(YEAR, fecha_nacimiento, CURDATE())),
    foto VARCHAR(255), -- Puede ser NULL si no es obligatoria al inicio
    id_departamento INT, -- Clave foránea
    FOREIGN KEY (id_departamento) REFERENCES departamento(id_d)
);

-- Contrato sigue igual, referenciando empleado.id_e
CREATE TABLE contrato (
    id_c INT PRIMARY KEY NOT NULL, -- Generalmente es id_empleado
    fecha_inicio DATE NOT NULL,
    fecha_fin DATE NOT NULL,
    salario_base DECIMAL(12,2) NOT NULL,
    antiguedad INT GENERATED ALWAYS AS (TIMESTAMPDIFF(YEAR, fecha_inicio, CURDATE())),
    bono DECIMAL(12,2) GENERATED ALWAYS AS (0.05 * salario_base * antiguedad),
    duracion INT GENERATED ALWAYS AS (TIMESTAMPDIFF(MONTH, fecha_inicio, fecha_fin)),
    FOREIGN KEY(id_c) REFERENCES empleado(id_e) ON DELETE CASCADE ON UPDATE CASCADE -- Añadir ON DELETE/UPDATE
);

-- Usuario sigue igual (pero recuerda hashear password)
CREATE TABLE usuario (
    id_u INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(30) NOT NULL UNIQUE, -- Username debería ser único
    password VARCHAR(255) NOT NULL, -- Para password hasheada
    rol VARCHAR(60) NOT NULL DEFAULT 'usuario' -- Rol por defecto
);