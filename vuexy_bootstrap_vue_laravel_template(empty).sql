/*
SQLyog Professional v13.1.1 (64 bit)
MySQL - 10.4.8-MariaDB : Database - vuexy_bootstrap_vue_laravel_template
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
CREATE DATABASE /*!32312 IF NOT EXISTS*/`vuexy_bootstrap_vue_laravel_template` /*!40100 DEFAULT CHARACTER SET utf8 */;

USE `vuexy_bootstrap_vue_laravel_template`;

/*Table structure for table `corridor_disinfection_table` */

DROP TABLE IF EXISTS `corridor_disinfection_table`;

CREATE TABLE `corridor_disinfection_table` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `unit` varchar(255) DEFAULT '',
  `floor` varchar(255) DEFAULT '',
  `corridor_number` int(11) DEFAULT 0,
  `spots_count` int(11) DEFAULT 0,
  `date` datetime DEFAULT NULL,
  `duration` int(11) DEFAULT 0,
  `is_completed` tinyint(1) DEFAULT 0,
  `robot_serial` varchar(255) DEFAULT '' COMMENT 'robots_info_table primary_key',
  `created_at` datetime DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `hospital_rooms_table` */

DROP TABLE IF EXISTS `hospital_rooms_table`;

CREATE TABLE `hospital_rooms_table` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `unit` varchar(255) DEFAULT '',
  `floor` varchar(255) DEFAULT '',
  `room_number` varchar(255) DEFAULT '',
  `room_size` int(11) DEFAULT 0,
  `creation_date` datetime DEFAULT NULL,
  `last_edit_date` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `migrations` */

DROP TABLE IF EXISTS `migrations`;

CREATE TABLE `migrations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `migrations` */

/*Table structure for table `robots_info_table` */

DROP TABLE IF EXISTS `robots_info_table`;

CREATE TABLE `robots_info_table` (
  `robot_serial` varchar(255) NOT NULL DEFAULT '',
  `robot_name` varchar(255) DEFAULT '',
  `robot_password` varchar(255) DEFAULT '',
  `robot_number` varchar(255) DEFAULT '',
  `created_at` datetime DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`robot_serial`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `room_disinfection_table` */

DROP TABLE IF EXISTS `room_disinfection_table`;

CREATE TABLE `room_disinfection_table` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `unit` varchar(255) DEFAULT '',
  `floor` varchar(255) DEFAULT '',
  `room` varchar(255) DEFAULT '',
  `spots_count` int(11) DEFAULT 0,
  `date` datetime DEFAULT NULL,
  `duration` int(11) DEFAULT 0,
  `is_completed` tinyint(1) DEFAULT 0,
  `robot_serial` varchar(255) DEFAULT '' COMMENT 'robots_info_table primary_key',
  `created_at` datetime DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `users` */

DROP TABLE IF EXISTS `users`;

CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT '',
  `password` varchar(255) DEFAULT '',
  `email` varchar(255) DEFAULT '',
  `is_admin` tinyint(1) DEFAULT 0 COMMENT '0:general,1:admin',
  `robot_serial` varchar(255) DEFAULT '',
  `status` tinyint(1) DEFAULT 0 COMMENT '0:deactive,1:active',
  `token` varchar(255) DEFAULT '',
  `remember_token` varchar(255) DEFAULT '',
  `created_at` datetime DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8;

/*Data for the table `users` */

insert  into `users`(`id`,`name`,`password`,`email`,`is_admin`,`robot_serial`,`status`,`token`,`remember_token`,`created_at`,`updated_at`) values 
(1,'test','$2y$10$Am9CgaeRu34xYOiQ3NTbWeSQM7rEfHZ2j.qrPluktjX0EboSn8v1G','test@test.com',1,'',1,'2NaqzwmXo1jLVEHMmTyiPoLq','','2021-09-02 03:37:24','2021-09-05 15:21:25');

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
