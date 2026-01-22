<?php 
abstract class cLicenciasCircuitosEstadosRolesdb
{


	function __construct(){}

	function __destruct(){}

	protected function BuscarxCodigo($datos,&$resultado,&$numfilas)
	{
		$spnombre="sel_LicenciasCircuitosEstadosRoles_xIdNodoWorkflow_IdRol";
		$sparam=array(
            'pBase'=> BASEDATOS,
			'pIdNodoWorkflow'=> $datos['IdNodoWorkflow'],
            'pIdRol'=> $datos['IdRol']
		);
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno) )
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al buscar al buscar por codigo. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		return true;
	}

    protected function BuscarxIdNodoWorkflow($datos,&$resultado,&$numfilas)
    {
        $spnombre="sel_LicenciasCircuitosEstadosRoles_xIdNodoWorkflow";
        $sparam=array(
            'pBase'=> BASEDATOS,
            'pIdNodoWorkflow'=> $datos['IdNodoWorkflow'],
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
		$spnombre="sel_LicenciasCircuitosEstadosRoles_busqueda_avanzada";
		$sparam=array(
		    'pBase'=> BASEDATOS,
            'pxIdNodoWorkflow'=> $datos['xIdNodoWorkflow'],
            'pIdNodoWorkflow'=> $datos['IdNodoWorkflow'],
            'pxIdRol'=> $datos['xIdRol'],
            'pIdRol'=> $datos['IdRol'],
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
		$spnombre="ins_LicenciasCircuitosEstadosRoles";
		$sparam=array(
            'pIdNodoWorkflow'=> $datos['IdNodoWorkflow'],
            'pIdRol'=> $datos['IdRol'],
            'pAltaUsuario'=> $_SESSION['usuariocod'],
            'pAltaFecha'=> $datos['AltaFecha'],
            'pAltaApp'=> APP,
            'pUltimaModificacionUsuario'=> $_SESSION['usuariocod'],
            'pUltimaModificacionFecha'=> $datos['UltimaModificacionFecha'],
            'pUltimaModificacionApp'=> APP
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
		$spnombre="del_LicenciasCircuitosEstadosRoles_xIdNodoWorkflow_IdRol";
		$sparam=array(
            'pIdNodoWorkflow'=> $datos['IdNodoWorkflow'],
            'pIdRol'=> $datos['IdRol']
		);
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno))
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al eliminar por codigo. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		return true;
	}

    protected function EliminarxIdNodoWorkflow($datos)
    {
        $spnombre="del_LicenciasCircuitosEstadosRoles_xIdNodoWorkflow";
        $sparam=array(
            'pIdNodoWorkflow'=> $datos['IdNodoWorkflow']
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