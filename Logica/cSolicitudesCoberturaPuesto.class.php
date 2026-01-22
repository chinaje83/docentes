<?php

use Bigtree\ExcepcionLogica;

include(DIR_CLASES_DB . 'cSolicitudesCoberturaPuesto.db.php');

class cSolicitudesCoberturaPuesto extends cSolicitudesCoberturaPuestoDB {
    /**
     * @var Elastic\Conexion|null
     */
    private $conexionES;

    /**
     * Constructor de la clase cSolicitudesCoberturaPersona.
     *
     * Recibe un objeto accesoBDLocal y el formato a de  los mensajes de salida
     * $formato = FMT_TEXTO escribe en pantalla una caja con el mensaje de error, el tipo de caja depende del nivel de error
     *            FMT_ARRAY escribe el mensaje de error en la propiedad $error de la clase la cual puede ser accedida desde el mÃ©todo getError()
     *            otros escribe en pantalla el mensaje en texto plano
     *
     * @param accesoBDLocal $conexion
     * @param Elastic\Conexion|null $conexionES
     * @param mixed $formato
     */
    function __construct(accesoBDLocal $conexion, $formato = FMT_TEXTO, ?Elastic\Conexion $conexionES = NULL) {
        parent::__construct($conexion, $formato);
        $this->conexionES = $conexionES;
    }

    /**
     * Destructor de la clase cSolicitudesCobertura.
     */
    function __destruct() {
        parent::__destruct();
    }

    public function BuscarxCodigo($datos, &$resultado, &$numfilas): bool {
        return parent::BuscarxCodigo($datos, $resultado, $numfilas);
    }

    public function BuscarxPuesto($datos, &$resultado, &$numfilas): bool {
        return parent::BuscarxPuesto($datos, $resultado, $numfilas);
    }

    public function BuscarxSolicitudCoberturaPersona($datos, &$resultado, &$numfilas): bool {
        return parent::BuscarxSolicitudCoberturaPersona($datos, $resultado, $numfilas);
    }

    public function buscarxSolicitudCobertura($datos, &$resultado, &$numfilas): bool {
        return parent::buscarxSolicitudCobertura($datos, $resultado, $numfilas);
    }

    public function buscarPuestosSobrantes($datos, &$resultado, &$numfilas): bool {
        return parent::buscarPuestosSobrantes($datos, $resultado, $numfilas);
    }

    public function buscarxSolicitudCoberturaPersonaxPuesto($datos, &$resultado, &$numfilas): bool {
        return parent::buscarxSolicitudCoberturaPersonaxPuesto($datos, $resultado, $numfilas);
    }


    public function buscarEscuelasPuestosxSolicitud($datos, &$resultado, &$numfilas): bool {
        return parent::buscarEscuelasPuestosxSolicitud($datos, $resultado, $numfilas);
    }

    /**
     * @inheritDoc
     */
    public function buscarParaElastic($datos, &$resultado, &$numfilas): bool {
        return parent::buscarParaElastic($datos, $resultado, $numfilas);
    }

    /**
     * @inheritDoc
     */
    public function buscarParaElasticXSubSolicitud($datos, &$resultado, &$numfilas): bool {
        return parent::buscarParaElasticXSubSolicitud($datos, $resultado, $numfilas);
    }

    /**
     * @param array $datos
     * @return \Generator
     * @throws \Bigtree\ExcepcionLogica
     */
    public function buscarPuestos(array $datos): Generator {
        if (!parent::buscarParaElasticXSubSolicitud($datos, $resultado, $numfilas))
            throw new ExcepcionLogica($this->getError('error_description'));

        $oObjeto = new cSolicitudesCoberturaDesempeno($this->conexion, FMT_ARRAY);


        while ($fila = $this->conexion->ObtenerSiguienteRegistro($resultado)) {

            if(!$oObjeto->buscarxPuesto(['IdSolicitudCoberturaPuesto' => $fila['Id']], $resultado_, $numfilas))
                throw new ExcepcionLogica($oObjeto->getError('error_description'));

            $fila['Desempenos'] = [];

            while ($filaDesempeno = $this->conexion->ObtenerSiguienteRegistro($resultado_)) {
                $fila['Desempenos'][] = [
                    'Id' => $filaDesempeno['Id'],
                    'TipoCantidad' => $filaDesempeno['TipoCantidad'],
                    'CantidadHorasModulos' => $filaDesempeno['CantidadHorasModulos'],
                    'Dia' => $filaDesempeno['Dia'],
                    'Hora' => (object) [
                        'gte' => $filaDesempeno['HoraInicio'],
                        'lte' => $filaDesempeno['HoraFin'],
                    ]
                ];
            }
            yield $fila;
        }
    }

