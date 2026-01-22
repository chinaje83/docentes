<?php 
include(DIR_CLASES_DB."cLicenciasWorkflowRoles.db.php");

class cLicenciasWorkflowRoles extends cLicenciasWorkflowRolesdb
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

    public function BuscarxIdLicenciaWorkflow($datos,&$resultado,&$numfilas)
    {
        if (!parent::BuscarxIdLicenciaWorkflow($datos,$resultado,$numfilas))
            return false;
        return true;
    }

    public function BuscarxIdNodoWorkflowInicialxRol($datos,&$resultado,&$numfilas)
    {
        if (!parent::BuscarxIdNodoWorkflowInicialxRol($datos,$resultado,$numfilas))
            return false;
        return true;
    }

	public function BusquedaAvanzada($datos,&$resultado,&$numfilas)
	{
		$sparam=array(
            'xId'=> 0,
            'Id'=> "",
            'xIdLicenciaWorkflow'=> 0,
            'IdLicenciaWorkflow'=> "",
            'xRol'=> 0,
            'Rol'=> "",
			'limit'=> '',
			'orderby'=> "Id DESC"
		);

        if(isset($datos['Id']) && $datos['Id']!="")
        {
            $sparam['Id']= $datos['Id'];
            $sparam['xId']= 1;
        }

        if(isset($datos['IdLicenciaWorkflow']) && $datos['IdLicenciaWorkflow']!="")
        {
            $sparam['IdLicenciaWorkflow']= $datos['IdLicenciaWorkflow'];
            $sparam['xIdLicenciaWorkflow']= 1;
        }

        if(isset($datos['Rol']) && $datos['Rol']!="")
        {
            $sparam['Rol']= $datos['Rol'];
            $sparam['xRol']= 1;
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

        $oLicenciasWorkflowRolesAcciones = new cLicenciasWorkflowRolesAcciones($this->conexion,$this->formato);
        if(!$oLicenciasWorkflowRolesAcciones->EliminarxIdLicenciaWorkflowxIdRol($datosRegistro))
            return false;

		$datosmodif['Id'] = $datos['Id'];
		if (!parent::Eliminar($datosmodif))
			return false;
		return true;
	}

    public function EliminarxIdLicenciaWorkflow($datos)
    {

        if (!parent::EliminarxIdLicenciaWorkflow($datos))
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
        if (!isset($datos['IdLicenciaWorkflow']) || $datos['IdLicenciaWorkflow']=="")
            $datos['IdLicenciaWorkflow']="NULL";

        if (!isset($datos['Rol']) || $datos['Rol']=="")
            $datos['Rol']="NULL";

		return true;
	}



	private function _ValidarDatosVacios($datos)
	{

        if (!isset($datos['IdLicenciaWorkflow']) || $datos['IdLicenciaWorkflow']=="")
        {
            FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Debe ingresar un circuito",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
            return false;
        }

        if (isset($datos['IdLicenciaWorkflow']) && $datos['IdLicenciaWorkflow']!="")
        {
            if (!FuncionesPHPLocal::ValidarContenido($this->conexion,$datos['IdLicenciaWorkflow'],"NumericoEntero"))
            {
                FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error debe ingresar un campo numérico.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
                return false;
            }
        }



        if (!isset($datos['Rol']) || $datos['Rol']=="")
        {
            FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Debe ingresar un rol",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
            return false;
        }

        if (isset($datos['Rol']) && $datos['Rol']!="")
        {
            if (!FuncionesPHPLocal::ValidarContenido($this->conexion,$datos['Rol'],"NumericoEntero"))
            {
                FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error debe ingresar un campo numérico.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
                return false;
            }
        }
		return true;
	}

}
?>