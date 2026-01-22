<?php 
abstract class cCircuitosAreasEstadosAreasdb
{


	function __construct(){}

	function __destruct(){}

	protected function BuscarxCodigo($datos,&$resultado,&$numfilas)
	{
		$spnombre="sel_CircuitosAreasEstadosAreas_xIdNodoWorkflowArea";
		$sparam=array(
			'pIdNodoWorkflowArea'=> $datos['IdNodoWorkflowArea']
		);
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno) )
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al buscar al buscar por codigo. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		return true;
	}


	protected function BuscarxCodigoNodo($datos,&$resultado,&$numfilas)
	{
		$spnombre="sel_CircuitosAreasEstadosAreas_xIdNodoWorkflow";
		$sparam=array(
			'pVigencia'=> $datos['Vigencia'],
			'pIdNodoWorkflow'=> $datos['IdNodoWorkflow']
		);
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno) )
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al buscar por nodo. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		return true;
	}


	protected function BuscarNombresAreasxCodigoNodo($datos,&$resultado,&$numfilas)
	{
		$spnombre="sel_CircuitosAreasEstadosAreas_Nombres_xIdNodoWorkflow";
		$sparam=array(
			'pVigencia'=> $datos['Vigencia'],
			'pIdNodoWorkflow'=> $datos['IdNodoWorkflow']
		);
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno) )
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al buscar por nodo. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		return true;
	}



	protected function BuscarxCodigoNodoxArea($datos,&$resultado,&$numfilas)
	{
		$spnombre="sel_CircuitosAreasEstadosAreas_xIdNodoWorkflow_xIdArea";
		$sparam=array(
			'pIdNodoWorkflow'=> $datos['IdNodoWorkflow'],
			'pIdArea'=> $datos['IdArea']
		);
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno) )
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al buscar por nodo y area. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		return true;
	}



	protected function BuscarAuditoriaRapida($datos,&$resultado,&$numfilas)
	{
		$spnombre="sel_CircuitosAreasEstadosAreas_AuditoriaRapida";
		$sparam=array(
			'pIdNodoWorkflowArea'=> $datos['IdNodoWorkflowArea']
		);
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno) )
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al buscar por codigo. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		return true;
	}



	protected function Insertar($datos,&$codigoinsertado)
	{
		$spnombre="ins_CircuitosAreasEstadosAreas";
		$sparam=array(
			'pIdNodoWorkflow'=> $datos['IdNodoWorkflow'],
			'pIdArea'=> $datos['IdArea'],
			'pFechaAlta'=> $datos['FechaAlta'],
			'pAltaUsuario'=> $datos['AltaUsuario'],
			'pUltimaModificacionUsuario'=> $_SESSION['usuariocod'],
			'pUltimaModificacionFecha'=> $datos['UltimaModificacionFecha']
		);
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno))
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al insertar. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		$codigoinsertado=$this->conexion->UltimoCodigoInsertado();

		return true;
	}



	protected function Modificar($datos)
	{
		$spnombre="upd_CircuitosAreasEstadosAreas_xIdNodoWorkflowArea";
		$sparam=array(
			'pIdNodoWorkflow'=> $datos['IdNodoWorkflow'],
			'pIdArea'=> $datos['IdArea'],
			'pFechaAlta'=> $datos['FechaAlta'],
			'pUltimaModificacionUsuario'=> $_SESSION['usuariocod'],
			'pUltimaModificacionFecha'=> $datos['UltimaModificacionFecha'],
			'pIdNodoWorkflowArea'=> $datos['IdNodoWorkflowArea']
		);
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno))
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al modificar. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		return true;
	}



	protected function EliminarxNodo($datos)
	{
		$spnombre="del_CircuitosAreasEstadosAreas_xIdNodoWorkflow";
		$sparam=array(
			'pIdNodoWorkflow'=> $datos['IdNodoWorkflow']
		);
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno))
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al eliminar por codigo de nodo. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		return true;
	}


	protected function Eliminar($datos)
	{
		$spnombre="del_CircuitosAreasEstadosAreas_xIdNodoWorkflowArea";
		$sparam=array(
			'pIdNodoWorkflow'=> $datos['IdNodoWorkflow'],
			'pIdNodoWorkflowArea'=> $datos['IdNodoWorkflowArea']
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