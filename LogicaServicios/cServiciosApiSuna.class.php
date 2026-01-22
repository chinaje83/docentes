<?php 
class cServiciosApiSuna 
{

	protected $oCurl;
	protected $conexion;
	protected $error;
	protected $Utf8;
	
	protected $MemCache;

	const MemCacheExpire = 86400;// 1 dia
	
	
	function __construct($conexion){
		$this->conexion = &$conexion;
 		$this->error = array();
		$this->oCurl = new CurlBigtree($this->conexion);
		$this->Utf8 = false;
		
	}
	public function __destruct() {	
		$this->oCurl->CloseCurl();
		unset($this->oCurl);
    } 	

	public function getCurl()
	{
		return 	$this->oCurl;
	}
	
	public function getToken()
	{
		return 	$this->token;
	}


	public function CodificarUtf8()
	{
		$this->Utf8 = true;
	}
	
	
	public function getLicencias($datos)
	{
		$url = "v1/licencias";
		
		$urlAnexa = "";
		if (isset($datos['from']) && $datos['from']!=="" && is_numeric($datos['from']))
		{
			$urlAnexa.="?from=".$datos['from'];
			if (isset($datos['size']) && $datos['size']!=="" && is_numeric($datos['size']))
				$urlAnexa.="&size=".$datos['size'];
		}
		
		if (isset($datos['Documento']) && $datos['Documento']!="")
		{
			if ($urlAnexa=="")
				$urlAnexa="?";
			else
				$urlAnexa.="&";
			
			$urlAnexa.="DOCUMENTO=".$datos['Documento'];
		}
		
		if (isset($datos['Id']) && $datos['Id']!="")
		{
			if ($urlAnexa=="")
				$urlAnexa="?";
			else
				$urlAnexa.="&";
			
			$urlAnexa.="ID=".$datos['Id'];
		}
		
		if (isset($datos['IdNovedad']) && $datos['IdNovedad']!="")
		{
			if ($urlAnexa=="")
				$urlAnexa="?";
			else
				$urlAnexa.="&";
			
			$urlAnexa.="IDNOVEDAD=".$datos['IdNovedad'];
		}
		
		if (isset($datos['RegimenEstatutario']) && $datos['RegimenEstatutario']!="")
		{
			if ($urlAnexa=="")
				$urlAnexa="?";
			else
				$urlAnexa.="&";
			
			$urlAnexa.="REGESTAT=".$datos['RegimenEstatutario'];
		}
		if (isset($datos['Estado']) && $datos['Estado']!="")
		{
			if ($urlAnexa=="")
				$urlAnexa="?";
			else
				$urlAnexa.="&";
			
			$urlAnexa.="ESTADO=".$datos['Estado'];
		}
		if (isset($datos['ProcesadoenSuna']) && $datos['ProcesadoenSuna']!="")
		{
			if ($urlAnexa=="")
				$urlAnexa="?";
			else
				$urlAnexa.="&";
			
			$urlAnexa.="PROCESADOENSUNA=".$datos['ProcesadoenSuna'];
		}
		if (isset($datos['ClaveEscuela']) && $datos['ClaveEscuela']!="")
		{
			if ($urlAnexa=="")
				$urlAnexa="?";
			else
				$urlAnexa.="&";
			
			$urlAnexa.="CLAVEESTAB=".$datos['ClaveEscuela'];
		}
		if (isset($datos['Encuadre']) && $datos['Encuadre']!="")
		{
			if ($urlAnexa=="")
				$urlAnexa="?";
			else
				$urlAnexa.="&";
			
			$urlAnexa.="ENCUADRE=".$datos['Encuadre'];
		}
		if (isset($datos['FechaDesde']) && $datos['FechaDesde']!="")
		{
			if ($urlAnexa=="")
				$urlAnexa="?";
			else
				$urlAnexa.="&";
			
			$urlAnexa.="FECHADESDE=".$datos['FechaDesde'];
		}
		
		if (isset($datos['Observacion']) && $datos['Observacion']!="")
		{
			if ($urlAnexa=="")
				$urlAnexa="?";
			else
				$urlAnexa.="&";
			
			$urlAnexa.="OBSERVACION=".utf8_encode($datos['Observacion']);
		}
		$url .= $urlAnexa;
		$header = array();
		$this->oCurl->setUrl(APISUNA);
		$fields_string = "";
		$this->oCurl->setFunction(get_class($this)."-".__FUNCTION__);
		//$this->oCurl->setDebug(true);
		if(!$this->oCurl->sendGet($url,$dataResult))
		{	
			$this->setError("Error","Error, ocurrio un error al buscar las licencias");
			return false;
		}
		if (!$this->Utf8)
			$array = FuncionesPHPLocal::DecodificarUtf8($dataResult);
		else
			$array = $dataResult;

		return $array;
	}
	

