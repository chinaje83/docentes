<?php 
abstract class cDocumentosArchivosdb
{


	function __construct(){}

	function __destruct(){}


	protected function BuscarxCodigo($datos,&$resultado,&$numfilas)
	{
		$spnombre="sel_DocumentosArchivos_xIdDocumentoArchivo";
		$sparam=array(
			'pIdDocumentoArchivo'=> $datos['IdDocumentoArchivo']
			);
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno) )
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al buscar al buscar por codigo. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		return true;
	}

	protected function BuscarxIdDocumentoxIdDocumentoAdjunto($datos,&$resultado,&$numfilas)
	{
		$spnombre="sel_DocumentosArchivos_xIdDocumento_IdDocumentoAdjunto";
		$sparam=array(
			'pIdDocumento'=> $datos['IdDocumento'],
			'pIdDocumentoAdjunto'=> $datos['IdDocumentoAdjunto']
			);
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno) )
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al buscar al buscar por codigo. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		return true;
	}
	
	protected function BuscarxIdDocumento($datos,&$resultado,&$numfilas)
	{
		$spnombre="sel_DocumentosArchivos_xIdDocumento";
		$sparam=array(
			'pIdDocumento'=> $datos['IdDocumento']
			);
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno) )
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al buscar al buscar por codigo. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		return true;
	}
	
	protected function Insertar($datos,&$codigoinsertado)
	{
		$spnombre="ins_DocumentosArchivos";
		$sparam=array(
			'pIdDocumento'=> $datos['IdDocumento'],
			'pIdDocumentoAdjunto'=> $datos['IdDocumentoAdjunto'],
			'pIdRegistroTipoDocumento'=> $datos['IdRegistroTipoDocumento'],
			'pArchivoUbicacion'=> $datos['ArchivoUbicacion'],
			'pArchivoNombre'=> $datos['ArchivoNombre'],
			'pArchivoSize'=> $datos['ArchivoSize'],
			'pArchivoHash'=> $datos['ArchivoHash'],
			'pAltaFecha'=> $datos['AltaFecha'],
			'pAltaUsuario'=> $datos['AltaUsuario'],
			'pAltaApp'=> APP,
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
	
	
	protected function EliminarxIdDocumentoArchivo($datos)
	{
		$spnombre="del_DocumentosArchivos_xIdDocumentoArchivo";
		$sparam=array(
			'pIdDocumentoArchivo'=> $datos['IdDocumentoArchivo']
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