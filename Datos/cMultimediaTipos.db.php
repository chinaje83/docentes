<?php  
abstract class cMultimediaTiposdb
{
	
	// Constructor de la clase
	function __construct(){


    } 
	
	// Destructor de la clase
	function __destruct() {	
    } 	

	
//-----------------------------------------------------------------------------------------
//							 PUBLICAS	
//----------------------------------------------------------------------------------------- 

//----------------------------------------------------------------------------------------- 

	protected function SpMultimediaTipos(&$spnombre,&$sparam)
	{
		$spnombre="sel_mul_multimedia_tipos";
		$sparam=array(
			);
	
		return true;
	}

	protected function SpMultimediaTiposxTipo($datos,&$spnombre,&$sparam)
	{
		$spnombre="sel_mul_multimedia_tipos_xmultimediaconjuntocod";
		$sparam=array(
			'pmultimediaconjuntocod'=> $datos['multimediaconjuntocod']
			);
	
		return true;
	}



	protected function BuscarMultimediaTiposxTipoArchivo($datos,&$resultado,&$numfilas)
	{
		$spnombre="sel_mul_multimedia_tipos_xmultimediatipoarchivo";
		$sparam=array(
			'pmultimediatipoarchivo'=> $datos['multimediatipoarchivo']
			);
	
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno))
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al buscar el tipo multimedia.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		
		return true;
	}




}
?>