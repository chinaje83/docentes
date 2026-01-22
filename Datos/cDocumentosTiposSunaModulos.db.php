<?php 
abstract class cDocumentosTiposSunaModulosdb
{


	function __construct(){}

	function __destruct(){}

	protected function BuscarxCodigo($datos,&$resultado,&$numfilas)
	{
		$spnombre="sel_DocumentosTiposSunaModulos_xIdTipoDocumentoSunaModulo";
		$sparam=array(
			'pIdTipoDocumentoSunaModulo'=> $datos['IdTipoDocumentoSunaModulo']
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
		$spnombre="sel_DocumentosTiposSunaModulos_xIdRegistroTipoDocumento";
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
	
	
	protected function BuscarxIdRegistroTipoDocumentoxIdSunaModulo($datos,&$resultado,&$numfilas)
	{
		$spnombre="sel_DocumentosTiposSunaModulos_xIdRegistroTipoDocumento_IdSunaModulo";
		$sparam=array(
			'pIdRegistroTipoDocumento'=> $datos['IdRegistroTipoDocumento'],
			'pIdSunaModulo'=> $datos['IdSunaModulo'],
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
		$spnombre="sel_DocumentosTiposSunaModulos_busqueda_avanzada";
		$sparam=array(
			'pxIdTipoDocumentoSunaModulo'=> $datos['xIdTipoDocumentoSunaModulo'],
			'pIdTipoDocumentoSunaModulo'=> $datos['IdTipoDocumentoSunaModulo'],
			'pxIdRegistroTipoDocumento'=> $datos['xIdRegistroTipoDocumento'],
			'pIdRegistroTipoDocumento'=> $datos['IdRegistroTipoDocumento'],
			'pxIdTipoDocumento'=> $datos['xIdTipoDocumento'],
			'pIdTipoDocumento'=> $datos['IdTipoDocumento'],
			'pxIdSunaModulo'=> $datos['xIdSunaModulo'],
			'pIdSunaModulo'=> $datos['IdSunaModulo'],
			'pxTitulo'=> $datos['xTitulo'],
			'pTitulo'=> $datos['Titulo'],
			'pxDescripcion'=> $datos['xDescripcion'],
			'pDescripcion'=> $datos['Descripcion'],
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
		$spnombre="sel_DocumentosTiposSunaModulos_AuditoriaRapida";
		$sparam=array(
			'pIdTipoDocumentoSunaModulo'=> $datos['IdTipoDocumentoSunaModulo']
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
		$spnombre="ins_DocumentosTiposSunaModulos";
		$sparam=array(
			'pIdRegistroTipoDocumento'=> $datos['IdRegistroTipoDocumento'],
			'pIdTipoDocumento'=> $datos['IdTipoDocumento'],
			'pIdSunaModulo'=> $datos['IdSunaModulo'],
			'pTitulo'=> $datos['Titulo'],
			'pDescripcion'=> $datos['Descripcion'],
			'pVisualiza'=> $datos['Visualiza'],
			'pObligatorio'=> $datos['Obligatorio'],
			'pOrden'=> $datos['Orden'],
			'pAltaFecha'=> $datos['AltaFecha'],
			'pAltaUsuario'=> $datos['AltaUsuario'],
			'pAltaApp'=> $datos['AltaApp'],
			'pUltimaModificacionUsuario'=> $_SESSION['usuariocod'],
			'pUltimaModificacionFecha'=> $datos['UltimaModificacionFecha'],
			'pUltimaModificacionApp'=> $datos['UltimaModificacionApp']
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
		$spnombre="upd_DocumentosTiposSunaModulos_xIdTipoDocumentoSunaModulo";
		$sparam=array(
			'pIdRegistroTipoDocumento'=> $datos['IdRegistroTipoDocumento'],
			'pIdTipoDocumento'=> $datos['IdTipoDocumento'],
			'pIdSunaModulo'=> $datos['IdSunaModulo'],
			'pTitulo'=> $datos['Titulo'],
			'pDescripcion'=> $datos['Descripcion'],
			'pVisualiza'=> $datos['Visualiza'],
			'pObligatorio'=> $datos['Obligatorio'],
			'pAltaApp'=> $datos['AltaApp'],
			'pUltimaModificacionUsuario'=> $_SESSION['usuariocod'],
			'pUltimaModificacionFecha'=> $datos['UltimaModificacionFecha'],
			'pUltimaModificacionApp'=> $datos['UltimaModificacionApp'],
			'pIdTipoDocumentoSunaModulo'=> $datos['IdTipoDocumentoSunaModulo']
		);
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno))
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al modificar. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		return true;
	}
	
	
	protected function ModificarObligatorio($datos)
	{
		$spnombre="upd_DocumentosTiposSunaModulos_Obligatorio_xIdTipoDocumentoSunaModulo";
		$sparam=array(
			'pObligatorio'=> $datos['Obligatorio'],
			'pUltimaModificacionUsuario'=> $_SESSION['usuariocod'],
			'pUltimaModificacionFecha'=> $datos['UltimaModificacionFecha'],
			'pUltimaModificacionApp'=> $datos['UltimaModificacionApp'],
			'pIdTipoDocumentoSunaModulo'=> $datos['IdTipoDocumentoSunaModulo']
		);
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno))
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al modificar. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		return true;
	}
	
	
	protected function ModificarTituloDescripcion($datos)
	{
		$spnombre="upd_DocumentosTiposSunaModulos_Titulo_Descripcion_xIdTipoDocumentoSunaModulo";
		$sparam=array(
			'pTitulo'=> $datos['Titulo'],
			'pDescripcion'=> $datos['Descripcion'],
			'pUltimaModificacionUsuario'=> $_SESSION['usuariocod'],
			'pUltimaModificacionFecha'=> $datos['UltimaModificacionFecha'],
			'pUltimaModificacionApp'=> $datos['UltimaModificacionApp'],
			'pIdTipoDocumentoSunaModulo'=> $datos['IdTipoDocumentoSunaModulo']
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
		$spnombre="del_DocumentosTiposSunaModulos_xIdTipoDocumentoSunaModulo";
		$sparam=array(
			'pIdTipoDocumentoSunaModulo'=> $datos['IdTipoDocumentoSunaModulo']
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
		$spnombre="sel_DocumentosTiposSunaModulos_max_orden_xIdRegistroTipoDocumento";
		$sparam=array(
			'pIdRegistroTipoDocumento'=> $datos['IdRegistroTipoDocumento']
		);
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno) )
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al buscar el maximo orden. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		return true;
	}



	protected function ModificarOrden($datos)
	{
		$spnombre="upd_DocumentosTiposSunaModulos_Orden_xIdTipoDocumentoSunaModulo";
		$sparam=array(
			'pOrden'=> $datos['Orden'],
			'pUltimaModificacionUsuario'=> $_SESSION['usuariocod'],
			'pUltimaModificacionFecha'=> date("Y-m-d H:i:s"),
			'pIdTipoDocumentoSunaModulo'=> $datos['IdTipoDocumentoSunaModulo']
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