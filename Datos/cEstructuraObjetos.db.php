<?php 
abstract class cEstructuraObjetosdb
{


	function __construct(){}

	function __destruct(){}

	protected function BuscarxCodigo($datos,&$resultado,&$numfilas)
	{
		$spnombre="sel_EstructuraObjetos_xIdObjeto";
		$sparam=array(
			'pIdObjeto'=> $datos['IdObjeto']
		);
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno) )
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al buscar al buscar por codigo. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		return true;
	}
	
	
	protected function BuscarTiposObjetosCampos($datos,&$resultado,&$numfilas)
	{

		$spnombre="sel_EstructuraCamposObjetos_Activos";
		$sparam=array(
			);
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno) )
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al buscar los tipos de campos activos. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		return true;
	}



	protected function BusquedaAvanzada($datos,&$resultado,&$numfilas)
	{
		$spnombre="sel_EstructuraObjetos_busqueda_avanzada";
		$sparam=array(
			'pxIdObjeto'=> $datos['xIdObjeto'],
			'pIdObjeto'=> $datos['IdObjeto'],
			'pxNombre'=> $datos['xNombre'],
			'pNombre'=> $datos['Nombre'],
			'pxClase'=> $datos['xClase'],
			'pClase'=> $datos['Clase'],
			'pxMetodo'=> $datos['xMetodo'],
			'pMetodo'=> $datos['Metodo'],
			'pxTipoCampoEditable'=> $datos['xTipoCampoEditable'],
			'pTipoCampoEditable'=> $datos['TipoCampoEditable'],
			'pxTieneValores'=> $datos['xTieneValores'],
			'pTieneValores'=> $datos['TieneValores'],
			'pxArchivo'=> $datos['xArchivo'],
			'pArchivo'=> $datos['Archivo'],
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
		$spnombre="ins_EstructuraObjetos";
		$sparam=array(
			'pNombre'=> $datos['Nombre'],
			'pEstado'=> $datos['Estado'],
			'pClase'=> $datos['Clase'],
			'pMetodo'=> $datos['Metodo'],
			'pTipoCampoEditable'=> $datos['TipoCampoEditable'],
			'pTieneValores'=> $datos['TieneValores'],
			'pArchivo'=> $datos['Archivo'],
			'pFechaAlta'=> $datos['FechaAlta'],
			'pAltaUsuario'=> $datos['AltaUsuario'],
			'pUltimaModificacionUsuario'=> $_SESSION['usuariocod'],
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
		$spnombre="upd_EstructuraObjetos_xIdObjeto";
		$sparam=array(
			'pNombre'=> $datos['Nombre'],
			'pClase'=> $datos['Clase'],
			'pMetodo'=> $datos['Metodo'],
			'pTipoCampoEditable'=> $datos['TipoCampoEditable'],
			'pTieneValores'=> $datos['TieneValores'],
			'pArchivo'=> $datos['Archivo'],
			'pUltimaModificacionUsuario'=> $_SESSION['usuariocod'],
			'pUltimaModificacionFecha'=> $datos['UltimaModificacionFecha'],
			'pIdObjeto'=> $datos['IdObjeto']
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
		$spnombre="del_EstructuraObjetos_xIdObjeto";
		$sparam=array(
			'pIdObjeto'=> $datos['IdObjeto']
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
		$spnombre="upd_EstructuraObjetos_Estado_xIdObjeto";
		$sparam=array(
			'pEstado'=> $datos['Estado'],
			'pUltimaModificacionUsuario'=> $_SESSION['usuariocod'],
			'pIdObjeto'=> $datos['IdObjeto']
		);
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno))
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al modificar el estado. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		return true;
	}





}
?>