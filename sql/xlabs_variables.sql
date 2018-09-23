CREATE DATABASE  IF NOT EXISTS `xlabs` /*!40100 DEFAULT CHARACTER SET latin1 */;
USE `xlabs`;
-- MySQL dump 10.13  Distrib 5.7.17, for Win64 (x86_64)
--
-- Host: 127.0.0.1    Database: xlabs
-- ------------------------------------------------------
-- Server version	5.5.5-10.1.26-MariaDB

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
-- Table structure for table `variables`
--

DROP TABLE IF EXISTS `variables`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `variables` (
  `id` char(36) NOT NULL,
  `idDispositivo` char(36) NOT NULL,
  `tipo` varchar(50) NOT NULL,
  `valor` varchar(255) NOT NULL,
  `fecha` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `latitud` varchar(15) DEFAULT NULL,
  `longitud` varchar(15) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idDispositivo` (`idDispositivo`),
  CONSTRAINT `variables_ibfk_1` FOREIGN KEY (`idDispositivo`) REFERENCES `dispositivos` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `variables`
--

LOCK TABLES `variables` WRITE;
/*!40000 ALTER TABLE `variables` DISABLE KEYS */;
INSERT INTO `variables` VALUES ('03f499aa-beba-11e8-b101-c85b76da12f5','af76a1ba-be2b-11e8-b101-c85b76da12f5','tmp','87','2018-09-22 22:51:10','7777','-84.103833'),('11beb3ef-bea1-11e8-b101-c85b76da12f5','af76a1ba-be2b-11e8-b101-c85b76da12f5','tmp','87','2018-09-22 19:52:36','9.935605','-84.103833'),('255db953-beba-11e8-b101-c85b76da12f5','af76a1ba-be2b-11e8-b101-c85b76da12f5','tmp','87','2018-09-22 22:52:06','9.956911','-84.103833'),('2756135b-bea0-11e8-b101-c85b76da12f5','af76a1ba-be2b-11e8-b101-c85b76da12f5','tmp','87','2018-09-22 19:46:02','9.935605','-84.103833'),('4644e724-bea1-11e8-b101-c85b76da12f5','af76a1ba-be2b-11e8-b101-c85b76da12f5','tmp','87','2018-09-22 19:54:04','9.935605','-84.103833'),('4bf23bf0-be9d-11e8-b101-c85b76da12f5','6d9729f6-be34-11e8-b101-c85b76da12f5','tmp','25','2018-09-22 19:25:35','9.971913','-84.119572'),('8b9f7c9a-bea1-11e8-b101-c85b76da12f5','af76a1ba-be2b-11e8-b101-c85b76da12f5','tmp','87','2018-09-22 19:56:00','9.935605','-84.103833'),('8c48eafc-bebd-11e8-b101-c85b76da12f5','af76a1ba-be2b-11e8-b101-c85b76da12f5','tmp','87','2018-09-22 23:16:27','9.967312','-84120086'),('8cc62c13-be9f-11e8-b101-c85b76da12f5','af76a1ba-be2b-11e8-b101-c85b76da12f5','tmp','87','2018-09-22 19:41:43','9.935605','-84.103833'),('a12ae523-bebd-11e8-b101-c85b76da12f5','af76a1ba-be2b-11e8-b101-c85b76da12f5','tmp','87','2018-09-22 23:17:02','9.967312','-84.120086'),('a4d9ec28-beba-11e8-b101-c85b76da12f5','af76a1ba-be2b-11e8-b101-c85b76da12f5','tmp','87','2018-09-22 22:55:40','9.967502','-84.120720'),('bcc82a3c-bea0-11e8-b101-c85b76da12f5','af76a1ba-be2b-11e8-b101-c85b76da12f5','tmp','87','2018-09-22 19:50:13','9.935605','-84.103833'),('cd4044ad-be9a-11e8-b101-c85b76da12f5','af76a1ba-be2b-11e8-b101-c85b76da12f5','tmp','87','2018-09-22 19:07:44','9.932922','-84.097657'),('cddc29cf-be9a-11e8-b101-c85b76da12f5','af76a1ba-be2b-11e8-b101-c85b76da12f5','tmp','87','2018-09-22 19:07:45','9.953127','-84.111272'),('ce20910e-be9a-11e8-b101-c85b76da12f5','af76a1ba-be2b-11e8-b101-c85b76da12f5','tmp','87','2018-09-22 19:07:45','9.926180','-84.088934'),('e3cc0a7f-bebd-11e8-b101-c85b76da12f5','af76a1ba-be2b-11e8-b101-c85b76da12f5','HR','87','2018-09-22 23:18:54','9.967312','-84.120082'),('f08128c3-bebd-11e8-b101-c85b76da12f5','af76a1ba-be2b-11e8-b101-c85b76da12f5','TMP','87','2018-09-22 23:19:15','9.967312','-84.120098');
/*!40000 ALTER TABLE `variables` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2018-09-23 12:31:56
