<?php  
abstract class cFilesConfigdb
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


	protected function BuscarxCodigo($datos,&$resultado,&$numfilas)
	{
		$spnombre="sel_fil_file_config_xfilecod";
		$sparam=array(
			'pfilecod'=> $datos['filecod']
			);

		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno))
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al buscar el Tipo de archivo. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>FMT_TEXTO));
			return false;
		}
		
		return true;
	}


	protected function BusquedaAvanzada ($datos,&$resultado,&$numfilas)
	{
		
		$spnombre="sel_fil_file_config";
		$sparam=array(
			);			
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno))
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al buscar los archivos config. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>FMT_TEXTO));
			return false;
		}


		return true;
	}	



	
	protected function Modificar ($datos)
	{
		$spnombre="upd_fil_file_config_xfilecod";
		$sparam=array(
			'pultmodusuario'=> $_SESSION['usuariocod'],
			'pultmodfecha'=> date("Y/m/d H:i:s"),
			'pfilecod'=> $datos['filecod']
			);			
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno))
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al modificar el archivo config. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>FMT_TEXTO));
			return false;
		}


		return true;
	}	






}
?>