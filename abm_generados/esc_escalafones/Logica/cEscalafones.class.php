<?php

include(DIR_CLASES_DB . "cEscalafones.db.php");

class cEscalafones extends cEscalafonesDB {
    function __construct(accesoBDLocal $conexion, $formato = FMT_TEXTO) {
        parent::__construct($conexion, $formato);
    }

    function __destruct() {
        parent::__destruct();
    }

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
        $sparam = [            'xIdEscalafon' => 0,
            'IdEscalafon' => '',
            'xIdEscalafonExterno' => 0,
            'IdEscalafonExterno' => '',
            'xNombre' => 0,
            'Nombre' => '',
            'xDescripcion' => 0,
            'Descripcion' => '',
            'xIdRegimenSalarial' => 0,
            'IdRegimenSalarial' => '',
            'xEstado' => 0,
            'Estado' => '',
            'xAltaFecha' => 0,
            'AltaFecha' => '',
            'xAltaUsuario' => 0,
            'AltaUsuario' => '',
            'xUltimaModificacionUsuario' => 0,
            'UltimaModificacionUsuario' => '',
            'xUltimaModificacionFecha' => 0,
            'UltimaModificacionFecha' => '',
            'limit' => '',
            'orderby' => 'IdEscalafon DESC',
        ];
        if (isset($datos['IdEscalafon']) && $datos['IdEscalafon'] !== '') {
            $sparam['IdEscalafon'] = $datos['IdEscalafon'];
            $sparam['xIdEscalafon'] = 1;
        }
        if (isset($datos['IdEscalafonExterno']) && $datos['IdEscalafonExterno'] !== '') {
            $sparam['IdEscalafonExterno'] = $datos['IdEscalafonExterno'];
            $sparam['xIdEscalafonExterno'] = 1;
        }
        if (isset($datos['Nombre']) && $datos['Nombre'] !== '') {
            $sparam['Nombre'] = $datos['Nombre'];
            $sparam['xNombre'] = 1;
        }
        if (isset($datos['Descripcion']) && $datos['Descripcion'] !== '') {
            $sparam['Descripcion'] = $datos['Descripcion'];
            $sparam['xDescripcion'] = 1;
        }
        if (isset($datos['IdRegimenSalarial']) && $datos['IdRegimenSalarial'] !== '') {
            $sparam['IdRegimenSalarial'] = $datos['IdRegimenSalarial'];
            $sparam['xIdRegimenSalarial'] = 1;
        }
        if (isset($datos['Estado']) && $datos['Estado'] !== '') {
            $sparam['Estado'] = $datos['Estado'];
            $sparam['xEstado'] = 1;
        }
        if (isset($datos['AltaFecha']) && $datos['AltaFecha'] !== '') {
            $sparam['AltaFecha'] = $datos['AltaFecha'];
            $sparam['xAltaFecha'] = 1;
        }
        if (isset($datos['AltaUsuario']) && $datos['AltaUsuario'] !== '') {
            $sparam['AltaUsuario'] = $datos['AltaUsuario'];
            $sparam['xAltaUsuario'] = 1;
        }
        if (isset($datos['UltimaModificacionUsuario']) && $datos['UltimaModificacionUsuario'] !== '') {
            $sparam['UltimaModificacionUsuario'] = $datos['UltimaModificacionUsuario'];
            $sparam['xUltimaModificacionUsuario'] = 1;
        }
        if (isset($datos['UltimaModificacionFecha']) && $datos['UltimaModificacionFecha'] !== '') {
            $sparam['UltimaModificacionFecha'] = $datos['UltimaModificacionFecha'];
            $sparam['xUltimaModificacionFecha'] = 1;
        }
        if (isset($datos['orderby']) && $datos['orderby'] !== '') {
            $sparam['orderby'] = $datos['orderby'];
        }
        if (isset($datos['limit']) && $datos['limit'] !== '') {
            $sparam['limit'] = $datos['limit'];
        }
        if (!parent::BusquedaAvanzada($sparam, $resultado, $numfilas)) {
            return false;
        }
        return true;
    }

    public function Insertar($datos, &$codigoInsertado): bool {
        if (!$this->_ValidarInsertar($datos)) {
            return false;
        }
        $this->_SetearNull($datos);        $datos['AltaFecha'] = date('Y-m-d H:i:s');
        $datos['AltaUsuario'] = $_SESSION['usuariocod'];
        $datos['UltimaModificacionUsuario'] = $_SESSION['usuariocod'];
        $datos['UltimaModificacionFecha'] = date('Y-m-d H:i:s');
        $datos['Estado'] = ACTIVO;
        if (!parent::Insertar($datos, $codigoInsertado)) {
            return false;
        }
        return true;
    }

    public function Modificar($datos): bool {
        if (!$this->_ValidarModificar($datos)) {
            return false;
        }
        $this->_SetearNull($datos);        $datos['UltimaModificacionUsuario'] = $_SESSION['usuariocod'];
        $datos['UltimaModificacionFecha'] = date('Y-m-d H:i:s');
        if (!parent::Modificar($datos)) {
            return false;
        }
        return true;
    }

    public function Eliminar($datos): bool {
        if (!parent::Eliminar($datos)) {
            return false;
        }
        return true;
    }
    public function Activar($datos): bool {
        $datos['Estado'] = ACTIVO;
        if (!parent::ModificarEstado($datos)) {
            return false;
        }
        return true;
    }

    public function DesActivar($datos): bool {
        $datos['Estado'] = NOACTIVO;
        if (!parent::ModificarEstado($datos)) {
            return false;
        }
        return true;
    }
    private function _ValidarInsertar($datos): bool {
Array
    }

    private function _ValidarModificar($datos): bool {
        if (!isset($datos['IdEscalafon']) || $datos['IdEscalafon'] === '') {
            $this->setError('IdEscalafon', 'El codigo es obligatorio.');
            return false;
        }
Array
    }

    private function _SetearNull(&$datos): void {
Array
    }
}