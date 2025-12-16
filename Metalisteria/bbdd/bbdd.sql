-- ==============================================================================
-- 1. CONFIGURACIÓN INICIAL Y LIMPIEZA DE BASE DE DATOS
-- ==============================================================================
DROP DATABASE IF EXISTS metalisteria;
CREATE DATABASE metalisteria;
USE metalisteria;

-- Desactivamos chequeo de claves foráneas temporalmente para evitar errores al crear tablas
SET FOREIGN_KEY_CHECKS = 0;

-- ==============================================================================
-- 2. CREACIÓN DE TABLAS (ESTRUCTURA)
-- ==============================================================================

-- 2.1 TABLA MATERIALES
CREATE TABLE materiales (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(50) NOT NULL
);

-- 2.2 TABLA CATEGORIAS
CREATE TABLE categorias (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(50) NOT NULL
);

-- 2.3 TABLA PRODUCTOS
CREATE TABLE productos (
  id INT AUTO_INCREMENT PRIMARY KEY,
  referencia VARCHAR(40) NOT NULL,
  nombre VARCHAR(150) NOT NULL,
  descripcion TEXT,
  precio DECIMAL(10, 2) DEFAULT 0.00,
  imagen_url VARCHAR(255),
  id_material INT NOT NULL,
  id_categoria INT NOT NULL,
  medidas VARCHAR(50),
  color VARCHAR(30),
  FOREIGN KEY (id_material) REFERENCES materiales(id),
  FOREIGN KEY (id_categoria) REFERENCES categorias(id)
);

-- 2.4 TABLA CLIENTES
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
    numero VARCHAR(20),     -- Nº
    piso VARCHAR(20),       -- Piso/Puerta
    
    ciudad VARCHAR(100),
    provincia VARCHAR(100) DEFAULT 'Granada',
    codigo_postal VARCHAR(10),
    fecha_registro DATETIME DEFAULT CURRENT_TIMESTAMP,
    activo TINYINT(1) DEFAULT 1
);

-- 2.5 TABLA CARRITO
CREATE TABLE carrito (
    id INT AUTO_INCREMENT PRIMARY KEY,
    cliente_id INT NOT NULL,     -- Quién compra
    producto_id INT NOT NULL,    -- Qué compra
    cantidad INT DEFAULT 1,
    fecha_agregado TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (cliente_id) REFERENCES clientes(id) ON DELETE CASCADE,
    FOREIGN KEY (producto_id) REFERENCES productos(id) ON DELETE CASCADE
);

-- 2.6 TABLA VENTAS (Cabecera)
CREATE TABLE ventas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_cliente INT NOT NULL,
    fecha DATETIME DEFAULT CURRENT_TIMESTAMP,
    total DECIMAL(10, 2) DEFAULT 0.00,
    FOREIGN KEY (id_cliente) REFERENCES clientes(id) ON DELETE CASCADE
);

-- 2.7 TABLA DETALLE_VENTAS (Líneas)
CREATE TABLE detalle_ventas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_venta INT NOT NULL,
    id_producto INT NOT NULL,
    cantidad INT NOT NULL,
    precio_unitario DECIMAL(10, 2) NOT NULL,
    -- Cálculo automático del subtotal
    subtotal DECIMAL(10, 2) GENERATED ALWAYS AS (cantidad * precio_unitario) STORED,
    
    FOREIGN KEY (id_venta) REFERENCES ventas(id) ON DELETE CASCADE,
    FOREIGN KEY (id_producto) REFERENCES productos(id)
);

-- Reactivamos chequeo de claves foráneas
SET FOREIGN_KEY_CHECKS = 1;

-- ==============================================================================
-- 3. INSERCIÓN DE DATOS MAESTROS (Materiales y Categorías)
-- ==============================================================================

INSERT INTO materiales (nombre) VALUES
('Aluminio'),
('PVC'),
('Hierro');

INSERT INTO categorias (nombre) VALUES
('Ventanas'),     -- id 1
('Balcones'),     -- id 2
('Rejas'),        -- id 3
('Escaleras'),    -- id 4
('Barandillas'),  -- id 5
('Pérgolas');     -- id 6

-- ==============================================================================
-- 4. INSERCIÓN DE CLIENTES
-- ==============================================================================

INSERT INTO clientes (nombre, apellidos, email, password, dni, telefono, rol, direccion, numero, piso, ciudad, provincia, codigo_postal) VALUES
('Juan', 'García López', 'juan.garcia@email.com', '1234', '44174833K', '600111222', 'admin', 'Calle Recogidas', '15', '2A', 'Granada', 'Granada', '18005'),
('María', 'Rodríguez Pérez', 'maria.rod@email.com', '1234', '42615152Q', '611222333', 'cliente', 'Av. Constitución', '20', '1º B', 'Granada', 'Granada', '18012'),
('Antonio', 'Fernández Ruiz', 'antonio.fer@email.com', '1234', '33569126M', '622333444', 'cliente', 'Calle Real', '45', 'Bajo', 'Armilla', 'Granada', '18100'),
('Laura', 'Sánchez Mota', 'laura.san@email.com', '1234', '23123455Z', '633444555', 'cliente', 'Camino de Ronda', '100', '3º D', 'Granada', 'Granada', '18003'),
('Carlos', 'Martínez Gómez', 'carlos.mar@email.com', '1234', '89926046W', '644555666', 'cliente', 'Calle Ancha', '12', '', 'Motril', 'Granada', '18600');

-- ==============================================================================
-- 5. INSERCIÓN DE TODOS LOS PRODUCTOS
-- ==============================================================================

-- 1. Ventana corredera de 2 hojas (Aluminio)
INSERT INTO productos (referencia, nombre, descripcion, precio, imagen_url, id_material, id_categoria, medidas, color) VALUES
('ALU-VEN-COR2H', 'Ventana corredera de 2 hojas (Aluminio)', 'Ventana de aluminio ligera y funcional, sistema de apertura corredera en acabado Blanco.', 120.00, 'imagenes/ALU-VEN-COR2H.jpg', 1, 1, '80x100', 'Blanco'),
('ALU-VEN-COR2H', 'Ventana corredera de 2 hojas (Aluminio)', 'Ventana de aluminio ligera y funcional, sistema de apertura corredera en acabado Plata.', 130.00, 'imagenes/ALU-VEN-COR2H.jpg', 1, 1, '80x100', 'Plata'),
('ALU-VEN-COR2H', 'Ventana corredera de 2 hojas (Aluminio)', 'Ventana de aluminio ligera y funcional, sistema de apertura corredera en acabado Marrón.', 130.00, 'imagenes/ALU-VEN-COR2H.jpg', 1, 1, '80x100', 'Marrón'),
('ALU-VEN-COR2H', 'Ventana corredera de 2 hojas (Aluminio)', 'Ventana de aluminio ligera y funcional, sistema de apertura corredera en acabado Blanco.', 140.00, 'imagenes/ALU-VEN-COR2H.jpg', 1, 1, '100x120', 'Blanco'),
('ALU-VEN-COR2H', 'Ventana corredera de 2 hojas (Aluminio)', 'Ventana de aluminio ligera y funcional, sistema de apertura corredera en acabado Plata.', 150.00, 'imagenes/ALU-VEN-COR2H.jpg', 1, 1, '100x120', 'Plata'),
('ALU-VEN-COR2H', 'Ventana corredera de 2 hojas (Aluminio)', 'Ventana de aluminio ligera y funcional, sistema de apertura corredera en acabado Marrón.', 150.00, 'imagenes/ALU-VEN-COR2H.jpg', 1, 1, '100x120', 'Marrón'),
('ALU-VEN-COR2H', 'Ventana corredera de 2 hojas (Aluminio)', 'Ventana de aluminio ligera y funcional, sistema de apertura corredera en acabado Blanco.', 160.00, 'imagenes/ALU-VEN-COR2H.jpg', 1, 1, '120x140', 'Blanco'),
('ALU-VEN-COR2H', 'Ventana corredera de 2 hojas (Aluminio)', 'Ventana de aluminio ligera y funcional, sistema de apertura corredera en acabado Plata.', 170.00, 'imagenes/ALU-VEN-COR2H.jpg', 1, 1, '120x140', 'Plata'),
('ALU-VEN-COR2H', 'Ventana corredera de 2 hojas (Aluminio)', 'Ventana de aluminio ligera y funcional, sistema de apertura corredera en acabado Marrón.', 170.00, 'imagenes/ALU-VEN-COR2H.jpg', 1, 1, '120x140', 'Marrón');

