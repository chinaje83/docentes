<?php 
include(DIR_CLASES_DB."cLicenciasCircuitosEstados.db.php");

class cLicenciasCircuitosEstados extends cLicenciasCircuitosEstadosdb
{

    protected $conexion;
    protected $formato;
    protected $IdCircuito;

    // Constructor de la clase
    public function __construct($conexion,$IdCircuito,$formato=FMT_TEXTO){
        $this->conexion = &$conexion;
        $this->formato = $formato;
        $this->IdCircuito = $IdCircuito;
        parent::__construct();
    }

    function __destruct(){parent::__destruct();}

	public function BuscarxCodigo($datos,&$resultado,&$numfilas)
	{
		if (!parent::BuscarxCodigo($datos,$resultado,$numfilas))
			return false;
		return true;
	}

    public function BuscarxIdCircuito($datos,&$resultado,&$numfilas)
    {
        if (!parent::BuscarxIdCircuito($datos,$resultado,$numfilas))
            return false;
        return true;
    }

    public function BuscarEstadosNodoInicialxCircuito(&$resultado,&$numfilas)
    {
        $datos['IdCircuito']=$this->IdCircuito;
        if (!parent::BuscarEstadosNodoInicialxCircuitoBd ($datos,$resultado,$numfilas))
            return false;

        return true;
    }

    public function Insertar ($datos,&$datosNodo,&$IdNodoWorkflow)
    {
        if (!isset($datos['PosicionArriba']))
            $datos['PosicionArriba'] = 0;
        if (!isset($datos['PosicionIzquierda']))
            $datos['PosicionIzquierda'] = 0;

        $datos['IdCircuito'] = $this->IdCircuito;

        $datos['FechaAlta']=date("Y-m-d H:i:s");
        $datos['UltimaModificacionFecha']=date("Y-m-d H:i:s");

        if (!$this->_ValidarInsertar($datos,$datosNodo))
            return false;


        $datos['NodoInicial'] = 0;
        if ($datos['NodoInicialDatos']=="true")
            $datos['NodoInicial'] = 1;

        $datos['NodoGeneral'] = 0;

        if($datos['NodoInicial'] == 1)
        {

            if(!$this->BuscarEstadosNodoInicialxCircuito($resultado,$numfilas))
                return false;
            if($numfilas>0)
            {
                FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error, ya existe otra area que inicia el documento.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
                return false;
            }

        }

        if (!parent::InsertarBD($datos,$IdNodoWorkflow))
            return false;

        return true;
    }

    public function Modificar($datos)
    {
        if (!$this->_ValidarModificar($datos,$datosRegistro))
            return false;

        $datos['UltimaModificacionFecha'] = date("Y/m/d H:i:s");
        $datos['NodoInicial'] = 0;
        if ($datos['NodoInicialDatos']=="true")
            $datos['NodoInicial'] = 1;

        $datos['NodoGeneral'] = 0;




        if($datos['NodoInicial'] == 1)
        {

            if(!$this->BuscarEstadosNodoInicialxCircuito($resultado,$numfilas))
                return false;
            if($numfilas>0)
            {

                $fila = $this->conexion->ObtenerSiguienteRegistro($resultado);
                if($fila['IdNodoWorkflow']!=$datos['IdNodoWorkflow'])
                {
                    FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error, ya existe otra area que inicia el documento.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
                    return false;

                }

            }

        }

        if(!parent::ModificarBD($datos))
            return false;

        return true;
    }

    public function ModificarPosicion ($datos)
    {
        if (!$this->_ValidarModificarPosicion($datos,$datosRegistro))
            return false;

        $datos['UltimaModificacionFecha'] = date("Y/m/d H:i:s");
        if (!parent::ModificarPosicion($datos))
            return false;

        return true;
    }

