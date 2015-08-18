-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Versión del servidor:         5.6.26-log - MySQL Community Server (GPL)
-- SO del servidor:              Win64
-- HeidiSQL Versión:             9.2.0.4947
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8mb4 */;
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
  `main_menu_color` varchar(50) NOT NULL,
  `main_menu_color_aux` varchar(50) NOT NULL,
  `main_submenu_color` varchar(50) NOT NULL,
  `button_color` varchar(50) NOT NULL,
  `top_menu_color` varchar(50) NOT NULL,
  `font_main_menu_color` varchar(50) NOT NULL DEFAULT '#111111',
  `font_top_menu_color` varchar(50) NOT NULL DEFAULT '#000000',
  `active` int(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`client_id`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8;

-- Volcando datos para la tabla manager.client: ~1 rows (aproximadamente)
DELETE FROM `client`;
/*!40000 ALTER TABLE `client` DISABLE KEYS */;
INSERT INTO `client` (`client_id`, `logo_path`, `name`, `country`, `contact_name`, `contact_phone`, `contact_email`, `main_menu_color`, `main_menu_color_aux`, `main_submenu_color`, `button_color`, `top_menu_color`, `font_main_menu_color`, `font_top_menu_color`, `active`) VALUES
	(18, 'files/client/55cb5c9b164cd_55b52eea57ec2_1434584130_plugin.png', 'TechDay', 'Panamá', 'Jessica Mendoza', '(+507)63282430', 'jessica.mendoza@synergy-gb.com', '#432EFF', '#6958ff', '#523eff', '#d9a325', '#FFC02B', '#FFFFFF', '#000000', 1);
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
) ENGINE=InnoDB AUTO_INCREMENT=29 DEFAULT CHARSET=utf8;

-- Volcando datos para la tabla manager.event: ~1 rows (aproximadamente)
DELETE FROM `event`;
/*!40000 ALTER TABLE `event` DISABLE KEYS */;
INSERT INTO `event` (`event_id`, `client_id`, `name`, `description`, `date_ini`, `date_end`, `address`, `map_path`, `country`, `phone`, `website`, `social_networks`, `organizers`, `active`) VALUES
	(28, 18, 'Grupo Cerca', NULL, '2015-08-12', '2015-08-17', 'Via Israel, edificio Terrawind piso 33.', 'files/event/1855cb6184d90d8_1555c9082aaa07e_mapa.jpg', 'Panamá', '(+507)63282430', 'http://neuvoo.com', '[{"type":"twitter","value":"http:\\/\\/twitter.com","title":"Mi Twitter"}]', '[{"name":"Juan Garcia","description":"Esta es una descripci\\u00f3n","url":"http:\\/\\/synergy.com"}]', 1);
/*!40000 ALTER TABLE `event` ENABLE KEYS */;


