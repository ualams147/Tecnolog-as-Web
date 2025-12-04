DROP DATABASE IF EXISTS metalisteria;
CREATE DATABASE metalisteria;
USE metalisteria;

-- 1. TABLA MATERIALES
CREATE TABLE materiales (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(50) NOT NULL
);

-- 2. TABLA CATEGORIAS
CREATE TABLE categorias (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(50) NOT NULL
);

-- 3. TABLA PRODUCTOS
CREATE TABLE IF NOT EXISTS productos (
  id INT AUTO_INCREMENT PRIMARY KEY,
  referencia VARCHAR(40) NOT NULL,
  nombre VARCHAR(150) NOT NULL,
  descripcion TEXT,
  precio DECIMAL(10, 2) DEFAULT 0.00,
  imagen_url VARCHAR(255),
  id_material INT NOT NULL,
  id_categoria INT NOT NULL,
  medidas VARCHAR(50),
  stock INT DEFAULT 1,  -- Stock por defecto positivo
  color VARCHAR(30),
  FOREIGN KEY (id_material) REFERENCES materiales(id),
  FOREIGN KEY (id_categoria) REFERENCES categorias(id)
);

-- INSERTS BASE
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

-- ==========================================
-- INSERTS DE PRODUCTOS (CON STOCK ACTUALIZADO)
-- ==========================================

-- 1. Ventana corredera de 2 hojas (Aluminio)
INSERT INTO productos (referencia, nombre, descripcion, precio, imagen_url, id_material, id_categoria, medidas, stock, color) VALUES
('ALU-VEN-COR2H', 'Ventana corredera de 2 hojas (Aluminio)', 'Ventana de aluminio ligera y funcional, sistema de apertura corredera en acabado Blanco.', 120.00, 'imagenes/ALU-VEN-COR2H.jpg', 1, 1, '80x100', 50, 'Blanco'),
('ALU-VEN-COR2H', 'Ventana corredera de 2 hojas (Aluminio)', 'Ventana de aluminio ligera y funcional, sistema de apertura corredera en acabado Plata.', 130.00, 'imagenes/ALU-VEN-COR2H.jpg', 1, 1, '80x100', 30, 'Plata'),
('ALU-VEN-COR2H', 'Ventana corredera de 2 hojas (Aluminio)', 'Ventana de aluminio ligera y funcional, sistema de apertura corredera en acabado Marrón.', 130.00, 'imagenes/ALU-VEN-COR2H.jpg', 1, 1, '80x100', 25, 'Marrón'),
('ALU-VEN-COR2H', 'Ventana corredera de 2 hojas (Aluminio)', 'Ventana de aluminio ligera y funcional, sistema de apertura corredera en acabado Blanco.', 140.00, 'imagenes/ALU-VEN-COR2H.jpg', 1, 1, '100x120', 40, 'Blanco'),
('ALU-VEN-COR2H', 'Ventana corredera de 2 hojas (Aluminio)', 'Ventana de aluminio ligera y funcional, sistema de apertura corredera en acabado Plata.', 150.00, 'imagenes/ALU-VEN-COR2H.jpg', 1, 1, '100x120', 20, 'Plata'),
('ALU-VEN-COR2H', 'Ventana corredera de 2 hojas (Aluminio)', 'Ventana de aluminio ligera y funcional, sistema de apertura corredera en acabado Marrón.', 150.00, 'imagenes/ALU-VEN-COR2H.jpg', 1, 1, '100x120', 15, 'Marrón'),
('ALU-VEN-COR2H', 'Ventana corredera de 2 hojas (Aluminio)', 'Ventana de aluminio ligera y funcional, sistema de apertura corredera en acabado Blanco.', 160.00, 'imagenes/ALU-VEN-COR2H.jpg', 1, 1, '120x140', 35, 'Blanco'),
('ALU-VEN-COR2H', 'Ventana corredera de 2 hojas (Aluminio)', 'Ventana de aluminio ligera y funcional, sistema de apertura corredera en acabado Plata.', 170.00, 'imagenes/ALU-VEN-COR2H.jpg', 1, 1, '120x140', 18, 'Plata'),
('ALU-VEN-COR2H', 'Ventana corredera de 2 hojas (Aluminio)', 'Ventana de aluminio ligera y funcional, sistema de apertura corredera en acabado Marrón.', 170.00, 'imagenes/ALU-VEN-COR2H.jpg', 1, 1, '120x140', 12, 'Marrón');

-- 2. Ventana corredera de 2 hojas (PVC)
INSERT INTO productos (referencia, nombre, descripcion, precio, imagen_url, id_material, id_categoria, medidas, stock, color) VALUES
('PVC-VEN-COR2H', 'Ventana corredera de 2 hojas (PVC)', 'Ventana de PVC con gran aislamiento térmico y acústico en acabado Blanco.', 180.00, 'imagenes/PVC-VEN-COR2H.jpg', 2, 1, '80x100', 45, 'Blanco'),
('PVC-VEN-COR2H', 'Ventana corredera de 2 hojas (PVC)', 'Ventana de PVC con gran aislamiento térmico y acústico en acabado Plata.', 190.00, 'imagenes/PVC-VEN-COR2H.jpg', 2, 1, '80x100', 28, 'Plata'),
('PVC-VEN-COR2H', 'Ventana corredera de 2 hojas (PVC)', 'Ventana de PVC con gran aislamiento térmico y acústico en acabado Marrón.', 190.00, 'imagenes/PVC-VEN-COR2H.jpg', 2, 1, '80x100', 22, 'Marrón'),
('PVC-VEN-COR2H', 'Ventana corredera de 2 hojas (PVC)', 'Ventana de PVC con gran aislamiento térmico y acústico en acabado Blanco.', 200.00, 'imagenes/PVC-VEN-COR2H.jpg', 2, 1, '100x120', 38, 'Blanco'),
('PVC-VEN-COR2H', 'Ventana corredera de 2 hojas (PVC)', 'Ventana de PVC con gran aislamiento térmico y acústico en acabado Plata.', 210.00, 'imagenes/PVC-VEN-COR2H.jpg', 2, 1, '100x120', 18, 'Plata'),
('PVC-VEN-COR2H', 'Ventana corredera de 2 hojas (PVC)', 'Ventana de PVC con gran aislamiento térmico y acústico en acabado Marrón.', 210.00, 'imagenes/PVC-VEN-COR2H.jpg', 2, 1, '100x120', 14, 'Marrón'),
('PVC-VEN-COR2H', 'Ventana corredera de 2 hojas (PVC)', 'Ventana de PVC con gran aislamiento térmico y acústico en acabado Blanco.', 220.00, 'imagenes/PVC-VEN-COR2H.jpg', 2, 1, '120x140', 30, 'Blanco'),
('PVC-VEN-COR2H', 'Ventana corredera de 2 hojas (PVC)', 'Ventana de PVC con gran aislamiento térmico y acústico en acabado Plata.', 230.00, 'imagenes/PVC-VEN-COR2H.jpg', 2, 1, '120x140', 15, 'Plata'),
('PVC-VEN-COR2H', 'Ventana corredera de 2 hojas (PVC)', 'Ventana de PVC con gran aislamiento térmico y acústico en acabado Marrón.', 230.00, 'imagenes/PVC-VEN-COR2H.jpg', 2, 1, '120x140', 10, 'Marrón');

