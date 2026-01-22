<?php

use Bigtree\ExcepcionLogica;

include(DIR_CLASES_DB . "cEscuelasPuestosTemporalidades.db.php");

class cEscuelasPuestosTemporalidades extends cEscuelasPuestosTemporalidadesdb {
    use ManejoErrores;
    use Validaciones;

    /**
     * @var Elastic\Conexion
     */
    private $conexionES;
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
    public function __construct(accesoBDLocal $conexion, ?Elastic\Conexion $conexionES = null, $formato = FMT_ARRAY) {
        $this->conexionES =& $conexionES;
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


    /**
     * @param array    $datos
     * @param          $resultado
     * @param int|null $numfilas
     *
     * @return bool
     */


    public function BuscarxCodigo($datos, &$resultado, &$numfilas): bool {
        if (!parent::BuscarxCodigo($datos, $resultado, $numfilas))
            return false;
        return true;
    }

    public function BusquedaAvanzada($datos, &$resultado, &$numfilas): bool {
        $sparam = array(
            'xEstado' => 0,
            'Estado' => "-1",
            'limit' => '',
            'orderby' => "Id DESC"
        );

        if (isset($datos['Estado']) && $datos['Estado'] != "") {
            $sparam['Estado'] = $datos['Estado'];
            $sparam['xEstado'] = 1;
        }

        if (isset($datos['orderby']) && $datos['orderby'] != "")
            $sparam['orderby'] = $datos['orderby'];

        if (isset($datos['limit']) && $datos['limit'] != "")
            $sparam['limit'] = $datos['limit'];

        if (!parent::BusquedaAvanzada($sparam, $resultado, $numfilas))
            return false;
        return true;
    }

}
