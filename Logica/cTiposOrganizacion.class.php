<?php 
include(DIR_CLASES_DB."cTiposOrganizacion.db.php");

class cTiposOrganizacion extends cTiposOrganizaciondb
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
			'xIdTipoOrganizacion'=> 0,
			'IdTipoOrganizacion'=> "",
			'xIdTipoOrganizacionExterno'=> 0,
			'IdTipoOrganizacionExterno'=> "",
			'xTipoOrganizacion'=> 0,
			'TipoOrganizacion'=> "",
			'xDescripcion'=> 0,
			'Descripcion'=> "",
			'xIdEnsenanzaExterno'=> 0,
			'IdEnsenanzaExterno'=> "",
			'xEstablecimientoEducativo'=> 0,
			'EstablecimientoEducativo'=> "",
			'xIdEnsenanzaExternoIngreso'=> 0,
			'IdEnsenanzaExternoIngreso'=> "",
			'xIdRamaExterno'=> 0,
			'IdRamaExterno'=> "",
			'xEstado'=> 0,
			'Estado'=> "-1",
			'limit'=> '',
			'orderby'=> "IdTipoOrganizacion DESC"
		);

		if(isset($datos['IdTipoOrganizacion']) && $datos['IdTipoOrganizacion']!="")
		{
			$sparam['IdTipoOrganizacion']= $datos['IdTipoOrganizacion'];
			$sparam['xIdTipoOrganizacion']= 1;
		}
		if(isset($datos['IdTipoOrganizacionExterno']) && $datos['IdTipoOrganizacionExterno']!="")
		{
			$sparam['IdTipoOrganizacionExterno']= $datos['IdTipoOrganizacionExterno'];
			$sparam['xIdTipoOrganizacionExterno']= 1;
		}
		if(isset($datos['TipoOrganizacion']) && $datos['TipoOrganizacion']!="")
		{
			$sparam['TipoOrganizacion']= $datos['TipoOrganizacion'];
			$sparam['xTipoOrganizacion']= 1;
		}
		if(isset($datos['Descripcion']) && $datos['Descripcion']!="")
		{
			$sparam['Descripcion']= $datos['Descripcion'];
			$sparam['xDescripcion']= 1;
		}
		if(isset($datos['IdEnsenanzaExterno']) && $datos['IdEnsenanzaExterno']!="")
		{
			$sparam['IdEnsenanzaExterno']= $datos['IdEnsenanzaExterno'];
			$sparam['xIdEnsenanzaExterno']= 1;
		}
		if(isset($datos['EstablecimientoEducativo']) && $datos['EstablecimientoEducativo']!="")
		{
			$sparam['EstablecimientoEducativo']= $datos['EstablecimientoEducativo'];
			$sparam['xEstablecimientoEducativo']= 1;
		}
		if(isset($datos['IdEnsenanzaExternoIngreso']) && $datos['IdEnsenanzaExternoIngreso']!="")
		{
			$sparam['IdEnsenanzaExternoIngreso']= $datos['IdEnsenanzaExternoIngreso'];
			$sparam['xIdEnsenanzaExternoIngreso']= 1;
		}
		if(isset($datos['IdRamaExterno']) && $datos['IdRamaExterno']!="")
		{
			$sparam['IdRamaExterno']= $datos['IdRamaExterno'];
			$sparam['xIdRamaExterno']= 1;
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

		$oAuditoriasTiposOrganizacion = new cAuditoriasTiposOrganizacion($this->conexion,$this->formato);
		$datos['IdTipoOrganizacion'] = $codigoinsertado;
		$datos['Accion'] = INSERTAR;
		$datos['AltaUsuario'] = $datos['AltaUsuario'];
		$datos['AltaFecha'] = $datos['AltaFecha'];
		if(!$oAuditoriasTiposOrganizacion->InsertarLog($datos,$codigoInsertadolog))
			return false;

		$datos['IdTipoOrganizacion'] =$codigoinsertado;
		if (!$this->Publicar($datos))
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

		$oAuditoriasTiposOrganizacion = new cAuditoriasTiposOrganizacion($this->conexion,$this->formato);
		$datosRegistro['Accion'] = MODIFICACION;
		if(!$oAuditoriasTiposOrganizacion->InsertarLog($datosRegistro,$codigoInsertadolog))
			return false;

		if (!$this->Publicar($datos))
			return false;

		return true;
	}



	public function Eliminar($datos)
	{
		if (!$this->_ValidarEliminar($datos,$datosRegistro))
			return false;

		$oAuditoriasTiposOrganizacion = new cAuditoriasTiposOrganizacion($this->conexion,$this->formato);
		$datosLog =$datosRegistro;
		$datosLog['Accion'] = ELIMINAR;
		if(!$oAuditoriasTiposOrganizacion->InsertarLog($datosLog,$codigoInsertadolog))
			return false;

		$datosmodif['IdTipoOrganizacion'] = $datos['IdTipoOrganizacion'];
		$datosmodif['Estado'] = ELIMINADO;
		if (!$this->ModificarEstado($datosmodif))
			return false;
		return true;
	}



	public function ModificarEstado($datos)
	{
		if (!parent::ModificarEstado($datos))
			return false;
		if (!$this->Publicar($datos))
			return false;

		return true;
	}



	public function Activar($datos)
	{
		$datosmodif['IdTipoOrganizacion'] = $datos['IdTipoOrganizacion'];
		$datosmodif['Estado'] = ACTIVO;
		if (!$this->ModificarEstado($datosmodif))
			return false;
		return true;
	}



	public function DesActivar($datos)
	{
		$datosmodif['IdTipoOrganizacion'] = $datos['IdTipoOrganizacion'];
		$datosmodif['Estado'] = NOACTIVO;
		if (!$this->ModificarEstado($datosmodif))
			return false;
		return true;
	}



	public function Publicar($datos)
	{
		if (!$this->PublicarListadoJson())
			return false;
		return true;
	}



	public function GuardarDatosJson($nombrearchivo,$carpeta,$array)
	{
		$datosJson = FuncionesPHPLocal::ConvertiraUtf8($array);
		$jsonData = json_encode($datosJson);
		if(!is_dir($carpeta)){
			@mkdir($carpeta);
		}
		if(!FuncionesPHPLocal::GuardarArchivo($carpeta,$jsonData,$nombrearchivo.".json"))
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_INF,"Error, al generar el archivo json. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		return true;
	}



	public function EliminarDatosJson($nombrearchivo,$carpeta)
	{
		if(file_exists($carpeta.$nombrearchivo.".json"))
		{
			unlink($carpeta.$nombrearchivo.".json");
		}
		return true;
	}



	public function PublicarListadoJson()
	{
		$nombrearchivo = "tipos_organizacion";
		$carpeta = PUBLICA."json/";
		if(!$this->GerenarArrayDatosJsonListado($array))
			return false;
		if(count($array)>0)
		{
			if(!$this->GuardarDatosJson($nombrearchivo,$carpeta,$array))
				return false;
		}
		else
		{
			if(!$this->EliminarDatosJson($nombrearchivo,$carpeta))
				return false;
		}
		return true;
	}



	public function GerenarArrayDatosJsonListado(&$array)
	{
		$array = array();
		$datos['Estado'] = ACTIVO;
		if(!$this->BusquedaAvanzada($datos,$resultados,$numfilas))
			return false;
		if($numfilas>0)
		{
			while($fila = $this->conexion->ObtenerSiguienteRegistro($resultados))
			{
				$array[$fila['IdTipoOrganizacion']] = $fila;
			}
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
		
		$datosbuscar['IdTipoOrganizacionExterno'] = $datos['IdTipoOrganizacionExterno'];	
		if(!$this->BusquedaAvanzada($datosbuscar,$resultado,$numfilas))
			return false;	
			
		if($numfilas>0)
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error, ya existe el id turno.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
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
		
		$datosbuscar['IdTipoOrganizacionExterno'] = $datos['IdTipoOrganizacionExterno'];	
		if(!$this->BusquedaAvanzada($datosbuscar,$resultado,$numfilas))
			return false;	
			
		if($numfilas>0)
		{
			$fila = $this->conexion->ObtenerSiguienteRegistro($resultado);
			if($fila['IdTipoOrganizacion']!=$datosRegistro['IdTipoOrganizacion'])
			{
				FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error, ya existe el id tipo organización.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
				return false;
			}
		}	
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


		if (!isset($datos['IdTipoOrganizacionExterno']) || $datos['IdTipoOrganizacionExterno']=="")
			$datos['IdTipoOrganizacionExterno']="NULL";

		if (!isset($datos['TipoOrganizacion']) || $datos['TipoOrganizacion']=="")
			$datos['TipoOrganizacion']="NULL";

		if (!isset($datos['Descripcion']) || $datos['Descripcion']=="")
			$datos['Descripcion']="NULL";

		if (!isset($datos['IdEnsenanzaExterno']) || $datos['IdEnsenanzaExterno']=="")
			$datos['IdEnsenanzaExterno']="NULL";

		if (!isset($datos['EstablecimientoEducativo']) || $datos['EstablecimientoEducativo']=="")
			$datos['EstablecimientoEducativo']="NULL";

		if (!isset($datos['IdEnsenanzaExternoIngreso']) || $datos['IdEnsenanzaExternoIngreso']=="")
			$datos['IdEnsenanzaExternoIngreso']="NULL";

		if (!isset($datos['IdRamaExterno']) || $datos['IdRamaExterno']=="")
			$datos['IdRamaExterno']="NULL";

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


		if (!isset($datos['IdTipoOrganizacionExterno']) || $datos['IdTipoOrganizacionExterno']=="")
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Debe ingresar un id tipo organización ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		if (!FuncionesPHPLocal::ValidarContenido($this->conexion,$datos['IdTipoOrganizacionExterno'],"NumericoEntero"))
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error debe ingresar un campo numÃ©rico.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		if (!isset($datos['TipoOrganizacion']) || $datos['TipoOrganizacion']=="")
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Debe ingresar un tipo de organización",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		if (!isset($datos['Descripcion']) || $datos['Descripcion']=="")
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Debe ingresar una descripción",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		if (!isset($datos['IdEnsenanzaExterno']) || $datos['IdEnsenanzaExterno']=="")
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Debe ingresar un id Enseñanza externo",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		if (!FuncionesPHPLocal::ValidarContenido($this->conexion,$datos['IdEnsenanzaExterno'],"NumericoEntero"))
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error debe ingresar un campo numÃ©rico.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		/*if (!isset($datos['EstablecimientoEducativo']) || $datos['EstablecimientoEducativo']=="")
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Debe ingresar un establecimiento educativo",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}*/

		/*if (!isset($datos['IdEnsenanzaExternoIngreso']) || $datos['IdEnsenanzaExternoIngreso']=="")
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Debe ingresar un id Enseñanza externo Ingreso",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}*/
		if (isset($datos['IdEnsenanzaExternoIngreso']) && $datos['IdEnsenanzaExternoIngreso']!="")
		{
			if (!FuncionesPHPLocal::ValidarContenido($this->conexion,$datos['IdEnsenanzaExternoIngreso'],"NumericoEntero"))
			{
				FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error debe ingresar un campo numÃ©rico.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
				return false;
			}
		}
		/*if (!isset($datos['IdRamaExterno']) || $datos['IdRamaExterno']=="")
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Debe ingresar un id rama externo",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}*/
		if (isset($datos['IdRamaExterno']) && $datos['IdRamaExterno']!="")
		{
				if (!FuncionesPHPLocal::ValidarContenido($this->conexion,$datos['IdRamaExterno'],"NumericoEntero"))
				{
					FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error debe ingresar un campo numÃ©rico.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
					return false;
				}
		}
		return true;
	}





}
?>