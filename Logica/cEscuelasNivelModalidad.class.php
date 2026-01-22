<?php
include(DIR_CLASES_DB . "cEscuelasNivelModalidad.db.php");

class cEscuelasNivelModalidad extends cEscuelasNivelModalidaddb {
    /**
     * Constructor de la clase cEscuelasNivelModalidad.
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
     * Destructor de la clase cEscuelasNivelModalidad.
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

    public function BuscarGradosAnioxId($datos, &$resultado, &$numfilas): bool {
        if (!parent::BuscarGradosAnioxId($datos, $resultado, $numfilas))
            return false;
        return true;
    }

    public function BuscarGradosAnioxIdxIdCiclo($datos, &$resultado, &$numfilas): bool {
        if (!parent::BuscarGradosAnioxIdxIdCiclo($datos, $resultado, $numfilas))
            return false;
        return true;
    }

    public function BuscarGradosAnioxPlanEducativo($datos, &$resultado, &$numfilas): bool {
        if (!parent::BuscarGradosAnioxPlanEducativo($datos, $resultado, $numfilas))
            return false;
        return true;
    }

    public function BuscarGradosAnioxIdEscuelaTurnoxPlanEducativo($datos, &$resultado, &$numfilas): bool {
        if (!parent::BuscarGradosAnioxIdEscuelaTurnoxPlanEducativo($datos, $resultado, $numfilas))
            return false;
        return true;
    }


    public function BuscarCiclosxId($datos, &$resultado, &$numfilas): bool {
        if (!parent::BuscarCiclosxId($datos, $resultado, $numfilas))
            return false;
        return true;
    }


    public function BusquedaAvanzada($datos, &$resultado, &$numfilas): bool {
        $sparam = [
            'xIdEscuela' => 0,
            'IdEscuela' => "-1",
            'xIdNivel' => 0,
            'IdNivel' => "-1",
            'xIdModalidad' => 0,
            'IdModalidad' => "-1",
            'xEstado' => 0,
            'Estado' => "-1",
            'limit' => '',
            'orderby' => "Id ASC",
        ];
        if (isset($datos['IdEscuela']) && $datos['IdEscuela'] != "") {

            if (is_array($datos['IdEscuela'])) {
                $sparam['IdEscuela'] = implode(",", $datos['IdEscuela']);
            } else {
                $sparam['IdEscuela'] = $datos['IdEscuela'];
            }

            $sparam['xIdEscuela'] = 1;
        }
        if (isset($datos['IdNivel']) && $datos['IdNivel'] != "") {
            $sparam['IdNivel'] = $datos['IdNivel'];
            $sparam['xIdNivel'] = 1;
        }

        if (isset($datos['IdsNiveles']) && $datos['IdsNiveles'] != "") {
            $sparam['IdNivel'] = is_array($datos['IdsNiveles']) ? $datos['IdsNiveles'] : explode(',', $datos['IdsNiveles']);
            $sparam['xIdNivel'] = 1;
        }

        if (isset($datos['IdModalidad']) && $datos['IdModalidad'] != "") {
            $sparam['IdModalidad'] = $datos['IdModalidad'];
            $sparam['xIdModalidad'] = 1;
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


    public function BuscarxIdEscuela($datos, &$resultado, &$numfilas): bool {
        if (!parent::BuscarxIdEscuela($datos, $resultado, $numfilas))
            return false;
        return true;
    }

    public function Buscar_Nivel_Nombre_xIdEscuela($datos, &$resultado, &$numfilas): bool {
        if (!parent::Buscar_Nivel_Nombre_xIdEscuela($datos, $resultado, $numfilas))
            return false;
        return true;
    }

    public function Buscar_TurnosxNivelxEscuela($datos, &$resultado, &$numfilas): bool {
        if (!parent::Buscar_TurnosxNivelxEscuela($datos, $resultado, $numfilas))
            return false;
        return true;
    }


    public function Insertar($datos, &$codigoInsertado): bool {
        if (!$this->_ValidarInsertar($datos))
            return false;
        $this->_SetearNull($datos);
        $this->ObtenerProximoOrden($datos, $proxorden);
        $datos['Id'] = $proxorden;
        $datos['AltaFecha'] = date("Y-m-d H:i:s");
        $datos['AltaUsuario'] = $_SESSION['usuariocod'];
        $datos['UltimaModificacionFecha'] = date("Y-m-d H:i:s");
        $datos['UltimaModificacionUsuario'] = $_SESSION['usuariocod'];
        $datos['Estado'] = ACTIVO;
        if (!parent::Insertar($datos, $codigoInsertado))
            return false;
        $oAuditoriasEscuelasNivelModalidad = new cAuditoriasEscuelasNivelModalidad($this->conexion, $this->formato);
        $datos['Id'] = $codigoInsertado;
        $datos['Accion'] = INSERTAR;
        if (!$oAuditoriasEscuelasNivelModalidad->InsertarLog($datos, $codigoInsertadolog))
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
        $oAuditoriasEscuelasNivelModalidad = new cAuditoriasEscuelasNivelModalidad($this->conexion, $this->formato);
        $datosRegistro['Accion'] = MODIFICACION;
        if (!$oAuditoriasEscuelasNivelModalidad->InsertarLog($datosRegistro, $codigoInsertadolog))
            return false;
        return true;
    }

    public function ModificarModalidad($datos): bool {
        if (!isset($datos['IdModalidad']) || $datos['IdModalidad'] == "")
            $datos['IdModalidad'] = 'NULL';
        $datos['UltimaModificacionFecha'] = $datosRegistro['UltimaModificacionFecha'] = date("Y-m-d H:i:s");
        $datos['UltimaModificacionUsuario'] = $datosRegistro['UltimaModificacionUsuario'] = $_SESSION['usuariocod'];
        if (!parent::ModificarModalidad($datos))
            return false;
        $oAuditoriasEscuelasNivelModalidad = new cAuditoriasEscuelasNivelModalidad($this->conexion, $this->formato);
        $datosRegistro['Accion'] = MODIFICACION;
        $datosRegistro['Id'] = $datos['Id'];
        if (!$oAuditoriasEscuelasNivelModalidad->InsertarLog($datosRegistro, $codigoInsertadolog))
            return false;
        return true;
    }

    public function Eliminar($datos): bool {
        if (!$this->_ValidarEliminar($datos, $datosRegistro))
            return false;
        $oAuditoriasEscuelasNivelModalidad = new cAuditoriasEscuelasNivelModalidad($this->conexion, $this->formato);
        $datosLog = $datosRegistro;
        $datosLog['Accion'] = ELIMINAR;
        if (!$oAuditoriasEscuelasNivelModalidad->InsertarLog($datosLog, $codigoInsertadolog))
            return false;
        $datosmodif['Id'] = $datos['Id'];
        $datosmodif['Estado'] = ELIMINADO;
        if (!$this->ModificarEstado($datosmodif))
            return false;
        return true;
    }

    public function EliminarxId($datos) {
        if (!$this->_ValidarEliminar($datos, $datosRegistro))
            return false;
        $oAuditoriasEscuelasNivelModalidad = new cAuditoriasEscuelasNivelModalidad($this->conexion, $this->formato);
        $datosLog = $datosRegistro;
        $datosLog['Accion'] = ELIMINAR;
        if (!$oAuditoriasEscuelasNivelModalidad->InsertarLog($datosLog, $codigoInsertadolog))
            return false;
        $datosmodif['Id'] = $datos['Id'];
        $datosmodif['Estado'] = ELIMINADO;

        if (!parent::Eliminar($datos))
            return false;

        return true;
    }


    public function ModificarEstado($datos): bool {
        if (!parent::ModificarEstado($datos))
            return false;
        return true;
    }


    public function Activar(array $datos): bool {
        $datosmodif['Id'] = $datos['Id'];
        $datosmodif['Estado'] = ACTIVO;
        if (!$this->ModificarEstado($datosmodif))
            return false;
        if (!$this->_ValidarEliminar($datos, $datosRegistro))
            return false;
        $oAuditoriasEscuelasNivelModalidad = new cAuditoriasEscuelasNivelModalidad($this->conexion, $this->formato);
        $datosRegistro['Accion'] = MODIFICACION;
        if (!$oAuditoriasEscuelasNivelModalidad->InsertarLog($datosRegistro, $codigoInsertadolog))
            return false;
        return true;
    }


    public function DesActivar(array $datos): bool {
        $datosmodif['Id'] = $datos['Id'];
        $datosmodif['Estado'] = NOACTIVO;
        if (!$this->ModificarEstado($datosmodif))
            return false;
        if (!$this->_ValidarEliminar($datos, $datosRegistro))
            return false;
        $oAuditoriasEscuelasNivelModalidad = new cAuditoriasEscuelasNivelModalidad($this->conexion, $this->formato);
        $datosRegistro['Accion'] = MODIFICACION;
        if (!$oAuditoriasEscuelasNivelModalidad->InsertarLog($datosRegistro, $codigoInsertadolog))
            return false;
        return true;
    }


    public function ModificarOrdenCompleto($datos): bool {
        $datosmodif['Id'] = 1;
        $arregloOrden = explode(",", $datos['orden']);
        foreach ($arregloOrden as $Id) {
            $datosmodif['Id'] = $Id;
            if (!parent::ModificarOrden($datosmodif))
                return false;
            $datosmodif['Id']++;
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
        return true;
    }


    private function _ValidarModificar($datos, &$datosRegistro) {
        if (!$this->BuscarxCodigo($datos, $resultado, $numfilas))
            return false;

        if ($numfilas != 1) {
            $this->setError(400, "Error debe ingresar un código valido.");
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
            $this->setError(400, 'Error debe ingresar un código valido.');
            return false;
        }
        $datosRegistro = $this->conexion->ObtenerSiguienteRegistro($resultado);
        return true;
    }


    private function _SetearNull(&$datos): void {
        if (!isset($datos['IdEscuela']) || $datos['IdEscuela'] == "")
            $datos['IdEscuela'] = "NULL";

        if (!isset($datos['IdNivel']) || $datos['IdNivel'] == "")
            $datos['IdNivel'] = "NULL";

        if (!isset($datos['IdModalidad']) || $datos['IdModalidad'] == "")
            $datos['IdModalidad'] = "NULL";

        if (!isset($datos['UltimaModificacionFecha']) || $datos['UltimaModificacionFecha'] == "")
            $datos['UltimaModificacionFecha'] = "NULL";

        if (!isset($datos['IdTipoPOF']) || $datos['IdTipoPOF'] == "")
            $datos['IdTipoPOF'] = "NULL";
    }


    private function _ValidarDatosVacios($datos) {
        if (!isset($datos['IdEscuela']) || $datos['IdEscuela'] == "") {
            $this->setError(400, 'Debe seleccionar una escuela');
            return false;
        }

        if (isset($datos['IdEscuela']) && $datos['IdEscuela'] != "") {

            if (!FuncionesPHPLocal::ValidarContenido($this->conexion, $datos['IdEscuela'], "NumericoEntero")) {
                $this->setError(400, 'Error debe ingresar un campo numérico para el campo Escuela.');
                return false;
            }
            if (strlen($datos['IdEscuela']) > 11) {
                $this->setError(400, 'Error, el campo Escuela no puede ser mayor a 11.');
                return false;
            }
        }

        if (!isset($datos['IdNivel']) || $datos['IdNivel'] == "") {
            $this->setError(400, 'Debe seleccionar un nivel');
            return false;
        }

        if (isset($datos['IdNivel']) && $datos['IdNivel'] != "") {
            if (!FuncionesPHPLocal::ValidarContenido($this->conexion, $datos['IdNivel'], "NumericoEntero")) {
                $this->setError(400, 'Error debe ingresar un campo numérico para el campo Nivel.');
                return false;
            }

            if (strlen($datos['IdNivel']) > 2) {
                $this->setError(400, 'Error, el campo Nivel no puede ser mayor a 2 .');
                return false;
            }
        }

        if (isset($datos['IdModalidad']) && $datos['IdModalidad'] != "") {
            if (!FuncionesPHPLocal::ValidarContenido($this->conexion, $datos['IdModalidad'], "NumericoEntero")) {
                $this->setError(400, "Error debe ingresar un campo numérico para el campo Modalidad.");
                return false;
            }
            if (strlen($datos['IdModalidad']) > 2) {
                $this->setError(400, "Error, el campo Modalidad no puede ser mayor a 2 .");
                return false;
            }
        }
        return true;
    }


}
