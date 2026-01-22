<?php
include(DIR_CLASES_DB."cJornadas.db.php");

class cJornadas extends cJornadasdb
{

    function __construct(accesoBDLocal $conexion,$formato=FMT_TEXTO){
        parent::__construct($conexion,$formato);
    }

    function __destruct(){
        parent::__destruct();
    }

    public function getError(): array {
        return $this->error;
    }

    public function BuscarxCodigo($datos, &$resultado,&$numfilas): bool
    {
        if (!parent::BuscarxCodigo($datos,$resultado,$numfilas))
            return false;
        return true;
    }

    public function BuscarListado($datos, &$resultado,&$numfilas): bool
    {
        if (!parent::BuscarListado($datos, $resultado,$numfilas))
            return false;
        return true;
    }

}
