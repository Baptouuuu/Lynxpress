# ************************************************************
# Sequel Pro SQL dump
# Version 3408
#
# http://www.sequelpro.com/
# http://code.google.com/p/sequel-pro/
#
# Host: localhost (MySQL 5.5.20)
# Database: lynxpress
# Generation Time: 2012-07-07 17:36:06 +0000
# ************************************************************


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


# Dump of table dev_activity
# ------------------------------------------------------------

DROP TABLE IF EXISTS `dev_activity`;

CREATE TABLE `dev_activity` (
  `user_id` int(11) unsigned NOT NULL,
  `_data` tinytext NOT NULL,
  `_date` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  KEY `idx_user_id` (`user_id`),
  CONSTRAINT `dev_activity_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `dev_user` (`_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table dev_category
# ------------------------------------------------------------

DROP TABLE IF EXISTS `dev_category`;

CREATE TABLE `dev_category` (
  `_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `_name` tinytext NOT NULL,
  `_type` tinytext NOT NULL,
  PRIMARY KEY (`_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

LOCK TABLES `dev_category` WRITE;
/*!40000 ALTER TABLE `dev_category` DISABLE KEYS */;

INSERT INTO `dev_category` (`_id`, `_name`, `_type`)
VALUES
	(2,'Uncategorized','album'),
	(3,'Uncategorized','post'),
	(4,'Uncategorized','video');

/*!40000 ALTER TABLE `dev_category` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table dev_comment
# ------------------------------------------------------------

DROP TABLE IF EXISTS `dev_comment`;

CREATE TABLE `dev_comment` (
  `_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `_name` tinytext NOT NULL,
  `_email` varchar(128) NOT NULL DEFAULT '',
  `_content` text NOT NULL,
  `_rel_id` int(10) unsigned NOT NULL,
  `_rel_type` varchar(5) NOT NULL DEFAULT 'post',
  `_status` varchar(8) NOT NULL DEFAULT 'pending',
  `_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table dev_link
# ------------------------------------------------------------

DROP TABLE IF EXISTS `dev_link`;

CREATE TABLE `dev_link` (
  `_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `_name` tinytext NOT NULL,
  `_link` tinytext NOT NULL,
  `_rss` tinytext,
  `_notes` text,
  `_priority` int(1) NOT NULL DEFAULT '3',
  PRIMARY KEY (`_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table dev_media
# ------------------------------------------------------------

DROP TABLE IF EXISTS `dev_media`;

CREATE TABLE `dev_media` (
  `_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `_name` tinytext NOT NULL,
  `_type` varchar(10) NOT NULL DEFAULT '',
  `_user` int(11) unsigned NOT NULL,
  `_status` varchar(7) NOT NULL DEFAULT 'draft',
  `_category` tinytext,
  `_allow_comment` varchar(6) NOT NULL DEFAULT 'closed',
  `_permalink` tinytext NOT NULL,
  `_embed_code` text,
  `_description` text,
  `_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `_attachment` int(11) unsigned DEFAULT NULL,
  `_attach_type` tinytext,
  `_extra` text COMMENT 'data formatted in json',
  PRIMARY KEY (`_id`),
  KEY `idx_user_id` (`_user`),
  CONSTRAINT `dev_media_ibfk_1` FOREIGN KEY (`_user`) REFERENCES `dev_user` (`_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table dev_post
# ------------------------------------------------------------

DROP TABLE IF EXISTS `dev_post`;

CREATE TABLE `dev_post` (
  `_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `_title` tinytext NOT NULL,
  `_content` text NOT NULL,
  `_allow_comment` varchar(6) NOT NULL DEFAULT 'closed',
  `_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `_user` int(11) unsigned NOT NULL,
  `_status` varchar(7) NOT NULL DEFAULT 'draft',
  `_category` tinytext NOT NULL,
  `_tags` tinytext NOT NULL,
  `_permalink` tinytext NOT NULL,
  `_updated` tinyint(1) NOT NULL DEFAULT '0',
  `_update_user` int(11) unsigned DEFAULT NULL,
  `_extra` text COMMENT 'data formatted in json',
  PRIMARY KEY (`_id`),
  KEY `idx_user_id` (`_user`),
  CONSTRAINT `dev_post_ibfk_1` FOREIGN KEY (`_user`) REFERENCES `dev_user` (`_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table dev_session
# ------------------------------------------------------------

DROP TABLE IF EXISTS `dev_session`;

CREATE TABLE `dev_session` (
  `_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `_user` int(11) unsigned NOT NULL,
  `_token` text NOT NULL,
  `_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `_ip` text NOT NULL,
  PRIMARY KEY (`_id`),
  KEY `idx_user_id` (`_user`),
  CONSTRAINT `dev_session_ibfk_1` FOREIGN KEY (`_user`) REFERENCES `dev_user` (`_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table dev_setting
# ------------------------------------------------------------

DROP TABLE IF EXISTS `dev_setting`;

CREATE TABLE `dev_setting` (
  `_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `_name` text,
  `_type` tinytext NOT NULL,
  `_data` text NOT NULL COMMENT 'data formatted in json',
  `_key` tinytext,
  PRIMARY KEY (`_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

LOCK TABLES `dev_setting` WRITE;
/*!40000 ALTER TABLE `dev_setting` DISABLE KEYS */;

INSERT INTO `dev_setting` (`_id`, `_name`, `_type`, `_data`, `_key`)
VALUES
	(1,'admin','role','{\"dashboard\":true,\"post\":true,\"media\":true,\"album\":true,\"comment\":true,\"setting\":true,\"delete\":true}','role_admin'),
	(4,'Homepage','homepage','{\"type\":\"post\",\"view\":\"all\"}','homepage'),
	(12,'Main Template','template','{\"name\": \"Main Template\",	\"author\": \"Baptiste Langlade\",\"url\": \"http://lynxpress.org\",\"infos\": {\"namespace\": \"main\",\"date\": \"2012-05-08\",\"compatibility\": [\"2.0\"]},\"files\": {\"js\": [],	\"css\": [],\"core\": []}}','template_main'),
	(13,'Main template','current_template','main','current_template'),
	(14,'Social Buttons','social_buttons','[]','social_buttons');

/*!40000 ALTER TABLE `dev_setting` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table dev_user
# ------------------------------------------------------------

DROP TABLE IF EXISTS `dev_user`;

CREATE TABLE `dev_user` (
  `_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `_username` varchar(20) NOT NULL DEFAULT '',
  `_nickname` varchar(20) DEFAULT '',
  `_firstname` tinytext,
  `_lastname` tinytext,
  `_publicname` tinytext NOT NULL,
  `_password` tinytext NOT NULL,
  `_email` varchar(128) NOT NULL DEFAULT '',
  `_website` tinytext,
  `_msn` tinytext,
  `_twitter` tinytext,
  `_facebook` tinytext,
  `_google` tinytext,
  `_bio` text,
  `_role` varchar(20) NOT NULL DEFAULT '',
  `_active` int(1) NOT NULL DEFAULT '1' COMMENT 'set to 0 when a user is deleted',
  PRIMARY KEY (`_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

LOCK TABLES `dev_user` WRITE;
/*!40000 ALTER TABLE `dev_user` DISABLE KEYS */;

INSERT INTO `dev_user` (`_id`, `_username`, `_nickname`, `_firstname`, `_lastname`, `_publicname`, `_password`, `_email`, `_website`, `_msn`, `_twitter`, `_facebook`, `_google`, `_bio`, `_role`, `_active`)
VALUES
	(1,'Admin',NULL,NULL,NULL,'Admin','ee8cffb8358e97d8c1d70242897748b9','admin@admin.fr',NULL,NULL,NULL,NULL,NULL,NULL,'admin',1);

/*!40000 ALTER TABLE `dev_user` ENABLE KEYS */;
UNLOCK TABLES;



/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
