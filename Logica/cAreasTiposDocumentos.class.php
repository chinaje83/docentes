<?php 
include(DIR_CLASES_DB."cAreasTiposDocumentos.db.php");

class cAreasTiposDocumentos extends cAreasTiposDocumentosdb
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
	
	public function BuscarxIdRegistroArea($datos,&$resultado,&$numfilas)
	{
		if (!parent::BuscarxIdRegistroArea($datos,$resultado,$numfilas))
			return false;
		return true;
	}
	
	public function BuscarxIdRegistroTipoDocumento($datos,&$resultado,&$numfilas)
	{
		if (!parent::BuscarxIdRegistroTipoDocumento($datos,$resultado,$numfilas))
			return false;
		return true;
	}


	public function BuscarxIdTipoDocumentoxIdArea($datos,&$resultado,&$numfilas)
	{
		if (!parent::BuscarxIdTipoDocumentoxIdArea($datos,$resultado,$numfilas))
			return false;
		return true;
	}


	public function BuscarTiposDocumentosRelacionadosAreaRaizVigentes($datos,&$resultado,&$numfilas)
	{
		if (!isset($datos['Vigencia']) && $datos['Vigencia']!="" && is_numeric($datos['Vigencia']))
			$datos['Vigencia'] = date("Y").str_pad(date("m"),2,"0")."01";

		if (!parent::BuscarTiposDocumentosRelacionadosAreaRaizVigentes($datos,$resultado,$numfilas))
			return false;
		return true;
	}
	
	
	public function BuscarTiposDocumentosRelacionadosAreaRaizVigentesActivos($datos,&$resultado,&$numfilas)
	{
		if (!isset($datos['Vigencia']) && $datos['Vigencia']!="" && is_numeric($datos['Vigencia']))
			$datos['Vigencia'] = date("Y").str_pad(date("m"),2,"0")."01";

		if (!parent::BuscarTiposDocumentosRelacionadosAreaRaizVigentesActivos($datos,$resultado,$numfilas))
			return false;
		return true;
	}



	public function BusquedaAvanzada($datos,&$resultado,&$numfilas)
	{
		$sparam=array(
			'xIdRegistroArea'=> 0,
			'IdRegistroArea'=> "",
			'xIdRegistroTipoDocumento'=> 0,
			'IdRegistroTipoDocumento'=> "",
			'Vigencia' => "", 
			'limit'=> '',
			'orderby'=> "IdRegistroArea DESC"
		);

		if(isset($datos['IdRegistroArea']) && $datos['IdRegistroArea']!="")
		{
			$sparam['IdRegistroArea']= $datos['IdRegistroArea'];
			$sparam['xIdRegistroArea']= 1;
		}
		if(isset($datos['IdRegistroTipoDocumento']) && $datos['IdRegistroTipoDocumento']!="")
		{
			$sparam['IdRegistroTipoDocumento']= $datos['IdRegistroTipoDocumento'];
			$sparam['xIdRegistroTipoDocumento']= 1;
		}
		
		if (!isset($datos['Anio']) || $datos['Anio']=="")
			$datos['Anio'] = date("Y");

		if (!isset($datos['Mes']) || $datos['Mes']=="")
			$datos['Mes'] = date("m");

		$sparam['Vigencia'] = $datos['Anio'].str_pad($datos['Mes'],2,"0")."01";


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
	
	
	public function ActualizarAreasVigentes($datos)
	{
		$oAreas = new cAreas($this->conexion,$this->formato);
		if(!$oAreas->BusquedaAvanzada($datos,$resultado,$numfilas))
			return false;
		if($numfilas == 0)
			return true;
		$dataBulkUpdate="";
		$oElastic = new cModifElastic(INDICEAREAS);
		while($fila = $this->conexion->ObtenerSiguienteRegistro($resultado))
		{	
			$datosArea = $fila;
			$fila['IdRegistroArea'] = $fila['IdRegistro'];
			$dataActualizar = $this->ArmarArrayElastic($fila,$datosArea);
			if ($dataActualizar===false)
				return false;
				
			$dataArrayActualizar['index'] = array();
			$dataArrayActualizar['index']["_id"] = $fila['uId'];
			$dataBulkUpdate.= json_encode($dataArrayActualizar,JSON_FORCE_OBJECT)."\n";
			$doc= $dataActualizar;
			$dataBulkUpdate .= json_encode($doc)."\n";
			
		}
		if(!$oElastic->ActualizarBulk($dataBulkUpdate))
			return false;
		
		return true;
	}



	public function Insertar($datos)
	{
		
		$oDocumentosTipos = new cDocumentosTipos($this->conexion,$this->formato);
		$Anio = $_SESSION['Anio'];
		$Mes = $_SESSION['Mes'];
		$string_estado_cat = ACTIVO;
		$arrcat = array();
		if(!$oDocumentosTipos->ArregloPadresVigente($datos['IdTipoDocumento'],$arrcat,$nivelarbol,$Anio,$Mes,$string_estado_cat))
			return false;
			
		
		if(count($arrcat)>0)
		{
		
			foreach($arrcat as $key=> $fila)
			{
				$datos['IdRegistroTipoDocumento'] = $fila['IdRegistro'];
				$datos['IdTipoDocumento'] = $fila['IdTipoDocumento'];
				
				
				if(!$this->BuscarxCodigo($datos,$resultado,$numfilas))
					return false;	
		
				if($numfilas==0)
				{
			
					if (!$this->_ValidarInsertar($datos))
						return false;
					
					$this->_SetearNull($datos);
					$datos['AltaFecha']=date("Y-m-d H:i:s");
					$datos['AltaUsuario']=$_SESSION['usuariocod'];
					$datos['UltimaModificacionUsuario']=$_SESSION['usuariocod'];
					$datos['UltimaModificacionFecha']=date("Y-m-d H:i:s");
					if (!parent::InsertarDB($datos))
						return false;
					
					$oAuditoriasAreasTiposDocumentos = new cAuditoriasAreasTiposDocumentos($this->conexion,$this->formato);
					$datos['IdRegistroArea'] = $datos['IdRegistroArea'];
					$datos['IdRegistroTipoDocumento'] = $datos['IdRegistroTipoDocumento'];
					$datos['IdTipoDocumento'] = $datos['IdTipoDocumento'];
					$datos['Accion'] = INSERTAR;
					$datos['AltaUsuario'] = $datos['AltaUsuario'];
					$datos['AltaFecha'] = $datos['AltaFecha'];
					if(!$oAuditoriasAreasTiposDocumentos->InsertarLog($datos,$codigoInsertadolog))
						return false;
					
					
					$datos['UltimaModificacionUsuarioNombre'] = $_SESSION['usuarionombre']." ".$_SESSION['usuarioapellido'];
					
				}
			}
		}
		return true;
	}
	
	
	
	public function InsertarArea($datos)
	{
		
		
			if (!$this->_ValidarInsertarArea($datos))
				return false;
			
			$this->_SetearNull($datos);
			$datos['AltaFecha']=date("Y-m-d H:i:s");
			$datos['AltaUsuario']=$_SESSION['usuariocod'];
			$datos['UltimaModificacionUsuario']=$_SESSION['usuariocod'];
			$datos['UltimaModificacionFecha']=date("Y-m-d H:i:s");
			if (!parent::InsertarDB($datos))
				return false;
			
			$oAuditoriasAreasTiposDocumentos = new cAuditoriasAreasTiposDocumentos($this->conexion,$this->formato);
			$datos['IdRegistroArea'] = $datos['IdRegistroArea'];
			$datos['IdRegistroTipoDocumento'] = $datos['IdRegistroTipoDocumento'];
			$datos['IdTipoDocumento'] = $datos['IdTipoDocumento'];
			$datos['Accion'] = INSERTAR;
			$datos['AltaUsuario'] = $datos['AltaUsuario'];
			$datos['AltaFecha'] = $datos['AltaFecha'];
			if(!$oAuditoriasAreasTiposDocumentos->InsertarLog($datos,$codigoInsertadolog))
				return false;
			
		/*	
			$datos['UltimaModificacionUsuarioNombre'] = $_SESSION['usuarionombre']." ".$_SESSION['usuarioapellido'];
			$oElastic = new cModifElastic(INDICEAREAS);
			$datosElastic = $this->ArmarArrayElastic($datos);
			if ($datosElastic===false)
				return false;
			if(!$oElastic->Actualizar($datos,$datosElastic))
				return false;
				*/

		return true;
	}
	
	
	



	public function Eliminar($datos)
	{
		
		if (!$this->_ValidarEliminar($datos,$datosRegistro))
			return false;
		
		
		$oAuditoriasAreasTiposDocumentos = new cAuditoriasAreasTiposDocumentos($this->conexion,$this->formato);
		$datosLog =$datosRegistro;
		$datosLog['Accion'] = ELIMINAR;
		if(!$oAuditoriasAreasTiposDocumentos->InsertarLog($datosLog,$codigoInsertadolog))
			return false;
		
		if (!parent::Eliminar($datos))
			return false;
		
		
		$datos['UltimaModificacionUsuario'] = $_SESSION['usuariocod'];
		$datos['UltimaModificacionUsuarioNombre'] = $_SESSION['usuarionombre']." ".$_SESSION['usuarioapellido'];
		$datos['UltimaModificacionFecha'] = date("Y-m-d H:i:s");
		/*$oElastic = new cModifElastic(INDICEAREAS);
		$datosElastic = $this->ArmarArrayElastic($datos);
		if ($datosElastic===false)
			return false;
		if(!$oElastic->Actualizar($datos,$datosElastic))
			return false;*/
			
		
		
		
		// Elimino En Cascada los documentos hijos
		$oDocumentosTipos = new cDocumentosTipos($this->conexion,$this->formato);
		$Anio = $_SESSION['Anio'];
		$Mes = $_SESSION['Mes'];
		$string_estado_cat = "";
		$arrcat = array();
		
		//si tiene hijos entonces llamo a la funcion recursivamente para armar el SubArbol dependiente
		if($oDocumentosTipos->TieneHijosVigente($datos["IdTipoDocumento"],$ok,$Anio,$Mes,$string_estado_cat) && $ok)
		{
			
			if(!$oDocumentosTipos->ArregloHijosVigente($datos['IdTipoDocumento'],$arrcat,$cantidadarreglo,$Anio,$Mes,$string_estado_cat))
				return false;
			
			if(count($arrcat)>0)
			{
				
				//busco los documentos existentes
				if(!$this->BuscarxIdRegistroArea($datos,$resultado,$numfilas))
					return false;
					
				$arrayexistentes = array();
				while($fila = $this->conexion->ObtenerSiguienteRegistro($resultado))
					$arrayexistentes[$fila['IdRegistroTipoDocumento']] =$fila['IdRegistroTipoDocumento'];
					
								
				foreach($arrcat as $key=> $fila)
				{
					if(array_key_exists($fila['IdRegistro'],$arrayexistentes))
					{
						$datos['IdRegistroTipoDocumento'] = $fila['IdRegistro'];
						$datos['IdTipoDocumento'] = $fila['IdTipoDocumento'];
						if(!$this->Eliminar($datos))
							return false;
					}
				}
			}
		}	

		return true;
	}
	
	
	public function EliminarxIdRegistroArea($datos)
	{
		if (!$this->BuscarxIdRegistroArea($datos,$resultado,$numfilas))
			return false;
		
		
		while($fila = $this->conexion->ObtenerSiguienteRegistro($resultado))
		{
			if (!parent::Eliminar($fila))
				return false;
		}
		return true;
	}
	
	public function ArmarArrayElastic(&$datos,$datosArea=array())
	{
		$oDocumentosTiposAsociados = new cDocumentosTiposAsociados($this->conexion,$this->formato);
		$datosBuscar['IdRegistroArea'] = $datos['IdRegistroArea'];
		$datosBuscar['IdRegistro'] = $datos['IdRegistroArea'];
		$datosBuscar['Anio'] = $_SESSION['Anio'];
		$datosBuscar['Mes'] = $_SESSION['Mes'];
		if(empty($datosArea))
		{
			$oAreas = new cAreas($this->conexion,$this->formato);
			if(!$oAreas->BuscarxIdRegistro($datosBuscar,$resultado,$numfilas))
				return false;
			if($numfilas == 0)
			{
				FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error,no existe el area.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
				return false;
			}
			$datosArea = $this->conexion->ObtenerSiguienteRegistro($resultado);
		}
		if(!$this->BusquedaAvanzada($datosBuscar,$resultado,$numfilas))
			return false;
		
			unset($datosBuscar['IdRegistro'],$datosBuscar['IdRegistroArea']);
		$TipoDocumento = array();
		while($filaTipo = $this->conexion->ObtenerSiguienteRegistro($resultado))
		{
			
			if(isset($filaTipo['Roles']) && $filaTipo['Roles']!="")
				$TipoDocumento[$filaTipo['IdTipoDocumento']]['Roles'] = explode(",",$filaTipo['Roles']);
			else
				$TipoDocumento[$filaTipo['IdTipoDocumento']]['Roles'] = array();
			
			$TipoDocumento[$filaTipo['IdTipoDocumento']]['Id'] = $filaTipo['IdTipoDocumento'];
			$TipoDocumento[$filaTipo['IdTipoDocumento']]['IdRegistro'] = $filaTipo['IdRegistroTipoDocumento'];
			$TipoDocumento[$filaTipo['IdTipoDocumento']]['Nombre'] = utf8_encode($filaTipo['Nombre']);
			$TipoDocumento[$filaTipo['IdTipoDocumento']]['NombreCorto'] = utf8_encode($filaTipo['NombreCorto']);
			$TipoDocumento[$filaTipo['IdTipoDocumento']]['Categoria']['Id'] = $filaTipo['IdCategoria'];
			$TipoDocumento[$filaTipo['IdTipoDocumento']]['Categoria']['Nombre'] = utf8_encode($filaTipo['CategoriaNombre']);
				
			
			if($filaTipo['IdTipoDocumentoPadre'] !="")
			{
				$TipoDocumento[$filaTipo['IdTipoDocumentoPadre']]['IdHijos'][] = $filaTipo['IdTipoDocumento'];
				$TipoDocumento[$filaTipo['IdTipoDocumentoPadre']]['IdRegistroHijos'][] = $filaTipo['IdRegistroTipoDocumento'];
			}
			
			$datosBuscar['IdRegistroTipoDocumento'] = $filaTipo['IdRegistroTipoDocumento'];
			$datosBuscar['Anio'] = $_SESSION['Anio'];
			$datosBuscar['Mes'] = $_SESSION['Mes'];
			if(!$oDocumentosTiposAsociados->BuscarxIdRegistroTipoDocumento($datosBuscar,$resultadoAsociados,$numfilasAsociados))
				die("BuscarxIdRegistroTipoDocumento");//continue;
			
			
			$TipoDocumento[$filaTipo['IdTipoDocumento']]['IdAsociados'] = array();
			$TipoDocumento[$filaTipo['IdTipoDocumento']]['IdRegistroAsociados'] = array();
			if($numfilasAsociados>0)
			{
				while($filaAsoc = $this->conexion->ObtenerSiguienteRegistro($resultadoAsociados))
				{
					$TipoDocumento[$filaTipo['IdTipoDocumento']]['IdAsociados'][] = $filaAsoc['IdTipoDocumento'];
					$TipoDocumento[$filaTipo['IdTipoDocumento']]['IdRegistroAsociados'][] = $filaAsoc['IdRegistroTipoDocumentoAsociado'];
				}
			}
			
		}
		
		$datos['IdRegistro'] = $datosArea['IdRegistro'];	
		$datos['uId'] = $_SESSION['IdCliente']."-".$datos['IdRegistro'];
		$datos['IdCliente'] = $_SESSION['IdCliente'];
		$jsonData['uId'] = $datos['uId'];
		$jsonData['Cliente']['Id'] = $datos['IdCliente'];
		$jsonData['Cliente']['Nombre'] = utf8_encode(CLIENTENOMBRE);
		
		$jsonData['Proyecto']['Id'] = PROYECTO;
		$jsonData['Proyecto']['Nombre'] = utf8_encode("");
		
		$jsonData['Area']['Id'] = $datosArea['IdArea'];
		$jsonData['Area']['Nombre'] = utf8_encode($datosArea['Nombre']);
		
		$jsonData['TipoDocumento'] = array_values($TipoDocumento);
		
		$jsonData['Vigencia']['Desde'] = $datosArea['VigenciaDesde'];
		if($datosArea['VigenciaHasta']!="")
			$jsonData['Vigencia']['Hasta'] = $datosArea['VigenciaHasta'];
		else
			$jsonData['Vigencia']['Hasta'] = NULLDATE;
		
		$jsonData['UltimaModificacion']['Fecha'] = $datos['UltimaModificacionFecha'];
		$jsonData['UltimaModificacion']['Usuario']['Id'] = $datos['UltimaModificacionUsuario'];
		$jsonData['UltimaModificacion']['Usuario']['Nombre'] = utf8_encode($datos['UltimaModificacionUsuarioNombre']);
		//file_put_contents(PUBLICA."test.txt",print_r($TipoDocumento,true));
		//file_put_contents(PUBLICA."test2.txt",print_r($jsonData,true));
		return $jsonData;
	}
	
	
	public function ActivarDocumentoVisualiza($datos)
	{
		$datosmodif['IdRegistroArea'] = $datos['IdRegistroArea'];
		$datosmodif['IdRegistroTipoDocumento'] = $datos['IdRegistroTipoDocumento'];
		$datosmodif['DocumentoVisualiza'] = 1;
		if (!$this->ModificarDocumentoVisualiza($datosmodif))
			return false;
		return true;
	}



	public function DesActivarDocumentoVisualiza($datos)
	{
		$datosmodif['IdRegistroArea'] = $datos['IdRegistroArea'];
		$datosmodif['IdRegistroTipoDocumento'] = $datos['IdRegistroTipoDocumento'];
		$datosmodif['DocumentoVisualiza'] = 0;
		if (!$this->ModificarDocumentoVisualiza($datosmodif))
			return false;
		return true;
	}
	
	
	public function ModificarDocumentoVisualiza($datos)
	{
		
		if (!$this->_ValidarEliminar($datos,$datosRegistro))
			return false;
		
		
		$datos['UltimaModificacionUsuario'] = $_SESSION['usuariocod'];
		$datos['UltimaModificacionFecha'] = date("Y-m-d H:i:s");
		
		if (!parent::ModificarDocumentoVisualiza($datos))
			return false;
			
		$datosRegistro['DocumentoVisualiza'] = $datos['DocumentoVisualiza'];
		$oAuditoriasAreasTiposDocumentos = new cAuditoriasAreasTiposDocumentos($this->conexion,$this->formato);
		$datosLog =$datosRegistro;
		$datosLog['Accion'] = MODIFICACION;
		if(!$oAuditoriasAreasTiposDocumentos->InsertarLog($datosLog,$codigoInsertadolog))
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
	
	
	private function _ValidarInsertarArea($datos)
	{
		if (!$this->_ValidarDatosVacios($datos))
			return false;
			
		if (!$this->BuscarxCodigo($datos,$resultado,$numfilas))
			return false;

		if ($numfilas==1)
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error, el area ya se encuentra asignada.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
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

		if (!isset($datos['IdRegistroArea']) || $datos['IdRegistroArea']=="")
			$datos['IdRegistroArea']="NULL";
			
		if (!isset($datos['IdRegistroTipoDocumento']) || $datos['IdRegistroTipoDocumento']=="")
			$datos['IdRegistroTipoDocumento']="NULL";	
		
		if (!isset($datos['IdTipoDocumento']) || $datos['IdTipoDocumento']=="")
			$datos['IdTipoDocumento']="NULL";
			
		if (!isset($datos['DocumentoVisualiza']) || $datos['DocumentoVisualiza']!="1")
			$datos['DocumentoVisualiza']="0";

		if (!isset($datos['AltaFecha']) || $datos['AltaFecha']=="")
			$datos['AltaFecha']="NULL";

		if (!isset($datos['AltaApp']) || $datos['AltaApp']=="")
			$datos['AltaApp']="NULL";

		if (!isset($datos['AltaUsuario']) || $datos['AltaUsuario']=="")
			$datos['AltaUsuario']="NULL";

		if (!isset($datos['UltimaModificacionFecha']) || $datos['UltimaModificacionFecha']=="")
			$datos['UltimaModificacionFecha']="NULL";
		return true;
	}



	private function _ValidarDatosVacios($datos)
	{

		if (!isset($datos['IdRegistroArea']) || $datos['IdRegistroArea']=="")
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error, debe ingreasar un area",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		if (!FuncionesPHPLocal::ValidarContenido($this->conexion,$datos['IdRegistroArea'],"NumericoEntero"))
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error debe ingresar un campo numérico.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		
		if (!isset($datos['IdRegistroTipoDocumento']) || $datos['IdRegistroTipoDocumento']=="")
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error, debe ingreasar un tipo de documento",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		if (!FuncionesPHPLocal::ValidarContenido($this->conexion,$datos['IdRegistroTipoDocumento'],"NumericoEntero"))
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error debe ingresar un campo numérico.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		

		if (!isset($datos['IdTipoDocumento']) || $datos['IdTipoDocumento']=="")
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error, debe ingreasar un tipo de documento",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		if (!FuncionesPHPLocal::ValidarContenido($this->conexion,$datos['IdTipoDocumento'],"NumericoEntero"))
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error debe ingresar un campo numérico.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		
		return true;
	}





}
?>