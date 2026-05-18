CREATE DATABASE IF NOT EXISTS jarbera_douce CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE jarbera_douce;

DROP TABLE IF EXISTS detalle_carrito;
DROP TABLE IF EXISTS carritos;
DROP TABLE IF EXISTS favoritos;
DROP TABLE IF EXISTS detalle_ventas;
DROP TABLE IF EXISTS ventas;
DROP TABLE IF EXISTS productos;
DROP TABLE IF EXISTS categorias;
DROP TABLE IF EXISTS usuarios;

CREATE TABLE usuarios (
    id_usuario INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(120) NOT NULL,
    correo VARCHAR(150) NOT NULL UNIQUE,
    contrasena VARCHAR(255) NOT NULL,
    rol ENUM('admin','cliente') NOT NULL DEFAULT 'cliente',
    estado ENUM('activo','inactivo','bloqueado') NOT NULL DEFAULT 'activo',
    codigo_2fa VARCHAR(10) NULL,
    estado_2fa TINYINT(1) NOT NULL DEFAULT 0,
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE categorias (
    id_categoria INT AUTO_INCREMENT PRIMARY KEY,
    nombre_categoria VARCHAR(100) NOT NULL,
    descripcion TEXT NULL
) ENGINE=InnoDB;

CREATE TABLE productos (
    id_producto INT AUTO_INCREMENT PRIMARY KEY,
    id_categoria INT NOT NULL,
    nombre VARCHAR(120) NOT NULL,
    marca VARCHAR(100) NOT NULL DEFAULT 'Jarbera Douce',
    descripcion TEXT NULL,
    precio DECIMAL(10,2) NOT NULL,
    stock INT NOT NULL DEFAULT 0,
    imagen VARCHAR(255) DEFAULT 'assets/img/default.svg',
    destacado TINYINT(1) NOT NULL DEFAULT 0,
    estado TINYINT(1) NOT NULL DEFAULT 1,
    FOREIGN KEY (id_categoria) REFERENCES categorias(id_categoria)
        ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB;

CREATE TABLE ventas (
    id_venta INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT NOT NULL,
    fecha TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    total DECIMAL(10,2) NOT NULL,
    estado_venta ENUM('pendiente','pagada','entregada','cancelada') NOT NULL DEFAULT 'pagada',
    metodo_pago VARCHAR(40) NOT NULL DEFAULT 'QR',
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario)
        ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB;

CREATE TABLE detalle_ventas (
    id_detalle INT AUTO_INCREMENT PRIMARY KEY,
    id_venta INT NOT NULL,
    id_producto INT NOT NULL,
    cantidad INT NOT NULL,
    subtotal DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (id_venta) REFERENCES ventas(id_venta)
        ON UPDATE CASCADE ON DELETE CASCADE,
    FOREIGN KEY (id_producto) REFERENCES productos(id_producto)
        ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB;

CREATE TABLE favoritos (
    id_favorito INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT NOT NULL,
    id_producto INT NOT NULL,
    fecha TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uq_favorito (id_usuario, id_producto),
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario)
        ON UPDATE CASCADE ON DELETE CASCADE,
    FOREIGN KEY (id_producto) REFERENCES productos(id_producto)
        ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE carritos (
    id_carrito INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT NOT NULL,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    estado ENUM('activo','comprado','abandonado') NOT NULL DEFAULT 'activo',
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario)
        ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE detalle_carrito (
    id_detalle_carrito INT AUTO_INCREMENT PRIMARY KEY,
    id_carrito INT NOT NULL,
    id_producto INT NOT NULL,
    cantidad INT NOT NULL,
    UNIQUE KEY uq_carrito_producto (id_carrito, id_producto),
    FOREIGN KEY (id_carrito) REFERENCES carritos(id_carrito)
        ON UPDATE CASCADE ON DELETE CASCADE,
    FOREIGN KEY (id_producto) REFERENCES productos(id_producto)
        ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB;

INSERT INTO usuarios (nombre, correo, contrasena, rol, estado) VALUES
('Administrador Jarbera', 'admin@jarberadouce.com', '$2y$12$yxBGmmDukgqTSALo/9RTweUsl1UOuN58/Q9PYVIgLJ/rYoPi7baNW', 'admin', 'activo'),
('Cliente Demo', 'cliente@demo.com', '$2y$12$dg.8TwE3luopkfS8dc/dgux8UVi8TcHy9bwY2WuRetX6EgNZgQque', 'cliente', 'activo');

INSERT INTO categorias (nombre_categoria, descripcion) VALUES
('Ramos', 'Ramos artesanales de flores de limpiapipas para regalar.'),
('Rosas', 'Rosas individuales y arreglos románticos personalizados.'),
('Tulipanes', 'Tulipanes de colores pastel hechos a mano.'),
('Girasoles', 'Girasoles eternos, brillantes y llenos de color.'),
('Personalizados', 'Diseños con iniciales, colores y mensajes a elección.'),
('Regalos', 'Cajas sorpresa y detalles especiales para ocasiones únicas.');

INSERT INTO productos (id_categoria, nombre, marca, descripcion, precio, stock, imagen, destacado, estado) VALUES
(1, 'Ramo Dulce Encanto', 'Jarbera Douce', 'Ramo mixto con flores de limpiapipas, envoltorio pastel y tarjeta personalizada.', 180.00, 15, 'assets/img/default.svg', 1, 1),
(2, 'Rosa Individual', 'Jarbera Douce', 'Rosa eterna hecha con limpiapipas. Color a elección.', 35.00, 40, 'assets/img/default.svg', 1, 1),
(3, 'Tulipanes Pastel', 'Jarbera Douce', 'Tres tulipanes de limpiapipas con tallo flexible y acabado delicado.', 95.00, 18, 'assets/img/default.svg', 1, 1),
(4, 'Girasoles de Amor', 'Jarbera Douce', 'Arreglo de girasoles eternos para iluminar cualquier espacio.', 150.00, 12, 'assets/img/default.svg', 1, 1),
(5, 'Mini Arreglo Feliz', 'Jarbera Douce', 'Arreglo pequeño para escritorio, repisa o detalle de amistad.', 85.00, 25, 'assets/img/default.svg', 0, 1),
(6, 'Caja Sorpresa', 'Jarbera Douce', 'Caja decorada con flores, tarjeta y diseño personalizado.', 220.00, 10, 'assets/img/default.svg', 1, 1);