-- 2. Ventana corredera de 2 hojas (PVC)
INSERT INTO productos (referencia, nombre, descripcion, precio, imagen_url, id_material, id_categoria, medidas, color) VALUES
('PVC-VEN-COR2H', 'Ventana corredera de 2 hojas (PVC)', 'Ventana de PVC con gran aislamiento térmico y acústico en acabado Blanco.', 180.00, 'imagenes/PVC-VEN-COR2H.jpg', 2, 1, '80x100', 'Blanco'),
('PVC-VEN-COR2H', 'Ventana corredera de 2 hojas (PVC)', 'Ventana de PVC con gran aislamiento térmico y acústico en acabado Plata.', 190.00, 'imagenes/PVC-VEN-COR2H.jpg', 2, 1, '80x100', 'Plata'),
('PVC-VEN-COR2H', 'Ventana corredera de 2 hojas (PVC)', 'Ventana de PVC con gran aislamiento térmico y acústico en acabado Marrón.', 190.00, 'imagenes/PVC-VEN-COR2H.jpg', 2, 1, '80x100', 'Marrón'),
('PVC-VEN-COR2H', 'Ventana corredera de 2 hojas (PVC)', 'Ventana de PVC con gran aislamiento térmico y acústico en acabado Blanco.', 200.00, 'imagenes/PVC-VEN-COR2H.jpg', 2, 1, '100x120', 'Blanco'),
('PVC-VEN-COR2H', 'Ventana corredera de 2 hojas (PVC)', 'Ventana de PVC con gran aislamiento térmico y acústico en acabado Plata.', 210.00, 'imagenes/PVC-VEN-COR2H.jpg', 2, 1, '100x120', 'Plata'),
('PVC-VEN-COR2H', 'Ventana corredera de 2 hojas (PVC)', 'Ventana de PVC con gran aislamiento térmico y acústico en acabado Marrón.', 210.00, 'imagenes/PVC-VEN-COR2H.jpg', 2, 1, '100x120', 'Marrón'),
('PVC-VEN-COR2H', 'Ventana corredera de 2 hojas (PVC)', 'Ventana de PVC con gran aislamiento térmico y acústico en acabado Blanco.', 220.00, 'imagenes/PVC-VEN-COR2H.jpg', 2, 1, '120x140', 'Blanco'),
('PVC-VEN-COR2H', 'Ventana corredera de 2 hojas (PVC)', 'Ventana de PVC con gran aislamiento térmico y acústico en acabado Plata.', 230.00, 'imagenes/PVC-VEN-COR2H.jpg', 2, 1, '120x140', 'Plata'),
('PVC-VEN-COR2H', 'Ventana corredera de 2 hojas (PVC)', 'Ventana de PVC con gran aislamiento térmico y acústico en acabado Marrón.', 230.00, 'imagenes/PVC-VEN-COR2H.jpg', 2, 1, '120x140', 'Marrón');

-- 3. Balcón corredera de 2 hojas (Aluminio)
INSERT INTO productos (referencia, nombre, descripcion, precio, imagen_url, id_material, id_categoria, medidas, color) VALUES
('ALU-BAL-COR2H', 'Balcón corredera de 2 hojas (Aluminio)', 'Balconera amplia de aluminio, ideal para terrazas, en acabado Blanco.', 300.00, 'imagenes/ALU-BAL-COR2H.jpg', 1, 2, '200x210', 'Blanco'),
('ALU-BAL-COR2H', 'Balcón corredera de 2 hojas (Aluminio)', 'Balconera amplia de aluminio, ideal para terrazas, en acabado Plata.', 320.00, 'imagenes/ALU-BAL-COR2H.jpg', 1, 2, '200x210', 'Plata'),
('ALU-BAL-COR2H', 'Balcón corredera de 2 hojas (Aluminio)', 'Balconera amplia de aluminio, ideal para terrazas, en acabado Marrón.', 320.00, 'imagenes/ALU-BAL-COR2H.jpg', 1, 2, '200x210', 'Marrón'),
('ALU-BAL-COR2H', 'Balcón corredera de 2 hojas (Aluminio)', 'Balconera amplia de aluminio, ideal para terrazas, en acabado Blanco.', 350.00, 'imagenes/ALU-BAL-COR2H.jpg', 1, 2, '220x220', 'Blanco'),
('ALU-BAL-COR2H', 'Balcón corredera de 2 hojas (Aluminio)', 'Balconera amplia de aluminio, ideal para terrazas, en acabado Plata.', 370.00, 'imagenes/ALU-BAL-COR2H.jpg', 1, 2, '220x220', 'Plata'),
('ALU-BAL-COR2H', 'Balcón corredera de 2 hojas (Aluminio)', 'Balconera amplia de aluminio, ideal para terrazas, en acabado Marrón.', 370.00, 'imagenes/ALU-BAL-COR2H.jpg', 1, 2, '220x220', 'Marrón'),
('ALU-BAL-COR2H', 'Balcón corredera de 2 hojas (Aluminio)', 'Balconera amplia de aluminio, ideal para terrazas, en acabado Blanco.', 400.00, 'imagenes/ALU-BAL-COR2H.jpg', 1, 2, '240x230', 'Blanco'),
('ALU-BAL-COR2H', 'Balcón corredera de 2 hojas (Aluminio)', 'Balconera amplia de aluminio, ideal para terrazas, en acabado Plata.', 420.00, 'imagenes/ALU-BAL-COR2H.jpg', 1, 2, '240x230', 'Plata'),
('ALU-BAL-COR2H', 'Balcón corredera de 2 hojas (Aluminio)', 'Balconera amplia de aluminio, ideal para terrazas, en acabado Marrón.', 420.00, 'imagenes/ALU-BAL-COR2H.jpg', 1, 2, '240x230', 'Marrón');

-- 4. Balcón corredera de 2 hojas (PVC)
INSERT INTO productos (referencia, nombre, descripcion, precio, imagen_url, id_material, id_categoria, medidas, color) VALUES
('PVC-BAL-COR2H', 'Balcón corredera de 2 hojas (PVC)', 'Balconera de PVC de alta eficiencia energética en acabado Blanco.', 450.00, 'imagenes/PVC-BAL-COR2H.jpg', 2, 2, '200x210', 'Blanco'),
('PVC-BAL-COR2H', 'Balcón corredera de 2 hojas (PVC)', 'Balconera de PVC de alta eficiencia energética en acabado Plata.', 480.00, 'imagenes/PVC-BAL-COR2H.jpg', 2, 2, '200x210', 'Plata'),
('PVC-BAL-COR2H', 'Balcón corredera de 2 hojas (PVC)', 'Balconera de PVC de alta eficiencia energética en acabado Marrón.', 480.00, 'imagenes/PVC-BAL-COR2H.jpg', 2, 2, '200x210', 'Marrón'),
('PVC-BAL-COR2H', 'Balcón corredera de 2 hojas (PVC)', 'Balconera de PVC de alta eficiencia energética en acabado Blanco.', 500.00, 'imagenes/PVC-BAL-COR2H.jpg', 2, 2, '220x220', 'Blanco'),
('PVC-BAL-COR2H', 'Balcón corredera de 2 hojas (PVC)', 'Balconera de PVC de alta eficiencia energética en acabado Plata.', 530.00, 'imagenes/PVC-BAL-COR2H.jpg', 2, 2, '220x220', 'Plata'),
('PVC-BAL-COR2H', 'Balcón corredera de 2 hojas (PVC)', 'Balconera de PVC de alta eficiencia energética en acabado Marrón.', 530.00, 'imagenes/PVC-BAL-COR2H.jpg', 2, 2, '220x220', 'Marrón'),
('PVC-BAL-COR2H', 'Balcón corredera de 2 hojas (PVC)', 'Balconera de PVC de alta eficiencia energética en acabado Blanco.', 550.00, 'imagenes/PVC-BAL-COR2H.jpg', 2, 2, '240x230', 'Blanco'),
('PVC-BAL-COR2H', 'Balcón corredera de 2 hojas (PVC)', 'Balconera de PVC de alta eficiencia energética en acabado Plata.', 580.00, 'imagenes/PVC-BAL-COR2H.jpg', 2, 2, '240x230', 'Plata'),
('PVC-BAL-COR2H', 'Balcón corredera de 2 hojas (PVC)', 'Balconera de PVC de alta eficiencia energética en acabado Marrón.', 580.00, 'imagenes/PVC-BAL-COR2H.jpg', 2, 2, '240x230', 'Marrón');