-- 3. Balcón corredera de 2 hojas (Aluminio)
INSERT INTO productos (referencia, nombre, descripcion, precio, imagen_url, id_material, id_categoria, medidas, stock, color) VALUES
('ALU-BAL-COR2H', 'Balcón corredera de 2 hojas (Aluminio)', 'Balconera amplia de aluminio, ideal para terrazas, en acabado Blanco.', 300.00, 'imagenes/ALU-BAL-COR2H.jpg', 1, 2, '200x210', 20, 'Blanco'),
('ALU-BAL-COR2H', 'Balcón corredera de 2 hojas (Aluminio)', 'Balconera amplia de aluminio, ideal para terrazas, en acabado Plata.', 320.00, 'imagenes/ALU-BAL-COR2H.jpg', 1, 2, '200x210', 15, 'Plata'),
('ALU-BAL-COR2H', 'Balcón corredera de 2 hojas (Aluminio)', 'Balconera amplia de aluminio, ideal para terrazas, en acabado Marrón.', 320.00, 'imagenes/ALU-BAL-COR2H.jpg', 1, 2, '200x210', 12, 'Marrón'),
('ALU-BAL-COR2H', 'Balcón corredera de 2 hojas (Aluminio)', 'Balconera amplia de aluminio, ideal para terrazas, en acabado Blanco.', 350.00, 'imagenes/ALU-BAL-COR2H.jpg', 1, 2, '220x220', 18, 'Blanco'),
('ALU-BAL-COR2H', 'Balcón corredera de 2 hojas (Aluminio)', 'Balconera amplia de aluminio, ideal para terrazas, en acabado Plata.', 370.00, 'imagenes/ALU-BAL-COR2H.jpg', 1, 2, '220x220', 10, 'Plata'),
('ALU-BAL-COR2H', 'Balcón corredera de 2 hojas (Aluminio)', 'Balconera amplia de aluminio, ideal para terrazas, en acabado Marrón.', 370.00, 'imagenes/ALU-BAL-COR2H.jpg', 1, 2, '220x220', 8, 'Marrón'),
('ALU-BAL-COR2H', 'Balcón corredera de 2 hojas (Aluminio)', 'Balconera amplia de aluminio, ideal para terrazas, en acabado Blanco.', 400.00, 'imagenes/ALU-BAL-COR2H.jpg', 1, 2, '240x230', 15, 'Blanco'),
('ALU-BAL-COR2H', 'Balcón corredera de 2 hojas (Aluminio)', 'Balconera amplia de aluminio, ideal para terrazas, en acabado Plata.', 420.00, 'imagenes/ALU-BAL-COR2H.jpg', 1, 2, '240x230', 8, 'Plata'),
('ALU-BAL-COR2H', 'Balcón corredera de 2 hojas (Aluminio)', 'Balconera amplia de aluminio, ideal para terrazas, en acabado Marrón.', 420.00, 'imagenes/ALU-BAL-COR2H.jpg', 1, 2, '240x230', 6, 'Marrón');

-- 4. Balcón corredera de 2 hojas (PVC)
INSERT INTO productos (referencia, nombre, descripcion, precio, imagen_url, id_material, id_categoria, medidas, stock, color) VALUES
('PVC-BAL-COR2H', 'Balcón corredera de 2 hojas (PVC)', 'Balconera de PVC de alta eficiencia energética en acabado Blanco.', 450.00, 'imagenes/PVC-BAL-COR2H.jpg', 2, 2, '200x210', 18, 'Blanco'),
('PVC-BAL-COR2H', 'Balcón corredera de 2 hojas (PVC)', 'Balconera de PVC de alta eficiencia energética en acabado Plata.', 480.00, 'imagenes/PVC-BAL-COR2H.jpg', 2, 2, '200x210', 12, 'Plata'),
('PVC-BAL-COR2H', 'Balcón corredera de 2 hojas (PVC)', 'Balconera de PVC de alta eficiencia energética en acabado Marrón.', 480.00, 'imagenes/PVC-BAL-COR2H.jpg', 2, 2, '200x210', 10, 'Marrón'),
('PVC-BAL-COR2H', 'Balcón corredera de 2 hojas (PVC)', 'Balconera de PVC de alta eficiencia energética en acabado Blanco.', 500.00, 'imagenes/PVC-BAL-COR2H.jpg', 2, 2, '220x220', 15, 'Blanco'),
('PVC-BAL-COR2H', 'Balcón corredera de 2 hojas (PVC)', 'Balconera de PVC de alta eficiencia energética en acabado Plata.', 530.00, 'imagenes/PVC-BAL-COR2H.jpg', 2, 2, '220x220', 9, 'Plata'),
('PVC-BAL-COR2H', 'Balcón corredera de 2 hojas (PVC)', 'Balconera de PVC de alta eficiencia energética en acabado Marrón.', 530.00, 'imagenes/PVC-BAL-COR2H.jpg', 2, 2, '220x220', 7, 'Marrón'),
('PVC-BAL-COR2H', 'Balcón corredera de 2 hojas (PVC)', 'Balconera de PVC de alta eficiencia energética en acabado Blanco.', 550.00, 'imagenes/PVC-BAL-COR2H.jpg', 2, 2, '240x230', 14, 'Blanco'),
('PVC-BAL-COR2H', 'Balcón corredera de 2 hojas (PVC)', 'Balconera de PVC de alta eficiencia energética en acabado Plata.', 580.00, 'imagenes/PVC-BAL-COR2H.jpg', 2, 2, '240x230', 8, 'Plata'),
('PVC-BAL-COR2H', 'Balcón corredera de 2 hojas (PVC)', 'Balconera de PVC de alta eficiencia energética en acabado Marrón.', 580.00, 'imagenes/PVC-BAL-COR2H.jpg', 2, 2, '240x230', 6, 'Marrón');

-- 5. Balcón corredera 3 hojas tricarril (Aluminio)
INSERT INTO productos (referencia, nombre, descripcion, precio, imagen_url, id_material, id_categoria, medidas, stock, color) VALUES
('ALU-BAL-COR3H', 'Balcón corredera 3 hojas tricarril (Aluminio)', 'Sistema tricarril para máxima apertura, aluminio resistente en acabado Blanco.', 600.00, 'imagenes/ALU-BAL-COR3H.jpg', 1, 2, '300x210', 10, 'Blanco'),
('ALU-BAL-COR3H', 'Balcón corredera 3 hojas tricarril (Aluminio)', 'Sistema tricarril para máxima apertura, aluminio resistente en acabado Plata.', 630.00, 'imagenes/ALU-BAL-COR3H.jpg', 1, 2, '300x210', 8, 'Plata'),
('ALU-BAL-COR3H', 'Balcón corredera 3 hojas tricarril (Aluminio)', 'Sistema tricarril para máxima apertura, aluminio resistente en acabado Marrón.', 630.00, 'imagenes/ALU-BAL-COR3H.jpg', 1, 2, '300x210', 6, 'Marrón'),
('ALU-BAL-COR3H', 'Balcón corredera 3 hojas tricarril (Aluminio)', 'Sistema tricarril para máxima apertura, aluminio resistente en acabado Blanco.', 650.00, 'imagenes/ALU-BAL-COR3H.jpg', 1, 2, '320x220', 9, 'Blanco'),
('ALU-BAL-COR3H', 'Balcón corredera 3 hojas tricarril (Aluminio)', 'Sistema tricarril para máxima apertura, aluminio resistente en acabado Plata.', 680.00, 'imagenes/ALU-BAL-COR3H.jpg', 1, 2, '320x220', 5, 'Plata'),
('ALU-BAL-COR3H', 'Balcón corredera 3 hojas tricarril (Aluminio)', 'Sistema tricarril para máxima apertura, aluminio resistente en acabado Marrón.', 680.00, 'imagenes/ALU-BAL-COR3H.jpg', 1, 2, '320x220', 4, 'Marrón'),
('ALU-BAL-COR3H', 'Balcón corredera 3 hojas tricarril (Aluminio)', 'Sistema tricarril para máxima apertura, aluminio resistente en acabado Blanco.', 700.00, 'imagenes/ALU-BAL-COR3H.jpg', 1, 2, '340x230', 8, 'Blanco'),
('ALU-BAL-COR3H', 'Balcón corredera 3 hojas tricarril (Aluminio)', 'Sistema tricarril para máxima apertura, aluminio resistente en acabado Plata.', 730.00, 'imagenes/ALU-BAL-COR3H.jpg', 1, 2, '340x230', 4, 'Plata'),
('ALU-BAL-COR3H', 'Balcón corredera 3 hojas tricarril (Aluminio)', 'Sistema tricarril para máxima apertura, aluminio resistente en acabado Marrón.', 730.00, 'imagenes/ALU-BAL-COR3H.jpg', 1, 2, '340x230', 3, 'Marrón');

