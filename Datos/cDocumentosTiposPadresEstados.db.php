<?php 
abstract class cDocumentosTiposPadresEstadosdb
{


	function __construct(){}

	function __destruct(){}

	protected function BuscarxCodigo($datos,&$resultado,&$numfilas)
	{
		$spnombre="sel_DocumentosTiposPadresEstados_xIdRegistroTipoDocumento_IdTipoDocumento_IdTipoDocumentoPadre_IdEstado";
		$sparam=array(
			'pIdRegistroTipoDocumento'=> $datos['IdRegistroTipoDocumento'],
			'pIdTipoDocumento'=> $datos['IdTipoDocumento'],
			'pIdTipoDocumentoPadre'=> $datos['IdTipoDocumentoPadre'],
			'pIdEstado'=> $datos['IdEstado']
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
		$spnombre="sel_DocumentosTiposPadresEstados_busqueda_avanzada";
		$sparam=array(
			'pxIdRegistroTipoDocumento'=> $datos['xIdRegistroTipoDocumento'],
			'pIdRegistroTipoDocumento'=> $datos['IdRegistroTipoDocumento'],
			'pxIdTipoDocumento'=> $datos['xIdTipoDocumento'],
			'pIdTipoDocumento'=> $datos['IdTipoDocumento'],
			'pxIdTipoDocumentoPadre'=> $datos['xIdTipoDocumentoPadre'],
			'pIdTipoDocumentoPadre'=> $datos['IdTipoDocumentoPadre'],
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
		$spnombre="sel_DocumentosTiposPadresEstados_AuditoriaRapida";
		$sparam=array(
			'pIdRegistroTipoDocumento'=> $datos['IdRegistroTipoDocumento']
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
		$spnombre="ins_DocumentosTiposPadresEstados";
		$sparam=array(
			'pIdRegistroTipoDocumento'=> $datos['IdRegistroTipoDocumento'],
			'pIdTipoDocumento'=> $datos['IdTipoDocumento'],
			'pIdTipoDocumentoPadre'=> $datos['IdTipoDocumentoPadre'],
			'pIdEstado'=> $datos['IdEstado'],
			'pAltaFecha'=> $datos['AltaFecha'],
			'pAltaUsuario'=> $datos['AltaUsuario'],
			'pAltaApp'=> APP,
			'pUltimaModificacionUsuario'=> $_SESSION['usuariocod'],
			'pUltimaModificacionFecha'=> $datos['UltimaModificacionFecha'],
			'pUltimaModificacionApp'=> APP
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
		$spnombre="del_DocumentosTiposPadresEstados_xIdRegistroTipoDocumento_IdTipoDocumento_IdTipoDocumentoPadre_IdEstado";
		$sparam=array(
			'pIdRegistroTipoDocumento'=> $datos['IdRegistroTipoDocumento'],
			'pIdTipoDocumento'=> $datos['IdTipoDocumento'],
			'pIdTipoDocumentoPadre'=> $datos['IdTipoDocumentoPadre'],
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