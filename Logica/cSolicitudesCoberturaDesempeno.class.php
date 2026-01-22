<?php
include(DIR_CLASES_DB . "cSolicitudesCoberturaDesempeno.db.php");

class cSolicitudesCoberturaDesempeno extends cSolicitudesCoberturaDesempenodb {
    /**
     * Constructor de la clase cSolicitudesCoberturaDesempeno.
     *
     * Recibe un objeto accesoBDLocal y el formato a de  los mensajes de salida
     * $formato = FMT_TEXTO escribe en pantalla una caja con el mensaje de error, el tipo de caja depende del nivel de error
     *            FMT_ARRAY escribe el mensaje de error en la propiedad $error de la clase la cual puede ser accedida desde el método getError()
     *            otros escribe en pantalla el mensaje en texto plano
     *
     * @param accesoBDLocal $conexion
     * @param mixed         $formato
     */
    function __construct(accesoBDLocal $conexion, $formato = FMT_TEXTO) {
        parent::__construct($conexion, $formato);
    }

    /**
     * Destructor de la clase cSolicitudesCoberturaDesempeno.
     */
    function __destruct() {
        parent::__destruct();
    }


    public function BuscarxCodigo($datos, &$resultado, &$numfilas): bool {
        if (!parent::BuscarxCodigo($datos, $resultado, $numfilas))
            return false;
        return true;
    }

    public function buscarxSolicitud($datos, &$resultado, &$numfilas): bool {

        $sparam = [
            'IdSolicitudCobertura' => $datos['IdSolicitudCobertura'],
            'IdEstado' => -1,
            'xIdEstado' => 0,
        ];

        if (isset($datos['IdEstado']) && !empty($datos['IdEstado'])) {
            $sparam['IdEstado'] = $datos['IdEstado'];
            $sparam['xIdEstado'] = 1;
        }

        return parent::buscarxSolicitud($sparam, $resultado, $numfilas);
    }

    public function buscarxSolicitudxPersona($datos, &$resultado, &$numfilas): bool {
        return parent::buscarxSolicitudxPersona($datos, $resultado, $numfilas);
    }

    public function buscarxPuesto($datos, &$resultado, &$numfilas): bool {
        return parent::buscarxPuesto($datos, $resultado, $numfilas);
    }

    public function buscarDesignadoxSolicitud($datos, &$resultado, &$numfilas): bool {
        return parent::buscarDesignadoxSolicitud($datos, $resultado, $numfilas);
    }

    public function buscarxNovedad($datos, &$resultado, &$numfilas): bool {
        return parent::buscarxNovedad($datos, $resultado, $numfilas);
    }

    public function BusquedaAvanzada($datos, &$resultado, &$numfilas): bool {
        $sparam = [
            'xId' => 0,
            'Id' => "",
            'xIdSolicitudCobertura' => 0,
            'IdSolicitudCobertura' => "",
            'xIdPuesto' => 0,
            'IdPuesto' => "",
            'xIdDesempeno' => 0,
            'IdDesempeno' => "",
            'limit' => '',
            'orderby' => "Id DESC",
        ];
        if (isset($datos['Id']) && $datos['Id'] != "") {
            $sparam['Id'] = $datos['Id'];
            $sparam['xId'] = 1;
        }
        if (isset($datos['IdSolicitudCobertura']) && $datos['IdSolicitudCobertura'] != "") {
            $sparam['IdSolicitudCobertura'] = $datos['IdSolicitudCobertura'];
            $sparam['xIdSolicitudCobertura'] = 1;
        }
        if (isset($datos['IdPuesto']) && $datos['IdPuesto'] != "") {
            $sparam['IdPuesto'] = $datos['IdPuesto'];
            $sparam['xIdPuesto'] = 1;
        }
        if (isset($datos['IdDesempeno']) && $datos['IdDesempeno'] != "") {
            $sparam['IdDesempeno'] = $datos['IdDesempeno'];
            $sparam['xIdDesempeno'] = 1;
        }

        if (isset($datos['orderby']) && $datos['orderby'] != "")
            $sparam['orderby'] = $datos['orderby'];
        if (isset($datos['limit']) && $datos['limit'] != "")
            $sparam['limit'] = $datos['limit'];
        if (!parent::BusquedaAvanzada($sparam, $resultado, $numfilas))
            return false;
        return true;
    }


