<?php

abstract class cAreasRolesdb {
    use ManejoErrores;

    /** @var accesoBDLocal */
    protected $conexion;
    /** @var mixed */
    protected $formato;
    /** @var array */
    protected $error;

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
    function __construct(accesoBDLocal $conexion, $formato) {
        $this->conexion = &$conexion;
        $this->formato = &$formato;
    }

    /**
     * Destructor de la clase cAreasRoles.
     */
    function __destruct() {
    }

    /**
     * @param array    $datos
     * @param          $resultado
     * @param int|null $numfilas
     *
     * @return bool
     */
    protected function BuscarxRol(array $datos, &$resultado, ?int &$numfilas): bool {
        $spnombre = 'sel_AreasRoles_xIdRol';
        $sparam = array(
            'pIdRol' => $datos['IdRol']
        );

        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            $this->setError(400, "Error al buscar por rol. ");
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
    protected function buscarxAreaRol(array $datos, &$resultado, ?int &$numfilas): bool {
        $spnombre = 'sel_AreasRoles_xIdArea_IdRol';
        $sparam = [
            'pIdRol' => $datos['IdRol'],
            'pIdArea' => $datos['IdArea'],
        ];

        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            $this->setError(400, 'Error al buscar por area y rol. ');
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
        $spnombre = 'ins_AreasRoles';
        $sparam = [
            'pIdRol' => $datos['IdRol'],
            'pIdArea' => $datos['IdArea'],
            'pAltaUsuario' => $datos['AltaUsuario'],
            'pAltaFecha' => $datos['AltaFecha'],
            'pUltimaModificacionUsuario' => $datos['UltimaModificacionUsuario'],
            'pUltimaModificacionFecha' => $datos['UltimaModificacionFecha'],
        ];

        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            $this->setError(400, 'Error al insertar. ');
            return false;
        }

        return true;
    }

    /**
     * @param array $datos
     *
     * @return bool
     */
    protected function eliminar(array $datos): bool {
        $spnombre = 'del_AreasRoles_xIdArea_IdRol';
        $sparam = [
            'pIdRol' => $datos['IdRol'],
            'pIdArea' => $datos['IdArea'],
        ];

        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            $this->setError(400, 'Error al eliminar. ');
            return false;
        }

        return true;
    }
}