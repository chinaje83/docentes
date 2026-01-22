<?php 
include(DIR_CLASES_DB."cDocumentosTiposAsociados.db.php");

class cDocumentosTiposAsociados extends cDocumentosTiposAsociadosdb
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
	
	
	public function BuscarxIdRegistroTipoDocumento($datos,&$resultado,&$numfilas)
	{
		
		if (!isset($datos['Anio']) || $datos['Anio']=="" || !is_numeric($datos['Anio']))
			$datos['Anio'] = date("Y");

		if (!isset($datos['Mes']) || $datos['Mes']=="" || !is_numeric($datos['Mes']))
			$datos['Mes'] = date("m");

		$datos['Vigencia'] = $datos['Anio'].str_pad($datos['Mes'],2,"0")."01";
		
		if (!parent::BuscarxIdRegistroTipoDocumento($datos,$resultado,$numfilas))
			return false;
		return true;
	}



	public function BuscarxIdRegistroTipoDocumentoxIdTipoDocumentos($datos,&$resultado,&$numfilas)
	{
		
		if (!parent::BuscarxIdRegistroTipoDocumentoxIdTipoDocumentos($datos,$resultado,$numfilas))
			return false;
		return true;
	}

	public function Insertar($datos,&$codigoinsertado)
	{
		if (!$this->_ValidarInsertar($datos))
			return false;
			
			
		$oEstructuraCampos = new cEstructuraCampos($this->conexion,$this->formato);	
		$datosBusqueda['IdCampo'] = $datos['IdCampos'];
		if(!$oEstructuraCampos->BusquedaxCodigos($datosBusqueda,$resultado,$numfilas))
			return false;
			
		$arrayCampos =array();
		while($fila = $this->conexion->ObtenerSiguienteRegistro($resultado))
			$arrayCampos[] = $fila['Nombre'];
			
		$datos['Campos'] = implode(",",$arrayCampos);	
			
		$this->_SetearNull($datos);
		$datos['AltaUsuario']=$_SESSION['usuariocod'];
		$datos['UltimaModificacionUsuario']=$_SESSION['usuariocod'];
		$datos['UltimaModificacionFecha']= $datos['AltaFecha'] = date("Y-m-d H:i:s");
		if (!parent::Insertar($datos,$codigoinsertado))
			return false;
			
		$oAreas = new cAreas($this->conexion,$this->formato);
		$oAreasTiposDocumentos = new cAreasTiposDocumentos($this->conexion,$this->formato);
		$oElastic = new cModifElastic(INDICEAREAS);
		$datosBuscar['Anio'] = $_SESSION['Anio'];
		$datosBuscar['Mes'] = $_SESSION['Mes'];
		if(!$oAreas->BusquedaAvanzada($datosBuscar,$resultadoAreas,$numfilasAreas))
			return false;
		while($filaArea = $this->conexion->ObtenerSiguienteRegistro($resultadoAreas))
		{
			$datosArea['IdRegistroArea'] = $filaArea['IdRegistro'];
			$datosArea['UltimaModificacionFecha'] = $filaArea['UltimaModificacionFecha'];
			$datosArea['UltimaModificacionUsuario'] = $filaArea['UltimaModificacionUsuario'];
			if($filaArea['UltimaModificacionUsuario'] == $_SESSION['usuariocod'])
				$datosArea['UltimaModificacionUsuarioNombre'] = $_SESSION['usuarionombre']." ".$_SESSION['usuarioapellido'];
			else
				$datosArea['UltimaModificacionUsuarioNombre'] = "";
			
			$datosElastic = $oAreasTiposDocumentos->ArmarArrayElastic($datosArea);
			if(!$oElastic->Actualizar($datos,$datosElastic))
				return false;
		}
			

		$oAuditoriasDocumentosTiposAsociados = new cAuditoriasDocumentosTiposAsociados($this->conexion,$this->formato);
		$datos['IdTipoDocumentoAsociado'] = $codigoinsertado;
		$datos['Accion'] = INSERTAR;
		$datos['AltaUsuario'] = $datos['AltaUsuario'];
		$datos['AltaFecha'] = $datos['AltaFecha'];
		if(!$oAuditoriasDocumentosTiposAsociados->InsertarLog($datos,$codigoInsertadolog))
			return false; 

		return true;
	}



	public function Eliminar($datos)
	{
		if (!$this->_ValidarEliminar($datos,$datosRegistro))
			return false;

		$oAuditoriasDocumentosTiposAsociados = new cAuditoriasDocumentosTiposAsociados($this->conexion,$this->formato);
		$datosLog =$datosRegistro;
		$datosLog['Accion'] = ELIMINAR;
		if(!$oAuditoriasDocumentosTiposAsociados->InsertarLog($datosLog,$codigoInsertadolog))
			return false;

		if (!parent::Eliminar($datos))
			return false;

		return true;
	}
	
	
	
	public function EliminarxIdRegistroTipoDocumento($datos)
	{
		if(!$this->BuscarxIdRegistroTipoDocumento($datos,$resultado,$numfilas))
			return false;

		while($fila = $this->conexion->ObtenerSiguienteRegistro($resultado))
		{
			if (!$this->Eliminar($fila))
				return false;
		}
		return true;
	}
	

