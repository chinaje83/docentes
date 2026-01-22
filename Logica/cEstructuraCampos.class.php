<?php 
include(DIR_CLASES_DB."cEstructuraCampos.db.php");

class cEstructuraCampos extends cEstructuraCamposdb
{

	protected $conexion;
	protected $formato;
	const SIMBOLOSVALIDOS = array('.', '-', "_");

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
	
	
	public function BusquedaxCodigos($datos,&$resultado,&$numfilas)
	{
		$sparam=array(
			'IdCampo'=> "-1"
		);

		if(isset($datos['IdCampo']) && $datos['IdCampo']!="")
			$sparam['IdCampo']= $datos['IdCampo'];
		
		if (!parent::BusquedaxCodigos($sparam,$resultado,$numfilas))
			return false;
		return true;
	}
	
	public function CaracteresAcepta(){return implode(" | ",self::SIMBOLOSVALIDOS);	}
	public function SimbolosValidos(){return self::SIMBOLOSVALIDOS;}
	
	public function BuscarxNombreCampo($datos,&$resultado,&$numfilas)
	{
		if (!parent::BuscarxNombreCampo($datos,$resultado,$numfilas))
			return false;
		return true;
	}


	public function BuscarTipoCamposxNombresCampo($datos,&$resultado,&$numfilas)
	{
		if (!parent::BuscarTipoCamposxNombresCampo($datos,$resultado,$numfilas))
			return false;
		return true;
	}




	public function BuscarCamposxClientexIdRegistroTipoDocumento($datos,&$resultado,&$numfilas)
	{
		if (!parent::BuscarCamposxClientexIdRegistroTipoDocumento($datos,$resultado,$numfilas))
			return false;
		return true;
	}


	public function BuscarCamposMapeoElasticSearch($datos,&$resultado,&$numfilas)
	{
		if (!parent::BuscarCamposMapeoElasticSearch($datos,$resultado,$numfilas))
			return false;
		return true;
	}

	public function BuscarCamposMapeoElasticSearchxTipoDocumento($datos,&$resultado,&$numfilas)
	{
		if (!parent::BuscarCamposMapeoElasticSearchxTipoDocumento($datos,$resultado,$numfilas))
			return false;
		return true;
	}



	public function BusquedaAvanzada($datos,&$resultado,&$numfilas)
	{
		$sparam=array(
			'xIdCampo'=> 0,
			'IdCampo'=> "",
			'xIdCliente'=> 0,
			'IdCliente'=> "",
			'xNombre'=> 0,
			'Nombre'=> "",
			'xIdObjeto'=> 0,
			'IdObjeto'=> "",
			'xEstado'=> 0,
			'Estado'=> "-1",
			'limit'=> '',
			'orderby'=> "IdCampo DESC"
		);

		if(isset($datos['IdCampo']) && $datos['IdCampo']!="")
		{
			$sparam['IdCampo']= $datos['IdCampo'];
			$sparam['xIdCampo']= 1;
		}
		if(isset($datos['IdCliente']) && $datos['IdCliente']!="")
		{
			$sparam['IdCliente']= $datos['IdCliente'];
			$sparam['xIdCliente']= 1;
		}
		if(isset($datos['Nombre']) && $datos['Nombre']!="")
		{
			$sparam['Nombre']= $datos['Nombre'];
			$sparam['xNombre']= 1;
		}
		if(isset($datos['IdObjeto']) && $datos['IdObjeto']!="")
		{
			$sparam['IdObjeto']= $datos['IdObjeto'];
			$sparam['xIdObjeto']= 1;
		}
		if(isset($datos['Estado']) && $datos['Estado']!="")
		{
			$sparam['Estado']= $datos['Estado'];
			$sparam['xEstado']= 1;
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



	public function EstructuraCamposTiposSP(&$spnombre,&$sparam)
	{
		if (!parent::EstructuraCamposTiposSP($spnombre,$sparam))
			return false;
		return true;
	}



	public function EstructuraCamposTiposSPResult(&$resultado,&$numfilas)
	{
		if (!$this->EstructuraCamposTiposSP($spnombre,$sparam))
			return false;

		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno) )
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al buscar el archivo multimedia por codigo y multimedia. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		return true;
	}