-- 6. Balcón corredera 3 hojas tricarril (PVC)
INSERT INTO productos (referencia, nombre, descripcion, precio, imagen_url, id_material, id_categoria, medidas, stock, color) VALUES
('PVC-BAL-COR3H', 'Balcón corredera 3 hojas tricarril (PVC)', 'Tricarril de PVC con refuerzo interno, gran aislamiento en acabado Blanco.', 750.00, 'imagenes/PVC-BAL-COR3H.jpg', 2, 2, '300x210', 8, 'Blanco'),
('PVC-BAL-COR3H', 'Balcón corredera 3 hojas tricarril (PVC)', 'Tricarril de PVC con refuerzo interno, gran aislamiento en acabado Plata.', 780.00, 'imagenes/PVC-BAL-COR3H.jpg', 2, 2, '300x210', 6, 'Plata'),
('PVC-BAL-COR3H', 'Balcón corredera 3 hojas tricarril (PVC)', 'Tricarril de PVC con refuerzo interno, gran aislamiento en acabado Marrón.', 780.00, 'imagenes/PVC-BAL-COR3H.jpg', 2, 2, '300x210', 5, 'Marrón'),
('PVC-BAL-COR3H', 'Balcón corredera 3 hojas tricarril (PVC)', 'Tricarril de PVC con refuerzo interno, gran aislamiento en acabado Blanco.', 800.00, 'imagenes/PVC-BAL-COR3H.jpg', 2, 2, '320x220', 7, 'Blanco'),
('PVC-BAL-COR3H', 'Balcón corredera 3 hojas tricarril (PVC)', 'Tricarril de PVC con refuerzo interno, gran aislamiento en acabado Plata.', 830.00, 'imagenes/PVC-BAL-COR3H.jpg', 2, 2, '320x220', 4, 'Plata'),
('PVC-BAL-COR3H', 'Balcón corredera 3 hojas tricarril (PVC)', 'Tricarril de PVC con refuerzo interno, gran aislamiento en acabado Marrón.', 830.00, 'imagenes/PVC-BAL-COR3H.jpg', 2, 2, '320x220', 3, 'Marrón'),
('PVC-BAL-COR3H', 'Balcón corredera 3 hojas tricarril (PVC)', 'Tricarril de PVC con refuerzo interno, gran aislamiento en acabado Blanco.', 850.00, 'imagenes/PVC-BAL-COR3H.jpg', 2, 2, '340x230', 6, 'Blanco'),
('PVC-BAL-COR3H', 'Balcón corredera 3 hojas tricarril (PVC)', 'Tricarril de PVC con refuerzo interno, gran aislamiento en acabado Plata.', 880.00, 'imagenes/PVC-BAL-COR3H.jpg', 2, 2, '340x230', 3, 'Plata'),
('PVC-BAL-COR3H', 'Balcón corredera 3 hojas tricarril (PVC)', 'Tricarril de PVC con refuerzo interno, gran aislamiento en acabado Marrón.', 880.00, 'imagenes/PVC-BAL-COR3H.jpg', 2, 2, '340x230', 2, 'Marrón');

-- 7. Ventana fija (Aluminio)
INSERT INTO productos (referencia, nombre, descripcion, precio, imagen_url, id_material, id_categoria, medidas, stock, color) VALUES
('ALU-VEN-FIJA', 'Ventana fija (Aluminio)', 'Ventana panorámica sin apertura para máxima entrada de luz en acabado Blanco.', 80.00, 'imagenes/ALU-VEN-FIJA.jpg', 1, 1, '80x100', 60, 'Blanco'),
('ALU-VEN-FIJA', 'Ventana fija (Aluminio)', 'Ventana panorámica sin apertura para máxima entrada de luz en acabado Plata.', 90.00, 'imagenes/ALU-VEN-FIJA.jpg', 1, 1, '80x100', 40, 'Plata'),
('ALU-VEN-FIJA', 'Ventana fija (Aluminio)', 'Ventana panorámica sin apertura para máxima entrada de luz en acabado Marrón.', 90.00, 'imagenes/ALU-VEN-FIJA.jpg', 1, 1, '80x100', 35, 'Marrón'),
('ALU-VEN-FIJA', 'Ventana fija (Aluminio)', 'Ventana panorámica sin apertura para máxima entrada de luz en acabado Blanco.', 100.00, 'imagenes/ALU-VEN-FIJA.jpg', 1, 1, '100x100', 50, 'Blanco'),
('ALU-VEN-FIJA', 'Ventana fija (Aluminio)', 'Ventana panorámica sin apertura para máxima entrada de luz en acabado Plata.', 110.00, 'imagenes/ALU-VEN-FIJA.jpg', 1, 1, '100x100', 30, 'Plata'),
('ALU-VEN-FIJA', 'Ventana fija (Aluminio)', 'Ventana panorámica sin apertura para máxima entrada de luz en acabado Marrón.', 110.00, 'imagenes/ALU-VEN-FIJA.jpg', 1, 1, '100x100', 25, 'Marrón'),
('ALU-VEN-FIJA', 'Ventana fija (Aluminio)', 'Ventana panorámica sin apertura para máxima entrada de luz en acabado Blanco.', 120.00, 'imagenes/ALU-VEN-FIJA.jpg', 1, 1, '120x120', 40, 'Blanco'),
('ALU-VEN-FIJA', 'Ventana fija (Aluminio)', 'Ventana panorámica sin apertura para máxima entrada de luz en acabado Plata.', 130.00, 'imagenes/ALU-VEN-FIJA.jpg', 1, 1, '120x120', 25, 'Plata'),
('ALU-VEN-FIJA', 'Ventana fija (Aluminio)', 'Ventana panorámica sin apertura para máxima entrada de luz en acabado Marrón.', 130.00, 'imagenes/ALU-VEN-FIJA.jpg', 1, 1, '120x120', 20, 'Marrón');

-- 8. Ventana fija (PVC)
INSERT INTO productos (referencia, nombre, descripcion, precio, imagen_url, id_material, id_categoria, medidas, stock, color) VALUES
('PVC-VEN-FIJA', 'Ventana fija (PVC)', 'Ventana fija de PVC, ideal para aislar zonas sin ventilación en acabado Blanco.', 100.00, 'imagenes/PVC-VEN-FIJA.jpg', 2, 1, '80x100', 55, 'Blanco'),
('PVC-VEN-FIJA', 'Ventana fija (PVC)', 'Ventana fija de PVC, ideal para aislar zonas sin ventilación en acabado Plata.', 115.00, 'imagenes/PVC-VEN-FIJA.jpg', 2, 1, '80x100', 35, 'Plata'),
('PVC-VEN-FIJA', 'Ventana fija (PVC)', 'Ventana fija de PVC, ideal para aislar zonas sin ventilación en acabado Marrón.', 115.00, 'imagenes/PVC-VEN-FIJA.jpg', 2, 1, '80x100', 30, 'Marrón'),
('PVC-VEN-FIJA', 'Ventana fija (PVC)', 'Ventana fija de PVC, ideal para aislar zonas sin ventilación en acabado Blanco.', 120.00, 'imagenes/PVC-VEN-FIJA.jpg', 2, 1, '100x100', 45, 'Blanco'),
('PVC-VEN-FIJA', 'Ventana fija (PVC)', 'Ventana fija de PVC, ideal para aislar zonas sin ventilación en acabado Plata.', 135.00, 'imagenes/PVC-VEN-FIJA.jpg', 2, 1, '100x100', 25, 'Plata'),
('PVC-VEN-FIJA', 'Ventana fija (PVC)', 'Ventana fija de PVC, ideal para aislar zonas sin ventilación en acabado Marrón.', 135.00, 'imagenes/PVC-VEN-FIJA.jpg', 2, 1, '100x100', 20, 'Marrón'),
('PVC-VEN-FIJA', 'Ventana fija (PVC)', 'Ventana fija de PVC, ideal para aislar zonas sin ventilación en acabado Blanco.', 140.00, 'imagenes/PVC-VEN-FIJA.jpg', 2, 1, '120x120', 35, 'Blanco'),
('PVC-VEN-FIJA', 'Ventana fija (PVC)', 'Ventana fija de PVC, ideal para aislar zonas sin ventilación en acabado Plata.', 155.00, 'imagenes/PVC-VEN-FIJA.jpg', 2, 1, '120x120', 20, 'Plata'),
('PVC-VEN-FIJA', 'Ventana fija (PVC)', 'Ventana fija de PVC, ideal para aislar zonas sin ventilación en acabado Marrón.', 155.00, 'imagenes/PVC-VEN-FIJA.jpg', 2, 1, '120x120', 15, 'Marrón');

