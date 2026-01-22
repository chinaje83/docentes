<?php 
abstract class cFormulariosMacrosdb
{


	function __construct(){}

	function __destruct(){}


	protected function BuscarMacros($datos,&$resultado,&$numfilas)
	{
		$spnombre="sel_FormulariosMacros";
		$sparam=array(
			"porderby"=>$datos['orderby']
		);
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno) )
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al buscar los macros. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		return true;
	}






}
?>