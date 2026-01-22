<?php 
include(DIR_CLASES_DB."cCircuitosAreasEstadosRolesAcciones.db.php");

class cCircuitosAreasEstadosRolesAcciones extends cCircuitosAreasEstadosRolesAccionesdb
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
			'xIdAccion'=> 0,
			'IdAccion'=> "",
			'limit'=> '',
			'orderby'=> "IdNodoWorkflow DESC"
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
		if(isset($datos['IdAccion']) && $datos['IdAccion']!="")
		{
			$sparam['IdAccion']= $datos['IdAccion'];
			$sparam['xIdAccion']= 1;
		}


		if(isset($datos['orderby']) && $datos['orderby']!="")
			$sparam['orderby']= $datos['orderby'];

		if(isset($datos['limit']) && $datos['limit']!="")
			$sparam['limit']= $datos['limit'];

		if (!parent::BusquedaAvanzada($sparam,$resultado,$numfilas))
			return false;
		return true;
	}



	public function BuscarAuditoriaRapida($datos,&$resultado,&$numfilas)
	{
		if (!parent::BuscarAuditoriaRapida($datos,$resultado,$numfilas))
			return false;
		return true;
	}



	public function Insertar($datos)
	{
		if (!$this->_ValidarInsertar($datos))
			return false;

		$this->_SetearNull($datos);
		$datos['AltaFecha']=date("Y-m-d H:i:s");
		$datos['AltaUsuario']=$_SESSION['usuariocod'];
		$datos['UltimaModificacionUsuario']=$_SESSION['usuariocod'];
		$datos['UltimaModificacionFecha']=date("Y-m-d H:i:s");
		if (!parent::Insertar($datos))
			return false;
			

		$oAuditoriasCircuitosAreasEstadosRolesAcciones = new cAuditoriasCircuitosAreasEstadosRolesAcciones($this->conexion,$this->formato);
		$datos['Accion'] = INSERTAR;
		$datos['AltaUsuario'] = $datos['AltaUsuario'];
		$datos['AltaFecha'] = $datos['AltaFecha'];
		if(!$oAuditoriasCircuitosAreasEstadosRolesAcciones->InsertarLog($datos,$codigoInsertadolog))
			return false;

		return true;
	}



	public function Modificar($datos)
	{
		if (!$this->_ValidarModificar($datos,$datosRegistro))
			return false;

		$datos['UltimaModificacionUsuario']= $datosRegistro['UltimaModificacionUsuario'] =$_SESSION['usuariocod'];
		$datos['UltimaModificacionFecha']= $datosRegistro['UltimaModificacionFecha'] = date("Y-m-d H:i:s");
		$this->_SetearNull($datos);
		if (!parent::Modificar($datos))
			return false;

		$oAuditoriasCircuitosAreasEstadosRolesAcciones = new cAuditoriasCircuitosAreasEstadosRolesAcciones($this->conexion,$this->formato);
		$datosRegistro['Accion'] = MODIFICACION;
		if(!$oAuditoriasCircuitosAreasEstadosRolesAcciones->InsertarLog($datosRegistro,$codigoInsertadolog))
			return false;

		return true;
	}



	public function Eliminar($datos)
	{
		if (!$this->_ValidarEliminar($datos,$datosRegistro))
			return false;

		$oAuditoriasCircuitosAreasEstadosRolesAcciones = new cAuditoriasCircuitosAreasEstadosRolesAcciones($this->conexion,$this->formato);
		$datosLog =$datosRegistro;
		$datosLog['Accion'] = ELIMINAR;
		if(!$oAuditoriasCircuitosAreasEstadosRolesAcciones->InsertarLog($datosLog,$codigoInsertadolog))
			return false;

		if (!parent::Eliminar($datos))
			return false;

		return true;
	}
	
	
	public function EliminarxIdNodoWorkflowxIdRol($datos)
	{
		
		$datosbuscar['IdNodoWorkflow'] = $datos['IdNodoWorkflow'];
		$datosbuscar['IdRol'] = $datos['IdRol'];
		if(!$this->BusquedaAvanzada($datosbuscar,$resultado,$numfilas))
			return false;
		
		
		while($fila = $this->conexion->ObtenerSiguienteRegistro($resultado))
		{
			
			if (!$this->_ValidarEliminar($fila,$datosRegistro))
				return false;
	
			
			$oAuditoriasCircuitosAreasEstadosRolesAcciones = new cAuditoriasCircuitosAreasEstadosRolesAcciones($this->conexion,$this->formato);
			$datosLog =$datosRegistro;
			$datosLog['Accion'] = ELIMINAR;
			if(!$oAuditoriasCircuitosAreasEstadosRolesAcciones->InsertarLog($datosLog,$codigoInsertadolog))
				return false;
				
			
	
			if (!parent::Eliminar($fila))
				return false;
				
		}

		return true;
	}
	
	
	
	public function EliminarxIdNodoWorkflow($datos)
	{
		
		$datosbuscar['IdNodoWorkflow'] = $datos['IdNodoWorkflow'];
		if(!$this->BusquedaAvanzada($datosbuscar,$resultado,$numfilas))
			return false;
		
		
		while($fila = $this->conexion->ObtenerSiguienteRegistro($resultado))
		{
			
			if (!$this->_ValidarEliminar($fila,$datosRegistro))
				return false;
	
			
			$oAuditoriasCircuitosAreasEstadosRolesAcciones = new cAuditoriasCircuitosAreasEstadosRolesAcciones($this->conexion,$this->formato);
			$datosLog =$datosRegistro;
			$datosLog['Accion'] = ELIMINAR;
			if(!$oAuditoriasCircuitosAreasEstadosRolesAcciones->InsertarLog($datosLog,$codigoInsertadolog))
				return false;
				
			
	
			if (!parent::Eliminar($fila))
				return false;
				
		}

		return true;
	}
	
	
	public function ActualizarRoles($datos)
	{
		$datosbuscar['IdNodoWorkflow'] = $datos['IdNodoWorkflow'];
		$datosbuscar['IdRol'] = $datos['IdRol'];
		if (!$this->BusquedaAvanzada($datosbuscar,$resultadoAcciones,$numfilasAcciones))
			return false;	

		$arregloinsertados = array();
		while($fila = $this->conexion->ObtenerSiguienteRegistro($resultadoAcciones))
			$arregloinsertados[] = $fila['IdAccion'];
		
		$datosdevueltos = array();
		$datosObligatorio = array();
		if(isset($datos['IdAccion'])){
			$datosdevueltos = $datos['IdAccion'];
			$datosObligatorio = $datos['AccionObligatorio'];
		}
		
		$arregloeliminar = array_diff($arregloinsertados,$datosdevueltos);
		$arregloinsertar = array_diff($datosdevueltos,$arregloinsertados);	
		
		$datosModificar['IdNodoWorkflow'] = $datoseliminar['IdNodoWorkflow'] = $datosinsertar['IdNodoWorkflow'] = $datos['IdNodoWorkflow'];
		$datosModificar['IdRol'] = $datoseliminar['IdRol'] = $datosinsertar['IdRol'] = $datos['IdRol'];

		if(count($arregloinsertar)>0)
		{
			foreach ($arregloinsertar as $IdAccion)
			{
				$datosinsertar['IdAccion'] = $IdAccion;
				if(!$this->Insertar($datosinsertar))
					return false;
					
			}
		}
		
		if(count($arregloeliminar)>0)
		{
			
			foreach ($arregloeliminar as $IdAccion)
			{
					$datoseliminar['IdAccion'] = $IdAccion;
					if(!$this->Eliminar($datoseliminar))
						return false;
			}
		}
		
		if(count($datosObligatorio))
		{
			foreach($datosObligatorio as $IdAccion=>$AccionObligatorio)
			{
				if(!in_array($IdAccion,$datosdevueltos))
					continue;
				$datosModificar['IdAccion'] = $IdAccion;
				$datosModificar['AccionObligatorio'] = $AccionObligatorio;
				if(!$this->ModificarAccionObligatorio($datosModificar))
					return false;
			}
		}
		
		
		return true;	
	}
	
	
	public function ModificarAccionObligatorio($datos)
	{
		if (!parent::ModificarAccionObligatorio($datos))
			return false;
		
		if (!$this->_ValidarEliminar($datos,$datosRegistro))
				return false;
		
		$oAuditoriasCircuitosAreasEstadosRolesAcciones = new cAuditoriasCircuitosAreasEstadosRolesAcciones($this->conexion,$this->formato);
		$datosRegistro['Accion'] = MODIFICACION;
		if(!$oAuditoriasCircuitosAreasEstadosRolesAcciones->InsertarLog($datosRegistro,$codigoInsertadolog))
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

		return true;
	}



	private function _ValidarModificar($datos,&$datosRegistro)
	{
		if (!$this->BuscarxCodigo($datos,$resultado,$numfilas))
			return false;

		if ($numfilas!=1)
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error debe ingresar un código valido1.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
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
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error debe ingresar un código valido2.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		$datosRegistro = $this->conexion->ObtenerSiguienteRegistro($resultado);
		return true;
	}



	private function _SetearNull(&$datos)
	{
		if (!isset($datos['IdNodoWorkflow']) || $datos['IdNodoWorkflow']=="")
			$datos['IdNodoWorkflow']="NULL";

		if (!isset($datos['IdRol']) || $datos['IdRol']=="")
			$datos['IdRol']="NULL";

		if (!isset($datos['IdAccion']) || $datos['IdAccion']=="")
			$datos['IdAccion']="NULL";

		
		return true;
	}



	private function _ValidarDatosVacios($datos)
	{


		if (!isset($datos['IdRol']) || $datos['IdRol']=="")
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Debe ingresar un rol",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		if (!isset($datos['IdAccion']) || $datos['IdAccion']=="")
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Debe ingresar una acción",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		return true;
	}





}
?>