	public function getLicenciaxId($datos)
	{
		$url = "v1/licencias/cod-".$datos['id'];
		
		
		$header = array();
		$this->oCurl->setUrl(APISUNA);
		$fields_string = "";
		$this->oCurl->setFunction(get_class($this)."-".__FUNCTION__);
		//$this->oCurl->setDebug(true);
		if(!$this->oCurl->sendGet($url,$dataResult))
		{	
			$this->setError("Error","Error, ocurrio un error al buscar la licencia por codigo");
			return false;
		}
		if (!$this->Utf8)
			$array = FuncionesPHPLocal::DecodificarUtf8($dataResult);
		else
			$array = $dataResult;

		return $array;
	}
	
	
	public function getLicenciaxTaskIds($datos)
	{
		$url = "v1/licencias/buscar/taskids";
		
		$header = array();
		$this->oCurl->setUrl(APISUNA.$url);
		
		$fields_string = "";
		$this->oCurl->setFunction(get_class($this)."-".__FUNCTION__);
		//$this->oCurl->setDebug(true);
		if(!$this->oCurl->sendPost($datos,$dataResult))
		{	
			$this->setError("Error","Error, ocurrio un error al buscar inasistencias por taskids");
			return false;
		}
		if (!$this->Utf8)
			$array = FuncionesPHPLocal::DecodificarUtf8($dataResult);
		else
			$array = $dataResult;

		if (isset($array['error']))
		{
			$this->setError($array['error'],$array['error_description']);
			return false;
		}
		
		return $array;
	}
	
	
	public function getLicenciaxIdNovedadSuna($datos)
	{
		$url = "v1/licencias/buscar/idnovedadsuna";
		
		$header = array();
		$this->oCurl->setUrl(APISUNA.$url);
		
		$fields_string = "";
		$this->oCurl->setFunction(get_class($this)."-".__FUNCTION__);
		//$this->oCurl->setDebug(true);
		if(!$this->oCurl->sendPost($datos,$dataResult))
		{	
			$this->setError("Error","Error, ocurrio un error al buscar inasistencias por taskids");
			return false;
		}
		if (!$this->Utf8)
			$array = FuncionesPHPLocal::DecodificarUtf8($dataResult);
		else
			$array = $dataResult;

		if (isset($array['error']))
		{
			$this->setError($array['error'],$array['error_description']);
			return false;
		}
		
		return $array;
	}
	
	public function getInasistenciasBloqueadas()
	{
		$url = "v1/licencias/bloqueadas";
		
		$header = array();
		$this->oCurl->setUrl(APISUNA);
		
		$fields_string = array();
		$this->oCurl->setFunction(get_class($this)."-".__FUNCTION__);
		//$this->oCurl->setDebug(true);
		if(!$this->oCurl->sendGet($url,$dataResult))
		{	
			$this->setError("Error","Error, ocurrio un error al buscar la cantidad de licencias bloqueadas");
			return false;
		}
		if (!$this->Utf8)
			$array = FuncionesPHPLocal::DecodificarUtf8($dataResult);
		else
			$array = $dataResult;

		if (isset($array['error']))
		{
			$this->setError($array['error'],$array['error_description']);
			return false;
		}
		
		return $array;
	}
	

