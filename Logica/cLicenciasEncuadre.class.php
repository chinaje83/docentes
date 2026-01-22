<?php 
include(DIR_CLASES_DB."cLicenciasEncuadre.db.php");

class cLicenciasEncuadre extends cLicenciasEncuadredb
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
	
	public function BuscarReglas()
	{
		$file = PUBLICA."json/licencia_encuadre.json";
		$datosReglas=array();
		if (file_exists($file))
		{
			$archivo = file_get_contents($file);
			$datosReglas = json_decode($archivo,1);
			$datosReglas = FuncionesPHPLocal::DecodificarUtf8($datosReglas);
			return $datosReglas;
		}
		return true;
	}
	
	public function BuscarEncuadreHost(&$vecReglas,$datosEnc,&$encDoc, &$encAux, &$descripcionRegla="")
	{
	
		if (!isset($datosEnc['StatusLicencia']) || $datosEnc['StatusLicencia']=="")
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_INF,"Error, debe ingresar el estado de la licencia. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		
		switch($datosEnc['StatusLicencia'])
		{
			case "JUNTA":	
			case "VISITA":	
			case "VISITACMZ":
				$encDoc="114ISLM";
				$encAux="049ISLM";
				return true;
				break;

		}
		if ($datosEnc["Revista"]=="TI")
			$datosEnc["Revista"]="T";

		if ($datosEnc["Familia"]=="true")
		{
			switch($datosEnc["Revista"])
			{
				case "T":
					$encDoc=ENCUADRELICENCIADOCFAMILIART;
					break;
				case "P":
					$encDoc=ENCUADRELICENCIADOCFAMILIARP;
					break;
				case "S":
					$encDoc=ENCUADRELICENCIADOCFAMILIARS;
					break;
				default:
					$encDoc="";
					break;
			}
			//si es familiar, suplente y tiene más de 1 año de antiguedad --> 114F1 
			if ($datosEnc["antDoc"]>=1 && $encDoc==ENCUADRELICENCIADOCFAMILIARS)//si tengo más de 5 año de antiguedad
				$encDoc=ENCUADRELICENCIADOCFAMILIART;
				
			$encAux=ENCUADRELICENCIANODOCFAMILIAR;
			return true;
		}
		else
		{

			//verifico si no es la misma reglas paara todas las revistas
			$revista=$datosEnc["Revista"];
			if (!isset($vecReglas[$encDoc][$revista]))
			{
				$revista=0;
			}
			if (isset($vecReglas[$encDoc][$revista]))
			{
		
				//si no esta definido el sexo en la regla, busco la regla de sexo=0
				if (isset($vecReglas[$encDoc][$revista][$datosEnc["Sexo"]]))
					$reglas_cargo=$vecReglas[$encDoc][$revista][$datosEnc["Sexo"]];
				else
					$reglas_cargo=$vecReglas[$encDoc][$revista][0];	

				//por caja regla encontrada
				foreach($reglas_cargo as $regla)
				{
					if (!isset($regla["AntiguedadDesde"]) || ($regla["AntiguedadDesde"]!="" && $datosEnc["antDoc"]<$regla["AntiguedadDesde"]))
						$encontro=false;
					else
					{
						if (!isset($regla["AntiguedadHasta"])|| ($regla["AntiguedadHasta"]!=""  && $datosEnc["antDoc"]>$regla["AntiguedadHasta"]))		
							$encontro=false;
						else
						{
							$encontro=true;
							$regla_cargo_seleccionada=$regla;
							$encDoc=$regla_cargo_seleccionada["CodigoEncuadreHost"];
							//$encAux=$encAux["antAdm"];//$regla_cargo_seleccionada["CodigoEncuadreHost"];
							$descripcionRegla=$regla_cargo_seleccionada["DescripcionEncuadre"];
							return true;
							break;
						}
					}
				}
			}
		}
		return false;
			
	}



	public function BusquedaAvanzada($datos,&$resultado,&$numfilas)
	{
		$sparam=array(
			'xIdLicenciaEncuadre'=> 0,
			'IdLicenciaEncuadre'=> "",
			'xCodigoEncuadre'=> 0,
			'CodigoEncuadre'=> "",
			'xCodigoRevista'=> 0,
			'CodigoRevista'=> "",
			'xSexo'=> 0,
			'Sexo'=> "",
			'xDescripcionEncuadre'=> 0,
			'DescripcionEncuadre'=> "",
			'xCodigoEncuadreHost'=> 0,
			'CodigoEncuadreHost'=> "",
			
			'xpxAntiguedadDesde'=> 0,
			'AntiguedadDesde'=> "",
			'xAntiguedadHasta'=> 0,
			'AntiguedadHasta'=> "",
			
			'limit'=> '',
			'orderby'=> "IdLicenciaEncuadre DESC"
		);

		if(isset($datos['IdLicenciaEncuadre']) && $datos['IdLicenciaEncuadre']!="")
		{
			$sparam['IdLicenciaEncuadre']= $datos['IdLicenciaEncuadre'];
			$sparam['xIdLicenciaEncuadre']= 1;
		}
		if(isset($datos['CodigoEncuadre']) && $datos['CodigoEncuadre']!="")
		{
			$sparam['CodigoEncuadre']= $datos['CodigoEncuadre'];
			$sparam['xCodigoEncuadre']= 1;
		}
		if(isset($datos['CodigoRevista']) && $datos['CodigoRevista']!="")
		{
			$sparam['CodigoRevista']= $datos['CodigoRevista'];
			$sparam['xCodigoRevista']= 1;
		}
		if(isset($datos['Sexo']) && $datos['Sexo']!="")
		{
			$sparam['Sexo']= $datos['Sexo'];
			$sparam['xSexo']= 1;
		}
		if(isset($datos['DescripcionEncuadre']) && $datos['DescripcionEncuadre']!="")
		{
			$sparam['DescripcionEncuadre']= $datos['DescripcionEncuadre'];
			$sparam['xDescripcionEncuadre']= 1;
		}
		if(isset($datos['CodigoEncuadreHost']) && $datos['CodigoEncuadreHost']!="")
		{
			$sparam['CodigoEncuadreHost']= $datos['CodigoEncuadreHost'];
			$sparam['xCodigoEncuadreHost']= 1;
		}
		
		if(isset($datos['AntiguedadDesde']) && $datos['AntiguedadDesde']!="")
		{
			$sparam['AntiguedadDesde']= $datos['AntiguedadDesde'];
			$sparam['xAntiguedadDesde']= 1;
		}
		if(isset($datos['AntiguedadHasta']) && $datos['AntiguedadHasta']!="")
		{
			$sparam['AntiguedadHasta']= $datos['AntiguedadHasta'];
			$sparam['xAntiguedadHasta']= 1;
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

		$oAuditoriasLicenciasEncuadre = new cAuditoriasLicenciasEncuadre($this->conexion,$this->formato);
		$datos['IdLicenciaEncuadre'] = $codigoinsertado;
		$datos['Accion'] = INSERTAR;
		$datos['AltaUsuario'] = $datos['AltaUsuario'];
		$datos['AltaFecha'] = $datos['AltaFecha'];
		if(!$oAuditoriasLicenciasEncuadre->InsertarLog($datos,$codigoInsertadolog))
			return false;
		
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

		$oAuditoriasLicenciasEncuadre = new cAuditoriasLicenciasEncuadre($this->conexion,$this->formato);
		$datosRegistro['Accion'] = MODIFICACION;
		if(!$oAuditoriasLicenciasEncuadre->InsertarLog($datosRegistro,$codigoInsertadolog))
			return false;
		
		if (!$this->Publicar($datos))
			return false;

		return true;
	}



	public function Eliminar($datos)
	{
		if (!$this->_ValidarEliminar($datos,$datosRegistro))
			return false;

		$oAuditoriasLicenciasEncuadre = new cAuditoriasLicenciasEncuadre($this->conexion,$this->formato);
		$datosLog =$datosRegistro;
		$datosLog['Accion'] = ELIMINAR;
		if(!$oAuditoriasLicenciasEncuadre->InsertarLog($datosLog,$codigoInsertadolog))
			return false;

		$datosmodif['IdLicenciaEncuadre'] = $datos['IdLicenciaEncuadre'];
		$datosmodif['Estado'] = ELIMINADO;
		if (!$this->ModificarEstado($datosmodif))
			return false;
		
		if (!$this->Publicar($datos))
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
		$datosmodif['IdLicenciaEncuadre'] = $datos['IdLicenciaEncuadre'];
		$datosmodif['Estado'] = ACTIVO;
		if (!$this->ModificarEstado($datosmodif))
			return false;
		return true;
	}



	public function DesActivar($datos)
	{
		$datosmodif['IdLicenciaEncuadre'] = $datos['IdLicenciaEncuadre'];
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
		$nombrearchivo = "licencia_encuadre";
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
				if ($fila['Sexo']=="")
					$fila['Sexo']="0";
				if ($fila['CodigoRevista']=="")
					$fila['CodigoRevista']="0";
				$array[$fila['CodigoEncuadre']][$fila['CodigoRevista']][$fila['Sexo']][]=$fila;
				/*
				$array[$fila['CodigoEncuadre']][$fila['CodigoRevista']][$fila['Sexo']]["AntiguedadDesde"] = $fila["AntiguedadDesde"] ;
				$array[$fila['CodigoEncuadre']][$fila['CodigoRevista']][$fila['Sexo']]["AntiguedadHasta"] = $fila["AntiguedadHasta"] ;
				$array[$fila['CodigoEncuadre']][$fila['CodigoRevista']][$fila['Sexo']]["DescripcionEncuadre"] = $fila["DescripcionEncuadre"] ;
				$array[$fila['CodigoEncuadre']][$fila['CodigoRevista']][$fila['Sexo']]["CodigoEncuadreHost"] = $fila["CodigoEncuadreHost"] ;
				*/
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

		return true;
	}



	private function _ValidarModificar($datos,&$datosRegistro)
	{
		if (!$this->BuscarxCodigo($datos,$resultado,$numfilas))
			return false;

		if ($numfilas!=1)
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error debe ingresar un cÃ³digo valido.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
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
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error debe ingresar un cÃ³digo valido.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		$datosRegistro = $this->conexion->ObtenerSiguienteRegistro($resultado);
		return true;
	}



	private function _SetearNull(&$datos)
	{


		if (!isset($datos['CodigoEncuadre']) || $datos['CodigoEncuadre']=="")
			$datos['CodigoEncuadre']="NULL";


		if (!isset($datos['CodigoRevista']) || $datos['CodigoRevista']=="")
			$datos['CodigoRevista']="NULL";

		if (!isset($datos['Sexo']) || $datos['Sexo']=="")
			$datos['Sexo']="NULL";

		if (!isset($datos['AntiguedadDesde']) || $datos['AntiguedadDesde']=="")
			$datos['AntiguedadDesde']="NULL";

		if (!isset($datos['AntiguedadHasta']) || $datos['AntiguedadHasta']=="")
			$datos['AntiguedadHasta']="NULL";

		if (!isset($datos['DescripcionEncuadre']) || $datos['DescripcionEncuadre']=="")
			$datos['DescripcionEncuadre']="NULL";

		if (!isset($datos['Particularidades']) || $datos['Particularidades']=="")
			$datos['Particularidades']="NULL";

		if (!isset($datos['CodigoEncuadreHost']) || $datos['CodigoEncuadreHost']=="")
			$datos['CodigoEncuadreHost']="NULL";

		if (!isset($datos['AltaFecha']) || $datos['AltaFecha']=="")
			$datos['AltaFecha']="NULL";

		if (!isset($datos['AltaUsuario']) || $datos['AltaUsuario']=="")
			$datos['AltaUsuario']="NULL";

		if (!isset($datos['UltModificacionUsuario']) || $datos['UltModificacionUsuario']=="")
			$datos['UltModificacionUsuario']="NULL";

		if (!isset($datos['UltimaModificacionFecha']) || $datos['UltimaModificacionFecha']=="")
			$datos['UltimaModificacionFecha']="NULL";
		return true;
	}



	private function _ValidarDatosVacios($datos)
	{


		if (!isset($datos['CodigoEncuadre']) || $datos['CodigoEncuadre']=="")
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Debe ingresar un código de encuadre",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
/*
		if (!isset($datos['CodigoRevista']) || $datos['CodigoRevista']=="")
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Debe ingresar un código de revista",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		if (!isset($datos['Sexo']) || $datos['Sexo']=="")
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Debe ingresar un sexo",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
*/
		/*if (!isset($datos['AntiguedadDesde']) || $datos['AntiguedadDesde']=="")
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Debe ingresar una antigüedad desde",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}*/

		if (isset($datos['AntiguedadDesde']) && $datos['AntiguedadDesde']!="")
		{
			if (!FuncionesPHPLocal::ValidarContenido($this->conexion,$datos['AntiguedadDesde'],"Numerico3Decimales"))
			{
				FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error debe ingresar un campo numérico de hasta 3 decimales separado por punto.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
				return false;
			}
		}

		/*if (!isset($datos['AntiguedadHasta']) || $datos['AntiguedadHasta']=="")
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Debe ingresar una antigüedad hasta",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}*/

		if (isset($datos['AntiguedadHasta']) && $datos['AntiguedadHasta']!="")
		{
			if (!FuncionesPHPLocal::ValidarContenido($this->conexion,$datos['AntiguedadHasta'],"Numerico3Decimales"))
			{
				FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error debe ingresar un campo numérico de hasta 3 decimales separado por punto.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
				return false;
			}
		}

		if (!isset($datos['DescripcionEncuadre']) || $datos['DescripcionEncuadre']=="")
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Debe ingresar una descripción de encuadre",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		/*if (!isset($datos['Particularidades']) || $datos['Particularidades']=="")
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Debe ingresar una particularidad",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}*/

		/*if (!isset($datos['CodigoEncuadreHost']) || $datos['CodigoEncuadreHost']=="")
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Debe ingresar un código de encuadre host",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}*/

		
		return true;
	}





}
?>