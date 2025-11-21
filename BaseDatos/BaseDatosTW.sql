CREATE DATABASE IF NOT EXISTS metalisteria;
USE metalisteria;

CREATE TABLE materiales (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(50) NOT NULL
);

CREATE TABLE categorias (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(50) NOT NULL
);

CREATE TABLE IF NOT EXISTS productos (
  id INT AUTO_INCREMENT PRIMARY KEY,
  referencia VARCHAR(40) NOT NULL UNIQUE,
  nombre VARCHAR(150) NOT NULL,
  id_material INT NOT NULL,
  id_categoria INT NOT NULL,
  medidas VARCHAR(50),
  stock INT DEFAULT 0,
  color VARCHAR(30),
  FOREIGN KEY (id_material) REFERENCES materiales(id),
  FOREIGN KEY (id_categoria) REFERENCES categorias(id)
);


INSERT INTO materiales (nombre) VALUES
('Aluminio'),   -- id 1
('PVC'),        -- id 2
('Hierro');     -- id 3

INSERT INTO categorias (nombre) VALUES
('Ventanas'),     -- id 1
('Balcones'),     -- id 2
('Rejas'),        -- id 3
('Escaleras'),    -- id 4
('Barandillas'),  -- id 5
('Pérgolas');     -- id 6

-- Ventana corredera de 2 hojas (Aluminio)
INSERT INTO productos (referencia, nombre, id_material, id_categoria, medidas, stock, color) VALUES
('ALU-VEN-COR2H-1','Ventana corredera de 2 hojas (Aluminio)','1','1','80x100','0','Blanco'),
('ALU-VEN-COR2H-2','Ventana corredera de 2 hojas (Aluminio)','1','1','80x100','0','Plata'),
('ALU-VEN-COR2H-3','Ventana corredera de 2 hojas (Aluminio)','1','1','80x100','0','Marrón'),
('ALU-VEN-COR2H-4','Ventana corredera de 2 hojas (Aluminio)','1','1','100x120','0','Blanco'),
('ALU-VEN-COR2H-5','Ventana corredera de 2 hojas (Aluminio)','1','1','100x120','0','Plata'),
('ALU-VEN-COR2H-6','Ventana corredera de 2 hojas (Aluminio)','1','1','100x120','0','Marrón'),
('ALU-VEN-COR2H-7','Ventana corredera de 2 hojas (Aluminio)','1','1','120x140','0','Blanco'),
('ALU-VEN-COR2H-8','Ventana corredera de 2 hojas (Aluminio)','1','1','120x140','0','Plata'),
('ALU-VEN-COR2H-9','Ventana corredera de 2 hojas (Aluminio)','1','1','120x140','0','Marrón');

-- Ventana corredera de 2 hojas (PVC)
INSERT INTO productos (referencia, nombre, id_material, id_categoria, medidas, stock, color) VALUES
('PVC-VEN-COR2H-1','Ventana corredera de 2 hojas (PVC)','2','1','80x100','0','Blanco'),
('PVC-VEN-COR2H-2','Ventana corredera de 2 hojas (PVC)','2','1','80x100','0','Plata'),
('PVC-VEN-COR2H-3','Ventana corredera de 2 hojas (PVC)','2','1','80x100','0','Marrón'),
('PVC-VEN-COR2H-4','Ventana corredera de 2 hojas (PVC)','2','1','100x120','0','Blanco'),
('PVC-VEN-COR2H-5','Ventana corredera de 2 hojas (PVC)','2','1','100x120','0','Plata'),
('PVC-VEN-COR2H-6','Ventana corredera de 2 hojas (PVC)','2','1','100x120','0','Marrón'),
('PVC-VEN-COR2H-7','Ventana corredera de 2 hojas (PVC)','2','1','120x140','0','Blanco'),
('PVC-VEN-COR2H-8','Ventana corredera de 2 hojas (PVC)','2','1','120x140','0','Plata'),
('PVC-VEN-COR2H-9','Ventana corredera de 2 hojas (PVC)','2','1','120x140','0','Marrón');