-- 5. Balcón corredera 3 hojas tricarril (Aluminio)
INSERT INTO productos (referencia, nombre, descripcion, precio, imagen_url, id_material, id_categoria, medidas, color) VALUES
('ALU-BAL-COR3H', 'Balcón corredera 3 hojas tricarril (Aluminio)', 'Sistema tricarril para máxima apertura, aluminio resistente en acabado Blanco.', 600.00, 'imagenes/ALU-BAL-COR3H.jpg', 1, 2, '300x210', 'Blanco'),
('ALU-BAL-COR3H', 'Balcón corredera 3 hojas tricarril (Aluminio)', 'Sistema tricarril para máxima apertura, aluminio resistente en acabado Plata.', 630.00, 'imagenes/ALU-BAL-COR3H.jpg', 1, 2, '300x210', 'Plata'),
('ALU-BAL-COR3H', 'Balcón corredera 3 hojas tricarril (Aluminio)', 'Sistema tricarril para máxima apertura, aluminio resistente en acabado Marrón.', 630.00, 'imagenes/ALU-BAL-COR3H.jpg', 1, 2, '300x210', 'Marrón'),
('ALU-BAL-COR3H', 'Balcón corredera 3 hojas tricarril (Aluminio)', 'Sistema tricarril para máxima apertura, aluminio resistente en acabado Blanco.', 650.00, 'imagenes/ALU-BAL-COR3H.jpg', 1, 2, '320x220', 'Blanco'),
('ALU-BAL-COR3H', 'Balcón corredera 3 hojas tricarril (Aluminio)', 'Sistema tricarril para máxima apertura, aluminio resistente en acabado Plata.', 680.00, 'imagenes/ALU-BAL-COR3H.jpg', 1, 2, '320x220', 'Plata'),
('ALU-BAL-COR3H', 'Balcón corredera 3 hojas tricarril (Aluminio)', 'Sistema tricarril para máxima apertura, aluminio resistente en acabado Marrón.', 680.00, 'imagenes/ALU-BAL-COR3H.jpg', 1, 2, '320x220', 'Marrón'),
('ALU-BAL-COR3H', 'Balcón corredera 3 hojas tricarril (Aluminio)', 'Sistema tricarril para máxima apertura, aluminio resistente en acabado Blanco.', 700.00, 'imagenes/ALU-BAL-COR3H.jpg', 1, 2, '340x230', 'Blanco'),
('ALU-BAL-COR3H', 'Balcón corredera 3 hojas tricarril (Aluminio)', 'Sistema tricarril para máxima apertura, aluminio resistente en acabado Plata.', 730.00, 'imagenes/ALU-BAL-COR3H.jpg', 1, 2, '340x230', 'Plata'),
('ALU-BAL-COR3H', 'Balcón corredera 3 hojas tricarril (Aluminio)', 'Sistema tricarril para máxima apertura, aluminio resistente en acabado Marrón.', 730.00, 'imagenes/ALU-BAL-COR3H.jpg', 1, 2, '340x230', 'Marrón');

-- 6. Balcón corredera 3 hojas tricarril (PVC)
INSERT INTO productos (referencia, nombre, descripcion, precio, imagen_url, id_material, id_categoria, medidas, color) VALUES
('PVC-BAL-COR3H', 'Balcón corredera 3 hojas tricarril (PVC)', 'Tricarril de PVC con refuerzo interno, gran aislamiento en acabado Blanco.', 750.00, 'imagenes/PVC-BAL-COR3H.jpg', 2, 2, '300x210', 'Blanco'),
('PVC-BAL-COR3H', 'Balcón corredera 3 hojas tricarril (PVC)', 'Tricarril de PVC con refuerzo interno, gran aislamiento en acabado Plata.', 780.00, 'imagenes/PVC-BAL-COR3H.jpg', 2, 2, '300x210', 'Plata'),
('PVC-BAL-COR3H', 'Balcón corredera 3 hojas tricarril (PVC)', 'Tricarril de PVC con refuerzo interno, gran aislamiento en acabado Marrón.', 780.00, 'imagenes/PVC-BAL-COR3H.jpg', 2, 2, '300x210', 'Marrón'),
('PVC-BAL-COR3H', 'Balcón corredera 3 hojas tricarril (PVC)', 'Tricarril de PVC con refuerzo interno, gran aislamiento en acabado Blanco.', 800.00, 'imagenes/PVC-BAL-COR3H.jpg', 2, 2, '320x220', 'Blanco'),
('PVC-BAL-COR3H', 'Balcón corredera 3 hojas tricarril (PVC)', 'Tricarril de PVC con refuerzo interno, gran aislamiento en acabado Plata.', 830.00, 'imagenes/PVC-BAL-COR3H.jpg', 2, 2, '320x220', 'Plata'),
('PVC-BAL-COR3H', 'Balcón corredera 3 hojas tricarril (PVC)', 'Tricarril de PVC con refuerzo interno, gran aislamiento en acabado Marrón.', 830.00, 'imagenes/PVC-BAL-COR3H.jpg', 2, 2, '320x220', 'Marrón'),
('PVC-BAL-COR3H', 'Balcón corredera 3 hojas tricarril (PVC)', 'Tricarril de PVC con refuerzo interno, gran aislamiento en acabado Blanco.', 850.00, 'imagenes/PVC-BAL-COR3H.jpg', 2, 2, '340x230', 'Blanco'),
('PVC-BAL-COR3H', 'Balcón corredera 3 hojas tricarril (PVC)', 'Tricarril de PVC con refuerzo interno, gran aislamiento en acabado Plata.', 880.00, 'imagenes/PVC-BAL-COR3H.jpg', 2, 2, '340x230', 'Plata'),
('PVC-BAL-COR3H', 'Balcón corredera 3 hojas tricarril (PVC)', 'Tricarril de PVC con refuerzo interno, gran aislamiento en acabado Marrón.', 880.00, 'imagenes/PVC-BAL-COR3H.jpg', 2, 2, '340x230', 'Marrón');

-- 7. Ventana fija (Aluminio)
INSERT INTO productos (referencia, nombre, descripcion, precio, imagen_url, id_material, id_categoria, medidas, color) VALUES
('ALU-VEN-FIJA', 'Ventana fija (Aluminio)', 'Ventana panorámica sin apertura para máxima entrada de luz en acabado Blanco.', 80.00, 'imagenes/ALU-VEN-FIJA.jpg', 1, 1, '80x100', 'Blanco'),
('ALU-VEN-FIJA', 'Ventana fija (Aluminio)', 'Ventana panorámica sin apertura para máxima entrada de luz en acabado Plata.', 90.00, 'imagenes/ALU-VEN-FIJA.jpg', 1, 1, '80x100', 'Plata'),
('ALU-VEN-FIJA', 'Ventana fija (Aluminio)', 'Ventana panorámica sin apertura para máxima entrada de luz en acabado Marrón.', 90.00, 'imagenes/ALU-VEN-FIJA.jpg', 1, 1, '80x100', 'Marrón'),
('ALU-VEN-FIJA', 'Ventana fija (Aluminio)', 'Ventana panorámica sin apertura para máxima entrada de luz en acabado Blanco.', 100.00, 'imagenes/ALU-VEN-FIJA.jpg', 1, 1, '100x100', 'Blanco'),
('ALU-VEN-FIJA', 'Ventana fija (Aluminio)', 'Ventana panorámica sin apertura para máxima entrada de luz en acabado Plata.', 110.00, 'imagenes/ALU-VEN-FIJA.jpg', 1, 1, '100x100', 'Plata'),
('ALU-VEN-FIJA', 'Ventana fija (Aluminio)', 'Ventana panorámica sin apertura para máxima entrada de luz en acabado Marrón.', 110.00, 'imagenes/ALU-VEN-FIJA.jpg', 1, 1, '100x100', 'Marrón'),
('ALU-VEN-FIJA', 'Ventana fija (Aluminio)', 'Ventana panorámica sin apertura para máxima entrada de luz en acabado Blanco.', 120.00, 'imagenes/ALU-VEN-FIJA.jpg', 1, 1, '120x120', 'Blanco'),
('ALU-VEN-FIJA', 'Ventana fija (Aluminio)', 'Ventana panorámica sin apertura para máxima entrada de luz en acabado Plata.', 130.00, 'imagenes/ALU-VEN-FIJA.jpg', 1, 1, '120x120', 'Plata'),
('ALU-VEN-FIJA', 'Ventana fija (Aluminio)', 'Ventana panorámica sin apertura para máxima entrada de luz en acabado Marrón.', 130.00, 'imagenes/ALU-VEN-FIJA.jpg', 1, 1, '120x120', 'Marrón');

