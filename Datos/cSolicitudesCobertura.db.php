<?php

abstract class cSolicitudesCoberturaDB {
    use ManejoErrores;

    /** @var accesoBDLocal */
    protected $conexion;
    /** @var mixed */
    protected $formato;
    /** @var array */
    protected $error;

    /**
     * Constructor de la clase cSolicitudesCoberturaDB.
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
     * Destructor de la clase cSolicitudesCoberturaDB.
     */
    function __destruct() {}


    /**
     * Guarda un mensaje de error
     *
     * @param string|array $error
     * @param string       $error_description
     */
    protected function setError($error, $error_description = ''): void {
        $this->error = is_array($error) ? $error : ['error' => $error, 'error_description' => $error_description];
    }

    protected function EscuelasSP(?string &$spnombre, ?array &$sparam): void {
        $spnombre = 'sel_Escuelas_combo_Nombre';
        $sparam = [];
    }

    public abstract function EscuelasSPResult(&$resultado, ?int &$numfilas): bool;


    protected function DocumentosTiposSP(?string &$spnombre, ?array &$sparam): void {
        $spnombre = 'sel_DocumentosTipos_SC_combo_Nombre';
        $sparam = [];
    }

    public abstract function DocumentosTiposSPResult(&$resultado, ?int &$numfilas): bool;


    protected function EscuelasPuestosSP(?string &$spnombre, ?array &$sparam): void {
        $spnombre = 'sel_EscuelasPuestos_combo_CodigoPuesto';
        $sparam = [];
    }

    public abstract function EscuelasPuestosSPResult(&$resultado, ?int &$numfilas): bool;


    protected function SP(?string &$spnombre, ?array &$sparam): void {
        $spnombre = 'sel__combo_';
        $sparam = [];
    }

    public abstract function SPResult(&$resultado, ?int &$numfilas): bool;


