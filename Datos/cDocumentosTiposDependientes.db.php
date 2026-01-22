<?php 
abstract class cDocumentosTiposDependientesdb
{


	function __construct(){}

	function __destruct(){}

	protected function BuscarxCodigo($datos,&$resultado,&$numfilas)
	{
		$spnombre="sel_DocumentosTiposDependientes_xIdTipoDocumentoDependiente";
		$sparam=array(
			'pIdTipoDocumentoDependiente'=> $datos['IdTipoDocumentoDependiente']
		);
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno) )
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al buscar al buscar por codigo. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		return true;
	}
	
	protected function BuscarxIdRegistroTipoDocumento($datos,&$resultado,&$numfilas)
	{
		$spnombre="sel_DocumentosTiposDependientes_xIdRegistroTipoDocumento";
		$sparam=array(
			'pIdRegistroTipoDocumento'=> $datos['IdRegistroTipoDocumento']
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
		$spnombre="sel_DocumentosTiposDependientes_busqueda_avanzada";
		$sparam=array(
			'pxIdTipoDocumentoDependiente'=> $datos['xIdTipoDocumentoDependiente'],
			'pIdTipoDocumentoDependiente'=> $datos['IdTipoDocumentoDependiente'],
			'pxIdRegistroTipoDocumento'=> $datos['xIdRegistroTipoDocumento'],
			'pIdRegistroTipoDocumento'=> $datos['IdRegistroTipoDocumento'],
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
		$spnombre="sel_DocumentosTiposDependientes_AuditoriaRapida";
		$sparam=array(
			'pIdTipoDocumentoDependiente'=> $datos['IdTipoDocumentoDependiente']
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
		$spnombre="ins_DocumentosTiposDependientes";
		$sparam=array(
			'pIdRegistroTipoDocumento'=> $datos['IdRegistroTipoDocumento'],
			'pIdTipoDocumento'=> $datos['IdTipoDocumento'],
			'pIdEstado'=> $datos['IdEstado'],
			'pOrden'=> $datos['Orden'],
			'pAltaFecha'=> $datos['AltaFecha'],
			'pAltaUsuario'=> $datos['AltaUsuario'],
			'pAltaApp'=>  APP,
			'pUltimaModificacionUsuario'=> $_SESSION['usuariocod'],
			'pUltimaModificacionFecha'=> $datos['UltimaModificacionFecha'],
			'pUltimaModificacionApp'=>  APP 
		);
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno))
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al insertar. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		$codigoinsertado=$this->conexion->UltimoCodigoInsertado();

		return true;
	}


	protected function Eliminar($datos)
	{
		$spnombre="del_DocumentosTiposDependientes_xIdTipoDocumentoDependiente";
		$sparam=array(
			'pIdTipoDocumentoDependiente'=> $datos['IdTipoDocumentoDependiente']
		);
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno))
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al eliminar por codigo. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		return true;
	}



	protected function BuscarUltimoOrden($datos,&$resultado,&$numfilas)
	{
		$spnombre="sel_DocumentosTiposDependientes_max_orden";
		$sparam=array();
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno) )
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al buscar el maximo orden. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		return true;
	}



	protected function ModificarOrden($datos)
	{
		$spnombre="upd_DocumentosTiposDependientes_Orden_xIdTipoDocumentoDependiente";
		$sparam=array(
			'pOrden'=> $datos['Orden'],
			'pUltimaModificacionUsuario'=> $_SESSION['usuariocod'],
			'pUltimaModificacionUsuario'=> date("Y-m-d H:i:s"),
			'pUltimaModificacionApp'=> APP,
			'pIdTipoDocumentoDependiente'=> $datos['IdTipoDocumentoDependiente']
		);
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno))
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al modificar el orden. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		return true;
	}


	protected function ModificarModificaCuitAgente($datos)
	{
		$spnombre="upd_DocumentosTiposDependientes_xIdTipoDocumentoDependiente";
		$sparam=array(
			'pModificaCuitAgente'=> $datos['ModificaCuitAgente'],
			'pUltimaModificacionUsuario'=> $_SESSION['usuariocod'],
			'pUltimaModificacionFecha'=> date("Y-m-d H:i:s"),
			'pUltimaModificacionApp'=> APP,
			'pIdTipoDocumentoDependiente'=> $datos['IdTipoDocumentoDependiente']
			);

		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno))
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al modificar el orden. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		return true;
	}
	
	
	protected function ModificarGeneraNovedadPorSubsecuencia($datos)
	{
		$spnombre="upd_DocumentosTiposDependientes_GeneraNovedadPorSubsecuencia_xIdTipoDocumentoDependiente";
		$sparam=array(
			'pGeneraNovedadPorSubsecuencia'=> $datos['GeneraNovedadPorSubsecuencia'],
			'pUltimaModificacionUsuario'=> $_SESSION['usuariocod'],
			'pUltimaModificacionFecha'=> date("Y-m-d H:i:s"),
			'pUltimaModificacionApp'=> APP,
			'pIdTipoDocumentoDependiente'=> $datos['IdTipoDocumentoDependiente']
			);
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno))
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al modificar el orden. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		return true;
	}




}
?>