<?php  
abstract class cCircuitosAreasEstadosRolesdb
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


	protected function BuscarxCodigo($datos, &$resultado,&$numfilas)
	{
		$spnombre="sel_CircuitosAreasEstadosRoles_xIdNodoWorkflow_IdRol";
		$sparam=array(
			'pIdNodoWorkflow'=> $datos['IdNodoWorkflow'],
			'pIdRol'=> $datos['IdRol']
			);	
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno))
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al buscar las areas por circuito.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		
		return true;
	}
	
	
	protected function BuscarxIdNodoWorkflow($datos, &$resultado,&$numfilas)
	{
		$spnombre="sel_CircuitosAreasEstadosRoles_xIdNodoWorkflow";
		$sparam=array(
			'pIdNodoWorkflow'=> $datos['IdNodoWorkflow']
			);	
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno))
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al buscar las areas por circuito.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		
		return true;
	}
	
	
	protected function InsertarBD($datos)
	{			

		$spnombre="ins_CircuitosAreasEstadosRoles";
		$sparam=array(
			'pIdNodoWorkflow'=> $datos['IdNodoWorkflow'],
			'pIdRol'=> $datos['IdRol'],
			'pAltaFecha'=> $datos['AltaFecha'],
			'pAltaUsuario'=> $datos['AltaUsuario'],
			'pUltimaModificacionUsuario'=> $datos['UltimaModificacionUsuario'],
			'pUltimaModificacionFecha'=> $datos['UltimaModificacionFecha']
			);
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$query,$numfilas,$errno))
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al insertar un rol. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		return true;
	}



	
	protected function Eliminar ($datos)
	{
		$spnombre="del_CircuitosAreasEstadosRoles_xIdNodoWorkflow_IdRol";
		$sparam=array(
			'pIdNodoWorkflow'=> $datos['IdNodoWorkflow'],
			'pIdRol'=> $datos['IdRol']
			);
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno))
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al eliminar el area.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		return true;
	}


}
?>