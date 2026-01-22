<?php
abstract class cJornadasdb
{
    /** @var accesoBDLocal  */
    protected $conexion;
    /** @var mixed  */
    protected $formato;
    /** @var array  */
    protected $error;

    function __construct(accesoBDLocal $conexion,$formato){
        $this->conexion = &$conexion;
        $this->formato = &$formato;
    }

    function __destruct(){}

    public abstract function getError(): array;

    protected function setError($error,$error_description=''): void {
        $this->error = is_array($error) ? $error : ['error' => $error, 'error_description' => $error_description];
    }

    protected function BuscarxCodigo(array $datos,  &$resultado, ?int &$numfilas): bool
    {
        $spnombre="sel_Jornadas_xIdJornada";
        $sparam=array(
            'pIdJornada'=> $datos['IdJornada']
        );
        if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno) )
        {
            FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al buscar al buscar por codigo. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
            return false;
        }
        return true;
    }

    protected function BuscarListado(array $datos,  &$resultado, ?int &$numfilas): bool
    {
        $spnombre="sel_Jornadas_listado";
        $sparam=array();
        if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno) )
        {
            FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al realizar la búsqueda avanzada. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
            return false;
        }
        return true;
    }

}
