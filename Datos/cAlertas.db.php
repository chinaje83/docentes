<?php 
abstract class cAlertasdb
{


	function __construct(){}

	function __destruct(){}

	protected function ModuloAccionesAlertasTiposSP(&$spnombre,&$sparam)
	{
		$spnombre="sel_ModuloAccionesAlertasTipos_combo_Nombre";
		$sparam=array(
		);
		return true;
	}



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



	protected function UsuariosSP(&$spnombre,&$sparam)
	{
		$spnombre="sel_Usuarios_combo_UsuarioAd";
		$sparam=array(
		);
		return true;
	}



	protected function BuscarxCodigo($datos,&$resultado,&$numfilas)
	{
		$spnombre="sel_Alertas_xIdAlerta";
		$sparam=array(
			'pIdAlerta'=> $datos['IdAlerta'],
			'pIdUsuario'=> $_SESSION['usuariocod']
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
		$spnombre="sel_Alertas_busqueda_avanzada";
		$sparam=array(
			'pxIdAlertaTipo'=> $datos['xIdAlertaTipo'],
			'pIdAlertaTipo'=> $datos['IdAlertaTipo'],
			'pxIdModulo'=> $datos['xIdModulo'],
			'pIdModulo'=> $datos['IdModulo'],
			'pxIdRol'=> $datos['xIdRol'],
			'pIdRol'=> $datos['IdRol'],
			'pxUsuarioGenero'=> $datos['xUsuarioGenero'],
			'pUsuarioGenero'=> $datos['UsuarioGenero'],
			'pIdUsuario'=> $_SESSION['usuariocod'],
			'pxEsObligatorio'=> $datos['xEsObligatorio'],
			'pEsObligatorio'=> $datos['EsObligatorio'],
			'pxFechaAlta'=> $datos['xFechaAlta'],
			'pFechaAlta'=> $datos['FechaAlta'],
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



	protected function BuscarNoLeidas($datos,&$resultado,&$numfilas)
	{
		$spnombre="sel_Alertas_busqueda_sin_leer";
		$sparam=array(
			'pxIdAlertaTipo'=> $datos['xIdAlertaTipo'],
			'pIdAlertaTipo'=> $datos['IdAlertaTipo'],
			'pxIdModulo'=> $datos['xIdModulo'],
			'pIdModulo'=> $datos['IdModulo'],
			'pxIdRol'=> $datos['xIdRol'],
			'pIdRol'=> $datos['IdRol'],
			'pxUsuarioGenero'=> $datos['xUsuarioGenero'],
			'pIdUsuario'=> $_SESSION['usuariocod'],
			'pxEsObligatorio'=> $datos['xEsObligatorio'],
			'pEsObligatorio'=> $datos['EsObligatorio'],
			'pxFechaAlta'=> $datos['xFechaAlta'],
			'pFechaAlta'=> $datos['FechaAlta'],
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
		$spnombre="ins_Alertas";
		$sparam=array(
			'pIdAlertaTipo'=> $datos['IdAlertaTipo'],
			'pIdModulo'=> $datos['IdModulo'],
			'pIdRol'=> $datos['IdRol'],
			'pUsuarioGenero'=> $_SESSION['usuariocod'],
			'pEsObligatorio'=> $datos['EsObligatorio'],
			'pFechaAlta'=> date("Y-m-d H:i:s"),
			'pJsonData'=> $datos['JsonData']
		);
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno))
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al insertar. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		$codigoinsertado=$this->conexion->UltimoCodigoInsertado();

		return true;
	}



	protected function Eliminar($datos)
	{
		$spnombre="del_Alertas_xIdAlerta";
		$sparam=array(
			'pIdAlerta'=> $datos['IdAlerta']
		);
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno))
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al eliminar por codigo. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		return true;
	}

	protected function BuscarCantidadMsgNoLeidosxUsuario(&$resultado,&$numfilas)
	{
		$spnombre="sel_Alertas_cantidad_no_leidos_xIdUsuario";
		$sparam=array(
			'pIdUsuario'=> $_SESSION['usuariocod'],
			'pIdRol'=> implode($_SESSION['rolcod'],",")
		);
		

		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno) )
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al buscar la cantidad de alertas no leidas. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		return true;
	}





}
?>