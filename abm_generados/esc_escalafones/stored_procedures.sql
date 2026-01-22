INSERT INTO `stored_procedures` (`spnombre`, `spoperacion`, `sptabla`, `spsqlstring`, `spobserv`, `ultmodusuario`, `ultmodfecha`) VALUES('sel_Escalafones_xIdEscalafon','SEL','ESCALAFONES','SELECT * FROM Escalafones WHERE IdEscalafon="#pIdEscalafon#"',NULL,'1',NOW());
INSERT INTO `stored_procedures` (`spnombre`, `spoperacion`, `sptabla`, `spsqlstring`, `spobserv`, `ultmodusuario`, `ultmodfecha`) VALUES('del_Escalafones_xIdEscalafon','DEL','ESCALAFONES','DELETE FROM Escalafones WHERE IdEscalafon="#pIdEscalafon#"',NULL,'1',NOW());
INSERT INTO `stored_procedures` (`spnombre`, `spoperacion`, `sptabla`, `spsqlstring`, `spobserv`, `ultmodusuario`, `ultmodfecha`) VALUES('upd_Escalafones_Estado_xIdEscalafon','UPD','ESCALAFONES','UPDATE Escalafones SET Estado="#pEstado#" WHERE IdEscalafon="#pIdEscalafon#"',NULL,'1',NOW());
INSERT INTO `stored_procedures` (`spnombre`, `spoperacion`, `sptabla`, `spsqlstring`, `spobserv`, `ultmodusuario`, `ultmodfecha`) VALUES('ins_Escalafones','INS','ESCALAFONES','INSERT INTO Escalafones (IdEscalafonExterno,
    Nombre,
    Descripcion,
    IdRegimenSalarial,
    Estado,
    AltaFecha,
    AltaUsuario,
    UltimaModificacionUsuario,
    UltimaModificacionFecha)
VALUES ("#pIdEscalafonExterno#",
    "#pNombre#",
    "#pDescripcion#",
    "#pIdRegimenSalarial#",
    "#pEstado#",
    "#pAltaFecha#",
    "#pAltaUsuario#",
    "#pUltimaModificacionUsuario#",
    "#pUltimaModificacionFecha#")',NULL,'1',NOW());
INSERT INTO `stored_procedures` (`spnombre`, `spoperacion`, `sptabla`, `spsqlstring`, `spobserv`, `ultmodusuario`, `ultmodfecha`) VALUES('upd_Escalafones_xIdEscalafon','UPD','ESCALAFONES','UPDATE Escalafones
SET IdEscalafonExterno="#pIdEscalafonExterno#",
    Nombre="#pNombre#",
    Descripcion="#pDescripcion#",
    IdRegimenSalarial="#pIdRegimenSalarial#",
    Estado="#pEstado#",
    UltimaModificacionUsuario="#pUltimaModificacionUsuario#",
    UltimaModificacionFecha="#pUltimaModificacionFecha#"
WHERE IdEscalafon="#pIdEscalafon#"',NULL,'1',NOW());
INSERT INTO `stored_procedures` (`spnombre`, `spoperacion`, `sptabla`, `spsqlstring`, `spobserv`, `ultmodusuario`, `ultmodfecha`) VALUES('sel_Escalafones_busqueda_avanzada','SEL','ESCALAFONES','SELECT * FROM Escalafones
WHERE
IF("#pxIdEscalafon#",IdEscalafon="#pIdEscalafon#",1)
AND
IF("#pxIdEscalafonExterno#",IdEscalafonExterno="#pIdEscalafonExterno#",1)
AND
IF("#pxNombre#", LCASE(Nombre) LIKE LCASE("%#pNombre#%"),1)
AND
IF("#pxDescripcion#", LCASE(Descripcion) LIKE LCASE("%#pDescripcion#%"),1)
AND
IF("#pxIdRegimenSalarial#",IdRegimenSalarial="#pIdRegimenSalarial#",1)
AND
IF("#pxEstado#",Estado IN (#pEstado#),1)
AND
IF("#pxAltaFecha#",AltaFecha="#pAltaFecha#",1)
AND
IF("#pxAltaUsuario#",AltaUsuario="#pAltaUsuario#",1)
AND
IF("#pxUltimaModificacionUsuario#",UltimaModificacionUsuario="#pUltimaModificacionUsuario#",1)
AND
IF("#pxUltimaModificacionFecha#",UltimaModificacionFecha="#pUltimaModificacionFecha#",1)
ORDER BY #porderby# #plimit#',NULL,'1',NOW());