    /**
     * @inheritDoc
     */
    public function buscarPuestosDivididos(array $datos, &$resultado, ?int &$numfilas): bool {
        return parent::buscarPuestosDivididos($datos, $resultado, $numfilas);
    }

    /**
     * @inheritDoc
     */
    public function buscarDatosPuestoxCodigo(array $datos, &$resultado, ?int &$numfilas): bool {
        return parent::buscarDatosPuestoxCodigo($datos, $resultado, $numfilas);
    }

    public function Insertar($datos, &$codigoInsertado): bool {

        if (!self::_ValidarInsertar($datos))
            return false;

        self::_SetearNull($datos);
        self::_SetearFechas($datos);

        if (!parent::Insertar($datos, $codigoInsertado))
            return false;

        $oAuditoriasSolicitudesCoberturaPuesto = new cAuditoriasSolicitudesCoberturaPuesto($this->conexion, $this->formato);
        $datos['Id'] = $codigoInsertado;
        $datos['Accion'] = INSERTAR;
        $datos['Estado'] = ACTIVO;

        if (!$oAuditoriasSolicitudesCoberturaPuesto->InsertarLog($datos, $codigoInsertadolog)) {
            $this->setError(400, utf8_encode('Error interno en auditorias - Código: iscpu. Comuníquese con el área de Sistemas.'));
            return false;
        }

        return true;
    }



    public function Modificar($datos): bool {

        if (!$this->_ValidarModificar($datos, $datosRegistro))
            return false;

        $datos['Tildado'] = $datos['Tildado'] ?? $datosRegistro['Tildado'];
        self::_SetearNull($datos);
        self::_SetearFechas($datos);

        if (!parent::Modificar($datos))
            return false;

        $oAuditoriasSolicitudesCoberturaPuesto = new cAuditoriasSolicitudesCoberturaPuesto($this->conexion, $this->formato);
        $datosRegistro['Accion'] = MODIFICACION;

        if (!$oAuditoriasSolicitudesCoberturaPuesto->InsertarLog($datosRegistro, $codigoInsertadolog))
            return false;

        return true;
    }


    /**
     * @param array $datos
     * @return bool
     */
    public function modificarDesglosePorPuesto(array $datos): bool {
        self::_SetearFechas($datos);
        return parent::modificarDesglosePorPuesto($datos);
    }

    /**
     * @param array $datos
     * @return bool
     */
    public function modificarDesglosePorSolicitud(array $datos): bool {
        self::_SetearFechas($datos);
        return parent::modificarDesglosePorSolicitud($datos);
    }

    /**
     * @inheritDoc
     */
    public function modificarTildado(array $datos): bool {

        self::_SetearFechas($datos);
        self::_SetearNull($datos);

        if (!parent::modificarTildado($datos))
            return false;

        $oSolicitudCoberturaDesempeno = new cSolicitudesCoberturaDesempeno($this->conexion, $this->formato);

        if (!$oSolicitudCoberturaDesempeno->ModificarTildadoxSolicitudCoberturaPuesto($datos)) {
            $this->setError($oSolicitudCoberturaDesempeno->getError());
            return false;
        }

        return true;
    }

    public function modificarPuesto(array $datos): bool {

        if (!$this->BuscarxCodigo($datos, $resultado, $numfilas))
            return false;

        if ($numfilas == 0) {
            $this->setError(400, 'Error, no existe el registro');
            return false;
        }
        $datosRegistro = $this->conexion->ObtenerSiguienteRegistro($resultado);

        if (!parent::modificarPuesto($datos))
            return false;


        $oAuditoriasSolicitudesCoberturaPuesto = new cAuditoriasSolicitudesCoberturaPuesto($this->conexion, $this->formato);
        $datosRegistro['Accion'] = MODIFICACION;

        if (!$oAuditoriasSolicitudesCoberturaPuesto->InsertarLog($datosRegistro, $codigoInsertadolog))
            return false;

        return true;
    }