	public function publicarLicencia($datos)
	{
		$url = "v1/licencias";
		
		
		$header = array();
		$this->oCurl->setUrl(APISUNA.$url);
		$fields_string = "";
		$this->oCurl->setFunction(get_class($this)."-".__FUNCTION__);
		//$this->oCurl->setDebug(true);
		if(!$this->oCurl->sendPost($datos,$dataResult))
		{	
			$this->setError("Error","Error, ocurrio un error al insertar la inasistencia");
			return false;
		}
		if (!$this->Utf8)
			$array = FuncionesPHPLocal::DecodificarUtf8($dataResult);
		else
			$array = $dataResult;
		
		if (isset($array['error']))
		{
			$this->setError($array['error'],$array['error_description']);
			return false;
		}
		
		return $array;
	}
	
	
	public function publicarBulkLicencias($datos)
	{
		$url = "v1/licencias/bulk";
		
		
		$header = array();
		$this->oCurl->setUrl(APISUNA.$url);
		$fields_string = "";
		$this->oCurl->setFunction(get_class($this)."-".__FUNCTION__);
		//$this->oCurl->setDebug(true);
		$this->oCurl->setHttpBuildPost(false);
		
		$arrayHeader = array('Content-Type: application/json', 'Content-Length: ' . strlen($datos));
		$this->oCurl->setHeader($arrayHeader);
			
		if(!$this->oCurl->sendPost($datos,$dataResult))
		{	
			$this->setError("Error","Error, ocurrio un error al insertar las inasistencia");
			return false;
		}
		
		if (!$this->Utf8)
			$array = FuncionesPHPLocal::DecodificarUtf8($dataResult);
		else
			$array = $dataResult;

		if (isset($array['error']))
		{
			$this->setError($array['error'],$array['error_description']);
			return false;
		}
		
		return $array;
	}
	
	
	