-- Balcón corredera de 2 hojas (Aluminio)
INSERT INTO productos (referencia, nombre, id_material, id_categoria, medidas, stock, color) VALUES
('ALU-BAL-COR2H-1','Balcón corredera de 2 hojas (Aluminio)','1','2','200x210','0','Blanco'),
('ALU-BAL-COR2H-2','Balcón corredera de 2 hojas (Aluminio)','1','2','200x210','0','Plata'),
('ALU-BAL-COR2H-3','Balcón corredera de 2 hojas (Aluminio)','1','2','200x210','0','Marrón'),
('ALU-BAL-COR2H-4','Balcón corredera de 2 hojas (Aluminio)','1','2','220x220','0','Blanco'),
('ALU-BAL-COR2H-5','Balcón corredera de 2 hojas (Aluminio)','1','2','220x220','0','Plata'),
('ALU-BAL-COR2H-6','Balcón corredera de 2 hojas (Aluminio)','1','2','220x220','0','Marrón'),
('ALU-BAL-COR2H-7','Balcón corredera de 2 hojas (Aluminio)','1','2','240x230','0','Blanco'),
('ALU-BAL-COR2H-8','Balcón corredera de 2 hojas (Aluminio)','1','2','240x230','0','Plata'),
('ALU-BAL-COR2H-9','Balcón corredera de 2 hojas (Aluminio)','1','2','240x230','0','Marrón');

-- Balcón corredera de 2 hojas (PVC)
INSERT INTO productos (referencia, nombre, id_material, id_categoria, medidas, stock, color) VALUES
('PVC-BAL-COR2H-1','Balcón corredera de 2 hojas (PVC)','2','2','200x210','0','Blanco'),
('PVC-BAL-COR2H-2','Balcón corredera de 2 hojas (PVC)','2','2','200x210','0','Plata'),
('PVC-BAL-COR2H-3','Balcón corredera de 2 hojas (PVC)','2','2','200x210','0','Marrón'),
('PVC-BAL-COR2H-4','Balcón corredera de 2 hojas (PVC)','2','2','220x220','0','Blanco'),
('PVC-BAL-COR2H-5','Balcón corredera de 2 hojas (PVC)','2','2','220x220','0','Plata'),
('PVC-BAL-COR2H-6','Balcón corredera de 2 hojas (PVC)','2','2','220x220','0','Marrón'),
('PVC-BAL-COR2H-7','Balcón corredera de 2 hojas (PVC)','2','2','240x230','0','Blanco'),
('PVC-BAL-COR2H-8','Balcón corredera de 2 hojas (PVC)','2','2','240x230','0','Plata'),
('PVC-BAL-COR2H-9','Balcón corredera de 2 hojas (PVC)','2','2','240x230','0','Marrón');

-- Balcón corredera 3 hojas tricarril (Aluminio)
INSERT INTO productos (referencia, nombre, id_material, id_categoria, medidas, stock, color) VALUES
('ALU-BAL-COR3H-1','Balcón corredera 3 hojas tricarril (Aluminio)','1','2','300x210','0','Blanco'),
('ALU-BAL-COR3H-2','Balcón corredera 3 hojas tricarril (Aluminio)','1','2','300x210','0','Plata'),
('ALU-BAL-COR3H-3','Balcón corredera 3 hojas tricarril (Aluminio)','1','2','300x210','0','Marrón'),
('ALU-BAL-COR3H-4','Balcón corredera 3 hojas tricarril (Aluminio)','1','2','320x220','0','Blanco'),
('ALU-BAL-COR3H-5','Balcón corredera 3 hojas tricarril (Aluminio)','1','2','320x220','0','Plata'),
('ALU-BAL-COR3H-6','Balcón corredera 3 hojas tricarril (Aluminio)','1','2','320x220','0','Marrón'),
('ALU-BAL-COR3H-7','Balcón corredera 3 hojas tricarril (Aluminio)','1','2','340x230','0','Blanco'),
('ALU-BAL-COR3H-8','Balcón corredera 3 hojas tricarril (Aluminio)','1','2','340x230','0','Plata'),
('ALU-BAL-COR3H-9','Balcón corredera 3 hojas tricarril (Aluminio)','1','2','340x230','0','Marrón');

