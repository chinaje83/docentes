<?php
include(DIR_CLASES_DB . "cArticulos.db.php");

class cArticulosTabla extends cArticulosTabladb {
    use Validaciones;

    public function __construct($conexion, $formato = FMT_ARRAY) {
        parent::__construct($conexion, $formato);
    }

    public function __destruct() {
        parent::__destruct();
    }

    public function BuscarxCodigo($datos, &$resultado, &$numfilas): bool {
        if (!parent::BuscarxCodigo($datos, $resultado, $numfilas))
            return false;
        return true;
    }

    public function buscarListado(&$resultado, ?int &$numfilas) {
        return parent::buscarListado($resultado, $numfilas);
    }

    public function BusquedaAvanzada($datos, &$resultado, &$numfilas): bool {
        $sparam = array(
            'xIdArticulo' => 0,
            'IdArticulo' => '',
            'xDescripcion' => 0,
            'Descripcion' => '',
            'xCodigo' => 0,
            'Codigo' => '',
            'xIdMotivo' => 0,
            'IdMotivo' => '',
            'xIdTipoLicencia' => 0,
            'IdTipoLicencia' => '',
            'xCantidadMaximaDias' => 0,
            'CantidadMaximaDias' => '',
            'xConGoceSueldo' => 0,
            'ConGoceSueldo' => '',
            'xPermiteOtroOrganismo' => 0,
            'PermiteOtroOrganismo' => '',
            'xEsAnual' => 0,
            'EsAnual' => '',
            'xEsAuxiliar' => 0,
            'EsAuxiliar' => '',
			'xIdExterno' => 0,
			'IdExterno' => '',
            'xEstado' => 0,
            'Estado' => '-1',
            'limit' => '',
            'orderby' => 'IdArticulo DESC'
        );

        if (isset($datos['IdArticulo']) && $datos['IdArticulo'] != "") {
            $sparam['IdArticulo'] = $datos['IdArticulo'];
            $sparam['xIdArticulo'] = 1;
        }

        if (isset($datos['Descripcion']) && $datos['Descripcion'] != "") {
            $sparam['Descripcion'] = $datos['Descripcion'];
            $sparam['xDescripcion'] = 1;
        }

        if (isset($datos['Codigo']) && $datos['Codigo'] != "") {
            $sparam['Codigo'] = $datos['Codigo'];
            $sparam['xCodigo'] = 1;
        }

        if (isset($datos['IdMotivo']) && $datos['IdMotivo'] != "") {
            $sparam['IdMotivo'] = $datos['IdMotivo'];
            $sparam['xIdMotivo'] = 1;
        }

        if (isset($datos['IdTipoLicencia']) && $datos['IdTipoLicencia'] != "") {
            $sparam['IdTipoLicencia'] = $datos['IdTipoLicencia'];
            $sparam['xIdTipoLicencia'] = 1;
        }

        if (isset($datos['CantidadMaximaDias']) && $datos['CantidadMaximaDias'] != "") {
            $sparam['CantidadMaximaDias'] = $datos['CantidadMaximaDias'];
            $sparam['xCantidadMaximaDias'] = 1;
        }

        if (isset($datos['ConGoceSueldo']) && $datos['ConGoceSueldo'] != "") {
            $sparam['ConGoceSueldo'] = $datos['ConGoceSueldo'];
            $sparam['xConGoceSueldo'] = 1;
        }

        if (isset($datos['PermiteOtroOrganismo']) && $datos['PermiteOtroOrganismo'] != "") {
            $sparam['PermiteOtroOrganismo'] = $datos['PermiteOtroOrganismo'];
            $sparam['xPermiteOtroOrganismo'] = 1;
        }

        if (isset($datos['EsAnual']) && $datos['EsAnual'] != "") {
            $sparam['EsAnual'] = $datos['EsAnual'];
            $sparam['xEsAnual'] = 1;
        }

        if (isset($datos['EsAuxiliar']) && $datos['EsAuxiliar'] != "") {
            $sparam['EsAuxiliar'] = $datos['EsAuxiliar'];
            $sparam['xEsAuxiliar'] = 1;
        }

		if (isset($datos['IdExterno']) && $datos['IdExterno'] != "") {
			$sparam['IdExterno'] = $datos['IdExterno'];
			$sparam['xIdExterno'] = 1;
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


    public function Insertar($datos, &$codigoinsertado): bool {
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


    public function Modificar($datos): bool {
        if (!$this->_ValidarModificar($datos, $datosRegistro))
            return false;

        $datos['UltimaModificacionFecha'] = date("Y-m-d H:i:s");
        $this->_SetearNull($datos);
        if (!parent::Modificar($datos))
            return false;


        return true;
    }


    public function Eliminar($datos): bool {
        if (!$this->_ValidarEliminar($datos, $datosRegistro))
            return false;

        $datosmodif['IdArticulo'] = $datos['IdArticulo'];
        $datosmodif['Estado'] = ELIMINADO;
        $datosmodif['UltimaModificacionFecha'] = date("Y-m-d H:i:s");
        if (!$this->ModificarEstado($datosmodif))
            return false;

        return true;
    }


    public function ModificarEstado($datos): bool {
        if (!parent::ModificarEstado($datos))
            return false;
        return true;
    }


    public function Activar($datos): bool {
        if (!$this->_validarExistencia($datos, $datosRegistro))
            return false;

        $datosModif['UltimaModificacionFecha'] = date("Y-m-d H:i:s");
        $datosModif['IdArticulo'] = $datos['IdArticulo'];
        $datosModif['Estado'] = ACTIVO;
        if (!$this->ModificarEstado($datosModif))
            return false;

        return true;
    }


    public function DesActivar($datos): bool {
        if (!$this->_validarExistencia($datos, $datosRegistro))
            return false;

        $datosModif['UltimaModificacionFecha'] = date("Y-m-d H:i:s");
        $datosModif['IdArticulo'] = $datos['IdArticulo'];
        $datosModif['Estado'] = NOACTIVO;
        if (!$this->ModificarEstado($datosModif))
            return false;

        return true;
    }




//-----------------------------------------------------------------------------------------
//FUNCIONES PRIVADAS
//-----------------------------------------------------------------------------------------

    private function _ValidarInsertar($datos): bool {
        if (!$this->_ValidarDatosVacios($datos))
            return false;

        return true;
    }


    private function _ValidarModificar($datos, &$datosRegistro): bool {
        if (!$this->_validarExistencia($datos, $datosRegistro))
            return false;
        if (!$this->_ValidarDatosVacios($datos))
            return false;

        return true;
    }


    private function _ValidarEliminar($datos, &$datosRegistro): bool {
        if (!$this->_validarExistencia($datos, $datosRegistro))
            return false;
        return true;
    }


    private function _SetearNull(&$datos): void {
        if (!isset($datos['Descripcion']) || $datos['Descripcion'] == "")
            $datos['Descripcion'] = "NULL";

        if (!isset($datos['Observaciones']) || $datos['Observaciones'] == "")
            $datos['Observaciones'] = "NULL";

        if (!isset($datos['CantidadMaximaDias']) || $datos['CantidadMaximaDias'] == "")
            $datos['CantidadMaximaDias'] = "NULL";

        if (!isset($datos['CantidadMaximaDiasMes']) || $datos['CantidadMaximaDiasMes'] == "")
            $datos['CantidadMaximaDiasMes'] = "NULL";

        if (!isset($datos['DiasHabiles']) || $datos['DiasHabiles'] == "")
            $datos['DiasHabiles'] = "NULL";

        if (!isset($datos['IdRegimenSalarial']) || $datos['IdRegimenSalarial'] == "")
            $datos['IdRegimenSalarial'] = "NULL";
    }


    private function _ValidarDatosVacios($datos): bool {

        if (!isset($datos['Codigo']) || $datos['Codigo'] == "") {
            $this->setError(400, 'Debe ingresar un Código de Articulo');
            return false;
        }

        if (!isset($datos['BajaLiquidacion']) || $datos['BajaLiquidacion'] == "") {
            $this->setError(400, 'Debe indicar si el artículo liquida');
            return false;
        }


        if (!isset($datos['Descripcion']) || $datos['Descripcion'] == "") {
            $this->setError(400, 'Debe ingresar una Descripción');
            return false;
        }

        if (!isset($datos['IdMotivo']) || $datos['IdMotivo'] == "") {
            $this->setError(400, 'Debe ingresar un Motivo');
            return false;
        }

        if (!FuncionesPHPLocal::ValidarContenido($this->conexion, $datos['IdMotivo'], "NumericoEntero")) {
            $this->setError(400, 'Error el motivo ingresado no es válido');
            return false;
        }

        if (!empty($datos['CantidadMaximaDias'])) {
            if (!FuncionesPHPLocal::ValidarContenido($this->conexion, $datos['CantidadMaximaDias'], "NumericoEntero")) {
                $this->setError(400, 'Error la cantidad máxima de días por año debe ser numérica');
                return false;
            }
        }

        if (!empty($datos['CantidadMaximaDiasMes'])) {
            if (!FuncionesPHPLocal::ValidarContenido($this->conexion, $datos['CantidadMaximaDiasMes'], "NumericoEntero")) {
                $this->setError(400, 'Error la cantidad máxima de días por mes debe ser numérica');
                return false;
            }
        }

        if (!isset($datos['ConGoceSueldo']) || $datos['ConGoceSueldo'] === "") {
            $this->setError(400, 'Debe indicar si es con goce de sueldo');
            return false;
        }

        if (!isset($datos['PermiteOtroOrganismo']) || $datos['PermiteOtroOrganismo'] === "") {
            $this->setError(400, 'Debe indicar si permite otro organismo');
            return false;
        }

        if (!isset($datos['IgnoraCargos']) || $datos['IgnoraCargos'] === "") {
            $this->setError(400, 'Debe indicar si permite la validacion por inconsistencias');
            return false;
        }

        if (!isset($datos['EsAnual']) || $datos['EsAnual'] === "") {
            $this->setError(400, 'Debe indicar si es anual');
            return false;
        }

        if (!isset($datos['EsAuxiliar']) || $datos['EsAuxiliar'] === "") {
            $this->setError(400, 'Debe indicar si es auxiliar');
            return false;
        }

        return true;
    }

}
