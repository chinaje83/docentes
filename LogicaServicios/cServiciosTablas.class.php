<?php 
class cServiciosTablas
{

	protected $formato;
	protected $conexion;
	protected $error;
	protected $Utf8;
	protected $oCurl;
	
	
	function __construct($conexion,$formato=FMT_TEXTO)
	{
		$this->conexion = &$conexion;
		$this->formato = $formato;
 		$this->error = array();
		$this->Utf8 = false;
		$this->oCurl = new CurlBigtree($this->conexion); 

	}
	public function __destruct()
	{	
		$this->oCurl->CloseCurl();
    } 	

	public function getCurl()
	{
		return 	$this->ch;
	}

	public function CodificarUtf8()
	{
		$this->Utf8 = true;
	}
	
	
	public function getDistritos($datos,&$resultJson)
	{
		
		
		$urlBase = ELASTICSERVER."/".INDICE.INDICETABLAS."/".TYPE."/_search";
		$datosEnviar = array();		$i = 0;
		$datosEnviar["from"] =0;
		$datosEnviar["size"] =1000;
		$datosEnviar["query"] = array();
		$datosEnviar["query"]["bool"] = array();
		$datosEnviar["query"]["bool"]["filter"] = array();
		$datosEnviar["query"]["bool"]["filter"][$i]["term"]["Tipo"] = "distritos";
		$datosEnviar["sort"]["Numero"]['order'] = "asc";
		
		
		$dataEnvio = json_encode($datosEnviar);
		/*echo $dataEnvio;*/
		
		$header = array("Content-Type: application/json");
		
		$fields_string = "";
		$this->oCurl->setFunction(get_class($this)."-".__FUNCTION__);
		//$this->oCurl->setDebug(true);
		//$this->oCurl->setSeguridad(true);
		$this->oCurl->setHeader($header);
		$this->oCurl->setUrl($urlBase);
		$this->oCurl->setHttpBuildPost(false);
		if(!$this->oCurl->sendPost($dataEnvio,$resultJson))
		{
			$this->setError("Error","Error, ocurrio un error al buscar los datos de los distritos");
			return false;
		}
		
		if (isset($resultJson['error']))
		{
			$error = $resultJson['error']['root_cause'];
			$errorDescription = "";
			foreach($error as $dataError)
				$errorDescription .= utf8_decode($dataError['reason']." (".$dataError['type'].")")." - ";
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,$errorDescription,array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		
		if (isset($resultJson['acknowledged']) && $resultJson['acknowledged']===true)
			return true;
		
		return true;
	}
	
	
	public function getCantidadDistritos(&$cantidad)
	{
		
		
		$urlBase = ELASTICSERVER."/".INDICE.INDICETABLAS."/_count";
		$datosEnviar = new stdClass();
		$datosEnviar->query = new stdClass();
		$datosEnviar->query->term = new stdClass();
		$datosEnviar->query->term->Tipo = 'distritos';
		
		
		$dataEnvio = json_encode($datosEnviar);
		//echo $dataEnvio;die;
		
		$header = array("Content-Type: application/json");
		
		$fields_string = "";
		$this->oCurl->setFunction(get_class($this)."-".__FUNCTION__);
		//$this->oCurl->setDebug(true);
		//$this->oCurl->setSeguridad(true);
		$this->oCurl->setHeader($header);
		$this->oCurl->setUrl($urlBase);
		$this->oCurl->setHttpBuildPost(false);
		if(!$this->oCurl->sendPost($dataEnvio,$resultJson))
		{
			$this->setError("Error","Error, ocurrio un error al buscar los datos de los distritos");
			return false;
		}
		
		if (isset($resultJson['error']))
		{
			$error = $resultJson['error']['root_cause'];
			$errorDescription = "";
			foreach($error as $dataError)
				$errorDescription .= utf8_decode($dataError['reason']." (".$dataError['type'].")")." - ";
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,$errorDescription,array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		
		if (isset($resultJson['acknowledged']) && $resultJson['acknowledged']===true)
			return true;
		
		$cantidad = $resultJson['count'];
		
		return true;
	}


	
	public function getDistritosbyIds($datos,&$resultJson)
	{

		
		$urlBase = ELASTICSERVER."/".INDICE.INDICETABLAS."/".TYPE."/_search";
		$datosEnviar = array();		$i = 0;	
		$datosEnviar["from"] =0;
		$datosEnviar["size"] =1000;
		$datosEnviar["_source"] = array("IdRegistroExterno","Numero","Descripcion");
		$datosEnviar["query"] = array();
		$datosEnviar["query"]["bool"] = array();
		$datosEnviar["query"]["bool"]["filter"] = array();
		$datosEnviar["query"]["bool"]["filter"][$i]["term"]["Tipo"] = "distritos";
		$i++;
		$datosEnviar["query"]["bool"]["filter"][$i]["terms"]["Numero"] = $datos['distritosnumeros'];
		$datosEnviar["sort"]["Numero"]['order'] = "asc";
		
		$dataEnvio = json_encode($datosEnviar);
		/*echo $dataEnvio;*/

		$header = array("Content-Type: application/json");

		$fields_string = "";
		$this->oCurl->setFunction(get_class($this)."-".__FUNCTION__);
		//$this->oCurl->setDebug(true);
		$this->oCurl->setHeader($header);
		$this->oCurl->setUrl($urlBase);
		$this->oCurl->setHttpBuildPost(false);

		if(!$this->oCurl->sendPost($dataEnvio,$resultJson))
		{	
			$this->setError("Error","Error, ocurrio un error al buscar los datos");
			return false;
		}
		if (isset($resultJson['error']))
		{
			$error = $resultJson['error']['root_cause'];
			$errorDescription = "";
			foreach($error as $dataError)
				$errorDescription .= utf8_decode($dataError['reason']." (".$dataError['type'].")")." - ";
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,$errorDescription,array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;	
		}
		
		if (isset($resultJson['acknowledged']) && $resultJson['acknowledged']===true)
			return true;	

		return true;
	}


		
	public function getTiposOrganizacion($datos,&$resultJson)
	{

		
		$urlBase = ELASTICSERVER."/".INDICE.INDICETABLAS."/".TYPE."/_search";
		$datosEnviar = array();		$i = 0;	
		$datosEnviar["from"] =0;
		$datosEnviar["size"] =1000;
		$datosEnviar["query"] = array();
		$datosEnviar["query"]["bool"] = array();
		$datosEnviar["query"]["bool"]["filter"] = array();
		$datosEnviar["query"]["bool"]["filter"][$i]["term"]["Tipo"] = "tiposorganizacion";
		$datosEnviar["sort"]["Codigo"]['order'] = "asc";
		
		/*$dataEnvio = json_encode($datosEnviar);
		
		$header = array("Content-Type: application/json");
		curl_setopt($this->ch, CURLOPT_HTTPHEADER, $header);
		curl_setopt($this->ch, CURLOPT_SSL_VERIFYPEER, false); 
		curl_setopt($this->ch, CURLOPT_URL, $urlBase);
		curl_setopt($this->ch, CURLOPT_CUSTOMREQUEST, "POST");
		curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, 1); 
		curl_setopt($this->ch, CURLOPT_POSTFIELDS,$dataEnvio);
		$result = curl_exec($this->ch);
		$resultJson = json_decode($result,1);*/


		$dataEnvio = json_encode($datosEnviar);
		/*echo $dataEnvio;*/

		$header = array("Content-Type: application/json");

		$fields_string = "";
		$this->oCurl->setFunction(get_class($this)."-".__FUNCTION__);
		//$this->oCurl->setDebug(true);
		$this->oCurl->setHeader($header);
		$this->oCurl->setUrl($urlBase);
		$this->oCurl->setHttpBuildPost(false);

		if(!$this->oCurl->sendPost($dataEnvio,$resultJson))
		{	
			$this->setError("Error","Error, ocurrio un error al buscar los datos");
			return false;
		}
		if (isset($resultJson['error']))
		{
			$error = $resultJson['error']['root_cause'];
			$errorDescription = "";
			foreach($error as $dataError)
				$errorDescription .= utf8_decode($dataError['reason']." (".$dataError['type'].")")." - ";
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,$errorDescription,array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;	
		}
		
		if (isset($resultJson['acknowledged']) && $resultJson['acknowledged']===true)
			return true;	

		return true;
	}
	


	public function getCarreras($datos,&$resultJson)
	{

		
		$urlBase = ELASTICSERVER."/".INDICE.INDICETABLAS."/".TYPE."/_search";
		$datosEnviar = array();		
		$i = 0;	
		$datosEnviar["from"] =0;
		$datosEnviar["size"] =1000;
		$datosEnviar["query"] = array();
		$datosEnviar["query"]["bool"] = array();
		$datosEnviar["query"]["bool"]["filter"] = array();
		$datosEnviar["query"]["bool"]["filter"][$i]["term"]["Tipo"] = "carreras";
		$i++;
		$datosEnviar["query"]["bool"]["filter"][$i]["exists"]["field"] = "Codigo";
		$datosEnviar["sort"]["Codigo"]['order'] = "asc";
		
		/*$dataEnvio = json_encode($datosEnviar);
		$header = array("Content-Type: application/json");
		curl_setopt($this->ch, CURLOPT_HTTPHEADER, $header);
		curl_setopt($this->ch, CURLOPT_SSL_VERIFYPEER, false); 
		curl_setopt($this->ch, CURLOPT_URL, $urlBase);
		curl_setopt($this->ch, CURLOPT_CUSTOMREQUEST, "POST");
		curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, 1); 
		curl_setopt($this->ch, CURLOPT_POSTFIELDS,$dataEnvio);
		$result = curl_exec($this->ch);
		$resultJson = json_decode($result,1);*/



		$dataEnvio = json_encode($datosEnviar);
		/*echo $dataEnvio;*/

		$header = array("Content-Type: application/json");

		$fields_string = "";
		$this->oCurl->setFunction(get_class($this)."-".__FUNCTION__);
		//$this->oCurl->setDebug(true);
		$this->oCurl->setHeader($header);
		$this->oCurl->setUrl($urlBase);
		$this->oCurl->setHttpBuildPost(false);

		if(!$this->oCurl->sendPost($dataEnvio,$resultJson))
		{	
			$this->setError("Error","Error, ocurrio un error al buscar los datos de las novedades");
			return false;
		}
		if (isset($resultJson['error']))
		{
			$error = $resultJson['error']['root_cause'];
			$errorDescription = "";
			foreach($error as $dataError)
				$errorDescription .= utf8_decode($dataError['reason']." (".$dataError['type'].")")." - ";
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,$errorDescription,array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;	
		}
		
		if (isset($resultJson['acknowledged']) && $resultJson['acknowledged']===true)
			return true;	

		return true;
	}
	


	public function getAsignaturas($datos,&$resultJson)
	{

		
		$urlBase = ELASTICSERVER."/".INDICE.INDICETABLAS."/".TYPE."/_search";
		$datosEnviar = array();		
		$i = 0;	
		$datosEnviar["from"] =0;
		$datosEnviar["size"] =1000;
		$datosEnviar["query"] = array();
		$datosEnviar["query"]["bool"] = array();
		$datosEnviar["query"]["bool"]["filter"] = array();
		$datosEnviar["query"]["bool"]["filter"][$i]["term"]["Tipo"] = "asignaturas";
		$i++;
		//$datosEnviar["query"]["bool"]["filter"][$i]["term"]["Carrera.Id"] = $datos['IdCarrera'];
		$datosEnviar["sort"]["Codigo"]['order'] = "asc";
		
		
		
		/*$dataEnvio = json_encode($datosEnviar);
		$header = array("Content-Type: application/json");
		curl_setopt($this->ch, CURLOPT_HTTPHEADER, $header);
		curl_setopt($this->ch, CURLOPT_SSL_VERIFYPEER, false); 
		curl_setopt($this->ch, CURLOPT_URL, $urlBase);
		curl_setopt($this->ch, CURLOPT_CUSTOMREQUEST, "POST");
		curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, 1); 
		curl_setopt($this->ch, CURLOPT_POSTFIELDS,$dataEnvio);
		$result = curl_exec($this->ch);
		$resultJson = json_decode($result,1);*/


		$dataEnvio = json_encode($datosEnviar);
		/*echo $dataEnvio;*/

		$header = array("Content-Type: application/json");

		$fields_string = "";
		$this->oCurl->setFunction(get_class($this)."-".__FUNCTION__);
		//$this->oCurl->setDebug(true);
		$this->oCurl->setHeader($header);
		$this->oCurl->setUrl($urlBase);
		$this->oCurl->setHttpBuildPost(false);

		if(!$this->oCurl->sendPost($dataEnvio,$resultJson))
		{	
			$this->setError("Error","Error, ocurrio un error al buscar los datos");
			return false;
		}
		if (isset($resultJson['error']))
		{
			$error = $resultJson['error']['root_cause'];
			$errorDescription = "";
			foreach($error as $dataError)
				$errorDescription .= utf8_decode($dataError['reason']." (".$dataError['type'].")")." - ";
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,$errorDescription,array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;	
		}
		
		if (isset($resultJson['acknowledged']) && $resultJson['acknowledged']===true)
			return true;	

		return true;
	}
	
	public function getAreas($datos,&$resultJson)
	{

		
		$urlBase = ELASTICSERVER."/".INDICE.INDICETABLAS."/".TYPE."/_search";
		$datosEnviar = array();		
		$i = 0;	
		$datosEnviar["from"] =0;
		$datosEnviar["size"] =10000;
		$datosEnviar["query"] = array();
		$datosEnviar["query"]["bool"] = array();
		$datosEnviar["query"]["bool"]["filter"] = array();
		$datosEnviar["query"]["bool"]["filter"][$i]["term"]["Tipo"] = "areas";
		$i++;
		//$datosEnviar["query"]["bool"]["filter"][$i]["term"]["Carrera.Id"] = $datos['IdCarrera'];
		$datosEnviar["sort"]["Codigo"]['order'] = "asc";
		
		
		
/*		$dataEnvio = json_encode($datosEnviar);
		$header = array("Content-Type: application/json");
		curl_setopt($this->ch, CURLOPT_HTTPHEADER, $header);
		curl_setopt($this->ch, CURLOPT_SSL_VERIFYPEER, false); 
		curl_setopt($this->ch, CURLOPT_URL, $urlBase);
		curl_setopt($this->ch, CURLOPT_CUSTOMREQUEST, "POST");
		curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, 1); 
		curl_setopt($this->ch, CURLOPT_POSTFIELDS,$dataEnvio);
		$result = curl_exec($this->ch);
		$resultJson = json_decode($result,1);*/



		$dataEnvio = json_encode($datosEnviar);
		/*echo $dataEnvio;*/

		$header = array("Content-Type: application/json");

		$fields_string = "";
		$this->oCurl->setFunction(get_class($this)."-".__FUNCTION__);
		//$this->oCurl->setDebug(true);
		$this->oCurl->setHeader($header);
		$this->oCurl->setUrl($urlBase);
		$this->oCurl->setHttpBuildPost(false);

		if(!$this->oCurl->sendPost($dataEnvio,$resultJson))
		{	
			$this->setError("Error","Error, ocurrio un error al buscar los datos");
			return false;
		}

		if (isset($resultJson['error']))
		{
			$error = $resultJson['error']['root_cause'];
			$errorDescription = "";
			foreach($error as $dataError)
				$errorDescription .= utf8_decode($dataError['reason']." (".$dataError['type'].")")." - ";
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,$errorDescription,array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;	
		}
		
		if (isset($resultJson['acknowledged']) && $resultJson['acknowledged']===true)
			return true;	

		return true;
	}
	

	
	public function getAsignaturasAreasbyId($datos,&$resultJson)
	{

		
		$urlBase = ELASTICSERVER."/".INDICE.INDICETABLAS."/".TYPE."/_search";
		$datosEnviar = array();		
		$i = 0;	
		$datosEnviar["from"] =0;
		$datosEnviar["size"] =1000;
		$datosEnviar["query"] = array();
		$datosEnviar["query"]["bool"] = array();
		$datosEnviar["query"]["bool"]["filter"] = array();
		$datosEnviar["query"]["bool"]["filter"][$i]["term"]["Tipo"] = "planeseducativos";
		$i++;
		$datosEnviar["query"]["bool"]["filter"][$i]["term"]["Carrera.Id"] = $datos['IdCarrera'];
		$i++;
		$datosEnviar["query"]["bool"]["filter"][$i]["term"]["IdRegistroExterno"] = $datos['Id'];

		
	/*	$dataEnvio = json_encode($datosEnviar);
		//echo $dataEnvio;
		$header = array("Content-Type: application/json");
		curl_setopt($this->ch, CURLOPT_HTTPHEADER, $header);
		curl_setopt($this->ch, CURLOPT_SSL_VERIFYPEER, false); 
		curl_setopt($this->ch, CURLOPT_URL, $urlBase);
		curl_setopt($this->ch, CURLOPT_CUSTOMREQUEST, "POST");
		curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, 1); 
		curl_setopt($this->ch, CURLOPT_POSTFIELDS,$dataEnvio);
		$result = curl_exec($this->ch);
		$resultJson = json_decode($result,1);*/


		$dataEnvio = json_encode($datosEnviar);
		/*echo $dataEnvio;*/

		$header = array("Content-Type: application/json");

		$fields_string = "";
		$this->oCurl->setFunction(get_class($this)."-".__FUNCTION__);
		//$this->oCurl->setDebug(true);
		$this->oCurl->setHeader($header);
		$this->oCurl->setUrl($urlBase);
		$this->oCurl->setHttpBuildPost(false);

		if(!$this->oCurl->sendPost($dataEnvio,$resultJson))
		{	
			$this->setError("Error","Error, ocurrio un error al buscar los datos");
			return false;
		}
		if (isset($resultJson['error']))
		{
			$error = $resultJson['error']['root_cause'];
			$errorDescription = "";
			foreach($error as $dataError)
				$errorDescription .= utf8_decode($dataError['reason']." (".$dataError['type'].")")." - ";
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,$errorDescription,array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;	
		}
		
		if (isset($resultJson['acknowledged']) && $resultJson['acknowledged']===true)
			return true;	

		return true;
	}
	

	public function getCargosxEnsenianza($datos,&$resultJson)
	{

		
		$urlBase = ELASTICSERVER."/".INDICE.INDICETABLAS."/".TYPE."/_search";
		$datosEnviar = array();		
		$i = 0;	
		$datosEnviar["from"] =0;
		$datosEnviar["size"] =1000;
		$datosEnviar["query"] = array();
		$datosEnviar["query"]["bool"] = array();
		$datosEnviar["query"]["bool"]["filter"] = array();
		$datosEnviar["query"]["bool"]["filter"][$i]["term"]["Tipo"] = "cargos";
		$i++;
		//$datosEnviar["query"]["bool"]["filter"][$i]["term"]["TipoEnsenanza"] = $datos['TipoEnsenanza'];
		$i++;

		
	/*	$dataEnvio = json_encode($datosEnviar);
		$header = array("Content-Type: application/json");
		curl_setopt($this->ch, CURLOPT_HTTPHEADER, $header);
		curl_setopt($this->ch, CURLOPT_SSL_VERIFYPEER, false); 
		curl_setopt($this->ch, CURLOPT_URL, $urlBase);
		curl_setopt($this->ch, CURLOPT_CUSTOMREQUEST, "POST");
		curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, 1); 
		curl_setopt($this->ch, CURLOPT_POSTFIELDS,$dataEnvio);
		$result = curl_exec($this->ch);
		$resultJson = json_decode($result,1);*/

		$dataEnvio = json_encode($datosEnviar);
		/*echo $dataEnvio;*/

		$header = array("Content-Type: application/json");

		$fields_string = "";
		$this->oCurl->setFunction(get_class($this)."-".__FUNCTION__);
		//$this->oCurl->setDebug(true);
		$this->oCurl->setHeader($header);
		$this->oCurl->setUrl($urlBase);
		$this->oCurl->setHttpBuildPost(false);

		if(!$this->oCurl->sendPost($dataEnvio,$resultJson))
		{	
			$this->setError("Error","Error, ocurrio un error al buscar los datos");
			return false;
		}
		
		if (isset($resultJson['error']))
		{
			$error = $resultJson['error']['root_cause'];
			$errorDescription = "";
			foreach($error as $dataError)
				$errorDescription .= utf8_decode($dataError['reason']." (".$dataError['type'].")")." - ";
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,$errorDescription,array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;	
		}
		
		if (isset($resultJson['acknowledged']) && $resultJson['acknowledged']===true)
			return true;	

		return true;
	}
	
	
	public function getCargosxEnsenianzaxIdRegimenEstatutarioExterno($datos,&$resultJson)
	{

		
		$urlBase = ELASTICSERVER."/".INDICE.INDICETABLAS."/".TYPE."/_search";
		$datosEnviar = array();		
		$i = 0;	
		$datosEnviar["from"] =0;
		$datosEnviar["size"] =1000;
		$datosEnviar["query"] = array();
		$datosEnviar["query"]["bool"] = array();
		$datosEnviar["query"]["bool"]["filter"] = array();
		$datosEnviar["query"]["bool"]["filter"][$i]["term"]["Tipo"] = "cargos";
		$i++;
		$datosEnviar["query"]["bool"]["filter"][$i]["term"]["TipoEnsenanza"] = $datos['TipoEnsenanza'];
		$i++;
		$datosEnviar["query"]["bool"]["filter"][$i]["term"]["IdRegimenEstatutarioExterno"] = $datos['IdRegimenEstatutarioExterno'];
		$i++;

		
	/*	$dataEnvio = json_encode($datosEnviar);
		$header = array("Content-Type: application/json");
		curl_setopt($this->ch, CURLOPT_HTTPHEADER, $header);
		curl_setopt($this->ch, CURLOPT_SSL_VERIFYPEER, false); 
		curl_setopt($this->ch, CURLOPT_URL, $urlBase);
		curl_setopt($this->ch, CURLOPT_CUSTOMREQUEST, "POST");
		curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, 1); 
		curl_setopt($this->ch, CURLOPT_POSTFIELDS,$dataEnvio);
		$result = curl_exec($this->ch);
		$resultJson = json_decode($result,1);*/

		$dataEnvio = json_encode($datosEnviar);
		/*echo $dataEnvio;*/

		$header = array("Content-Type: application/json");

		$fields_string = "";
		$this->oCurl->setFunction(get_class($this)."-".__FUNCTION__);
		//$this->oCurl->setDebug(true);
		$this->oCurl->setHeader($header);
		$this->oCurl->setUrl($urlBase);
		$this->oCurl->setHttpBuildPost(false);

		if(!$this->oCurl->sendPost($dataEnvio,$resultJson))
		{	
			$this->setError("Error","Error, ocurrio un error al buscar los datos");
			return false;
		}
		
		if (isset($resultJson['error']))
		{
			$error = $resultJson['error']['root_cause'];
			$errorDescription = "";
			foreach($error as $dataError)
				$errorDescription .= utf8_decode($dataError['reason']." (".$dataError['type'].")")." - ";
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,$errorDescription,array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;	
		}
		
		if (isset($resultJson['acknowledged']) && $resultJson['acknowledged']===true)
			return true;	

		return true;
	}
	
	
	public function getCantidadCargosxRegimenxMarcaModuloxCodigoCargo($datos,&$resultJson)
	{

		
		$urlBase = ELASTICSERVER."/".INDICE.INDICETABLAS."/".TYPE."/_count";
		$datosEnviar = array();		
		$i = 0;	
		$datosEnviar["query"] = array();
		$datosEnviar["query"]["bool"] = array();
		$datosEnviar["query"]["bool"]["filter"] = array();
		$datosEnviar["query"]["bool"]["filter"][$i]["term"]["Tipo"] = "cargos";
		$i++;
		$datosEnviar["query"]["bool"]["filter"][$i]["term"]["IdRegimenEstatutarioExterno"] = $datos['IdRegimenEstatutarioExterno'];
		$i++;
		$datosEnviar["query"]["bool"]["filter"][$i]["term"]["Codigo"] = $datos['Codigo'];
		$i++;
		$datosEnviar["query"]["bool"]["filter"][$i]["term"]["TipoEnsenanza"] = $datos['TipoEnsenanza'];
		$i++;
		if (isset($datos["MarcaModulo"]) && $datos["MarcaModulo"]!="")
		{
			$datos["MarcaModulo"]=explode(",",$datos["MarcaModulo"]);
			$datosEnviar["query"]["bool"]["filter"][$i]["terms"]["MarcaModulo"] = $datos["MarcaModulo"];
			$i++;
		}
		

		
		$dataEnvio = json_encode($datosEnviar);
		$header = array("Content-Type: application/json");

		$fields_string = "";
		$this->oCurl->setFunction(get_class($this)."-".__FUNCTION__);
		//$this->oCurl->setDebug(true);
		$this->oCurl->setHeader($header);
		$this->oCurl->setUrl($urlBase);
		$this->oCurl->setHttpBuildPost(false);

		if(!$this->oCurl->sendPost($dataEnvio,$resultJson))
		{	
			$this->setError("Error","Error, ocurrio un error al buscar los datos");
			return false;
		}
		
		if (isset($resultJson['error']))
		{
			$error = $resultJson['error']['root_cause'];
			$errorDescription = "";
			foreach($error as $dataError)
				$errorDescription .= utf8_decode($dataError['reason']." (".$dataError['type'].")")." - ";
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,$errorDescription,array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;	
		}
		
		if (isset($resultJson['acknowledged']) && $resultJson['acknowledged']===true)
			return true;	

		return true;
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

	
	
	
}//FIN CLASE

?>