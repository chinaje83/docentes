<?php
//-----------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------
// Clase con la lógica para las busquedas relacionadas a documentos de clientes
class cLicenciasEscuela
{


	protected $oCurl;
	protected $conexion;
	protected $formato;
	protected $TipoDatos;
	protected $error;
	const INDEX = INDICESUNA;
	
	// Constructor de la clase
	function __construct($conexion,$formato=FMT_TEXTO){
		$this->conexion = &$conexion;
		$this->formato = $formato;
		$this->oCurl = new CurlBigtree();
	}
	
	// Destructor de la clase
	function __destruct() {
		/*curl_close($this->ch);*/
		$this->oCurl->CloseCurl();
    }
	
	
	public function SetearTipoDatos($TipoDatos)
	{
		$this->TipoDatos = $TipoDatos;
	}


	public function BuscarInasistenciasMenu($datos,&$resultJson)
	{
		if(empty($datos['Tipo']) && isset($this->TipoDatos) && $this->TipoDatos!="")
			$datos['Tipo'] = $this->TipoDatos;
		$i=0;
		$urlBase = ELASTICSERVER."/".INDICE.INDICESUNA;
		if(INCLUDETYPE)
			$urlBase .= '/'.TYPE;
		$urlBase .= "/_search";
		if (RESTTOTALHITS==true)
			$urlBase .= "?rest_total_hits_as_int=true";
		if (TOTALHISTTRACK==true)
			$urlBase .= "?track_total_hits=true";

		$datosEnviar = array();
		
		$SortField = "IdDocumento";
		$SortOrder = "desc";
		

		$datosEnviar['from'] = 0;
		$datosEnviar['size'] = 0;

		$i = 0;
		$datosEnviar["query"] = array();
		$datosEnviar["query"]["bool"] = array();
		$datosEnviar["query"]["bool"]["filter"] = array();
		
		
		$f = 0;
		/*print_r($datos['IdEstado']);*/
		if(isset($datos['ClaveEscuela']) && $datos['ClaveEscuela']!="")
		{
			/*print_r('ñaña');*/
			$datosEnviar["query"]["bool"]["filter"][$i]["term"]["ClaveEscuela"] = $datos['ClaveEscuela'];
			$i++;
		}
		
		if(isset($datos['Distrito']) && $datos['Distrito']!="")
		{
			$datosEnviar["query"]["bool"]["filter"][$i]["term"]["Distrito"] = $datos['Distrito'];
			$i++;
		}
		if(isset($datos['IdArea']) && $datos['IdArea']!="")
		{
			$datosEnviar["query"]["bool"]["filter"][$i]["term"]["Area.Id"] = $datos['IdArea'];
			$i++;
		}

		if(isset($datos['IdEstado']) && $datos['IdEstado']!="" && is_array($datos['IdEstado']))
		{
			$datosEnviar["query"]["bool"]["filter"][$i]["terms"]["Estado.Id"] = $datos['IdEstado'];
			$i++;
		}
		else{
			$datosEnviar["query"]["bool"]["filter"][$i]["bool"]["must_not"]["term"]["Estado.Id"] =99;
			$i++;
		}

		if(isset($datos['IdCategoria']) && $datos['IdCategoria']!="")
		{
			$datosEnviar["query"]["bool"]["filter"][$i]["term"]["TipoDocumento.Categoria.Id"] = $datos['IdCategoria'];
			$i++;
		}
		
		if(isset($datos['IdClasificacion']) && $datos['IdClasificacion']!="")
		{
			$datosEnviar["query"]["bool"]["filter"][$i]["term"]["TipoDocumento.Clasificacion.Id"] = $datos['IdClasificacion'];
			$i++;
		}


		if(isset($datos['TaskId']) && $datos['TaskId']!="")
        {
            $datosEnviar["query"]["bool"]["filter"][$i]["term"]["TaskId"] = $datos['TaskId'];
            $i++;
        }
		
		if(isset($datos['TipoDocumento']) && is_array($datos['TipoDocumento']) && count($datos['TipoDocumento'])>0)
		{
			$datosEnviar["query"]["bool"]["filter"][$i]["terms"]["TipoDocumento.Id"] = $datos['TipoDocumento'];
			$i++;
		}elseif(isset($datos['TipoDocumento']) && is_numeric($datos['TipoDocumento']))
		{
			$datosEnviar["query"]["bool"]["filter"][$i]["term"]["TipoDocumento.Id"] = $datos['TipoDocumento'];
			$i++;
		}


		if(isset($datos['IdDocumentoPadre']) && $datos['IdDocumentoPadre']!="")
		{
			$datosEnviar["query"]["bool"]["filter"][$i]["term"]["IdDocumentoPadre"] = $datos['IdDocumentoPadre'];
			$i++;
		}


		if(isset($datos['FechaDesde']) && $datos['FechaDesde']!="")
		{
			$datosEnviar["query"]["bool"]["filter"][$i]["range"][$Campo]['gte'] = $datos['FechaDesde']." 00:00:00";
			if (isset($datos['FechaHasta']) && $datos['FechaHasta']!="")
				$datosEnviar["query"]["bool"]["filter"][$i]["range"][$Campo]['lt'] = $datos['FechaHasta']." 23:59:59";
			else
				$datosEnviar["query"]["bool"]["filter"][$i]["range"][$Campo]['lte'] = date("d/m/Y H:i:s");
			
			$datosEnviar["query"]["bool"]["filter"][$i]["range"][$Campo]['format'] = "dd/MM/yyyy HH:mm:ss";
			$i++;
		}
		
		$datosEnviar["query"]["bool"]["filter"][$i]["term"]["Tipo"] = TIPODOC;
		if(isset($datos['Tipo']) && $datos['Tipo']!="")
			$datosEnviar["query"]["bool"]["filter"][$i]["term"]["Tipo"] = $datos['Tipo'];
		$i++;

		
		
		$iMust = 0;
		if(isset($datos['AgenteNombre']) && $datos['AgenteNombre']!="")
		{
			$NombreCampo = "Agente.Nombre";
			$pattern = array(utf8_decode("/[ÁáÀàÂâÄäÃã]/"),utf8_decode("/[ÉéÈèËëÊê]/"),utf8_decode("/[ÍíÌìÏïÎî]/"),utf8_decode("/[ÓóÒòÖöÔôÕõ]/"),utf8_decode("/[ÚúÙùÜüÛû]/"),utf8_decode("/[Ññ]/"),utf8_decode("/[Çç]/"),"/[\"']/");
			$replacement = array("a","e","i","o","u","n","c","'");
			$datosPreproc = strtolower(preg_replace($pattern,$replacement,utf8_decode($datos['AgenteNombre'])));
			$query = preg_replace("/\b(['\-\w]+)\b/",$NombreCampo.":(($1~2)^2 OR *$1*)",$datosPreproc);
			//$query = preg_replace("/\b(['\-\w]+)\b/",$NombreCampo.":(($1~2)^2 OR *$1*)",$datosPreproc)." OR \"{$datosPreproc}\"~5";
			$datosEnviar["query"]["bool"]["must"][$iMust]["query_string"]["query"] = $query;
			$datosEnviar["query"]["bool"]["must"][$iMust]["query_string"]['default_operator'] = "AND";
			$iMust++;
		}
		if(isset($datos['AgenteCuil']) && $datos['AgenteCuil']!="")
		{
			$datosEnviar["query"]["bool"]["must"][$iMust]["term"]["Agente.Cuil"] = $datos['AgenteCuil'];
			$iMust++;
		}

		$datosEnviar['aggs']['TiposDocumentos'] = array();
		$datosEnviar['aggs']['TiposDocumentos']['terms'] = array();
		$datosEnviar['aggs']['TiposDocumentos']['terms']['field'] = "TipoDocumento.Id";
		$datosEnviar['aggs']['TiposDocumentos']['terms']['size'] = count($datos['TipoDocumento']);

		
		$datosEnviar['aggs']['TiposDocumentos']['aggs']['NombreTipoDocumento']['top_hits']['size'] = 1;
		$datosEnviar['aggs']['TiposDocumentos']['aggs']['NombreTipoDocumento']['top_hits']['_source']['include'] = "TipoDocumento.Nombre";
		
	
		
		$datosEnviar['aggs']['Estados'] = array();
		$datosEnviar['aggs']['Estados']['terms'] = array();
		$datosEnviar['aggs']['Estados']['terms']['field'] = "Estado.Id";
		$datosEnviar['aggs']['Estados']['terms']['size'] = 20;

		
		$dataEnvio = json_encode($datosEnviar);

	

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
		
		if (isset($resultJson['hits']['total']['value']) && is_numeric($resultJson['hits']['total']['value']))
			$resultJson['hits']['total']=$resultJson['hits']['total']['value'];
			
		if (isset($resultJson['acknowledged']) && $resultJson['acknowledged']===true)
			return true;

		return true;
	}

	
	public function BuscarInasistencias($datos,&$resultJson)
	{
		if(empty($datos['Tipo']) && isset($this->TipoDatos) && $this->TipoDatos!="")
			$datos['Tipo'] = $this->TipoDatos;
		$i=0;
		$urlBase = ELASTICSERVER."/".INDICE.INDICESUNA;
		if(INCLUDETYPE)
			$urlBase .= '/'.TYPE;
		$urlBase .= "/_search";
		if (RESTTOTALHITS==true)
			$urlBase .= "?rest_total_hits_as_int=true";
		if (TOTALHISTTRACK==true)
			$urlBase .= "?track_total_hits=true";

		$datosEnviar = array();
		
		$SortField = "IdDocumento";
		$SortOrder = "desc";
		

		$datosEnviar['sort'] = array();
		if (isset($datos['SortField']) && is_array($datos['SortField']) && count($datos['SortField'])>0)
		{
			foreach($datos['SortField'] as $Order)
				$datosEnviar['sort'][][$Order['Field']] = array('order'=>$Order['Sort']);
		}else{
			if (isset($datos['SortField']) && $datos['SortField']!="")
				$SortField = $datos['SortField'];
			if (isset($datos['Sort']) && $datos['Sort']!="")
				$SortOrder = $datos['Sort'];
			
			$datosEnviar['sort'][][$SortField] = array('order'=>$SortOrder);
		}
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
		
		
		$f = 0;
		/*print_r($datos['IdEstado']);*/
		if(isset($datos['ClaveEscuela']) && $datos['ClaveEscuela']!="")
		{
			/*print_r('ñaña');*/
			$datosEnviar["query"]["bool"]["filter"][$i]["term"]["ClaveEscuela"] = $datos['ClaveEscuela'];
			$i++;
		}
		
		if(isset($datos['Distrito']) && $datos['Distrito']!="")
		{
			$datosEnviar["query"]["bool"]["filter"][$i]["term"]["Distrito"] = $datos['Distrito'];
			$i++;
		}
		if(isset($datos['IdArea']) && $datos['IdArea']!="")
		{
			$datosEnviar["query"]["bool"]["filter"][$i]["term"]["Area.Id"] = $datos['IdArea'];
			$i++;
		}

		if(isset($datos['IdEstado']) && $datos['IdEstado']!="" && is_array($datos['IdEstado']) && count($datos['IdEstado'])>0)
		{
			$datosEnviar["query"]["bool"]["filter"][$i]["terms"]["Estado.Id"] = $datos['IdEstado'];
			$i++;
		}
		else{
			$datosEnviar["query"]["bool"]["filter"][$i]["bool"]["must_not"]["term"]["Estado.Id"] =99;
			$i++;
		}

		if(isset($datos['IdCategoria']) && $datos['IdCategoria']!="")
		{
			$datosEnviar["query"]["bool"]["filter"][$i]["term"]["TipoDocumento.Categoria.Id"] = $datos['IdCategoria'];
			$i++;
		}
		
		if(isset($datos['IdClasificacion']) && $datos['IdClasificacion']!="")
		{
			$datosEnviar["query"]["bool"]["filter"][$i]["term"]["TipoDocumento.Clasificacion.Id"] = $datos['IdClasificacion'];
			$i++;
		}


		if(isset($datos['TaskId']) && $datos['TaskId']!="")
        {
            $datosEnviar["query"]["bool"]["filter"][$i]["term"]["TaskId"] = $datos['TaskId'];
            $i++;
        }
		
		if(isset($datos['TipoDocumento']) && is_array($datos['TipoDocumento']) && count($datos['TipoDocumento'])>0)
		{
			$datosEnviar["query"]["bool"]["filter"][$i]["terms"]["TipoDocumento.Id"] = $datos['TipoDocumento'];
			$i++;
		}elseif(isset($datos['TipoDocumento']) && is_numeric($datos['TipoDocumento']))
		{
			$datosEnviar["query"]["bool"]["filter"][$i]["term"]["TipoDocumento.Id"] = $datos['TipoDocumento'];
			$i++;
		}


		if(isset($datos['IdDocumentoPadre']) && $datos['IdDocumentoPadre']!="")
		{
			$datosEnviar["query"]["bool"]["filter"][$i]["term"]["IdDocumentoPadre"] = $datos['IdDocumentoPadre'];
			$i++;
		}

		$Campo = "MovimientoFecha"; //$Campo = "FechaEnvio";//"FechaEnvio"
		if(isset($datos['Enviados']) && $datos['Enviados']!="")
		{
			
			switch($datos['Enviados'])
			{
				case 1:
					$datosEnviar["query"]["bool"]["filter"][$i]["bool"]["must_not"]["exists"]["field"] = $Campo;
					break;
				case 2:
					$mesAnterior = date("01/m/Y", strtotime("-1 month"));
					$mesActual = date("01/m/Y");
					$datosEnviar["query"]["bool"]["filter"][$i]["range"][$Campo]['gte'] = $mesAnterior;
					$datosEnviar["query"]["bool"]["filter"][$i]["range"][$Campo]['lt'] = $mesActual;
					$datosEnviar["query"]["bool"]["filter"][$i]["range"][$Campo]['format'] = "dd/MM/yyyy";
					break;
				case 3:
					$anioActual = date("Y");
					$anioSiguiente = date("Y", strtotime("+1 year"));
					$datosEnviar["query"]["bool"]["filter"][$i]["range"][$Campo]['gte'] = $anioActual;
					$datosEnviar["query"]["bool"]["filter"][$i]["range"][$Campo]['lte'] = $anioSiguiente;
					$datosEnviar["query"]["bool"]["filter"][$i]["range"][$Campo]['format'] = "yyyy";
					break;
				case 4:
					$anioAnterior = date("Y", strtotime("-1 year"));
					$anioActual = date("Y");
					$datosEnviar["query"]["bool"]["filter"][$i]["range"][$Campo]['gte'] = $anioAnterior;
					$datosEnviar["query"]["bool"]["filter"][$i]["range"][$Campo]['lte'] = $anioActual;
					$datosEnviar["query"]["bool"]["filter"][$i]["range"][$Campo]['format'] = "yyyy";
					break;
				case 5:
					$mesSiguiente = date("01/m/Y", strtotime("+1 month"));
					$mesActual = date("01/m/Y");
					$datosEnviar["query"]["bool"]["filter"][$i]["range"][$Campo]['gte'] = $mesActual;
					$datosEnviar["query"]["bool"]["filter"][$i]["range"][$Campo]['lt'] = $mesSiguiente;
					$datosEnviar["query"]["bool"]["filter"][$i]["range"][$Campo]['format'] = "dd/MM/yyyy";
					break;
					
			}
			
			$i++;
		}
		

		if(isset($datos['FechaDesde']) && $datos['FechaDesde']!="")
		{
			$datosEnviar["query"]["bool"]["filter"][$i]["range"][$Campo]['gte'] = $datos['FechaDesde']." 00:00:00";
			if (isset($datos['FechaHasta']) && $datos['FechaHasta']!="")
				$datosEnviar["query"]["bool"]["filter"][$i]["range"][$Campo]['lt'] = $datos['FechaHasta']." 23:59:59";
			else
				$datosEnviar["query"]["bool"]["filter"][$i]["range"][$Campo]['lte'] = date("d/m/Y H:i:s");
			
			$datosEnviar["query"]["bool"]["filter"][$i]["range"][$Campo]['format'] = "dd/MM/yyyy HH:mm:ss";
			$i++;
		}
		
		$datosEnviar["query"]["bool"]["filter"][$i]["term"]["Tipo"] = TIPODOC;
		if(isset($datos['Tipo']) && $datos['Tipo']!="")
			$datosEnviar["query"]["bool"]["filter"][$i]["term"]["Tipo"] = $datos['Tipo'];
		$i++;

		
		
		$iMust = 0;
		if(isset($datos['AgenteNombre']) && $datos['AgenteNombre']!="")
		{
			$NombreCampo = "Agente.Nombre";
			$pattern = array(utf8_decode("/[ÁáÀàÂâÄäÃã]/"),utf8_decode("/[ÉéÈèËëÊê]/"),utf8_decode("/[ÍíÌìÏïÎî]/"),utf8_decode("/[ÓóÒòÖöÔôÕõ]/"),utf8_decode("/[ÚúÙùÜüÛû]/"),utf8_decode("/[Ññ]/"),utf8_decode("/[Çç]/"),"/[\"']/");
			$replacement = array("a","e","i","o","u","n","c","'");
			$datosPreproc = strtolower(preg_replace($pattern,$replacement,utf8_decode($datos['AgenteNombre'])));
			$query = preg_replace("/\b(['\-\w]+)\b/",$NombreCampo.":(($1~2)^2 OR *$1*)",$datosPreproc);
			//$query = preg_replace("/\b(['\-\w]+)\b/",$NombreCampo.":(($1~2)^2 OR *$1*)",$datosPreproc)." OR \"{$datosPreproc}\"~5";
			$datosEnviar["query"]["bool"]["must"][$iMust]["query_string"]["query"] = $query;
			$datosEnviar["query"]["bool"]["must"][$iMust]["query_string"]['default_operator'] = "AND";
			$iMust++;
		}
		if(isset($datos['AgenteCuil']) && $datos['AgenteCuil']!="")
		{
			$datosEnviar["query"]["bool"]["must"][$iMust]["term"]["Agente.Cuil"] = $datos['AgenteCuil'];
			$iMust++;
		}
		$dataEnvio = json_encode($datosEnviar);
		//		echo $dataEnvio;

		//die;
	

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
		
		if (isset($resultJson['hits']['total']['value']) && is_numeric($resultJson['hits']['total']['value']))
			$resultJson['hits']['total']=$resultJson['hits']['total']['value'];
			
		if (isset($resultJson['acknowledged']) && $resultJson['acknowledged']===true)
			return true;

		return true;
	}

	
	public function getError()
	{
		return $this->error;
	}
	private function setError($error,$errordesc="")
	{
		$this->error['error']=$error;
		$this->error['error_description']=$errordesc;
	}
	
}//FIN CLASE

?>