	public function Insertar($datos,&$codigoinsertado)
	{
		if (!$this->_ValidarInsertar($datos))
			return false;

		$this->_SetearNull($datos);
		$datos['AltaFecha']=date("Y-m-d H:i:s");
		$datos['AltaUsuario']=$_SESSION['usuariocod'];
		$datos['UltimaModificacionUsuario']=$_SESSION['usuariocod'];
		$datos['UltimaModificacionFecha']=date("Y-m-d H:i:s");
		$datos['Estado'] = ACTIVO;

		if (!parent::Insertar($datos,$codigoinsertado))
			return false;


		$oEstructuraObjetos = new cEstructuraObjetos($this->conexion,$this->formato);
		if(!$oEstructuraObjetos->BuscarxCodigo($datos,$resultado,$numfilas))
			return false;
		$datosObjeto = $this->conexion->ObtenerSiguienteRegistro($resultado);
		
		if (class_exists($datosObjeto['Clase']))
		{
			$class = new $datosObjeto['Clase']($this->conexion,$this->formato);
			if(method_exists($class,$datosObjeto['Metodo']))
			{
				$datos['IdCampo'] = $codigoinsertado;
				$metodo = $datosObjeto['Metodo'];
				if(!$class->$metodo($datos))
					return false;
			}
		}
		
		$oAuditoriasEstructuraCampos = new cAuditoriasEstructuraCampos($this->conexion,$this->formato);
		$datos['IdCampo'] = $codigoinsertado;
		$datos['Accion'] = INSERTAR;
		$datos['AltaUsuario'] = $datos['AltaUsuario'];
		$datos['AltaFecha'] = $datos['AltaFecha'];
		if(!$oAuditoriasEstructuraCampos->InsertarLog($datos,$codigoInsertadolog))
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

		$oEstructuraObjetos = new cEstructuraObjetos($this->conexion,$this->formato);
		if(!$oEstructuraObjetos->BuscarxCodigo($datosRegistro,$resultado,$numfilas))
			return false;
		$datosObjeto = $this->conexion->ObtenerSiguienteRegistro($resultado);
		
		if (class_exists($datosObjeto['Clase']))
		{
			$class = new $datosObjeto['Clase']($this->conexion,$this->formato);
			if(method_exists($class,$datosObjeto['Metodo']))
			{
				$metodo = $datosObjeto['Metodo'];
				if(!$class->$metodo($datos))
					return false;
			}
		}


		$oAuditoriasEstructuraCampos = new cAuditoriasEstructuraCampos($this->conexion,$this->formato);
		$datosRegistro['Accion'] = MODIFICACION;
		if(!$oAuditoriasEstructuraCampos->InsertarLog($datosRegistro,$codigoInsertadolog))
			return false;


		return true;
	}



	public function Eliminar($datos)
	{
		if (!$this->_ValidarEliminar($datos,$datosRegistro))
			return false;

		$oAuditoriasEstructuraCampos = new cAuditoriasEstructuraCampos($this->conexion,$this->formato);
		$datosLog =$datosRegistro;
		$datosLog['Accion'] = ELIMINAR;
		if(!$oAuditoriasEstructuraCampos->InsertarLog($datosLog,$codigoInsertadolog))
			return false;

		if (!parent::Eliminar($datos))
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
		$datosmodif['IdCampo'] = $datos['IdCampo'];
		$datosmodif['Estado'] = ACTIVO;
		if (!$this->ModificarEstado($datosmodif))
			return false;
		return true;
	}



