<?php

use Bigtree\ExcepcionLogica;

include(DIR_CLASES_DB . "cFuncionesCargo.db.php");

class cFuncionesCargo extends cFuncionesCargodb {
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

    public function BusquedaAvanzada($datos,&$resultado,&$numfilas): bool
    {
        $sparam=array(
            'xIdFuncionCargo'=> 0,
            'IdFuncionCargo'=> "",
            'xCodigo'=> 0,
            'Codigo'=> "",
            'xNombre'=> 0,
            'Nombre'=> "",
            'xIdExterno'=> 0,
            'IdExterno'=> "",
            'xEstado'=> 0,
            'Estado'=> "-1",
            'limit'=> '',
            'orderby'=> "IdFuncionCargo ASC"
        );
        if (isset($datos['IdFuncionCargo']) && $datos['IdFuncionCargo'] != "")
        {
            $sparam['IdFuncionCargo'] = $datos['IdFuncionCargo'];
            $sparam['xIdFuncionCargo'] = 1;
        }
        if (isset($datos['Codigo']) && $datos['Codigo'] != "")
        {
            $sparam['Codigo'] = $datos['Codigo'];
            $sparam['xCodigo'] = 1;
        }

        if (isset($datos['Nombre']) && $datos['Nombre'] != "")
        {
            $sparam['Nombre'] = utf8_decode($datos['Nombre']);
            $sparam['xNombre'] = 1;
        }
        if (isset($datos['xIdExterno']) && $datos['xIdExterno'] != "")
        {
            $sparam['xIdExterno'] = utf8_decode($datos['xIdExterno']);
            $sparam['xIdExterno'] = 1;
        }

        if(isset($datos['Estado']) && $datos['Estado']!="")
        {
            $sparam['Estado']= $datos['Estado'];
            $sparam['xEstado']= 1;
        }

        if(isset($datos['orderby']) && $datos['orderby']!="")
            $sparam['orderby']= $datos['orderby'];
        if(isset($datos['limit']) && $datos['limit']!="")
            $sparam['limit']= $datos['limit'];
        if (!parent::BusquedaAvanzada($sparam,$resultado,$numfilas))
            return false;
        return true;
    }

}
