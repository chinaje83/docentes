<?php

abstract class cFuncionesCargoDB
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
        $spnombre = "sel_FuncionesCargo_xIdFuncionCargo";
        $sparam = array(
            'pIdFuncionCargo' => $datos['IdFuncionCargo']
        );
        if (!$this->conexion->ejecutarStoredProcedure($spnombre, $sparam, $resultado, $numfilas, $errno)) {
            $this->setError(400, "Error al buscar por codigo. ");
            return false;
        }
        return true;
    }

    protected function BusquedaAvanzada(array $datos,  &$resultado, ?int &$numfilas): bool
    {
        $spnombre="sel_FuncionesCargo_busqueda_avanzada";
        $sparam=array(
            'pxIdFuncionCargo'=> $datos['xIdFuncionCargo'],
            'pIdFuncionCargo'=> $datos['IdFuncionCargo'],
            'pxCodigo'=> $datos['xCodigo'],
            'pCodigo'=> $datos['Codigo'],
            'pxNombre'=> $datos['xNombre'],
            'pNombre'=> $datos['Nombre'],
            'pxIdExterno'=> $datos['xIdExterno'],
            'pIdExterno'=> $datos['IdExterno'],
            'pxEstado'=> $datos['xEstado'],
            'pEstado'=> $datos['Estado'],
            'plimit'=> $datos['limit'],
            'porderby'=> $datos['orderby']
        );
        if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno) )
        {
            FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al realizar la búsqueda avanzada. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
            return false;
        }
        return true;
    }


}