-- 9. Ventana abatible de 1 hoja (Aluminio)
INSERT INTO productos (referencia, nombre, descripcion, precio, imagen_url, id_material, id_categoria, medidas, stock, color) VALUES
('ALU-VEN-ABA1H', 'Ventana abatible 1 hoja (Aluminio)', 'Ventana practicable de una hoja, apertura clásica en acabado Blanco.', 95.00, 'imagenes/ALU-VEN-ABA1H.jpg', 1, 1, '60x100', 50, 'Blanco'),
('ALU-VEN-ABA1H', 'Ventana abatible 1 hoja (Aluminio)', 'Ventana practicable de una hoja, apertura clásica en acabado Plata.', 105.00, 'imagenes/ALU-VEN-ABA1H.jpg', 1, 1, '60x100', 35, 'Plata'),
('ALU-VEN-ABA1H', 'Ventana abatible 1 hoja (Aluminio)', 'Ventana practicable de una hoja, apertura clásica en acabado Marrón.', 105.00, 'imagenes/ALU-VEN-ABA1H.jpg', 1, 1, '60x100', 30, 'Marrón'),
('ALU-VEN-ABA1H', 'Ventana abatible 1 hoja (Aluminio)', 'Ventana practicable de una hoja, apertura clásica en acabado Blanco.', 115.00, 'imagenes/ALU-VEN-ABA1H.jpg', 1, 1, '70x120', 40, 'Blanco'),
('ALU-VEN-ABA1H', 'Ventana abatible 1 hoja (Aluminio)', 'Ventana practicable de una hoja, apertura clásica en acabado Plata.', 125.00, 'imagenes/ALU-VEN-ABA1H.jpg', 1, 1, '70x120', 25, 'Plata'),
('ALU-VEN-ABA1H', 'Ventana abatible 1 hoja (Aluminio)', 'Ventana practicable de una hoja, apertura clásica en acabado Marrón.', 125.00, 'imagenes/ALU-VEN-ABA1H.jpg', 1, 1, '70x120', 20, 'Marrón'),
('ALU-VEN-ABA1H', 'Ventana abatible 1 hoja (Aluminio)', 'Ventana practicable de una hoja, apertura clásica en acabado Blanco.', 135.00, 'imagenes/ALU-VEN-ABA1H.jpg', 1, 1, '80x140', 30, 'Blanco'),
('ALU-VEN-ABA1H', 'Ventana abatible 1 hoja (Aluminio)', 'Ventana practicable de una hoja, apertura clásica en acabado Plata.', 145.00, 'imagenes/ALU-VEN-ABA1H.jpg', 1, 1, '80x140', 18, 'Plata'),
('ALU-VEN-ABA1H', 'Ventana abatible 1 hoja (Aluminio)', 'Ventana practicable de una hoja, apertura clásica en acabado Marrón.', 145.00, 'imagenes/ALU-VEN-ABA1H.jpg', 1, 1, '80x140', 15, 'Marrón');

-- 10. Ventana abatible de 1 hoja (PVC)
INSERT INTO productos (referencia, nombre, descripcion, precio, imagen_url, id_material, id_categoria, medidas, stock, color) VALUES
('PVC-VEN-ABA1H', 'Ventana abatible 1 hoja (PVC)', 'Ventana de PVC de una hoja, cierre a presión hermético en acabado Blanco.', 120.00, 'imagenes/PVC-VEN-ABA1H.jpg', 2, 1, '60x100', 45, 'Blanco'),
('PVC-VEN-ABA1H', 'Ventana abatible 1 hoja (PVC)', 'Ventana de PVC de una hoja, cierre a presión hermético en acabado Plata.', 135.00, 'imagenes/PVC-VEN-ABA1H.jpg', 2, 1, '60x100', 30, 'Plata'),
('PVC-VEN-ABA1H', 'Ventana abatible 1 hoja (PVC)', 'Ventana de PVC de una hoja, cierre a presión hermético en acabado Marrón.', 135.00, 'imagenes/PVC-VEN-ABA1H.jpg', 2, 1, '60x100', 25, 'Marrón'),
('PVC-VEN-ABA1H', 'Ventana abatible 1 hoja (PVC)', 'Ventana de PVC de una hoja, cierre a presión hermético en acabado Blanco.', 140.00, 'imagenes/PVC-VEN-ABA1H.jpg', 2, 1, '70x120', 35, 'Blanco'),
('PVC-VEN-ABA1H', 'Ventana abatible 1 hoja (PVC)', 'Ventana de PVC de una hoja, cierre a presión hermético en acabado Plata.', 155.00, 'imagenes/PVC-VEN-ABA1H.jpg', 2, 1, '70x120', 20, 'Plata'),
('PVC-VEN-ABA1H', 'Ventana abatible 1 hoja (PVC)', 'Ventana de PVC de una hoja, cierre a presión hermético en acabado Marrón.', 155.00, 'imagenes/PVC-VEN-ABA1H.jpg', 2, 1, '70x120', 18, 'Marrón'),
('PVC-VEN-ABA1H', 'Ventana abatible 1 hoja (PVC)', 'Ventana de PVC de una hoja, cierre a presión hermético en acabado Blanco.', 160.00, 'imagenes/PVC-VEN-ABA1H.jpg', 2, 1, '80x140', 28, 'Blanco'),
('PVC-VEN-ABA1H', 'Ventana abatible 1 hoja (PVC)', 'Ventana de PVC de una hoja, cierre a presión hermético en acabado Plata.', 175.00, 'imagenes/PVC-VEN-ABA1H.jpg', 2, 1, '80x140', 15, 'Plata'),
('PVC-VEN-ABA1H', 'Ventana abatible 1 hoja (PVC)', 'Ventana de PVC de una hoja, cierre a presión hermético en acabado Marrón.', 175.00, 'imagenes/PVC-VEN-ABA1H.jpg', 2, 1, '80x140', 12, 'Marrón');

-- 11. Ventana abatible de 2 hojas (Aluminio)
INSERT INTO productos (referencia, nombre, descripcion, precio, imagen_url, id_material, id_categoria, medidas, stock, color) VALUES
('ALU-VEN-ABA2H', 'Ventana abatible 2 hojas (Aluminio)', 'Ventana doble hoja con apertura central, ideal para dormitorios en acabado Blanco.', 160.00, 'imagenes/ALU-VEN-ABA2H.jpg', 1, 1, '120x100', 30, 'Blanco'),
('ALU-VEN-ABA2H', 'Ventana abatible 2 hojas (Aluminio)', 'Ventana doble hoja con apertura central, ideal para dormitorios en acabado Plata.', 180.00, 'imagenes/ALU-VEN-ABA2H.jpg', 1, 1, '120x100', 20, 'Plata'),
('ALU-VEN-ABA2H', 'Ventana abatible 2 hojas (Aluminio)', 'Ventana doble hoja con apertura central, ideal para dormitorios en acabado Marrón.', 180.00, 'imagenes/ALU-VEN-ABA2H.jpg', 1, 1, '120x100', 15, 'Marrón'),
('ALU-VEN-ABA2H', 'Ventana abatible 2 hojas (Aluminio)', 'Ventana doble hoja con apertura central, ideal para dormitorios en acabado Blanco.', 190.00, 'imagenes/ALU-VEN-ABA2H.jpg', 1, 1, '140x120', 25, 'Blanco'),
('ALU-VEN-ABA2H', 'Ventana abatible 2 hojas (Aluminio)', 'Ventana doble hoja con apertura central, ideal para dormitorios en acabado Plata.', 210.00, 'imagenes/ALU-VEN-ABA2H.jpg', 1, 1, '140x120', 12, 'Plata'),
('ALU-VEN-ABA2H', 'Ventana abatible 2 hojas (Aluminio)', 'Ventana doble hoja con apertura central, ideal para dormitorios en acabado Marrón.', 210.00, 'imagenes/ALU-VEN-ABA2H.jpg', 1, 1, '140x120', 10, 'Marrón'),
('ALU-VEN-ABA2H', 'Ventana abatible 2 hojas (Aluminio)', 'Ventana doble hoja con apertura central, ideal para dormitorios en acabado Blanco.', 220.00, 'imagenes/ALU-VEN-ABA2H.jpg', 1, 1, '160x140', 20, 'Blanco'),
('ALU-VEN-ABA2H', 'Ventana abatible 2 hojas (Aluminio)', 'Ventana doble hoja con apertura central, ideal para dormitorios en acabado Plata.', 240.00, 'imagenes/ALU-VEN-ABA2H.jpg', 1, 1, '160x140', 10, 'Plata'),
('ALU-VEN-ABA2H', 'Ventana abatible 2 hojas (Aluminio)', 'Ventana doble hoja con apertura central, ideal para dormitorios en acabado Marrón.', 240.00, 'imagenes/ALU-VEN-ABA2H.jpg', 1, 1, '160x140', 8, 'Marrón');