-- 8. Ventana fija (PVC)
INSERT INTO productos (referencia, nombre, descripcion, precio, imagen_url, id_material, id_categoria, medidas, color) VALUES
('PVC-VEN-FIJA', 'Ventana fija (PVC)', 'Ventana fija de PVC, ideal para aislar zonas sin ventilación en acabado Blanco.', 100.00, 'imagenes/PVC-VEN-FIJA.jpg', 2, 1, '80x100', 'Blanco'),
('PVC-VEN-FIJA', 'Ventana fija (PVC)', 'Ventana fija de PVC, ideal para aislar zonas sin ventilación en acabado Plata.', 115.00, 'imagenes/PVC-VEN-FIJA.jpg', 2, 1, '80x100', 'Plata'),
('PVC-VEN-FIJA', 'Ventana fija (PVC)', 'Ventana fija de PVC, ideal para aislar zonas sin ventilación en acabado Marrón.', 115.00, 'imagenes/PVC-VEN-FIJA.jpg', 2, 1, '80x100', 'Marrón'),
('PVC-VEN-FIJA', 'Ventana fija (PVC)', 'Ventana fija de PVC, ideal para aislar zonas sin ventilación en acabado Blanco.', 120.00, 'imagenes/PVC-VEN-FIJA.jpg', 2, 1, '100x100', 'Blanco'),
('PVC-VEN-FIJA', 'Ventana fija (PVC)', 'Ventana fija de PVC, ideal para aislar zonas sin ventilación en acabado Plata.', 135.00, 'imagenes/PVC-VEN-FIJA.jpg', 2, 1, '100x100', 'Plata'),
('PVC-VEN-FIJA', 'Ventana fija (PVC)', 'Ventana fija de PVC, ideal para aislar zonas sin ventilación en acabado Marrón.', 135.00, 'imagenes/PVC-VEN-FIJA.jpg', 2, 1, '100x100', 'Marrón'),
('PVC-VEN-FIJA', 'Ventana fija (PVC)', 'Ventana fija de PVC, ideal para aislar zonas sin ventilación en acabado Blanco.', 140.00, 'imagenes/PVC-VEN-FIJA.jpg', 2, 1, '120x120', 'Blanco'),
('PVC-VEN-FIJA', 'Ventana fija (PVC)', 'Ventana fija de PVC, ideal para aislar zonas sin ventilación en acabado Plata.', 155.00, 'imagenes/PVC-VEN-FIJA.jpg', 2, 1, '120x120', 'Plata'),
('PVC-VEN-FIJA', 'Ventana fija (PVC)', 'Ventana fija de PVC, ideal para aislar zonas sin ventilación en acabado Marrón.', 155.00, 'imagenes/PVC-VEN-FIJA.jpg', 2, 1, '120x120', 'Marrón');

-- 9. Ventana abatible de 1 hoja (Aluminio)
INSERT INTO productos (referencia, nombre, descripcion, precio, imagen_url, id_material, id_categoria, medidas, color) VALUES
('ALU-VEN-ABA1H', 'Ventana abatible 1 hoja (Aluminio)', 'Ventana practicable de una hoja, apertura clásica en acabado Blanco.', 95.00, 'imagenes/ALU-VEN-ABA1H.jpg', 1, 1, '60x100', 'Blanco'),
('ALU-VEN-ABA1H', 'Ventana abatible 1 hoja (Aluminio)', 'Ventana practicable de una hoja, apertura clásica en acabado Plata.', 105.00, 'imagenes/ALU-VEN-ABA1H.jpg', 1, 1, '60x100', 'Plata'),
('ALU-VEN-ABA1H', 'Ventana abatible 1 hoja (Aluminio)', 'Ventana practicable de una hoja, apertura clásica en acabado Marrón.', 105.00, 'imagenes/ALU-VEN-ABA1H.jpg', 1, 1, '60x100', 'Marrón'),
('ALU-VEN-ABA1H', 'Ventana abatible 1 hoja (Aluminio)', 'Ventana practicable de una hoja, apertura clásica en acabado Blanco.', 115.00, 'imagenes/ALU-VEN-ABA1H.jpg', 1, 1, '70x120', 'Blanco'),
('ALU-VEN-ABA1H', 'Ventana abatible 1 hoja (Aluminio)', 'Ventana practicable de una hoja, apertura clásica en acabado Plata.', 125.00, 'imagenes/ALU-VEN-ABA1H.jpg', 1, 1, '70x120', 'Plata'),
('ALU-VEN-ABA1H', 'Ventana abatible 1 hoja (Aluminio)', 'Ventana practicable de una hoja, apertura clásica en acabado Marrón.', 125.00, 'imagenes/ALU-VEN-ABA1H.jpg', 1, 1, '70x120', 'Marrón'),
('ALU-VEN-ABA1H', 'Ventana abatible 1 hoja (Aluminio)', 'Ventana practicable de una hoja, apertura clásica en acabado Blanco.', 135.00, 'imagenes/ALU-VEN-ABA1H.jpg', 1, 1, '80x140', 'Blanco'),
('ALU-VEN-ABA1H', 'Ventana abatible 1 hoja (Aluminio)', 'Ventana practicable de una hoja, apertura clásica en acabado Plata.', 145.00, 'imagenes/ALU-VEN-ABA1H.jpg', 1, 1, '80x140', 'Plata'),
('ALU-VEN-ABA1H', 'Ventana abatible 1 hoja (Aluminio)', 'Ventana practicable de una hoja, apertura clásica en acabado Marrón.', 145.00, 'imagenes/ALU-VEN-ABA1H.jpg', 1, 1, '80x140', 'Marrón');

-- 10. Ventana abatible de 1 hoja (PVC)
INSERT INTO productos (referencia, nombre, descripcion, precio, imagen_url, id_material, id_categoria, medidas, color) VALUES
('PVC-VEN-ABA1H', 'Ventana abatible 1 hoja (PVC)', 'Ventana de PVC de una hoja, cierre a presión hermético en acabado Blanco.', 120.00, 'imagenes/PVC-VEN-ABA1H.jpg', 2, 1, '60x100', 'Blanco'),
('PVC-VEN-ABA1H', 'Ventana abatible 1 hoja (PVC)', 'Ventana de PVC de una hoja, cierre a presión hermético en acabado Plata.', 135.00, 'imagenes/PVC-VEN-ABA1H.jpg', 2, 1, '60x100', 'Plata'),
('PVC-VEN-ABA1H', 'Ventana abatible 1 hoja (PVC)', 'Ventana de PVC de una hoja, cierre a presión hermético en acabado Marrón.', 135.00, 'imagenes/PVC-VEN-ABA1H.jpg', 2, 1, '60x100', 'Marrón'),
('PVC-VEN-ABA1H', 'Ventana abatible 1 hoja (PVC)', 'Ventana de PVC de una hoja, cierre a presión hermético en acabado Blanco.', 140.00, 'imagenes/PVC-VEN-ABA1H.jpg', 2, 1, '70x120', 'Blanco'),
('PVC-VEN-ABA1H', 'Ventana abatible 1 hoja (PVC)', 'Ventana de PVC de una hoja, cierre a presión hermético en acabado Plata.', 155.00, 'imagenes/PVC-VEN-ABA1H.jpg', 2, 1, '70x120', 'Plata'),
('PVC-VEN-ABA1H', 'Ventana abatible 1 hoja (PVC)', 'Ventana de PVC de una hoja, cierre a presión hermético en acabado Marrón.', 155.00, 'imagenes/PVC-VEN-ABA1H.jpg', 2, 1, '70x120', 'Marrón'),
('PVC-VEN-ABA1H', 'Ventana abatible 1 hoja (PVC)', 'Ventana de PVC de una hoja, cierre a presión hermético en acabado Blanco.', 160.00, 'imagenes/PVC-VEN-ABA1H.jpg', 2, 1, '80x140', 'Blanco'),
('PVC-VEN-ABA1H', 'Ventana abatible 1 hoja (PVC)', 'Ventana de PVC de una hoja, cierre a presión hermético en acabado Plata.', 175.00, 'imagenes/PVC-VEN-ABA1H.jpg', 2, 1, '80x140', 'Plata'),
('PVC-VEN-ABA1H', 'Ventana abatible 1 hoja (PVC)', 'Ventana de PVC de una hoja, cierre a presión hermético en acabado Marrón.', 175.00, 'imagenes/PVC-VEN-ABA1H.jpg', 2, 1, '80x140', 'Marrón');

-- 11. Ventana abatible de 2 hojas (Aluminio)
INSERT INTO productos (referencia, nombre, descripcion, precio, imagen_url, id_material, id_categoria, medidas, color) VALUES
('ALU-VEN-ABA2H', 'Ventana abatible 2 hojas (Aluminio)', 'Ventana doble hoja con apertura central, ideal para dormitorios en acabado Blanco.', 160.00, 'imagenes/ALU-VEN-ABA2H.jpg', 1, 1, '120x100', 'Blanco'),
('ALU-VEN-ABA2H', 'Ventana abatible 2 hojas (Aluminio)', 'Ventana doble hoja con apertura central, ideal para dormitorios en acabado Plata.', 180.00, 'imagenes/ALU-VEN-ABA2H.jpg', 1, 1, '120x100', 'Plata'),
('ALU-VEN-ABA2H', 'Ventana abatible 2 hojas (Aluminio)', 'Ventana doble hoja con apertura central, ideal para dormitorios en acabado Marrón.', 180.00, 'imagenes/ALU-VEN-ABA2H.jpg', 1, 1, '120x100', 'Marrón'),
('ALU-VEN-ABA2H', 'Ventana abatible 2 hojas (Aluminio)', 'Ventana doble hoja con apertura central, ideal para dormitorios en acabado Blanco.', 190.00, 'imagenes/ALU-VEN-ABA2H.jpg', 1, 1, '140x120', 'Blanco'),
('ALU-VEN-ABA2H', 'Ventana abatible 2 hojas (Aluminio)', 'Ventana doble hoja con apertura central, ideal para dormitorios en acabado Plata.', 210.00, 'imagenes/ALU-VEN-ABA2H.jpg', 1, 1, '140x120', 'Plata'),
('ALU-VEN-ABA2H', 'Ventana abatible 2 hojas (Aluminio)', 'Ventana doble hoja con apertura central, ideal para dormitorios en acabado Marrón.', 210.00, 'imagenes/ALU-VEN-ABA2H.jpg', 1, 1, '140x120', 'Marrón'),
('ALU-VEN-ABA2H', 'Ventana abatible 2 hojas (Aluminio)', 'Ventana doble hoja con apertura central, ideal para dormitorios en acabado Blanco.', 220.00, 'imagenes/ALU-VEN-ABA2H.jpg', 1, 1, '160x140', 'Blanco'),
('ALU-VEN-ABA2H', 'Ventana abatible 2 hojas (Aluminio)', 'Ventana doble hoja con apertura central, ideal para dormitorios en acabado Plata.', 240.00, 'imagenes/ALU-VEN-ABA2H.jpg', 1, 1, '160x140', 'Plata'),
('ALU-VEN-ABA2H', 'Ventana abatible 2 hojas (Aluminio)', 'Ventana doble hoja con apertura central, ideal para dormitorios en acabado Marrón.', 240.00, 'imagenes/ALU-VEN-ABA2H.jpg', 1, 1, '160x140', 'Marrón');