-- Balcón corredera 3 hojas tricarril (PVC)
INSERT INTO productos (referencia, nombre, id_material, id_categoria, medidas, stock, color) VALUES
('PVC-BAL-COR3H-1','Balcón corredera 3 hojas tricarril (PVC)','2','2','300x210','0','Blanco'),
('PVC-BAL-COR3H-2','Balcón corredera 3 hojas tricarril (PVC)','2','2','300x210','0','Plata'),
('PVC-BAL-COR3H-3','Balcón corredera 3 hojas tricarril (PVC)','2','2','300x210','0','Marrón'),
('PVC-BAL-COR3H-4','Balcón corredera 3 hojas tricarril (PVC)','2','2','320x220','0','Blanco'),
('PVC-BAL-COR3H-5','Balcón corredera 3 hojas tricarril (PVC)','2','2','320x220','0','Plata'),
('PVC-BAL-COR3H-6','Balcón corredera 3 hojas tricarril (PVC)','2','2','320x220','0','Marrón'),
('PVC-BAL-COR3H-7','Balcón corredera 3 hojas tricarril (PVC)','2','2','340x230','0','Blanco'),
('PVC-BAL-COR3H-8','Balcón corredera 3 hojas tricarril (PVC)','2','2','340x230','0','Plata'),
('PVC-BAL-COR3H-9','Balcón corredera 3 hojas tricarril (PVC)','2','2','340x230','0','Marrón');

-- Ventana fija (Aluminio)
INSERT INTO productos (referencia, nombre, id_material, id_categoria, medidas, stock, color) VALUES
('ALU-VEN-FIJA-1','Ventana fija (Aluminio)','1','1','80x100','0','Blanco'),
('ALU-VEN-FIJA-2','Ventana fija (Aluminio)','1','1','80x100','0','Plata'),
('ALU-VEN-FIJA-3','Ventana fija (Aluminio)','1','1','80x100','0','Marrón'),
('ALU-VEN-FIJA-4','Ventana fija (Aluminio)','1','1','100x100','0','Blanco'),
('ALU-VEN-FIJA-5','Ventana fija (Aluminio)','1','1','100x100','0','Plata'),
('ALU-VEN-FIJA-6','Ventana fija (Aluminio)','1','1','100x100','0','Marrón'),
('ALU-VEN-FIJA-7','Ventana fija (Aluminio)','1','1','120x120','0','Blanco'),
('ALU-VEN-FIJA-8','Ventana fija (Aluminio)','1','1','120x120','0','Plata'),
('ALU-VEN-FIJA-9','Ventana fija (Aluminio)','1','1','120x120','0','Marrón');

-- Ventana fija (PVC)
INSERT INTO productos (referencia, nombre, id_material, id_categoria, medidas, stock, color) VALUES
('PVC-VEN-FIJA-1','Ventana fija (PVC)','2','1','80x100','0','Blanco'),
('PVC-VEN-FIJA-2','Ventana fija (PVC)','2','1','80x100','0','Plata'),
('PVC-VEN-FIJA-3','Ventana fija (PVC)','2','1','80x100','0','Marrón'),
('PVC-VEN-FIJA-4','Ventana fija (PVC)','2','1','100x100','0','Blanco'),
('PVC-VEN-FIJA-5','Ventana fija (PVC)','2','1','100x100','0','Plata'),
('PVC-VEN-FIJA-6','Ventana fija (PVC)','2','1','100x100','0','Marrón'),
('PVC-VEN-FIJA-7','Ventana fija (PVC)','2','1','120x120','0','Blanco'),
('PVC-VEN-FIJA-8','Ventana fija (PVC)','2','1','120x120','0','Plata'),
('PVC-VEN-FIJA-9','Ventana fija (PVC)','2','1','120x120','0','Marrón');