-- 12. Ventana abatible 2 hojas con oscilobatiente (Aluminio)
INSERT INTO productos (referencia, nombre, descripcion, precio, imagen_url, id_material, id_categoria, medidas, stock, color) VALUES
('ALU-VEN-ABA2H-OB', 'Ventana abatible 2 hojas con oscilobatiente (Aluminio)', 'Ventana premium con sistema oscilobatiente para ventilación segura en acabado Blanco.', 200.00, 'imagenes/ALU-VEN-ABA2H-OB.jpg', 1, 1, '120x100', 25, 'Blanco'),
('ALU-VEN-ABA2H-OB', 'Ventana abatible 2 hojas con oscilobatiente (Aluminio)', 'Ventana premium con sistema oscilobatiente para ventilación segura en acabado Plata.', 220.00, 'imagenes/ALU-VEN-ABA2H-OB.jpg', 1, 1, '120x100', 15, 'Plata'),
('ALU-VEN-ABA2H-OB', 'Ventana abatible 2 hojas con oscilobatiente (Aluminio)', 'Ventana premium con sistema oscilobatiente para ventilación segura en acabado Marrón.', 220.00, 'imagenes/ALU-VEN-ABA2H-OB.jpg', 1, 1, '120x100', 10, 'Marrón'),
('ALU-VEN-ABA2H-OB', 'Ventana abatible 2 hojas con oscilobatiente (Aluminio)', 'Ventana premium con sistema oscilobatiente para ventilación segura en acabado Blanco.', 230.00, 'imagenes/ALU-VEN-ABA2H-OB.jpg', 1, 1, '140x120', 20, 'Blanco'),
('ALU-VEN-ABA2H-OB', 'Ventana abatible 2 hojas con oscilobatiente (Aluminio)', 'Ventana premium con sistema oscilobatiente para ventilación segura en acabado Plata.', 250.00, 'imagenes/ALU-VEN-ABA2H-OB.jpg', 1, 1, '140x120', 12, 'Plata'),
('ALU-VEN-ABA2H-OB', 'Ventana abatible 2 hojas con oscilobatiente (Aluminio)', 'Ventana premium con sistema oscilobatiente para ventilación segura en acabado Marrón.', 250.00, 'imagenes/ALU-VEN-ABA2H-OB.jpg', 1, 1, '140x120', 8, 'Marrón'),
('ALU-VEN-ABA2H-OB', 'Ventana abatible 2 hojas con oscilobatiente (Aluminio)', 'Ventana premium con sistema oscilobatiente para ventilación segura en acabado Blanco.', 260.00, 'imagenes/ALU-VEN-ABA2H-OB.jpg', 1, 1, '160x140', 18, 'Blanco'),
('ALU-VEN-ABA2H-OB', 'Ventana abatible 2 hojas con oscilobatiente (Aluminio)', 'Ventana premium con sistema oscilobatiente para ventilación segura en acabado Plata.', 280.00, 'imagenes/ALU-VEN-ABA2H-OB.jpg', 1, 1, '160x140', 10, 'Plata'),
('ALU-VEN-ABA2H-OB', 'Ventana abatible 2 hojas con oscilobatiente (Aluminio)', 'Ventana premium con sistema oscilobatiente para ventilación segura en acabado Marrón.', 280.00, 'imagenes/ALU-VEN-ABA2H-OB.jpg', 1, 1, '160x140', 6, 'Marrón');

-- 13. Ventana abatible de 2 hojas (PVC)
INSERT INTO productos (referencia, nombre, descripcion, precio, imagen_url, id_material, id_categoria, medidas, stock, color) VALUES
('PVC-VEN-ABA2H', 'Ventana abatible 2 hojas (PVC)', 'Doble hoja de PVC con cierre reforzado, máximo silencio en acabado Blanco.', 210.00, 'imagenes/PVC-VEN-ABA2H.jpg', 2, 1, '120x100', 25, 'Blanco'),
('PVC-VEN-ABA2H', 'Ventana abatible 2 hojas (PVC)', 'Doble hoja de PVC con cierre reforzado, máximo silencio en acabado Plata.', 230.00, 'imagenes/PVC-VEN-ABA2H.jpg', 2, 1, '120x100', 15, 'Plata'),
('PVC-VEN-ABA2H', 'Ventana abatible 2 hojas (PVC)', 'Doble hoja de PVC con cierre reforzado, máximo silencio en acabado Marrón.', 230.00, 'imagenes/PVC-VEN-ABA2H.jpg', 2, 1, '120x100', 12, 'Marrón'),
('PVC-VEN-ABA2H', 'Ventana abatible 2 hojas (PVC)', 'Doble hoja de PVC con cierre reforzado, máximo silencio en acabado Blanco.', 240.00, 'imagenes/PVC-VEN-ABA2H.jpg', 2, 1, '140x120', 22, 'Blanco'),
('PVC-VEN-ABA2H', 'Ventana abatible 2 hojas (PVC)', 'Doble hoja de PVC con cierre reforzado, máximo silencio en acabado Plata.', 260.00, 'imagenes/PVC-VEN-ABA2H.jpg', 2, 1, '140x120', 10, 'Plata'),
('PVC-VEN-ABA2H', 'Ventana abatible 2 hojas (PVC)', 'Doble hoja de PVC con cierre reforzado, máximo silencio en acabado Marrón.', 260.00, 'imagenes/PVC-VEN-ABA2H.jpg', 2, 1, '140x120', 8, 'Marrón'),
('PVC-VEN-ABA2H', 'Ventana abatible 2 hojas (PVC)', 'Doble hoja de PVC con cierre reforzado, máximo silencio en acabado Blanco.', 270.00, 'imagenes/PVC-VEN-ABA2H.jpg', 2, 1, '160x140', 18, 'Blanco'),
('PVC-VEN-ABA2H', 'Ventana abatible 2 hojas (PVC)', 'Doble hoja de PVC con cierre reforzado, máximo silencio en acabado Plata.', 290.00, 'imagenes/PVC-VEN-ABA2H.jpg', 2, 1, '160x140', 8, 'Plata'),
('PVC-VEN-ABA2H', 'Ventana abatible 2 hojas (PVC)', 'Doble hoja de PVC con cierre reforzado, máximo silencio en acabado Marrón.', 290.00, 'imagenes/PVC-VEN-ABA2H.jpg', 2, 1, '160x140', 6, 'Marrón');

