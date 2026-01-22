<?php
require_once DIR_CLASES_DB . "cAreasRoles.db.php";

class cAreasRoles extends cAreasRolesdb {
    /**
     * Constructor de la clase cAreasRoles.
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
     * Destructor de la clase cAreasRoles.
     */
    function __destruct() {
        parent::__destruct();
    }

    /**
     * @inheritDoc
     */
    public function BuscarxRol($datos, &$resultado, &$numfilas): bool {
        if (!parent::BuscarxRol($datos, $resultado, $numfilas))
            return false;
        return true;
    }

    /**
     * @inheritDoc
     */
    public function buscarxAreaRol(array $datos, &$resultado, ?int &$numfilas): bool {
        return parent::buscarxAreaRol($datos, $resultado, $numfilas);
    }

    /**
     * @inheritDoc
     */
    public function insertar(array $datos, ?int &$codigoInsertado): bool {
        if (!$this->_validarInsertar($datos))
            return false;
        self::setearFechas($datos);
        return parent::insertar($datos, $codigoInsertado);
    }

    /**
     * @inheritDoc
     */
    public function eliminar(array $datos): bool {
        if (!$this->_validarEliminar($datos))
            return false;

        return parent:: eliminar($datos);
    }

    /**
     * @param array $datos
     *
     * @return bool
     */
    private function _validarInsertar(array $datos): bool {
        if (!$this->_validarDatosVacios($datos))
            return false;

        if (!$this->buscarxAreaRol($datos, $resultado, $numfilas))
            return false;

        if ($numfilas > 0) {
            $this->setError(400, 'El area ya esta asignada al rol');
            return false;
        }

        return true;
    }

    /**
     * @param array $datos
     *
     * @return bool
     */
    private function _validarEliminar(array $datos): bool {
        if (!$this->_validarDatosVacios($datos))
            return false;

        if (!$this->buscarxAreaRol($datos, $resultado, $numfilas))
            return false;
        if ($numfilas != 1) {
            $this->setError(400, 'No existe el registro');
            return false;
        }

        return true;
    }

    /**
     * @param $datos
     *
     * @return bool
     */
    private function _validarDatosVacios($datos): bool {
        if (FuncionesPHPLocal::isEmpty($datos['IdArea'])) {
            $this->setError(400, 'Debe ingresar un area');
            return false;
        }

        if (FuncionesPHPLocal::isEmpty($datos['IdRol'])) {
            $this->setError(400, 'Debe ingresar un rol');
            return false;
        }

        return true;
    }

    /**
     * @param array $datos
     */
    private static function setearFechas(array &$datos): void {
        $datos['AltaUsuario'] = $datos['UltimaModificacionUsuario'] = $_SESSION['usuariocod'];
        $datos['AltaFecha'] = $datos['UltimaModificacionFecha'] = date('Y-m-d H:i:s');
    }
}