-- 12. Ventana abatible 2 hojas con oscilobatiente (Aluminio)
INSERT INTO productos (referencia, nombre, descripcion, precio, imagen_url, id_material, id_categoria, medidas, color) VALUES
('ALU-VEN-ABA2H-OB', 'Ventana abatible 2 hojas con oscilobatiente (Aluminio)', 'Ventana premium con sistema oscilobatiente para ventilación segura en acabado Blanco.', 200.00, 'imagenes/ALU-VEN-ABA2H-OB.jpg', 1, 1, '120x100', 'Blanco'),
('ALU-VEN-ABA2H-OB', 'Ventana abatible 2 hojas con oscilobatiente (Aluminio)', 'Ventana premium con sistema oscilobatiente para ventilación segura en acabado Plata.', 220.00, 'imagenes/ALU-VEN-ABA2H-OB.jpg', 1, 1, '120x100', 'Plata'),
('ALU-VEN-ABA2H-OB', 'Ventana abatible 2 hojas con oscilobatiente (Aluminio)', 'Ventana premium con sistema oscilobatiente para ventilación segura en acabado Marrón.', 220.00, 'imagenes/ALU-VEN-ABA2H-OB.jpg', 1, 1, '120x100', 'Marrón'),
('ALU-VEN-ABA2H-OB', 'Ventana abatible 2 hojas con oscilobatiente (Aluminio)', 'Ventana premium con sistema oscilobatiente para ventilación segura en acabado Blanco.', 230.00, 'imagenes/ALU-VEN-ABA2H-OB.jpg', 1, 1, '140x120', 'Blanco'),
('ALU-VEN-ABA2H-OB', 'Ventana abatible 2 hojas con oscilobatiente (Aluminio)', 'Ventana premium con sistema oscilobatiente para ventilación segura en acabado Plata.', 250.00, 'imagenes/ALU-VEN-ABA2H-OB.jpg', 1, 1, '140x120', 'Plata'),
('ALU-VEN-ABA2H-OB', 'Ventana abatible 2 hojas con oscilobatiente (Aluminio)', 'Ventana premium con sistema oscilobatiente para ventilación segura en acabado Marrón.', 250.00, 'imagenes/ALU-VEN-ABA2H-OB.jpg', 1, 1, '140x120', 'Marrón'),
('ALU-VEN-ABA2H-OB', 'Ventana abatible 2 hojas con oscilobatiente (Aluminio)', 'Ventana premium con sistema oscilobatiente para ventilación segura en acabado Blanco.', 260.00, 'imagenes/ALU-VEN-ABA2H-OB.jpg', 1, 1, '160x140', 'Blanco'),
('ALU-VEN-ABA2H-OB', 'Ventana abatible 2 hojas con oscilobatiente (Aluminio)', 'Ventana premium con sistema oscilobatiente para ventilación segura en acabado Plata.', 280.00, 'imagenes/ALU-VEN-ABA2H-OB.jpg', 1, 1, '160x140', 'Plata'),
('ALU-VEN-ABA2H-OB', 'Ventana abatible 2 hojas con oscilobatiente (Aluminio)', 'Ventana premium con sistema oscilobatiente para ventilación segura en acabado Marrón.', 280.00, 'imagenes/ALU-VEN-ABA2H-OB.jpg', 1, 1, '160x140', 'Marrón');

-- 13. Ventana abatible de 2 hojas (PVC)
INSERT INTO productos (referencia, nombre, descripcion, precio, imagen_url, id_material, id_categoria, medidas, color) VALUES
('PVC-VEN-ABA2H', 'Ventana abatible 2 hojas (PVC)', 'Doble hoja de PVC con cierre reforzado, máximo silencio en acabado Blanco.', 210.00, 'imagenes/PVC-VEN-ABA2H.jpg', 2, 1, '120x100', 'Blanco'),
('PVC-VEN-ABA2H', 'Ventana abatible 2 hojas (PVC)', 'Doble hoja de PVC con cierre reforzado, máximo silencio en acabado Plata.', 230.00, 'imagenes/PVC-VEN-ABA2H.jpg', 2, 1, '120x100', 'Plata'),
('PVC-VEN-ABA2H', 'Ventana abatible 2 hojas (PVC)', 'Doble hoja de PVC con cierre reforzado, máximo silencio en acabado Marrón.', 230.00, 'imagenes/PVC-VEN-ABA2H.jpg', 2, 1, '120x100', 'Marrón'),
('PVC-VEN-ABA2H', 'Ventana abatible 2 hojas (PVC)', 'Doble hoja de PVC con cierre reforzado, máximo silencio en acabado Blanco.', 240.00, 'imagenes/PVC-VEN-ABA2H.jpg', 2, 1, '140x120', 'Blanco'),
('PVC-VEN-ABA2H', 'Ventana abatible 2 hojas (PVC)', 'Doble hoja de PVC con cierre reforzado, máximo silencio en acabado Plata.', 260.00, 'imagenes/PVC-VEN-ABA2H.jpg', 2, 1, '140x120', 'Plata'),
('PVC-VEN-ABA2H', 'Ventana abatible 2 hojas (PVC)', 'Doble hoja de PVC con cierre reforzado, máximo silencio en acabado Marrón.', 260.00, 'imagenes/PVC-VEN-ABA2H.jpg', 2, 1, '140x120', 'Marrón'),
('PVC-VEN-ABA2H', 'Ventana abatible 2 hojas (PVC)', 'Doble hoja de PVC con cierre reforzado, máximo silencio en acabado Blanco.', 270.00, 'imagenes/PVC-VEN-ABA2H.jpg', 2, 1, '160x140', 'Blanco'),
('PVC-VEN-ABA2H', 'Ventana abatible 2 hojas (PVC)', 'Doble hoja de PVC con cierre reforzado, máximo silencio en acabado Plata.', 290.00, 'imagenes/PVC-VEN-ABA2H.jpg', 2, 1, '160x140', 'Plata'),
('PVC-VEN-ABA2H', 'Ventana abatible 2 hojas (PVC)', 'Doble hoja de PVC con cierre reforzado, máximo silencio en acabado Marrón.', 290.00, 'imagenes/PVC-VEN-ABA2H.jpg', 2, 1, '160x140', 'Marrón');

