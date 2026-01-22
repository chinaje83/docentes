<?php 
abstract class cUsuariosZonasDashboarddb
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



	protected function UsuariosMacrosDashboardZonasSP(&$spnombre,&$sparam)
	{
		$spnombre="sel_UsuariosMacrosDashboardZonas_combo_IdZona";
		$sparam=array(
		);
		return true;
	}



	protected function ModulosDashboardSP(&$spnombre,&$sparam)
	{
		$spnombre="sel_ModulosDashboard_combo_Nombre";
		$sparam=array(
		);
		return true;
	}



	protected function BuscarxCodigo($datos,&$resultado,&$numfilas)
	{
		$spnombre="sel_UsuariosZonasDashboard_xIdUsuarioDashboard";
		$sparam=array(
			'pIdUsuarioDashboard'=> $datos['IdUsuarioDashboard']
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
		$spnombre="sel_UsuariosZonasDashboard_busqueda_avanzada";
		$sparam=array(
			'pxIdUsuario'=> $datos['xIdUsuario'],
			'pIdUsuario'=> $datos['IdUsuario'],
			'pxIdZona'=> $datos['xIdZona'],
			'pIdZona'=> $datos['IdZona'],
			'pxIdModulosDashboard'=> $datos['xIdModulosDashboard'],
			'pIdModulosDashboard'=> $datos['IdModulosDashboard'],
			'pxNombre'=> $datos['xNombre'],
			'pNombre'=> $datos['Nombre'],
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
		$spnombre="ins_UsuariosZonasDashboard";
		$sparam=array(
			'pIdUsuario'=> $_SESSION['usuariocod'],
			'pIdZona'=> $datos['IdZona'],
			'pIdModulosDashboard'=> $datos['IdModulosDashboard'],
			'pNombre'=> $datos['Nombre'],
			'pJson'=> $datos['Json'],
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
		$spnombre="upd_UsuariosZonasDashboard_xIdUsuarioDashboard";
		$sparam=array(
			'pIdUsuario'=> $_SESSION['usuariocod'],
			'pIdZona'=> $datos['IdZona'],
			'pIdModulosDashboard'=> $datos['IdModulosDashboard'],
			'pNombre'=> $datos['Nombre'],
			'pJson'=> $datos['Json'],
			'pUltimaModificacionFecha'=> date("Y-m-d H:i:s"),
			'pIdUsuarioDashboard'=> $datos['IdUsuarioDashboard']
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
		$spnombre="del_UsuariosZonasDashboard_xIdUsuarioDashboard";
		$sparam=array(
			'pIdUsuarioDashboard'=> $datos['IdUsuarioDashboard']
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
		$spnombre="sel_UsuariosZonasDashboard_max_orden";
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
		$spnombre="upd_UsuariosZonasDashboard_Orden_xIdUsuarioDashboard";
		$sparam=array(
			'pIdZona'=> $datos['IdZona'],
			'pOrden'=> $datos['Orden'],
			'pIdUsuarioDashboard'=> $datos['IdUsuarioDashboard']
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