<?php
include(DIR_CLASES_DB . "cCircuitosEstados.db.php");

class cCircuitosEstados extends cCircuitosEstadosdb {

    protected $conexion;
    protected $formato;

    function __construct($conexion, $formato = FMT_TEXTO) {
        $this->conexion = &$conexion;
        $this->formato = &$formato;
        parent::__construct();
    }

    function __destruct() {
        parent::__destruct();
    }

    public function BuscarxCodigo($datos, &$resultado, &$numfilas) {
        if (!parent::BuscarxCodigo($datos, $resultado, $numfilas))
            return false;
        return true;
    }

    public function BuscarCombo(&$resultado, &$numfilas) {
        if (!parent::BuscarCombo($resultado, $numfilas))
            return false;
        return true;
    }

    public function BusquedaAvanzada($datos, &$resultado, &$numfilas) {
        $sparam = [
            'xEstado' => 0,
            'Estado' => "",
            'xIdEstadoPublico' => 0,
            'IdEstadoPublico' => "",
            'xIdEstadoPublicoSad' => 0,
            'IdEstadoPublicoSad' => "",
            'xIdEstadoPublicoConsejo' => 0,
            'IdEstadoPublicoConsejo' => "",
            'xNombre' => 0,
            'Nombre' => "",
            'xNombrePublico' => 0,
            'NombrePublico' => "",
            'limit' => '',
            'orderby' => "Nombre ASC",
        ];

        if (isset($datos['Estado']) && $datos['Estado'] != "") {
            $sparam['Estado'] = $datos['Estado'];
            $sparam['xEstado'] = 1;
        }

        if (isset($datos['IdEstadoPublico']) && $datos['IdEstadoPublico'] != "") {
            $sparam['IdEstadoPublico'] = $datos['IdEstadoPublico'];
            $sparam['xIdEstadoPublico'] = 1;
        }

        if (isset($datos['IdEstadoPublicoSad']) && $datos['IdEstadoPublicoSad'] != "") {
            $sparam['IdEstadoPublicoSad'] = $datos['IdEstadoPublicoSad'];
            $sparam['xIdEstadoPublicoSad'] = 1;
        }

        if (isset($datos['IdEstadoPublicoConsejo']) && $datos['IdEstadoPublicoConsejo'] != "") {
            $sparam['IdEstadoPublicoConsejo'] = $datos['IdEstadoPublicoConsejo'];
            $sparam['xIdEstadoPublicoConsejo'] = 1;
        }

        if (isset($datos['Nombre']) && $datos['Nombre'] != "") {
            $sparam['Nombre'] = $datos['Nombre'];
            $sparam['xNombre'] = 1;
        }

        if (isset($datos['NombrePublico']) && $datos['NombrePublico'] != "") {
            $sparam['NombrePublico'] = $datos['NombrePublico'];
            $sparam['xNombrePublico'] = 1;
        }


        if (isset($datos['orderby']) && $datos['orderby'] != "")
            $sparam['orderby'] = $datos['orderby'];

        if (isset($datos['limit']) && $datos['limit'] != "")
            $sparam['limit'] = $datos['limit'];

        if (!parent::BusquedaAvanzada($sparam, $resultado, $numfilas))
            return false;
        return true;
    }


    public function BuscarAuditoriaRapida($datos, &$resultado, &$numfilas) {
        if (!parent::BuscarAuditoriaRapida($datos, $resultado, $numfilas))
            return false;
        return true;
    }


    public function BuscarEstadosFinales($datos, &$resultado, &$numfilas) {
        if (!parent::BuscarEstadosFinales($datos, $resultado, $numfilas))
            return false;
        return true;
    }