-- 14. Ventana abatible 2 hojas con oscilobatiente (PVC)
INSERT INTO productos (referencia, nombre, descripcion, precio, imagen_url, id_material, id_categoria, medidas, stock, color) VALUES
('PVC-VEN-ABA2H-OB', 'Ventana abatible 2 hojas con oscilobatiente (PVC)', 'La mejor ventana de PVC, oscilobatiente y hermética en acabado Blanco.', 250.00, 'imagenes/PVC-VEN-ABA2H-OB.jpg', 2, 1, '120x100', 20, 'Blanco'),
('PVC-VEN-ABA2H-OB', 'Ventana abatible 2 hojas con oscilobatiente (PVC)', 'La mejor ventana de PVC, oscilobatiente y hermética en acabado Plata.', 275.00, 'imagenes/PVC-VEN-ABA2H-OB.jpg', 2, 1, '120x100', 12, 'Plata'),
('PVC-VEN-ABA2H-OB', 'Ventana abatible 2 hojas con oscilobatiente (PVC)', 'La mejor ventana de PVC, oscilobatiente y hermética en acabado Marrón.', 275.00, 'imagenes/PVC-VEN-ABA2H-OB.jpg', 2, 1, '120x100', 10, 'Marrón'),
('PVC-VEN-ABA2H-OB', 'Ventana abatible 2 hojas con oscilobatiente (PVC)', 'La mejor ventana de PVC, oscilobatiente y hermética en acabado Blanco.', 280.00, 'imagenes/PVC-VEN-ABA2H-OB.jpg', 2, 1, '140x120', 18, 'Blanco'),
('PVC-VEN-ABA2H-OB', 'Ventana abatible 2 hojas con oscilobatiente (PVC)', 'La mejor ventana de PVC, oscilobatiente y hermética en acabado Plata.', 305.00, 'imagenes/PVC-VEN-ABA2H-OB.jpg', 2, 1, '140x120', 8, 'Plata'),
('PVC-VEN-ABA2H-OB', 'Ventana abatible 2 hojas con oscilobatiente (PVC)', 'La mejor ventana de PVC, oscilobatiente y hermética en acabado Marrón.', 305.00, 'imagenes/PVC-VEN-ABA2H-OB.jpg', 2, 1, '140x120', 6, 'Marrón'),
('PVC-VEN-ABA2H-OB', 'Ventana abatible 2 hojas con oscilobatiente (PVC)', 'La mejor ventana de PVC, oscilobatiente y hermética en acabado Blanco.', 310.00, 'imagenes/PVC-VEN-ABA2H-OB.jpg', 2, 1, '160x140', 15, 'Blanco'),
('PVC-VEN-ABA2H-OB', 'Ventana abatible 2 hojas con oscilobatiente (PVC)', 'La mejor ventana de PVC, oscilobatiente y hermética en acabado Plata.', 335.00, 'imagenes/PVC-VEN-ABA2H-OB.jpg', 2, 1, '160x140', 7, 'Plata'),
('PVC-VEN-ABA2H-OB', 'Ventana abatible 2 hojas con oscilobatiente (PVC)', 'La mejor ventana de PVC, oscilobatiente y hermética en acabado Marrón.', 335.00, 'imagenes/PVC-VEN-ABA2H-OB.jpg', 2, 1, '160x140', 5, 'Marrón');

-- 15. Balcón abatible de 1 hoja (Aluminio)
INSERT INTO productos (referencia, nombre, descripcion, precio, imagen_url, id_material, id_categoria, medidas, stock, color) VALUES
('ALU-BAL-ABA1H', 'Balcón abatible 1 hoja (Aluminio)', 'Puerta balconera de una hoja, paso cómodo y resistente en acabado Blanco.', 280.00, 'imagenes/ALU-BAL-ABA1H.jpg', 1, 2, '60x100', 30, 'Blanco'),
('ALU-BAL-ABA1H', 'Balcón abatible 1 hoja (Aluminio)', 'Puerta balconera de una hoja, paso cómodo y resistente en acabado Plata.', 300.00, 'imagenes/ALU-BAL-ABA1H.jpg', 1, 2, '60x100', 18, 'Plata'),
('ALU-BAL-ABA1H', 'Balcón abatible 1 hoja (Aluminio)', 'Puerta balconera de una hoja, paso cómodo y resistente en acabado Marrón.', 300.00, 'imagenes/ALU-BAL-ABA1H.jpg', 1, 2, '60x100', 15, 'Marrón'),
('ALU-BAL-ABA1H', 'Balcón abatible 1 hoja (Aluminio)', 'Puerta balconera de una hoja, paso cómodo y resistente en acabado Blanco.', 310.00, 'imagenes/ALU-BAL-ABA1H.jpg', 1, 2, '70x120', 25, 'Blanco'),
('ALU-BAL-ABA1H', 'Balcón abatible 1 hoja (Aluminio)', 'Puerta balconera de una hoja, paso cómodo y resistente en acabado Plata.', 330.00, 'imagenes/ALU-BAL-ABA1H.jpg', 1, 2, '70x120', 12, 'Plata'),
('ALU-BAL-ABA1H', 'Balcón abatible 1 hoja (Aluminio)', 'Puerta balconera de una hoja, paso cómodo y resistente en acabado Marrón.', 330.00, 'imagenes/ALU-BAL-ABA1H.jpg', 1, 2, '70x120', 10, 'Marrón'),
('ALU-BAL-ABA1H', 'Balcón abatible 1 hoja (Aluminio)', 'Puerta balconera de una hoja, paso cómodo y resistente en acabado Blanco.', 340.00, 'imagenes/ALU-BAL-ABA1H.jpg', 1, 2, '80x140', 20, 'Blanco'),
('ALU-BAL-ABA1H', 'Balcón abatible 1 hoja (Aluminio)', 'Puerta balconera de una hoja, paso cómodo y resistente en acabado Plata.', 360.00, 'imagenes/ALU-BAL-ABA1H.jpg', 1, 2, '80x140', 10, 'Plata'),
('ALU-BAL-ABA1H', 'Balcón abatible 1 hoja (Aluminio)', 'Puerta balconera de una hoja, paso cómodo y resistente en acabado Marrón.', 360.00, 'imagenes/ALU-BAL-ABA1H.jpg', 1, 2, '80x140', 8, 'Marrón');

-- 16. Balcón abatible de 1 hoja (PVC)
INSERT INTO productos (referencia, nombre, descripcion, precio, imagen_url, id_material, id_categoria, medidas, stock, color) VALUES
('PVC-BAL-ABA1H', 'Balcón abatible 1 hoja (PVC)', 'Puerta balconera de PVC, aislamiento superior en acabado Blanco.', 320.00, 'imagenes/PVC-BAL-ABA1H.jpg', 2, 2, '60x100', 25, 'Blanco'),
('PVC-BAL-ABA1H', 'Balcón abatible 1 hoja (PVC)', 'Puerta balconera de PVC, aislamiento superior en acabado Plata.', 340.00, 'imagenes/PVC-BAL-ABA1H.jpg', 2, 2, '60x100', 15, 'Plata'),
('PVC-BAL-ABA1H', 'Balcón abatible 1 hoja (PVC)', 'Puerta balconera de PVC, aislamiento superior en acabado Marrón.', 340.00, 'imagenes/PVC-BAL-ABA1H.jpg', 2, 2, '60x100', 12, 'Marrón'),
('PVC-BAL-ABA1H', 'Balcón abatible 1 hoja (PVC)', 'Puerta balconera de PVC, aislamiento superior en acabado Blanco.', 350.00, 'imagenes/PVC-BAL-ABA1H.jpg', 2, 2, '70x120', 20, 'Blanco'),
('PVC-BAL-ABA1H', 'Balcón abatible 1 hoja (PVC)', 'Puerta balconera de PVC, aislamiento superior en acabado Plata.', 370.00, 'imagenes/PVC-BAL-ABA1H.jpg', 2, 2, '70x120', 10, 'Plata'),
('PVC-BAL-ABA1H', 'Balcón abatible 1 hoja (PVC)', 'Puerta balconera de PVC, aislamiento superior en acabado Marrón.', 370.00, 'imagenes/PVC-BAL-ABA1H.jpg', 2, 2, '70x120', 8, 'Marrón'),
('PVC-BAL-ABA1H', 'Balcón abatible 1 hoja (PVC)', 'Puerta balconera de PVC, aislamiento superior en acabado Blanco.', 380.00, 'imagenes/PVC-BAL-ABA1H.jpg', 2, 2, '80x140', 18, 'Blanco'),
('PVC-BAL-ABA1H', 'Balcón abatible 1 hoja (PVC)', 'Puerta balconera de PVC, aislamiento superior en acabado Plata.', 400.00, 'imagenes/PVC-BAL-ABA1H.jpg', 2, 2, '80x140', 8, 'Plata'),
('PVC-BAL-ABA1H', 'Balcón abatible 1 hoja (PVC)', 'Puerta balconera de PVC, aislamiento superior en acabado Marrón.', 400.00, 'imagenes/PVC-BAL-ABA1H.jpg', 2, 2, '80x140', 6, 'Marrón');

