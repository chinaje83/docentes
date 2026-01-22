<?php 
include(DIR_CLASES_DB."cDocumentosTiposSunaModulos.db.php");

class cDocumentosTiposSunaModulos extends cDocumentosTiposSunaModulosdb
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
		if (!parent::BuscarxIdRegistroTipoDocumento($datos,$resultado,$numfilas))
			return false;
		return true;
	}
	
	
	public function BuscarxIdRegistroTipoDocumentoxIdSunaModulo($datos,&$resultado,&$numfilas)
	{
		if (!parent::BuscarxIdRegistroTipoDocumentoxIdSunaModulo($datos,$resultado,$numfilas))
			return false;
		return true;
	}
	
	



	public function BusquedaAvanzada($datos,&$resultado,&$numfilas)
	{
		$sparam=array(
			'xIdTipoDocumentoSunaModulo'=> 0,
			'IdTipoDocumentoSunaModulo'=> "",
			'xIdRegistroTipoDocumento'=> 0,
			'IdRegistroTipoDocumento'=> "",
			'xIdTipoDocumento'=> 0,
			'IdTipoDocumento'=> "",
			'xIdSunaModulo'=> 0,
			'IdSunaModulo'=> "",
			'xTitulo'=> 0,
			'Titulo'=> "",
			'xDescripcion'=> 0,
			'Descripcion'=> "",
			'limit'=> '',
			'orderby'=> "Orden ASC"
		);

		if(isset($datos['IdTipoDocumentoSunaModulo']) && $datos['IdTipoDocumentoSunaModulo']!="")
		{
			$sparam['IdTipoDocumentoSunaModulo']= $datos['IdTipoDocumentoSunaModulo'];
			$sparam['xIdTipoDocumentoSunaModulo']= 1;
		}
		if(isset($datos['IdRegistroTipoDocumento']) && $datos['IdRegistroTipoDocumento']!="")
		{
			$sparam['IdRegistroTipoDocumento']= $datos['IdRegistroTipoDocumento'];
			$sparam['xIdRegistroTipoDocumento']= 1;
		}
		if(isset($datos['IdTipoDocumento']) && $datos['IdTipoDocumento']!="")
		{
			$sparam['IdTipoDocumento']= $datos['IdTipoDocumento'];
			$sparam['xIdTipoDocumento']= 1;
		}
		if(isset($datos['IdSunaModulo']) && $datos['IdSunaModulo']!="")
		{
			$sparam['IdSunaModulo']= $datos['IdSunaModulo'];
			$sparam['xIdSunaModulo']= 1;
		}
		if(isset($datos['Titulo']) && $datos['Titulo']!="")
		{
			$sparam['Titulo']= $datos['Titulo'];
			$sparam['xTitulo']= 1;
		}
		if(isset($datos['Descripcion']) && $datos['Descripcion']!="")
		{
			$sparam['Descripcion']= $datos['Descripcion'];
			$sparam['xDescripcion']= 1;
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
		$this->ObtenerProximoOrden($datos,$proxorden);
		$datos['Orden'] = $proxorden;
		$datos['AltaFecha']=date("Y-m-d H:i:s");
		$datos['AltaUsuario']=$_SESSION['usuariocod'];
		$datos['UltimaModificacionUsuario']=$_SESSION['usuariocod'];
		$datos['UltimaModificacionFecha']=date("Y-m-d H:i:s");
		$datos['AltaApp']= APP;
		$datos['UltimaModificacionApp']= APP;
		
		if (!parent::Insertar($datos,$codigoinsertado))
			return false;
		$datos['IdTipoDocumentoSunaModulo'] = $codigoinsertado;
		
		
		$oObjeto = new cDocumentosTipos($this->conexion,$this->formato);
		if(!$oObjeto->AgregarCamposJson($datos))
			return false;
		
		
		$oAuditoriasDocumentosTiposSunaModulos = new cAuditoriasDocumentosTiposSunaModulos($this->conexion,$this->formato);
		
		$datos['Accion'] = INSERTAR;
		$datos['AltaUsuario'] = $datos['AltaUsuario'];
		$datos['AltaFecha'] = $datos['AltaFecha'];
		if(!$oAuditoriasDocumentosTiposSunaModulos->InsertarLog($datos,$codigoInsertadolog))
			return false;

		return true;
	}



	public function Modificar($datos)
	{
		if (!$this->_ValidarModificar($datos,$datosRegistro))
			return false;

		$datos['UltimaModificacionUsuario']= $datosRegistro['UltimaModificacionUsuario'] =$_SESSION['usuariocod'];
		$datos['UltimaModificacionFecha']= $datosRegistro['UltimaModificacionFecha'] = date("Y-m-d H:i:s");
		$datos['UltimaModificacionApp']= APP;
		$this->_SetearNull($datos);
		if (!parent::Modificar($datos))
			return false;

		$oAuditoriasDocumentosTiposSunaModulos = new cAuditoriasDocumentosTiposSunaModulos($this->conexion,$this->formato);
		$datosRegistro['Accion'] = MODIFICACION;
		if(!$oAuditoriasDocumentosTiposSunaModulos->InsertarLog($datosRegistro,$codigoInsertadolog))
			return false;

		return true;
	}



	public function Eliminar($datos)
	{
		if (!$this->_ValidarEliminar($datos,$datosRegistro))
			return false;

		$oAuditoriasDocumentosTiposSunaModulos = new cAuditoriasDocumentosTiposSunaModulos($this->conexion,$this->formato);
		$datosLog =$datosRegistro;
		$datosLog['Accion'] = ELIMINAR;
		if(!$oAuditoriasDocumentosTiposSunaModulos->InsertarLog($datosLog,$codigoInsertadolog))
			return false;
		
		
		
		if (!parent::Eliminar($datos))
			return false;
		
		$oObjeto = new cDocumentosTipos($this->conexion,$this->formato);
		if(!$oObjeto->AgregarCamposJson($datos))
			return false;

		return true;
	}
	
	
	public function ModificarObligatorio($datos)
	{
		if (!$this->_ValidarEliminar($datos,$datosRegistro))
			return false;
		$datos['UltimaModificacionUsuario']= $datosRegistro['UltimaModificacionUsuario'] =$_SESSION['usuariocod'];
		$datos['UltimaModificacionFecha']= $datosRegistro['UltimaModificacionFecha'] = date("Y-m-d H:i:s");
		$datos['UltimaModificacionApp']= APP;
		if (!parent::ModificarObligatorio($datos))
			return false;
		
		$oObjeto = new cDocumentosTipos($this->conexion,$this->formato);
		if(!$oObjeto->AgregarCamposJson($datos))
			return false;
		
		$oAuditoriasDocumentosTiposSunaModulos = new cAuditoriasDocumentosTiposSunaModulos($this->conexion,$this->formato);
		$datosRegistro['Accion'] = MODIFICACION;
		if(!$oAuditoriasDocumentosTiposSunaModulos->InsertarLog($datosRegistro,$codigoInsertadolog))
			return false;
		

		return true;
	}
	
	
	public function ModificarTituloDescripcion($datos)
	{
		if (!$this->_ValidarEliminar($datos,$datosRegistro))
			return false;
		$datos['UltimaModificacionUsuario']= $datosRegistro['UltimaModificacionUsuario'] =$_SESSION['usuariocod'];
		$datos['UltimaModificacionFecha']= $datosRegistro['UltimaModificacionFecha'] = date("Y-m-d H:i:s");
		$datos['UltimaModificacionApp']= APP;
		$this->_SetearNull($datos);
		if (!parent::ModificarTituloDescripcion($datos))
			return false;
		
		$oObjeto = new cDocumentosTipos($this->conexion,$this->formato);
		if(!$oObjeto->AgregarCamposJson($datos))
			return false;
		
		$oAuditoriasDocumentosTiposSunaModulos = new cAuditoriasDocumentosTiposSunaModulos($this->conexion,$this->formato);
		$datosRegistro['Accion'] = MODIFICACION;
		if(!$oAuditoriasDocumentosTiposSunaModulos->InsertarLog($datosRegistro,$codigoInsertadolog))
			return false;
		

		return true;
	}
	



	public function ModificarOrdenCompleto($datos)
	{
		$datosmodif['Orden'] = 1;
		foreach ($datos['IdTipoDocumentoSunaModulo'] as $IdTipoDocumentoSunaModulo){
			$datosmodif['IdTipoDocumentoSunaModulo'] = $IdTipoDocumentoSunaModulo;
			//print_r($datosmodif);
			if (!parent::ModificarOrden($datosmodif))
				return false;
			$datosmodif['Orden']++;
		}
		
		$oObjeto = new cDocumentosTipos($this->conexion,$this->formato);
		if(!$oObjeto->AgregarCamposJson($datos))
			return false;
		
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
		
		if (!$this->BuscarxIdRegistroTipoDocumentoxIdSunaModulo($datos,$resultado,$numfilas))
			return false;

		if ($numfilas>0)
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error, el modulo ya fue agregado.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
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
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error debe ingresar un c�digo valido.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
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
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error debe ingresar un c�digo valido.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
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

		if (!isset($datos['IdSunaModulo']) || $datos['IdSunaModulo']=="")
			$datos['IdSunaModulo']="NULL";

		if (!isset($datos['Titulo']) || $datos['Titulo']=="")
			$datos['Titulo']="NULL";

		if (!isset($datos['Descripcion']) || $datos['Descripcion']=="")
			$datos['Descripcion']="NULL";

		if (!isset($datos['Visualiza']) || $datos['Visualiza']=="")
			$datos['Visualiza']="1";

		if (!isset($datos['Obligatorio']) || $datos['Obligatorio']=="")
			$datos['Obligatorio']="0";

		
		return true;
	}



	private function _ValidarDatosVacios($datos)
	{


		if (!isset($datos['IdRegistroTipoDocumento']) || $datos['IdRegistroTipoDocumento']=="")
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Debe ingresar un Id Registro Tipo Documento",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		if (isset($datos['IdRegistroTipoDocumento']) && $datos['IdRegistroTipoDocumento']!="")
		{
			if (!FuncionesPHPLocal::ValidarContenido($this->conexion,$datos['IdRegistroTipoDocumento'],"NumericoEntero"))
			{
				FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error debe ingresar un campo numérico.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
				return false;
			}
		}

		if (!isset($datos['IdTipoDocumento']) || $datos['IdTipoDocumento']=="")
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Debe ingresar un Tipo Documento",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		if (isset($datos['IdTipoDocumento']) && $datos['IdTipoDocumento']!="")
		{
			if (!FuncionesPHPLocal::ValidarContenido($this->conexion,$datos['IdTipoDocumento'],"NumericoEntero"))
			{
				FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error debe ingresar un campo numérico.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
				return false;
			}
		}

		if (!isset($datos['IdSunaModulo']) || $datos['IdSunaModulo']=="")
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Debe ingresar un Modulo",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		if (isset($datos['IdSunaModulo']) && $datos['IdSunaModulo']!="")
		{
			if (!FuncionesPHPLocal::ValidarContenido($this->conexion,$datos['IdSunaModulo'],"NumericoEntero"))
			{
				FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error debe ingresar un campo numérico.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
				return false;
			}
		}

		/*if (!isset($datos['Titulo']) || $datos['Titulo']=="")
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Debe ingresar un T�tulo",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		if (!isset($datos['Descripcion']) || $datos['Descripcion']=="")
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Debe ingresar una Descripci�n",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}*/

		/*if (!isset($datos['Visualiza']) || $datos['Visualiza']=="")
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Debe ingresar  Visualiza",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		if (isset($datos['Visualiza']) && $datos['Visualiza']!="")
		{
			if (!FuncionesPHPLocal::ValidarContenido($this->conexion,$datos['Visualiza'],"NumericoEntero"))
			{
				FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error debe ingresar un campo numérico.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
				return false;
			}
		}*/

		if (!isset($datos['Obligatorio']) || $datos['Obligatorio']=="")
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Debe ingresar un Obligatorio",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		if (isset($datos['Obligatorio']) && $datos['Obligatorio']!="")
		{
			if (!FuncionesPHPLocal::ValidarContenido($this->conexion,$datos['Obligatorio'],"NumericoEntero"))
			{
				FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error debe ingresar un campo numérico.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
				return false;
			}
		}

		
		return true;
	}





}
?>