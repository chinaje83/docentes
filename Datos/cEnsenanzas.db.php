<?php 
abstract class cEnsenanzasdb
{

	function __construct(){}
	function __destruct(){}
	protected function BuscarxCodigo($datos,&$resultado,&$numfilas)
	{
		$spnombre="sel_Ensenanzas_xIdEnsenanza";
		$sparam=array(
			'pIdEnsenanza'=> $datos['IdEnsenanza']
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
		$spnombre="sel_Ensenanzas_busqueda_avanzada";
		$sparam=array(
			'pxIdEnsenanza'=> $datos['xIdEnsenanza'],
			'pIdEnsenanza'=> $datos['IdEnsenanza'],
			'pxIdEnsenanzaExterno'=> $datos['xIdEnsenanzaExterno'],
			'pIdEnsenanzaExterno'=> $datos['IdEnsenanzaExterno'],
			'pxTipoEnsenanza'=> $datos['xTipoEnsenanza'],
			'pTipoEnsenanza'=> $datos['TipoEnsenanza'],
			'pxDescripcion'=> $datos['xDescripcion'],
			'pDescripcion'=> $datos['Descripcion'],
			'pxEstado'=> $datos['xEstado'],
			'pEstado'=> $datos['Estado'],
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
		$spnombre="sel_Ensenanzas_AuditoriaRapida";
		$sparam=array(
			'pIdEnsenanza'=> $datos['IdEnsenanza']
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
		$spnombre="ins_Ensenanzas";
		$sparam=array(
			'pIdEnsenanzaExterno'=> $datos['IdEnsenanzaExterno'],
			'pTipoEnsenanza'=> $datos['TipoEnsenanza'],
			'pDescripcion'=> $datos['Descripcion'],
			'pEstado'=> $datos['Estado'],
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
		$spnombre="upd_Ensenanzas_xIdEnsenanza";
		$sparam=array(
			'pIdEnsenanzaExterno'=> $datos['IdEnsenanzaExterno'],
			'pTipoEnsenanza'=> $datos['TipoEnsenanza'],
			'pDescripcion'=> $datos['Descripcion'],
			'pUltimaModificacionUsuario'=> $_SESSION['usuariocod'],
			'pUltimaModificacionFecha'=> $datos['UltimaModificacionFecha'],
			'pIdEnsenanza'=> $datos['IdEnsenanza']
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
		$spnombre="del_Ensenanzas_xIdEnsenanza";
		$sparam=array(
			'pIdEnsenanza'=> $datos['IdEnsenanza']
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
		$spnombre="upd_Ensenanzas_Estado_xIdEnsenanza";
		$sparam=array(
			'pEstado'=> $datos['Estado'],
			'pUltimaModificacionUsuario'=> $_SESSION['usuariocod'],
			'pIdEnsenanza'=> $datos['IdEnsenanza']
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