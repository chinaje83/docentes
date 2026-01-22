<?php
include(DIR_CLASES_DB."cLicenciasWorkflow.db.php");

class cLicenciasWorkflow extends cLicenciasWorkflowdb
{

    protected $conexion;
    protected $formato;

    function __construct($conexion,$formato=FMT_TEXTO){
        $this->conexion = &$conexion;
        $this->formato = &$formato;
        parent::__construct();
    }

    function __destruct(){parent::__destruct();}

    public function BuscarxCodigo($datos,&$resultado,&$numfilas)
    {
        if (!parent::BuscarxCodigo($datos,$resultado,$numfilas))
            return false;
        return true;
    }

    public function BuscarxIdCircuitoxIdNodoWorkflowInicialxIdNodoWorkflowFinal($datos,&$resultado,&$numfilas)
    {
        if (!parent::BuscarxIdCircuitoxIdNodoWorkflowInicialxIdNodoWorkflowFinal($datos,$resultado,$numfilas))
            return false;
        return true;
    }

    public function BuscarConexionesxCircuito($datos,&$resultado,&$numfilas)
    {
        if (!parent::BuscarConexionesxCircuito ($datos,$resultado,$numfilas))
            return false;
        return true;
    }

    public function BuscarConexionesxIdNodoWorkflow($datos,&$resultado,&$numfilas)
    {
        if (!parent::BuscarConexionesxIdNodoWorkflow ($datos,$resultado,$numfilas))
            return false;
        return true;
    }




    public function BusquedaAvanzada($datos,&$resultado,&$numfilas)
    {
        $sparam=array(
            'IdCircuito'=> $datos['IdCircuito'],
            'xId'=> 0,
            'Id'=> "",
            'xIdEstadoInicial'=> 0,
            'IdEstadoInicial'=> "",
            'xIdEstadoFinal'=> 0,
            'IdEstadoFinal'=> "",
            'xNombre'=> 0,
            'Nombre'=> "",
            'xEstado'=> 0,
            'Estado'=> "-1",
            'limit'=> '',
            'orderby'=> "Id DESC"
        );

        if(isset($datos['Id']) && $datos['Id']!="")
        {
            $sparam['Id']= $datos['Id'];
            $sparam['xId']= 1;
        }

        if(isset($datos['IdEstadoInicial']) && $datos['IdEstadoInicial']!="")
        {
            $sparam['IdEstadoInicial']= $datos['IdEstadoInicial'];
            $sparam['xIdEstadoInicial']= 1;
        }

        if(isset($datos['IdEstadoFinal']) && $datos['IdEstadoFinal']!="")
        {
            $sparam['IdEstadoFinal']= $datos['IdEstadoFinal'];
            $sparam['xIdEstadoFinal']= 1;
        }

        if(isset($datos['Nombre']) && $datos['Nombre']!="")
        {
            $sparam['Nombre']= $datos['Nombre'];
            $sparam['xNombre']= 1;
        }

        if(isset($datos['Estado']) && $datos['Estado']!="")
        {
            $sparam['Estado']= $datos['Estado'];
            $sparam['xEstado']= 1;
        }

        if(isset($datos['orderby']) && $datos['orderby']!="")
            $sparam['orderby']= $datos['orderby'];

        if(isset($datos['limit']) && $datos['limit']!="")
            $sparam['limit']= $datos['limit'];

        if (!parent::BusquedaAvanzada($sparam,$resultado,$numfilas))
            return false;
        return true;
    }


    public function Insertar($datos,&$codigoinsertado)
    {
        if (!isset($datos['IdCircuito']) || $datos['IdCircuito']=="")
        {
            FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Debe ingresar un circuito",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
            return false;
        }

        if (isset($datos['IdCircuito']) && $datos['IdCircuito']!="")
        {
            if (!FuncionesPHPLocal::ValidarContenido($this->conexion,$datos['IdCircuito'],"NumericoEntero"))
            {
                FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error debe ingresar un campo numérico.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
                return false;
            }
        }

        $datos['AltaFecha']=date("Y-m-d H:i:s");
        $datos['UltimaModificacionFecha']=date("Y-m-d H:i:s");
        $datos['Estado'] = ACTIVO;

        $this->_SetearNull($datos);
        $this->ObtenerProximoOrden($datos,$proxorden);
        $datos['Orden'] = $proxorden;
        if (!parent::Insertar($datos,$codigoinsertado))
            return false;


        /*	$oAuditoriasCircuitos = new cAuditoriasCircuitos($this->conexion,$this->formato);
            $datos['IdCircuito'] = $datos['IdCircuito'];
            $datos['AltaUsuario'] = $_SESSION['usuariocod'];
            $datos['FechaAlta'] = $datos['FechaAlta'];
            $datos['Estado'] = $datos['Estado'];
            $datos['Accion'] = INSERTAR;
            $datos['Nombre'] = $datos['Nombre'];
            $datos['NombreCorto'] = $datos['NombreCorto'];
            $datos['Descripcion'] = $datos['Descripcion'];
            $datos['UltimaModificacionUsuario'] = $_SESSION['usuariocod'];
            if(!$oAuditoriasCircuitos->InsertarLog($datos,$codigoLogInsertado))
                return false;*/

        return true;
    }



