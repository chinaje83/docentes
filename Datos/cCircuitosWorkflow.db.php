<?php

/**
 * Class cCircuitosWorkflowdb
 * @property accesoBDLocal conexion
 */
abstract class cCircuitosWorkflowdb
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
		$spnombre="sel_CircuitosWorkflow_xIdWorkflow";
		$sparam=array(
			'pIdWorkflow'=> $datos['IdWorkflow']
			);
			
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno))
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al buscar la conexion por codigo.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		
		return true;
	}

	protected function BuscarConexionesxCircuito($datos, &$resultado,&$numfilas)
	{
		$spnombre="sel_CircuitosWorkflow_xIdCircuito";
		$sparam=array(
			'pIdCircuito'=> $datos['IdCircuito']
			);
			
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno))
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al buscar las conexiones por circuito.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		
		return true;
	}

	protected function BuscarAccionesWorkflow($datos, &$resultado,&$numfilas)
	{
		$spnombre="sel_CircuitosWorkflowAcciones_xAreaRolEstado";
		$sparam=array(
			'pIdArea'=> $datos['IdArea'],
			'pIdRol'=> $datos['IdRol'],
			'pIdEstado'=> $datos['IdEstado'],
			'pIdTipoDocumento' => $datos['IdTipoDocumento']
			);
			
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno))
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al buscar las acciones del workflow.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		
		return true;
	}
	
	protected function BuscarAccionesWorkflowxCodigoWorkflow($datos, &$resultado,&$numfilas)
	{
		$spnombre="sel_CircuitosWorkflowAcciones_xAreaRolEstadoIdWorkflow";
		$sparam=array(
			'pIdArea'=> $datos['IdArea'],
			'pIdRol'=> $datos['IdRol'],
			'pIdEstado'=> $datos['IdEstado'],
			'pIdWorkflow'=> $datos['IdWorkflow']
			);
			
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno))
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al buscar las acciones del workflow.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		
		return true;
	}
	
	
	
	

	protected function BuscarConexionesxWorkflowAreaCod($datos, &$resultado,&$numfilas)
	{
		$spnombre="sel_CircuitosWorkflow_xIdNodoWorkflowArea";
		$sparam=array(
			'pIdNodoWorkflowArea'=> $datos['IdNodoWorkflowArea']
			);			
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno))
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al buscar las conexiones por area.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		
		return true;
	}
	
	
	protected function BuscarConexionesxCircuitoxAreaInicialxEstadoInicialxAreaFinalxEstadoFinal($datos, &$resultado,&$numfilas)
	{
		$spnombre="sel_CircuitosWorkflow_xIdCircuito_IdEstadoInicial_IdAreaInicial_IdEstadoFinal_IdAreaFinal";
		$sparam=array(
			'pIdEstadoInicial'=> $datos['IdEstadoInicial'],
			'pIdAreaInicial'=> $datos['IdAreaInicial'],
			'pIdEstadoFinal'=> $datos['IdEstadoFinal'],
			'pIdAreaFinal'=> $datos['IdAreaFinal'],
			'pIdCircuito'=> $datos['IdCircuito']
			);
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno))
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al buscar las conexiones por area.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		
		return true;
	}

	protected function BuscarAccionesParticularesxIdWorkflow($datos, &$resultado,&$numfilas)
	{
		$spnombre="sel_CircuitoWorkflow_AccionesParticulares";
		$sparam=array(
			'pIdWorkflow'=> $datos['IdWorkflow']
			);
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno))
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al buscar las acciones particulares.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		
		return true;
	}

	protected function Insertar($datos,&$codigoinsertado)
	{			

		$spnombre="ins_CircuitosWorkflow";
		$sparam=array(
			'pIdCircuito'=> $datos['IdCircuito'],
			'pNombreAccion'=> $datos['NombreAccion'],
			'pClassBoton'=> $datos['ClassBoton'],
			'pClassIcono'=> $datos['ClassIcono'],
			'pAreaOrigen' => $datos['AreaOrigen'],
            'pNoValidaDatos'  => $datos['NoValidaDatos'],
			'pClase'=> $datos['Clase'],
			'pMetodo'=> $datos['Metodo'],
			'pIdNodoWorkflowActual'=> $datos['IdNodoWorkflowActual'],
			'pIdNodoWorkflowFinal'=> $datos['IdNodoWorkflowFinal'],
			'pFechaAlta'=>  $datos['FechaAlta'],
			'pAltaUsuario'=> $_SESSION['usuariocod'],
			'pUltimaModificacionUsuario'=> $_SESSION['usuariocod'],
			'pUltimaModificacionFecha'=> $datos['UltimaModificacionFecha']
			);
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$query,$numfilas,$errno))
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al insertar un circuito de workflow. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		$codigoinsertado=$this->conexion->UltimoCodigoInsertado();

		return true;
	}

	protected function Modificar($datos)
	{			

		$spnombre="upd_CircuitosWorkflow_xIdWorkflow";
		$sparam=array(
			'pNombreAccion'=> $datos['NombreAccion'],
			'pClassBoton'=> $datos['ClassBoton'],
			'pClassIcono'=> $datos['ClassIcono'],
			'pAreaOrigen' => $datos['AreaOrigen'],
			'pNoValidaDatos'  => $datos['NoValidaDatos'],
			'pClase'=> $datos['Clase'],
			'pMetodo'=> $datos['Metodo'],
			'pUltimaModificacionUsuario'=> $datos['UltimaModificacionUsuario'],
			'pUltimaModificacionFecha'=> $datos['UltimaModificacionFecha'],
			'pIdWorkflow'=> $datos['IdWorkflow']
			);
		
	  if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$query,$numfilas,$errno))
	  {
		  FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al modificar un circuito de workflow. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
		  return false;
	  }
	
	  return true;
	}

	protected function Eliminar($datos)
	{			
		$spnombre="del_CircuitosWorkflow_xIdWorkflow";
		$sparam=array(
			'pIdWorkflow'=> $datos['IdWorkflow']
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