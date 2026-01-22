<?php
abstract class cSolicitudesCoberturaPersonaDB
{
    use ManejoErrores;
    /** @var accesoBDLocal  */
    protected $conexion;
    /** @var mixed  */
    protected $formato;
    /** @var array  */
    protected $error;
    /**
     * Constructor de la clase cSolicitudesCoberturaPersonaDB.
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

        $spnombre = 'sel_SolicitudesCoberturaPersona_xId';
        $sparam = [
            'pId'=> $datos['Id'],
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
    protected function buscarxSolicitudCobertura(array $datos, &$resultado, ?int &$numfilas): bool {

        $spnombre = 'sel_SolicitudesCoberturaPersona_xIdSolicitudCobertura';
        $sparam = [
            'pBasePersonas' => BASEDATOS_PERSONAS,
            'pIdSolicitudCobertura'=> $datos['IdSolicitudCobertura'],
        ];

        if (!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno)) {
            $this->setError(400,"Error al buscar al buscar por solicitud. ");
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
    protected function buscarAsignadosxSolicitudCobertura(array $datos, &$resultado, ?int &$numfilas): bool {

        $spnombre = 'sel_SolicitudesCoberturaPersona_xIdSolicitudCobertura_Asignados';
        $sparam = [
            'pIdSolicitudCobertura'=> $datos['IdSolicitudCobertura'],
        ];

        if (!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno)) {
            $this->setError(400,"Error al buscar al buscar por solicitud. ");
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
    protected function buscarxSolicitudCoberturaPersona(array $datos, &$resultado, ?int &$numfilas): bool {

        $spnombre = 'sel_SolicitudesCoberturaPersona_xIdSolicitudCobertura_IdPersona';
        $sparam = [
            'pIdSolicitudCobertura' => $datos['IdSolicitudCobertura'],
            'pIdPersona' => $datos['IdPersona'],
            'pTipo' => $datos['Tipo'],
            'pNull' => $datos['TraerNull']??0,
        ];

        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            $this->setError(400, 'Error al buscar por solicitud. ');
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
    protected function buscarxSolicitudCoberturaDesempeno(array $datos, &$resultado, ?int &$numfilas): bool {

        $spnombre = 'sel_SolicitudesCoberturaPersona_xIdSolicitudCoberturaDesempeno';
        $sparam = [
            'pIdSolicitudCoberturaDesempeno' => $datos['IdSolicitudCoberturaDesempeno'],
        ];

        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            $this->setError(400, 'Error al buscar por solicitud. ');
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
    protected function buscarxSolicitudCoberturaxPuesto(array $datos, &$resultado, ?int &$numfilas): bool {

        $spnombre = 'sel_SolicitudesCoberturaPersona_xIdSolicitudCobertura_xIdPuesto';
        $sparam = [
            'pIdSolicitudCobertura' => $datos['IdSolicitudCobertura'],
            'pIdPuesto' => $datos['IdPuesto'],
        ];

        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            $this->setError(400, 'Error al buscar por solicitud. ');
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
    protected function buscarxSolicitudCoberturaxPuestoxPersona(array $datos, &$resultado, ?int &$numfilas): bool {

        $spnombre = 'sel_SolicitudesCoberturaPersona_xIdSolicitudCobertura_xIdPersonaDesignada_xIdPuesto';
        $sparam = [
            'pIdSolicitudCobertura' => $datos['IdSolicitudCobertura'],
            'pIdPersonaDesignada' => $datos['IdPersonaDesignada'],
            'pIdPuesto' => $datos['IdPuesto'],
        ];

        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            $this->setError(400, 'Error al buscar por solicitud. ');
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
    protected function buscarxSolicitudCoberturaxPersona(array $datos, &$resultado, ?int &$numfilas): bool {

        $spnombre = 'sel_SolicitudesCoberturaPersona_xIdSolicitudCobertura_xIdPersonaDesignada';
        $sparam = [
            'pIdSolicitudCobertura' => $datos['IdSolicitudCobertura'],
            'pIdPersonaDesignada' => $datos['IdPersonaDesignada'],
        ];

        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            $this->setError(400, 'Error al buscar por solicitud. ');
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
    protected function buscarPersonasSobrantes(array $datos, &$resultado, ?int &$numfilas): bool {

        $spnombre = 'sel_SolicitudesCoberturaPersona_personas_sobrantes';
        $sparam = [
            'pIdSolicitudCobertura' => $datos['IdSolicitudCobertura'],
        ];

        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            $this->setError(400, 'Error al buscar por solicitud. ');
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
    protected function buscarPersonasPuestosSobrantes(array $datos, &$resultado, ?int &$numfilas): bool {

        $spnombre = 'sel_SolicitudesCoberturaPersona_personasPuestos_sobrantes';
        $sparam = [
            'pIdSolicitudCobertura' => $datos['IdSolicitudCobertura'],
        ];

        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            $this->setError(400, 'Error al buscar por solicitud. ');
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
    protected function buscarxSolicitudCoberturaNull(array $datos, &$resultado, ?int &$numfilas): bool {

        $spnombre = 'sel_SolicitudesCoberturaPersona_Null';
        $sparam = [
            'pIdSolicitudCobertura' => $datos['IdSolicitudCobertura'],
        ];

        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            $this->setError(400, 'Error al buscar por solicitud. ');
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

        $spnombre = 'sel_SolicitudesCoberturaPersona_es_xId';
        $sparam = [
            'pBasePersonas' => BASEDATOS_PERSONAS,
            'pId'=> $datos['Id'],
        ];

        if (!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno)) {
            $this->setError(400,"Error al buscar al buscar por solicitud. ");
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
    protected function buscarParaElasticXSolicitud(array $datos, &$resultado, ?int &$numfilas): bool {

        $spnombre = 'sel_SolicitudesCoberturaPersona_es_xIdSolicitudCobertura';
        $sparam = [
            'pBasePersonas' => BASEDATOS_PERSONAS,
            'pIdSolicitudCobertura' => $datos['IdSolicitudCobertura'],
        ];

        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            $this->setError(400, 'Error al buscar al buscar por solicitud. ');
            return false;
        }
        return true;
    }

    protected function Insertar(array $datos, ?int &$codigoInsertado): bool {

        $spnombre = 'ins_SolicitudesCoberturaPersona';
        $sparam = [
            'pIdNovedad' => $datos['IdNovedad'],
            'pIdSolicitudCobertura' => $datos['IdSolicitudCobertura'],
            'pInstrumentoLegal' => $datos['InstrumentoLegal'],
            'pCantidadHorasModulos' => $datos['CantidadHorasModulos'],
            'pIdPersonaPropuesta' => $datos['IdPersonaPropuesta'],
            'pIdPersonaDesignada' => $datos['IdPersonaDesignada'],
            'pAltaUsuario'=> $datos['AltaUsuario'],
            'pAltaFecha'=> $datos['AltaFecha'],
            'pUltimaModificacionFecha'=> $datos['UltimaModificacionFecha'],
            'pUltimaModificacionUsuario'=> $datos['UltimaModificacionUsuario'],
        ];

        if (!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno)) {
            $this->setError(400,"Error al insertar. ");
            return false;
        }

        $codigoInsertado = $this->conexion->UltimoCodigoInsertado();
        return true;
    }


    protected function Modificar(array $datos): bool {

        $spnombre = 'upd_SolicitudesCoberturaPersona_xId';
        $sparam = [
            'pIdNovedad' => $datos['IdNovedad'],
            'pInstrumentoLegal' => $datos['InstrumentoLegal'],
            'pCantidadHorasModulos' => $datos['CantidadHorasModulos'],
            'pIdPersonaPropuesta' => $datos['IdPersonaPropuesta'],
            'pIdPersonaDesignada' => $datos['IdPersonaDesignada'],
            'pUltimaModificacionFecha'=> $datos['UltimaModificacionFecha'],
            'pUltimaModificacionUsuario'=> $datos['UltimaModificacionUsuario'],
            'pId' => $datos['Id']
        ];

        if (!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno)) {
            $this->setError(400,"Error al actualizar. ");
            return false;
        }

        return true;
    }

    protected function ModificarxSolicitudCobertura(array $datos): bool {

        $spnombre = 'upd_SolicitudesCoberturaPersona_xIdSolicitudCobertura';
        $sparam = [
            'pIdNovedad' => $datos['IdNovedad'],
            'pInstrumentoLegal' => $datos['InstrumentoLegal'],
            'pCantidadHorasModulos' => $datos['CantidadHorasModulos'],
            'pIdPersonaDesignada' => $datos['IdPersonaDesignada'],
            'pUltimaModificacionFecha'=> $datos['UltimaModificacionFecha'],
            'pUltimaModificacionUsuario'=> $datos['UltimaModificacionUsuario'],
            'pIdSolicitudCobertura' => $datos['IdSolicitudCobertura']
        ];

        if (!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno)) {
            $this->setError(400,"Error al actualizar. ");
            return false;
        }

        return true;
    }

    protected function Eliminar(array $datos): bool {

        $spnombre = 'del_SolicitudesCoberturaPersona_xId';
        $sparam = [
            'pId'=> $datos['Id']
        ];

        if (!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno)) {
            $this->setError(400,"Error al eliminar por codigo. ");
            return false;
        }
        return true;
    }

    protected function EliminarVarios(array $datos): bool {

        $spnombre = 'del_SolicitudesCoberturaPersona_xIds';
        $sparam = [
            'pIds'=> $datos['Ids']
        ];

        if (!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno)) {
            $this->setError(400,"Error al eliminar por codigo. ");
            return false;
        }
        return true;
    }

    protected function actualizarNovedadRelacionada(array $datos): bool
    {
        $spnombre="upd_SolicitudesCoberturaPersona_IdNovedad_xId";
        $sparam=array(
            'pIdNovedad'=> $datos['IdNovedad'],
            'pId'=> $datos['Id']
        );
        if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno))
        {
            $this->setError(400,"Error al modificar la novedad. ");
            return false;
        }
        return true;
    }

}