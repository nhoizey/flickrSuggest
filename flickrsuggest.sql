# Sequel Pro dump
# Version 1630
# http://code.google.com/p/sequel-pro
#
# Host: localhost (MySQL 5.0.41)
# Database: flickrsuggest
# Generation Time: 2010-03-16 22:12:50 +0100
# ************************************************************

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


# Dump of table favorites
# ------------------------------------------------------------

DROP TABLE IF EXISTS `favorites`;

CREATE TABLE `favorites` (
  `user_nsid` varchar(100) NOT NULL default '',
  `photo_id` varchar(20) NOT NULL default '',
  `date_faved` int(11) NOT NULL default '0',
  `nb` int(11) NOT NULL default '1',
  PRIMARY KEY  (`user_nsid`,`photo_id`),
  KEY `photo_id` (`photo_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



# Dump of table ignored
# ------------------------------------------------------------

DROP TABLE IF EXISTS `ignored`;

CREATE TABLE `ignored` (
  `photo_id` varchar(20) NOT NULL default '',
  PRIMARY KEY  (`photo_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



# Dump of table photos
# ------------------------------------------------------------

DROP TABLE IF EXISTS `photos`;

CREATE TABLE `photos` (
  `photo_id` varchar(20) NOT NULL default '',
  `date_updated` int(11) NOT NULL default '0',
  `date_faved` int(11) NOT NULL default '0',
  PRIMARY KEY  (`photo_id`),
  KEY `date_faved` (`date_faved`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



# Dump of table users
# ------------------------------------------------------------

DROP TABLE IF EXISTS `users`;

CREATE TABLE `users` (
  `user_nsid` varchar(100) NOT NULL default '',
  `date_updated` int(11) NOT NULL default '0',
  PRIMARY KEY  (`user_nsid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;






/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
