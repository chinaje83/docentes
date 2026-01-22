<?php  
include(DIR_CLASES_DB."cCircuitosWorkflowRoles.db.php");

class cCircuitosWorkflowRoles extends cCircuitosWorkflowRolesdb	
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

	public function BuscarxIdWorkflowxIdNodoWorkflow($datos,&$resultado,&$numfilas)
	{
		if (!parent::BuscarxIdWorkflowxIdNodoWorkflow ($datos,$resultado,$numfilas))
			return false;
		return true;			
	}
	
	
	public function BuscarxIdWorkflow($datos,&$resultado,&$numfilas)
	{
		if (!parent::BuscarxIdWorkflow ($datos,$resultado,$numfilas))
			return false;
		return true;			
	}
	
	
	public function BuscarxIdNodoWorkflowxIdRol($datos,&$resultado,&$numfilas)
	{
		if (!parent::BuscarxIdNodoWorkflowxIdRol ($datos,$resultado,$numfilas))
			return false;
		return true;			
	}
	
	public function BuscarxIdWorkflowxIdRol($datos,&$resultado,&$numfilas)
	{
		if (!parent::BuscarxIdWorkflowxIdRol ($datos,$resultado,$numfilas))
			return false;
		return true;			
	}
	

	public function ActualizarRoles($datos)
	{
		
		$oCircuitosWorkflow = new cCircuitosWorkflow($this->conexion,$this->formato);
		
		if(!$oCircuitosWorkflow->BuscarxCodigo($datos,$resultado,$numfilas))
			return false;
			
		$filaCircuitosWorkflow = $this->conexion->ObtenerSiguienteRegistro($resultado);	
	
		$datos['IdNodoWorkflow'] = $filaCircuitosWorkflow['IdNodoWorkflowActual'];
		
		if (!$this->BuscarxIdWorkflowxIdNodoWorkflow($datos,$resultadoRoles,$numfilasRoles))
			return false;	

		$arregloinsertados = array();
		while($fila = $this->conexion->ObtenerSiguienteRegistro($resultadoRoles))
			$arregloinsertados[$fila['IdRol']] = $fila['IdRol'];
		
		$datosdevueltos = array();
		if(isset($datos['IdRol']))
			$datosdevueltos = $datos['IdRol'];
				
		$arregloeliminar = array_diff($arregloinsertados,$datosdevueltos);
		$arregloinsertar = array_diff($datosdevueltos,$arregloinsertados);			

		$oCircuitosWorkflowRolesAcciones = new cCircuitosWorkflowRolesAcciones($this->conexion,$this->formato);		

		
		$datoseliminar['IdWorkflow'] = $datosinsertar['IdWorkflow'] = $datos['IdWorkflow'];
		$datoseliminar['IdNodoWorkflow'] = $datosinsertar['IdNodoWorkflow'] = $datos['IdNodoWorkflow'];
		if(count($arregloinsertar)>0)
		{
			foreach ($arregloinsertar as $IdRol)
			{
				$datosinsertar['IdRol'] = $IdRol;
				if(!$this->Insertar($datosinsertar))
					return false;
			}
		}
		if(count($arregloeliminar)>0)
		{
			foreach ($arregloeliminar as $IdRol)
			{
					
				$datoseliminar['IdRol'] = $IdRol;
				
				if(!$oCircuitosWorkflowRolesAcciones->EliminarxIdWorkflowxIdNodoWorkflowxIdRol($datoseliminar))
					return false;
				
				if(!$this->Eliminar($datoseliminar))
					return false;
			}
		}
		
		
		if(isset($datos['IdRol']) && count($datos['IdRol'])>0)
		{
			
			$datosactualizar['IdWorkflow'] = $datos['IdWorkflow'];
			$datosactualizar['IdNodoWorkflow'] = $datos['IdNodoWorkflow'];
			foreach ($datos['IdRol'] as $IdRol)
			{
				$datosactualizar['IdRol'] = $IdRol;
				$datosactualizar['IdAccion'] = array();
				if(isset($datos['IdAccion'][$IdRol]))
					$datosactualizar['IdAccion'] = $datos['IdAccion'][$IdRol];
				$datosactualizar['AccionObligatorio'] = array();
				if(isset($datos['AccionObligatorio'][$IdRol]))
					$datosactualizar['AccionObligatorio'] = $datos['AccionObligatorio'][$IdRol];
				
				if(!$oCircuitosWorkflowRolesAcciones->ActualizarRoles($datosactualizar))
					return false;
					
			}
			
		}

		return true;	
	}



	public function Insertar($datos)
	{	

		$datos['AltaFecha']=date("Y-m-d H:i:s");
		$datos['AltaUsuario'] = $_SESSION['usuariocod'];
		$datos['UltimaModificacionFecha']=date("Y-m-d H:i:s");
		$datos['UltimaModificacionUsuario'] = $_SESSION['usuariocod'];
		
		
		if (!$this->_ValidarInsertar($datos))
			return false;
			

		
		if(!parent::InsertarDB($datos))
			return false;
			
		/*$oAuditorias = new cAuditoriasCircuitosAreasEstados($this->conexion,$this->formato);

		$datosLog['IdNodoWorkflow'] = $IdNodoWorkflow;
		$datosLog['IdCircuito'] = $datos['IdCircuito'];
		$datosLog['AltaUsuario'] = $datosLog['UltimaModificacionUsuario'] = $_SESSION['usuariocod'];
		$datosLog['FechaAlta'] = $datos['FechaAlta'];
		$datosLog['IdArea'] = $datos['IdArea'];
		$datosLog['Accion'] = INSERTAR;
		$datosLog['IdEstado'] = $datos['IdEstado'];
		$datosLog['PosicionArriba'] = $datos['PosicionArriba'];
		$datosLog['PosicionIzquierda'] = $datos['PosicionIzquierda'];
		$datosLog['UltimaModificacionFecha'] = $datos['UltimaModificacionFecha'];
		
		$datosJson = json_encode(FuncionesPHPLocal::DecodificarUtf8($datosLog));
		$datosLog['DatosJson'] = $datosJson;
		if(!$oAuditorias->InsertarLog($datosLog,$codigoInsertado))
			return false;*/	
		
		return true;
	} 
	
	public function Eliminar($datos)
	{	

		if (!$this->_ValidarEliminar($datos,$datosRegistro))
			return false;
		
		/*$oAuditorias = new cAuditoriasCircuitosAreasEstados($this->conexion,$this->formato);
		$datosLog = $datosRegistro;
		$datosLog['Accion'] = ELIMINAR;
		$datosLog['UltimaModificacionFecha']= $datos['UltimaModificacionFecha']= date("Y-m-d H:i:s");
		$datosLog['UltimaModificacionUsuario'] = $datos['UltimaModificacionUsuario'] =$_SESSION['usuariocod'];
		if(!$oAuditorias->InsertarLog($datosLog,$codigoInsertado))
			return false;*/

		if (!parent::Eliminar($datos))
			return false;
		
		return true;
	} 
	
	
	
	private function _ValidarInsertar ($datos)
	{
		if (!$this->_ValidarDatosVacios($datos))
			return false;
		
		$oCircuitosAreasEstadosRoles = new cCircuitosAreasEstadosRoles($this->conexion,$this->formato);
		if(!$oCircuitosAreasEstadosRoles->BuscarxCodigo($datos, $resultado,$numfilas))
			return false;
		if ($numfilas!=1)
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error, el rol no se encuentra asociado al nodo del area de envio. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>""));
			return false;
		}	
		return true;
	}	
	
	
	private function _ValidarEliminar ($datos,&$datosRegistro)
	{
		if(!$this->BuscarxIdWorkflowxIdRol($datos,$resultado,$numfilas))
			return false;
		
		if ($numfilas!=1)
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error, debe ingresar de workflow y rol.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		$datosRegistro = $this->conexion->ObtenerSiguienteRegistro($resultado);
		
		return true;
	}	



	
	private function _ValidarDatosVacios($datos)
	{
		if ($datos['IdWorkflow']=="")
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error, debe ingresar una conexion. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>""));
			return false;
		}
		
		if ($datos['IdNodoWorkflow']=="")
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error, debe ingresar un nodo. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>""));
			return false;
		}
		
		if ($datos['IdRol']=="")
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error, debe ingresar un rol. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>""));
			return false;
		}

		return true;
	}


	
}//FIN CLASS
?>