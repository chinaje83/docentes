<?php 
abstract class cDocumentacionAdjuntadb
{


	function __construct(){}

	function __destruct(){}

	protected function BuscarxCodigo($datos,&$resultado,&$numfilas)
	{
		$spnombre="sel_DocumentacionAdjunta_xIdDocumentoAdjunto";
		$sparam=array(
			'pIdDocumentoAdjunto'=> $datos['IdDocumentoAdjunto']
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
		$spnombre="sel_DocumentacionAdjunta_busqueda_avanzada";
		$sparam=array(
			'pxIdDocumentoAdjunto'=> $datos['xIdDocumentoAdjunto'],
			'pIdDocumentoAdjunto'=> $datos['IdDocumentoAdjunto'],
			'pxNombre'=> $datos['xNombre'],
			'pNombre'=> $datos['Nombre'],
			'pxTipoPermitido'=> $datos['xTipoPermitido'],
			'pTipoPermitido'=> $datos['TipoPermitido'],
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
		$spnombre="sel_DocumentacionAdjunta_AuditoriaRapida";
		$sparam=array(
			'pIdDocumentoAdjunto'=> $datos['IdDocumentoAdjunto']
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
		$spnombre="ins_DocumentacionAdjunta";
		$sparam=array(
			'pNombre'=> $datos['Nombre'],
			'pTipoPermitido'=> str_replace(array(" ","\t","\n","\r","\0","\x0B"),"",strtoupper($datos['TipoPermitido'])),
			'pIdEstado'=> $datos['IdEstado'],
			'pFechaAlta'=> $datos['FechaAlta'],
			'pConstante'=> strtoupper($datos['Constante']),
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

		$codigoinsertado=$this->conexion->UltimoCodigoInsertado();

		return true;
	}



	protected function Modificar($datos)
	{
		$spnombre="upd_DocumentacionAdjunta_xIdDocumentoAdjunto";
		$sparam=array(
			'pNombre'=> $datos['Nombre'],
			'pTipoPermitido'=> str_replace(array(" ","\t","\n","\r","\0","\x0B"),"",strtoupper($datos['TipoPermitido'])),
			'pConstante'=> strtoupper($datos['Constante']),
			'pUltimaModificacionUsuario'=> $_SESSION['usuariocod'],
			'pUltimaModificacionFecha'=> $datos['UltimaModificacionFecha'],
			'pUltimaModificacionApp'=> APP,
			'pIdDocumentoAdjunto'=> $datos['IdDocumentoAdjunto']
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
		$spnombre="del_DocumentacionAdjunta_xIdDocumentoAdjunto";
		$sparam=array(
			'pIdDocumentoAdjunto'=> $datos['IdDocumentoAdjunto']
		);
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno))
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al eliminar por codigo. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		return true;
	}



	protected function ModificarEstado($datos)
	{
		$spnombre="upd_DocumentacionAdjunta_IdEstado_xIdDocumentoAdjunto";
		$sparam=array(
			'pIdEstado'=> $datos['IdEstado'],
			'pUltimaModificacionUsuario'=> $_SESSION['usuariocod'],
			'pIdDocumentoAdjunto'=> $datos['IdDocumentoAdjunto']
		);
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno))
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al modificar el estado. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		return true;
	}
	
	
	protected function ModificarTipoPermitido($datos)
	{
		$spnombre="upd_DocumentacionAdjunta_TipoPermitido_xIdDocumentoAdjunto";
		$sparam=array(
			'pUltimaModificacionUsuario'=> $_SESSION['usuariocod'],
			'pUltimaModificacionFecha'=> $datos['UltimaModificacionFecha'],
			'pUltimaModificacionApp'=> APP,
			'pIdDocumentoAdjunto'=> $datos['IdDocumentoAdjunto']
		);
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno))
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al modificar el estado. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		return true;
	}





}
?>