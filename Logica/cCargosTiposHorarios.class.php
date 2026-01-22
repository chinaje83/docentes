<?php

include(DIR_CLASES_DB . "cCargosTiposHorarios.db.php");

class cCargosTiposHorarios extends cCargosTiposHorariosdb {
    /**
     * Constructor de la clase cCargosTipos.
     *
     * Recibe un objeto accesoBDLocal y el formato a de  los mensajes de salida
     * $formato = FMT_TEXTO escribe en pantalla una caja con el mensaje de error, el tipo de caja depende del nivel de error
     *            FMT_ARRAY escribe el mensaje de error en la propiedad $error de la clase la cual puede ser accedida desde el método getError()
     *            otros escribe en pantalla el mensaje en texto plano
     *
     * @param accesoBDLocal $conexion
     * @param mixed         $formato
     */
    function __construct(accesoBDLocal $conexion, $formato = FMT_TEXTO) {
        parent::__construct($conexion, $formato);
    }

    /**
     * Destructor de la clase cCargosTipos.
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


    public function BusquedaAvanzada($datos, &$resultado, &$numfilas): bool {
        $sparam = [
            'xIdTipo' => 0,
            'IdTipo' => "",
            'xNombre' => 0,
            'Nombre' => "",
            'xEstado' => 0,
            'Estado' => "-1",
            'limit' => '',
            'orderby' => "IdTipo ASC",
        ];
        if (isset($datos['IdTipo']) && $datos['IdTipo'] != "") {
            $sparam['IdTipo'] = $datos['IdTipo'];
            $sparam['xIdTipo'] = 1;
        }
        if (isset($datos['Nombre']) && $datos['Nombre'] != "") {
            $sparam['Nombre'] = $datos['Nombre'];
            $sparam['xNombre'] = 1;
        }
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


    public function BuscarAuditoriaRapida($datos, &$resultado, &$numfilas): bool {
        if (!parent::BuscarAuditoriaRapida($datos, $resultado, $numfilas))
            return false;
        return true;
    }

    public function buscarCombo(&$resultado, &$numfilas): bool {
        return parent::buscarCombo($resultado, $numfilas);
    }





}