-- Ventana abatible de 1 hoja (Aluminio)
INSERT INTO productos (referencia, nombre, id_material, id_categoria, medidas, stock, color) VALUES
('ALU-VEN-ABA1H-1','Ventana abatible 1 hoja (Aluminio)','1','1','60x100','0','Blanco'),
('ALU-VEN-ABA1H-2','Ventana abatible 1 hoja (Aluminio)','1','1','60x100','0','Plata'),
('ALU-VEN-ABA1H-3','Ventana abatible 1 hoja (Aluminio)','1','1','60x100','0','Marrón'),
('ALU-VEN-ABA1H-4','Ventana abatible 1 hoja (Aluminio)','1','1','70x120','0','Blanco'),
('ALU-VEN-ABA1H-5','Ventana abatible 1 hoja (Aluminio)','1','1','70x120','0','Plata'),
('ALU-VEN-ABA1H-6','Ventana abatible 1 hoja (Aluminio)','1','1','70x120','0','Marrón'),
('ALU-VEN-ABA1H-7','Ventana abatible 1 hoja (Aluminio)','1','1','80x140','0','Blanco'),
('ALU-VEN-ABA1H-8','Ventana abatible 1 hoja (Aluminio)','1','1','80x140','0','Plata'),
('ALU-VEN-ABA1H-9','Ventana abatible 1 hoja (Aluminio)','1','1','80x140','0','Marrón');

-- Ventana abatible de 1 hoja (PVC)
INSERT INTO productos (referencia, nombre, id_material, id_categoria, medidas, stock, color) VALUES
('PVC-VEN-ABA1H-1','Ventana abatible 1 hoja (PVC)','2','1','60x100','0','Blanco'),
('PVC-VEN-ABA1H-2','Ventana abatible 1 hoja (PVC)','2','1','60x100','0','Plata'),
('PVC-VEN-ABA1H-3','Ventana abatible 1 hoja (PVC)','2','1','60x100','0','Marrón'),
('PVC-VEN-ABA1H-4','Ventana abatible 1 hoja (PVC)','2','1','70x120','0','Blanco'),
('PVC-VEN-ABA1H-5','Ventana abatible 1 hoja (PVC)','2','1','70x120','0','Plata'),
('PVC-VEN-ABA1H-6','Ventana abatible 1 hoja (PVC)','2','1','70x120','0','Marrón'),
('PVC-VEN-ABA1H-7','Ventana abatible 1 hoja (PVC)','2','1','80x140','0','Blanco'),
('PVC-VEN-ABA1H-8','Ventana abatible 1 hoja (PVC)','2','1','80x140','0','Plata'),
('PVC-VEN-ABA1H-9','Ventana abatible 1 hoja (PVC)','2','1','80x140','0','Marrón');

-- Ventana abatible de 2 hojas (Aluminio)
INSERT INTO productos (referencia, nombre, id_material, id_categoria, medidas, stock, color) VALUES
('ALU-VEN-ABA2H-1','Ventana abatible 2 hojas (Aluminio)','1','1','120x100','0','Blanco'),
('ALU-VEN-ABA2H-2','Ventana abatible 2 hojas (Aluminio)','1','1','120x100','0','Plata'),
('ALU-VEN-ABA2H-3','Ventana abatible 2 hojas (Aluminio)','1','1','120x100','0','Marrón'),
('ALU-VEN-ABA2H-4','Ventana abatible 2 hojas (Aluminio)','1','1','140x120','0','Blanco'),
('ALU-VEN-ABA2H-5','Ventana abatible 2 hojas (Aluminio)','1','1','140x120','0','Plata'),
('ALU-VEN-ABA2H-6','Ventana abatible 2 hojas (Aluminio)','1','1','140x120','0','Marrón'),
('ALU-VEN-ABA2H-7','Ventana abatible 2 hojas (Aluminio)','1','1','160x140','0','Blanco'),
('ALU-VEN-ABA2H-8','Ventana abatible 2 hojas (Aluminio)','1','1','160x140','0','Plata'),
('ALU-VEN-ABA2H-9','Ventana abatible 2 hojas (Aluminio)','1','1','160x140','0','Marrón');

