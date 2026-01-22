<?php 
abstract class cAlertasVisualizacionesdb
{


	function __construct(){}

	function __destruct(){}

	protected function AlertasSP(&$spnombre,&$sparam)
	{
		$spnombre="sel_Alertas_combo_FechaAlta";
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
		$spnombre="sel_AlertasVisualizaciones_xIdAlertaVisualizacion";
		$sparam=array(
			'pIdAlertaVisualizacion'=> $datos['IdAlertaVisualizacion']
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
		$spnombre="sel_AlertasVisualizaciones_busqueda_avanzada";
		$sparam=array(
			'pxIdAlerta'=> $datos['xIdAlerta'],
			'pIdAlerta'=> $datos['IdAlerta'],
			'pxIdUsuario'=> $datos['xIdUsuario'],
			'pIdUsuario'=> $datos['IdUsuario'],
			'pxFechaLectura'=> $datos['xFechaLectura'],
			'pFechaLectura'=> $datos['FechaLectura'],
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
		$spnombre="ins_AlertasVisualizaciones";
		$sparam=array(
			'pIdAlerta'=> $datos['IdAlerta'],
			'pIdUsuario'=> $datos['IdUsuario'],
			'pFechaLectura'=> $datos['FechaLectura'],
			'pIP'=> $datos['IP'],
			'pNavegador'=> $datos['Navegador'],
			'pSO'=> $datos['SO']
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
		$spnombre="upd_AlertasVisualizaciones_xIdAlertaVisualizacion";
		$sparam=array(
			'pIdAlerta'=> $datos['IdAlerta'],
			'pIdUsuario'=> $datos['IdUsuario'],
			'pFechaLectura'=> $datos['FechaLectura'],
			'pIP'=> $datos['IP'],
			'pNavegador'=> $datos['Navegador'],
			'pSO'=> $datos['SO'],
			'pIdAlertaVisualizacion'=> $datos['IdAlertaVisualizacion']
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
		$spnombre="del_AlertasVisualizaciones_xIdAlertaVisualizacion";
		$sparam=array(
			'pIdAlertaVisualizacion'=> $datos['IdAlertaVisualizacion']
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