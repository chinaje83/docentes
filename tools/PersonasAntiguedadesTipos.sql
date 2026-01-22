/*
SQLyog Ultimate v13.1.1 (64 bit)
MySQL - 8.0.31-google : Database - dev_rh_pba_docente
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
/*Table structure for table `PersonasAntiguedadesTipos` */

DROP TABLE IF EXISTS `PersonasAntiguedadesTipos`;

CREATE TABLE `PersonasAntiguedadesTipos` (
  `IdAntiguedadTipo` int NOT NULL AUTO_INCREMENT,
  `Nombre` varchar(255) COLLATE latin1_spanish_ci NOT NULL,
  `Estado` smallint DEFAULT NULL,
  `AltaFecha` datetime DEFAULT NULL,
  `AltaUsuario` int DEFAULT NULL,
  `UltimaModificacionesFecha` datetime DEFAULT NULL,
  `UltimaModificacionUsuario` int DEFAULT NULL,
  `SoloLiquidacion` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`IdAntiguedadTipo`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