-- Ventana abatible 2 hojas con oscilobatiente (Aluminio)
INSERT INTO productos (referencia, nombre, id_material, id_categoria, medidas, stock, color) VALUES
('ALU-VEN-ABA2H-OB-1','Ventana abatible 2 hojas con oscilobatiente (Aluminio)','1','1','120x100','0','Blanco'),
('ALU-VEN-ABA2H-OB-2','Ventana abatible 2 hojas con oscilobatiente (Aluminio)','1','1','120x100','0','Plata'),
('ALU-VEN-ABA2H-OB-3','Ventana abatible 2 hojas con oscilobatiente (Aluminio)','1','1','120x100','0','Marrón'),
('ALU-VEN-ABA2H-OB-4','Ventana abatible 2 hojas con oscilobatiente (Aluminio)','1','1','140x120','0','Blanco'),
('ALU-VEN-ABA2H-OB-5','Ventana abatible 2 hojas con oscilobatiente (Aluminio)','1','1','140x120','0','Plata'),
('ALU-VEN-ABA2H-OB-6','Ventana abatible 2 hojas con oscilobatiente (Aluminio)','1','1','140x120','0','Marrón'),
('ALU-VEN-ABA2H-OB-7','Ventana abatible 2 hojas con oscilobatiente (Aluminio)','1','1','160x140','0','Blanco'),
('ALU-VEN-ABA2H-OB-8','Ventana abatible 2 hojas con oscilobatiente (Aluminio)','1','1','160x140','0','Plata'),
('ALU-VEN-ABA2H-OB-9','Ventana abatible 2 hojas con oscilobatiente (Aluminio)','1','1','160x140','0','Marrón');

-- Ventana abatible de 2 hojas (PVC)
INSERT INTO productos (referencia, nombre, id_material, id_categoria, medidas, stock, color) VALUES
('PVC-VEN-ABA2H-1','Ventana abatible 2 hojas (PVC)','2','1','120x100','0','Blanco'),
('PVC-VEN-ABA2H-2','Ventana abatible 2 hojas (PVC)','2','1','120x100','0','Plata'),
('PVC-VEN-ABA2H-3','Ventana abatible 2 hojas (PVC)','2','1','120x100','0','Marrón'),
('PVC-VEN-ABA2H-4','Ventana abatible 2 hojas (PVC)','2','1','140x120','0','Blanco'),
('PVC-VEN-ABA2H-5','Ventana abatible 2 hojas (PVC)','2','1','140x120','0','Plata'),
('PVC-VEN-ABA2H-6','Ventana abatible 2 hojas (PVC)','2','1','140x120','0','Marrón'),
('PVC-VEN-ABA2H-7','Ventana abatible 2 hojas (PVC)','2','1','160x140','0','Blanco'),
('PVC-VEN-ABA2H-8','Ventana abatible 2 hojas (PVC)','2','1','160x140','0','Plata'),
('PVC-VEN-ABA2H-9','Ventana abatible 2 hojas (PVC)','2','1','160x140','0','Marrón');

