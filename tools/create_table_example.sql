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
