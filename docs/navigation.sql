-- phpMyAdmin SQL Dump
-- version 3.4.11.1deb2+deb7u2
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Erstellungszeit: 24. Feb 2016 um 16:57
-- Server Version: 5.5.47
-- PHP-Version: 5.4.45-0+deb7u2

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `navigation`
--

CREATE TABLE IF NOT EXISTS `navigation` (
  `id` int(12) NOT NULL AUTO_INCREMENT,
  `root_id` int(12) NOT NULL,
  `parent_id` int(12) DEFAULT NULL,
  `name` varchar(50) NOT NULL,
  `lft` int(12) NOT NULL,
  `rgt` int(12) NOT NULL,
  `level` int(12) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `ltf` (`lft`,`rgt`),
  KEY `parent_id` (`parent_id`),
  KEY `root_id` (`root_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=6 ;

--
-- Daten für Tabelle `navigation`
--

INSERT INTO `navigation` (`id`, `root_id`, `parent_id`, `name`, `lft`, `rgt`, `level`) VALUES
(1, 1, NULL, 'Säugetiere', 1, 10, 1),
(2, 1, 1, 'Primaten', 2, 7, 2),
(3, 1, 1, 'Nagetiere', 8, 9, 2),
(4, 1, 2, 'Halbaffen', 3, 4, 3),
(5, 1, 2, 'Affen', 5, 6, 3);
