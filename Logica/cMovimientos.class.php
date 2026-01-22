<?php

namespace Bigtree\Logica;
include(DIR_CLASES_DB . 'cMovimientos.db.php');

use accesoBDLocal;
use Bigtree\ExcepcionLogica;
use Bigtree\Datos\Movimientos as MovimientosDB;
use Validaciones;
use DateTime;
use Exception;
use FuncionesPHPLocal;


class Movimientos extends MovimientosDB {

    use Validaciones;

    /**
     * Valor del campo nulo en la exportaci n
     */
    const NULL = ''; // 'NULL'; //'\N'; //"\0"; //

    /**
     * Formato de periodo
     */
    const FORMATO_PERIODO = 'Ym';

    /**
     * Formato de fecha
     */
    const FORMATO_FECHA = 'd-m-Y'; // 'd/m/Y'; // 'Y-m-d'; //

    /**
     * Formato de fecha y hora
     */
    const FORMATO_FECHA_HORA = self::FORMATO_FECHA . ' H:i:s.000';

    /**
     * Separador de campos
     */
    const SEPARADOR = "\t"; //';'; //',';  //

    /**
     * Caracter que encierra el texto escapado
     */
    const ENCLOSURE = '"';

    /**
     * Escape de caracteres
     */
    const ESCAPE = "\\"; //'^'; //

    const DECIMALES = 2;
    const SEPARADOR_DECIMAL = '.'; //','; //
    const SEPARADOR_MILES = ' '; //','; //


//    const FECHA_DESDE = 'fifth day last month midnight'; //  'first day of this month'; // '2000-01-01'; //
//    const FECHA_HASTA = 'fifth day midnight'; //  'last day of this month'; // '2021-12-31'; //

//    const FECHA_DESDE = 'this day last month midnight';
//    const FECHA_HASTA = 'today midnight';

    const FECHA_DESDE = '2021-07-16';
    const FECHA_HASTA = '2021-08-15';

    const EXTENSION = 'txt'; //'csv'; //

    const MOSTRAR_ENCABEZADO = false;

    const IGNORAR_HORAS = false;

    const CARGOS_SIN_HORAS = [];

    const TIPO_LICENCIA = 'lic';
    const TIPO_NOVEDAD = 'nov';

    /** Constructor de la clase
     *
     * @param accesoBDLocal $conexion
     * @param mixed         $formato
     */

    function __construct(accesoBDLocal $conexion, $formato = FMT_TEXTO) {
        parent::__construct($conexion, $formato);
    }

    /**
     * Destructor de la clase
     */
    function __destruct() {
        parent::__destruct();
    }


    public function BuscarTipoMovimientoCombo(&$resultado, &$numfilas): bool {
        return parent::BuscarTipoMovimientoCombo($resultado, $numfilas);
    }

    public function BuscarEstadoCombo($datos, &$resultado, &$numfilas): bool {
        $sparam = [
            'CambioManual' => "",
            'xCambioManual' => 0,
        ];

        if (isset($datos['CambioManual']) && $datos['CambioManual'] != "") {
            $sparam['CambioManual'] = $datos['CambioManual'];
            $sparam['xCambioManual'] = 1;
        }
        return parent::BuscarEstadoCombo($sparam, $resultado, $numfilas);
    }

    public function BuscarPeriodoCombo(&$resultado, &$numfilas): bool {
        return parent::BuscarPeriodoCombo($resultado, $numfilas);
    }

    public function buscarTipoLiquidacion(&$resultado, &$numfilas): bool {
        return parent::buscarTipoLiquidacion($resultado, $numfilas);
    }

    public function BuscarMovimientoEstadoCombo(&$resultado, &$numfilas): bool {
        return parent::BuscarMovimientoEstadoCombo($resultado, $numfilas);
    }

    public function BuscarxCodigo(array $datos, &$resultado, &$numfilas): bool {
        return parent::BuscarxCodigo($datos, $resultado, $numfilas);
    }

    public function buscarParaTXT($datos, &$resultado, &$numfilas): bool {
        return parent::buscarParaTXT($datos, $resultado, $numfilas);
    }


    public function BusquedaAvanzada($datos, &$resultado, &$numfilas): bool {
        $sparam = [
            'xId' => 0,
            'Id' => '',
            'xFechaEjecucion' => 0,
            'FechaEjecucion' => '',
            'xFechaDesde' => 0,
            'FechaDesde' => '',
            'xFechaHasta' => 0,
            'FechaHasta' => '',
            'xIdEstado' => 0,
            'IdEstado' => "-1",
            'xIdTipoLiquidacion' => 0,
            'IdTipoLiquidacion' => "-1",
            'xIdEscuela' => 0,
            'IdEscuela' => "-1",
            'limit' => '',
            'orderby' => 'Id DESC',

        ];
        if (isset($datos['Id']) && $datos['Id'] != "") {
            $sparam['Id'] = $datos['Id'];
            $sparam['xId'] = 1;
        }

        if (isset($datos['FechaEjecucion']) && $datos['FechaEjecucion'] != "") {
            $sparam['FechaEjecucion'] = $datos['FechaEjecucion'];
            $sparam['xFechaEjecucion'] = 1;
        }

        if (isset($datos['FechaDesde']) && $datos['FechaDesde'] != "") {
            $sparam['FechaDesde'] = $datos['FechaDesde'];
            $sparam['xFechaDesde'] = 1;
        }

        if (isset($datos['FechaHasta']) && $datos['FechaHasta'] != "") {
            $sparam['FechaHasta'] = $datos['FechaHasta'];
            $sparam['xFechaHasta'] = 1;
        }

        if (isset($datos['IdEstado']) && $datos['IdEstado'] != "") {
            $sparam['IdEstado'] = $datos['IdEstado'];
            $sparam['xIdEstado'] = 1;
        }

        if (isset($datos['IdTipoLiquidacion']) && $datos['IdTipoLiquidacion'] != "") {
            $sparam['IdTipoLiquidacion'] = $datos['IdTipoLiquidacion'];
            $sparam['xIdTipoLiquidacion'] = 1;
        }

        if (isset($datos['IdEscuela']) && $datos['IdEscuela'] != "") {
            $sparam['IdEscuela'] = $datos['IdEscuela'];
            $sparam['xIdEscuela'] = 1;
        }

        if (isset($datos['orderby']) && $datos['orderby'] != "")
            $sparam['orderby'] = $datos['orderby'];

        if (isset($datos['limit']) && $datos['limit'] != "")
            $sparam['limit'] = $datos['limit'];


        if (!parent::BusquedaAvanzada($sparam, $resultado, $numfilas))
            return false;

        return true;
    }

    public function BusquedaAvanzadaCantidad($datos, &$resultado, &$numfilas): bool {
        $sparam = [
            'xId' => 0,
            'Id' => '',
            'xFechaEjecucion' => 0,
            'FechaEjecucion' => '',
            'xFechaDesde' => 0,
            'FechaDesde' => '',
            'xFechaHasta' => 0,
            'FechaHasta' => '',
            'xIdEstado' => 0,
            'IdEstado' => "-1",
            'xIdTipoLiquidacion' => 0,
            'IdTipoLiquidacion' => "-1",
            'xIdEscuela' => 0,
            'IdEscuela' => "-1",

        ];
        if (isset($datos['Id']) && $datos['Id'] != "") {
            $sparam['Id'] = $datos['Id'];
            $sparam['xId'] = 1;
        }

        if (isset($datos['FechaEjecucion']) && $datos['FechaEjecucion'] != "") {
            $sparam['FechaEjecucion'] = $datos['FechaEjecucion'];
            $sparam['xFechaEjecucion'] = 1;
        }

        if (isset($datos['FechaDesde']) && $datos['FechaDesde'] != "") {
            $sparam['FechaDesde'] = $datos['FechaDesde'];
            $sparam['xFechaDesde'] = 1;
        }

        if (isset($datos['FechaHasta']) && $datos['FechaHasta'] != "") {
            $sparam['FechaHasta'] = $datos['FechaHasta'];
            $sparam['xFechaHasta'] = 1;
        }

        if (isset($datos['IdEstado']) && $datos['IdEstado'] != "") {
            $sparam['IdEstado'] = $datos['IdEstado'];
            $sparam['xIdEstado'] = 1;
        }

        if (isset($datos['IdTipoLiquidacion']) && $datos['IdTipoLiquidacion'] != "") {
            $sparam['IdTipoLiquidacion'] = $datos['IdTipoLiquidacion'];
            $sparam['xIdTipoLiquidacion'] = 1;
        }

        if (isset($datos['IdEscuela']) && $datos['IdEscuela'] != "") {
            $sparam['IdEscuela'] = $datos['IdEscuela'];
            $sparam['xIdEscuela'] = 1;
        }

        if (!parent::BusquedaAvanzadaCantidad($sparam, $resultado, $numfilas))
            return false;

        return true;
    }

    public function BusquedaAvanzadaxEscuela($datos, &$resultado, &$numfilas): bool {
        $sparam = [
            'IdLogMovimientos' => $datos['IdLogMovimientos'],
            'xIdEscuela' => 0,
            'IdEscuela' => "-1",
            'limit' => '',
            'orderby' => 'Nombre DESC',
        ];

        if (isset($datos['IdEscuela']) && $datos['IdEscuela'] != "") {
            $sparam['IdEscuela'] = $datos['IdEscuela'];
            $sparam['xIdEscuela'] = 1;
        }

        if (isset($datos['orderby']) && $datos['orderby'] != "")
            $sparam['orderby'] = $datos['orderby'];

        if (isset($datos['limit']) && $datos['limit'] != "")
            $sparam['limit'] = $datos['limit'];


        if (!parent::BusquedaAvanzadaxEscuela($sparam, $resultado, $numfilas))
            return false;

        return true;
    }

    public function buscarComboEscuelasxMovimiento(array $datos, &$resultado, &$numfilas): bool {
        $sparam = [
            'IdLogMovimientos' => "",
            'xIdLogMovimientos' => 0,
        ];

        if (isset($datos['IdLogMovimientos']) && $datos['IdLogMovimientos'] != "") {
            $sparam['IdLogMovimientos'] = $datos['IdLogMovimientos'];
            $sparam['xIdLogMovimientos'] = 1;
        }
        return parent::buscarComboEscuelasxMovimiento($sparam, $resultado, $numfilas);
    }