-- 14. Ventana abatible 2 hojas con oscilobatiente (PVC)
INSERT INTO productos (referencia, nombre, descripcion, precio, imagen_url, id_material, id_categoria, medidas, color) VALUES
('PVC-VEN-ABA2H-OB', 'Ventana abatible 2 hojas con oscilobatiente (PVC)', 'La mejor ventana de PVC, oscilobatiente y hermética en acabado Blanco.', 250.00, 'imagenes/PVC-VEN-ABA2H-OB.jpg', 2, 1, '120x100', 'Blanco'),
('PVC-VEN-ABA2H-OB', 'Ventana abatible 2 hojas con oscilobatiente (PVC)', 'La mejor ventana de PVC, oscilobatiente y hermética en acabado Plata.', 275.00, 'imagenes/PVC-VEN-ABA2H-OB.jpg', 2, 1, '120x100', 'Plata'),
('PVC-VEN-ABA2H-OB', 'Ventana abatible 2 hojas con oscilobatiente (PVC)', 'La mejor ventana de PVC, oscilobatiente y hermética en acabado Marrón.', 275.00, 'imagenes/PVC-VEN-ABA2H-OB.jpg', 2, 1, '120x100', 'Marrón'),
('PVC-VEN-ABA2H-OB', 'Ventana abatible 2 hojas con oscilobatiente (PVC)', 'La mejor ventana de PVC, oscilobatiente y hermética en acabado Blanco.', 280.00, 'imagenes/PVC-VEN-ABA2H-OB.jpg', 2, 1, '140x120', 'Blanco'),
('PVC-VEN-ABA2H-OB', 'Ventana abatible 2 hojas con oscilobatiente (PVC)', 'La mejor ventana de PVC, oscilobatiente y hermética en acabado Plata.', 305.00, 'imagenes/PVC-VEN-ABA2H-OB.jpg', 2, 1, '140x120', 'Plata'),
('PVC-VEN-ABA2H-OB', 'Ventana abatible 2 hojas con oscilobatiente (PVC)', 'La mejor ventana de PVC, oscilobatiente y hermética en acabado Marrón.', 305.00, 'imagenes/PVC-VEN-ABA2H-OB.jpg', 2, 1, '140x120', 'Marrón'),
('PVC-VEN-ABA2H-OB', 'Ventana abatible 2 hojas con oscilobatiente (PVC)', 'La mejor ventana de PVC, oscilobatiente y hermética en acabado Blanco.', 310.00, 'imagenes/PVC-VEN-ABA2H-OB.jpg', 2, 1, '160x140', 'Blanco'),
('PVC-VEN-ABA2H-OB', 'Ventana abatible 2 hojas con oscilobatiente (PVC)', 'La mejor ventana de PVC, oscilobatiente y hermética en acabado Plata.', 335.00, 'imagenes/PVC-VEN-ABA2H-OB.jpg', 2, 1, '160x140', 'Plata'),
('PVC-VEN-ABA2H-OB', 'Ventana abatible 2 hojas con oscilobatiente (PVC)', 'La mejor ventana de PVC, oscilobatiente y hermética en acabado Marrón.', 335.00, 'imagenes/PVC-VEN-ABA2H-OB.jpg', 2, 1, '160x140', 'Marrón');

-- 15. Balcón abatible de 1 hoja (Aluminio)
INSERT INTO productos (referencia, nombre, descripcion, precio, imagen_url, id_material, id_categoria, medidas, color) VALUES
('ALU-BAL-ABA1H', 'Balcón abatible 1 hoja (Aluminio)', 'Puerta balconera de una hoja, paso cómodo y resistente en acabado Blanco.', 280.00, 'imagenes/ALU-BAL-ABA1H.jpg', 1, 2, '60x100', 'Blanco'),
('ALU-BAL-ABA1H', 'Balcón abatible 1 hoja (Aluminio)', 'Puerta balconera de una hoja, paso cómodo y resistente en acabado Plata.', 300.00, 'imagenes/ALU-BAL-ABA1H.jpg', 1, 2, '60x100', 'Plata'),
('ALU-BAL-ABA1H', 'Balcón abatible 1 hoja (Aluminio)', 'Puerta balconera de una hoja, paso cómodo y resistente en acabado Marrón.', 300.00, 'imagenes/ALU-BAL-ABA1H.jpg', 1, 2, '60x100', 'Marrón'),
('ALU-BAL-ABA1H', 'Balcón abatible 1 hoja (Aluminio)', 'Puerta balconera de una hoja, paso cómodo y resistente en acabado Blanco.', 310.00, 'imagenes/ALU-BAL-ABA1H.jpg', 1, 2, '70x120', 'Blanco'),
('ALU-BAL-ABA1H', 'Balcón abatible 1 hoja (Aluminio)', 'Puerta balconera de una hoja, paso cómodo y resistente en acabado Plata.', 330.00, 'imagenes/ALU-BAL-ABA1H.jpg', 1, 2, '70x120', 'Plata'),
('ALU-BAL-ABA1H', 'Balcón abatible 1 hoja (Aluminio)', 'Puerta balconera de una hoja, paso cómodo y resistente en acabado Marrón.', 330.00, 'imagenes/ALU-BAL-ABA1H.jpg', 1, 2, '70x120', 'Marrón'),
('ALU-BAL-ABA1H', 'Balcón abatible 1 hoja (Aluminio)', 'Puerta balconera de una hoja, paso cómodo y resistente en acabado Blanco.', 340.00, 'imagenes/ALU-BAL-ABA1H.jpg', 1, 2, '80x140', 'Blanco'),
('ALU-BAL-ABA1H', 'Balcón abatible 1 hoja (Aluminio)', 'Puerta balconera de una hoja, paso cómodo y resistente en acabado Plata.', 360.00, 'imagenes/ALU-BAL-ABA1H.jpg', 1, 2, '80x140', 'Plata'),
('ALU-BAL-ABA1H', 'Balcón abatible 1 hoja (Aluminio)', 'Puerta balconera de una hoja, paso cómodo y resistente en acabado Marrón.', 360.00, 'imagenes/ALU-BAL-ABA1H.jpg', 1, 2, '80x140', 'Marrón');

-- 16. Balcón abatible de 1 hoja (PVC)
INSERT INTO productos (referencia, nombre, descripcion, precio, imagen_url, id_material, id_categoria, medidas, color) VALUES
('PVC-BAL-ABA1H', 'Balcón abatible 1 hoja (PVC)', 'Puerta balconera de PVC, aislamiento superior en acabado Blanco.', 320.00, 'imagenes/PVC-BAL-ABA1H.jpg', 2, 2, '60x100', 'Blanco'),
('PVC-BAL-ABA1H', 'Balcón abatible 1 hoja (PVC)', 'Puerta balconera de PVC, aislamiento superior en acabado Plata.', 340.00, 'imagenes/PVC-BAL-ABA1H.jpg', 2, 2, '60x100', 'Plata'),
('PVC-BAL-ABA1H', 'Balcón abatible 1 hoja (PVC)', 'Puerta balconera de PVC, aislamiento superior en acabado Marrón.', 340.00, 'imagenes/PVC-BAL-ABA1H.jpg', 2, 2, '60x100', 'Marrón'),
('PVC-BAL-ABA1H', 'Balcón abatible 1 hoja (PVC)', 'Puerta balconera de PVC, aislamiento superior en acabado Blanco.', 350.00, 'imagenes/PVC-BAL-ABA1H.jpg', 2, 2, '70x120', 'Blanco'),
('PVC-BAL-ABA1H', 'Balcón abatible 1 hoja (PVC)', 'Puerta balconera de PVC, aislamiento superior en acabado Plata.', 370.00, 'imagenes/PVC-BAL-ABA1H.jpg', 2, 2, '70x120', 'Plata'),
('PVC-BAL-ABA1H', 'Balcón abatible 1 hoja (PVC)', 'Puerta balconera de PVC, aislamiento superior en acabado Marrón.', 370.00, 'imagenes/PVC-BAL-ABA1H.jpg', 2, 2, '70x120', 'Marrón'),
('PVC-BAL-ABA1H', 'Balcón abatible 1 hoja (PVC)', 'Puerta balconera de PVC, aislamiento superior en acabado Blanco.', 380.00, 'imagenes/PVC-BAL-ABA1H.jpg', 2, 2, '80x140', 'Blanco'),
('PVC-BAL-ABA1H', 'Balcón abatible 1 hoja (PVC)', 'Puerta balconera de PVC, aislamiento superior en acabado Plata.', 400.00, 'imagenes/PVC-BAL-ABA1H.jpg', 2, 2, '80x140', 'Plata'),
('PVC-BAL-ABA1H', 'Balcón abatible 1 hoja (PVC)', 'Puerta balconera de PVC, aislamiento superior en acabado Marrón.', 400.00, 'imagenes/PVC-BAL-ABA1H.jpg', 2, 2, '80x140', 'Marrón');

-- 17. Balcón abatible de 2 hojas (Aluminio)
INSERT INTO productos (referencia, nombre, descripcion, precio, imagen_url, id_material, id_categoria, medidas, color) VALUES
('ALU-BAL-ABA2H', 'Balcón abatible 2 hojas (Aluminio)', 'Balconera doble de aluminio, apertura amplia en acabado Blanco.', 450.00, 'imagenes/ALU-BAL-ABA2H.jpg', 1, 2, '120x100', 'Blanco'),
('ALU-BAL-ABA2H', 'Balcón abatible 2 hojas (Aluminio)', 'Balconera doble de aluminio, apertura amplia en acabado Plata.', 480.00, 'imagenes/ALU-BAL-ABA2H.jpg', 1, 2, '120x100', 'Plata'),
('ALU-BAL-ABA2H', 'Balcón abatible 2 hojas (Aluminio)', 'Balconera doble de aluminio, apertura amplia en acabado Marrón.', 480.00, 'imagenes/ALU-BAL-ABA2H.jpg', 1, 2, '120x100', 'Marrón'),
('ALU-BAL-ABA2H', 'Balcón abatible 2 hojas (Aluminio)', 'Balconera doble de aluminio, apertura amplia en acabado Blanco.', 480.00, 'imagenes/ALU-BAL-ABA2H.jpg', 1, 2, '140x120', 'Blanco'),
('ALU-BAL-ABA2H', 'Balcón abatible 2 hojas (Aluminio)', 'Balconera doble de aluminio, apertura amplia en acabado Plata.', 510.00, 'imagenes/ALU-BAL-ABA2H.jpg', 1, 2, '140x120', 'Plata'),
('ALU-BAL-ABA2H', 'Balcón abatible 2 hojas (Aluminio)', 'Balconera doble de aluminio, apertura amplia en acabado Marrón.', 510.00, 'imagenes/ALU-BAL-ABA2H.jpg', 1, 2, '140x120', 'Marrón'),
('ALU-BAL-ABA2H', 'Balcón abatible 2 hojas (Aluminio)', 'Balconera doble de aluminio, apertura amplia en acabado Blanco.', 510.00, 'imagenes/ALU-BAL-ABA2H.jpg', 1, 2, '160x140', 'Blanco'),
('ALU-BAL-ABA2H', 'Balcón abatible 2 hojas (Aluminio)', 'Balconera doble de aluminio, apertura amplia en acabado Plata.', 540.00, 'imagenes/ALU-BAL-ABA2H.jpg', 1, 2, '160x140', 'Plata'),
('ALU-BAL-ABA2H', 'Balcón abatible 2 hojas (Aluminio)', 'Balconera doble de aluminio, apertura amplia en acabado Marrón.', 540.00, 'imagenes/ALU-BAL-ABA2H.jpg', 1, 2, '160x140', 'Marrón');

