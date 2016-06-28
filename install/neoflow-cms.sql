-- phpMyAdmin SQL Dump
-- version 4.5.2
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Erstellungszeit: 28. Jun 2016 um 15:06
-- Server-Version: 5.7.9
-- PHP-Version: 5.6.16

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Datenbank: `neoflow-cms`
--

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `languages`
--

DROP TABLE IF EXISTS `languages`;
CREATE TABLE IF NOT EXISTS `languages` (
  `language_id` int(11) NOT NULL AUTO_INCREMENT,
  `is_active` tinyint(1) NOT NULL DEFAULT '0',
  `code` varchar(2) CHARACTER SET utf8 NOT NULL,
  `title` varchar(50) COLLATE utf8_bin DEFAULT NULL,
  `flag_code` varchar(2) COLLATE utf8_bin DEFAULT NULL,
  PRIMARY KEY (`language_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Daten für Tabelle `languages`
--

INSERT INTO `languages` (`language_id`, `is_active`, `code`, `title`, `flag_code`) VALUES
(1, 1, 'de', 'German', 'de'),
(2, 1, 'en', 'English', 'gb'),
(3, 0, 'fr', 'French', 'fr');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `modules`
--

DROP TABLE IF EXISTS `modules`;
CREATE TABLE IF NOT EXISTS `modules` (
  `module_id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(50) NOT NULL,
  `folder` varchar(50) NOT NULL,
  PRIMARY KEY (`module_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

--
-- Daten für Tabelle `modules`
--

INSERT INTO `modules` (`module_id`, `title`, `folder`) VALUES
(1, 'Hello World', 'hello-world');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `navigations`
--

DROP TABLE IF EXISTS `navigations`;
CREATE TABLE IF NOT EXISTS `navigations` (
  `navigation_id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(50) CHARACTER SET utf8 NOT NULL,
  `description` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  PRIMARY KEY (`navigation_id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Daten für Tabelle `navigations`
--

INSERT INTO `navigations` (`navigation_id`, `title`, `description`) VALUES
(1, 'Default navigation', 'All pages are added to this navigation. You cannot edit or delete the default navigation.'),
(3, '8i67867', NULL),
(4, 'Hauptnavigation', NULL),
(5, 'fasdfasd', NULL),
(6, '123123', '123123123'),
(8, 'asd', ''),
(9, 'asd8', ''),
(10, '5', NULL);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `navitems`
--

DROP TABLE IF EXISTS `navitems`;
CREATE TABLE IF NOT EXISTS `navitems` (
  `navitem_id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(150) COLLATE utf8_bin DEFAULT NULL,
  `page_id` int(11) DEFAULT NULL,
  `parent_navitem_id` int(11) DEFAULT NULL,
  `navigation_id` int(11) DEFAULT NULL,
  `language_id` int(11) DEFAULT NULL,
  `position` int(11) DEFAULT NULL,
  PRIMARY KEY (`navitem_id`)
) ENGINE=InnoDB AUTO_INCREMENT=88 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Daten für Tabelle `navitems`
--

INSERT INTO `navitems` (`navitem_id`, `title`, `page_id`, `parent_navitem_id`, `navigation_id`, `language_id`, `position`) VALUES
(72, 'Startseite', 72, NULL, 1, 1, 1),
(73, 'Über uns', 73, NULL, 1, 1, 3),
(74, 'Beispiele', 74, NULL, 1, 1, 2),
(75, 'Küche', 75, 74, 1, 1, 1),
(76, 'Bad', 76, 74, 1, 1, 2),
(77, 'Garage', 77, 74, 1, 1, 3),
(78, 'Impressum', 78, NULL, 1, 1, 4),
(87, 'Test', 87, NULL, 1, 1, 5);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `pages`
--

DROP TABLE IF EXISTS `pages`;
CREATE TABLE IF NOT EXISTS `pages` (
  `page_id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(100) NOT NULL,
  `slug` varchar(100) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `keywords` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `language_id` int(11) DEFAULT NULL,
  `visibility` enum('visible','restricted','hidden') DEFAULT 'visible',
  PRIMARY KEY (`page_id`)
) ENGINE=InnoDB AUTO_INCREMENT=88 DEFAULT CHARSET=utf8;

--
-- Daten für Tabelle `pages`
--

INSERT INTO `pages` (`page_id`, `title`, `slug`, `description`, `keywords`, `is_active`, `language_id`, `visibility`) VALUES
(72, 'Küche5687', 'startseite', NULL, NULL, 1, 1, 'restricted'),
(73, 'Über uns', 'uber-uns', NULL, NULL, 1, 1, 'visible'),
(74, 'Beispiele', 'beispiele', NULL, NULL, 1, 1, 'visible'),
(75, 'Küche2', 'kueche', NULL, NULL, 1, 1, 'visible'),
(76, 'Bad', 'bad', NULL, NULL, 1, 1, 'visible'),
(77, 'Garage', 'garage', NULL, NULL, 1, 1, 'visible'),
(78, 'Impressum', 'impressum', NULL, NULL, 1, 1, 'visible'),
(87, 'Test', 'test', NULL, NULL, 1, 1, 'visible');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `roles`
--

DROP TABLE IF EXISTS `roles`;
CREATE TABLE IF NOT EXISTS `roles` (
  `role_id` int(11) NOT NULL,
  `role` varchar(50) NOT NULL,
  `description` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`role_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `sections`
--

DROP TABLE IF EXISTS `sections`;
CREATE TABLE IF NOT EXISTS `sections` (
  `section_id` int(11) NOT NULL AUTO_INCREMENT,
  `page_id` int(11) NOT NULL,
  `module_id` int(11) NOT NULL,
  `position` int(11) NOT NULL,
  `block` int(11) NOT NULL,
  PRIMARY KEY (`section_id`),
  KEY `fk_page_id_idx` (`page_id`),
  KEY `fk_module_id_idx` (`module_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `settings`
--

DROP TABLE IF EXISTS `settings`;
CREATE TABLE IF NOT EXISTS `settings` (
  `setting_id` int(11) NOT NULL AUTO_INCREMENT,
  `website_title` varchar(50) DEFAULT NULL,
  `website_description` varchar(150) DEFAULT NULL,
  `keywords` varchar(255) DEFAULT NULL,
  `author` varchar(50) DEFAULT NULL,
  `theme_id` int(11) NOT NULL,
  `backend_theme_id` int(11) NOT NULL,
  `language_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`setting_id`),
  KEY `fk_theme_id_idx` (`theme_id`),
  KEY `fk_backend_theme_id_idx` (`backend_theme_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

--
-- Daten für Tabelle `settings`
--

INSERT INTO `settings` (`setting_id`, `website_title`, `website_description`, `keywords`, `author`, `theme_id`, `backend_theme_id`, `language_id`) VALUES
(1, 'Dev-page of Lahaina CMS', 'Hello World :) Website description... 7675', 'Keyword, KEYWORDS, bla, Laaina567', 'Jonathan Nessier', 2, 1, 2);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `themes`
--

DROP TABLE IF EXISTS `themes`;
CREATE TABLE IF NOT EXISTS `themes` (
  `theme_id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(50) NOT NULL,
  `folder` varchar(50) NOT NULL,
  `type` enum('frontend','backend') NOT NULL DEFAULT 'frontend',
  PRIMARY KEY (`theme_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COMMENT='	';

--
-- Daten für Tabelle `themes`
--

INSERT INTO `themes` (`theme_id`, `title`, `folder`, `type`) VALUES
(1, ' Lahaina Backend Theme', 'lahaina-backend', 'backend'),
(2, 'Cloudy', 'cloudy', 'frontend');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `lastname` varchar(50) DEFAULT NULL,
  `firstname` varchar(50) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `role_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`user_id`),
  KEY `fk_role_id_idx` (`role_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

--
-- Daten für Tabelle `users`
--

INSERT INTO `users` (`user_id`, `username`, `password`, `lastname`, `firstname`, `email`, `role_id`) VALUES
(1, 'admin', '7110eda4d09e062aa5e4a390b0a572ac0d2c0220', 'John', 'Doe', 'john.doe@neoflow.ch', NULL);

--
-- Constraints der exportierten Tabellen
--

--
-- Constraints der Tabelle `sections`
--
ALTER TABLE `sections`
  ADD CONSTRAINT `fk_module_id` FOREIGN KEY (`module_id`) REFERENCES `modules` (`module_id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_page_id` FOREIGN KEY (`page_id`) REFERENCES `pages` (`page_id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints der Tabelle `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `fk_role_id` FOREIGN KEY (`role_id`) REFERENCES `roles` (`role_id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
