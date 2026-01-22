<?php 
abstract class cModulosAccionesdb
{


	function __construct(){}

	function __destruct(){}

	protected function BuscarxCodigo($datos,&$resultado,&$numfilas)
	{
		$spnombre="sel_modulosacciones_xIdAccion";
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



	protected function BuscarAccionesHabilitadasDB($datos,&$resultado,&$numfilas)
	{
		$spnombre="sel_modulos_acciones_xrol_habilitados_xrolcod";
		$sparam=array(
			'pxIdRol'=> $datos['xIdRol'],
			'pIdRol'=> $datos['IdRol'],
			'porderby'=> $datos['orderby']
		);

		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno) )
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al realizar la búsqueda de acciones habilitadas. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		return true;
	}



	protected function BusquedaAvanzada($datos,&$resultado,&$numfilas)
	{
		$spnombre="sel_modulosacciones_busqueda_avanzada";
		$sparam=array(
			'pxIdAccion'=> $datos['xIdAccion'],
			'pIdAccion'=> $datos['IdAccion'],
			'pxIdModulo'=> $datos['xIdModulo'],
			'pIdModulo'=> $datos['IdModulo'],
			'pxDescripcion'=> $datos['xDescripcion'],
			'pDescripcion'=> $datos['Descripcion'],
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
		$spnombre="ins_modulosacciones";
		$sparam=array(
			'pIdModulo'=> $datos['IdModulo'],
			'pDescripcion'=> $datos['Descripcion'],
			'pUltimaModificacionUsuario'=> $datos['UltimaModificacionUsuario'],
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
		$spnombre="upd_modulosacciones_xIdAccion";
		$sparam=array(
			'pIdModulo'=> $datos['IdModulo'],
			'pDescripcion'=> $datos['Descripcion'],
			'pUltimaModificacionUsuario'=> $datos['UltimaModificacionUsuario'],
			'pUltimaModificacionFecha'=> $datos['UltimaModificacionFecha'],
			'pIdAccion'=> $datos['IdAccion']
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
		$spnombre="del_modulosacciones_xIdAccion";
		$sparam=array(
			'pIdAccion'=> $datos['IdAccion']
		);
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno))
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al eliminar por codigo. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		return true;
	}


	protected function BuscarAccionesxModulos($datos,&$resultado,&$numfilas)
	{
		$spnombre="sel_ModulosAcciones_xIdModulo";
		$sparam=array(
			'pIdModulo'=> $datos['IdModulo'],
			);
		
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno))
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al buscar los modulos para actualizar.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		
		return true;
	}





}
?>