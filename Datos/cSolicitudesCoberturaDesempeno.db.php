<?php

abstract class cSolicitudesCoberturaDesempenoDB {
    use ManejoErrores;

    /** @var accesoBDLocal */
    protected $conexion;
    /** @var mixed */
    protected $formato;
    /** @var array */
    protected $error;

    public const CONFLICTO_REGLAS = 'reglas';
    public const CONFLICTO_HORARIO = 'horario';
    public const CONFLICTO_AMBOS = 'ambos';

    /**
     * Constructor de la clase cSolicitudesCoberturaDesempenoDB.
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
     * Destructor de la clase cSolicitudesCoberturaDesempenoDB.
     */
    function __destruct() {}

    protected function SolicitudesCoberturaSP(?string &$spnombre, ?array &$sparam): void {
        $spnombre = 'sel_SolicitudesCobertura_combo_Observaciones';
        $sparam = [];
    }

    public abstract function SolicitudesCoberturaSPResult(&$resultado, ?int &$numfilas): bool;


    protected function EscuelasPuestosSP(?string &$spnombre, ?array &$sparam): void {
        $spnombre = 'sel_EscuelasPuestos_combo_CodigoPuesto';
        $sparam = [];
    }

    public abstract function EscuelasPuestosSPResult(&$resultado, ?int &$numfilas): bool;


    protected function EscuelasPuestosDesempenoSP(?string &$spnombre, ?array &$sparam): void {
        $spnombre = 'sel_EscuelasPuestosDesempeno_combo_Dia';
        $sparam = [];
    }

    public abstract function EscuelasPuestosDesempenoSPResult(&$resultado, ?int &$numfilas): bool;


