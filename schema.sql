SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=0 ;

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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='amount to generate invoices' AUTO_INCREMENT=0 ;

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
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=0 ;
