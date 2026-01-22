<?php 
abstract class cDocumentosTiposAsociadosdb
{


	function __construct(){}

	function __destruct(){}

	protected function BuscarxCodigo($datos,&$resultado,&$numfilas)
	{
		$spnombre="sel_DocumentosTiposAsociados_xIdTipoDocumentoAsociado";
		$sparam=array(
			'pIdTipoDocumentoAsociado'=> $datos['IdTipoDocumentoAsociado']
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
		
		$spnombre="sel_DocumentosTiposAsociados_xIdRegistroTipoDocumento";
		$sparam=array(
			'pVigencia'=> $datos['Vigencia'],
			'pIdRegistroTipoDocumento'=> $datos['IdRegistroTipoDocumento']
			);
		
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno) )
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al buscar por Id del tipo de documento. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		return true;
	}


	protected function BuscarxIdRegistroTipoDocumentoxIdTipoDocumentos($datos,&$resultado,&$numfilas)
	{
		$spnombre="sel_DocumentosTiposAsociados_xIdRegistroTipoDocumento_IdTipoDocumento";
		$sparam=array(
			'pIdRegistroTipoDocumento'=> $datos['IdRegistroTipoDocumento'],
			'pIdTipoDocumento'=> $datos['IdTipoDocumento']
			);
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno) )
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al realizar la búsqueda avanzada. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		return true;
	}

	protected function Insertar($datos,&$codigoinsertado)
	{
		
		$spnombre="ins_DocumentosTiposAsociados";
		$sparam=array(
			'pIdRegistroTipoDocumento'=> $datos['IdRegistroTipoDocumento'],
			'pIdTipoDocumento'=> $datos['IdTipoDocumento'],
			'pIdCampos'=> $datos['IdCampos'],
			'pCampos'=> $datos['Campos'],
			'pAltaFecha'=> $datos['AltaFecha'],
			'pAltaUsuario'=> $datos['AltaUsuario'],
			'pAltaApp'=>  APP,
			'pUltimaModificacionUsuario'=> $datos['UltimaModificacionUsuario'],
			'pUltimaModificacionFecha'=> $datos['UltimaModificacionFecha'],
			'pUltimaModificacionApp'=> APP
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
		$spnombre="del_DocumentosTiposAsociados_xIdTipoDocumentoAsociado";
		$sparam=array(
			'pIdTipoDocumentoAsociado'=> $datos['IdTipoDocumentoAsociado']
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