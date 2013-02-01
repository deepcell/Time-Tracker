-- phpMyAdmin SQL Dump
--
-- Host: localhost
-- Generation Time: Feb 01, 2013 at 02:26 PM
-- Server version: 5.5.16
-- PHP Version: 5.3.8

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `time`
--


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
  `status` enum('unpaid','paid','draft') NOT NULL DEFAULT 'unpaid',
  PRIMARY KEY (`id`),
  UNIQUE KEY `timeid` (`timeid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=55 ;



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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='amount to generate invoices' AUTO_INCREMENT=62 ;


--
-- Table structure for table `company`
--
CREATE TABLE IF NOT EXISTS `company` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  `responsible` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `unique_code` varchar(32) COLLATE utf8_unicode_ci NOT NULL COMMENT 'client unique code',
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_code` (`unique_code`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=5 ;


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
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=62 ;


/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;