-- 0. LIMPIEZA (Si ya existían, las borramos para evitar conflictos)
DROP TABLE IF EXISTS detalle_ventas;
DROP TABLE IF EXISTS ventas;

-- 1. TABLA VENTAS (Cabecera del ticket)
CREATE TABLE ventas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_cliente INT NOT NULL,
    fecha DATETIME DEFAULT CURRENT_TIMESTAMP,
    total DECIMAL(10, 2) DEFAULT 0.00,
    -- Relación: Si borras un cliente, se borran sus ventas
    FOREIGN KEY (id_cliente) REFERENCES clientes(id) ON DELETE CASCADE
);

-- 2. TABLA DETALLE_VENTAS (Líneas del ticket)
CREATE TABLE detalle_ventas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_venta INT NOT NULL,
    id_producto INT NOT NULL,
    cantidad INT NOT NULL,
    precio_unitario DECIMAL(10, 2) NOT NULL,
    -- Cálculo automático del subtotal (cantidad * precio)
    subtotal DECIMAL(10, 2) GENERATED ALWAYS AS (cantidad * precio_unitario) STORED,
    
    FOREIGN KEY (id_venta) REFERENCES ventas(id) ON DELETE CASCADE,
    FOREIGN KEY (id_producto) REFERENCES productos(id)
);

-- ==========================================
-- VENTAS DE EJEMPLO (Usando tus clientes y productos reales)
-- ==========================================

-- VENTA 1: Cliente "Juan García" (Buscamos su ID por email para evitar errores)
SET @id_cliente1 = (SELECT id FROM clientes WHERE email = 'juan.garcia@email.com' LIMIT 1);

-- Solo insertamos si existe el cliente
INSERT INTO ventas (id_cliente, fecha, total) VALUES (@id_cliente1, '2023-11-01 10:30:00', 0);
SET @id_v1 = LAST_INSERT_ID();

-- Detalles Venta 1:
-- 2 unidades del Producto 1 (Ventana Corredera Aluminio Blanco) a 120.00€
INSERT INTO detalle_ventas (id_venta, id_producto, cantidad, precio_unitario) VALUES (@id_v1, 1, 2, 120.00);
-- 1 unidad del Producto 2 (Ventana Corredera Aluminio Plata) a 130.00€
INSERT INTO detalle_ventas (id_venta, id_producto, cantidad, precio_unitario) VALUES (@id_v1, 2, 1, 130.00);

-- Calculamos el total de la Venta 1
UPDATE ventas SET total = (SELECT IFNULL(SUM(subtotal), 0) FROM detalle_ventas WHERE id_venta = @id_v1) WHERE id = @id_v1;


-- VENTA 2: Cliente "María Rodríguez" (Buscamos su ID por email)
SET @id_cliente2 = (SELECT id FROM clientes WHERE email = 'maria.rod@email.com' LIMIT 1);

-- Solo insertamos si existe el cliente
INSERT INTO ventas (id_cliente, fecha, total) VALUES (@id_cliente2, '2023-11-05 16:45:00', 0);
SET @id_v2 = LAST_INSERT_ID();

-- Detalles Venta 2:
-- 1 unidad del Producto 19 (Pérgola Aluminio Redondo) a 450.00€
INSERT INTO detalle_ventas (id_venta, id_producto, cantidad, precio_unitario) VALUES (@id_v2, 19, 1, 450.00);

-- Calculamos el total de la Venta 2
UPDATE ventas SET total = (SELECT IFNULL(SUM(subtotal), 0) FROM detalle_ventas WHERE id_venta = @id_v2) WHERE id = @id_v2;