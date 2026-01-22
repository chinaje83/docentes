<?php

namespace Bigtree\Logica;

use accesoBDLocal;
use Bigtree\Datos\DerivadorAutomatico as DerivadorAutomaticoDB;
use Bigtree\ExcepcionLogica;
use FuncionesPHPLocal;
use Validaciones;

require_once DIR_CLASES_DB . 'cDerivadorAutomatico.db.php';

class DerivadorAutomatico extends DerivadorAutomaticoDB {
    use Validaciones;

    /**
     * @inheritDoc
     */
    public function __construct(accesoBDLocal $conexion, int $formato = FMT_ARRAY) {
        parent::__construct($conexion, $formato);
    }

    /**
     * @inheritDoc
     */
    public function __destruct() {
        parent::__destruct();
    }

    /**
     * @return array
     */
    public function getCampos(): array {
        return $this->campos;
    }

    /**
     * @param string $id
     *
     * @return array
     * @throws \Bigtree\ExcepcionLogica
     */
    public function getCampo(string $id): array {
        if (isset($this->campos[$id]))
            return $this->campos[$id];
        throw new ExcepcionLogica('No existe el campo');
    }

    /**
     * @inheritDoc
     */
    public function OperacionesSPResult(&$resultado, &$numfilas): bool {
        return parent::OperacionesSPResult($resultado, $numfilas);
    }

    /**
     * @inheritDoc
     */
    public function buscarxCodigo(array $datos, &$resultado, ?int &$numfilas): bool {
        return parent::buscarxCodigo($datos, $resultado, $numfilas);
    }

    /**
     * @inheritDoc
     */
    public function buscarPorCircuito(array $datos, &$resultado, ?int &$numfilas): bool {
        return parent::buscarPorCircuito($datos, $resultado, $numfilas);
    }

    /**
     * @inheritDoc
     */
    public function insertar(array $datos, ?int &$codigoInsertado): bool {
        if (!$this->_validarInsertar($datos))
            return false;

        self::setearNull($datos);

        return parent::insertar($datos, $codigoInsertado);
    }

    /**
     * @inheritDoc
     */
    public function modificar(array $datos): bool {
        if (!$this->_validarModificar($datos, $datosRegistro))
            return false;

        self::setearNull($datos);

        return parent::modificar($datos);
    }

    /**
     * @param array $datos
     *
     * @return bool
     */
    public function eliminar(array $datos): bool {
        $datos['Estado'] = ELIMINADO;
        return $this->cambiarEstado($datos);
    }

    /**
     * @inheritDoc
     */
    public function cambiarEstado(array $datos): bool {
        if (!$this->_validarExistencia($datos, $datosRegistro))
            return false;
        self::setearNull($datos);
        return parent::cambiarEstado($datos);
    }

    /**
     * @inheritDoc
     */
    protected function _validarInsertar(array $datos): bool {
        return $this->_validarDatosVacios($datos);
    }

    /**
     * @inheritDoc
     */
    protected function _validarModificar(array $datos, ?array &$datosRegistro): bool {
        if (!$this->_validarExistencia($datos, $datosRegistro))
            return false;

        return $this->_validarDatosVacios($datos);
    }

    /**
     * @inheritDoc
     */
    protected function _validarDatosVacios(array $datos): bool {

        if (FuncionesPHPLocal::isEmpty($datos['IdCircuito'])) {
            $this->setError(400, 'Debe seleccionar un circuito');
            return false;
        }

        if (FuncionesPHPLocal::isEmpty($datos['IdOperacion'])) {
            $this->setError(400, 'Debe seleccionar una operacion');
            return false;
        }

        if (FuncionesPHPLocal::isEmpty($datos['IdEstadDestinoAfirmativo'])) {
            $this->setError(400, 'Debe seleccionar un estado final si la operacion es afirmativa');
            return false;
        }

        if (FuncionesPHPLocal::isEmpty($datos['IdEstadoDestinoNegativo'])) {
            $this->setError(400, 'Debe seleccionar un estado final si la operacion es negativa');
            return false;
        }

        /*if (FuncionesPHPLocal::isEmpty($datos['CampoBD'])) {
            $this->setError(400, 'Debe seleccionar un campo en base relacional');
            return false;
        }*/

        /*if (FuncionesPHPLocal::isEmpty($datos['CampoElastic'])) {
            $this->setError(400, 'Debe seleccionar un campo en base documental');
            return false;
        }*/

        return true;
    }

    /**
     * @inheritDoc
     */
    protected static function setearNull(array &$datos): void {

        if (FuncionesPHPLocal::isEmpty($datos['IdCircuito']))
            $datos['IdCircuito'] = NULL;

        if (FuncionesPHPLocal::isEmpty($datos['IdOperacion']))
            $datos['IdOperacion'] = NULL;

        if (FuncionesPHPLocal::isEmpty($datos['IdEstadDestinoAfirmativo']))
            $datos['IdEstadDestinoAfirmativo'] = NULL;

        if (FuncionesPHPLocal::isEmpty($datos['IdEstadoDestinoNegativo']))
            $datos['IdEstadoDestinoNegativo'] = NULL;

        if (FuncionesPHPLocal::isEmpty($datos['CampoBD']))
            $datos['CampoBD'] = NULL;

        if (FuncionesPHPLocal::isEmpty($datos['CampoElastic']))
            $datos['CampoElastic'] = NULL;

        if (FuncionesPHPLocal::isEmpty($datos['Estado']))
            $datos['Estado'] = ACTIVO;

        $datos['UltimaModificacionFecha'] = $datos['AltaFecha'] = date('Y-m-d H:i:s');
        $datos['UltimaModificacionUsuario'] = $datos['AltaUsuario'] = $_SESSION['usuariocod'];
    }
}