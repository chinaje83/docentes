<?php 
include(DIR_CLASES_DB."cModulosDashboard.db.php");

class cModulosDashboard extends cModulosDashboarddb
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
			'xIdModulo'=> 0,
			'IdModulo'=> "-1",
			'xIdRol'=> 0,
			'IdRol'=> "-1",
			'xNombre'=> 0,
			'Nombre'=> "",
			'xArchivo'=> 0,
			'Archivo'=> "",
			'xEsDefault'=> 0,
			'EsDefault'=> "",
			'xEstado'=> 0,
			'Estado'=> "-1",
			'limit'=> '',
			'orderby'=> "IdModulosDashboard DESC"
		);

		if(isset($datos['IdModulo']) && $datos['IdModulo']!="")
		{
			$sparam['IdModulo']= $datos['IdModulo'];
			$sparam['xIdModulo']= 1;
		}
		if(isset($datos['IdRol']) && $datos['IdRol']!="")
		{
			$sparam['IdRol']= $datos['IdRol'];
			$sparam['xIdRol']= 1;
		}
		if(isset($datos['Nombre']) && $datos['Nombre']!="")
		{
			$sparam['Nombre']= $datos['Nombre'];
			$sparam['xNombre']= 1;
		}
		if(isset($datos['Archivo']) && $datos['Archivo']!="")
		{
			$sparam['Archivo']= $datos['Archivo'];
			$sparam['xArchivo']= 1;
		}
		if(isset($datos['EsDefault']) && $datos['EsDefault']!="")
		{
			if(mb_strtolower($datos['EsDefault']) == 'sí' || mb_strtolower($datos['EsDefault']) == 'si')
				$datos['EsDefault'] = 1;
			elseif(mb_strtolower($datos['EsDefault']) == 'no')
				$datos['EsDefault'] = 0;
				
			$sparam['EsDefault']= $datos['EsDefault'];
			$sparam['xEsDefault']= 1;
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



	public function ModulosSP(&$spnombre,&$sparam)
	{
		if (!parent::ModulosSP($spnombre,$sparam))
			return false;
		return true;
	}



	public function ModulosSPResult(&$resultado,&$numfilas)
	{
		if (!$this->ModulosSP($spnombre,$sparam))
			return false;

		if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno) )
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error al buscar el archivo multimedia por codigo y multimedia. ",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		return true;
	}



	public function RolesSP(&$spnombre,&$sparam)
	{
		if (!parent::RolesSP($spnombre,$sparam))
			return false;
		return true;
	}



	public function RolesSPResult(&$resultado,&$numfilas)
	{
		if (!$this->RolesSP($spnombre,$sparam))
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
		$carpeta = DOCUMENT_ROOT."modulos_dashboard";
		if(!is_dir($carpeta))
			mkdir($carpeta);
		if(!is_dir("$carpeta/form"))
			mkdir("$carpeta/form");
		if(!is_dir("$carpeta/html"))
			mkdir("$carpeta/html");
		$Nombre = utf8_encode($datos['Nombre']);
		$str = 	"<div class='tap_modules clearfix' id='module_<?php echo \$vars['IdUsuarioDashboard']?>'><?php\n\techo \$vars['htmledit'];?>\n\t<div class='x_panel tile'>\n\t\t<div class='x_title'>\n\t\t\t<h2>$Nombre</h2>\n\t\t\t\t\n\t\t\t<div class='clearfix'></div>\n\t\t</div>\n\t\t<div class='x_content' style='display: block;'>\n\t\t\t<!-- Coloque aquí el contenido -->\n\t\t</div>\n\t</div>\n</div>";
		$file = "$carpeta/form/{$datos['Archivo']}.php";
		if(!file_exists($file))
		{
			$handle = fopen($file,"wb");
			fwrite($handle,$str);
			fclose($handle);
		}
		$file = "$carpeta/html/{$datos['Archivo']}.php";
		if(!file_exists($file))
		{
			$handle = fopen($file,"wb");
			fwrite($handle,$str);
			fclose($handle);
		}
		$datos['Estado'] = ACTIVO;
		$this->_SetearNull($datos);
		if (!parent::Insertar($datos,$codigoinsertado))
			return false;
		
		$datosins['IdModulosDashboard'] = $codigoinsertado;
		
		foreach($datos['IdRol'] as $rol)
		{
			$datosins['IdRol'] = $rol;
			if(!$this->InsertarRoles($datosins))
				return false;
		}
		
		return true;
	}



	public function InsertarRoles($datos)
	{
		if(!$this->_ValidarInsertarRoles($datos))
			return false;
		if (!parent::InsertarRoles($datos))
			return false;
		return true;
	}



	public function Modificar($datos)
	{
		if (!$this->_ValidarModificar($datos))
			return false;
		$carpeta = DOCUMENT_ROOT."modulos_dashboard";
		if(!is_dir($carpeta))
			mkdir($carpeta);
		$file = "$carpeta/{$datos['Archivo']}.php";
		if(!file_exists($file))
		{
			$handle = fopen($file,"wb");
			fwrite($handle,utf8_encode("<!doctype html>\n<html>\n\t<head>\n\t\t<meta charset='utf-8'>\n\t\t<title>{$datos['Nombre']}</title>\n\t</head>\n\t\n\t<body>\n\t\t<!-- Coloque aquí el contenido -->\n\t</body>\n</html>"));
			fclose($handle);
		}

		$this->_SetearNull($datos);
		
		if (!parent::Modificar($datos))
			return false;
		
		$datosins['IdModulosDashboard'] = $datos['IdModulosDashboard'];
		
		if (!$this->EliminarRoles($datosins))
			return false;
			
		foreach($datos['IdRol'] as $rol)
		{
			$datosins['IdRol'] = $rol;
			if(!$this->InsertarRoles($datosins))
				return false;
		}

		return true;
	}



	public function Eliminar($datos)
	{
		if (!$this->_ValidarEliminar($datos))
			return false;

		$datosmodif['IdModulosDashboard'] = $datos['IdModulosDashboard'];
		$datosmodif['Estado'] = ELIMINADO;
		if (!$this->ModificarEstado($datosmodif))
			return false;
		return true;
	}


	public function EliminarRoles($datos)
	{

		if (!parent::EliminarRoles($datos))
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
		$datosmodif['IdModulosDashboard'] = $datos['IdModulosDashboard'];
		$datosmodif['Estado'] = ACTIVO;
		if (!$this->ModificarEstado($datosmodif))
			return false;
		return true;
	}



	public function DesActivar($datos)
	{
		$datosmodif['IdModulosDashboard'] = $datos['IdModulosDashboard'];
		$datosmodif['Estado'] = NOACTIVO;
		if (!$this->ModificarEstado($datosmodif))
			return false;
		return true;
	}



	public function BuscarModulos($datos,&$array)
	{
		if(empty($array))
			$array = array();
		
		unset($datos['EsDefault']);
		$datosBuscar['IdUsuario'] = $datos['IdUsuario'];	
		$datos['IdModulo'] = $this->traerModulosxUsuario($datosBuscar);
		$datos['orderby'] = "IdModulosDashboard ASC";
		if(!$this->BusquedaAvanzada($datos,$resultado,$numfilas))
			return false;
		
		while($fila = $this->conexion->ObtenerSiguienteRegistro($resultado))
		{
			$array[$fila['IdModulo']]['Nombre'] = $fila['IdModulodesc'];
			$array[$fila['IdModulo']]['modulo'][$fila['IdModulosDashboard']] = $fila;
			
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



	private function _ValidarInsertarRoles($datos)
	{
		if (!FuncionesPHPLocal::ValidarContenido($this->conexion,$datos['IdRol'],"NumericoEntero"))
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error debe ingresar un campo numÃ©rico.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		
	
		if (!$this->conexion->TraerCampo('Roles','IdRol',array('IdRol='.$datos['IdRol']),$dato,$numfilas,$errno))
			return false;


		if ($numfilas!=1)
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error debe ingresar un campo valido.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		return true;
	}



	private function _ValidarModificar($datos)
	{
		if (!$this->BuscarxCodigo($datos,$resultado,$numfilas))
			return false;

		if ($numfilas!=1)
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error debe ingresar un código valido.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		if (!$this->_ValidarDatosVacios($datos))
			return false;

		return true;
	}



	private function _ValidarEliminar($datos)
	{
		if (!$this->BuscarxCodigo($datos,$resultado,$numfilas))
			return false;

		if ($numfilas!=1)
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error debe ingresar un código valido.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		return true;
	}



	private function _SetearNull(&$datos)
	{
		if (!isset($datos['IdModulo']) || $datos['IdModulo']=="")
			$datos['IdModulo']="NULL";

		if (!isset($datos['IdRol']) || $datos['IdRol']=="")
			$datos['IdRol']="NULL";

		if (!isset($datos['Archivo']) || $datos['Archivo']=="")
			$datos['Archivo']="NULL";

		if (!isset($datos['EsDefault']) || $datos['EsDefault']=="")
			$datos['EsDefault']="NULL";

		return true;
	}



	private function _ValidarDatosVacios($datos)
	{


		/*if (!isset($datos['IdModulo']) || $datos['IdModulo']=="")
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Debe ingresar un m�dulo",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		if (!FuncionesPHPLocal::ValidarContenido($this->conexion,$datos['IdModulo'],"NumericoEntero"))
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error debe ingresar un campo num�rico.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}*/

		if (!isset($datos['Nombre']) || $datos['Nombre']=="")
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Debe ingresar un nombre de modulo de dashboard",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		if (!isset($datos['Archivo']) || $datos['Archivo']=="")
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Debe ingresar un nombre de archivo",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		if (!isset($datos['EsDefault']) || $datos['EsDefault']=="")
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Debe seccionar si es default",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		if (!isset($datos['IdModulo']) && $datos['IdModulo']!=""){

			if (!$this->conexion->TraerCampo('Modulos','IdModulo',array('IdModulo='.$datos['IdModulo']),$dato,$numfilas,$errno))
				return false;
	
	
			if ($numfilas!=1)
			{
				FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error debe ingresar un campo valido.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
				return false;
			}
		}
		return true;
	}
	
	
	protected function traerModulosxUsuario($datos)
	{
		if(!parent::traerModulosxUsuarioDB($datos,$resultado,$numfilas))
			return false;
		
		if($numfilas != 1)
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"No se encuentran modulos.",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		
		$fila = $this->conexion->ObtenerSiguienteRegistro($resultado);
		
		return $fila['IdModulo'];
	}





}
?>