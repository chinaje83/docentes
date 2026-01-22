<?php

abstract class cPeriodosLiquidacionDB {
    /** @var accesoBDLocal */
    protected $conexion;
    /** @var mixed */
    protected $formato;
    /** @var array */
    protected $error;

    /**
     * Constructor de la clase cPeriodosLiquidacionDB.
     *
     * Recibe un objeto accesoBDLocal y el formato a de  los mensajes de salida
     * $formato = FMT_TEXTO escribe en pantalla una caja con el mensaje de error, el tipo de caja depende del nivel de error
     *            FMT_ARRAY escribe el mensaje de error en la propiedad $error de la clase la cual puede ser accedida desde el método getError()
     *            otros escribe en pantalla el mensaje en texto plano
     *
     * @param accesoBDLocal $conexion
     * @param mixed         $formato
     */
    function __construct(accesoBDLocal $conexion, $formato) {

        $this->conexion = &$conexion;
        $this->formato = &$formato;
    }

    /**
     * Destructor de la clase cPeriodosLiquidacionDB.
     */
    function __destruct() {}

    /**
     * Devuelve el mensaje de error almacenado
     *
     * @return array
     */
    public abstract function getError(): array;


    /**
     * Guarda un mensaje de error
     *
     * @param string|array $error
     * @param string       $error_description
     */
    protected function setError($error, $error_description = ''): void {
        $this->error = is_array($error) ? $error : ['error' => $error, 'error_description' => $error_description];
    }

