INSERT INTO `stored_procedures` (`spnombre`, `spoperacion`, `sptabla`, `spsqlstring`, `spobserv`, `ultmodusuario`, `ultmodfecha`) VALUES('sel_PersonasAntiguedadesTipos_xIdAntiguedadTipo','SEL','PERSONASANTIGUEDADESTIPOS','SELECT * FROM PersonasAntiguedadesTipos WHERE IdAntiguedadTipo="#pIdAntiguedadTipo#"',NULL,'1',NOW());
INSERT INTO `stored_procedures` (`spnombre`, `spoperacion`, `sptabla`, `spsqlstring`, `spobserv`, `ultmodusuario`, `ultmodfecha`) VALUES('del_PersonasAntiguedadesTipos_xIdAntiguedadTipo','DEL','PERSONASANTIGUEDADESTIPOS','DELETE FROM PersonasAntiguedadesTipos WHERE IdAntiguedadTipo="#pIdAntiguedadTipo#"',NULL,'1',NOW());
INSERT INTO `stored_procedures` (`spnombre`, `spoperacion`, `sptabla`, `spsqlstring`, `spobserv`, `ultmodusuario`, `ultmodfecha`) VALUES('upd_PersonasAntiguedadesTipos_Estado_xIdAntiguedadTipo','UPD','PERSONASANTIGUEDADESTIPOS','UPDATE PersonasAntiguedadesTipos SET Estado="#pEstado#" WHERE IdAntiguedadTipo="#pIdAntiguedadTipo#"',NULL,'1',NOW());
INSERT INTO `stored_procedures` (`spnombre`, `spoperacion`, `sptabla`, `spsqlstring`, `spobserv`, `ultmodusuario`, `ultmodfecha`) VALUES('ins_PersonasAntiguedadesTipos','INS','PERSONASANTIGUEDADESTIPOS','INSERT INTO PersonasAntiguedadesTipos (Nombre,
    Estado,
    AltaFecha,
    AltaUsuario,
    UltimaModificacionesFecha,
    UltimaModificacionUsuario,
    SoloLiquidacion)
VALUES ("#pNombre#",
    "#pEstado#",
    "#pAltaFecha#",
    "#pAltaUsuario#",
    "#pUltimaModificacionesFecha#",
    "#pUltimaModificacionUsuario#",
    "#pSoloLiquidacion#")',NULL,'1',NOW());
INSERT INTO `stored_procedures` (`spnombre`, `spoperacion`, `sptabla`, `spsqlstring`, `spobserv`, `ultmodusuario`, `ultmodfecha`) VALUES('upd_PersonasAntiguedadesTipos_xIdAntiguedadTipo','UPD','PERSONASANTIGUEDADESTIPOS','UPDATE PersonasAntiguedadesTipos
SET Nombre="#pNombre#",
    Estado="#pEstado#",
    UltimaModificacionesFecha="#pUltimaModificacionesFecha#",
    UltimaModificacionUsuario="#pUltimaModificacionUsuario#",
    SoloLiquidacion="#pSoloLiquidacion#"
WHERE IdAntiguedadTipo="#pIdAntiguedadTipo#"',NULL,'1',NOW());
INSERT INTO `stored_procedures` (`spnombre`, `spoperacion`, `sptabla`, `spsqlstring`, `spobserv`, `ultmodusuario`, `ultmodfecha`) VALUES('sel_PersonasAntiguedadesTipos_busqueda_avanzada','SEL','PERSONASANTIGUEDADESTIPOS','SELECT * FROM PersonasAntiguedadesTipos
WHERE
IF("#pxIdAntiguedadTipo#",IdAntiguedadTipo="#pIdAntiguedadTipo#",1)
AND
IF("#pxNombre#", LCASE(Nombre) LIKE LCASE("%#pNombre#%"),1)
AND
IF("#pxEstado#",Estado IN (#pEstado#),1)
AND
IF("#pxAltaFecha#",AltaFecha="#pAltaFecha#",1)
AND
IF("#pxAltaUsuario#",AltaUsuario="#pAltaUsuario#",1)
AND
IF("#pxUltimaModificacionesFecha#",UltimaModificacionesFecha="#pUltimaModificacionesFecha#",1)
AND
IF("#pxUltimaModificacionUsuario#",UltimaModificacionUsuario="#pUltimaModificacionUsuario#",1)
AND
IF("#pxSoloLiquidacion#",SoloLiquidacion="#pSoloLiquidacion#",1)
ORDER BY #porderby# #plimit#',NULL,'1',NOW());
