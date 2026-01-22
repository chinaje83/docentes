<?php

abstract class cEscuelasPuestosPersonasDB {
    use ManejoErrores;

    /** @var accesoBDLocal */
    protected $conexion;
    /** @var mixed */
    protected $formato;

    /**
     * Constructor de la clase cEscuelasPuestosPersonasDB.
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
     * Destructor de la clase cEscuelasPuestosPersonasDB.
     */
    function __destruct() {}

    protected function BuscarxCodigo(array $datos, &$resultado, ?int &$numfilas): bool {
        $spnombre = "sel_EscuelasPuestosPersonas_xIdPofa";
        $sparam = [
            'pIdPofa' => $datos['IdPofa'],
        ];
        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            $this->setError(500, 'Error al buscar al buscar por codigo.');
            return false;
        }
        return true;
    }

    /**
     * @param array    $datos
     * @param          $resultado
     * @param int|null $numfilas
     *
     * @return bool
     */
    protected function BuscarxPuestoEstado(array $datos, &$resultado, ?int &$numfilas): bool {
        $spnombre = "sel_EscuelasPuestosPersonas_xIdPuesto_IdEstado";
        $sparam = [
            'pIdPuesto' => $datos['IdPuesto'],
            'pIdEstado' => $datos['IdEstado'],
        ];
        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            $this->setError(500, 'Error al buscar al buscar por puesto y estado.');
            return false;
        }
        return true;
    }


    /**
     * @param array    $datos
     * @param          $resultado
     * @param int|null $numfilas
     *
     * @return bool
     */
    protected function buscarxPuesto(array $datos, &$resultado, ?int &$numfilas): bool {

        $spnombre = "sel_EscuelasPuestosPersonas_xIdPuesto";
        $sparam = [
            'pIdPuesto' => $datos['IdPuesto'],
            'pEstado' => $datos['Estado'] ?? ACTIVO,
        ];

        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            $this->setError(500, 'Error al buscar al buscar por puesto.');
            return false;
        }
        return true;
    }

    protected function BuscarPersonaxIdPuesto(array $datos, &$resultado, ?int &$numfilas): bool {
        $spnombre = 'sel_EscuelasPuestosPersonas_IdPersona_xIdPuesto';
        $sparam = [
            'pIdPuesto' => $datos['IdPuesto'],
            'pBasePersonas' => BASEDATOS_PERSONAS,
        ];
        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, "Error al buscar al buscar por codigo. ", ["archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__], ["formato" => $this->formato]);
            return false;
        }
        return true;
    }

    protected function BuscarxIdPuestoRaiz(array $datos, &$resultado, ?int &$numfilas): bool {

        $spnombre = "sel_EscuelasPuestosPersonas_xIdPuestoRaiz";

        $sparam = [
            'pIdPuesto' => $datos['IdPuesto'],
            'pEstado' => ACTIVO,
        ];

        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            $this->setError(400, 'Error al buscar por Puesto Raiz.');
            return false;
        }

        return true;
    }

    public function BuscarxIdPuestoxIdPersona(array $datos, &$resultado, ?int &$numfilas): bool {
        $spnombre = "sel_EscuelasPuestosPersonas_xIdPuesto_IdPersona";

        $estado = isset($datos['Estado']) && $datos['Estado'] !== '' ? $datos['Estado'] : ACTIVO;

        $sparam = [
            'pIdPuesto' => $datos['IdPuesto'],
            'pIdPersona' => $datos['IdPersona'],
            'pEstado' => $estado,
        ];
        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            $this->setError(400, 'Error al buscar por codigo. ');
            return false;
        }
        return true;
    }

    public function BuscarxIdPuestoxIdPersonaxFechas(array $datos, &$resultado, ?int &$numfilas): bool {
        $spnombre = "sel_EscuelasPuestosPersonas_xIdPuesto_IdPersona_xFechas";

        $estado = isset($datos['Estado']) && $datos['Estado'] !== '' ? $datos['Estado'] : implode(",", [ACTIVO, NOACTIVO]);

        $sparam = [
            'pIdPuesto' => $datos['IdPuesto'],
            'pIdPersona' => $datos['IdPersona'],
            'pEstado' => $estado,
            'pFechaTomaPosesion' => $datos["FechaTomaPosesion"],
            'pFechaHasta' => $datos["FechaHasta"]
        ];
        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            $this->setError(400, 'Error al buscar por codigo. ');
            return false;
        }
        return true;
    }


    public function BuscarxPuestoxIdPersona(array $datos, &$resultado, ?int &$numfilas): bool {
        $spnombre = "sel_EscuelasPuestosPersonas_IdPersona";
        $estado = isset($datos['Estado']) && $datos['Estado'] !== '' ? $datos['Estado'] : ACTIVO;

        $sparam = [
            'pIdPersona' => $datos['IdPersona'],
            'pEstado' => $estado,
        ];
        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            $this->setError(400, 'Error al buscar por codigo. ');
            return false;
        }
        return true;
    }


    protected function BuscarxIdPofaxIdPersona(array $datos, &$resultado, ?int &$numfilas): bool {
        $spnombre = "sel_EscuelasPuestosPersonas_xIdPofa_xIdPersona";
        $sparam = [
            'pIdPofa' => $datos['IdPofa'],
            'pIdPersona' => $datos['IdPersona'],
        ];
        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, "Error al buscar al buscar por codigo. ", ["archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__], ["formato" => $this->formato]);
            return false;
        }
        return true;
    }


    protected function buscarParaElastic(array $datos, &$resultado, ?int &$numfilas): bool {

        $spnombre = "sel_EscuelasPuestosPersonas_xParaElastic";
        $sparam = [
            'pBasePersonas' => BASEDATOS_PERSONAS,
            'pIdPofa' => $datos['IdPofa'],
        ];
        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, "Error al buscar los datos de la persona para el cargo. ", ["archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__], ["formato" => $this->formato]);
            return false;
        }
        return true;
    }

    protected function buscarParaElasticxEscuela(array $datos, &$resultado, ?int &$numfilas): bool {

        if (!$this->conexion->buscarStoredProcedure("sel_EscuelasPuestosPersonas_xParaElastic", $sql))
            return false;

        $sql = preg_replace('/#pBasePersonas#/', BASEDATOS_PERSONAS, $sql);
        $sql = preg_replace('/WHERE IdPofa = "#pIdPofa#"/', "$1 WHERE EP.IdEscuela IN ({$datos['IdEscuela']})", $sql);

        if (!$this->conexion->ejecutarSQL($sql, 'SEL', $resultado, $numfilas, $errno)) {
            $this->setError(400, "Error al buscar el puesto por codigo. ");
            return false;
        }
        return true;
    }


    protected function buscarParaHistoricos(array $datos, &$resultado, ?int &$numfilas): bool {
        $spnombre = "sel_EscuelasPuestosPersonas_xParaHistoricos";
        $sparam = [
            'pBase' => BASEDATOS_PERSONAS,
            'pIdPofa' => $datos['IdPofa'],
        ];
        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, "Error al buscar los datos de la persona para el cargo. ", ["archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__], ["formato" => $this->formato]);
            return false;
        }
        return true;
    }

    protected function buscarHistorico(array $datos, &$resultado, ?int &$numfilas): bool {
        $spnombre = "sel_EscuelasPuestosPersonas_historico";
        $sparam = [
            'pIdPersona' => $datos['IdPersona'],
        ];

        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            $this->setError(400, "Error al buscar los datos de la persona para el cargo. ");
            return false;
        }

        return true;
    }

    public function buscarCargoxIdPuestoxIdPersona(array $datos, &$resultado, &$numfilas) {

        $spnombre = 'sel_EscuelasPuestosPersonas_Cargo_xIdPuesto_xIdPersona';
        $sparam = [
            'pIdPuesto' => $datos['IdPuesto'],
            'pIdPersona' => $datos['IdPersona'],
        ];

        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            $this->setError('Error interno en búsqueda de datos. Comuniquese con el area de Sistemas');
            return false;
        }

        return true;
    }

    protected function buscarTotalAgentesxEscuelas(&$resultado, &$numfilas) {

        $spnombre = 'sel_EscuelasPuestosPersonas_total_habilitadas';
        $sparam = [
            'pIdEscuelaExcluir' => ESCUELAS_DE_PRUEBA,
        ];

        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            $this->setError('Error interno en búsqueda de datos. Comuniquese con el area de Sistemas');
            return false;
        }

        return true;
    }

    protected function buscarLicenciasxPuesto(array $datos, &$resultado, ?int &$numfilas): bool {

        $spnombre = 'sel_EscuelasPuestosPersonas_Licencia';
        $sparam = [
            'pBaseLicencias' => BASEDATOSLICENCIAS,
            'pIdPuesto' => $datos['IdPuesto'],
        ];

        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            $this->setError('Error interno en búsqueda de datos. Comuniquese con el area de Sistemas');
            return false;
        }

        return true;
    }

    protected function buscarSuplenciasVencidasEscuela($datos, &$resultado, ?int &$numfilas): bool {

        $spnombre = 'sel_EscuelasPuestosPersonas_xIdRevista_xIdEscuela';
        $sparam = [
            'pIdEscuela' => $datos['IdEscuela'],
            'pFechaHoy' => date("Y-m-d"),
        ];

        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            $this->setError('Error interno en búsqueda de datos de suplencias vencidas. Comuniquese con el area de Sistemas');
            return false;
        }

        return true;
    }

    protected function buscarSuplenciasVencidas(&$resultado, ?int &$numfilas): bool {

        $spnombre = 'sel_EscuelasPuestosPersonas_xIdRevista_xFechaHasta';
        $sparam = [
            'pEscuelasPrueba' => ESCUELAS_DE_PRUEBA,
            'pFechaHoy' => date("Y-m-d"),
        ];

        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            $this->setError('Error interno en búsqueda de datos de suplencias vencidas. Comuniquese con el area de Sistemas');
            return false;
        }

        return true;
    }

    protected function buscarSuplencias(&$resultado, ?int &$numfilas): bool {

        $spnombre = 'sel_EscuelasPuestosPersonas_xIdRevista';
        $sparam = [
            'pEscuelasPrueba' => ESCUELAS_DE_PRUEBA,
        ];

        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            $this->setError('Error interno en búsqueda de datos de suplencias vencidas. Comuniquese con el area de Sistemas');
            return false;
        }

        return true;
    }

    protected function BuscarIdPofaMigracionxIdPersona(array $datos, &$resultado, ?int &$numfilas): bool {

        $spnombre = 'sel_EscuelasPuestosPersonas_IdPersona_xIdPofaMigracion_xEstado';
        $sparam = [
            'pIdPofaMigracion' => $datos['IdPofaMigracion'],
            'pIdPersona' => $datos['IdPersona'],
        ];
        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, "Error al buscar al buscar por codigo. ", ["archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__], ["formato" => $this->formato]);
            return false;
        }
        return true;
    }


    protected function BusquedaAvanzada(array $datos, &$resultado, ?int &$numfilas): bool {
        $spnombre = "sel_EscuelasPuestosPersonas_busqueda_avanzada";
        $sparam = [
            /*'pBasePersona' => BASEDATOS_PERSONAS,*/
            'pxIdPofa' => $datos['xIdPofa'],
            'pIdPofa' => $datos['IdPofa'],
            'pxIdPuesto' => $datos['xIdPuesto'],
            'pIdPuesto' => $datos['IdPuesto'],
            'pxIdPersona' => $datos['xIdPersona'],
            'pIdPersona' => $datos['IdPersona'],
            'pxIdPofaSuperior' => $datos['xIdPofaSuperior'],
            'pIdPofaSuperior' => $datos['IdPofaSuperior'],
            'pxIdRevista' => $datos['xIdRevista'],
            'pIdRevista' => $datos['IdRevista'],
            'pxCodigoLiquidador' => $datos['xCodigoLiquidador'],
            'pCodigoLiquidador' => $datos['CodigoLiquidador'],
            'pxFechaDesde' => $datos['xFechaDesde'],
            'pFechaDesde' => $datos['FechaDesde'],
            'pxFechaHasta' => $datos['xFechaHasta'],
            'pFechaHasta' => $datos['FechaHasta'],
            'pxFechaDesignacion' => $datos['xFechaDesignacion'],
            'pFechaDesignacion' => $datos['FechaDesignacion'],
            'pxFechaTomaPosesion' => $datos['xFechaTomaPosesion'],
            'pFechaTomaPosesion' => $datos['FechaTomaPosesion'],
            'pxEstado' => $datos['xEstado'],
            'pEstado' => $datos['Estado'],
            'plimit' => $datos['limit'],
            'porderby' => $datos['orderby'],
        ];
        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, "Error al realizar la búsqueda avanzada. ", ["archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__], ["formato" => $this->formato]);
            return false;
        }
        return true;
    }


    protected function buscarSuplentesDeLaPersona2(array $datos, &$resultados, &$numfilas): bool {
        $spnombre = 'sel_EscuelasPuestosPersonas_blk_xIdPuesto_IdPersona';
        $sparam = [
            'pIdPersona' => $datos['IdPersona'],
            'pIdPuesto' => $datos['IdPuesto'],
        ];

        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultados, $numfilas, $errno)) {
            $this->setError(500, 'Error al buscar');
            return false;
        }


        return true;
    }

    protected function buscarPorPadre(array $datos, &$resultados, &$numfilas): bool {
        $spnombre = 'sel_EscuelasPuestosPersonas_Padre_xIdPuesto';
        $sparam = [
            'pIdPuesto' => $datos['IdPuesto'],
        ];

        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultados, $numfilas, $errno)) {
            $this->setError(500, 'Error al buscar');
            return false;
        }


        return true;
    }


    protected function BuscarAuditoriaRapida(array $datos, &$resultado, ?int &$numfilas): bool {
        $spnombre = "sel_EscuelasPuestosPersonas_AuditoriaRapida";
        $sparam = [
            'pIdPersona' => $datos['IdPersona'],
        ];
        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, "Error al buscar al buscar por codigo. ", ["archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__], ["formato" => $this->formato]);
            return false;
        }
        return true;
    }


    protected function BuscarEscuelaPOFA(array $datos, &$resultado, ?int &$numfilas): bool {
        $spnombre = "sel_EscuelasPuestosPersonas_xIdDivision";
        $sparam = [
            'pxIdEstado' => $datos['xIdEstado'],
            'pIdEstado' => $datos['IdEstado'],
            'pIdSeccion' => $datos['IdSeccion'],
        ];
        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, "Error al buscar al buscar por codigo. ", ["archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__], ["formato" => $this->formato]);
            return false;
        }
        return true;
    }


    protected function Insertar(array $datos, ?int &$codigoInsertado): bool {
        $msg['IsSucceed'] = true;
        $spnombre = "ins_EscuelasPuestosPersonas";
        $sparam = [
            'pIdPuesto' => $datos['IdPuesto'],
            'pIdPersona' => $datos['IdPersona'],
            'pIdPofaSuperior' => $datos['IdPofaSuperior'],
            'pIdPofaOrigen' => $datos['IdPofaOrigen'],
            'pIdRevista' => $datos['IdRevista'],
            'pCodigoLiquidador' => $datos['CodigoLiquidador'],
            'pFechaDesde' => $datos['FechaDesde'],
            'pFechaHasta' => $datos['FechaHasta'],
            'pFechaDesignacion' => $datos['FechaDesignacion'],
            'pFechaTomaPosesion' => $datos['FechaTomaPosesion'],
            'pFechaHastaPosesion' => $datos['FechaHastaPosesion'],
            'pOrden' => $datos['Orden'],
            'pInstrumentoLegal' => $datos['InstrumentoLegal'],
            'pIdPofaMigracion' => $datos['IdPofaMigracion'],
            'pIdExcepcionTipo' => $datos['IdExcepcionTipo'],
            'pEstado' => $datos['Estado'],
            'pIdEstado' => $datos['IdEstado'],
            'pAltaFecha' => $datos['AltaFecha'],
            'pAltaUsuario' => $datos['AltaUsuario'],
            'pUltimaModificacionFecha' => $datos['UltimaModificacionFecha'],
            'pUltimaModificacionUsuario' => $datos['UltimaModificacionUsuario'],
        ];
        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, "Error al insertar. ", ["archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__], ["formato" => $this->formato]);
            return false;
        }
        $codigoInsertado = $this->conexion->UltimoCodigoInsertado();
        return true;
    }

    /**
     * @param array $datos
     *
     * @return bool
     */
    protected function Modificar(array $datos): bool {
        $spnombre = "upd_EscuelasPuestosPersonas_xIdPofa";
        $sparam = [
            'pIdRevista' => $datos['IdRevista'],
            'pCodigoLiquidador' => $datos['CodigoLiquidador'],
            'pFechaDesde' => $datos['FechaDesde'],
            'pFechaHasta' => $datos['FechaHasta'],
            'pFechaDesignacion' => $datos['FechaDesignacion'],
            'pFechaTomaPosesion' => $datos['FechaTomaPosesion'],
            'pFechaHastaPosesion' => $datos['FechaHastaPosesion'],
            'pIdPofaMigracion' => $datos['IdPofaMigracion'],
            'pUltimaModificacionFecha' => date("Y-m-d H:i:s"),
            'pUltimaModificacionUsuario' => $_SESSION['usuariocod'],
            'pIdPofa' => $datos['IdPofa'],
            'pIdExcepcionTipo' => $datos["IdExcepcionTipo"],
        ];
        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, "Error al modificar. ", ["archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__], ["formato" => $this->formato]);
            return false;
        }
        return true;
    }

    /**
     * @param array $datos
     *
     * @return bool
     */
    protected function ModificarPuestoOrigen(array $datos): bool {
        $spnombre = "upd_EscuelasPuestosPersonas_xIdPuestoOrigen";
        $sparam = [
            'pIdPuesto' => $datos['IdPuesto'],
            'pIdEstado' => $datos['IdEstado'],
            'pUltimaModificacionFecha' => date("Y-m-d H:i:s"),
            'pUltimaModificacionUsuario' => $_SESSION['usuariocod'],
        ];
        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, "Error al modificar. ", ["archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__], ["formato" => $this->formato]);
            return false;
        }
        return true;
    }

    /**
     * @param array $datos
     *
     * @return bool
     */
    protected function ModificarFechaHasta(array $datos): bool {
        $spnombre = "upd_EscuelasPuestosPersonas_FechaHasta_xIdPofa";
        $sparam = [
            'pFechaHasta' => $datos['FechaHasta'],
            'pUltimaModificacionFecha' => date("Y-m-d H:i:s"),
            'pUltimaModificacionUsuario' => $_SESSION['usuariocod'],
            'pIdPofa' => $datos['IdPofa'],
        ];
        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, "Error al modificar. ", ["archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__], ["formato" => $this->formato]);
            return false;
        }
        return true;
    }

    protected function ExtensionFechaHasta(array $datos): bool {
        $spnombre = "upd_EscuelasPuestosPersonas_FechaHasta_xIdPofa";
        $sparam = [
            'pFechaHasta' => $datos['FechaHasta'],
            'pUltimaModificacionFecha' => date("Y-m-d H:i:s"),
            'pUltimaModificacionUsuario' => $datos['UltimaModificacionUsuario'],
            'pIdPofa' => $datos['IdPofa'],
        ];
        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, "Error al modificar. ", ["archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__], ["formato" => $this->formato]);
            return false;
        }
        return true;
    }

    /**
     * @param array $datos
     *
     * @return bool
     */
    protected function ModificarExisteInconsistencia(array $datos): bool {
        $spnombre = "upd_EscuelasPuestosPersonas_ExisteInconsistencia_xIdPofa";
        $sparam = [
            'pExisteInconsistencia' => $datos['ExisteInconsistencia'],
            'pJsonInconsistencia' => $datos['JsonInconsistencia'],
            'pIdPofa' => $datos['IdPofa'],
        ];
        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            $this->setError(400, "Error al registrar inconsistencia. ");
            return false;
        }
        return true;
    }


    protected function ModificarOrden(array $datos): bool {

        $spnombre = 'upd_EscuelasPuestosPersonas_Orden_xIdPuesto';
        $sparam = [
            'pUltimaModificacionFecha' => date("Y-m-d H:i:s"),
            'pUltimaModificacionUsuario' => $_SESSION['usuariocod'],
            'pIdPuesto' => $datos['IdPuesto'],
        ];

        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            $this->setError(400, 'Error al modificar. ');
            return false;
        }

        return true;
    }


    protected function ReacomodarPuesto(array $datos): bool {

        $spnombre = 'upd_EscuelasPuestos_Reacomodar_xIdPuesto';
        $sparam = [
            'pIdPuesto' => $datos['IdPuesto'],
            'pUltimaModificacionFecha' => date("Y-m-d H:i:s"),
            'pUltimaModificacionUsuario' => $_SESSION['usuariocod'],

        ];

        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            $this->setError(400, 'Error al modificar. ');
            return false;
        }

        return true;
    }


    protected function Eliminar(array $datos): bool {
        $spnombre = "del_EscuelasPuestosPersonas_xIdPofa";
        $sparam = [
            'pIdPofa' => $datos['IdPofa'],
        ];
        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, "Error al eliminar por codigo. ", ["archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__], ["formato" => $this->formato]);
            return false;
        }
        return true;
    }


    protected function ModificarEstado(array $datos): bool {
        $spnombre = "upd_EscuelasPuestosPersonas_Estado_xIdPofa";
        $sparam = [
            'pEstado' => $datos['Estado'],
            'pUltimaModificacionUsuario' => $_SESSION['usuariocod'],
            'pUltimaModificacionFecha' => date('Y-m-d H:i:s'),
            'pIdPofa' => $datos['IdPofa'],
        ];
        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            $this->setError(400, 'Error al modificar el estado. ');
            return false;
        }
        return true;
    }

    protected function ModificarRevista(array $datos): bool {
        $spnombre = 'upd_EscuelasPuestosPersonas_IdRevista_xIdPuesto_xIdPersona';
        $sparam = [
            'pIdRevista' => $datos['IdRevista'],
            'pIdPersona' => $datos['IdPersona'],
            'pIdPuesto' => $datos['IdPuesto'],
        ];

        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, "Error al modificar el estado. ", ["archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__], ["formato" => $this->formato]);
            return false;
        }
        return true;
    }

    protected function ModificarIdEstado(array $datos): bool {
        $spnombre = "upd_EscuelasPuestosPersonas_IdEstado_xIdPofa";
        $sparam = [
            'pIdEstado' => $datos['IdEstado'],
            'pUltimaModificacionUsuario' => $datos['UltimaModificacionUsuario'],
            'pUltimaModificacionFecha' => $datos['UltimaModificacionFecha'],
            'pIdPofa' => $datos['IdPofa'],
        ];
        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            $this->setError(400, 'Error al modificar el estado. ');
            return false;
        }
        return true;
    }


    protected function BuscarUltimoOrden($datos, &$resultado, &$numfilas) {
        $spnombre = "sel_EscuelasPuestosPersonas_max_orden_xIdPuesto";
        $sparam = [
            'pIdPuesto' => $datos['IdPuesto'],
        ];
        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, "Error al buscar el maximo orden. ", ["archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__], ["formato" => $this->formato]);
            return false;
        }

        return true;
    }


    protected function buscarLicenciasxPofa($datos, &$resultado, &$numfilas) {
        $spnombre = "sel_EscuelasPuestosPersonas_Licencia_xPofa";
        $sparam = [
            'pBaseLicencias' => BASEDATOSLICENCIAS,
            'pIdPofa' => $datos['IdPofa'],
        ];
        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, "Error al buscar licencias por POFA. ", ["archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__], ["formato" => $this->formato]);
            return false;
        }

        return true;
    }

    protected function ModificarEstadoFechaHasta(array $datos): bool {
        $spnombre = "upd_EscuelasPuestosPersonas_EstadoFechHasta_xIdPofa";
        $sparam = [
            'pEstado' => $datos['Estado'],
            'pFechaHasta' => $datos['FechaHasta'],
            'pUltimaModificacionUsuario' => $_SESSION['usuariocod'],
            'pUltimaModificacionFecha' => date('Y-m-d H:i:s'),
            'pIdPofa' => $datos['IdPofa'],
        ];
        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            $this->setError(400, 'Error al modificar el estado. ');
            return false;
        }
        return true;
    }

    /**
     * @param array $datos
     *
     * @return bool
     */
    protected function ModificarCodigoLiquidador(array $datos): bool {
        $spnombre = "upd_EscuelasPuestosPersonas_CodigoLiquidador_xIdPofa";
        $sparam = [
            'pCodigoLiquidador' => $datos['CodigoLiquidador'],
            'pUltimaModificacionFecha' => date("Y-m-d H:i:s"),
            'pUltimaModificacionUsuario' => $_SESSION['usuariocod'],
            'pIdPofa' => $datos['IdPofa'],
        ];
        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, "Error al modificar. ", ["archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__], ["formato" => $this->formato]);
            return false;
        }
        return true;
    }

    protected function BuscarExcepcionesTipo(array $datos, &$resultado, ?int &$numfilas): bool {
        $spnombre = "sel_EscuelasPuestosPersonasExcepcionesTipo_xIdEstado";
        $sparam = [
            'pIdEstado' => $datos['IdEstado'],
        ];
        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            $this->setError(500, 'Error al buscar las excepciones.');
            return false;
        }
        return true;
    }

    protected function BuscarExcepcionesTipoxId(array $datos, &$resultado, ?int &$numfilas): bool {
        $spnombre = "sel_EscuelasPuestosPersonasExcepcionesTipo_xIdExcepcionTipo";
        $sparam = [
            'pIdExcepcionTipo' => $datos['IdExcepcionTipo'],
        ];
        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            $this->setError(500, 'Error al buscar las excepciones.');
            return false;
        }
        return true;
    }

    protected function BuscarPorEscuelaPorPersona(array $datos, &$resultado, ?int &$numfilas): bool {
        $spnombre = "sel_EscuelasPuestosPersonas_xIdEscuela_xIdPersona";
        $sparam = [
            'pIdPersona' => $datos['IdPersona'],
            'pIdEscuela' => $datos["IdEscuela"],
        ];
        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            $this->setError(500, 'Error al buscar las excepciones.');
            return false;
        }
        return true;
    }
    /**
     * @param array $datos
     *
     * @return bool
     */
    protected function InsertarRepublicarElastic(array $datos): bool {
        $spnombre = "ins_EscuelasRepublicarElastic";
        $sparam = [
            'pIdEscuela' => $datos['IdEscuela'],
            'pBaseInterfaz' => BDINTERFACES
        ];
        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, "Error al modificar. ", ["archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__], ["formato" => $this->formato]);
            return false;
        }
        return true;
    }
}
