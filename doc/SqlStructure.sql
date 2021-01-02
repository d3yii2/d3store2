/*
SQLyog Ultimate v11.31 (32 bit)
MySQL - 10.1.37-MariaDB : Database - blankon_store
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
/*Table structure for table `store2_stack` */

DROP TABLE IF EXISTS `store2_stack`;

CREATE TABLE `store2_stack` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `store_id` smallint(5) unsigned NOT NULL COMMENT 'Store',
  `name` varchar(255) DEFAULT NULL COMMENT 'Stack name',
  `notes` text COMMENT 'Notes',
  `active` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT 'Active',
  PRIMARY KEY (`id`),
  KEY `store_id` (`store_id`),
  CONSTRAINT `store_stack_ibfk_1` FOREIGN KEY (`store_id`) REFERENCES `store2_store` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8;

/*Table structure for table `store2_store` */

DROP TABLE IF EXISTS `store2_store`;

CREATE TABLE `store2_store` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `company_id` smallint(5) unsigned NOT NULL,
  `name` varchar(50) DEFAULT NULL COMMENT 'Store Name',
  `address` varchar(255) DEFAULT NULL COMMENT 'Store Address',
  `active` tinyint(4) DEFAULT '1' COMMENT 'Active',
  PRIMARY KEY (`id`),
  KEY `sys_company_id` (`company_id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8;

/*Table structure for table `store2_tran_ref` */

DROP TABLE IF EXISTS `store2_tran_ref`;

CREATE TABLE `store2_tran_ref` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `transaction_id` int(10) unsigned NOT NULL,
  `model_id` tinyint(5) unsigned NOT NULL,
  `model_record_id` smallint(5) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `transaction_id` (`transaction_id`),
  KEY `model_id` (`model_id`),
  CONSTRAINT `store2_tran_ref_ibfk_1` FOREIGN KEY (`transaction_id`) REFERENCES `store2_transaction` (`id`),
  CONSTRAINT `store2_tran_ref_ibfk_2` FOREIGN KEY (`model_id`) REFERENCES `sys_models` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;

/*Table structure for table `store2_transaction` */

DROP TABLE IF EXISTS `store2_transaction`;

CREATE TABLE `store2_transaction` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `from_id` int(10) unsigned DEFAULT NULL COMMENT 'From stack',
  `type` enum('In','Out','Transfer') NOT NULL COMMENT 'Type',
  `time` timestamp NULL DEFAULT NULL COMMENT 'Time',
  `user_id` smallint(5) unsigned DEFAULT NULL COMMENT 'User',
  `stack_id` smallint(5) unsigned DEFAULT NULL COMMENT 'Stack',
  `qnt` decimal(10,3) unsigned NOT NULL COMMENT 'Quantity',
  `remain_qnt` decimal(10,3) unsigned NOT NULL COMMENT 'Remain Quantity',
  PRIMARY KEY (`id`),
  KEY `from_id` (`from_id`),
  KEY `stack_id` (`stack_id`),
  CONSTRAINT `store2_transaction_ibfk_1` FOREIGN KEY (`from_id`) REFERENCES `store2_transaction` (`id`),
  CONSTRAINT `store2_transaction_ibfk_2` FOREIGN KEY (`stack_id`) REFERENCES `store2_stack` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=30 DEFAULT CHARSET=latin1;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
