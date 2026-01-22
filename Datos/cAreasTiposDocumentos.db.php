<?php 
abstract class cAreasTiposDocumentosdb
{


	function __construct(){}

	function __destruct(){}

	protected function BuscarxCodigo($datos,&$resultado,&$numfilas)
	{
		$spnombre="sel_AreasTiposDocumentos_xIdRegistroArea_IdRegistroTipoDocumento";
		$sparam=array(
			'pIdRegistroArea'=> $datos['IdRegistroArea'],
			'pIdRegistroTipoDocumento' => $datos['IdRegistroTipoDocumento']
		);
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno) )
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al buscar al buscar por codigo. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		return true;
	}
	
	protected function BuscarxIdRegistroArea($datos,&$resultado,&$numfilas)
	{
		$spnombre="sel_AreasTiposDocumentos_xIdRegistroArea";
		$sparam=array(
			'pIdRegistroArea'=> $datos['IdRegistroArea']
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
		$spnombre="sel_AreasTiposDocumentos_xIdRegistroTipoDocumento";
		$sparam=array(
			'pIdRegistroTipoDocumento'=> $datos['IdRegistroTipoDocumento']
		);
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno) )
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al buscar al buscar por codigo. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		return true;
	}

	protected function BuscarxIdTipoDocumentoxIdArea($datos,&$resultado,&$numfilas)
	{
		$spnombre="sel_AreasTiposDocumentos_xIdArea_xIdTipoDocumento";
		$sparam=array(
			'pIdArea'=> $datos['IdArea'],
			'pIdTipoDocumento'=> $datos['IdTipoDocumento']
		);
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno) )
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al buscar si visualiza los tipos de documento por area. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		return true;
	}


	protected function BuscarTiposDocumentosRelacionadosAreaRaizVigentes($datos,&$resultado,&$numfilas)
	{
		$spnombre="sel_AreasTiposDocumentos_xTiposDocumentosRaiz_xIdArea_Vigencia";
		$sparam=array(
			'pIdArea'=> $datos['IdArea'],
			'pVigencia'=> $datos['Vigencia']
		);
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno) )
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al buscar los documentos raiz por area. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		return true;
	}
	
	protected function BuscarTiposDocumentosRelacionadosAreaRaizVigentesActivos($datos,&$resultado,&$numfilas)
	{
		$spnombre="sel_AreasTiposDocumentos_xTiposDocumentosRaiz_xIdArea_Vigencia_Activos";
		$sparam=array(
			'pIdArea'=> $datos['IdArea'],
			'pVigencia'=> $datos['Vigencia']
		);
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno) )
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al buscar los documentos raiz por area. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		return true;
	}




	protected function BusquedaAvanzada($datos,&$resultado,&$numfilas)
	{
		$spnombre="sel_AreasTiposDocumentos_busqueda_avanzada";
		$sparam=array(
			'pxIdRegistroArea'=> $datos['xIdRegistroArea'],
			'pIdRegistroArea'=> $datos['IdRegistroArea'],
			'pxIdRegistroTipoDocumento'=> $datos['xIdRegistroTipoDocumento'],
			'pIdRegistroTipoDocumento'=> $datos['IdRegistroTipoDocumento'],
			'pVigencia'=> $datos['Vigencia'],
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
		$spnombre="sel_AreasTiposDocumentos_AuditoriaRapida";
		$sparam=array(
			'pIdRegistroArea'=> $datos['IdRegistroArea'],
			'pIdRegistroTipoDocumento' => $datos['IdRegistroTipoDocumento']
		);
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno) )
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al buscar por codigo. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		return true;
	}



	protected function InsertarDB($datos)
	{
		$spnombre="ins_AreasTiposDocumentos";
		$sparam=array(
			'pIdRegistroArea'=> $datos['IdRegistroArea'],
			'pIdRegistroTipoDocumento' => $datos['IdRegistroTipoDocumento'], 
			'pIdTipoDocumento'=> $datos['IdTipoDocumento'],
			'pAltaFecha'=> $datos['AltaFecha'],
			'pAltaApp'=> $datos['AltaApp'],
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



	protected function Eliminar($datos)
	{
		$spnombre="del_AreasTiposDocumentos_xIdRegistroArea_IdRegistroTipoDocumento";
		$sparam=array(
			'pIdRegistroArea'=> $datos['IdRegistroArea'],
			'pIdRegistroTipoDocumento'=> $datos['IdRegistroTipoDocumento']
		);
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno))
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al eliminar por codigo. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		return true;
	}
	
	
	
	protected function ModificarDocumentoVisualiza($datos)
	{
		$spnombre="upd_AreasTiposDocumentos_xIdRegistroArea_IdRegistroTipoDocumento";
		$sparam=array(
			'pDocumentoVisualiza'=> $datos['DocumentoVisualiza'],
			'pUltimaModificacionUsuario'=> $datos['UltimaModificacionUsuario'],
			'pUltimaModificacionFecha'=> $datos['UltimaModificacionFecha'],
			'pIdRegistroArea'=> $datos['IdRegistroArea'],
			'pIdRegistroTipoDocumento'=> $datos['IdRegistroTipoDocumento']
			);
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno))
		{
			echo "aca";die;
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al modificar el estado. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		return true;
	}





}
?>