	public function anularLicencia($datos)
	{
		$url = "v1/licencias/cod-".$datos['id']."/anular";
		$header = array();
		$this->oCurl->setUrl(APISUNA.$url);
		$fields_string = "";
		$this->oCurl->setFunction(get_class($this)."-".__FUNCTION__);
		//$this->oCurl->setDebug(true);
		if(!$this->oCurl->sendPost($datos,$dataResult))
		{	
			$this->setError("Error","Error, ocurrio un error al insertar la inasistencia");
			return false;
		}
		if (!$this->Utf8)
			$array = FuncionesPHPLocal::DecodificarUtf8($dataResult);
		else
			$array = $dataResult;
		
		if (isset($array['error']))
		{
			$this->setError($array['error'],$array['error_description']);
			return false;
		}
		
		return $array;
	}
	
	
	
	
	
	
	
	
	
	
	public function EliminarLicencia($datos)
	{
		$url = "v1/licencias";
		
		$header = array();
		$this->oCurl->setUrl(APISUNA.$url);
		$fields_string = "";
		$this->oCurl->setFunction(get_class($this)."-".__FUNCTION__);
		//$this->oCurl->setHttpBuildPost(false);
		//$this->oCurl->setDebug(true);
		if(!$this->oCurl->sendDelete($datos,$dataResult))
		{	
			$this->setError("Error","Error, al Eliminar Licencia");
			return false;
		}
		$array = FuncionesPHPLocal::DecodificarUtf8($dataResult);
		//print_r($array);
		if (isset($array['error']))
		{
			$this->setError($array['error'],$array['error_description']);
			return false;
		}
		//print_r($array);die;
		return $array;
	}
	
	
	public function RectificarLicencia($datos)
	{
		$url = "v1/licencias/rectificar";
		
		$header = array();
		$this->oCurl->setUrl(APISUNA.$url);
		$fields_string = "";
		$this->oCurl->setFunction(get_class($this)."-".__FUNCTION__);
		//$this->oCurl->setHttpBuildPost(false);
		//$this->oCurl->setDebug(true);
		if(!$this->oCurl->sendPost($datos,$dataResult))
		{	
			$this->setError("Error","Error, al Eliminar Licencia");
			return false;
		}
		$array = FuncionesPHPLocal::DecodificarUtf8($dataResult);
		//print_r($array);
		if (isset($array['error']))
		{
			$this->setError($array['error'],$array['error_description']);
			return false;
		}
		//print_r($array);die;
		return $array;
	}
	
	

	
	
	
	public function publicarMovimiento($datos)
	{
		$url = "v1/movimientos";
		
		
		$header = array();
		$this->oCurl->setUrl(APISUNA.$url);
		$fields_string = "";
		$this->oCurl->setFunction(get_class($this)."-".__FUNCTION__);
		//$this->oCurl->setDebug(true);
		if(!$this->oCurl->sendPost($datos,$dataResult))
		{	
			$this->setError("Error","Error, ocurrio un error al insertar el movimiento");
			return false;
		}
		if (!$this->Utf8)
			$array = FuncionesPHPLocal::DecodificarUtf8($dataResult);
		else
			$array = $dataResult;

		if (isset($array['error']))
		{
			$this->setError($array['error'],$array['error_description']);
			return false;
		}
		
		return $array;
	}
	

	
	
	
	
	
	
	
	public function getMovimientos($datos)
	{
		$url = "v1/movimientos";
		
		$urlAnexa = "";
		if (isset($datos['from']) && $datos['from']!=="" && is_numeric($datos['from']))
		{
			$urlAnexa.="?from=".$datos['from'];
			if (isset($datos['size']) && $datos['size']!=="" && is_numeric($datos['size']))
				$urlAnexa.="&size=".$datos['size'];
		}
		
		if (isset($datos['Documento']) && $datos['Documento']!="")
		{
			if ($urlAnexa=="")
				$urlAnexa="?";
			else
				$urlAnexa.="&";
			
			$urlAnexa.="DOCUMENTO=".$datos['Documento'];
		}
		
		if (isset($datos['CodigoCambio']) && $datos['CodigoCambio']!="")
		{
			if ($urlAnexa=="")
				$urlAnexa="?";
			else
				$urlAnexa.="&";
			
			$urlAnexa.="CODIGOCAMBIO=".$datos['CodigoCambio'];
		}
		
		
		
		
		if (isset($datos['Id']) && $datos['Id']!="")
		{
			if ($urlAnexa=="")
				$urlAnexa="?";
			else
				$urlAnexa.="&";
			
			$urlAnexa.="ID=".$datos['Id'];
		}
		
		if (isset($datos['IdNovedad']) && $datos['IdNovedad']!="")
		{
			if ($urlAnexa=="")
				$urlAnexa="?";
			else
				$urlAnexa.="&";
			
			$urlAnexa.="IDNOVEDAD=".$datos['IdNovedad'];
		}
		
		if (isset($datos['RegimenEstatutario']) && $datos['RegimenEstatutario']!="")
		{
			if ($urlAnexa=="")
				$urlAnexa="?";
			else
				$urlAnexa.="&";
			
			$urlAnexa.="REGESTAT=".$datos['RegimenEstatutario'];
		}
		$url .= $urlAnexa;
		$header = array();
		$this->oCurl->setUrl(APISUNA);
		$fields_string = "";
		$this->oCurl->setFunction(get_class($this)."-".__FUNCTION__);
		//$this->oCurl->setDebug(true);
		if(!$this->oCurl->sendGet($url,$dataResult))
		{	
			$this->setError("Error","Error, ocurrio un error al buscar los movimientos");
			return false;
		}
		if (!$this->Utf8)
			$array = FuncionesPHPLocal::DecodificarUtf8($dataResult);
		else
			$array = $dataResult;

		return $array;
	}
	

