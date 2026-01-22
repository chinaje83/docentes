<?php

abstract class cEscuelasPuestosDB {
    use ManejoErrores;

    /** @var accesoBDLocal */
    protected $conexion;
    /** @var mixed */
    protected $formato;
    /** @var array */
    protected $error;

    /**
     * Constructor de la clase cEscuelasPuestosDB.
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
     * Destructor de la clase cEscuelasPuestosDB.
     */
    function __destruct() {}

    /**
     * Devuelve el mensaje de error almacenado
     *
     * @return array
     */
    public abstract function getError(): array;

    public function duplicarPuesto(array $datos, ?int &$idPuestoHijo): bool {
        $spnombre = 'ins_EscuelasPuestos_duplicado_xIdPuesto';
        $sparam = [
            'pFechaDesde' => $datos['FechaDesde'],
            'pFechaHasta' => $datos['FechaHasta'],
            'pAltaFecha' => $datos['AltaFecha'],
            'pAltaUsuario' => $datos['AltaUsuario'],
            'pUltimaModificacionFecha' => $datos['UltimaModificacionFecha'],
            'pUltimaModificacionUsuario' => $datos['UltimaModificacionUsuario'],
            'pIdPuesto' => $datos['IdPuesto'],
        ];
        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            $this->setError(400, 'Error al duplicar el puesto por codigo. ');
            return false;
        }

