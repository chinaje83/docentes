<?php 
abstract class cDocumentosTiposZonasdb
{


	function __construct(){}

	function __destruct(){}

	protected function BuscarxCodigo($datos,&$resultado,&$numfilas)
	{
		$spnombre="sel_DocumentosTiposZonas_xIdZonaModulo";
		$sparam=array(
			'pIdZonaModulo'=> $datos['IdZonaModulo']
		);
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno) )
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al buscar al buscar por codigo. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		return true;
	}



	protected function BuscarxZonaTipoDocumento($datos,&$resultado,&$numfilas)
	{
		$spnombre="sel_DocumentosTiposZonas_xIdZona_xIdRegistroTipoDocumento";
		$sparam=array(
			'pIdZona'=> $datos['IdZona'],
			'pIdRegistroTipoDocumento' => $datos['IdRegistroTipoDocumento']
		);
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno) )
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al buscar los modulos por zona y tipo de documento. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		return true;
	}

	protected function BuscarxIdRegistroTipoDocumentoxIdCampo($datos,&$resultado,&$numfilas)
	{
		$spnombre="sel_DocumentosTiposZonas_xIdCampo_xIdRegistroTipoDocumento";
		$sparam=array(
			'pIdCampo'=> $datos['IdCampo'],
			'pIdRegistroTipoDocumento' => $datos['IdRegistroTipoDocumento']
		);
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno) )
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al buscar el modulo por tipo de documento y campo. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		return true;
	}
	
	
	
	protected function BuscarxIdRegistroTipoDocumentoxIdDocumentoAdjunto($datos,&$resultado,&$numfilas)
	{
		$spnombre="sel_DocumentosTiposZonas_xIdRegistroTipoDocumento_xIdDocumentoAdjunto";
		$sparam=array(
			'pIdRegistroTipoDocumento' => $datos['IdRegistroTipoDocumento'],
			'pIdDocumentoAdjunto' => $datos['IdDocumentoAdjunto']
		);
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno) )
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al buscar el modulo por tipo de documento y campo. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		return true;
	}

	protected function BuscarxIdZona($datos,&$resultado,&$numfilas)
	{
		$spnombre="sel_DocumentosTiposZonas_xIdZona";
		$sparam=array(
			'pIdZona'=> $datos['IdZona']
		);
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno) )
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al buscar los modulos por zona. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		return true;
	}






	protected function BuscarAuditoriaRapida($datos,&$resultado,&$numfilas)
	{
		$spnombre="sel_DocumentosTiposZonas_AuditoriaRapida";
		$sparam=array(
			'pIdZonaModulo'=> $datos['IdZonaModulo']
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
		$spnombre="ins_DocumentosTiposZonas";
		$sparam=array(
			'pIdRegistroTipoDocumento'=> $datos['IdRegistroTipoDocumento'],
			'pIdZona'=> $datos['IdZona'],
			'pIdCampo'=> $datos['IdCampo'],
			'pIdModulo'=> $datos['IdModulo'],
			'pIdDocumentoAdjunto'=> $datos['IdDocumentoAdjunto'],
			'pCampoObligatorio'=> $datos['CampoObligatorio'],
			'pDataJson'=> $datos['DataJson'],
			'pOrden'=> $datos['Orden'],
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

		$codigoinsertado=$this->conexion->UltimoCodigoInsertado();

		return true;
	}



	protected function Modificar($datos)
	{
		$spnombre="upd_DocumentosTiposZonas_xIdZonaModulo";
		$sparam=array(
			'pDataJson'=> $datos['DataJson'],
			'pUltimaModificacionUsuario'=> $_SESSION['usuariocod'],
			'pUltimaModificacionFecha'=> $datos['UltimaModificacionFecha'],
			'pIdZonaModulo'=> $datos['IdZonaModulo']
		);
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno))
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al modificar. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		return true;
	}



	protected function ModificarDatosConfiguracion($datos)
	{
		$spnombre="upd_DocumentosTiposZonas_DatosConfiguracion_xIdZonaModulo";
		$sparam=array(
			'pCampoObligatorio'=> $datos['CampoObligatorio'],
			'pUltimaModificacionUsuario'=> $_SESSION['usuariocod'],
			'pUltimaModificacionFecha'=> $datos['UltimaModificacionFecha'],
			'pIdZonaModulo'=> $datos['IdZonaModulo']
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
		$spnombre="del_DocumentosTiposZonas_xIdZonaModulo";
		$sparam=array(
			'pIdZonaModulo'=> $datos['IdZonaModulo']
		);
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno))
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al eliminar por codigo. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		return true;
	}
	
	
	protected function EliminarxIdMacroPosicion($datos)
	{
		$spnombre="del_DocumentosTiposZonas_xIdMacroPosicion";
		$sparam=array(
			'pIdMacroPosicion'=> $datos['IdMacroPosicion']
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
		$spnombre="sel_DocumentosTiposZonas_max_orden";
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
		$spnombre="upd_DocumentosTiposZonas_Orden_xIdZonaModulo";
		$sparam=array(
			'pOrden'=> $datos['Orden'],
			'pIdZona'=> $datos['IdZona'],
			'pUltimaModificacionUsuario'=> $_SESSION['usuariocod'],
			'pUltimaModificacionFecha'=> date("Y-m-d H:i:s"),
			'pIdZonaModulo'=> $datos['IdZonaModulo']
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