-- Volcando estructura para tabla manager.exhibitor
DROP TABLE IF EXISTS `exhibitor`;
CREATE TABLE IF NOT EXISTS `exhibitor` (
  `exhibitor_id` int(11) NOT NULL AUTO_INCREMENT,
  `event_id` int(11) NOT NULL,
  `category_id` int(11) DEFAULT NULL,
  `image_path` text NOT NULL,
  `company_name` varchar(150) DEFAULT NULL,
  `description` text,
  `position` int(11) DEFAULT NULL,
  `other` varchar(255) DEFAULT NULL,
  `active` int(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`exhibitor_id`),
  KEY `FK1_EXHIBITOR_EVENT` (`event_id`),
  KEY `FK2_EXHIBITOR_CATEGORY` (`category_id`),
  CONSTRAINT `FK1_EXHIBITOR_EVENT` FOREIGN KEY (`event_id`) REFERENCES `event` (`event_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK2_EXHIBITOR_CATEGORY` FOREIGN KEY (`category_id`) REFERENCES `category` (`category_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8;

-- Volcando datos para la tabla manager.exhibitor: ~3 rows (aproximadamente)
DELETE FROM `exhibitor`;
/*!40000 ALTER TABLE `exhibitor` DISABLE KEYS */;
INSERT INTO `exhibitor` (`exhibitor_id`, `event_id`, `category_id`, `image_path`, `company_name`, `description`, `position`, `other`, `active`) VALUES
	(10, 28, 2, 'files/exhibitor/182855cb661bb7198_1155c01f7f5d595_1434593493_wordpress.png', 'Synergy', 'Está es una descripción sobre Synergy como expositor Platinum', 1, NULL, 1),
	(11, 28, 3, 'files/exhibitor/182855cb66f759a16_71755b59719d4f62_1434496634_computer.png', '', '', NULL, '', 1);
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
) ENGINE=InnoDB AUTO_INCREMENT=89 DEFAULT CHARSET=utf8;

-- Volcando datos para la tabla manager.permission: ~12 rows (aproximadamente)
DELETE FROM `permission`;
/*!40000 ALTER TABLE `permission` DISABLE KEYS */;
INSERT INTO `permission` (`permission_id`, `section_id`, `user_id`, `create`, `read`, `update`, `delete`) VALUES
	(84, 3, 45, 1, 1, 0, 0),
	(85, 4, 45, 1, 1, 0, 0),
	(86, 7, 45, 1, 1, 0, 0),
	(87, 8, 45, 1, 1, 0, 0),
	(88, 9, 45, 1, 1, 0, 0);
/*!40000 ALTER TABLE `permission` ENABLE KEYS */;


-- Volcando estructura para tabla manager.question_option
DROP TABLE IF EXISTS `question_option`;
CREATE TABLE IF NOT EXISTS `question_option` (
  `option_id` int(11) NOT NULL AUTO_INCREMENT,
  `question_id` int(11) NOT NULL,
  `optionDesc` varchar(50) NOT NULL,
  `active` int(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`option_id`),
  KEY `FK1_OPTION_QUESTION` (`question_id`),
  CONSTRAINT `FK1_OPTION_QUESTION` FOREIGN KEY (`question_id`) REFERENCES `survey_question` (`question_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=31 DEFAULT CHARSET=utf8;

-- Volcando datos para la tabla manager.question_option: ~2 rows (aproximadamente)
DELETE FROM `question_option`;
/*!40000 ALTER TABLE `question_option` DISABLE KEYS */;
INSERT INTO `question_option` (`option_id`, `question_id`, `optionDesc`, `active`) VALUES
	(28, 14, 'Bueno', 1),
	(29, 14, 'Malo', 1),
	(30, 14, 'Regular', 1);
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
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8;

-- Volcando datos para la tabla manager.question_result: ~0 rows (aproximadamente)
DELETE FROM `question_result`;
/*!40000 ALTER TABLE `question_result` DISABLE KEYS */;
INSERT INTO `question_result` (`result_id`, `question_id`, `option_id`, `user_id`) VALUES
	(8, 14, 28, 'ABAJA');
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
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8;

-- Volcando datos para la tabla manager.review: ~0 rows (aproximadamente)
DELETE FROM `review`;
/*!40000 ALTER TABLE `review` DISABLE KEYS */;
INSERT INTO `review` (`review_id`, `session_id`, `ranking`, `comment`, `user_id`) VALUES
	(8, 20, 5, 'Me encantó la exposición', '23348'),
	(9, 20, 4, NULL, '2423425');
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
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8;

-- Volcando datos para la tabla manager.room: ~6 rows (aproximadamente)
DELETE FROM `room`;
/*!40000 ALTER TABLE `room` DISABLE KEYS */;
INSERT INTO `room` (`room_id`, `event_id`, `name`, `active`) VALUES
	(22, 28, 'Sala Conferencia IT', 1);
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
	(6, 'evaluaciones', 'cliente', 'reviews.php', 4),
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
  `time_ini` time NOT NULL,
  `time_end` time NOT NULL,
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
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8;

-- Volcando datos para la tabla manager.session: ~2 rows (aproximadamente)
DELETE FROM `session`;
/*!40000 ALTER TABLE `session` DISABLE KEYS */;
INSERT INTO `session` (`session_id`, `event_id`, `room_id`, `title`, `date`, `time_ini`, `time_end`, `speaker`, `description`, `image_path`, `link`, `active`) VALUES
	(20, 28, 22, 'Las tecnologías del futuro', '2015-08-12', '10:10:00', '10:30:00', 'Marión Carámbula', NULL, 'files/session/182855cb64e658cbb_132355bea091611a2_1434576812_man.png', 'http://synergy.com', 1),
	(21, 28, 22, 'Las tecnologías del futuro', '2015-08-12', '10:30:00', '10:40:00', 'Marión Carámbula', '', 'files/session/182855cb67898e4cf_112055bbe3824adec_1434567760_check.png', '', 0);
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
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;

-- Volcando datos para la tabla manager.speaker: ~2 rows (aproximadamente)
DELETE FROM `speaker`;
/*!40000 ALTER TABLE `speaker` DISABLE KEYS */;
INSERT INTO `speaker` (`speaker_id`, `event_id`, `name`, `company_name`, `image_path`, `description`, `session_title`, `other`, `active`) VALUES
	(6, 28, 'Javier Lugo', 'Ford', 'files/speaker/182855cb689e1c958_132355bea091611a2_1434576812_man.png', 'Descripción del speaker', 'Las tecnologías del futuro', '', 1);
/*!40000 ALTER TABLE `speaker` ENABLE KEYS */;


-- Volcando estructura para tabla manager.survey_question
DROP TABLE IF EXISTS `survey_question`;
CREATE TABLE IF NOT EXISTS `survey_question` (
  `question_id` int(11) NOT NULL AUTO_INCREMENT,
  `event_id` int(11) NOT NULL,
  `question` text NOT NULL,
  `active` int(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`question_id`),
  KEY `FK1_QUESTION_EVENT` (`event_id`),
  CONSTRAINT `FK1_QUESTION_EVENT` FOREIGN KEY (`event_id`) REFERENCES `event` (`event_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8;

-- Volcando datos para la tabla manager.survey_question: ~1 rows (aproximadamente)
DELETE FROM `survey_question`;
/*!40000 ALTER TABLE `survey_question` DISABLE KEYS */;
INSERT INTO `survey_question` (`question_id`, `event_id`, `question`, `active`) VALUES
	(14, 28, '¿Cómo calificaría este evento?', 1);
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
) ENGINE=InnoDB AUTO_INCREMENT=46 DEFAULT CHARSET=utf8;

-- Volcando datos para la tabla manager.user: ~2 rows (aproximadamente)
DELETE FROM `user`;
/*!40000 ALTER TABLE `user` DISABLE KEYS */;
INSERT INTO `user` (`user_id`, `first_name`, `last_name`, `email`, `password`, `photo_path`, `type`, `client_id`) VALUES
	(42, 'Marion', 'Carambula', 'marion.carambula@synergy-gb.com', 'e10adc3949ba59abbe56e057f20f883e', NULL, 'Super Usuario', NULL),
	(44, 'Juan José', 'García', 'juangarcia@synergy-gb.com', 'e10adc3949ba59abbe56e057f20f883e', 'files/user/55cb5f3694850_55a1eba091399_1434575257_profle.png', 'Administrador', 18),
	(45, 'Jessica Daiana', 'Mendoza', 'jessica.mendoza@synergy-gb.com', 'e10adc3949ba59abbe56e057f20f883e', NULL, 'Supervisor', 18);
/*!40000 ALTER TABLE `user` ENABLE KEYS */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
