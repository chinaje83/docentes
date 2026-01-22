<?php 
abstract class cLicenciasWorkflowRolesdb
{


	function __construct(){}

	function __destruct(){}

	protected function BuscarxCodigo($datos,&$resultado,&$numfilas)
	{
		$spnombre="sel_LicenciasWorkflowRoles_xId";
		$sparam=array(
            'pBase'=> BASEDATOS,
			'pId'=> $datos['Id']
		);
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno) )
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al buscar al buscar por codigo. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		return true;
	}

    protected function BuscarxIdLicenciaWorkflow($datos,&$resultado,&$numfilas)
    {
        $spnombre="sel_LicenciasWorkflowRoles_xIdLicenciaWorkflow";
        $sparam=array(
            'pBase'=> BASEDATOS,
            'pIdLicenciaWorkflow'=> $datos['IdLicenciaWorkflow']
        );
        if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno) )
        {
            FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al buscar al buscar por codigo. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
            return false;
        }

        return true;
    }

    protected function BuscarxIdNodoWorkflowInicialxRol($datos,&$resultado,&$numfilas)
    {
        $spnombre="sel_LicenciasWorkflowRoles_xIdNodoWorkflowInicial_Rol";
        $sparam=array(
            'pIdNodoWorkflowInicial'=> $datos['IdNodoWorkflowInicial'],
            'pRol'=> $datos['Rol'],
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
		$spnombre="sel_LicenciasWorkflowRoles_busqueda_avanzada";
		$sparam=array(
		    'pBase'=> BASEDATOS,
            'pxId'=> $datos['xId'],
            'pId'=> $datos['Id'],
            'pxIdLicenciaWorkflow'=> $datos['IdLicenciaWorkflow'],
            'pIdLicenciaWorkflow'=> $datos['IdLicenciaWorkflow'],
            'pxRol'=> $datos['Rol'],
            'pRol'=> $datos['Rol'],
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
		$spnombre="ins_LicenciasWorkflowRoles";
		$sparam=array(
            'pIdLicenciaWorkflow'=> $datos['IdLicenciaWorkflow'],
            'pRol'=> $datos['Rol'],
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



	protected function Modificar($datos)
	{
		$spnombre="upd_LicenciasWorkflow_xId";
		$sparam=array(
            'pIdEstadoInicial'=> $datos['IdEstadoInicial'],
            'pIdEstadoFinal'=> $datos['IdEstadoFinal'],
            'pNombre'=> $datos['Nombre'],
            'pAccion'=> $datos['Accion'],
            'pFuncion'=> $datos['Funcion'],
            'pClase'=> $datos['Clase'],
            'pIcono'=> $datos['Icono'],
            'pTooltip'=> $datos['Tooltip'],
            'pUltimaModificacionUsuario'=> $_SESSION['usuariocod'],
            'pUltimaModificacionFecha'=> $datos['UltimaModificacionFecha'],
            'pUltimaModificacionApp'=> APP,
			'pId'=> $datos['Id']
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
		$spnombre="del_LicenciasWorkflowRoles_xId";
		$sparam=array(
			'pId'=> $datos['Id']
		);
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno))
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al eliminar por codigo. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		return true;
	}

    protected function EliminarxIdLicenciaWorkflow($datos)
    {
        $spnombre="del_LicenciasWorkflowRoles_xIdLicenciaWorkflow";
        $sparam=array(
            'pIdLicenciaWorkflow'=> $datos['IdLicenciaWorkflow']
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
		$spnombre="upd_LicenciasWorkflow_Estado_xId";
		$sparam=array(
			'pEstado'=> $datos['Estado'],
			'pUltimaModificacionUsuario'=> $_SESSION['usuariocod'],
			'pUltimaModificacionFecha'=> $datos['UltimaModificacionFecha'],
            'pUltimaModificacionApp'=>  APP,
			'pId'=> $datos['Id']
		);
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno))
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al modificar el estado. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		return true;
	}

    protected function ModificarOrden($datos)
    {
        $spnombre="upd_LicenciasWorkflow_Orden_xId";
        $sparam=array(
            'pOrden'=> $datos['Orden'],
            'pUltimaModificacionUsuario'=> $_SESSION['usuariocod'],
            'pUltimaModificacionFecha'=> $datos['UltimaModificacionFecha'],
            'pUltimaModificacionApp'=>  APP,
            'pId'=> $datos['Id']
        );
        if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno))
        {
            FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al modificar el orden. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
            return false;
        }
        return true;
    }


    protected function BuscarUltimoOrden(array $datos,  &$resultado, ?int &$numfilas): bool
    {
        $spnombre="sel_LicenciasWorkflow_max_orden";
        $sparam=array(
            'pIdCircuito'=> $datos['IdCircuito']
        );
        if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno) )
        {
            FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al buscar el maximo orden. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
            return false;
        }
        return true;
    }




}
?>