<?php
abstract class cSubGruposOcupacionaldb
{


	function __construct(){}

	function __destruct(){}

	protected function BuscarxCodigo($datos,&$resultado,&$numfilas)
	{
		$spnombre="sel_SubGruposOcupacional_xIdSubGrupoOcupacional";
		$sparam=array(
			'pIdSubGrupoOcupacional'=> $datos['IdSubGrupoOcupacional']
		);
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno) )
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al buscar al buscar por codigo. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		return true;
	}


	protected function BuscarSubGruposActivosAgrupadosxCodigo($datos,&$resultado,&$numfilas)
	{
		$spnombre="sel_SubGruposOcupacional_Activos_Agrupados_xCodigo";
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
		$spnombre="sel_SubGruposOcupacional_busqueda_avanzada";
		$sparam=array(
			'pxIdSubGrupoOcupacional'=> $datos['xIdSubGrupoOcupacional'],
			'pIdSubGrupoOcupacional'=> $datos['IdSubGrupoOcupacional'],
			'pxIdSubGrupoOcupacionalExterno'=> $datos['xIdSubGrupoOcupacionalExterno'],
			'pIdSubGrupoOcupacionalExterno'=> $datos['IdSubGrupoOcupacionalExterno'],
			'pxCodigo'=> $datos['xCodigo'],
			'pCodigo'=> $datos['Codigo'],
			'pxDescripcion'=> $datos['xDescripcion'],
			'pDescripcion'=> $datos['Descripcion'],
			'pxIdGrupoOcupacionalExterno'=> $datos['xIdGrupoOcupacionalExterno'],
			'pIdGrupoOcupacionalExterno'=> $datos['IdGrupoOcupacionalExterno'],
			'pxExcepcion'=> $datos['xExcepcion'],
			'pExcepcion'=> $datos['Excepcion'],
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
        $spnombre="sel_SubGruposOcupacional_combo";
        $sparam=array(
            'pxIdGrupoOcupacional'=> $datos['xIdGrupoOcupacional'],
            'pIdGrupoOcupacional'=> $datos['IdGrupoOcupacional']
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
		$spnombre="sel_SubGruposOcupacional_AuditoriaRapida";
		$sparam=array(
			'pIdSubGrupoOcupacional'=> $datos['IdSubGrupoOcupacional']
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
		$spnombre="ins_SubGruposOcupacional";
		$sparam=array(
			'pIdSubGrupoOcupacionalExterno'=> $datos['IdSubGrupoOcupacionalExterno'],
			'pCodigo'=> $datos['Codigo'],
			'pDescripcion'=> $datos['Descripcion'],
			'pIdGrupoOcupacionalExterno'=> $datos['IdGrupoOcupacionalExterno'],
			'pExcepcion'=> $datos['Excepcion'],
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
		$spnombre="upd_SubGruposOcupacional_xIdSubGrupoOcupacional";
		$sparam=array(
			'pIdSubGrupoOcupacionalExterno'=> $datos['IdSubGrupoOcupacionalExterno'],
			'pCodigo'=> $datos['Codigo'],
			'pDescripcion'=> $datos['Descripcion'],
			'pIdGrupoOcupacionalExterno'=> $datos['IdGrupoOcupacionalExterno'],
			'pExcepcion'=> $datos['Excepcion'],
			'pUltimaModificacionUsuario'=> $_SESSION['usuariocod'],
			'pUltimaModificacionFecha'=> $datos['UltimaModificacionFecha'],
			'pIdSubGrupoOcupacional'=> $datos['IdSubGrupoOcupacional']
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
		$spnombre="del_SubGruposOcupacional_xIdSubGrupoOcupacional";
		$sparam=array(
			'pIdSubGrupoOcupacional'=> $datos['IdSubGrupoOcupacional']
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
		$spnombre="upd_SubGruposOcupacional_Estado_xIdSubGrupoOcupacional";
		$sparam=array(
			'pEstado'=> $datos['Estado'],
			'pUltimaModificacionUsuario'=> $_SESSION['usuariocod'],
			'pIdSubGrupoOcupacional'=> $datos['IdSubGrupoOcupacional']
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