    public function Modificar($datos)
    {
        if (!$this->_ValidarModificar($datos,$datosRegistro))
            return false;

        $datos['UltimaModificacionFecha']=date("Y-m-d H:i:s");
        $this->_SetearNull($datos);
        if (!parent::Modificar($datos))
            return false;

        /*$oAuditoriasCircuitos = new cAuditoriasCircuitos($this->conexion,$this->formato);
        $datos['IdCircuito'] = $datosRegistro['IdCircuito'];
        $datos['AltaUsuario'] = $datosRegistro['AltaUsuario'];
        $datos['FechaAlta'] = $datosRegistro['FechaAlta'];
        $datos['Estado'] = $datosRegistro['Estado'];
        $datos['Accion'] = MODIFICACION;
        $datos['Nombre'] = $datos['Nombre'];
        $datos['NombreCorto'] = $datos['NombreCorto'];
        $datos['Descripcion'] = $datos['Descripcion'];
        $datos['UltimaModificacionUsuario'] = $_SESSION['usuariocod'];
        if(!$oAuditoriasCircuitos->InsertarLog($datos,$codigoInsertado))
            return false;*/



        return true;
    }



    public function Eliminar($datos)
    {
        if (!$this->_ValidarEliminar($datos,$datosRegistro))
            return false;

        $oLicenciasWorkflowRolesAcciones = new cLicenciasWorkflowRolesAcciones($this->conexion,$this->formato);
        $datosmodif['IdLicenciaWorkflow'] = $datos['Id'];
        if(!$oLicenciasWorkflowRolesAcciones->EliminarxIdLicenciaWorkflow($datosmodif))
            return false;


        $oLicenciasWorkflowRoles = new cLicenciasWorkflowRoles($this->conexion,$this->formato);
        $datosmodif['IdLicenciaWorkflow'] = $datos['Id'];
        if(!$oLicenciasWorkflowRoles->EliminarxIdLicenciaWorkflow($datosmodif))
            return false;


        $datosmodif['Id'] = $datos['Id'];
        if (!parent::Eliminar($datosmodif))
            return false;


        /*$oAuditoriasCircuitos = new cAuditoriasCircuitos($this->conexion,$this->formato);
        $datos = $datosRegistro;
        $datos['UltimaModificacionUsuario'] = $_SESSION['usuariocod'];
        $datos['UltimaModificacionFecha'] = $datosmodif['UltimaModificacionFecha'];
        $datos['Accion'] = ELIMINAR;
        if(!$oAuditoriasCircuitos->InsertarLog($datos,$codigoLogInsertado))
            return false;*/


        return true;
    }



    public function ModificarEstado($datos)
    {
        if (!parent::ModificarEstado($datos))
            return false;
        return true;
    }



    public function Activar($datos)
    {
        if (!$this->BuscarxCodigo($datos,$resultado,$numfilas))
            return false;

        if ($numfilas!=1)
        {
            FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error debe ingresar un código valido.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
            return false;
        }
        $datosRegistro = $this->conexion->ObtenerSiguienteRegistro($resultado);

        $datosmodif['UltimaModificacionFecha']=date("Y-m-d H:i:s");
        $datosmodif['Id'] = $datos['Id'];
        $datosmodif['Estado'] = ACTIVO;
        if (!$this->ModificarEstado($datosmodif))
            return false;


        /*$oAuditoriasCircuitos = new cAuditoriasCircuitos($this->conexion,$this->formato);
        $datos = $datosRegistro;
        $datos['UltimaModificacionUsuario'] = $_SESSION['usuariocod'];
        $datos['UltimaModificacionFecha'] = $datosmodif['UltimaModificacionFecha'];
        $datos['Accion'] = ACTIVAR;
        if(!$oAuditoriasCircuitos->InsertarLog($datos,$codigoLogInsertado))
            return false;*/
        return true;
    }



    public function DesActivar($datos)
    {
        if (!$this->BuscarxCodigo($datos,$resultado,$numfilas))
            return false;

        if ($numfilas!=1)
        {
            FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error debe ingresar un código valido.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
            return false;
        }
        $datosRegistro = $this->conexion->ObtenerSiguienteRegistro($resultado);


        $datosmodif['UltimaModificacionFecha']=date("Y-m-d H:i:s");
        $datosmodif['Id'] = $datos['Id'];
        $datosmodif['Estado'] = NOACTIVO;
        if (!$this->ModificarEstado($datosmodif))
            return false;

        /*$oAuditoriasCircuitos = new cAuditoriasCircuitos($this->conexion,$this->formato);
        $datos = $datosRegistro;
        $datos['UltimaModificacionUsuario'] = $_SESSION['usuariocod'];
        $datos['UltimaModificacionFecha'] = $datosmodif['UltimaModificacionFecha'];
        $datos['Accion'] = DESACTIVAR;
        if(!$oAuditoriasCircuitos->InsertarLog($datos,$codigoLogInsertado))
            return false;*/

        return true;
    }


