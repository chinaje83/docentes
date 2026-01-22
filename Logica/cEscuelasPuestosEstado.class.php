<?php
include(DIR_CLASES_DB . 'cEscuelasPuestosEstado.db.php');

/**
 * Class cEscuelasPuestosEstado
 */
class cEscuelasPuestosEstado extends cEscuelasPuestosEstadoDB {

    /**
     * Constructor de la clase cEscuelasPuestosEstado
     *
     * Recibe un objeto accesoBDLocal y el formato a de  los mensajes de salida
     * $formato = FMT_TEXTO escribe en pantalla una caja con el mensaje de error, el tipo de caja depende del nivel de error
     *            FMT_ARRAY escribe el mensaje de error en la propiedad $error de la clase la cual puede ser accedida desde el mÃ©todo getError()
     *            otros escribe en pantalla el mensaje en texto plano
     *
     * @param accesoBDLocal $conexion
     * @param mixed         $formato
     */
    function __construct(accesoBDLocal $conexion, $formato = FMT_TEXTO) {
        parent::__construct($conexion, $formato);
    }

    /**
     * Destructor de la clase cEscuelasPuestosPersonasEstados.
     */
    function __destruct() {
        parent::__destruct();
    }

    public function buscarCombo(&$resultado, &$numfilas): bool {
        return parent::buscarCombo($resultado, $numfilas);
    }
}