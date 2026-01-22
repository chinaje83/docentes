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
/*Table structure for table `Escalafones` */

DROP TABLE IF EXISTS `Escalafones`;

CREATE TABLE `Escalafones` (
  `IdEscalafon` int unsigned NOT NULL AUTO_INCREMENT COMMENT 'Id autonumerico de la tabla',
  `IdEscalafonExterno` int NOT NULL COMMENT 'Id Externo',
  `Nombre` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL COMMENT 'Nombre del Campo',
  `Descripcion` varchar(255) DEFAULT NULL COMMENT 'Descripcion del campo',
  `IdRegimenSalarial` int DEFAULT NULL,
  `Estado` smallint NOT NULL COMMENT 'Estado 10 Activo 90 Baja',
  `AltaFecha` datetime NOT NULL COMMENT 'Fecha de alta del registro',
  `AltaUsuario` int NOT NULL COMMENT 'Usuario que dio de alta el registro',
  `UltimaModificacionUsuario` int NOT NULL COMMENT 'Ultimo usuario que realizo la modificacion del registro',
  `UltimaModificacionFecha` datetime NOT NULL COMMENT 'Ultima fecha de modificacion del registro',
  PRIMARY KEY (`IdEscalafon`)
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=latin1;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