    public function buscarMovPendientesCantidad($datos, &$resultado, &$numfilas): bool {
        $sparam = [
            'xId' => 0,
            'Id' => '',
            'xIdPlaza' => 0,
            'IdPlaza' => '',
            'xCuil' => 0,
            'Cuil' => '',
            'xIdMovimiento' => 0,
            'IdMovimiento' => '',
            'xIdServicioTGE' => 0,
            'IdServicioTGE' => '',
            'xIdSubServicioNovTGE' => 0,
            'IdSubServicioNovTGE' => '',
            'xIdSubServicioLicTGE' => 0,
            'IdSubServicioLicTGE' => '',
            'xFechaAlta' => 0,
            'FechaAlta' => '',
            'xFechaBaja' => 0,
            'FechaBaja' => '',
            'xIdEstado' => 0,
            'IdEstado' => '-1',
            'xIdEscuela' => 0,
            'IdEscuela' => '',
            'xPeriodo' => 0,
            'Periodo' => '',
            'xIdNivel' => 0,
            'IdNivel' => '-1',
            'xIdRegion' => 0,
            'IdRegion' => '-1',

            'xIdTipoLicencia' => 0,
            'IdTipoLicencia' => '-1',
            'xIdMotivo' => 0,
            'IdMotivo' => '-1',
            'xIdArticulo' => 0,
            'IdArticulo' => '-1',

            'groupby' => 'GROUP BY LMN.Id',
        ];

        if (isset($datos['Id']) && $datos['Id'] != "") {
            $sparam['Id'] = $datos['Id'];
            $sparam['xId'] = 1;
        }

        if (isset($datos['IdPlaza']) && $datos['IdPlaza'] != "") {
            $sparam['IdPlaza'] = $datos['IdPlaza'];
            $sparam['xIdPlaza'] = 1;
        }

        if (isset($datos['Cuil']) && $datos['Cuil'] != "") {
            $sparam['Cuil'] = $datos['Cuil'];
            $sparam['xCuil'] = 1;
        }

        if (isset($datos['IdMovimiento']) && $datos['IdMovimiento'] != "") {
            $sparam['IdMovimiento'] = $datos['IdMovimiento'];
            $sparam['xIdMovimiento'] = 1;
        }

        if (isset($datos['IdServicioTGE']) && $datos['IdServicioTGE'] != "") {
            $sparam['IdServicioTGE'] = $datos['IdServicioTGE'];
            $sparam['xIdServicioTGE'] = 1;
        }

        if (isset($datos['IdSubServicioNovTGE']) && $datos['IdSubServicioNovTGE'] != "") {
            $sparam['IdSubServicioNovTGE'] = $datos['IdSubServicioNovTGE'];
            $sparam['xIdSubServicioNovTGE'] = 1;
        }

        if (isset($datos['IdSubServicioLicTGE']) && $datos['IdSubServicioLicTGE'] != "") {
            $sparam['IdSubServicioLicTGE'] = $datos['IdSubServicioLicTGE'];
            $sparam['xIdSubServicioLicTGE'] = 1;
        }

        if (isset($datos['FechaAlta']) && $datos['FechaAlta'] != "") {
            $sparam['FechaAlta'] = $datos['FechaAlta'];
            $sparam['xFechaAlta'] = 1;
        }

        if (isset($datos['FechaBaja']) && $datos['FechaBaja'] != "") {
            $sparam['FechaBaja'] = $datos['FechaBaja'];
            $sparam['xFechaBaja'] = 1;
        }

        if (isset($datos['IdEstado']) && $datos['IdEstado'] != "") {
            $sparam['IdEstado'] = $datos['IdEstado'];
            $sparam['xIdEstado'] = 1;
        }

        if (isset($datos['IdEscuela']) && $datos['IdEscuela'] != "") {
            $sparam['IdEscuela'] = $datos['IdEscuela'];
            $sparam['xIdEscuela'] = 1;
        }
        if (isset($datos['Periodo']) && $datos['Periodo'] != "") {
            $sparam['Periodo'] = $datos['Periodo'];
            $sparam['xPeriodo'] = 1;
        }

        if (isset($datos['groupby']) && $datos['groupby'] != "")
            $sparam['groupby'] = $datos['groupby'];

        if (isset($datos['IdNivel']) && $datos['IdNivel'] != "") {
            $sparam['IdNivel'] = $datos['IdNivel'];
            $sparam['xIdNivel'] = 1;
        }

        if (isset($datos['IdRegion']) && $datos['IdRegion'] != "") {
            $sparam['IdRegion'] = $datos['IdRegion'];
            $sparam['xIdRegion'] = 1;
        }

        if (isset($datos['IdTipoLicencia']) && $datos['IdTipoLicencia'] != "") {
            $sparam['IdTipoLicencia'] = $datos['IdTipoLicencia'];
            $sparam['xIdTipoLicencia'] = 1;
        }
        if (isset($datos['IdMotivo']) && $datos['IdMotivo'] != "") {
            $sparam['IdMotivo'] = $datos['IdMotivo'];
            $sparam['xIdMotivo'] = 1;
        }
        if (isset($datos['IdArticulo']) && $datos['IdArticulo'] != "") {
            $sparam['IdArticulo'] = $datos['IdArticulo'];
            $sparam['xIdArticulo'] = 1;
        }


        return parent::buscarMovPendientesCantidad($sparam, $resultado, $numfilas);
    }

    public function buscarMovPendientes(array $datos, &$resultado, &$numfilas): bool {
        $sparam = [
            'xId' => 0,
            'Id' => '',
            'xIdPlaza' => 0,
            'IdPlaza' => '',
            'xCuil' => 0,
            'Cuil' => '',
            'xIdMovimiento' => 0,
            'IdMovimiento' => '',
            'xIdServicioTGE' => 0,
            'IdServicioTGE' => '',
            'xIdSubServicioNovTGE' => 0,
            'IdSubServicioNovTGE' => '',
            'xIdSubServicioLicTGE' => 0,
            'IdSubServicioLicTGE' => '',
            'xFechaAlta' => 0,
            'FechaAlta' => '',
            'xFechaBaja' => 0,
            'FechaBaja' => '',
            'xIdEstado' => 0,
            'IdEstado' => '-1',
            'xIdEscuela' => 0,
            'IdEscuela' => '',
            'xPeriodo' => 0,
            'Periodo' => '',
            'limit' => '',
            'xIdNivel' => 0,
            'IdNivel' => '-1',
            'xIdRegion' => 0,
            'IdRegion' => '-1',

            'xIdTipoLicencia' => 0,
            'IdTipoLicencia' => '-1',
            'xIdMotivo' => 0,
            'IdMotivo' => '-1',
            'xIdArticulo' => 0,
            'IdArticulo' => '-1',

            'groupby' => 'GROUP BY LMN.Id',
            'orderby' => "LMN.Id, IdPuestoRaiz, FechaCarga DESC,  CodigoMovimiento DESC",
        ];

        if (isset($datos['Id']) && $datos['Id'] != "") {
            $sparam['Id'] = $datos['Id'];
            $sparam['xId'] = 1;
        }

        if (isset($datos['IdPlaza']) && $datos['IdPlaza'] != "") {
            $sparam['IdPlaza'] = $datos['IdPlaza'];
            $sparam['xIdPlaza'] = 1;
        }

        if (isset($datos['Cuil']) && $datos['Cuil'] != "") {
            $sparam['Cuil'] = $datos['Cuil'];
            $sparam['xCuil'] = 1;
        }

        if (isset($datos['IdMovimiento']) && $datos['IdMovimiento'] != "") {
            $sparam['IdMovimiento'] = $datos['IdMovimiento'];
            $sparam['xIdMovimiento'] = 1;
        }

        if (isset($datos['IdServicioTGE']) && $datos['IdServicioTGE'] != "") {
            $sparam['IdServicioTGE'] = $datos['IdServicioTGE'];
            $sparam['xIdServicioTGE'] = 1;
        }

        if (isset($datos['IdSubServicioNovTGE']) && $datos['IdSubServicioNovTGE'] != "") {
            $sparam['IdSubServicioNovTGE'] = $datos['IdSubServicioNovTGE'];
            $sparam['xIdSubServicioNovTGE'] = 1;
        }

        if (isset($datos['IdSubServicioLicTGE']) && $datos['IdSubServicioLicTGE'] != "") {
            $sparam['IdSubServicioLicTGE'] = $datos['IdSubServicioLicTGE'];
            $sparam['xIdSubServicioLicTGE'] = 1;
        }

        if (isset($datos['FechaAlta']) && $datos['FechaAlta'] != "") {
            $sparam['FechaAlta'] = $datos['FechaAlta'];
            $sparam['xFechaAlta'] = 1;
        }

        if (isset($datos['FechaBaja']) && $datos['FechaBaja'] != "") {
            $sparam['FechaBaja'] = $datos['FechaBaja'];
            $sparam['xFechaBaja'] = 1;
        }

        if (isset($datos['IdEstado']) && $datos['IdEstado'] != "") {
            $sparam['IdEstado'] = $datos['IdEstado'];
            $sparam['xIdEstado'] = 1;
        }

        if (isset($datos['IdEscuela']) && $datos['IdEscuela'] != "") {
            $sparam['IdEscuela'] = $datos['IdEscuela'];
            $sparam['xIdEscuela'] = 1;
        }
        if (isset($datos['Periodo']) && $datos['Periodo'] != "") {
            $sparam['Periodo'] = $datos['Periodo'];
            $sparam['xPeriodo'] = 1;
        }

        if (isset($datos['groupby']) && $datos['groupby'] != "")
            $sparam['groupby'] = $datos['groupby'];

        if (isset($datos['orderby']) && $datos['orderby'] != "")
            $sparam['orderby'] = $datos['orderby'];

        if (isset($datos['limit']) && $datos['limit'] != "")
            $sparam['limit'] = $datos['limit'];

        if (isset($datos['IdNivel']) && $datos['IdNivel'] != "") {
            $sparam['IdNivel'] = $datos['IdNivel'];
            $sparam['xIdNivel'] = 1;
        }

        if (isset($datos['IdRegion']) && $datos['IdRegion'] != "") {
            $sparam['IdRegion'] = $datos['IdRegion'];
            $sparam['xIdRegion'] = 1;
        }

        if (isset($datos['IdTipoLicencia']) && $datos['IdTipoLicencia'] != "") {
            $sparam['IdTipoLicencia'] = $datos['IdTipoLicencia'];
            $sparam['xIdTipoLicencia'] = 1;
        }
        if (isset($datos['IdMotivo']) && $datos['IdMotivo'] != "") {
            $sparam['IdMotivo'] = $datos['IdMotivo'];
            $sparam['xIdMotivo'] = 1;
        }
        if (isset($datos['IdArticulo']) && $datos['IdArticulo'] != "") {
            $sparam['IdArticulo'] = $datos['IdArticulo'];
            $sparam['xIdArticulo'] = 1;
        }

        if (!parent::buscarMovPendientes($sparam, $resultado, $numfilas))
            return false;
        return true;

    }


    public function BusquedaAvanzadaNovedades($datos, &$resultado, &$numfilas): bool {

        $sparam = [

            'limit' => '',
            'orderby' => 'LMN.Id DESC',

        ];

        if (isset($datos['Id']) && $datos['Id'] != "") {
            $sparam['Id'] = $datos['Id'];
            $sparam['xId'] = 1;
        }

        if (isset($datos['IdLogMovimientos']) && $datos['IdLogMovimientos'] != "") {
            $sparam['IdLogMovimientos'] = $datos['IdLogMovimientos'];
            $sparam['xIdLogMovimientos'] = 1;
        }

        if (isset($datos['IdPlaza']) && $datos['IdPlaza'] != "") {
            $sparam['IdPlaza'] = $datos['IdPlaza'];
            $sparam['xIdPlaza'] = 1;
        }

        if (isset($datos['Cuil']) && $datos['Cuil'] != "") {
            $sparam['Cuil'] = $datos['Cuil'];
            $sparam['xCuil'] = 1;
        }

        if (isset($datos['IdMovimiento']) && $datos['IdMovimiento'] != "") {
            $sparam['IdMovimiento'] = $datos['IdMovimiento'];
            $sparam['xIdMovimiento'] = 1;
        }

        if (isset($datos['IdServicioTGE']) && $datos['IdServicioTGE'] != "") {
            $sparam['IdServicioTGE'] = $datos['IdServicioTGE'];
            $sparam['xIdServicioTGE'] = 1;
        }

        if (isset($datos['IdSubServicioNovTGE']) && $datos['IdSubServicioNovTGE'] != "") {
            $sparam['IdSubServicioNovTGE'] = $datos['IdSubServicioNovTGE'];
            $sparam['xIdSubServicioNovTGE'] = 1;
        }

        if (isset($datos['IdSubServicioLicTGE']) && $datos['IdSubServicioLicTGE'] != "") {
            $sparam['IdSubServicioLicTGE'] = $datos['IdSubServicioLicTGE'];
            $sparam['xIdSubServicioLicTGE'] = 1;
        }
        if (isset($datos['IdMotivo']) && $datos['IdMotivo'] != "") {
            $sparam['IdMotivo'] = $datos['IdMotivo'];
            $sparam['xIdMotivo'] = 1;
        }

        if (isset($datos['FechaAlta']) && $datos['FechaAlta'] != "") {
            $sparam['FechaAlta'] = $datos['FechaAlta'];
            $sparam['xFechaAlta'] = 1;
        }

        if (isset($datos['FechaBaja']) && $datos['FechaBaja'] != "") {
            $sparam['FechaBaja'] = $datos['FechaBaja'];
            $sparam['xFechaBaja'] = 1;
        }

        if (isset($datos['IdEstado']) && $datos['IdEstado'] != "") {
            $sparam['IdEstado'] = $datos['IdEstado'];
            $sparam['xIdEstado'] = 1;
        }

        if (isset($datos['NotIdEstado']) && $datos['NotIdEstado'] != "") {
            $sparam['NotIdEstado'] = $datos['NotIdEstado'];
            $sparam['xNotIdEstado'] = 1;
        }

        if (isset($datos['IdEscuela']) && $datos['IdEscuela'] != "") {
            $sparam['IdEscuela'] = $datos['IdEscuela'];
            $sparam['xIdEscuela'] = 1;
        }

        if (isset($datos['IdNivel']) && $datos['IdNivel'] != "") {
            $sparam['IdNivel'] = $datos['IdNivel'];
            $sparam['xIdNivel'] = 1;
        }

        if (isset($datos['IdRegion']) && $datos['IdRegion'] != "") {
            $sparam['IdRegion'] = $datos['IdRegion'];
            $sparam['xIdRegion'] = 1;
        }

        if (isset($datos['IdTipoDocumento']) && $datos['IdTipoDocumento'] != "") {
            $sparam['IdTipoDocumento'] = $datos['IdTipoDocumento'];
            $sparam['xIdTipoDocumento'] = 1;
        }

        if (isset($datos['groupby']) && $datos['groupby'] != "")
            $sparam['groupby'] = $datos['groupby'];

        if (isset($datos['orderby']) && $datos['orderby'] != "")
            $sparam['orderby'] = $datos['orderby'];

        if (isset($datos['limit']) && $datos['limit'] != "")
            $sparam['limit'] = $datos['limit'];

        if (isset($datos['MesPeriodo']) && $datos['MesPeriodo'] != "" && is_numeric($datos['MesPeriodo'])) {
            $sparam['MesPeriodo'] = $datos['MesPeriodo'];
            $sparam['xMesPeriodo'] = 1;
        }

        if (isset($datos['AnioPeriodo']) && $datos['AnioPeriodo'] != "" && is_numeric($datos['AnioPeriodo'])) {
            $sparam['AnioPeriodo'] = $datos['AnioPeriodo'];
            $sparam['xAnioPeriodo'] = 1;
        }

        if (isset($datos['ConGoceSueldo']) && $datos['ConGoceSueldo'] != "") {
            $sparam['ConGoceSueldo'] = $datos['ConGoceSueldo'];
            $sparam['xConGoceSueldo'] = 1;
        }
        if (isset($datos['IdExcepcionTipo']) && $datos['IdExcepcionTipo'] != "") {
            $sparam['IdExcepcionTipo'] = $datos['IdExcepcionTipo'];
            $sparam['xIdExcepcionTipo'] = 1;
        }

        if (isset($datos['FechaMovimientoDesde']) && $datos['FechaMovimientoDesde'] != "") {
            $sparam['FechaMovimientoDesde'] = $datos['FechaMovimientoDesde'];
            $sparam['xFechaMovimientoDesde'] = 1;
        }

        if (isset($datos['FechaMovimientoHasta']) && $datos['FechaMovimientoHasta'] != "") {
            $sparam['FechaMovimientoHasta'] = $datos['FechaMovimientoHasta'];
            $sparam['xFechaMovimientoHasta'] = 1;
        }

        if (!parent::BusquedaAvanzadaNovedades($sparam, $resultado, $numfilas))
            return false;
        return true;
    }

