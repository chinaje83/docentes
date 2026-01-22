<?php 
abstract class cDocumentacionAdjuntaTiposdb
{


	function __construct(){}

	function __destruct(){}

	protected function BuscarxCodigo($datos,&$resultado,&$numfilas)
	{
		$spnombre="sel_DocumentacionAdjuntaTipos_xIdDocumentoAdjunto_IdDocumentoTipo";
		$sparam=array(
			'pIdDocumentoAdjunto'=> $datos['IdDocumentoAdjunto'],
			'pIdDocumentoTipo'=> $datos['IdDocumentoTipo']
			);
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno) )
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al buscar al buscar por codigo. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		return true;
	}
	
	protected function BuscarxIdDocumentoAdjunto($datos,&$resultado,&$numfilas)
	{
		$spnombre="sel_DocumentacionAdjuntaTipos_xIdDocumentoAdjunto";
		$sparam=array(
			'pIdDocumentoAdjunto'=> $datos['IdDocumentoAdjunto']
			);
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno) )
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al buscar al buscar por documento adjunto. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		return true;
	}
	
	protected function Insertar($datos)
	{
		$spnombre="ins_DocumentacionAdjuntaTipos";
		$sparam=array(
			'pIdDocumentoAdjunto'=> $datos['IdDocumentoAdjunto'],
			'pIdDocumentoTipo'=> $datos['IdDocumentoTipo'],
			'pFechaAlta'=> $datos['FechaAlta'],
			'pAltaUsuario'=> $datos['AltaUsuario'],
			'pAltaApp'=> APP,
			'pUltimaModificacionUsuario'=> $datos['UltimaModificacionUsuario'],
			'pUltimaModificacionFecha'=> $datos['UltimaModificacionFecha'],
			'pUltimaModificacionApp'=> APP
			);
		
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno))
		{
			echo "aca";die;
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al insertar. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		return true;
	}

	protected function Eliminar($datos)
	{
		$spnombre="del_DocumentacionAdjuntaTipos_xIdDocumentoAdjunto_IdDocumentoTipo";
		$sparam=array(
			'pIdDocumentoAdjunto'=> $datos['IdDocumentoAdjunto'],
			'pIdDocumentoTipo'=> $datos['IdDocumentoTipo']
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