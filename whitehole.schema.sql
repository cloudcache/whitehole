-- MySQL dump 10.13  Distrib 5.5.31, for debian-linux-gnu (x86_64)
--
-- Host: localhost    Database: whitehole
-- ------------------------------------------------------
-- Server version	5.5.31-0ubuntu0.12.04.1

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
-- Table structure for table `info_node`
--

DROP TABLE IF EXISTS `info_node`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `info_node` (
  `ip_address` varchar(256) NOT NULL,
  `hostname` varchar(256) DEFAULT NULL,
  `create_time` int(11) DEFAULT NULL,
  `update_time` int(11) DEFAULT NULL,
  `status` int(11) DEFAULT NULL,
  `total_sys_core` varchar(11) DEFAULT NULL,
  `total_sys_mem` int(11) DEFAULT NULL,
  `free_sys_cpu` varchar(11) DEFAULT NULL,
  `free_sys_mem` int(11) DEFAULT NULL,
  `total_vm_count` int(11) DEFAULT '0',
  `active_vm_count` int(11) DEFAULT '0',
  `inactive_vm_count` int(11) DEFAULT '0',
  `hypervisor` varchar(256) DEFAULT NULL,
  `host_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`ip_address`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `info_vm`
--

DROP TABLE IF EXISTS `info_vm`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `info_vm` (
  `uuid` varchar(256) NOT NULL,
  `create_time` int(11) DEFAULT NULL,
  `sshkey_uuid` varchar(256) DEFAULT NULL,
  `sshkey_desc` varchar(256) DEFAULT NULL,
  `ip_address` varchar(256) DEFAULT NULL,
  `name` varchar(256) DEFAULT NULL,
  `cpu` int(11) DEFAULT NULL,
  `memory` int(11) DEFAULT NULL,
  `mac` varchar(256) DEFAULT NULL,
  `bits` int(11) DEFAULT NULL,
  `hypervisor` varchar(256) DEFAULT NULL,
  `node` varchar(256) DEFAULT NULL,
  `vnc` int(11) DEFAULT NULL,
  `account` varchar(256) DEFAULT NULL,
  `data_volume` int(11) DEFAULT NULL,
  `os_type` varchar(256) DEFAULT NULL,
  `hostname` varchar(256) DEFAULT NULL,
  `status` int(11) DEFAULT NULL,
  `security_group_uuid` varchar(256) DEFAULT NULL,
  `origin` varchar(256) DEFAULT NULL,
  `protect` int(1) NOT NULL,
  PRIMARY KEY (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `iso`
--

DROP TABLE IF EXISTS `iso`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `iso` (
  `uuid` varchar(256) NOT NULL,
  `create_time` int(11) NOT NULL,
  `name` varchar(256) NOT NULL,
  `description` varchar(256) DEFAULT NULL,
  `os_type` varchar(256) NOT NULL,
  `url` varchar(256) DEFAULT NULL,
  PRIMARY KEY (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `monitoring`
--

DROP TABLE IF EXISTS `monitoring`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `monitoring` (
  `num` int(11) NOT NULL AUTO_INCREMENT,
  `timestamp` int(11) NOT NULL,
  `hostname` varchar(256) NOT NULL,
  `uuid` varchar(256) NOT NULL,
  `vm_maxMem` bigint(20) DEFAULT NULL,
  `vm_memUsed` bigint(20) DEFAULT NULL,
  `vm_nrVirtCpu` int(11) DEFAULT NULL,
  `vm_cpuUsed` int(11) DEFAULT NULL,
  `vda_rd_req` bigint(20) DEFAULT NULL,
  `vda_rd_bytes` bigint(20) DEFAULT NULL,
  `vda_wr_req` bigint(20) DEFAULT NULL,
  `vda_wr_bytes` bigint(20) DEFAULT NULL,
  `vdb_rd_req` bigint(20) DEFAULT NULL,
  `vdb_rd_bytes` bigint(20) DEFAULT NULL,
  `vdb_wr_req` bigint(20) DEFAULT NULL,
  `vdb_wr_bytes` bigint(20) DEFAULT NULL,
  `vnet_rx_bytes` bigint(20) DEFAULT NULL,
  `vnet_rx_packets` bigint(20) DEFAULT NULL,
  `vnet_rx_errs` bigint(20) DEFAULT NULL,
  `vnet_rx_drop` bigint(20) DEFAULT NULL,
  `vnet_tx_bytes` bigint(20) DEFAULT NULL,
  `vnet_tx_packets` bigint(20) DEFAULT NULL,
  `vnet_tx_errs` bigint(20) DEFAULT NULL,
  `vnet_tx_drop` bigint(20) DEFAULT NULL,
  PRIMARY KEY (`num`)
) ENGINE=InnoDB AUTO_INCREMENT=65994 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `nbd`
--

DROP TABLE IF EXISTS `nbd`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `nbd` (
  `num` varchar(256) NOT NULL,
  `used` int(11) NOT NULL,
  PRIMARY KEY (`num`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `network_pool`
--

DROP TABLE IF EXISTS `network_pool`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `network_pool` (
  `ip_address` varchar(256) NOT NULL,
  `used` int(11) DEFAULT NULL,
  `vm` varchar(256) DEFAULT NULL,
  `account` varchar(256) DEFAULT NULL,
  PRIMARY KEY (`ip_address`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `primary_storage`
--

DROP TABLE IF EXISTS `primary_storage`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `primary_storage` (
  `uuid` varchar(256) NOT NULL,
  `host` varchar(256) DEFAULT NULL,
  `fs_type` varchar(256) DEFAULT NULL,
  `export_path` varchar(256) DEFAULT NULL,
  `mount_path` varchar(256) DEFAULT NULL,
  `create_time` int(11) DEFAULT NULL,
  `update_time` int(11) DEFAULT NULL,
  `total` int(11) DEFAULT NULL,
  `used` int(11) DEFAULT NULL,
  `free` int(11) DEFAULT NULL,
  PRIMARY KEY (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `secondary_storage`
--

DROP TABLE IF EXISTS `secondary_storage`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `secondary_storage` (
  `uuid` varchar(256) NOT NULL,
  `host` varchar(256) DEFAULT NULL,
  `fs_type` varchar(256) DEFAULT NULL,
  `export_path` varchar(256) DEFAULT NULL,
  `mount_path` varchar(256) DEFAULT NULL,
  `create_time` int(11) DEFAULT NULL,
  `update_time` int(11) DEFAULT NULL,
  `total` int(11) DEFAULT NULL,
  `used` int(11) DEFAULT NULL,
  `free` int(11) DEFAULT NULL,
  PRIMARY KEY (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `security_group`
--

DROP TABLE IF EXISTS `security_group`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `security_group` (
  `uuid` varchar(256) NOT NULL,
  `rule_name` varchar(256) NOT NULL,
  `account` varchar(256) NOT NULL,
  `description` varchar(256) DEFAULT NULL,
  `used_count` int(11) NOT NULL,
  `create_time` int(11) DEFAULT NULL,
  PRIMARY KEY (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `security_ruleset`
--

DROP TABLE IF EXISTS `security_ruleset`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `security_ruleset` (
  `num` int(11) NOT NULL AUTO_INCREMENT,
  `create_time` int(11) NOT NULL,
  `uuid` varchar(256) NOT NULL,
  `rule_name` varchar(256) NOT NULL,
  `account` varchar(256) NOT NULL,
  `protocol` varchar(256) DEFAULT NULL,
  `src_ip` varchar(256) DEFAULT NULL,
  `src_ip_mask` varchar(256) DEFAULT NULL,
  `dst_port_start` int(11) DEFAULT NULL,
  `dst_port_end` int(11) DEFAULT NULL,
  `action` varchar(256) DEFAULT NULL,
  PRIMARY KEY (`num`)
) ENGINE=InnoDB AUTO_INCREMENT=46 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `snapshots`
--

DROP TABLE IF EXISTS `snapshots`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `snapshots` (
  `num` int(11) NOT NULL AUTO_INCREMENT,
  `create_time` int(11) NOT NULL,
  `vm_uuid` varchar(256) NOT NULL,
  `description` varchar(256) DEFAULT NULL,
  PRIMARY KEY (`num`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ssh_keypair`
--

DROP TABLE IF EXISTS `ssh_keypair`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ssh_keypair` (
  `uuid` varchar(256) NOT NULL,
  `account` varchar(256) DEFAULT NULL,
  `description` varchar(256) DEFAULT NULL,
  `used_count` int(11) DEFAULT NULL,
  `create_time` int(11) DEFAULT NULL,
  PRIMARY KEY (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `vm_template`
--

DROP TABLE IF EXISTS `vm_template`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vm_template` (
  `uuid` varchar(256) NOT NULL,
  `name` varchar(256) DEFAULT NULL,
  `public` int(1) DEFAULT NULL,
  `featured` int(1) DEFAULT NULL,
  `hypervisor` varchar(256) DEFAULT NULL,
  `bits` int(11) DEFAULT NULL,
  `url` varchar(256) DEFAULT NULL,
  `format` varchar(256) DEFAULT NULL,
  `create_time` int(11) DEFAULT NULL,
  `account` varchar(256) DEFAULT NULL,
  `description` varchar(256) DEFAULT NULL,
  `bootable` int(1) DEFAULT NULL,
  `size_virtual` varchar(256) DEFAULT NULL,
  `size_real` varchar(256) DEFAULT NULL,
  `size_verify` varchar(256) DEFAULT NULL,
  `os_type` varchar(256) DEFAULT NULL,
  PRIMARY KEY (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2013-07-10 21:18:22
