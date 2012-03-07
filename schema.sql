-- phpMyAdmin SQL Dump
-- version 3.3.9
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Feb 06, 2012 at 06:08 PM
-- Server version: 5.5.8
-- PHP Version: 5.3.5

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `time`
--

-- --------------------------------------------------------

--
-- Table structure for table `billable_rates`
--

CREATE TABLE IF NOT EXISTS `billable_rates` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `workspace` varchar(255) NOT NULL COMMENT 'company name',
  `type` tinyint(1) NOT NULL COMMENT 'hourly rate, fixed sum or not billable',
  `rate` decimal(10,2) NOT NULL COMMENT 'hourly rate, fixed sum or empty',
  `currency` varchar(5) NOT NULL COMMENT 'default currency symbol',
  `timeid` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `timeid` (`timeid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=17 ;

-- --------------------------------------------------------

--
-- Table structure for table `billing`
--

CREATE TABLE IF NOT EXISTS `billing` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `billable_rates` int(11) NOT NULL,
  `time` varchar(25) NOT NULL COMMENT 'in hours',
  `amount` decimal(10,2) NOT NULL,
  `datetime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='amount to generate invoices' AUTO_INCREMENT=12 ;

-- --------------------------------------------------------

--
-- Table structure for table `time`
--

CREATE TABLE IF NOT EXISTS `time` (
  `tid` int(11) NOT NULL AUTO_INCREMENT,
  `clockedIn` int(11) NOT NULL,
  `clockedOut` int(11) NOT NULL,
  `created` datetime DEFAULT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`tid`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=22 ;
