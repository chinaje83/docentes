<?php 
abstract class cTiposOrganizaciondb
{

	function __construct(){}
	function __destruct(){}
	protected function BuscarxCodigo($datos,&$resultado,&$numfilas)
	{
		$spnombre="sel_TiposOrganizacion_xIdTipoOrganizacion";
		$sparam=array(
			'pIdTipoOrganizacion'=> $datos['IdTipoOrganizacion']
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
		$spnombre="sel_TiposOrganizacion_busqueda_avanzada";
		$sparam=array(
			'pxIdTipoOrganizacion'=> $datos['xIdTipoOrganizacion'],
			'pIdTipoOrganizacion'=> $datos['IdTipoOrganizacion'],
			'pxIdTipoOrganizacionExterno'=> $datos['xIdTipoOrganizacionExterno'],
			'pIdTipoOrganizacionExterno'=> $datos['IdTipoOrganizacionExterno'],
			'pxTipoOrganizacion'=> $datos['xTipoOrganizacion'],
			'pTipoOrganizacion'=> $datos['TipoOrganizacion'],
			'pxDescripcion'=> $datos['xDescripcion'],
			'pDescripcion'=> $datos['Descripcion'],
			'pxIdEnsenanzaExterno'=> $datos['xIdEnsenanzaExterno'],
			'pIdEnsenanzaExterno'=> $datos['IdEnsenanzaExterno'],
			'pxEstablecimientoEducativo'=> $datos['xEstablecimientoEducativo'],
			'pEstablecimientoEducativo'=> $datos['EstablecimientoEducativo'],
			'pxIdEnsenanzaExternoIngreso'=> $datos['xIdEnsenanzaExternoIngreso'],
			'pIdEnsenanzaExternoIngreso'=> $datos['IdEnsenanzaExternoIngreso'],
			'pxIdRamaExterno'=> $datos['xIdRamaExterno'],
			'pIdRamaExterno'=> $datos['IdRamaExterno'],
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
		$spnombre="sel_TiposOrganizacion_AuditoriaRapida";
		$sparam=array(
			'pIdTipoOrganizacion'=> $datos['IdTipoOrganizacion']
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
		$spnombre="ins_TiposOrganizacion";
		$sparam=array(
			'pIdTipoOrganizacionExterno'=> $datos['IdTipoOrganizacionExterno'],
			'pTipoOrganizacion'=> $datos['TipoOrganizacion'],
			'pDescripcion'=> $datos['Descripcion'],
			'pIdEnsenanzaExterno'=> $datos['IdEnsenanzaExterno'],
			'pEstablecimientoEducativo'=> $datos['EstablecimientoEducativo'],
			'pIdEnsenanzaExternoIngreso'=> $datos['IdEnsenanzaExternoIngreso'],
			'pIdRamaExterno'=> $datos['IdRamaExterno'],
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
		$spnombre="upd_TiposOrganizacion_xIdTipoOrganizacion";
		$sparam=array(
			'pIdTipoOrganizacionExterno'=> $datos['IdTipoOrganizacionExterno'],
			'pTipoOrganizacion'=> $datos['TipoOrganizacion'],
			'pDescripcion'=> $datos['Descripcion'],
			'pIdEnsenanzaExterno'=> $datos['IdEnsenanzaExterno'],
			'pEstablecimientoEducativo'=> $datos['EstablecimientoEducativo'],
			'pIdEnsenanzaExternoIngreso'=> $datos['IdEnsenanzaExternoIngreso'],
			'pIdRamaExterno'=> $datos['IdRamaExterno'],
			'pUltimaModificacionUsuario'=> $_SESSION['usuariocod'],
			'pUltimaModificacionFecha'=> $datos['UltimaModificacionFecha'],
			'pIdTipoOrganizacion'=> $datos['IdTipoOrganizacion']
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
		$spnombre="del_TiposOrganizacion_xIdTipoOrganizacion";
		$sparam=array(
			'pIdTipoOrganizacion'=> $datos['IdTipoOrganizacion']
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
		$spnombre="upd_TiposOrganizacion_Estado_xIdTipoOrganizacion";
		$sparam=array(
			'pEstado'=> $datos['Estado'],
			'pUltimaModificacionUsuario'=> $_SESSION['usuariocod'],
			'pIdTipoOrganizacion'=> $datos['IdTipoOrganizacion']
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