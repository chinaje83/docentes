<?php 
abstract class cLicenciasWorkflowdb
{


	function __construct(){}

	function __destruct(){}

	protected function BuscarxCodigo($datos,&$resultado,&$numfilas)
	{
		$spnombre="sel_LicenciasWorkflow_xId";
		$sparam=array(
			'pId'=> $datos['Id']
		);
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno) )
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al buscar al buscar por codigo. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		return true;
	}

    protected function BuscarxIdCircuitoxIdNodoWorkflowInicialxIdNodoWorkflowFinal($datos,&$resultado,&$numfilas)
    {
        $spnombre="sel_LicenciasWorkflow_xIdCircuito_IdNodoWorkflowInicial_IdNodoWorkflowFinal";
        $sparam=array(
            'pIdCircuito'=> $datos['IdCircuito'],
            'pIdNodoWorkflowInicial'=> $datos['IdNodoWorkflowInicial'],
            'pIdNodoWorkflowFinal'=> $datos['IdNodoWorkflowFinal']
        );
        if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno) )
        {
            FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al buscar al buscar por codigo. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
            return false;
        }

        return true;
    }

    protected function BuscarConexionesxCircuito($datos, &$resultado,&$numfilas)
    {
        $spnombre="sel_LicenciasWorkflow_xIdCircuito";
        $sparam=array(
            'pIdCircuito'=> $datos['IdCircuito']
        );

        if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno))
        {
            FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al buscar las conexiones por circuito.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
            return false;
        }

        return true;
    }

    protected function BuscarConexionesxIdNodoWorkflow($datos, &$resultado,&$numfilas)
    {
        $spnombre="sel_LicenciasWorkflow_Conexiones_xIdNodoWorkflow";
        $sparam=array(
            'pIdNodoWorkflow'=> $datos['IdNodoWorkflow']
        );

        if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno))
        {
            FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al buscar las conexiones por circuito.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
            return false;
        }

        return true;
    }





	protected function BusquedaAvanzada($datos,&$resultado,&$numfilas)
	{
		$spnombre="sel_LicenciasWorkflow_busqueda_avanzada";
		$sparam=array(
		    'pIdCircuito'=> $datos['IdCircuito'],
            'pxId'=> $datos['xId'],
            'pId'=> $datos['Id'],
            'pxIdEstadoInicial'=> $datos['xIdEstadoInicial'],
            'pIdEstadoInicial'=> $datos['IdEstadoInicial'],
            'pxIdEstadoFinal'=> $datos['xIdEstadoFinal'],
            'pIdEstadoFinal'=> $datos['IdEstadoFinal'],
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



	protected function Insertar($datos,&$codigoinsertado)
	{
		$spnombre="ins_LicenciasWorkflow";
		$sparam=array(
            'pIdCircuito'=> $datos['IdCircuito'],
            'pIdNodoWorkflowInicial'=> $datos['IdNodoWorkflowInicial'],
            'pIdNodoWorkflowFinal'=> $datos['IdNodoWorkflowFinal'],
            'pNombre'=> $datos['Nombre'],
            'pAccion'=> $datos['Accion'],
            'pFuncion'=> $datos['Funcion'],
            'pClase'=> $datos['Clase'],
            'pIcono'=> $datos['Icono'],
            'pTooltip'=> $datos['Tooltip'],
            'pOrden'=> $datos['Orden'],
            'pEstado'=> $datos['Estado'],
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
		$spnombre="del_LicenciasWorkflow_xId";
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