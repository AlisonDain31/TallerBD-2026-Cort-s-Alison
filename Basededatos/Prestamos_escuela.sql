CREATE DATABASE IF NOT EXISTS db_inventario;

USE db_inventario;


CREATE TABLE categorias (
    id_categoria INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL UNIQUE,
    descripcion VARCHAR(150) NULL
);


CREATE TABLE equipos (
    id_equipo INT AUTO_INCREMENT PRIMARY KEY,
    numero_inventario VARCHAR(50) NOT NULL UNIQUE,
    descripcion VARCHAR(150) NOT NULL,
    marca_modelo VARCHAR(100) NOT NULL,
    id_categoria INT NOT NULL,
    estado ENUM('Disponible', 'Prestado', 'Mantenimiento', 'Baja') NOT NULL DEFAULT 'Disponible',
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_equipo_categoria FOREIGN KEY (id_categoria)
        REFERENCES categorias(id_categoria) ON UPDATE CASCADE ON DELETE RESTRICT
);

CREATE TABLE solicitantes (
    id_solicitante INT AUTO_INCREMENT PRIMARY KEY,
    numero_empleado VARCHAR(30) NOT NULL UNIQUE,
    nombre_completo VARCHAR(120) NOT NULL,
    departamento VARCHAR(100) NOT NULL,
    activo BOOLEAN NOT NULL DEFAULT TRUE
);


CREATE TABLE usuarios_sistema (
    id_usuario INT AUTO_INCREMENT PRIMARY KEY,
    nombre_usuario VARCHAR(50) NOT NULL UNIQUE,
    contraseña_usuario VARCHAR(255) NOT NULL,
    nombre_completo VARCHAR(120) NOT NULL,
    rol ENUM('Administrador', 'Consulta') NOT NULL DEFAULT 'Consulta',
    activo BOOLEAN NOT NULL DEFAULT TRUE
);


CREATE TABLE prestamos (
    id_prestamo INT AUTO_INCREMENT PRIMARY KEY,
    id_equipo INT NOT NULL,
    id_solicitante INT NOT NULL,
    id_usuario_entrega INT NOT NULL,
    id_usuario_recibe INT NULL,
    fecha_prestamo DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    fecha_devolucion_prevista DATETIME NOT NULL,
    fecha_devolucion_real DATETIME NULL,
    condicion_devolucion ENUM('Buena', 'Con falla', 'Pendiente') NOT NULL DEFAULT 'Pendiente',
    observaciones TEXT NULL,
    CONSTRAINT fk_prestamo_equipo FOREIGN KEY (id_equipo)
        REFERENCES equipos(id_equipo) ON UPDATE CASCADE ON DELETE RESTRICT,
    CONSTRAINT fk_prestamo_solicitante FOREIGN KEY (id_solicitante)
        REFERENCES solicitantes(id_solicitante) ON UPDATE CASCADE ON DELETE RESTRICT,
    CONSTRAINT fk_prestamo_usuario_entrega FOREIGN KEY (id_usuario_entrega)
        REFERENCES usuarios_sistema(id_usuario) ON UPDATE CASCADE ON DELETE RESTRICT,
    CONSTRAINT fk_prestamo_usuario_recibe FOREIGN KEY (id_usuario_recibe)
        REFERENCES usuarios_sistema(id_usuario) ON UPDATE CASCADE ON DELETE RESTRICT
);

