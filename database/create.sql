/*
SQLyog Ultimate v11.5 (64 bit)
MySQL - 10.1.12-MariaDB : Database - actor
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
CREATE DATABASE /*!32312 IF NOT EXISTS*/`actor` /*!40100 DEFAULT CHARACTER SET utf8 */;

USE `actor`;

/*Table structure for table `ac_area` */

DROP TABLE IF EXISTS `ac_area`;

CREATE TABLE `ac_area` (
  `Id` smallint(8) NOT NULL AUTO_INCREMENT,
  `AreaName` varchar(40) NOT NULL,
  `Level` int(11) DEFAULT NULL,
  `Id_Level_0` int(11) DEFAULT NULL,
  `Id_Level_1` int(11) DEFAULT NULL,
  `Domain` varchar(50) DEFAULT 'www',
  `PinYin` varchar(50) DEFAULT NULL,
  `PY` varchar(10) NOT NULL DEFAULT '',
  `SortId` int(11) DEFAULT NULL,
  PRIMARY KEY (`Id`),
  KEY `Id_Level_0` (`Id_Level_0`),
  KEY `Id_Level_1` (`Id_Level_1`),
  KEY `Domain` (`Domain`),
  KEY `IDX_AR_NA` (`AreaName`),
  KEY `IDX_PI_YI` (`PinYin`),
  KEY `IDX_LFT_RGT_ID` (`Id`)
) ENGINE=InnoDB AUTO_INCREMENT=3002 DEFAULT CHARSET=utf8;

/*Table structure for table `ac_article` */

DROP TABLE IF EXISTS `ac_article`;

CREATE TABLE `ac_article` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '消息编号',
  `title` varchar(200) DEFAULT NULL COMMENT '招聘标题',
  `content` text COMMENT '招聘内容',
  `pnum` smallint(4) unsigned DEFAULT '0' COMMENT '招聘人数',
  `catid` smallint(4) unsigned DEFAULT '0' COMMENT '分类编号',
  `areaid` mediumint(8) unsigned DEFAULT '0' COMMENT '地区编号',
  `centerid` smallint(4) unsigned DEFAULT '0' COMMENT '影城编号',
  `dateline` int(10) unsigned DEFAULT NULL COMMENT '添加时间',
  `pubuid` int(11) unsigned DEFAULT NULL COMMENT '发布人',
  `published` tinyint(1) unsigned DEFAULT '0' COMMENT '是否发布 0:未发布，1：已发布',
  `publishtime` int(10) unsigned DEFAULT NULL COMMENT '发布时间',
  PRIMARY KEY (`id`),
  KEY `pubuid` (`pubuid`),
  KEY `published_2` (`published`,`centerid`,`areaid`,`catid`),
  KEY `published` (`publishtime`,`published`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `ac_category` */

DROP TABLE IF EXISTS `ac_category`;

CREATE TABLE `ac_category` (
  `id` smallint(4) unsigned NOT NULL AUTO_INCREMENT COMMENT '分类编号',
  `cname` varchar(100) NOT NULL COMMENT '分类名称',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `ac_center` */

DROP TABLE IF EXISTS `ac_center`;

CREATE TABLE `ac_center` (
  `id` smallint(4) unsigned NOT NULL AUTO_INCREMENT COMMENT '影城编号',
  `fname` varchar(200) NOT NULL COMMENT '影城名称',
  `areaid` mediumint(8) DEFAULT NULL COMMENT '城区编号',
  `address` varchar(200) DEFAULT '' COMMENT '详细地址',
  PRIMARY KEY (`id`),
  KEY `areaid` (`areaid`),
  KEY `fname` (`fname`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `ac_enroll` */

DROP TABLE IF EXISTS `ac_enroll`;

CREATE TABLE `ac_enroll` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '报名编号',
  `articleid` int(11) unsigned NOT NULL COMMENT '文章编号',
  `uid` int(11) unsigned NOT NULL COMMENT '用户编号',
  `dateline` int(11) unsigned NOT NULL COMMENT '报名时间',
  PRIMARY KEY (`id`),
  KEY `articleid` (`articleid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `ac_user` */

DROP TABLE IF EXISTS `ac_user`;

CREATE TABLE `ac_user` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '用户编号',
  `loginname` varchar(20) NOT NULL COMMENT '登录名称',
  `fullname` varchar(40) DEFAULT '' COMMENT '真实姓名',
  `phone` varchar(16) DEFAULT NULL COMMENT '手机',
  `disabled` tinyint(1) DEFAULT NULL COMMENT '0：正常，1；已禁用',
  `groupid` smallint(4) DEFAULT NULL COMMENT '分组编号',
  `dateline` int(10) DEFAULT NULL COMMENT '注册时间',
  PRIMARY KEY (`id`),
  KEY `phone` (`phone`),
  KEY `disabled` (`disabled`),
  KEY `groupid` (`groupid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `ac_user_group` */

DROP TABLE IF EXISTS `ac_user_group`;

CREATE TABLE `ac_user_group` (
  `groupid` smallint(4) unsigned NOT NULL AUTO_INCREMENT COMMENT '分组编号',
  `groupname` varchar(200) NOT NULL COMMENT '分组名称',
  `disabled` tinyint(1) DEFAULT NULL COMMENT '状态 0:正常，1：禁用',
  PRIMARY KEY (`groupid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