    public function BuscarAuditoriaRapida($datos, &$resultado, &$numfilas): bool {
        if (!parent::BuscarAuditoriaRapida($datos, $resultado, $numfilas))
            return false;
        return true;
    }


    public function SolicitudesCoberturaSP(&$spnombre, &$sparam): void {
        parent::SolicitudesCoberturaSP($spnombre, $sparam);
    }


    public function SolicitudesCoberturaSPResult(&$resultado, &$numfilas): bool {
        $this->SolicitudesCoberturaSP($spnombre, $sparam);

        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            $this->setError(400, "Error al buscar por codigo. ");
            return false;
        }
        return true;
    }


    public function EscuelasPuestosSP(&$spnombre, &$sparam): void {
        parent::EscuelasPuestosSP($spnombre, $sparam);
    }


    public function EscuelasPuestosSPResult(&$resultado, &$numfilas): bool {
        $this->EscuelasPuestosSP($spnombre, $sparam);

        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            $this->setError(400, "Error al buscar por codigo. ");
            return false;
        }
        return true;
    }


    public function EscuelasPuestosDesempenoSP(&$spnombre, &$sparam): void {
        parent::EscuelasPuestosDesempenoSP($spnombre, $sparam);
    }


    public function EscuelasPuestosDesempenoSPResult(&$resultado, &$numfilas): bool {
        $this->EscuelasPuestosDesempenoSP($spnombre, $sparam);

        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            $this->setError(400, "Error al buscar por codigo. ");
            return false;
        }
        return true;
    }

    public function Insertar($datos, &$codigoInsertado): bool {
        if (!$this->_ValidarInsertar($datos))
            return false;

        self::_SetearNull($datos);
        self::_SetearFechas($datos);
        if (!parent::Insertar($datos, $codigoInsertado))
            return false;

        $oAuditoriasSolicitudesCoberturaDesempeno = new cAuditoriasSolicitudesCoberturaDesempeno($this->conexion, $this->formato);
        $datos['Id'] = $codigoInsertado;
        $datos['Accion'] = INSERTAR;
        $datos['Estado'] = ACTIVO;
        if (!$oAuditoriasSolicitudesCoberturaDesempeno->InsertarLog($datos, $codigoInsertadolog)) {
            $this->setError(400, utf8_encode('Error interno en auditorias - c�d: iscd. <br>Comun�quese con el �rea de Sistemas.'));
            return false;
        }

        return true;
    }

    public function insertarValores(array $datos, ?int &$codigoInsertado): bool {
        if (!$this->_ValidarInsertar($datos))
            return false;
        $this->_SetearNull($datos);
        $datos['AltaUsuario'] = $datos['UltimaModificacionUsuario'] = $_SESSION['usuariocod'];
        $datos['AltaFecha'] = $datos['UltimaModificacionFecha'] = date('Y-m-d H:i:s');
        if (!parent::insertarValores($datos, $codigoInsertado))
            return false;

        $oAuditoriasSolicitudesCoberturaDesempeno = new cAuditoriasSolicitudesCoberturaDesempeno($this->conexion, $this->formato);
        $datos['Id'] = $codigoInsertado;
        $datos['Accion'] = INSERTAR;
        if (!$oAuditoriasSolicitudesCoberturaDesempeno->InsertarLog($datos, $codigoInsertadolog))
            return false;

        return true;
    }


    public function Modificar($datos): bool {
        if (!$this->_ValidarModificar($datos, $datosRegistro))
            return false;
        $datos['Tildado'] = $datos['Tildado'] ?? $datosRegistro['Tildado'];
        $this->_SetearNull($datos);
        if (!parent::Modificar($datos))
            return false;
        $oAuditoriasSolicitudesCoberturaDesempeno = new cAuditoriasSolicitudesCoberturaDesempeno($this->conexion, $this->formato);
        $datosRegistro['Accion'] = MODIFICACION;
        if (!$oAuditoriasSolicitudesCoberturaDesempeno->InsertarLog($datosRegistro, $codigoInsertadolog))
            return false;

        return true;
    }

    public function modificarPersonaxDesempeno($datos): bool {

        $datos['TipoDesignacion'] = 'desempeno';
        if (!$this->_ValidarDesignar($datos))
            return false;

        self::_SetearFechas($datos);
        if (!parent::modificarPersonaxDesempeno($datos))
            return false;

        if ($datos['InstrumentoLegal'] != 'NULL') {
            # asigno mismo instrumento para la misma persona repetida en la sc
            if (!parent::modificarInstrumentoLegalxPersonaxSolicitud($datos))
                return false;
        }

        if (isset($datos['conConflicto']) && $datos['conConflicto'])
            $this->extraerConflicto($datos);

        if (!$this->modificarConflicto($datos))
            return false;

        return true;
    }

    public function modificarPersonaxPuesto($datos): bool {

        $datos['TipoDesignacion'] = 'puesto';
        if (!$this->_ValidarDesignar($datos))
            return false;

        self::_SetearFechas($datos);
        if (!parent::modificarPersonaxPuesto($datos))
            return false;

        if ($datos['InstrumentoLegal'] != 'NULL') {
            # asigno mismo instrumento para la misma persona repetida en la sc
            if (!parent::modificarInstrumentoLegalxPersonaxSolicitud($datos))
                return false;
        }


        if (isset($datos['conConflicto']) && $datos['conConflicto'])
            $this->extraerConflicto($datos);

        if (!$this->modificarConflictoxPuesto($datos))
            return false;

        return true;
    }

    public function modificarPersonaxSolicitud($datos): bool {

        $datos['TipoDesignacion'] = 'todos';
        if (!$this->_ValidarDesignar($datos))
            return false;

        self::_SetearFechas($datos);
        if (!parent::modificarPersonaxSolicitud($datos))
            return false;

        if ($datos['InstrumentoLegal'] != 'NULL') {
            # asigno mismo instrumento para la misma persona repetida en la sc
            if (!parent::modificarInstrumentoLegalxPersonaxSolicitud($datos))
                return false;
        }

        if (isset($datos['conConflicto']) && $datos['conConflicto'])
            $this->extraerConflicto($datos);

        if (!$this->modificarConflictoxSolicitud($datos))
            return false;

        return true;
    }

    /**
     * @inheritDoc
     */
    public function modificarConflicto(array $datos): bool {
        $datos['ExisteInconsistencia'] = empty($datos['TipoConflicto']) ? 0 : 1;
        $datos['JsonInconsistencia'] = empty($datos['TipoConflicto']) ? 'NULL' : json_encode([
            'IdUsuario' => $_SESSION['usuariocod'],
            'Fecha' => date('Y-m-d H:i:s'),
            'TipoConflicto' => $datos['TipoConflicto'],
            'Conflictos' => $datos['Conflictos'] ?? null,
        ]);
        return parent::modificarConflicto($datos);
    }

    /**
     * @inheritDoc
     */
    public function modificarConflictoxPuesto(array $datos): bool {
        $datos['ExisteInconsistencia'] = empty($datos['TipoConflicto']) ? 0 : 1;
        $datos['JsonInconsistencia'] = empty($datos['TipoConflicto']) ? 'NULL' : json_encode([
            'IdUsuario' => $_SESSION['usuariocod'],
            'Fecha' => date('Y-m-d H:i:s'),
            'TipoConflicto' => $datos['TipoConflicto'],
            'Conflictos' => $datos['Conflictos'] ?? null,
        ]);
        return parent::modificarConflictoxPuesto($datos);
    }

    /**
     * @inheritDoc
     */
    public function modificarConflictoxSolicitud(array $datos): bool {
        $datos['ExisteInconsistencia'] = empty($datos['TipoConflicto']) ? 0 : 1;
        $datos['JsonInconsistencia'] = empty($datos['TipoConflicto']) ? 'NULL' : json_encode([
            'IdUsuario' => $_SESSION['usuariocod'],
            'Fecha' => date('Y-m-d H:i:s'),
            'TipoConflicto' => $datos['TipoConflicto'],
            'Conflictos' => $datos['Conflictos'] ?? null,
        ]);
        return parent::modificarConflictoxSolicitud($datos);
    }

    /**
     * @param array $datos
     *
     * @return bool
     */
    public function modificarPuesto(array $datos): bool {
        self::_SetearFechas($datos);
        return parent::modificarPuesto($datos);
    }

    /**
     * @param array $datos
     *
     * @return bool
     */
    public function modificarInstrumentoLegalxPersonaxSolicitud(array $datos): bool {
        self::_SetearFechas($datos);
        return parent::modificarInstrumentoLegalxPersonaxSolicitud($datos);
    }

    /**
     * @param array $datos
     *
     * @return bool
     */
    public function modificarNovedadxPersonaxSolicitud(array $datos): bool {
        self::_SetearFechas($datos);
        return parent::modificarNovedadxPersonaxSolicitud($datos);
    }

    /**
     * @param array $datos
     *
     * @return bool
     */
    public function modificarEstadoPersonaxSolicitud(array $datos): bool {
        self::_SetearFechas($datos);
        return parent::modificarEstadoPersonaxSolicitud($datos);
    }

    /**
     * @param array $datos
     *
     * @return bool
     */
    public function modificarEstadoPersonaxNovedad(array $datos): bool {
        self::_SetearFechas($datos);
        return parent::modificarEstadoPersonaxNovedad($datos);
    }

    /**
     * @param array $datos
     *
     * @return bool
     */
    public function modificarEstadoxSolicitudxEstado(array $datos): bool {
        self::_SetearFechas($datos);
        return parent::modificarEstadoxSolicitudxEstado($datos);
    }

    /**
     * @param array $datos
     *
     * @return bool
     */
    public function modificarxNovedad(array $datos): bool {
        self::_SetearFechas($datos);
        return parent::modificarxNovedad($datos);
    }

    /**
     * @param array $datos
     *
     * @return bool
     */
    public function rectificarPersonaxPuesto(array $datos): bool {

        if (!$this->_validarRectificar($datos))
            return false;

        self::_SetearFechas($datos);
        return parent::rectificarPersonaxPuesto($datos);
    }

    /**
     * @param array $datos
     *
     * @return bool
     */
    public function rectificarPersonaxDesempeno(array $datos): bool {

        if (!$this->_validarRectificar($datos))
            return false;

        self::_SetearFechas($datos);
        return parent::rectificarPersonaxDesempeno($datos);
    }

    /**
     * @param array $datos
     *
     * @return bool
     */
    public function rectificarPersonaxSolicitud(array $datos): bool {

        if (!$this->_validarRectificar($datos))
            return false;

        self::_SetearFechas($datos);
        return parent::rectificarPersonaxSolicitud($datos);
    }

    /**
     * @param array $datos
     * @param       $html
     *
     * @return bool
     */
    public function armarDatosCompletos(array $datos, &$html): bool {

        if (!$this->buscarxSolicitud($datos, $resultado, $numfilas))
            return false;

        if ($numfilas < 1) {
            $this->setError(404, 'No se encontraron resultados');
            return false;
        }

        $totalHoras = $totalHorasDesignadas = 0;
        $persona = [];
        $totalACubrir = 0;
        $cubiertos = 0;
        while ($fila = $this->conexion->ObtenerSiguienteRegistro($resultado)) {

            # TOTAL DE HORAS DE LA SOLICITUD
            $totalHoras += $fila['CantidadHorasModulos'];
            # TOTAL DE DESEMPE�OS DISPONIBLES A CUBRIR
            $totalACubrir++;

            if (!FuncionesPHPLocal::isEmpty($fila['IdPersonaDesignada'])) {

                # TOTAL DE DESEMPE�OS CUBIERTOS
                $cubiertos++;

                if (!isset($persona[$fila['IdPersonaDesignada']]['Horas']))
                    $persona[$fila['IdPersonaDesignada']]['Horas'] = 0;

                # TOTAL DE HORAS DESIGNADAS POR PERSONA
                $persona[$fila['IdPersonaDesignada']]['Horas'] += $fila['CantidadHorasModulos'];

                # TOTAL DE HORAS DESIGNADAS
                $totalHorasDesignadas += $fila['CantidadHorasModulos'];
            }
        }

        $conexionES = new Elastic\Conexion();
        $oObjeto = new Elastic\Personas($conexionES);

        $html = '<table class="table table-bordered">
                    <tr>
                        <td style="text-align: left; width: 80%;"><b>Horas totales a designar</b></td>
                        <td style="width: 20%"><b>' . number_format($totalHoras / 100, 2, ',', '.') . ' HS</b></td>
                    </tr>
                </table>';

        $html .= '<table class="table table-bordered">
                    <tr>
                        <td style="text-align: left; width: 80%;"><b>Horas totales designadas</b></td>
                        <td style="width: 20%"><b>' . number_format($totalHorasDesignadas / 100, 2, ',', '.') . ' HS</b></td>
                    </tr>';
        foreach ($persona as $key => $p) {

            $datosBuscar['IdPersona'] = $key;
            if (!$oObjeto->buscarxCodigo($datosBuscar, $datosPersona)) {
                echo 'Elastic\Personas::buscarxCodigo';
                return false;
            }
            $datosPersona = FuncionesPHPLocal::DecodificarUtf8($datosPersona);
            $nombre = $datosPersona['NombreCompleto'] . ' - ' . $datosPersona['Documento']['Numero'];

            $html .=
                '<tr>
                            <td style="text-align: left; width: 80%;">' . $nombre . '</td>
                            <td style="width: 20%">' . number_format($p['Horas'] / 100, 2, ',', '.') . ' HS </td>
                        </tr>';
        }
        $html .= '</table>';

        if ($cubiertos < $totalACubrir)
            $html .= '<br><h3>Han quedado desempe&ntilde;os sin cubrir</h3><br>En caso de necesitar cubrirlos en un futuro, podr&aacute; reabrir la solicitud<br><br><h3>&iquest;Desea solicitar cobertura?</h3>';

        return true;
    }


    /*----------------*/


    public function modificarTildado($datos): bool {
        self::_SetearNull($datos);
        return parent::modificarTildado($datos);
    }

    public function ModificarxIdPuestos($datos): bool {

        if (!$this->_ValidarModificarxPuestos($datos, $datosRegistro))
            return false;

        if (!parent::ModificarxIdPuestos($datos))
            return false;

        /*$oAuditoriasSolicitudesCoberturaDesempeno = new cAuditoriasSolicitudesCoberturaDesempeno($this->conexion, $this->formato);
        $datosRegistro['Accion'] = MODIFICACION;
        if (!$oAuditoriasSolicitudesCoberturaDesempeno->InsertarLog($datosRegistro,$codigoInsertadolog))
            return false;*/

        return true;
    }

    public function ModificarxSolicitudCoberturaPuesto($datos): bool {

//        if (!$this->_ValidarModificar($datos,$datosRegistro))
//            return false;

        $this->_SetearNull($datos);
        if (!parent::ModificarxSolicitudCoberturaPuesto($datos))
            return false;

//        $oAuditoriasSolicitudesCoberturaDesempeno = new cAuditoriasSolicitudesCoberturaDesempeno($this->conexion,$this->formato);
//        $datosRegistro['Accion'] = MODIFICACION;
//        if (!$oAuditoriasSolicitudesCoberturaDesempeno->InsertarLog($datosRegistro,$codigoInsertadolog))
//            return false;

        return true;
    }

    public function ModificarTildadoxSolicitudCoberturaPuesto($datos): bool {


        $this->_SetearNull($datos);
        if (!parent::ModificarTildadoxSolicitudCoberturaPuesto($datos))
            return false;

        return true;
    }

    public function Eliminar($datos): bool {
        if (!$this->_ValidarEliminar($datos, $datosRegistro))
            return false;
        $oAuditoriasSolicitudesCoberturaDesempeno = new cAuditoriasSolicitudesCoberturaDesempeno($this->conexion, $this->formato);
        $datosLog = $datosRegistro;
        $datosLog['Accion'] = ELIMINAR;
        if (!$oAuditoriasSolicitudesCoberturaDesempeno->InsertarLog($datosLog, $codigoInsertadolog))
            return false;
        if (!parent::Eliminar($datos))
            return false;
        return true;
    }


    public function eliminarxSolicitud($datos): bool {

        if (!$this->_validarEliminarxSolicitud($datos, $datosRegistros))
            return false;

        if (empty($datosRegistros))
            return true;

        $oAuditoriasSolicitudesCoberturaDesempeno = new cAuditoriasSolicitudesCoberturaDesempeno($this->conexion, $this->formato);
        foreach ($datosRegistros as $datosRegistro) {
            $datosLog = $datosRegistro;
            $datosLog['Accion'] = ELIMINAR;
            if (!$oAuditoriasSolicitudesCoberturaDesempeno->InsertarLog($datosLog, $codigoInsertadolog))
                return false;
        }

        return parent::eliminarxSolicitud($datos);
    }




