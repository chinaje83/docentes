<?php 
include(DIR_CLASES_DB."cAccesos.db.php");

class cAccesos extends cAccesosdb
{

	protected $conexion;
	protected $formato;

	function __construct($conexion,$formato=FMT_TEXTO){
		$this->conexion = &$conexion;
		$this->formato = &$formato;
		parent::__construct();
	}

	function __destruct(){parent::__destruct();}

	public function setAcceso($datos)
	{
		if(!$this->Insertar($datos))
			return false;
	
		return true;
	}

	public function Insertar($datos)
	{
		if (!$this->_ValidarInsertar($datos))
			return false;
		
		$datos['UrlPagina'] = $_SERVER['PHP_SELF'];
		
		
		
		$datos['Ip'] = $_SERVER['REMOTE_ADDR'];
		if(isset($_SERVER['HTTP_X_FORWARDED_FOR']))
			 $datos['Ip'] =$_SERVER['HTTP_X_FORWARDED_FOR'];
		
	
		
		/* $meta = unserialize(file_get_contents('http://www.geoplugin.net/php.gp?ip=181.31.1.131'));
		$meta = unserialize(file_get_contents('http://www.geoplugin.net/php.gp?ip='.$datos['Ip']));
		
		if(isset($meta['geoplugin_status']) && $meta['geoplugin_status']=="200")
		{
			$datos['Lat'] = $meta['geoplugin_latitude'];
			$datos['Lng'] = $meta['geoplugin_longitude'];
		}
		*/
		
		$datos['SistemaOperativo'] = FuncionesPHPLocal::ObtenerSistemaOperativo();
		$datos['Navegador'] = $_SERVER['HTTP_USER_AGENT'];
		
		$datos['IdUsuario']=$_SESSION['usuariocod'];
		$datos['FechaAcceso']=date("Y-m-d H:i:s");
		$datos['DatosAnexos'] = $this->_DatosAnexos($datos);
		$this->_SetearNull($datos);
		
		if (!parent::Insertar($datos))
			return false;

		return true;
	}


//-----------------------------------------------------------------------------------------
//FUNCIONES PRIVADAS
//-----------------------------------------------------------------------------------------

	private function _ValidarInsertar($datos)
	{
		return true;
	}
	
	private function _SetearNull(&$datos)
	{


		if (!isset($datos['UrlPagina']) || $datos['UrlPagina']=="")
			$datos['UrlPagina']="NULL";

		if (!isset($datos['Modulo']) || $datos['Modulo']=="")
			$datos['Modulo']="NULL";
		
		if (!isset($datos['ModuloCte']) || $datos['ModuloCte']=="")
			$datos['ModuloCte']="NULL";

		if (!isset($datos['IdUsuario']) || $datos['IdUsuario']=="")
			$datos['IdUsuario']="NULL";

		if (!isset($datos['Ip']) || $datos['Ip']=="")
			$datos['Ip']="NULL";

		if (!isset($datos['SistemaOperativo']) || $datos['SistemaOperativo']=="")
			$datos['SistemaOperativo']="NULL";
		
		if (!isset($datos['Navegador']) || $datos['Navegador']=="")
			$datos['Navegador']="NULL";

		if (!isset($datos['Lat']) || $datos['Lat']=="")
			$datos['Lat']="NULL";
		
		if (!isset($datos['Lng']) || $datos['Lng']=="")
			$datos['Lng']="NULL";
		
		if (!isset($datos['DatosAnexos']) || $datos['DatosAnexos']=="")
			$datos['DatosAnexos']="NULL";
		
		if (!isset($datos['FechaAcceso']) || $datos['FechaAcceso']=="")
			$datos['FechaAcceso']="NULL";
		return true;
	}
	
	
	private function _DatosAnexos($datos)
	{
		$datosDevolver = $datos;
				
		$this->_UnsetDatos($datosDevolver);
		
		$array= array();
		foreach($datosDevolver as $key=>$value)
				$array[] = $key.":".$value;
		
		return implode("||",$array);
	}
	
	
	private function _UnsetDatos(&$datos)
	{
		
		if (isset($datos['UrlPagina']))
			unset($datos['UrlPagina']);

		if (isset($datos['Modulo']))
			unset($datos['Modulo']);
		
		if (isset($datos['ModuloCte']))
			unset($datos['ModuloCte']);

		if (isset($datos['IdUsuario']))
			unset($datos['IdUsuario']);

		if (isset($datos['Ip']))
			unset($datos['Ip']);

		if (isset($datos['SistemaOperativo']))
			unset($datos['SistemaOperativo']);
		
		if (isset($datos['Navegador']))
			unset($datos['Navegador']);

		if (isset($datos['Lat']))
			unset($datos['Lat']);
		
		if (isset($datos['Lng']))
			unset($datos['Lng']);
		
		if (isset($datos['DatosAnexos']))
			unset($datos['DatosAnexos']);
		
		if (isset($datos['FechaAcceso']))
			unset($datos['FechaAcceso']);
		
		
	}
}
?>