<?php

/**
 * Class cEscuelasPuestosEstadoDB
 */
abstract class cEscuelasPuestosEstadoDB {

    use ManejoErrores;
    /** @var accesoBDLocal */
    protected $conexion;
    /** @var mixed */
    protected $formato;

    /**
     * Constructor de la clase cEscuelasPuestosEstadoDB.
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
     * Destructor de la clase cEscuelasPuestosPersonasEstadosDB
     */
    function __destruct() {}

    protected function buscarCombo(&$resultado, ?int &$numfilas): bool {

        $spnombre = "sel_EscuelasPuestosEstados_xCombo";
        $sparam = [];

        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            $this->setError(400,"Error al buscar al buscar combo");
            return false;
        }

        return true;
    }
}