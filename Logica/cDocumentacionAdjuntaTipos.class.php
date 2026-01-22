<?php 
include(DIR_CLASES_DB."cDocumentacionAdjuntaTipos.db.php");

class cDocumentacionAdjuntaTipos extends cDocumentacionAdjuntaTiposdb
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
	
	public function BuscarxIdDocumentoAdjunto($datos,&$resultado,&$numfilas)
	{
		if (!parent::BuscarxIdDocumentoAdjunto($datos,$resultado,$numfilas))
			return false;
		return true;
	}


	public function Actualizar($datos)
	{
		$datosBuscar['IdDocumentoAdjunto'] = $datos['IdDocumentoAdjunto'];
		if(!$this->BuscarxIdDocumentoAdjunto($datosBuscar,$resultModulosInsertados,$numfilasModulosInsertados))
			die();
		
		$arrayinicial = array();
		while($fila = $this->conexion->ObtenerSiguienteRegistro($resultModulosInsertados))
			$arrayinicial[$fila['IdDocumentoTipo']] = $fila['IdDocumentoTipo'];

		
		$arrayfinal = array();
		foreach($datos['IdDocumentoTipo'] as $IdDocumentoTipo)
			$arrayfinal[$IdDocumentoTipo] = $IdDocumentoTipo;	
		
		$arraysacar = array_diff($arrayinicial,$arrayfinal);
		$arrayponer = array_diff($arrayfinal,$arrayinicial);

		$datosinsertar['IdDocumentoAdjunto'] = $datos['IdDocumentoAdjunto'];
		foreach($arrayponer as $IdDocumentoTipo)
		{
			$datosinsertar['IdDocumentoTipo'] = $IdDocumentoTipo;
			if (!$this->Insertar($datosinsertar))
				return false;
		}
		
		$datoseliminar['IdDocumentoAdjunto'] = $datos['IdDocumentoAdjunto'];
		foreach($arraysacar as $IdDocumentoTipo)
		{
			$datoseliminar['IdDocumentoTipo'] = $IdDocumentoTipo;
			if (!$this->Eliminar($datoseliminar))
				return false;
		}
		return true;	
	}


	public function Insertar($datos)
	{
		if (!$this->_ValidarInsertar($datos))
			return false;

		$this->_SetearNull($datos);
		$datos['FechaAlta']=date("Y-m-d H:i:s");
		$datos['AltaUsuario']=$_SESSION['usuariocod'];
		$datos['UltimaModificacionUsuario']=$_SESSION['usuariocod'];
		$datos['UltimaModificacionFecha']=date("Y-m-d H:i:s");
		if (!parent::Insertar($datos))
			return false;

		/*$oAuditoriasDocumentosTiposCamposProgramables = new cAuditoriasDocumentosTiposCamposProgramables($this->conexion,$this->formato);
		$datos['IdDocumentoTipoProgamable'] = $codigoinsertado;
		$datos['Accion'] = INSERTAR;
		$datos['AltaUsuario'] = $datos['AltaUsuario'];
		$datos['AltaFecha'] = $datos['AltaFecha'];
		if(!$oAuditoriasDocumentosTiposCamposProgramables->InsertarLog($datos,$codigoInsertadolog))
			return false;
*/
		return true;
	}



	public function Eliminar($datos)
	{
		if (!$this->_ValidarEliminar($datos,$datosRegistro))
			return false;

		/*$oAuditoriasDocumentosTiposCamposProgramables = new cAuditoriasDocumentosTiposCamposProgramables($this->conexion,$this->formato);
		$datosLog =$datosRegistro;
		$datosLog['Accion'] = ELIMINAR;
		if(!$oAuditoriasDocumentosTiposCamposProgramables->InsertarLog($datosLog,$codigoInsertadolog))
			return false;
*/
		if (!parent::Eliminar($datosRegistro))
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


		if (!isset($datos['IdDocumentoAdjunto']) || $datos['IdDocumentoAdjunto']=="")
			$datos['IdDocumentoAdjunto']="NULL";

		if (!isset($datos['IdDocumentoTipo']) || $datos['IdDocumentoTipo']=="")
			$datos['IdDocumentoTipo']="NULL";

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


		if (!isset($datos['IdDocumentoAdjunto']) || $datos['IdDocumentoAdjunto']=="")
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error, debe ingresar un documento adjunto",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		/*if (!isset($datos['TipoPermitido']) || $datos['TipoPermitido']=="")
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error, debe ingresar un tipo de permitido",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		if (!isset($datos['Constante']) || $datos['Constante']=="")
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error, debe ingresar una constante",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		*/

		return true;
	}





}
?>