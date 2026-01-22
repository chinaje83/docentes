<?php
include(DIR_CLASES_DB . "cFuenteFinanciamiento.db.php");

class cFuenteFinanciamiento extends cFuenteFinanciamientodb {
    protected $conexion;
    protected $formato;
    private $private;
    private $public;

    function __construct(accesoBDLocal $conexion, $formato = FMT_TEXTO, ?Elastic\Conexion $conexionES = null) {
        parent::__construct($conexion, $formato);
        $this->conexionES = $conexionES;
        $this->conexion = &$conexion;
        $this->formato = &$formato;
    }


    function __destruct() {
        parent::__destruct();
    }


    public function BusquedaFuentesFinanciamiento(&$resultado, &$numfilas) {
        if (!parent::BusquedaFuentesFinanciamiento($resultado, $numfilas))
            return false;
        return true;
    }

    //-----------------------------------------------------------------------------------------
    //FUNCIONES PRIVADAS
    //-----------------------------------------------------------------------------------------


    private function _ValidarDatosVacios(&$datos) {


        if (!isset($datos['IdEscuela']) || $datos['IdEscuela'] == "") {
            FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, "Debe ingresar una escuela", ["archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__], ["formato" => $this->formato]);
            return false;
        }

        $oEscuelas = new cEscuelas($this->conexion, $this->formato);
        if (!$oEscuelas->BuscarxCodigo($datos, $resultado, $numfilas))
            return false;
        if ($numfilas != 1) {
            FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, "Error, escuela inexixtente", ["archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__], ["formato" => $this->formato]);
            return false;
        }
        $datosEscuela = $this->conexion->ObtenerSiguienteRegistro($resultado);

        if (!isset($datos['NombreTipoDocumento']) || $datos['NombreTipoDocumento'] == "") {
            FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, "Debe ingresar un nombre de tipo de documento", ["archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__], ["formato" => $this->formato]);
            return false;
        }

        if (file_exists(CARPETACONFIGURACIONTIPOSDOCUMENTOS_FISICA . "documentos_tipos/documento_tipo_" . $datos['IdTipoDocumento'] . ".php"))
            include(CARPETACONFIGURACIONTIPOSDOCUMENTOS_FISICA . "documentos_tipos/documento_tipo_" . $datos['IdTipoDocumento'] . ".php");
        else {
            FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, "Ha ocurrido un error al buscar el tipo de documento {$datos['IdTipoDocumento']}", ["archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__], ["formato" => $this->formato]);
            return false;
        }
        $oValidaciones = new cValidaciones($this->conexion, $this, $oEscuelas, $this->formato);


        if (!$oValidaciones->ValidarCajasDatos($ArrayValidacion, $datos)) {
            return false;
        }

        return true;
    }

}

?>
