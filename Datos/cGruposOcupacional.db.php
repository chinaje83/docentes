<?php
abstract class cGruposOcupacionaldb
{


	function __construct(){}

	function __destruct(){}

	protected function BuscarxCodigo($datos,&$resultado,&$numfilas)
	{
		$spnombre="sel_GruposOcupacional_xIdGrupoOcupacional";
		$sparam=array(
			'pIdGrupoOcupacional'=> $datos['IdGrupoOcupacional']
		);
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno) )
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al buscar al buscar por codigo. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		return true;
	}



	protected function BuscarGruposActivosAgrupadosxCodigo($datos,&$resultado,&$numfilas)
	{
		$spnombre="sel_GruposOcupacional_Activos_Agrupados_xCodigo";
		$sparam=array(
			'porderby'=> $datos['orderby']
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
		$spnombre="sel_GruposOcupacional_busqueda_avanzada";
		$sparam=array(
			'pxIdGrupoOcupacional'=> $datos['xIdGrupoOcupacional'],
			'pIdGrupoOcupacional'=> $datos['IdGrupoOcupacional'],
			'pxIdGrupoOcupacionalExterno'=> $datos['xIdGrupoOcupacionalExterno'],
			'pIdGrupoOcupacionalExterno'=> $datos['IdGrupoOcupacionalExterno'],
			'pxCodigo'=> $datos['xCodigo'],
			'pCodigo'=> $datos['Codigo'],
			'pxDescripcion'=> $datos['xDescripcion'],
			'pDescripcion'=> $datos['Descripcion'],
			'pxIdRevistaExterno'=> $datos['xIdRevistaExterno'],
			'pIdRevistaExterno'=> $datos['IdRevistaExterno'],
			'pxIdRegimenEstatutarioExterno'=> $datos['xIdRegimenEstatutarioExterno'],
			'pIdRegimenEstatutarioExterno'=> $datos['IdRegimenEstatutarioExterno'],
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

    protected function BuscarCombo($datos, &$resultado, &$numfilas) {
        $spnombre="sel_GruposOcupacional_combo";
        $sparam=array(
            'pxIdRegimenSalarial'=> $datos['xIdRegimenSalarial'],
            'pIdRegimenSalarial'=> $datos['IdRegimenSalarial']
        );
        if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno) )
        {
            FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al buscar combo. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
            return false;
        }

        return true;
    }

	protected function BuscarAuditoriaRapida($datos,&$resultado,&$numfilas)
	{
		$spnombre="sel_GruposOcupacional_AuditoriaRapida";
		$sparam=array(
			'pIdGrupoOcupacional'=> $datos['IdGrupoOcupacional']
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
		$spnombre="ins_GruposOcupacional";
		$sparam=array(
			'pIdGrupoOcupacionalExterno'=> $datos['IdGrupoOcupacionalExterno'],
			'pCodigo'=> $datos['Codigo'],
			'pDescripcion'=> $datos['Descripcion'],
			'pIdRevistaExterno'=> $datos['IdRevistaExterno'],
			'pIdRegimenEstatutarioExterno'=> $datos['IdRegimenEstatutarioExterno'],
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
		$spnombre="upd_GruposOcupacional_xIdGrupoOcupacional";
		$sparam=array(
			'pIdGrupoOcupacionalExterno'=> $datos['IdGrupoOcupacionalExterno'],
			'pCodigo'=> $datos['Codigo'],
			'pDescripcion'=> $datos['Descripcion'],
			'pIdRevistaExterno'=> $datos['IdRevistaExterno'],
			'pIdRegimenEstatutarioExterno'=> $datos['IdRegimenEstatutarioExterno'],
			'pUltimaModificacionUsuario'=> $_SESSION['usuariocod'],
			'pUltimaModificacionFecha'=> $datos['UltimaModificacionFecha'],
			'pIdGrupoOcupacional'=> $datos['IdGrupoOcupacional']
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
		$spnombre="del_GruposOcupacional_xIdGrupoOcupacional";
		$sparam=array(
			'pIdGrupoOcupacional'=> $datos['IdGrupoOcupacional']
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
		$spnombre="upd_GruposOcupacional_Estado_xIdGrupoOcupacional";
		$sparam=array(
			'pEstado'=> $datos['Estado'],
			'pUltimaModificacionUsuario'=> $_SESSION['usuariocod'],
			'pIdGrupoOcupacional'=> $datos['IdGrupoOcupacional']
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
