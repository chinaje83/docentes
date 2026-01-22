<?php
include(DIR_CLASES_DB . "cPlanesEducativosAniosGrados.db.php");

class cPlanesEducativosAniosGrados extends cPlanesEducativosAniosGradosdb {
    /**
     * Constructor de la clase cPlanesEducativosAniosGrados.
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
     * Destructor de la clase cPlanesEducativosAniosGrados.
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
        if (!parent::BuscarxCodigo($datos, $resultado, $numfilas))
            return false;
        return true;
    }


    public function BusquedaAvanzada($datos, &$resultado, &$numfilas): bool {
        $sparam = [
            'xIdPlanEducativo' => 0,
            'IdPlanEducativo' => "",
            'xIdGradoAnio' => 0,
            'IdGradoAnio' => "",
            'xEstado' => 0,
            'Estado' => "-1",
            'limit' => '',
            'orderby' => "IdPlanGradoAnio ASC",
        ];
        if (isset($datos['IdPlanEducativo']) && $datos['IdPlanEducativo'] != "") {
            $sparam['IdPlanEducativo'] = $datos['IdPlanEducativo'];
            $sparam['xIdPlanEducativo'] = 1;
        }
        if (isset($datos['IdGradoAnio']) && $datos['IdGradoAnio'] != "") {
            $sparam['IdGradoAnio'] = $datos['IdGradoAnio'];
            $sparam['xIdGradoAnio'] = 1;
        }
        if (isset($datos['Estado']) && $datos['Estado'] != "") {
            $sparam['Estado'] = $datos['Estado'];
            $sparam['xEstado'] = 1;
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


    public function BuscarInsertar($datos, &$resultado, &$numfilas): bool {
        if (!parent::BuscarInsertar($datos, $resultado, $numfilas))
            return false;

        return true;
    }


    public function Insertar($datos, &$codigoInsertado): bool {
        if (!$this->_ValidarInsertar($datos))
            return false;
        $this->_SetearNull($datos);
        $datos['AltaFecha'] = date("Y-m-d H:i:s");
        $datos['AltaUsuario'] = $_SESSION['usuariocod'];
        $datos['UltimaModificacionFecha'] = date("Y-m-d H:i:s");
        $datos['UltimaModificacionUsuario'] = $_SESSION['usuariocod'];
        $datos['Estado'] = ACTIVO;
        if (!parent::Insertar($datos, $codigoInsertado))
            return false;
        $oAuditoriasPlanesEducativosAniosGrados = new cAuditoriasPlanesEducativosAniosGrados($this->conexion, $this->formato);
        $datos['IdPlanGradoAnio'] = $codigoInsertado;
        $datos['Accion'] = INSERTAR;
        $datos['AltaUsuario'] = $datos['AltaUsuario'];
        $datos['AltaFecha'] = $datos['AltaFecha'];
        if (!$oAuditoriasPlanesEducativosAniosGrados->InsertarLog($datos, $codigoInsertadolog))
            return false;
        return true;
    }


    public function Modificar($datos): bool {
        if (!$this->_ValidarModificar($datos, $datosRegistro))
            return false;
        $datos['UltimaModificacionFecha'] = $datosRegistro['UltimaModificacionFecha'] = date("Y-m-d H:i:s");
        $datos['UltimaModificacionUsuario'] = $datosRegistro['UltimaModificacionUsuario'] = $_SESSION['usuariocod'];
        $this->_SetearNull($datos);
        if (!parent::Modificar($datos))
            return false;
        $oAuditoriasPlanesEducativosAniosGrados = new cAuditoriasPlanesEducativosAniosGrados($this->conexion, $this->formato);
        $datosRegistro['Accion'] = MODIFICACION;
        if (!$oAuditoriasPlanesEducativosAniosGrados->InsertarLog($datosRegistro, $codigoInsertadolog))
            return false;
        return true;
    }


    public function Eliminar($datos): bool {
        if (!$this->_ValidarEliminar($datos, $datosRegistro))
            return false;
        $oAuditoriasPlanesEducativosAniosGrados = new cAuditoriasPlanesEducativosAniosGrados($this->conexion, $this->formato);
        $datosLog = $datosRegistro;
        $datosLog['Accion'] = ELIMINAR;
        if (!$oAuditoriasPlanesEducativosAniosGrados->InsertarLog($datosLog, $codigoInsertadolog))
            return false;
        $datosmodif['IdPlanGradoAnio'] = $datos['IdPlanGradoAnio'];
        $datosmodif['Estado'] = ELIMINADO;
        if (!$this->ModificarEstado($datosmodif))
            return false;
        return true;
    }


    public function ModificarEstado($datos): bool {
        if (!parent::ModificarEstado($datos))
            return false;
        return true;
    }


    public function Activar(array $datos): bool {
        $datosmodif['IdPlanGradoAnio'] = $datos['IdPlanGradoAnio'];
        $datosmodif['Estado'] = ACTIVO;
        if (!$this->ModificarEstado($datosmodif))
            return false;
        if (!$this->_ValidarEliminar($datos, $datosRegistro))
            return false;
        $oAuditoriasPlanesEducativosAniosGrados = new cAuditoriasPlanesEducativosAniosGrados($this->conexion, $this->formato);
        $datosRegistro['Accion'] = MODIFICACION;
        if (!$oAuditoriasPlanesEducativosAniosGrados->InsertarLog($datosRegistro, $codigoInsertadolog))
            return false;
        return true;
    }


    public function DesActivar(array $datos): bool {
        $datosmodif['IdPlanGradoAnio'] = $datos['IdPlanGradoAnio'];
        $datosmodif['Estado'] = NOACTIVO;
        if (!$this->ModificarEstado($datosmodif))
            return false;
        if (!$this->_ValidarEliminar($datos, $datosRegistro))
            return false;
        $oAuditoriasPlanesEducativosAniosGrados = new cAuditoriasPlanesEducativosAniosGrados($this->conexion, $this->formato);
        $datosRegistro['Accion'] = MODIFICACION;
        if (!$oAuditoriasPlanesEducativosAniosGrados->InsertarLog($datosRegistro, $codigoInsertadolog))
            return false;
        return true;
    }


    public function ModificarOrdenCompleto($datos): bool {
        $datosmodif['IdPlanGradoAnio'] = 1;
        $arregloOrden = explode(",", $datos['orden']);
        foreach ($arregloOrden as $IdPlanGradoAnio) {
            $datosmodif['IdPlanGradoAnio'] = $IdPlanGradoAnio;
            if (!parent::ModificarOrden($datosmodif))
                return false;
            $datosmodif['IdPlanGradoAnio']++;
        }
        return true;
    }


    private function ObtenerProximoOrden(array $datos, ?int &$proxorden): bool {
        $proxorden = 0;
        if (!parent::BuscarUltimoOrden($datos, $resultado, $numfilas))
            return false;
        if ($numfilas != 0) {
            $datos = $this->conexion->ObtenerSiguienteRegistro($resultado);
            $proxorden = $datos['maximo'] + 1;
        }
        return true;
    }




//-----------------------------------------------------------------------------------------
//FUNCIONES PRIVADAS
//-----------------------------------------------------------------------------------------

    private function _ValidarInsertar($datos) {
        if (!$this->_ValidarDatosVacios($datos))
            return false;

        $datosBuscar['IdPlanEducativo'] = $datos['IdPlanEducativo'];
        $datosBuscar['IdGradoAnio'] = $datos['IdGradoAnio'];
        if (!$this->BuscarInsertar($datosBuscar, $resultado, $numfilas))
            return false;

        if ($numfilas > 0) {
            FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, utf8_decode("Actualmente el Grado/Año ya se encuentra asociado."), ["archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__], ["formato" => $this->formato]);
            return false;
        }

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
        $oPlanesMaterias = new cPlanesEducativosMaterias($this->conexion, "");
        $oPlanesMaterias->BuscarExistente($datos, $resultado, $numfilas);

        if ($numfilas > 0) {
            while ($fila = $this->conexion->ObtenerSiguienteRegistro($resultado)) {
                $datosEliminar['Id'] = $fila['Id'];
                if (!$oPlanesMaterias->EliminarFisico($datosEliminar))
                    return false;
            }
        }

        if (!$this->BuscarxCodigo($datos, $resultado, $numfilas))
            return false;

        if ($numfilas != 1) {
            FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, "Error debe ingresar un código valido.", ["archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__], ["formato" => $this->formato]);
            return false;
        }
        $datosRegistro = $this->conexion->ObtenerSiguienteRegistro($resultado);
        return true;
    }


    private function _SetearNull(&$datos): void {


        if (!isset($datos['IdPlanEducativo']) || $datos['IdPlanEducativo'] == "")
            $datos['IdPlanEducativo'] = "NULL";

        if (!isset($datos['IdGradoAnio']) || $datos['IdGradoAnio'] == "")
            $datos['IdGradoAnio'] = "NULL";

        if (!isset($datos['UltimaModificacionFecha']) || $datos['UltimaModificacionFecha'] == "")
            $datos['UltimaModificacionFecha'] = "NULL";

    }


    private function _ValidarDatosVacios($datos) {
        if (!isset($datos['IdPlanEducativo']) || $datos['IdPlanEducativo'] == "") {
            FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, "Debe ingresar un Id PlanEducativo|", ["archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__], ["formato" => $this->formato]);
            return false;
        }

        if (isset($datos['IdPlanEducativo']) && $datos['IdPlanEducativo'] != "") {
            if (!FuncionesPHPLocal::ValidarContenido($this->conexion, $datos['IdPlanEducativo'], "NumericoEntero")) {
                FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, "Error debe ingresar un campo numérico para el campo Plan Educativo.", ["archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__], ["formato" => $this->formato]);
                return false;
            }
            if (strlen($datos['IdPlanEducativo']) > 11) {
                FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, "Error, el campo Plan Educativo no puede ser mayor a 11 .", ["archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__], ["formato" => $this->formato]);
                return false;
            }
        }

        if (!isset($datos['IdGradoAnio']) || $datos['IdGradoAnio'] == "") {
            FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, "Debe ingresar un Id GradoAnio", ["archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__], ["formato" => $this->formato]);
            return false;
        }

        if (isset($datos['IdGradoAnio']) && $datos['IdGradoAnio'] != "") {

            if (!FuncionesPHPLocal::ValidarContenido($this->conexion, $datos['IdGradoAnio'], "NumericoEntero")) {
                FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, "Error debe ingresar un campo numérico para el campo Grados - A�os.", ["archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__], ["formato" => $this->formato]);
                return false;
            }
            if (strlen($datos['IdGradoAnio']) > 5) {
                FuncionesPHPLocal::MostrarMensaje($this->conexion, MSG_ERRGRAVE, "Error, el campo Grados - A�os no puede ser mayor a 2 .", ["archivo" => __FILE__, "funcion" => __FUNCTION__, "linea" => __LINE__], ["formato" => $this->formato]);
                return false;
            }
        }
        return true;
    }


}
