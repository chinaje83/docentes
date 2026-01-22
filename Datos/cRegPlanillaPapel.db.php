<?php 
abstract class cRegPlanillaPapeldb
{


	function __construct(){}

	function __destruct(){}

	protected function BuscarxCodigo($datos,&$resultado,&$numfilas)
	{
		$spnombre="sel_reg_papel_xIdRegistro";
		$sparam=array(
			'pIdRegistro'=> $datos['IdRegistro']
		);
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno) )
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al buscar al buscar por codigo. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		return true;
	}

	protected function TraerTodosRegistros($datos,&$resultado,&$numfilas)
	{
		$spnombre="sel_reg_papel_todos";
	
		$sparam=array(
			'pClaveEscuela'=> $datos['ClaveEscuela'],
			'pAltaFecha'=> FuncionesPHPLocal::ConvertirFecha($datos['AltaFecha'],'dd/mm/aaaa','aaaa-mm-dd')." 00:00:00",
		);
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno) )
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al buscar el registro. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		return true;
	}

	protected function TraerTodosRegistrosEdit($datos,&$resultado,&$numfilas)
	{
		$spnombre="sel_reg_papel_todos_edit";
	
		$sparam=array(
			'pClaveEscuela'=> $datos['ClaveEscuela'],
			'pAltaFecha'=> FuncionesPHPLocal::ConvertirFecha($datos['AltaFecha'],'dd/mm/aaaa','aaaa-mm-dd')." 00:00:00",
			'pIdRegistro'=> $datos['IdRegistro']
		);
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno) )
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al buscar el registro. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		return true;
	}

	protected function BusquedaAvanzada($datos,&$resultado,&$numfilas)
	{

		$spnombre="sel_reg_papel_busqueda_avanzada";
		$sparam=array(
			'pxIdRegistro'=> $datos['xIdRegistro'],
			'pIdRegistro'=> $datos['IdRegistro'],
			'pxIdRegistroTipocod'=> $datos['xIdRegistroTipocod'],
			'pIdRegistroTipocod'=> $datos['IdRegistroTipocod'],
			'pxIdDocumento'=> $datos['xIdDocumento'],
			'pIdDocumento'=> $datos['IdDocumento'],
			'pxIdTipoDocumento'=> $datos['xIdTipoDocumento'],
			'pIdTipoDocumento'=> $datos['IdTipoDocumento'],
			'pxDescripcion'=> $datos['xDescripcion'],
			'pDescripcion'=> $datos['Descripcion'],
			'pxClaveEscuela'=> $datos['xClaveEscuela'],
			'pClaveEscuela'=> $datos['ClaveEscuela'],
			'pxIdDistrito'=> $datos['xIdDistrito'],
			'pIdDistrito'=> $datos['IdDistrito'],
			'pxIdTipoOrganismo'=> $datos['xIdTipoOrganismo'],
			'pIdTipoOrganismo'=> $datos['IdTipoOrganismo'],
			'pxNroEscuela'=> $datos['xNroEscuela'],
			'pNroEscuela'=> $datos['NroEscuela'],
			'pxEntregoPlanilla'=> $datos['xEntregoPlanilla'],
			'pEntregoPlanilla'=> $datos['EntregoPlanilla'],
			'pxIdArea'=> $datos['xIdArea'],
			'pIdArea'=> $datos['IdArea'],
			'pxIdRol'=> $datos['xIdRol'],
			'pIdRol'=> $datos['IdRol'],
			'pxFechaDesde'=> $datos['xFechaDesde'],
			'pFechaDesde'=> $datos['FechaDesde'],
			'pxFechaHasta'=> $datos['xFechaHasta'],
			'pFechaHasta'=> $datos['FechaHasta'],
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



	protected function Insertar($datos,&$codigoinsertado)
	{
		$spnombre="ins_reg_papel";
		$sparam=array(
			'pIdRegistroTipocod'=> $datos['IdRegistroTipocod'],
			'pDescripcion'=> $datos['Descripcion'],
			'pClaveEscuela'=> $datos['ClaveEscuela'],
			'pIdDistrito'=> $datos['IdDistrito'],
			'pIdTipoOrganismo'=> $datos['IdTipoOrganismo'],
			'pAltaUsuario'=> $datos['AltaUsuario'],
			'pAltaFecha'=> $datos['AltaFecha'],
			'pNroHojas'=> $datos['NroHojas'],
			'pNroEscuela'=> $datos['NroEscuela'],
			'pEntregoPlanilla'=> $datos['EntregoPlanilla'],
			'pIdArea'=> $datos['IdArea'],
			'pIdRol'=> $datos['IdRol'],
			'pUltimaModificacionFecha'=> $datos['UltimaModificacionFecha'],
			'pUltimaModificacionUsuario'=> $_SESSION['usuariocod']
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
		$spnombre="upd_RegPlanillaPapel_xIdRegistro";
		$sparam=array(
			'pIdRegistroTipocod'=> $datos['IdRegistroTipocod'],
			'pDescripcion'=> $datos['Descripcion'],
			'pClaveEscuela'=> $datos['ClaveEscuela'],
			'pIdDistrito'=> $datos['IdDistrito'],
			'pIdTipoOrganismo'=> $datos['IdTipoOrganismo'],
			'pAltaUsuario'=> $datos['AltaUsuario'],
			'pAltaFecha'=> $datos['AltaFecha'],
			'pNroHojas'=> $datos['NroHojas'],
			'pNroEscuela'=> $datos['NroEscuela'],
			'pEntregoPlanilla'=> $datos['EntregoPlanilla'],
			'pUltimaModificacionFecha'=> $datos['UltimaModificacionFecha'],
			'pUltimaModificacionUsuario'=> $_SESSION['usuariocod'],
			'pIdRegistro'=> $datos['IdRegistro']
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

		$spnombre="del_reg_papel_xIdRegistro";
		$sparam=array(
			'pIdRegistro'=> $datos['IdRegistro']
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