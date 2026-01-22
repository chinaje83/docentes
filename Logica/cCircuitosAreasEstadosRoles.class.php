<?php  
include(DIR_CLASES_DB."cCircuitosAreasEstadosRoles.db.php");

class cCircuitosAreasEstadosRoles extends cCircuitosAreasEstadosRolesdb	
{
	protected $conexion;
	protected $formato;
	protected $IdNodoWorkflow;
	
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

// Trae las encuestas

// Parámetros de Entrada:
//	Sin parametros de entrada

// Retorna:
//		numfilas,resultado: cantidad de filas y query de resultado
//		la función retorna true o false si se pudo ejecutar con éxito o no
	
	public function BuscarxCodigo($datos,&$resultado,&$numfilas)
	{
		if (!parent::BuscarxCodigo ($datos,$resultado,$numfilas))
			return false;
		return true;			
	}
	
	public function BuscarxIdNodoWorkflow($datos,&$resultado,&$numfilas)
	{
		if (!parent::BuscarxIdNodoWorkflow ($datos,$resultado,$numfilas))
			return false;
		return true;			
	}
	
	
	
	
	
	public function ActualizarRoles($datos)
	{
		
		if (!$this->BuscarxIdNodoWorkflow($datos,$resultadoRoles,$numfilasRoles))
			return false;	

		$arregloinsertados = array();
		while($fila = $this->conexion->ObtenerSiguienteRegistro($resultadoRoles))
			$arregloinsertados[$fila['IdRol']] = $fila['IdRol'];
		
		$datosdevueltos = array();
		if(isset($datos['IdRol']))
			$datosdevueltos = $datos['IdRol'];
				
		$arregloeliminar = array_diff($arregloinsertados,$datosdevueltos);
		$arregloinsertar = array_diff($datosdevueltos,$arregloinsertados);	
		
		$oCircuitosAreasEstadosRolesAcciones = new cCircuitosAreasEstadosRolesAcciones($this->conexion,$this->formato);		

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
		//print_r($arregloeliminar);die;
		if(count($arregloeliminar)>0)
		{
			$oCircuitosWorkflowRoles = new cCircuitosWorkflowRoles($this->conexion,$this->formato);
			
			foreach ($arregloeliminar as $IdRol)
			{
					
					$datoseliminar['IdRol'] = $IdRol;
					if(!$oCircuitosAreasEstadosRolesAcciones->EliminarxIdNodoWorkflowxIdRol($datoseliminar))
							return false;
					
					if(!$oCircuitosWorkflowRoles->BuscarxIdNodoWorkflowxIdRol($datoseliminar,$resultadoRol,$numfilasRol))
						return false;
						
					if($numfilasRol>0)
					{	
						FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error, debe eliminar el rol en los envios hacia otras areas.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
						return false;
					}
					
					if(!$this->Eliminar($datoseliminar))
						return false;
						
			}
		}
		
		if(isset($datos['IdRol']) && count($datos['IdRol'])>0)
		{
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
				
				if(!$oCircuitosAreasEstadosRolesAcciones->ActualizarRoles($datosactualizar))
					return false;
					
			}
			
		}

		
		return true;	
	}

	
	
	public function Insertar ($datos)
	{
		
		$datos['AltaFecha']=date("Y-m-d H:i:s");
		$datos['AltaUsuario'] = $_SESSION['usuariocod'];
		$datos['UltimaModificacionFecha']=date("Y-m-d H:i:s");
		$datos['UltimaModificacionUsuario'] = $_SESSION['usuariocod'];
		
		
		if (!$this->_ValidarInsertar($datos))
			return false;

		if (!parent::InsertarBD($datos))
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
	
	public function Eliminar ($datos)
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
	
	
	
	public function EliminarxIdNodoWorkflow ($datos)
	{
		if(!$this->BuscarxIdNodoWorkflow($datos,$resultado,$numfilas))
			return false;
		
		while($fila = $this->conexion->ObtenerSiguienteRegistro($resultado))
		{
			$fila['IdCircuito'] = $datos['IdCircuito'];
			if(!$this->Eliminar($fila))
				return false;	
			
		}
		return true;
	}	
	
	
	
	
	
	private function _ValidarInsertar ($datos)
	{
		if (!$this->_ValidarDatosVacios($datos))
			return false;
		
		
		
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
		

		return true;
	}	




	private function _ValidarDatosVacios($datos)
	{
		
		if (!isset ($datos['IdNodoWorkflow']) || ($datos['IdNodoWorkflow']==""))
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error, debe ingresar un area.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		
		if (!isset ($datos['IdRol']) || ($datos['IdRol']===""))
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error, debe ingresar un rol.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		

		return true;		
		
	}
	

	
}//FIN CLASS
?>