<?php  
abstract class cCircuitosWorkflowRolesdb
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
		print_r($datos);die;
		$spnombre="sel_CircuitosWorkflowRoles_xIdWorkflow_IdNodoWorkflow_IdRol";
		$sparam=array(
			'pIdWorkflow'=> $datos['IdWorkflow'],
			'pIdNodoWorkflow'=> $datos['IdNodoWorkflow'],
			'pIdRol'=> $datos['IdRol']
			);
			
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno))
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al buscar la conexion por codigo.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		
		return true;
	}

	
	
	protected function BuscarxIdWorkflowxIdNodoWorkflow($datos, &$resultado,&$numfilas)
	{
		$spnombre="sel_CircuitosWorkflowRoles_xIdWorkflow_IdNodoWorkflow";
		$sparam=array(
			'pIdWorkflow'=> $datos['IdWorkflow'],
			'pIdNodoWorkflow'=> $datos['IdNodoWorkflow']
			);
			
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno))
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al buscar las conexiones por circuito.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		
		return true;
	}
	
	
	protected function BuscarxIdNodoWorkflowxIdRol($datos, &$resultado,&$numfilas)
	{
		$spnombre="sel_CircuitosWorkflowRoles_xIdNodoWorkflow_IdRol";
		$sparam=array(
			'pIdRol'=> $datos['IdRol'],
			'pIdNodoWorkflow'=> $datos['IdNodoWorkflow']
			);
			
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno))
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al buscar las conexiones por circuito.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		
		return true;
	}
	
	
	
	protected function BuscarxIdWorkflowxIdRol($datos, &$resultado,&$numfilas)
	{
		$spnombre="sel_CircuitosWorkflowRoles_xIdWorkflow_IdRol";
		$sparam=array(
			'pIdRol'=> $datos['IdRol'],
			'pIdWorkflow'=> $datos['IdWorkflow']
			);
			
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno))
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al buscar las conexiones por circuito.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		
		return true;
	}
	
	
	
	
	
	protected function BuscarxIdWorkflow($datos, &$resultado,&$numfilas)
	{
		$spnombre="sel_CircuitosWorkflowRoles_xIdWorkflow";
		$sparam=array(
			'pIdWorkflow'=> $datos['IdWorkflow']
			);
			
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno))
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al buscar las conexiones por circuito.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		
		return true;
	}
	
	protected function BuscarxIdNodoWorkflow($datos, &$resultado,&$numfilas)
	{
		$spnombre="sel_CircuitosWorkflowRoles_xIdNodoWorkflow";
		$sparam=array(
			'pIdNodoWorkflow'=> $datos['IdNodoWorkflow']
			);
			
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno))
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al buscar las conexiones por circuito.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		
		return true;
	}
	
	protected function InsertarDB($datos)
	{			

		$spnombre="ins_CircuitosWorkflowRoles";
		$sparam=array(
			'pIdWorkflow'=> $datos['IdWorkflow'],
			'pIdNodoWorkflow'=> $datos['IdNodoWorkflow'],
			'pIdRol'=> $datos['IdRol'],
			'pAltaFecha'=> $datos['AltaFecha'],
			'pAltaUsuario'=> $datos['AltaUsuario'],
			'pUltimaModificacionUsuario'=> $datos['UltimaModificacionUsuario'],
			'pUltimaModificacionFecha'=> $datos['UltimaModificacionFecha']
			);
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$query,$numfilas,$errno))
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al insertar un circuito de workflow. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}


		return true;
	}

	protected function Eliminar($datos)
	{			
		$spnombre="del_CircuitosWorkflowRoles_xIdNodoWorkflow_xIdWorkflow_IdRol";
		$sparam=array(
			'pIdNodoWorkflow'=> $datos['IdNodoWorkflow'],
			'pIdWorkflow'=> $datos['IdWorkflow'],
			'pIdRol'=> $datos['IdRol']
			);
			
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$query,$numfilas,$errno))
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al eliminar el circuito del workflow. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		return true;
	}



}
?>