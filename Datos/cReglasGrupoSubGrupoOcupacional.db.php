<?php 
abstract class cReglasGrupoSubGrupoOcupacionaldb
{


	function __construct(){}

	function __destruct(){}

	protected function BuscarxCodigo($datos,&$resultado,&$numfilas)
	{
		$spnombre="sel_ReglasGrupoSubGrupoOcupacional_xIdReglasGrupoSubGrupoOcupacional";
		$sparam=array(
			'pIdReglasGrupoSubGrupoOcupacional'=> $datos['IdReglasGrupoSubGrupoOcupacional']
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
		$spnombre="sel_ReglasGrupoSubGrupoOcupacional_busqueda_avanzada";
		$sparam=array(
			'pxIdReglasGrupoSubGrupoOcupacional'=> $datos['xIdReglasGrupoSubGrupoOcupacional'],
			'pIdReglasGrupoSubGrupoOcupacional'=> $datos['IdReglasGrupoSubGrupoOcupacional'],
			'pxCodigoRegimenEstatutario'=> $datos['xCodigoRegimenEstatutario'],
			'pCodigoRegimenEstatutario'=> $datos['CodigoRegimenEstatutario'],
			'pxCodigoRevista'=> $datos['xCodigoRevista'],
			'pCodigoRevista'=> $datos['CodigoRevista'],
			'pxCodigoGrupo'=> $datos['xCodigoGrupo'],
			'pCodigoGrupo'=> $datos['CodigoGrupo'],
			'pxCodigoSubGrupo'=> $datos['xCodigoSubGrupo'],
			'pCodigoSubGrupo'=> $datos['CodigoSubGrupo'],
			'pxCategoriaDesde'=> $datos['xCategoriaDesde'],
			'pCategoriaDesde'=> $datos['CategoriaDesde'],
			'pxCategoriaHasta'=> $datos['xCategoriaHasta'],
			'pCategoriaHasta'=> $datos['CategoriaHasta'],
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
	
	
	protected function BuscarRegimenEstatutarios($datos,&$resultado,&$numfilas)
	{
		$spnombre="sel_ReglasGrupoSubGrupoOcupacional_RegimenEstatutarios";
		$sparam=array(
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
	
	protected function BuscarRevistas($datos,&$resultado,&$numfilas)
	{
		$spnombre="sel_ReglasGrupoSubGrupoOcupacional_Revistas";
		$sparam=array(
			'pCodigoRegimenEstatutario'=> $datos['CodigoRegimenEstatutario'],
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
	
	protected function BuscarGruposOcupacional($datos,&$resultado,&$numfilas)
	{
		$spnombre="sel_ReglasGrupoSubGrupoOcupacional_GruposOcupacional";
		$sparam=array(
			'pCodigoRegimenEstatutario'=> $datos['CodigoRegimenEstatutario'],
			'pCodigoRevista'=> $datos['CodigoRevista'],
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
	
	protected function BuscarSubGruposOcupacional($datos,&$resultado,&$numfilas)
	{
		$spnombre="sel_ReglasGrupoSubGrupoOcupacional_SubGruposOcupacional";
		$sparam=array(
			'pCodigoRegimenEstatutario'=> $datos['CodigoRegimenEstatutario'],
			'pCodigoRevista'=> $datos['CodigoRevista'],
			'pCodigoGrupo'=> $datos['CodigoGrupo'],
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
	
	
	protected function BuscarCargos($datos,&$resultado,&$numfilas)
	{
		$spnombre="sel_ReglasGrupoSubGrupoOcupacional_Cargos";
		$sparam=array(
			'pCodigoRegimenEstatutario'=> $datos['CodigoRegimenEstatutario'],
			'pCodigoRevista'=> $datos['CodigoRevista'],
			'pCodigoGrupo'=> $datos['CodigoGrupo'],
			'pxCodigoSubGrupo'=> $datos['xCodigoSubGrupo'],
			'pCodigoSubGrupo'=> $datos['CodigoSubGrupo'],
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
	
	
	protected function BuscarRegla($datos,&$resultado,&$numfilas)
	{
		$spnombre="sel_ReglasGrupoSubGrupoOcupacional_Regla";
		$sparam=array(
			'pCodigoRegimenEstatutario'=> $datos['CodigoRegimenEstatutario'],
			'pCodigoRevista'=> $datos['CodigoRevista'],
			'pCodigoGrupo'=> $datos['CodigoGrupo'],
			'pCodigoCargo'=> $datos['CodigoCargo'],
			'pxCodigoSubGrupo'=> $datos['xCodigoSubGrupo'],
			'pCodigoSubGrupo'=> $datos['CodigoSubGrupo']
			
		);

		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno) )
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al realizar la búsqueda avanzada. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		return true;
	}
	
	
	



	protected function BuscarAuditoriaRapida($datos,&$resultado,&$numfilas)
	{
		$spnombre="sel_ReglasGrupoSubGrupoOcupacional_AuditoriaRapida";
		$sparam=array(
			'pIdReglasGrupoSubGrupoOcupacional'=> $datos['IdReglasGrupoSubGrupoOcupacional']
		);
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno) )
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al buscar por codigo. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		return true;
	}



	protected function Insertar($datos,&$codigoinsertado)
	{
		$spnombre="ins_ReglasGrupoSubGrupoOcupacional";
		$sparam=array(
			'pCodigoRegimenEstatutario'=> $datos['CodigoRegimenEstatutario'],
			'pCodigoRevista'=> $datos['CodigoRevista'],
			'pCodigoGrupo'=> $datos['CodigoGrupo'],
			'pCodigoSubGrupo'=> $datos['CodigoSubGrupo'],
			'pCategoriaDesde'=> $datos['CategoriaDesde'],
			'pCategoriaHasta'=> $datos['CategoriaHasta'],
			'pEstado'=> $datos['Estado'],
			'pAltaFecha'=> $datos['AltaFecha'],
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
		$spnombre="upd_ReglasGrupoSubGrupoOcupacional_xIdReglasGrupoSubGrupoOcupacional";
		$sparam=array(
			'pCodigoRegimenEstatutario'=> $datos['CodigoRegimenEstatutario'],
			'pCodigoRevista'=> $datos['CodigoRevista'],
			'pCodigoGrupo'=> $datos['CodigoGrupo'],
			'pCodigoSubGrupo'=> $datos['CodigoSubGrupo'],
			'pCategoriaDesde'=> $datos['CategoriaDesde'],
			'pCategoriaHasta'=> $datos['CategoriaHasta'],
			'pUltimaModificacionUsuario'=> $_SESSION['usuariocod'],
			'pUltimaModificacionFecha'=> $datos['UltimaModificacionFecha'],
			'pIdReglasGrupoSubGrupoOcupacional'=> $datos['IdReglasGrupoSubGrupoOcupacional']
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
		$spnombre="del_ReglasGrupoSubGrupoOcupacional_xIdReglasGrupoSubGrupoOcupacional";
		$sparam=array(
			'pIdReglasGrupoSubGrupoOcupacional'=> $datos['IdReglasGrupoSubGrupoOcupacional']
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
		$spnombre="upd_ReglasGrupoSubGrupoOcupacional_Estado_xIdReglasGrupoSubGrupoOcupacional";
		$sparam=array(
			'pEstado'=> $datos['Estado'],
			'pUltimaModificacionUsuario'=> $_SESSION['usuariocod'],
			'pIdReglasGrupoSubGrupoOcupacional'=> $datos['IdReglasGrupoSubGrupoOcupacional']
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