    protected function BuscarxCodigo(array $datos, &$resultado, ?int &$numfilas): bool {
        $spPeriodo = "sel_PeriodosLiquidacion_xId";
        $sparam = [
            'pId' => $datos['Id'],
        ];
        if (!$this->conexion->ejecutarStoredProcedure($spPeriodo, $sparam, $resultado, $numfilas, $errno)) {
            FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, "Error al buscar al buscar por codigo. ", ["archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__], ["formato" => $this->formato]);
            return false;
        }
        return true;
    }

    protected function BuscarExistexEstados(array $datos, &$resultado, ?int &$numfilas): bool {
        $spPeriodo = "sel_LogMovimientos_xIdPeriodo_IdTipoLiquidacion_IdEstados";
        $sparam = [
            'pIdPeriodo' => $datos['IdPeriodo'],
            'pIdTipoLiquidacion' => $datos['IdTipoLiquidacion'],
            'pIdEstado' => $datos['IdEstado'],
        ];
        if (!$this->conexion->ejecutarStoredProcedure($spPeriodo, $sparam, $resultado, $numfilas, $errno)) {
            FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, "Error al buscar al buscar existencia de periodo sin finalizar. ", ["archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__], ["formato" => $this->formato]);
            return false;
        }
        return true;
    }


    protected function BuscarLicenciasLiquidacion(array $datos, &$resultado, ?int &$numfilas): bool {
        $spPeriodo = "sel_licencias_a_liquidar";

            $sparam = [
                'pBaseDatosLicencias' => BASEDATOSLICENCIAS,
                'pBaseDatosPersonas' => BASEDATOS_PERSONAS,
                'pBaseDatosDocentes' => BASEDATOS,
                'pxEstadosLicencias' => $datos['xEstadosLicencias'],
                'pEstadosLicencias' => $datos['EstadosLicencias'],
                'pFechaDesde' => $datos['FechaDesde'],
                'pFechaHasta' => $datos['FechaHasta'],
                'pExcluir_Escuela' => $datos['Excluir_Escuela'],
                'pxEscuelas' => $datos['xEscuelas'],
                'pEscuelas' => $datos['Escuelas'],
                'plimit' => $datos['limit'],
            ];

        if (!$this->conexion->ejecutarStoredProcedure($spPeriodo, $sparam, $resultado, $numfilas, $errno)) {
            FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, "Error al realizar la búsqueda avanzada. ", ["archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__], ["formato" => $this->formato]);
            return false;
        }
        return true;
    }


    protected function BuscarDocumentosLiquidacion(array $datos, &$resultado, ?int &$numfilas): bool {
        $spPeriodo = "sel_documentos_a_liquidar";

        $sparam = [
            'pBaseDatosPersonas' => BASEDATOS_PERSONAS,
            'pBaseDatosDocentes' => BASEDATOS,
            'pFechaDesde' => $datos['FechaDesde'],
            'pFechaHasta' => $datos['FechaHasta'],
            'pxEstadosFinales' => $datos['xEstadosFinales'],
            'pEstadosFinales' => $datos['EstadosFinales'],
            'pExcluir_Escuela' => $datos['Excluir_Escuela'],
//              'pMovimientosAltaBaja'=> $datos['MovimientosAltaBaja'],
//              'pNotDocumentos'=> $datos['NotDocumentos'],
            'pxEscuelas' => $datos['xEscuelas'],
            'pEscuelas' => $datos['Escuelas'],
            'plimit' => $datos['limit'],
        ];

        if (!$this->conexion->ejecutarStoredProcedure($spPeriodo, $sparam, $resultado, $numfilas, $errno)) {
            FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, "Error al realizar la búsqueda avanzada. ", ["archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__], ["formato" => $this->formato]);
            return false;
        }
        return true;
    }

    protected function buscarUltimoPeriodoLiquidado(array $datos, &$resultado, ?int &$numfilas) {

        $spnombre = "sel_LogMovimientos_xIdTipoLiquidacion_xIdEstado_Limit";
        $sparam = [
            'pIdTipoLiquidacion' => $datos['IdTipoLiquidacion'],
            'pIdEstado' => $datos['IdEstado'],
        ];

        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            $this->setError(400, utf8_encode('Error al buscar último periodo liquidatorio.'));
            return false;
        }

        return true;
    }


    protected function buscarLicenciasTiempoReal(array $datos, &$resultado, ?int &$numfilas): bool {

        $spPeriodo = "sel_PeriodosLiquidacion_Licencias_tiempo_real";
        $sparam = [
            'pBaseDatosLicencias' => BASEDATOSLICENCIAS,
            'pBaseDatosPersonas' => BASEDATOS_PERSONAS,
            'pBaseDatosDocentes' => BASEDATOS,
            'pFechaDesde' => $datos['FechaDesde'],
            'pFechaHasta' => $datos['FechaHasta'],
            'pExcluir_Escuela' => $datos['Excluir_Escuela'],
            'pxEscuelas' => $datos['xEscuelas'],
            'pEscuelas' => $datos['Escuelas'],
            'plimit' => $datos['limit'],
        ];

        if (!$this->conexion->ejecutarStoredProcedure($spPeriodo, $sparam, $resultado, $numfilas, $errno)) {
            FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, "Error al buscar licencias. ", ["archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__], ["formato" => $this->formato]);
            return false;
        }
        return true;
    }

    protected function buscarDocumentosTiempoReal(array $datos, &$resultado, ?int &$numfilas): bool {

        $spPeriodo = "sel_PeriodosLiquidacion_Documentos_tiempo_real";
        $sparam = [
            'pBaseDatosPersonas' => BASEDATOS_PERSONAS,
            'pFechaDesde' => $datos['FechaDesde'],
            'pFechaHasta' => $datos['FechaHasta'],
            'pEstadosFinales' => $datos['EstadosFinales'],
            'pExcluir_Escuela' => $datos['Excluir_Escuela'],
            'pMovimientosAltaBaja' => $datos['MovimientosAltaBaja'],
            'pNotDocumentos' => $datos['NotDocumentos'],
            'pxEscuelas' => $datos['xEscuelas'],
            'pEscuelas' => $datos['Escuelas'],
            'plimit' => $datos['limit'],
        ];

        if (!$this->conexion->ejecutarStoredProcedure($spPeriodo, $sparam, $resultado, $numfilas, $errno)) {
            FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, "Error al buscar documentos.", ["archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__], ["formato" => $this->formato]);
            return false;
        }
        return true;
    }


    protected function BusquedaAvanzada(array $datos, &$resultado, ?int &$numfilas): bool {
        $spPeriodo = "sel_PeriodosLiquidacion_busqueda_avanzada";
        $sparam = [
            'pxId' => $datos['xId'],
            'pId' => $datos['Id'],
            'pFechaDesde' => $datos['FechaDesde'],
            'pFechaHasta' => $datos['FechaHasta'],
            'pxPeriodo' => $datos['xPeriodo'],
            'pPeriodo' => $datos['Periodo'],
            'pxEstado' => $datos['xEstado'],
            'pEstado' => $datos['Estado'],
            'plimit' => $datos['limit'],
            'porderby' => $datos['orderby'],
        ];
        if (!$this->conexion->ejecutarStoredProcedure($spPeriodo, $sparam, $resultado, $numfilas, $errno)) {
            FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, "Error al realizar la búsqueda avanzada. ", ["archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__], ["formato" => $this->formato]);
            return false;
        }
        return true;
    }

    protected function Insertar(array $datos, ?int &$codigoInsertado): bool {
        $spPeriodo = "ins_PeriodosLiquidacion";
        $sparam = [
            'pFechaDesde' => $datos['FechaDesde'],
            'pFechaHasta' => $datos['FechaHasta'],
            'pFechaFinReal' => $datos['FechaFinReal'],
            'pPeriodo' => $datos['Periodo'],
            'pEstado' => $datos['Estado'],
            'pAltaFecha' => $datos['AltaFecha'],
            'pAltaUsuario' => $datos['AltaUsuario'],
            'pUltimaModificacionFecha' => $datos['UltimaModificacionFecha'],
            'pUltimaModificacionUsuario' => $datos['UltimaModificacionUsuario'],
        ];
        if (!$this->conexion->ejecutarStoredProcedure($spPeriodo, $sparam, $resultado, $numfilas, $errno)) {
            FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, "Error al insertar. ", ["archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__], ["formato" => $this->formato]);
            return false;
        }
        $codigoInsertado = $this->conexion->UltimoCodigoInsertado();
        return true;
    }


    protected function Modificar(array $datos): bool {
        $spPeriodo = "upd_PeriodosLiquidacion_xId";
        $sparam = [
            'pFechaDesde' => $datos['FechaDesde'],
            'pFechaHasta' => $datos['FechaHasta'],
            'pFechaFinReal' => $datos['FechaFinReal'],
            'pPeriodo' => $datos['Periodo'],
            'pUltimaModificacionFecha' => date("Y/m/d H:i:s"),
            'pUltimaModificacionUsuario' => $_SESSION['usuariocod'],
            'pId' => $datos['Id'],
        ];
        if (!$this->conexion->ejecutarStoredProcedure($spPeriodo, $sparam, $resultado, $numfilas, $errno)) {
            FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, "Error al modificar. ", ["archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__], ["formato" => $this->formato]);
            return false;
        }
        return true;
    }


    protected function Eliminar(array $datos): bool {
        $spPeriodo = "del_PeriodosLiquidacion_xId";
        $sparam = [
            'pId' => $datos['Id'],
        ];
        if (!$this->conexion->ejecutarStoredProcedure($spPeriodo, $sparam, $resultado, $numfilas, $errno)) {
            FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, "Error al eliminar por codigo. ", ["archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__], ["formato" => $this->formato]);
            return false;
        }
        return true;
    }


    protected function ModificarEstado(array $datos): bool {
        $spPeriodo = "upd_PeriodosLiquidacion_Estado_xId";
        $sparam = [
            'pEstado' => $datos['Estado'],
            'pId' => $datos['Id'],
        ];
        if (!$this->conexion->ejecutarStoredProcedure($spPeriodo, $sparam, $resultado, $numfilas, $errno)) {
            FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, "Error al modificar el estado. ", ["archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__], ["formato" => $this->formato]);
            return false;
        }
        return true;
    }

    protected function BuscarxIdLogMovimiento(array $datos, &$resultado, ?int &$numfilas): bool {
        $spPeriodo = "sel_PeriodoLiquidacion_xIdLogMovimiento";
        $sparam = [
            'pIdLogMovimientos' => $datos['IdLogMovimientos'],
        ];
        if (!$this->conexion->ejecutarStoredProcedure($spPeriodo, $sparam, $resultado, $numfilas, $errno)) {
            FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, "Error al buscar al buscar por codigo. ", ["archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__], ["formato" => $this->formato]);
            return false;
        }
        return true;
    }


}