    public function BusquedaAvanzadaNovedadesCantidad($datos, &$resultado, &$numfilas): bool {
        /*
        $sparam = [
            'xId' => 0,
            'Id' => '',
            'xIdLogMovimientos' => 0,
            'IdLogMovimientos' => '',
            'xIdPlaza' => 0,
            'IdPlaza' => '',
            'xCuil' => 0,
            'Cuil' => '',
            'xIdMovimiento' => 0,
            'IdMovimiento' => '',
            'xIdServicioTGE' => 0,
            'IdServicioTGE' => '',
            'xIdSubServicioNovTGE' => 0,
            'IdSubServicioNovTGE' => '',
            'xIdSubServicioLicTGE' => 0,
            'IdSubServicioLicTGE' => '',
            'xFechaAlta' => 0,
            'FechaAlta' => '',
            'xFechaBaja' => 0,
            'FechaBaja' => '',
            'xIdEstado' => 0,
            'IdEstado' => '-1',
            'xNotIdEstado' => 0,
            'NotIdEstado' => '-1',
            'xIdEscuela' => 0,
            'IdEscuela' => '',
            'xIdNivel' => 0,
            'IdNivel' => '-1',
            'xIdRegion' => 0,
            'IdRegion' => '-1',
            'xMesPeriodo' => 0,
            'MesPeriodo' => "",
            'xAnioPeriodo' => 0,
            'AnioPeriodo' => "",
            'IdTipoDocumento' => "-1",
            'xIdTipoDocumento' => "0",
            'xConGoceSueldo' => 0,
            'ConGoceSueldo' => "",
            'xIdExcepcionTipo' => 0,
            'IdExcepcionTipo' => "",
            'groupby' => 'GROUP BY LMN.Id',
        ];*/

        if (isset($datos['Id']) && $datos['Id'] != "") {
            $sparam['Id'] = $datos['Id'];
            $sparam['xId'] = 1;
        }

        if (isset($datos['IdLogMovimientos']) && $datos['IdLogMovimientos'] != "") {
            $sparam['IdLogMovimientos'] = $datos['IdLogMovimientos'];
            $sparam['xIdLogMovimientos'] = 1;
        }

        if (isset($datos['IdPlaza']) && $datos['IdPlaza'] != "") {
            $sparam['IdPlaza'] = $datos['IdPlaza'];
            $sparam['xIdPlaza'] = 1;
        }

        if (isset($datos['Cuil']) && $datos['Cuil'] != "") {
            $sparam['Cuil'] = $datos['Cuil'];
            $sparam['xCuil'] = 1;
        }

        if (isset($datos['IdMovimiento']) && $datos['IdMovimiento'] != "") {
            $sparam['IdMovimiento'] = $datos['IdMovimiento'];
            $sparam['xIdMovimiento'] = 1;
        }

        if (isset($datos['IdServicioTGE']) && $datos['IdServicioTGE'] != "") {
            $sparam['IdServicioTGE'] = $datos['IdServicioTGE'];
            $sparam['xIdServicioTGE'] = 1;
        }

        if (isset($datos['IdSubServicioNovTGE']) && $datos['IdSubServicioNovTGE'] != "") {
            $sparam['IdSubServicioNovTGE'] = $datos['IdSubServicioNovTGE'];
            $sparam['xIdSubServicioNovTGE'] = 1;
        }

        if (isset($datos['IdSubServicioLicTGE']) && $datos['IdSubServicioLicTGE'] != "") {
            $sparam['IdSubServicioLicTGE'] = $datos['IdSubServicioLicTGE'];
            $sparam['xIdSubServicioLicTGE'] = 1;
        }

        if (isset($datos['IdMotivo']) && $datos['IdMotivo'] != "") {
            $sparam['IdMotivo'] = $datos['IdMotivo'];
            $sparam['xIdMotivo'] = 1;
        }

        if (isset($datos['FechaAlta']) && $datos['FechaAlta'] != "") {
            $sparam['FechaAlta'] = $datos['FechaAlta'];
            $sparam['xFechaAlta'] = 1;
        }

        if (isset($datos['FechaBaja']) && $datos['FechaBaja'] != "") {
            $sparam['FechaBaja'] = $datos['FechaBaja'];
            $sparam['xFechaBaja'] = 1;
        }

        if (isset($datos['IdEstado']) && $datos['IdEstado'] != "") {
            $sparam['IdEstado'] = $datos['IdEstado'];
            $sparam['xIdEstado'] = 1;
        }

        if (isset($datos['NotIdEstado']) && $datos['NotIdEstado'] != "") {
            $sparam['NotIdEstado'] = $datos['NotIdEstado'];
            $sparam['xNotIdEstado'] = 1;
        }

        if (isset($datos['IdEscuela']) && $datos['IdEscuela'] != "") {
            $sparam['IdEscuela'] = $datos['IdEscuela'];
            $sparam['xIdEscuela'] = 1;
        }

        if (isset($datos['IdNivel']) && $datos['IdNivel'] != "") {
            $sparam['IdNivel'] = $datos['IdNivel'];
            $sparam['xIdNivel'] = 1;
        }

        if (isset($datos['IdRegion']) && $datos['IdRegion'] != "") {
            $sparam['IdRegion'] = $datos['IdRegion'];
            $sparam['xIdRegion'] = 1;
        }

        if (isset($datos['IdTipoDocumento']) && $datos['IdTipoDocumento'] != "") {
            $sparam['IdTipoDocumento'] = $datos['IdTipoDocumento'];
            $sparam['xIdTipoDocumento'] = 1;
        }

        if (isset($datos['groupby']) && $datos['groupby'] != "")
            $sparam['groupby'] = $datos['groupby'];

        if (isset($datos['MesPeriodo']) && $datos['MesPeriodo'] != "" && is_numeric($datos['MesPeriodo'])) {
            $sparam['MesPeriodo'] = $datos['MesPeriodo'];
            $sparam['xMesPeriodo'] = 1;
        }

        if (isset($datos['AnioPeriodo']) && $datos['AnioPeriodo'] != "" && is_numeric($datos['AnioPeriodo'])) {
            $sparam['AnioPeriodo'] = $datos['AnioPeriodo'];
            $sparam['xAnioPeriodo'] = 1;
        }

        if (isset($datos['ConGoceSueldo']) && $datos['ConGoceSueldo'] != "") {
            $sparam['ConGoceSueldo'] = $datos['ConGoceSueldo'];
            $sparam['xConGoceSueldo'] = 1;
        }

        if (isset($datos['IdExcepcionTipo']) && $datos['IdExcepcionTipo'] != "") {
            $sparam['IdExcepcionTipo'] = $datos['IdExcepcionTipo'];
            $sparam['xIdExcepcionTipo'] = 1;
        }

        if (isset($datos['FechaMovimientoDesde']) && $datos['FechaMovimientoDesde'] != "") {
            $sparam['FechaMovimientoDesde'] = $datos['FechaMovimientoDesde'];
            $sparam['xFechaMovimientoDesde'] = 1;
        }

        if (isset($datos['FechaMovimientoHasta']) && $datos['FechaMovimientoHasta'] != "") {
            $sparam['FechaMovimientoHasta'] = $datos['FechaMovimientoHasta'];
            $sparam['xFechaMovimientoHasta'] = 1;
        }


        if (!parent::BusquedaAvanzadaNovedadesCantidad($sparam, $resultado, $numfilas))
            return false;
        return true;
    }

