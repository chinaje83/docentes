<?php 
abstract class cModulosAlertasModulosRolesdb
{


	function __construct(){}

	function __destruct(){}

	protected function ModulosSP(&$spnombre,&$sparam)
	{
		$spnombre="sel_Modulos_combo";
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
		$spnombre="sel_ModulosAlertasModulosRoles_xIdRegistro";
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



	protected function BusquedaAvanzada($datos,&$resultado,&$numfilas)
	{
		$spnombre="sel_ModulosAlertasModulosRoles_busqueda_avanzada";
		$sparam=array(
			'pxIdModuloInicial'=> $datos['xIdModuloInicial'],
			'pIdModuloInicial'=> $datos['IdModuloInicial'],
			'pxIdModuloFinal'=> $datos['xIdModuloFinal'],
			'pIdModuloFinal'=> $datos['IdModuloFinal'],
			'pxIdRol'=> $datos['xIdRol'],
			'pIdRol'=> $datos['IdRol'],
			'pxEnviaMail'=> $datos['xEnviaMail'],
			'pEnviaMail'=> $datos['EnviaMail'],
			'pxEsObligatorio'=> $datos['xEsObligatorio'],
			'pEsObligatorio'=> $datos['EsObligatorio'],
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
		$spnombre="ins_ModulosAlertasModulosRoles";
		$sparam=array(
			'pIdModuloInicial'=> $datos['IdModuloInicial'],
			'pEnviaMail'=> $datos['EnviaMail'],
			'pEsObligatorio'=> $datos['EsObligatorio'],
			'pUltimaModificacionUsuario'=> $_SESSION['usuariocod'],
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



	protected function InsertarModulosFinal($datos)
	{
		$spnombre="ins_ModulosAlertasModulosRolesModulosFinales";
		$sparam=array(
			'pIdModulo'=> $datos['IdModulo'],
			'pIdRegistro'=> $datos['IdRegistro']
		);
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno))
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al modificar. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		return true;
	}



	protected function InsertarRoles($datos)
	{
		$spnombre="ins_ModulosAlertasModulosRolesRoles";
		$sparam=array(
			'pIdRol'=> $datos['IdRol'],
			'pIdRegistro'=> $datos['IdRegistro']
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
		$spnombre="upd_ModulosAlertasModulosRoles_xIdRegistro";
		$sparam=array(
			'pIdModuloInicial'=> $datos['IdModuloInicial'],
			'pEnviaMail'=> $datos['EnviaMail'],
			'pEsObligatorio'=> $datos['EsObligatorio'],
			'pUltimaModificacionUsuario'=> $_SESSION['usuariocod'],
			'pUltimaModificacionFecha'=> date("Y-m-d H:i:s"),
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
		$spnombre="del_ModulosAlertasModulosRoles_xIdRegistro";
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



	protected function EliminarModulosFinales($datos)
	{
		$spnombre="del_ModulosAlertasModulosRolesModulosFinales_xIdRegistro";
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



	protected function EliminarRoles($datos)
	{
		$spnombre="del_ModulosAlertasModulosRolesRoles_xIdRegistro";
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



	protected function BuscarRepetidos($datos,&$resultado,&$numfilas)
	{
		$spnombre="sel_ModulosAlertasModulosRoles_busqueda_repetidos";
		$sparam=array(
			'pIdModuloInicial'=> $datos['IdModuloInicial'],
			'pxIdRegistro'=> $datos['xIdRegistro'],
			'pIdRegistro'=> $datos['IdRegistro']
		);

		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno) )
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al realizar la búsqueda avanzada. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		return true;
	}





}
?>