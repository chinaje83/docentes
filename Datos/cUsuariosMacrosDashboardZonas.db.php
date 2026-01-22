<?php 
abstract class cUsuariosMacrosDashboardZonasdb
{


	function __construct(){}

	function __destruct(){}

	protected function UsuariosMacrosDashboardSP(&$spnombre,&$sparam)
	{
		$spnombre="sel_UsuariosMacrosDashboard_combo_IdUsuarioMacro";
		$sparam=array(
		);
		return true;
	}



	protected function MacrosDashboardEstructurasSP(&$spnombre,&$sparam)
	{
		$spnombre="sel_MacrosDashboardEstructuras_combo_Descripcion";
		$sparam=array(
		);
		return true;
	}



	protected function MacrosDashboardSP(&$spnombre,&$sparam)
	{
		$spnombre="sel_MacrosDashboard_combo_Descripcion";
		$sparam=array(
		);
		return true;
	}



	protected function UsuariosSP(&$spnombre,&$sparam)
	{
		$spnombre="sel_Usuarios_combo_Nombre";
		$sparam=array(
		);
		return true;
	}



	protected function BuscarxCodigo($datos,&$resultado,&$numfilas)
	{
		$spnombre="sel_UsuariosMacrosDashboardZonas_xIdZona";
		$sparam=array(
			'pIdZona'=> $datos['IdZona']
		);
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno) )
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al buscar al buscar por codigo. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		return true;
	}



	protected function BuscarZonasxMacros($datos,&$resultado,&$numfilas)
	{
		$spnombre="sel_UsuariosMacrosDashboardZonas_xIdUsuario_xIdUsuarioMacro";
		$sparam=array(
			'pIdUsuario'=> $_SESSION['usuariocod'],
			'pIdUsuarioMacro'=> $datos['IdUsuarioMacro']
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
		$spnombre="sel_UsuariosMacrosDashboardZonas_busqueda_avanzada";
		$sparam=array(
			'pxIdZona'=> $datos['xIdZona'],
			'pIdZona'=> $datos['IdZona'],
			'pxIdUsuarioMacro'=> $datos['xIdUsuarioMacro'],
			'pIdUsuarioMacro'=> $datos['IdUsuarioMacro'],
			'pxIdEstructura'=> $datos['xIdEstructura'],
			'pIdEstructura'=> $datos['IdEstructura'],
			'pxIdMacro'=> $datos['xIdMacro'],
			'pIdMacro'=> $datos['IdMacro'],
			'pxIdUsuario'=> $datos['xIdUsuario'],
			'pIdUsuario'=> $datos['IdUsuario'],
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
		$spnombre="ins_UsuariosMacrosDashboardZonas";
		$sparam=array(
			'pIdUsuarioMacro'=> $datos['IdUsuarioMacro'],
			'pIdEstructura'=> $datos['IdEstructura'],
			'pIdMacro'=> $datos['IdMacro'],
			'pIdUsuario'=> $_SESSION['usuariocod'],
			'pUltimaModificacionFecha'=> date("Y-m-d H:i:s")
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
		$spnombre="upd_UsuariosMacrosDashboardZonas_xIdZona";
		$sparam=array(
			'pIdUsuarioMacro'=> $datos['IdUsuarioMacro'],
			'pIdEstructura'=> $datos['IdEstructura'],
			'pIdMacro'=> $datos['IdMacro'],
			'pIdUsuario'=> $_SESSION['usuariocod'],
			'pUltimaModificacionFecha'=> date("Y-m-d H:i:s"),
			'pIdZona'=> $datos['IdZona']
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
		$spnombre="del_UsuariosMacrosDashboardZonas_xIdZona";
		$sparam=array(
			'pIdZona'=> $datos['IdZona']
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