-- Ventana abatible 2 hojas con oscilobatiente (PVC)
INSERT INTO productos (referencia, nombre, id_material, id_categoria, medidas, stock, color) VALUES
('PVC-VEN-ABA2H-OB-1','Ventana abatible 2 hojas con oscilobatiente (PVC)','2','1','120x100','0','Blanco'),
('PVC-VEN-ABA2H-OB-2','Ventana abatible 2 hojas con oscilobatiente (PVC)','2','1','120x100','0','Plata'),
('PVC-VEN-ABA2H-OB-3','Ventana abatible 2 hojas con oscilobatiente (PVC)','2','1','120x100','0','Marrón'),
('PVC-VEN-ABA2H-OB-4','Ventana abatible 2 hojas con oscilobatiente (PVC)','2','1','140x120','0','Blanco'),
('PVC-VEN-ABA2H-OB-5','Ventana abatible 2 hojas con oscilobatiente (PVC)','2','1','140x120','0','Plata'),
('PVC-VEN-ABA2H-OB-6','Ventana abatible 2 hojas con oscilobatiente (PVC)','2','1','140x120','0','Marrón'),
('PVC-VEN-ABA2H-OB-7','Ventana abatible 2 hojas con oscilobatiente (PVC)','2','1','160x140','0','Blanco'),
('PVC-VEN-ABA2H-OB-8','Ventana abatible 2 hojas con oscilobatiente (PVC)','2','1','160x140','0','Plata'),
('PVC-VEN-ABA2H-OB-9','Ventana abatible 2 hojas con oscilobatiente (PVC)','2','1','160x140','0','Marrón');

-- Balcón abatible de 1 hoja (Aluminio) - mismos colores y medidas que ventana
INSERT INTO productos (referencia, nombre, id_material, id_categoria, medidas, stock, color) VALUES
('ALU-BAL-ABA1H-1','Balcón abatible 1 hoja (Aluminio)','1','2','60x100','0','Blanco'),
('ALU-BAL-ABA1H-2','Balcón abatible 1 hoja (Aluminio)','1','2','60x100','0','Plata'),
('ALU-BAL-ABA1H-3','Balcón abatible 1 hoja (Aluminio)','1','2','60x100','0','Marrón'),
('ALU-BAL-ABA1H-4','Balcón abatible 1 hoja (Aluminio)','1','2','70x120','0','Blanco'),
('ALU-BAL-ABA1H-5','Balcón abatible 1 hoja (Aluminio)','1','2','70x120','0','Plata'),
('ALU-BAL-ABA1H-6','Balcón abatible 1 hoja (Aluminio)','1','2','70x120','0','Marrón'),
('ALU-BAL-ABA1H-7','Balcón abatible 1 hoja (Aluminio)','1','2','80x140','0','Blanco'),
('ALU-BAL-ABA1H-8','Balcón abatible 1 hoja (Aluminio)','1','2','80x140','0','Plata'),
('ALU-BAL-ABA1H-9','Balcón abatible 1 hoja (Aluminio)','1','2','80x140','0','Marrón');

-- Balcón abatible de 1 hoja (PVC)
INSERT INTO productos (referencia, nombre, id_material, id_categoria, medidas, stock, color) VALUES
('PVC-BAL-ABA1H-1','Balcón abatible 1 hoja (PVC)','2','2','60x100','0','Blanco'),
('PVC-BAL-ABA1H-2','Balcón abatible 1 hoja (PVC)','2','2','60x100','0','Plata'),
('PVC-BAL-ABA1H-3','Balcón abatible 1 hoja (PVC)','2','2','60x100','0','Marrón'),
('PVC-BAL-ABA1H-4','Balcón abatible 1 hoja (PVC)','2','2','70x120','0','Blanco'),
('PVC-BAL-ABA1H-5','Balcón abatible 1 hoja (PVC)','2','2','70x120','0','Plata'),
('PVC-BAL-ABA1H-6','Balcón abatible 1 hoja (PVC)','2','2','70x120','0','Marrón'),
('PVC-BAL-ABA1H-7','Balcón abatible 1 hoja (PVC)','2','2','80x140','0','Blanco'),
('PVC-BAL-ABA1H-8','Balcón abatible 1 hoja (PVC)','2','2','80x140','0','Plata'),
('PVC-BAL-ABA1H-9','Balcón abatible 1 hoja (PVC)','2','2','80x140','0','Marrón');

