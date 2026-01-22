<?php 
include(DIR_CLASES_DB."cAreasTiposDocumentosRoles.db.php");

class cAreasTiposDocumentosRoles extends cAreasTiposDocumentosRolesdb
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
		if(!isset($datos['IdRegistroArea']) || $datos['IdRegistroArea']=="")
			$datos['IdRegistroArea'] ="-1";
		
		if (!parent::BuscarxIdRegistroArea($datos,$resultado,$numfilas))
			return false;
		return true;
	}
	
	public function BuscarxIdRegistroTipoDocumentoxIdRegistroArea($datos,&$resultado,&$numfilas)
	{
		if(!isset($datos['IdRegistroArea']) || $datos['IdRegistroArea']=="")
			$datos['IdRegistroArea'] ="-1";
		
		if (!parent::BuscarxIdRegistroTipoDocumentoxIdRegistroArea($datos,$resultado,$numfilas))
			return false;
		return true;
	}
	
	
	public function BuscarRolesxIdRegistroTipoDocumento($datos,&$resultado,&$numfilas)
	{
		if (!parent::BuscarRolesxIdRegistroTipoDocumento($datos,$resultado,$numfilas))
			return false;
		return true;
	}
	
	
	public function BuscarxIdRegistroTipoDocumento($datos,&$resultado,&$numfilas)
	{
		if (!parent::BuscarxIdRegistroTipoDocumento($datos,$resultado,$numfilas))
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



	public function BusquedaAvanzada($datos,&$resultado,&$numfilas)
	{
		$sparam=array(
			'xIdRegistroArea'=> 0,
			'IdRegistroArea'=> "",
			'xIdRegistroTipoDocumento'=> 0,
			'IdRegistroTipoDocumento'=> "",
			'xIdRol'=> 0,
			'IdRol'=> "",			 
			'limit'=> '',
			'orderby'=> "IdRol DESC"
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



	public function Insertar($datos)
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
			
		$oAuditoriasAreasTiposDocumentosRoles = new cAuditoriasAreasTiposDocumentosRoles($this->conexion,$this->formato);
		$datos['IdRegistroArea'] = $datos['IdRegistroArea'];
		$datos['IdRegistroTipoDocumento'] = $datos['IdRegistroTipoDocumento'];
		$datos['IdRol'] = $datos['IdRol'];
		$datos['Accion'] = INSERTAR;
		$datos['AltaUsuario'] = $datos['AltaUsuario'];
		$datos['AltaFecha'] = $datos['AltaFecha'];
		if(!$oAuditoriasAreasTiposDocumentosRoles->InsertarLog($datos,$codigoInsertadolog))
			return false;	
		
		return true;
	}
	
	
	
	public function Eliminar($datos)
	{
		if (!$this->_ValidarEliminar($datos,$datosRegistro))
			return false;
		
		
		$oAuditoriasAreasTiposDocumentosRoles = new cAuditoriasAreasTiposDocumentosRoles($this->conexion,$this->formato);
		$datosLog =$datosRegistro;
		$datosLog['Accion'] = ELIMINAR;
		if(!$oAuditoriasAreasTiposDocumentosRoles->InsertarLog($datosLog,$codigoInsertadolog))
			return false;
		
		if (!parent::Eliminar($datos))
			return false;
		
		
		return true;
	}
	
	
	public function ActivarDocumentoVisualiza($datos)
	{
		$datosmodif['IdRegistroArea'] = $datos['IdRegistroArea'];
		$datosmodif['IdRegistroTipoDocumento'] = $datos['IdRegistroTipoDocumento'];
		$datosmodif['IdRol'] = $datos['IdRol'];
		$datosmodif['DocumentoVisualiza'] = 1;
		if (!$this->ModificarDocumentoVisualiza($datosmodif))
			return false;
		return true;
	}



	public function DesActivarDocumentoVisualiza($datos)
	{
		$datosmodif['IdRegistroArea'] = $datos['IdRegistroArea'];
		$datosmodif['IdRegistroTipoDocumento'] = $datos['IdRegistroTipoDocumento'];
		$datosmodif['IdRol'] = $datos['IdRol'];
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
		$oAuditoriasAreasTiposDocumentosRoles = new cAuditoriasAreasTiposDocumentosRoles($this->conexion,$this->formato);
		$datosLog =$datosRegistro;
		$datosLog['Accion'] = MODIFICACION;
		if(!$oAuditoriasAreasTiposDocumentosRoles->InsertarLog($datosLog,$codigoInsertadolog))
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
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error debe ingresar un codigo valido.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
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
		
		if (!isset($datos['IdRol']) || $datos['IdRol']=="")
			$datos['IdRol']="NULL";
			
		if (!isset($datos['DocumentoVisualiza']) || $datos['DocumentoVisualiza']!="1")
			$datos['DocumentoVisualiza']="1";
			
		if (!isset($datos['AltaFecha']) || $datos['AltaFecha']=="")
			$datos['AltaFecha']="NULL";

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
		

		if (!isset($datos['IdRol']) || $datos['IdRol']=="")
		{
			
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error, debe ingreasar un Rol",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		//print_r($datos);die;
		if (!FuncionesPHPLocal::ValidarContenido($this->conexion,$datos['IdRol'],"NumericoEntero"))
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error debe ingresar un campo numérico3.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		
		return true;
	}





}
?>