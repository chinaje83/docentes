<?php 
abstract class cUsuariosMacrosDashboarddb
{


	function __construct(){}

	function __destruct(){}

	protected function UsuariosSP(&$spnombre,&$sparam)
	{
		$spnombre="sel_Usuarios_combo_Nombre";
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



	protected function BuscarxCodigo($datos,&$resultado,&$numfilas)
	{
		$spnombre="sel_UsuariosMacrosDashboard_xIdUsuarioMacro";
		$sparam=array(
			'pIdUsuarioMacro'=> $datos['IdUsuarioMacro']
		);
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno) )
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al buscar al buscar por codigo. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		return true;
	}



	protected function BuscarMacros($datos,&$resultado,&$numfilas)
	{
		$spnombre="sel_UsuariosMacrosDashboard_xIdUsuario";
		$sparam=array(
			'pIdUsuario'=> $_SESSION['usuariocod'],
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
		$spnombre="sel_UsuariosMacrosDashboard_busqueda_avanzada";
		$sparam=array(
			'pxIdUsuarioMacro'=> $datos['xIdUsuarioMacro'],
			'pIdUsuarioMacro'=> $datos['IdUsuarioMacro'],
			'pxIdUsuario'=> $datos['xIdUsuario'],
			'pIdUsuario'=> $datos['IdUsuario'],
			'pxIdMacro'=> $datos['xIdMacro'],
			'pIdMacro'=> $datos['IdMacro'],
			'pxOrden'=> $datos['xOrden'],
			'pOrden'=> $datos['Orden'],
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
		$spnombre="ins_UsuariosMacrosDashboard";
		$sparam=array(
			'pIdUsuario'=> $_SESSION['usuariocod'],
			'pIdMacro'=> $datos['IdMacro'],
			'pOrden'=> $datos['Orden'],
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
		$spnombre="upd_UsuariosMacrosDashboard_xIdUsuarioMacro";
		$sparam=array(
			'pIdUsuario'=> $_SESSION['usuariocod'],
			'pIdMacro'=> $datos['IdMacro'],
			'pUltimaModificacionFecha'=> date("Y-m-d H:i:s"),
			'pIdUsuarioMacro'=> $datos['IdUsuarioMacro']
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
		$spnombre="del_UsuariosMacrosDashboard_xIdUsuarioMacro";
		$sparam=array(
			'pIdUsuarioMacro'=> $datos['IdUsuarioMacro']
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
		$spnombre="sel_UsuariosMacrosDashboard_max_orden";
		$sparam=array(
			'pIdUsuario'=> $_SESSION['usuariocod'],
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
		$spnombre="upd_UsuariosMacrosDashboard_Orden_xIdUsuarioMacro";
		$sparam=array(
			'pOrden'=> $datos['Orden'],
			'pIdUsuarioMacro'=> $datos['IdUsuarioMacro']
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