<?php

abstract class cTiposPOFdb {
    use ManejoErrores;

    /** @var accesoBDLocal */
    protected $conexion;
    /** @var mixed */
    protected $formato;
    /** @var array */
    protected $error;

    /**
     * Constructor de la clase cTiposPOFDB.
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



    protected function BusquedaAvanzada(array $datos, &$resultado, ?int &$numfilas): bool {
        $spnombre = "sel_TiposPOF_busqueda_avanzada";
        $sparam = [
            'pxIdNivel' => $datos['xIdNivel'],
            'pIdNivel' => $datos['IdNivel'],
            'pxIdModalidad' => $datos['xIdModalidad'],
            'pIdModalidad' => $datos['IdModalidad'],
            'pxEstado' => $datos['xEstado'],
            'pEstado' => $datos['Estado'],
            'plimit' => $datos['limit'],
            'porderby' => $datos['orderby'],
        ];
        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            $this->setError(400, "Error al realizar la búsqueda avanzada de Tipos de POF. ");
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




    protected function Insertar(array $datos, ?int &$codigoInsertado): bool {
        $spnombre = "ins_EscuelasPuestos";
        $sparam = [
            'pIdPuestoPadre' => $datos['IdPuestoPadre'],
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

}