-- 18. Balcón abatible de 2 hojas (PVC)
INSERT INTO productos (referencia, nombre, descripcion, precio, imagen_url, id_material, id_categoria, medidas, color) VALUES
('PVC-BAL-ABA2H', 'Balcón abatible 2 hojas (PVC)', 'Balcón doble hoja de PVC, robusto y elegante en acabado Blanco.', 500.00, 'imagenes/PVC-BAL-ABA2H.jpg', 2, 2, '120x100', 'Blanco'),
('PVC-BAL-ABA2H', 'Balcón abatible 2 hojas (PVC)', 'Balcón doble hoja de PVC, robusto y elegante en acabado Plata.', 530.00, 'imagenes/PVC-BAL-ABA2H.jpg', 2, 2, '120x100', 'Plata'),
('PVC-BAL-ABA2H', 'Balcón abatible 2 hojas (PVC)', 'Balcón doble hoja de PVC, robusto y elegante en acabado Marrón.', 530.00, 'imagenes/PVC-BAL-ABA2H.jpg', 2, 2, '120x100', 'Marrón'),
('PVC-BAL-ABA2H', 'Balcón abatible 2 hojas (PVC)', 'Balcón doble hoja de PVC, robusto y elegante en acabado Blanco.', 530.00, 'imagenes/PVC-BAL-ABA2H.jpg', 2, 2, '140x120', 'Blanco'),
('PVC-BAL-ABA2H', 'Balcón abatible 2 hojas (PVC)', 'Balcón doble hoja de PVC, robusto y elegante en acabado Plata.', 560.00, 'imagenes/PVC-BAL-ABA2H.jpg', 2, 2, '140x120', 'Plata'),
('PVC-BAL-ABA2H', 'Balcón abatible 2 hojas (PVC)', 'Balcón doble hoja de PVC, robusto y elegante en acabado Marrón.', 560.00, 'imagenes/PVC-BAL-ABA2H.jpg', 2, 2, '140x120', 'Marrón'),
('PVC-BAL-ABA2H', 'Balcón abatible 2 hojas (PVC)', 'Balcón doble hoja de PVC, robusto y elegante en acabado Blanco.', 560.00, 'imagenes/PVC-BAL-ABA2H.jpg', 2, 2, '160x140', 'Blanco'),
('PVC-BAL-ABA2H', 'Balcón abatible 2 hojas (PVC)', 'Balcón doble hoja de PVC, robusto y elegante en acabado Plata.', 590.00, 'imagenes/PVC-BAL-ABA2H.jpg', 2, 2, '160x140', 'Plata'),
('PVC-BAL-ABA2H', 'Balcón abatible 2 hojas (PVC)', 'Balcón doble hoja de PVC, robusto y elegante en acabado Marrón.', 590.00, 'imagenes/PVC-BAL-ABA2H.jpg', 2, 2, '160x140', 'Marrón');

-- 19. Pérgola de aluminio redondo
INSERT INTO productos (referencia, nombre, descripcion, precio, imagen_url, id_material, id_categoria, medidas, color) VALUES
('ALU-PERG-RED', 'Pérgola aluminio tubo redondo', 'Pérgola resistente ideal para jardín, estructura tubular en acabado Blanco.', 450.00, 'imagenes/ALU-PERG-RED.jpg', 1, 6, '200x300', 'Blanco'),
('ALU-PERG-RED', 'Pérgola aluminio tubo redondo', 'Pérgola resistente ideal para jardín, estructura tubular en acabado Plata.', 480.00, 'imagenes/ALU-PERG-RED.jpg', 1, 6, '200x300', 'Plata'),
('ALU-PERG-RED', 'Pérgola aluminio tubo redondo', 'Pérgola resistente ideal para jardín, estructura tubular en acabado Marrón.', 480.00, 'imagenes/ALU-PERG-RED.jpg', 1, 6, '200x300', 'Marrón'),
('ALU-PERG-RED', 'Pérgola aluminio tubo redondo', 'Pérgola resistente ideal para jardín, estructura tubular en acabado Blanco.', 500.00, 'imagenes/ALU-PERG-RED.jpg', 1, 6, '250x350', 'Blanco'),
('ALU-PERG-RED', 'Pérgola aluminio tubo redondo', 'Pérgola resistente ideal para jardín, estructura tubular en acabado Plata.', 530.00, 'imagenes/ALU-PERG-RED.jpg', 1, 6, '250x350', 'Plata'),
('ALU-PERG-RED', 'Pérgola aluminio tubo redondo', 'Pérgola resistente ideal para jardín, estructura tubular en acabado Marrón.', 530.00, 'imagenes/ALU-PERG-RED.jpg', 1, 6, '250x350', 'Marrón'),
('ALU-PERG-RED', 'Pérgola aluminio tubo redondo', 'Pérgola resistente ideal para jardín, estructura tubular en acabado Blanco.', 550.00, 'imagenes/ALU-PERG-RED.jpg', 1, 6, '300x400', 'Blanco'),
('ALU-PERG-RED', 'Pérgola aluminio tubo redondo', 'Pérgola resistente ideal para jardín, estructura tubular en acabado Plata.', 580.00, 'imagenes/ALU-PERG-RED.jpg', 1, 6, '300x400', 'Plata'),
('ALU-PERG-RED', 'Pérgola aluminio tubo redondo', 'Pérgola resistente ideal para jardín, estructura tubular en acabado Marrón.', 580.00, 'imagenes/ALU-PERG-RED.jpg', 1, 6, '300x400', 'Marrón');

-- 20. Pérgola de PVC cuadrado
INSERT INTO productos (referencia, nombre, descripcion, precio, imagen_url, id_material, id_categoria, medidas, color) VALUES
('PVC-PERG-CUA', 'Pérgola PVC tubo cuadrado', 'Pérgola de PVC de diseño moderno con líneas rectas en acabado Blanco.', 460.00, 'imagenes/PVC-PERG-CUA.jpg', 2, 6, '200x300', 'Blanco'),
('PVC-PERG-CUA', 'Pérgola PVC tubo cuadrado', 'Pérgola de PVC de diseño moderno con líneas rectas en acabado Plata.', 490.00, 'imagenes/PVC-PERG-CUA.jpg', 2, 6, '200x300', 'Plata'),
('PVC-PERG-CUA', 'Pérgola PVC tubo cuadrado', 'Pérgola de PVC de diseño moderno con líneas rectas en acabado Marrón.', 490.00, 'imagenes/PVC-PERG-CUA.jpg', 2, 6, '200x300', 'Marrón'),
('PVC-PERG-CUA', 'Pérgola PVC tubo cuadrado', 'Pérgola de PVC de diseño moderno con líneas rectas en acabado Blanco.', 510.00, 'imagenes/PVC-PERG-CUA.jpg', 2, 6, '250x350', 'Blanco'),
('PVC-PERG-CUA', 'Pérgola PVC tubo cuadrado', 'Pérgola de PVC de diseño moderno con líneas rectas en acabado Plata.', 540.00, 'imagenes/PVC-PERG-CUA.jpg', 2, 6, '250x350', 'Plata'),
('PVC-PERG-CUA', 'Pérgola PVC tubo cuadrado', 'Pérgola de PVC de diseño moderno con líneas rectas en acabado Marrón.', 540.00, 'imagenes/PVC-PERG-CUA.jpg', 2, 6, '250x350', 'Marrón'),
('PVC-PERG-CUA', 'Pérgola PVC tubo cuadrado', 'Pérgola de PVC de diseño moderno con líneas rectas en acabado Blanco.', 560.00, 'imagenes/PVC-PERG-CUA.jpg', 2, 6, '300x400', 'Blanco'),
('PVC-PERG-CUA', 'Pérgola PVC tubo cuadrado', 'Pérgola de PVC de diseño moderno con líneas rectas en acabado Plata.', 590.00, 'imagenes/PVC-PERG-CUA.jpg', 2, 6, '300x400', 'Plata'),
('PVC-PERG-CUA', 'Pérgola PVC tubo cuadrado', 'Pérgola de PVC de diseño moderno con líneas rectas en acabado Marrón.', 590.00, 'imagenes/PVC-PERG-CUA.jpg', 2, 6, '300x400', 'Marrón');

