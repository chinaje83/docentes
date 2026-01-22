<?php  
include(DIR_CLASES_DB."cCircuitosAreasEstados.db.php");

class cCircuitosAreasEstados extends cCircuitosAreasEstadosdb	
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
	
	public function BuscarAreasEstadosxCircuito(&$resultado,&$numfilas)
	{
		$datos['IdCircuito']=$this->IdCircuito;
		if (!parent::BuscarAreasEstadosxCircuitoBd ($datos,$resultado,$numfilas))
			return false;	

		return true;			
	}
	
	public function BuscarAreasEstadosNodoInicialxCircuito(&$resultado,&$numfilas)
	{
		$datos['IdCircuito']=$this->IdCircuito;
		if (!parent::BuscarAreasEstadosNodoInicialxCircuitoBd ($datos,$resultado,$numfilas))
			return false;	

		return true;			
	}
	
	public function BuscarAreasEstadosVigentesxCircuito(&$resultado,&$numfilas)
	{
		$datos['IdCircuito']=$this->IdCircuito;
		$datos['Vigencia'] = $_SESSION['Anio'].str_pad($_SESSION['Mes'],2,0)."01";
		if (!parent::BuscarAreasEstadosVigentesxCircuitoBd ($datos,$resultado,$numfilas))
			return false;	

		return true;			
	}
	
	public function BuscarEstadosxCircuito(&$resultado,&$numfilas)
	{
		$datos['IdCircuito']=$this->IdCircuito;
		if (!parent::BuscarEstadosxCircuitoBd ($datos,$resultado,$numfilas))
			return false;	

		return true;			
	}
	
	public function BuscarxAreaxEstadoxCircuito($datos, &$resultado,&$numfilas)
	{
		$datos['IdCircuito']=$this->IdCircuito;
		if (!parent::BuscarxAreaxEstadoxCircuito ($datos,$resultado,$numfilas))
			return false;
		return true;			
	}
	
	public function BuscarxAreaxEstadoxCircuitoNodoGeneral($datos, &$resultado,&$numfilas)
	{
		$datos['IdCircuito']=$this->IdCircuito;
		if (!parent::BuscarxAreaxEstadoxCircuitoNodoGeneral ($datos,$resultado,$numfilas))
			return false;
		return true;			
	}
	
	public function BuscarxAreaxEstadoxCircuitoxEstado($datos, &$resultado,&$numfilas)
	{
		$datos['IdCircuito']=$this->IdCircuito;
		if (!parent::BuscarxAreaxEstadoxCircuitoxEstado ($datos,$resultado,$numfilas))
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
		if ($datos['AreasCompletas']=="true")
			$datos['NodoGeneral'] = 1;
			
			
		if($datos['NodoInicial'] == 1)
		{
			
			if(!$this->BuscarAreasEstadosNodoInicialxCircuito($resultado,$numfilas))
				return false;
			if($numfilas>0)
			{
					FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error, ya existe otra area que inicia el documento.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
					return false;
			}	
							
		}
		
			

		if (!parent::InsertarBD($datos,$IdNodoWorkflow))
			return false;
			
		$oAuditorias = new cAuditoriasCircuitosAreasEstados($this->conexion,$this->formato);

		$datosLog['IdNodoWorkflow'] = $IdNodoWorkflow;
		$datosLog['IdCircuito'] = $datos['IdCircuito'];
		$datosLog['AltaUsuario'] = $datosLog['UltimaModificacionUsuario'] = $_SESSION['usuariocod'];
		$datosLog['FechaAlta'] = $datos['FechaAlta'];
		$datosLog['NodoInicial'] = $datos['NodoInicial'];
		$datosLog['Accion'] = INSERTAR;
		$datosLog['IdEstado'] = $datos['IdEstado'];
		$datosLog['NodoInicial'] = $datos['NodoInicial'];
		$datosLog['NodoGeneral'] = $datos['NodoGeneral'];
		$datosLog['PosicionArriba'] = $datos['PosicionArriba'];
		$datosLog['PosicionIzquierda'] = $datos['PosicionIzquierda'];
		$datosLog['UltimaModificacionFecha'] = $datos['UltimaModificacionFecha'];
		
		$datosJson = json_encode(FuncionesPHPLocal::ConvertiraUtf8($datosLog));
		$datosLog['DatosJson'] = $datosJson;
		if(!$oAuditorias->InsertarLog($datosLog,$codigoInsertado))
			return false;
		
		
		$oCircuitosAreasEstadosAreas = new cCircuitosAreasEstadosAreas($this->conexion,$this->formato);	
		$ArrayAreas = explode(",",$datos['IdArea']);
		$datosInsertar['IdCircuito'] = $datos['IdCircuito'];
		$datosInsertar['IdNodoWorkflow'] = $IdNodoWorkflow;

		if ($datos['AreasCompletas']=="false")
		{
			foreach ($ArrayAreas as $IdArea)
			{
				$datosInsertar['IdArea'] = $IdArea;
				if(!$oCircuitosAreasEstadosAreas->Insertar($datosInsertar,$codigoInsertadoNodo))
					return false;
				
			}
		}
		return true;
	}	
	
	public function Eliminar ($datos)
	{
		
		if (!$this->_ValidarEliminar($datos,$datosRegistro))
			return false;
		
		
		$oAuditorias = new cAuditoriasCircuitosAreasEstados($this->conexion,$this->formato);
		$datosLog = $datosRegistro;
		$datosLog['Accion'] = ELIMINAR;
		$datosLog['UltimaModificacionFecha']=date("Y-m-d H:i:s");
		$datosLog['UltimaModificacionUsuario'] = $_SESSION['usuariocod'];
		if(!$oAuditorias->InsertarLog($datosLog,$codigoInsertado))
			return false;
			
			
		
		$oCircuitosWorkflow = new cCircuitosWorkflow($this->conexion,$this->formato);
		$datos['IdNodoWorkflowArea'] = $datos['IdNodoWorkflow'];
		if(!$oCircuitosWorkflow->BuscarConexionesxWorkflowAreaCod($datos,$resultado,$numfilas))
			return false;
			
		if($numfilas>0)
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error, debe eliminar las conexiones.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
			
		}	
		
		
		$oCircuitosAreasEstadosRolesAcciones = new cCircuitosAreasEstadosRolesAcciones($this->conexion,$this->formato);
		if (!$oCircuitosAreasEstadosRolesAcciones->EliminarxIdNodoWorkflow($datos))
			return false;
		
		$oCircuitosAreasEstadosRoles = new cCircuitosAreasEstadosRoles($this->conexion,$this->formato);
		if (!$oCircuitosAreasEstadosRoles->EliminarxIdNodoWorkflow($datos))
			return false;
		
		$oCircuitosAreasEstadosAreas = new cCircuitosAreasEstadosAreas($this->conexion,$this->formato);
		if (!$oCircuitosAreasEstadosAreas->Eliminar($datos))
			return false;
			
		

		if (!parent::Eliminar($datos))
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

		$oAuditorias = new cAuditoriasCircuitosAreasEstados($this->conexion,$this->formato);
		$datosLog = $datosRegistro;
		$datosLog['Accion'] = MODIFICACION;
		$datosLog['PosicionArriba'] = $datos['PosicionArriba'];
		$datosLog['PosicionIzquierda'] = $datos['PosicionIzquierda'];
		$datosLog['UltimaModificacionFecha'] = $datos['UltimaModificacionFecha'];
		$datosLog['UltimaModificacionUsuario'] = $_SESSION['usuariocod'];
		if(!$oAuditorias->InsertarLog($datosLog,$codigoInsertado))
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
		if ($datos['AreasCompletas']=="true")
			$datos['NodoGeneral'] = 1;
			
			
		
		if($datos['NodoInicial'] == 1)
		{
			
			if(!$this->BuscarAreasEstadosNodoInicialxCircuito($resultado,$numfilas))
				return false;
			if($numfilas>0)
			{
				if($numfilas>1)
				{
					FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error, ya existe otra area que inicia el documento.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
					return false;
					
				}
				else
				{
					$fila = $this->conexion->ObtenerSiguienteRegistro($resultado);
					if($fila['IdNodoWorkflow']!=$datos['IdNodoWorkflow'])
					{
						FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error, ya existe otra area que inicia el documento.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
						return false;
						
					}
				}
			}	
							
		}


		$oAuditorias = new cAuditoriasCircuitosAreasEstados($this->conexion,$this->formato);
		$datosLog = $datosRegistro;
		if ($datosRegistro['NodoGeneral']!=$datos['NodoGeneral'])
		{
			if(!parent::ModificarNodoGeneral($datos))
				return false;
			
			$oCircuitosAreasEstadosAreas = new cCircuitosAreasEstadosAreas($this->conexion,$this->formato);
			if(!$oCircuitosAreasEstadosAreas->Eliminar($datos))
				return false;
			
			$datosLog['NodoGeneral'] = $datos['NodoGeneral'];
		}			
		if ($datosRegistro['NodoInicial']!=$datos['NodoInicial'])
		{
			if(!parent::ModificarNodoInicial($datos))
				return false;
			
			$datosLog['NodoInicial'] = $datos['NodoInicial'];
		}
				
		if ($datosRegistro['NodoGeneral']!=$datos['NodoGeneral'] || $datosRegistro['NodoInicial']!=$datos['NodoInicial'])
		{		
			$datosLog['Accion'] = MODIFICACION;
			$datosLog['NodoGeneral'] = $datos['NodoGeneral'];
			$datosLog['UltimaModificacionFecha'] = $datos['UltimaModificacionFecha'];
			$datosLog['UltimaModificacionUsuario'] = $_SESSION['usuariocod'];
			if(!$oAuditorias->InsertarLog($datosLog,$codigoInsertado))
				return false;
		}
		return true;
	}


	public function ModificarNodoGeneral ($datos)
	{
		if (!$this->_ValidarModificar($datos,$datosRegistro))
			return false;

		$datos['UltimaModificacionFecha'] = date("Y/m/d H:i:s");
		if (!parent::ModificarNodoGeneral($datos))
			return false;

		$oAuditorias = new cAuditoriasCircuitosAreasEstados($this->conexion,$this->formato);
		$datosLog = $datosRegistro;
		$datosLog['Accion'] = MODIFICACION;
		$datosLog['NodoGeneral'] = $datos['NodoGeneral'];
		$datosLog['UltimaModificacionFecha'] = $datos['UltimaModificacionFecha'];
		$datosLog['UltimaModificacionUsuario'] = $_SESSION['usuariocod'];
		if(!$oAuditorias->InsertarLog($datosLog,$codigoInsertado))
			return false;
		
		return true;
	}	
	

	public function ModificarNodoInicial ($datos)
	{
		if (!$this->_ValidarModificar($datos,$datosRegistro))
			return false;

		$datos['UltimaModificacionFecha'] = date("Y/m/d H:i:s");
		if (!parent::ModificarNodoInicial($datos))
			return false;

		$oAuditorias = new cAuditoriasCircuitosAreasEstados($this->conexion,$this->formato);
		$datosLog = $datosRegistro;
		$datosLog['Accion'] = MODIFICACION;
		$datosLog['NodoInicial'] = $datos['NodoInicial'];
		$datosLog['UltimaModificacionFecha'] = $datos['UltimaModificacionFecha'];
		$datosLog['UltimaModificacionUsuario'] = $_SESSION['usuariocod'];
		if(!$oAuditorias->InsertarLog($datosLog,$codigoInsertado))
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

		/*
		if(!$this->BuscarxAreaxEstadoxCircuitoNodoGeneral($datos,$resultado,$numfilas))
			return false;
		if($numfilas!=0)
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error, existe un nodo con todas las areas en dicho estado.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}*/
		
		if (isset($datos['AreasCompletas']) && $datos['AreasCompletas']=="false")
		{
			if (!isset ($datos['IdArea']) || ($datos['IdArea']==""))
			{
				FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error, debe ingresar un area.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
				return false;
			}
			
			$oAreas = new cAreas($this->conexion,$this->formato);
			$ArrayAreas = explode(",",$datos['IdArea']);
			
			foreach($ArrayAreas as $IdArea)
			{
				if (!is_numeric($IdArea) || $IdArea=="")
				{
					FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error, debe ingresar un area valida.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
					return false;
				}
			}
			
			if(!$oAreas->BuscarxCodigosActivos($datos,$resultado,$numfilas))
				return false;
				
			if ($numfilas!=count($ArrayAreas))
			{
				FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error, debe seleccionar todas las areas validas2.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
				return false;
			}
		
			/*
			if(!$this->BuscarxAreaxEstadoxCircuito($datos,$resultadoCircuito,$numfilasCircuito))
				return false;
			
			if ($numfilasCircuito>0)
			{
				FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error, ya se encuentra un area y estado dentro del circuito.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
				return false;
			}*/
			
			$datosNodo['NombreArea'] = "";		
			$datosNodo['NombreAreaSuperior'] = "";	
			$ArrayAreas=array();
			while ($datosArea = $this->conexion->ObtenerSiguienteRegistro($resultado))
			{		
				$ArrayAreas[]=$datosArea['Nombre'];		
			}
			$datosNodo['NombreArea'] = implode('<div class="clearboth"></div>', $ArrayAreas);
		}else
		{
			/*
			if(!$this->BuscarxAreaxEstadoxCircuitoxEstado($datos,$resultado,$numfilas))
				return false;
			if($numfilas!=0)
			{
				FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error, existe un nodo con dicho estado, debe eliminarlo o modificarlo para seleccionar todas las areas.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
				return false;
			}		
			*/
			
			$datosNodo['NombreArea'] = "Todas las areas";		
			$datosNodo['NombreAreaSuperior'] = "";	
		}

		$oCircuitosEstados = new cCircuitosEstados($this->conexion,$this->formato);
		if(!$oCircuitosEstados->BuscarxCodigo($datos,$resultado,$numfilas))
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
	

	
}//FIN CLASS
?>