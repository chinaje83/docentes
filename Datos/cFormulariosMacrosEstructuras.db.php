<?php 
abstract class cFormulariosMacrosEstructurasdb
{


	function __construct(){}

	function __destruct(){}


	protected function BuscarEstructurasxIdMacro($datos,&$resultado,&$numfilas)
	{
		$spnombre="sel_formularios_macros_estructuras_xIdMacro";
		$sparam=array(
			"pIdMacro"=>$datos['IdMacro'],
			"porderby"=>$datos['orderby']
		);
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno) )
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al buscar las estructuras del macro. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		return true;
	}






}
?>