<?php

abstract class cEscuelasPuestosTemporalidadesDB
{
    use ManejoErrores;

    /** @var accesoBDLocal */
    protected $conexion;
    /** @var mixed */
    protected $formato;
    /** @var array */
    protected $error;

    /**
     * Constructor de la clase cEscuelasPuestosDB.
     *
     * Recibe un objeto accesoBDLocal y el formato a de  los mensajes de salida
     * $formato = FMT_TEXTO escribe en pantalla una caja con el mensaje de error, el tipo de caja depende del nivel de error
     *            FMT_ARRAY escribe el mensaje de error en la propiedad $error de la clase la cual puede ser accedida desde el método getError()
     *            otros escribe en pantalla el mensaje en texto plano
     *
     * @param accesoBDLocal $conexion
     * @param mixed $formato
     */
    function __construct(accesoBDLocal $conexion, $formato)
    {

        $this->conexion = &$conexion;
        $this->formato = &$formato;
    }

    /**
     * Destructor de la clase cEscuelasPuestosDB.
     */
    function __destruct()
    {
    }

    /**
     * Devuelve el mensaje de error almacenado
     *
     * @return array
     */
    public abstract function getError(): array;



    protected function BuscarxCodigo(array $datos, &$resultado, ?int &$numfilas): bool
    {
        $spnombre = "sel_EscuelasPuestosTemporalidades_xId";
        $sparam = array(
            'pId' => $datos['Id']
        );
        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            $this->setError(400, "Error al buscar por codigo. ");
            return false;
        }
        return true;
    }


    protected function BusquedaAvanzada(array $datos, &$resultado, ?int &$numfilas): bool
    {
        $spnombre = "sel_EscuelasPuestosTemporalidades_BusquedaAvanzada";
        $sparam = array(
            'pxEstado' => $datos['xEstado'],
            'pEstado' => $datos['Estado'],
            'plimit' => $datos['limit'],
            'porderby' => $datos['orderby']
        );

        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            $this->setError(400, "Error al buscar al buscar por codigo. ");
            return false;
        }
        return true;
    }

}

