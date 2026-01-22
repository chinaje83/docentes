<?php
include(DIR_CLASES_DB . "cMotivos.db.php");

class cMotivos extends cMotivosdb {

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

    public function BuscarLicenciasTiposActivas($datos, &$resultado, &$numfilas) {
        if (!parent::BuscarLicenciasTiposActivas($datos, $resultado, $numfilas))
            return false;
        return true;
    }


    public function BusquedaAvanzada($datos, &$resultado, &$numfilas) {
        $sparam = [
            'xId' => 0,
            'Id' => '',
            'xNombre' => 0,
            'Nombre' => "",
            'xDescripcion' => 0,
            'Descripcion' => '',
            'xEstado' => 0,
            'Estado' => '',
            'xIdCircuito' => 0,
            'IdCircuito' => '',
            'xIdTipoLicencia' => 0,
            'IdTipoLicencia' => '',
            'xReincorporacionAutomatico' => 0,
            'ReincorporacionAutomatico' => '',
            'limit' => '',
            'orderby' => 'Descripcion ASC',
        ];

        if (isset($datos['Id']) && $datos['Id'] != "") {
            $sparam['Id'] = $datos['Id'];
            $sparam['xId'] = 1;
        }

        if (isset($datos['Nombre']) && $datos['Nombre'] != "") {
            $sparam['Nombre'] = $datos['Nombre'];
            $sparam['xNombre'] = 1;
        }

        if (isset($datos['Descripcion']) && $datos['Descripcion'] != "") {
            $sparam['Descripcion'] = $datos['Descripcion'];
            $sparam['xDescripcion'] = 1;
        }

        if (isset($datos['Estado']) && $datos['Estado'] != "") {
            $sparam['Estado'] = $datos['Estado'];
            $sparam['xEstado'] = 1;
        }

        if (isset($datos['IdCircuito']) && $datos['IdCircuito'] != "") {
            $sparam['IdCircuito'] = $datos['IdCircuito'];
            $sparam['xIdCircuito'] = 1;
        }
        if (isset($datos['IdTipoLicencia']) && $datos['IdTipoLicencia'] != "") {
            $sparam['IdTipoLicencia'] = $datos['IdTipoLicencia'];
            $sparam['xIdTipoLicencia'] = 1;
        }
        if (isset($datos['ReincorporacionAutomatico']) && $datos['ReincorporacionAutomatico'] != "") {
            $sparam['ReincorporacionAutomatico'] = $datos['ReincorporacionAutomatico'];
            $sparam['xReincorporacionAutomatico'] = 1;
        }


        if (isset($datos['orderby']) && $datos['orderby'] != "")
            $sparam['orderby'] = $datos['orderby'];

        if (isset($datos['limit']) && $datos['limit'] != "")
            $sparam['limit'] = $datos['limit'];

        if (!parent::BusquedaAvanzada($sparam, $resultado, $numfilas))
            return false;
        return true;
    }


    public function InactivaCargos(&$resultado, &$numfilas) {
        if (!parent::InactivaCargos($resultado, $numfilas))
            return false;
        return true;
    }


    public function Insertar($datos, &$codigoinsertado) {
        if (!$this->_ValidarInsertar($datos))
            return false;

        $datos['AltaFecha'] = date("Y-m-d H:i:s");
        $datos['UltimaModificacionFecha'] = date("Y-m-d H:i:s");
        $datos['Estado'] = ACTIVO;
        $this->_SetearNull($datos);
        if (!parent::Insertar($datos, $codigoinsertado))
            return false;

        return true;
    }


    public function Modificar($datos) {
        if (!$this->_ValidarModificar($datos, $datosRegistro))
            return false;

        $datos['UltimaModificacionFecha'] = date("Y-m-d H:i:s");
        $this->_SetearNull($datos);
        if (!parent::Modificar($datos))
            return false;


        return true;
    }


    public function Eliminar($datos) {
        if (!$this->_ValidarEliminar($datos, $datosRegistro))
            return false;

        $datosmodif['Id'] = $datos['Id'];
        $datosmodif['Estado'] = ELIMINADO;
        $datosmodif['UltimaModificacionFecha'] = date("Y-m-d H:i:s");
        if (!$this->ModificarEstado($datosmodif))
            return false;

        return true;
    }


    public function ModificarEstado($datos) {
        if (!parent::ModificarEstado($datos))
            return false;
        return true;
    }