	public function getMovimientoxId($datos)
	{
		$url = "v1/movimientos/cod-".$datos['id'];
		
		
		$header = array();
		$this->oCurl->setUrl(APISUNA);
		$fields_string = "";
		$this->oCurl->setFunction(get_class($this)."-".__FUNCTION__);
		//$this->oCurl->setDebug(true);
		if(!$this->oCurl->sendGet($url,$dataResult))
		{	
			$this->setError("Error","Error, ocurrio un error al buscar el movimiento por codigo");
			return false;
		}
		if (!$this->Utf8)
			$array = FuncionesPHPLocal::DecodificarUtf8($dataResult);
		else
			$array = $dataResult;

		return $array;
	}
	

	
	public function obtenerLicenciasProcesadasHostSinProcesar($datos)
	{
		$url = "v1/licencias/sinprocesar";
		
		
		$header = array();
		$this->oCurl->setUrl(APISUNA);
		$fields_string = "";
		$this->oCurl->setFunction(get_class($this)."-".__FUNCTION__);
		//$this->oCurl->setDebug(true);
		if(!$this->oCurl->sendGet($url,$dataResult))
		{	
			$this->setError("Error","Error, ocurrio un error al buscar el movimiento por codigo");
			return false;
		}
		if (!$this->Utf8)
			$array = FuncionesPHPLocal::DecodificarUtf8($dataResult);
		else
			$array = $dataResult;

		return $array;
	}
	
	
	

	public function EnviarNovedadesProcesadas($datos)
	{
		$url = "v1/licencias/procesadas";
		
		
		$header = array();
		$this->oCurl->setUrl(APISUNA.$url);
		$fields_string = "";
		$this->oCurl->setFunction(get_class($this)."-".__FUNCTION__);
		//$this->oCurl->setDebug(true);
		$this->oCurl->setHttpBuildPost(false);
		
		$arrayHeader = array('Content-Type: application/json', 'Content-Length: ' . strlen($datos));
		$this->oCurl->setHeader($arrayHeader);
			
		if(!$this->oCurl->sendPost($datos,$dataResult))
		{	
			$this->setError("Error","Error, ocurrio un error al modificar el campo de novedad procesada");
			return false;
		}
		
		if (!$this->Utf8)
			$array = FuncionesPHPLocal::DecodificarUtf8($dataResult);
		else
			$array = $dataResult;

		if (isset($array['error']))
		{
			$this->setError($array['error'],$array['error_description']);
			return false;
		}
		
		return $array;
	}

	public function getEscuelas()
	{
		$url = "v1/escuelas";
		
		
		$header = array();
		$this->oCurl->setUrl(APISUNA);
		$fields_string = "";
		$this->oCurl->setFunction(get_class($this)."-".__FUNCTION__);
		//$this->oCurl->setDebug(true);
		if(!$this->oCurl->sendGet($url,$dataResult))
		{	
			$this->setError("Error","Error, ocurrio un error al buscar las escuelas");
			return false;
		}
		if (!$this->Utf8)
			$array = FuncionesPHPLocal::DecodificarUtf8($dataResult);
		else
			$array = $dataResult;

		return $array;
	}
	
	
	
	public function GetError()
	{
		return $this->error;	
	}
	private function SetError($error,$errordesc="")
	{
		$this->error['error']=$error;	
		$this->error['errordesc']=$errordesc;	
	}
	
	public function obtenerLicenciasDuplicadasSinProcesar($datos)
	{
		$url = "v1/licencias/duplicadas";
		
		$header = array();
		$this->oCurl->setUrl(APISUNA);
		$fields_string = "";
		$this->oCurl->setFunction(get_class($this)."-".__FUNCTION__);
		//$this->oCurl->setDebug(true);
		if(!$this->oCurl->sendGet($url,$dataResult))
		{	
			$this->setError("Error","Error, ocurrio un error al buscar el movimiento por codigo");
			return false;
		}
		if (!$this->Utf8)
			$array = FuncionesPHPLocal::DecodificarUtf8($dataResult);
		else
			$array = $dataResult;

		return $array;
	}
	
	
	
}//FIN CLASE

?>