    public function BusquedaAvanzadaNovedadesCSV($datos, &$resultado, &$numfilas): bool {
        $sparam = [
            'xId' => 0,
            'Id' => '',
            'xIdLogMovimientos' => 0,
            'IdLogMovimientos' => '',
            'xIdPlaza' => 0,
            'IdPlaza' => '',
            'CodigoPuesto' => "",
            'xCuil' => 0,
            'Cuil' => '',
            'xIdMovimiento' => 0,
            'IdMovimiento' => '',
            'xIdServicioTGE' => 0,
            'IdServicioTGE' => '',
            'xIdSubServicioNovTGE' => 0,
            'IdSubServicioNovTGE' => '',
            'xIdSubServicioLicTGE' => 0,
            'IdSubServicioLicTGE' => '',
            'xIdMotivo' => 0,
            'IdMotivo' => '',
            'xFechaAlta' => 0,
            'FechaAlta' => '',
            'xFechaBaja' => 0,
            'FechaBaja' => '',
            'xIdEstado' => 0,
            'IdEstado' => '-1',
            'xNotIdEstado' => 0,
            'NotIdEstado' => '-1',
            'xIdEscuela' => 0,
            'IdEscuela' => '',
            'xIdNivel' => 0,
            'IdNivel' => '-1',
            'xIdRegion' => 0,
            'IdRegion' => '-1',
            'limit' => '',
            'groupby' => 'GROUP BY Id',
            'orderby' => 'Id, IdPuestoRaiz, FechaCarga, CodigoMovimiento DESC',
            'xMesPeriodo' => 0,
            'MesPeriodo' => "",
            'xAnioPeriodo' => 0,
            'AnioPeriodo' => "",
            'IdTipoDocumento' => "-1",
            'xIdTipoDocumento' => "0",
            'xConGoceSueldo' => 0,
            'ConGoceSueldo' => "",
            'xIdExcepcionTipo' => 0,
            'IdExcepcionTipo' => "",
            'xFechaMovimientoDesde' => 0,
            'FechaMovimientoDesde' => '',
            'xFechaMovimientoHasta' => 0,
            'FechaMovimientoHasta' => '',
        ];

        if (isset($datos['Id']) && $datos['Id'] != "") {
            $sparam['Id'] = $datos['Id'];
            $sparam['xId'] = 1;
        }

        if (isset($datos['IdLogMovimientos']) && $datos['IdLogMovimientos'] != "") {
            $sparam['IdLogMovimientos'] = $datos['IdLogMovimientos'];
            $sparam['xIdLogMovimientos'] = 1;
        }

        if (isset($datos['IdPlaza']) && $datos['IdPlaza'] != "") {
            $sparam['xIdPlaza'] = 1;
            $sparam['IdPlaza'] = $datos['IdPlaza'];
            $sparam["CodigoPuesto"] = strtoupper(trim($datos['IdPlaza']));
        }

        if (isset($datos['Cuil']) && $datos['Cuil'] != "") {
            $sparam['Cuil'] = $datos['Cuil'];
            $sparam['xCuil'] = 1;
        }

        if (isset($datos['IdMovimiento']) && $datos['IdMovimiento'] != "") {
            $sparam['IdMovimiento'] = $datos['IdMovimiento'];
            $sparam['xIdMovimiento'] = 1;
        }

        if (isset($datos['IdServicioTGE']) && $datos['IdServicioTGE'] != "") {
            $sparam['IdServicioTGE'] = $datos['IdServicioTGE'];
            $sparam['xIdServicioTGE'] = 1;
        }

        if (isset($datos['IdSubServicioNovTGE']) && $datos['IdSubServicioNovTGE'] != "") {
            $sparam['IdSubServicioNovTGE'] = $datos['IdSubServicioNovTGE'];
            $sparam['xIdSubServicioNovTGE'] = 1;
        }

        if (isset($datos['IdSubServicioLicTGE']) && $datos['IdSubServicioLicTGE'] != "") {
            $sparam['IdSubServicioLicTGE'] = $datos['IdSubServicioLicTGE'];
            $sparam['xIdSubServicioLicTGE'] = 1;
        }

        if (isset($datos['IdMotivo']) && $datos['IdMotivo'] != "") {
            $sparam['IdMotivo'] = $datos['IdMotivo'];
            $sparam['xIdMotivo'] = 1;
        }

        if (isset($datos['FechaAlta']) && $datos['FechaAlta'] != "") {
            $sparam['FechaAlta'] = $datos['FechaAlta'];
            $sparam['xFechaAlta'] = 1;
        }

        if (isset($datos['FechaBaja']) && $datos['FechaBaja'] != "") {
            $sparam['FechaBaja'] = $datos['FechaBaja'];
            $sparam['xFechaBaja'] = 1;
        }

        if (isset($datos['IdEstado']) && $datos['IdEstado'] != "") {
            $sparam['IdEstado'] = $datos['IdEstado'];
            $sparam['xIdEstado'] = 1;
        }

        if (isset($datos['NotIdEstado']) && $datos['NotIdEstado'] != "") {
            $sparam['NotIdEstado'] = $datos['NotIdEstado'];
            $sparam['xNotIdEstado'] = 1;
        }

        if (isset($datos['IdEscuela']) && $datos['IdEscuela'] != "") {
            $sparam['IdEscuela'] = $datos['IdEscuela'];
            $sparam['xIdEscuela'] = 1;
        }

        if (isset($datos['IdNivel']) && $datos['IdNivel'] != "") {
            $sparam['IdNivel'] = $datos['IdNivel'];
            $sparam['xIdNivel'] = 1;
        }

        if (isset($datos['IdRegion']) && $datos['IdRegion'] != "") {
            $sparam['IdRegion'] = $datos['IdRegion'];
            $sparam['xIdRegion'] = 1;
        }

        if (isset($datos['IdTipoDocumento']) && $datos['IdTipoDocumento'] != "") {
            $sparam['IdTipoDocumento'] = $datos['IdTipoDocumento'];
            $sparam['xIdTipoDocumento'] = 1;
        }

        if (isset($datos['ConGoceSueldo']) && $datos['ConGoceSueldo'] != "") {
            $sparam['ConGoceSueldo'] = $datos['ConGoceSueldo'];
            $sparam['xConGoceSueldo'] = 1;
        }

        if (isset($datos['IdExcepcionTipo']) && $datos['IdExcepcionTipo'] != "") {
            $sparam['IdExcepcionTipo'] = $datos['IdExcepcionTipo'];
            $sparam['xIdExcepcionTipo'] = 1;
        }

        if (isset($datos['groupby']) && $datos['groupby'] != "")
            $sparam['groupby'] = $datos['groupby'];

        if (isset($datos['orderby']) && $datos['orderby'] != "")
            $sparam['orderby'] = $datos['orderby'];

        if (isset($datos['limit']) && $datos['limit'] != "")
            $sparam['limit'] = $datos['limit'];

        if (isset($datos['MesPeriodo']) && $datos['MesPeriodo'] != "" && is_numeric($datos['MesPeriodo'])) {
            $sparam['MesPeriodo'] = $datos['MesPeriodo'];
            $sparam['xMesPeriodo'] = 1;
        }

        if (isset($datos['AnioPeriodo']) && $datos['AnioPeriodo'] != "" && is_numeric($datos['AnioPeriodo'])) {
            $sparam['AnioPeriodo'] = $datos['AnioPeriodo'];
            $sparam['xAnioPeriodo'] = 1;
        }

        if (isset($datos['FechaMovimientoDesde']) && $datos['FechaMovimientoDesde'] != "") {
            $sparam['FechaMovimientoDesde'] = $datos['FechaMovimientoDesde'];
            $sparam['xFechaMovimientoDesde'] = 1;
        }

        if (isset($datos['FechaMovimientoHasta']) && $datos['FechaMovimientoHasta'] != "") {
            $sparam['FechaMovimientoHasta'] = $datos['FechaMovimientoHasta'];
            $sparam['xFechaMovimientoHasta'] = 1;
        }


        if (!parent::BusquedaAvanzadaNovedadesCSV($sparam, $resultado, $numfilas))
            return false;
        return true;
    }


    public function InsertarLog($datos, &$codigoInsertado): bool {

        if (!isset($datos['IdUsuario']) || $datos['IdUsuario'] == "")
            $datos['IdUsuario'] = 1;

        if (!isset($datos['IdEstado']) || $datos['IdEstado'] == "")
            $datos['IdEstado'] = 1;

        self::_setearNullInsertarLog($datos);
        return parent::InsertarLog($datos, $codigoInsertado);
    }

    public function InsertarLogNovedad($datos, &$codigoInsertado): bool {

        self::_setearFechas($datos);
        self::_setearNull($datos);
        $datos["UltimaModificacionFecha"] = date("Y-m-d H:i:s");
        $datos["UltimaModificacionUsuario"] = !empty($_SESSION['usuariocod']) ? $_SESSION['usuariocod'] : 1;
        $datos["AltaFecha"] = date("Y-m-d H:i:s");
        $datos["AltaUsuario"] = !empty($_SESSION['usuariocod']) ? $_SESSION['usuariocod'] : 1;

        return parent::InsertarLogNovedad($datos, $codigoInsertado);
    }

    public function InsertarLogNovedadBulkTmp($datos): bool {

        return parent::InsertarLogNovedadBulkTmp($datos);
    }

    public function CambiarEstado($datos): bool {


        if (!$this->_ValidarCambiarEstado($datos, $datosRegistro))
            return false;

        switch ($datosRegistro['IdTipoLiquidacion']) {

            case 1:
                $datosModif['IdEstado'] = $datos['IdEstado'];
                $datosModif['IdLogMovimientos'] = $datos['Id'];
                if (!$this->CambiarEstadoLogNovedad($datosModif))
                    return false;
                break;
            case 2:
            case 3:
                /*
                if($datos['IdEstado']==ESTADO_LOG_NOVEDAD_PENDIENTE_LIQUIDACION)
                {

                    $datosModif['IdLogMovimientos'] = $datos['Id'];
                    if(!$this->ProcesarLotePendiente($datosModif)) {
                        echo "error al procesar";die();
                        return false;
                    }
                }

                if($datos['IdEstado']==ESTADO_LOG_NOVEDAD_ANULADO)
                {

                    $datosModif['IdLogMovimientos'] = 'NULL';// seteo el campo en null
                    $datosModif['IdLogMovimientosBuscar'] = $datos['Id']; // valor del campo a buscar
                    if(!$this->ActualizarLicencias_IdLiquidacionAlta_IdLiquidacionBaja_xIdLogMovimientos($datosModif))
                        return false;

                    $datosModif['IdLogMovimientos'] = 'NULL';// seteo el campo en null
                    $datosModif['IdLogMovimientosBuscar'] = $datos['Id']; // valor del campo a buscar
                    if(!$this->ActualizarDocumentos_IdLiquidacionAlta_IdLiquidacionBaja_xIdLogMovimientos($datosModif))
                        return false;


                    $datosModif['IdEstado'] = $datos['IdEstado'];
                    $datosModif['IdLogMovimientos'] = $datos['Id'];
                    if(!$this->CambiarEstadoLogNovedad($datosModif))
                        return false;

                    #quito el bajaliquidacion de todas las licencias liquidadas.
                    $datosModif['BajaLiquidacion'] = 1;
                    $datosModif['BajaLiquidacionActual'] = 0;
                    $datosModif['IdLogMovimientos'] = $datos['Id'];
                    if(!$this->ActualizarLicencias_BajaLiquidacion_xIdLogMovimientos($datosModif))
                        return false;

                    #quito el bajaliquidacion de todas las novedades liquidadas.
                    $datosModif['BajaLiquidacion'] = 1;
                    $datosModif['BajaLiquidacionActual'] = 0;
                    $datosModif['IdLogMovimientos'] = $datos['Id'];
                    if(!$this->ActualizarNovedades_BajaLiquidacion_xIdLogMovimientos($datosModif))
                        return false;

                }*/
                /*
                if($datos['IdEstado']==ESTADO_LOG_NOVEDAD_LIQUIDADO_CON_ERRORES)
                {
                    $datosModif['IdEstado'] = $datos['IdEstado'];
                    $datosModif['IdLogMovimientos'] = $datos['Id'];
                    if(!$this->CambiarEstadoLogNovedad($datosModif))
                        return false;
                }*/

                //CIERRO LIQUIDACION
                if ($datos['IdEstado'] == ESTADO_LOG_NOVEDAD_PENDIENTE_LIQUIDACION) {
                    //CAMBIO EL ESTADO DE LA LIQUIDACION
                    $datosModif['IdEstado'] = $datos['IdEstado'];
                    $datosModif['IdLogMovimientos'] = $datos['Id'];
                    if (!$this->CambiarEstadoLogNovedad($datosModif))
                        return false;

                    /*
                    #quito el liquidado de todas las licencias liquidadas.
                    $datosModif['BajaLiquidacion'] = 0;
                    $datosModif['BajaLiquidacionActual'] = 1;
                    $datosModif['IdLogMovimientos'] = $datos['Id'];
                    if(!$this->ActualizarLicencias_BajaLiquidacion_xIdLogMovimientos($datosModif))
                        return false;

                    #quito el bajaliquidacion de todas las novedades liquidadas.
                    $datosModif['BajaLiquidacion'] = 0;
                    $datosModif['BajaLiquidacionActual'] =1 ;
                    $datosModif['IdLogMovimientos'] = $datos['Id'];


                    if(!$this->ActualizarNovedades_BajaLiquidacion_xIdLogMovimientos($datosModif)) {
                        return false;
                    }*/
                }


                break;
        }


        return parent::CambiarEstado($datos);
    }

    #metodo que procesa todos los movimientos de las fechas del periodo seleccionado
    # y determina cuales van a proceso automatico y cuales a proceso manual
    #tambien borra los movimientos pendientes asociados a las licencias y posiciones
    protected function ProcesarLotePendiente(array $datos): bool {

        #busco periodo por IdLogMovimiento para obtener las fechas
        $oPeriodo = new \cPeriodosLiquidacion($this->conexion);
        if (!$oPeriodo->BuscarxIdLogMovimiento($datos, $resultado, $numfilas))
            return false;
        if ($numfilas == 0) {
            return false;
        }
        $filaPeriodo = $this->conexion->ObtenerSiguienteRegistro($resultado);

        #paso 1 -borro movimientos pendientes de OTROS lotes de las licencias/novedades de ESTE lote
        #falta novedades
        $datosProceso = [];
        $datosProceso["EstadoAnular"] = 1;//1- nuevos
        $datosProceso["IdLogMovimientos"] = $datos["IdLogMovimientos"];//proceso a obviar
        if (!$this->AnularMovimientosPendientes($datosProceso)) //anulo novedades
            return true;

        if (!$this->AnularMovimientosLicenciasPendientes($datosProceso)) //anulo licencias
            return true;

        #paso 2 -los movimientos que requieren RELIQUIDARSE pasan a PEND LIQ MANUAL
        $datosProceso = [];
        $datosProceso["EstadoInicial"] = 1;//nuevo
        $datosProceso["EstadoFinal"] = 7;//pendiente liquidacion manual
        $datosProceso["IdEstadosIniciales"] = "2,3,4,7";//estados de movs ya liquidados
        $datosProceso["IdLogMovimientos"] = $datos["IdLogMovimientos"];
        if (!$this->PasarManualMovimientosYaLiquidados($datosProceso))
            return true;

        #paso 3- paso los movimientos pendientes (restantes) que tengan FechaLiquidacion <fecha real ddel periodo>
        $datosProceso = [];
        $datosProceso["EstadoInicial"] = 1;//nuevo
        $datosProceso["EstadoFinal"] = 2;//pendiente liquidacion automatica
        //$datosProceso["FechaDesde"]=$filaPeriodo["FechaDesde"];
        $datosProceso["FechaHasta"] = $filaPeriodo["FechaFinReal"];
        return $this->CambiarEstadoMovimientosXFechas($datosProceso);

    }

