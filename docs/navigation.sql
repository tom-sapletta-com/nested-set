-- phpMyAdmin SQL Dump
-- version 3.4.11.1deb2+deb7u2
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Erstellungszeit: 16. Mrz 2016 um 16:05
-- Server Version: 5.5.47
-- PHP-Version: 5.4.45-0+deb7u2

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `navigation`
--

DROP TABLE IF EXISTS `navigation`;
CREATE TABLE IF NOT EXISTS `navigation` (
  `id` int(12) NOT NULL AUTO_INCREMENT,
  `root_id` int(12) DEFAULT NULL,
  `parent_id` int(12) DEFAULT NULL,
  `name` varchar(50) NOT NULL,
  `lft` int(12) NOT NULL,
  `rgt` int(12) NOT NULL,
  `level` int(12) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `ltf` (`lft`,`rgt`),
  KEY `parent_id` (`parent_id`),
  KEY `root_id` (`root_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=12 ;

--
-- Daten für Tabelle `navigation`
--

INSERT INTO `navigation` (`id`, `root_id`, `parent_id`, `name`, `lft`, `rgt`, `level`) VALUES
(1, NULL, NULL, 'Base', 1, 22, 1),
(2, 1, 1, 'A', 2, 7, 2),
(3, 2, 1, 'B', 8, 13, 2),
(4, 3, 1, 'C', 14, 21, 2),
(5, 1, 2, 'A1', 3, 6, 3),
(6, 2, 3, 'B1', 9, 10, 3),
(7, 2, 3, 'B2', 11, 12, 3),
(8, 3, 4, 'C1', 15, 20, 3),
(9, 1, 5, 'A1I', 4, 5, 4),
(10, 3, 8, 'C1I', 16, 17, 4),
(11, 3, 8, 'C1II', 18, 19, 4);