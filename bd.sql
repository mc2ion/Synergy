-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Versión del servidor:         5.6.26-log - MySQL Community Server (GPL)
-- SO del servidor:              Win64
-- HeidiSQL Versión:             9.2.0.4947
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;

-- Volcando estructura de base de datos para manager
DROP DATABASE IF EXISTS `manager`;
CREATE DATABASE IF NOT EXISTS `manager` /*!40100 DEFAULT CHARACTER SET utf8 */;
USE `manager`;


-- Volcando estructura para tabla manager.category
DROP TABLE IF EXISTS `category`;
CREATE TABLE IF NOT EXISTS `category` (
  `category_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `type` varchar(100) NOT NULL,
  `active` int(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`category_id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;

-- Volcando datos para la tabla manager.category: ~5 rows (aproximadamente)
DELETE FROM `category`;
/*!40000 ALTER TABLE `category` DISABLE KEYS */;
INSERT INTO `category` (`category_id`, `name`, `type`, `active`) VALUES
	(1, 'Diamond', 'contenido', 1),
	(2, 'Platinum', 'contenido', 1),
	(3, 'Gold', 'grid', 1),
	(4, 'Silver', 'grid', 1),
	(5, 'Bronce', 'grid', 1);
/*!40000 ALTER TABLE `category` ENABLE KEYS */;


-- Volcando estructura para tabla manager.client
DROP TABLE IF EXISTS `client`;
CREATE TABLE IF NOT EXISTS `client` (
  `client_id` int(11) NOT NULL AUTO_INCREMENT,
  `logo_path` text NOT NULL,
  `name` varchar(150) NOT NULL,
  `country` varchar(150) NOT NULL,
  `contact_name` varchar(100) NOT NULL,
  `contact_phone` varchar(100) NOT NULL,
  `contact_email` varchar(255) NOT NULL,
  `active` int(1) NOT NULL DEFAULT '1',
  `main_menu_color` varchar(50) NOT NULL,
  `main_menu_color_aux` varchar(50) NOT NULL,
  `main_submenu_color` varchar(50) NOT NULL,
  `button_color` varchar(50) NOT NULL,
  `top_menu_color` varchar(50) NOT NULL,
  `font_main_menu_color` varchar(50) NOT NULL DEFAULT '#111111',
  `font_top_menu_color` varchar(50) NOT NULL DEFAULT '#000000',
  PRIMARY KEY (`client_id`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8;

-- Volcando datos para la tabla manager.client: ~2 rows (aproximadamente)
DELETE FROM `client`;
/*!40000 ALTER TABLE `client` DISABLE KEYS */;
INSERT INTO `client` (`client_id`, `logo_path`, `name`, `country`, `contact_name`, `contact_phone`, `contact_email`, `active`, `main_menu_color`, `main_menu_color_aux`, `main_submenu_color`, `button_color`, `top_menu_color`, `font_main_menu_color`, `font_top_menu_color`) VALUES
	(15, 'files/client/55c520ebdd1af_Artboard-11 copia 21.png', 'Grupo Cerca', 'Venezuela', 'Jessica Mendoza Josefina del Carmen', '(+58)434324', 'jessica.mendoza@synergy-gb.com', 1, '#00E39F', '#33e9b2', '#14e5a6', '#6f0e54', '#821063', '#000000', '#FFFFFF'),
	(16, 'files/client/55c63b1d621d4_55bbc3cc4ffe7_1434496634_computer.png', 'Simón Bolívar', 'Afganistán', 'Marión', '3443534', 'fa@gmail.com', 0, '#FFFFFF', '#ffffff', '#ffffff', '#b3b3b3', '#FFFFFF', '#111111', '#000000');
/*!40000 ALTER TABLE `client` ENABLE KEYS */;


-- Volcando estructura para tabla manager.event
DROP TABLE IF EXISTS `event`;
CREATE TABLE IF NOT EXISTS `event` (
  `event_id` int(11) NOT NULL AUTO_INCREMENT,
  `client_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text,
  `date_ini` date NOT NULL,
  `date_end` date NOT NULL,
  `address` text NOT NULL,
  `map_path` text NOT NULL,
  `country` varchar(150) NOT NULL,
  `phone` varchar(100) NOT NULL,
  `website` varchar(255) NOT NULL,
  `social_networks` text,
  `organizers` text NOT NULL,
  `active` int(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`event_id`),
  KEY `FK1_EVENT_CLIENT` (`client_id`),
  CONSTRAINT `FK1_EVENT_CLIENT` FOREIGN KEY (`client_id`) REFERENCES `client` (`client_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=28 DEFAULT CHARSET=utf8;

-- Volcando datos para la tabla manager.event: ~2 rows (aproximadamente)
DELETE FROM `event`;
/*!40000 ALTER TABLE `event` DISABLE KEYS */;
INSERT INTO `event` (`event_id`, `client_id`, `name`, `description`, `date_ini`, `date_end`, `address`, `map_path`, `country`, `phone`, `website`, `social_networks`, `organizers`, `active`) VALUES
	(26, 15, 'Simón Bolívar', NULL, '2015-08-21', '2015-08-23', 'via israel', 'files/event/1555c90a24416f2_71755b96bbc52971_1434576812_man.png', 'Uruguay', '(+598)543367778', 'neuvoo.com', '[{"type":"facebook","value":"myFacebook"}]', '[{"name":"Marion","description":"Carambula"}]', 1),
	(27, 15, 'Technology Day - El Salvador', 'Prueba', '2015-08-10', '2015-08-10', 'El Salvador', 'files/event/1555c9082aaa07e_mapa.jpg', 'El Salvador', '(+503)34567895', 'www.itnow.com', '[{"type":"twitter","value":"https:\\/\\/trello.com\\/b\\/m9FHyULm\\/seguimiento"}]', '[{"name":"It Now 1","description":"Prueba"},{"name":"It Now 2","description":"Prueba"}]', 1);
/*!40000 ALTER TABLE `event` ENABLE KEYS */;


-- Volcando estructura para tabla manager.exhibitor
DROP TABLE IF EXISTS `exhibitor`;
CREATE TABLE IF NOT EXISTS `exhibitor` (
  `exhibitor_id` int(11) NOT NULL AUTO_INCREMENT,
  `event_id` int(11) NOT NULL,
  `category_id` int(11) DEFAULT NULL,
  `company_name` varchar(150) DEFAULT NULL,
  `description` text,
  `image_path` text NOT NULL,
  `position` int(11) DEFAULT NULL,
  `other` varchar(255) DEFAULT NULL,
  `active` int(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`exhibitor_id`),
  KEY `FK1_EXHIBITOR_EVENT` (`event_id`),
  KEY `FK2_EXHIBITOR_CATEGORY` (`category_id`),
  CONSTRAINT `FK1_EXHIBITOR_EVENT` FOREIGN KEY (`event_id`) REFERENCES `event` (`event_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK2_EXHIBITOR_CATEGORY` FOREIGN KEY (`category_id`) REFERENCES `category` (`category_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;

-- Volcando datos para la tabla manager.exhibitor: ~0 rows (aproximadamente)
DELETE FROM `exhibitor`;
/*!40000 ALTER TABLE `exhibitor` DISABLE KEYS */;
/*!40000 ALTER TABLE `exhibitor` ENABLE KEYS */;


-- Volcando estructura para tabla manager.permission
DROP TABLE IF EXISTS `permission`;
CREATE TABLE IF NOT EXISTS `permission` (
  `permission_id` int(11) NOT NULL AUTO_INCREMENT,
  `section_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `create` int(1) NOT NULL DEFAULT '1',
  `read` int(1) NOT NULL DEFAULT '1',
  `update` int(1) NOT NULL DEFAULT '1',
  `delete` int(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`permission_id`),
  KEY `FK1_PERMISSION_SECTION` (`section_id`),
  KEY `FK2_PERMISSION_USER` (`user_id`),
  CONSTRAINT `FK1_PERMISSION_SECTION` FOREIGN KEY (`section_id`) REFERENCES `section` (`section_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK2_PERMISSION_USER` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=79 DEFAULT CHARSET=utf8;

-- Volcando datos para la tabla manager.permission: ~18 rows (aproximadamente)
DELETE FROM `permission`;
/*!40000 ALTER TABLE `permission` DISABLE KEYS */;
INSERT INTO `permission` (`permission_id`, `section_id`, `user_id`, `create`, `read`, `update`, `delete`) VALUES
	(37, 3, 15, 1, 1, 0, 0),
	(38, 4, 15, 1, 1, 0, 0),
	(39, 7, 15, 0, 0, 0, 0),
	(40, 8, 15, 0, 0, 0, 0),
	(41, 6, 15, 0, 0, 0, 0),
	(42, 9, 15, 0, 0, 0, 0),
	(49, 3, 35, 1, 1, 0, 0),
	(50, 4, 35, 1, 1, 0, 0),
	(51, 6, 35, 0, 0, 0, 0),
	(52, 7, 35, 0, 0, 0, 0),
	(53, 8, 35, 0, 0, 0, 0),
	(54, 9, 35, 0, 0, 0, 0),
	(61, 3, 39, 0, 0, 0, 0),
	(62, 4, 39, 0, 0, 0, 0),
	(63, 6, 39, 0, 0, 0, 0),
	(64, 7, 39, 0, 0, 0, 0),
	(65, 8, 39, 0, 0, 0, 0),
	(66, 9, 39, 0, 0, 0, 0);
/*!40000 ALTER TABLE `permission` ENABLE KEYS */;


-- Volcando estructura para tabla manager.question_option
DROP TABLE IF EXISTS `question_option`;
CREATE TABLE IF NOT EXISTS `question_option` (
  `option_id` int(11) NOT NULL AUTO_INCREMENT,
  `question_id` int(11) NOT NULL,
  `optionDesc` varchar(50) NOT NULL,
  `position` int(11) NOT NULL,
  `active` int(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`option_id`),
  KEY `FK1_OPTION_QUESTION` (`question_id`),
  CONSTRAINT `FK1_OPTION_QUESTION` FOREIGN KEY (`question_id`) REFERENCES `survey_question` (`question_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8;

-- Volcando datos para la tabla manager.question_option: ~0 rows (aproximadamente)
DELETE FROM `question_option`;
/*!40000 ALTER TABLE `question_option` DISABLE KEYS */;
/*!40000 ALTER TABLE `question_option` ENABLE KEYS */;


-- Volcando estructura para tabla manager.question_result
DROP TABLE IF EXISTS `question_result`;
CREATE TABLE IF NOT EXISTS `question_result` (
  `result_id` int(11) NOT NULL AUTO_INCREMENT,
  `question_id` int(11) NOT NULL,
  `option_id` int(11) NOT NULL,
  `user_id` varchar(255) NOT NULL,
  PRIMARY KEY (`result_id`),
  KEY `FK1_RESULT_QUESTION` (`question_id`),
  KEY `FK2_RESULT_OPTION` (`option_id`),
  CONSTRAINT `FK1_RESULT_QUESTION` FOREIGN KEY (`question_id`) REFERENCES `survey_question` (`question_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK2_RESULT_OPTION` FOREIGN KEY (`option_id`) REFERENCES `question_option` (`option_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;

-- Volcando datos para la tabla manager.question_result: ~0 rows (aproximadamente)
DELETE FROM `question_result`;
/*!40000 ALTER TABLE `question_result` DISABLE KEYS */;
/*!40000 ALTER TABLE `question_result` ENABLE KEYS */;


-- Volcando estructura para tabla manager.review
DROP TABLE IF EXISTS `review`;
CREATE TABLE IF NOT EXISTS `review` (
  `review_id` int(11) NOT NULL AUTO_INCREMENT,
  `session_id` int(11) NOT NULL,
  `ranking` int(11) NOT NULL DEFAULT '0',
  `comment` text,
  `user_id` varchar(255) NOT NULL,
  PRIMARY KEY (`review_id`),
  KEY `FK1_REVIEW_SESSION` (`session_id`),
  CONSTRAINT `FK1_REVIEW_SESSION` FOREIGN KEY (`session_id`) REFERENCES `session` (`session_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;

-- Volcando datos para la tabla manager.review: ~0 rows (aproximadamente)
DELETE FROM `review`;
/*!40000 ALTER TABLE `review` DISABLE KEYS */;
/*!40000 ALTER TABLE `review` ENABLE KEYS */;


-- Volcando estructura para tabla manager.room
DROP TABLE IF EXISTS `room`;
CREATE TABLE IF NOT EXISTS `room` (
  `room_id` int(11) NOT NULL AUTO_INCREMENT,
  `event_id` int(11) NOT NULL,
  `name` varchar(150) NOT NULL,
  `active` int(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`room_id`),
  KEY `FK1_ROOM_EVENT` (`event_id`),
  CONSTRAINT `FK1_ROOM_EVENT` FOREIGN KEY (`event_id`) REFERENCES `event` (`event_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8;

-- Volcando datos para la tabla manager.room: ~0 rows (aproximadamente)
DELETE FROM `room`;
/*!40000 ALTER TABLE `room` DISABLE KEYS */;
/*!40000 ALTER TABLE `room` ENABLE KEYS */;


-- Volcando estructura para tabla manager.section
DROP TABLE IF EXISTS `section`;
CREATE TABLE IF NOT EXISTS `section` (
  `section_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(150) NOT NULL,
  `type` varchar(50) NOT NULL DEFAULT 'cliente',
  `file` varchar(50) NOT NULL,
  `father_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`section_id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8;

-- Volcando datos para la tabla manager.section: ~9 rows (aproximadamente)
DELETE FROM `section`;
/*!40000 ALTER TABLE `section` DISABLE KEYS */;
INSERT INTO `section` (`section_id`, `name`, `type`, `file`, `father_id`) VALUES
	(1, 'clientes', 'administrador', 'clients.php', NULL),
	(2, 'usuarios', 'administrador', 'users.php', NULL),
	(3, 'eventos', 'cliente', 'events.php', NULL),
	(4, 'sesiones', 'cliente', 'sessions.php', NULL),
	(5, 'salas', 'cliente', 'rooms.php', 4),
	(6, 'evaluaciones', 'cliente', 'reviews.php', NULL),
	(7, 'expositores', 'cliente', 'exhibitors.php', NULL),
	(8, 'presentadores', 'cliente', 'speakers.php', NULL),
	(9, 'encuestas', 'cliente', 'surveys.php', NULL);
/*!40000 ALTER TABLE `section` ENABLE KEYS */;


-- Volcando estructura para tabla manager.session
DROP TABLE IF EXISTS `session`;
CREATE TABLE IF NOT EXISTS `session` (
  `session_id` int(11) NOT NULL AUTO_INCREMENT,
  `event_id` int(11) NOT NULL,
  `room_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `date` date NOT NULL,
  `time` time NOT NULL,
  `speaker` varchar(255) NOT NULL,
  `description` text,
  `image_path` text NOT NULL,
  `link` varchar(255) DEFAULT NULL,
  `active` int(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`session_id`),
  KEY `FK2_SESSION_EVENT` (`event_id`),
  KEY `FK1_SESSION_ROOM` (`room_id`),
  CONSTRAINT `FK1_SESSION_ROOM` FOREIGN KEY (`room_id`) REFERENCES `room` (`room_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK2_SESSION_EVENT` FOREIGN KEY (`event_id`) REFERENCES `event` (`event_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8;

-- Volcando datos para la tabla manager.session: ~0 rows (aproximadamente)
DELETE FROM `session`;
/*!40000 ALTER TABLE `session` DISABLE KEYS */;
/*!40000 ALTER TABLE `session` ENABLE KEYS */;


-- Volcando estructura para tabla manager.speaker
DROP TABLE IF EXISTS `speaker`;
CREATE TABLE IF NOT EXISTS `speaker` (
  `speaker_id` int(11) NOT NULL AUTO_INCREMENT,
  `event_id` int(11) NOT NULL,
  `name` varchar(150) NOT NULL,
  `company_name` varchar(150) NOT NULL,
  `image_path` text NOT NULL,
  `description` text NOT NULL,
  `session_title` varchar(255) DEFAULT NULL,
  `other` varchar(255) DEFAULT NULL,
  `active` int(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`speaker_id`),
  KEY `FK1_SPEAKER_EVENT` (`event_id`),
  CONSTRAINT `FK1_SPEAKER_EVENT` FOREIGN KEY (`event_id`) REFERENCES `event` (`event_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

-- Volcando datos para la tabla manager.speaker: ~0 rows (aproximadamente)
DELETE FROM `speaker`;
/*!40000 ALTER TABLE `speaker` DISABLE KEYS */;
/*!40000 ALTER TABLE `speaker` ENABLE KEYS */;


-- Volcando estructura para tabla manager.survey_question
DROP TABLE IF EXISTS `survey_question`;
CREATE TABLE IF NOT EXISTS `survey_question` (
  `question_id` int(11) NOT NULL AUTO_INCREMENT,
  `event_id` int(11) NOT NULL,
  `question` text NOT NULL,
  `position` int(5) NOT NULL,
  `active` int(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`question_id`),
  KEY `FK1_QUESTION_EVENT` (`event_id`),
  CONSTRAINT `FK1_QUESTION_EVENT` FOREIGN KEY (`event_id`) REFERENCES `event` (`event_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;

-- Volcando datos para la tabla manager.survey_question: ~0 rows (aproximadamente)
DELETE FROM `survey_question`;
/*!40000 ALTER TABLE `survey_question` DISABLE KEYS */;
/*!40000 ALTER TABLE `survey_question` ENABLE KEYS */;


-- Volcando estructura para tabla manager.user
DROP TABLE IF EXISTS `user`;
CREATE TABLE IF NOT EXISTS `user` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(50) NOT NULL,
  `photo_path` text,
  `type` varchar(50) NOT NULL,
  `client_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`user_id`),
  KEY `FK1_USER_CLIENT` (`client_id`),
  CONSTRAINT `FK1_USER_CLIENT` FOREIGN KEY (`client_id`) REFERENCES `client` (`client_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=42 DEFAULT CHARSET=utf8;

-- Volcando datos para la tabla manager.user: ~3 rows (aproximadamente)
DELETE FROM `user`;
/*!40000 ALTER TABLE `user` DISABLE KEYS */;
INSERT INTO `user` (`user_id`, `first_name`, `last_name`, `email`, `password`, `photo_path`, `type`, `client_id`) VALUES
	(15, 'Jessica', 'Mendoza', 'jessica.mendoza@synergy-gb.com', 'e10adc3949ba59abbe56e057f20f883e', 'files/user/55c4e06f61e21_55b565c0b0d45_1434760354_user_male2.png', 'Super Usuario', 15),
	(35, 'Juan', 'García', 'juan.garcia@synergy-gb.com', '2ce13af8705b52796ef115e2e92b460b', 'files/user/55c8fa3195bfe_55a1eba091399_1434575257_profle.png', 'Supervisor', 15),
	(39, 'Marión', 'Carámbula', 'marion.carambula@synergy-gb.com', 'e10adc3949ba59abbe56e057f20f883e', 'files/user/55c8fe417da9d_55be472359a2a_1434164009_group.png', 'Administrador', 15);
/*!40000 ALTER TABLE `user` ENABLE KEYS */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
