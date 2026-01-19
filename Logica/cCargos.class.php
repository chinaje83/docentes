<?php

include(DIR_CLASES_DB . "cCargos.db.php");

class cCargos extends cCargosdb {
    /**
     * Constructor de la clase cCargos.
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
     * Destructor de la clase cCargos.
     */
    function __destruct() {
        parent::__destruct();
    }

    /**
     * Devuelve el mensaje de error almacenado
     *
     * @return array
     */
    public function getError(): array {
        return $this->error;
    }

    public function BuscarxCodigo($datos, &$resultado, &$numfilas): bool {
        if (!parent::BuscarxCodigo($datos, $resultado, $numfilas)) {
            return false;
        }
        return true;
    }


    public function BusquedaAvanzada($datos, &$resultado, &$numfilas): bool {
        $sparam = [
            'xIdCargo' => 0,
            'IdCargo' => "",
            'xIdTipoCargo' => 0,
            'IdTipoCargo' => "",
            'xCodigo' => 0,
            'Codigo' => "",
            'xDescripcion' => 0,
            'Descripcion' => "",
            'xJerarquico' => 0,
            'Jerarquico' => "",
            'xEsdeno' => 0,
            'Esdeno' => "",
            'xEquivalenciaHs' => 0,
            'EquivalenciaHs' => "",
            'xIdRegimenSalarial' => 0,
            'IdRegimenSalarial' => "",
            'xIdEscalafon' => 0,
            'IdEscalafon' => "",
            'xDesempenoLugar' => 0,
            'DesempenoLugar' => "",
            'xEstado' => 0,
            'Estado' => "-1",
            'limit' => '',
            'orderby' => "Descripcion ASC",
        ];
        if (isset($datos['IdEscalafon']) && $datos['IdEscalafon'] != "") {
            $sparam['IdEscalafon'] = $datos['IdEscalafon'];
            $sparam['xIdEscalafon'] = 1;
        }
        if (isset($datos['DesempenoLugar']) && $datos['DesempenoLugar'] != "") {
            $sparam['DesempenoLugar'] = $datos['DesempenoLugar'];
            $sparam['xDesempenoLugar'] = 1;
        }
        if (isset($datos['IdCargo']) && $datos['IdCargo'] != "") {
            $sparam['IdCargo'] = $datos['IdCargo'];
            $sparam['xIdCargo'] = 1;
        }
        if (isset($datos['IdTipoCargo']) && $datos['IdTipoCargo'] != "") {
            $sparam['IdTipoCargo'] = $datos['IdTipoCargo'];
            $sparam['xIdTipoCargo'] = 1;
        }
        if (isset($datos['Codigo']) && $datos['Codigo'] != "") {
            $sparam['Codigo'] = $datos['Codigo'];
            $sparam['xCodigo'] = 1;
        }
        if (isset($datos['Descripcion']) && $datos['Descripcion'] != "") {
            $sparam['Descripcion'] = utf8_decode($datos['Descripcion']);
            $sparam['xDescripcion'] = 1;
        }
        if (isset($datos['Esdeno']) && $datos['Esdeno'] != "") {
            $sparam['Esdeno'] = $datos['Esdeno'];
            $sparam['xEsdeno'] = 1;
        }
        if (isset($datos['EquivalenciaHs']) && $datos['EquivalenciaHs'] != "") {
            $sparam['EquivalenciaHs'] = $datos['EquivalenciaHs'];
            $sparam['xEquivalenciaHs'] = 1;
        }
        if (isset($datos['Jerarquico']) && $datos['Jerarquico'] != "") {
            $sparam['Jerarquico'] = $datos['Jerarquico'];
            $sparam['xJerarquico'] = 1;
        }

        if (isset($datos['Estado']) && $datos['Estado'] != "") {
            $sparam['Estado'] = $datos['Estado'];
            $sparam['xEstado'] = 1;
        }
        if (isset($datos['IdRegimenSalarial']) && $datos['IdRegimenSalarial'] != "") {
            $sparam['IdRegimenSalarial'] = $datos['IdRegimenSalarial'];
            $sparam['xIdRegimenSalarial'] = 1;
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


    public function BuscarCombo(&$resultado, &$numfilas): bool {
        if (!parent::BuscarCombo($resultado, $numfilas))
            return false;
        return true;
    }


    public function BuscarxJerarquia($datos, &$resultado, &$numfilas): bool {
        if (!parent::BuscarxJerarquia($datos, $resultado, $numfilas))
            return false;
        return true;
    }


    public function CargosTiposSP(&$spnombre, &$sparam): void {
        parent::CargosTiposSP($spnombre, $sparam);
    }


    public function CargosTiposSPResult(&$resultado, &$numfilas): bool {
        $this->CargosTiposSP($spnombre, $sparam);

        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, "Error al buscar por codigo. ", ["archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__], ["formato" => $this->formato]);
            return false;
        }
        return true;
    }

    public function RegimenTipos(&$spnombre, &$sparam): void {
        parent::RegimenTipos($spnombre, $sparam);
    }


    public function RegimenTiposResult(&$resultado, &$numfilas): bool {
        $this->RegimenTipos($spnombre, $sparam);

        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, "Error al buscar los Regimenes. ", ["archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__], ["formato" => $this->formato]);
            return false;
        }
        return true;
    }

    /**
     * @return array
     * @throws \Bigtree\ExcepcionDB
     */
    public function cargosComputables(): array {
        return parent::cargosComputables();

    }


    public function Insertar($datos, &$codigoInsertado): bool {
        if (!$this->_ValidarInsertar($datos)) {
            return false;
        }
        $this->_SetearNull($datos);
        $datos['AltaFecha'] = date("Y-m-d H:i:s");
        $datos['AltaUsuario'] = $_SESSION['usuariocod'];
        $datos['UltimaModificacionUsuario'] = $_SESSION['usuariocod'];
        $datos['UltimaModificacionFecha'] = date("Y-m-d H:i:s");
        $datos['Estado'] = ACTIVO;
        if (!parent::Insertar($datos, $codigoInsertado))
            return false;
        $oAuditoriasCargos = new cAuditoriasCargos($this->conexion, $this->formato);
        $datos['IdCargo'] = $codigoInsertado;
        $datos['Accion'] = INSERTAR;
        if (!$oAuditoriasCargos->InsertarLog($datos, $codigoInsertadolog))
            return false;
        return true;
    }


    public function Modificar($datos): bool {
        if (!$this->_ValidarModificar($datos, $datosRegistro))
            return false;
        $datos['UltimaModificacionUsuario'] = $datosRegistro['UltimaModificacionUsuario'] = $_SESSION['usuariocod'];
        $datos['UltimaModificacionFecha'] = $datosRegistro['UltimaModificacionFecha'] = date("Y-m-d H:i:s");
        $this->_SetearNull($datos);
        if (!parent::Modificar($datos))
            return false;
        $oAuditoriasCargos = new cAuditoriasCargos($this->conexion, $this->formato);
        $datosRegistro['Accion'] = MODIFICACION;
        if (!$oAuditoriasCargos->InsertarLog($datosRegistro, $codigoInsertadolog))
            return false;

        $conexionES = new Elastic\Conexion();
        $oModificacion = new Elastic\Modificacion(SUFFIX_PUESTOS, $conexionES);

        $query = new stdClass();
        $query->bool = new stdClass();
        $query->bool->filter = new stdClass();
        $query->bool->filter->term = new stdClass();
        $query->bool->filter->term->{'Cargo.Id'} = new stdClass();
        $query->bool->filter->term->{'Cargo.Id'}->value = $datos['IdCargo'];

        $script = 'ctx._source.Cargo=params.Cargo';

        $params = [];
        $params['Cargo']['Tipo']['Id'] = (int)$datos['IdTipoCargo'];
        $params['Cargo']['Tipo']['Descripcion'] = utf8_encode($datos['NombreTipoCargo']);
        $params['Cargo']['Id'] = (int)$datos['IdCargo'];
        $params['Cargo']['Codigo'] = $datos['Codigo'];
        $params['Cargo']['Descripcion'] = utf8_encode($datos['Descripcion']);
        $params['Cargo']['AdmiteSuplente'] = (bool)$datos['AdmiteSuplente'];
        $params['Cargo']['Jerarquico'] = (bool)$datos['Jerarquico'];
        $params['Cargo']['IdTipo'] = (int)$datos['IdTipo'];
        $params['Cargo']['IdEscalafon'] = (int)$datos['IdEscalafon'];
        $params['Cargo']['DesempenoLugar'] = (int)$datos['DesempenoLugar'];

        $lang = 'painless';

        if (!$oModificacion->actualizarPorConsulta($query, $script, $resultado, $lang, $params)) {
            $this->setError($oModificacion->getError());
            return false;
        }

        return true;
    }

    public function ModificarJerarquia($datos): bool {
        $datos['UltimaModificacionUsuario'] = $datosRegistro['UltimaModificacionUsuario'] = $_SESSION['usuariocod'];
        $datos['UltimaModificacionFecha'] = $datosRegistro['UltimaModificacionFecha'] = date("Y-m-d H:i:s");
        $this->_SetearNull($datos);
        if (!parent::ModificarJerarquia($datos))
            return false;

        return true;
    }


    public function Eliminar($datos): bool {
        if (!$this->_ValidarEliminar($datos, $datosRegistro))
            return false;
        $oAuditoriasCargos = new cAuditoriasCargos($this->conexion, $this->formato);
        $datosLog = $datosRegistro;
        $datosLog['Accion'] = ELIMINAR;
        if (!$oAuditoriasCargos->InsertarLog($datosLog, $codigoInsertadolog))
            return false;
        $datosmodif['IdCargo'] = $datos['IdCargo'];
        $datosmodif['Estado'] = ELIMINADO;
        if (!$this->ModificarEstado($datosmodif))
            return false;
        return true;
    }


    public function ModificarEstado($datos): bool {
        if (!parent::ModificarEstado($datos)) {
            return false;
        }
        return true;
    }


    public function Activar(array $datos): bool {
        $datosmodif['IdCargo'] = $datos['IdCargo'];
        $datosmodif['Estado'] = ACTIVO;
        if (!$this->ModificarEstado($datosmodif)) {
            return false;
        }
        if (!$this->_ValidarEliminar($datos, $datosRegistro)) {
            return false;
        }
        $oAuditoriasCargos = new cAuditoriasCargos($this->conexion, $this->formato);
        $datosRegistro['Accion'] = MODIFICACION;
        if (!$oAuditoriasCargos->InsertarLog($datosRegistro, $codigoInsertadolog)) {
            return false;
        }
        return true;
    }


    public function DesActivar(array $datos): bool {
        $datosmodif['IdCargo'] = $datos['IdCargo'];
        $datosmodif['Estado'] = NOACTIVO;
        if (!$this->ModificarEstado($datosmodif)) {
            return false;
        }
        if (!$this->_ValidarEliminar($datos, $datosRegistro)) {
            return false;
        }
        $oAuditoriasCargos = new cAuditoriasCargos($this->conexion, $this->formato);
        $datosRegistro['Accion'] = MODIFICACION;
        if (!$oAuditoriasCargos->InsertarLog($datosRegistro, $codigoInsertadolog)) {
            return false;
        }
        return true;
    }

    public function ComboDesempenosLugar($datos, &$resultado, &$numfilas): bool
    {
        return parent::ComboDesempenosLugar($datos, $resultado, $numfilas);
    }



//-----------------------------------------------------------------------------------------
//FUNCIONES PRIVADAS
//-----------------------------------------------------------------------------------------

    private function _ValidarInsertar($datos) {
        if (!$this->_ValidarDatosVacios($datos))
            return false;
        return true;
    }


    private function _ValidarModificar($datos, &$datosRegistro) {
        if (!$this->BuscarxCodigo($datos, $resultado, $numfilas))
            return false;

        if ($numfilas != 1) {
            FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, "Error debe ingresar un código valido.", ["archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__], ["formato" => $this->formato]);
            return false;
        }
        $datosRegistro = $this->conexion->ObtenerSiguienteRegistro($resultado);
        if (!$this->_ValidarDatosVacios($datos)) {
            return false;
        }
        return true;
    }


    private function _ValidarEliminar($datos, &$datosRegistro) {
        if (!$this->BuscarxCodigo($datos, $resultado, $numfilas)) {
            return false;
        }

        if ($numfilas != 1) {
            FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, "Error debe ingresar un código valido.", ["archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__], ["formato" => $this->formato]);
            return false;
        }
        $datosRegistro = $this->conexion->ObtenerSiguienteRegistro($resultado);
        return true;
    }


    private function _SetearNull(&$datos): void {


        if (!isset($datos['IdTipoCargo']) || $datos['IdTipoCargo'] == "")
            $datos['IdTipoCargo'] = "NULL";

        if (!isset($datos['Codigo']) || $datos['Codigo'] == "")
            $datos['Codigo'] = "NULL";

        if (!isset($datos['Descripcion']) || $datos['Descripcion'] == "")
            $datos['Descripcion'] = "NULL";

        if (!isset($datos['Esdeno']) || $datos['Esdeno'] == "")
            $datos['Esdeno'] = "0";

        if (!isset($datos['SCParcial']) || $datos['SCParcial'] == "")
            $datos['SCParcial'] = "0";

        if (!isset($datos['EquivalenciaHs']) || $datos['EquivalenciaHs'] == "")
            $datos['EquivalenciaHs'] = "0";

        if (!isset($datos['UltimaModificacionFecha']) || $datos['UltimaModificacionFecha'] == "")
            $datos['UltimaModificacionFecha'] = "NULL";

        if (!isset($datos["IdJornada"]) || $datos["IdJornada"] == "")
            $datos['IdJornada'] = "NULL";

        if (FuncionesPHPLocal::isEmpty($datos['DesempenoLugar'])) {
            $datos['DesempenoLugar'] = 'NULL';
        }

        if (FuncionesPHPLocal::isEmpty($datos['IdEscalafon'])) {
            $datos['IdEscalafon'] = 'NULL';
        }

        if (!isset($datos['IdTipo']) || $datos['IdTipo'] == "")
            $datos['IdTipo'] = "NULL";
    }


    private function _ValidarDatosVacios($datos) {
        if (!isset($datos['PermiteSimultaneo']) || $datos['PermiteSimultaneo'] == "") {
            FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, "Debe indicar si permite o no simultaneidad de horarios ", ["archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__], ["formato" => $this->formato]);
            return false;
        }

        if (!isset($datos['IdTipoCargo']) || $datos['IdTipoCargo'] == "") {
            FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, "Debe ingresar un tipo", ["archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__], ["formato" => $this->formato]);
            return false;
        }

        if (isset($datos['IdTipoCargo']) && $datos['IdTipoCargo'] != "") {
            if (!FuncionesPHPLocal::ValidarContenido($this->conexion, $datos['IdTipoCargo'], "NumericoEntero")) {
                FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, "Error debe ingresar un campo numérico.", ["archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__], ["formato" => $this->formato]);
                return false;
            }
        }
        if (!isset($datos['IdExterno']) || $datos['IdExterno'] == "") {
            FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, "Debe ingresar IdExterno", ["archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__], ["formato" => $this->formato]);
            return false;
        }

        if (!isset($datos['Codigo']) || $datos['Codigo'] == "") {
            FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, "Debe ingresar un codigo", ["archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__], ["formato" => $this->formato]);
            return false;
        }

        if (!isset($datos['AdmiteSuplente']) || $datos['AdmiteSuplente'] == "") {
            FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, "Debe seleccionar si el cargo admite suplente", ["archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__], ["formato" => $this->formato]);
            return false;
        }

        if (isset($datos['AdmiteSuplente']) && $datos['AdmiteSuplente'] != "") {
            if (!FuncionesPHPLocal::ValidarContenido($this->conexion, $datos['AdmiteSuplente'], "NumericoEntero")) {
                FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, "Error debe ingresar un campo numérico.", ["archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__], ["formato" => $this->formato]);
                return false;
            }
        }

        if (!isset($datos['Descripcion']) || $datos['Descripcion'] == "") {
            FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, "Debe ingresar un descripcion", ["archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__], ["formato" => $this->formato]);
            return false;
        }

        if (!isset($datos['Esdeno']) || $datos['Esdeno'] == "") {
            FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, "Debe ingresar un esdeno", ["archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__], ["formato" => $this->formato]);
            return false;
        }
        if (isset($datos['Esdeno']) && $datos['Esdeno'] != "") {
            if (!FuncionesPHPLocal::ValidarContenido($this->conexion, $datos['Esdeno'], "NumericoEntero")) {
                FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, "Error debe ingresar un campo num?rico.", ["archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__], ["formato" => $this->formato]);
                return false;
            }
        }

        if (isset($datos['EquivalenciaHs']) && $datos['EquivalenciaHs'] != "") {
            if (!FuncionesPHPLocal::ValidarContenido($this->conexion, $datos['EquivalenciaHs'], "Numerico2Decimales")) {
                FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, "Error debe ingresar un campo num?rico.", ["archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__], ["formato" => $this->formato]);
                return false;
            }
        }

        if (!$this->conexion->TraerCampo('CargosTipos', 'IdTipoCargo', ['IdTipoCargo=' . $datos['IdTipoCargo']], $dato, $numfilas, $errno))
            return false;


        if (!isset($datos['Jerarquico']) || $datos['Jerarquico'] == "") {
            FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, utf8_decode("Debe seleccionar si el cargo es jerárquico"), ["archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__], ["formato" => $this->formato]);
            return false;
        }

        if (!isset($datos['IdRegimenSalarial']) || $datos['IdRegimenSalarial'] == "") {
            FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, utf8_decode("Debe seleccionar un regimen salarial"), ["archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__], ["formato" => $this->formato]);
            return false;
        }

        if ($numfilas != 1) {
            FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, "Error debe ingresar un campo valido.", ["archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__], ["formato" => $this->formato]);
            return false;
        }
        return true;
    }


}