    public function Insertar($datos, &$codigoinsertado) {
        if (!$this->_ValidarInsertar($datos))
            return false;

        $this->_SetearNull($datos);
        $datos['AltaUsuario'] = $_SESSION['usuariocod'];
        $datos['AltaFecha'] = date("Y/m/d H:i:s");
        $datos['UltimaModificacionUsuario'] = $_SESSION['usuariocod'];
        $datos['UltimaModificacionFecha'] = date("Y/m/d H:i:s");
        if (!parent::Insertar($datos, $codigoinsertado))
            return false;

        $oAuditoriasCircuitosEstados = new cAuditoriasCircuitosEstados($this->conexion, $this->formato);
        $datos['IdEstado'] = $codigoinsertado;
        $datos['Accion'] = INSERTAR;
        $datos['AltaUsuario'] = $datos['AltaUsuario'];
        $datos['AltaFecha'] = $datos['AltaFecha'];
        if (!$oAuditoriasCircuitosEstados->InsertarLog($datos, $codigoInsertadolog))
            return false;

        if (!$this->PublicarListadoJson())
            return false;

        return true;
    }


    public function Modificar($datos) {
        if (!$this->_ValidarModificar($datos, $datosRegistro))
            return false;

        $datos['UltimaModificacionUsuario'] = $datosRegistro['UltimaModificacionUsuario'] = $_SESSION['usuariocod'];
        $datos['UltimaModificacionFecha'] = $datosRegistro['UltimaModificacionFecha'] = date("Y/m/d H:i:s");
        $this->_SetearNull($datos);
        if (!parent::Modificar($datos))
            return false;

        $oAuditoriasCircuitosEstados = new cAuditoriasCircuitosEstados($this->conexion, $this->formato);
        $datosRegistro['Accion'] = MODIFICACION;
        if (!$oAuditoriasCircuitosEstados->InsertarLog($datosRegistro, $codigoInsertadolog))
            return false;

        if (!$this->PublicarListadoJson())
            return false;

        return true;
    }


    public function Eliminar($datos) {
        if (!$this->_ValidarEliminar($datos, $datosRegistro))
            return false;

        $oAuditoriasCircuitosEstados = new cAuditoriasCircuitosEstados($this->conexion, $this->formato);
        $datosLog = $datosRegistro;
        $datosLog['Accion'] = ELIMINAR;
        if (!$oAuditoriasCircuitosEstados->InsertarLog($datosLog, $codigoInsertadolog))
            return false;

        if (!parent::Eliminar($datos))
            return false;

        if (!$this->PublicarListadoJson())
            return false;

        return true;
    }

    public function BusquedaRapida($datos, &$resultado, &$numfilas) {
        $sparam = [
            'xIdEstado' => 0,
            'IdEstado' => "",
            'xNombre' => 0,
            'Nombre' => "",
            'limit' => '',
            'orderby' => "IdEstado DESC",
        ];

        if (isset($datos['IdEstado']) && $datos['IdEstado'] != "") {
            $sparam['IdEstado'] = $datos['IdEstado'];
            $sparam['xIdEstado'] = 1;
        }
        if (isset($datos['Nombre']) && $datos['Nombre'] != "") {
            $sparam['Nombre'] = $datos['Nombre'];
            $sparam['xNombre'] = 1;
        }


        if (isset($datos['orderby']) && $datos['orderby'] != "")
            $sparam['orderby'] = $datos['orderby'];

        if (isset($datos['limit']) && $datos['limit'] != "")
            $sparam['limit'] = $datos['limit'];

        if (!parent::BusquedaRapida($sparam, $resultado, $numfilas))
            return false;
        return true;
    }


