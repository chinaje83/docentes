<?php 
abstract class cSugerenciasdb
{


	function __construct(){}

	function __destruct(){}

	protected function BuscarxCodigo($datos,&$resultado,&$numfilas)
	{
		$spnombre="sel_Sugerencias_xIdSugerencia";
		$sparam=array(
			'pIdSugerencia'=> $datos['IdSugerencia']
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
		$spnombre="sel_Sugerencias_busqueda_avanzada";
		$sparam=array(
			'pxIdSugerencia'=> $datos['xIdSugerencia'],
			'pIdSugerencia'=> $datos['IdSugerencia'],
			'pxIdSugerenciaTipocod'=> $datos['xIdSugerenciaTipocod'],
			'pIdSugerenciaTipocod'=> $datos['IdSugerenciaTipocod'],
			'pxIdDocumento'=> $datos['xIdDocumento'],
			'pIdDocumento'=> $datos['IdDocumento'],
			'pxIdTipoDocumento'=> $datos['xIdTipoDocumento'],
			'pIdTipoDocumento'=> $datos['IdTipoDocumento'],
			'pxDescripcion'=> $datos['xDescripcion'],
			'pDescripcion'=> $datos['Descripcion'],
			'pxClaveEscuela'=> $datos['xClaveEscuela'],
			'pClaveEscuela'=> $datos['ClaveEscuela'],
			'pxIdDistrito'=> $datos['xIdDistrito'],
			'pIdDistrito'=> $datos['IdDistrito'],
			'pxIdTipoOrganismo'=> $datos['xIdTipoOrganismo'],
			'pIdTipoOrganismo'=> $datos['IdTipoOrganismo'],
			'pxFechaDesde'=> $datos['xFechaDesde'],
			'pFechaDesde'=> $datos['FechaDesde'],
			'pxFechaHasta'=> $datos['xFechaHasta'],
			'pFechaHasta'=> $datos['FechaHasta'],
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
		$spnombre="ins_Sugerencias";
		$sparam=array(
			'pIdSugerenciaTipocod'=> $datos['IdSugerenciaTipocod'],
			'pIdDocumento'=> $datos['IdDocumento'],
			'pDescripcion'=> $datos['Descripcion'],
			'pEstado'=> $datos['Estado'],
			'pUrlPagina'=> $_SERVER['HTTP_REFERER'],
			'pClaveEscuela'=> $datos['ClaveEscuela'],
			'pNombreEscuela'=> $datos['NombreEscuela'],
			'pIdDistrito'=> $datos['IdDistrito'],
			'pIdTipoOrganismo'=> $datos['IdTipoOrganismo'],
			'pAltaUsuario'=> $datos['AltaUsuario'],
			'pAltaFecha'=> $datos['AltaFecha'],
			'pUltimaModificacionFecha'=> $datos['UltimaModificacionFecha'],
			'pUltimaModificacionUsuario'=> $_SESSION['usuariocod']
		);
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno))
		{
			echo "aca";die;
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al insertar. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		$codigoinsertado=$this->conexion->UltimoCodigoInsertado();

		return true;
	}



	protected function Modificar($datos)
	{
		$spnombre="upd_Sugerencias_xIdSugerencia";
		$sparam=array(
			'pIdSugerenciaTipocod'=> $datos['IdSugerenciaTipocod'],
			'pIdDocumento'=> $datos['IdDocumento'],
			'pDescripcion'=> $datos['Descripcion'],
			'pUltimaModificacionFecha'=> $datos['UltimaModificacionFecha'],
			'pUltimaModificacionUsuario'=> $_SESSION['usuariocod'],
			'pIdSugerencia'=> $datos['IdSugerencia']
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
		$spnombre="del_Sugerencias_xIdSugerencia";
		$sparam=array(
			'pIdSugerencia'=> $datos['IdSugerencia']
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
		$spnombre="upd_Sugerencias_Estado_xIdSugerencia";
		$sparam=array(
			'pEstado'=> $datos['Estado'],
			'pUltimaModificacionUsuario'=> $_SESSION['usuariocod'],
			'pIdSugerencia'=> $datos['IdSugerencia']
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