-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: localhost:3307
-- Tiempo de generación: 19-11-2025 a las 00:08:37
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
-- Base de datos: `vivero_mym`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `administradores`
--

CREATE TABLE `administradores` (
  `id` int(11) NOT NULL,
  `nombre` varchar(150) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password` varchar(255) NOT NULL,
  `fecha_registro` timestamp NOT NULL DEFAULT current_timestamp(),
  `ultimo_acceso` timestamp NULL DEFAULT NULL,
  `estado` tinyint(4) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `administradores`
--

INSERT INTO `administradores` (`id`, `nombre`, `email`, `password`, `fecha_registro`, `ultimo_acceso`, `estado`) VALUES
(1, 'Administrador', 'admin@viveromym.com', '$2a$12$TN/a70xeAJR/j.fV9bmJr.5kwVVkkTbeXFyv.mAO9FpzjjF89SMJ6', '2025-11-17 22:00:54', NULL, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `categorias`
--

CREATE TABLE `categorias` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `estado` tinyint(4) DEFAULT 1,
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `categorias`
--

INSERT INTO `categorias` (`id`, `nombre`, `descripcion`, `estado`, `fecha_creacion`) VALUES
(1, 'Cítricos', 'Frutas cítricas como naranjas, limones, pomelos', 1, '2025-11-17 21:58:32'),
(2, 'Frutales de carozo', 'Frutas con carozo como duraznos y ciruelas', 1, '2025-11-17 21:58:32'),
(3, 'Frutas tropicales', 'Frutas tropicales como mango y palta', 1, '2025-11-17 21:58:32');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pedidos`
--

CREATE TABLE `pedidos` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `total` decimal(10,2) NOT NULL,
  `estado` enum('pendiente','confirmado','cancelado') DEFAULT 'pendiente',
  `fecha_pedido` timestamp NOT NULL DEFAULT current_timestamp(),
  `fecha_entrega` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `pedidos`
--

INSERT INTO `pedidos` (`id`, `usuario_id`, `total`, `estado`, `fecha_pedido`, `fecha_entrega`) VALUES
(1, 3, 73500.00, 'pendiente', '2025-11-18 06:12:22', NULL),
(2, 3, 6500.00, 'pendiente', '2025-11-18 06:34:00', NULL),
(3, 6, 18000.00, 'pendiente', '2025-11-18 06:48:31', NULL),
(4, 6, 24000.00, 'pendiente', '2025-11-18 06:57:43', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pedidos_detalles`
--

CREATE TABLE `pedidos_detalles` (
  `id` int(11) NOT NULL,
  `pedido_id` int(11) NOT NULL,
  `producto_id` int(11) NOT NULL,
  `cantidad` int(11) NOT NULL,
  `precio_unitario` decimal(10,2) NOT NULL,
  `subtotal` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `pedidos_detalles`
--

INSERT INTO `pedidos_detalles` (`id`, `pedido_id`, `producto_id`, `cantidad`, `precio_unitario`, `subtotal`) VALUES
(1, 1, 1, 10, 6000.00, 60000.00),
(2, 1, 11, 1, 6500.00, 6500.00),
(3, 1, 10, 1, 7000.00, 7000.00),
(4, 2, 11, 1, 6500.00, 6500.00),
(5, 3, 8, 3, 6000.00, 18000.00),
(6, 4, 3, 4, 6000.00, 24000.00);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `productos`
--

CREATE TABLE `productos` (
  `id` int(11) NOT NULL,
  `nombre` varchar(150) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `precio` decimal(10,2) NOT NULL,
  `stock` int(11) DEFAULT 0,
  `cantidad_vendida` int(11) DEFAULT 0,
  `categoria_id` int(11) DEFAULT NULL,
  `imagen` varchar(255) DEFAULT NULL,
  `estado` tinyint(4) DEFAULT 1,
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `productos`
--

INSERT INTO `productos` (`id`, `nombre`, `descripcion`, `precio`, `stock`, `cantidad_vendida`, `categoria_id`, `imagen`, `estado`, `fecha_creacion`) VALUES
(1, 'Limón 4 estaciones', 'Limón de excelente calidad que produce frutos durante todo el año. Ideal para climas cálidos y templados.', 5000.00, 13, 62, 1, '/integrador3.0/assets/imagenes/productos/691c0adec8f90.jpg', 1, '2025-11-17 22:02:06'),
(2, 'Naranja ombligo', 'Naranja dulce y jugosa, perfecta para consumo fresco. Frutos grandes sin semillas.', 6000.00, 12, 38, 1, '/integrador3.0/assets/imagenes/productos/691c0b1bbb88f.jpg', 1, '2025-11-17 22:02:06'),
(3, 'Mango', 'Mango tropical de pulpa dulce y aromática. Excelente para consumo fresco y preparaciones.', 6000.00, 0, 26, 3, '/integrador3.0/assets/imagenes/productos/691c0af53debf.jpg', 1, '2025-11-17 22:02:06'),
(4, 'Naranja de jugo', 'Naranja especialmente seleccionada para jugos. Alto contenido de jugo y sabor intenso.', 6000.00, 20, 35, 1, '/integrador3.0/assets/imagenes/productos/691c0b0206638.jpg', 1, '2025-11-17 22:02:06'),
(5, 'Palta', 'Palta cremosa y nutritiva. Perfecta para ensaladas y preparaciones saludables.', 6000.00, 0, 18, 3, '/integrador3.0/assets/imagenes/productos/691c0b2f65e46.jpg', 1, '2025-11-17 22:02:06'),
(6, 'Pomelo rosado', 'Pomelo de pulpa rosada, dulce y refrescante. Rico en vitamina C y antioxidantes.', 6000.00, 8, 25, 1, '/integrador3.0/assets/imagenes/productos/691c0b4843e17.jpg', 1, '2025-11-17 22:02:06'),
(7, 'Kinoto', 'Cítrico pequeño y aromático. Ideal para mermeladas y decoración de platos.', 6000.00, 0, 12, 1, '/integrador3.0/assets/imagenes/productos/691c0b57480c0.jpg', 1, '2025-11-17 22:02:06'),
(8, 'Durazno', 'Durazno jugoso y dulce. Excelente para consumo fresco, conservas y postres.', 6000.00, 2, 33, 2, '/integrador3.0/assets/imagenes/productos/691c0362b611c.jpg', 1, '2025-11-17 22:02:06'),
(9, 'Ciruela', 'Ciruela dulce y jugosa. Perfecta para consumo fresco y preparación de mermeladas.', 7000.00, 10, 16, 2, '/integrador3.0/assets/imagenes/productos/691bfad9c4c2f.jpg', 0, '2025-11-17 22:02:06'),
(10, 'Uva venus negra', 'Uva de mesa sin semillas, de maduración muy temprana, apreciada por su intenso color oscuro, sabor dulce y textura jugosa', 7000.00, 2, 1, 3, '/integrador3.0/assets/imagenes/productos/691bfbc3bc2c5.jpg', 1, '2025-11-18 04:53:23'),
(11, 'Ciruela roja', 'Conocida por su piel de color rojo intenso y su pulpa jugosa y dulce, que puede ser anaranjada o roja por dentro.', 7000.00, 6, 4, 2, '/integrador3.0/assets/imagenes/productos/691bff96ec749.jpg', 1, '2025-11-18 05:09:42'),
(12, 'Ciruela amarilla', 'Fruta de forma redonda u ovalada, con piel lisa y brillante de color amarillo dorado y pulpa jugosa, dulce y ligeramente ácida. Es una fuente de vitaminas (como la C) y fibra, y se puede consumir fresca o en diversas recetas', 6500.00, 10, 0, 2, '/integrador3.0/assets/imagenes/productos/691cb9421b80a.jpg', 1, '2025-11-18 18:21:54'),
(13, 'Pera de agua', 'jssjjsjakaka', 7000.00, 6, 0, 3, '/integrador3.0/assets/imagenes/productos/691ce74631d1b.jpg', 1, '2025-11-18 21:38:14');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `nombre` varchar(150) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password` varchar(255) NOT NULL,
  `rol` enum('usuario','administrador') DEFAULT 'usuario',
  `fecha_registro` timestamp NOT NULL DEFAULT current_timestamp(),
  `ultimo_acceso` timestamp NULL DEFAULT NULL,
  `estado` tinyint(4) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `nombre`, `email`, `password`, `rol`, `fecha_registro`, `ultimo_acceso`, `estado`) VALUES
(2, 'Usuario Demo', 'usuario@viveromym.com', '$2a$12$ov79AoZ9SE8nWj0mlV/hB.IHIvTwyp0t0BdVLVuwAmM.RKItcVtsC', 'usuario', '2025-11-17 22:00:19', NULL, 1),
(3, 'nombre prueba', 'prueba@gmail.com', '$2y$12$VBEYj5Uvqs8dOwl5snCToePf.si5PXKZwg4ENC3t53Ge6J4.N5Qkq', 'usuario', '2025-11-18 02:01:44', '2025-11-18 21:38:42', 1),
(4, 'kiara gamarra', 'kigama@gmail.com', '$2y$12$NRKRfSDmquNWIapFg3SeTufFHq98sOMrTHRipOv4WQ.Lr4z0Zf0Q6', 'usuario', '2025-11-18 02:17:43', NULL, 1),
(5, 'nombre dos', 'pruebaa@gmail.com', '$2y$12$Nb5oNYZUkDqGCRDDpXk9SevpNmeGeMokXlVzj8y7Qg8O40yEd.jza', 'usuario', '2025-11-18 02:38:15', '2025-11-18 03:19:49', 1),
(6, 'Gonzalo Gonzalez', 'gg@gmail.com', '$2y$12$OPI.ywKYF7.yq/ieNpXQVOzLCjruS5FB2kkgRKPFuVjTA9Zl9WWY.', 'usuario', '2025-11-18 06:47:23', '2025-11-18 07:59:39', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ventas`
--

CREATE TABLE `ventas` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `producto_id` int(11) NOT NULL,
  `cantidad` int(11) NOT NULL,
  `precio_unitario` decimal(10,2) NOT NULL,
  `subtotal` decimal(10,2) NOT NULL,
  `fecha_venta` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `ventas`
--

INSERT INTO `ventas` (`id`, `usuario_id`, `producto_id`, `cantidad`, `precio_unitario`, `subtotal`, `fecha_venta`) VALUES
(1, 6, 1, 5, 6000.00, 30000.00, '2025-11-18 07:59:58'),
(2, 3, 1, 2, 5000.00, 10000.00, '2025-11-18 20:24:22'),
(3, 3, 11, 2, 7000.00, 14000.00, '2025-11-18 21:39:29');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `administradores`
--
ALTER TABLE `administradores`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `idx_email_admin` (`email`);

--
-- Indices de la tabla `categorias`
--
ALTER TABLE `categorias`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nombre` (`nombre`);

--
-- Indices de la tabla `pedidos`
--
ALTER TABLE `pedidos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_usuario_pedidos` (`usuario_id`);

--
-- Indices de la tabla `pedidos_detalles`
--
ALTER TABLE `pedidos_detalles`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_pedido_detalles` (`pedido_id`),
  ADD KEY `idx_producto_detalles` (`producto_id`);

--
-- Indices de la tabla `productos`
--
ALTER TABLE `productos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_categoria` (`categoria_id`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `idx_email_usuarios` (`email`);

--
-- Indices de la tabla `ventas`
--
ALTER TABLE `ventas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `usuario_id` (`usuario_id`),
  ADD KEY `producto_id` (`producto_id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `administradores`
--
ALTER TABLE `administradores`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `categorias`
--
ALTER TABLE `categorias`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `pedidos`
--
ALTER TABLE `pedidos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `pedidos_detalles`
--
ALTER TABLE `pedidos_detalles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `productos`
--
ALTER TABLE `productos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `ventas`
--
ALTER TABLE `ventas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `pedidos`
--
ALTER TABLE `pedidos`
  ADD CONSTRAINT `pedidos_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`);

--
-- Filtros para la tabla `pedidos_detalles`
--
ALTER TABLE `pedidos_detalles`
  ADD CONSTRAINT `pedidos_detalles_ibfk_1` FOREIGN KEY (`pedido_id`) REFERENCES `pedidos` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `pedidos_detalles_ibfk_2` FOREIGN KEY (`producto_id`) REFERENCES `productos` (`id`);

--
-- Filtros para la tabla `productos`
--
ALTER TABLE `productos`
  ADD CONSTRAINT `productos_ibfk_1` FOREIGN KEY (`categoria_id`) REFERENCES `categorias` (`id`);

--
-- Filtros para la tabla `ventas`
--
ALTER TABLE `ventas`
  ADD CONSTRAINT `ventas_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`),
  ADD CONSTRAINT `ventas_ibfk_2` FOREIGN KEY (`producto_id`) REFERENCES `productos` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
