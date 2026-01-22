<?php
abstract class cSolicitudesCoberturaPuestoDB
{
    use ManejoErrores;
    /** @var accesoBDLocal  */
    protected $conexion;
    /** @var mixed  */
    protected $formato;
    /** @var array  */
    protected $error;
    /**
     * Constructor de la clase cSolicitudesCoberturaPuestoDB.
     *
     * Recibe un objeto accesoBDLocal y el formato a de  los mensajes de salida
     * $formato = FMT_TEXTO escribe en pantalla una caja con el mensaje de error, el tipo de caja depende del nivel de error
     *            FMT_ARRAY escribe el mensaje de error en la propiedad $error de la clase la cual puede ser accedida desde el método getError()
     *            otros escribe en pantalla el mensaje en texto plano
     *
     * @param accesoBDLocal $conexion
     * @param mixed         $formato
     */
    function __construct(accesoBDLocal $conexion,$formato) {
        $this->conexion = &$conexion;
        $this->formato = &$formato;
    }

    /**
     * Destructor de la clase cSolicitudesCoberturaDB.
     */
    function __destruct() {}

    /**
     * @param array    $datos
     * @param          $resultado
     * @param int|null $numfilas
     * @return bool
     */
    protected function buscarxCodigo(array $datos, &$resultado, ?int &$numfilas): bool {

        $spnombre = 'sel_SolicitudesCoberturaPuesto_xId';
        $sparam = [
            'pId' => $datos['Id']
        ];

        if (!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno)) {
            $this->setError(400,"Error al buscar al buscar por codigo. ");
            return false;
        }
        return true;
    }

    /**
     * @param array    $datos
     * @param          $resultado
     * @param int|null $numfilas
     * @return bool
     */
    protected function BuscarxPuesto(array $datos, &$resultado, ?int &$numfilas): bool {

        $spnombre = 'sel_SolicitudesCoberturaPuesto_xIdPuesto';
        $sparam = [
            'pIdPuesto' => $datos['IdPuesto']
        ];

        if (!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno)) {
            $this->setError(400,"Error al buscar al buscar por puesto. ");
            return false;
        }
        return true;
    }

    /**
     * @param array    $datos
     * @param          $resultado
     * @param int|null $numfilas
     * @return bool
     */
    protected function BuscarxSolicitudCoberturaPersona(array $datos, &$resultado, ?int &$numfilas): bool {

        $spnombre = 'sel_SolicitudesCoberturaPuesto_xIdSolicitudCoberturaPersona';
        $sparam = [
            'pIdSolicitudCoberturaPersona' => $datos['IdSolicitudCoberturaPersona']
        ];

        if (!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno)) {
            $this->setError(400,"Error al buscar al buscar por persona en solicitud. ");
            return false;
        }
        return true;
    }

    /**
     * @param array $datos
     * @param          $resultado
     * @param int|null $numfilas
     * @return bool
     */
    protected function buscarxSolicitudCobertura(array $datos, &$resultado, ?int &$numfilas): bool {

        $spnombre = 'sel_SolicitudesCoberturaPuesto_xIdSolicitudCobertura';
        $sparam = [
            'pIdSolicitudCobertura' => $datos['IdSolicitudCobertura']
        ];

        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            $this->setError(400, 'Error al buscar al buscar por codigo. ');
            return false;
        }
        return true;
    }

    /**
     * @param array $datos
     * @param          $resultado
     * @param int|null $numfilas
     * @return bool
     */
    protected function buscarPuestosSobrantes(array $datos, &$resultado, ?int &$numfilas): bool {

        $spnombre = 'sel_SolicitudesCoberturaPuesto_puestos_sobrantes';
        $sparam = [
            'pIdSolicitudCobertura' => $datos['IdSolicitudCobertura']
        ];

        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            $this->setError(400, 'Error al buscar al buscar por codigo. ');
            return false;
        }
        return true;
    }

    /**
     * @param array $datos
     * @param          $resultado
     * @param int|null $numfilas
     * @return bool
     */
    protected function buscarxSolicitudCoberturaPersonaxPuesto(array $datos, &$resultado, ?int &$numfilas): bool {

        $spnombre = 'sel_SolicitudesCoberturaPuesto_xIdPuesto_xIdSCPersona';
        $sparam = [
            'pIdSolicitudCoberturaPersona' => $datos['IdSolicitudCoberturaPersona'],
            'pIdPuesto' => $datos['IdPuesto'],
        ];

        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            $this->setError(400, 'Error al buscar al buscar por codigo. ');
            return false;
        }
        return true;
    }

    /**
     * @param array $datos
     * @param          $resultado
     * @param int|null $numfilas
     * @return bool
     */
    protected function buscarEscuelasPuestosxSolicitud(array $datos, &$resultado, ?int &$numfilas): bool {

        $spnombre = 'sel_SolicitudesCoberturaPuesto_EscuelasPuestos_xIdSolicitudCobertura';
        $sparam = [
            'pIdSolicitudCobertura' => $datos['IdSolicitudCobertura'],
            'pIdEstado' => DESIGNADO,
        ];

        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            $this->setError(400, 'Error al buscar al buscar por codigo. ');
            return false;
        }
        return true;
    }


    /**
     * @param array    $datos
     * @param          $resultado
     * @param int|null $numfilas
     * @return bool
     */
    protected function buscarParaElastic(array $datos, &$resultado, ?int &$numfilas): bool {

        $spnombre = 'sel_SolicitudesCoberturaPuesto_es_xId';
        $sparam = [
            'pBasePersonas' => BASEDATOS_PERSONAS,
            'pId' => $datos['Id']
        ];

        if (!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno)) {
            $this->setError(400,"Error al buscar al buscar por persona en solicitud. ");
            return false;
        }
        return true;
    }

    /**
     * @param array $datos
     * @param          $resultado
     * @param int|null $numfilas
     * @return bool
     */
    protected function buscarParaElasticXSubSolicitud(array $datos, &$resultado, ?int &$numfilas): bool {

        $spnombre = 'sel_SolicitudesCoberturaPuesto_es_xIdSolicitudCoberturaPersona';
        $sparam = [
            'pBasePersonas' => BASEDATOS_PERSONAS,
            'pIdSolicitudCoberturaPersona' => $datos['IdSolicitudCoberturaPersona']
        ];

        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            $this->setError(400, 'Error al buscar al buscar por persona en solicitud. ');
            return false;
        }
        return true;
    }

    /**
     * ### Busca los puestos cuyos desempeños fueron asignados a diferentes agentes
     *
     * Devuelve una lista de puestos, la cantidad de divisiones que tienen y un listados
     * de identificadores registros por cada uno.
     *
     * @param array    $datos
     * @param          $resultado
     * @param int|null $numfilas
     *
     * @return bool
     */
    protected function buscarPuestosDivididos(array $datos, &$resultado, ?int &$numfilas): bool {
        $spnombre = 'sel_SolicitudesCoberturaPuesto_CantidadesxIdSolicitudCobertura';
        $sparam = [
            'pIdSolicitudCobertura' => $datos['IdSolicitudCobertura'],
        ];

        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            $this->setError(400, 'Error al buscar puestos divididos ');
            return false;
        }
        return true;
    }

    /**
     * ### Busca los datos del puesto
     *
     * Este método, fundamental para la inserción de sub-puestos trae todos
     * los datos necesarios para insertarlos.
     *
     * @param array    $datos
     * @param          $resultado
     * @param int|null $numfilas
     *
     * @return bool
     */
    protected function buscarDatosPuestoxCodigo(array $datos, &$resultado, ?int &$numfilas): bool {
        $spnombre = 'sel_SolicitudesCoberturaPuesto_Completo_xId';
        $sparam = [
            'pId' => $datos['Id'],
        ];

        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            $this->setError(400, 'Error al buscar los datos del puesto');
            return false;
        }
        return true;
    }

    /**
     * @param array    $datos
     * @param int|null $codigoInsertado
     *
     * @return bool
     */
    protected function Insertar(array $datos, ?int &$codigoInsertado): bool {

        $spnombre = 'ins_SolicitudesCoberturaPuesto';
        $sparam = [
            'pIdSolicitudCobertura' => $datos['IdSolicitudCobertura'],
            'pIdPuesto' => $datos['IdPuesto'],
            'pTipoCantidad' => $datos['TipoCantidad'],
            'pCantidadHorasModulos' => $datos['CantidadHorasModulos'],
            'pPuedeDesglosar' => $datos['PuedeDesglosar'],
            'pDesglosado' => $datos['Desglosado'],
            'pEstado' => ACTIVO,
            'pAltaUsuario' => $datos['AltaUsuario'],
            'pAltaFecha' => $datos['AltaFecha'],
            'pUltimaModificacionUsuario' => $datos['UltimaModificacionUsuario'],
            'pUltimaModificacionFecha' => $datos['UltimaModificacionFecha']
        ];

        if (!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno)) {
            $this->setError(400, utf8_encode("Error interno - cód: iscp - Comuníquese con el área de Sistemas. "));
            return false;
        }

        $codigoInsertado = $this->conexion->UltimoCodigoInsertado();
        return true;
    }

    /**
     * @param array    $datos
     * @return bool
     */
    protected function Modificar(array $datos): bool {

        $spnombre = 'upd_SolicitudesCoberturaPuesto_xId';
        $sparam = [
            'pIdSolicitudCoberturaPersona' => $datos['IdSolicitudCoberturaPersona'],
            'pCantidadHorasModulos' => $datos['CantidadHorasModulos'],
            'pTildado' => $datos['Tildado'],
            'pUltimaModificacionUsuario' => $datos['UltimaModificacionUsuario'],
            'pUltimaModificacionFecha' => $datos['UltimaModificacionFecha'],
            'pId' => $datos['Id']
        ];

        if (!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno)) {
            $this->setError(400,"Error al modificar sc puesto. ");
            return false;
        }

        return true;
    }

    /**
     * @param array $datos
     *
     * @return bool
     */
    protected function modificarTildado(array $datos): bool {
        $spnombre = 'upd_SolicitudesCoberturaPuesto_Tildado_xId';
        $sparam = [
            'pTildado' => $datos['Tildado'],
            'pUltimaModificacionUsuario' => $datos['UltimaModificacionUsuario'],
            'pUltimaModificacionFecha' => $datos['UltimaModificacionFecha'],
            'pId' => $datos['Id']
        ];

        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            $this->setError(400, 'Error al modificar sc puesto.');
            return false;
        }
        return true;
    }

    /**
     * @param array $datos
     * @return bool
     */
    public function modificarDesglosePorPuesto(array $datos): bool {

        $spnombre = 'upd_SolicitudesCoberturaPuesto_Desglosado_xId';
        $sparam = [
            'pDesglosado' => $datos['Desglosado'],
            'pUltimaModificacionUsuario' => $datos['UltimaModificacionUsuario'],
            'pUltimaModificacionFecha' => $datos['UltimaModificacionFecha'],
            'pId' => $datos['Id']
        ];

        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            $this->setError(400, 'Error al modificar desglose por puesto.');
            return false;
        }
        return true;
    }

    /**
     * @param array $datos
     * @return bool
     */
    public function modificarDesglosePorSolicitud(array $datos): bool {

        $spnombre = 'upd_SolicitudesCoberturaPuesto_Desglosado_xIdSolicitudCobertura';
        $sparam = [
            'pDesglosado' => $datos['Desglosado'],
            'pUltimaModificacionUsuario' => $datos['UltimaModificacionUsuario'],
            'pUltimaModificacionFecha' => $datos['UltimaModificacionFecha'],
            'pIdSolicitudCobertura' => $datos['IdSolicitudCobertura']
        ];

        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            $this->setError(400, 'Error al modificar desglose por sc1.');
            return false;
        }
        return true;
    }




    /**
     * ### Modifica el IdPuesto para que refleje el valor del sub-puesto
     *
     * @param array $datos
     *
     * @return bool
     */
    protected function modificarPuesto(array $datos): bool {

        $spnombre = 'upd_SolicitudesCoberturaPuesto_IdPuesto_xId';
        $sparam = [
            'pIdPuesto' => $datos['IdPuesto'],
            'pUltimaModificacionUsuario' => $datos['UltimaModificacionUsuario'],
            'pUltimaModificacionFecha' => $datos['UltimaModificacionFecha'],
            'pId' => $datos['Id']
        ];

        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            $this->setError(400, 'Error al modificar sc puesto.--');
            return false;
        }

        return true;
    }

    /**
     * @param array    $datos
     * @return bool
     */
    protected function ModificarxPuestoxSolicitudCobertura(array $datos): bool {

        $spnombre = 'upd_SolicitudesCoberturaPuesto_xIdPuesto_xIdSolicitudCobertura';
        $sparam = [
            'pIdSolicitudCoberturaPersona' => $datos['IdSolicitudCoberturaPersona'],
            'pTildado' => $datos['Tildado'],
            'pUltimaModificacionUsuario' => $datos['UltimaModificacionUsuario'],
            'pUltimaModificacionFecha' => $datos['UltimaModificacionFecha'],
            'pIdPuesto' => $datos['IdPuesto'],
            'pIdSolicitudCobertura' => $datos['IdSolicitudCobertura']
        ];

        if (!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno)) {
            $this->setError(400,"Error al modificar sc puesto. ");
            return false;
        }

        return true;
    }

    /**
     * @param array    $datos
     * @return bool
     */
    protected function ModificarxSolicitudCoberturaPersona(array $datos): bool {

        $spnombre = 'upd_SolicitudesCoberturaPuesto_xIdPuesto_xIdSolicitudCobertura';
        $sparam = [
            'pIdSolicitudCoberturaPersona' => $datos['IdSolicitudCoberturaPersona'],
            'pUltimaModificacionUsuario' => $datos['UltimaModificacionUsuario'],
            'pUltimaModificacionFecha' => $datos['UltimaModificacionFecha'],
            'pIdPuesto' => $datos['IdPuesto'],
            'pIdSolicitudCobertura' => $datos['IdSolicitudCobertura']
        ];

        if (!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno)) {
            $this->setError(400,"Error al modificar sc puesto. ");
            return false;
        }

        return true;
    }

    /**
     * @param array    $datos
     * @return bool
     */
    protected function ModificarxSolicitudCobertura(array $datos): bool {

        $spnombre = 'upd_SolicitudesCoberturaPuesto_xIdSolicitudCobertura';
        $sparam = [
            'pIdSolicitudCoberturaPersona' => $datos['IdSolicitudCoberturaPersona'],
            'pTildado' => $datos['Tildado'],
            'pUltimaModificacionUsuario' => $datos['UltimaModificacionUsuario'],
            'pUltimaModificacionFecha' => $datos['UltimaModificacionFecha'],
            'pIdSolicitudCobertura' => $datos['IdSolicitudCobertura']
        ];

        if (!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno)) {
            $this->setError(400,"Error al modificar sc puesto. ");
            return false;
        }

        return true;
    }

    /**
     * @param array    $datos
     * @return bool
     */
    protected function ModificarxSolicitudCoberturaxPuestoxDesempeno(array $datos): bool {

        $spnombre = 'upd_SolicitudesCoberturaPuesto_x_IdSolicitudCobertura_xIdPuesto_xIdDesempeno';
        $sparam = [
            'pIdSolicitudCoberturaPersona' => $datos['IdSolicitudCoberturaPersona'],
            'pUltimaModificacionUsuario' => $datos['UltimaModificacionUsuario'],
            'pUltimaModificacionFecha' => $datos['UltimaModificacionFecha'],
            'pIdSolicitudCobertura' => $datos['IdSolicitudCobertura'],
            'pIdPuesto' => $datos['IdPuesto'],
            'pIdDesempeno' => $datos['IdDesempeno'],
        ];

        if (!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno)) {
            $this->setError(400,"Error al insertar sc puesto. ");
            return false;
        }

        return true;
    }

    /**
     * @param array    $datos
     * @return bool
     */   protected function Eliminar(array $datos): bool {

        $spnombre = 'del_SolicitudesCoberturaPuesto_xId';
        $sparam = [
            'pId'=> $datos['Id']
        ];

        if (!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno)) {
            $this->setError(400,"Error al eliminar por codigo. ");
            return false;
        }
        return true;
    }

    /**
     * @param array $datos
     * @return bool
     */
    /*
    protected function eliminarxSolicitud(array $datos): bool {
        $spnombre = "del_SolicitudesCoberturaPuesto_xIdSolicitudCobertura";
        $sparam = array(
            'pIdSolicitudCobertura' => $datos['IdSolicitudCobertura']
        );
        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            $this->setError(400, "Error al eliminar puesto por solicitud. ");
            return false;
        }
        return true;
    } */
    protected function eliminarxSolicitud(array $datos): bool {
        $spnombre = "upd_SolicitudesCoberturaPuesto_Estado_xIdSolicitudCobertura";
        $sparam = array(
            'pIdSolicitudCobertura' => $datos['IdSolicitudCobertura'],
            "pEstado" => $datos["Estado"],
            'pUltimaModificacionUsuario'=> $datos['UltimaModificacionUsuario'],
            'pUltimaModificacionFecha'=> $datos['UltimaModificacionFecha']
        );
        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            $this->setError(400, "Error al eliminar puesto por solicitud. ");
            return false;
        }
        return true;
    }

    /**
     * @param array    $datos
     * @return bool
     */
    protected function EliminarVarios(array $datos): bool {

        $spnombre = 'del_SolicitudesCoberturaPuesto_xIds';
        $sparam = [
            'pIds'=> $datos['Ids']
        ];

        if (!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno)) {
            $this->setError(400,"Error al eliminar por codigo. ");
            return false;
        }
        return true;
    }

}