    public function Eliminar ($datos)
    {

        if (!$this->_ValidarEliminar($datos,$datosRegistro))
            return false;

        $oLicenciasWorkflow = new cLicenciasWorkflow($this->conexion,$this->formato);
        $datos['IdNodoWorkflow'] = $datos['IdNodoWorkflow'];
        if(!$oLicenciasWorkflow->BuscarConexionesxIdNodoWorkflow($datos,$resultado,$numfilas))
            return false;

        if($numfilas>0)
        {
            FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error, debe eliminar las conexiones.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
            return false;

        }

        $oLicenciasCircuitosEstadosRolesAcciones = new cLicenciasCircuitosEstadosRolesAcciones($this->conexion,$this->formato);
        if(!$oLicenciasCircuitosEstadosRolesAcciones->EliminarxIdNodoWorkflow($datos))
            return false;

        $oLicenciasCircuitosEstadosRoles = new cLicenciasCircuitosEstadosRoles($this->conexion,$this->formato);
        if(!$oLicenciasCircuitosEstadosRoles->EliminarxIdNodoWorkflow($datos))
            return false;

        if (!parent::EliminarDB($datos))
            return false;

        return true;
    }


    private function _ValidarInsertar ($datos,&$datosNodo)
    {
        if (!$this->_ValidarDatosVacios($datos))
            return false;
        if (!isset ($datos['IdCircuito']) || ($datos['IdCircuito']==""))
        {
            FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error, debe ingresar un circuito.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
            return false;
        }
        if (!isset ($datos['IdEstado']) || ($datos['IdEstado']==""))
        {
            FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error, debe ingresar un estado.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
            return false;
        }

        $oLicenciasEstados = new cLicenciasEstados($this->conexion,$this->formato);
        $datosbuscar['Id'] = $datos['IdEstado'];
        if(!$oLicenciasEstados->BuscarxCodigo($datosbuscar,$resultado,$numfilas))
            return false;

        if($numfilas!=1)
        {
            FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error, debe ingresar un estado valida.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
            return false;
        }
        $datosEstado = $this->conexion->ObtenerSiguienteRegistro($resultado);


        $datosNodo['NombreEstado'] = $datosEstado['Nombre'];


        return true;
    }


    private function _ValidarModificarPosicion ($datos,&$datosRegistro)
    {
        if (!$this->_ValidarDatosVacios($datos))
            return false;
        if (!isset ($datos['IdNodoWorkflow']) || ($datos['IdNodoWorkflow']==""))
        {
            FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error, debe ingresar un codigo de area.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
            return false;
        }
        if(!$this->BuscarxCodigo($datos,$resultado,$numfilas))
            return false;

        if ($numfilas!=1)
        {
            FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error, debe ingresar un codigo de area.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
            return false;
        }
        $datosRegistro = $this->conexion->ObtenerSiguienteRegistro($resultado);
        return true;
    }


    private function _ValidarModificar ($datos,&$datosRegistro)
    {
        if (!isset ($datos['IdNodoWorkflow']) || ($datos['IdNodoWorkflow']==""))
        {
            FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error, debe ingresar un codigo de area.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
            return false;
        }
        if(!$this->BuscarxCodigo($datos,$resultado,$numfilas))
            return false;

        if ($numfilas!=1)
        {
            FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error, debe ingresar un codigo de area.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
            return false;
        }
        $datosRegistro = $this->conexion->ObtenerSiguienteRegistro($resultado);
        return true;
    }


    private function _ValidarEliminar ($datos,&$datosRegistro)
    {
        if(!$this->BuscarxCodigo($datos,$resultado,$numfilas))
            return false;

        if ($numfilas!=1)
        {
            FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error, debe ingresar un codigo de area.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
            return false;
        }
        $datosRegistro = $this->conexion->ObtenerSiguienteRegistro($resultado);
        //FALTA HACER

        /*
        $oExpWorkflow = new cExpWorkflow($this->conexion,$this->formato);
        if(!$oExpWorkflow->BuscarConexionesxWorkflowAreaCod($datos,$resultado,$numfilas))
            return false;

        if ($numfilas>0)
        {
            FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error, debe eliminar todas las conexiones al area. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>""));
            return false;
        }
        */

        return true;
    }




    private function _ValidarDatosVacios($datos)
    {
        if (!isset ($datos['PosicionArriba']) || ($datos['PosicionArriba']===""))
        {
            FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error, debe ingresar una posicion de alto.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
            return false;
        }
        if (!isset ($datos['PosicionIzquierda']) || ($datos['PosicionIzquierda']===""))
        {
            FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error, debe ingresar una posicion de izquierda.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
            return false;
        }

        return true;

    }



}
?>