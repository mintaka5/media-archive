-- MySQL dump 10.13  Distrib 5.1.73, for redhat-linux-gnu (x86_64)
--
-- Host: cwisdb2.cwis.uci.edu    Database: prod-image-archive
-- ------------------------------------------------------
-- Server version	5.0.95

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Not dumping tablespaces as no INFORMATION_SCHEMA.FILES table on this server
--

--
-- Table structure for table `asset_group_cnx`
--

DROP TABLE IF EXISTS `asset_group_cnx`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `asset_group_cnx` (
  `id` int(11) NOT NULL auto_increment,
  `asset_id` varchar(50) NOT NULL,
  `group_id` varchar(50) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `asset_group_cnx_asset_fk` (`asset_id`),
  KEY `asset_group_cnx_group_fk` (`group_id`),
  CONSTRAINT `asset_group_cnx_ibfk_1` FOREIGN KEY (`asset_id`) REFERENCES `assets` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `asset_group_cnx_ibfk_2` FOREIGN KEY (`group_id`) REFERENCES `groups` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=19508 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `asset_group_def`
--

DROP TABLE IF EXISTS `asset_group_def`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `asset_group_def` (
  `id` int(11) NOT NULL auto_increment,
  `cnx_id` int(11) NOT NULL,
  `width` int(11) default NULL,
  `height` int(11) default NULL,
  `is_default` smallint(1) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `cnx_id` (`cnx_id`)
) ENGINE=InnoDB AUTO_INCREMENT=19497 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `asset_metadata`
--

DROP TABLE IF EXISTS `asset_metadata`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `asset_metadata` (
  `id` int(11) NOT NULL auto_increment,
  `metadata_name` varchar(45) NOT NULL,
  `metadata_value` text,
  `asset_id` varchar(50) NOT NULL,
  `is_deleted` smallint(1) NOT NULL default '0',
  `created` datetime default NULL,
  `modified` datetime default NULL,
  PRIMARY KEY  (`id`),
  KEY `metadata_asset_id_fk` (`asset_id`),
  KEY `asset_metadata_id_fk` (`asset_id`),
  CONSTRAINT `asset_metadata_ibfk_1` FOREIGN KEY (`asset_id`) REFERENCES `assets` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=19364 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `asset_outtakes`
--

DROP TABLE IF EXISTS `asset_outtakes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `asset_outtakes` (
  `id` int(11) NOT NULL auto_increment,
  `asset_id` varchar(50) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `asset_outtakes_asset_fk14` (`asset_id`),
  CONSTRAINT `asset_outtakes_ibfk_1` FOREIGN KEY (`asset_id`) REFERENCES `assets` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `asset_published`
--

DROP TABLE IF EXISTS `asset_published`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `asset_published` (
  `id` int(11) NOT NULL auto_increment,
  `asset_id` varchar(50) NOT NULL,
  `pub_id` int(11) NOT NULL,
  `pub_date` datetime NOT NULL,
  `user_id` varchar(50) NOT NULL,
  `created` datetime NOT NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `asset_id` (`asset_id`),
  KEY `asset_published_user_fk12` (`user_id`),
  KEY `asset_published_pub_fk12` (`pub_id`),
  CONSTRAINT `asset_published_ibfk_1` FOREIGN KEY (`asset_id`) REFERENCES `assets` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `asset_published_ibfk_2` FOREIGN KEY (`pub_id`) REFERENCES `publications` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `asset_published_ibfk_3` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `asset_restriction_embargo`
--

DROP TABLE IF EXISTS `asset_restriction_embargo`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `asset_restriction_embargo` (
  `id` int(11) NOT NULL auto_increment,
  `asset_id` varchar(50) NOT NULL,
  `start_date` datetime NOT NULL,
  `created_by` varchar(50) NOT NULL,
  `created` datetime NOT NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `asset_id` (`asset_id`),
  KEY `asset_restrict_user_fk12` (`created_by`),
  CONSTRAINT `asset_restriction_embargo_ibfk_1` FOREIGN KEY (`asset_id`) REFERENCES `assets` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `asset_restriction_embargo_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=36 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `asset_restriction_external`
--

DROP TABLE IF EXISTS `asset_restriction_external`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `asset_restriction_external` (
  `id` int(11) NOT NULL auto_increment,
  `asset_id` varchar(50) NOT NULL,
  `description` text NOT NULL,
  `user_id` varchar(50) NOT NULL,
  `created` datetime NOT NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `asset_id` (`asset_id`),
  KEY `asset_restrict_ext_user_fk12` (`user_id`),
  CONSTRAINT `asset_restriction_external_ibfk_1` FOREIGN KEY (`asset_id`) REFERENCES `assets` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `asset_restriction_external_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `asset_restriction_hippa`
--

DROP TABLE IF EXISTS `asset_restriction_hippa`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `asset_restriction_hippa` (
  `id` int(11) NOT NULL auto_increment,
  `asset_id` varchar(50) NOT NULL,
  `user_id` varchar(50) NOT NULL,
  `created` datetime NOT NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `asset_id` (`asset_id`),
  KEY `asset_restrict_hippa_user_fk12` (`user_id`),
  CONSTRAINT `asset_restriction_hippa_ibfk_1` FOREIGN KEY (`asset_id`) REFERENCES `assets` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `asset_restriction_hippa_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `asset_restriction_internal`
--

DROP TABLE IF EXISTS `asset_restriction_internal`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `asset_restriction_internal` (
  `id` int(11) NOT NULL auto_increment,
  `asset_id` varchar(50) NOT NULL,
  `description` text NOT NULL,
  `user_id` varchar(50) NOT NULL,
  `created` datetime NOT NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `asset_id` (`asset_id`),
  KEY `asset_restrict_int_user_fk12` (`user_id`),
  CONSTRAINT `asset_restriction_internal_ibfk_1` FOREIGN KEY (`asset_id`) REFERENCES `assets` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `asset_restriction_internal_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `asset_restriction_ncaa`
--

DROP TABLE IF EXISTS `asset_restriction_ncaa`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `asset_restriction_ncaa` (
  `id` int(11) NOT NULL auto_increment,
  `asset_id` varchar(50) NOT NULL,
  `user_id` varchar(50) NOT NULL,
  `created` datetime NOT NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `asset_id` (`asset_id`),
  KEY `asset_restrict_ncaa_user_fk12` (`user_id`),
  CONSTRAINT `asset_restriction_ncaa_ibfk_1` FOREIGN KEY (`asset_id`) REFERENCES `assets` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `asset_restriction_ncaa_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `asset_restriction_subject`
--

DROP TABLE IF EXISTS `asset_restriction_subject`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `asset_restriction_subject` (
  `id` int(11) NOT NULL auto_increment,
  `asset_id` varchar(50) NOT NULL,
  `description` text NOT NULL,
  `user_id` varchar(50) NOT NULL,
  `created` datetime NOT NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `asset_id` (`asset_id`),
  KEY `asset_restrict_subj_user_fk12` (`user_id`),
  CONSTRAINT `asset_restriction_subject_ibfk_1` FOREIGN KEY (`asset_id`) REFERENCES `assets` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `asset_restriction_subject_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `asset_selects`
--

DROP TABLE IF EXISTS `asset_selects`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `asset_selects` (
  `id` int(11) NOT NULL auto_increment,
  `asset_id` varchar(50) NOT NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `asset_id` (`asset_id`),
  CONSTRAINT `asset_selects_ibfk_1` FOREIGN KEY (`asset_id`) REFERENCES `assets` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `asset_statuses`
--

DROP TABLE IF EXISTS `asset_statuses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `asset_statuses` (
  `id` int(11) NOT NULL auto_increment,
  `s_name` varchar(45) NOT NULL,
  `title` varchar(45) NOT NULL,
  `is_active` smallint(1) NOT NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `s_name` (`s_name`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `asset_types`
--

DROP TABLE IF EXISTS `asset_types`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `asset_types` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(45) NOT NULL,
  `title` varchar(45) NOT NULL,
  `is_active` smallint(6) NOT NULL default '1',
  `mime_type` varchar(45) NOT NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `name_UNIQUE` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `assets`
--

DROP TABLE IF EXISTS `assets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `assets` (
  `id` varchar(50) NOT NULL,
  `public_id` varchar(50) NOT NULL,
  `type_id` int(11) NOT NULL,
  `filename` varchar(255) NOT NULL,
  `title` varchar(150) default NULL,
  `caption` varchar(100) default NULL,
  `description` varchar(255) default NULL,
  `photographer_id` int(11) NOT NULL,
  `shoot_id` int(11) default NULL,
  `credit` varchar(75) default NULL,
  `location` varchar(255) default NULL,
  `lat` varchar(25) default NULL,
  `lng` varchar(25) default NULL,
  `is_active` smallint(1) NOT NULL default '1',
  `is_deleted` smallint(1) NOT NULL default '0',
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  `modified_by` varchar(50) NOT NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `public_id` (`public_id`),
  KEY `asset_type_id_fk` (`type_id`),
  KEY `asset_user_id_fk` (`modified_by`),
  CONSTRAINT `assets_ibfk_1` FOREIGN KEY (`type_id`) REFERENCES `asset_types` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `assets_ibfk_2` FOREIGN KEY (`modified_by`) REFERENCES `users` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `caption_types`
--

DROP TABLE IF EXISTS `caption_types`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `caption_types` (
  `id` int(11) NOT NULL auto_increment,
  `capn_name` varchar(25) NOT NULL,
  `title` varchar(50) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `captions`
--

DROP TABLE IF EXISTS `captions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `captions` (
  `id` int(11) NOT NULL auto_increment,
  `type_id` int(11) NOT NULL,
  `asset_id` varchar(50) NOT NULL,
  `caption` text NOT NULL,
  `modified_by` varchar(50) NOT NULL,
  `is_active` smallint(1) NOT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `captions_type_fk12` (`type_id`),
  KEY `captions_asset_fk12` (`asset_id`),
  KEY `captions_user_fk12` (`modified_by`),
  CONSTRAINT `captions_ibfk_1` FOREIGN KEY (`type_id`) REFERENCES `caption_types` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `captions_ibfk_2` FOREIGN KEY (`modified_by`) REFERENCES `users` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `captions_ibfk_3` FOREIGN KEY (`asset_id`) REFERENCES `assets` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=7238 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `container_metadata`
--

DROP TABLE IF EXISTS `container_metadata`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `container_metadata` (
  `id` int(11) NOT NULL auto_increment,
  `container_id` varchar(50) NOT NULL,
  `meta_key` varchar(45) NOT NULL,
  `meta_value` text NOT NULL,
  `is_deleted` smallint(1) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `container_id_fk12` (`container_id`),
  CONSTRAINT `container_metadata_ibfk_3` FOREIGN KEY (`container_id`) REFERENCES `containers` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=55 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `containers`
--

DROP TABLE IF EXISTS `containers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `containers` (
  `id` varchar(50) NOT NULL,
  `title` varchar(45) default NULL,
  `description` varchar(255) default NULL,
  `is_approved` smallint(1) NOT NULL default '0',
  `is_deleted` smallint(6) NOT NULL default '0',
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  `modified_by` varchar(50) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `container_user_id_fk` (`modified_by`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `group_container_cnx`
--

DROP TABLE IF EXISTS `group_container_cnx`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `group_container_cnx` (
  `id` int(11) NOT NULL auto_increment,
  `group_id` varchar(50) NOT NULL,
  `container_id` varchar(50) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `cnx_group_id_fk` (`group_id`),
  KEY `cnx_container_id_fk` (`container_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `group_meta`
--

DROP TABLE IF EXISTS `group_meta`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `group_meta` (
  `id` int(11) NOT NULL auto_increment,
  `group_id` varchar(50) NOT NULL,
  `meta_name` varchar(45) NOT NULL,
  `meta_value` text NOT NULL,
  `is_deleted` smallint(1) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `group_meta_group_fk12` (`group_id`),
  CONSTRAINT `group_meta_ibfk_2` FOREIGN KEY (`group_id`) REFERENCES `groups` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=374 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `groups`
--

DROP TABLE IF EXISTS `groups`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `groups` (
  `id` varchar(50) NOT NULL,
  `title` varchar(45) NOT NULL,
  `date_start` datetime default NULL,
  `date_end` datetime default NULL,
  `is_approved` smallint(1) NOT NULL default '0',
  `is_deleted` smallint(1) NOT NULL default '0',
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  `modified_by` varchar(50) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `group_user_id_fk` (`modified_by`),
  CONSTRAINT `groups_ibfk_1` FOREIGN KEY (`modified_by`) REFERENCES `users` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `keyword_asset_cnx`
--

DROP TABLE IF EXISTS `keyword_asset_cnx`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `keyword_asset_cnx` (
  `id` int(11) NOT NULL auto_increment,
  `keyword_id` int(11) NOT NULL,
  `asset_id` varchar(50) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `keyword_asset_cnx_keyword_fk` (`keyword_id`),
  KEY `keyowrd_asset_cnx_asset_fk` (`asset_id`),
  CONSTRAINT `keyword_asset_cnx_ibfk_1` FOREIGN KEY (`asset_id`) REFERENCES `assets` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `keyword_asset_cnx_ibfk_2` FOREIGN KEY (`keyword_id`) REFERENCES `keywords` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=34735 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `keywords`
--

DROP TABLE IF EXISTS `keywords`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `keywords` (
  `id` int(11) NOT NULL auto_increment,
  `keyword` varchar(255) NOT NULL,
  `is_deleted` smallint(1) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3770 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `order_lineitems`
--

DROP TABLE IF EXISTS `order_lineitems`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `order_lineitems` (
  `id` int(11) NOT NULL auto_increment,
  `order_id` varchar(50) NOT NULL,
  `asset_id` varchar(50) NOT NULL,
  `is_approved` smallint(1) NOT NULL default '0',
  `created` datetime NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `lineitems_order_fk12` (`order_id`),
  KEY `lineitems_asset_fk12` (`asset_id`),
  CONSTRAINT `order_lineitems_ibfk_1` FOREIGN KEY (`asset_id`) REFERENCES `assets` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `order_lineitems_ibfk_2` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=1524 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `orders`
--

DROP TABLE IF EXISTS `orders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `orders` (
  `id` varchar(50) NOT NULL,
  `order_id` varchar(50) NOT NULL,
  `user_id` varchar(50) NOT NULL,
  `is_active` smallint(1) NOT NULL default '0',
  `is_deleted` smallint(1) NOT NULL default '0',
  `created` datetime NOT NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `order_id` (`order_id`),
  KEY `orders_user_fk12` (`user_id`),
  CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `organizations`
--

DROP TABLE IF EXISTS `organizations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `organizations` (
  `id` int(11) NOT NULL auto_increment,
  `org_name` varchar(45) NOT NULL,
  `title` varchar(45) NOT NULL,
  `is_deleted` smallint(1) NOT NULL default '0',
  `created` datetime NOT NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `org_name` (`org_name`)
) ENGINE=InnoDB AUTO_INCREMENT=185 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `photographers`
--

DROP TABLE IF EXISTS `photographers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `photographers` (
  `id` int(11) NOT NULL auto_increment,
  `firstname` varchar(50) NOT NULL,
  `lastname` varchar(50) NOT NULL,
  `modified_by` varchar(50) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `photogs_user_fk12` (`modified_by`),
  CONSTRAINT `photographers_ibfk_1` FOREIGN KEY (`modified_by`) REFERENCES `users` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `properties`
--

DROP TABLE IF EXISTS `properties`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `properties` (
  `id` int(11) NOT NULL auto_increment,
  `machine_name` varchar(45) NOT NULL,
  `value` text NOT NULL,
  `title` varchar(45) NOT NULL,
  `is_enabled` smallint(6) NOT NULL default '1',
  `modified` datetime NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `publications`
--

DROP TABLE IF EXISTS `publications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `publications` (
  `id` int(11) NOT NULL auto_increment,
  `title` varchar(100) NOT NULL,
  `is_active` smallint(1) NOT NULL default '1',
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `shoots`
--

DROP TABLE IF EXISTS `shoots`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `shoots` (
  `id` int(11) NOT NULL auto_increment,
  `shoot_name` varchar(45) NOT NULL,
  `title` varchar(100) NOT NULL,
  `description` varchar(255) NOT NULL,
  `shoot_date` datetime NOT NULL,
  `is_active` smallint(1) NOT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  `modified_by` varchar(50) NOT NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `shoot_name` (`shoot_name`),
  KEY `shoots_user_fk12` (`modified_by`),
  CONSTRAINT `shoots_ibfk_1` FOREIGN KEY (`modified_by`) REFERENCES `users` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Temporary table structure for view `userTypeQuickview`
--

DROP TABLE IF EXISTS `userTypeQuickview`;
/*!50001 DROP VIEW IF EXISTS `userTypeQuickview`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE TABLE `userTypeQuickview` (
 `type_id` tinyint NOT NULL,
  `title` tinyint NOT NULL,
  `user_id` tinyint NOT NULL,
  `email` tinyint NOT NULL,
  `fullname` tinyint NOT NULL,
  `is_active` tinyint NOT NULL
) ENGINE=MyISAM */;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `user_api`
--

DROP TABLE IF EXISTS `user_api`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_api` (
  `id` int(11) NOT NULL auto_increment,
  `user_id` varchar(50) NOT NULL,
  `api_key` varchar(50) NOT NULL,
  `secret` varchar(100) NOT NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `user_id` (`user_id`,`api_key`,`secret`),
  CONSTRAINT `user_api_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `user_organization_cnx`
--

DROP TABLE IF EXISTS `user_organization_cnx`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_organization_cnx` (
  `id` int(11) NOT NULL auto_increment,
  `user_id` varchar(50) NOT NULL,
  `org_id` int(11) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `user_org_user_fk12` (`user_id`),
  KEY `user_org_org_fk12` (`org_id`),
  CONSTRAINT `user_organization_cnx_ibfk_3` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `user_organization_cnx_ibfk_4` FOREIGN KEY (`org_id`) REFERENCES `organizations` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=572 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `user_type_cnx`
--

DROP TABLE IF EXISTS `user_type_cnx`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_type_cnx` (
  `id` int(11) NOT NULL auto_increment,
  `user_id` varchar(50) NOT NULL,
  `type_id` int(11) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `user_type_type_fk` (`type_id`),
  KEY `user_type_user_fk` (`user_id`),
  CONSTRAINT `user_type_cnx_ibfk_1` FOREIGN KEY (`type_id`) REFERENCES `user_types` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `user_type_cnx_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=3663 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `user_types`
--

DROP TABLE IF EXISTS `user_types`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_types` (
  `id` int(11) NOT NULL auto_increment,
  `type_name` varchar(45) NOT NULL,
  `title` varchar(45) NOT NULL,
  `is_deleted` smallint(1) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `type_name_UNIQUE` (`type_name`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `user_webauths`
--

DROP TABLE IF EXISTS `user_webauths`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_webauths` (
  `id` int(11) NOT NULL auto_increment,
  `campusid` varchar(45) NOT NULL,
  `user_id` varchar(50) NOT NULL,
  `created` datetime NOT NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `user_id_UNIQUE` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=662 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `id` varchar(50) NOT NULL,
  `username` varchar(45) NOT NULL,
  `password` varchar(45) NOT NULL,
  `email` varchar(45) NOT NULL,
  `firstname` varchar(45) default NULL,
  `lastname` varchar(45) default NULL,
  `is_active` smallint(1) NOT NULL default '0',
  `is_deleted` smallint(1) NOT NULL default '0',
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `username_UNIQUE` (`username`),
  UNIQUE KEY `email_UNIQUE` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Temporary table structure for view `view_publicAssetsMostViewed`
--

DROP TABLE IF EXISTS `view_publicAssetsMostViewed`;
/*!50001 DROP VIEW IF EXISTS `view_publicAssetsMostViewed`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE TABLE `view_publicAssetsMostViewed` (
 `id` tinyint NOT NULL,
  `public_id` tinyint NOT NULL,
  `type_id` tinyint NOT NULL,
  `filename` tinyint NOT NULL,
  `title` tinyint NOT NULL,
  `caption` tinyint NOT NULL,
  `description` tinyint NOT NULL,
  `photographer_id` tinyint NOT NULL,
  `shoot_id` tinyint NOT NULL,
  `credit` tinyint NOT NULL,
  `location` tinyint NOT NULL,
  `lat` tinyint NOT NULL,
  `lng` tinyint NOT NULL,
  `is_active` tinyint NOT NULL,
  `is_deleted` tinyint NOT NULL,
  `created` tinyint NOT NULL,
  `modified` tinyint NOT NULL,
  `modified_by` tinyint NOT NULL
) ENGINE=MyISAM */;
SET character_set_client = @saved_cs_client;

--
-- Temporary table structure for view `view_publicContainerAssets`
--

DROP TABLE IF EXISTS `view_publicContainerAssets`;
/*!50001 DROP VIEW IF EXISTS `view_publicContainerAssets`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE TABLE `view_publicContainerAssets` (
 `container_id` tinyint NOT NULL,
  `id` tinyint NOT NULL,
  `public_id` tinyint NOT NULL,
  `type_id` tinyint NOT NULL,
  `filename` tinyint NOT NULL,
  `title` tinyint NOT NULL,
  `caption` tinyint NOT NULL,
  `description` tinyint NOT NULL,
  `photographer_id` tinyint NOT NULL,
  `shoot_id` tinyint NOT NULL,
  `credit` tinyint NOT NULL,
  `location` tinyint NOT NULL,
  `lat` tinyint NOT NULL,
  `lng` tinyint NOT NULL,
  `is_active` tinyint NOT NULL,
  `is_deleted` tinyint NOT NULL,
  `created` tinyint NOT NULL,
  `modified` tinyint NOT NULL,
  `modified_by` tinyint NOT NULL
) ENGINE=MyISAM */;
SET character_set_client = @saved_cs_client;

--
-- Temporary table structure for view `view_publicGroupImages`
--

DROP TABLE IF EXISTS `view_publicGroupImages`;
/*!50001 DROP VIEW IF EXISTS `view_publicGroupImages`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE TABLE `view_publicGroupImages` (
 `group_id` tinyint NOT NULL,
  `id` tinyint NOT NULL,
  `public_id` tinyint NOT NULL,
  `type_id` tinyint NOT NULL,
  `filename` tinyint NOT NULL,
  `title` tinyint NOT NULL,
  `caption` tinyint NOT NULL,
  `description` tinyint NOT NULL,
  `photographer_id` tinyint NOT NULL,
  `shoot_id` tinyint NOT NULL,
  `credit` tinyint NOT NULL,
  `location` tinyint NOT NULL,
  `lat` tinyint NOT NULL,
  `lng` tinyint NOT NULL,
  `is_active` tinyint NOT NULL,
  `is_deleted` tinyint NOT NULL,
  `created` tinyint NOT NULL,
  `modified` tinyint NOT NULL,
  `modified_by` tinyint NOT NULL
) ENGINE=MyISAM */;
SET character_set_client = @saved_cs_client;

--
-- Temporary table structure for view `view_userWithTypes`
--

DROP TABLE IF EXISTS `view_userWithTypes`;
/*!50001 DROP VIEW IF EXISTS `view_userWithTypes`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE TABLE `view_userWithTypes` (
 `type_id` tinyint NOT NULL,
  `title` tinyint NOT NULL,
  `user_id` tinyint NOT NULL,
  `email` tinyint NOT NULL,
  `fullname` tinyint NOT NULL,
  `is_active` tinyint NOT NULL
) ENGINE=MyISAM */;
SET character_set_client = @saved_cs_client;

--
-- Final view structure for view `userTypeQuickview`
--

/*!50001 DROP TABLE IF EXISTS `userTypeQuickview`*/;
/*!50001 DROP VIEW IF EXISTS `userTypeQuickview`*/;
/*!50001 CREATE ALGORITHM=UNDEFINED DEFINER=`image_archive`@`128.200.134.5` SQL SECURITY DEFINER VIEW `userTypeQuickview` AS select `b`.`id` AS `type_id`,`b`.`title` AS `title`,`c`.`id` AS `user_id`,`c`.`email` AS `email`,concat(`c`.`firstname`,_latin1' ',`c`.`lastname`) AS `fullname`,`c`.`is_active` AS `is_active` from ((`user_type_cnx` `a` left join `user_types` `b` on((`b`.`id` = `a`.`type_id`))) left join `users` `c` on((`c`.`id` = `a`.`user_id`))) */;

--
-- Final view structure for view `view_publicAssetsMostViewed`
--

/*!50001 DROP TABLE IF EXISTS `view_publicAssetsMostViewed`*/;
/*!50001 DROP VIEW IF EXISTS `view_publicAssetsMostViewed`*/;
/*!50001 CREATE ALGORITHM=UNDEFINED DEFINER=`image_archive`@`128.200.134.5` SQL SECURITY DEFINER VIEW `view_publicAssetsMostViewed` AS select `a`.`id` AS `id`,`a`.`public_id` AS `public_id`,`a`.`type_id` AS `type_id`,`a`.`filename` AS `filename`,`a`.`title` AS `title`,`a`.`caption` AS `caption`,`a`.`description` AS `description`,`a`.`photographer_id` AS `photographer_id`,`a`.`shoot_id` AS `shoot_id`,`a`.`credit` AS `credit`,`a`.`location` AS `location`,`a`.`lat` AS `lat`,`a`.`lng` AS `lng`,`a`.`is_active` AS `is_active`,`a`.`is_deleted` AS `is_deleted`,`a`.`created` AS `created`,`a`.`modified` AS `modified`,`a`.`modified_by` AS `modified_by` from (((((((`asset_metadata` `b` left join `assets` `a` on((`a`.`id` = `b`.`asset_id`))) left join `asset_restriction_embargo` `c` on((`c`.`asset_id` = `a`.`id`))) left join `asset_restriction_hippa` `d` on((`d`.`asset_id` = `a`.`id`))) left join `asset_restriction_ncaa` `e` on((`e`.`asset_id` = `a`.`id`))) left join `asset_restriction_external` `f` on((`f`.`asset_id` = `a`.`id`))) left join `asset_restriction_internal` `g` on((`g`.`asset_id` = `a`.`id`))) left join `asset_restriction_subject` `h` on((`h`.`asset_id` = `a`.`id`))) where ((`a`.`is_active` = 1) and (`a`.`is_deleted` = 0) and (isnull(`c`.`id`) or (`c`.`start_date` < now())) and isnull(`d`.`id`) and isnull(`e`.`id`) and isnull(`f`.`id`) and isnull(`g`.`id`) and isnull(`h`.`id`) and (`b`.`metadata_name` = _latin1'num_views')) group by `a`.`id` order by cast(`b`.`metadata_value` as unsigned) desc,`a`.`created` desc */;

--
-- Final view structure for view `view_publicContainerAssets`
--

/*!50001 DROP TABLE IF EXISTS `view_publicContainerAssets`*/;
/*!50001 DROP VIEW IF EXISTS `view_publicContainerAssets`*/;
/*!50001 CREATE ALGORITHM=UNDEFINED DEFINER=`image_archive`@`128.200.134.5` SQL SECURITY DEFINER VIEW `view_publicContainerAssets` AS select `c`.`container_id` AS `container_id`,`a`.`id` AS `id`,`a`.`public_id` AS `public_id`,`a`.`type_id` AS `type_id`,`a`.`filename` AS `filename`,`a`.`title` AS `title`,`a`.`caption` AS `caption`,`a`.`description` AS `description`,`a`.`photographer_id` AS `photographer_id`,`a`.`shoot_id` AS `shoot_id`,`a`.`credit` AS `credit`,`a`.`location` AS `location`,`a`.`lat` AS `lat`,`a`.`lng` AS `lng`,`a`.`is_active` AS `is_active`,`a`.`is_deleted` AS `is_deleted`,`a`.`created` AS `created`,`a`.`modified` AS `modified`,`a`.`modified_by` AS `modified_by` from ((((((((`assets` `a` left join `asset_group_cnx` `b` on((`b`.`asset_id` = `a`.`id`))) left join `container_metadata` `c` on((`c`.`meta_value` = `b`.`group_id`))) left join `asset_restriction_embargo` `d` on((`d`.`asset_id` = `a`.`id`))) left join `asset_restriction_external` `e` on((`e`.`asset_id` = `a`.`id`))) left join `asset_restriction_hippa` `f` on((`f`.`asset_id` = `a`.`id`))) left join `asset_restriction_internal` `g` on((`g`.`asset_id` = `a`.`id`))) left join `asset_restriction_ncaa` `h` on((`h`.`asset_id` = `a`.`id`))) left join `asset_restriction_subject` `i` on((`i`.`asset_id` = `a`.`id`))) where ((`c`.`meta_key` = _latin1'group_id') and (`a`.`is_active` = 1) and (`a`.`is_deleted` = 0) and (isnull(`d`.`id`) or (`d`.`start_date` < now())) and isnull(`e`.`id`) and isnull(`f`.`id`) and isnull(`g`.`id`) and isnull(`h`.`id`) and isnull(`i`.`id`)) */;

--
-- Final view structure for view `view_publicGroupImages`
--

/*!50001 DROP TABLE IF EXISTS `view_publicGroupImages`*/;
/*!50001 DROP VIEW IF EXISTS `view_publicGroupImages`*/;
/*!50001 CREATE ALGORITHM=UNDEFINED DEFINER=`image_archive`@`128.200.134.5` SQL SECURITY DEFINER VIEW `view_publicGroupImages` AS select `grp`.`id` AS `group_id`,`b`.`id` AS `id`,`b`.`public_id` AS `public_id`,`b`.`type_id` AS `type_id`,`b`.`filename` AS `filename`,`b`.`title` AS `title`,`b`.`caption` AS `caption`,`b`.`description` AS `description`,`b`.`photographer_id` AS `photographer_id`,`b`.`shoot_id` AS `shoot_id`,`b`.`credit` AS `credit`,`b`.`location` AS `location`,`b`.`lat` AS `lat`,`b`.`lng` AS `lng`,`b`.`is_active` AS `is_active`,`b`.`is_deleted` AS `is_deleted`,`b`.`created` AS `created`,`b`.`modified` AS `modified`,`b`.`modified_by` AS `modified_by` from ((((((((`asset_group_cnx` `a` left join `groups` `grp` on((`grp`.`id` = `a`.`group_id`))) left join `assets` `b` on((`b`.`id` = `a`.`asset_id`))) left join `asset_restriction_embargo` `c` on((`c`.`asset_id` = `b`.`id`))) left join `asset_restriction_hippa` `hippa` on((`hippa`.`asset_id` = `b`.`id`))) left join `asset_restriction_ncaa` `ncaa` on((`ncaa`.`asset_id` = `b`.`id`))) left join `asset_restriction_external` `ext` on((`ext`.`asset_id` = `b`.`id`))) left join `asset_restriction_internal` `internal` on((`internal`.`asset_id` = `b`.`id`))) left join `asset_restriction_subject` `subj` on((`subj`.`asset_id` = `b`.`id`))) where ((`b`.`is_active` = 1) and (`b`.`is_deleted` = 0) and (isnull(`c`.`id`) or (`c`.`start_date` < now())) and isnull(`hippa`.`id`) and isnull(`ncaa`.`id`) and isnull(`ext`.`id`) and isnull(`internal`.`id`) and isnull(`subj`.`id`)) */;

--
-- Final view structure for view `view_userWithTypes`
--

/*!50001 DROP TABLE IF EXISTS `view_userWithTypes`*/;
/*!50001 DROP VIEW IF EXISTS `view_userWithTypes`*/;
/*!50001 CREATE ALGORITHM=UNDEFINED DEFINER=`image_archive`@`128.200.134.5` SQL SECURITY DEFINER VIEW `view_userWithTypes` AS select `b`.`id` AS `type_id`,`b`.`title` AS `title`,`c`.`id` AS `user_id`,`c`.`email` AS `email`,concat(`c`.`firstname`,_latin1' ',`c`.`lastname`) AS `fullname`,`c`.`is_active` AS `is_active` from ((`user_type_cnx` `a` left join `user_types` `b` on((`b`.`id` = `a`.`type_id`))) left join `users` `c` on((`c`.`id` = `a`.`user_id`))) */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2014-09-29 11:59:36
