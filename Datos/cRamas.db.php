<?php 
abstract class cRamasdb
{

	function __construct(){}
	function __destruct(){}
	protected function BuscarxCodigo($datos,&$resultado,&$numfilas)
	{
		$spnombre="sel_Ramas_xIdRama";
		$sparam=array(
			'pIdRama'=> $datos['IdRama']
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
		$spnombre="sel_Ramas_busqueda_avanzada";
		$sparam=array(
			'pxIdRama'=> $datos['xIdRama'],
			'pIdRama'=> $datos['IdRama'],
			'pxIdRamaExterno'=> $datos['xIdRamaExterno'],
			'pIdRamaExterno'=> $datos['IdRamaExterno'],
			'pxCodigo'=> $datos['xCodigo'],
			'pCodigo'=> $datos['Codigo'],
			'pxDescripcion'=> $datos['xDescripcion'],
			'pDescripcion'=> $datos['Descripcion'],
			'pxIdNivelExterno'=> $datos['xIdNivelExterno'],
			'pIdNivelExterno'=> $datos['IdNivelExterno'],
			'pxIdModalidadExterno'=> $datos['xIdModalidadExterno'],
			'pIdModalidadExterno'=> $datos['IdModalidadExterno'],
			'pxIdEnsenanzaExterno'=> $datos['xIdEnsenanzaExterno'],
			'pIdEnsenanzaExterno'=> $datos['IdEnsenanzaExterno'],
			'pxIdDependenciaFuncionalExterno'=> $datos['xIdDependenciaFuncionalExterno'],
			'pIdDependenciaFuncionalExterno'=> $datos['IdDependenciaFuncionalExterno'],
			'pxAuditoriaTitulo'=> $datos['xAuditoriaTitulo'],
			'pAuditoriaTitulo'=> $datos['AuditoriaTitulo'],
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
		$spnombre="sel_Ramas_AuditoriaRapida";
		$sparam=array(
			'pIdRama'=> $datos['IdRama']
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
		$spnombre="ins_Ramas";
		$sparam=array(
			'pIdRamaExterno'=> $datos['IdRamaExterno'],
			'pCodigo'=> $datos['Codigo'],
			'pDescripcion'=> $datos['Descripcion'],
			'pIdNivelExterno'=> $datos['IdNivelExterno'],
			'pIdModalidadExterno'=> $datos['IdModalidadExterno'],
			'pIdEnsenanzaExterno'=> $datos['IdEnsenanzaExterno'],
			'pIdDependenciaFuncionalExterno'=> $datos['IdDependenciaFuncionalExterno'],
			'pAuditoriaTitulo'=> $datos['AuditoriaTitulo'],
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
		$spnombre="upd_Ramas_xIdRama";
		$sparam=array(
			'pIdRamaExterno'=> $datos['IdRamaExterno'],
			'pCodigo'=> $datos['Codigo'],
			'pDescripcion'=> $datos['Descripcion'],
			'pIdNivelExterno'=> $datos['IdNivelExterno'],
			'pIdModalidadExterno'=> $datos['IdModalidadExterno'],
			'pIdEnsenanzaExterno'=> $datos['IdEnsenanzaExterno'],
			'pIdDependenciaFuncionalExterno'=> $datos['IdDependenciaFuncionalExterno'],
			'pAuditoriaTitulo'=> $datos['AuditoriaTitulo'],
			'pUltimaModificacionUsuario'=> $_SESSION['usuariocod'],
			'pUltimaModificacionFecha'=> $datos['UltimaModificacionFecha'],
			'pIdRama'=> $datos['IdRama']
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
		$spnombre="del_Ramas_xIdRama";
		$sparam=array(
			'pIdRama'=> $datos['IdRama']
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
		$spnombre="upd_Ramas_Estado_xIdRama";
		$sparam=array(
			'pEstado'=> $datos['Estado'],
			'pUltimaModificacionUsuario'=> $_SESSION['usuariocod'],
			'pIdRama'=> $datos['IdRama']
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