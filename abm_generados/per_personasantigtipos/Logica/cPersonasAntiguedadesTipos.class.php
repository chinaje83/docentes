<?php

include(DIR_CLASES_DB . "cPersonasAntiguedadesTipos.db.php");

class cPersonasAntiguedadesTipos extends cPersonasAntiguedadesTiposDB {
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
        $sparam = [            'xIdAntiguedadTipo' => 0,
            'IdAntiguedadTipo' => '',
            'xNombre' => 0,
            'Nombre' => '',
            'xEstado' => 0,
            'Estado' => '',
            'xAltaFecha' => 0,
            'AltaFecha' => '',
            'xAltaUsuario' => 0,
            'AltaUsuario' => '',
            'xUltimaModificacionesFecha' => 0,
            'UltimaModificacionesFecha' => '',
            'xUltimaModificacionUsuario' => 0,
            'UltimaModificacionUsuario' => '',
            'xSoloLiquidacion' => 0,
            'SoloLiquidacion' => '',
            'limit' => '',
            'orderby' => 'IdAntiguedadTipo DESC',
        ];
        if (isset($datos['IdAntiguedadTipo']) && $datos['IdAntiguedadTipo'] !== '') {
            $sparam['IdAntiguedadTipo'] = $datos['IdAntiguedadTipo'];
            $sparam['xIdAntiguedadTipo'] = 1;
        }
        if (isset($datos['Nombre']) && $datos['Nombre'] !== '') {
            $sparam['Nombre'] = $datos['Nombre'];
            $sparam['xNombre'] = 1;
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
        if (isset($datos['UltimaModificacionesFecha']) && $datos['UltimaModificacionesFecha'] !== '') {
            $sparam['UltimaModificacionesFecha'] = $datos['UltimaModificacionesFecha'];
            $sparam['xUltimaModificacionesFecha'] = 1;
        }
        if (isset($datos['UltimaModificacionUsuario']) && $datos['UltimaModificacionUsuario'] !== '') {
            $sparam['UltimaModificacionUsuario'] = $datos['UltimaModificacionUsuario'];
            $sparam['xUltimaModificacionUsuario'] = 1;
        }
        if (isset($datos['SoloLiquidacion']) && $datos['SoloLiquidacion'] !== '') {
            $sparam['SoloLiquidacion'] = $datos['SoloLiquidacion'];
            $sparam['xSoloLiquidacion'] = 1;
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
        if (!isset($datos['Nombre']) || $datos['Nombre'] === '') {
            $this->setError('Nombre', 'El campo Nombre es obligatorio.');
            return false;
        }
    }

    private function _ValidarModificar($datos): bool {
        if (!isset($datos['IdAntiguedadTipo']) || $datos['IdAntiguedadTipo'] === '') {
            $this->setError('IdAntiguedadTipo', 'El codigo es obligatorio.');
            return false;
        }
        if (!isset($datos['Nombre']) || $datos['Nombre'] === '') {
            $this->setError('Nombre', 'El campo Nombre es obligatorio.');
            return false;
        }
    }

    private function _SetearNull(&$datos): void {
        if (array_key_exists('IdAntiguedadTipo', $datos) && $datos['IdAntiguedadTipo'] === '') {
            $datos['IdAntiguedadTipo'] = null;
        }
        if (array_key_exists('Nombre', $datos) && $datos['Nombre'] === '') {
            $datos['Nombre'] = null;
        }
        if (array_key_exists('Estado', $datos) && $datos['Estado'] === '') {
            $datos['Estado'] = null;
        }
        if (array_key_exists('AltaFecha', $datos) && $datos['AltaFecha'] === '') {
            $datos['AltaFecha'] = null;
        }
        if (array_key_exists('AltaUsuario', $datos) && $datos['AltaUsuario'] === '') {
            $datos['AltaUsuario'] = null;
        }
        if (array_key_exists('UltimaModificacionesFecha', $datos) && $datos['UltimaModificacionesFecha'] === '') {
            $datos['UltimaModificacionesFecha'] = null;
        }
        if (array_key_exists('UltimaModificacionUsuario', $datos) && $datos['UltimaModificacionUsuario'] === '') {
            $datos['UltimaModificacionUsuario'] = null;
        }
        if (array_key_exists('SoloLiquidacion', $datos) && $datos['SoloLiquidacion'] === '') {
            $datos['SoloLiquidacion'] = null;
        }
    }
}