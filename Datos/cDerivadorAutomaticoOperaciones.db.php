<?php

namespace Bigtree\Datos;

use accesoBDLocal;
use Bigtree\ExcepcionLogica;
use ManejoErrores;

abstract class DerivadorAutomaticoOperaciones {
    use ManejoErrores;

    /** @var accesoBDLocal */
    protected $conexion;
    /** @var int */
    protected $formato;
    /** @var array */
    protected $campos = [];

    /**
     * @param accesoBDLocal $conexion
     * @param int           $formato
     */
    protected function __construct(accesoBDLocal $conexion, int $formato) {
        $this->conexion =& $conexion;
        $this->formato = $formato;
        if (file_exists(PUBLICA . '/json/campos_derivador.json')) {
            $campos = json_decode(file_get_contents(PUBLICA . '/json/campos_derivador.json'), true);
            if (!empty($campos))
                $this->campos = $campos;
        }
    }

    /**
     * Destructor de la clase
     */
    protected function __destruct() {
    }

    /**
     * @param array    $datos
     * @param          $resultado
     * @param int|null $numfilas
     *
     * @return bool
     */
    protected function buscarxCodigo(array $datos, &$resultado, ?int &$numfilas): bool {
        $spnombre = 'sel_DerivadorAutomaticoOperaciones_xId';
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
    protected function buscarPorDerivador(array $datos, &$resultado, ?int &$numfilas): bool {
        $spnombre = 'sel_DerivadorAutomaticoOperaciones_xIdDerivador';
        $sparam = array(
            'pIdDerivador' => $datos['IdDerivador']
        );
        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            $this->setError(400, 'Error al buscar al buscar por derivador.');
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
        $spnombre = 'ins_DerivadorAutomaticoOperaciones';
        $sparam = [
            'pIdDerivador' => $datos['IdDerivador'],
            'pIdOperacion' => $datos['IdOperacion'],
            'pEstricto' => $datos['Estricto'],
            'pValorComparacion' => $datos['ValorComparacion'],
            'pCampoBD' => $datos['CampoBD'],
            'pCampoElastic' => $datos['CampoElastic'],
            'pOrden' => $datos['Orden'],
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
        $spnombre = 'upd_DerivadorAutomaticoOperaciones_xId';
        $sparam = [
            'pIdDerivador' => $datos['IdDerivador'],
            'pIdOperacion' => $datos['IdOperacion'],
            'pEstricto' => $datos['Estricto'],
            'pValorComparacion' => $datos['ValorComparacion'],
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
        $spnombre = 'upd_DerivadorAutomaticoOperaciones_Estado_xId';
        $sparam = [
            'pEstado' => $datos['Estado'],
            'pUltimaModificacionFecha' => $datos['UltimaModificacionFecha'],
            'pUltimaModificacionUsuario' => $datos['UltimaModificacionUsuario'],
            'pId' => $datos['Id'],
        ];

        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            $this->setError(500, 'Error al cambiar el estado');
            return false;
        }
        return true;
    }

    /**
     * @param array $datos
     *
     * @return bool
     */
    protected function modificarOrden(array $datos): bool {
        $spnombre = 'upd_DerivadorAutomaticoOperaciones_Orden_xId';
        $sparam = array(
            'pOrden' => $datos['Orden'],
            'pUltimaModificacionFecha' => $datos['UltimaModificacionFecha'],
            'pUltimaModificacionUsuario' => $datos['UltimaModificacionUsuario'],
            'pId' => $datos['Id']
        );
        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            $this->setError(500, 'Error al modificar el orden.');
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

    /**
     * @param array $datos
     *
     * @return int
     * @throws \Bigtree\ExcepcionLogica
     */
    protected function obtenerUltimoOrden(array $datos): int {
        $spnombre = 'sel_DerivadorAutomaticoOperaciones_UltimoOrden_xIdDerivador';
        $sparam = array(
            'pIdDerivador' => $datos['IdDerivador']
        );
        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno))
            throw new ExcepcionLogica('Error al buscar al buscar por circuito.');

        return $this->conexion->ObtenerSiguienteRegistro($resultado)['Orden'] ?: 0;
    }
}