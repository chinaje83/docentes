<?php

use Bigtree\ExcepcionLogica;

include(DIR_CLASES_DB . "cEscuelasPuestosExtendida.db.php");

class cEscuelasPuestosExtendida extends cEscuelasPuestosExtendidadb {
    use ManejoErrores;
    use Validaciones;

    /**
     * Constructor de la clase cEscuelasPuestosExtendida.
     *
     * Recibe un objeto accesoBDLocal y el formato a de  los mensajes de salida
     * $formato = FMT_TEXTO escribe en pantalla una caja con el mensaje de error, el tipo de caja depende del nivel de error
     *            FMT_ARRAY escribe el mensaje de error en la propiedad $error de la clase la cual puede ser accedida desde el método getError()
     *            otros escribe en pantalla el mensaje en texto plano
     *
     * @param accesoBDLocal $conexion
     * @param mixed         $formato
     */
    /**
     * @inheritDoc
     */
    public function __construct(accesoBDLocal $conexion, $formato = FMT_ARRAY) {
        parent::__construct($conexion, $formato);
    }

    /**
     * Destructor de la clase cEscuelasPuestos.
     */
    function __destruct() {
        parent::__destruct();
    }

    /**
     * Devuelve el mensaje de error almacenado
     *
     * @return array
     */
    public function getError(): array {
        return $this->error;
    }



    public function BuscarxCodigo($datos, &$resultado, &$numfilas): bool {
        if (!parent::BuscarxCodigo($datos, $resultado, $numfilas))
            return false;
        return true;
    }



}
