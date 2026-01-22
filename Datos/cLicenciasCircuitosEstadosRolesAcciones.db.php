<?php 
abstract class cLicenciasCircuitosEstadosRolesAccionesdb
{


	function __construct(){}

	function __destruct(){}

    protected function BuscarxCodigo($datos,&$resultado,&$numfilas)
    {
        $spnombre="sel_LicenciasCircuitosEstadosRolesAcciones_xIdNodoWorkflow_IdRol_IdAccion";
        $sparam=array(
            'pIdNodoWorkflow'=> $datos['IdNodoWorkflow'],
            'pIdRol'=> $datos['IdRol'],
            'pIdAccion'=> $datos['IdAccion']

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
        $spnombre="sel_LicenciasCircuitosEstadosRolesAcciones_xIdNodoWorkflow";
        $sparam=array(
            'pIdNodoWorkflow'=> $datos['IdNodoWorkflow']
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
        $spnombre="sel_LicenciasCircuitosEstadosRolesAcciones_busqueda_avanzada";
        $sparam=array(
            'pxIdNodoWorkflow'=> $datos['xIdNodoWorkflow'],
            'pIdNodoWorkflow'=> $datos['IdNodoWorkflow'],
            'pxIdRol'=> $datos['xIdRol'],
            'pIdRol'=> $datos['IdRol'],
            'pxIdAccion'=> $datos['xIdAccion'],
            'pIdAccion'=> $datos['IdAccion'],
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



    protected function Insertar($datos)
    {
        $spnombre="ins_LicenciasCircuitosEstadosRolesAcciones";
        $sparam=array(
            'pIdNodoWorkflow'=> $datos['IdNodoWorkflow'],
            'pIdRol'=> $datos['IdRol'],
            'pIdAccion'=> $datos['IdAccion'],
            'pAccionObligatorio'=> $datos['AccionObligatorio'],
            'pAltaFecha'=> $datos['AltaFecha'],
            'pAltaUsuario'=> $datos['AltaUsuario'],
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

        return true;
    }



    protected function Modificar($datos)
    {
        $spnombre="upd_LicenciasCircuitosEstadosRolesAcciones_xIdNodoWorkflow";
        $sparam=array(
            'pIdRol'=> $datos['IdRol'],
            'pIdAccion'=> $datos['IdAccion'],
            'pUltimaModificacionUsuario'=> $_SESSION['usuariocod'],
            'pUltimaModificacionFecha'=> $datos['UltimaModificacionFecha'],
            'pUltimaModificacionApp'=> APP,
            'pIdNodoWorkflow'=> $datos['IdNodoWorkflow']
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
        $spnombre="del_LicenciasCircuitosEstadosRolesAcciones_xIdNodoWorkflow_IdRol_IdAccion";
        $sparam=array(
            'pIdNodoWorkflow'=> $datos['IdNodoWorkflow'],
            'pIdRol'=> $datos['IdRol'],
            'pIdAccion'=> $datos['IdAccion']
        );
        if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno))
        {
            FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al eliminar por codigo. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
            return false;
        }

        return true;
    }



    protected function ModificarAccionObligatorio($datos)
    {
        $spnombre="upd_LicenciasCircuitosEstadosRolesAcciones_xIdNodoWorkflow_IdRol_IdAccion";
        $sparam=array(
            'pIdNodoWorkflow'=> $datos['IdNodoWorkflow'],
            'pIdRol'=> $datos['IdRol'],
            'pIdAccion'=> $datos['IdAccion'],
            'pAccionObligatorio'=> $datos['AccionObligatorio']
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