-- 17. Balcón abatible de 2 hojas (Aluminio)
INSERT INTO productos (referencia, nombre, descripcion, precio, imagen_url, id_material, id_categoria, medidas, stock, color) VALUES
('ALU-BAL-ABA2H', 'Balcón abatible 2 hojas (Aluminio)', 'Balconera doble de aluminio, apertura amplia en acabado Blanco.', 450.00, 'imagenes/ALU-BAL-ABA2H.jpg', 1, 2, '120x100', 15, 'Blanco'),
('ALU-BAL-ABA2H', 'Balcón abatible 2 hojas (Aluminio)', 'Balconera doble de aluminio, apertura amplia en acabado Plata.', 480.00, 'imagenes/ALU-BAL-ABA2H.jpg', 1, 2, '120x100', 10, 'Plata'),
('ALU-BAL-ABA2H', 'Balcón abatible 2 hojas (Aluminio)', 'Balconera doble de aluminio, apertura amplia en acabado Marrón.', 480.00, 'imagenes/ALU-BAL-ABA2H.jpg', 1, 2, '120x100', 8, 'Marrón'),
('ALU-BAL-ABA2H', 'Balcón abatible 2 hojas (Aluminio)', 'Balconera doble de aluminio, apertura amplia en acabado Blanco.', 480.00, 'imagenes/ALU-BAL-ABA2H.jpg', 1, 2, '140x120', 12, 'Blanco'),
('ALU-BAL-ABA2H', 'Balcón abatible 2 hojas (Aluminio)', 'Balconera doble de aluminio, apertura amplia en acabado Plata.', 510.00, 'imagenes/ALU-BAL-ABA2H.jpg', 1, 2, '140x120', 7, 'Plata'),
('ALU-BAL-ABA2H', 'Balcón abatible 2 hojas (Aluminio)', 'Balconera doble de aluminio, apertura amplia en acabado Marrón.', 510.00, 'imagenes/ALU-BAL-ABA2H.jpg', 1, 2, '140x120', 5, 'Marrón'),
('ALU-BAL-ABA2H', 'Balcón abatible 2 hojas (Aluminio)', 'Balconera doble de aluminio, apertura amplia en acabado Blanco.', 510.00, 'imagenes/ALU-BAL-ABA2H.jpg', 1, 2, '160x140', 10, 'Blanco'),
('ALU-BAL-ABA2H', 'Balcón abatible 2 hojas (Aluminio)', 'Balconera doble de aluminio, apertura amplia en acabado Plata.', 540.00, 'imagenes/ALU-BAL-ABA2H.jpg', 1, 2, '160x140', 6, 'Plata'),
('ALU-BAL-ABA2H', 'Balcón abatible 2 hojas (Aluminio)', 'Balconera doble de aluminio, apertura amplia en acabado Marrón.', 540.00, 'imagenes/ALU-BAL-ABA2H.jpg', 1, 2, '160x140', 4, 'Marrón');

-- 18. Balcón abatible de 2 hojas (PVC)
INSERT INTO productos (referencia, nombre, descripcion, precio, imagen_url, id_material, id_categoria, medidas, stock, color) VALUES
('PVC-BAL-ABA2H', 'Balcón abatible 2 hojas (PVC)', 'Balcón doble hoja de PVC, robusto y elegante en acabado Blanco.', 500.00, 'imagenes/PVC-BAL-ABA2H.jpg', 2, 2, '120x100', 12, 'Blanco'),
('PVC-BAL-ABA2H', 'Balcón abatible 2 hojas (PVC)', 'Balcón doble hoja de PVC, robusto y elegante en acabado Plata.', 530.00, 'imagenes/PVC-BAL-ABA2H.jpg', 2, 2, '120x100', 8, 'Plata'),
('PVC-BAL-ABA2H', 'Balcón abatible 2 hojas (PVC)', 'Balcón doble hoja de PVC, robusto y elegante en acabado Marrón.', 530.00, 'imagenes/PVC-BAL-ABA2H.jpg', 2, 2, '120x100', 6, 'Marrón'),
('PVC-BAL-ABA2H', 'Balcón abatible 2 hojas (PVC)', 'Balcón doble hoja de PVC, robusto y elegante en acabado Blanco.', 530.00, 'imagenes/PVC-BAL-ABA2H.jpg', 2, 2, '140x120', 10, 'Blanco'),
('PVC-BAL-ABA2H', 'Balcón abatible 2 hojas (PVC)', 'Balcón doble hoja de PVC, robusto y elegante en acabado Plata.', 560.00, 'imagenes/PVC-BAL-ABA2H.jpg', 2, 2, '140x120', 7, 'Plata'),
('PVC-BAL-ABA2H', 'Balcón abatible 2 hojas (PVC)', 'Balcón doble hoja de PVC, robusto y elegante en acabado Marrón.', 560.00, 'imagenes/PVC-BAL-ABA2H.jpg', 2, 2, '140x120', 5, 'Marrón'),
('PVC-BAL-ABA2H', 'Balcón abatible 2 hojas (PVC)', 'Balcón doble hoja de PVC, robusto y elegante en acabado Blanco.', 560.00, 'imagenes/PVC-BAL-ABA2H.jpg', 2, 2, '160x140', 8, 'Blanco'),
('PVC-BAL-ABA2H', 'Balcón abatible 2 hojas (PVC)', 'Balcón doble hoja de PVC, robusto y elegante en acabado Plata.', 590.00, 'imagenes/PVC-BAL-ABA2H.jpg', 2, 2, '160x140', 6, 'Plata'),
('PVC-BAL-ABA2H', 'Balcón abatible 2 hojas (PVC)', 'Balcón doble hoja de PVC, robusto y elegante en acabado Marrón.', 590.00, 'imagenes/PVC-BAL-ABA2H.jpg', 2, 2, '160x140', 4, 'Marrón');

-- 19. Pérgola de aluminio redondo
INSERT INTO productos (referencia, nombre, descripcion, precio, imagen_url, id_material, id_categoria, medidas, stock, color) VALUES
('ALU-PERG-RED', 'Pérgola aluminio tubo redondo', 'Pérgola resistente ideal para jardín, estructura tubular en acabado Blanco.', 450.00, 'imagenes/ALU-PERG-RED.jpg', 1, 6, '200x300', 10, 'Blanco'),
('ALU-PERG-RED', 'Pérgola aluminio tubo redondo', 'Pérgola resistente ideal para jardín, estructura tubular en acabado Plata.', 480.00, 'imagenes/ALU-PERG-RED.jpg', 1, 6, '200x300', 5, 'Plata'),
('ALU-PERG-RED', 'Pérgola aluminio tubo redondo', 'Pérgola resistente ideal para jardín, estructura tubular en acabado Marrón.', 480.00, 'imagenes/ALU-PERG-RED.jpg', 1, 6, '200x300', 4, 'Marrón'),
('ALU-PERG-RED', 'Pérgola aluminio tubo redondo', 'Pérgola resistente ideal para jardín, estructura tubular en acabado Blanco.', 500.00, 'imagenes/ALU-PERG-RED.jpg', 1, 6, '250x350', 8, 'Blanco'),
('ALU-PERG-RED', 'Pérgola aluminio tubo redondo', 'Pérgola resistente ideal para jardín, estructura tubular en acabado Plata.', 530.00, 'imagenes/ALU-PERG-RED.jpg', 1, 6, '250x350', 4, 'Plata'),
('ALU-PERG-RED', 'Pérgola aluminio tubo redondo', 'Pérgola resistente ideal para jardín, estructura tubular en acabado Marrón.', 530.00, 'imagenes/ALU-PERG-RED.jpg', 1, 6, '250x350', 3, 'Marrón'),
('ALU-PERG-RED', 'Pérgola aluminio tubo redondo', 'Pérgola resistente ideal para jardín, estructura tubular en acabado Blanco.', 550.00, 'imagenes/ALU-PERG-RED.jpg', 1, 6, '300x400', 6, 'Blanco'),
('ALU-PERG-RED', 'Pérgola aluminio tubo redondo', 'Pérgola resistente ideal para jardín, estructura tubular en acabado Plata.', 580.00, 'imagenes/ALU-PERG-RED.jpg', 1, 6, '300x400', 3, 'Plata'),
('ALU-PERG-RED', 'Pérgola aluminio tubo redondo', 'Pérgola resistente ideal para jardín, estructura tubular en acabado Marrón.', 580.00, 'imagenes/ALU-PERG-RED.jpg', 1, 6, '300x400', 2, 'Marrón');