    public function ModificarxPuestoxSolicitudCobertura($datos): bool {

        self::_SetearFechas($datos);
        return parent::ModificarxPuestoxSolicitudCobertura($datos);
    }

    public function ModificarxSolicitudCoberturaPersona($datos): bool {

        self::_SetearFechas($datos);
        return parent::ModificarxSolicitudCoberturaPersona($datos);
    }

    public function ModificarxSolicitudCobertura($datos): bool {

        self::_SetearFechas($datos);
        return parent::ModificarxSolicitudCobertura($datos);
    }

    public function ModificarxSolicitudCoberturaxPuestoxDesempeno($datos): bool {

        if (!$this->buscarxSolicitudCoberturaPersonaxPuesto($datos, $resultado, $numfilas))
            return false;

        # si existe el puesto-persona, actualizamos desempeno con idscpuesto
        if ($numfilas == 1) {

            $fila = $this->conexion->ObtenerSiguienteRegistro($resultado);

            $oSolicitudCoberturaDesempeno = new cSolicitudesCoberturaDesempeno($this->conexion, FMT_ARRAY);
            $datosModificar = [
                'IdSolicitudCoberturaPuesto' => $fila['Id'],
                'Tildado' => 1,
                'Id' => $datos['IdDesempeno']
            ];
            if (!$oSolicitudCoberturaDesempeno->Modificar($datosModificar)) {
                $this->setError($oSolicitudCoberturaDesempeno->getError());
                return false;
            }

            return true;
        }

        self::_SetearFechas($datos);
        return parent::ModificarxSolicitudCoberturaxPuestoxDesempeno($datos);
    }

    public function actualizarElastic(array $datos): bool {
        $oElastic = new Elastic\Modificacion(SUFFIX_SOLICITUDCOBERTURA, $this->conexionES ?? new Elastic\Conexion());
        if (!$this->_armarDatosElastic($datos, $datosRegistro, $datosElastic))
            return false;

        if (!$oElastic->Actualizar((array)$datosElastic, $datosElastic)) {
            $this->setError($oElastic->getError());
            return false;
        }
        return true;
    }

    public function eliminarxSolicitud($datos): bool {

        if (!$this->_validarEliminarxSolicitud($datos, $datosRegistros))
            return false;

        if (empty($datosRegistros))
            return true;

        $oAuditoriasSolicitudesCoberturaPuesto = new cAuditoriasSolicitudesCoberturaPuesto($this->conexion, $this->formato);
        foreach ($datosRegistros as $datosRegistro) {
            $datosLog = $datosRegistro;
            $datosLog['Accion'] = ELIMINAR;
            if (!$oAuditoriasSolicitudesCoberturaPuesto->InsertarLog($datosLog, $codigoInsertadolog))
                return false;
        }

        return parent::eliminarxSolicitud($datos);
    }


    public function Eliminar($datos): bool {

        if (!$this->_ValidarEliminar($datos, $datosRegistro))
            return false;

        $oAuditoriasSolicitudesCoberturaPuesto = new cAuditoriasSolicitudesCoberturaPuesto($this->conexion, $this->formato);
        $datosLog = $datosRegistro;
        $datosLog['Accion'] = ELIMINAR;
        if (!$oAuditoriasSolicitudesCoberturaPuesto->InsertarLog($datosLog, $codigoInsertadolog))
            return false;

        return parent::Eliminar($datos);
    }

    public function EliminarVarios($datos): bool {

        return parent::EliminarVarios($datos);
    }

    private function _ValidarInsertar($datos): bool {

        return self::_ValidarDatosVacios($datos);
    }

    private function _ValidarModificar($datos, &$datosRegistro) {

        if (!parent::buscarxCodigo($datos, $resultado, $numfilas))
            return false;

        if ($numfilas != 1) {
            $this->setError(400, "Error debe ingresar un código valido.");
            return false;
        }

        $datosRegistro = $this->conexion->ObtenerSiguienteRegistro($resultado);
        //var_dump('modificar', $datos);die;
        if (!self::_ValidarDatosVacios($datos))
            return false;

        return true;
    }