    public function CambiarEstadoMovimientosXFechas(array $datos): bool {
        return parent::CambiarEstadoMovimientosXFechas($datos);
    }

    public function AnularMovimientosPendientes(array $datos): bool {
        return parent::AnularMovimientosPendientes($datos);
    }

    public function AnularMovimientosLicenciasPendientes(array $datos): bool {
        return parent::AnularMovimientosLicenciasPendientes($datos);
    }

    public function PasarManualMovimientosYaLiquidados(array $datos): bool {
        return parent::PasarManualMovimientosYaLiquidados($datos);
    }

    public function CambiarEstadoLogNovedad(array $datos): bool {
        return parent::CambiarEstadoLogNovedad($datos);
    }

    public function CambiarEstadoLogNovedadxId(array $datos): bool {

        if (\FuncionesPHPLocal::isEmpty($datos['IdEstado'])) {
            return false;
        }

        $datos['UltimaModificacionFecha'] = date("Y-m-d H:i:s");
        $datos['UltimaModificacionUsuario'] = $_SESSION['usuariocod'];
        return parent::CambiarEstadoLogNovedadxId($datos);
    }


    public function ActualizarLicencias_IdLiquidacionAlta_IdLiquidacionBaja_xIdLogMovimientos(array $datos): bool {
        return parent::ActualizarLicencias_IdLiquidacionAlta_IdLiquidacionBaja_xIdLogMovimientos($datos);
    }

    public function ActualizarDocumentos_IdLiquidacionAlta_IdLiquidacionBaja_xIdLogMovimientos(array $datos): bool {
        return parent::ActualizarDocumentos_IdLiquidacionAlta_IdLiquidacionBaja_xIdLogMovimientos($datos);
    }


