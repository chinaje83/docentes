<?php 
include(DIR_CLASES_DB."cDocumentosTiposMacros.db.php");

class cDocumentosTiposMacros extends cDocumentosTiposMacrosdb
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


	public function BuscarPasosMacros($datos,&$resultado,&$numfilas)
	{
		if (!parent::BuscarPasosMacros($datos,$resultado,$numfilas))
			return false;
		return true;
	}


	public function AgregarMacro($datos,&$codigoinsertado)
	{
		
		if(!$this->Insertar($datos,$codigoinsertado))
			return false;
		
		return true;	
	}
	
	
	public function InsertarMacro($datos,&$codigoinsertado)
	{
		if (!$this->_ValidarInsertar($datos))
			return false;

		$this->_SetearNull($datos);
		
		$this->ObtenerProximoOrden($datos,$proxorden);
		$datos['Orden'] = $proxorden;
		$datos['AltaUsuario']=$_SESSION['usuariocod'];
		$datos['UltimaModificacionUsuario']=$_SESSION['usuariocod'];
		$datos['UltimaModificacionFecha'] = $datos['AltaFecha'] =date("Y-m-d H:i:s");
		if (!parent::Insertar($datos,$codigoinsertado))
			return false;
		
		return true;
	}
	

	public function Insertar($datos,&$codigoinsertado)
	{
		if(!$this->InsertarMacro($datos,$codigoinsertado))
			return false;

		$datosBusqueda['IdMacro'] = $datos['IdMacro'];
		$oObjeto = new cFormulariosMacrosEstructuras($this->conexion,$this->formato);	
		if(!$oObjeto->BuscarEstructurasxIdMacro($datosBusqueda,$resultado,$numfilas))
			return false;
		
		$datosInsertar['IdMacroPosicion'] = $codigoinsertado;
		$datosInsertar['IdRegistroTipoDocumento'] = $datos['IdRegistroTipoDocumento'];
		$datosInsertar['IdMacro'] = $datos['IdMacro'];
		$oDocumentosTiposMacrosZonas = new cDocumentosTiposMacrosZonas($this->conexion,$this->formato);	
		while($fila = $this->conexion->ObtenerSiguienteRegistro($resultado))
		{
			$datosInsertar['IdEstructura'] = $fila['IdEstructura'];
			if(!$oDocumentosTiposMacrosZonas->Insertar($datosInsertar,$IdZona))
				return false;
				
		}	

		/*
		$oAuditoriasDocumentosTiposMacros = new cAuditoriasDocumentosTiposMacros($this->conexion,$this->formato);
		$datos['IdMacroPosicion'] = $codigoinsertado;
		$datos['Accion'] = INSERTAR;
		$datos['AltaUsuario'] = $datos['AltaUsuario'];
		$datos['AltaFecha'] = $datos['AltaFecha'];
		if(!$oAuditoriasDocumentosTiposMacros->InsertarLog($datos,$codigoInsertadolog))
			return false;
		*/
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

		/*
		$oAuditoriasDocumentosTiposMacros = new cAuditoriasDocumentosTiposMacros($this->conexion,$this->formato);
		$datosRegistro['Accion'] = MODIFICACION;
		if(!$oAuditoriasDocumentosTiposMacros->InsertarLog($datosRegistro,$codigoInsertadolog))
			return false;
		
		*/
		return true;
	}



	public function Eliminar($datos)
	{
		if (!$this->_ValidarEliminar($datos,$datosRegistro))
			return false;

		/*
		$oAuditoriasDocumentosTiposMacros = new cAuditoriasDocumentosTiposMacros($this->conexion,$this->formato);
		$datosLog =$datosRegistro;
		$datosLog['Accion'] = ELIMINAR;
		if(!$oAuditoriasDocumentosTiposMacros->InsertarLog($datosLog,$codigoInsertadolog))
			return false;
		*/
		$oDocumentosTiposZonas = new cDocumentosTiposZonas($this->conexion,$this->formato);	
		if(!$oDocumentosTiposZonas->EliminarxIdMacroPosicion($datos))
			return false;
		
		$oDocumentosTiposMacrosZonas = new cDocumentosTiposMacrosZonas($this->conexion,$this->formato);	
		if(!$oDocumentosTiposMacrosZonas->EliminarxIdMacroPosicion($datos))
			return false;
			
		if (!parent::Eliminar($datos))
			return false;

		return true;
	}



	public function ModificarOrdenCompleto($datos)
	{
		$datosmodif['Orden'] = 1;
		$arregloOrden = $datos['macro'];
		foreach ($arregloOrden as $IdMacroPosicion){
			$datosmodif['IdMacroPosicion'] = $IdMacroPosicion;
			if (!parent::ModificarOrden($datosmodif))
				return false;
			$datosmodif['Orden']++;
		}
		return true;
	}



	private function ObtenerProximoOrden($datos,&$proxorden)
	{
		$proxorden = 0;
		if (!parent::BuscarUltimoOrden($datos,$resultado,$numfilas))
			return false;

		if ($numfilas!=0){
			$datos = $this->conexion->ObtenerSiguienteRegistro($resultado);
			$proxorden = $datos['maximo'] + 1;
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

		if (!isset($datos['IdMacro']) || $datos['IdMacro']=="")
			$datos['IdMacro']="NULL";

		if (!isset($datos['FechaAlta']) || $datos['FechaAlta']=="")
			$datos['FechaAlta']="NULL";

		if (!isset($datos['AltaUsuario']) || $datos['AltaUsuario']=="")
			$datos['AltaUsuario']="NULL";

		if (!isset($datos['UltimaModificacionFecha']) || $datos['UltimaModificacionFecha']=="")
			$datos['UltimaModificacionFecha']="NULL";
		return true;
	}



	private function _ValidarDatosVacios($datos)
	{


		if (!isset($datos['IdRegistroTipoDocumento']) || $datos['IdRegistroTipoDocumento']=="")
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Debe ingresar un id del tipo de documento",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		if (!FuncionesPHPLocal::ValidarContenido($this->conexion,$datos['IdRegistroTipoDocumento'],"NumericoEntero"))
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error debe ingresar un campo numérico.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		if (!isset($datos['IdMacro']) || $datos['IdMacro']=="")
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Debe ingresar un id macro",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		if (!FuncionesPHPLocal::ValidarContenido($this->conexion,$datos['IdMacro'],"NumericoEntero"))
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error debe ingresar un campo numérico.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		return true;
	}





}
?>