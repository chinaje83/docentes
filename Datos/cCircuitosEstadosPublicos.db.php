<?php 
abstract class cCircuitosEstadosPublicosdb
{


	function __construct(){}

	function __destruct(){}

	protected function BuscarxCodigo($datos,&$resultado,&$numfilas)
	{
		$spnombre="sel_CircuitosEstadosPublicos_xIdEstadoPublico";
		$sparam=array(
			'pIdEstadoPublico'=> $datos['IdEstadoPublico']
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
		$spnombre="sel_CircuitosEstadosPublicos_busqueda_avanzada";
		$sparam=array(
			'pxIdEstadoPublico'=> $datos['xIdEstadoPublico'],
			'pIdEstadoPublico'=> $datos['IdEstadoPublico'],
			'pxNombre'=> $datos['xNombre'],
			'pNombre'=> $datos['Nombre'],
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



	protected function BuscarAuditoriaRapida($datos,&$resultado,&$numfilas)
	{
		$spnombre="sel_CircuitosEstadosPublicos_AuditoriaRapida";
		$sparam=array(
			'pIdEstadoPublico'=> $datos['IdEstadoPublico']
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
		$spnombre="ins_CircuitosEstadosPublicos";
		$sparam=array(
			'pNombre'=> $datos['Nombre'],
			'pDescripcion'=> $datos['Descripcion'],
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
		$spnombre="upd_CircuitosEstadosPublicos_xIdEstadoPublico";
		$sparam=array(
			'pNombre'=> $datos['Nombre'],
			'pDescripcion'=> $datos['Descripcion'],
			'pAltaFecha'=> $datos['AltaFecha'],
			'pUltimaModificacionUsuario'=> $_SESSION['usuariocod'],
			'pUltimaModificacionFecha'=> $datos['UltimaModificacionFecha'],
			'pIdEstadoPublico'=> $datos['IdEstadoPublico']
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
		$spnombre="del_CircuitosEstadosPublicos_xIdEstadoPublico";
		$sparam=array(
			'pIdEstadoPublico'=> $datos['IdEstadoPublico']
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
        $spnombre="upd_CircuitosEstadosPublicos_Estado_xIdEstadoPublico";
        $sparam=array(
            'pEstado'=> $datos['Estado'],
            'pUltimaModificacionUsuario'=> $_SESSION['usuariocod'],
            'pUltimaModificacionFecha'=> date("Y/m/d H:i:s"),
            'pIdEstadoPublico'=> $datos['IdEstadoPublico']
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