<?php  
include(DIR_CLASES_DB."cCircuitosWorkflow.db.php");

class cCircuitosWorkflow extends cCircuitosWorkflowdb	
{
	protected $conexion;
	protected $formato;
	
	// Constructor de la clase
	public function __construct($conexion,$formato=FMT_TEXTO){
		$this->conexion = &$conexion;
		$this->formato = $formato;
		parent::__construct(); 
    } 
	
	// Destructor de la clase
	public function __destruct() {	
		parent::__destruct(); 
    } 	



//-----------------------------------------------------------------------------------------
//							 PUBLICAS	
//----------------------------------------------------------------------------------------- 

	public function BuscarxCodigo($datos,&$resultado,&$numfilas)
	{
		if (!parent::BuscarxCodigo ($datos,$resultado,$numfilas))
			return false;
		return true;			
	}


	public function BuscarAccionesWorkflow($datos,&$resultado,&$numfilas)
	{
		$datos['IdRol'] = implode(",",$_SESSION['rolcod']);
		if (!parent::BuscarAccionesWorkflow ($datos,$resultado,$numfilas))
			return false;
		return true;			
	}

	public function BuscarAccionesWorkflowxCodigoWorkflow($datos,&$resultado,&$numfilas)
	{
		$datos['IdRol'] = implode(",",$_SESSION['rolcod']);
		if (!parent::BuscarAccionesWorkflowxCodigoWorkflow ($datos,$resultado,$numfilas))
			return false;
		return true;			
	}


	public function BuscarConexionesxCircuito($datos,&$resultado,&$numfilas)
	{
		if (!parent::BuscarConexionesxCircuito ($datos,$resultado,$numfilas))
			return false;
		return true;			
	}


	public function BuscarConexionesxWorkflowAreaCod($datos,&$resultado,&$numfilas)
	{
		if (!parent::BuscarConexionesxWorkflowAreaCod ($datos,$resultado,$numfilas))
			return false;
		return true;			
	}
	
	
	public function BuscarConexionesxCircuitoxAreaInicialxEstadoInicialxAreaFinalxEstadoFinal($datos,&$resultado,&$numfilas)
	{
		if (!parent::BuscarConexionesxCircuitoxAreaInicialxEstadoInicialxAreaFinalxEstadoFinal ($datos,$resultado,$numfilas))
			return false;
		return true;			
	}

	public function BuscarAccionesParticularesxIdWorkflow($datos,&$resultado,&$numfilas)
	{
		if (!parent::BuscarAccionesParticularesxIdWorkflow ($datos,$resultado,$numfilas))
			return false;
		return true;			
	}


	

	public function Modificar($datos)
	{	
	
		if (!$this->_ValidarModificar($datos))
			return false;
		
		if (isset($datos['AreaOrigen']) && $datos['AreaOrigen']==1)
			$datos['AreaOrigen'] = 1;
		else
			$datos['AreaOrigen'] = 0;

        if (isset($datos['NoValidaDatos']) && $datos['NoValidaDatos']==1)
            $datos['NoValidaDatos'] = 1;
        else
            $datos['NoValidaDatos'] = 0;

		$datos['UltimaModificacionFecha']=date("Y-m-d H:i:s");
		$datos['UltimaModificacionUsuario']=$_SESSION['usuariocod'];
		if(!parent::Modificar($datos))
			return false;

		$oCircuitosWorkflowRoles = new cCircuitosWorkflowRoles($this->conexion,$this->formato);
		if (!$oCircuitosWorkflowRoles->ActualizarRoles($datos))
			return false;
		
		return true;
	} 
	
	public function Insertar($datos,&$workflowcod)
	{	

		if (!$this->_ValidarInsertar($datos))
			return false;
		
		if (!isset($datos['NombreAccion']))
			$datos['NombreAccion'] = "Enviar";	
		if (!isset($datos['ClassBoton']))
			$datos['ClassBoton'] = "";	
		if (!isset($datos['ClassIcono']))
			$datos['ClassIcono'] = "";	
		if (!isset($datos['AreaOrigen']))
			$datos['AreaOrigen'] = "0";	
		if (!isset($datos['Clase']))
			$datos['Clase'] = "NULL";	
		if (!isset($datos['Metodo']))
			$datos['Metodo'] = "NULL";

        if (isset($datos['NoValidaDatos']) && $datos['NoValidaDatos']==1)
            $datos['NoValidaDatos'] = 1;
        else
            $datos['NoValidaDatos'] = 0;


		$datos['FechaAlta']=date("Y-m-d H:i:s");
		$datos['UltimaModificacionFecha']=date("Y-m-d H:i:s");
		if(!parent::Insertar($datos,$workflowcod))
			return false;
		
		return true;
	} 
	
	public function Eliminar($datos)
	{	

		if (!$this->_ValidarEliminar($datos))
			return false;
			
		
		$oCircuitosAreasEstadosRolesAcciones = new cCircuitosWorkflowRolesAcciones($this->conexion,$this->formato);
		
		if(!$oCircuitosAreasEstadosRolesAcciones->EliminarxIdWorkflow($datos))
			return false;	
		
		
		$oCircuitosWorkflowRoles = new cCircuitosWorkflowRoles($this->conexion,$this->formato);
		
		if(!$oCircuitosWorkflowRoles->BuscarxIdWorkflow($datos,$resultado,$numfilas))
			return false;
			
		while($fila = $this->conexion->ObtenerSiguienteRegistro($resultado))
		{
			if(!$oCircuitosWorkflowRoles->Eliminar($fila))
				return false;	
			
		}	
		
		
		
		$oCircuitosWorkflowTiposDocumentos = new cCircuitosWorkflowTiposDocumentos($this->conexion,$this->formato);
		
		if(!$oCircuitosWorkflowTiposDocumentos->BuscarxIdWorkflow($datos,$resultado,$numfilas))
			return false;
			
		while($fila = $this->conexion->ObtenerSiguienteRegistro($resultado))
		{
			if(!$oCircuitosWorkflowTiposDocumentos->Eliminar($fila))
				return false;	
			
		}	
		
			
			
		if(!parent::Eliminar($datos))
			return false;
		
		return true;
	} 
	
	
	
	
	private function _ValidarInsertar($datos)
	{

		if (!$this->_ValidarDatosVacios($datos))
			return false;

		return true;
	}


	private function _ValidarEliminar($datos)
	{

		if(!$this->BuscarxCodigo($datos,$resultado,$numfilas))
			return false;
			
		if ($numfilas!=1)
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error, debe ingresar una codigo. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>""));
			return false;
		}


		return true;
	}

	private function _ValidarModificar($datos)
	{

		if(!$this->BuscarxCodigo($datos,$resultado,$numfilas))
			return false;
			
		if ($numfilas!=1)
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error, debe ingresar una codigo. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>""));
			return false;
		}
		if (trim($datos['NombreAccion'])=="")
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error, debe ingresar un nombre de la accion. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>""));
			return false;
		}


		return true;
	}


	
	private function _ValidarDatosVacios($datos)
	{
		if ($datos['IdCircuito']=="")
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error, debe ingresar un circuito. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>""));
			return false;
		}

		return true;
	}


	
}//FIN CLASS
?>