<?php
include(DIR_CLASES_DB . "cDeclaracionJuradaTipos.db.php");

class cDeclaracionJuradaTipos extends cDeclaracionJuradaTiposDB
{
    function __construct(accesoBDLocal $conexion, $formato = FMT_TEXTO)
    {
        parent::__construct($conexion, $formato);
    }

    function __destruct()
    {
        parent::__destruct();
    }

    /**
     * Devuelve el mensaje de error almacenado
     *
     * @return array
     */
    public function getError(): array
    {
        return $this->error;
    }


    public function BuscarxTipo($datos, &$resultado, &$numfilas): bool
    {
        if (!parent::BuscarxTipo($datos, $resultado, $numfilas))
            return false;

        return true;
    }
}