    /* public function _ValidarModificar($datos,&$datosRegistro)
     {

         if (!isset($datos['UltimaModificacionFecha']) || $datos['UltimaModificacionFecha']=="")
             $datos['UltimaModificacionFecha']="NULL";
     }*/
    public function _ValidarCambiarEstado($datos, &$datosRegistro) {
        if (!$this->BuscarxCodigo($datos, $resultado, $numfilas))
            return false;

        if ($numfilas != 1) {
            FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, "Error, debe ingresar un movimiento existente.", ["archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__], ["formato" => $this->formato]);
            return false;
        }

        $datosRegistro = $this->conexion->ObtenerSiguienteRegistro($resultado);

        if (!$this->conexion->TraerCampo('LogMovimientosEstado', 'Id', ['Id=' . $datos['IdEstado']], $dato, $numfilas, $errno))
            return false;


        if ($numfilas != 1) {
            FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, "Error debe ingresar un Estado valido.", ["archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__], ["formato" => $this->formato]);
            return false;
        }

        return true;
    }

    public static function _setearFechas(&$datos): void {

        if (!\FuncionesPHPLocal::isEmpty($datos['FechaAlta']))
            $datos['FechaAlta'] = \FuncionesPHPLocal::ConvertirFecha($datos['FechaAlta'], 'dd-mm-aaaa', 'aaaa/mm/dd');

        if (!\FuncionesPHPLocal::isEmpty($datos['FechaBaja']))
            $datos['FechaBaja'] = \FuncionesPHPLocal::ConvertirFecha($datos['FechaBaja'], 'dd-mm-aaaa', 'aaaa/mm/dd');

        if (!\FuncionesPHPLocal::isEmpty($datos['FechaMovimiento']))
            $datos['FechaMovimiento'] = \FuncionesPHPLocal::ConvertirFecha($datos['FechaMovimiento'], 'dd-mm-aaaa', 'aaaa/mm/dd');

        if (!\FuncionesPHPLocal::isEmpty($datos['FechaLiquidacion']))
            $datos['FechaLiquidacion'] = \FuncionesPHPLocal::ConvertirFecha($datos['FechaLiquidacion'], 'dd-mm-aaaa', 'aaaa/mm/dd');

        if (!\FuncionesPHPLocal::isEmpty($datos['FechaReintegro']))
            $datos['FechaReintegro'] = \FuncionesPHPLocal::ConvertirFecha($datos['FechaReintegro'], 'dd-mm-aaaa', 'aaaa/mm/dd');


        $fecha_carga = explode('.', $datos['FechaCarga']);
        $fecha_carga = explode(' ', $fecha_carga[0]);
        $datos['FechaCarga'] = \FuncionesPHPLocal::ConvertirFecha($fecha_carga[0], 'dd-mm-aaaa', 'aaaa/mm/dd') . ' ' . $fecha_carga[1];
    }

    public static function _setearNull(&$datos): void {

        if (\FuncionesPHPLocal::isEmpty($datos['idSubServicioNovTGE']))
            $datos['idSubServicioNovTGE'] = 'NULL';

        if (\FuncionesPHPLocal::isEmpty($datos['FechaAlta']))
            $datos['FechaAlta'] = 'NULL';

        if (\FuncionesPHPLocal::isEmpty($datos['FechaBaja']))
            $datos['FechaBaja'] = 'NULL';

        if (\FuncionesPHPLocal::isEmpty($datos['idLicencia']))
            $datos['idLicencia'] = 'NULL';

        if (\FuncionesPHPLocal::isEmpty($datos['idServicioTGEQueSuple']))
            $datos['idServicioTGEQueSuple'] = 'NULL';

        if (\FuncionesPHPLocal::isEmpty($datos['idServicioTGERelacionado']))
            $datos['idServicioTGERelacionado'] = 'NULL';

        if (\FuncionesPHPLocal::isEmpty($datos['CausaAlta']))
            $datos['CausaAlta'] = 'NULL';

        if (\FuncionesPHPLocal::isEmpty($datos['CausaBaja']))
            $datos['CausaBaja'] = 'NULL';

        if (\FuncionesPHPLocal::isEmpty($datos['IdRevistaAntigua']))
            $datos['IdRevistaAntigua'] = 'NULL';

        if (\FuncionesPHPLocal::isEmpty($datos['IdRevistaNueva']))
            $datos['IdRevistaNueva'] = 'NULL';

        if (\FuncionesPHPLocal::isEmpty($datos['idSubServicioLicTGE']))
            $datos['idSubServicioLicTGE'] = 'NULL';

        if (\FuncionesPHPLocal::isEmpty($datos['FechaLiquidacion']))
            $datos['FechaLiquidacion'] = 'NULL';
    }

    public static function _setearNullInsertarLog(&$datos): void {

        if (\FuncionesPHPLocal::isEmpty($datos['IdUsuario']))
            $datos['IdUsuario'] = 'NULL';

        if (\FuncionesPHPLocal::isEmpty($datos['FechaEjecucion']))
            $datos['FechaEjecucion'] = 'NULL';

        if (\FuncionesPHPLocal::isEmpty($datos['FechaDesde']))
            $datos['FechaDesde'] = 'NULL';

        if (\FuncionesPHPLocal::isEmpty($datos['FechaHasta']))
            $datos['FechaHasta'] = 'NULL';

        if (\FuncionesPHPLocal::isEmpty($datos['FechaReintegro']))
            $datos['FechaReintegro'] = 'NULL';

        if (\FuncionesPHPLocal::isEmpty($datos['IdEstado']))
            $datos['IdEstado'] = 'NULL';

        if (\FuncionesPHPLocal::isEmpty($datos['IdPeriodo']))
            $datos['IdPeriodo'] = 'NULL';

        if (\FuncionesPHPLocal::isEmpty($datos['IdTipoLiquidacion']))
            $datos['IdTipoLiquidacion'] = 'NULL';

    }


    public function BuscarDocumentosLiquidacion($datos, &$resultado, &$numfilas): bool {
        $sparam = [
            'Excluir_Escuela' => "-1",
            'IdDocumento' => '',
        ];
        if (isset($datos['Excluir_Escuela']) && $datos['Excluir_Escuela'] != "") {
            $sparam['Excluir_Escuela'] = $datos['Excluir_Escuela'];
        }
        if (isset($datos['IdDocumento']) && $datos['IdDocumento'] != "") {
            $sparam['IdDocumento'] = $datos['IdDocumento'];
        }
        if (!parent::BuscarDocumentosLiquidacion($sparam, $resultado, $numfilas))
            return false;
        return true;
    }

    public function BuscarLicenciasLiquidacion($datos, &$resultado, &$numfilas): bool {
        $sparam = [
            #'Excluir_Escuela'=> "-1",
            'IdLicencia' => '',
            'xSoloSinGoce' => '0',
            'xIdMovimiento' => 0,
            'IdMovimiento' => '',
            # 'SoloSinGoce'=> '0'
        ];
        #if (isset($datos['Excluir_Escuela']) && $datos['Excluir_Escuela'] != "") {
        #   $sparam['Excluir_Escuela'] = $datos['Excluir_Escuela'];
        #}
        if (isset($datos['IdLicencia']) && $datos['IdLicencia'] != "") {
            $sparam['IdLicencia'] = $datos['IdLicencia'];
        }
        if (LIQ_SOLOLICENCIASSINGOCE == 1) {
            #$sparam['SoloSinGoce'] = 1;
            $sparam['xSoloSinGoce'] = 1;
        }


        if (isset($datos['IdMovimiento']) && $datos['IdMovimiento'] != '') {
            $sparam['IdMovimiento'] = $datos['IdMovimiento'];
            $sparam['xIdMovimiento'] = 1;
        }

        if (!parent::BuscarLicenciasLiquidacion($sparam, $resultado, $numfilas))
            return false;
        return true;
    }


    protected function _validarExistencia(array $datos, ?array &$datosRegistro): bool {}

    /**
     * @inheritDoc
     * @throws Exception
     */
    public function _armarFila(array $datos, &$resultadoBD): array {
        $movimiento = $datos['Movimiento'];
        $fechaHasta = self::NULL;
        $fechaDesde = self::NULL;
        $fechaReintegro = self::NULL;
        $fechaLiquidacion = self::NULL;
        $horas = $datos['Horas'];
        //print_r($datos);
        switch ($movimiento) {
            case '1'://alta
            case '4'://Alta revista
            case '3'://Baja revista
                //  $fechaLiquidacion = isset($datos['FechaLiquidacion'])?new DateTime($datos['FechaLiquidacion']) : '@0';
                //$fechaLiquidacion= $fechaLiquidacion->format(self::FORMATO_FECHA);
                $fechaDesde = isset($datos['fechaDesde']) ? new DateTime($datos['fechaDesde']) : '@0';
                $fechaDesde = $fechaDesde->format(self::FORMATO_FECHA);
                $fechaLiquidacion = $fechaDesde;

                if (isset($datos['fechaHasta']) && $datos['fechaHasta'] != "") {
                    $fechaHasta = isset($datos['fechaHasta']) ? new DateTime($datos['fechaHasta']) : '@0';
                    $fechaHasta = $fechaHasta->format(self::FORMATO_FECHA);
                }


                break;
            case '2'://baja
                //$fechaLiquidacion = isset($datos['FechaLiquidacion'])?new DateTime($datos['FechaLiquidacion']) : '@0';
                //$fechaLiquidacion= $fechaLiquidacion->format(self::FORMATO_FECHA);
                if ((!isset($datos['fechaHasta']) || $datos['fechaHasta'] == "") && $datos['fechaDesde'] != "")
                    $datos['fechaHasta'] = $datos['fechaDesde'];
                $fechaHasta = isset($datos['fechaHasta']) ? new DateTime($datos['fechaHasta']) : '@0';
                $fechaHasta = $fechaHasta->format(self::FORMATO_FECHA);
                $fechaLiquidacion = $fechaHasta;
                break;
            case '10'://inicio lic
                $fechaDesde = isset($datos['fechaDesde']) ? new DateTime($datos['fechaDesde']) : '@0';
                $fechaDesde = $fechaDesde->format(self::FORMATO_FECHA);
                $fechaLiquidacion = $fechaDesde;

                if ((!isset($datos['fechaHasta']) || $datos['fechaHasta'] == "") && $datos['fechaDesde'] != "")
                    $datos['fechaHasta'] = $datos['fechaDesde'];
                $fechaHasta = isset($datos['fechaHasta']) ? new DateTime($datos['fechaHasta']) : '@0';
                $fechaHasta = $fechaHasta->format(self::FORMATO_FECHA);

                break;
            case '12'://fin lic

                if ((!isset($datos['fechaHasta']) || $datos['fechaHasta'] == "") && $datos['fechaDesde'] != "")
                    $datos['fechaHasta'] = $datos['fechaDesde'];
                //$fechaDesde = isset($datos['fechaDesde']) ? new DateTime($datos['fechaDesde']) : '@0';
                //$fechaDesde = $fechaDesde->format(self::FORMATO_FECHA);
                $fechaHasta = isset($datos['fechaHasta']) ? new DateTime($datos['fechaHasta']) : '@0';
                $fechaHasta = $fechaHasta->format(self::FORMATO_FECHA);

                if (isset($datos['fechaReintegro']) && $datos['fechaReintegro'] != "" && $datos['fechaReintegro'] != "NULL") {
                    $fechaReintegro = isset($datos['fechaReintegro']) ? new DateTime($datos['fechaReintegro']) : '@0';

//echo "fechaReintegro". $fechaReintegro->format(self::FORMATO_FECHA);

                    $fechaReintegro = $fechaReintegro->sub(new \DateInterval("P1D"))->format(self::FORMATO_FECHA);
//echo "fechaReintegro2". $fechaReintegro;
//die();
                    $fechaHasta = $fechaReintegro;
                }

//echo "fechaHasta". $fechaHasta;

                $fechaLiquidacion = $fechaHasta;
                break;
            case '13'://modif lic
                $fecha = isset($datos['fechaDesde']) ? new DateTime($datos['fechaDesde']) : '@0';
                $fechaDesde = $fecha->format(self::FORMATO_FECHA);
                $fecha = isset($datos['fechaHasta']) ? new DateTime($datos['fechaHasta']) : '@0';
                $fechaHasta = $fecha->format(self::FORMATO_FECHA);
                if (isset($datos['FechaReintegro']) && $datos['FechaReintegro'] != "" && $datos['FechaReintegro'] != "NULL") {
                    $fechaReintegro = $datos['FechaReintegro'];
                }
                $fechaLiquidacion = $fechaHasta;

                break;
            default:
                throw new ExcepcionLogica($movimiento);
        }
        //echo "Mov:".$movimiento." - fechaHasta:".$fechaHasta;die();
        //el periodo lo armo con la fecha de liquidacion, concatenando año y mes
        $PeriodoMes = date("m", strtotime($fechaLiquidacion));
        $PeriodoAnio = date("Y", strtotime($fechaLiquidacion));
        if (strlen($PeriodoMes) <= 1)
            $PeriodoMes = "0" . $PeriodoMes;
        $fechaPeriodo = $PeriodoAnio . $PeriodoMes;

        $array = [
            'Periodo' => $fechaPeriodo,
            'Agente' => $datos['CuilAgente'] ?: self::NULL,
            'idMovimiento' => $movimiento ?: self::NULL,
            'idPlaza' => $datos['IdPuestoMigracion'] ?? self::NULL,
            'IdEscuela' => $datos['IdEscuela'] ?? self::NULL,
            'idServicioTGE' => $datos['IdPofa'] ?? self::NULL,
            'idSubServicioNovTGE' => $datos['IdDocumento'] ?? self::NULL,
            'CargoSalarial' => $datos['CodigoPuesto'] ?: self::NULL,
            'idSituacionRevista' => $datos['IdRevistaExterno'],
            'FechaAlta' => $fechaDesde,
            'FechaBaja' => $fechaHasta,
            'Horas' => number_format($horas, self::DECIMALES, self::SEPARADOR_DECIMAL, self::SEPARADOR_MILES),
            'idLicencia' => $datos['IdArticuloExterno'] ?? self::NULL,
            'idServicioTGEQueSuple' => $datos['IdPofaPadre'] ?? self::NULL,
            'idServicioTGERelacionado' => self::NULL,
            'CausaAlta' => self::NULL,
            'CausaBaja' => self::NULL,
            'FechaCarga' => date_create($datos['AltaFecha'])->format(self::FORMATO_FECHA_HORA),
            'UsuarioCarga' => $datos['AltaUsuario'] ?: self::NULL,
            'idSubServicioLicTGE' => $datos['IdLicencia'] ?? self::NULL,
            'IdRevistaAntigua' => $datos['IdRevistaAntigua'] ?? self::NULL,
            'IdRevistaNueva' => $datos['IdRevista'] ?? self::NULL,
            'IdPersona' => $datos['IdPersona'] ?? self::NULL,
            'IdTipoDocumento' => $datos['IdTipoDocumento'] ?? self::NULL,
            'IdArticulo' => $datos['IdArticulo'] ?? self::NULL,
            'BajaLiquidacion' => $datos['BajaLiquidacion'] ?? self::NULL,
            'IdPuesto' => $datos['IdPuesto'] ?? self::NULL,
            'IdPuestoDestino' => $datos['IdPuestoDestino'] ?? self::NULL,
            'FechaReintegro' => $fechaReintegro,
            'IdEstadoNovedad' => $datos['IdEstadoNovedad'] ?? self::NULL,
            'EstadoNovedad' => $datos['EstadoNovedad'] ?? self::NULL,
            'FechaLiquidacion' => $fechaLiquidacion,

            'CodigoPuesto' => $datos['CUPOF'] ?? self::NULL,
            'IdExcepcionTipo' => $datos['IdExcepcionTipo'] ?? self::NULL,
            'ConGoceSueldo' => $datos['ConGoceSueldo'] ?? self::NULL,
            'IdNivel' => $datos['IdNivel'] ?? self::NULL,
            'IdRegion' => $datos['IdRegion'] ?? self::NULL,
            'CodigoLiquidador' => $datos['CodigoLiquidador'] ?? self::NULL,
            'IdDocumentoTipo' => $datos['IdDocumentoTipo'] ?? self::NULL,
            'NombreCompleto' => $datos['NombreCompleto'] ?? self::NULL,
        ];
        //print_r($array);die();
        $resultadoBD = $array;
        $resultadoBD['FechaMovimiento'] = date_create($datos['FechaMovimiento'])->format(self::FORMATO_FECHA_HORA);

        //FuncionesPHPLocal::print_pre($resultadoBD, true);
        return $array;
    }

    public function _detectarHorasTipoCargo(array $datos, string $tipo = self::TIPO_NOVEDAD): float {

        if ($datos['IdRegimenSalarial'] != REGIMEN_SALARIAL_HORAS)
            return 0;
        switch ($tipo) {
            case self::TIPO_LICENCIA:
                if (self::IGNORAR_HORAS && empty($datos['CantModulos']))
                    return 0;
                if (!empty((int)$datos['Horas']))
                    return $datos['Horas'];
            case self::TIPO_NOVEDAD:
                return self::IGNORAR_HORAS
                    ? (float)$datos['CantModulos']
                    : (float)$datos['CantHoras'] + (float)$datos['CantModulos'];
            default:
                throw new ExcepcionLogica('Tipo de movimiento incorrecto');
        }
    }


    public function eliminarMovimientoLicencia(array $datos, &$codigoInsertado): bool {
        #|1- busco liquidacion activa
        $datosPeriodo = [
            "IdEstado" => 2,//pendiente
            "IdTipoLiquidacion" => 2, //liquidacion
        ];
        if (!$this->BusquedaAvanzada($datosPeriodo, $resultadoPer, $numfilasPer)) {
            $this->setError("Error al buscar liquidación activa");
            return false;
        }
        if ($numfilasPer <= 0) {
            $this->setError("No existe liquidación activa para liquidar la novedad seleccionada");
            return false;
        }
        $LiquidacionActiva = $this->conexion->ObtenerSiguienteRegistro($resultadoPer);
        $IdLogMovimiento = $LiquidacionActiva['Id'];

        #anulo los movimientos de la misma novedad en el mismo periodo
        $datosActualizarMovimientos = [
            "IdLicencia" => $datos["IdLicencia"],
            "IdEstado" => 5,//anulado
        ];
        if (!$this->ActualizarMovimientosEstadoIdPeriodoxLicencia($datosActualizarMovimientos)) {
            return false;
        }

        return true;
        //return parent::insertarMovimientoNovedad($datos);
    }


    public function ActualizarMovimientosEstadoIdPeriodoxLicencia($datos): bool {
        $sparam = [
            'IdLicencia' => '',
            'IdEstado' => '',
            'xIdPeriodo' => 0,
            'IdPeriodo' => '',
            'xIdMovimiento' => 0,
            'IdMovimiento' => '',

        ];
        if (isset($datos['IdLicencia']) && $datos['IdLicencia'] != "") {
            $sparam['IdLicencia'] = $datos['IdLicencia'];
        }
        if (isset($datos['IdEstado']) && $datos['IdEstado'] != "") {
            $sparam['IdEstado'] = $datos['IdEstado'];
        }

        if (isset($datos['IdPeriodo']) && $datos['IdPeriodo'] != "") {
            $sparam['IdPeriodo'] = $datos['IdPeriodo'];
            $sparam['xIdPeriodo'] = 1;
        }

        if (isset($datos['IdMovimiento']) && $datos['IdMovimiento'] != "") {
            $sparam['IdMovimiento'] = $datos['IdMovimiento'];
            $sparam['xIdMovimiento'] = 1;
        }

        if (!parent::ActualizarMovimientosEstadoIdPeriodoxLicencia($sparam))
            return false;

        return true;
    }

    public function ActualizarMovimientosEstadoxIdPofaxIdMovimiento($datos): bool {
        $sparam = [
            'IdEstado' => '-1',
            'xIdPofa' => 0,
            'IdPofa' => '',
            'xIdMovimiento' => 0,
            'IdMovimiento' => '',
            'xIdTipoDocumento' => 0,
            'IdTipoDocumento' => '',
        ];

        if (isset($datos['IdEstado']) && $datos['IdEstado'] != "") {
            $sparam['IdEstado'] = $datos['IdEstado'];
        }

        if (isset($datos['IdPofa']) && $datos['IdPofa'] != "") {
            $sparam['IdPofa'] = $datos['IdPofa'];
            $sparam['xIdPofa'] = 1;
        }

        if (isset($datos['IdMovimiento']) && $datos['IdMovimiento'] != "") {
            $sparam['IdMovimiento'] = $datos['IdMovimiento'];
            $sparam['xIdMovimiento'] = 1;
        }

        if (isset($datos['IdTipoDocumento']) && $datos['IdTipoDocumento'] != "") {
            $sparam['IdTipoDocumento'] = $datos['IdTipoDocumento'];
            $sparam['xIdTipoDocumento'] = 1;
        }

        if (!parent::ActualizarMovimientosEstadoxIdPofaxIdMovimiento($sparam))
            return false;

        return true;
    }

    public function ActualizarMovimientosEstadoxIdDocumentoxIdMovimiento($datos): bool {
        $sparam = [
            'IdDocumento' => '',
            'IdEstado' => '',
            'xIdMovimiento' => 0,
            'IdMovimiento' => '-1',

        ];
        if (isset($datos['IdDocumento']) && $datos['IdDocumento'] != "") {
            $sparam['IdDocumento'] = $datos['IdDocumento'];
        }

        if (isset($datos['IdEstado']) && $datos['IdEstado'] != "") {
            $sparam['IdEstado'] = $datos['IdEstado'];
        }


        if (isset($datos['IdMovimiento']) && $datos['IdMovimiento'] != "") {
            $sparam['IdMovimiento'] = $datos['IdMovimiento'];
            $sparam['xIdMovimiento'] = 1;
        }

        if (!parent::ActualizarMovimientosEstadoxIdDocumentoxIdMovimiento($sparam))
            return false;

        return true;
    }

    public function insertarMovimientoNovedad(array $datos, &$codigoInsertado): bool {
        #|1- busco liquidacion activa
        $datosPeriodo = [
            "IdEstado" => 2,//pendiente
            "IdTipoLiquidacion" => 2, //liquidacion
        ];
        if (!$this->BusquedaAvanzada($datosPeriodo, $resultadoPer, $numfilasPer)) {
            $this->setError("Error al buscar liquidacion activa");
            return false;
        }
        if ($numfilasPer <= 0) {
            $this->setError("No existe liquidacion activa para liquidar la novedad seleccionada");
            return false;
        }
        $LiquidacionActiva = $this->conexion->ObtenerSiguienteRegistro($resultadoPer);
        $IdLogMovimiento = $LiquidacionActiva['Id'];

        #verifico si me vino por parametro  un IDMovimiento
        if (!isset($datos["IdMovimiento"]) || $datos["IdMovimiento"] == "") {
            $datos["IdMovimiento"] = "";

        }
        #anulo los movimientos de la misma novedad pendientes
        $datosActualizarMovimientos = [
            "IdDocumento" => $datos["IdDocumento"],
            "IdEstado" => 5,//ANULADO
            "IdMovimiento" => $datos["IdMovimiento"],
        ];
        if (!$this->ActualizarMovimientosEstadoxIdDocumentoxIdMovimiento($datosActualizarMovimientos)) {
            $this->setError("Error al anular movimientos pendientes a liquidar de la novedad seleccionada");
            return false;
        }

        #busco la novedad a liquidar
        if (!$this->BuscarDocumentosLiquidacion($datos, $resultadoDocumentos, $numfilasDocumentos))
            return false;
        if ($numfilasDocumentos > 0) {
            #recorro puesto x puesto de la novedad e inserto como movimiento
            while ($filaNov = $this->conexion->ObtenerSiguienteRegistro($resultadoDocumentos)) {

                $redefinoMovimiento = false;
                if (isset($datos['IdMovimiento']) && $datos['IdMovimiento'] != "") {
                    $filaNov['Movimiento'] = $datos['IdMovimiento'];
                    $redefinoMovimiento = true;

                }

                //armo info general del bloque
                $filaNov['Horas'] = $this->_detectarHorasTipoCargo($filaNov, "nov");
                $filaNov['IdRevistaNueva'] = 'NULL';
                $filaNov['IdRevistaAntigua'] = 'NULL';
                $filaNov['FechaLiquidacion'] = $filaNov['FechaMovimiento'];

                //if (strtotime($filaNov['FechaLiquidacion']) < strtotime($filaNov['fechaDesde']))
                //  $filaNov['FechaLiquidacion'] = $filaNov['fechaDesde'];

                //si hay fecha toma, la fechaDesde es la fecha de toma de posesion
                if (isset($filaNov['FechaToma']) && $filaNov['FechaToma'] != "") {
                    $filaNov['fechaDesde'] = $filaNov['FechaToma'];
                }
                //si es un alta, la fechakey es la desde, en un cese la fecha de baja es la desde
                $fechakey = date("Ymd", strtotime($filaNov['fechaDesde']));

                //si el periodo de la novedad es distinto <> al que estamos liquidando, el estado del movimiento tiene que ser de liquidacion manual
                $filaNov["IdEstadoMovimiento"] = 2;//pend liquidacion
                //if (isset($filaNov["IdPeriodo"]) && $filaNov["IdPeriodo"] != $LiquidacionActiva["IdPeriodo"]) {
                if (isset($filaNov["IdMovLiq"]) && $filaNov["IdMovLiq"] != "") {
                    $filaNov["IdEstadoMovimiento"] = 7;//estado de revision manual o anulado ya liquidado
                }

                #agrego el movimiento al array de movimientos
                $datosInsertar["d{$fechakey}-{$filaNov['IdPofa']}-n{$filaNov['IdDocumento']}"] = $filaNov;

                #si tengo esta constante en 1, siempre genero baja
                //echo "LIQ_GENEROFINDOCUMENTOS".LIQ_GENEROFINDOCUMENTOS;
                if (LIQ_GENEROFINDOCUMENTOS == 1) {
                    if (isset($filaNov['fechaHasta']) && $filaNov['fechaHasta'] != "" && !$redefinoMovimiento) {
                        $filaNov['Movimiento'] = 2;//baja
                        $filaNov['FechaLiquidacion'] = $filaNov['fechaHasta'];
                        $fechakey = date("Ymd", strtotime($filaNov['fechaHasta']));
                        $datosInsertar["h{$fechakey}-{$filaNov['IdPofa']}-n{$filaNov['IdDocumento']}"] = $filaNov;
                    }
                } elseif (LIQ_GENEROFINDOCUMENTOSFUTUROS == 1) {
                    #genero baja si la fecha Baja es diferente MES a la fecha desde
                    if (isset($filaNov['fechaHasta']) && $filaNov['fechaHasta'] != "" && !$redefinoMovimiento) {
                        $periodoDesde = date("Ym", strtotime($filaNov['fechaDesde']));
                        $periodoHasta = date("Ym", strtotime($filaNov['fechaHasta']));
                        //echo $periodoDesde."vs".$periodoHasta;die();
                        if ($periodoDesde != $periodoHasta) {
                            $filaNov['Movimiento'] = 2;//baja
                            $filaNov['FechaLiquidacion'] = $filaNov['fechaHasta'];
                            $fechakey = date("Ymd", strtotime($filaNov['fechaHasta']));
                            $datosInsertar["h{$fechakey}-{$filaNov['IdPofa']}-n{$filaNov['IdDocumento']}"] = $filaNov;
                        }

                    }

                }


            }

            $i = 0;
            foreach ($datosInsertar as $fila) {

                $movimiento = $this->_armarFila($fila, $resultadoBD);

                //anulo movimientos pendientes por IdMovimiento/IdPofa/
                if ($fila['Movimiento'] == "2")// SOLO SI ES UN CESE
                {
                    $datosActualizarMovimientos = [
                        "IdEstado" => 5,//ANULADO
                        "IdMovimiento" => $fila["Movimiento"],
                        "IdPofa" => $fila["IdPofa"],
                    ];
                    if (!$this->ActualizarMovimientosEstadoxIdPofaxIdMovimiento($datosActualizarMovimientos)) {
                        $this->setError("Error al anular movimientos pendientes por movimiento y POFA");
                        return false;
                    }
                }

                $datosInsertarNov = $resultadoBD;
                $datosInsertarNov['IdLogMovimientos'] = $IdLogMovimiento;
                $datosInsertarNov['IdEstado'] = $fila["IdEstadoMovimiento"];
                $datosInsertarNov['Orden'] = 1;

                if (!$this->InsertarLogNovedad($datosInsertarNov, $idLogNovTmp)) {
                    $this->setError($this->getError());
                    return false;
                }
            }
        }

        return true;
        //return parent::insertarMovimientoNovedad($datos);
    }

    public function insertarMovimientoLicencia(array $datos, &$codigoInsertado): bool {
        //echo "liquido ID:" . $datos["IdLicencia"];
        #|1- busco liquidacion activa
        $datosPeriodo = [
            "IdEstado" => 2,//pendiente
            "IdTipoLiquidacion" => 2, //liquidacion
        ];
        if (!$this->BusquedaAvanzada($datosPeriodo, $resultadoPer, $numfilasPer)) {
            $this->setError("Error al buscar liquidación activa");
            return false;
        }
        if ($numfilasPer <= 0) {
            $this->setError("No existe liquidación activa para liquidar la novedad seleccionada");
            return false;
        }
        $LiquidacionActiva = $this->conexion->ObtenerSiguienteRegistro($resultadoPer);
        $datos["IdLogMovimiento"] = $LiquidacionActiva['Id'];

        #verifico si me vino por parametro  un IDMovimiento
        if (!isset($datos["IdMovimiento"]))
            $datos["IdMovimiento"] = "";

        #ELIMINO los movimientos PENDIENTES de la misma novedad (y tipo si viene definido)
        #no importa el periodo
        $datosActualizarMovimientos = [
            "IdLicencia" => $datos["IdLicencia"],
            "IdEstado" => 5,//ANULADO
            "IdMovimiento" => $datos["IdMovimiento"],
        ];
        if (!$this->ActualizarMovimientosEstadoIdPeriodoxLicencia($datosActualizarMovimientos)) {
            $this->setError("Error al anular movimientos de liquidacion de la licencia seleccionada");
            return false;
        }

        if (!$this->_ArmarMovimientosLicencia($datos, $LiquidacionActiva)) {
            $this->setError("Error al armar movimiento de licencia");
            return false;
        }
        return true;
    }


    private function _ArmarMovimientosLicencia($datos, $LiquidacionActiva) {

        #busco la licencia a liquidar
        if (!$this->BuscarLicenciasLiquidacion($datos, $resultadoLicencias, $numfilasLics)) {
            $this->setError("No se puede liquidar la licencia seleccionada. Error al buscar.");
            return false;
        }

        if ($numfilasLics > 0) {

            #recorro puesto x puesto de la novedad e inserto como movimiento
            $datosInsertar = $filaLic = [];
            while ($filaLic = $this->conexion->ObtenerSiguienteRegistro($resultadoLicencias)) {
                //echo $filaLic["fechaReintegro"]."vs".$datos["fechaReintegro"];
                #si viene la fecha de reintegro de la novedad, priorizo esa sobre la de la  consulta de BD
                if (isset($datos["fechaReintegro"]) && $datos["fechaReintegro"] != "")
                    $filaLic["fechaReintegro"] = $datos["fechaReintegro"];

                //si viene seteado el movimiento desde la novedad/licencia , desestimo el movimiento que me viene de la consulta
                $redefinoMovimiento = false;
                if (isset($datos['IdMovimiento']) && $datos['IdMovimiento'] != "") {
                    $filaLic['Movimiento'] = $datos['IdMovimiento'];
                    $redefinoMovimiento = true;
                } else
                    $filaLic['Movimiento'] = 10;//por default inicio de lic

                //defino las hora del movimiento segun el cargo -- remplazar por calculo con el regimen salarial!
                $filaLic['Horas'] = $this->_detectarHorasTipoCargo($filaLic, "lic");

                //por defecto pendiente,si ya fue liquidada se m	arca como liquidacion manual
                $filaLic["IdEstadoMovimiento"] = 2;//pend liquidacion

                if (isset($filaLic["IdMovLiq"]) && $filaLic["IdMovLiq"] != "") {
                    $filaLic["IdEstadoMovimiento"] = 7;//pend liquidacion manual
                }

                #siempre meto el inicio y fin de la licencia, cambia la fecha de liquidacion nomas
                // $filaLic['Movimiento'] =  10;
                $filaLic['FechaLiquidacion'] = $filaLic['Inicio'];
                if (strtotime($filaLic['FechaLiquidacion']) <= strtotime($filaLic['FechaMovimiento']))
                    $filaLic['FechaLiquidacion'] = $filaLic['FechaMovimiento'];
                $fechakey = date("Ymd", strtotime($filaLic['Inicio']));

                //armo el movimiento en el array
                $datosInsertar["{$filaLic['Movimiento']}--{$fechakey}-{$filaLic['IdPofa']}-l{$filaLic['IdLicencia']}"] = $filaLic;

                //si tengo configurado que genere movimiento de fin, armo otro movimiento en el array
                if (LIQ_GENEROFINLICENCIAS == 1 && !$redefinoMovimiento) {
                    $filaLic['Movimiento'] = 12;
                    $filaLic['FechaLiquidacion'] = $filaLic['Fin'];
                    if (strtotime($filaLic['FechaLiquidacion']) <= strtotime($filaLic['FechaMovimiento']))
                        $filaLic['FechaLiquidacion'] = $filaLic['FechaMovimiento'];
                    $fechakey = date("Ymd", strtotime($filaLic['Fin']));
                    $datosInsertar["{$filaLic['Movimiento']}--{$fechakey}-{$filaLic['IdPofa']}-l{$filaLic['IdLicencia']}"] = $filaLic;
                }
            }

            //ksort($datosInsertar);
            $i = 0;
            foreach ($datosInsertar as $fila) {

                $fila['Periodo'] = $LiquidacionActiva["Periodo"];//new DateTime($datos['FechaHasta'] ?? '@0');

                $movimiento = $this->_armarFila($fila, $resultadoBD);

                $datosInsertarNov = $resultadoBD;
                $datosInsertarNov['IdLogMovimientos'] = $datos["IdLogMovimiento"];
                $datosInsertarNov['IdEstado'] = $fila["IdEstadoMovimiento"];
                $datosInsertarNov['Orden'] = 1;
                if (!$this->InsertarLogNovedad($datosInsertarNov, $idLogNovTmp)) {
                    $this->setError($this->getError());
                    return false;
                }
            }
        }
        return true;
    }


    public function insertarMovimientoPofa(array $datos, &$codigoInsertado): bool {

        #verifico si me vino por parametro  un IDMovimiento
        if (!isset($datos["IdMovimiento"]) || $datos["IdMovimiento"] == "") {
            $this->setError("Movimiento de liquidacion no definido");
            return false;
        }

        #verifico si me vino por parametro  un IdPofa
        if (!isset($datos["IdPofa"]) || $datos["IdPofa"] == "") {
            $this->setError("IdPofa de liquidacion no definido");
            return false;
        }

        #verifico si me vino por parametro  un IdTipoDocumento
        if (!isset($datos["IdTipoDocumento"]) || $datos["IdTipoDocumento"] == "") {
            $IdTipoDocumento = "";
        } else {
            $IdTipoDocumento = $datos['IdTipoDocumento'];
        }

        $omitirAnulacion = false;
        if (isset($datos['OmitirAnulacion'])) {
            $omitirAnulacion = $datos['OmitirAnulacion'];
        }

        #|1- busco liquidacion activa
        $datosPeriodo = [
            "IdEstado" => 2,//pendiente
            "IdTipoLiquidacion" => 2, //liquidacion
        ];
        if (!$this->BusquedaAvanzada($datosPeriodo, $resultadoPer, $numfilasPer)) {
            $this->setError("Error al buscar liquidacion activa");
            return false;
        }
        if ($numfilasPer <= 0) {
            $this->setError("No existe liquidacion activa para liquidar la novedad seleccionada");
            return false;
        }
        $LiquidacionActiva = $this->conexion->ObtenerSiguienteRegistro($resultadoPer);
        $IdLogMovimiento = $LiquidacionActiva['Id'];

        # En el caso de las reincorporaciones donde no se anulan movimientos previos, omite anulacion
        if (!$omitirAnulacion) {
            #anulo los movimientos de la misma novedad pendientes
            $datosActualizarMovimientos = [
                'IdPofa' => $datos['IdPofa'],
                'IdEstado' => 5,//ANULADO
                'IdMovimiento' => $datos['IdMovimiento'],
                'IdTipoDocumento' => $IdTipoDocumento,
            ];
            if (!$this->ActualizarMovimientosEstadoxIdPofaxIdMovimiento($datosActualizarMovimientos)) {
                $this->setError('Error al anular movimientos pendientes a liquidar de la novedad seleccionada');
                return false;
            }
        }


        #busco la POFA a liquidar
        if (!$this->BuscarPofaLiquidacion($datos, $resultado, $numfilasPofa))
            return false;

        if ($numfilasPofa > 0) {
            #recorro puesto x puesto de la novedad e inserto como movimiento
            while ($filaNov = $this->conexion->ObtenerSiguienteRegistro($resultado)) {

                //il ID de documento viene por parametro
                if (isset($datos['IdDocumento']))
                    $filaNov['IdDocumento'] = $datos['IdDocumento'];

                //il ID de documento viene por parametro
                if (isset($datos['IdTipoDocumento']))
                    $filaNov['IdTipoDocumento'] = $datos['IdTipoDocumento'];

                $filaNov['Movimiento'] = $datos['IdMovimiento'];

                //si me vino por parametro fechaDesde uso esa
                $filaNov['fechaDesde'] = $filaNov['FechaToma'] = $datos['FechaDesde'];

                //armo info general del bloque
                $filaNov['Horas'] = $this->_detectarHorasTipoCargo($filaNov, "nov");
                $filaNov['IdRevistaNueva'] = 'NULL';
                $filaNov['IdRevistaAntigua'] = 'NULL';
                $filaNov['FechaLiquidacion'] = $filaNov['FechaMovimiento'];

                //if (strtotime($filaNov['FechaLiquidacion']) < strtotime($filaNov['fechaDesde']))
                //  $filaNov['FechaLiquidacion'] = $filaNov['fechaDesde'];

                //si hay fecha toma, la fechaDesde es la fecha de toma de posesion
                if ((!isset($datos['FechaDesde']) || $datos['FechaDesde'] == "") && isset($filaNov['FechaToma']) && $filaNov['FechaToma'] != "") {
                    $filaNov['fechaDesde'] = $filaNov['FechaToma'];
                }
                //si es un alta, la fechakey es la desde, en un cese la fecha de baja es la desde
                $fechakey = date("Ymd", strtotime($filaNov['fechaDesde']));

                //si existe un movimiento previo de ese id pofa y mismo idmovimiento lo seteo como mov ya liquidado
                $filaNov["IdEstadoMovimiento"] = 2;//pend liquidacion
                if (isset($filaNov["IdMovLiq"]) && $filaNov["IdMovLiq"] != "" && !$omitirAnulacion) {
                    $filaNov["IdEstadoMovimiento"] = 7;//estado de pendiente de liquidacion/ liquidacion manual
                }

                #agrego el movimiento al array de movimientos
                $datosInsertar["d{$fechakey}-{$filaNov['IdPofa']}-n{$filaNov['IdDocumento']}"] = $filaNov;

                #si tengo esta constante en 1, siempre genero baja
                //echo "LIQ_GENEROFINDOCUMENTOS".LIQ_GENEROFINDOCUMENTOS;
                if (LIQ_GENEROFINDOCUMENTOS == 1) {
                    if (isset($filaNov['fechaHasta']) && $filaNov['fechaHasta'] != "") {
                        $filaNov['Movimiento'] = 2;//baja
                        $filaNov['FechaLiquidacion'] = $filaNov['fechaHasta'];
                        $fechakey = date("Ymd", strtotime($filaNov['fechaHasta']));
                        $datosInsertar["h{$fechakey}-{$filaNov['IdPofa']}-n{$filaNov['IdDocumento']}"] = $filaNov;
                    }
                } elseif (LIQ_GENEROFINDOCUMENTOSFUTUROS == 1) {
                    #genero baja si la fecha Baja es diferente MES a la fecha desde
                    if (isset($filaNov['fechaHasta']) && $filaNov['fechaHasta'] != "") {
                        $periodoDesde = date("Ym", strtotime($filaNov['fechaDesde']));
                        $periodoHasta = date("Ym", strtotime($filaNov['fechaHasta']));
                        //echo $periodoDesde."vs".$periodoHasta;die();
                        if ($periodoDesde != $periodoHasta) {
                            $filaNov['Movimiento'] = 2;//baja
                            $filaNov['FechaLiquidacion'] = $filaNov['fechaHasta'];
                            $fechakey = date("Ymd", strtotime($filaNov['fechaHasta']));
                            $datosInsertar["h{$fechakey}-{$filaNov['IdPofa']}-n{$filaNov['IdDocumento']}"] = $filaNov;
                        }

                    }

                }

            }
            $i = 0;
            foreach ($datosInsertar as $fila) {

                $movimiento = $this->_armarFila($fila, $resultadoBD);

                //anulo movimientos pendientes por IdMovimiento/IdPofa/
                if ($fila['Movimiento'] == "2")// SOLO SI ES UN CESE
                {
                    $datosActualizarMovimientos = [
                        "IdEstado" => 5,//ANULADO
                        "IdMovimiento" => $fila["Movimiento"],
                        "IdPofa" => $fila["IdPofa"],
                    ];
                    if (!$this->ActualizarMovimientosEstadoxIdPofaxIdMovimiento($datosActualizarMovimientos)) {
                        $this->setError("Error al anular movimientos pendientes por movimiento y POFA");
                        return false;
                    }
                }

                $datosInsertarNov = $resultadoBD;
                $datosInsertarNov['IdLogMovimientos'] = $IdLogMovimiento;
                $datosInsertarNov['IdEstado'] = $fila["IdEstadoMovimiento"];
                $datosInsertarNov['Orden'] = 1;

                if (!$this->InsertarLogNovedad($datosInsertarNov, $idLogNovTmp)) {
                    $this->setError($this->getError());
                    return false;
                }
            }
        }
        return true;

    }

    public function BuscarPofaLiquidacion($datos, &$resultado, &$numfilas): bool {
        $sparam = [
            'IdPofa' => '',
            'IdMovimiento' => '',
        ];

        if (isset($datos['IdPofa']) && $datos['IdPofa'] != "") {
            $sparam['IdPofa'] = $datos['IdPofa'];
        }
        if (isset($datos['IdMovimiento']) && $datos['IdMovimiento'] != "") {
            $sparam['IdMovimiento'] = $datos['IdMovimiento'];
        }
        if (!parent::BuscarPofaLiquidacion($sparam, $resultado, $numfilas))
            return false;
        return true;
    }


    public function BuscarMovimientosxLicencia($datos, &$resultado, &$numfilas): bool {
        $sparam = [
            'IdLicencia' => '',
            'xIdEstado' => '',
            'IdEstado' => '',
        ];

        if (isset($datos['IdLicencia']) && $datos['IdLicencia'] != '') {
            $sparam['IdLicencia'] = $datos['IdLicencia'];
        }

        if (isset($datos['IdEstado']) && $datos['IdEstado'] != '') {
            $sparam['IdEstado'] = $datos['IdEstado'];
            $sparam['xIdEstado'] = 1;
        }

        if (!parent::BuscarMovimientosxLicencia($sparam, $resultado, $numfilas))
            return false;
        return true;
    }


    public function ActualizarEstadoxIdLicencia($datos, &$resultado, &$numfilas): bool {
        $sparam = [
            'IdLicencia' => '',
            'IdEstado' => '',
        ];

        if (isset($datos['IdLicencia']) && $datos['IdLicencia'] != '') {
            $sparam['IdLicencia'] = $datos['IdLicencia'];
        }
        if (isset($datos['IdEstado']) && $datos['IdEstado'] != '') {
            $sparam['IdEstado'] = $datos['IdEstado'];
        }
        if (!parent::ActualizarEstadoxIdLicencia($sparam, $resultado, $numfilas))
            return false;

        return true;
    }

    public function anularMovimientoDocumento(array $datos): bool {
        #verifico si me vino por parametro  un IDMovimiento
        if (!isset($datos['IdMovimiento']))
            $datos['IdMovimiento'] = '';

        #ELIMINO los movimientos PENDIENTES de la misma novedad
        #anulo los movimientos de la misma novedad/licencia pendientes
        $datosActualizarMovimientos = [
            'IdDocumento' => $datos['IdDocumento'],
            'IdEstadoAnulado' => 5,//ANULADO
            'IdEstadoLiquidado' => '3,4,7,8',
            'IdMovimiento' => $datos['IdMovimiento'],

        ];

        if (!$this->AnularMovimientosLiquidados($datosActualizarMovimientos)) {
            $this->setError('Error al anular movimientos pendientes a liquidar de la novedad seleccionada');
            return false;
        }
        return true;
    }

    public function anularMovimientoLicencia(array $datos): bool {
        #verifico si me vino por parametro  un IDMovimiento
        if (!isset($datos["IdMovimiento"]))
            $datos["IdMovimiento"] = "";

        #ELIMINO los movimientos PENDIENTES de la misma novedad
        #anulo los movimientos de la misma novedad/licencia pendientes
        $datosActualizarMovimientos = [
            "IdLicencia" => $datos["IdLicencia"],
            "IdEstadoAnulado" => 5,//ANULADO
            "IdEstadoLiquidado" => "3,4,7,8",
            "IdMovimiento" => $datos["IdMovimiento"],

        ];

        if (!$this->AnularMovimientosLiquidados($datosActualizarMovimientos)) {
            $this->setError("Error al anular movimientos pendientes a liquidar de la novedad seleccionada");
            return false;
        }
        return true;
    }

    public function AnularMovimientosLiquidados($datos): bool {
        $sparam = [
            'IdLicencia' => '',
            'xIdLicencia' => '0',
            'IdDocumento' => '',
            'xIdDocumento' => '0',
            'IdMovimiento' => '',
            'xIdMovimiento' => '0',
            'IdEstadoLiquidado' => '-1',
            'IdEstadoAnulado' => '-1',
            'UltimaModificacionFecha' => date("Y-m-d H:i:s"),
            'UltimaModificacionUsuario' => $_SESSION['usuariocod'],
        ];

        if (isset($datos['IdLicencia']) && $datos['IdLicencia'] != '') {
            $sparam['IdLicencia'] = $datos['IdLicencia'];
            $sparam['xIdLicencia'] = 1;
        }
        if (isset($datos['IdDocumento']) && $datos['IdDocumento'] != '') {
            $sparam['IdDocumento'] = $datos['IdDocumento'];
            $sparam['xIdDocumento'] = 1;
        }
        if (isset($datos['IdMovimiento']) && $datos['IdMovimiento'] != '') {
            $sparam['IdMovimiento'] = $datos['IdMovimiento'];
            $sparam['xIdMovimiento'] = 1;
        }
        if (isset($datos['IdEstadoAnulado']) && $datos['IdEstadoAnulado'] != '') {
            $sparam['IdEstadoAnulado'] = $datos['IdEstadoAnulado'];
        }
        if (isset($datos['IdEstadoLiquidado']) && $datos['IdEstadoLiquidado'] != '') {
            $sparam['IdEstadoLiquidado'] = $datos['IdEstadoLiquidado'];
        }
        if (!parent::AnularMovimientosLiquidados($sparam))
            return false;

        return true;
    }
}
