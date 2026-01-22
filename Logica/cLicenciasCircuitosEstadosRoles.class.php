<?php 
include(DIR_CLASES_DB."cLicenciasCircuitosEstadosRoles.db.php");

class cLicenciasCircuitosEstadosRoles extends cLicenciasCircuitosEstadosRolesdb
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

    public function BuscarxIdNodoWorkflow($datos,&$resultado,&$numfilas)
    {
        if (!parent::BuscarxIdNodoWorkflow($datos,$resultado,$numfilas))
            return false;
        return true;
    }

	public function BusquedaAvanzada($datos,&$resultado,&$numfilas)
	{
		$sparam=array(
            'xIdNodoWorkflow'=> 0,
            'IdNodoWorkflow'=> "",
            'xIdRol'=> 0,
            'IdRol'=> "",
			'limit'=> '',
			'orderby'=> "IdNodoWorkflow DESC, IdRol DESC"
		);

        if(isset($datos['IdNodoWorkflow']) && $datos['IdNodoWorkflow']!="")
        {
            $sparam['IdNodoWorkflow']= $datos['IdNodoWorkflow'];
            $sparam['xIdNodoWorkflow']= 1;
        }

        if(isset($datos['IdRol']) && $datos['IdRol']!="")
        {
            $sparam['IdRol']= $datos['IdRol'];
            $sparam['xIdRol']= 1;
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
		if (!$this->_ValidarInsertar($datos))
			return false;

		$datos['AltaFecha']=date("Y-m-d H:i:s");
		$datos['UltimaModificacionFecha']=date("Y-m-d H:i:s");
		$datos['Estado'] = ACTIVO;
		$this->_SetearNull($datos);
		if (!parent::Insertar($datos,$codigoinsertado))
            return false;

		return true;
	}


	public function Eliminar($datos)
	{
		if (!$this->_ValidarEliminar($datos,$datosRegistro))
			return false;

		$oLicenciasWorkflowRoles = new cLicenciasWorkflowRoles($this->conexion,$this->formato);
        $datosBuscar['IdNodoWorkflowInicial'] = $datos['IdNodoWorkflow'];
		$datosBuscar['Rol'] = $datos['IdRol'];
        if(!$oLicenciasWorkflowRoles->BuscarxIdNodoWorkflowInicialxRol($datosBuscar,$resultado,$numfilas))
            return false;

        if($numfilas>0)
        {
            FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error, debe eliminar el rol de la conexion.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
            return false;
        }

		$oLicenciasCircuitosEstadosRolesAcciones = new cLicenciasCircuitosEstadosRolesAcciones($this->conexion,$this->formato);
        $datosmodif['IdNodoWorkflow'] = $datos['IdNodoWorkflow'];
        $datosmodif['IdRol'] = $datos['IdRol'];

		if(!$oLicenciasCircuitosEstadosRolesAcciones->EliminarxIdNodoWorkflowxIdRol($datosmodif))
		    return false;

		if (!parent::Eliminar($datosmodif))
			return false;
		return true;
	}

    public function EliminarxIdNodoWorkflow($datos)
    {

        if (!parent::EliminarxIdNodoWorkflow($datos))
            return false;
        return true;
    }








//-----------------------------------------------------------------------------------------
//FUNCIONES PRIVADAS
//-----------------------------------------------------------------------------------------

	private function _ValidarInsertar($datos)
	{
		if (!$this->_ValidarDatosVacios($datos))
			return false;


        if(!$this->BusquedaAvanzada($datos,$resultado,$numfilas))
            return false;

        if($numfilas>0)
        {
            FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error, ya existe el rol.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
            return false;
        }

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
        if (!isset($datos['IdNodoWorkflow']) || $datos['IdNodoWorkflow']=="")
            $datos['IdNodoWorkflow']="NULL";

        if (!isset($datos['Rol']) || $datos['Rol']=="")
            $datos['Rol']="NULL";

		return true;
	}



	private function _ValidarDatosVacios($datos)
	{

        if (!isset($datos['IdNodoWorkflow']) || $datos['IdNodoWorkflow']=="")
        {
            FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Debe ingresar un circuito",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
            return false;
        }

        if (isset($datos['IdNodoWorkflow']) && $datos['IdNodoWorkflow']!="")
        {
            if (!FuncionesPHPLocal::ValidarContenido($this->conexion,$datos['IdNodoWorkflow'],"NumericoEntero"))
            {
                FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error debe ingresar un campo numérico.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
                return false;
            }
        }



        if (!isset($datos['IdRol']) || $datos['IdRol']=="")
        {
            FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Debe ingresar un rol",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
            return false;
        }

        if (isset($datos['IdRol']) && $datos['IdRol']!="")
        {
            if (!FuncionesPHPLocal::ValidarContenido($this->conexion,$datos['IdRol'],"NumericoEntero"))
            {
                FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error debe ingresar un campo numérico.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
                return false;
            }
        }
		return true;
	}

}
?>