    public function GuardarDatosJson($nombrearchivo, $carpeta, $array) {
        $datosJson = FuncionesPHPLocal::ConvertiraUtf8($array);
        $jsonData = json_encode($datosJson);
        if (!is_dir($carpeta)) {
            @mkdir($carpeta);
        }
        if (!FuncionesPHPLocal::GuardarArchivo($carpeta, $jsonData, $nombrearchivo . ".json")) {
            FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_INF, "Error, al generar el archivo json. ", ["archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__], ["formato" => $this->formato]);
            return false;
        }
        return true;
    }


    public function PublicarListadoJson() {
        $nombrearchivo = "estados";
        $carpeta = PUBLICA . "json/";
        if (!$this->GerenarArrayDatosJsonListado($array))
            return false;
        if (count($array) > 0) {
            if (!$this->GuardarDatosJson($nombrearchivo, $carpeta, $array))
                return false;
        }

        $oCircuitosEstadosPublicos = new cCircuitosEstadosPublicos($this->conexion, $this->formato);

        if (!$oCircuitosEstadosPublicos->PublicarListadoJson())
            return false;

        return true;
    }


    public function GerenarArrayDatosJsonListado(&$array) {
        $array = [];
        $datos['orderby'] = "IdEstado ASC";
        if (!$this->BusquedaAvanzada($datos, $resultados, $numfilas))
            return false;
        if ($numfilas > 0) {
            while ($fila = $this->conexion->ObtenerSiguienteRegistro($resultados)) {
                $array[$fila['IdEstado']] = $fila;
            }
        }
        return true;
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
        if (!$this->_ValidarDatosVacios($datos))
            return false;

        return true;
    }


    private function _ValidarEliminar($datos, &$datosRegistro) {
        if (!$this->BuscarxCodigo($datos, $resultado, $numfilas))
            return false;

        if ($numfilas != 1) {
            FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, "Error debe ingresar un código valido.", ["archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__], ["formato" => $this->formato]);
            return false;
        }
        $datosRegistro = $this->conexion->ObtenerSiguienteRegistro($resultado);
        return true;
    }


    private function _SetearNull(&$datos) {


        if (!isset($datos['Nombre']) || $datos['Nombre'] == "")
            $datos['Nombre'] = "NULL";

        if (!isset($datos['IdEstadoPublico']) || $datos['IdEstadoPublico'] == "")
            $datos['IdEstadoPublico'] = "NULL";

        if (!isset($datos['IdEstadoPublicoSad']) || $datos['IdEstadoPublicoSad'] == "")
            $datos['IdEstadoPublicoSad'] = "NULL";

        if (!isset($datos['IdEstadoPublicoConsejo']) || $datos['IdEstadoPublicoConsejo'] == "")
            $datos['IdEstadoPublicoConsejo'] = "NULL";

        if (!isset($datos['Descripcion']) || $datos['Descripcion'] == "")
            $datos['Descripcion'] = "NULL";

        if (!isset($datos['AltaFecha']) || $datos['AltaFecha'] == "")
            $datos['AltaFecha'] = "NULL";

        if (!isset($datos['AltaUsuario']) || $datos['AltaUsuario'] == "")
            $datos['AltaUsuario'] = "NULL";

        if (!isset($datos['UltimaModificacionFecha']) || $datos['UltimaModificacionFecha'] == "")
            $datos['UltimaModificacionFecha'] = "NULL";
        return true;
    }


    private function _ValidarDatosVacios($datos) {


        if (!isset($datos['Nombre']) || $datos['Nombre'] == "") {
            FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, "Debe ingresar un Nombre del Circuito", ["archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__], ["formato" => $this->formato]);
            return false;
        }

        if (!isset($datos['IdEstadoPublico']) || $datos['IdEstadoPublico'] == "") {
            FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, "Debe ingresar un estado publico Escuela", ["archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__], ["formato" => $this->formato]);
            return false;
        }

        if (!isset($datos['IdEstadoPublicoSad']) || $datos['IdEstadoPublicoSad'] == "") {
            FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, "Debe ingresar un estado publico Sad", ["archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__], ["formato" => $this->formato]);
            return false;
        }

        if (!isset($datos['IdEstadoPublicoConsejo']) || $datos['IdEstadoPublicoConsejo'] == "") {
            FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, "Debe ingresar un estado publico Consejo", ["archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__], ["formato" => $this->formato]);
            return false;
        }


        return true;
    }


}

?>