        $idPuestoHijo = $this->conexion->UltimoCodigoInsertado();
        return true;
    }


    protected function buscarParaElastic(array $datos, &$resultado, ?int &$numfilas): bool {
        $spnombre = "sel_EscuelasPuestos_xParaElastic";
        $sparam = [
            'pIdPuesto' => $datos['IdPuesto'],
        ];
        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            $this->setError(400, "Error al buscar el puesto por codigo. ");
            return false;
        }
        return true;
    }

    protected function buscarParaElasticxEscuela(array $datos, &$resultado, ?int &$numfilas): bool {
        if (!$this->conexion->buscarStoredProcedure("sel_EscuelasPuestos_xParaElastic", $sql))
            return false;

        $sql = preg_replace('/(.*?WHERE).*/', "$1 a.IdEscuela IN ({$datos['IdEscuela']})", $sql);

        if (!$this->conexion->ejecutarSQL($sql, 'SEL', $resultado, $numfilas, $errno)) {
            $this->setError(400, "Error al buscar el puesto por codigo. ");
            return false;
        }
        return true;
    }

    protected function buscarArbolDePuestos(array $datos, &$resultado, ?int &$numfilas): bool {

        $spnombre = "sel_EscuelasPuestos_blk_xIdPuesto";
        $sparam = [
            'pIdPuesto' => $datos['IdPuesto'],
        ];

        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            $this->setError(400, "Error al buscar puestos.");
            return false;
        }

        return true;
    }

    protected function BuscarxCodigo(array $datos, &$resultado, ?int &$numfilas): bool {
        $spnombre = "sel_EscuelasPuestos_xIdPuesto";
        $sparam = [
            'pIdPuesto' => $datos['IdPuesto'],
        ];
        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            $this->setError(400, "Error al buscar por codigo. ");
            return false;
        }
        return true;
    }

    protected function BuscarxCodigoVista(array $datos, &$resultado, ?int &$numfilas): bool {
        $spnombre = "sel_EscuelasPuestos_xIdPuesto_vista";
        $sparam = [
            'pIdPuesto' => $datos['IdPuesto'],
        ];
        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            $this->setError(400, "Error al buscar por codigo. ");
            return false;
        }
        return true;
    }

    protected function BuscarCargoxIdPuesto(array $datos, &$resultado, ?int &$numfilas): bool {
        $spnombre = "sel_EscuelasPuestos_Cargo_xIdPuesto";
        $sparam = [
            'pIdPuesto' => $datos['IdPuesto'],
        ];
        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            $this->setError(400, "Error al buscar por codigo. ");
            return false;
        }
        return true;
    }


    protected function BusquedaxIdPuestoRaiz(array $datos, &$resultado, ?int &$numfilas): bool {
        $spnombre = "sel_EscuelasPuestos_xIdPuestoRaiz";
        $sparam = [
            'pIdPuestoRaiz' => $datos['IdPuestoRaiz'],
        ];
        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            $this->setError(400, "Error al buscar por codigo. ");
            return false;
        }
        return true;
    }

    protected function buscarExistentexEscuela(array $datos, &$resultado, ?int &$numfilas): bool {

        $spnombre = 'sel_EscuelasPuestos_cantidad_existente';
        $sparam = [
            'pIdEscuela' => $datos['IdEscuela'],
        ];

        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            $this->setError(400, "Error al buscar por codigo. ");
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
    protected function BuscarxCodigoMigracion(array $datos, &$resultado, ?int &$numfilas): bool {
        $spnombre = "sel_EscuelasPuestos_xIdPuestoMigracion";
        $sparam = [
            'pIdPuestoMigracion' => $datos['IdPuestoMigracion'],
        ];
        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            $this->setError(400, "Error al buscar por codigo migracion. ");
            return false;
        }
        return true;
    }


    protected function BuscarxIdSeccion(array $datos, &$resultado, ?int &$numfilas): bool {
        $spnombre = "sel_EscuelasPuestos_xIdSeccion";
        $sparam = [
            'pIdSeccion' => $datos['IdSeccion'],
            'pxPuestoPadre' => $datos['xPuestoPadre'],
        ];
        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            $this->setError(400, "Error al buscar al buscar por codigo. ");
            return false;
        }
        return true;
    }


    protected function BuscarxPuestoOrigen(array $datos, &$resultado, ?int &$numfilas): bool {
        $spnombre = 'sel_EscuelasPuestos_xIdPuestoOrigen';
        $sparam = [
            'pIdPuesto' => $datos['IdPuesto'],
        ];
        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            $this->setError(400, 'Error al buscar por codigo. ');
            return false;
        }
        return true;
    }


    protected function BusquedaAvanzadaxHijo(array $datos, &$resultado, ?int &$numfilas): bool {

        $spnombre = "sel_Puestos_Hijos_xIdPuesto";
        $sparam = [
            'pBasePersonas' => BASEDATOS_PERSONAS,
            'pIdPuesto' => $datos['IdPuesto'],

        ];
        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            $this->setError(400, "Error al buscar al buscar por codigo. ");
            return false;
        }
        return true;
    }

    protected function BuscarxIdSeccionxIdPuesto(array $datos, &$resultado, ?int &$numfilas): bool {
        $spnombre = "sel_EscuelasPuestos_xIdSeccion_IdPuesto";
        $sparam = [
            'pIdSeccion' => $datos['IdSeccion'],
            'pIdPuesto' => $datos['IdPuesto'],
        ];
        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            $this->setError(400, "Error al buscar al buscar por codigo. ");
            return false;
        }
        return true;
    }


    protected function BuscarPuestosVacios(array $datos, &$resultado, ?int &$numfilas): bool {
        $spnombre = "sel_EscuelasPuestos_xIdEscuela";
        $sparam = [
            'pIdEscuela' => $datos['IdEscuela'],
        ];
        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            $this->setError(400, "Error al buscar el maximo orden. ");
            return false;
        }
        return true;
    }

    protected function BuscarPuestosxEscuela(array $datos, &$resultado, ?int &$numfilas): bool {
        $spnombre = 'sel_EscuelasPuestos_xEscuela';
        $sparam = [
            'pIdEscuela' => $datos['IdEscuela'],
        ];

        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            $this->setError(400, "Error al buscar puestos por escuela. ");
            return false;
        }
        return true;
    }

    protected function BuscarGradoDivision(array $datos, &$resultado, ?int &$numfilas): bool {
        $spnombre = "sel_EscuelasPuestos_Grados_Seccion";
        $sparam = [
            'pIdPuesto' => $datos['IdPuesto'],
        ];
        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            $this->setError(400, "Error al buscar al buscar por codigo. ");
            return false;
        }
        return true;
    }

    protected function BuscarxIdPuestoPadre(array $datos, &$resultado, ?int &$numfilas): bool {
        $spnombre = "sel_Puestos_xIdPuestoPadre";
        $sparam = [
            'pIdPuestoPadre' => $datos['IdPuestoPadre'],
        ];
        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            $this->setError(400, "Error al buscar al buscar por codigo.");
            return false;
        }
        return true;
    }


    protected function BusquedaSuplentesActivos(array $datos, &$resultado, ?int &$numfilas): bool {
        $spnombre = "sel_Puestos_xIdPuestoPadre_xEstado";
        $sparam = [
            'pIdPuestoPadre' => $datos['IdPuestoPadre'],
            'pEstado' => ACTIVO,
        ];
        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            $this->setError(400, "Error al buscar al buscar por codigo.");
            return false;
        }
        return true;
    }


    protected function BusquedaAvanzadaxSeccion(array $datos, &$resultado, ?int &$numfilas): bool {
        $spnombre = "sel_EscuelasPuestos_IdSeccion";
        $sparam = [
            'pxIdEscuelaTurno' => $datos['xIdEscuelaTurno'],
            'pIdEscuelaTurno' => $datos['IdEscuelaTurno'],
            'pxIdCiclo' => $datos['xIdCiclo'],
            'pIdCiclo' => $datos['IdCiclo'],
            'pxIdOrientacion' => $datos['xIdOrientacion'],
            'pIdOrientacion' => $datos['IdOrientacion'],
            'pxIdGradoAnio' => $datos['xIdGradoAnio'],
            'pIdGradoAnio' => $datos['IdGradoAnio'],
            'pxIdSeccion' => $datos['xIdSeccionBusqueda'],
            'pIdSeccion' => $datos['IdSeccionBusqueda'],
            'pxEstado' => $datos['xEstado'],
            'pEstado' => $datos['Estado'],
            'plimit' => $datos['limit'],
            'porderby' => $datos['orderby'],
        ];

        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            $this->setError(400, "Error al buscar al buscar por codigo. ");
            return false;
        }
        return true;
    }

    protected function BusquedaAvanzada(array $datos, &$resultado, ?int &$numfilas): bool {
        $spnombre = "sel_EscuelasPuestos_busqueda_avanzada";
        $sparam = [
            'pBasePersonas' => BASEDATOS_PERSONAS,
            'pxIdEscuela' => $datos['xIdEscuela'],
            'pIdEscuela' => $datos['IdEscuela'],
            'pxIdPuesto' => $datos['xIdPuesto'],
            'pIdPuesto' => $datos['IdPuesto'],
            'pxIdPuestoRaiz' => $datos['xIdPuestoRaiz'],
            'pIdPuestoRaiz' => $datos['IdPuestoRaiz'],
            'pxIdEscuelaTurno' => $datos['xIdEscuelaTurno'],
            'pIdEscuelaTurno' => $datos['IdEscuelaTurno'],
            'pxIdSeccion' => $datos['xIdSeccion'],
            'pIdSeccion' => $datos['IdSeccion'],
            'pxIdCargo' => $datos['xIdCargo'],
            'pIdCargo' => $datos['IdCargo'],
            'pxIdGrupo' => $datos['xIdGrupo'],
            'pIdGrupo' => $datos['IdGrupo'],
            'pxIdMateria' => $datos['xIdMateria'],
            'pIdMateria' => $datos['IdMateria'],
            'pxJerarquico' => $datos['xJerarquico'],
            'pJerarquico' => $datos['Jerarquico'],
            'pxIdTipoCargo' => $datos['xIdTipoCargo'],
            'pIdTipoCargo' => $datos['IdTipoCargo'],
            'pxCodigoPuesto' => $datos['xCodigoPuesto'],
            'pCodigoPuesto' => $datos['CodigoPuesto'],
            'pxTurno' => $datos['xTurno'],
            'pTurno' => $datos['Turno'],
            'pxGrado' => $datos['xGrado'],
            'pGrado' => $datos['Grado'],
            'pxIdPuestoMigracion' => $datos['xIdPuestoMigracion'],
            'pIdPuestoMigracion' => $datos['IdPuestoMigracion'],
            'pxCuil' => $datos['xCuil'],
            'pCuil' => $datos['Cuil'],
            'pxEstado' => $datos['xEstado'],
            'pEstado' => $datos['Estado'],
            'pxIdPofaMigracion' => $datos['xIdPofaMigracion'],
            'pIdPofaMigracion' => $datos['IdPofaMigracion'],
            'pxIdExcepcionTipo' => $datos['xIdExcepcionTipo'],
            'pIdExcepcionTipo' => $datos['IdExcepcionTipo'],
            'pxIdPofa' => $datos['xIdPofa'],
            'pIdPofa' => $datos['IdPofa'],
            'pxEstadoPofa' => $datos['xEstadoPofa'],
            'pEstadoPofa' => $datos['EstadoPofa'],
            'pxPuestoVacante' => $datos['xPuestoVacante'],
            'pxEsPuestoRaiz' => $datos['xEsPuestoRaiz'],
            'plimit' => $datos['limit'],
            'porderby' => $datos['orderby'],
        ];
        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            $this->setError(400, "Error al realizar la búsqueda avanzada. ");
            return false;
        }
        return true;
    }


    protected function BusquedaAvanzadaCantidad(array $datos, &$resultado, ?int &$numfilas): bool {
        $spnombre = "sel_EscuelasPuestos_busqueda_avanzada_cantidad";
        $sparam = [
            'pBasePersonas' => BASEDATOS_PERSONAS,
            'pxIdEscuela' => $datos['xIdEscuela'],
            'pIdEscuela' => $datos['IdEscuela'],
            'pxIdPuesto' => $datos['xIdPuesto'],
            'pIdPuesto' => $datos['IdPuesto'],
            'pxIdPuestoRaiz' => $datos['xIdPuestoRaiz'],
            'pIdPuestoRaiz' => $datos['IdPuestoRaiz'],
            'pxIdEscuelaTurno' => $datos['xIdEscuelaTurno'],
            'pIdEscuelaTurno' => $datos['IdEscuelaTurno'],
            'pxIdSeccion' => $datos['xIdSeccion'],
            'pIdSeccion' => $datos['IdSeccion'],
            'pxIdCargo' => $datos['xIdCargo'],
            'pIdCargo' => $datos['IdCargo'],
            'pxIdGrupo' => $datos['xIdGrupo'],
            'pIdGrupo' => $datos['IdGrupo'],
            'pxIdMateria' => $datos['xIdMateria'],
            'pIdMateria' => $datos['IdMateria'],
            'pxJerarquico' => $datos['xJerarquico'],
            'pJerarquico' => $datos['Jerarquico'],
            'pxIdTipoCargo' => $datos['xIdTipoCargo'],
            'pIdTipoCargo' => $datos['IdTipoCargo'],
            'pxCodigoPuesto' => $datos['xCodigoPuesto'],
            'pCodigoPuesto' => $datos['CodigoPuesto'],
            'pxTurno' => $datos['xTurno'],
            'pTurno' => $datos['Turno'],
            'pxGrado' => $datos['xGrado'],
            'pGrado' => $datos['Grado'],
            'pxIdPuestoMigracion' => $datos['xIdPuestoMigracion'],
            'pIdPuestoMigracion' => $datos['IdPuestoMigracion'],
            'pxCuil' => $datos['xCuil'],
            'pCuil' => $datos['Cuil'],
            'pxEstado' => $datos['xEstado'],
            'pEstado' => $datos['Estado'],
            'pxIdPofaMigracion' => $datos['xIdPofaMigracion'],
            'pIdPofaMigracion' => $datos['IdPofaMigracion'],
            'pxIdExcepcionTipo' => $datos['xIdExcepcionTipo'],
            'pIdExcepcionTipo' => $datos['IdExcepcionTipo'],
            'pxEstadoPofa' => $datos['xEstadoPofa'],
            'pEstadoPofa' => $datos['EstadoPofa'],
            'pxPuestoVacante' => $datos['xPuestoVacante'],
            'pxEsPuestoRaiz' => $datos['xEsPuestoRaiz'],
        ];

        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            $this->setError(400, "Error al realizar la búsqueda avanzada. ");
            return false;
        }
        return true;
    }

    protected function BusquedaAvanzadaSinSeccion(array $datos, &$resultado, ?int &$numfilas): bool {
        $spnombre = "sel_EscuelasPuestos_busqueda_avanzada_xIdSeccionNull";
        $sparam = [
            'pxIdPuesto' => $datos['xIdPuesto'],
            'pIdPuesto' => $datos['IdPuesto'],
            'pxIdEscuela' => $datos['xIdEscuela'],
            'pIdEscuela' => $datos['IdEscuela'],
            'pxIdNivelModalidad' => $datos['xIdNivelModalidad'],
            'pIdNivelModalidad' => $datos['IdNivelModalidad'],
            'pxIdEscuelaTurno' => $datos['xIdEscuelaTurno'],
            'pIdEscuelaTurno' => $datos['IdEscuelaTurno'],
            'pxIdCargo' => $datos['xIdCargo'],
            'pIdCargo' => $datos['IdCargo'],
            'pxIdGrupo' => $datos['xIdGrupo'],
            'pIdGrupo' => $datos['IdGrupo'],
            'pxIdMateria' => $datos['xIdMateria'],
            'pIdMateria' => $datos['IdMateria'],
            'pxCodigoPuesto' => $datos['xCodigoPuesto'],
            'pCodigoPuesto' => $datos['CodigoPuesto'],
            'pxJerarquico' => $datos['xJerarquico'],
            'pJerarquico' => $datos['Jerarquico'],
            'pxIdTipoCargo' => $datos['xIdTipoCargo'],
            'pIdTipoCargo' => $datos['IdTipoCargo'],
            'pxPuestoPadre' => $datos['xPuestoPadre'],
            'pxEstado' => $datos['xEstado'],
            'pEstado' => $datos['Estado'],
            'plimit' => $datos['limit'],
            'porderby' => $datos['orderby'],
        ];
        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            $this->setError(400, "Error al realizar la búsqueda avanzada. ");
            return false;
        }
        return true;
    }


    protected function BusquedaAvanzadaPofa(array $datos, &$resultado, ?int &$numfilas): bool {
        $spnombre = "sel_EscuelasPuestos_busqueda_avanzada_pofa";
        $sparam = [
            'pxIdEscuela' => $datos['xIdEscuela'],
            'pIdEscuela' => $datos['IdEscuela'],
            'pxIdCicloLectivo' => $datos['xIdCicloLectivo'],
            'pIdCicloLectivo' => $datos['IdCicloLectivo'],
            'pxIdNivelModalidad' => $datos['xIdNivelModalidad'],
            'pIdNivelModalidad' => $datos['IdNivelModalidad'],
            'pxIdTurno' => $datos['xIdTurno'],
            'pIdTurno' => $datos['IdTurno'],
            'pxIdGradoAnio' => $datos['xIdGradoAnio'],
            'pIdGradoAnio' => $datos['IdGradoAnio'],
            'pxIdSeccion' => $datos['xIdSeccion'],
            'pIdSeccion' => $datos['IdSeccion'],
            'porderby' => $datos['orderby'],
            'plimit' => $datos['limit'],
        ];
        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            $this->setError(400, "Error al realizar la búsqueda avanzada. ");
            return false;
        }
        return true;
    }


    protected function BuscarAuditoriaRapida(array $datos, &$resultado, ?int &$numfilas): bool {
        $spnombre = "sel_EscuelasPuestos_AuditoriaRapida";
        $sparam = [
            'pIdPuesto' => $datos['IdPuesto'],
        ];
        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            $this->setError(400, "Error al buscar al buscar por codigo. ");
            return false;
        }
        return true;
    }


    protected function BuscarCargosPuestos(array $datos, &$resultado, ?int &$numfilas): bool {
        $spnombre = "sel_CargosTipos_combo_Nombre";
        $sparam = [];
        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            $this->setError(400, "Error al buscar al buscar por codigo. ");
            return false;
        }
        return true;
    }


    protected function BuscarEscuelasPuestos(array $datos, &$resultado, ?int &$numfilas): bool {
        $spnombre = "sel_EscuelasPuestos_xId";
        $sparam = [
            'pIdEscuelaTurno' => $datos['IdEscuelaTurno'],
            'pIdSeccion' => $datos['IdSeccion'],
            'pIdCargo' => $datos['IdCargo'],
            'pIdGrupo' => $datos['IdGrupo'],
            'pIdMateria' => $datos['IdMateria'],
        ];
        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            $this->setError(400, "Error al buscar al buscar por codigo. ");
            return false;
        }
        return true;
    }


    protected function BuscarSecciones(array $datos, &$resultado, ?int &$numfilas): bool {
        $spnombre = "sel_EscuelasCiclos_Secciones_xIdEscuelaTurno_xIdGradoAnio";
        $sparam = [
            'pIdEscuelaTurno' => $datos['IdEscuelaTurno'],
            'pIdGradoAnio' => $datos['IdGradoAnio'],
        ];
        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            $this->setError(400, "Error al buscar al buscar por codigo. ");
            return false;
        }
        return true;
    }

    protected function BuscarEscuelaPOF(array $datos, &$resultado, ?int &$numfilas): bool {
        $spnombre = "sel_EscuelasPuestos_xIdSeccion_Existe";
        $sparam = [
            'pIdSeccion' => $datos['IdSeccion'],
        ];
        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            $this->setError(400, "Error al buscar al buscar por codigo. ");
            return false;
        }
        return true;
    }

    protected function BuscarDetallePuesto(array $datos, &$resultado, ?int &$numfilas): bool {
        $spnombre = "sel_EscuelasPuestos_xIdPuesto_Detalle";
        $sparam = [
            'pIdPuesto' => $datos['IdPuesto'],
        ];
        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            $this->setError(400, "Error al buscar al buscar por codigo. ");
            return false;
        }
        return true;
    }


    protected function BuscarDetallePuestosxIdPersona(array $datos, &$resultado, ?int &$numfilas): bool {
        $spnombre = "sel_EscuelasPuestos_Detalle_xIdPersona";
        $sparam = [
            'pIdPersona' => $datos['IdPersona'],
        ];
        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            $this->setError(400, "Error al buscar al buscar por codigo. ");
            return false;
        }
        return true;
    }


    protected function BuscarDetallePuestosxIdEscuela(array $datos, &$resultado, ?int &$numfilas): bool {
        $spnombre = "sel_EscuelasPuestos_Detalle_xIdEscuela";
        $sparam = [
            'pIdEscuela' => $datos['IdEscuela'],
        ];
        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            $this->setError(400, "Error al buscar al buscar por codigo. ");
            return false;
        }
        return true;
    }


    protected function BuscarDetallePuestoVacantexIdEscuela(array $datos, &$resultado, ?int &$numfilas): bool {
        $spnombre = "sel_EscuelasPuestos_Detalle_Vacante_xIdEscuela";
        $sparam = [
            'pIdPuesto' => $datos['IdPuesto'],
            'pIdEscuela' => $datos['IdEscuela'],
        ];
        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            $this->setError(400, "Error al buscar al buscar por codigo. ");
            return false;
        }
        return true;
    }

    protected function buscarNoVacantesxEscuela(array $datos, &$resultado, ?int &$numfilas): bool {
        $spnombre = "sel_EscuelasPuestos_Vacantes_xIdEscuela";
        $sparam = [
            'pBasePersonas' => BASEDATOS_PERSONAS,
            'pIdEscuela' => $datos['IdEscuela'],
            'pxIdPersona' => $datos['xIdPersona'],
            'pIdPersona' => $datos['IdPersona'],
            'pxCodigoPuesto' => $datos['xCodigoPuesto'],
            'pCodigoPuesto' => $datos['CodigoPuesto'],
            'pxIdCargo' => $datos['xIdCargo'],
            'pIdCargo' => $datos['IdCargo'],
            'pxIdMateria' => $datos['xIdMateria'],
            'pIdMateria' => $datos['IdMateria'],
            'pxIdNivel' => $datos['xIdNivel'],
            'pIdNivel' => $datos['IdNivel'],
            'pxIdTipoCargo' => $datos['xIdTipoCargo'],
            'pIdTipoCargo' => $datos['IdTipoCargo'],
            'pxDNI' => $datos['xDNI'],
            'pDNI' => $datos['DNI'],
            //'pQuery' => $datos['Query'],
        ];

        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            $this->setError(400, "Error al buscar cargos ocupados. ");
            return false;
        }

        return true;
    }

    protected function buscarNoVacantesxIdPuesto(array $datos, &$resultado, ?int &$numfilas): bool {
        $spnombre = "sel_EscuelasPuestos_Vacantes_xIdPuesto";
        $sparam = [
            'pIdEscuela' => $datos['IdEscuela'],
            'pCodigoPuesto' => $datos['CodigoPuesto'],
            'pIdPuesto' => $datos['IdPuesto'],
            'pBasePersonas' => BASEDATOS_PERSONAS,
        ];

        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            $this->setError(400, "Error al buscar cargo ocupado. ");
            return false;
        }
        return true;
    }
    protected function obtenerSeccionesSinAtender(array $datos, &$resultado, ?int &$numfilas): bool {
        $spnombre = "sel_EscuelasPuestos_secciones_vacias_xIdEscuela";
        $sparam = [
            'pIdEscuela' => $datos['IdEscuela']
        ];

        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            $this->setError(400, "Error al buscar cargo ocupado. ");
            return false;
        }
        return true;
    }

    protected function buscarActivosxEscuela(array $datos, &$resultado, ?int &$numfilas): bool {
        $spnombre = "sel_EscuelasPuestos_xIdEscuela_Activos";
        $sparam = [
            'pIdEscuela' => $datos['IdEscuela'],
            //'pQuery' => $datos['Query'],
        ];
        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            $this->setError(400, "Error al buscar no vacantes. ");
            return false;
        }
        return true;
    }

    protected function BuscarDesempenosFaltantesxEscuela(array $datos, &$resultado, ?int &$numfilas): bool {

        $spnombre = 'sel_EscuelasPuestos_Desempeno_xIdEscuela';
        $sparam = [
            'pIdEscuela' => $datos['IdEscuela'],
            'pPlazaInconsistente' => $datos['PlazaInconsistente'],
            'pxAdmiteSuplente' => $datos['xAdmiteSuplente'],
            'pAdmiteSuplente' => $datos['AdmiteSuplente'],
            'pxEnDisponibilidad' => $datos['xEnDisponibilidad'],
            'pEnDisponibilidad' => $datos['EnDisponibilidad'],
        ];

        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            $this->setError(400, utf8_decode("Error al buscar horarios faltantes."));
            return false;
        }
        return true;
    }

    protected function BuscarPersonasFaltantesxEscuela(array $datos, &$resultado, ?int &$numfilas): bool {
        $spnombre = "sel_EscuelasPuestos_Personas_xIdEscuela";
        $sparam = [
            'pIdEscuela' => $datos['IdEscuela'],
        ];
        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            $this->setError(400, "Error al buscar al buscar por codigo. ");
            return false;
        }
        return true;
    }

    protected function BuscarConflictosEnPuestos(array $datos, &$resultado, ?int &$numfilas): bool {
        $spnombre = "sel_EscuelasPuestos_Desempeno_Personas";
        $sparam = [
            'pIdEscuela' => $datos['IdEscuela'],
        ];
        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            $this->setError(400, "Error al buscar al buscar por codigo. ");
            return false;
        }
        return true;
    }

    protected function BuscarPuestosxSeccionxEscuelaTurnoxNivelModalidad(array $datos, &$resultado, ?int &$numfilas): bool {
        $spnombre = 'sel_EscuelasPuestos_IdPuesto_xIdSeccion_xIdEscuelaTurno_xIdNivelModalidad';
        $sparam = [
            'pIdNivelModalidad' => $datos['IdNivelModalidad'],
            'pIdEscuelaTurno' => $datos['IdEscuelaTurno'],
            'pIdSeccion' => $datos['IdSeccion'],
        ];

        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            $this->setError(400, "Error al buscar al buscar por codigo. ");
            return false;
        }
        return true;
    }


    protected function BuscarNombrePuesto(array $datos, &$resultado, ?int &$numfilas): bool {
        $spnombre = 'sel_EscuelasPuestos_NombrePuesto_xIdPuesto';
        $sparam = [
            'pIdPuesto' => $datos['IdPuesto'],
        ];

        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            $this->setError(400, "Error al buscar al buscar por codigo. ");
            return false;
        }
        return true;
    }


    protected function BuscarNombreEscuelaPuesto(array $datos, &$resultado, ?int &$numfilas): bool {
        $spnombre = 'sel_EscuelasPuestos_NombreEscuela_xIdPuesto';
        $sparam = [
            'pIdPuesto' => $datos['IdPuesto'],
        ];

        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            $this->setError(400, "Error al buscar al buscar por codigo. ");
            return false;
        }
        return true;
    }

    protected function buscarTotalxEscuelas(&$resultado, ?int &$numfilas): bool {

        $spnombre = 'sel_EscuelasPuestos_total_habilitadas';
        $sparam = [
            'pIdEscuelaExcluir' => ESCUELAS_DE_PRUEBA,
        ];

        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            $this->setError(400, "Error al buscar al buscar por codigo. ");
            return false;
        }
        return true;
    }


    protected function buscarCargosxPersonaxEscuela(array $datos, &$resultado, ?int &$numfilas): bool {

        $spnombre = 'sel_EscuelasPuestos_xIdPersona_xIdEscuela';
        $sparam = [
            'pIdPersona' => $datos['IdPersona'],
            'pxIdEscuela' => $datos['xIdEscuela'],
            'pIdEscuela' => $datos['IdEscuela'],
            'pxIdPuesto' => $datos['xIdPuesto'],
            'pIdPuesto' => $datos['IdPuesto'],
            'pxEstadosPuestoPersona' => $datos['xEstadosPuestoPersona'],
            'pEstadosPuestoPersona' => $datos['EstadosPuestoPersona'],
            'pxEstadosPuesto' => $datos['xEstadosPuesto'],
            'pEstadosPuesto' => $datos['EstadosPuesto'],
        ];

        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            $this->setError(400, "Error al buscar cargos de la escuela por persona.");
            return false;
        }
        return true;
    }


    protected function buscarxCodigoPuesto(array $datos, &$resultado, ?int &$numfilas): bool {

        $spnombre = 'sel_EscuelasPuestos_xCodigoPuesto';
        $sparam = [
            'pCodigoPuesto' => $datos['CodigoPuesto'],
            'pEstado' => $datos['Estado'],
        ];

        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            $this->setError(400, "Error al buscar por codigo puesto.");
            return false;
        }
        return true;
    }


    protected function buscarPuestosMad(array $datos, &$resultado, ?int &$numfilas): bool {

        $spnombre = 'sel_EscuelasPuestos_Mad';
        $sparam = [
            'pCodigoPuesto' => $datos['CodigoPuesto'],
            'pEstado' => $datos['Estado'],
        ];

        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            $this->setError(400, "Error al buscar por codigo puesto.");
            return false;
        }
        return true;
    }

    public function puestosComputables($datos): array {
        $this->conexion->getParent()->_EjecutarQuery('SET SESSION group_concat_max_len = 1000000;', __FILE__, $_, $errno_salida);

        $spnombre = 'sel_Puestos_computables';
        $sparam = [
            'pIdPersona' => $datos['IdPersona'],
        ];
        $this->conexion->getParent()->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno);
        $datos = $this->conexion->getParent()->ObtenerSiguienteRegistro($resultado);
        return explode(',', $datos['puestos_computables'] ?? '');

    }

    protected function BusquedaRecursivaRaizNullxIdPuesto(array $datos, &$resultado, ?int &$numfilas): bool {

        $spnombre = 'sel_EscuelasPuestos_Recursiva_IdPuestoRaiz_Null_xIdPuesto';
        $sparam = [
            'pIdPuesto' => $datos['IdPuesto'],
        ];

        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            $this->setError(400, "Error al buscar por codigo puesto.");
            return false;
        }
        return true;
    }


    protected function ModificarAdmisionSuplente(array $datos): bool {

        $spnombre = 'upd_EscuelasPuestos_AdmiteSuplente_xId';
        $sparam = [
            'pAdmiteSuplente' => $datos['AdmiteSuplente'],
            'pIdPuesto' => $datos['IdPuesto'],
        ];

        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            $this->setError(400, "Error al actualizar admision de suplente ");
            return false;
        }
        return true;
    }

    protected function InsertarMateria(array $datos, ?int &$codigoInsertado): bool {
        $spnombre = "ins_EscuelasPuestos_Materias";
        $sparam = [
            'pCodigoPuesto' => $datos['CodigoPuesto'],
            'pIdEscuela' => $datos['IdEscuela'],
            'pIdNivelModalidad' => $datos['IdNivelModalidad'],
            'pIdEscuelaTurno' => $datos['IdEscuelaTurno'],
            'pIdSeccion' => $datos['IdSeccion'],
            'pIdPlanEducativo' => $datos['IdPlanEducativo'],
            'pIdGradoAnio' => $datos['IdGradoAnio'],
            'pEstado' => $datos['Estado'],
            'pAltaFecha' => $datos['AltaFecha'],
            'pAltaUsuario' => $datos['AltaUsuario'],
            'pUltimaModificacionFecha' => $datos['UltimaModificacionFecha'],
            'pUltimaModificacionUsuario' => $datos['UltimaModificacionUsuario'],
        ];
        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            $this->setError(400, "Error al insertar. ");
            return false;
        }
        $codigoInsertado = $this->conexion->UltimoCodigoInsertado();
        return true;
    }


    protected function Insertar(array $datos, ?int &$codigoInsertado): bool {
        $spnombre = "ins_EscuelasPuestos";
        $sparam = [
            'pIdPuestoPadre' => $datos['IdPuestoPadre'],
            'pIdPuestoOrigen' => $datos['IdPuestoOrigen'],
            'pCodigoPuesto' => $datos['CodigoPuesto'],
            'pIdEscuela' => $datos['IdEscuela'],
            'pIdNivelModalidad' => $datos['IdNivelModalidad'],
            'pIdEscuelaTurno' => $datos['IdEscuelaTurno'],
            'pIdSeccion' => $datos['IdSeccion'],
            'pIdCargo' => $datos['IdCargo'],
            'pIdGrupo' => $datos['IdGrupo'],
            'pIdMateria' => $datos['IdMateria'],
            'pIdFuncionCargo' => $datos['IdFuncionCargo'],
            'pCantHoras' => $datos['CantHoras'],
            'pCantModulos' => $datos['CantModulos'],
            'pFechaDesde' => $datos['FechaDesde'],
            'pNroResolucion' => $datos['NroResolucion'],
            'pIdPuestoMigracion' => $datos['IdPuestoMigracion'],
            'pAdmiteSuplente' => $datos['AdmiteSuplente'],
            'pMotivoPuesto' => $datos['MotivoPuesto'],
            'pIdTemporalidadPuesto' => $datos['IdTemporalidadPuesto'],
            'pFechaFinPuesto' => $datos['FechaFinPuesto'],
            'pEstado' => $datos['Estado'],
            'pAltaFecha' => $datos['AltaFecha'],
            'pAltaUsuario' => $datos['AltaUsuario'],
            'pUltimaModificacionFecha' => $datos['UltimaModificacionFecha'],
            'pUltimaModificacionUsuario' => $datos['UltimaModificacionUsuario'],
            'pIdFuenteFinanciamiento' => $datos['IdFuenteFinanciamiento'],
            'pCargaManual' => $datos['CargaManual'],
            'pIdRegimenSalarial' => $datos['IdRegimenSalarial'],
            'pIdTipo' => $datos["IdTipo"],
            'pIdEscalafon' => $datos["IdEscalafon"],
            "pDesempenoLugar" => $datos["DesempenoLugar"]
        ];

        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            $this->setError(400, "Error al insertar.");
            return false;
        }
        $codigoInsertado = $this->conexion->UltimoCodigoInsertado();
        return true;
    }


    protected function Modificar(array $datos): bool {

        $spnombre = "upd_EscuelasPuestos_xIdPuesto";
        $sparam = [
            'pCodigoPuesto' => $datos['CodigoPuesto'],
            'pIdEscuela' => $datos['IdEscuela'],
            'pIdNivelModalidad' => $datos['IdNivelModalidad'],
            'pIdEscuelaTurno' => $datos['IdEscuelaTurno'],
            'pIdSeccion' => $datos['IdSeccion'],
            'pIdCargo' => $datos['IdCargo'],
            'pIdGrupo' => $datos['IdGrupo'],
            'pIdSubGrupo' => $datos['IdSubGrupo'],
            'pIdMateria' => $datos['IdMateria'],
            'pIdFuncionCargo' => $datos['IdFuncionCargo'],
            'pCantHoras' => $datos['CantHoras'],
            'pCantModulos' => $datos['CantModulos'],
            'pFechaDesde' => $datos['FechaDesde'],
            'pNroResolucion' => $datos['NroResolucion'],
            'pIdPuestoMigracion' => $datos['IdPuestoMigracion'],
            'pAdmiteSuplente' => $datos['AdmiteSuplente'],
            'pMotivoPuesto' => $datos['MotivoPuesto'],
            'pIdTemporalidadPuesto' => $datos['IdTemporalidadPuesto'],
            'pIdFuenteFinanciamiento' => $datos['IdFuenteFinanciamiento'],
            'pFechaFinPuesto' => $datos['FechaFinPuesto'],
            'pEstado' => $datos['Estado'],
            'pUltimaModificacionFecha' => date("Y/m/d H:i:s"),
            'pUltimaModificacionUsuario' => $_SESSION['usuariocod'],
            'pIdPuesto' => $datos['IdPuesto'],
            'pIdRegimenSalarial' => $datos['IdRegimenSalarial'],
            'pIdTipo' => $datos["IdTipo"],
            'pIdEscalafon' => $datos["IdEscalafon"],
            "pDesempenoLugar" => $datos["DesempenoLugar"]
        ];

        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            $this->setError(400, "Error al modificar. ");
            return false;
        }
        return true;
    }


    protected function ModificarSeccionCargoMateriaxIdPuesto(array $datos): bool {
        $spnombre = "upd_EscuelasPuestos_IdSeccion_IdCargo_IdMateria_xIdPuesto";
        $sparam = [
            'pCodigoPuesto' => $datos['CodigoPuesto'],
            'pIdPuestoMigracion' => $datos['IdPuestoMigracion'],
            'pIdSeccion' => $datos['IdSeccion'],
            'pIdCargo' => $datos['IdCargo'],
            'pIdMateria' => $datos['IdMateria'],
            'pFechaDesde' => $datos['FechaDesde'],
            /*'pMotivoPuesto' => $datos['MotivoPuesto'],
            'pIdTemporalidadPuesto' => $datos['IdTemporalidadPuesto'],
            'pFechaFinPuesto' => $datos['FechaFinPuesto'],*/
            'pUltimaModificacionFecha' => date("Y/m/d H:i:s"),
            'pUltimaModificacionUsuario' => $_SESSION['usuariocod'],
            'pIdPuesto' => $datos['IdPuesto'],
        ];
        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            $this->setError(400, "Error al modificar. ");
            return false;
        }
        return true;
    }


    protected function ModificarHoras(array $datos): bool {
        $spnombre = "upd_EscuelasPuestos_CantHoras";
        $sparam = [
            'pCantHoras' => $datos['CantHorasModulos'],
            'pUltimaModificacionFecha' => date("Y/m/d H:i:s"),
            'pUltimaModificacionUsuario' => $_SESSION['usuariocod'],
            'pIdPuesto' => $datos['IdPuesto'],
        ];
        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            $this->setError(400, "Error al modificar. ");
            return false;
        }
        return true;
    }


    protected function ModificarModulos(array $datos): bool {
        $spnombre = "upd_EscuelasPuestos_CantModulos";
        $sparam = [
            'pCantModulos' => $datos['CantHorasModulos'],
            'pUltimaModificacionFecha' => date("Y/m/d H:i:s"),
            'pUltimaModificacionUsuario' => $_SESSION['usuariocod'],
            'pIdPuesto' => $datos['IdPuesto'],
        ];
        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            $this->setError(400, "Error al modificar. ");
            return false;
        }
        return true;
    }

    protected function ModificarCargo(array $datos): bool {
        $spnombre = "upd_EscuelasPuestos_Cargo";
        $sparam = [
            'pIdCargo' => $datos['IdCargo'],
            'pUltimaModificacionFecha' => date("Y/m/d H:i:s"),
            'pUltimaModificacionUsuario' => $_SESSION['usuariocod'],
            'pIdPuesto' => $datos['IdPuesto'],
        ];
        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            $this->setError(400, "Error al modificar. ");
            return false;
        }
        return true;
    }


    protected function ModificarMateria(array $datos): bool {
        $spnombre = "upd_EscuelasPuestos_Materia";
        $sparam = [
            'pIdMateria' => $datos['IdMateria'],
            'pUltimaModificacionFecha' => date("Y/m/d H:i:s"),
            'pUltimaModificacionUsuario' => $_SESSION['usuariocod'],
            'pIdPuesto' => $datos['IdPuesto'],
        ];
        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            $this->setError(400, "Error al modificar. ");
            return false;
        }
        return true;
    }

    protected function ModificarCodigoPuesto(array $datos): bool {
        $spnombre = 'upd_EscuelasPuestos_CodigoPuesto_xIdPuesto';
        $sparam = [
            'pCodigoPuesto' => $datos['CodigoPuesto'],
            'pUltimaModificacionFecha' => date("Y/m/d H:i:s"),
            'pUltimaModificacionUsuario' => $_SESSION['usuariocod'],
            'pIdPuesto' => $datos['IdPuesto'],
        ];

        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            $this->setError(400, "Error al modificar. ");
            return false;
        }
        return true;
    }


    protected function ModificarPuestoRaiz(array $datos): bool {
        $spnombre = 'upd_EscuelasPuestos_IdPuestoRaiz_xIdPuesto';
        $sparam = [
            'pIdPuestoRaiz' => $datos['IdPuestoRaiz'],
            'pUltimaModificacionFecha' => date("Y/m/d H:i:s"),
            'pUltimaModificacionUsuario' => $datos['UltimaModificacionUsuario'],
            'pIdPuesto' => $datos['IdPuesto'],
        ];

        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            $this->setError(400, "Error al modificar. ");
            return false;
        }
        return true;
    }

    protected function ModificarNumeroResolucion(array $datos): bool {
        $spnombre = 'upd_EscuelasPuestos_NroResolucion';
        $sparam = [
            'pNroResolucion' => $datos['NroResolucion'],
            'pUltimaModificacionFecha' => date("Y/m/d H:i:s"),
            'pUltimaModificacionUsuario' => $_SESSION['usuariocod'],
            'pIdPuesto' => $datos['IdPuesto'],
        ];

        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            $this->setError(400, "Error al modificar. ");
            return false;
        }
        return true;
    }

    protected function Eliminar(array $datos): bool {
        $spnombre = "del_EscuelasPuestos_xIdPuesto";
        $sparam = [
            'pIdPuesto' => $datos['IdPuesto'],
        ];
        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            $this->setError(400, "Error al eliminar por codigo. ");
            return false;
        }
        return true;
    }


    protected function ReacomodarPuesto(array $datos, $datosRegistro): bool {

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


    protected function ModificarEstado(array $datos): bool {
        $spnombre = "upd_EscuelasPuestos_Estado_xIdPuesto";
        $sparam = [
            'pEstado' => $datos['Estado'],
            'pIdPuesto' => $datos['IdPuesto'],
        ];
        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            $this->setError(400, "Error al modificar el estado. ");
            return false;
        }
        return true;
    }


    protected function ModificarFechaHasta(array $datos): bool {
        $spnombre = "upd_EscuelasPuestos_FechaHasta_xIdPuesto";
        $sparam = [
            'pFechaHasta' => $datos['FechaHasta'],
            'pUltimaModificacionFecha' => date("Y-m-d H:i:s"),
            'pUltimaModificacionUsuario' => $_SESSION['usuariocod'],
            'pIdPuesto' => $datos['IdPuesto'],
        ];
        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            $this->setError(400, "Error al modificar el estado. ");
            return false;
        }
        return true;
    }


    protected function ObtenerDatosPlazasXRangoFecha_Cantidad($datos, &$resultado, &$numfilas) {
        $spnombre = "sel_EscuelasPuestos_cantidad";
        $sparam = [
            'pxIdPuesto' => $datos['xIdPuesto'],
            'pIdPuesto' => $datos['IdPuesto'],
            'pxIdPuestoMigracion' => $datos['xIdPuestoMigracion'],
            'pIdPuestoMigracion' => $datos['IdPuestoMigracion'],
            'pxCodigoPuesto' => $datos['xCodigoPuesto'],
            'pCodigoPuesto' => $datos['CodigoPuesto'],
            'pxUltimaModificacionFecha' => $datos['xUltimaModificacionFecha'],
            'pUltimaModificacionFecha' => $datos['UltimaModificacionFecha'],
            'pxFechaFinPuesto' => $datos['xFechaFinPuesto'],
            'pFechaFinPuesto' => $datos['FechaFinPuesto'],
            'pxAltaFecha' => $datos['xAltaFecha'],
            'pAltaFecha' => $datos['AltaFecha'],
            'pEscuelasPrueba' => ESCUELAS_DE_PRUEBA,
            'plimit' => $datos['limit'],
            'porderby' => $datos['orderby'],
        ];

        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, "Error al realizar la búsqueda avanzada. ", ["archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__], ["formato" => $this->formato]);
            return false;
        }

        return true;
    }


    protected function ObtenerDatosPlazasXRangoFecha($datos, &$resultado, &$numfilas) {
        $spnombre = "sel_EscuelasPuestos";
        $sparam = [
            'pxIdPuesto' => $datos['xIdPuesto'],
            'pIdPuesto' => $datos['IdPuesto'],
            'pxIdPuestoMigracion' => $datos['xIdPuestoMigracion'],
            'pIdPuestoMigracion' => $datos['IdPuestoMigracion'],
            'pxCodigoPuesto' => $datos['xCodigoPuesto'],
            'pCodigoPuesto' => $datos['CodigoPuesto'],
            'pxUltimaModificacionFecha' => $datos['xUltimaModificacionFecha'],
            'pUltimaModificacionFecha' => $datos['UltimaModificacionFecha'],
            'pxFechaFinPuesto' => $datos['xFechaFinPuesto'],
            'pFechaFinPuesto' => $datos['FechaFinPuesto'],
            'pxAltaFecha' => $datos['xAltaFecha'],
            'pAltaFecha' => $datos['AltaFecha'],
            'pEscuelasPrueba' => ESCUELAS_DE_PRUEBA,
            'plimit' => $datos['limit'],
            'porderby' => $datos['orderby'],
        ];

        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, "Error al realizar la búsqueda avanzada. ", ["archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__], ["formato" => $this->formato]);
            return false;
        }

        return true;
    }



    protected function BuscarDatosCupof($datos, &$resultado){

        $spnombre="sel_DatosCupof";
        $sparam=array(
            "pIdEscuela" => $datos["IdEscuela"],
            "pxIdPlanEducativo" => $datos["xIdPlanEducativo"],
            'pIdPlanEducativo'=> $datos['IdPlanEducativo'],
            'pxIdEscuelaTurnoAnioGrado'=> $datos['xIdEscuelaTurnoAnioGrado'],
            'pIdEscuelaTurnoAnioGrado'=> $datos['IdEscuelaTurnoAnioGrado'],
            'pxIdSeccion'=> $datos['xIdSeccion'],
            'pIdSeccion'=> $datos['IdSeccion'],
            'pIdFuncionCargo'=> $datos['IdFuncionCargo'],
            'pxIdMateria'=> $datos['xIdMateria'],
            'pIdMateria'=> $datos['IdMateria'],
            'pIdEscuelaTurno'=> $datos['IdEscuelaTurno'],
            'pxIdPuesto' => $datos["xIdPuesto"],
            'pIdPuesto' => $datos["IdPuesto"],
            'pIdCargo'=> $datos['IdCargo']
        );

        if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno) )
        {
            FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al realizar la busqueda de datos para el cupof",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
            return false;
        }

        return true;
    }

    protected function ComboDesempenosLugar($datos, &$resultado, &$numfilas): bool
    {
        $spnombre="sel_EscuelasPuestosDesempenoLugar_combo";
        $sparam=array();

        if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno) )
        {
            FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al realizar la busqueda de datos para el cupof",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
            return false;
        }

        return true;
    }


}