    public function ModificarOrdenCompleto($datos): bool
    {
        $datosmodif['Orden'] = 1;
        $datosmodif['IdCircuito'] = $datos['IdCircuito'];
        $datosmodif['UltimaModificacionFecha']=date("Y-m-d H:i:s");
        $arregloOrden = explode(",",$datos['orden']);

        foreach ($arregloOrden as $Id){
            $datosmodif['Id'] = $Id;
            if (!parent::ModificarOrden($datosmodif))
                return false;
            $datosmodif['IdMateria']++;
        }
        return true;
    }




//-----------------------------------------------------------------------------------------
//FUNCIONES PRIVADAS
//-----------------------------------------------------------------------------------------

    private function _ValidarInsertar($datos)
    {
        if (!$this->_ValidarDatosVacios($datos))
            return false;

        if(!$this->BuscarxIdCircuitoxIdNodoWorkflowInicialxIdNodoWorkflowFinal($datos,$resultado,$numfilas))
            return false;

        if($numfilas>0)
        {
            FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error, ya existe la conexion.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
            return false;
        }

        return true;
    }



    private function _ValidarModificar($datos,&$datosRegistro)
    {
        if (!$this->BuscarxCodigo($datos,$resultado,$numfilas))
            return false;

        if ($numfilas!=1)
        {
            FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error debe ingresar un código valido.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
            return false;
        }
        $datosRegistro = $this->conexion->ObtenerSiguienteRegistro($resultado);
        if (!$this->_ValidarDatosVacios($datos))
            return false;

        return true;
    }



    private function _ValidarEliminar($datos,&$datosRegistro)
    {
        if (!$this->BuscarxCodigo($datos,$resultado,$numfilas))
            return false;

        if ($numfilas!=1)
        {
            FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error debe ingresar un código valido.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
            return false;
        }
        $datosRegistro = $this->conexion->ObtenerSiguienteRegistro($resultado);
        return true;
    }



    private function _SetearNull(&$datos)
    {
        if (!isset($datos['IdCircuito']) || $datos['IdCircuito']=="")
            $datos['IdCircuito']="NULL";

        if (!isset($datos['IdNodoWorkflowInicial']) || $datos['IdNodoWorkflowInicial']=="")
            $datos['IdNodoWorkflowInicial']="NULL";

        if (!isset($datos['IdNodoWorkflowFinal']) || $datos['IdNodoWorkflowFinal']=="")
            $datos['IdNodoWorkflowFinal']="NULL";

        if (!isset($datos['Nombre']) || $datos['Nombre']=="")
            $datos['Nombre']="Enviar";

        if (!isset($datos['Accion']) || $datos['Accion']=="")
            $datos['Accion']="NULL";

        if (!isset($datos['Funcion']) || $datos['Funcion']=="")
            $datos['Funcion']="devolverVerdadero";

        if (!isset($datos['Clase']) || $datos['Clase']=="")
            $datos['Clase']="default";

        if (!isset($datos['Icono']) || $datos['Icono']=="")
            $datos['Icono']="NULL";

        if (!isset($datos['Tooltip']) || $datos['Tooltip']=="")
            $datos['Tooltip']="NULL";

        if (!isset($datos['Orden']) || $datos['Orden']=="")
            $datos['Orden']="NULL";

        return true;
    }



    private function _ValidarDatosVacios($datos)
    {

        if (!isset($datos['IdCircuito']) || $datos['IdCircuito']=="")
        {
            FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Debe ingresar un circuito",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
            return false;
        }

        if (isset($datos['IdCircuito']) && $datos['IdCircuito']!="")
        {
            if (!FuncionesPHPLocal::ValidarContenido($this->conexion,$datos['IdCircuito'],"NumericoEntero"))
            {
                FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error debe ingresar un campo numérico.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
                return false;
            }
        }

        if (!isset($datos['Nombre']) || $datos['Nombre']=="")
        {
            FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Debe ingresar un nombre",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
            return false;
        }

        /*if (!isset($datos['Accion']) || $datos['Accion']=="")
        {
            FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Debe ingresar una accion",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
            return false;
        }*/

        if (!isset($datos['Funcion']) || $datos['Funcion']=="")
        {
            FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Debe ingresar una funcion",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
            return false;
        }

        if (!isset($datos['Clase']) || $datos['Clase']=="")
        {
            FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Debe ingresar una clase",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
            return false;
        }

       /* if (!isset($datos['Icono']) || $datos['Icono']=="")
        {
            FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Debe ingresar un icono",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
            return false;
        }

        if (!isset($datos['Tooltip']) || $datos['Tooltip']=="")
        {
            FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Debe ingresar un tooltip",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
            return false;
        }*/

        return true;
    }


    private function ObtenerProximoOrden(array $datos, ?int &$proxorden): bool
    {
        $proxorden = 0;
        if (!parent::BuscarUltimoOrden($datos,$resultado,$numfilas))
            return false;
        if ($numfilas!=0){
            $datos = $this->conexion->ObtenerSiguienteRegistro($resultado);
            $proxorden = $datos['maximo'] + 1;
        }
        return true;
    }



}
?>