//-----------------------------------------------------------------------------------------
//FUNCIONES PRIVADAS
//-----------------------------------------------------------------------------------------

	private function _ValidarInsertar($datos)
	{
		if (!$this->_ValidarDatosVacios($datos))
			return false;
			
		$oDocumentosTipos = new cDocumentosTipos($this->conexion,$this->formato);
		$datosbuscar['IdRegistro'] = $datos['IdRegistroTipoDocumento'];
		if(!$oDocumentosTipos->BuscarxCodigo($datosbuscar,$resultado,$numfilas))
			return false;
			
		$filaDocumentoTipo = $this->conexion->ObtenerSiguienteRegistro($resultado);
		if($filaDocumentoTipo['IdTipoDocumento']==$datos['IdTipoDocumento'])
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error, no se puede agregar al mismo tipo de documento.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
			
		}	
		
		$datosbuscar['IdRegistroTipoDocumento'] = $datos['IdRegistroTipoDocumento'];
		$datosbuscar['IdTipoDocumento'] = $datos['IdTipoDocumento'];
		
		
		if(!$this->BuscarxIdRegistroTipoDocumentoxIdTipoDocumentos($datosbuscar,$resultado,$numfilas))
			return false;
			
			
		if($numfilas==1)
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error, ya existe el tipo de documento.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
			
		}	
		
		$Campos = explode(",",$filaDocumentoTipo['Campos']);
		
		$IdCampos = explode(",",$datos['IdCampos']);
		
		if(count(array_intersect($IdCampos,$Campos))==0)
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error, el Documento no contiene al campo a agregar.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
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


		if (!isset($datos['IdRegistroTipoDocumento']) || $datos['IdRegistroTipoDocumento']=="")
			$datos['IdRegistroTipoDocumento']="NULL";

		if (!isset($datos['IdTipoDocumento']) || $datos['IdTipoDocumento']=="")
			$datos['IdTipoDocumento']="NULL";

		
		if (!isset($datos['IdCampos']) || $datos['IdCampos']=="")
			$datos['IdCampos']="NULL";
		
		if (!isset($datos['Campos']) || $datos['Campos']=="")
			$datos['Campos']="NULL";

		if (!isset($datos['AltaFecha']) || $datos['AltaFecha']=="")
			$datos['AltaFecha']="NULL";

		if (!isset($datos['AltaUsuario']) || $datos['AltaUsuario']=="")
			$datos['AltaUsuario']="NULL";

		if (!isset($datos['AltaApp']) || $datos['AltaApp']=="")
			$datos['AltaApp']="NULL";

		if (!isset($datos['UltimaModificacionFecha']) || $datos['UltimaModificacionFecha']=="")
			$datos['UltimaModificacionFecha']="NULL";

		if (!isset($datos['UltimaModificacionApp']) || $datos['UltimaModificacionApp']=="")
			$datos['UltimaModificacionApp']="NULL";
		return true;
	}



	private function _ValidarDatosVacios($datos)
	{


		if (!isset($datos['IdRegistroTipoDocumento']) || $datos['IdRegistroTipoDocumento']=="")
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Debe ingresar un c�digo  de tipo  de documento",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		if (!FuncionesPHPLocal::ValidarContenido($this->conexion,$datos['IdRegistroTipoDocumento'],"NumericoEntero"))
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error debe ingresar un campo num�rico.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		
		if (!isset($datos['IdTipoDocumento']) || $datos['IdTipoDocumento']=="")
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Debe ingresar un tipo de documento",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		if (!FuncionesPHPLocal::ValidarContenido($this->conexion,$datos['IdTipoDocumento'],"NumericoEntero"))
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error debe ingresar un campo num�rico.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		if (!isset($datos['IdCampos']) || $datos['IdCampos']=="")
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Debe ingresar un campo",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		
		return true;
	}





}
?>