-- Balcón abatible de 2 hojas (Aluminio)
INSERT INTO productos (referencia, nombre, id_material, id_categoria, medidas, stock, color) VALUES
('ALU-BAL-ABA2H-1','Balcón abatible 2 hojas (Aluminio)','1','2','120x100','0','Blanco'),
('ALU-BAL-ABA2H-2','Balcón abatible 2 hojas (Aluminio)','1','2','120x100','0','Plata'),
('ALU-BAL-ABA2H-3','Balcón abatible 2 hojas (Aluminio)','1','2','120x100','0','Marrón'),
('ALU-BAL-ABA2H-4','Balcón abatible 2 hojas (Aluminio)','1','2','140x120','0','Blanco'),
('ALU-BAL-ABA2H-5','Balcón abatible 2 hojas (Aluminio)','1','2','140x120','0','Plata'),
('ALU-BAL-ABA2H-6','Balcón abatible 2 hojas (Aluminio)','1','2','140x120','0','Marrón'),
('ALU-BAL-ABA2H-7','Balcón abatible 2 hojas (Aluminio)','1','2','160x140','0','Blanco'),
('ALU-BAL-ABA2H-8','Balcón abatible 2 hojas (Aluminio)','1','2','160x140','0','Plata'),
('ALU-BAL-ABA2H-9','Balcón abatible 2 hojas (Aluminio)','1','2','160x140','0','Marrón');

-- Balcón abatible de 2 hojas (PVC)
INSERT INTO productos (referencia, nombre, id_material, id_categoria, medidas, stock, color) VALUES
('PVC-BAL-ABA2H-1','Balcón abatible 2 hojas (PVC)','2','2','120x100','0','Blanco'),
('PVC-BAL-ABA2H-2','Balcón abatible 2 hojas (PVC)','2','2','120x100','0','Plata'),
('PVC-BAL-ABA2H-3','Balcón abatible 2 hojas (PVC)','2','2','120x100','0','Marrón'),
('PVC-BAL-ABA2H-4','Balcón abatible 2 hojas (PVC)','2','2','140x120','0','Blanco'),
('PVC-BAL-ABA2H-5','Balcón abatible 2 hojas (PVC)','2','2','140x120','0','Plata'),
('PVC-BAL-ABA2H-6','Balcón abatible 2 hojas (PVC)','2','2','140x120','0','Marrón'),
('PVC-BAL-ABA2H-7','Balcón abatible 2 hojas (PVC)','2','2','160x140','0','Blanco'),
('PVC-BAL-ABA2H-8','Balcón abatible 2 hojas (PVC)','2','2','160x140','0','Plata'),
('PVC-BAL-ABA2H-9','Balcón abatible 2 hojas (PVC)','2','2','160x140','0','Marrón');

-- Pérgola de aluminio redondo
INSERT INTO productos (referencia, nombre, id_material, id_categoria, medidas, stock, color) VALUES
('ALU-PERG-RED-1','Pérgola aluminio tubo redondo','1','3','200x300','0','Blanco'),
('ALU-PERG-RED-2','Pérgola aluminio tubo redondo','1','3','200x300','0','Plata'),
('ALU-PERG-RED-3','Pérgola aluminio tubo redondo','1','3','200x300','0','Marrón'),
('ALU-PERG-RED-4','Pérgola aluminio tubo redondo','1','3','250x350','0','Blanco'),
('ALU-PERG-RED-5','Pérgola aluminio tubo redondo','1','3','250x350','0','Plata'),
('ALU-PERG-RED-6','Pérgola aluminio tubo redondo','1','3','250x350','0','Marrón'),
('ALU-PERG-RED-7','Pérgola aluminio tubo redondo','1','3','300x400','0','Blanco'),
('ALU-PERG-RED-8','Pérgola aluminio tubo redondo','1','3','300x400','0','Plata'),
('ALU-PERG-RED-9','Pérgola aluminio tubo redondo','1','3','300x400','0','Marrón');

