-- 0. BORRAR TABLA ANTIGUA SI EXISTE
--DROP TABLE IF EXISTS clientes;

-- 1. CREAR LA TABLA CLIENTES (Con Numero y Piso)
CREATE TABLE clientes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    apellidos VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    dni VARCHAR(20) UNIQUE,
    telefono VARCHAR(20),
    rol ENUM('admin', 'cliente') DEFAULT 'cliente',
    
    -- Dirección desglosada
    direccion VARCHAR(255), -- Calle / Vía
    numero VARCHAR(20),     -- Nuevo: Nº
    piso VARCHAR(20),       -- Nuevo: Piso/Puerta
    
    ciudad VARCHAR(100),
    codigo_postal VARCHAR(10),
    fecha_registro DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- 2. INSERTAR CLIENTES (Con tus DNI originales)
-- He añadido números y pisos inventados para rellenar los huecos

INSERT INTO clientes (nombre, apellidos, email, password, dni, telefono, rol, direccion, numero, piso, ciudad, codigo_postal) VALUES
('Juan', 'García López', 'juan.garcia@email.com', '1234', '44174833K', '600111222', 'admin', 'Calle Recogidas', '15', '2A', 'Granada', '18005'),
('María', 'Rodríguez Pérez', 'maria.rod@email.com', '1234', '42615152Q', '611222333', 'cliente', 'Av. Constitución', '20', '1º B', 'Granada', '18012'),
('Antonio', 'Fernández Ruiz', 'antonio.fer@email.com', '1234', '33569126M', '622333444', 'cliente', 'Calle Real', '45', 'Bajo', 'Armilla', '18100'),
('Laura', 'Sánchez Mota', 'laura.san@email.com', '1234', '23123455Z', '633444555', 'cliente', 'Camino de Ronda', '100', '3º D', 'Granada', '18003'),
('Carlos', 'Martínez Gómez', 'carlos.mar@email.com', '1234', '89926046W', '644555666', 'cliente', 'Calle Ancha', '12', '', 'Motril', '18600');