-- 21. Pérgola de aluminio cuadrado
INSERT INTO productos (referencia, nombre, descripcion, precio, imagen_url, id_material, id_categoria, medidas, color) VALUES
('ALU-PERG-CUA', 'Pérgola aluminio tubo cuadrado', 'Pérgola de aluminio minimalista, estructura cuadrada reforzada en acabado Blanco.', 470.00, 'imagenes/ALU-PERG-CUA.jpg', 1, 6, '200x300', 'Blanco'),
('ALU-PERG-CUA', 'Pérgola aluminio tubo cuadrado', 'Pérgola de aluminio minimalista, estructura cuadrada reforzada en acabado Plata.', 500.00, 'imagenes/ALU-PERG-CUA.jpg', 1, 6, '200x300', 'Plata'),
('ALU-PERG-CUA', 'Pérgola aluminio tubo cuadrado', 'Pérgola de aluminio minimalista, estructura cuadrada reforzada en acabado Marrón.', 500.00, 'imagenes/ALU-PERG-CUA.jpg', 1, 6, '200x300', 'Marrón'),
('ALU-PERG-CUA', 'Pérgola aluminio tubo cuadrado', 'Pérgola de aluminio minimalista, estructura cuadrada reforzada en acabado Blanco.', 520.00, 'imagenes/ALU-PERG-CUA.jpg', 1, 6, '250x350', 'Blanco'),
('ALU-PERG-CUA', 'Pérgola aluminio tubo cuadrado', 'Pérgola de aluminio minimalista, estructura cuadrada reforzada en acabado Plata.', 550.00, 'imagenes/ALU-PERG-CUA.jpg', 1, 6, '250x350', 'Plata'),
('ALU-PERG-CUA', 'Pérgola aluminio tubo cuadrado', 'Pérgola de aluminio minimalista, estructura cuadrada reforzada en acabado Marrón.', 550.00, 'imagenes/ALU-PERG-CUA.jpg', 1, 6, '250x350', 'Marrón'),
('ALU-PERG-CUA', 'Pérgola aluminio tubo cuadrado', 'Pérgola de aluminio minimalista, estructura cuadrada reforzada en acabado Blanco.', 570.00, 'imagenes/ALU-PERG-CUA.jpg', 1, 6, '300x400', 'Blanco'),
('ALU-PERG-CUA', 'Pérgola aluminio tubo cuadrado', 'Pérgola de aluminio minimalista, estructura cuadrada reforzada en acabado Plata.', 600.00, 'imagenes/ALU-PERG-CUA.jpg', 1, 6, '300x400', 'Plata'),
('ALU-PERG-CUA', 'Pérgola aluminio tubo cuadrado', 'Pérgola de aluminio minimalista, estructura cuadrada reforzada en acabado Marrón.', 600.00, 'imagenes/ALU-PERG-CUA.jpg', 1, 6, '300x400', 'Marrón');

-- 22. Pérgola de PVC redondo
INSERT INTO productos (referencia, nombre, descripcion, precio, imagen_url, id_material, id_categoria, medidas, color) VALUES
('PVC-PERG-RED', 'Pérgola PVC tubo redondo', 'Pérgola clásica de PVC, resistente a la intemperie, tubo redondo en acabado Blanco.', 440.00, 'imagenes/PVC-PERG-RED.jpg', 2, 6, '200x300', 'Blanco'),
('PVC-PERG-RED', 'Pérgola PVC tubo redondo', 'Pérgola clásica de PVC, resistente a la intemperie, tubo redondo en acabado Plata.', 470.00, 'imagenes/PVC-PERG-RED.jpg', 2, 6, '200x300', 'Plata'),
('PVC-PERG-RED', 'Pérgola PVC tubo redondo', 'Pérgola clásica de PVC, resistente a la intemperie, tubo redondo en acabado Marrón.', 470.00, 'imagenes/PVC-PERG-RED.jpg', 2, 6, '200x300', 'Marrón'),
('PVC-PERG-RED', 'Pérgola PVC tubo redondo', 'Pérgola clásica de PVC, resistente a la intemperie, tubo redondo en acabado Blanco.', 490.00, 'imagenes/PVC-PERG-RED.jpg', 2, 6, '250x350', 'Blanco'),
('PVC-PERG-RED', 'Pérgola PVC tubo redondo', 'Pérgola clásica de PVC, resistente a la intemperie, tubo redondo en acabado Plata.', 520.00, 'imagenes/PVC-PERG-RED.jpg', 2, 6, '250x350', 'Plata'),
('PVC-PERG-RED', 'Pérgola PVC tubo redondo', 'Pérgola clásica de PVC, resistente a la intemperie, tubo redondo en acabado Marrón.', 520.00, 'imagenes/PVC-PERG-RED.jpg', 2, 6, '250x350', 'Marrón'),
('PVC-PERG-RED', 'Pérgola PVC tubo redondo', 'Pérgola clásica de PVC, resistente a la intemperie, tubo redondo en acabado Blanco.', 540.00, 'imagenes/PVC-PERG-RED.jpg', 2, 6, '300x400', 'Blanco'),
('PVC-PERG-RED', 'Pérgola PVC tubo redondo', 'Pérgola clásica de PVC, resistente a la intemperie, tubo redondo en acabado Plata.', 570.00, 'imagenes/PVC-PERG-RED.jpg', 2, 6, '300x400', 'Plata'),
('PVC-PERG-RED', 'Pérgola PVC tubo redondo', 'Pérgola clásica de PVC, resistente a la intemperie, tubo redondo en acabado Marrón.', 570.00, 'imagenes/PVC-PERG-RED.jpg', 2, 6, '300x400', 'Marrón');

-- ==============================================================================
-- 6. SIMULACIÓN DE VENTAS DE EJEMPLO
-- ==============================================================================

-- VENTA 1: Cliente "Juan García"
-- Buscamos su ID por email
SET @id_cliente1 = (SELECT id FROM clientes WHERE email = 'juan.garcia@email.com' LIMIT 1);

-- Insertamos la cabecera de la venta
INSERT INTO ventas (id_cliente, fecha, total) VALUES (@id_cliente1, '2023-11-01 10:30:00', 0);
SET @id_v1 = LAST_INSERT_ID();

-- Detalles Venta 1:
-- 2 unidades del Producto 1 (Ventana Corredera Aluminio Blanco) a 120.00€
INSERT INTO detalle_ventas (id_venta, id_producto, cantidad, precio_unitario) VALUES (@id_v1, 1, 2, 120.00);
-- 1 unidad del Producto 2 (Ventana Corredera Aluminio Plata) a 130.00€
INSERT INTO detalle_ventas (id_venta, id_producto, cantidad, precio_unitario) VALUES (@id_v1, 2, 1, 130.00);

-- Calculamos el total de la Venta 1
UPDATE ventas SET total = (SELECT IFNULL(SUM(subtotal), 0) FROM detalle_ventas WHERE id_venta = @id_v1) WHERE id = @id_v1;


-- VENTA 2: Cliente "María Rodríguez"
-- Buscamos su ID por email
SET @id_cliente2 = (SELECT id FROM clientes WHERE email = 'maria.rod@email.com' LIMIT 1);

-- Insertamos la cabecera de la venta
INSERT INTO ventas (id_cliente, fecha, total) VALUES (@id_cliente2, '2023-11-05 16:45:00', 0);
SET @id_v2 = LAST_INSERT_ID();

-- Detalles Venta 2:
-- 1 unidad del Producto 171 (Primer producto de la categoría 19: Pérgola Aluminio Redondo Blanco 200x300)
-- NOTA: El ID 171 es aproximado basándonos en el orden secuencial de inserts anteriores (9 productos por bloque x 18 bloques previos = 162 + margen).
-- Para asegurar consistencia, haremos una subconsulta para buscar el ID exacto del producto 'ALU-PERG-RED'
SET @id_prod_pergola = (SELECT id FROM productos WHERE referencia = 'ALU-PERG-RED' LIMIT 1);

INSERT INTO detalle_ventas (id_venta, id_producto, cantidad, precio_unitario) VALUES (@id_v2, @id_prod_pergola, 1, 450.00);

-- Calculamos el total de la Venta 2
UPDATE ventas SET total = (SELECT IFNULL(SUM(subtotal), 0) FROM detalle_ventas WHERE id_venta = @id_v2) WHERE id = @id_v2;