SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

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
  PRIMARY KEY (`navitem_id`)
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
  PRIMARY KEY (`page_id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;

--
-- Daten für Tabelle `pages`
--

INSERT INTO `pages` (`page_id`, `title`, `slug`, `description`, `keywords`, `is_active`, `language_id`, `visibility`) VALUES
(1, 'Startseite', 'startseite', NULL, NULL, 1, 1, 'restricted'),
(2, 'Über uns', 'uber-uns', NULL, NULL, 1, 1, 'visible'),
(3, 'Beispiele', 'beispiele', NULL, NULL, 1, 1, 'visible'),
(4, 'Küche', 'kueche', NULL, NULL, 1, 1, 'visible'),
(5, 'Bad', 'bad', NULL, NULL, 1, 1, 'visible'),
(6, 'Garage', 'garage', NULL, NULL, 1, 1, 'visible'),
(7, 'Impressum', 'impressum', NULL, NULL, 1, 1, 'visible');

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
(1, 'Website title...', 'Website description...', 'Key, words, ...', 'Au Thor...', 2, 1, 2);

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
(1, ' Neoflow Backend Theme', 'neoflow-backend', 'backend'),
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
(1, 'admin', sha1('admin'), 'John', 'Doe', 'john.doe@neoflow.ch', NULL);

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