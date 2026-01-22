<?php 
include(DIR_CLASES_DB."cDocumentosTiposDocumentacionAdjunta.db.php");

class cDocumentosTiposDocumentacionAdjunta extends cDocumentosTiposDocumentacionAdjuntadb
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
	
	public function BuscarxIdRegistroTipoDocumentoActivos($datos,&$resultado,&$numfilas)
	{
		if (!parent::BuscarxIdRegistroTipoDocumentoActivos($datos,$resultado,$numfilas))
			return false;
		return true;
	}
	
	public function BuscarxIdRegistroTipoDocumentoxIdDocumentoAdjunto($datos,&$resultado,&$numfilas)
	{
		if (!parent::BuscarxIdRegistroTipoDocumentoxIdDocumentoAdjunto($datos,$resultado,$numfilas))
			return false;
		return true;
	}


	public function BuscarxIdRegistroTipoDocumentoNoRelacionados($datos,&$resultado,&$numfilas)
	{
		if (!parent::BuscarxIdRegistroTipoDocumentoNoRelacionados($datos,$resultado,$numfilas))
			return false;
		return true;
	}


	public function BusquedaAvanzada($datos,&$resultado,&$numfilas)
	{
		$sparam=array(
			'xIdDocumentoAdjunto'=> 0,
			'IdDocumentoAdjunto'=> "",
			'xIdRegistroTipoDocumento'=> 0,
			'IdRegistroTipoDocumento'=> "",
			'xNombre'=> 0,
			'Nombre'=> "",
			'xCantidad'=> 0,
			'Cantidad'=> "",
			'xEsObligatorio'=> 0,
			'EsObligatorio'=> "",
			'xCantidadMaxObligatoria'=> 0,
			'CantidadMaxObligatoria'=> "",
			'limit'=> '',
			'orderby'=> "IdDocumentoAdjunto DESC"
		);

		if(isset($datos['IdDocumentoAdjunto']) && $datos['IdDocumentoAdjunto']!="")
		{
			$sparam['IdDocumentoAdjunto']= $datos['IdDocumentoAdjunto'];
			$sparam['xIdDocumentoAdjunto']= 1;
		}
		if(isset($datos['IdRegistroTipoDocumento']) && $datos['IdRegistroTipoDocumento']!="")
		{
			$sparam['IdRegistroTipoDocumento']= $datos['IdRegistroTipoDocumento'];
			$sparam['xIdRegistroTipoDocumento']= 1;
		}
		if(isset($datos['Nombre']) && $datos['Nombre']!="")
		{
			$sparam['Nombre']= $datos['Nombre'];
			$sparam['xNombre']= 1;
		}
		if(isset($datos['Cantidad']) && $datos['Cantidad']!="")
		{
			$sparam['Cantidad']= $datos['Cantidad'];
			$sparam['xCantidad']= 1;
		}
		
		if(isset($datos['EsObligatorio']) && $datos['EsObligatorio']!="")
		{
			$sparam['EsObligatorio']= $datos['EsObligatorio'];
			$sparam['xEsObligatorio']= 1;
		}

		if(isset($datos['CantidadMaxObligatoria']) && $datos['CantidadMaxObligatoria']!="")
		{
			$sparam['CantidadMaxObligatoria']= $datos['CantidadMaxObligatoria'];
			$sparam['xCantidadMaxObligatoria']= 1;
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
		$datos['AltaUsuario']=$_SESSION['usuariocod'];
		$datos['UltimaModificacionUsuario']=$_SESSION['usuariocod'];
		$datos['UltimaModificacionFecha']= $datos['FechaAlta'] = date("Y-m-d H:i:s");
		if (!parent::Insertar($datos))
			return false;
		
		$oAuditoriasDocumentosTiposDocumentacionAdjunta = new cAuditoriasDocumentosTiposDocumentacionAdjunta($this->conexion,$this->formato);
		$datos['IdDocumentoAdjunto'] = $datos['IdDocumentoAdjunto'];
		$datos['Accion'] = INSERTAR;
		$datos['AltaUsuario'] = $datos['AltaUsuario'];
		$datos['FechaAlta'] = $datos['FechaAlta'];
		if(!$oAuditoriasDocumentosTiposDocumentacionAdjunta->InsertarLog($datos,$codigoInsertadolog))
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

		/*$oAuditoriasDocumentosTiposDocumentacionAdjunta = new cAuditoriasDocumentosTiposDocumentacionAdjunta($this->conexion,$this->formato);
		$datosRegistro['Accion'] = MODIFICACION;
		if(!$oAuditoriasDocumentosTiposDocumentacionAdjunta->InsertarLog($datosRegistro,$codigoInsertadolog))
			return false;
*/
		return true;
	}



	public function Eliminar($datos)
	{
		if (!$this->_ValidarEliminar($datos,$datosRegistro))
			return false;

		$oAuditoriasDocumentosTiposDocumentacionAdjunta = new cAuditoriasDocumentosTiposDocumentacionAdjunta($this->conexion,$this->formato);
		$datosLog =$datosRegistro;
		$datosLog['Accion'] = ELIMINAR;
		if(!$oAuditoriasDocumentosTiposDocumentacionAdjunta->InsertarLog($datosLog,$codigoInsertadolog))
			return false;

		if (!parent::Eliminar($datos))
			return false;

		return true;
	}
	
	
	
	public function EliminarxIdRegistroTipoDocumento($datos)
	{
		if (!$this->BuscarxIdRegistroTipoDocumento($datos,$resultado,$numfilas))
			return false;

		while($fila = $this->conexion->ObtenerSiguienteRegistro($resultado))
		{
			
			if(!$this->Eliminar($fila))
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
			
		if(!$this->BuscarxIdRegistroTipoDocumentoxIdDocumentoAdjunto($datos,$resultado,$numfilas))
			return false;
			
		if($numfilas>0)
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error, ya existe el adjunto.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
			
		}		

		return true;
	}



	private function _ValidarModificar($datos,&$datosRegistro)
	{
		if (!$this->BuscarxIdRegistroTipoDocumentoxIdDocumentoAdjunto($datos,$resultado,$numfilas))
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
		if (!$this->BuscarxIdRegistroTipoDocumentoxIdDocumentoAdjunto($datos,$resultado,$numfilas))
			return false;

		if ($numfilas!=1)
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error debe ingresar un código valido.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		$datosRegistro = $this->conexion->ObtenerSiguienteRegistro($resultado);
		$oDocumentosTiposZonas = new cDocumentosTiposZonas($this->conexion,$this->formato);
		if(!$oDocumentosTiposZonas->BuscarxIdRegistroTipoDocumentoxIdDocumentoAdjunto($datosRegistro,$resultado,$numfilas))
			return false;
			
		if($numfilas!=0)
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error debe eliminar el documento del formulario.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
			
		}	
		return true;
	}



	private function _SetearNull(&$datos)
	{


		if (!isset($datos['IdDocumentoAdjunto']) || $datos['IdDocumentoAdjunto']=="")
			$datos['IdDocumentoAdjunto']="NULL";
			
		if (!isset($datos['IdRegistroTipoDocumento']) || $datos['IdRegistroTipoDocumento']=="")
			$datos['IdRegistroTipoDocumento']="NULL";	

		if (!isset($datos['Cantidad']) || $datos['Cantidad']=="")
			$datos['Cantidad']="NULL";

		if (!isset($datos['EsObligatorio']) || $datos['EsObligatorio']!="1")
			$datos['EsObligatorio']="0";
			
		if (!isset($datos['CantidadMaxObligatoria']) || $datos['CantidadMaxObligatoria']=="")
			$datos['CantidadMaxObligatoria']="NULL";	
			
		if (isset($datos['EsObligatorio']) && $datos['EsObligatorio']!="1")
			$datos['CantidadMaxObligatoria']="NULL";	

		if (!isset($datos['FechaAlta']) || $datos['FechaAlta']=="")
			$datos['FechaAlta']="NULL";

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
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Debe ingresar un tipo de documento",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		if (!FuncionesPHPLocal::ValidarContenido($this->conexion,$datos['IdRegistroTipoDocumento'],"NumericoEntero"))
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error debe ingresar un campo numérico.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		
		if (!isset($datos['IdDocumentoAdjunto']) || $datos['IdDocumentoAdjunto']=="")
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Debe ingresar un adjunto",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		if (!FuncionesPHPLocal::ValidarContenido($this->conexion,$datos['IdDocumentoAdjunto'],"NumericoEntero"))
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error debe ingresar un campo numérico.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		
		if (!isset($datos['Cantidad']) || $datos['Cantidad']=="")
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Debe ingresar una cantidad",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		if (!FuncionesPHPLocal::ValidarContenido($this->conexion,$datos['Cantidad'],"NumericoEntero"))
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error debe ingresar un campo numérico.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		if (isset($datos['Cantidad']) && $datos['Cantidad']<1)
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Debe ingresar una cantidad mayor a cero",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		if (!isset($datos['EsObligatorio']) || $datos['EsObligatorio']=="")
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Debe ingresar si es obligatorio",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		if (!FuncionesPHPLocal::ValidarContenido($this->conexion,$datos['EsObligatorio'],"NumericoEntero"))
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error debe ingresar un campo numérico.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		
		if (isset($datos['EsObligatorio']) && $datos['EsObligatorio']=="1")
		{
			
			if (!isset($datos['CantidadMaxObligatoria']) || $datos['CantidadMaxObligatoria']=="")
			{
				FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Debe ingresar una cantidad max obligatoria",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
				return false;
			}
			if (!FuncionesPHPLocal::ValidarContenido($this->conexion,$datos['CantidadMaxObligatoria'],"NumericoEntero"))
			{
				FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error debe ingresar un campo numérico.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
				return false;
			}
			if (isset($datos['CantidadMaxObligatoria']) && $datos['CantidadMaxObligatoria']<1)
			{
				FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Debe ingresar una cantidad max obligatoria mayor a cero",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
				return false;
			}
			
			if($datos['CantidadMaxObligatoria']>$datos['Cantidad'])
			{
				FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Debe ingresar una cantidad max obligatoria menor o igual a la cantidad",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
				return false;
			}
			
			
		}
		


		
		return true;
	}





}
?>