-- 20. Pérgola de PVC cuadrado
INSERT INTO productos (referencia, nombre, descripcion, precio, imagen_url, id_material, id_categoria, medidas, stock, color) VALUES
('PVC-PERG-CUA', 'Pérgola PVC tubo cuadrado', 'Pérgola de PVC de diseño moderno con líneas rectas en acabado Blanco.', 460.00, 'imagenes/PVC-PERG-CUA.jpg', 2, 6, '200x300', 12, 'Blanco'),
('PVC-PERG-CUA', 'Pérgola PVC tubo cuadrado', 'Pérgola de PVC de diseño moderno con líneas rectas en acabado Plata.', 490.00, 'imagenes/PVC-PERG-CUA.jpg', 2, 6, '200x300', 6, 'Plata'),
('PVC-PERG-CUA', 'Pérgola PVC tubo cuadrado', 'Pérgola de PVC de diseño moderno con líneas rectas en acabado Marrón.', 490.00, 'imagenes/PVC-PERG-CUA.jpg', 2, 6, '200x300', 5, 'Marrón'),
('PVC-PERG-CUA', 'Pérgola PVC tubo cuadrado', 'Pérgola de PVC de diseño moderno con líneas rectas en acabado Blanco.', 510.00, 'imagenes/PVC-PERG-CUA.jpg', 2, 6, '250x350', 8, 'Blanco'),
('PVC-PERG-CUA', 'Pérgola PVC tubo cuadrado', 'Pérgola de PVC de diseño moderno con líneas rectas en acabado Plata.', 540.00, 'imagenes/PVC-PERG-CUA.jpg', 2, 6, '250x350', 4, 'Plata'),
('PVC-PERG-CUA', 'Pérgola PVC tubo cuadrado', 'Pérgola de PVC de diseño moderno con líneas rectas en acabado Marrón.', 540.00, 'imagenes/PVC-PERG-CUA.jpg', 2, 6, '250x350', 3, 'Marrón'),
('PVC-PERG-CUA', 'Pérgola PVC tubo cuadrado', 'Pérgola de PVC de diseño moderno con líneas rectas en acabado Blanco.', 560.00, 'imagenes/PVC-PERG-CUA.jpg', 2, 6, '300x400', 6, 'Blanco'),
('PVC-PERG-CUA', 'Pérgola PVC tubo cuadrado', 'Pérgola de PVC de diseño moderno con líneas rectas en acabado Plata.', 590.00, 'imagenes/PVC-PERG-CUA.jpg', 2, 6, '300x400', 3, 'Plata'),
('PVC-PERG-CUA', 'Pérgola PVC tubo cuadrado', 'Pérgola de PVC de diseño moderno con líneas rectas en acabado Marrón.', 590.00, 'imagenes/PVC-PERG-CUA.jpg', 2, 6, '300x400', 2, 'Marrón');

-- 21. Pérgola de aluminio cuadrado
INSERT INTO productos (referencia, nombre, descripcion, precio, imagen_url, id_material, id_categoria, medidas, stock, color) VALUES
('ALU-PERG-CUA', 'Pérgola aluminio tubo cuadrado', 'Pérgola de aluminio minimalista, estructura cuadrada reforzada en acabado Blanco.', 470.00, 'imagenes/ALU-PERG-CUA.jpg', 1, 6, '200x300', 15, 'Blanco'),
('ALU-PERG-CUA', 'Pérgola aluminio tubo cuadrado', 'Pérgola de aluminio minimalista, estructura cuadrada reforzada en acabado Plata.', 500.00, 'imagenes/ALU-PERG-CUA.jpg', 1, 6, '200x300', 10, 'Plata'),
('ALU-PERG-CUA', 'Pérgola aluminio tubo cuadrado', 'Pérgola de aluminio minimalista, estructura cuadrada reforzada en acabado Marrón.', 500.00, 'imagenes/ALU-PERG-CUA.jpg', 1, 6, '200x300', 8, 'Marrón'),
('ALU-PERG-CUA', 'Pérgola aluminio tubo cuadrado', 'Pérgola de aluminio minimalista, estructura cuadrada reforzada en acabado Blanco.', 520.00, 'imagenes/ALU-PERG-CUA.jpg', 1, 6, '250x350', 12, 'Blanco'),
('ALU-PERG-CUA', 'Pérgola aluminio tubo cuadrado', 'Pérgola de aluminio minimalista, estructura cuadrada reforzada en acabado Plata.', 550.00, 'imagenes/ALU-PERG-CUA.jpg', 1, 6, '250x350', 7, 'Plata'),
('ALU-PERG-CUA', 'Pérgola aluminio tubo cuadrado', 'Pérgola de aluminio minimalista, estructura cuadrada reforzada en acabado Marrón.', 550.00, 'imagenes/ALU-PERG-CUA.jpg', 1, 6, '250x350', 5, 'Marrón'),
('ALU-PERG-CUA', 'Pérgola aluminio tubo cuadrado', 'Pérgola de aluminio minimalista, estructura cuadrada reforzada en acabado Blanco.', 570.00, 'imagenes/ALU-PERG-CUA.jpg', 1, 6, '300x400', 9, 'Blanco'),
('ALU-PERG-CUA', 'Pérgola aluminio tubo cuadrado', 'Pérgola de aluminio minimalista, estructura cuadrada reforzada en acabado Plata.', 600.00, 'imagenes/ALU-PERG-CUA.jpg', 1, 6, '300x400', 4, 'Plata'),
('ALU-PERG-CUA', 'Pérgola aluminio tubo cuadrado', 'Pérgola de aluminio minimalista, estructura cuadrada reforzada en acabado Marrón.', 600.00, 'imagenes/ALU-PERG-CUA.jpg', 1, 6, '300x400', 3, 'Marrón');

-- 22. Pérgola de PVC redondo
INSERT INTO productos (referencia, nombre, descripcion, precio, imagen_url, id_material, id_categoria, medidas, stock, color) VALUES
('PVC-PERG-RED', 'Pérgola PVC tubo redondo', 'Pérgola clásica de PVC, resistente a la intemperie, tubo redondo en acabado Blanco.', 440.00, 'imagenes/PVC-PERG-RED.jpg', 2, 6, '200x300', 14, 'Blanco'),
('PVC-PERG-RED', 'Pérgola PVC tubo redondo', 'Pérgola clásica de PVC, resistente a la intemperie, tubo redondo en acabado Plata.', 470.00, 'imagenes/PVC-PERG-RED.jpg', 2, 6, '200x300', 9, 'Plata'),
('PVC-PERG-RED', 'Pérgola PVC tubo redondo', 'Pérgola clásica de PVC, resistente a la intemperie, tubo redondo en acabado Marrón.', 470.00, 'imagenes/PVC-PERG-RED.jpg', 2, 6, '200x300', 7, 'Marrón'),
('PVC-PERG-RED', 'Pérgola PVC tubo redondo', 'Pérgola clásica de PVC, resistente a la intemperie, tubo redondo en acabado Blanco.', 490.00, 'imagenes/PVC-PERG-RED.jpg', 2, 6, '250x350', 11, 'Blanco'),
('PVC-PERG-RED', 'Pérgola PVC tubo redondo', 'Pérgola clásica de PVC, resistente a la intemperie, tubo redondo en acabado Plata.', 520.00, 'imagenes/PVC-PERG-RED.jpg', 2, 6, '250x350', 6, 'Plata'),
('PVC-PERG-RED', 'Pérgola PVC tubo redondo', 'Pérgola clásica de PVC, resistente a la intemperie, tubo redondo en acabado Marrón.', 520.00, 'imagenes/PVC-PERG-RED.jpg', 2, 6, '250x350', 5, 'Marrón'),
('PVC-PERG-RED', 'Pérgola PVC tubo redondo', 'Pérgola clásica de PVC, resistente a la intemperie, tubo redondo en acabado Blanco.', 540.00, 'imagenes/PVC-PERG-RED.jpg', 2, 6, '300x400', 8, 'Blanco'),
('PVC-PERG-RED', 'Pérgola PVC tubo redondo', 'Pérgola clásica de PVC, resistente a la intemperie, tubo redondo en acabado Plata.', 570.00, 'imagenes/PVC-PERG-RED.jpg', 2, 6, '300x400', 4, 'Plata'),
('PVC-PERG-RED', 'Pérgola PVC tubo redondo', 'Pérgola clásica de PVC, resistente a la intemperie, tubo redondo en acabado Marrón.', 570.00, 'imagenes/PVC-PERG-RED.jpg', 2, 6, '300x400', 3, 'Marrón');