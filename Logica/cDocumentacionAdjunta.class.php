<?php 
include(DIR_CLASES_DB."cDocumentacionAdjunta.db.php");

class cDocumentacionAdjunta extends cDocumentacionAdjuntadb
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



	public function BusquedaAvanzada($datos,&$resultado,&$numfilas)
	{
		$sparam=array(
			'xIdDocumentoAdjunto'=> 0,
			'IdDocumentoAdjunto'=> "",
			'xNombre'=> 0,
			'Nombre'=> "",
			'xTipoPermitido'=> 0,
			'TipoPermitido'=> "",
			'xIdEstado'=> 0,
			'IdEstado'=> "-1",
			'limit'=> '',
			'orderby'=> "IdDocumentoAdjunto DESC"
		);

		if(isset($datos['IdDocumentoAdjunto']) && $datos['IdDocumentoAdjunto']!="")
		{
			$sparam['IdDocumentoAdjunto']= $datos['IdDocumentoAdjunto'];
			$sparam['xIdDocumentoAdjunto']= 1;
		}
		if(isset($datos['Nombre']) && $datos['Nombre']!="")
		{
			$sparam['Nombre']= $datos['Nombre'];
			$sparam['xNombre']= 1;
		}
		if(isset($datos['TipoPermitido']) && $datos['TipoPermitido']!="")
		{
			$sparam['TipoPermitido']= $datos['TipoPermitido'];
			$sparam['xTipoPermitido']= 1;
		}
		
		if(isset($datos['IdEstado']) && $datos['IdEstado']!="")
		{
			$sparam['IdEstado']= $datos['IdEstado'];
			$sparam['xIdEstado']= 1;
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



	public function Insertar($datos,&$codigoinsertado)
	{
		if (!$this->_ValidarInsertar($datos))
			return false;
		
		$this->_SetearNull($datos);
		$datos['AltaUsuario']=$_SESSION['usuariocod'];
		$datos['UltimaModificacionUsuario']=$_SESSION['usuariocod'];
		$datos['UltimaModificacionFecha']= $datos['FechaAlta'] = date("Y-m-d H:i:s");
		$datos['IdEstado'] = ACTIVO;
		if (!parent::Insertar($datos,$codigoinsertado))
			return false;
		
		$datos['IdDocumentoAdjunto'] = $codigoinsertado;
		$oDocumentacionAdjuntaTipos = new cDocumentacionAdjuntaTipos($this->conexion,$this->formato);
		if(!$oDocumentacionAdjuntaTipos->Actualizar($datos))
			return false;
			
		if (!parent::ModificarTipoPermitido($datos))
			return false;		
		
		$oAuditoriasDocumentacionAdjunta = new cAuditoriasDocumentacionAdjunta($this->conexion,$this->formato);
		
		$datos['Accion'] = INSERTAR;
		$datos['AltaUsuario'] = $datos['AltaUsuario'];
		$datos['FechaAlta'] = $datos['FechaAlta'];
		if(!$oAuditoriasDocumentacionAdjunta->InsertarLog($datos,$codigoInsertadolog))
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
			
		$oDocumentacionAdjuntaTipos = new cDocumentacionAdjuntaTipos($this->conexion,$this->formato);
		if(!$oDocumentacionAdjuntaTipos->Actualizar($datos))
			return false;
			
		if (!parent::ModificarTipoPermitido($datos))
			return false;	
				

		$oAuditoriasDocumentacionAdjunta = new cAuditoriasDocumentacionAdjunta($this->conexion,$this->formato);
		$datosRegistro['Accion'] = MODIFICACION;
		$datosRegistro['Nombre'] = $datos['Nombre'];
		$datosRegistro['TipoPermitido'] = strtoupper($datos['TipoPermitido']);
		$datosRegistro['Constante'] = strtoupper($datos['Constante']);	
		if(!$oAuditoriasDocumentacionAdjunta->InsertarLog($datosRegistro,$codigoInsertadolog))
			return false;
		return true;
	}



	public function Eliminar($datos)
	{
		if (!$this->_ValidarEliminar($datos,$datosRegistro))
			return false;

		$oAuditoriasDocumentacionAdjunta = new cAuditoriasDocumentacionAdjunta($this->conexion,$this->formato);
		$datosLog =$datosRegistro;
		$datosLog['Accion'] = ELIMINAR;
		if(!$oAuditoriasDocumentacionAdjunta->InsertarLog($datosLog,$codigoInsertadolog))
			return false;

		$datosmodif['IdDocumentoAdjunto'] = $datos['IdDocumentoAdjunto'];
		$datosmodif['IdEstado'] = ELIMINADO;
		if (!$this->ModificarEstado($datosmodif))
			return false;
		return true;
	}



	public function ModificarEstado($datos)
	{
		if (!parent::ModificarEstado($datos))
			return false;
		return true;
	}



	public function Activar($datos)
	{
		$datosmodif['IdDocumentoAdjunto'] = $datos['IdDocumentoAdjunto'];
		$datosmodif['IdEstado'] = ACTIVO;
		if (!$this->ModificarEstado($datosmodif))
			return false;
		return true;
	}



	public function DesActivar($datos)
	{
		$datosmodif['IdDocumentoAdjunto'] = $datos['IdDocumentoAdjunto'];
		$datosmodif['IdEstado'] = NOACTIVO;
		if (!$this->ModificarEstado($datosmodif))
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


		if (!isset($datos['Nombre']) || $datos['Nombre']=="")
			$datos['Nombre']="NULL";

		if (!isset($datos['TipoPermitido']) || $datos['TipoPermitido']=="")
			$datos['TipoPermitido']="NULL";

		if (!isset($datos['FechaAlta']) || $datos['FechaAlta']=="")
			$datos['FechaAlta']="NULL";

		if (!isset($datos['Constante']) || $datos['Constante']=="")
			$datos['Constante']="NULL";

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


		if (!isset($datos['Nombre']) || $datos['Nombre']=="")
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error, debe ingresar un nombre",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		/*if (!isset($datos['TipoPermitido']) || $datos['TipoPermitido']=="")
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error, debe ingresar un tipo de permitido",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
*/

		if (!isset($datos['IdDocumentoTipo']) || count($datos['IdDocumentoTipo'])=="0")
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error, debe ingresar un tipo de permitido",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		if (!isset($datos['Constante']) || $datos['Constante']=="")
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error, debe ingresar una constante",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		return true;
	}





}
?>