    public function Activar($datos) {
        if (!$this->BuscarxCodigo($datos, $resultado, $numfilas))
            return false;

        if ($numfilas != 1) {
            FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, "Error debe ingresar un código valido.", ["archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__], ["formato" => $this->formato]);
            return false;
        }
        $datosRegistro = $this->conexion->ObtenerSiguienteRegistro($resultado);

        $datosmodif['UltimaModificacionFecha'] = date("Y-m-d H:i:s");
        $datosmodif['Id'] = $datos['Id'];
        $datosmodif['Estado'] = ACTIVO;
        if (!$this->ModificarEstado($datosmodif))
            return false;

        return true;
    }


    public function DesActivar($datos) {
        if (!$this->BuscarxCodigo($datos, $resultado, $numfilas))
            return false;

        if ($numfilas != 1) {
            FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, "Error debe ingresar un código valido.", ["archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__], ["formato" => $this->formato]);
            return false;
        }
        $datosRegistro = $this->conexion->ObtenerSiguienteRegistro($resultado);


        $datosmodif['UltimaModificacionFecha'] = date("Y-m-d H:i:s");
        $datosmodif['Id'] = $datos['Id'];
        $datosmodif['Estado'] = NOACTIVO;
        if (!$this->ModificarEstado($datosmodif))
            return false;

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

        if (!isset($datos['Descripcion']) || $datos['Descripcion'] == "")
            $datos['Descripcion'] = "NULL";

        if (FuncionesPHPLocal::isEmpty($datos['DiasAnticipacionDesde'])) {
            $datos['DiasAnticipacionDesde'] = 'NULL';
        }

        if (FuncionesPHPLocal::isEmpty($datos['DiasAnticipacionHasta'])) {
            $datos['DiasAnticipacionHasta'] = 'NULL';
        }

        if (FuncionesPHPLocal::isEmpty($datos['MinimoDiasDuracion'])) {
            $datos['MinimoDiasDuracion'] = 'NULL';
        }

        if (FuncionesPHPLocal::isEmpty($datos['MaximoDiasDuracion'])) {
            $datos['MaximoDiasDuracion'] = 'NULL';
        }

        if (FuncionesPHPLocal::isEmpty($datos['InactivaCargos'])) {
            $datos['InactivaCargos'] = 'NULL';
        }

        if (FuncionesPHPLocal::isEmpty($datos['IdRegimenSalarial'])) {
            $datos['IdRegimenSalarial'] = 'NULL';
        }

        if (FuncionesPHPLocal::isEmpty($datos['PermiteInasistenciaDiaria'])) {
            $datos['PermiteInasistenciaDiaria'] = 'NULL';
        }

        if (FuncionesPHPLocal::isEmpty($datos['SeleccionaDuracion'])) {
            $datos['SeleccionaDuracion'] = 'NULL';
        }

        if (FuncionesPHPLocal::isEmpty($datos['EligeDiagnostico'])) {
            $datos['EligeDiagnostico'] = 'NULL';
        }
        if (FuncionesPHPLocal::isEmpty($datos['ConHistoriaClinica'])) {
            $datos['ConHistoriaClinica'] = 'NULL';
        }
        if (FuncionesPHPLocal::isEmpty($datos['EligeArticulo'])) {
            $datos['EligeArticulo'] = 'NULL';
        }
        if (FuncionesPHPLocal::isEmpty($datos['EmpiezaHoy'])) {
            $datos['EmpiezaHoy'] = 'NULL';
        }

        return true;
    }


    private function _ValidarDatosVacios($datos) {


        if (!isset($datos['IdTipoLicencia']) || $datos['IdTipoLicencia'] == "") {
            FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, "Debe ingresar un tipo de licencia", ["archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__], ["formato" => $this->formato]);
            return false;
        }

        /*if (!isset($datos['Nombre']) || $datos['Nombre']=="")
        {
            FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Debe ingresar un nombre",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
            return false;
        }*/

        if (!isset($datos['PermiteLicenciaFamiliar']) || $datos['PermiteLicenciaFamiliar'] === "") {
            FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, "Debe ingresar si permite licencia familiar", ["archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__], ["formato" => $this->formato]);
            return false;
        }

        if (!isset($datos['PermiteSuperposicion']) || $datos['PermiteSuperposicion'] === "") {
            FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, "Debe ingresar si permite superposicion", ["archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__], ["formato" => $this->formato]);
            return false;
        }

        if (!isset($datos['PermiteAgentes']) || $datos['PermiteAgentes'] === "") {
            FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, "Debe ingresar si permite agentes", ["archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__], ["formato" => $this->formato]);
            return false;
        }

        if (!isset($datos['SeleccionaCargos']) || $datos['SeleccionaCargos'] === "") {
            FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, "Debe ingresar si selecciona cargos", ["archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__], ["formato" => $this->formato]);
            return false;
        }

        if (!isset($datos['IdCircuito']) || $datos['IdCircuito'] == "") {
            FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, "Debe ingresar un circuito", ["archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__], ["formato" => $this->formato]);
            return false;
        }


        if (!isset($datos['ReincorporacionAutomatico']) || $datos['ReincorporacionAutomatico'] == "") {
            FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, "Debe ingresar si reincorpora automaticamente", ["archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__], ["formato" => $this->formato]);
            return false;
        }


        if (!isset($datos['AdmiteSuplente']) || $datos['AdmiteSuplente'] == "") {
            FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, "Debe ingresar si admite suplente", ["archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__], ["formato" => $this->formato]);
            return false;
        }

        return true;
    }


}

?>
