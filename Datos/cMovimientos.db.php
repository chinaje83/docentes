<?php

namespace Bigtree\Datos;

use accesoBDLocal;
use ManejoErrores;

abstract class Movimientos {
    use ManejoErrores;

    /** @var accesoBDLocal */
    protected $conexion;
    /** @var mixed */
    protected $formato;
    /** @var array */
    protected $error;

    /**
     * Constructor de la clase
     *
     * @param accesoBDLocal $conexion
     * @param mixed         $formato
     */
    function __construct(accesoBDLocal $conexion, $formato) {
        $this->conexion = &$conexion;
        $this->formato = $formato;
    }

    /**
     * Destructor de la clase
     */
    function __destruct() {}


    protected function BuscarxCodigo(array $datos, &$resultado, ?int &$numfilas) {
        $spnombre = "sel_LogMovimientos_xId";
        $sparam = [
            'pId' => $datos['Id'],
        ];

        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            $this->setError(400, 'Error al Buscar por Codigo de Este Movimiento. ');
            return false;
        }

        return true;
    }

    protected function BusquedaAvanzada(array $datos, &$resultado, ?int &$numfilas) {

        $spnombre = "sel_LogMovimientos_busqueda_avanzada";
        $sparam = [
            'pBasePersonas' => BASEDATOS_PERSONAS,
            'pxId' => $datos['xId'],
            'pId' => $datos['Id'],
            'pxFechaEjecucion' => $datos['xFechaEjecucion'],
            'pFechaEjecucion' => $datos['FechaEjecucion'],
            'pxFechaDesde' => $datos['xFechaDesde'],
            'pFechaDesde' => $datos['FechaDesde'],
            'pxFechaHasta' => $datos['xFechaHasta'],
            'pFechaHasta' => $datos['FechaHasta'],
            'pxIdEstado' => $datos['xIdEstado'],
            'pIdEstado' => $datos['IdEstado'],
            'pxIdTipoLiquidacion' => $datos['xIdTipoLiquidacion'],
            'pIdTipoLiquidacion' => $datos['IdTipoLiquidacion'],
            'pxIdEscuela' => $datos['xIdEscuela'],
            'pIdEscuela' => $datos['IdEscuela'],
            'plimit' => $datos['limit'],
            'porderby' => $datos['orderby'],
        ];

        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            $this->setError(400, 'Error al realizar la búsqueda avanzada. ');
            return false;
        }

        return true;
    }

    protected function BusquedaAvanzadaCantidad(array $datos, &$resultado, ?int &$numfilas) {

        $spnombre = "sel_LogMovimientos_busqueda_avanzada_cantidad";
        $sparam = [
            'pBasePersonas' => BASEDATOS_PERSONAS,
            'pxId' => $datos['xId'],
            'pId' => $datos['Id'],
            'pxFechaEjecucion' => $datos['xFechaEjecucion'],
            'pFechaEjecucion' => $datos['FechaEjecucion'],
            'pxFechaDesde' => $datos['xFechaDesde'],
            'pFechaDesde' => $datos['FechaDesde'],
            'pxFechaHasta' => $datos['xFechaHasta'],
            'pFechaHasta' => $datos['FechaHasta'],
            'pxIdEstado' => $datos['xIdEstado'],
            'pIdEstado' => $datos['IdEstado'],
            'pxIdTipoLiquidacion' => $datos['xIdTipoLiquidacion'],
            'pIdTipoLiquidacion' => $datos['IdTipoLiquidacion'],
            'pxIdEscuela' => $datos['xIdEscuela'],
            'pIdEscuela' => $datos['IdEscuela'],
        ];

        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            $this->setError(400, 'Error al realizar la búsqueda avanzada. ');
            return false;
        }

        return true;
    }

    protected function BusquedaAvanzadaxEscuela(array $datos, &$resultado, ?int &$numfilas) {

        $spnombre = "sel_LogMovimientos_busqueda_avanzada_xIdEscuela";
        $sparam = [
            'pIdLogMovimientos' => $datos['IdLogMovimientos'],
            'pxIdEscuela' => $datos['xIdEscuela'],
            'pIdEscuela' => $datos['IdEscuela'],
            'plimit' => $datos['limit'],
            'porderby' => $datos['orderby'],
        ];

        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            $this->setError(400, 'Error al realizar la búsqueda avanzada. ');
            return false;
        }

        return true;
    }

    protected function BusquedaAvanzadaNovedadesCantidad(array $datos, &$resultado, ?int &$numfilas) {

        $baseLicencias = BASEDATOSLICENCIAS;

        //$spnombre = 'sel_LogMovimientosNovedades_busqueda_avanzada_cantidad';
        $sparam = [];
        $sql_sp = "SELECT COUNT(DISTINCT LMN.`Id`) AS cantidad
         FROM LogMovimientosNovedades AS LMN
            LEFT JOIN EscuelasPuestos EP ON EP.IdPuesto = LMN.IdPuesto
            LEFT JOIN $baseLicencias.Licencias L ON L.Id = LMN.IdSubServicioLicTGE
        WHERE 1=1 ";
        if (isset($datos['IdLogMovimientos']) && $datos['IdLogMovimientos'] != "") {
            $sql_sp .= ' AND LMN.IdLogMovimientos = "#pIdLogMovimientos#"';
            $sparam['pIdLogMovimientos'] = $datos['IdLogMovimientos'];
        }
        if (isset($datos['Id']) && $datos['Id'] != "") {
            $sql_sp .= ' AND LMN.Id = "#pId#"';
            $sparam['pId'] = $datos['Id'];
        }
        if (isset($datos['IdPlaza']) && $datos['IdPlaza'] != "") {
            $sql_sp .= ' AND (LMN.IdPlaza = "#pIdPlaza#" OR EP.IdPuestoRaiz="#pIdPlaza#"  OR UCASE(TRIM(LMN.CodigoPuesto))=UCASE(TRIM("#pIdPlaza#")) OR LMN.IdServicioTGE = "#pIdPlaza#")';
            $sparam['pIdPlaza'] = $datos['IdPlaza'];
        }
        if (isset($datos['Cuil']) && $datos['Cuil'] != "") {
            $sql_sp .= ' AND LMN.Cuil LIKE "%#pCuil#%"';
            $sparam['pCuil'] = $datos['Cuil'];
        }
        if (isset($datos['IdMovimiento']) && $datos['IdMovimiento'] != "") {
            $sql_sp .= ' AND LMN.IdMovimiento = "#pIdMovimiento#"';
            $sparam['pIdMovimiento'] = $datos['IdMovimiento'];
        }

        if (isset($datos['IdServicioTGE']) && $datos['IdServicioTGE'] != "") {
            $sql_sp .= ' AND LMN.IdMovimiento = "#IdServicioTGE#"';
            $sparam['pIdServicioTGE'] = $datos['IdServicioTGE'];
        }
        if (isset($datos['IdSubServicioNovTGE']) && $datos['IdSubServicioNovTGE'] != "") {
            $sql_sp .= ' AND LMN.IdSubServicioNovTGE = "#pIdSubServicioNovTGE#"';
            $sparam['pIdSubServicioNovTGE'] = $datos['IdSubServicioNovTGE'];
        }
        if (isset($datos['IdSubServicioLicTGE']) && $datos['IdSubServicioLicTGE'] != "") {
            $sql_sp .= ' AND LMN.IdSubServicioLicTGE = "#pIdSubServicioLicTGE#"';
            $sparam['pIdSubServicioLicTGE'] = $datos['IdSubServicioLicTGE'];
        }
        if (isset($datos['IdMotivo']) && $datos['IdMotivo'] != "") {
            $sql_sp .= ' AND L.IdMotivo = "#pIdMotivo#"';
            $sparam['pIdMotivo'] = $datos['IdMotivo'];
        }
        if (isset($datos['FechaAlta']) && $datos['FechaAlta'] != "") {
            $sql_sp .= ' AND LMN.FechaAlta between "#pFechaAlta#" and "#pFechaAlta# 23:59:59"';
            $sparam['pFechaAlta'] = $datos['FechaAlta'];
        }

        if (isset($datos['FechaBaja']) && $datos['FechaBaja'] != "") {
            $sql_sp .= ' AND LMN.FechaBaja between "#pFechaBaja#" and "#pFechaBaja# 23:59:59"';
            $sparam['pFechaBaja'] = $datos['FechaBaja'];
        }

        if (isset($datos['IdEstado']) && $datos['IdEstado'] != "") {
            $sql_sp .= ' AND LMN.IdEstado IN (#pIdEstado#)';
            $sparam['pIdEstado'] = $datos['IdEstado'];
        }

        if (isset($datos['NotIdEstado']) && $datos['NotIdEstado'] != "") {
            $sql_sp .= ' AND LMN.IdEstado NOT IN (#pNotIdEstado#)';
            $sparam['pNotIdEstado'] = $datos['NotIdEstado'];
        }

        if (isset($datos['IdEscuela']) && $datos['IdEscuela'] != "") {
            $sql_sp .= ' AND LMN.IdEscuela = "#pIdEscuela#"';
            $sparam['pIdEscuela'] = $datos['IdEscuela'];
        }

        if (isset($datos['IdNivel']) && $datos['IdNivel'] != "") {
            $sql_sp .= ' AND LMN.IdNivel IN (#pIdNivel#)';
            $sparam['pIdNivel'] = $datos['IdNivel'];
        }

        if (isset($datos['IdRegion']) && $datos['IdRegion'] != "") {
            $sql_sp .= ' AND LMN.IdRegion IN (#pIdRegion#)';
            $sparam['pIdRegion'] = $datos['IdRegion'];
        }

        if (isset($datos['MesPeriodo']) && $datos['MesPeriodo'] != "") {
            $sql_sp .= ' AND  LMN.Mes="#pMesPeriodo#"';
            $sparam['pMesPeriodo'] = $datos['MesPeriodo'];
        }


        if (isset($datos['AnioPeriodo']) && $datos['AnioPeriodo'] != "") {
            $sql_sp .= ' AND  LMN.Anio="#pAnioPeriodo#"';
            $sparam['pAnioPeriodo'] = $datos['AnioPeriodo'];
        }

        if (isset($datos['IdTipoDocumento']) && $datos['IdTipoDocumento'] != "") {
            $sql_sp .= ' AND   LMN.`IdTipoDocumento` IN (#pIdTipoDocumento#)';
            $sparam['pIdTipoDocumento'] = $datos['IdTipoDocumento'];
        }

        if (isset($datos['ConGoceSueldo']) && $datos['ConGoceSueldo'] != "") {
            $sql_sp .= ' AND  IF("#pConGoceSueldo#" = 1, LMN.ConGoceSueldo = 1, (LMN.ConGoceSueldo = 0 OR LMN.ConGoceSueldo IS NULL))';
            $sparam['pConGoceSueldo'] = $datos['ConGoceSueldo'];
        }

        if (isset($datos['IdExcepcionTipo']) && $datos['IdExcepcionTipo'] != "") {
            $sql_sp .= ' AND   LMN.IdExcepcionTipo = "#pIdExcepcionTipo#"';
            $sparam['pIdExcepcionTipo'] = $datos['IdExcepcionTipo'];
        }


        if (isset($datos['FechaMovimientoDesde']) && $datos['FechaMovimientoDesde'] != "") {
            $sql_sp .= ' AND LMN.FechaMovimiento >= "#pFechaMovimientoDesde#"';
            $sparam['pFechaMovimientoDesde'] = $datos['FechaMovimientoDesde'];
        }

        if (isset($datos['FechaMovimientoHasta']) && $datos['FechaMovimientoHasta'] != "") {
            $sql_sp .= ' AND LMN.FechaMovimiento <= "#pFechaMovimientoHasta#"';
            $sparam['pFechaMovimientoHasta'] = $datos['FechaMovimientoHasta'];
        }

        #reemplazo los parametros
        $sql_resultado = "";
        try {
            $this->conexion->_ReemplazarParametros($sql_sp, $sparam, $sql_resultado);
        } catch (Exception $e) {
            throw new ExcepcionDB("Error al reemplazar parámetros en la consulta.", 1);
        }

        if (!$this->conexion->ejecutarSQL($sql_resultado, "SEL", $resultado, $numfilas, $errno)) {
            $this->setError(400, 'Error al realizar la búsqueda avanzada cantidad. ');
            return false;
        }


        return true;
    }

    protected function BusquedaAvanzadaNovedades(array $datos, &$resultado, ?int &$numfilas) {

        $baseLicencias = BASEDATOSLICENCIAS;

        $sparam = [
            'plimit' => $datos['limit'],
            'porderby' => $datos['orderby'],
        ];
        $sql_sp = "SELECT EP.IdPuestoRaiz,LMN.*,
            LMT.Nombre,LMT.NombreCorto AS CodigoMovimiento,
            LMNE.Nombre AS NombreEstado,
            CASE WHEN FechaAlta IS NOT NULL THEN FechaAlta ELSE FechaBaja END AS Fecha,

             R.Codigo AS CodigoRevista,

             LMN.CUIL,
             LMN.NombreCompleto   AS NombreCompleto,
             EP.CodigoPuesto AS CUPOF,
             EP.CantHoras AS CantHoras,
             EP.CantModulos AS CantModulos,
             E.Nombre AS NombreEscuela,
             E.ClaveUnicaEscuela AS ClaveUnicaEscuela,
             E.CodigoEscuela AS CodigoEscuela,
             T.Turno,
             T.Descripcion AS NombreTurno,
             EAGS.NombreSeccion,
             GA.Nombre    AS NombreGrado,
             GA.NombreCorto AS Grado,
             DT.Nombre AS NombreDocumento,
             DT.NombreCorto AS DescripcionDocumento
        ,LMN.`IdNivel`, N.`Nombre` AS Nivel,
        LMN.IdServicioTGE  AS IdPofa,
        EPP.CodigoLiquidador  AS Secuencia ,
        LMT.`SolicitaCodigoLiquidacion`,
        LMN.IdExcepcionTipo,
        concat(U.Nombre,' ',U.Apellido) AS UsuarioModificacion,
        LMN.UltimaModificacionFecha AS FechaModificacion
         FROM LogMovimientosNovedades AS LMN
         INNER JOIN LogMovimientosTipos AS LMT  ON LMN.IdMovimiento = LMT.Id
         INNER JOIN LogMovimientosNovedadesEstado AS LMNE ON LMN.IdEstado = LMNE.Id
         INNER JOIN Escuelas E ON E.IdEscuela = LMN.IdEscuela
         LEFT JOIN Documentos D ON D.IdDocumento = LMN.IdSubServicioNovTGE
         LEFT JOIN DocumentosTipos DT ON DT.IdTipoDocumento = D.IdTipoDocumento
         LEFT JOIN Revistas R ON R.IdRevista = LMN.IdRevistaNueva
         LEFT JOIN $baseLicencias.Licencias L ON L.Id = LMN.IdSubServicioLicTGE
         LEFT JOIN EscuelasPuestos EP ON EP.IdPuesto = LMN.IdPuesto
        LEFT JOIN EscuelasPuestosPersonas EPP ON EPP.IdPofa=LMN.`IdServicioTGE`
         LEFT JOIN EscuelasTurnos ET ON ET.IdEscuelaTurno = EP.IdEscuelaTurno
         LEFT JOIN Turnos T ON T.IdTurno = ET.IdTurno
         LEFT JOIN EscuelasAGSecciones EAGS ON EAGS.IdSeccion = EP.IdSeccion
         LEFT JOIN EscuelasTurnosAG ETAG ON ETAG.IdEscuelaTurnoAnioGrado = EAGS.IdEscuelaTurnoAnioGrado
         LEFT JOIN GradosAnios GA ON GA.IdGradoAnio = ETAG.IdGradoAnio
         LEFT JOIN Niveles N ON N.IdNivel=LMN.IdNivel
         LEFT JOIN Usuarios U ON U.IdUsuario=LMN.UltimaModificacionUsuario
        WHERE 1=1 ";
        if (isset($datos['IdLogMovimientos']) && $datos['IdLogMovimientos'] != "") {
            $sql_sp .= ' AND LMN.IdLogMovimientos = "#pIdLogMovimientos#"';
            $sparam['pIdLogMovimientos'] = $datos['IdLogMovimientos'];
        }
        if (isset($datos['Id']) && $datos['Id'] != "") {
            $sql_sp .= ' AND LMN.Id = "#pId#"';
            $sparam['pId'] = $datos['Id'];
        }
        if (isset($datos['IdPlaza']) && $datos['IdPlaza'] != "") {
            $sql_sp .= ' AND (LMN.IdPlaza = "#pIdPlaza#" OR EP.IdPuestoRaiz="#pIdPlaza#"  OR UCASE(TRIM(LMN.CodigoPuesto))=UCASE(TRIM("#pIdPlaza#")) OR LMN.IdServicioTGE = "#pIdPlaza#")';
            $sparam['pIdPlaza'] = $datos['IdPlaza'];
        }
        if (isset($datos['Cuil']) && $datos['Cuil'] != "") {
            $sql_sp .= ' AND LMN.Cuil LIKE "%#pCuil#%"';
            $sparam['pCuil'] = $datos['Cuil'];
        }
        if (isset($datos['IdMovimiento']) && $datos['IdMovimiento'] != "") {
            $sql_sp .= ' AND LMN.IdMovimiento = "#pIdMovimiento#"';
            $sparam['pIdMovimiento'] = $datos['IdMovimiento'];
        }

        if (isset($datos['IdServicioTGE']) && $datos['IdServicioTGE'] != "") {
            $sql_sp .= ' AND LMN.IdMovimiento = "#IdServicioTGE#"';
            $sparam['pIdServicioTGE'] = $datos['IdServicioTGE'];
        }
        if (isset($datos['IdSubServicioNovTGE']) && $datos['IdSubServicioNovTGE'] != "") {
            $sql_sp .= ' AND LMN.IdSubServicioNovTGE = "#pIdSubServicioNovTGE#"';
            $sparam['pIdSubServicioNovTGE'] = $datos['IdSubServicioNovTGE'];
        }
        if (isset($datos['IdSubServicioLicTGE']) && $datos['IdSubServicioLicTGE'] != "") {
            $sql_sp .= ' AND LMN.IdSubServicioLicTGE = "#pIdSubServicioLicTGE#"';
            $sparam['pIdSubServicioLicTGE'] = $datos['IdSubServicioLicTGE'];
        }

        if (isset($datos['IdMotivo']) && $datos['IdMotivo'] != "") {
            $sql_sp .= ' AND L.IdMotivo = "#pIdMotivo#"';
            $sparam['pIdMotivo'] = $datos['IdMotivo'];
        }

        if (isset($datos['FechaAlta']) && $datos['FechaAlta'] != "") {
            $sql_sp .= ' AND LMN.FechaAlta between "#pFechaAlta#" and "#pFechaAlta# 23:59:59"';
            $sparam['pFechaAlta'] = $datos['FechaAlta'];
        }

        if (isset($datos['FechaBaja']) && $datos['FechaBaja'] != "") {
            $sql_sp .= ' AND LMN.FechaBaja between "#pFechaBaja#" and "#pFechaBaja# 23:59:59"';
            $sparam['pFechaBaja'] = $datos['FechaBaja'];
        }

        if (isset($datos['IdEstado']) && $datos['IdEstado'] != "") {
            $sql_sp .= ' AND LMN.IdEstado IN (#pIdEstado#)';
            $sparam['pIdEstado'] = $datos['IdEstado'];
        }

        if (isset($datos['NotIdEstado']) && $datos['NotIdEstado'] != "") {
            $sql_sp .= ' AND LMN.IdEstado NOT IN (#pNotIdEstado#)';
            $sparam['pNotIdEstado'] = $datos['NotIdEstado'];
        }

        if (isset($datos['IdEscuela']) && $datos['IdEscuela'] != "") {
            $sql_sp .= ' AND LMN.IdEscuela = "#pIdEscuela#"';
            $sparam['pIdEscuela'] = $datos['IdEscuela'];
        }

        if (isset($datos['IdNivel']) && $datos['IdNivel'] != "") {
            $sql_sp .= ' AND LMN.IdNivel IN (#pIdNivel#)';
            $sparam['pIdNivel'] = $datos['IdNivel'];
        }

        if (isset($datos['IdRegion']) && $datos['IdRegion'] != "") {
            $sql_sp .= ' AND LMN.IdRegion IN (#pIdRegion#)';
            $sparam['pIdRegion'] = $datos['IdRegion'];
        }

        if (isset($datos['MesPeriodo']) && $datos['MesPeriodo'] != "") {
            #$sql_sp.=' AND  MONTH(LMN.FechaLiquidacion)="#pMesPeriodo#"';
            $sql_sp .= ' AND  LMN.Mes="#pMesPeriodo#"';
            $sparam['pMesPeriodo'] = $datos['MesPeriodo'];
        }


        if (isset($datos['AnioPeriodo']) && $datos['AnioPeriodo'] != "") {
            #$sql_sp.=' AND  YEAR(LMN.FechaLiquidacion)="#pAnioPeriodo#"';
            $sql_sp .= ' AND  LMN.Anio="#pAnioPeriodo#"';
            $sparam['pAnioPeriodo'] = $datos['AnioPeriodo'];
        }

        if (isset($datos['IdTipoDocumento']) && $datos['IdTipoDocumento'] != "") {
            $sql_sp .= ' AND   LMN.`IdTipoDocumento` IN (#pIdTipoDocumento#)';
            $sparam['pIdTipoDocumento'] = $datos['IdTipoDocumento'];
        }

        if (isset($datos['ConGoceSueldo']) && $datos['ConGoceSueldo'] != "") {
            $sql_sp .= ' AND  IF("#pConGoceSueldo#" = 1, LMN.ConGoceSueldo = 1, (LMN.ConGoceSueldo = 0 OR LMN.ConGoceSueldo IS NULL))';
            $sparam['pConGoceSueldo'] = $datos['ConGoceSueldo'];
        }

        if (isset($datos['IdExcepcionTipo']) && $datos['IdExcepcionTipo'] != "") {
            $sql_sp .= ' AND   LMN.IdExcepcionTipo = "#pIdExcepcionTipo#"';
            $sparam['pIdExcepcionTipo'] = $datos['IdExcepcionTipo'];
        }


        if (isset($datos['FechaMovimientoDesde']) && $datos['FechaMovimientoDesde'] != "") {
            $sql_sp .= ' AND LMN.FechaMovimiento >= "#pFechaMovimientoDesde#"';
            $sparam['pFechaMovimientoDesde'] = $datos['FechaMovimientoDesde'];
        }

        if (isset($datos['FechaMovimientoHasta']) && $datos['FechaMovimientoHasta'] != "") {
            $sql_sp .= ' AND LMN.FechaMovimiento <= "#pFechaMovimientoHasta#"';
            $sparam['pFechaMovimientoHasta'] = $datos['FechaMovimientoHasta'];
        }


        $sql_sp .= ' GROUP BY LMN.Id';
        $sql_sp .= ' ORDER BY #porderby# #plimit#';

        #reemplazo los parametros
        $sql_resultado = "";
        try {
            $this->conexion->_ReemplazarParametros($sql_sp, $sparam, $sql_resultado);
        } catch (Exception $e) {
            throw new ExcepcionDB("Error al reemplazar parámetros en la consulta.", 1);
        }

        //echo $sql_resultado;die();
        if (!$this->conexion->ejecutarSQL($sql_resultado, "SEL", $resultado, $numfilas, $errno)) {
            $this->setError(400, 'Error al realizar la búsqueda avanzada cantidad. ');
            return false;
        }


        return true;
    }

    protected function BusquedaAvanzadaNovedadesCSV(array $datos, &$resultado, ?int &$numfilas) {

        $spnombre = 'sel_LogMovimientosNovedades_busqueda_avanzada_csv';
        $sparam = [
            'pBaseLicencias' => BASEDATOSLICENCIAS,
            'pBasePersonas' => BASEDATOS_PERSONAS,
            /*
            'pxIdLogMovimientos' => $datos['xIdLogMovimientos'],
            'pIdLogMovimientos' => $datos['IdLogMovimientos'],*/
            'pId' => $datos['Id'],
            'pxId' => $datos['xId'],
            'pIdPlaza' => $datos['IdPlaza'],
            'pxIdPlaza' => $datos['xIdPlaza'],
            'pCodigoPuesto' => $datos["CodigoPuesto"],
            'pCuil' => $datos['Cuil'],
            'pxCuil' => $datos['xCuil'],
            'pIdMovimiento' => $datos['IdMovimiento'],
            'pxIdMovimiento' => $datos['xIdMovimiento'],
            /*
            'pIdServicioTGE' => $datos['IdServicioTGE'],
            'pxIdServicioTGE' => $datos['xIdServicioTGE'],*/
            'pIdSubServicioNovTGE' => $datos['IdSubServicioNovTGE'],
            'pxIdSubServicioNovTGE' => $datos['xIdSubServicioNovTGE'],
            'pIdSubServicioLicTGE' => $datos['IdSubServicioLicTGE'],
            'pxIdSubServicioLicTGE' => $datos['xIdSubServicioLicTGE'],
            'pFechaAlta' => $datos['FechaAlta'],
            'pxFechaAlta' => $datos['xFechaAlta'],
            'pFechaBaja' => $datos['FechaBaja'],
            'pxFechaBaja' => $datos['xFechaBaja'],
            'pIdEstado' => $datos['IdEstado'],
            'pxIdEstado' => $datos['xIdEstado'],
            'pNotIdEstado' => $datos['NotIdEstado'],
            'pxNotIdEstado' => $datos['xNotIdEstado'],
            'pIdEscuela' => $datos['IdEscuela'],
            'pxIdEscuela' => $datos['xIdEscuela'],
            'pIdNivel' => $datos['IdNivel'],
            'pxIdNivel' => $datos['xIdNivel'],
            'pIdRegion' => $datos['IdRegion'],
            'pxIdRegion' => $datos['xIdRegion'],
            'pMesPeriodo' => $datos['MesPeriodo'],
            'pxMesPeriodo' => $datos['xMesPeriodo'],
            'pAnioPeriodo' => $datos['AnioPeriodo'],
            'pxAnioPeriodo' => $datos['xAnioPeriodo'],
            'pIdTipoDocumento' => $datos['IdTipoDocumento'],
            'pxIdTipoDocumento' => $datos['xIdTipoDocumento'],
            'pxConGoceSueldo' => $datos["xConGoceSueldo"],
            'pConGoceSueldo' => $datos["ConGoceSueldo"],
            'pxIdExcepcionTipo' => $datos["xIdExcepcionTipo"],
            'pIdExcepcionTipo' => $datos["IdExcepcionTipo"],
            'pxIdMotivo' => $datos["xIdMotivo"],
            'pIdMotivo' => $datos["IdMotivo"],
            'pxFechaMovimientoDesde' => $datos["xFechaMovimientoDesde"],
            'pFechaMovimientoDesde' => $datos["FechaMovimientoDesde"],
            'pxFechaMovimientoHasta' => $datos["xFechaMovimientoHasta"],
            'pFechaMovimientoHasta' => $datos["FechaMovimientoHasta"],
            'plimit' => $datos['limit'],
            'porderby' => $datos['orderby'],
            "pgroupby" => $datos["groupby"],
        ];

        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            $this->setError(400, 'Error al realizar la busqueda avanzada para el CSV.');
            return false;
        }

        return true;
    }

    protected function buscarComboEscuelasxMovimiento(array $datos, &$resultado, ?int &$numfilas): bool {

        $spnombre = "sel_LogMovimientosNovedades_Escuelas_combo";
        $sparam = [
            'pxIdLogMovimientos' => $datos['xIdLogMovimientos'],
            'pIdLogMovimientos' => $datos['IdLogMovimientos'],
        ];

        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            $this->setError(400, 'Error al buscar escuelas del movimiento. ');
            return false;
        }

        return true;
    }


    protected function buscarMovPendientes(array $datos, &$resultado, ?int &$numfilas): bool {

        $spnombre = "sel_LogMovimientos_Pendientes_busqueda_avanzada";
        $sparam = [
            'pBaseLicencias' => BASEDATOSLICENCIAS,
            'pBasePersonas' => BASEDATOS_PERSONAS,
            'pId' => $datos['Id'],
            'pxId' => $datos['xId'],
            'pIdPlaza' => $datos['IdPlaza'],
            'pxIdPlaza' => $datos['xIdPlaza'],
            'pCuil' => $datos['Cuil'],
            'pxCuil' => $datos['xCuil'],
            'pIdMovimiento' => $datos['IdMovimiento'],
            'pxIdMovimiento' => $datos['xIdMovimiento'],
            'pIdServicioTGE' => $datos['IdServicioTGE'],
            'pxIdServicioTGE' => $datos['xIdServicioTGE'],
            'pIdSubServicioNovTGE' => $datos['IdSubServicioNovTGE'],
            'pxIdSubServicioNovTGE' => $datos['xIdSubServicioNovTGE'],
            'pIdSubServicioLicTGE' => $datos['IdSubServicioLicTGE'],
            'pxIdSubServicioLicTGE' => $datos['xIdSubServicioLicTGE'],
            'pFechaAlta' => $datos['FechaAlta'],
            'pxFechaAlta' => $datos['xFechaAlta'],
            'pFechaBaja' => $datos['FechaBaja'],
            'pxFechaBaja' => $datos['xFechaBaja'],
            'pIdEstado' => $datos['IdEstado'],
            'pxIdEstado' => $datos['xIdEstado'],
            'pIdEscuela' => $datos['IdEscuela'],
            'pxIdEscuela' => $datos['xIdEscuela'],
            'pPeriodo' => $datos['Periodo'],
            'pxPeriodo' => $datos['xPeriodo'],
            'plimit' => $datos['limit'],
            'pgroupby' => $datos['groupby'],
            'porderby' => $datos['orderby'],
        ];

        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            $this->setError(400, 'Error al buscar el movimiento pendiente. ');
            return false;
        }

        return true;
    }


    protected function buscarMovPendientesCantidad(array $datos, &$resultado, ?int &$numfilas): bool {

        $spnombre = "sel_LogMovimientos_Pendientes_busqueda_avanzada_cantidad";
        $sparam = [
            'pBaseLicencias' => BASEDATOSLICENCIAS,
            'pBasePersonas' => BASEDATOS_PERSONAS,
            'pId' => $datos['Id'],
            'pxId' => $datos['xId'],
            'pIdPlaza' => $datos['IdPlaza'],
            'pxIdPlaza' => $datos['xIdPlaza'],
            'pCuil' => $datos['Cuil'],
            'pxCuil' => $datos['xCuil'],
            'pIdMovimiento' => $datos['IdMovimiento'],
            'pxIdMovimiento' => $datos['xIdMovimiento'],
            'pIdServicioTGE' => $datos['IdServicioTGE'],
            'pxIdServicioTGE' => $datos['xIdServicioTGE'],
            'pIdSubServicioNovTGE' => $datos['IdSubServicioNovTGE'],
            'pxIdSubServicioNovTGE' => $datos['xIdSubServicioNovTGE'],
            'pIdSubServicioLicTGE' => $datos['IdSubServicioLicTGE'],
            'pxIdSubServicioLicTGE' => $datos['xIdSubServicioLicTGE'],
            'pFechaAlta' => $datos['FechaAlta'],
            'pxFechaAlta' => $datos['xFechaAlta'],
            'pFechaBaja' => $datos['FechaBaja'],
            'pxFechaBaja' => $datos['xFechaBaja'],
            'pIdEstado' => $datos['IdEstado'],
            'pxIdEstado' => $datos['xIdEstado'],
            'pIdEscuela' => $datos['IdEscuela'],
            'pxIdEscuela' => $datos['xIdEscuela'],
            'pPeriodo' => $datos['Periodo'],
            'pxPeriodo' => $datos['xPeriodo'],
            'pgroupby' => $datos['groupby'],
        ];

        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            $this->setError(400, 'Error al buscar el movimiento pendiente. ');
            return false;
        }

        return true;
    }


    protected function buscarParaTXT(array $datos, &$resultado, ?int &$numfilas) {

        $spnombre = 'sel_LogMovimientosNovedades_txt';
        $sparam = [
            'pIdLogMovimientos' => $datos['IdLogMovimientos'],
            'pIdEstado' => $datos['IdEstado'],
        ];

        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            $this->setError(400, 'Error al realizar la búsqueda de datos txt. ');
            return false;
        }

        return true;
    }


    protected function BuscarTipoMovimientoCombo(&$resultado, ?int &$numfilas) {
        $spnombre = "sel_LogMovimientosTipos_Combo";
        $sparam = [];

        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            $this->setError(400, 'Error al Buscar los tipos de Movimientos. ');
            return false;
        }

        return true;
    }

    protected function BuscarEstadoCombo($datos, &$resultado, ?int &$numfilas) {
        $spnombre = "sel_LogMovimientosNovedadesEstado_Combo";
        $sparam = [
            'pCambioManual' => $datos['CambioManual'],
            'pxCambioManual' => $datos['xCambioManual'],
        ];

        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            $this->setError(400, 'Error al buscar estados de movimientos. ');
            return false;
        }

        return true;
    }


    protected function BuscarPeriodoCombo(&$resultado, ?int &$numfilas) {
        $spnombre = 'sel_Periodos_combo';
        $sparam = [


        ];
        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, "Error al buscar roles por combo. ", ["archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__], ["formato" => $this->formato]);
            return false;
        }

        return true;
    }


    protected function buscarTipoLiquidacion(&$resultado, ?int &$numfilas) {

        $spnombre = "sel_TiposLiquidacion_combo";
        $sparam = [];

        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            $this->setError(400, 'Error al buscar los tipos de liquidaciones.');
            return false;
        }

        return true;
    }

    protected function BuscarMovimientoEstadoCombo(&$resultado, ?int &$numfilas) {
        $spnombre = "sel_LogMovimientosEstado_Combo";
        $sparam = [];

        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            $this->setError(400, 'Error al Buscar los tipos de Movimientos. ');
            return false;
        }

        return true;
    }


    protected function InsertarLog(array $datos, ?int &$codigoInsertado): bool {

        $spnombre = 'ins_LogMovimientos';
        $sparam = [
            'pIdUsuario' => $datos['IdUsuario'],
            'pFechaEjecucion' => $datos['FechaEjecucion'],
            'pFechaDesde' => $datos['FechaDesde'],
            'pFechaHasta' => $datos['FechaHasta'],
            'pIdEstado' => $datos['IdEstado'],
            'pIdPeriodo' => $datos['IdPeriodo'],
            'pIdTipoLiquidacion' => $datos['IdTipoLiquidacion'],
        ];

        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            $this->setError(400, 'Error al insertar log de movimiento');
            return false;
        }

        $codigoInsertado = $this->conexion->UltimoCodigoInsertado();
        return true;
    }

    protected function InsertarLogNovedad(array $datos, ?int &$codigoInsertado): bool {

        $spnombre = 'ins_LogMovimientosNovedades';
        $sparam = [
            'pIdLogMovimientos' => $datos['IdLogMovimientos'],
            'pOrden' => $datos['Orden'],
            'pPeriodo' => $datos['Periodo'],
            'pCuil' => $datos['Agente'],
            'pIdMovimiento' => $datos['idMovimiento'],
            'pIdPlaza' => $datos['idPlaza'],
            'pIdEscuela' => $datos['IdEscuela'],
            'pIdServicioTGE' => $datos['idServicioTGE'],
            'pIdSubServicioNovTGE' => $datos['idSubServicioNovTGE'],
            'pCargoSalarial' => $datos['CargoSalarial'],
            'pIdSituacionRevista' => $datos['idSituacionRevista'],
            'pFechaAlta' => $datos['FechaAlta'],
            'pFechaBaja' => $datos['FechaBaja'],
            'pHoras' => $datos['Horas'],
            'pIdLicencia' => $datos['idLicencia'],
            'pIdServicioTGEQueSuple' => $datos['idServicioTGEQueSuple'],
            'pIdServicioTGERelacionado' => $datos['idServicioTGERelacionado'],
            'pCausaAlta' => $datos['CausaAlta'],
            'pCausaBaja' => $datos['CausaBaja'],
            'pFechaCarga' => $datos['FechaCarga'],
            'pUsuarioCarga' => $datos['UsuarioCarga'],
            'pIdRevistaAntigua' => $datos['IdRevistaAntigua'],
            'pIdRevistaNueva' => $datos['IdRevistaNueva'],
            'pIdSubServicioLicTGE' => $datos['idSubServicioLicTGE'],
            'pFechaMovimiento' => $datos['FechaMovimiento'],
            'pIdEstado' => $datos['IdEstado'], //$datos['IdEstado'],
            'pErrorCod' => 1, //$datos['ErrorCod'],

            'pIdPersona' => $datos['IdPersona'],
            'pIdTipoDocumento' => $datos['IdTipoDocumento'],
            'pIdArticulo' => $datos['IdArticulo'],
            'pBajaLiquidacion' => $datos['BajaLiquidacion'],
            'pIdPuesto' => $datos['IdPuesto'],
            'pIdPuestoDestino' => $datos['IdPuestoDestino'],
            'pFechaReintegro' => $datos['FechaReintegro'],

            'pIdEstadoNovedad' => $datos['IdEstadoNovedad'],
            'pEstadoNovedad' => $datos['EstadoNovedad'],
            'pFechaLiquidacion' => $datos['FechaLiquidacion'],


            'pCodigoPuesto' => $datos['CodigoPuesto'],
            'pIdExcepcionTipo' => $datos['IdExcepcionTipo'],
            'pConGoceSueldo' => $datos['ConGoceSueldo'],
            'pIdNivel' => $datos['IdNivel'],
            'pIdRegion' => $datos['IdRegion'],
            'pCodigoLiquidador' => $datos['CodigoLiquidador'],
            'pIdDocumentoTipo' => $datos['IdDocumentoTipo'],
            'pNombreCompleto' => $datos['NombreCompleto']

            , 'pUltimaModificacionFecha' => $datos['UltimaModificacionFecha']
            , 'pUltimaModificacionUsuario' => $datos['UltimaModificacionUsuario']
            , 'pAltaUsuario' => $datos['AltaUsuario']
            , 'pAltaFecha' => $datos['AltaFecha'],
        ];

        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            $this->setError(400, 'Error al insertar movimiento novedad1');
            return false;
        }

        $codigoInsertado = $this->conexion->UltimoCodigoInsertado();
        return true;
    }


    protected function InsertarLogNovedadBulkTmp(array $datos): bool {

        $spnombre = 'ins_LogMovimientosNovedades_Bulk_Tmp';
        $sparam = [
            'pIdLogMovimientos' => $datos['IdLogMovimientos'],
        ];

        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            $this->setError(400, 'Error al insertar movimiento novedad2');
            return false;
        }

        return true;
    }

    protected function CambiarEstado(array $datos): bool {

        $spnombre = 'upd_LogMovimientos_IdEstado_xId';
        $sparam = [
            'pId' => $datos['Id'],
            'pIdEstado' => $datos['IdEstado'],

        ];

        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            $this->setError(400, 'Error al insertar movimiento novedad3');
            return false;
        }

        return true;
    }

    protected function CambiarEstadoLogNovedad(array $datos): bool {

        $spnombre = 'upd_LogMovimientosNovedades_IdEstado_xIdLogMovimientos';
        $sparam = [
            'pIdLogMovimientos' => $datos['IdLogMovimientos'],
            'pIdEstado' => $datos['IdEstado'],

        ];

        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            $this->setError(400, 'Error al insertar movimiento novedad');
            return false;
        }

        return true;
    }

    protected function CambiarEstadoLogNovedadxId(array $datos): bool {

        $spnombre = 'upd_LogMovimientosNovedades_IdEstado_xId';
        $sparam = [
            'pId' => $datos['Id'],
            'pIdEstado' => $datos['IdEstado'],
            'pObservaciones' => $datos["Observaciones"],
            'pUltimaModificacionFecha' => $datos['UltimaModificacionFecha'],
            'pUltimaModificacionUsuario' => $datos['UltimaModificacionUsuario'],

        ];

        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            $this->setError(400, 'Error al insertar movimiento novedad');
            return false;
        }

        return true;
    }

    protected function ActualizarLicencias_IdLiquidacionAlta_IdLiquidacionBaja_xIdLogMovimientos(array $datos): bool {

        $spnombre = 'upd_Licencias_IdLiquidacionAlta_IdLiquidacionBaja_xIdLogMovimientos';
        $sparam = [
            'pBaseLicencias' => BASEDATOSLICENCIAS,
            'pIdLogMovimientos' => $datos['IdLogMovimientos'],
            'pIdLogMovimientosBuscar' => $datos['IdLogMovimientosBuscar'],

        ];

        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            $this->setError(400, 'Error al actualizar alta y baja movimiento licencias');
            return false;
        }

        return true;
    }

    protected function ActualizarDocumentos_IdLiquidacionAlta_IdLiquidacionBaja_xIdLogMovimientos(array $datos): bool {

        $spnombre = 'upd_Documentos_IdLiquidacionAlta_IdLiquidacionBaja_xIdLogMovimientos';
        $sparam = [
            'pIdLogMovimientos' => $datos['IdLogMovimientos'],
            'pIdLogMovimientosBuscar' => $datos['IdLogMovimientosBuscar'],

        ];


        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            $this->setError(400, 'Error al actualizar alta y baja movimiento documentos');
            return false;
        }

        return true;
    }

    protected function ActualizarLicencias_BajaLiquidacion_xIdLogMovimientos(array $datos): bool {

        $spnombre = 'upd_Licencias_BajaLiquidacion_xIdLogMovimientos';
        $sparam = [
            'pBaseLicencias' => BASEDATOSLICENCIAS,
            'pIdLogMovimientos' => $datos['IdLogMovimientos'],
            'pBajaLiquidacion' => $datos['BajaLiquidacion'],
            'pBajaLiquidacionActual' => $datos['BajaLiquidacionActual'],

        ];

        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            $this->setError(400, 'Error al actualizar BajaLiquidacion de licencias liquidadas');
            return false;
        }

        return true;
    }

    protected function ActualizarNovedades_BajaLiquidacion_xIdLogMovimientos(array $datos): bool {

        $spnombre = 'upd_Documentos_BajaLiquidacion_xIdLogMovimientos';
        $sparam = [
            'pIdLogMovimientos' => $datos['IdLogMovimientos'],
            'pBajaLiquidacion' => $datos['BajaLiquidacion'],
            'pBajaLiquidacionActual' => $datos['BajaLiquidacionActual'],

        ];

        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            $this->setError(400, 'Error al actualizar BajaLiquidacion de licencias liquidadas');
            return false;
        }

        return true;
    }

    protected function CambiarEstadoMovimientosXFechas(array $datos): bool {

        $spnombre = 'upd_LogMovimientosNovedades_Estado_xFechas';
        $sparam = [
            'pEstadoInicial' => $datos['EstadoInicial'],
            'pEstadoFinal' => $datos['EstadoFinal'],
            'pFechaHasta' => $datos['FechaHasta'],

        ];

        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            $this->setError(400, 'Error al cambiar de estado movimientos novedades');
            return false;
        }

        return true;
    }

    protected function AnularMovimientosLicenciasPendientes(array $datos): bool {

        $spnombre = 'upd_LogMovimientosNovedades_AnularLicenciasPendientes_xIdLogMovimientos';
        $sparam = [
            'pIdLogMovimientos' => $datos['IdLogMovimientos'],
            'pEstadoAnular' => $datos['EstadoAnular'],
        ];
        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            $this->setError(400, 'Error al cambiar de estado movimientos novedades');
            return false;
        }

        return true;
    }

    protected function AnularMovimientosPendientes(array $datos): bool {

        $spnombre = 'upd_LogMovimientosNovedades_AnularPendientes_xIdLogMovimientos';
        $sparam = [
            'pIdLogMovimientos' => $datos['IdLogMovimientos'],
            'pEstadoAnular' => $datos['EstadoAnular'],
        ];
        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            $this->setError(400, 'Error al cambiar de estado movimientos novedades');
            return false;
        }

        return true;
    }

    protected function PasarManualMovimientosYaLiquidados(array $datos): bool {

        $spnombre = 'upd_LogMovimientosNovedades_LiquidarManual';
        $sparam = [
            'pEstadoInicial' => $datos['EstadoInicial'],
            'pEstadoFinal' => $datos['EstadoFinal'],
            'pIdEstadosIniciales' => $datos['IdEstadosIniciales'],
            'pIdLogMovimientos' => $datos['IdLogMovimientos'],
        ];

        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            $this->setError(400, 'Error al cambiar de estado movimientos novedades3');
            return false;
        }

        return true;
    }


    protected function BuscarDocumentosLiquidacion(array $datos, &$resultado, ?int &$numfilas): bool {
        $spPeriodo = "sel_documentos_a_liquidar_xIdDocumento";
        $sparam = [
            'pBaseDatosPersonas' => BASEDATOS_PERSONAS,
            'pIdDocumento' => $datos['IdDocumento'],
        ];

        if (!$this->conexion->ejecutarStoredProcedure($spPeriodo, $sparam, $resultado, $numfilas, $errno)) {
            FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, "Error al realizar la búsqueda avanzada. ", ["archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__], ["formato" => $this->formato]);
            return false;
        }
        return true;
    }

    protected function BuscarLicenciasLiquidacion(array $datos, &$resultado, ?int &$numfilas): bool {
        $spPeriodo = "sel_licencias_a_liquidar_xLicencia";
        $sparam = [
            'pBaseDatosPersonas' => BASEDATOS_PERSONAS,
            'pBaseDatosLicencias' => BASEDATOSLICENCIAS,
            #'pExcluir_Escuela' => $datos['Excluir_Escuela'],
            'pIdLicencia' => $datos['IdLicencia'],
            'pxSoloSinGoce' => $datos['xSoloSinGoce'],
            'pIdMovimiento' => $datos['IdMovimiento'],
            'pxIdMovimiento' => $datos['xIdMovimiento'],
            #'pSoloSinGoce'=>$datos['SoloSinGoce']
        ];


        if (!$this->conexion->ejecutarStoredProcedure($spPeriodo, $sparam, $resultado, $numfilas, $errno)) {
            FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, "Error al realizar la búsqueda avanzada. ", ["archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__], ["formato" => $this->formato]);
            return false;
        }
        return true;
    }

    /*
    protected function ActualizarMovimientosEstadoxIdPofaxIdMovimiento(array $datos): bool {
        $spnombre = "upd_Estado_LogMovimientosNovedades_xIdDocumento_xIdPeriodo";
        $sparam = [
            'pIdEstado' => $datos['IdEstado'],
            'pIdPeriodo'=> $datos['IdPeriodo'],
            'pIdDocumento' => $datos['IdDocumento']
        ];

        if (!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno)) {
            FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, "Error al actualizar movimientos por estado ", ["archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__], ["formato" => $this->formato]);
            return false;
        }
        return true;
    }*/
    protected function ActualizarMovimientosEstadoIdPeriodoxLicencia(array $datos): bool {
        $spnombre = "upd_Estado_LogMovimientosNovedades_xLicencia_xIdPeriodo";
        $sparam = [
            'pIdEstado' => $datos['IdEstado'],
            'pxIdPeriodo' => $datos['xIdPeriodo'],
            'pIdPeriodo' => $datos['IdPeriodo'],
            'pIdLicencia' => $datos['IdLicencia'],
            'pxIdMovimiento' => $datos['xIdMovimiento'],
            'pIdMovimiento' => $datos['IdMovimiento'],
        ];

        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, "Error al actualizar movimientos por estado ", ["archivo" => _FILE, "funcion" => __FUNCTION, "linea" => __LINE_], ["formato" => $this->formato]);
            return false;
        }
        return true;
    }

    protected function ActualizarMovimientosEstadoxIdDocumentoxIdMovimiento(array $datos): bool {
        $spnombre = "upd_Estado_LogMovimientosNovedades_xIdDocumento_xIdMovimiento";
        $sparam = [
            'pIdEstado' => $datos['IdEstado'],
            'pIdDocumento' => $datos['IdDocumento'],
            'pxIdMovimiento' => $datos['xIdMovimiento'],
            'pIdMovimiento' => $datos['IdMovimiento'],
        ];


        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            $this->setError(400, "Error al actualizar movimientos por estado ");
            return false;
        }
        return true;
    }

    protected function ActualizarMovimientosEstadoxIdPofaxIdMovimiento(array $datos): bool {
        $spnombre = 'upd_LogMovimientosNovedades_AnularPendientes_xIdMovimiento_xIdPofa';
        $sparam = [
            'pIdPofa' => $datos['IdPofa'],
            'pIdEstado' => $datos['IdEstado'],
            'pIdMovimiento' => $datos['IdMovimiento'],
            'pxIdTipoDocumento' => $datos['xIdTipoDocumento'],
            'pIdTipoDocumento' => $datos['IdTipoDocumento'],
        ];
        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            $this->setError(400, 'Error al actualizar movimientos por estado por IdPofa ');
            return false;
        }
        return true;
    }

    protected function BuscarPofaLiquidacion(array $datos, &$resultado, ?int &$numfilas): bool {
        $spPeriodo = "sel_documentos_a_liquidar_xIdPofa";
        $sparam = [
            'pIdPofa' => $datos['IdPofa'],
            'pIdMovimiento' => $datos['IdMovimiento'],
        ];

        if (!$this->conexion->ejecutarStoredProcedure($spPeriodo, $sparam, $resultado, $numfilas, $errno)) {
            $this->setError(400, 'Error al buscar movimientos por IdPofa ');
            return false;
        }
        return true;
    }

    protected function BuscarMovimientosxLicencia(array $datos, &$resultado, ?int &$numfilas): bool {
        $spPeriodo = 'sel_log_movimientos_novedades_xIdLicencia_xIdEstado';
        $sparam = [
            'pIdLicencia' => $datos['IdLicencia'],
            'pxIdEstado' => $datos['xIdEstado'],
            'pIdEstado' => $datos['IdEstado'],
        ];

        if (!$this->conexion->ejecutarStoredProcedure($spPeriodo, $sparam, $resultado, $numfilas, $errno)) {
            FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, 'Error al realizar la búsqueda de Pofa para liquidar. ', ['archivo' => __FILE__, 'funcion' => __FUNCTION__, 'linea' => __LINE__], ['formato' => $this->formato]);
            return false;
        }
        return true;
    }

    protected function ActualizarEstadoxIdLicencia(array $datos, &$resultado, ?int &$numfilas): bool {
        $spPeriodo = 'upd_LogMovimientosNovedades_xIdLicencia_xIdEstado';
        $sparam = [
            'pIdLicencia' => $datos['IdLicencia'],
            'pIdEstado' => $datos['IdEstado'],
            'pUltimaModificacionUsuario' => $_SESSION['usuariocod'],
            'pUltimaModificacionFecha' => date('Y-m-d H:i:s'),
        ];

        if (!$this->conexion->ejecutarStoredProcedure($spPeriodo, $sparam, $resultado, $numfilas, $errno)) {
            FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, 'Error al realizar la búsqueda de Pofa para liquidar. ', ['archivo' => __FILE__, 'funcion' => __FUNCTION__, 'linea' => __LINE__], ['formato' => $this->formato]);
            return false;
        }
        return true;
    }

    protected function AnularMovimientosLiquidados(array $datos): bool {
        $spnombre = "upd_Estado_LogMovimientosNovedades_xIdLicencia_xIdDocumento";
        $sparam = [
            'pIdEstadoAnulado' => $datos['IdEstadoAnulado'],
            'pIdEstadoLiquidado' => $datos['IdEstadoLiquidado'],
            'pIdLicencia' => $datos['IdLicencia'],
            'pxIdLicencia' => $datos['xIdLicencia'],
            'pIdDocumento' => $datos['IdDocumento'],
            'pxIdDocumento' => $datos['xIdDocumento'],
            'pxIdMovimiento' => $datos['xIdMovimiento'],
            'pIdMovimiento' => $datos['IdMovimiento'],
            'pUltimaModificacionFecha' => $datos['UltimaModificacionFecha'],
            'pUltimaModificacionUsuario' => $datos['UltimaModificacionUsuario'],
        ];

        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, "Error al actualizar movimientos por estado ", ["archivo" => _FILE, "funcion" => __FUNCTION, "linea" => __LINE_], ["formato" => $this->formato]);
            return false;
        }
        return true;
    }

}
