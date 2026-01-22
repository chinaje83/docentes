<?php 
abstract class cModulosDashboarddb
{


	function __construct(){}

	function __destruct(){}

	protected function ModulosSP(&$spnombre,&$sparam)
	{
		$spnombre="sel_Modulos_combo_Dashboard";
		$sparam=array(
		);
		return true;
	}




	protected function RolesSP(&$spnombre,&$sparam)
	{
		$spnombre="sel_Roles_combo_Descripcion";
		$sparam=array(
		);
		return true;
	}


	protected function BuscarxCodigo($datos,&$resultado,&$numfilas)
	{
		$spnombre="sel_ModulosDashboard_xIdModulosDashboard";
		$sparam=array(
			'pIdModulosDashboard'=> $datos['IdModulosDashboard']
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
		$spnombre="sel_ModulosDashboard_busqueda_avanzada";
		$sparam=array(
			'pxIdModulo'=> $datos['xIdModulo'],
			'pIdModulo'=> $datos['IdModulo'],
			'pxIdRol'=> $datos['xIdRol'],
			'pIdRol'=> $datos['IdRol'],
			'pxNombre'=> $datos['xNombre'],
			'pNombre'=> $datos['Nombre'],
			'pxArchivo'=> $datos['xArchivo'],
			'pArchivo'=> $datos['Archivo'],
			'pxEsDefault'=> $datos['xEsDefault'],
			'pEsDefault'=> $datos['EsDefault'],
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



	protected function Insertar($datos,&$codigoinsertado)
	{
		$spnombre="ins_ModulosDashboard";
		$sparam=array(
			'pIdModulo'=> $datos['IdModulo'],
			'pArchivo'=> $datos['Archivo'],
			'pNombre'=> $datos['Nombre'],
			'pEsDefault'=> $datos['EsDefault'],
			'pEstado'=> $datos['Estado'],
			'pUltimaModificacionFecha'=> date("Y-m-d H:i:s"),
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



	protected function InsertarRoles($datos)
	{
		$spnombre="ins_ModulosDashboardRoles";
		$sparam=array(
			'pIdModulosDashboard'=> $datos['IdModulosDashboard'],
			'pIdRol'=> $datos['IdRol']
		);
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno))
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al modificar. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		return true;
	}



	protected function Modificar($datos)
	{
		$spnombre="upd_ModulosDashboard_xIdModulosDashboard";
		$sparam=array(
			'pIdModulo'=> $datos['IdModulo'],
			'pArchivo'=> $datos['Archivo'],
			'pNombre'=> $datos['Nombre'],
			'pEsDefault'=> $datos['EsDefault'],
			'pUltimaModificacionFecha'=> date("Y-m-d H:i:s"),
			'pUltimaModificacionUsuario'=> $_SESSION['usuariocod'],
			'pIdModulosDashboard'=> $datos['IdModulosDashboard']
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
		$spnombre="del_ModulosDashboard_xIdModulosDashboard";
		$sparam=array(
			'pIdModulosDashboard'=> $datos['IdModulosDashboard']
		);
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno))
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al eliminar por codigo. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		return true;
	}



	protected function EliminarRoles($datos)
	{
		$spnombre="del_ModulosDashboardRoles_xIdModulosDashboard";
		$sparam=array(
			'pIdModulosDashboard'=> $datos['IdModulosDashboard']
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
		$spnombre="upd_ModulosDashboard_Estado_xIdModulosDashboard";
		$sparam=array(
			'pEstado'=> $datos['Estado'],
			'pUltimaModificacionUsuario'=> $_SESSION['usuariocod'],
			'pUltimaModificacionFecha'=> date("Y-m-d H:i:s"),
			'pIdModulosDashboard'=> $datos['IdModulosDashboard']
		);
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno))
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al modificar el estado. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		return true;
	}



	protected function traerModulosxUsuarioDB($datos,&$resultado,&$numfilas)
	{
		$spnombre="sel_Modulos_xIdUsuario";
		$sparam=array(
			'pIdUsuario'=> $datos['IdUsuario']
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