//-----------------------------------------------------------------------------------------
//FUNCIONES PRIVADAS
//-----------------------------------------------------------------------------------------
    /**
     * @param array $datos
     */
    private function extraerConflicto(array &$datos): void {
        $sConflictos = $this->getError('error_description');
        $oConflictos = json_decode($sConflictos);
        if (empty($oConflictos)) {
            $datos['TipoConflicto'] = self::CONFLICTO_REGLAS;
            $datos['Conflictos'] = $sConflictos;
        } elseif (!is_null($sConflictos)) {
            $datos['TipoConflicto'] = empty($oConflictos->Reglas) ? self::CONFLICTO_HORARIO : self::CONFLICTO_AMBOS;
            $datos['Conflictos'] = $oConflictos;
        }
    }


    private function obtenerTipoCantidad(array &$datos): bool {
        $oPuestos = new cSolicitudesCoberturaPuesto($this->conexion, FMT_ARRAY);
        $datosBuscar['Id'] = $datos['IdSolicitudCoberturaPuesto'];
        if (!$oPuestos->BuscarxCodigo($datosBuscar, $resultado, $numfilas)) {
            $this->setError($oPuestos->getError());
            return false;
        }

        $datosPuesto = $this->conexion->ObtenerSiguienteRegistro($resultado);
        $datos['TipoCantidad'] = (int)$datosPuesto['TipoCantidad'];
        $datos['RazonSegundos'] = 60 * (2 === $datos['TipoCantidad'] ? CANT_MODULOS_PUESTO : CANT_HORAS_PUESTO);

        return true;
    }

    private function _validarRectificar(array &$datos): bool {

        if ($datos['Rectificar'])
            $datos['IdEstado'] = RECTIFICADO;
        else
            $datos['IdEstado'] = DESIGNADO;

        return true;
    }

    private function _ValidarDesignar(array &$datos): bool {

        if (FuncionesPHPLocal::isEmpty($datos['InstrumentoLegal']))
            $datos['InstrumentoLegal'] = 'NULL';

        if (FuncionesPHPLocal::isEmpty($datos['FechaDesignacion']))
            $datos['FechaDesignacion'] = 'NULL';
        else
            $datos['FechaDesignacion'] = FuncionesPHPLocal::ConvertirFecha($datos['FechaDesignacion'], 'dd/mm/aaaa', 'aaaa-mm-dd');


        if (!isset($datos['IdExcepcionTipo']) || FuncionesPHPLocal::isEmpty($datos['IdExcepcionTipo']))
            $datos['IdExcepcionTipo'] = 1;


        if (!$datos['Desasignar']) {

            if (FuncionesPHPLocal::isEmpty($datos['IdPersonaDesignada'])) {
                $this->setError(400, 'Falta designar un agente');
                return false;
            }

            $oSolicitudCobertura = new cSolicitudesCobertura($this->conexion, FMT_ARRAY, new Elastic\Conexion());
            if (!$oSolicitudCobertura->validarPreDesignacion($datos)) {
                $this->setError($oSolicitudCobertura->getError());
                return false;
            } elseif (!is_null($oSolicitudCobertura->getError('error')))
                $this->setError($oSolicitudCobertura->getError());

            $datos['IdEstado'] = DESIGNADO;
        } else {
            $datos['IdEstado'] = NUEVO;
        }

        return true;
    }

    private function _ValidarInsertar($datos) {
        if (!$this->_ValidarDatosVacios($datos))
            return false;
        return true;
    }


    private function _ValidarModificar($datos, &$datosRegistro) {
        if (!$this->BuscarxCodigo($datos, $resultado, $numfilas))
            return false;

        if ($numfilas != 1) {
            $this->setError(400, "Error debe ingresar un codigo valido.");
            return false;
        }
        $datosRegistro = $this->conexion->ObtenerSiguienteRegistro($resultado);
        if (!$this->_ValidarDatosVacios($datos))
            return false;
        return true;
    }

    private function _ValidarModificarxPuestos($datos, &$datosRegistro) {
        if (FuncionesPHPLocal::isEmpty($datos['IdSolicitudCoberturaPuesto'])) {
            $this->setError(400, 'Debe ingresar un puesto');
            return false;
        }

        if (FuncionesPHPLocal::isEmpty($datos['IdSCPuestos'])) {
            $this->setError(400, 'Debe ingresar un puesto');
            return false;
        }

        return true;
    }

    private function _ValidarEliminar($datos, &$datosRegistro) {
        if (!$this->BuscarxCodigo($datos, $resultado, $numfilas))
            return false;

        if ($numfilas != 1) {
            $this->setError(400, "Error debe ingresar un c�digo valido.");
            return false;
        }
        $datosRegistro = $this->conexion->ObtenerSiguienteRegistro($resultado);
        return true;
    }

    private function _validarEliminarxSolicitud(array $datos, ?array &$datosRegistros): bool {
        $datosRegistros = [];
        if (!$this->buscarxSolicitud($datos, $resultado, $numfilas))
            return false;

        if ($numfilas < 1)
            return true;

        while ($fila = $this->conexion->ObtenerSiguienteRegistro($resultado))
            $datosRegistros[] = $fila;


        return true;
    }

    private function _SetearFechas(&$datos): void {

        $datos['AltaUsuario'] = $_SESSION['usuariocod'];
        $datos['AltaFecha'] = date("Y-m-d H:i:s");
        $datos['UltimaModificacionFecha'] = date("Y-m-d H:i:s");
        $datos['UltimaModificacionUsuario'] = $_SESSION['usuariocod'];
    }

    private function _SetearNull(&$datos): void {

        if (FuncionesPHPLocal::isEmpty($datos['IdPersonaDesignada']))
            $datos['IdPersonaDesignada'] = "NULL";

        if (FuncionesPHPLocal::isEmpty($datos['IdNovedad']))
            $datos['IdNovedad'] = "NULL";

        if (FuncionesPHPLocal::isEmpty($datos['TipoCantidad']))
            $datos['TipoCantidad'] = 1;

        if (FuncionesPHPLocal::isEmpty($datos['CantidadHorasModulos']))
            $datos['CantidadHorasModulos'] = "NULL";

        if (FuncionesPHPLocal::isEmpty($datos['InstrumentoLegal']))
            $datos['InstrumentoLegal'] = "NULL";
    }


    private function _ValidarDatosVacios($datos) {

        if (FuncionesPHPLocal::isEmpty($datos['IdSolicitudCoberturaPuesto'])) {
            $this->setError(400, utf8_encode('Error interno - c�d: vdvscp. Comun�quese con el �rea de Sistemas.'));
            return false;
        }

        if (FuncionesPHPLocal::isEmpty($datos['IdSolicitudCobertura'])) {
            $this->setError(400, utf8_encode('Error interno - c�d: vdvsc. Comun�quese con el �rea de Sistemas.'));
            return false;
        }

        if (FuncionesPHPLocal::isEmpty($datos['IdPuesto'])) {
            $this->setError(400, utf8_encode('Error interno - c�d: vdvip. Comun�quese con el �rea de Sistemas.'));
            return false;
        }


        if (FuncionesPHPLocal::isEmpty($datos['Dia'])) {
            $this->setError(400, utf8_encode('El cargo no posee los datos completos del desempe�os'));
            return false;
        }

        if (FuncionesPHPLocal::isEmpty($datos['HoraInicio'])) {
            $this->setError(400, utf8_encode('El cargo no posee los datos completos del desempe�os'));
            return false;
        }

        if (FuncionesPHPLocal::isEmpty($datos['HoraFin'])) {
            $this->setError(400, utf8_encode('El cargo no posee los datos completos del desempe�os'));
            return false;
        }

        return true;
    }


    public static function getTipoConflicto(string $tipo): ?string {
        switch ($tipo) {
            case 'H':
            case 'h':
                return self::CONFLICTO_HORARIO;
            case 'R':
            case 'r':
                return self::CONFLICTO_REGLAS;
            case 'A':
            case 'a':
                return self::CONFLICTO_AMBOS;
            default:
                return null;
        }
    }

}
