<?php 
abstract class cCircuitosWorkflowRolesAccionesdb
{


	function __construct(){}

	function __destruct(){}

	protected function BuscarxCodigo($datos,&$resultado,&$numfilas)
	{
		$spnombre="sel_CircuitosWorkflowRolesAcciones_xIdWorkflow_IdNodoWorkflow_IdRol_IdAccion";
		$sparam=array(
			'pIdWorkflow'=> $datos['IdWorkflow'],
			'pIdNodoWorkflow'=> $datos['IdNodoWorkflow'],
			'pIdRol'=> $datos['IdRol'],
			'pIdAccion'=> $datos['IdAccion']
		);
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno) )
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al buscar al buscar por codigo. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		return true;
	}



	protected function BusquedaAvanzada($datos,&$resultado,&$numfilas)
	{
		$spnombre="sel_CircuitosWorkflowRolesAcciones_busqueda_avanzada";
		$sparam=array(
			'pxIdWorkflow'=> $datos['xIdWorkflow'],
			'pIdWorkflow'=> $datos['IdWorkflow'],
			'pxIdNodoWorkflow'=> $datos['xIdNodoWorkflow'],
			'pIdNodoWorkflow'=> $datos['IdNodoWorkflow'],
			'pxIdRol'=> $datos['xIdRol'],
			'pIdRol'=> $datos['IdRol'],
			'pxIdAccion'=> $datos['xIdAccion'],
			'pIdAccion'=> $datos['IdAccion'],
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



	protected function BuscarAuditoriaRapida($datos,&$resultado,&$numfilas)
	{
		$spnombre="sel_CircuitosWorkflowRolesAcciones_AuditoriaRapida";
		$sparam=array(
			'pIdWorkflow'=> $datos['IdWorkflow'],
			'pIdNodoWorkflow'=> $datos['IdNodoWorkflow'],
			'pIdRol'=> $datos['IdRol'],
			'pIdAccion'=> $datos['IdAccion']
		);
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno) )
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al buscar por codigo. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		return true;
	}



	protected function Insertar($datos)
	{
		$spnombre="ins_CircuitosWorkflowRolesAcciones";
		$sparam=array(
			'pIdWorkflow'=> $datos['IdWorkflow'],
			'pIdNodoWorkflow'=> $datos['IdNodoWorkflow'],
			'pIdRol'=> $datos['IdRol'],
			'pIdAccion'=> $datos['IdAccion'],
			'pAltaFecha'=> $datos['AltaFecha'],
			'pAltaUsuario'=> $datos['AltaUsuario'],
			'pUltimaModificacionUsuario'=> $_SESSION['usuariocod'],
			'pUltimaModificacionFecha'=> $datos['UltimaModificacionFecha']
		);
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno))
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al insertar. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}


		return true;
	}



	protected function Modificar($datos)
	{
		$spnombre="upd_CircuitosWorkflowRolesAcciones_xIdWorkflow";
		$sparam=array(
			'pIdNodoWorkflow'=> $datos['IdNodoWorkflow'],
			'pIdRol'=> $datos['IdRol'],
			'pIdAccion'=> $datos['IdAccion'],
			'pUltimaModificacionUsuario'=> $_SESSION['usuariocod'],
			'pUltimaModificacionFecha'=> $datos['UltimaModificacionFecha'],
			'pIdWorkflow'=> $datos['IdWorkflow']
		);
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno))
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al modificar. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		return true;
	}



	protected function Eliminar($datos)
	{
		$spnombre="del_CircuitosWorkflowRolesAcciones_xIdWorkflow_IdNodoWorkflow_IdRol_IdAccion";
		$sparam=array(
			'pIdWorkflow'=> $datos['IdWorkflow'],
			'pIdNodoWorkflow'=> $datos['IdNodoWorkflow'],
			'pIdRol'=> $datos['IdRol'],
			'pIdAccion'=> $datos['IdAccion']
		);
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno))
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al eliminar por codigo. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		return true;
	}



	protected function EliminarxIdWorkflow($datos)
	{
		$spnombre="del_CircuitosWorkflowRolesAcciones_xIdWorkflow";
		$sparam=array(
			'pIdWorkflow'=> $datos['IdWorkflow']
		);
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno))
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al eliminar por codigo de workflow. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		return true;
	}
	
	
	
	protected function ModificarAccionObligatorio($datos)
	{
		$spnombre="upd_CircuitosWorkflowRolesAcciones_xIdWorkflow_IdNodoWorkflow_IdRol_IdAccion";
		$sparam=array(
			'pIdWorkflow'=> $datos['IdWorkflow'],
			'pIdNodoWorkflow'=> $datos['IdNodoWorkflow'],
			'pIdRol'=> $datos['IdRol'],
			'pIdAccion'=> $datos['IdAccion'], 
			'pAccionObligatorio'=> $datos['AccionObligatorio']
		);
		
		
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno))
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al eliminar por codigo. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		return true;
	}






}
?>