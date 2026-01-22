<?php 
abstract class cCircuitosWorkflowTiposDocumentosdb
{


	function __construct(){}

	function __destruct(){}

	protected function BuscarxCodigo($datos,&$resultado,&$numfilas)
	{
		$spnombre="sel_CircuitosWorkflowTiposDocumentos_xIdWorkflow_IdTipoDocumento_IdEstado";
		$sparam=array(
			'pIdWorkflow'=> $datos['IdWorkflow'],
			'pIdTipoDocumento'=> $datos['IdTipoDocumento'],
			'pIdEstado'=> $datos['IdEstado']
			
		);
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno) )
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al buscar al buscar por codigo. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		return true;
	}


	protected function BuscarxIdWorkflow($datos,&$resultado,&$numfilas)
	{
		$spnombre="sel_CircuitosWorkflowTiposDocumentos_xIdWorkflow";
		$sparam=array(
			'pIdWorkflow'=> $datos['IdWorkflow'],
			'pVigencia'=> $datos['Vigencia']
		);
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno) )
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al buscar por Id del tipo de documento. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		return true;
	}


	protected function BusquedaAvanzada($datos,&$resultado,&$numfilas)
	{
		$spnombre="sel_CircuitosWorkflowTiposDocumentos_busqueda_avanzada";
		$sparam=array(
			'pxIdWorkflow'=> $datos['xIdWorkflow'],
			'pIdWorkflow'=> $datos['IdWorkflow'],
			'pxIdTipoDocumento'=> $datos['xIdTipoDocumento'],
			'pIdTipoDocumento'=> $datos['IdTipoDocumento'],
			'pxIdEstado'=> $datos['xIdEstado'],
			'pIdEstado'=> $datos['IdEstado'],
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
		$spnombre="sel_CircuitosWorkflowTiposDocumentos_AuditoriaRapida";
		$sparam=array(
			'pIdWorkflow'=> $datos['IdWorkflow'],
			'pIdTipoDocumento'=> $datos['IdTipoDocumento'],
			'pIdEstado'=> $datos['IdEstado']
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
		$spnombre="ins_CircuitosWorkflowTiposDocumentos";
		$sparam=array(
			'pIdWorkflow'=> $datos['IdWorkflow'],
			'pIdTipoDocumento'=> $datos['IdTipoDocumento'],
			'pIdEstado'=> $datos['IdEstado'],
			'pFechaAlta'=> $datos['FechaAlta'],
			'pAltaUsuario'=> $datos['AltaUsuario'],
			'pUltimaModificacionUsuario'=> $_SESSION['usuariocod'],
			'pUltimaModificacionFecha'=> $datos['UltimaModificacionFecha']
		);
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno))
		{
			echo "aca";die;
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al insertar. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}


		return true;
	}



	protected function Modificar($datos)
	{
		$spnombre="upd_CircuitosWorkflowTiposDocumentos_xIdWorkflow";
		$sparam=array(
			'pIdTipoDocumento'=> $datos['IdTipoDocumento'],
			'pIdEstado'=> $datos['IdEstado'],
			'pFechaAlta'=> $datos['FechaAlta'],
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
		$spnombre="del_CircuitosWorkflowTiposDocumentos_xIdWorkflow_IdTipoDocumento_IdEstado";
		$sparam=array(
			'pIdWorkflow'=> $datos['IdWorkflow'],
			'pIdTipoDocumento'=> $datos['IdTipoDocumento'],
			'pIdEstado'=> $datos['IdEstado']
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