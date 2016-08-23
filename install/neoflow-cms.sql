-- phpMyAdmin SQL Dump
-- version 4.5.5.1
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Erstellungszeit: 19. Aug 2016 um 14:04
-- Server-Version: 5.7.11
-- PHP-Version: 7.0.4

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

CREATE TABLE `languages` (
  `language_id` int(11) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '0',
  `code` varchar(2) CHARACTER SET utf8 NOT NULL,
  `title` varchar(50) COLLATE utf8_bin DEFAULT NULL,
  `flag_code` varchar(2) COLLATE utf8_bin DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

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

CREATE TABLE `modules` (
  `module_id` int(11) NOT NULL,
  `title` varchar(50) NOT NULL,
  `folder` varchar(50) NOT NULL,
  `route` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Daten für Tabelle `modules`
--

INSERT INTO `modules` (`module_id`, `title`, `folder`, `route`) VALUES
(1, 'Hello World', 'hello-world', 'mod_hello_world_index');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `navigations`
--

CREATE TABLE `navigations` (
  `navigation_id` int(11) NOT NULL,
  `title` varchar(50) CHARACTER SET utf8 NOT NULL,
  `description` varchar(255) COLLATE utf8_bin DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

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

CREATE TABLE `navitems` (
  `navitem_id` int(11) NOT NULL,
  `title` varchar(150) COLLATE utf8_bin DEFAULT NULL,
  `page_id` int(11) DEFAULT NULL,
  `parent_navitem_id` int(11) DEFAULT NULL,
  `navigation_id` int(11) DEFAULT NULL,
  `language_id` int(11) DEFAULT NULL,
  `position` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Daten für Tabelle `navitems`
--

INSERT INTO `navitems` (`navitem_id`, `title`, `page_id`, `parent_navitem_id`, `navigation_id`, `language_id`, `position`) VALUES
(1, 'Startseite', 1, NULL, 1, 1, 1),
(2, 'Über uns', 2, 3, 1, 1, 4),
(3, 'Beispiele', 3, NULL, 1, 1, 2),
(4, 'Küche', 4, 3, 1, 1, 1),
(5, 'Bad', 5, 3, 1, 1, 2),
(6, 'Garage', 6, 3, 1, 1, 3),
(7, 'Impressum', 7, NULL, 1, 1, 3);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `pages`
--

CREATE TABLE `pages` (
  `page_id` int(11) NOT NULL,
  `title` varchar(100) NOT NULL,
  `slug` varchar(100) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `keywords` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `language_id` int(11) DEFAULT NULL,
  `visibility` enum('visible','restricted','hidden') DEFAULT 'visible'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

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

CREATE TABLE `permissions` (
  `permission_id` int(11) NOT NULL,
  `permission_key` varchar(50) CHARACTER SET utf8 NOT NULL,
  `title` varchar(50) COLLATE utf8_bin DEFAULT NULL,
  `description` text CHARACTER SET utf8
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

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

CREATE TABLE `roles` (
  `role_id` int(11) NOT NULL,
  `title` varchar(20) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `description` varchar(150) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Daten für Tabelle `roles`
--

INSERT INTO `roles` (`role_id`, `title`, `description`) VALUES
(1, 'Administrator', '...'),
(4, 'Süperüser', 'No description');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `roles_permissions`
--

CREATE TABLE `roles_permissions` (
  `role_permission_id` int(11) NOT NULL,
  `role_id` int(11) NOT NULL,
  `permission_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Daten für Tabelle `roles_permissions`
--

INSERT INTO `roles_permissions` (`role_permission_id`, `role_id`, `permission_id`) VALUES
(1, 1, 1),
(2, 1, 2),
(3, 1, 3),
(4, 1, 4),
(5, 1, 5),
(6, 1, 6),
(7, 1, 7),
(8, 1, 8),
(9, 1, 9),
(10, 4, 2);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `sections`
--

CREATE TABLE `sections` (
  `section_id` int(11) NOT NULL,
  `page_id` int(11) NOT NULL,
  `module_id` int(11) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `position` int(11) NOT NULL,
  `block` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `settings`
--

CREATE TABLE `settings` (
  `setting_id` int(11) NOT NULL,
  `website_title` varchar(50) DEFAULT NULL,
  `website_description` varchar(150) DEFAULT NULL,
  `keywords` varchar(255) DEFAULT NULL,
  `author` varchar(50) DEFAULT NULL,
  `theme_id` int(11) NOT NULL,
  `backend_theme_id` int(11) NOT NULL,
  `language_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Daten für Tabelle `settings`
--

INSERT INTO `settings` (`setting_id`, `website_title`, `website_description`, `keywords`, `author`, `theme_id`, `backend_theme_id`, `language_id`) VALUES
(1, 'Website title...', 'Website description...', 'Key, words, ...', 'Au Thor...', 2, 1, 1);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `themes`
--

CREATE TABLE `themes` (
  `theme_id` int(11) NOT NULL,
  `title` varchar(50) NOT NULL,
  `folder` varchar(50) NOT NULL,
  `type` enum('frontend','backend') NOT NULL DEFAULT 'frontend'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='	';

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

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `email` varchar(100) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `password` varchar(100) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `lastname` varchar(50) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `firstname` varchar(50) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `reset_key` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `reseted_when` int(11) DEFAULT NULL,
  `role_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Daten für Tabelle `users`
--

INSERT INTO `users` (`user_id`, `email`, `password`, `lastname`, `firstname`, `reset_key`, `reseted_when`, `role_id`) VALUES
(1, 'john.doe@neoflow.ch', sha('123456'), 'Doe', 'John', NULL, NULL, 1),
(2, 'jonathan.nessier@outlook.com', sha('123456'), 'Nessier', 'Jonathan', NULL, NULL, 4);

--
-- Indizes der exportierten Tabellen
--

--
-- Indizes für die Tabelle `languages`
--
ALTER TABLE `languages`
  ADD PRIMARY KEY (`language_id`);

--
-- Indizes für die Tabelle `modules`
--
ALTER TABLE `modules`
  ADD PRIMARY KEY (`module_id`);

--
-- Indizes für die Tabelle `navigations`
--
ALTER TABLE `navigations`
  ADD PRIMARY KEY (`navigation_id`);

--
-- Indizes für die Tabelle `navitems`
--
ALTER TABLE `navitems`
  ADD PRIMARY KEY (`navitem_id`),
  ADD KEY `fk_navitems_page_id_idx` (`page_id`),
  ADD KEY `fk_navitems_navitem_id_idx` (`parent_navitem_id`),
  ADD KEY `fk_navitems_navigation_id_idx` (`navigation_id`),
  ADD KEY `fk_navitems_language_id_idx` (`language_id`);

--
-- Indizes für die Tabelle `pages`
--
ALTER TABLE `pages`
  ADD PRIMARY KEY (`page_id`),
  ADD KEY `fk_pages_language_id_idx` (`language_id`);

--
-- Indizes für die Tabelle `permissions`
--
ALTER TABLE `permissions`
  ADD PRIMARY KEY (`permission_id`),
  ADD UNIQUE KEY `tag` (`permission_key`),
  ADD UNIQUE KEY `title_UNIQUE` (`title`);

--
-- Indizes für die Tabelle `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`role_id`);

--
-- Indizes für die Tabelle `roles_permissions`
--
ALTER TABLE `roles_permissions`
  ADD PRIMARY KEY (`role_permission_id`),
  ADD KEY `fk_roles_permissions_role_id_idx` (`role_id`),
  ADD KEY `fk_roles_permissions_permission_id_idx` (`permission_id`);

--
-- Indizes für die Tabelle `sections`
--
ALTER TABLE `sections`
  ADD PRIMARY KEY (`section_id`),
  ADD KEY `fk_page_id_idx` (`page_id`),
  ADD KEY `fk_module_id_idx` (`module_id`);

--
-- Indizes für die Tabelle `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`setting_id`),
  ADD KEY `fk_theme_id_idx` (`theme_id`),
  ADD KEY `fk_backend_theme_id_idx` (`backend_theme_id`);

--
-- Indizes für die Tabelle `themes`
--
ALTER TABLE `themes`
  ADD PRIMARY KEY (`theme_id`);

--
-- Indizes für die Tabelle `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD KEY `fk_user_role_id_idx` (`role_id`);

--
-- AUTO_INCREMENT für exportierte Tabellen
--

--
-- AUTO_INCREMENT für Tabelle `languages`
--
ALTER TABLE `languages`
  MODIFY `language_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT für Tabelle `modules`
--
ALTER TABLE `modules`
  MODIFY `module_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT für Tabelle `navigations`
--
ALTER TABLE `navigations`
  MODIFY `navigation_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT für Tabelle `navitems`
--
ALTER TABLE `navitems`
  MODIFY `navitem_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;
--
-- AUTO_INCREMENT für Tabelle `pages`
--
ALTER TABLE `pages`
  MODIFY `page_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;
--
-- AUTO_INCREMENT für Tabelle `permissions`
--
ALTER TABLE `permissions`
  MODIFY `permission_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;
--
-- AUTO_INCREMENT für Tabelle `roles`
--
ALTER TABLE `roles`
  MODIFY `role_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
--
-- AUTO_INCREMENT für Tabelle `roles_permissions`
--
ALTER TABLE `roles_permissions`
  MODIFY `role_permission_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;
--
-- AUTO_INCREMENT für Tabelle `sections`
--
ALTER TABLE `sections`
  MODIFY `section_id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT für Tabelle `settings`
--
ALTER TABLE `settings`
  MODIFY `setting_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT für Tabelle `themes`
--
ALTER TABLE `themes`
  MODIFY `theme_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT für Tabelle `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
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
