<?php

include(DIR_CLASES_DB . "cExtensionSuplenciaTipo.db.php");

class cExtensionSuplenciaTipo extends cExtensionSuplenciaTipodb {
    use ManejoErrores;
    use Validaciones;

    /**
     * Constructor de la clase cEscuelasPuestos.
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


    function __destruct() {
        parent::__destruct();
    }

    public function BusquedaxNivelxTipoCargo($datos, &$resultado, &$numfilas) {
        if (!parent::BusquedaxNivelxTipoCargo($datos, $resultado, $numfilas))
            return false;
        return true;
    }



//-----------------------------------------------------------------------------------------
//FUNCIONES PRIVADAS
//-----------------------------------------------------------------------------------------

}