	public function DesActivar($datos)
	{
		$datosmodif['IdCampo'] = $datos['IdCampo'];
		$datosmodif['Estado'] = NOACTIVO;
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


		if (!isset($datos['NombreCampo']) || $datos['NombreCampo']=="")
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Debe ingresar un nombre de campo",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		if(!ctype_alnum(str_replace(self::SIMBOLOSVALIDOS, '', $datos['NombreCampo']))) 
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Debe ingresar un nombre de campo, con letras, numeros, se aceptan los siguientes caracteres: ".implode(",",self::SIMBOLOSVALIDOS),array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		if (!isset($datos['IdObjeto']) || $datos['IdObjeto']=="")
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Debe ingresar un id de objeto de campo",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		if (!FuncionesPHPLocal::ValidarContenido($this->conexion,$datos['IdObjeto'],"NumericoEntero"))
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error debe ingresar un campo numérico.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		if (!isset($datos['IdTipoCampo']) || $datos['IdTipoCampo']=="")
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Debe ingresar un id de tipo de campo",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		if (!FuncionesPHPLocal::ValidarContenido($this->conexion,$datos['IdTipoCampo'],"NumericoEntero"))
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error debe ingresar un campo numérico.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}



		if (!$this->conexion->TraerCampo('EstructuraObjetos','IdObjeto',array('IdObjeto='.$datos['IdObjeto']),$dato,$numfilas,$errno))
			return false;


		if ($numfilas!=1)
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error debe ingresar un objeto valido.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		
		
		if (!$this->conexion->TraerCampo('EstructuraCamposTipos','IdTipoCampo',array('IdTipoCampo='.$datos['IdTipoCampo']),$dato,$numfilas,$errno))
			return false;


		if ($numfilas!=1)
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error debe ingresar un tipo de campo valido.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		

		if (!$this->BuscarxNombreCampo($datos,$resultado,$numfilas))
			return false;
		if ($numfilas>0)
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error, ya existe un nombre de campo igual.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		$oCamposReservados = new cCamposReservados($this->conexion,$this->formato);
		if (!$oCamposReservados->BuscarCamposReservadosxNombreCampo($datos,$resultado,$numfilas))
			return false;
		if ($numfilas>0)
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error, existe un nombre de campo reservado.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
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


		if (!isset($datos['Nombre']) || $datos['Nombre']=="")
			$datos['Nombre']="NULL";

		if (!isset($datos['LabelDefault']) || $datos['LabelDefault']=="")
			$datos['LabelDefault']="NULL";

		if (!isset($datos['NombreCampo']) || $datos['NombreCampo']=="")
			$datos['NombreCampo']="NULL";

		if (!isset($datos['IdTipoCampo']) || $datos['IdTipoCampo']=="")
			$datos['IdTipoCampo']="NULL";

		if (!isset($datos['CantidadMaximaCampo']) || $datos['CantidadMaximaCampo']=="")
			$datos['CantidadMaximaCampo']="NULL";

		if (!isset($datos['CantidadDecimales']) || $datos['CantidadDecimales']=="")
			$datos['CantidadDecimales']="0";
			
			

		return true;
	}



	private function _ValidarDatosVacios($datos)
	{


		if (!isset($datos['Nombre']) || $datos['Nombre']=="")
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Debe ingresar un nombre",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}



		if (isset($datos['CantidadMaximaCampo']) && $datos['CantidadMaximaCampo']!="")
		{
			if (!FuncionesPHPLocal::ValidarContenido($this->conexion,$datos['CantidadMaximaCampo'],"NumericoEntero"))
			{
				FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error debe ingresar un campo numérico.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
				return false;
			}
		}

		if (isset($datos['CantidadDecimales']) && $datos['CantidadDecimales']!="")
		{
			if (!FuncionesPHPLocal::ValidarContenido($this->conexion,$datos['CantidadDecimales'],"NumericoEntero"))
			{
				FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error debe ingresar un campo numérico en decimales.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
				return false;
			}
		}

		
		return true;
	}





}
?>