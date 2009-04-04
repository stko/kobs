-- phpMyAdmin SQL Dump
-- version 3.1.1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Apr 04, 2009 at 12:54 PM
-- Server version: 5.0.26
-- PHP Version: 5.2.6

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `admidio`
--

-- --------------------------------------------------------

--
-- Table structure for table `adm_klobs_training`
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
  PRIMARY KEY  (`tra_id`),
  UNIQUE KEY `locationId` (`locationId`,`timestamp`,`usr_id`,`starttimeint`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=4 ;
