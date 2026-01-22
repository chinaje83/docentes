<?php

/**
 * Class cDocumentosPermisosdb
 * @property accesoBDLocal conexion
 */
abstract class cDocumentosPermisosdb
{


	function __construct(){}

	function __destruct(){}



	protected function BuscarAccesoTipoDocumentoxIdAreaxIdTipoDocumentoxAccion($datos,&$resultado,&$numfilas)
	{
		$spnombre="sel_AreasTiposDocumentos_Acceso_xIdArea_xIdTipoDocumento_xAccion";
		$sparam=array(
			'pIdArea'=> $datos['IdArea'],
			'pIdRol'=> $datos['IdRol'],
			'pIdTipoDocumento'=> $datos['IdTipoDocumento'],
			'pVigencia'=> $datos['Vigencia'],
			'pIdAccion'=> $datos['IdAccion']
		);
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno) )
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al buscar el acceso al tipo de documento por accion. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		return true;
	}


	protected function BuscarAccesoTipoDocumentoxIdAreaxIdTipoDocumentoxIdDocumentoxAccion($datos,&$resultado,&$numfilas)
	{
		$spnombre="sel_AreasTiposDocumentos_Acceso_xIdArea_xIdTipoDocumento_xIdDocumento_xAccion";
		$sparam=array(
			/*'pIdArea'=> $datos['IdArea'],*/
			'pIdRol'=> $datos['IdRol'],
			'pIdTipoDocumento'=> $datos['IdTipoDocumento'],
			'pIdDocumento'=> $datos['IdDocumento'],
			'pVigencia'=> $datos['Vigencia'],
			'pxIdAccion'=> $datos['xIdAccion'],
			'pIdAccion'=> $datos['IdAccion']?:'-1'
		);

		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno) )
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al buscar el acceso al tipo de documento por accion. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		return true;
	}


    protected function buscarAccionesNodoInicial($datos,&$resultado,&$numfilas)
    {
        $spnombre="sel_DocumentosTipos_nodo_inicial_acciones";
        $sparam = [
            'pIdTipoDocumento'=> $datos['IdTipoDocumento'],
            'pIdRol'=> $datos['IdRol'],
            'pVigencia'=> $datos['Vigencia'],
        ];

        if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno) ) {
            FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al buscar permisos. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
            return false;
        }

        return true;
    }



    protected function BuscarAccesoTipoDocumentoxIdAreaxIdTipoDocumentoxIdSolicitudCoberturaxAccion($datos,&$resultado,&$numfilas)
	{
		$spnombre="sel_AreasTiposDocumentos_Acceso_xIdArea_xIdTipoDocumento_xIdSolicitudCobertura_xAccion";
		$sparam=array(
			/*'pIdArea'=> $datos['IdArea'],*/
			'pIdRol'=> $datos['IdRol'],
			'pIdTipoDocumento'=> $datos['IdTipoDocumento'],
			'pIdSolicitudCobertura'=> $datos['IdSolicitudCobertura'],
			'pVigencia'=> $datos['Vigencia'],
			'pxIdAccion'=> $datos['xIdAccion'],
			'pIdAccion'=> $datos['IdAccion']
		);
		
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno) )
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al buscar el acceso al tipo de documento por accion. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		
		return true;
	}


    protected function BuscarPermisosxMad($datos,&$resultado,&$numfilas)
    {
        $spnombre="sel_AreasTiposDocumentos_Acceso_xIdArea_xIdTipoDocumento_xIdMad_xAccion";
        $sparam=array(
            'pIdRol'=> $datos['IdRol'],
            'pIdTipoDocumento'=> $datos['IdTipoDocumento'],
            'pId'=> $datos['Id'],
            'pVigencia'=> $datos['Vigencia'],
            'pxIdAccion'=> $datos['xIdAccion'],
            'pIdAccion'=> $datos['IdAccion']
        );

        if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno) )
        {
            FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al buscar el acceso al tipo de documento por accion. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
            return false;
        }

        return true;
    }





	protected function BuscarAccesoTipoDocumentoxIdAreaxIdTipoDocumento($datos,&$resultado,&$numfilas)
	{
		$spnombre="sel_AreasTiposDocumentos_Acceso_xIdArea_xIdTipoDocumento";
		$sparam=array(
			'pIdArea'=> $datos['IdArea'],
			'pIdRol'=> $datos['IdRol'],
			'pIdTipoDocumento'=> $datos['IdTipoDocumento'],
			'pVigencia'=> $datos['Vigencia']
		);

		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno) )
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al buscar el acceso al tipo de documento. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		return true;
	}


	protected function BuscarTiposDocumentosParaDarAlta($datos,&$resultado,&$numfilas)
	{
		$spnombre="sel_DocumentosTipos_Alta_xIdArea_xIdRol";
		$sparam=array(
			'pIdArea'=> $datos['IdArea'],
			'pIdRol'=> $datos['IdRol'],
			'pVigencia'=> $datos['Vigencia']
		);
		//print_r($sparam);
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno) )
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al buscar al buscar por codigo. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		return true;
	}



	protected function BuscarTiposDocumentosAccesoxIdDocumento($datos,&$resultado,&$numfilas)
	{
		$spnombre="sel_DocumentosPermisos_TiposDocumentos_xIdArea_IdDocumento_xIdRol";
		$sparam=array(
			'pIdArea'=> $datos['IdArea'],
			'pIdRol'=> $datos['IdRol'],
			'pIdDocumento'=> $datos['IdDocumento'],
			'pVigencia'=> $datos['Vigencia']
		);
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno) )
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al buscar los permisos por documento. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		return true;
	}
	
	
	protected function BuscarTiposDocumentosAccesoxTipoDocumento($datos,&$resultado,&$numfilas)
	{
		$spnombre="sel_DocumentosPermisos_TiposDocumentos_xIdArea_xIdTipoDocumento_xIdRol";
		$sparam=array(
			'pIdArea'=> $datos['IdArea'],
			'pIdRol'=> $datos['IdRol'],
			'pIdTipoDocumento'=> $datos['IdTipoDocumento'],
			'pVigencia'=> $datos['Vigencia']
		);
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno) )
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al buscar los permisos por tipo de documento. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		return true;
	}
	
		




	protected function BuscarWorkflowxIdDocumentoxRol($datos,&$resultado,&$numfilas)
	{
		$spnombre="sel_Workflow_xIdDocumento_xIdRol_Vigente";
		$sparam=array(
			/*'pIdArea'=>$datos['IdArea'],*/
			'pIdDocumento'=> $datos['IdDocumento'],
			'pIdRol'=> $datos['IdRol'],
			'pVigencia'=> $datos['Vigencia']
		);
		

		//print_r($sparam);
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno) )
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al buscar las acciones del workflow. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		return true;
	}


	protected function BuscarAreasEnvioxIdWorkflowIdDocumentoxRol($datos,&$resultado,&$numfilas)
	{
		$spnombre="sel_DocumentosAreasEnvio_xIdWorkflow_IdRol_IdDocumento";
		$sparam=array(
			/*'pIdArea'=>$datos['IdArea'],*/
			'pIdWorkflow'=> $datos['IdWorkflow'],
			'pIdDocumento'=> $datos['IdDocumento'],
			'pIdRol'=> $datos['IdRol'],
			'pVigencia'=> $datos['Vigencia']
		);
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno) )
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al buscar las areas de envio por Id del workflow. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		return true;
	}
	
	
	protected function BuscarAreasEnvioxIdWorkflowIdSolicitudCoberturaxRol($datos,&$resultado,&$numfilas)
	{
		$spnombre="sel_SolicitudesCoberturasAreasEnvio_xIdWorkflow_IdRol_IdSolicitudCobertura";
		$sparam=array(
			'pIdWorkflow'=> $datos['IdWorkflow'],
			'pIdSolicitudCobertura'=> $datos['IdSolicitudCobertura'],
			'pIdRol'=> $datos['IdRol'],
			'pVigencia'=> $datos['Vigencia']
		);
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno) )
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al buscar las areas de envio por Id del workflow. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		
		return true;
	}

    protected function BuscarAreasEnvioxIdWorkflowMadxRol($datos,&$resultado,&$numfilas)
    {
        $spnombre="sel_MadAreasEnvio_xIdWorkflow_IdRol_Mad";
        $sparam=array(
            'pIdWorkflow'=> $datos['IdWorkflow'],
            'pIdMad'=> $datos['IdMad'],
            'pIdRol'=> $datos['IdRol'],
            'pVigencia'=> $datos['Vigencia']
        );
        if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno) )
        {
            FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al buscar las areas de envio por Id del workflow. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
            return false;
        }

        return true;
    }



	protected function BuscarAccionesxIdWorkflowxRol($datos,&$resultado,&$numfilas)
	{
		$spnombre="sel_CircuitosWorkflowRolesAcciones_xIdWorkflow";
		$sparam=array(
			'pIdWorkflow'=> $datos['IdWorkflow'],
			'pIdRol'=> $datos['IdRol']
		);
		//print_r($sparam);
		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno) )
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al buscar la acciones en el Id del workflow. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		return true;
	}


    protected function BuscarMetodosAcciones($datos, &$resultado, &$numfilas)
    {
        $spnombre = 'sel_CircuitosWorkflowRoles_CircuitosAcciones';
        $sparam = [
            'pIdWorkflow'=> $datos['IdWorkflow'],
            'pIdRol'=> $datos['IdRol']
        ];

        if (!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno)) {
            FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al buscar la acciones en el Id del workflow. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
            return false;
        }

        return true;
    }



	protected function BuscarDocumentosAltaxIdTipoDocumentoxIdRol($datos,&$resultado,&$numfilas)
	{
		$spnombre="sel_DocumentosAlta_xIdTipoDocumentoPadre_xIdRol";
		$sparam=array(
			'pIdArea'=>$datos['IdArea'],
			'pIdTipoDocumentoPadre'=> $datos['IdTipoDocumentoPadre'],
			'pIdRol'=> $datos['IdRol'],
			'pxNombre'=> $datos['xNombre'],
			'pNombre'=> $datos['Nombre'],
			'pxNombreCorto'=> $datos['xNombreCorto'],
			'pIdCategoria'=> $datos['IdCategoria'],
			'pxIdCategoria'=> $datos['xIdCategoria'],
			'pNombreCorto'=> $datos['NombreCorto'],
			'pVigencia'=> $datos['Vigencia'],
			'pLimit'=> $datos['Limit']
		);

		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno) )
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al buscar los documentos a dar de alta. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		return true;
	}
	
	
	protected function BuscarDocumentosAltaxIdRol($datos,&$resultado,&$numfilas)
	{
		$spnombre="sel_DocumentosAlta_xIdRol";
		$sparam=array(
			'pIdArea'=>$datos['IdArea'],
			'pIdRol'=> $datos['IdRol'],
			'pxIdNivel'=> $datos['xIdNivel'],
			'pIdNivel'=> $datos['IdNivel'],
			'pxIdTipoDocumentoPadre'=> $datos['xIdTipoDocumentoPadre'],
			'pIdTipoDocumentoPadre'=> $datos['IdTipoDocumentoPadre'],
			'pxNombre'=> $datos['xNombre'],
			'pNombre'=> $datos['Nombre'],
			'pxNombreCorto'=> $datos['xNombreCorto'],
			'pIdCategoria'=> $datos['IdCategoria'],
			'pxIdCategoria'=> $datos['xIdCategoria'],
			'pIdClasificacion'=> $datos['IdClasificacion'],
			'pxIdClasificacion'=> $datos['xIdClasificacion'],
            'pMostrarAlta'=> $datos['MostrarAlta'],
            'pxMostrarAlta'=> $datos['xMostrarAlta'],
            'pMostrarAltaPON'=> $datos['MostrarAltaPON'],
            'pxMostrarAltaPON'=> $datos['xMostrarAltaPON'],
			'pNombreCorto'=> $datos['NombreCorto'],
			'pVigencia'=> $datos['Vigencia'],
			'pLimit'=> $datos['Limit']
		);

		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno) )
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al buscar los documentos a dar de alta. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		return true;
	}





	
	protected function BuscarDocumentosAltaDependientesxIdRolTipoDocumentoEstado($datos,&$resultado,&$numfilas)
	{
		$spnombre="sel_DocumentosTiposDependientes_Alta_xTipoDocumentoEstadoRol";
		$sparam=array(
			'pIdRol'=> $datos['IdRol'],
			'pIdTipoDocumento'=> $datos['IdTipoDocumento'],
			'pIdEstado'=> $datos['IdEstado']
		);

		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno) )
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al buscar los documentos a dar de alta desde uno generado. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		return true;
	}


	protected function BuscarDocumentosAltaDependientesxIdRolTipoDocumentoEstadoxIdTipoDocumento($datos,&$resultado,&$numfilas)
	{
		$spnombre="sel_DocumentosTiposDependientes_Alta_xTipoDocumentoEstadoRol_xIdTipoDocumentoSeleccionado";
		$sparam=array(
			'pIdRol'=> $datos['IdRol'],
			'pIdTipoDocumento'=> $datos['IdTipoDocumento'],
			'pIdEstado'=> $datos['IdEstado'],
			'pIdTipoDocumentoSeleccionado'=> $datos['IdTipoDocumentoSeleccionado']
		);

		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno) )
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al buscar los documentos a dar de alta desde uno generado. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		return true;
	}







}
?>