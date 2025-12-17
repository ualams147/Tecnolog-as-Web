-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 16-12-2025 a las 21:58:36
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `metalisteria`
--



-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `carrito`
--



DROP TABLE IF EXISTS `carrito`;
CREATE TABLE `carrito` (
  `id` int(11) NOT NULL,
  `cliente_id` int(11) NOT NULL,
  `producto_id` int(11) NOT NULL,
  `cantidad` int(11) DEFAULT 1,
  `fecha_agregado` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `carrito`
--

INSERT INTO `carrito` (`id`, `cliente_id`, `producto_id`, `cantidad`, `fecha_agregado`) VALUES
(23, 2, 10, 1, '2025-12-16 20:47:09');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `categorias`
--

DROP TABLE IF EXISTS `categorias`;
CREATE TABLE `categorias` (
  `id` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `categorias`
--

INSERT INTO `categorias` (`id`, `nombre`) VALUES
(1, 'Ventanas'),
(2, 'Balcones'),
(3, 'Rejas'),
(4, 'Escaleras'),
(5, 'Barandillas'),
(6, 'Pérgolas');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `clientes`
--

DROP TABLE IF EXISTS `clientes`;
CREATE TABLE `clientes` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `apellidos` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `dni` varchar(20) DEFAULT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `rol` enum('admin','cliente') DEFAULT 'cliente',
  `direccion` varchar(255) DEFAULT NULL,
  `numero` varchar(20) DEFAULT NULL,
  `piso` varchar(20) DEFAULT NULL,
  `ciudad` varchar(100) DEFAULT NULL,
  `provincia` varchar(100) DEFAULT 'Granada',
  `codigo_postal` varchar(10) DEFAULT NULL,
  `fecha_registro` datetime DEFAULT current_timestamp(),
  `activo` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `clientes`
--

INSERT INTO `clientes` (`id`, `nombre`, `apellidos`, `email`, `password`, `dni`, `telefono`, `rol`, `direccion`, `numero`, `piso`, `ciudad`, `provincia`, `codigo_postal`, `fecha_registro`, `activo`) VALUES
(1, 'Juan', 'García López', 'juan.garcia@email.com', '1234', '44174833K', '600111222', 'admin', 'Calle Recogidas', '15', '2A', 'Granada', 'Granada', '18005', '2025-12-16 14:38:25', 1),
(2, 'María', 'Rodríguez Pérez', 'maria.rod@email.com', '1234', '42615152Q', '611222333', 'cliente', 'Av. Constitución', '20', '1º B', 'Granada', 'Granada', '18012', '2025-12-16 14:38:25', 1),
(3, 'Antonio', 'Fernández Ruiz', 'antonio.fer@email.com', '1234', '33569126M', '622333444', 'cliente', 'Calle Real', '45', 'Bajo', 'Armilla', 'Granada', '18100', '2025-12-16 14:38:25', 1),
(4, 'Laura', 'Sánchez Mota', 'laura.san@email.com', '1234', '23123455Z', '633444555', 'cliente', 'Camino de Ronda', '100', '3º D', 'Granada', 'Granada', '18003', '2025-12-16 14:38:25', 1),
(5, 'Carlos', 'Martínez Gómez', 'carlos.mar@email.com', '1234', '89926046W', '644555666', 'cliente', 'Calle Ancha', '12', '', 'Motril', 'Granada', '18600', '2025-12-16 14:38:25', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detalle_ventas`
--

DROP TABLE IF EXISTS `detalle_ventas`;
CREATE TABLE `detalle_ventas` (
  `id` int(11) NOT NULL,
  `id_venta` int(11) NOT NULL,
  `id_producto` int(11) NOT NULL,
  `cantidad` int(11) NOT NULL,
  `precio_unitario` decimal(10,2) NOT NULL,
  `subtotal` decimal(10,2) GENERATED ALWAYS AS (`cantidad` * `precio_unitario`) STORED
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `detalle_ventas`
--

INSERT INTO `detalle_ventas` (`id`, `id_venta`, `id_producto`, `cantidad`, `precio_unitario`) VALUES
(1, 1, 1, 2, 120.00),
(2, 1, 2, 1, 130.00),
(3, 2, 163, 1, 450.00),
(17, 12, 37, 1, 600.00),
(18, 13, 37, 1, 600.00),
(19, 14, 1, 7, 120.00),
(20, 14, 172, 3, 460.00),
(21, 15, 1, 1, 120.00),
(22, 16, 1, 1, 120.00),
(23, 17, 1, 1, 120.00),
(24, 18, 1, 2, 120.00);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `materiales`
--

DROP TABLE IF EXISTS `materiales`;
CREATE TABLE `materiales` (
  `id` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `materiales`
--

INSERT INTO `materiales` (`id`, `nombre`) VALUES
(1, 'Aluminio'),
(2, 'PVC'),
(3, 'Hierro');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `productos`
--

DROP TABLE IF EXISTS `productos`;
CREATE TABLE `productos` (
  `id` int(11) NOT NULL,
  `referencia` varchar(40) NOT NULL,
  `nombre` varchar(150) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `precio` decimal(10,2) DEFAULT 0.00,
  `imagen_url` varchar(255) DEFAULT NULL,
  `id_material` int(11) NOT NULL,
  `id_categoria` int(11) NOT NULL,
  `medidas` varchar(50) DEFAULT NULL,
  `color` varchar(30) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `productos`
--

INSERT INTO `productos` (`id`, `referencia`, `nombre`, `descripcion`, `precio`, `imagen_url`, `id_material`, `id_categoria`, `medidas`, `color`) VALUES
(1, 'ALU-VEN-COR2H', 'Ventana corredera de 2 hojas (Aluminio)', 'Ventana de aluminio ligera y funcional, sistema de apertura corredera en acabado Blanco.', 120.00, 'imagenes/ALU-VEN-COR2H.jpg', 1, 1, '80x100', 'Blanco'),
(2, 'ALU-VEN-COR2H', 'Ventana corredera de 2 hojas (Aluminio)', 'Ventana de aluminio ligera y funcional, sistema de apertura corredera en acabado Plata.', 130.00, 'imagenes/ALU-VEN-COR2H.jpg', 1, 1, '80x100', 'Plata'),
(3, 'ALU-VEN-COR2H', 'Ventana corredera de 2 hojas (Aluminio)', 'Ventana de aluminio ligera y funcional, sistema de apertura corredera en acabado Marrón.', 130.00, 'imagenes/ALU-VEN-COR2H.jpg', 1, 1, '80x100', 'Marrón'),
(4, 'ALU-VEN-COR2H', 'Ventana corredera de 2 hojas (Aluminio)', 'Ventana de aluminio ligera y funcional, sistema de apertura corredera en acabado Blanco.', 140.00, 'imagenes/ALU-VEN-COR2H.jpg', 1, 1, '100x120', 'Blanco'),
(5, 'ALU-VEN-COR2H', 'Ventana corredera de 2 hojas (Aluminio)', 'Ventana de aluminio ligera y funcional, sistema de apertura corredera en acabado Plata.', 150.00, 'imagenes/ALU-VEN-COR2H.jpg', 1, 1, '100x120', 'Plata'),
(6, 'ALU-VEN-COR2H', 'Ventana corredera de 2 hojas (Aluminio)', 'Ventana de aluminio ligera y funcional, sistema de apertura corredera en acabado Marrón.', 150.00, 'imagenes/ALU-VEN-COR2H.jpg', 1, 1, '100x120', 'Marrón'),
(7, 'ALU-VEN-COR2H', 'Ventana corredera de 2 hojas (Aluminio)', 'Ventana de aluminio ligera y funcional, sistema de apertura corredera en acabado Blanco.', 160.00, 'imagenes/ALU-VEN-COR2H.jpg', 1, 1, '120x140', 'Blanco'),
(8, 'ALU-VEN-COR2H', 'Ventana corredera de 2 hojas (Aluminio)', 'Ventana de aluminio ligera y funcional, sistema de apertura corredera en acabado Plata.', 170.00, 'imagenes/ALU-VEN-COR2H.jpg', 1, 1, '120x140', 'Plata'),
(9, 'ALU-VEN-COR2H', 'Ventana corredera de 2 hojas (Aluminio)', 'Ventana de aluminio ligera y funcional, sistema de apertura corredera en acabado Marrón.', 170.00, 'imagenes/ALU-VEN-COR2H.jpg', 1, 1, '120x140', 'Marrón'),
(10, 'PVC-VEN-COR2H', 'Ventana corredera de 2 hojas (PVC)', 'Ventana de PVC con gran aislamiento térmico y acústico en acabado Blanco.', 180.00, 'imagenes/PVC-VEN-COR2H.jpg', 2, 1, '80x100', 'Blanco'),
(11, 'PVC-VEN-COR2H', 'Ventana corredera de 2 hojas (PVC)', 'Ventana de PVC con gran aislamiento térmico y acústico en acabado Plata.', 190.00, 'imagenes/PVC-VEN-COR2H.jpg', 2, 1, '80x100', 'Plata'),
(12, 'PVC-VEN-COR2H', 'Ventana corredera de 2 hojas (PVC)', 'Ventana de PVC con gran aislamiento térmico y acústico en acabado Marrón.', 190.00, 'imagenes/PVC-VEN-COR2H.jpg', 2, 1, '80x100', 'Marrón'),
(13, 'PVC-VEN-COR2H', 'Ventana corredera de 2 hojas (PVC)', 'Ventana de PVC con gran aislamiento térmico y acústico en acabado Blanco.', 200.00, 'imagenes/PVC-VEN-COR2H.jpg', 2, 1, '100x120', 'Blanco'),
(14, 'PVC-VEN-COR2H', 'Ventana corredera de 2 hojas (PVC)', 'Ventana de PVC con gran aislamiento térmico y acústico en acabado Plata.', 210.00, 'imagenes/PVC-VEN-COR2H.jpg', 2, 1, '100x120', 'Plata'),
(15, 'PVC-VEN-COR2H', 'Ventana corredera de 2 hojas (PVC)', 'Ventana de PVC con gran aislamiento térmico y acústico en acabado Marrón.', 210.00, 'imagenes/PVC-VEN-COR2H.jpg', 2, 1, '100x120', 'Marrón'),
(16, 'PVC-VEN-COR2H', 'Ventana corredera de 2 hojas (PVC)', 'Ventana de PVC con gran aislamiento térmico y acústico en acabado Blanco.', 220.00, 'imagenes/PVC-VEN-COR2H.jpg', 2, 1, '120x140', 'Blanco'),
(17, 'PVC-VEN-COR2H', 'Ventana corredera de 2 hojas (PVC)', 'Ventana de PVC con gran aislamiento térmico y acústico en acabado Plata.', 230.00, 'imagenes/PVC-VEN-COR2H.jpg', 2, 1, '120x140', 'Plata'),
(18, 'PVC-VEN-COR2H', 'Ventana corredera de 2 hojas (PVC)', 'Ventana de PVC con gran aislamiento térmico y acústico en acabado Marrón.', 230.00, 'imagenes/PVC-VEN-COR2H.jpg', 2, 1, '120x140', 'Marrón'),
(19, 'ALU-BAL-COR2H', 'Balcón corredera de 2 hojas (Aluminio)', 'Balconera amplia de aluminio, ideal para terrazas, en acabado Blanco.', 300.00, 'imagenes/ALU-BAL-COR2H.jpg', 1, 2, '200x210', 'Blanco'),
(20, 'ALU-BAL-COR2H', 'Balcón corredera de 2 hojas (Aluminio)', 'Balconera amplia de aluminio, ideal para terrazas, en acabado Plata.', 320.00, 'imagenes/ALU-BAL-COR2H.jpg', 1, 2, '200x210', 'Plata'),
(21, 'ALU-BAL-COR2H', 'Balcón corredera de 2 hojas (Aluminio)', 'Balconera amplia de aluminio, ideal para terrazas, en acabado Marrón.', 320.00, 'imagenes/ALU-BAL-COR2H.jpg', 1, 2, '200x210', 'Marrón'),
(22, 'ALU-BAL-COR2H', 'Balcón corredera de 2 hojas (Aluminio)', 'Balconera amplia de aluminio, ideal para terrazas, en acabado Blanco.', 350.00, 'imagenes/ALU-BAL-COR2H.jpg', 1, 2, '220x220', 'Blanco'),
(23, 'ALU-BAL-COR2H', 'Balcón corredera de 2 hojas (Aluminio)', 'Balconera amplia de aluminio, ideal para terrazas, en acabado Plata.', 370.00, 'imagenes/ALU-BAL-COR2H.jpg', 1, 2, '220x220', 'Plata'),
(24, 'ALU-BAL-COR2H', 'Balcón corredera de 2 hojas (Aluminio)', 'Balconera amplia de aluminio, ideal para terrazas, en acabado Marrón.', 370.00, 'imagenes/ALU-BAL-COR2H.jpg', 1, 2, '220x220', 'Marrón'),
(25, 'ALU-BAL-COR2H', 'Balcón corredera de 2 hojas (Aluminio)', 'Balconera amplia de aluminio, ideal para terrazas, en acabado Blanco.', 400.00, 'imagenes/ALU-BAL-COR2H.jpg', 1, 2, '240x230', 'Blanco'),
(26, 'ALU-BAL-COR2H', 'Balcón corredera de 2 hojas (Aluminio)', 'Balconera amplia de aluminio, ideal para terrazas, en acabado Plata.', 420.00, 'imagenes/ALU-BAL-COR2H.jpg', 1, 2, '240x230', 'Plata'),
(27, 'ALU-BAL-COR2H', 'Balcón corredera de 2 hojas (Aluminio)', 'Balconera amplia de aluminio, ideal para terrazas, en acabado Marrón.', 420.00, 'imagenes/ALU-BAL-COR2H.jpg', 1, 2, '240x230', 'Marrón'),
(28, 'PVC-BAL-COR2H', 'Balcón corredera de 2 hojas (PVC)', 'Balconera de PVC de alta eficiencia energética en acabado Blanco.', 450.00, 'imagenes/PVC-BAL-COR2H.jpg', 2, 2, '200x210', 'Blanco'),
(29, 'PVC-BAL-COR2H', 'Balcón corredera de 2 hojas (PVC)', 'Balconera de PVC de alta eficiencia energética en acabado Plata.', 480.00, 'imagenes/PVC-BAL-COR2H.jpg', 2, 2, '200x210', 'Plata'),
(30, 'PVC-BAL-COR2H', 'Balcón corredera de 2 hojas (PVC)', 'Balconera de PVC de alta eficiencia energética en acabado Marrón.', 480.00, 'imagenes/PVC-BAL-COR2H.jpg', 2, 2, '200x210', 'Marrón'),
(31, 'PVC-BAL-COR2H', 'Balcón corredera de 2 hojas (PVC)', 'Balconera de PVC de alta eficiencia energética en acabado Blanco.', 500.00, 'imagenes/PVC-BAL-COR2H.jpg', 2, 2, '220x220', 'Blanco'),
(32, 'PVC-BAL-COR2H', 'Balcón corredera de 2 hojas (PVC)', 'Balconera de PVC de alta eficiencia energética en acabado Plata.', 530.00, 'imagenes/PVC-BAL-COR2H.jpg', 2, 2, '220x220', 'Plata'),
(33, 'PVC-BAL-COR2H', 'Balcón corredera de 2 hojas (PVC)', 'Balconera de PVC de alta eficiencia energética en acabado Marrón.', 530.00, 'imagenes/PVC-BAL-COR2H.jpg', 2, 2, '220x220', 'Marrón'),
(34, 'PVC-BAL-COR2H', 'Balcón corredera de 2 hojas (PVC)', 'Balconera de PVC de alta eficiencia energética en acabado Blanco.', 550.00, 'imagenes/PVC-BAL-COR2H.jpg', 2, 2, '240x230', 'Blanco'),
(35, 'PVC-BAL-COR2H', 'Balcón corredera de 2 hojas (PVC)', 'Balconera de PVC de alta eficiencia energética en acabado Plata.', 580.00, 'imagenes/PVC-BAL-COR2H.jpg', 2, 2, '240x230', 'Plata'),
(36, 'PVC-BAL-COR2H', 'Balcón corredera de 2 hojas (PVC)', 'Balconera de PVC de alta eficiencia energética en acabado Marrón.', 580.00, 'imagenes/PVC-BAL-COR2H.jpg', 2, 2, '240x230', 'Marrón'),
(37, 'ALU-BAL-COR3H', 'Balcón corredera 3 hojas tricarril (Aluminio)', 'Sistema tricarril para máxima apertura, aluminio resistente en acabado Blanco.', 600.00, 'imagenes/ALU-BAL-COR3H.jpg', 1, 2, '300x210', 'Blanco'),
(38, 'ALU-BAL-COR3H', 'Balcón corredera 3 hojas tricarril (Aluminio)', 'Sistema tricarril para máxima apertura, aluminio resistente en acabado Plata.', 630.00, 'imagenes/ALU-BAL-COR3H.jpg', 1, 2, '300x210', 'Plata'),
(39, 'ALU-BAL-COR3H', 'Balcón corredera 3 hojas tricarril (Aluminio)', 'Sistema tricarril para máxima apertura, aluminio resistente en acabado Marrón.', 630.00, 'imagenes/ALU-BAL-COR3H.jpg', 1, 2, '300x210', 'Marrón'),
(40, 'ALU-BAL-COR3H', 'Balcón corredera 3 hojas tricarril (Aluminio)', 'Sistema tricarril para máxima apertura, aluminio resistente en acabado Blanco.', 650.00, 'imagenes/ALU-BAL-COR3H.jpg', 1, 2, '320x220', 'Blanco'),
(41, 'ALU-BAL-COR3H', 'Balcón corredera 3 hojas tricarril (Aluminio)', 'Sistema tricarril para máxima apertura, aluminio resistente en acabado Plata.', 680.00, 'imagenes/ALU-BAL-COR3H.jpg', 1, 2, '320x220', 'Plata'),
(42, 'ALU-BAL-COR3H', 'Balcón corredera 3 hojas tricarril (Aluminio)', 'Sistema tricarril para máxima apertura, aluminio resistente en acabado Marrón.', 680.00, 'imagenes/ALU-BAL-COR3H.jpg', 1, 2, '320x220', 'Marrón'),
(43, 'ALU-BAL-COR3H', 'Balcón corredera 3 hojas tricarril (Aluminio)', 'Sistema tricarril para máxima apertura, aluminio resistente en acabado Blanco.', 700.00, 'imagenes/ALU-BAL-COR3H.jpg', 1, 2, '340x230', 'Blanco'),
(44, 'ALU-BAL-COR3H', 'Balcón corredera 3 hojas tricarril (Aluminio)', 'Sistema tricarril para máxima apertura, aluminio resistente en acabado Plata.', 730.00, 'imagenes/ALU-BAL-COR3H.jpg', 1, 2, '340x230', 'Plata'),
(45, 'ALU-BAL-COR3H', 'Balcón corredera 3 hojas tricarril (Aluminio)', 'Sistema tricarril para máxima apertura, aluminio resistente en acabado Marrón.', 730.00, 'imagenes/ALU-BAL-COR3H.jpg', 1, 2, '340x230', 'Marrón'),
(46, 'PVC-BAL-COR3H', 'Balcón corredera 3 hojas tricarril (PVC)', 'Tricarril de PVC con refuerzo interno, gran aislamiento en acabado Blanco.', 750.00, 'imagenes/PVC-BAL-COR3H.jpg', 2, 2, '300x210', 'Blanco'),
(47, 'PVC-BAL-COR3H', 'Balcón corredera 3 hojas tricarril (PVC)', 'Tricarril de PVC con refuerzo interno, gran aislamiento en acabado Plata.', 780.00, 'imagenes/PVC-BAL-COR3H.jpg', 2, 2, '300x210', 'Plata'),
(48, 'PVC-BAL-COR3H', 'Balcón corredera 3 hojas tricarril (PVC)', 'Tricarril de PVC con refuerzo interno, gran aislamiento en acabado Marrón.', 780.00, 'imagenes/PVC-BAL-COR3H.jpg', 2, 2, '300x210', 'Marrón'),
(49, 'PVC-BAL-COR3H', 'Balcón corredera 3 hojas tricarril (PVC)', 'Tricarril de PVC con refuerzo interno, gran aislamiento en acabado Blanco.', 800.00, 'imagenes/PVC-BAL-COR3H.jpg', 2, 2, '320x220', 'Blanco'),
(50, 'PVC-BAL-COR3H', 'Balcón corredera 3 hojas tricarril (PVC)', 'Tricarril de PVC con refuerzo interno, gran aislamiento en acabado Plata.', 830.00, 'imagenes/PVC-BAL-COR3H.jpg', 2, 2, '320x220', 'Plata'),
(51, 'PVC-BAL-COR3H', 'Balcón corredera 3 hojas tricarril (PVC)', 'Tricarril de PVC con refuerzo interno, gran aislamiento en acabado Marrón.', 830.00, 'imagenes/PVC-BAL-COR3H.jpg', 2, 2, '320x220', 'Marrón'),
(52, 'PVC-BAL-COR3H', 'Balcón corredera 3 hojas tricarril (PVC)', 'Tricarril de PVC con refuerzo interno, gran aislamiento en acabado Blanco.', 850.00, 'imagenes/PVC-BAL-COR3H.jpg', 2, 2, '340x230', 'Blanco'),
(53, 'PVC-BAL-COR3H', 'Balcón corredera 3 hojas tricarril (PVC)', 'Tricarril de PVC con refuerzo interno, gran aislamiento en acabado Plata.', 880.00, 'imagenes/PVC-BAL-COR3H.jpg', 2, 2, '340x230', 'Plata'),
(54, 'PVC-BAL-COR3H', 'Balcón corredera 3 hojas tricarril (PVC)', 'Tricarril de PVC con refuerzo interno, gran aislamiento en acabado Marrón.', 880.00, 'imagenes/PVC-BAL-COR3H.jpg', 2, 2, '340x230', 'Marrón'),
(55, 'ALU-VEN-FIJA', 'Ventana fija (Aluminio)', 'Ventana panorámica sin apertura para máxima entrada de luz en acabado Blanco.', 80.00, 'imagenes/ALU-VEN-FIJA.jpg', 1, 1, '80x100', 'Blanco'),
(56, 'ALU-VEN-FIJA', 'Ventana fija (Aluminio)', 'Ventana panorámica sin apertura para máxima entrada de luz en acabado Plata.', 90.00, 'imagenes/ALU-VEN-FIJA.jpg', 1, 1, '80x100', 'Plata'),
(57, 'ALU-VEN-FIJA', 'Ventana fija (Aluminio)', 'Ventana panorámica sin apertura para máxima entrada de luz en acabado Marrón.', 90.00, 'imagenes/ALU-VEN-FIJA.jpg', 1, 1, '80x100', 'Marrón'),
(58, 'ALU-VEN-FIJA', 'Ventana fija (Aluminio)', 'Ventana panorámica sin apertura para máxima entrada de luz en acabado Blanco.', 100.00, 'imagenes/ALU-VEN-FIJA.jpg', 1, 1, '100x100', 'Blanco'),
(59, 'ALU-VEN-FIJA', 'Ventana fija (Aluminio)', 'Ventana panorámica sin apertura para máxima entrada de luz en acabado Plata.', 110.00, 'imagenes/ALU-VEN-FIJA.jpg', 1, 1, '100x100', 'Plata'),
(60, 'ALU-VEN-FIJA', 'Ventana fija (Aluminio)', 'Ventana panorámica sin apertura para máxima entrada de luz en acabado Marrón.', 110.00, 'imagenes/ALU-VEN-FIJA.jpg', 1, 1, '100x100', 'Marrón'),
(61, 'ALU-VEN-FIJA', 'Ventana fija (Aluminio)', 'Ventana panorámica sin apertura para máxima entrada de luz en acabado Blanco.', 120.00, 'imagenes/ALU-VEN-FIJA.jpg', 1, 1, '120x120', 'Blanco'),
(62, 'ALU-VEN-FIJA', 'Ventana fija (Aluminio)', 'Ventana panorámica sin apertura para máxima entrada de luz en acabado Plata.', 130.00, 'imagenes/ALU-VEN-FIJA.jpg', 1, 1, '120x120', 'Plata'),
(63, 'ALU-VEN-FIJA', 'Ventana fija (Aluminio)', 'Ventana panorámica sin apertura para máxima entrada de luz en acabado Marrón.', 130.00, 'imagenes/ALU-VEN-FIJA.jpg', 1, 1, '120x120', 'Marrón'),
(64, 'PVC-VEN-FIJA', 'Ventana fija (PVC)', 'Ventana fija de PVC, ideal para aislar zonas sin ventilación en acabado Blanco.', 100.00, 'imagenes/PVC-VEN-FIJA.jpg', 2, 1, '80x100', 'Blanco'),
(65, 'PVC-VEN-FIJA', 'Ventana fija (PVC)', 'Ventana fija de PVC, ideal para aislar zonas sin ventilación en acabado Plata.', 115.00, 'imagenes/PVC-VEN-FIJA.jpg', 2, 1, '80x100', 'Plata'),
(66, 'PVC-VEN-FIJA', 'Ventana fija (PVC)', 'Ventana fija de PVC, ideal para aislar zonas sin ventilación en acabado Marrón.', 115.00, 'imagenes/PVC-VEN-FIJA.jpg', 2, 1, '80x100', 'Marrón'),
(67, 'PVC-VEN-FIJA', 'Ventana fija (PVC)', 'Ventana fija de PVC, ideal para aislar zonas sin ventilación en acabado Blanco.', 120.00, 'imagenes/PVC-VEN-FIJA.jpg', 2, 1, '100x100', 'Blanco'),
(68, 'PVC-VEN-FIJA', 'Ventana fija (PVC)', 'Ventana fija de PVC, ideal para aislar zonas sin ventilación en acabado Plata.', 135.00, 'imagenes/PVC-VEN-FIJA.jpg', 2, 1, '100x100', 'Plata'),
(69, 'PVC-VEN-FIJA', 'Ventana fija (PVC)', 'Ventana fija de PVC, ideal para aislar zonas sin ventilación en acabado Marrón.', 135.00, 'imagenes/PVC-VEN-FIJA.jpg', 2, 1, '100x100', 'Marrón'),
(70, 'PVC-VEN-FIJA', 'Ventana fija (PVC)', 'Ventana fija de PVC, ideal para aislar zonas sin ventilación en acabado Blanco.', 140.00, 'imagenes/PVC-VEN-FIJA.jpg', 2, 1, '120x120', 'Blanco'),
(71, 'PVC-VEN-FIJA', 'Ventana fija (PVC)', 'Ventana fija de PVC, ideal para aislar zonas sin ventilación en acabado Plata.', 155.00, 'imagenes/PVC-VEN-FIJA.jpg', 2, 1, '120x120', 'Plata'),
(72, 'PVC-VEN-FIJA', 'Ventana fija (PVC)', 'Ventana fija de PVC, ideal para aislar zonas sin ventilación en acabado Marrón.', 155.00, 'imagenes/PVC-VEN-FIJA.jpg', 2, 1, '120x120', 'Marrón'),
(73, 'ALU-VEN-ABA1H', 'Ventana abatible 1 hoja (Aluminio)', 'Ventana practicable de una hoja, apertura clásica en acabado Blanco.', 95.00, 'imagenes/ALU-VEN-ABA1H.jpg', 1, 1, '60x100', 'Blanco'),
(74, 'ALU-VEN-ABA1H', 'Ventana abatible 1 hoja (Aluminio)', 'Ventana practicable de una hoja, apertura clásica en acabado Plata.', 105.00, 'imagenes/ALU-VEN-ABA1H.jpg', 1, 1, '60x100', 'Plata'),
(75, 'ALU-VEN-ABA1H', 'Ventana abatible 1 hoja (Aluminio)', 'Ventana practicable de una hoja, apertura clásica en acabado Marrón.', 105.00, 'imagenes/ALU-VEN-ABA1H.jpg', 1, 1, '60x100', 'Marrón'),
(76, 'ALU-VEN-ABA1H', 'Ventana abatible 1 hoja (Aluminio)', 'Ventana practicable de una hoja, apertura clásica en acabado Blanco.', 115.00, 'imagenes/ALU-VEN-ABA1H.jpg', 1, 1, '70x120', 'Blanco'),
(77, 'ALU-VEN-ABA1H', 'Ventana abatible 1 hoja (Aluminio)', 'Ventana practicable de una hoja, apertura clásica en acabado Plata.', 125.00, 'imagenes/ALU-VEN-ABA1H.jpg', 1, 1, '70x120', 'Plata'),
(78, 'ALU-VEN-ABA1H', 'Ventana abatible 1 hoja (Aluminio)', 'Ventana practicable de una hoja, apertura clásica en acabado Marrón.', 125.00, 'imagenes/ALU-VEN-ABA1H.jpg', 1, 1, '70x120', 'Marrón'),
(79, 'ALU-VEN-ABA1H', 'Ventana abatible 1 hoja (Aluminio)', 'Ventana practicable de una hoja, apertura clásica en acabado Blanco.', 135.00, 'imagenes/ALU-VEN-ABA1H.jpg', 1, 1, '80x140', 'Blanco'),
(80, 'ALU-VEN-ABA1H', 'Ventana abatible 1 hoja (Aluminio)', 'Ventana practicable de una hoja, apertura clásica en acabado Plata.', 145.00, 'imagenes/ALU-VEN-ABA1H.jpg', 1, 1, '80x140', 'Plata'),
(81, 'ALU-VEN-ABA1H', 'Ventana abatible 1 hoja (Aluminio)', 'Ventana practicable de una hoja, apertura clásica en acabado Marrón.', 145.00, 'imagenes/ALU-VEN-ABA1H.jpg', 1, 1, '80x140', 'Marrón'),
(82, 'PVC-VEN-ABA1H', 'Ventana abatible 1 hoja (PVC)', 'Ventana de PVC de una hoja, cierre a presión hermético en acabado Blanco.', 120.00, 'imagenes/PVC-VEN-ABA1H.jpg', 2, 1, '60x100', 'Blanco'),
(83, 'PVC-VEN-ABA1H', 'Ventana abatible 1 hoja (PVC)', 'Ventana de PVC de una hoja, cierre a presión hermético en acabado Plata.', 135.00, 'imagenes/PVC-VEN-ABA1H.jpg', 2, 1, '60x100', 'Plata'),
(84, 'PVC-VEN-ABA1H', 'Ventana abatible 1 hoja (PVC)', 'Ventana de PVC de una hoja, cierre a presión hermético en acabado Marrón.', 135.00, 'imagenes/PVC-VEN-ABA1H.jpg', 2, 1, '60x100', 'Marrón'),
(85, 'PVC-VEN-ABA1H', 'Ventana abatible 1 hoja (PVC)', 'Ventana de PVC de una hoja, cierre a presión hermético en acabado Blanco.', 140.00, 'imagenes/PVC-VEN-ABA1H.jpg', 2, 1, '70x120', 'Blanco'),
(86, 'PVC-VEN-ABA1H', 'Ventana abatible 1 hoja (PVC)', 'Ventana de PVC de una hoja, cierre a presión hermético en acabado Plata.', 155.00, 'imagenes/PVC-VEN-ABA1H.jpg', 2, 1, '70x120', 'Plata'),
(87, 'PVC-VEN-ABA1H', 'Ventana abatible 1 hoja (PVC)', 'Ventana de PVC de una hoja, cierre a presión hermético en acabado Marrón.', 155.00, 'imagenes/PVC-VEN-ABA1H.jpg', 2, 1, '70x120', 'Marrón'),
(88, 'PVC-VEN-ABA1H', 'Ventana abatible 1 hoja (PVC)', 'Ventana de PVC de una hoja, cierre a presión hermético en acabado Blanco.', 160.00, 'imagenes/PVC-VEN-ABA1H.jpg', 2, 1, '80x140', 'Blanco'),
(89, 'PVC-VEN-ABA1H', 'Ventana abatible 1 hoja (PVC)', 'Ventana de PVC de una hoja, cierre a presión hermético en acabado Plata.', 175.00, 'imagenes/PVC-VEN-ABA1H.jpg', 2, 1, '80x140', 'Plata'),
(90, 'PVC-VEN-ABA1H', 'Ventana abatible 1 hoja (PVC)', 'Ventana de PVC de una hoja, cierre a presión hermético en acabado Marrón.', 175.00, 'imagenes/PVC-VEN-ABA1H.jpg', 2, 1, '80x140', 'Marrón'),
(91, 'ALU-VEN-ABA2H', 'Ventana abatible 2 hojas (Aluminio)', 'Ventana doble hoja con apertura central, ideal para dormitorios en acabado Blanco.', 160.00, 'imagenes/ALU-VEN-ABA2H.jpg', 1, 1, '120x100', 'Blanco'),
(92, 'ALU-VEN-ABA2H', 'Ventana abatible 2 hojas (Aluminio)', 'Ventana doble hoja con apertura central, ideal para dormitorios en acabado Plata.', 180.00, 'imagenes/ALU-VEN-ABA2H.jpg', 1, 1, '120x100', 'Plata'),
(93, 'ALU-VEN-ABA2H', 'Ventana abatible 2 hojas (Aluminio)', 'Ventana doble hoja con apertura central, ideal para dormitorios en acabado Marrón.', 180.00, 'imagenes/ALU-VEN-ABA2H.jpg', 1, 1, '120x100', 'Marrón'),
(94, 'ALU-VEN-ABA2H', 'Ventana abatible 2 hojas (Aluminio)', 'Ventana doble hoja con apertura central, ideal para dormitorios en acabado Blanco.', 190.00, 'imagenes/ALU-VEN-ABA2H.jpg', 1, 1, '140x120', 'Blanco'),
(95, 'ALU-VEN-ABA2H', 'Ventana abatible 2 hojas (Aluminio)', 'Ventana doble hoja con apertura central, ideal para dormitorios en acabado Plata.', 210.00, 'imagenes/ALU-VEN-ABA2H.jpg', 1, 1, '140x120', 'Plata'),
(96, 'ALU-VEN-ABA2H', 'Ventana abatible 2 hojas (Aluminio)', 'Ventana doble hoja con apertura central, ideal para dormitorios en acabado Marrón.', 210.00, 'imagenes/ALU-VEN-ABA2H.jpg', 1, 1, '140x120', 'Marrón'),
(97, 'ALU-VEN-ABA2H', 'Ventana abatible 2 hojas (Aluminio)', 'Ventana doble hoja con apertura central, ideal para dormitorios en acabado Blanco.', 220.00, 'imagenes/ALU-VEN-ABA2H.jpg', 1, 1, '160x140', 'Blanco'),
(98, 'ALU-VEN-ABA2H', 'Ventana abatible 2 hojas (Aluminio)', 'Ventana doble hoja con apertura central, ideal para dormitorios en acabado Plata.', 240.00, 'imagenes/ALU-VEN-ABA2H.jpg', 1, 1, '160x140', 'Plata'),
(99, 'ALU-VEN-ABA2H', 'Ventana abatible 2 hojas (Aluminio)', 'Ventana doble hoja con apertura central, ideal para dormitorios en acabado Marrón.', 240.00, 'imagenes/ALU-VEN-ABA2H.jpg', 1, 1, '160x140', 'Marrón'),
(100, 'ALU-VEN-ABA2H-OB', 'Ventana abatible 2 hojas con oscilobatiente (Aluminio)', 'Ventana premium con sistema oscilobatiente para ventilación segura en acabado Blanco.', 200.00, 'imagenes/ALU-VEN-ABA2H-OB.jpg', 1, 1, '120x100', 'Blanco'),
(101, 'ALU-VEN-ABA2H-OB', 'Ventana abatible 2 hojas con oscilobatiente (Aluminio)', 'Ventana premium con sistema oscilobatiente para ventilación segura en acabado Plata.', 220.00, 'imagenes/ALU-VEN-ABA2H-OB.jpg', 1, 1, '120x100', 'Plata'),
(102, 'ALU-VEN-ABA2H-OB', 'Ventana abatible 2 hojas con oscilobatiente (Aluminio)', 'Ventana premium con sistema oscilobatiente para ventilación segura en acabado Marrón.', 220.00, 'imagenes/ALU-VEN-ABA2H-OB.jpg', 1, 1, '120x100', 'Marrón'),
(103, 'ALU-VEN-ABA2H-OB', 'Ventana abatible 2 hojas con oscilobatiente (Aluminio)', 'Ventana premium con sistema oscilobatiente para ventilación segura en acabado Blanco.', 230.00, 'imagenes/ALU-VEN-ABA2H-OB.jpg', 1, 1, '140x120', 'Blanco'),
(104, 'ALU-VEN-ABA2H-OB', 'Ventana abatible 2 hojas con oscilobatiente (Aluminio)', 'Ventana premium con sistema oscilobatiente para ventilación segura en acabado Plata.', 250.00, 'imagenes/ALU-VEN-ABA2H-OB.jpg', 1, 1, '140x120', 'Plata'),
(105, 'ALU-VEN-ABA2H-OB', 'Ventana abatible 2 hojas con oscilobatiente (Aluminio)', 'Ventana premium con sistema oscilobatiente para ventilación segura en acabado Marrón.', 250.00, 'imagenes/ALU-VEN-ABA2H-OB.jpg', 1, 1, '140x120', 'Marrón'),
(106, 'ALU-VEN-ABA2H-OB', 'Ventana abatible 2 hojas con oscilobatiente (Aluminio)', 'Ventana premium con sistema oscilobatiente para ventilación segura en acabado Blanco.', 260.00, 'imagenes/ALU-VEN-ABA2H-OB.jpg', 1, 1, '160x140', 'Blanco'),
(107, 'ALU-VEN-ABA2H-OB', 'Ventana abatible 2 hojas con oscilobatiente (Aluminio)', 'Ventana premium con sistema oscilobatiente para ventilación segura en acabado Plata.', 280.00, 'imagenes/ALU-VEN-ABA2H-OB.jpg', 1, 1, '160x140', 'Plata'),
(108, 'ALU-VEN-ABA2H-OB', 'Ventana abatible 2 hojas con oscilobatiente (Aluminio)', 'Ventana premium con sistema oscilobatiente para ventilación segura en acabado Marrón.', 280.00, 'imagenes/ALU-VEN-ABA2H-OB.jpg', 1, 1, '160x140', 'Marrón'),
(109, 'PVC-VEN-ABA2H', 'Ventana abatible 2 hojas (PVC)', 'Doble hoja de PVC con cierre reforzado, máximo silencio en acabado Blanco.', 210.00, 'imagenes/PVC-VEN-ABA2H.jpg', 2, 1, '120x100', 'Blanco'),
(110, 'PVC-VEN-ABA2H', 'Ventana abatible 2 hojas (PVC)', 'Doble hoja de PVC con cierre reforzado, máximo silencio en acabado Plata.', 230.00, 'imagenes/PVC-VEN-ABA2H.jpg', 2, 1, '120x100', 'Plata'),
(111, 'PVC-VEN-ABA2H', 'Ventana abatible 2 hojas (PVC)', 'Doble hoja de PVC con cierre reforzado, máximo silencio en acabado Marrón.', 230.00, 'imagenes/PVC-VEN-ABA2H.jpg', 2, 1, '120x100', 'Marrón'),
(112, 'PVC-VEN-ABA2H', 'Ventana abatible 2 hojas (PVC)', 'Doble hoja de PVC con cierre reforzado, máximo silencio en acabado Blanco.', 240.00, 'imagenes/PVC-VEN-ABA2H.jpg', 2, 1, '140x120', 'Blanco'),
(113, 'PVC-VEN-ABA2H', 'Ventana abatible 2 hojas (PVC)', 'Doble hoja de PVC con cierre reforzado, máximo silencio en acabado Plata.', 260.00, 'imagenes/PVC-VEN-ABA2H.jpg', 2, 1, '140x120', 'Plata'),
(114, 'PVC-VEN-ABA2H', 'Ventana abatible 2 hojas (PVC)', 'Doble hoja de PVC con cierre reforzado, máximo silencio en acabado Marrón.', 260.00, 'imagenes/PVC-VEN-ABA2H.jpg', 2, 1, '140x120', 'Marrón'),
(115, 'PVC-VEN-ABA2H', 'Ventana abatible 2 hojas (PVC)', 'Doble hoja de PVC con cierre reforzado, máximo silencio en acabado Blanco.', 270.00, 'imagenes/PVC-VEN-ABA2H.jpg', 2, 1, '160x140', 'Blanco'),
(116, 'PVC-VEN-ABA2H', 'Ventana abatible 2 hojas (PVC)', 'Doble hoja de PVC con cierre reforzado, máximo silencio en acabado Plata.', 290.00, 'imagenes/PVC-VEN-ABA2H.jpg', 2, 1, '160x140', 'Plata'),
(117, 'PVC-VEN-ABA2H', 'Ventana abatible 2 hojas (PVC)', 'Doble hoja de PVC con cierre reforzado, máximo silencio en acabado Marrón.', 290.00, 'imagenes/PVC-VEN-ABA2H.jpg', 2, 1, '160x140', 'Marrón'),
(118, 'PVC-VEN-ABA2H-OB', 'Ventana abatible 2 hojas con oscilobatiente (PVC)', 'La mejor ventana de PVC, oscilobatiente y hermética en acabado Blanco.', 250.00, 'imagenes/PVC-VEN-ABA2H-OB.jpg', 2, 1, '120x100', 'Blanco'),
(119, 'PVC-VEN-ABA2H-OB', 'Ventana abatible 2 hojas con oscilobatiente (PVC)', 'La mejor ventana de PVC, oscilobatiente y hermética en acabado Plata.', 275.00, 'imagenes/PVC-VEN-ABA2H-OB.jpg', 2, 1, '120x100', 'Plata'),
(120, 'PVC-VEN-ABA2H-OB', 'Ventana abatible 2 hojas con oscilobatiente (PVC)', 'La mejor ventana de PVC, oscilobatiente y hermética en acabado Marrón.', 275.00, 'imagenes/PVC-VEN-ABA2H-OB.jpg', 2, 1, '120x100', 'Marrón'),
(121, 'PVC-VEN-ABA2H-OB', 'Ventana abatible 2 hojas con oscilobatiente (PVC)', 'La mejor ventana de PVC, oscilobatiente y hermética en acabado Blanco.', 280.00, 'imagenes/PVC-VEN-ABA2H-OB.jpg', 2, 1, '140x120', 'Blanco'),
(122, 'PVC-VEN-ABA2H-OB', 'Ventana abatible 2 hojas con oscilobatiente (PVC)', 'La mejor ventana de PVC, oscilobatiente y hermética en acabado Plata.', 305.00, 'imagenes/PVC-VEN-ABA2H-OB.jpg', 2, 1, '140x120', 'Plata'),
(123, 'PVC-VEN-ABA2H-OB', 'Ventana abatible 2 hojas con oscilobatiente (PVC)', 'La mejor ventana de PVC, oscilobatiente y hermética en acabado Marrón.', 305.00, 'imagenes/PVC-VEN-ABA2H-OB.jpg', 2, 1, '140x120', 'Marrón'),
(124, 'PVC-VEN-ABA2H-OB', 'Ventana abatible 2 hojas con oscilobatiente (PVC)', 'La mejor ventana de PVC, oscilobatiente y hermética en acabado Blanco.', 310.00, 'imagenes/PVC-VEN-ABA2H-OB.jpg', 2, 1, '160x140', 'Blanco'),
(125, 'PVC-VEN-ABA2H-OB', 'Ventana abatible 2 hojas con oscilobatiente (PVC)', 'La mejor ventana de PVC, oscilobatiente y hermética en acabado Plata.', 335.00, 'imagenes/PVC-VEN-ABA2H-OB.jpg', 2, 1, '160x140', 'Plata'),
(126, 'PVC-VEN-ABA2H-OB', 'Ventana abatible 2 hojas con oscilobatiente (PVC)', 'La mejor ventana de PVC, oscilobatiente y hermética en acabado Marrón.', 335.00, 'imagenes/PVC-VEN-ABA2H-OB.jpg', 2, 1, '160x140', 'Marrón'),
(127, 'ALU-BAL-ABA1H', 'Balcón abatible 1 hoja (Aluminio)', 'Puerta balconera de una hoja, paso cómodo y resistente en acabado Blanco.', 280.00, 'imagenes/ALU-BAL-ABA1H.jpg', 1, 2, '60x100', 'Blanco'),
(128, 'ALU-BAL-ABA1H', 'Balcón abatible 1 hoja (Aluminio)', 'Puerta balconera de una hoja, paso cómodo y resistente en acabado Plata.', 300.00, 'imagenes/ALU-BAL-ABA1H.jpg', 1, 2, '60x100', 'Plata'),
(129, 'ALU-BAL-ABA1H', 'Balcón abatible 1 hoja (Aluminio)', 'Puerta balconera de una hoja, paso cómodo y resistente en acabado Marrón.', 300.00, 'imagenes/ALU-BAL-ABA1H.jpg', 1, 2, '60x100', 'Marrón'),
(130, 'ALU-BAL-ABA1H', 'Balcón abatible 1 hoja (Aluminio)', 'Puerta balconera de una hoja, paso cómodo y resistente en acabado Blanco.', 310.00, 'imagenes/ALU-BAL-ABA1H.jpg', 1, 2, '70x120', 'Blanco'),
(131, 'ALU-BAL-ABA1H', 'Balcón abatible 1 hoja (Aluminio)', 'Puerta balconera de una hoja, paso cómodo y resistente en acabado Plata.', 330.00, 'imagenes/ALU-BAL-ABA1H.jpg', 1, 2, '70x120', 'Plata'),
(132, 'ALU-BAL-ABA1H', 'Balcón abatible 1 hoja (Aluminio)', 'Puerta balconera de una hoja, paso cómodo y resistente en acabado Marrón.', 330.00, 'imagenes/ALU-BAL-ABA1H.jpg', 1, 2, '70x120', 'Marrón'),
(133, 'ALU-BAL-ABA1H', 'Balcón abatible 1 hoja (Aluminio)', 'Puerta balconera de una hoja, paso cómodo y resistente en acabado Blanco.', 340.00, 'imagenes/ALU-BAL-ABA1H.jpg', 1, 2, '80x140', 'Blanco'),
(134, 'ALU-BAL-ABA1H', 'Balcón abatible 1 hoja (Aluminio)', 'Puerta balconera de una hoja, paso cómodo y resistente en acabado Plata.', 360.00, 'imagenes/ALU-BAL-ABA1H.jpg', 1, 2, '80x140', 'Plata'),
(135, 'ALU-BAL-ABA1H', 'Balcón abatible 1 hoja (Aluminio)', 'Puerta balconera de una hoja, paso cómodo y resistente en acabado Marrón.', 360.00, 'imagenes/ALU-BAL-ABA1H.jpg', 1, 2, '80x140', 'Marrón'),
(136, 'PVC-BAL-ABA1H', 'Balcón abatible 1 hoja (PVC)', 'Puerta balconera de PVC, aislamiento superior en acabado Blanco.', 320.00, 'imagenes/PVC-BAL-ABA1H.jpg', 2, 2, '60x100', 'Blanco'),
(137, 'PVC-BAL-ABA1H', 'Balcón abatible 1 hoja (PVC)', 'Puerta balconera de PVC, aislamiento superior en acabado Plata.', 340.00, 'imagenes/PVC-BAL-ABA1H.jpg', 2, 2, '60x100', 'Plata'),
(138, 'PVC-BAL-ABA1H', 'Balcón abatible 1 hoja (PVC)', 'Puerta balconera de PVC, aislamiento superior en acabado Marrón.', 340.00, 'imagenes/PVC-BAL-ABA1H.jpg', 2, 2, '60x100', 'Marrón'),
(139, 'PVC-BAL-ABA1H', 'Balcón abatible 1 hoja (PVC)', 'Puerta balconera de PVC, aislamiento superior en acabado Blanco.', 350.00, 'imagenes/PVC-BAL-ABA1H.jpg', 2, 2, '70x120', 'Blanco'),
(140, 'PVC-BAL-ABA1H', 'Balcón abatible 1 hoja (PVC)', 'Puerta balconera de PVC, aislamiento superior en acabado Plata.', 370.00, 'imagenes/PVC-BAL-ABA1H.jpg', 2, 2, '70x120', 'Plata'),
(141, 'PVC-BAL-ABA1H', 'Balcón abatible 1 hoja (PVC)', 'Puerta balconera de PVC, aislamiento superior en acabado Marrón.', 370.00, 'imagenes/PVC-BAL-ABA1H.jpg', 2, 2, '70x120', 'Marrón'),
(142, 'PVC-BAL-ABA1H', 'Balcón abatible 1 hoja (PVC)', 'Puerta balconera de PVC, aislamiento superior en acabado Blanco.', 380.00, 'imagenes/PVC-BAL-ABA1H.jpg', 2, 2, '80x140', 'Blanco'),
(143, 'PVC-BAL-ABA1H', 'Balcón abatible 1 hoja (PVC)', 'Puerta balconera de PVC, aislamiento superior en acabado Plata.', 400.00, 'imagenes/PVC-BAL-ABA1H.jpg', 2, 2, '80x140', 'Plata'),
(144, 'PVC-BAL-ABA1H', 'Balcón abatible 1 hoja (PVC)', 'Puerta balconera de PVC, aislamiento superior en acabado Marrón.', 400.00, 'imagenes/PVC-BAL-ABA1H.jpg', 2, 2, '80x140', 'Marrón'),
(145, 'ALU-BAL-ABA2H', 'Balcón abatible 2 hojas (Aluminio)', 'Balconera doble de aluminio, apertura amplia en acabado Blanco.', 450.00, 'imagenes/ALU-BAL-ABA2H.jpg', 1, 2, '120x100', 'Blanco'),
(146, 'ALU-BAL-ABA2H', 'Balcón abatible 2 hojas (Aluminio)', 'Balconera doble de aluminio, apertura amplia en acabado Plata.', 480.00, 'imagenes/ALU-BAL-ABA2H.jpg', 1, 2, '120x100', 'Plata'),
(147, 'ALU-BAL-ABA2H', 'Balcón abatible 2 hojas (Aluminio)', 'Balconera doble de aluminio, apertura amplia en acabado Marrón.', 480.00, 'imagenes/ALU-BAL-ABA2H.jpg', 1, 2, '120x100', 'Marrón'),
(148, 'ALU-BAL-ABA2H', 'Balcón abatible 2 hojas (Aluminio)', 'Balconera doble de aluminio, apertura amplia en acabado Blanco.', 480.00, 'imagenes/ALU-BAL-ABA2H.jpg', 1, 2, '140x120', 'Blanco'),
(149, 'ALU-BAL-ABA2H', 'Balcón abatible 2 hojas (Aluminio)', 'Balconera doble de aluminio, apertura amplia en acabado Plata.', 510.00, 'imagenes/ALU-BAL-ABA2H.jpg', 1, 2, '140x120', 'Plata'),
(150, 'ALU-BAL-ABA2H', 'Balcón abatible 2 hojas (Aluminio)', 'Balconera doble de aluminio, apertura amplia en acabado Marrón.', 510.00, 'imagenes/ALU-BAL-ABA2H.jpg', 1, 2, '140x120', 'Marrón'),
(151, 'ALU-BAL-ABA2H', 'Balcón abatible 2 hojas (Aluminio)', 'Balconera doble de aluminio, apertura amplia en acabado Blanco.', 510.00, 'imagenes/ALU-BAL-ABA2H.jpg', 1, 2, '160x140', 'Blanco'),
(152, 'ALU-BAL-ABA2H', 'Balcón abatible 2 hojas (Aluminio)', 'Balconera doble de aluminio, apertura amplia en acabado Plata.', 540.00, 'imagenes/ALU-BAL-ABA2H.jpg', 1, 2, '160x140', 'Plata'),
(153, 'ALU-BAL-ABA2H', 'Balcón abatible 2 hojas (Aluminio)', 'Balconera doble de aluminio, apertura amplia en acabado Marrón.', 540.00, 'imagenes/ALU-BAL-ABA2H.jpg', 1, 2, '160x140', 'Marrón'),
(154, 'PVC-BAL-ABA2H', 'Balcón abatible 2 hojas (PVC)', 'Balcón doble hoja de PVC, robusto y elegante en acabado Blanco.', 500.00, 'imagenes/PVC-BAL-ABA2H.jpg', 2, 2, '120x100', 'Blanco'),
(155, 'PVC-BAL-ABA2H', 'Balcón abatible 2 hojas (PVC)', 'Balcón doble hoja de PVC, robusto y elegante en acabado Plata.', 530.00, 'imagenes/PVC-BAL-ABA2H.jpg', 2, 2, '120x100', 'Plata'),
(156, 'PVC-BAL-ABA2H', 'Balcón abatible 2 hojas (PVC)', 'Balcón doble hoja de PVC, robusto y elegante en acabado Marrón.', 530.00, 'imagenes/PVC-BAL-ABA2H.jpg', 2, 2, '120x100', 'Marrón'),
(157, 'PVC-BAL-ABA2H', 'Balcón abatible 2 hojas (PVC)', 'Balcón doble hoja de PVC, robusto y elegante en acabado Blanco.', 530.00, 'imagenes/PVC-BAL-ABA2H.jpg', 2, 2, '140x120', 'Blanco'),
(158, 'PVC-BAL-ABA2H', 'Balcón abatible 2 hojas (PVC)', 'Balcón doble hoja de PVC, robusto y elegante en acabado Plata.', 560.00, 'imagenes/PVC-BAL-ABA2H.jpg', 2, 2, '140x120', 'Plata'),
(159, 'PVC-BAL-ABA2H', 'Balcón abatible 2 hojas (PVC)', 'Balcón doble hoja de PVC, robusto y elegante en acabado Marrón.', 560.00, 'imagenes/PVC-BAL-ABA2H.jpg', 2, 2, '140x120', 'Marrón'),
(160, 'PVC-BAL-ABA2H', 'Balcón abatible 2 hojas (PVC)', 'Balcón doble hoja de PVC, robusto y elegante en acabado Blanco.', 560.00, 'imagenes/PVC-BAL-ABA2H.jpg', 2, 2, '160x140', 'Blanco'),
(161, 'PVC-BAL-ABA2H', 'Balcón abatible 2 hojas (PVC)', 'Balcón doble hoja de PVC, robusto y elegante en acabado Plata.', 590.00, 'imagenes/PVC-BAL-ABA2H.jpg', 2, 2, '160x140', 'Plata'),
(162, 'PVC-BAL-ABA2H', 'Balcón abatible 2 hojas (PVC)', 'Balcón doble hoja de PVC, robusto y elegante en acabado Marrón.', 590.00, 'imagenes/PVC-BAL-ABA2H.jpg', 2, 2, '160x140', 'Marrón'),
(163, 'ALU-PERG-RED', 'Pérgola aluminio tubo redondo', 'Pérgola resistente ideal para jardín, estructura tubular en acabado Blanco.', 450.00, 'imagenes/ALU-PERG-RED.jpg', 1, 6, '200x300', 'Blanco'),
(164, 'ALU-PERG-RED', 'Pérgola aluminio tubo redondo', 'Pérgola resistente ideal para jardín, estructura tubular en acabado Plata.', 480.00, 'imagenes/ALU-PERG-RED.jpg', 1, 6, '200x300', 'Plata'),
(165, 'ALU-PERG-RED', 'Pérgola aluminio tubo redondo', 'Pérgola resistente ideal para jardín, estructura tubular en acabado Marrón.', 480.00, 'imagenes/ALU-PERG-RED.jpg', 1, 6, '200x300', 'Marrón'),
(166, 'ALU-PERG-RED', 'Pérgola aluminio tubo redondo', 'Pérgola resistente ideal para jardín, estructura tubular en acabado Blanco.', 500.00, 'imagenes/ALU-PERG-RED.jpg', 1, 6, '250x350', 'Blanco'),
(167, 'ALU-PERG-RED', 'Pérgola aluminio tubo redondo', 'Pérgola resistente ideal para jardín, estructura tubular en acabado Plata.', 530.00, 'imagenes/ALU-PERG-RED.jpg', 1, 6, '250x350', 'Plata'),
(168, 'ALU-PERG-RED', 'Pérgola aluminio tubo redondo', 'Pérgola resistente ideal para jardín, estructura tubular en acabado Marrón.', 530.00, 'imagenes/ALU-PERG-RED.jpg', 1, 6, '250x350', 'Marrón'),
(169, 'ALU-PERG-RED', 'Pérgola aluminio tubo redondo', 'Pérgola resistente ideal para jardín, estructura tubular en acabado Blanco.', 550.00, 'imagenes/ALU-PERG-RED.jpg', 1, 6, '300x400', 'Blanco'),
(170, 'ALU-PERG-RED', 'Pérgola aluminio tubo redondo', 'Pérgola resistente ideal para jardín, estructura tubular en acabado Plata.', 580.00, 'imagenes/ALU-PERG-RED.jpg', 1, 6, '300x400', 'Plata'),
(171, 'ALU-PERG-RED', 'Pérgola aluminio tubo redondo', 'Pérgola resistente ideal para jardín, estructura tubular en acabado Marrón.', 580.00, 'imagenes/ALU-PERG-RED.jpg', 1, 6, '300x400', 'Marrón'),
(172, 'PVC-PERG-CUA', 'Pérgola PVC tubo cuadrado', 'Pérgola de PVC de diseño moderno con líneas rectas en acabado Blanco.', 460.00, 'imagenes/PVC-PERG-CUA.jpg', 2, 6, '200x300', 'Blanco'),
(173, 'PVC-PERG-CUA', 'Pérgola PVC tubo cuadrado', 'Pérgola de PVC de diseño moderno con líneas rectas en acabado Plata.', 490.00, 'imagenes/PVC-PERG-CUA.jpg', 2, 6, '200x300', 'Plata'),
(174, 'PVC-PERG-CUA', 'Pérgola PVC tubo cuadrado', 'Pérgola de PVC de diseño moderno con líneas rectas en acabado Marrón.', 490.00, 'imagenes/PVC-PERG-CUA.jpg', 2, 6, '200x300', 'Marrón'),
(175, 'PVC-PERG-CUA', 'Pérgola PVC tubo cuadrado', 'Pérgola de PVC de diseño moderno con líneas rectas en acabado Blanco.', 510.00, 'imagenes/PVC-PERG-CUA.jpg', 2, 6, '250x350', 'Blanco'),
(176, 'PVC-PERG-CUA', 'Pérgola PVC tubo cuadrado', 'Pérgola de PVC de diseño moderno con líneas rectas en acabado Plata.', 540.00, 'imagenes/PVC-PERG-CUA.jpg', 2, 6, '250x350', 'Plata'),
(177, 'PVC-PERG-CUA', 'Pérgola PVC tubo cuadrado', 'Pérgola de PVC de diseño moderno con líneas rectas en acabado Marrón.', 540.00, 'imagenes/PVC-PERG-CUA.jpg', 2, 6, '250x350', 'Marrón'),
(178, 'PVC-PERG-CUA', 'Pérgola PVC tubo cuadrado', 'Pérgola de PVC de diseño moderno con líneas rectas en acabado Blanco.', 560.00, 'imagenes/PVC-PERG-CUA.jpg', 2, 6, '300x400', 'Blanco'),
(179, 'PVC-PERG-CUA', 'Pérgola PVC tubo cuadrado', 'Pérgola de PVC de diseño moderno con líneas rectas en acabado Plata.', 590.00, 'imagenes/PVC-PERG-CUA.jpg', 2, 6, '300x400', 'Plata'),
(180, 'PVC-PERG-CUA', 'Pérgola PVC tubo cuadrado', 'Pérgola de PVC de diseño moderno con líneas rectas en acabado Marrón.', 590.00, 'imagenes/PVC-PERG-CUA.jpg', 2, 6, '300x400', 'Marrón'),
(181, 'ALU-PERG-CUA', 'Pérgola aluminio tubo cuadrado', 'Pérgola de aluminio minimalista, estructura cuadrada reforzada en acabado Blanco.', 470.00, 'imagenes/ALU-PERG-CUA.jpg', 1, 6, '200x300', 'Blanco'),
(182, 'ALU-PERG-CUA', 'Pérgola aluminio tubo cuadrado', 'Pérgola de aluminio minimalista, estructura cuadrada reforzada en acabado Plata.', 500.00, 'imagenes/ALU-PERG-CUA.jpg', 1, 6, '200x300', 'Plata'),
(183, 'ALU-PERG-CUA', 'Pérgola aluminio tubo cuadrado', 'Pérgola de aluminio minimalista, estructura cuadrada reforzada en acabado Marrón.', 500.00, 'imagenes/ALU-PERG-CUA.jpg', 1, 6, '200x300', 'Marrón'),
(184, 'ALU-PERG-CUA', 'Pérgola aluminio tubo cuadrado', 'Pérgola de aluminio minimalista, estructura cuadrada reforzada en acabado Blanco.', 520.00, 'imagenes/ALU-PERG-CUA.jpg', 1, 6, '250x350', 'Blanco'),
(185, 'ALU-PERG-CUA', 'Pérgola aluminio tubo cuadrado', 'Pérgola de aluminio minimalista, estructura cuadrada reforzada en acabado Plata.', 550.00, 'imagenes/ALU-PERG-CUA.jpg', 1, 6, '250x350', 'Plata'),
(186, 'ALU-PERG-CUA', 'Pérgola aluminio tubo cuadrado', 'Pérgola de aluminio minimalista, estructura cuadrada reforzada en acabado Marrón.', 550.00, 'imagenes/ALU-PERG-CUA.jpg', 1, 6, '250x350', 'Marrón'),
(187, 'ALU-PERG-CUA', 'Pérgola aluminio tubo cuadrado', 'Pérgola de aluminio minimalista, estructura cuadrada reforzada en acabado Blanco.', 570.00, 'imagenes/ALU-PERG-CUA.jpg', 1, 6, '300x400', 'Blanco'),
(188, 'ALU-PERG-CUA', 'Pérgola aluminio tubo cuadrado', 'Pérgola de aluminio minimalista, estructura cuadrada reforzada en acabado Plata.', 600.00, 'imagenes/ALU-PERG-CUA.jpg', 1, 6, '300x400', 'Plata'),
(189, 'ALU-PERG-CUA', 'Pérgola aluminio tubo cuadrado', 'Pérgola de aluminio minimalista, estructura cuadrada reforzada en acabado Marrón.', 600.00, 'imagenes/ALU-PERG-CUA.jpg', 1, 6, '300x400', 'Marrón'),
(190, 'PVC-PERG-RED', 'Pérgola PVC tubo redondo', 'Pérgola clásica de PVC, resistente a la intemperie, tubo redondo en acabado Blanco.', 440.00, 'imagenes/PVC-PERG-RED.jpg', 2, 6, '200x300', 'Blanco'),
(191, 'PVC-PERG-RED', 'Pérgola PVC tubo redondo', 'Pérgola clásica de PVC, resistente a la intemperie, tubo redondo en acabado Plata.', 470.00, 'imagenes/PVC-PERG-RED.jpg', 2, 6, '200x300', 'Plata'),
(192, 'PVC-PERG-RED', 'Pérgola PVC tubo redondo', 'Pérgola clásica de PVC, resistente a la intemperie, tubo redondo en acabado Marrón.', 470.00, 'imagenes/PVC-PERG-RED.jpg', 2, 6, '200x300', 'Marrón'),
(193, 'PVC-PERG-RED', 'Pérgola PVC tubo redondo', 'Pérgola clásica de PVC, resistente a la intemperie, tubo redondo en acabado Blanco.', 490.00, 'imagenes/PVC-PERG-RED.jpg', 2, 6, '250x350', 'Blanco'),
(194, 'PVC-PERG-RED', 'Pérgola PVC tubo redondo', 'Pérgola clásica de PVC, resistente a la intemperie, tubo redondo en acabado Plata.', 520.00, 'imagenes/PVC-PERG-RED.jpg', 2, 6, '250x350', 'Plata'),
(195, 'PVC-PERG-RED', 'Pérgola PVC tubo redondo', 'Pérgola clásica de PVC, resistente a la intemperie, tubo redondo en acabado Marrón.', 520.00, 'imagenes/PVC-PERG-RED.jpg', 2, 6, '250x350', 'Marrón'),
(196, 'PVC-PERG-RED', 'Pérgola PVC tubo redondo', 'Pérgola clásica de PVC, resistente a la intemperie, tubo redondo en acabado Blanco.', 540.00, 'imagenes/PVC-PERG-RED.jpg', 2, 6, '300x400', 'Blanco'),
(197, 'PVC-PERG-RED', 'Pérgola PVC tubo redondo', 'Pérgola clásica de PVC, resistente a la intemperie, tubo redondo en acabado Plata.', 570.00, 'imagenes/PVC-PERG-RED.jpg', 2, 6, '300x400', 'Plata'),
(198, 'PVC-PERG-RED', 'Pérgola PVC tubo redondo', 'Pérgola clásica de PVC, resistente a la intemperie, tubo redondo en acabado Marrón.', 570.00, 'imagenes/PVC-PERG-RED.jpg', 2, 6, '300x400', 'Marrón');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ventas`
--

DROP TABLE IF EXISTS `ventas`;
CREATE TABLE `ventas` (
  `id` int(11) NOT NULL,
  `id_cliente` int(11) NOT NULL,
  `fecha` datetime DEFAULT current_timestamp(),
  `total` decimal(10,2) DEFAULT 0.00,
  `estado` varchar(20) DEFAULT 'Pendiente',
  `nombre_completo` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `telefono` varchar(50) DEFAULT NULL,
  `calle` varchar(255) DEFAULT NULL,
  `numero` varchar(50) DEFAULT NULL,
  `piso` varchar(50) DEFAULT NULL,
  `codigo_postal` varchar(20) DEFAULT NULL,
  `localidad` varchar(100) DEFAULT NULL,
  `notas` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `ventas`
--

INSERT INTO `ventas` (`id`, `id_cliente`, `fecha`, `total`, `estado`, `nombre_completo`, `email`, `telefono`, `calle`, `numero`, `piso`, `codigo_postal`, `localidad`, `notas`) VALUES
(1, 1, '2023-11-01 10:30:00', 370.00, 'Pendiente', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(2, 2, '2023-11-05 16:45:00', 450.00, 'Pendiente', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(12, 2, '2025-12-16 16:37:08', 600.00, 'Pendiente', 'María Rodríguez Sánchez', 'maria.rod@email.com', '611222333', 'Av. Constitución', '20', '1º B', '18012', 'Granada', ''),
(13, 2, '2025-12-16 16:40:28', 600.00, 'Pendiente', 'María Rodríguez Pérez', 'maria.rod@email.com', '611222333', 'Av. Constitución', '20', '1º B', '18012', 'Granada', ''),
(14, 1, '2025-12-16 16:44:21', 2220.00, 'Pendiente', 'Juan García López', 'juan.garcia@email.com', '600111222', 'Calle Recogidas', '15', '2A', '18005', 'Granada', ''),
(15, 2, '2025-12-16 20:46:35', 120.00, 'Pendiente', 'María Rodríguez Pérez', 'maria.rod@email.com', '611222333', 'Av. Constitución', '20', '1º B', '18012', 'Granada', ''),
(16, 2, '2025-12-16 21:10:54', 120.00, 'Pendiente', 'María Rodríguez Pérez', 'maria.rod@email.com', '611222333', 'Av. Constitución', '20', '1º B', '18012', 'Granada', ''),
(17, 2, '2025-12-16 21:18:23', 120.00, 'Pendiente', 'María Rodríguez Pérez', 'maria.rod@email.com', '611222333', 'Av. Constitución', '20', '1º B', '18012', 'Granada', ''),
(18, 2, '2025-12-16 21:46:31', 240.00, 'Pendiente', 'María Rodríguez Pérez', 'maria.rod@email.com', '611222333', 'Av. Constitución', '20', '1º B', '18012', 'Granada', '');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `carrito`
--
ALTER TABLE `carrito`
  ADD PRIMARY KEY (`id`),
  ADD KEY `cliente_id` (`cliente_id`),
  ADD KEY `producto_id` (`producto_id`);

--
-- Indices de la tabla `categorias`
--
ALTER TABLE `categorias`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `clientes`
--
ALTER TABLE `clientes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `dni` (`dni`);

--
-- Indices de la tabla `detalle_ventas`
--
ALTER TABLE `detalle_ventas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_venta` (`id_venta`),
  ADD KEY `id_producto` (`id_producto`);

--
-- Indices de la tabla `materiales`
--
ALTER TABLE `materiales`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `productos`
--
ALTER TABLE `productos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_material` (`id_material`),
  ADD KEY `id_categoria` (`id_categoria`);

--
-- Indices de la tabla `ventas`
--
ALTER TABLE `ventas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_cliente` (`id_cliente`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `carrito`
--
ALTER TABLE `carrito`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT de la tabla `categorias`
--
ALTER TABLE `categorias`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `clientes`
--
ALTER TABLE `clientes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `detalle_ventas`
--
ALTER TABLE `detalle_ventas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT de la tabla `materiales`
--
ALTER TABLE `materiales`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `productos`
--
ALTER TABLE `productos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=199;

--
-- AUTO_INCREMENT de la tabla `ventas`
--
ALTER TABLE `ventas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `carrito`
--
ALTER TABLE `carrito`
  ADD CONSTRAINT `carrito_ibfk_1` FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `carrito_ibfk_2` FOREIGN KEY (`producto_id`) REFERENCES `productos` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `detalle_ventas`
--
ALTER TABLE `detalle_ventas`
  ADD CONSTRAINT `detalle_ventas_ibfk_1` FOREIGN KEY (`id_venta`) REFERENCES `ventas` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `detalle_ventas_ibfk_2` FOREIGN KEY (`id_producto`) REFERENCES `productos` (`id`);

--
-- Filtros para la tabla `productos`
--
ALTER TABLE `productos`
  ADD CONSTRAINT `productos_ibfk_1` FOREIGN KEY (`id_material`) REFERENCES `materiales` (`id`),
  ADD CONSTRAINT `productos_ibfk_2` FOREIGN KEY (`id_categoria`) REFERENCES `categorias` (`id`);

--
-- Filtros para la tabla `ventas`
--
ALTER TABLE `ventas`
  ADD CONSTRAINT `ventas_ibfk_1` FOREIGN KEY (`id_cliente`) REFERENCES `clientes` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