-- Pérgola de PVC redondo
INSERT INTO productos (referencia, nombre, id_material, id_categoria, medidas, stock, color) VALUES
('PVC-PERG-CUA-1','Pérgola PVC tubo cuadrado','2','3','200x300','0','Blanco'),
('PVC-PERG-CUA-2','Pérgola PVC tubo cuadrado','2','3','200x300','0','Plata'),
('PVC-PERG-CUA-3','Pérgola PVC tubo cuadrado','2','3','200x300','0','Marrón'),
('PVC-PERG-CUA-4','Pérgola PVC tubo cuadrado','2','3','250x350','0','Blanco'),
('PVC-PERG-CUA-5','Pérgola PVC tubo cuadrado','2','3','250x350','0','Plata'),
('PVC-PERG-CUA-6','Pérgola PVC tubo cuadrado','2','3','250x350','0','Marrón'),
('PVC-PERG-CUA-7','Pérgola PVC tubo cuadrado','2','3','300x400','0','Blanco'),
('PVC-PERG-CUA-8','Pérgola PVC tubo cuadrado','2','3','300x400','0','Plata'),
('PVC-PERG-CUA-9','Pérgola PVC tubo cuadrado','2','3','300x400','0','Marrón');

-- Pérgola de aluminio cuadrado
INSERT INTO productos (referencia, nombre, id_material, id_categoria, medidas, stock, color) VALUES
('ALU-PERG-CUA-1','Pérgola aluminio tubo cuadrado','1','3','200x300','0','Blanco'),
('ALU-PERG-CUA-2','Pérgola aluminio tubo cuadrado','1','3','200x300','0','Plata'),
('ALU-PERG-CUA-3','Pérgola aluminio tubo cuadrado','1','3','200x300','0','Marrón'),
('ALU-PERG-CUA-4','Pérgola aluminio tubo cuadrado','1','3','250x350','0','Blanco'),
('ALU-PERG-CUA-5','Pérgola aluminio tubo cuadrado','1','3','250x350','0','Plata'),
('ALU-PERG-CUA-6','Pérgola aluminio tubo cuadrado','1','3','250x350','0','Marrón'),
('ALU-PERG-CUA-7','Pérgola aluminio tubo cuadrado','1','3','300x400','0','Blanco'),
('ALU-PERG-CUA-8','Pérgola aluminio tubo cuadrado','1','3','300x400','0','Plata'),
('ALU-PERG-CUA-9','Pérgola aluminio tubo cuadrado','1','3','300x400','0','Marrón');

-- Pérgola de aluminio redondo
INSERT INTO productos (referencia, nombre, id_material, id_categoria, medidas, stock, color) VALUES
('PVC-PERG-RED-1','Pérgola PVC tubo redondo','2','3','200x300','0','Blanco'),
('PVC-PERG-RED-2','Pérgola PVC tubo redondo','2','3','200x300','0','Plata'),
('PVC-PERG-RED-3','Pérgola PVC tubo redondo','2','3','200x300','0','Marrón'),
('PVC-PERG-RED-4','Pérgola PVC tubo redondo','2','3','250x350','0','Blanco'),
('PVC-PERG-RED-5','Pérgola PVC tubo redondo','2','3','250x350','0','Plata'),
('PVC-PERG-RED-6','Pérgola PVC tubo redondo','2','3','250x350','0','Marrón'),
('PVC-PERG-RED-7','Pérgola PVC tubo redondo','2','3','300x400','0','Blanco'),
('PVC-PERG-RED-8','Pérgola PVC tubo redondo','2','3','300x400','0','Plata'),
('PVC-PERG-RED-9','Pérgola PVC tubo redondo','2','3','300x400','0','Marrón');