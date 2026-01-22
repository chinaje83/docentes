<?php
include(DIR_CLASES_DB . "cRegimenSalarial.db.php");

class cRegimenSalarial extends cRegimenSalarialDB {

    function __construct(accesoBDLocal $conexion, $formato = FMT_TEXTO) {
        parent::__construct($conexion, $formato);
    }

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

    public function BuscarListado($datos, &$resultado, &$numfilas): bool {
        if (!parent::BuscarListado($datos, $resultado, $numfilas))
            return false;
        return true;
    }

    public function BuscarxCodigo($datos,&$resultado,&$numfilas)
    {
        if (!parent::BuscarxCodigo($datos,$resultado,$numfilas))
            return false;
        return true;
    }
}
