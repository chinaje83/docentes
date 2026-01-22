<?php

namespace Bigtree\Logica;

include (DIR_CLASES_DB.'cReporteEscuelas.db.php');

use accesoBDLocal;
use Bigtree\Datos\ReporteEscuelasDB as ReporteEscuelasDB;
use Validaciones;

class cReporteEscuelas extends ReporteEscuelasDB
{

    use Validaciones;

    /** Constructor de la clase
     * @param accesoBDLocal $conexion
     * @param mixed $formato
     */

    function __construct(accesoBDLocal $conexion, $formato = FMT_TEXTO)
    {
        parent::__construct($conexion, $formato);
    }

    /**
     * Destructor de la clase
     */
    function __destruct()
    {
        parent::__destruct();
    }

    public function InsertarLog($datos, &$codigoInsertado): bool {
        return parent::InsertarLog($datos, $codigoInsertado);
    }

    public function InsertarLogReporte($datos, &$codigoInsertado): bool {
        return parent::InsertarLogReporte($datos, $codigoInsertado);
    }

}