    protected function BuscarxCodigo(array $datos, &$resultado, ?int &$numfilas): bool {
        $spnombre = "sel_SolicitudesCobertura_xId";
        $sparam = [
            'pId' => $datos['Id'],
        ];
        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            $this->setError(400, "Error al buscar al buscar por codigo. ");
            return false;
        }
        return true;
    }


    protected function BuscarxCodigoLog(array $datos, &$resultado, ?int &$numfilas): bool {
        $spnombre = "sel_SolicitudesCoberturaLog_xId";
        $sparam = [
            'pBase' => BASEDATOS_PERSONAS,
            'pId' => $datos['Id'],
        ];
        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            $this->setError(400, "Error al buscar al buscar por codigo log. ");
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
    protected function buscarParElastic(array $datos, &$resultado, ?int &$numfilas): bool {
        $spnombre = "sel_SolicitudesCobertura_es_xId";
        $sparam = [
            'pBasePersonas' => BASEDATOS_PERSONAS,
            'pBaseLicencias' => BASEDATOSLICENCIAS,
            'pId' => $datos['Id'],
        ];
        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            $this->setError(400, "Error al buscar al buscar por codigo.s ");
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
    protected function buscarxPuestoLicencia(array $datos, &$resultado, ?int &$numfilas): bool {
        $spnombre = "sel_SolicitudesCobertura_xIdPuesto_IdLicencia";
        $sparam = [
            'pIdPuesto' => $datos['IdPuesto'],
            'pIdLicencia' => $datos['IdLicencia'],
        ];
        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            $this->setError(400, "Error al buscar al buscar por licencia. ");
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
    protected function buscarxPuestoNovedad(array $datos, &$resultado, ?int &$numfilas): bool {
        $spnombre = "sel_SolicitudesCobertura_xIdPuesto_IdNovedad";
        $sparam = [
            'pIdPuesto' => $datos['IdPuesto'],
            'pIdNovedad' => $datos['IdNovedad'],
        ];
        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            $this->setError(400, "Error al buscar al buscar por licencia. ");
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
    protected function buscarxNovedad(array $datos, &$resultado, ?int &$numfilas): bool {
        $spnombre = "sel_SolicitudesCobertura_xIdNovedad";
        $sparam = [
            'pIdNovedad' => $datos['IdNovedad'],
        ];
        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            $this->setError(400, "Error al buscar al buscar sc por novedad. ");
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
    protected function buscarAnexos(array $datos, &$resultado, ?int &$numfilas): bool {

        $spnombre = "sel_SolicitudesCobertura_DatosAnexos";
        $sparam = [
            'pId' => $datos['Id'],
        ];

        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            $this->setError(400, "Error al buscar al buscar anexos. ");
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
    protected function buscarxLicencia(array $datos, &$resultado, ?int &$numfilas): bool {

        $spnombre = "sel_SolicitudesCobertura_xIdLicencia";
        $sparam = [
            'pxIdEstado' => $datos['xIdEstado'],
            'pIdEstado' => $datos['IdEstado'],
            'pIdLicencia' => $datos['IdLicencia'],
            'pIdPuesto' => $datos['IdPuesto'],
            'pxEstado' => $datos["xEstado"],
            'pEstado' => $datos["Estado"]
        ];

        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            $this->setError(400, "Error al buscar al buscar sc x licencia. ");
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
    protected function BusquedaAvanzada(array $datos, &$resultado, ?int &$numfilas): bool {
        $spnombre = "sel_SolicitudesCobertura_busqueda_avanzada";
        $sparam = [
            'pBasePersonas' => BASEDATOS_PERSONAS,
            'pxId' => $datos['xId'],
            'pId' => $datos['Id'],
            'pxIdEscuela' => $datos['xIdEscuela'],
            'pIdEscuela' => $datos['IdEscuela'],
            'pxIdTipoDocumento' => $datos['xIdTipoDocumento'],
            'pIdTipoDocumento' => $datos['IdTipoDocumento'],
            'pxDni' => $datos['xDni'],
            'pDni' => $datos['Dni'],
            'pxIdLicencia' => $datos['xIdLicencia'],
            'pIdLicencia' => $datos['IdLicencia'],
            'pxIdPersonaSaliente' => $datos['xIdPersonaSaliente'],
            'pIdPersonaSaliente' => $datos['IdPersonaSaliente'],
            'pxFechaDesde' => $datos['xFechaDesde'],
            'pFechaDesde' => $datos['FechaDesde'],
            'pxIdArea' => $datos['xIdArea'],
            'pIdArea' => $datos['IdArea'],
            'pxIdEstado' => $datos['xIdEstado'],
            'pIdEstado' => $datos['IdEstado'],
            'pxIdCargo' => $datos['xIdCargo'],
            'pIdCargo' => $datos['IdCargo'],
            'pxIdMateria' => $datos['xIdMateria'],
            'pIdMateria' => $datos['IdMateria'],
            'pxIdRegion' => $datos['xIdRegion'],
            'pIdRegion' => $datos['IdRegion'],
            'pxIdAreaIgnorar' => $datos['xIdAreaIgnorar'],
            'pIdAreaIgnorar' => $datos['IdAreaIgnorar'],
            'pxIdEstadoIgnorar' => $datos['xIdEstadoIgnorar'],
            'pIdEstadoIgnorar' => $datos['IdEstadoIgnorar'],
            'pxFiltros' => $datos['xFiltros'],
            'pFiltros' => $datos['Filtros'],
            'pxIdsNivel' => $datos['xIdsNivel'],
            'pIdsNivel' => $datos['IdsNivel'],
            'pxIdPersonaEntrante' => $datos['xIdPersonaEntrante'],
            'pIdPersonaEntrante' => $datos['IdPersonaEntrante'],
            "pxEstado" => $datos["xEstado"],
            "pEstado" => $datos["Estado"],
            'plimit' => $datos['limit'],
            'porderby' => $datos['orderby'],
        ];
        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            $this->setError(400, "Error al realizar la búsqueda avanzada. ");
            return false;
        }
        return true;
    }

    protected function BusquedaListado(array $datos, &$resultado, ?int &$numfilas): bool {
        $spnombre = 'sel_SolicitudesCobertura_busqueda_listado';
        $sparam = [
            'pxId' => $datos['xId'],
            'pId' => $datos['Id'],
            'pxIdEscuela' => $datos['xIdEscuela'],
            'pIdEscuela' => $datos['IdEscuela'],
            'pxIdTipoDocumento' => $datos['xIdTipoDocumento'],
            'pIdTipoDocumento' => $datos['IdTipoDocumento'],
            'pxDni' => $datos['xDni'],
            'pDni' => $datos['Dni'],
            'pxIdLicencia' => $datos['xIdLicencia'],
            'pIdLicencia' => $datos['IdLicencia'],
            'pxIdPersonaSaliente' => $datos['xIdPersonaSaliente'],
            'pIdPersonaSaliente' => $datos['IdPersonaSaliente'],
            'pxFechaDesde' => $datos['xFechaDesde'],
            'pFechaDesde' => $datos['FechaDesde'],
            'pxIdArea' => $datos['xIdArea'],
            'pIdArea' => $datos['IdArea'],
            'pxIdEstado' => $datos['xIdEstado'],
            'pIdEstado' => $datos['IdEstado'],
            'pxIdCargo' => $datos['xIdCargo'],
            'pIdCargo' => $datos['IdCargo'],
            'pxIdMateria' => $datos['xIdMateria'],
            'pIdMateria' => $datos['IdMateria'],
            'pxIdRegion' => $datos['xIdRegion'],
            'pIdRegion' => $datos['IdRegion'],
            'pxIdAreaIgnorar' => $datos['xIdAreaIgnorar'],
            'pIdAreaIgnorar' => $datos['IdAreaIgnorar'],
            'pxIdEstadoIgnorar' => $datos['xIdEstadoIgnorar'],
            'pIdEstadoIgnorar' => $datos['IdEstadoIgnorar'],
            'pxFiltros' => $datos['xFiltros'],
            'pFiltros' => $datos['Filtros'],
            'pxIdsNivel' => $datos['xIdsNivel'],
            'pIdsNivel' => $datos['IdsNivel'],
            'pxIdTurno' => $datos["xIdTurno"],
            "pIdTurno" => $datos["IdTurno"],
            "pxIdsFiltradosPuestos" => $datos["xIdsFiltradosPuestos"],
            "pIdsFiltradosPuestos" => $datos["IdsFiltradosPuestos"],
            'plimit' => $datos['limit'],
            'porderby' => $datos['orderby'],
        ];
        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            $this->setError(400, 'Error al realizar la búsqueda avanzada. ');
            return false;
        }
        return true;
    }

    protected function BusquedaListadoCantidad(array $datos, &$resultado, ?int &$numfilas): bool {
        $spnombre = 'sel_SolicitudesCobertura_busqueda_listado_cantidad';
        $sparam = [
            'pxId' => $datos['xId'],
            'pId' => $datos['Id'],
            'pxIdEscuela' => $datos['xIdEscuela'],
            'pIdEscuela' => $datos['IdEscuela'],
            'pxIdTipoDocumento' => $datos['xIdTipoDocumento'],
            'pIdTipoDocumento' => $datos['IdTipoDocumento'],
            'pxDni' => $datos['xDni'],
            'pDni' => $datos['Dni'],
            'pxIdLicencia' => $datos['xIdLicencia'],
            'pIdLicencia' => $datos['IdLicencia'],
            'pxIdPersonaSaliente' => $datos['xIdPersonaSaliente'],
            'pIdPersonaSaliente' => $datos['IdPersonaSaliente'],
            'pxFechaDesde' => $datos['xFechaDesde'],
            'pFechaDesde' => $datos['FechaDesde'],
            'pxIdArea' => $datos['xIdArea'],
            'pIdArea' => $datos['IdArea'],
            'pxIdEstado' => $datos['xIdEstado'],
            'pIdEstado' => $datos['IdEstado'],
            'pxIdCargo' => $datos['xIdCargo'],
            'pIdCargo' => $datos['IdCargo'],
            'pxIdMateria' => $datos['xIdMateria'],
            'pIdMateria' => $datos['IdMateria'],
            'pxIdRegion' => $datos['xIdRegion'],
            'pIdRegion' => $datos['IdRegion'],
            'pxIdAreaIgnorar' => $datos['xIdAreaIgnorar'],
            'pIdAreaIgnorar' => $datos['IdAreaIgnorar'],
            'pxIdEstadoIgnorar' => $datos['xIdEstadoIgnorar'],
            'pIdEstadoIgnorar' => $datos['IdEstadoIgnorar'],
            'pxFiltros' => $datos['xFiltros'],
            'pFiltros' => $datos['Filtros'],
            'pxIdsNivel' => $datos['xIdsNivel'],
            'pIdsNivel' => $datos['IdsNivel'],
            'pxIdTurno' => $datos["xIdTurno"],
            "pIdTurno" => $datos["IdTurno"],
            "pxIdsFiltradosPuestos" => $datos["xIdsFiltradosPuestos"],
            "pIdsFiltradosPuestos" => $datos["IdsFiltradosPuestos"],
            'plimit' => $datos['limit'],
            'porderby' => $datos['orderby'],
        ];

        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            $this->setError(400, 'Error al realizar la búsqueda avanzada. ');
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


    protected function busquedaAvanzadaTipo(&$resultado, ?int &$numfilas): bool {

        $spnombre = "sel_SolicitudesCoberturaTipo_constanteTipoDocumento";
        $sparam = [

        ];

        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            $this->setError(400, "Error al buscar tipo de documento");
            return false;
        }
        return true;
    }

    protected function BuscarAuditoriaRapida(array $datos, &$resultado, ?int &$numfilas): bool {
        $spnombre = "sel_SolicitudesCobertura_AuditoriaRapida";
        $sparam = [
            'pId' => $datos['Id'],
        ];
        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            $this->setError(400, "Error al buscar al buscar por codigo. ");
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
    protected function buscarRepeticiones(array $datos, &$resultado, ?int &$numfilas): bool {
        $spnombre = "sel_SolicitudesCobertura_repetidas";
        $sparam = [
            'pIdTipo' => $datos['IdTipo'],
            'pIdPersonaSaliente' => $datos['IdPersonaSaliente'],
            'pFechaDesde' => $datos['FechaDesde'],
            'pIdPuesto' => $datos['IdPuesto'],
        ];
        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            $this->setError(400, "Error al buscar al buscar repeticiones.");
            return false;
        }
        return true;
    }


    protected function Insertar(array $datos, ?int &$codigoInsertado): bool {
        $spnombre = 'ins_SolicitudesCobertura';
        $sparam = [
            'pIdEscuela' => $datos['IdEscuela'],
            'pIdTipoDocumento' => $datos['IdTipoDocumento'],
            'pIdRegistroTipoDocumento' => $datos['IdRegistroTipoDocumento'],
            'pIdLicencia' => $datos['IdLicencia'],
            'pIdPersonaSaliente' => $datos['IdPersonaSaliente'],
            'pFechaDesde' => $datos['FechaDesde'],
            'pFechaHasta' => $datos['FechaHasta'],
            'pObservaciones' => $datos['Observaciones'],
            'pEsAuxiliar' => $datos['EsAuxiliar'],
            'pIdNivel' => $datos['IdNivel'],
            'pIdArea' => $datos['IdArea'],
            'pIdEstado' => $datos['IdEstado'],
            'pIdAreaInicial' => $datos['IdAreaInicial'],
            'pIdEstadoInicial' => $datos['IdEstadoInicial'],
            'pMovimientoFecha' => $datos['MovimientoFecha'],
            'pFechaEnvio' => $datos['FechaEnvio'],
            'pAltaUsuario' => $datos['AltaUsuario'],
            'pAltaFecha' => $datos['AltaFecha'],
            'pUltimaModificacionFecha' => $datos['UltimaModificacionFecha'],
            'pUltimaModificacionUsuario' => $datos['UltimaModificacionUsuario'],
            'pHashDato' => $datos['HashDato'],
        ];
        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            $this->setError(400, 'Error al insertar solicitud');
            return false;
        }
        $codigoInsertado = $this->conexion->UltimoCodigoInsertado();
        return true;
    }


    protected function Modificar(array $datos): bool {
        $spnombre = "upd_SolicitudesCobertura_xId";
        $sparam = [
            'pIdEscuela' => $datos['IdEscuela'],
            'pIdTipoDocumento' => $datos['IdTipoDocumento'],
            'pIdRegistroTipoDocumento' => $datos['IdRegistroTipoDocumento'],
            'pIdLicencia' => $datos['IdLicencia'],
            'pIdPersonaSaliente' => $datos['IdPersonaSaliente'],
            'pFechaDesde' => $datos['FechaDesde'],
            'pFechaHasta' => $datos['FechaHasta'],
            'pObservaciones' => $datos['Observaciones'],
            'pMovimientoFecha' => $datos['MovimientoFecha'],
            'pFechaEnvio' => $datos['FechaEnvio'],
            'pUltimaModificacionFecha' => date("Y/m/d H:i:s"),
            'pUltimaModificacionUsuario' => $_SESSION['usuariocod'],
            'pHashDato' => $datos['HashDato'],
            'pId' => $datos['Id'],
        ];

        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            $this->setError(400, "Error al modificar. ");
            return false;
        }
        return true;
    }

    /**
     * @param array $datos
     *
     * @return bool
     */
    public function modificarDesglosePorSolicitud(array $datos): bool {

        $spnombre = 'upd_SolicitudesCobertura_Desglosado_xId';
        $sparam = [
            'pDesglosado' => $datos['Desglosado'],
            'pDesignadoEnTodos' => $datos['DesignadoEnTodos'],
            'pUltimaModificacionUsuario' => $datos['UltimaModificacionUsuario'],
            'pUltimaModificacionFecha' => $datos['UltimaModificacionFecha'],
            'pId' => $datos['Id'],
        ];

        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            $this->setError(400, 'Error al modificar desglose por sc.');
            return false;
        }
        return true;
    }


    /**
     * @param array $datos
     *
     * @return bool
     */
    public function modificarDesignadoEnTodos(array $datos): bool {

        $spnombre = 'upd_SolicitudCobertura_DesignadoEnTodos_xId';
        $sparam = [
            'pDesignadoEnTodos' => $datos['DesignadoEnTodos'],
            'pUltimaModificacionUsuario' => $datos['UltimaModificacionUsuario'],
            'pUltimaModificacionFecha' => $datos['UltimaModificacionFecha'],
            'pId' => $datos['Id'],
        ];

        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            $this->setError(400, 'Error al modificar designado en todos por sc.');
            return false;
        }
        return true;
    }

    /*
    protected function Eliminar(array $datos): bool {
        $spnombre = "del_SolicitudesCobertura_xId";
        $sparam = [
            'pId' => $datos['Id'],
        ];
        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            $this->setError(400, "Error al eliminar sc por codigo. ");
            return false;
        }
        return true;
    } */
    protected function Eliminar(array $datos): bool {
        $spnombre = "upd_SolicitudesCobertura_Estado_xId";
        $sparam = [
            'pId' => $datos['Id'],
            "pEstado" => $datos["Estado"],
            'pUltimaModificacionUsuario'=> $datos['UltimaModificacionUsuario'],
            'pUltimaModificacionFecha'=> $datos['UltimaModificacionFecha']
        ];
        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            $this->setError(400, "Error al eliminar sc por codigo. ");
            return false;
        }
        return true;
    }


    protected function ModificarEstado(array $datos): bool {
        $spnombre = "upd_SolicitudesCobertura_IdEstado_xId";
        $sparam = [
            'pIdEstado' => $datos['IdEstado'],
            'pId' => $datos['Id'],
        ];
        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            $this->setError(400, "Error al modificar el estado. ");
            return false;
        }
        return true;
    }


    protected function ModificarAreaEstado(array $datos): bool {
        $spnombre = "upd_SolicitudesCobertura_IdArea_IdEstado_xId";
        $sparam = [
            'pIdArea' => $datos['IdArea'],
            'pIdEstado' => $datos['IdEstado'],
            'pMovimientoFecha' => $datos['MovimientoFecha'],
            'pUltimaModificacionUsuario' => $datos['UltimaModificacionUsuario'],
            'pUltimaModificacionFecha' => $datos['UltimaModificacionFecha'],
            'pId' => $datos['Id'],
        ];

        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            $this->setError(400, "Error al modificar el area y estado. ");
            return false;
        }
        return true;
    }


    protected function ModificarFechas(array $datos): bool {
        $spnombre = 'upd_SolicitudesCobertura_Fechas_xId';
        $sparam = [
            'pFechaDesde' => $datos['FechaDesde'],
            'pFechaHasta' => $datos['FechaHasta'],
            'pUltimaModificacionFecha' => $datos['UltimaModificacionFecha'],
            'pUltimaModificacionUsuario' => $datos['UltimaModificacionUsuario'],
            'pId' => $datos['Id'],
        ];

        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            $this->setError(400, "Error al modificar fechas de la solicitud. ");
            return false;
        }
        return true;
    }

    protected function ModificarObservacion(array $datos): bool {
        $spnombre = 'upd_SolicitudesCobertura_Observaciones_xId';
        $sparam = [
            'pObservaciones' => $datos['Observaciones'],
            'pUltimaModificacionFecha' => $datos['UltimaModificacionFecha'],
            'pUltimaModificacionUsuario' => $datos['UltimaModificacionUsuario'],
            'pId' => $datos['Id'],
        ];

        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            $this->setError(400, "Error al modificar comentario de la solicitud. ");
            return false;
        }
        return true;
    }

    protected function actualizarFechaEnvio(array $datos): bool {
        $spnombre = "upd_SolicitudesCobertura_FechaEnvio_xId";
        $sparam = [
            'pFechaEnvio' => $datos['FechaEnvio'],
            'pId' => $datos['Id'],
        ];
        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            $this->setError(400, "Error al modificar el estado. ");
            return false;
        }
        return true;
    }


    protected function actualizarPersonaDesignada(array $datos): bool {
        $spnombre = "upd_SolicitudesCobertura_IdPersonaDesignada_xId";
        $sparam = [
            'pIdPersonaDesignada' => $datos['IdPersonaDesignada'],
            'pId' => $datos['Id'],
        ];
        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            $this->setError(400, "Error al modificar la persona designada. ");
            return false;
        }
        return true;
    }


    protected function actualizarNovedadRelacionada(array $datos): bool {
        $spnombre = "upd_SolicitudesCobertura_IdNovedad_xId";
        $sparam = [
            'pIdNovedad' => $datos['IdNovedad'],
            'pId' => $datos['Id'],
        ];
        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            $this->setError(400, "Error al modificar la novedad. ");
            return false;
        }
        return true;
    }

    protected function CantidadSolicitudes(array $datos, &$resultado): bool {
        $spnombre = "sel_SolicitudesCobertura_cantidad_xIdEstado";
        $sparam = [

            'pBasePersonas' => BASEDATOS_PERSONAS,
            'pxId' => $datos['xId'],
            'pId' => $datos['Id'],
            'pxIdEscuela' => $datos['xIdEscuela'],
            'pIdEscuela' => $datos['IdEscuela'],
            'pxIdTipoDocumento' => $datos['xIdTipoDocumento'],
            'pIdTipoDocumento' => $datos['IdTipoDocumento'],
            'pxDni' => $datos['xDni'],
            'pDni' => $datos['Dni'],
            'pxIdLicencia' => $datos['xIdLicencia'],
            'pIdLicencia' => $datos['IdLicencia'],
            'pxIdPersonaSaliente' => $datos['xIdPersonaSaliente'],
            'pIdPersonaSaliente' => $datos['IdPersonaSaliente'],
            'pxFechaDesde' => $datos['xFechaDesde'],
            'pFechaDesde' => $datos['FechaDesde'],
            'pxIdArea' => $datos['xIdArea'],
            'pIdArea' => $datos['IdArea'],
            'pxIdEstado' => $datos['xIdEstado'],
            'pIdEstado' => $datos['IdEstado'],
            'pxIdAreaIgnorar' => $datos['xIdAreaIgnorar'],
            'pIdAreaIgnorar' => $datos['IdAreaIgnorar'],
            'pxIdEstadoIgnorar' => $datos['xIdEstadoIgnorar'],
            'pIdEstadoIgnorar' => $datos['IdEstadoIgnorar'],
            'pxFiltros' => $datos['xFiltros'],
            'pFiltros' => $datos['Filtros'],
            'pxIdsNivel' => $datos['xIdsNivel'],
            'pIdsNivel' => $datos['IdsNivel'],

        ];

        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {

            $this->setError(400, "Error al modificar el area y estado. ");
            return false;
        }
        return true;
    }


    protected function BuscarxIdSolicutudEnDocumento(array $datos, &$resultado, ?int &$numfilas): bool {
        $spnombre = "sel_documentosSolicitudCobertura_xIdSolicitud";
        $sparam = [
            'pId' => $datos['Id'],
        ];
        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            $this->setError(400, "Error al buscar al buscar el documento.");
            return false;
        }
        return true;
    }


    protected function BuscarxCodigoEnDocumento(array $datos, &$resultado, ?int &$numfilas): bool {
        $spnombre = "sel_documentosSolicitudCobertura_xId";
        $sparam = [
            'pId' => $datos['Id'],
        ];
        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            $this->setError(400, "Error al buscar al buscar el documento.");
            return false;
        }
        return true;
    }

    protected function GuardarDocumento($datos)
    {
        $spnombre = "ins_documentosSolicitudCobertura";

        $sparam = array(
            'ptable'             => BASEDATOS,
            'pIdSolicitudCobertura'  => $datos['IdSolicitudCobertura'],
            'pIdDocumentoAdjunto'     => $datos['IdDocumentoAdjunto'],
            'pIdRegistroTipoDocumento'       => $datos['IdRegistroTipoDocumento'],
            'pArchivoUbicacion'  => $datos['ArchivoUbicacion'],
            'pArchivoNombre'     => $datos['ArchivoNombre'],
            'pArchivoSize'       => $datos['ArchivoSize'],
            'pArchivoHash'       => $datos['ArchivoHash'],
            'pAltaUsuario'       => $_SESSION['usuariocod'],
            'pAltaFecha'         => date("Y/m/d H:i:s")
        );

        if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno) )
        {
            FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al guardar el documento.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
            return false;
        }

        return true;
    }


    protected function EliminarDocumento($datos) 
    {
		$spnombre = "del_documentosSolicitudCobertura";
		
        $sparam = array(
            'ptable'             => BASEDATOS,
			'pIdDocumentoArchivo' => $datos['IdDocumentoArchivo']
		);

		if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
			FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, "Error al eliminar el documento.", array("archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__), array("formato" => $this->formato));
			return false;
		}

		return true;
	}

}