    private function _ValidarEliminar($datos, &$datosRegistro) {

        if (!$this->buscarParaElastic($datos, $resultado, $numfilas))
            return false;

        if ($numfilas != 1) {
            $this->setError(400, "Error debe ingresar un código valido.");
            return false;
        }
        $datosRegistro = $this->conexion->ObtenerSiguienteRegistro($resultado);
        return true;
    }

    private function _validarEliminarxSolicitud(array $datos, ?array &$datosRegistros): bool {
        $datosRegistros = [];
        if (!$this->buscarxSolicitudCobertura($datos, $resultado, $numfilas))
            return false;

        if ($numfilas < 1)
            return true;

        while ($fila = $this->conexion->ObtenerSiguienteRegistro($resultado))
            $datosRegistros[] = $fila;


        return true;
    }

    private function _SetearNull(&$datos): void {

        if (FuncionesPHPLocal::isEmpty($datos['TipoCantidad']))
            $datos['TipoCantidad'] = 1;

        if (FuncionesPHPLocal::isEmpty($datos['PuedeDesglosar']))
            $datos['PuedeDesglosar'] = 1;

        if (FuncionesPHPLocal::isEmpty($datos['CantidadHorasModulos']))
            $datos['CantidadHorasModulos'] = 0;
    }

    private function _SetearFechas(&$datos): void {

        $datos['AltaUsuario'] = $_SESSION['usuariocod'];
        $datos['AltaFecha'] = date("Y-m-d H:i:s");
        $datos['UltimaModificacionFecha'] = date("Y-m-d H:i:s");
        $datos['UltimaModificacionUsuario'] = $_SESSION['usuariocod'];
    }

    private function _ValidarDatosVacios($datos): bool {

        if (FuncionesPHPLocal::isEmpty($datos['IdSolicitudCobertura'])) {
            $this->setError(400, 'Debe ingresar un puesto');
            return false;
        }

        if (FuncionesPHPLocal::isEmpty($datos['IdPuesto'])) {
            $this->setError(400, 'Debe ingresar un puesto');
            return false;
        }

        return true;
    }

    private function _armarDatosElastic(array $datos, ?array &$datosRegistro, ?stdClass &$datosElastic): bool {

        $oObjeto = new cSolicitudesCoberturaDesempeno($this->conexion, FMT_ARRAY);
        if (empty($datosRegistro)) {
            if (!$this->buscarParaElastic($datos, $resultado, $numfilas))
                return false;

            if ($numfilas != 1) {
                $this->setError(400, "Debe ingresar código válido-");
                return false;
            }
            $datosRegistro = $this->conexion->ObtenerSiguienteRegistro($resultado);
        }

        if (empty($datosRegistro['Desempenos'])) {

            $datosBuscar['IdSolicitudCoberturaPuesto'] = $datosRegistro['Id'];
            if (!$oObjeto->buscarxPuesto($datosBuscar, $resultado, $numfilas)) {
                $this->setError($oObjeto->getError());
                return false;
            }
            $datosRegistro['Desempenos'] = [];

            while ($fila = $this->conexion->ObtenerSiguienteRegistro($resultado)) {
                $datosRegistro['Desempenos'][] = [
                    'Id' => $fila['Id'],
                    'TipoCantidad' => $fila['TipoCantidad'],
                    'CantidadHorasModulos' => $fila['CantidadHorasModulos'],
                    'Dia' => $fila['Dia'],
                    'Hora' => (object) [
                        'gte' => $fila['HoraInicio'],
                        'lte' => $fila['HoraFin'],
                    ]
                ];
                $datosRegistro['CantidadHorasModulos'] += $fila['CantidadHorasModulos'];
            }
        }
        $datosRegistro['Tipo'] = 'Puesto';
        //var_dump($datosRegistro); echo PHP_EOL;
        $datosElastic = Elastic\SolicitudCobertura::armarDatosElastic($datosRegistro);
        //var_dump($datosElastic);die;
        return true;
    }

}
