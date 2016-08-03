-- phpMyAdmin SQL Dump
-- version 4.5.2
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Erstellungszeit: 03. Aug 2016 um 15:08
-- Server-Version: 5.7.9
-- PHP-Version: 7.0.0

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
(3, 1, 'fr', 'French', 'fr');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `modules`
--

DROP TABLE IF EXISTS `modules`;
CREATE TABLE IF NOT EXISTS `modules` (
  `module_id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(50) NOT NULL,
  `folder` varchar(50) NOT NULL,
  `route` varchar(50) NOT NULL,
  PRIMARY KEY (`module_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

--
-- Daten für Tabelle `modules`
--

INSERT INTO `modules` (`module_id`, `title`, `folder`, `route`) VALUES
(1, 'Hello World', 'hello-world', 'mod_hello_world_index');

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
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Daten für Tabelle `navigations`
--

INSERT INTO `navigations` (`navigation_id`, `title`, `description`) VALUES
(1, 'Default navigation', 'All pages are added to this navigation. You cannot edit or delete the default navigation.'),
(2, 'Footer navigation', NULL);

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
  PRIMARY KEY (`navitem_id`),
  KEY `fk_navitems_page_id_idx` (`page_id`),
  KEY `fk_navitems_navitem_id_idx` (`parent_navitem_id`),
  KEY `fk_navitems_navigation_id_idx` (`navigation_id`),
  KEY `fk_navitems_language_id_idx` (`language_id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Daten für Tabelle `navitems`
--

INSERT INTO `navitems` (`navitem_id`, `title`, `page_id`, `parent_navitem_id`, `navigation_id`, `language_id`, `position`) VALUES
(1, 'Startseite', 1, NULL, 1, 1, 1),
(2, 'Über uns', 2, NULL, 1, 1, 3),
(3, 'Beispiele', 3, NULL, 1, 1, 2),
(4, 'Küche', 4, 3, 1, 1, 1),
(5, 'Bad', 5, 3, 1, 1, 2),
(6, 'Garage', 6, 3, 1, 1, 3),
(7, 'Impressum', 7, NULL, 1, 1, 4);

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
  PRIMARY KEY (`page_id`),
  KEY `fk_pages_language_id_idx` (`language_id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;

--
-- Daten für Tabelle `pages`
--

INSERT INTO `pages` (`page_id`, `title`, `slug`, `description`, `keywords`, `is_active`, `language_id`, `visibility`) VALUES
(1, 'Startseite', 'startseite', NULL, NULL, 1, 1, 'visible'),
(2, 'Über uns', 'uber-uns', NULL, NULL, 1, 1, 'visible'),
(3, 'Beispiele', 'beispiele', NULL, NULL, 1, 1, 'visible'),
(4, 'Küche', 'kueche', NULL, NULL, 1, 1, 'visible'),
(5, 'Bad', 'bad', NULL, NULL, 1, 1, 'visible'),
(6, 'Garage', 'garage', NULL, NULL, 1, 1, 'visible'),
(7, 'Impressum', 'impressum', NULL, NULL, 1, 1, 'visible');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `permissions`
--

DROP TABLE IF EXISTS `permissions`;
CREATE TABLE IF NOT EXISTS `permissions` (
  `permission_id` int(11) NOT NULL AUTO_INCREMENT,
  `permission_key` varchar(50) CHARACTER SET utf8 NOT NULL,
  `title` varchar(50) COLLATE utf8_bin DEFAULT NULL,
  `description` text CHARACTER SET utf8,
  PRIMARY KEY (`permission_id`),
  UNIQUE KEY `tag` (`permission_key`),
  UNIQUE KEY `title_UNIQUE` (`title`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Daten für Tabelle `permissions`
--

INSERT INTO `permissions` (`permission_id`, `permission_key`, `title`, `description`) VALUES
(1, 'manage_pages', 'Pages', 'Manage pages and page content'),
(2, 'manage_navigations', 'Navigations', 'Manage navigations'),
(3, 'manage_modules', 'Modules', 'Manage modules'),
(4, 'manage_templates', 'Templates', 'Manage templates'),
(5, 'manage_media', 'Media', 'Manage media data'),
(6, 'manage_settings', 'Settings', 'Update website settings'),
(7, 'manage_users', 'Users', 'Manage user accounts'),
(8, 'manage_roles', 'Roles', 'Manage roles and permissions'),
(9, 'maintenance', 'Maintenance', 'Maintain website and system');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `roles`
--

DROP TABLE IF EXISTS `roles`;
CREATE TABLE IF NOT EXISTS `roles` (
  `role_id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(50) NOT NULL,
  `description` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`role_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

--
-- Daten für Tabelle `roles`
--

INSERT INTO `roles` (`role_id`, `title`, `description`) VALUES
(1, 'Administrator', '...');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `roles_permissions`
--

DROP TABLE IF EXISTS `roles_permissions`;
CREATE TABLE IF NOT EXISTS `roles_permissions` (
  `role_permission_id` int(11) NOT NULL AUTO_INCREMENT,
  `role_id` int(11) NOT NULL,
  `permission_id` int(11) NOT NULL,
  PRIMARY KEY (`role_permission_id`),
  KEY `fk_roles_permissions_role_id_idx` (`role_id`),
  KEY `fk_roles_permissions_permission_id_idx` (`permission_id`)
) ENGINE=InnoDB AUTO_INCREMENT=33 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Daten für Tabelle `roles_permissions`
--

INSERT INTO `roles_permissions` (`role_permission_id`, `role_id`, `permission_id`) VALUES
(24, 1, 1),
(25, 1, 2),
(26, 1, 3),
(27, 1, 4),
(28, 1, 5),
(29, 1, 6),
(30, 1, 7),
(31, 1, 8),
(32, 1, 9);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `sections`
--

DROP TABLE IF EXISTS `sections`;
CREATE TABLE IF NOT EXISTS `sections` (
  `section_id` int(11) NOT NULL AUTO_INCREMENT,
  `page_id` int(11) NOT NULL,
  `module_id` int(11) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
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
(1, 'Website title...', 'Website description...', 'Key, words, ...', 'Au Thor...', 2, 1, 1);

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
(1, 'Neoflow Backend Theme', 'neoflow-backend', 'backend'),
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
  KEY `fk_user_role_id_idx` (`role_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

--
-- Daten für Tabelle `users`
--

INSERT INTO `users` (`user_id`, `username`, `password`, `lastname`, `firstname`, `email`, `role_id`) VALUES
(1, 'admin', sha1('1234'), 'John', 'Doe', 'john.doe@neoflow.ch', NULL);

--
-- Constraints der exportierten Tabellen
--

--
-- Constraints der Tabelle `navitems`
--
ALTER TABLE `navitems`
  ADD CONSTRAINT `fk_navitems_language_id` FOREIGN KEY (`language_id`) REFERENCES `languages` (`language_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_navitems_navigation_id` FOREIGN KEY (`navigation_id`) REFERENCES `navigations` (`navigation_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_navitems_navitem_id` FOREIGN KEY (`parent_navitem_id`) REFERENCES `navitems` (`navitem_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_navitems_page_id` FOREIGN KEY (`page_id`) REFERENCES `pages` (`page_id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints der Tabelle `pages`
--
ALTER TABLE `pages`
  ADD CONSTRAINT `fk_pages_language_id` FOREIGN KEY (`language_id`) REFERENCES `languages` (`language_id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints der Tabelle `roles_permissions`
--
ALTER TABLE `roles_permissions`
  ADD CONSTRAINT `fk_roles_permissions_permission_id` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`permission_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_roles_permissions_role_id` FOREIGN KEY (`role_id`) REFERENCES `roles` (`role_id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints der Tabelle `sections`
--
ALTER TABLE `sections`
  ADD CONSTRAINT `fk_sections_module_id` FOREIGN KEY (`module_id`) REFERENCES `modules` (`module_id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_sections_page_id` FOREIGN KEY (`page_id`) REFERENCES `pages` (`page_id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints der Tabelle `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `fk_users_role_id` FOREIGN KEY (`role_id`) REFERENCES `roles` (`role_id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
