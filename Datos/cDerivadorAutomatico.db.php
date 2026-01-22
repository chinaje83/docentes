<?php

namespace Bigtree\Datos;

use accesoBDLocal;
use ManejoErrores;

abstract class DerivadorAutomatico {
    use ManejoErrores;

    /** @var accesoBDLocal */
    protected $conexion;
    /** @var int */
    protected $formato;

    protected $campos = [];

    /**
     * @param accesoBDLocal $conexion
     * @param int           $formato
     */
    protected function __construct(accesoBDLocal $conexion, int $formato) {
        $this->conexion =& $conexion;
        $this->formato = $formato;
        if (file_exists(PUBLICA . '/json/campos_derivador.json'))
            $this->campos = json_decode(file_get_contents(PUBLICA . '/json/campos_derivador.json'), true);
    }

    /**
     * Destructor de la clase
     */
    protected function __destruct() {
    }

    /**
     * @param $resultado
     * @param $numfilas
     *
     * @return bool
     */
    protected function OperacionesSPResult(&$resultado, &$numfilas): bool {
        $spnombre = 'sel_Operaciones_combo_Nombre';
        $sparam = ['pEstado' => ACTIVO];

        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            $this->setError(400, 'Error al buscar operaciones. ');
            return false;
        }
        return true;
    }

    /**
     * @param array    $datos
     * @param          $resultado
     * @param int|null $numfilas
     *
     * @return bool
     */
    protected function buscarxCodigo(array $datos, &$resultado, ?int &$numfilas): bool {
        $spnombre = 'sel_DerivadorAutomatico_xId';
        $sparam = array(
            'pId' => $datos['Id']
        );
        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            $this->setError(400, 'Error al buscar al buscar por codigo.');
            return false;
        }
        return true;
    }

    /**
     * @param array    $datos
     * @param          $resultado
     * @param int|null $numfilas
     *
     * @return bool
     */
    protected function buscarPorCircuito(array $datos, &$resultado, ?int &$numfilas): bool {
        $spnombre = 'sel_DerivadorAutomatico_xIdCircuito';
        $sparam = array(
            'pIdCircuito' => $datos['IdCircuito']
        );
        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            $this->setError(400, 'Error al buscar al buscar por circuito.');
            return false;
        }
        return true;
    }

    /**
     * @param array    $datos
     * @param int|null $codigoInsertado
     *
     * @return bool
     */
    protected function insertar(array $datos, ?int &$codigoInsertado): bool {
        $spnombre = 'ins_DerivadorAutomatico';
        $sparam = [
            'pIdCircuito' => $datos['IdCircuito'],
            'pIdEstadDestinoAfirmativo' => $datos['IdEstadDestinoAfirmativo'],
            'pIdEstadoDestinoNegativo' => $datos['IdEstadoDestinoNegativo'],
            'pIdOperacion' => $datos['IdOperacion'],
            'pCampoBD' => $datos['CampoBD'],
            'pCampoElastic' => $datos['CampoElastic'],
            'pEstado' => $datos['Estado'],
            'pAltaFecha' => $datos['AltaFecha'],
            'pAltaUsuario' => $datos['AltaUsuario'],
            'pUltimaModificacionFecha' => $datos['UltimaModificacionFecha'],
            'pUltimaModificacionUsuario' => $datos['UltimaModificacionUsuario'],
        ];

        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            $this->setError(500, 'Error al insertar');
            return false;
        }
        $codigoInsertado = $this->conexion->UltimoCodigoInsertado();
        return true;
    }

    /**
     * @param array $datos
     *
     * @return bool
     */
    protected function modificar(array $datos): bool {
        $spnombre = 'upd_DerivadorAutomatico_xId';
        $sparam = [
            'pIdCircuito' => $datos['IdCircuito'],
            'pIdEstadDestinoAfirmativo' => $datos['IdEstadDestinoAfirmativo'],
            'pIdEstadoDestinoNegativo' => $datos['IdEstadoDestinoNegativo'],
            'pIdOperacion' => $datos['IdOperacion'],
            'pCampoBD' => $datos['CampoBD'],
            'pCampoElastic' => $datos['CampoElastic'],
            'pUltimaModificacionFecha' => $datos['UltimaModificacionFecha'],
            'pUltimaModificacionUsuario' => $datos['UltimaModificacionUsuario'],
            'pId' => $datos['Id'],
        ];

        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            $this->setError(500, 'Error al modificar');
            return false;
        }
        return true;
    }

    /**
     * @param array $datos
     *
     * @return bool
     */
    protected function cambiarEstado(array $datos): bool {
        $spnombre = 'upd_DerivadorAutomatico_Estado_xId';
        $sparam = [
            'pEstado' => $datos['Estado'],
            'pUltimaModificacionFecha' => $datos['UltimaModificacionFecha'],
            'pUltimaModificacionUsuario' => $datos['UltimaModificacionUsuario'],
            'pId' => $datos['Id'],
        ];

        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            $this->setError(500, 'Error al eliminar');
            return false;
        }
        return true;
    }

    /**
     * @param array $datos
     *
     * @return bool
     */
    protected abstract function _validarInsertar(array $datos): bool;

    /**
     * @param array      $datos
     * @param array|null $datosRegistro
     *
     * @return bool
     */
    protected abstract function _validarModificar(array $datos, ?array &$datosRegistro): bool;

    /**
     * @param array $datos
     *
     * @return bool
     */
    protected abstract function _validarDatosVacios(array $datos): bool;

    /**
     * @param array $datos
     */
    protected abstract static function setearNull(array &$datos): void;
}