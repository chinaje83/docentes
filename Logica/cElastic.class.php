<?php
class cElastic
{


	protected $conexion;
	protected $formato;
	protected $Indice;
	protected $Type;
	protected $PrefijoId;
	protected $TipoDatos;
	function __construct($conexion,$formato=FMT_TEXTO){
		$this->conexion = &$conexion;
		$this->formato = $formato;
		$this->ch = curl_init();
		$this->Type="doc/";
	}

	function __destruct(){}


	public function SetearIndice($indice){$this->Indice = $indice;}
	public function SetearPrefijo($PrefijoId){$this->PrefijoId = $PrefijoId;}
	public function SetearTipoDatos($TipoDatos){$this->TipoDatos = $TipoDatos;}

	public function GenerarIndiceCliente($datos,&$resultJson)
	{
		$datosIndice['settings'] = array();
		$datosIndice['settings']['index'] = array();
		$datosIndice['settings']['index']['number_of_shards'] = CANTIDAD_SHARDS;
		$datosIndice['settings']['index']['number_of_replicas'] = CANTIDAD_REPLICAS;


		$datosIndice['settings']['analysis']['filter']['spanish_stop']['type'] = "stop";
		$datosIndice['settings']['analysis']['filter']['spanish_stop']['stopwords'] = "_spanish_";
		$datosIndice['settings']['analysis']['filter']['spanish_stemmer']['type'] = "stemmer";
		$datosIndice['settings']['analysis']['filter']['spanish_stemmer']['stopwords'] = "light_spanish";

		$datosIndice['settings']['analysis']['analyzer']['case_insensitive_sort']['tokenizer'] = "keyword";
		$datosIndice['settings']['analysis']['analyzer']['case_insensitive_sort']['filter'] = array("lowercase","spanish_stop","spanish_stemmer");

		//$datosIndice['settings']['processors'][0]['script']['lang'] = "painless";
		//$datosIndice['settings']['processors'][0]['script']['inline'] = utf8_encode("if (ctx.IdCliente == null) { throw new Exception('El identificador de cliente es obligatorio') }");

		$datosEnviar = json_encode($datosIndice,1);
		$urlBase = ELASTICSERVER."/".$this->Indice;
		$header = array();
		$header = array("Content-Type: application/json");
		curl_setopt($this->ch, CURLOPT_HTTPHEADER, $header);
		curl_setopt($this->ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($this->ch, CURLOPT_URL, $urlBase);
		curl_setopt($this->ch, CURLOPT_CUSTOMREQUEST, "PUT");
		curl_setopt($this->ch, CURLOPT_POSTFIELDS,$datosEnviar);
		curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, 1);
		$result = curl_exec($this->ch);
		$resultJson = json_decode($result,1);
		if (isset($resultJson['error']))
		{
			$error = $resultJson['error']['root_cause'];
			$errorDescription = "";
			foreach($error as $dataError)
				$errorDescription .= utf8_decode($dataError['reason']." (".$dataError['type'].")")." - ";
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,$errorDescription,array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		if (isset($resultJson['acknowledged']) && isset($resultJson['shards_acknowledged']) && $resultJson['acknowledged']===true && $resultJson['shards_acknowledged']===true)
		{

			$oMappingAuditorias = new cMappingElastic(CLIENTAUDIT);
			if(!$oMappingAuditorias->CrearIndice($datosIndice['settings']['index']['number_of_shards'],$datosIndice['settings']['index']['number_of_replicas']))
				return false;
		}
		return false;
	}

	public function EliminarIndiceCliente($datos,&$resultJson)
	{
		$urlBase = ELASTICSERVER."/".$this->Indice;
		$header = array("Content-Type: application/json");
		curl_setopt($this->ch, CURLOPT_HTTPHEADER, $header);
		curl_setopt($this->ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($this->ch, CURLOPT_URL, $urlBase);
		curl_setopt($this->ch, CURLOPT_CUSTOMREQUEST, "DELETE");
		curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, 1);
		$result = curl_exec($this->ch);
		$resultJson = json_decode($result,1);
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


		return false;
	}



	public function GenerarAliaseCliente($datos,&$resultJson)
	{
		$i=0;
		$datosAlias['actions'] = array();
		$datosAlias['actions'][$i]['add'] = array();
		$datosAlias['actions'][$i]['add']['index'] = $this->Indice;
		$datosAlias['actions'][$i]['add']['alias'] = $datos['alias'];
		$datosEnviar = json_encode($datosAlias);
		$urlBase = ELASTICSERVER."/_aliases";

		$header = array("Content-Type: application/json");
		curl_setopt($this->ch, CURLOPT_HTTPHEADER, $header);
		curl_setopt($this->ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($this->ch, CURLOPT_URL, $urlBase);
		curl_setopt($this->ch, CURLOPT_CUSTOMREQUEST, "POST");
		curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($this->ch, CURLOPT_POSTFIELDS,$datosEnviar);
		$result = curl_exec($this->ch);
		$resultJson = json_decode($result,1);

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

	public function EliminarAliaseCliente($datos,&$resultJson)
	{
		$i=0;
		$datosAlias['actions'] = array();
		$datosAlias['actions'][$i]['remove'] = array();
		$datosAlias['actions'][$i]['remove']['index'] = $this->Indice;
		$datosAlias['actions'][$i]['remove']['alias'] = $datos['alias'];
		$datosEnviar = json_encode($datosAlias);
		$urlBase = ELASTICSERVER."/_aliases";

		$header = array("Content-Type: application/json");
		curl_setopt($this->ch, CURLOPT_HTTPHEADER, $header);
		curl_setopt($this->ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($this->ch, CURLOPT_URL, $urlBase);
		curl_setopt($this->ch, CURLOPT_CUSTOMREQUEST, "POST");
		curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($this->ch, CURLOPT_POSTFIELDS,$datosEnviar);
		$result = curl_exec($this->ch);
		$resultJson = json_decode($result,1);

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


	public function GenerarPipelines()
	{


		$jsonData['description'] = utf8_encode("Hace que el IdCliente sea obligatorio en los documentos");
		$jsonData['processors'][0]['script']['lang'] = "painless";
		$jsonData['processors'][0]['script']['inline'] = utf8_encode("if (ctx.IdCliente == null) { throw new Exception('El identificador de cliente es obligatorio') }");


		//$jsonData['analysis']['normalizer']['ignorar_mayusculas']['type'] = "custom";
		//$jsonData['analysis']['normalizer']['ignorar_mayusculas']['char_filter'] = array();
		//$jsonData['analysis']['normalizer']['ignorar_mayusculas']['filter'] = array("lowercase", "asciifolding");


		$datosEnviar = json_encode($jsonData);

		$urlBase = ELASTICSERVER."/_ingest/pipeline/obligatorio";
		$header = array("Content-Type: application/json");
		curl_setopt($this->ch, CURLOPT_HTTPHEADER, $header);
		curl_setopt($this->ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($this->ch, CURLOPT_URL, $urlBase);
		curl_setopt($this->ch, CURLOPT_CUSTOMREQUEST, "PUT");
		curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($this->ch, CURLOPT_POSTFIELDS,$datosEnviar);
		$result = curl_exec($this->ch);
		$resultJson = json_decode($result,1);

		if (isset($resultJson['error']))
		{
			$error = array();
			if(isset($resultJson['error']['root_cause']) && $resultJson['error']['root_cause']!="")
				$error = $resultJson['error']['root_cause'];
			$errorDescription = "";
			foreach($error as $dataError)
				$errorDescription .= utf8_decode($dataError['reason']." (".$dataError['type'].")")." - ";
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,$errorDescription,array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		return true;
	}


	public function GenerarAnalizadores()
	{


		$jsonData['analysis']['analyzer']['case_insensitive_sort']['tokenizer'] = "keyword";
		$jsonData['analysis']['analyzer']['case_insensitive_sort']['filter'] = array("lowercase","spanish");


		//$jsonData['analysis']['normalizer']['ignorar_mayusculas']['type'] = "custom";
		//$jsonData['analysis']['normalizer']['ignorar_mayusculas']['char_filter'] = array();
		//$jsonData['analysis']['normalizer']['ignorar_mayusculas']['filter'] = array("lowercase", "asciifolding");


		$datosEnviar = json_encode($jsonData);

		$urlBase = ELASTICSERVER."/".INDICECLIENTE."/_settings";
		$header = array("Content-Type: application/json");
		curl_setopt($this->ch, CURLOPT_HTTPHEADER, $header);
		curl_setopt($this->ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($this->ch, CURLOPT_URL, $urlBase);
		curl_setopt($this->ch, CURLOPT_CUSTOMREQUEST, "PUT");
		curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($this->ch, CURLOPT_POSTFIELDS,$datosEnviar);
		$result = curl_exec($this->ch);
		$resultJson = json_decode($result,1);

		if (isset($resultJson['error']))
		{
			$error = array();
			if(isset($resultJson['error']['root_cause']) && $resultJson['error']['root_cause']!="")
				$error = $resultJson['error']['root_cause'];
			$errorDescription = "";
			foreach($error as $dataError)
				$errorDescription .= utf8_decode($dataError['reason']." (".$dataError['type'].")")." - ";
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,$errorDescription,array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		print_r($resultJson );


		return true;

	}

	public function GenerarMapping($Campos,&$resultJson)
	{
		$i=0;

		$jsonData['dynamic'] = "strict";
		$jsonData['properties']['IdCliente']['type'] = "integer";
		$jsonData['properties']['IdDocumento']['type'] = "integer";
		$jsonData['properties']['IdDocumentoPadre']['type'] = "integer";
		$jsonData['properties']['ArregloPadres']['type'] = "integer";
		$jsonData['properties']['DocumentosAsociados']['type'] = "integer";
		$jsonData['properties']['Tipo']['type'] = "keyword";

		$jsonData['properties']['IdCaja']['type'] = "integer";
		$jsonData['properties']['IdLote']['type'] = "integer";

		$jsonData['properties']['Imagen']['type'] = "keyword";

		$jsonData['properties']['name']['type'] = "keyword";
		$jsonData['properties']['name']['type'] = "keyword";
		$jsonData['properties']['name']['type'] = "keyword";
		$jsonData['properties']['name']['type'] = "keyword";


		foreach ($Campos as $Campo)
		{
			switch($Campo['TipoCampoElastic'])
			{
				case "text":
					$jsonData['properties'][$Campo['NombreCampo']]['type'] = $Campo['TipoCampoElastic'];
					$jsonData['properties'][$Campo['NombreCampo']]['analyzer'] = "spanish";
					$jsonData['properties'][$Campo['NombreCampo']]['fields']["raw"]['type'] = "keyword";
					//$jsonData['properties'][$Campo['NombreCampo']]['fields']["raw"]['normalizer'] = "ignorar_mayusculas";

					$jsonData['properties'][$Campo['NombreCampo']]['fields']["sort"]['type'] = "text";
					$jsonData['properties'][$Campo['NombreCampo']]['fields']["sort"]['fielddata'] = "true";
					$jsonData['properties'][$Campo['NombreCampo']]['fields']["sort"]['analyzer'] = "case_insensitive_sort";


				break;
				case "date":
					$jsonData['properties'][$Campo['NombreCampo']]['type'] = $Campo['TipoCampoElastic'];
					$jsonData['properties'][$Campo['NombreCampo']]['format'] = "yyyy-MM-dd HH:mm:ss||yyyy-MM-dd||epoch_millis";
					$jsonData['properties'][$Campo['NombreCampo']]['null_value'] = "NULL";
				break;

				case "scaled_float":
					$jsonData['properties'][$Campo['NombreCampo']]['type'] = $Campo['TipoCampoElastic'];
					$jsonData['properties'][$Campo['NombreCampo']]['scaling_factor'] = pow(10,$Campo['CantidadDecimales']);

				break;

				default:
					$jsonData['properties'][$Campo['NombreCampo']]['type'] = $Campo['TipoCampoElastic'];
					break;
			}

		}

		$jsonData['properties']['TipoArchivo']['properties']['Id']['type'] = "integer";
		$jsonData['properties']['TipoArchivo']['properties']['Nombre']['type'] = "text";
		$jsonData['properties']['TipoArchivo']['properties']['Nombre']['fields']['raw']['type'] = "keyword";

		$jsonData['properties']['IdArchivo']['type'] = "integer";
		$jsonData['properties']['NombreArchivo']['type'] = "keyword";
		$jsonData['properties']['TamanioArchivo']['type'] = "integer";
		$jsonData['properties']['UbicacionArchivo']['type'] = "keyword";
		$jsonData['properties']['HashArchivo']['type'] = "keyword";


		$jsonData['properties']['TipoDocumento']['properties']['Id']['type'] = "integer";
		$jsonData['properties']['TipoDocumento']['properties']['IdRegistro']['type'] = "integer";
		$jsonData['properties']['TipoDocumento']['properties']['Nombre']['type'] = "text";
		$jsonData['properties']['TipoDocumento']['properties']['Nombre']['fields']['raw']['type'] = "keyword";
		$jsonData['properties']['TipoDocumento']['properties']['NombreCorto']['type'] = "keyword";
		$jsonData['properties']['TipoDocumento']['properties']['Categoria']['properties']['Id']['type'] = "integer";
		$jsonData['properties']['TipoDocumento']['properties']['Categoria']['properties']['Nombre']['type'] = "text";
		$jsonData['properties']['TipoDocumento']['properties']['Categoria']['properties']['Nombre']['analyzer'] = "spanish";
		$jsonData['properties']['TipoDocumento']['properties']['Categoria']['properties']['Nombre']['fields']['raw']  = array("type" => "keyword");



		$jsonData['properties']['AltaAPP']['type'] = "text";

		$jsonData['properties']['AltaUsuario']['type'] = "integer";

		$jsonData['properties']['AltaFecha']['type'] = "date";
		$jsonData['properties']['AltaFecha']['format'] = "yyyy-MM-dd HH:mm:ss||yyyy-MM-dd||epoch_millis";

		$jsonData['properties']['Estado']['properties']['Id']['type'] = "integer";
		$jsonData['properties']['Estado']['properties']['Nombre']['type'] = "text";

		$jsonData['properties']['Area']['properties']['Id']['type'] = "integer";
		$jsonData['properties']['Area']['properties']['Nombre']['type'] = "text";

		$jsonData['properties']['UltimaModificacionUsuario']['type'] = "integer";

		$jsonData['properties']['MovimientoFecha']['type'] = "date";
		$jsonData['properties']['MovimientoFecha']['format'] = "yyyy-MM-dd HH:mm:ss||yyyy-MM-dd||epoch_millis";

		$jsonData['properties']['UltimaModificacionFecha']['type'] = "date";
		$jsonData['properties']['UltimaModificacionFecha']['format'] = "yyyy-MM-dd HH:mm:ss||yyyy-MM-dd||epoch_millis";

		$jsonData['properties']['UltimaModificacionAPP']['type'] = "text";



		$datosEnviar = json_encode($jsonData);

		$urlBase = ELASTICSERVER."/".INDICECLIENTE."/_mapping/".$this->Type;
		$header = array("Content-Type: application/json");
		curl_setopt($this->ch, CURLOPT_HTTPHEADER, $header);
		curl_setopt($this->ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($this->ch, CURLOPT_URL, $urlBase);
		curl_setopt($this->ch, CURLOPT_CUSTOMREQUEST, "POST");
		curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($this->ch, CURLOPT_POSTFIELDS,$datosEnviar);
		$result = curl_exec($this->ch);
		$resultJson = json_decode($result,1);

		if (isset($resultJson['error']))
		{
			//file_put_contents(PATH_STORAGE."error_".date("Ymd").".log",print_r($resultJson,true));
			$error = array();
			if(isset($resultJson['error']['root_cause']) && $resultJson['error']['root_cause']!="")
				$error = $resultJson['error']['root_cause'];
			$errorDescription = "";
			foreach($error as $dataError)
				$errorDescription .= utf8_decode($dataError['reason']." (".$dataError['type'].")")." - ";
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,$errorDescription,array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		if (isset($resultJson['acknowledged']) && $resultJson['acknowledged']===true)
		{
			$oMappingAuditorias = new cMappingElastic(CLIENTAUDIT);
			if(!$oMappingAuditorias->Mapping())
				return false;
			return true;
		}

		return false;
	}


	public function ModificarEstadoAreaDocumento($datosEnvio,&$resultJson)
	{
		if(empty($datos['Tipo']) && isset($this->TipoDatos) && $this->TipoDatos!="")
			$datos['Tipo'] = $this->TipoDatos;
		$i=0;
		$Id = self::_ObtenerId($datosEnvio);
		$urlBase = ELASTICSERVER."/".INDICECLIENTE."/".$this->Type.$Id."/_update";
		unset($datosEnvio['IdDocumento']);
		$datosEnvioData['doc'] = FuncionesPHPLocal::ConvertiraUtf8($datosEnvio);
		$datosEnviar = json_encode($datosEnvioData);
		$header = array("Content-Type: application/json");
		curl_setopt($this->ch, CURLOPT_HTTPHEADER, $header);
		curl_setopt($this->ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($this->ch, CURLOPT_URL, $urlBase);
		curl_setopt($this->ch, CURLOPT_CUSTOMREQUEST, "POST");
		curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($this->ch, CURLOPT_POSTFIELDS,$datosEnviar);
		$result = curl_exec($this->ch);
		$resultJson = json_decode($result,1);


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



	public function SubirDocumento($datosEnvio,&$resultJson)
	{
		if(empty($datos['Tipo']) && isset($this->TipoDatos) && $this->TipoDatos!="")
			$datos['Tipo'] = $this->TipoDatos;
		$i=0;
		$Id = self::_ObtenerId($datosEnvio);
		if($Id===false)
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Debe ingresar un código válido",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}
		if(CLIENTEOBLIGATORIO===true)
			$Id .= "?pipeline=obligatorio";
		$urlBase = ELASTICSERVER."/".INDICECLIENTE."/".$this->Type.$Id;
		$datosEnvio['IdCliente'] = $_SESSION['IdCliente'];
		$datosEnvio = FuncionesPHPLocal::ConvertiraUtf8($datosEnvio);
		$datosEnviar = json_encode($datosEnvio);
		//file_put_contents(PUBLICA."subir.json",$urlBase." -XPOST\n".$datosEnviar."\n");
		$header = array("Content-Type: application/json");
		curl_setopt($this->ch, CURLOPT_HTTPHEADER, $header);
		curl_setopt($this->ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($this->ch, CURLOPT_URL, $urlBase);
		curl_setopt($this->ch, CURLOPT_CUSTOMREQUEST, "POST");
		curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($this->ch, CURLOPT_POSTFIELDS,$datosEnviar);
		$result = curl_exec($this->ch);
		$resultJson = json_decode($result,1);


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


	public function EliminarDocumento($datos,&$resultJson)
	{
		if(empty($datos['Tipo']) && isset($this->TipoDatos) && $this->TipoDatos!="")
			$datos['Tipo'] = $this->TipoDatos;

		$i=0;
		$Id = self::_ObtenerId($datos);
		$urlBase = ELASTICSERVER."/".INDICECLIENTE."/".$this->Type.$Id;
		$header = array("Content-Type: application/json");
		curl_setopt($this->ch, CURLOPT_HTTPHEADER, $header);
		curl_setopt($this->ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($this->ch, CURLOPT_URL, $urlBase);
		curl_setopt($this->ch, CURLOPT_CUSTOMREQUEST, "DELETE");
		curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, 1);
		$result = curl_exec($this->ch);
		$resultJson = json_decode($result,1);


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


	public function ActualizarEstadDocumento($datos,&$resultJson)
	{
		if(empty($datos['Tipo']) && isset($this->TipoDatos) && $this->TipoDatos!="")
			$datos['Tipo'] = $this->TipoDatos;
		$Id = self::_ObtenerId($datos);
		$datosEnvio['doc']['Estado'] = $datos['Estado'];
		$urlBase = ELASTICSERVER."/".INDICECLIENTE."/".$this->Type.$Id."/_update";
		$datosEnvio = FuncionesPHPLocal::ConvertiraUtf8($datosEnvio);
		$datosEnviar = json_encode($datosEnvio);
		$header = array("Content-Type: application/json");
		curl_setopt($this->ch, CURLOPT_HTTPHEADER, $header);
		curl_setopt($this->ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($this->ch, CURLOPT_URL, $urlBase);
		curl_setopt($this->ch, CURLOPT_CUSTOMREQUEST, "POST");
		curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($this->ch, CURLOPT_POSTFIELDS,$datosEnviar);
		$result = curl_exec($this->ch);
		$resultJson = json_decode($result,1);


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


	public function ActualizarFechaMovimientoDocumento($datos,&$resultJson)
	{
		if(empty($datos['Tipo']) && isset($this->TipoDatos) && $this->TipoDatos!="")
			$datos['Tipo'] = $this->TipoDatos;
		$Id = self::_ObtenerId($datos);
		$datosEnvio['doc']['MovimientoFecha'] = $datos['MovimientoFecha'];
		$urlBase = ELASTICSERVER."/".INDICECLIENTE."/".$this->Type.$Id."/_update";
		$datosEnvio = FuncionesPHPLocal::ConvertiraUtf8($datosEnvio);
		$datosEnviar = json_encode($datosEnvio);
		$header = array("Content-Type: application/json");
		curl_setopt($this->ch, CURLOPT_HTTPHEADER, $header);
		curl_setopt($this->ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($this->ch, CURLOPT_URL, $urlBase);
		curl_setopt($this->ch, CURLOPT_CUSTOMREQUEST, "POST");
		curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($this->ch, CURLOPT_POSTFIELDS,$datosEnviar);
		$result = curl_exec($this->ch);
		$resultJson = json_decode($result,1);


		if (isset($resultJson['error']))
		{
			$error = $resultJson['error']['root_cause'];
			$errorDescription = "";
			foreach($error as $dataError)
				$errorDescription .= utf8_decode($dataError['reason']." (".$dataError['type'].")")." - ";
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,$errorDescription,array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}


		return true;
	}



	public function BuscarCantidadDocumentos($datos,&$resultJson)
	{
		if(empty($datos['Tipo']) && isset($this->TipoDatos) && $this->TipoDatos!="")
			$datos['Tipo'] = $this->TipoDatos;
		$i=0;
		$urlBase = ELASTICSERVER."/".INDICECLIENTE."/".$this->Type."_count";
		$datosEnviar = array();




		$i = 0;
		$datosEnviar["query"] = array();
		$datosEnviar["query"]["bool"] = array();
		$datosEnviar["query"]["bool"]["filter"] = array();

		if(isset($_SESSION['IdCliente']) && $_SESSION['IdCliente']!="")
		{
			$datosEnviar["query"]["bool"]["filter"][$i]["term"]["IdCliente"] = $_SESSION['IdCliente'];
			$i++;
		}
		else
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Debe ingresar un cliente",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		if(isset($datos['TipoDocumento']) && $datos['TipoDocumento']!="")
		{
			$datosEnviar["query"]["bool"]["filter"][$i]["term"]["TipoDocumento.Id"] = $datos['TipoDocumento'];
			$i++;
		}
		if(isset($datos['IdRegistroTipoDocumento']) && $datos['IdRegistroTipoDocumento']!="")
		{
			$datosEnviar["query"]["bool"]["filter"][$i]["term"]["TipoDocumento.IdRegistro"] = $datos['IdRegistroTipoDocumento'];
			$i++;
		}
		if(isset($datos['IdDocumentoPadre']) && $datos['IdDocumentoPadre']!="")
		{
			$datosEnviar["query"]["bool"]["filter"][$i]["term"]["IdDocumentoPadre"] = $datos['IdDocumentoPadre'];
			$i++;
		}
		if(empty($datos['IgnorarArea']))
		{
			$s = 0;
			if(isset($_SESSION['IdArea']) && $_SESSION['IdArea']!="")
			{
				if(!is_array($_SESSION['IdArea']))
					$IdArea = explode(",",$_SESSION['IdArea']);
				else
					$IdArea = $_SESSION['IdArea'];
				$datosEnviar["query"]["bool"]["filter"][$i]["bool"]["should"][$s]["terms"]["Area.Id"] = $IdArea;
				$s++;
			}
			$datosEnviar["query"]["bool"]["filter"][$i]["bool"]["should"][$s]["exists"] = array("field"=>"MovimientoFecha");
			$s++;
			$i++;
		}
		$datosEnviar["query"]["bool"]["filter"][$i]["term"]["Tipo"] = TIPODOC;
		$i++;

		self::CamposBusquedaDocumento($datos,$datosEnviar);

		$dataEnvio = json_encode($datosEnviar);
	//	file_put_contents(PUBLICA."cantidad.json",$dataEnvio);

		$header = array("Content-Type: application/json");
		curl_setopt($this->ch, CURLOPT_HTTPHEADER, $header);
		curl_setopt($this->ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($this->ch, CURLOPT_URL, $urlBase);
		curl_setopt($this->ch, CURLOPT_CUSTOMREQUEST, "POST");
		curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($this->ch, CURLOPT_POSTFIELDS,$dataEnvio);
		$result = curl_exec($this->ch);
		$resultJson = json_decode($result,1);

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



	public function BuscarCantidadAdjuntos($datos,&$resultJson)
	{
		if(empty($datos['Tipo']) && isset($this->TipoDatos) && $this->TipoDatos!="")
			$datos['Tipo'] = $this->TipoDatos;
		$i=0;
		$urlBase = ELASTICSERVER."/".INDICECLIENTE."/".$this->Type."_count";
		$datosEnviar = array();




		$i = 0;
		$datosEnviar["query"] = array();
		$datosEnviar["query"]["bool"] = array();
		$datosEnviar["query"]["bool"]["filter"] = array();
		if(isset($datos['IdDocumento']) && $datos['IdDocumento']!="")
		{
			$datosEnviar["query"]["bool"]["filter"][$i]["term"]["IdDocumento"] = $datos['IdDocumento'];
			$i++;
		}
		if(isset($datos['IdTipoArchivo']) && $datos['IdTipoArchivo']!="")
		{
			$datosEnviar["query"]["bool"]["filter"][$i]["term"]["TipoArchivo.Id"] = $datos['IdTipoArchivo'];
			$i++;
		}
		$datosEnviar["query"]["bool"]["filter"][$i]["term"]["Tipo"] = TIPOARCHIVO;
		$i++;

		$datosEnviar["query"]["bool"]["filter"][$i]["term"]["Estado.Id"] = ACTIVO;
		$i++;


		$dataEnvio = json_encode($datosEnviar);
		$header = array("Content-Type: application/json");
		curl_setopt($this->ch, CURLOPT_HTTPHEADER, $header);
		curl_setopt($this->ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($this->ch, CURLOPT_URL, $urlBase);
		curl_setopt($this->ch, CURLOPT_CUSTOMREQUEST, "POST");
		curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($this->ch, CURLOPT_POSTFIELDS,$dataEnvio);
		$result = curl_exec($this->ch);
		$resultJson = json_decode($result,1);

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



	public function BuscarDocumentosRaiz($datos,&$resultJson)
	{
		if(empty($datos['Tipo']) && isset($this->TipoDatos) && $this->TipoDatos!="")
			$datos['Tipo'] = $this->TipoDatos;
		$i=0;
		$urlBase = ELASTICSERVER."/".INDICECLIENTE."/".$this->Type."_search";
		$datosEnviar = array();

		//$datosEnviar['sort'] = array();
		//$datosEnviar['sort'][0] = array();
		//$datosEnviar['sort'][0]['UltimaModificacionFecha'] = array('order'=>'asc');


		if (!isset($datos['from']))
			$datosEnviar['from'] = 0;
		else
			$datosEnviar['from'] = $datos['from'];
		if (!isset($datos['size']))
			$datosEnviar['size'] = 10000;
		else
			$datosEnviar['size'] = $datos['size'];


		$f = 0;
		$i = 0;
		$datosEnviar["query"] = array();
		$datosEnviar["query"]["bool"] = array();
		$datosEnviar["query"]["bool"]["filter"] = array();
		if(isset($datos['Tipo']) && $datos['Tipo']!="")
		{
			$datosEnviar["query"]["bool"]["filter"][$f]["term"]["Tipo"] = $datos['Tipo'];
			$f++;
		}

		$datosEnviar["query"]["bool"]["must_not"][$i]["exists"] = array("field"=>"IdDocumentoPadre");
		$i++;

		$datosEnviar['aggs'] = array();
		$datosEnviar['aggs']['menu'] = array();
		$datosEnviar['aggs']['menu']['terms'] = array("field"=>"TipoDocumento.Nombre.raw");

		$dataEnvio = json_encode($datosEnviar);
//echo $dataEnvio;die;
		$header = array("Content-Type: application/json");
		curl_setopt($this->ch, CURLOPT_HTTPHEADER, $header);
		curl_setopt($this->ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($this->ch, CURLOPT_URL, $urlBase);
		curl_setopt($this->ch, CURLOPT_CUSTOMREQUEST, "POST");
		curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($this->ch, CURLOPT_POSTFIELDS,$dataEnvio);
		$result = curl_exec($this->ch);
		$resultJson = json_decode($result,1);

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



	public function BusquedaGeneral($datos,&$resultJson)
	{

		if(empty($datos['Tipo']) && isset($this->TipoDatos) && $this->TipoDatos!="")
			$datos['Tipo'] = $this->TipoDatos;
		$i=0;
		$urlBase = ELASTICSERVER."/".INDICECLIENTE."/".$this->Type."_search";
		$datosEnviar = array();

		$SortField = "IdDocumento";
		$SortOrder = "desc";


		$datosEnviar['sort'] = array();
		if (isset($datos['SortField']) && is_array($datos['SortField']) && count($datos['SortField'])>0)
		{
			foreach($datos['SortField'] as $Order)
				$datosEnviar['sort'][][$Order['Field']] = array('order'=>$Order['Sort']);
		}
		if (!isset($datos['from']))
			$datosEnviar['from'] = 0;
		else
			$datosEnviar['from'] = $datos['from'];
		if (!isset($datos['size']))
			$datosEnviar['size'] = PAGINAR;
		else
			$datosEnviar['size'] = $datos['size'];

		if (isset($datos['campos']))
		{
			if(!is_array($datos['campos']))
				$datos['campos'] = explode(",",$datos['campos']);
			$datosEnviar['_source'] = $datos['campos'];
		}


		$i = 0;
		$datosEnviar["query"] = array();
		$datosEnviar["query"]["bool"] = array();
		$datosEnviar["query"]["bool"]["filter"] = array();

		if(CLIENTEOBLIGATORIO===true)
		{
			if(isset($_SESSION['IdCliente']) && $_SESSION['IdCliente']!="")
			{
				$datosEnviar["query"]["bool"]["filter"][$i]["term"]["IdCliente"] = $_SESSION['IdCliente'];
				$i++;
			}
			else
			{
				FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Debe ingresar un cliente",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
				return false;
			}
		}

		if(isset($datos['TipoDocumento']) && is_array($datos['TipoDocumento']) && count($datos['TipoDocumento'])>0)
		{
			$datosEnviar["query"]["bool"]["filter"][$i]["terms"]["TipoDocumento.Id"] = $datos['TipoDocumento'];
			$i++;
		}elseif(isset($datos['TipoDocumento']) && is_string($datos['TipoDocumento']))
		{
			$datosEnviar["query"]["bool"]["filter"][$i]["term"]["TipoDocumento.Id"] = $datos['TipoDocumento'];
			$i++;
		}

		if(isset($datos['IdRegistroTipoDocumento']) && $datos['IdRegistroTipoDocumento']!="")
		{
			$datosEnviar["query"]["bool"]["filter"][$i]["term"]["TipoDocumento.IdRegistro"] = $datos['IdRegistroTipoDocumento'];
			$i++;
		}
		if(isset($datos['IdDocumentoPadre']) && $datos['IdDocumentoPadre']!="")
		{
			$datosEnviar["query"]["bool"]["filter"][$i]["term"]["IdDocumentoPadre"] = $datos['IdDocumentoPadre'];
			$i++;
		}
		if(isset($datos['IdDocumento']) && $datos['IdDocumento']!="")
		{
			$datosEnviar["query"]["bool"]["filter"][$i]["term"]["IdDocumento"] = $datos['IdDocumento'];
			$i++;
		}
		if(empty($datos['IgnorarArea']))
		{
			$s = 0;
			if(isset($_SESSION['IdArea']) && $_SESSION['IdArea']!="")
			{
				if(!is_array($_SESSION['IdArea']))
					$IdArea = explode(",",$_SESSION['IdArea']);
				else
					$IdArea = $_SESSION['IdArea'];
				$datosEnviar["query"]["bool"]["filter"][$i]["bool"]["should"][$s]["terms"]["Area.Id"] = $IdArea;
				$s++;
			}
			$datosEnviar["query"]["bool"]["filter"][$i]["bool"]["should"][$s]["exists"] = array("field"=>"MovimientoFecha");
			$s++;
			$i++;
		}elseif(!empty($datos['SoloMovimientoFecha']))
		{
			$datosEnviar["query"]["bool"]["filter"][$i]["bool"]["should"][$s]["exists"] = array("field"=>"MovimientoFecha");
			$s++;
			$i++;
		}

		$datosEnviar["query"]["bool"]["filter"][$i]["term"]["Tipo"] = TIPODOC;
		if(isset($datos['Tipo']) && $datos['Tipo']!="")
			$datosEnviar["query"]["bool"]["filter"][$i]["term"]["Tipo"] = $datos['Tipo'];
		$i++;


		if(!isset($datos['IdBuscador']) || $datos['IdBuscador']=="")
			$datos['IdBuscador'] = 1;

		$this->CamposBusquedaDocumento($datos,$datosEnviar);

		if(empty($datos['SinAggs']))
		{
			$datosEnviar['aggs'] = array();
			$datosEnviar['aggs']['menu'] = array();
			$datosEnviar['aggs']['menu']['terms'] = array();
			$datosEnviar['aggs']['menu']['terms']['script'] = array();//array("field"=>"TipoDocumento.Nombre.raw");
			//cuando actualicemos a elastic 6.3 cambiar inline por source
			$datosEnviar['aggs']['menu']['terms']['script']['inline'] = "doc['TipoDocumento.Id'].value + '".SEPARADORDATOS."' + doc['TipoDocumento.Nombre.raw'].value";
			$datosEnviar['aggs']['menu']['terms']['script']['lang'] = "painless";
			$datosEnviar['aggs']['menu']['terms']['size'] = 10000;
			$datosEnviar['aggs']['menu']['terms']['order'] = array("_count"=>"desc");
		}

		$dataEnvio = json_encode($datosEnviar,JSON_UNESCAPED_SLASHES);
		file_put_contents(PUBLICA."general.json",$dataEnvio);

		$header = array("Content-Type: application/json");
		curl_setopt($this->ch, CURLOPT_HTTPHEADER, $header);
		curl_setopt($this->ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($this->ch, CURLOPT_URL, $urlBase);
		curl_setopt($this->ch, CURLOPT_CUSTOMREQUEST, "POST");
		curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($this->ch, CURLOPT_POSTFIELDS,$dataEnvio);
		$result = curl_exec($this->ch);
		$resultJson = json_decode($result,1);

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



	public function BuscarDocumentos($datos,&$resultJson)
	{
		if(empty($datos['Tipo']) && isset($this->TipoDatos) && $this->TipoDatos!="")
			$datos['Tipo'] = $this->TipoDatos;
		$i=0;
		$urlBase = ELASTICSERVER."/".INDICECLIENTE."/".$this->Type."_search";
		$datosEnviar = array();

		$SortField = "IdDocumento";
		$SortOrder = "desc";


		$datosEnviar['sort'] = array();
		if (isset($datos['SortField']) && is_array($datos['SortField']) && count($datos['SortField'])>0)
		{
			foreach($datos['SortField'] as $Order)
				$datosEnviar['sort'][][$Order['Field']] = array('order'=>$Order['Sort']);
		}else
				$datosEnviar['sort'][][$SortField] = array('order'=>$SortOrder);

		if (!isset($datos['from']))
			$datosEnviar['from'] = 0;
		else
			$datosEnviar['from'] = $datos['from'];
		if (!isset($datos['size']))
			$datosEnviar['size'] = PAGINAR;
		else
			$datosEnviar['size'] = $datos['size'];

		if (isset($datos['source']))
		{
			if(!is_array($datos['campos']))
				$datos['campos'] = explode(",",$datos['campos']);
			$datosEnviar['_source'] = $datos['campos'];
		}


		$i = 0;
		$datosEnviar["query"] = array();
		$datosEnviar["query"]["bool"] = array();
		$datosEnviar["query"]["bool"]["filter"] = array();

		if(CLIENTEOBLIGATORIO===true)
		{
			if(isset($_SESSION['IdCliente']) && $_SESSION['IdCliente']!="")
			{
				$datosEnviar["query"]["bool"]["filter"][$i]["term"]["IdCliente"] = $_SESSION['IdCliente'];
				$i++;
			}
			else
			{
				FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Debe ingresar un cliente",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
				return false;
			}
		}

		if(isset($datos['TipoDocumento']) && is_array($datos['TipoDocumento']) && count($datos['TipoDocumento'])>0)
		{
			$datosEnviar["query"]["bool"]["filter"][$i]["terms"]["TipoDocumento.Id"] = $datos['TipoDocumento'];
			$i++;
		}elseif(isset($datos['TipoDocumento']) && is_string($datos['TipoDocumento']))
		{
			$datosEnviar["query"]["bool"]["filter"][$i]["term"]["TipoDocumento.Id"] = $datos['TipoDocumento'];
			$i++;
		}

		if(isset($datos['IdRegistroTipoDocumento']) && $datos['IdRegistroTipoDocumento']!="")
		{
			$datosEnviar["query"]["bool"]["filter"][$i]["term"]["TipoDocumento.IdRegistro"] = $datos['IdRegistroTipoDocumento'];
			$i++;
		}
		if(isset($datos['IdDocumentoPadre']) && $datos['IdDocumentoPadre']!="")
		{
			$datosEnviar["query"]["bool"]["filter"][$i]["term"]["IdDocumentoPadre"] = $datos['IdDocumentoPadre'];
			$i++;
		}
		if(empty($datos['IgnorarArea']))
		{
			$s = 0;
			if(isset($_SESSION['IdArea']) && $_SESSION['IdArea']!="")
			{
				if(!is_array($_SESSION['IdArea']))
					$IdArea = explode(",",$_SESSION['IdArea']);
				else
					$IdArea = $_SESSION['IdArea'];
				$datosEnviar["query"]["bool"]["filter"][$i]["bool"]["should"][$s]["terms"]["Area.Id"] = $IdArea;
				$s++;
			}
			$oAreasTiposDocumentos = new cAreasTiposDocumentos($this->conexion);

			if (isset($datos['IdTipoDocumento']))
			{
				$datosBusqueda['IdTipoDocumento'] = $datos['IdTipoDocumento'];
				$datosBusqueda['IdArea'] = $_SESSION['IdArea'];
				if(!$oAreasTiposDocumentos->BuscarxIdTipoDocumentoxIdArea($datosBusqueda,$resultado,$numfilas))
					return false;
				if ($numfilas==1)
				{
					$datosEncontrados = $this->conexion->ObtenerSiguienteRegistro($resultado);
					if ($datosEncontrados['DocumentoVisualiza']==1)
					{
						$datosEnviar["query"]["bool"]["filter"][$i]["bool"]["should"][$s]["exists"] = array("field"=>"MovimientoFecha");
						$s++;
					}
				}
			}
			$i++;
		}

		$datosEnviar["query"]["bool"]["filter"][$i]["term"]["Tipo"] = TIPODOC;
		if(isset($datos['Tipo']) && $datos['Tipo']!="")
			$datosEnviar["query"]["bool"]["filter"][$i]["term"]["Tipo"] = $datos['Tipo'];
		$i++;

		$this->CamposBusquedaDocumento($datos,$datosEnviar);
		//print_r($datosEnviar);

		$dataEnvio = json_encode($datosEnviar);
		file_put_contents(PUBLICA."documentos.json",$dataEnvio);



		$header = array("Content-Type: application/json");
		curl_setopt($this->ch, CURLOPT_HTTPHEADER, $header);
		curl_setopt($this->ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($this->ch, CURLOPT_URL, $urlBase);
		curl_setopt($this->ch, CURLOPT_CUSTOMREQUEST, "POST");
		curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($this->ch, CURLOPT_POSTFIELDS,$dataEnvio);
		$result = curl_exec($this->ch);
		$resultJson = json_decode($result,1);

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



	public function BuscarAdjuntos($datos,&$resultado)
	{
		if(empty($datos['Tipo']) && isset($this->TipoDatos) && $this->TipoDatos!="")
			$datos['Tipo'] = $this->TipoDatos;
		$resultado = array();
		$i=0;
		$urlBase = ELASTICSERVER."/".INDICECLIENTE."/".$this->Type."_search";
		$datosEnviar = array();

		$SortField = "IdDocumento";
		$SortOrder = "desc";
		if(isset($datos['SortField']) && $datos['SortField']!="")
			$SortField = $datos['SortField'];
		if(isset($datos['SortOrder']) && $datos['SortOrder']!="")
			$SortOrder = $datos['SortOrder'];
		$datosEnviar['sort'] = array();
		$datosEnviar['sort'][0] = array();
		$datosEnviar['sort'][0][$SortField] = array('order'=>$SortOrder);


		if (!isset($datos['from']))
			$datosEnviar['from'] = 0;
		else
			$datosEnviar['from'] = $datos['from'];
		if (!isset($datos['size']))
			$datosEnviar['size'] = 1000;
		else
			$datosEnviar['size'] = $datos['size'];


		$i = 0;
		$datosEnviar["query"] = array();
		$datosEnviar["query"]["bool"] = array();
		$datosEnviar["query"]["bool"]["filter"] = array();
		if(isset($datos['IdDocumento']) && $datos['IdDocumento']!="")
		{
			$datosEnviar["query"]["bool"]["filter"][$i]["term"]["IdDocumento"] = $datos['IdDocumento'];
			$i++;
		}
		if(isset($datos['IdTipoArchivo']) && $datos['IdTipoArchivo']!="")
		{
			$datosEnviar["query"]["bool"]["filter"][$i]["term"]["TipoArchivo.Id"] = $datos['IdTipoArchivo'];
			$i++;
		}
		$datosEnviar["query"]["bool"]["filter"][$i]["term"]["Tipo"] = TIPOARCHIVO;
		$i++;

		$datosEnviar["query"]["bool"]["filter"][$i]["term"]["Estado.Id"] = ACTIVO;
		$i++;

		$dataEnvio = json_encode($datosEnviar);
		//file_put_contents(PUBLICA."asdfg.json",$dataEnvio);

		$header = array("Content-Type: application/json");
		curl_setopt($this->ch, CURLOPT_HTTPHEADER, $header);
		curl_setopt($this->ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($this->ch, CURLOPT_URL, $urlBase);
		curl_setopt($this->ch, CURLOPT_CUSTOMREQUEST, "POST");
		curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($this->ch, CURLOPT_POSTFIELDS,$dataEnvio);
		$result = curl_exec($this->ch);
		$resultJson = json_decode($result,1);

		if (isset($resultJson['error']))
		{
			$error = $resultJson['error']['root_cause'];
			$errorDescription = "";
			foreach($error as $dataError)
				$errorDescription .= utf8_decode($dataError['reason']." (".$dataError['type'].")")." - ";
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,$errorDescription,array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		if (count($resultJson['hits']['hits'])>0)
		{
			foreach($resultJson['hits']['hits'] as $hits)
			{
				$source = $hits['_source'];
				if($source['Estado']['Id'] == ELIMINADO)
					continue;
				$resultado['Adjuntos'][$source['TipoArchivo']['Id']][] = $source;
			}
			return true;
		}

		return true;
	}




	public function BuscarCantidadDocumentosHijosxId($datos,&$resultado)
	{
		//echo "<pre>";print_r($datos);echo("</pre>");
		$urlBase = ELASTICSERVER."/".INDICECLIENTE."/".$this->Type."_search";
		$dataEnviar = array();
		$dataEnviar['size'] = array();
		$f = $m = 0;
		$dataEnviar = array();
		$dataEnviar['size'] = 0;
		$dataEnviar['query'] = array();
		$dataEnviar['query']['bool'] = array();
		$dataEnviar['query']['bool']['filter'] = array();

		if(CLIENTEOBLIGATORIO===true)
		{
			if(isset($_SESSION['IdCliente']) && $_SESSION['IdCliente']!="")
			{
				$dataEnviar["query"]["bool"]["filter"][$f]["term"]["IdCliente"] = $_SESSION['IdCliente'];
				$f++;
			}
			else
			{
				FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Debe ingresar un cliente",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
				return false;
			}
		}

		if(isset($datos['IdDocumentoPadre']) && $datos['IdDocumentoPadre']!="")
		{
			$dataEnviar['query']['bool']['filter'][$f]['term'] = array('IdDocumentoPadre' => $datos['IdDocumentoPadre']);
			$f++;
		}
		if(isset($datos['Tipo']) && $datos['Tipo']!="")
		{
			$dataEnviar["query"]["bool"]["filter"][$f]["term"]["Tipo"] = $datos['Tipo'];
			$f++;
		}
		if(empty($datos['IgnorarArea']))
		{
			$s = 0;
			if(isset($_SESSION['IdArea']) && $_SESSION['IdArea']!="")
			{
				if(!is_array($_SESSION['IdArea']))
					$IdArea = explode(",",$_SESSION['IdArea']);
				else
					$IdArea = $_SESSION['IdArea'];
				$dataEnviar["query"]["bool"]["filter"][$f]["bool"]["should"][$s]["terms"]["Area.Id"] = $IdArea;
				$s++;
			}
			$dataEnviar["query"]["bool"]["filter"][$f]["bool"]["should"][$s]["exists"] = array("field"=>"MovimientoFecha");
			$s++;
			$f++;
		}
		$dataEnviar['aggs'] = array();
		$dataEnviar['aggs']['menu'] = array();
		$dataEnviar['aggs']['menu']['terms'] = array("field"=>"TipoDocumento.Id");
		$dataEnviar['aggs']['menu']['terms']['size'] = 1000;
		$dataEnviar['aggs']['menu']['terms']['order'] = array("_count"=>"desc");
		$dataEnvio = json_encode($dataEnviar);

		//file_put_contents(PUBLICA."hijos.json",$dataEnvio);

		$header = array("Content-Type: application/json");
		curl_setopt($this->ch, CURLOPT_HTTPHEADER, $header);
		curl_setopt($this->ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($this->ch, CURLOPT_URL, $urlBase);
		curl_setopt($this->ch, CURLOPT_CUSTOMREQUEST, "POST");
		curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($this->ch, CURLOPT_POSTFIELDS,$dataEnvio);


		$result = curl_exec($this->ch);
		$data = json_decode($result,true);
		if (!isset($data['hits']))
		{
			FuncionesElastic::MostrarError($data);
			return false;
		}
		$resultado = FuncionesPHPLocal::DecodificarUtf8($data);

		return true;
	}


	public function BuscarCantidadDocumentosHijosxId_Nombre($datos,&$resultado)
	{
		//echo "<pre>";print_r($datos);echo("</pre>");
		$urlBase = ELASTICSERVER."/".INDICECLIENTE."/".$this->Type."_search";
		$dataEnviar = array();
		$dataEnviar['size'] = array();
		$f = $m = 0;
		$dataEnviar = array();
		$dataEnviar['size'] = 0;
		$dataEnviar['query'] = array();
		$dataEnviar['query']['bool'] = array();
		$dataEnviar['query']['bool']['filter'] = array();

		if(isset($_SESSION['IdCliente']) && $_SESSION['IdCliente']!="")
		{
			$dataEnviar["query"]["bool"]["filter"][$i]["term"]["IdCliente"] = $_SESSION['IdCliente'];
			$i++;
		}
		else
		{
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Debe ingresar un cliente",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		if(isset($datos['IdDocumentoPadre']) && $datos['IdDocumentoPadre']!="")
		{
			$dataEnviar['query']['bool']['filter'][$f]['term'] = array('IdDocumentoPadre' => $datos['IdDocumentoPadre']);
			$f++;
		}
		if(isset($datos['Tipo']) && $datos['Tipo']!="")
		{
			$dataEnviar["query"]["bool"]["filter"][$f]["term"]["Tipo"] = $datos['Tipo'];
			$f++;
		}
		if(empty($datos['IgnorarArea']))
		{
			$s = 0;
			if(isset($_SESSION['IdArea']) && $_SESSION['IdArea']!="")
			{
				if(!is_array($_SESSION['IdArea']))
					$IdArea = explode(",",$_SESSION['IdArea']);
				else
					$IdArea = $_SESSION['IdArea'];
				$dataEnviar["query"]["bool"]["filter"][$f]["bool"]["should"][$s]["terms"]["Area.Id"] = $IdArea;
				$s++;
			}
			$dataEnviar["query"]["bool"]["filter"][$f]["bool"]["should"][$s]["exists"] = array("field"=>"MovimientoFecha");
			$s++;
			$f++;
		}
		$dataEnviar['aggs'] = array();
		$dataEnviar['aggs']['menu'] = array();
		$dataEnviar['aggs']['menu']['terms'] = array("field"=>"TipoDocumento.Nombre.raw");

		$dataEnvio = json_encode($dataEnviar);
		//file_put_contents(PUBLICA."hijos.json",$dataEnvio);

		$header = array("Content-Type: application/json");
		curl_setopt($this->ch, CURLOPT_HTTPHEADER, $header);
		curl_setopt($this->ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($this->ch, CURLOPT_URL, $urlBase);
		curl_setopt($this->ch, CURLOPT_CUSTOMREQUEST, "POST");
		curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($this->ch, CURLOPT_POSTFIELDS,$dataEnvio);


		$result = curl_exec($this->ch);
		$data = json_decode($result,true);
		if (!isset($data['hits']))
		{
			FuncionesElastic::MostrarError($data);
			return false;
		}
		$resultado = FuncionesPHPLocal::DecodificarUtf8($data);

		return true;
	}




	public function BuscarCantidadDocumentosDescendientes($datos,&$resultado)
	{
		//echo "<pre>";print_r($datos);echo("</pre>");
		$urlBase = ELASTICSERVER."/".INDICECLIENTE."/".$this->Type."_search";
		$dataEnviar = array();
		$dataEnviar['size'] = array();
		$f = $m = 0;
		$dataEnviar = array();
		$dataEnviar['size'] = 0;
		$dataEnviar['query'] = array();
		$dataEnviar['query']['bool'] = array();
		$dataEnviar['query']['bool']['filter'] = array();

		if(CLIENTEOBLIGATORIO===true)
		{
			if(isset($_SESSION['IdCliente']) && $_SESSION['IdCliente']!="")
			{
				$dataEnviar["query"]["bool"]["filter"][$i]["term"]["IdCliente"] = $_SESSION['IdCliente'];
				$i++;
			}
			else
			{
				FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Debe ingresar un cliente",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
				return false;
			}
		}

		if(isset($datos['IdDocumento']) && $datos['IdDocumento']!="")
		{
			$dataEnviar['query']['bool']['filter'][$f]['term'] = array('ArregloPadres' => $datos['IdDocumento']);
			$f++;
		}
		$dataEnviar['aggs'] = array();
		$dataEnviar['aggs']['menu'] = array();
		$dataEnviar['aggs']['menu']['terms'] = array("field"=>"TipoDocumento.Id");

		$dataEnvio = json_encode($dataEnviar);
		//print_r($dataEnvio);die;

		$header = array("Content-Type: application/json");
		curl_setopt($this->ch, CURLOPT_HTTPHEADER, $header);
		curl_setopt($this->ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($this->ch, CURLOPT_URL, $urlBase);
		curl_setopt($this->ch, CURLOPT_CUSTOMREQUEST, "POST");
		curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($this->ch, CURLOPT_POSTFIELDS,$dataEnvio);


		$result = curl_exec($this->ch);
		$data = json_decode($result,true);
		if (!isset($data['hits']))
		{
			FuncionesElastic::MostrarError($data);
			return false;
		}
		$resultado = FuncionesPHPLocal::DecodificarUtf8($data);

		return true;
	}

	public function BuscarxCodigo($datos,&$datosRegistro,&$numfilas)
	{
		if(empty($datos['Tipo']) && isset($this->TipoDatos) && $this->TipoDatos!="")
			$datos['Tipo'] = $this->TipoDatos;

		$datosRegistro = array();
		$numfilas = 0;
		$Id = self::_ObtenerId($datos);

		$urlBase = ELASTICSERVER."/".INDICECLIENTE."/".$this->Type.$Id;
		$header = array("Content-Type: application/json");
		curl_setopt($this->ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($this->ch, CURLOPT_URL, $urlBase);
		curl_setopt($this->ch, CURLOPT_CUSTOMREQUEST, "GET");
		curl_setopt($this->ch, CURLOPT_POSTFIELDS,"");
		curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, 1);

		//execute post
		$result = curl_exec($this->ch);
		$data = json_decode($result,true);
		if (!isset($data['found']))
		{
			FuncionesElastic::MostrarError($data);
			return false;
		}
		elseif($data['found']===false)
			return true;
		else
		{
			$numfilas = 1;
			$datosRegistro = $data['_source'];
			return true;
		}

	}

 	public function ArregloDocumentoPadres($IdDocumento,&$arrcat,&$nivelarbol)
	{
		$arrcat = array();
		if ($IdDocumento!="")
		{
			$datoscat['IdDocumento'] = $IdDocumento;
			$datoscat['Tipo'] = TIPODOC;
			if (!$this->BuscarxCodigo($datoscat,$resultado,$numfilas))
				return false;

			if ($numfilas == 1)
			{
				$filasub = FuncionesPHPLocal::DecodificarUtf8($resultado);
				$padre = "";
				if(isset($filasub['IdDocumentoPadre']) && $filasub['IdDocumentoPadre']!="")
					$padre=$filasub['IdDocumentoPadre'];
				$arrcat[]=$filasub;
				$nivelarbol++;

				if (!$this->ArregloDocumentoPadres($padre,$arrcat,$nivelarbol))
					return false;
				if(is_array($arrcat) && count($arrcat)>0 )
					$darvueltaarreglo=asort($arrcat);
			}
		}
		return true;
	}

	public function TraerCantidadDocumentosxIndices($datos,&$resultJson)
	{
		$cantidad= 0;
		$urlBase = ELASTICSERVER."/".INDICECLIENTE."/".$this->Type."_count";
		$jsonData['query'] = array();
		$i = 0;

		foreach($datos['campos'] as $data)
		{
			$j = 0;
			foreach($data as $clave=>$Info)
			{
				$jsonData['query']['bool']['should'][$i]['bool']['must'][$j]['term'] = $Info;
				$j++;
			}
			$jsonData['query']['bool']['should'][$i]['bool']['must'][$j]['term']['TipoDocumento.Id'] = $datos['IdTipoDocumento'];
			$j++;
			if ($datos['IdDocumento']!="")
			{
				$jsonData['query']['bool']['should'][$i]['bool']['must'][$j]['bool']['must_not']['term']['IdDocumento'] = $datos['IdDocumento'];
				$j++;
			}
			if(isset($datos['Tipo']) && $datos['Tipo']!="")
			{
				$jsonData["query"]["bool"]['should'][$i]['bool']['must'][$j]["term"]["Tipo"] = $datos['Tipo'];
				$j++;
			}
			$i++;
		}
		if (count($datos['campos'])==0)
		{
			$resultJson['count'] = 0;
			return true;
		}
		$jsonConsulta = json_encode($jsonData);

		$header = array("Content-Type: application/json");
		curl_setopt($this->ch, CURLOPT_HTTPHEADER, $header);
		curl_setopt($this->ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($this->ch, CURLOPT_URL, $urlBase);
		curl_setopt($this->ch, CURLOPT_CUSTOMREQUEST, "POST");
		curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($this->ch, CURLOPT_POSTFIELDS,$jsonConsulta);
		$result = curl_exec($this->ch);
		$resultJson = json_decode($result,1);

		if (isset($resultJson['error']))
		{
			$error = $resultJson['error']['root_cause'];
			$errorDescription = "";
			foreach($error as $dataError)
				$errorDescription .= utf8_decode($dataError['reason']." (".$dataError['type'].")")." - ";
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,$errorDescription,array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			return false;
		}

		return true;
	}

	public function Buscar($datosEnviar,&$resultJson)
	{

		$i=0;
		$urlBase = ELASTICSERVER."/".INDICECLIENTE."/".$this->Type."_search";
		$dataEnvio = json_encode($datosEnviar);
		$header = array("Content-Type: application/json");
		curl_setopt($this->ch, CURLOPT_HTTPHEADER, $header);
		curl_setopt($this->ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($this->ch, CURLOPT_URL, $urlBase);
		curl_setopt($this->ch, CURLOPT_CUSTOMREQUEST, "POST");
		curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($this->ch, CURLOPT_POSTFIELDS,$dataEnvio);
		$result = curl_exec($this->ch);
		$resultJson = json_decode($result,1);

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



	/*
	 * Recibe IdDocumento e IdTipoDocumento como arrays.
	 * Devuelve el numero de tipos de documentos dependientes con documentos ($completados)
	 * y el total de tipos de documentos dependientes como $total.
	 */
	public function BuscarCompletitudxIdDocumento($datos,&$completados,&$total)
	{
		$oDocumentosTipos = new cDocumentosTipos($this->conexion,$this->formato);
		foreach($datos['IdDocumento'] as $key=>$IdDocumento)
		{
			$resultado_hijos = $Cantidades = array();
			$numfilas_hijos = 0;
			$datosCant['IdDocumento'] = $filaDoc['IdDocumento'];
			if(!$this->BuscarCantidadDocumentosDescendientes($datosCant,$resultado_cantidades))
				return false;

			foreach($resultado_cantidades['aggregations']['menu']['buckets'] as $bucket)
				$Cantidades[$bucket['key']] = $bucket['doc_count'];
			$IdTipoDocumento = $filaDoc['IdTipoDocumento'];
			if(!$oDocumentosTipos->ArregloArbolHijosVigente($IdTipoDocumento,$resultado_hijos,$numfilas_hijos,date("Y"),date("m"),ACTIVO))
				return false;
			$completados = 1;
			$total = 1;
			foreach($resultado_hijos as $filaHijo)
				cAreasDocumentosElastic::calcularCompletadosHijos($filaHijo,$Cantidades,$completados,$total);
		}
		return true;
	}


	/*
	 * Recibe IdDocumento e IdTipoDocumento como arrays,
	 * y IdArea como entero. Devuelve el numero de tipos de documentos dependientes
	 * con documentos ($completados) y el total de tipos de documentos dependientes
	 * como $total.
	 */
	public function BuscarCompletitudxIdDocumentoxIdArea($datos,&$completados,&$total)
	{
		$oAreasElastic = new cAreasDocumentosElastic();
		foreach($datos['IdDocumento'] as $key=>$IdDocumento)
		{
			$Cantidades = array();
			$datosCant['IdDocumento'] = $IdDocumento;
			if(!$this->BuscarCantidadDocumentosDescendientes($datosCant,$resultado_cantidades))
				return false;

			foreach($resultado_cantidades['aggregations']['menu']['buckets'] as $bucket)
				$Cantidades[$bucket['key']] = $bucket['doc_count'];
			$datosElastic['IdArea'] = $datos['IdArea'];
			$datosElastic['IdTipoDocumento'] = $datos['IdTipoDocumento'][$key];
			if(!$oAreasElastic->BusquedaMenu($datosElastic,$resultado_hijos,$numfilas_hijos,$breadcrumb))
				return false;
			$completados = 1;
			$total = 1;
			foreach($resultado_hijos as $filaHijo)
				cAreasDocumentosElastic::calcularCompletadosHijos($filaHijo,$Cantidades,$completados,$total);

		}
		return true;
	}



	/*
	 * Recibe IdDocumento e IdTipoDocumento como arrays,
	 * y IdArea como entero. Devuelve el numero de tipos de documentos dependientes
	 * con documentos ($completados) y el total de tipos de documentos dependientes
	 * como $total.
	 */
	public function reIndex($IndexOrigen, $IndexDestino)
	{

		$dataEnviar['source'] = array();
		$dataEnviar['dest'] = array();
		$dataEnviar['source']['index'] = $IndexOrigen;
		$dataEnviar['dest']['index']= $IndexDestino;

		$dataEnvio = json_encode($dataEnviar);

		$i=0;
		$urlBase = ELASTICSERVER."/_reindex";
		$header = array("Content-Type: application/json");
		curl_setopt($this->ch, CURLOPT_HTTPHEADER, $header);
		curl_setopt($this->ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($this->ch, CURLOPT_URL, $urlBase);
		curl_setopt($this->ch, CURLOPT_CUSTOMREQUEST, "POST");
		curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($this->ch, CURLOPT_POSTFIELDS,$dataEnvio);
		$result = curl_exec($this->ch);
		$resultJson = json_decode($result,1);

		print_r($result);
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


	private function CamposBusquedaDocumento($datos,&$datosEnviar)
	{
		$arrayClaves = array();

		$oEstructuraCampos = new cEstructuraCampos($this->conexion);
		foreach ($datos as $Clave=>$valor)
		{
			if (!is_array($valor))
			{
				if(!ctype_alnum(str_replace($oEstructuraCampos->SimbolosValidos(), '', $Clave)))
				{
					FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Ha ocurrido un error en los campos de busqueda",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
					return false;
				}
				$arrayClaves[] = $Clave;
			}
		}

		$datosBusqueda['NombreCampo'] = $arrayClaves;

		if(!$oEstructuraCampos->BuscarTipoCamposxNombresCampo($datosBusqueda,$resultado,$numfilas))
			return false;

		$i=0;
		while ($CampoBusqueda = $this->conexion->ObtenerSiguienteRegistro($resultado))
		{
			if(!isset($CampoBusqueda['TipoCampoElastic']) || $CampoBusqueda['TipoCampoElastic']=="")
				continue;
			$NombreCampo = trim($CampoBusqueda['NombreCampo']);
			$TipoCampoElastic = trim($CampoBusqueda['TipoCampoElastic']);
			$ValorCampo = "";
			if (isset($datos[$NombreCampo]))
				$ValorCampo = self::LimpiarCampo($datos[$NombreCampo],$TipoCampoElastic,$SearchType);
			switch($TipoCampoElastic)
			{
				case "text":
					if($ValorCampo!="")
					{
						$pattern = array(utf8_decode("/[ÁáÀàÂâÄäÃã]/"),utf8_decode("/[ÉéÈèËëÊê]/"),utf8_decode("/[ÍíÌìÏïÎî]/"),utf8_decode("/[ÓóÒòÖöÔôÕõ]/"),utf8_decode("/[ÚúÙùÜüÛû]/"),utf8_decode("/[Ññ]/"),utf8_decode("/[Çç]/"),"/[\"']/");
						$replacement = array("a","e","i","o","u","n","c","'");
						$datosPreproc = preg_replace($pattern,$replacement,utf8_decode($ValorCampo));
						$query = preg_replace("/\b(['\-\w]+)\b/",$NombreCampo.":(($1~1)^2 OR *$1*)",strtolower($datosPreproc));
						$datosEnviar["query"]["bool"]["must"][$i]["query_string"]["query"] = $query;
						$datosEnviar["query"]["bool"]["must"][$i]["query_string"]['default_operator'] = "AND";
						//$datosEnviar["query"]["bool"]["must"][$i]["query_string"]["query"] = "(".$NombreCampo.":".strtolower($ValorCampo)."~2)^2 OR ".$NombreCampo.":*".strtolower($ValorCampo)."*";

						$i++;
					}

				break;

				default:
					if($ValorCampo!="")
					{
						$datosEnviar["query"]["bool"]["must"][$i][$SearchType][$NombreCampo] = $ValorCampo;
						$i++;
					}
				break;
			}
		}

		file_put_contents(PUBLICA."general_array.txt",print_r($datosEnviar,true));
		/*
		if(!isset($datos['IdRegistroTipoDocumento']) || $datos['IdRegistroTipoDocumento'] == "")
			return false;
		$i = 0;
		$file = CARPETA_SERVIDOR_MULTIMEDIA_CLIENTE_FISICA."cliente_".CLIENTE."/json/buscador_".$datos['IdRegistroTipoDocumento'].".json";
		if (!file_exists($file))
			return false;

		$jsonData = file_get_contents($file);
		$data = json_decode($jsonData,true);
		foreach($data as $CampoBusqueda)
		{
			if(!isset($CampoBusqueda['TipoCampoElastic']) || $CampoBusqueda['TipoCampoElastic']=="")
				continue;
			$NombreCampo = trim($CampoBusqueda['NombreCampo']);
			$TipoCampoElastic = trim($CampoBusqueda['TipoCampoElastic']);
			$ValorCampo = "";
			if (isset($datos[$NombreCampo]))
				$ValorCampo = self::LimpiarCampo($datos[$NombreCampo],$TipoCampoElastic,$SearchType);
			switch($TipoCampoElastic)
			{
				case "text":
					if($ValorCampo!="")
					{
						$datosEnviar["query"]["bool"]["must"][$i]["query_string"]["query"] = $NombreCampo.":*".strtolower($ValorCampo)."*";

						$i++;
					}
				break;

				default:
					if($ValorCampo!="")
					{
						$datosEnviar["query"]["bool"]["must"][$i][$SearchType][$NombreCampo] = strtolower($ValorCampo);
						$i++;
					}
				break;
			}
		}*/

		return true;
	}


	private static function CamposBusquedaGeneral($datos,&$datosEnviar)
	{

		if(!isset($datos['IdRegistroTipoDocumento']) || $datos['IdRegistroTipoDocumento'] == "")
			return false;
		$i = 0;
		$file = CARPETA_SERVIDOR_MULTIMEDIA_CLIENTE_FISICA."cliente_{$_SESSION['IdCliente']}/json/buscador_general_{$datos['IdBuscador']}.json";
		if (!file_exists($file))
			return false;

		$jsonData = file_get_contents($file);
		$data = json_decode($jsonData,true);
		foreach($data as $CampoBusqueda)
		{
			if(!isset($CampoBusqueda['TipoCampoElastic']) || $CampoBusqueda['TipoCampoElastic']=="")
				continue;
			$NombreCampo = trim($CampoBusqueda['NombreCampo']);
			$TipoCampoElastic = trim($CampoBusqueda['TipoCampoElastic']);
			$ValorCampo = "";
			if (isset($datos[$NombreCampo]))
				$ValorCampo = self::LimpiarCampo($datos[$NombreCampo],$TipoCampoElastic,$SearchType);
			switch($TipoCampoElastic)
			{
				case "text":
					if($ValorCampo!="")
					{
						$datosEnviar["query"]["bool"]["must"][$i]["query_string"]["query"] = $NombreCampo.":*".strtolower($ValorCampo)."*";
						$i++;
					}
				break;

				default:
					if($ValorCampo!="")
					{
						$datosEnviar["query"]["bool"]["must"][$i][$SearchType][$NombreCampo] = strtolower($ValorCampo);
						$i++;
					}
				break;
			}
		}

		return true;
	}


	private  function LimpiarCampo(&$Campo,$TipoCampoElastic,&$SearchType)
	{
		$SearchType = "match";
		if ($Campo=="")
			return "";
		switch($TipoCampoElastic)
		{

			case "date":
				if (isset($Campo) && trim($Campo)!="")
				{
					if(!preg_match("/^[0-9]{2}\/[0-9]{2}\/[0-9]{4}$/",$Campo))
						return "";
					list($dia, $mes, $anio) = explode ('/', $Campo);
					if(!checkdate($mes, $dia, $anio))
						return "";

					$Campo = FuncionesPHPLocal::ConvertirFecha($Campo,"dd/mm/aaaa","aaaa-mm-dd");
				}
			break;

			case "short":
				if(!FuncionesPHPLocal::validarNumerico($Campo,32767))
					return -1;
			case "integer":
				if(!FuncionesPHPLocal::validarNumerico($Campo,2147483647))
					return -1;
			break;
			case "long":
				if(!FuncionesPHPLocal::validarNumerico($Campo,NULLDATE))
					return -1;

			case "scaled_float":
				if(!preg_match("/^-?[0-9]+([.][0-9]*)?$/",$Campo))
					return -1;
			break;

			case "boolean":
				$SearchType = "term";
				if(!in_array($Campo,array("true","false")))
					return "";
			break;

			case "text"://"/]/","/[]/","/[]/","/[]/","/[]/","/[/","/[]/");
				if(!preg_match("/^[\wÁáÀàÂâÄäÃãÉéÈèËëÊêÍíÌìÏïÎîÓóÒòÖöÔôÕõÚúÙùÜüÛûÑñÇç\"\']/",$Campo))
					return "-1";
			break;

			default:
				//$Campo = utf8_encode($Campo);
			break;

		}
		return $Campo;
	}

	private static function _ObtenerId($datos)
	{
		switch($datos['Tipo'])
		{
			case TIPODOC:
				if(isset($datos['IdDocumento']) && is_numeric($datos['IdDocumento']))
					return PREFIJODOC.$datos['IdDocumento'];
				else
					return false;
			break;
			case TIPOARCHIVO:
				if(isset($datos['IdArchivo']) && $datos['IdArchivo']!="")
					return PREFIJOARCHIVO.$datos['IdArchivo'];
				else
					return false;
			break;
			default:
				return false;
			break;
		}
	}




}
?>
