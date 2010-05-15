-- phpMyAdmin SQL Dump
-- version 2.11.6
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Erstellungszeit: 13. Mai 2010 um 18:01
-- Server Version: 5.0.51
-- PHP-Version: 4.4.8

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--
--
-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `adm_klobs_training`
--

CREATE TABLE IF NOT EXISTS `adm_klobs_training` (
  `tra_id` int(11) NOT NULL auto_increment,
  `locationId` int(11) NOT NULL,
  `location` tinytext NOT NULL,
  `date` date NOT NULL,
  `timestamp` int(11) NOT NULL,
  `year` int(11) NOT NULL,
  `mon` int(11) NOT NULL,
  `mday` int(11) NOT NULL,
  `wday` int(11) NOT NULL,
  `usr_id` int(11) NOT NULL,
  `typ` int(11) NOT NULL,
  `subtyp` int(11) NOT NULL,
  `trainerid` int(11) NOT NULL,
  `starttime` tinytext NOT NULL,
  `duration` int(11) NOT NULL,
  `starttimeint` int(11) NOT NULL,
  `deleted` tinyint(1) NOT NULL default '0' COMMENT 'marks a deleted entry',
  `changedate` timestamp NOT NULL default CURRENT_TIMESTAMP COMMENT 'timestamp of last change',
  `changeby` int(11) NOT NULL COMMENT 'id of user of last change',
  `public` tinyint(1) NOT NULL COMMENT 'makes an success entry public',
  `comment` text NOT NULL COMMENT 'description of a success entry',
  PRIMARY KEY  (`tra_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;
