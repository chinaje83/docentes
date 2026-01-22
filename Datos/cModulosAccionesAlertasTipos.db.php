<?php 
abstract class cModulosAccionesAlertasTiposdb
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

	protected function AlertaPrioridadesSP(&$spnombre,&$sparam)
	{
		$spnombre="sel_AlertasPrioridadesSP_combo";
		$sparam=array(
		);
		return true;
	}



	protected function ModulosAccionesSP(&$spnombre,&$sparam)
	{
		$spnombre="sel_ModulosAcciones_combo_Descripcion";
		$sparam=array(
		);
		return true;
	}



	protected function BuscarxCodigo($datos,&$resultado,&$numfilas)
	{
		$spnombre="sel_ModuloAccionesAlertasTipos_xIdAlertaTipo";
		$sparam=array(
			'pIdAlertaTipo'=> $datos['IdAlertaTipo']
		);
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno) )
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al buscar al buscar por codigo. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		return true;
	}



	protected function BuscarxAccion($datos,&$resultado,&$numfilas)
	{
		$spnombre="sel_ModuloAccionesAlertasTipos_xIdAccion";
		$sparam=array(
			'pIdAccion'=> $datos['IdAccion']
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
		$spnombre="sel_ModuloAccionesAlertasTipos_busqueda_avanzada";
		$sparam=array(
			'pxIdModulo'=> $datos['xIdModulo'],
			'pIdModulo'=> $datos['IdModulo'],
			'pxIdAccion'=> $datos['xIdAccion'],
			'pIdAccion'=> $datos['IdAccion'],
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
		$spnombre="ins_ModuloAccionesAlertasTipos";
		$sparam=array(
			'pIdModulo'=> $datos['IdModulo'],
			'pIdAccion'=> $datos['IdAccion'],
			'pIdAlertaPrioridad'=> $datos['IdAlertaPrioridad'],
			'pNombre'=> $datos['Nombre'],
			'pUsaDefault'=> $datos['UsaDefault'],
			'pTextoDefault'=> $datos['TextoDefault'],
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



	protected function Modificar($datos)
	{
		$spnombre="upd_ModuloAccionesAlertasTipos_xIdAlertaTipo";
		$sparam=array(
			'pIdModulo'=> $datos['IdModulo'],
			'pIdAccion'=> $datos['IdAccion'],
			'pIdAlertaPrioridad'=> $datos['IdAlertaPrioridad'],
			'pNombre'=> $datos['Nombre'],
			'pUsaDefault'=> $datos['UsaDefault'],
			'pTextoDefault'=> $datos['TextoDefault'],
			'pUltimaModificacionUsuario'=> $_SESSION['usuariocod'],
			'pUltimaModificacionFecha'=> date("Y-m-d H:i:s"),
			'pIdAlertaTipo'=> $datos['IdAlertaTipo']
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
		$spnombre="del_ModuloAccionesAlertasTipos_xIdAlertaTipo";
		$sparam=array(
			'pIdAlertaTipo'=> $datos['IdAlertaTipo']
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