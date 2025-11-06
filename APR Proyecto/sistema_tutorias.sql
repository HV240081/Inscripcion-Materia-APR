-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1:3306
-- Tiempo de generación: 06-11-2025 a las 02:54:31
-- Versión del servidor: 9.1.0
-- Versión de PHP: 8.3.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `sistema_tutorias`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `inscripciones`
--

DROP TABLE IF EXISTS `inscripciones`;
CREATE TABLE IF NOT EXISTS `inscripciones` (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_tutoria` int NOT NULL,
  `id_alumno` int NOT NULL,
  `comentario` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `estado` enum('pendiente','confirmado','cancelado') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT 'pendiente',
  `fecha_inscripcion` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `id_tutoria` (`id_tutoria`),
  KEY `id_alumno` (`id_alumno`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `inscripciones`
--

INSERT INTO `inscripciones` (`id`, `id_tutoria`, `id_alumno`, `comentario`, `estado`, `fecha_inscripcion`) VALUES
(1, 4, 7, NULL, 'pendiente', '2025-11-05 22:07:44'),
(2, 5, 7, NULL, 'pendiente', '2025-11-05 23:21:25'),
(3, 4, 4, NULL, 'pendiente', '2025-11-05 23:10:42'),
(4, 4, 9, NULL, 'pendiente', '2025-11-06 01:48:39'),
(5, 7, 7, NULL, 'pendiente', '2025-11-06 02:29:23');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `materias`
--

DROP TABLE IF EXISTS `materias`;
CREATE TABLE IF NOT EXISTS `materias` (
  `id` int NOT NULL AUTO_INCREMENT,
  `codigo` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `nombre` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `codigo` (`codigo`)
) ENGINE=MyISAM AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `materias`
--

INSERT INTO `materias` (`id`, `codigo`, `nombre`) VALUES
(1, 'ALG501', 'Álgebra Vectorial y Matrices'),
(2, 'ANF231', 'Antropología Filosófica'),
(3, 'LME404', 'Lenguajes de Marcado y Estilo Web'),
(4, 'PAL404', 'Programación de Algoritmos'),
(5, 'REC404', 'Redes de Comunicación'),
(6, 'ASB404', 'Análisis y Diseño de Sistemas y Base de Datos'),
(7, 'DAW404', 'Desarrollo de Aplic. Web con Soft. Interpret. en el Cliente'),
(8, 'DSP404', 'Desarrollo de Aplicaciones con Software Propietario'),
(9, 'POO404', 'Programación Orientada a Objetos'),
(10, 'PSC231', 'Pensamiento Social Cristiano'),
(11, 'ASN441', 'Administración de Servicios en la Nube'),
(12, 'DPS441', 'Diseño y Programación de Software Multiplataforma'),
(13, 'DSS404', 'Desarrollo de Aplic. Web con Soft. Interpret. en el Servidor'),
(14, 'DWF404', 'Desarrollo de Aplicaciones con Web Frameworks'),
(15, 'SPP404', 'Servidores en Plataformas Propietarias'),
(16, 'APR404', 'Administración de Proyectos'),
(17, 'DSM441', 'Desarrollo de Software para Móviles'),
(18, 'EAI441', 'Electrónica Aplicada al Internet de las Cosas'),
(19, 'SDR404', 'Seguridad de Redes'),
(20, 'SPL404', 'Servidores en Plataformas Libres');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `notificaciones`
--

DROP TABLE IF EXISTS `notificaciones`;
CREATE TABLE IF NOT EXISTS `notificaciones` (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_usuario` int NOT NULL,
  `mensaje` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `leido` tinyint(1) DEFAULT '0',
  `fecha` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `id_usuario` (`id_usuario`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tutorias`
--

DROP TABLE IF EXISTS `tutorias`;
CREATE TABLE IF NOT EXISTS `tutorias` (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_docente` int NOT NULL,
  `id_materia` int NOT NULL,
  `titulo` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `descripcion` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `lugar` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `tipo` enum('virtual','presencial') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `plataforma` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `fecha` date NOT NULL,
  `hora_inicio` time NOT NULL,
  `hora_fin` time NOT NULL,
  `cupo_maximo` int DEFAULT '10',
  `fecha_creacion` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `id_docente` (`id_docente`),
  KEY `id_materia` (`id_materia`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tutorias`
--

INSERT INTO `tutorias` (`id`, `id_docente`, `id_materia`, `titulo`, `descripcion`, `lugar`, `tipo`, `plataforma`, `fecha`, `hora_inicio`, `hora_fin`, `cupo_maximo`, `fecha_creacion`) VALUES
(1, 3, 1, 'Matrices y vectores', 'Llevar cuaderlo y calculadora.', 'Biblioteca', 'presencial', NULL, '2025-10-31', '12:00:00', '15:00:00', 10, '2025-10-31 22:08:21'),
(2, 2, 1, 'Metrices y vectores', 'Llevar calculadora y regla.', 'Biblioteca VIPE', 'presencial', NULL, '2025-11-04', '13:00:00', '14:00:00', 20, '2025-11-04 23:29:48'),
(3, 2, 1, 'Metrices y vectores', 'Llevar calculadora y regla.', 'Biblioteca VIPE', 'presencial', NULL, '2025-11-04', '13:00:00', '14:00:00', 20, '2025-11-04 23:36:17'),
(4, 2, 1, 'R4', 'Enlace: https://microsof.teams.com', 'Teams', 'virtual', NULL, '2025-11-13', '07:00:00', '09:00:00', 20, '2025-11-04 23:41:24'),
(5, 2, 9, 'Principios de POO y clases', '', 'Teams', 'virtual', NULL, '2025-11-05', '09:00:00', '10:00:00', 1, '2025-11-05 23:10:29'),
(6, 1, 19, 'Configuracion de una VPN', '', 'B-26', 'presencial', NULL, '2025-11-11', '09:00:00', '11:00:00', 20, '2025-11-06 01:57:30'),
(7, 1, 1, 'Creación de matrices y vectores', 'Favor estimados estidiantes, tomar en cuenta que es indispensable el uso de calculadora para resolver los ejercicios, favor llevarla.', 'Edif C-26', 'presencial', NULL, '2025-11-06', '09:00:00', '11:00:00', 15, '2025-11-06 02:26:50');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

DROP TABLE IF EXISTS `usuarios`;
CREATE TABLE IF NOT EXISTS `usuarios` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `correo` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `carnet` char(8) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `contrasena` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `rol` enum('docente','alumno') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `estado` enum('activo','inactivo') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT 'activo',
  `fecha_registro` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `correo` (`correo`)
) ENGINE=MyISAM AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `nombre`, `correo`, `carnet`, `contrasena`, `rol`, `estado`, `fecha_registro`) VALUES
(10, 'Paola Vega', 'pvega@tutorias.com', 'AL000007', '12345678', 'alumno', 'activo', '2025-10-31 03:41:11'),
(1, 'Carlos Hernández', 'chernandez@tutorias.com', 'DO000001', '12345678', 'docente', 'activo', '2025-10-31 03:41:11'),
(2, 'María López', 'mlopez@tutorias.com', 'DO000002', '12345678', 'docente', 'activo', '2025-10-31 03:41:11'),
(3, 'Andrés Gómez', 'agomez@tutorias.com', 'DO000003', '12345678', 'docente', 'activo', '2025-10-31 03:41:11'),
(4, 'Laura Torres', 'ltorres@tutorias.com', 'AL000001', '12345678', 'alumno', 'activo', '2025-10-31 03:41:11'),
(5, 'José Martínez', 'jmartinez@tutorias.com', 'AL000002', '12345678', 'alumno', 'activo', '2025-10-31 03:41:11'),
(6, 'Camila Díaz', 'cdiaz@tutorias.com', 'AL000003', '12345678', 'alumno', 'activo', '2025-10-31 03:41:11'),
(7, 'Daniel Rojas', 'drojas@tutorias.com', 'AL000004', '12345678', 'alumno', 'activo', '2025-10-31 03:41:11'),
(8, 'Sofía Ramírez', 'sramirez@tutorias.com', 'AL000005', '12345678', 'alumno', 'activo', '2025-10-31 03:41:11'),
(9, 'Felipe Castro', 'fcastro@tutorias.com', 'AL000006', '12345678', 'alumno', 'activo', '2025-10-31 03:41:11'),
(11, 'Luis Pineda', 'lpineda@tutorias.com', 'AL000008', '12345678', 'alumno', 'activo', '2025-10-31 03:41:11'),
(12, 'Valeria Flores', 'vflores@tutorias.com', 'AL000009', '12345678', 'alumno', 'activo', '2025-10-31 03:41:11');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