    protected function BuscarxCodigo(array $datos, &$resultado, ?int &$numfilas): bool {
        $spnombre = "sel_SolicitudesCoberturaDesempeno_xId";
        $sparam = [
            'pId' => $datos['Id'],
        ];
        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            $this->setError(400, "Error al buscar al buscar por codigo. ");
            return false;
        }
        return true;
    }


    protected function buscarxSolicitud(array $datos, &$resultado, ?int &$numfilas): bool {
        $spnombre = "sel_SolicitudesCoberturaDesempeno_xIdSolicitudCobertura";
        $sparam = [
            'pIdSolicitudCobertura' => $datos['IdSolicitudCobertura'],
            'pIdEstado' => $datos['IdEstado'],
            'pxIdEstado' => $datos['xIdEstado'],
        ];
        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            $this->setError(400, "Error al buscar por solicitud. --");
            return false;
        }
        return true;
    }

    protected function buscarxSolicitudxPersona(array $datos, &$resultado, ?int &$numfilas): bool {
        $spnombre = "sel_SolicitudesCoberturaDesempeno_xIdSolicitudCobertura_xIdPersonaDesignada";
        $sparam = [
            'pIdSolicitudCobertura' => $datos['IdSolicitudCobertura'],
            'pIdPersonaDesignada' => $datos['IdPersonaDesignada'],
        ];
        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            $this->setError(400, "Error al buscar por solicitud. --");
            return false;
        }
        return true;
    }


    protected function buscarxPuesto(array $datos, &$resultado, ?int &$numfilas): bool {
        $spnombre = 'sel_SolicitudesCoberturaDesempeno_xIdSolicitudCoberturaPuesto';
        $sparam = [
            'pIdSolicitudCoberturaPuesto' => $datos['IdSolicitudCoberturaPuesto'],
        ];
        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            $this->setError(400, 'Error al buscar por solicitud puesto. ');
            return false;
        }
        return true;
    }

    protected function buscarDesignadoxSolicitud(array $datos, &$resultado, ?int &$numfilas): bool {
        $spnombre = 'sel_SolicitudesCoberturaDesempeno_Designado_xIdSolictudCobertura';
        $sparam = [
            'pIdSolicitudCobertura' => $datos['IdSolicitudCobertura'],
        ];
        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            $this->setError(400, 'Error al buscar designado x idsc. ');
            return false;
        }
        return true;
    }

    protected function buscarxNovedad(array $datos, &$resultado, ?int &$numfilas): bool {
        $spnombre = 'sel_SolicitudesCoberturaDesempeno_xIdNovedad';
        $sparam = [
            'pIdNovedad' => $datos['IdNovedad'],
        ];
        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            $this->setError(400, 'Error al buscar x novedad x idscd. ');
            return false;
        }
        return true;
    }

    protected function BusquedaAvanzada(array $datos, &$resultado, ?int &$numfilas): bool {
        $spnombre = "sel_SolicitudesCoberturaDesempeno_busqueda_avanzada";
        $sparam = [
            'pxId' => $datos['xId'],
            'pId' => $datos['Id'],
            'pxIdSolicitudCobertura' => $datos['xIdSolicitudCobertura'],
            'pIdSolicitudCobertura' => $datos['IdSolicitudCobertura'],
            'pxIdPuesto' => $datos['xIdPuesto'],
            'pIdPuesto' => $datos['IdPuesto'],
            'pxIdDesempeno' => $datos['xIdDesempeno'],
            'pIdDesempeno' => $datos['IdDesempeno'],
            'plimit' => $datos['limit'],
            'porderby' => $datos['orderby'],
        ];
        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            $this->setError(400, "Error al realizar la búsqueda avanzada. ");
            return false;
        }
        return true;
    }


    protected function BuscarAuditoriaRapida(array $datos, &$resultado, ?int &$numfilas): bool {
        $spnombre = "sel_SolicitudesCoberturaDesempeno_AuditoriaRapida";
        $sparam = [
            'pId' => $datos['Id'],
        ];
        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            $this->setError(400, "Error al buscar al buscar por codigo. ");
            return false;
        }
        return true;
    }


    protected function Insertar(array $datos, ?int &$codigoInsertado): bool {

        $spnombre = 'ins_SolicitudesCoberturaDesempeno';
        $sparam = [
            'pIdSolicitudCoberturaPuesto' => $datos['IdSolicitudCoberturaPuesto'],
            'pIdSolicitudCobertura' => $datos['IdSolicitudCobertura'],
            'pIdPuesto' => $datos['IdPuesto'],
            'pIdPersonaDesignada' => $datos['IdPersonaDesignada'],
            'pIdNovedad' => $datos['IdNovedad'],
            'pTipoCantidad' => $datos['TipoCantidad'],
            'pCantidadHorasModulos' => $datos['CantidadHorasModulos'],
            'pDia' => $datos['Dia'],
            'pHoraInicio' => $datos['HoraInicio'],
            'pHoraFin' => $datos['HoraFin'],
            'pInstrumentoLegal' => $datos['InstrumentoLegal'],
            'pEstado' => ACTIVO,
            'pAltaUsuario' => $datos['AltaUsuario'],
            'pAltaFecha' => $datos['AltaFecha'],
            'pUltimaModificacionUsuario' => $datos['UltimaModificacionUsuario'],
            'pUltimaModificacionFecha' => $datos['UltimaModificacionFecha'],
        ];

        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            $this->setError(400, "Error interno - Cód: iscd <br> Comuníquese con el área de Sistemas. ");
            return false;
        }
        $codigoInsertado = $this->conexion->UltimoCodigoInsertado();
        return true;
    }


    protected function insertarValores(array $datos, ?int &$codigoInsertado): bool {
        $spnombre = "ins_SolicitudesCoberturaDesempeno_valores";
        $sparam = [
            'pIdSolicitudCoberturaPuesto' => $datos['IdSolicitudCoberturaPuesto'],
            'pCantidadHorasModulos' => $datos['CantidadHorasModulos'],
            'pDia' => $datos['Dia'],
            'pHoraInicio' => $datos['HoraInicio'],
            'pHoraFin' => $datos['HoraFin'],
        ];
        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            $this->setError(400, "Error al insertar. ");
            return false;
        }
        $codigoInsertado = $this->conexion->UltimoCodigoInsertado();
        return true;
    }


    protected function Modificar(array $datos): bool {
        $spnombre = "upd_SolicitudesCoberturaDesempeno_xId";
        $sparam = [
            'pIdSolicitudCoberturaPuesto' => $datos['IdSolicitudCoberturaPuesto'],
            'pTildado' => $datos['Tildado'],
            'pId' => $datos['Id'],
        ];
        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            $this->setError(400, "Error al modificar. ");
            return false;
        }
        return true;
    }


    protected function modificarPersonaxDesempeno(array $datos): bool {

        $spnombre = 'upd_SolicitudCoberturaDesempeno_IdPersona_xId';
        $sparam = [
            'pIdPersonaDesignada' => $datos['IdPersonaDesignada'],
            'pInstrumentoLegal' => $datos['InstrumentoLegal'],
            'pFechaDesignacion' => $datos['FechaDesignacion'],
            'pIdExcepcionTipo' => $datos['IdExcepcionTipo'],
            'pIdEstado' => $datos['IdEstado'],
            'pUltimaModificacionFecha' => $datos['UltimaModificacionFecha'],
            'pUltimaModificacionUsuario' => $datos['UltimaModificacionUsuario'],
            'pId' => $datos['Id'],
        ];
        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            $this->setError(400, 'Error al modificar persona por desempeno.');
            return false;
        }
        return true;
    }

    protected function modificarPersonaxPuesto(array $datos): bool {

        $spnombre = 'upd_SolicitudCoberturaDesempeno_IdPersona_xIdSolicitudCoberturaPuesto';
        $sparam = [
            'pIdPersonaDesignada' => $datos['IdPersonaDesignada'],
            'pInstrumentoLegal' => $datos['InstrumentoLegal'],
            'pFechaDesignacion' => $datos['FechaDesignacion'],
            'pIdExcepcionTipo' => $datos['IdExcepcionTipo'],
            'pIdEstado' => $datos['IdEstado'],
            'pUltimaModificacionFecha' => $datos['UltimaModificacionFecha'],
            'pUltimaModificacionUsuario' => $datos['UltimaModificacionUsuario'],
            'pIdSolicitudCoberturaPuesto' => $datos['IdSolicitudCoberturaPuesto'],
        ];
        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            $this->setError(400, 'Error al modificar persona por puesto.');
            return false;
        }
        return true;
    }

    protected function modificarPersonaxSolicitud(array $datos): bool {

        $spnombre = 'upd_SolicitudCoberturaDesempeno_IdPersona_xIdSolicitudCobertura';
        $sparam = [
            'pIdPersonaDesignada' => $datos['IdPersonaDesignada'],
            'pInstrumentoLegal' => $datos['InstrumentoLegal'],
            'pFechaDesignacion' => $datos['FechaDesignacion'],
            'pIdExcepcionTipo' => $datos['IdExcepcionTipo'],
            'pIdEstado' => $datos['IdEstado'],
            'pUltimaModificacionFecha' => $datos['UltimaModificacionFecha'],
            'pUltimaModificacionUsuario' => $datos['UltimaModificacionUsuario'],
            'pIdSolicitudCobertura' => $datos['IdSolicitudCobertura'],
        ];
        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            $this->setError(400, 'Error al modificar persona por solicitud.');
            return false;
        }
        return true;
    }

    /**
     * ### Inserta los datos del conflicto
     *
     * @param array $datos
     *
     * @return bool
     */
    protected function modificarConflicto(array $datos): bool {
        $spnombre = 'upd_SolicitudCoberturaDesempeno_ExisteInconsistencia_xId';
        $sparam = [
            'pExisteInconsistencia' => $datos['ExisteInconsistencia'],
            'pJsonInconsistencia' => $datos['JsonInconsistencia'],
            'pId' => $datos['Id'],
        ];
        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            $this->setError(400, 'Error al modificar conflictos.');
            return false;
        }

        return true;
    }

    /**
     * ### Inserta los datos del conflicto en todos los desempeños del puesto
     *
     * @param array $datos
     *
     * @return bool
     */
    protected function modificarConflictoxPuesto(array $datos): bool {
        $spnombre = 'upd_SolicitudCoberturaDesempeno_ExisteInconsistencia_xIdSolicitudCoberturaPuesto';
        $sparam = [
            'pExisteInconsistencia' => $datos['ExisteInconsistencia'],
            'pJsonInconsistencia' => $datos['JsonInconsistencia'],
            'pIdSolicitudCoberturaPuesto' => $datos['IdSolicitudCoberturaPuesto'],
        ];
        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            $this->setError(400, 'Error al modificar conflictos por puesto.');
            return false;
        }

        return true;
    }

    /**
     * ### Inserta los datos del conflicto en todos los desempeños de la solicitud
     *
     * @param array $datos
     *
     * @return bool
     */
    protected function modificarConflictoxSolicitud(array $datos): bool {
        $spnombre = 'upd_SolicitudCoberturaDesempeno_ExisteInconsistencia_xIdSolicitudCobertura';
        $sparam = [
            'pExisteInconsistencia' => $datos['ExisteInconsistencia'],
            'pJsonInconsistencia' => $datos['JsonInconsistencia'],
            'pIdSolicitudCobertura' => $datos['IdSolicitudCobertura'],
        ];
        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            $this->setError(400, 'Error al modificar conflcitos por solicitud.');
            return false;
        }

        return true;
    }

    /**
     * @param array $datos
     *
     * @return bool
     */
    public function modificarPuesto(array $datos): bool {

        $spnombre = 'upd_SolicitudesCoberturaDesempeno_IdPuesto_xId';
        $sparam = [
            'pIdPuesto' => $datos['IdPuesto'],
            'pUltimaModificacionUsuario' => $datos['UltimaModificacionUsuario'],
            'pUltimaModificacionFecha' => $datos['UltimaModificacionFecha'],
            'pId' => $datos['Id'],
        ];

        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            $this->setError(400, 'Error al modificar puesto en scd.');
            return false;
        }
        return true;
    }

    /**
     * @param array $datos
     *
     * @return bool
     */
    public function modificarInstrumentoLegalxPersonaxSolicitud(array $datos): bool {

        $spnombre = 'upd_SolicitudesCoberturaDesempeno_InstrumentoLegal_xIdPersonaDesignada_xIdSolicitudCobertura';
        $sparam = [
            'pInstrumentoLegal' => $datos['InstrumentoLegal'],
            'pFechaDesignacion' => $datos['FechaDesignacion'],
            'pUltimaModificacionUsuario' => $datos['UltimaModificacionUsuario'],
            'pUltimaModificacionFecha' => $datos['UltimaModificacionFecha'],
            'pIdSolicitudCobertura' => $datos['IdSolicitudCobertura'],
            'pIdPersonaDesignada' => $datos['IdPersonaDesignada'],
        ];

        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            $this->setError(400, 'Error al modificar puesto en scd.');
            return false;
        }
        return true;
    }

    /**
     * @param array $datos
     *
     * @return bool
     */
    public function modificarNovedadxPersonaxSolicitud(array $datos): bool {

        $spnombre = 'upd_SolicitudesCoberturaDesempeno_xIdSolicitudCobertura_xIdPersonaDesignada';
        $sparam = [
            'pIdNovedad' => $datos['IdNovedad'],
            'pUltimaModificacionUsuario' => $datos['UltimaModificacionUsuario'],
            'pUltimaModificacionFecha' => $datos['UltimaModificacionFecha'],
            'pIdSolicitudCobertura' => $datos['IdSolicitudCobertura'],
            'pIdPersonaDesignada' => $datos['IdPersonaDesignada'],
        ];

        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            $this->setError(400, 'Error al modificar novedad en scd x sc x persona.');
            return false;
        }
        return true;
    }


    /**
     * @param array $datos
     *
     * @return bool
     */
    public function modificarEstadoPersonaxSolicitud(array $datos): bool {

        $spnombre = 'upd_SolicitudesCoberturaDesempeno_IdEstado_xIdSolicitudCobertura_xIdPersonaDesignada';
        $sparam = [
            'pIdEstado' => $datos['IdEstado'],
            'pUltimaModificacionUsuario' => $datos['UltimaModificacionUsuario'],
            'pUltimaModificacionFecha' => $datos['UltimaModificacionFecha'],
            'pIdSolicitudCobertura' => $datos['IdSolicitudCobertura'],
            'pIdPersonaDesignada' => $datos['IdPersonaDesignada'],
        ];

        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            $this->setError(400, 'Error al modificar estado de novedad en scd x sc x persona.');
            return false;
        }
        return true;
    }


    /**
     * @param array $datos
     *
     * @return bool
     */
    public function modificarEstadoPersonaxNovedad(array $datos): bool {

        $spnombre = 'upd_SolicitudesCoberturaDesempeno_IdEstado_xIdNovedad';
        $sparam = [
            'pIdEstado' => $datos['IdEstado'],
            'pUltimaModificacionUsuario' => $datos['UltimaModificacionUsuario'],
            'pUltimaModificacionFecha' => $datos['UltimaModificacionFecha'],
            'pIdNovedad' => $datos['IdNovedad'],
        ];

        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            $this->setError(400, 'Error al modificar estado de novedad en scd x sc x persona.');
            return false;
        }
        return true;
    }


    /**
     * @param array $datos
     *
     * @return bool
     */
    public function modificarEstadoxSolicitudxEstado(array $datos): bool {

        $spnombre = 'upd_SolicitudesCoberturaDesempeno_IdEstado_xIdSolicitudCobertura_xIdEstado';
        $sparam = [
            'pIdEstado' => $datos['IdEstado'],
            'pUltimaModificacionUsuario' => $datos['UltimaModificacionUsuario'],
            'pUltimaModificacionFecha' => $datos['UltimaModificacionFecha'],
            'pIdSolicitudCobertura' => $datos['IdSolicitudCobertura'],
            'pIdEstadoNuevo' => $datos['IdEstadoNuevo'],
        ];

        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            $this->setError(400, 'Error al modificar estado de novedad en scd x sc x estado.');
            return false;
        }
        return true;
    }


    /**
     * @param array $datos
     *
     * @return bool
     */
    public function modificarxNovedad(array $datos): bool {

        $spnombre = 'upd_SolicitudesCoberturaDesempeno_xIdNovedad';
        $sparam = [
            'pIdNovedadNuevo' => $datos['IdNovedadNuevo'],
            'pIdPersonaDesignada' => $datos['IdPersonaDesignada'],
            'pInstrumentoLegal' => $datos['InstrumentoLegal'],
            'pFechaDesignacion' => $datos['FechaDesignacion'],
            'pUltimaModificacionUsuario' => $datos['UltimaModificacionUsuario'],
            'pUltimaModificacionFecha' => $datos['UltimaModificacionFecha'],
            'pIdNovedad' => $datos['IdNovedad'],
        ];

        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            $this->setError(400, 'Error al modificar estado de novedad en scd x sc x estado.');
            return false;
        }
        return true;
    }


    /**
     * @param array $datos
     *
     * @return bool
     */
    public function rectificarPersonaxPuesto(array $datos): bool {

        $spnombre = 'upd_SolicitudesCoberturaDesempeno_Estado_xIdPuesto';
        $sparam = [
            'pIdEstado' => $datos['IdEstado'],
            'pUltimaModificacionUsuario' => $datos['UltimaModificacionUsuario'],
            'pUltimaModificacionFecha' => $datos['UltimaModificacionFecha'],
            'pIdSolicitudCoberturaPuesto' => $datos['IdSolicitudCoberturaPuesto'],
        ];

        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            $this->setError(400, 'Error al rectificar por puesto.');
            return false;
        }
        return true;
    }


    /**
     * @param array $datos
     *
     * @return bool
     */
    public function rectificarPersonaxDesempeno(array $datos): bool {

        $spnombre = 'upd_SolicitudesCoberturaDesempeno_Estado_xIdDesempeno';
        $sparam = [
            'pIdEstado' => $datos['IdEstado'],
            'pUltimaModificacionUsuario' => $datos['UltimaModificacionUsuario'],
            'pUltimaModificacionFecha' => $datos['UltimaModificacionFecha'],
            'pId' => $datos['Id'],
        ];

        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            $this->setError(400, 'Error al rectificar por desempeno.');
            return false;
        }
        return true;
    }

    /**
     * @param array $datos
     *
     * @return bool
     */
    public function rectificarPersonaxSolicitud(array $datos): bool {

        $spnombre = 'upd_SolicitudesCoberturaDesempeno_Estado_xIdSolicitud';
        $sparam = [
            'pIdEstado' => $datos['IdEstado'],
            'pUltimaModificacionUsuario' => $datos['UltimaModificacionUsuario'],
            'pUltimaModificacionFecha' => $datos['UltimaModificacionFecha'],
            'pIdSolicitudCobertura' => $datos['IdSolicitudCobertura'],
        ];

        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            $this->setError(400, 'Error al rectificar por desempeno.');
            return false;
        }
        return true;
    }


    protected function modificarTildado(array $datos): bool {
        $spnombre = 'upd_SolicitudesCoberturaDesempeno_Tildado_xId';
        $sparam = [
            'pTildado' => $datos['Tildado'],
            'pId' => $datos['Id'],
        ];

        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            $this->setError(400, 'Error al modificar sc puesto.');
            return false;
        }
        return true;
    }


    protected function ModificarxIdPuestos(array $datos): bool {

        $spnombre = 'upd_SolicitudesCoberturaDesempeno_xIdPuesto';
        $sparam = [
            'pIdSolicitudCoberturaPuesto' => $datos['IdSolicitudCoberturaPuesto'],
            'pTildado' => $datos['Tildado'],
            'pIdSCPuestos' => $datos['IdSCPuestos'],
        ];

        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            $this->setError(400, "Error al actualizar desempenos. ");
            return false;
        }

        return true;
    }

    protected function ModificarxSolicitudCoberturaPuesto(array $datos): bool {

        $spnombre = 'upd_SolicitudesCoberturaDesempeno_xIdSolicitudCoberturaPuesto';
        $sparam = [
            'pIdSolicitudCoberturaPuestoNuevo' => $datos['IdSolicitudCoberturaPuestoNuevo'],
            'pTildado' => $datos['Tildado'],
            'pIdSolicitudCoberturaPuesto' => $datos['IdSolicitudCoberturaPuesto'],
        ];

        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            $this->setError(400, "Error al actualizar desempenos. ");
            return false;
        }

        return true;
    }

    protected function ModificarTildadoxSolicitudCoberturaPuesto(array $datos): bool {

        $spnombre = 'upd_SolicitudesCoberturaDesempeno_Tildado_xIdSolicitudCoberturaPuesto';
        $sparam = [
            'pTildado' => $datos['Tildado'],
            'pIdSolicitudCoberturaPuesto' => $datos['IdSolicitudCoberturaPuesto'],
        ];

        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            $this->setError(400, 'Error al actualizar desempenos. ');
            return false;
        }

        return true;
    }

    protected function Eliminar(array $datos): bool {
        $spnombre = "del_SolicitudesCoberturaDesempeno_xId";
        $sparam = [
            'pId' => $datos['Id'],
        ];
        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            $this->setError(400, "Error al eliminar por codigo. ");
            return false;
        }
        return true;
    }

/*
    protected function eliminarxSolicitud(array $datos): bool {
        $spnombre = "del_SolicitudesCoberturaDesempeno_xIdSolicitudCobertura";
        $sparam = [
            'pIdSolicitudCobertura' => $datos['IdSolicitudCobertura'],
        ];
        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            $this->setError(400, "Error al eliminar desempeno por solicitud. ");
            return false;
        }
        return true;
    }
    */

    protected function eliminarxSolicitud(array $datos): bool {
        $spnombre = "upd_SolicitudesCoberturaDesempeno_Estado_xIdSolicitudCobertura";
        $sparam = [
            'pIdSolicitudCobertura' => $datos['IdSolicitudCobertura'],
            "pEstado" => $datos["Estado"],
            'pUltimaModificacionUsuario'=> $datos['UltimaModificacionUsuario'],
            'pUltimaModificacionFecha'=> $datos['UltimaModificacionFecha']
        ];
        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            $this->setError(400, "Error al eliminar desempeno por solicitud. ");
            return false;
        }
        return true;
    }
}
