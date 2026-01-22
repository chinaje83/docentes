<?php
//----------------------------------------------------------------------------------------- 
//----------------------------------------------------------------------------------------- 
// Clase con la lógica para las busquedas relacionadas a documentos de clientes
class cReportesElastic
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
		$this->oCurl = new CurlBigtree($this->conexion);    
	} 
	
	// Destructor de la clase
	function __destruct() {	
		/*curl_close($this->ch);*/
		$this->oCurl->CloseCurl();
    } 	
	
	
	public function SetearTipoDatos($TipoDatos){$this->TipoDatos = $TipoDatos;}
	
	
	public function getError()
	{
		return $this->error;
	}
	private function setError($error,$errordesc="")
	{
		$this->error['error']=$error;
		$this->error['error_description']=$errordesc;
	}
	
	public function BuscarDocumentosTodos($datos,&$resultJson)
	{
		//echo "<pre>".print_r($datos,true)."</pre>";
		$scroll = "";
		if(empty($datos['Tipo']) && isset($this->TipoDatos) && $this->TipoDatos!="")
			$datos['Tipo'] = $this->TipoDatos;
		if(!empty($datos['scroll']) && preg_match("/\d+[dhms]/",$datos['scroll']))
        {
            $scroll = "?scroll={$datos['scroll']}";
            unset($datos['scroll']);
        }
		$i=0;
		$urlBase = ELASTICSERVER."/".INDICE.INDICESUNA;
		if(INCLUDETYPE)
			$urlBase .= '/'.TYPE;

		$urlBase .= "/_search$scroll";
		
		
		if ($scroll!="" && RESTTOTALHITS==true)
			$urlBase.="&rest_total_hits_as_int=true";
		elseif(RESTTOTALHITS==true && $scroll=="")
			$urlBase.="?rest_total_hits_as_int=true";

		if ($scroll!="" && TOTALHISTTRACK==true)
			$urlBase.="&track_total_hits=true";
		elseif(TOTALHISTTRACK==true && $scroll=="")
			$urlBase.="?track_total_hits=true";


		$datosEnviar = array();
		
		$SortField = "MovimientoFecha";
		$SortOrder = "asc";
		

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
		if (!isset($datos['size'])){
			$datosEnviar['size'] = PAGINAR;
		}
		else{
			$datosEnviar['size'] = $datos['size'];	
		}
			
		if (isset($datos['campos']))
		{
			if(!is_array($datos['campos']))
				$datos['campos'] = explode(",",$datos['campos']);
			$datosEnviar['_source'] = $datos['campos'];
		}


		$i = 0;	
		$datosEnviar["query"] = array();
		$datosEnviar["query"]["bool"] = array();
		$datosEnviar["query"]["bool"]["must_not"] = array();
		if(isset($datos['OcultarEliminadas']) && 1 == $datos['OcultarEliminadas'])
			$datosEnviar["query"]["bool"]["must_not"][0]['term']['Estado.Id'] = 99;
		$datosEnviar["query"]["bool"]["filter"] = array();
			
		$f = 0;	
		if(isset($datos['ClaveEscuela']) && $datos['ClaveEscuela']!="")
		{
			$datosEnviar["query"]["bool"]["filter"][$i]["term"]["ClaveEscuela"] = $datos['ClaveEscuela'];
			$i++;	
		}else
		{		
		
			if(isset($datos['Distrito']) && $datos['Distrito']!="" && $datos['Distrito']!="000")
			{
				$datosEnviar["query"]["bool"]["filter"][$i]["terms"]["Distrito.Id"] = $datos['Distrito'];
				$i++;	
			}
			if(isset($datos['TipoOrganizacion']) && $datos['TipoOrganizacion']!="")
			{
				$datosEnviar["query"]["bool"]["filter"][$i]["term"]["TipoOrg.Id"] = strtoupper($datos['TipoOrganizacion']);
				$i++;	
			}	
			if(isset($datos['NroEscuela']) && $datos['NroEscuela']!="")
			{
				$datosEnviar["query"]["bool"]["filter"][$i]["term"]["Escuela.Id"] = $datos['NroEscuela'];
				$i++;	
			}
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

		if(isset($datos['IdDocumento']) && $datos['IdDocumento']!="")
		{
			$datosEnviar["query"]["bool"]["filter"][$i]["term"]["IdDocumento"] = $datos['IdDocumento'];
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

		if(isset($datos['IdEstado']) && $datos['IdEstado']!="")
		{
			$datosEnviar["query"]["bool"]["filter"][$i]["term"]["Estado.Id"] = $datos['IdEstado'];
			$i++;	
		}	
		if(isset($datos['IdArea']) && $datos['IdArea']!="")
		{
			$datosEnviar["query"]["bool"]["filter"][$i]["term"]["Area.Id"] = $datos['IdArea'];
			$i++;	
		}	
				
		

		if(isset($datos['IdDocumentoPadre']) && $datos['IdDocumentoPadre']!="")
		{
			$datosEnviar["query"]["bool"]["filter"][$i]["term"]["IdDocumentoPadre"] = $datos['IdDocumentoPadre'];
			$i++;
		}

        if(isset($datos['Adecuacion']) && $datos['Adecuacion']!="")
        {
            $valor = false;
            if($datos['Adecuacion']==1)
                $valor = true;

            $datosEnviar["query"]["bool"]["filter"][$i]["term"]["Adecuacion"] = $valor;
            $i++;
        }

        if(isset($datos['ClaveEscuelaDestino']) && $datos['ClaveEscuelaDestino']!="")
        {
            $datosEnviar["query"]["bool"]["filter"][$i]["term"]["ClaveEscuelaDestino"] = $datos['ClaveEscuelaDestino'];
            $i++;
        }
		
		$datosEnviar["query"]["bool"]["filter"][$i]["term"]["Tipo"] = TIPODOC;
		if(isset($datos['Tipo']) && $datos['Tipo']!="")
			$datosEnviar["query"]["bool"]["filter"][$i]["term"]["Tipo"] = $datos['Tipo'];
		$i++;

		
		//$Campo = "FechaEnvio";//"FechaEnvio"
		$Campo="MovimientoFecha";
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
		
		/*$datosEnviar["query"]["bool"]["filter"][$i]["term"]["Tipo"] = TIPODOC;
		if(isset($datos['Tipo']) && $datos['Tipo']!="")
			$datosEnviar["query"]["bool"]["filter"][$i]["term"]["Tipo"] = $datos['Tipo'];
		$i++;*/

		
		
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
		
		
		if(isset($datos['AgenteDNI']) && $datos['AgenteDNI']!="")
		{
			$datosEnviar["query"]["bool"]["must"][$iMust]["term"]["Agente.Dni"] = $datos['AgenteDNI'];
			$iMust++;
		}

		
		$dataEnvio = json_encode($datosEnviar);
		//echo $dataEnvio;
		//echo "1.1";
		$header = array("Content-Type: application/json");

		$fields_string = "";
		$this->oCurl->setFunction(get_class($this)."-".__FUNCTION__);
		//$this->oCurl->setDebug(true);
		$this->oCurl->setHeader($header);
		$this->oCurl->setUrl($urlBase);
		$this->oCurl->setHttpBuildPost(false);
		if(!$this->oCurl->sendPost($dataEnvio,$resultJson))
		{
			//echo "<pre>".print_r($resultJson,true)."</pre>";
			$this->setError("Error","Error, ocurrio un error al buscar los datos de las novedades");
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,$this->getError()['error_description'],array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
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


		return true;
	}


    public function Scroll($datos,&$dataResult)
    {
        $urlBase = ELASTICSERVER."/_search/scroll";
        $datosEnviar = new StdClass;
        $datosEnviar->scroll = $datos['scroll'];
        $datosEnviar->scroll_id = $datos['scroll_id'];

        $dataEnvio = json_encode($datosEnviar);
        //echo $dataEnvio;
        //file_put_contents(PUBLICA."documentos.json",$dataEnvio);



        $header = array("Content-Type: application/json");;
        $this->oCurl->setFunction(get_class($this)."-".__FUNCTION__);
        $this->oCurl->setHeader($header);
        $this->oCurl->setUrl($urlBase);
        $this->oCurl->setHttpBuildPost(false);
        if(!$this->oCurl->sendPost($dataEnvio,$dataResult))
        {
            $this->setError("Error","Error, ocurrio un error al buscar los datos de las novedades");
	        FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,$this->getError()['error_description'],array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
            return false;
        }


        if (isset($dataResult['error']))
        {
            $error = $dataResult['error']['root_cause'];
            $errorDescription = "";
            foreach($error as $dataError)
                $errorDescription .= utf8_decode($dataError['reason']." (".$dataError['type'].")")." - ";
            FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,$errorDescription,array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
            return false;
        }

        return true;
    }

    public function clearScroll($scroll_id)
    {
        $urlBase = ELASTICSERVER."/_search/scroll/$scroll_id";

        $dataEnvio = [];

        $header = array("Content-Type: application/json");;
        $this->oCurl->setFunction(get_class($this)."-".__FUNCTION__);
        $this->oCurl->setHeader($header);
        $this->oCurl->setUrl($urlBase);
        $this->oCurl->setHttpBuildPost(false);

        if(!$this->oCurl->sendDelete($dataEnvio,$dataResult))
        {
            $this->setError("Error","Error, ocurrio un error al buscar los datos de las novedades");
            return false;
        }

        if (isset($dataResult['error']))
        {
            $error = $dataResult['error']['root_cause'];
            $errorDescription = "";
            foreach($error as $dataError)
                $errorDescription .= utf8_decode($dataError['reason']." (".$dataError['type'].")")." - ";
            FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,$errorDescription,array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
            return false;
        }

        return true;
    }
	
	
	/**
	 * @param array $datos
	 * @param array $datosDevueltos
	 * @return bool
	 */
	public function ReporteEscuelasInspectores(array $datos, &$datosDevueltos): bool{
	    $ff = -1;
		$datosEnviar = new stdClass();
		$datosEnviar->size = 0;
		$datosEnviar->query = new stdClass();
	    $datosEnviar->query->bool = new stdClass();
	    //$datosEnviar->query->bool->filter = [];
	    $filter = [];
	    $urlBase = ELASTICSERVER.'/'.INDICE.INDICESUNA.'/_search';
	
	    if(isset($datos['Distrito']) && $datos['Distrito']!='' && $datos['Distrito']!='000')
	    {
	    	$filter[++$ff] = new stdClass();
	    	$filter[$ff]->term = new stdClass();
	    	$filter[$ff]->term->{'Distrito.Id'} = $datos['Distrito'];
	    } elseif(!empty($datos['IdDistritos'])) {
		    $filter[++$ff] = new stdClass();
		    $filter[$ff]->terms = new stdClass();
		    $filter[$ff]->terms->{'Distrito.Id'} = is_array($datos['IdDistritos']) ? $datos['IdDistritos'] : explode(',', $datos['IdDistritos']);
	    }
	    
	    if(isset($datos['TipoOrganizacion']) && $datos['TipoOrganizacion']!='')
	    {
	    	$filter[++$ff] = new stdClass();
	    	$filter[$ff]->term = new stdClass();
	    	$filter[$ff]->term->{'TipoOrg.Id'} = strtoupper($datos['TipoOrganizacion']);
	    }
	    if(isset($datos['NroEscuela']) && $datos['NroEscuela']!="")
	    {
	    	$filter[++$ff] = new stdClass();
	    	$filter[$ff]->term = new stdClass();
	    	$filter[$ff]->term->{'Escuela.Id'} = $datos['NroEscuela'];
	    }
		if(isset($datos['CampoFecha']) && $datos['CampoFecha']!=''){
			$filter[++$ff] = new stdClass();
			$filter[$ff]->range = new stdClass();
			$filter[$ff]->range->{$datos['CampoFecha']} = new stdClass();
			$filter[$ff]->range->{$datos['CampoFecha']}->gte = empty($datos['FechaDesde']) ? date('Y-m-d||/\M') : $datos['FechaDesde'].'||/d';
			$filter[$ff]->range->{$datos['CampoFecha']}->lte = (empty($datos['FechaHasta']) ? date('Y-m-d') : $datos['FechaHasta']).'||/d';
		}
	    
	    
	    if(!empty($filter))
	    	$datosEnviar->query->bool->filter = $filter;
	    
	    $datosEnviar->aggs = new stdClass();
	    $datosEnviar->aggs->{'Escuelas'} = new stdClass();
	    $datosEnviar->aggs->{'Escuelas'}->terms = new stdClass();
	    $datosEnviar->aggs->{'Escuelas'}->terms->field = 'ClaveEscuela';
	    $datosEnviar->aggs->{'Escuelas'}->terms->size = 1000000;
	
	    $datosEnviar->aggs->{'Escuelas'}->aggs = new stdClass();
	
	    
	    $InasistenciasInjustificadas = new stdClass();
	    $InasistenciasInjustificadas->filter = new stdClass();
	    $InasistenciasInjustificadas->filter->terms = new stdClass();
	    $InasistenciasInjustificadas->filter->terms->{'TipoDocumento.Id'} = [121, 138];
	    $datosEnviar->aggs->{'Escuelas'}->aggs->{'InasistenciasInjustificadas'} = $InasistenciasInjustificadas;
	    
	    $Licencias = new stdClass();
	    $Licencias->filter = new stdClass();
	    $Licencias->filter->terms = new stdClass();
	    $Licencias->filter->terms->{'TipoDocumento.Id'} = [159, 160, 161, 162, 178];
	    $datosEnviar->aggs->{'Escuelas'}->aggs->{'Licencias'} = $Licencias;
	    
	    $Paros = new stdClass();
	    $Paros->filter = new stdClass();
	    $Paros->filter->terms = new stdClass();
	    $Paros->filter->terms->{'TipoDocumento.Id'} = [120, 137];
	    $datosEnviar->aggs->{'Escuelas'}->aggs->{'Paros'} = $Paros;
	    
	    $SolicitudesCobertura = new stdClass();
	    $SolicitudesCobertura->filter = new stdClass();
	    $SolicitudesCobertura->filter->terms = new stdClass();
	    $SolicitudesCobertura->filter->terms->{'TipoDocumento.Id'} = [145, 170, 171, 187, 192];
	    $SolicitudesCobertura->aggs = new stdClass();
	    $SolicitudesCobertura->aggs->{'Pendientes'} = new stdClass();
	    $SolicitudesCobertura->aggs->{'Pendientes'}->filter = new stdClass();
	    $SolicitudesCobertura->aggs->{'Pendientes'}->filter->term = new stdClass();
	    $SolicitudesCobertura->aggs->{'Pendientes'}->filter->term->{'Estado.Id'} = 105;
	    $datosEnviar->aggs->{'Escuelas'}->aggs->{'SolicitudesCobertura'} = $SolicitudesCobertura;
	    
	    $NovedadesSinEnviar = new stdClass();
	    $NovedadesSinEnviar->filter = new stdClass();
	    $NovedadesSinEnviar->filter->term = new stdClass();
	    $NovedadesSinEnviar->filter->term->{'Estado.Id'} = 1;
	    $datosEnviar->aggs->{'Escuelas'}->aggs->{'NovedadesSinEnviar'} = $NovedadesSinEnviar;
	    
	    $dataEnvio = json_encode($datosEnviar);
	    
	
	    $header = array("Content-Type: application/json");;
	    $this->oCurl->setFunction(get_class($this)."-".__FUNCTION__);
	    $this->oCurl->setHeader($header);
	    $this->oCurl->setUrl($urlBase);
	    $this->oCurl->setHttpBuildPost(false);
	    //$this->oCurl->setDebug(true);
	    if(!$this->oCurl->sendPost($dataEnvio,$dataResult))
	    {
		    $this->setError("Error","Error, ocurrio un error al buscar los datos de las novedades");
		    //FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,$this->getError()['error_description'],array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
		    return false;
	    }
	
	
	    if (isset($dataResult['error']))
	    {
		    $error = $dataResult['error']['root_cause'];
		    $errorDescription = "";
		    foreach($error as $dataError)
			    $errorDescription .= utf8_decode($dataError['reason']." (".$dataError['type'].")")." - ";
		    FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,$errorDescription,array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
		    return false;
	    }
	    //echo'<pre>'.print_r($dataResult,true);die('</pre>');
	    $datosDevueltos = $dataResult['aggregations']['Escuelas']['buckets'];
	    
	    
		return true;


    }
	
	
}//FIN CLASE

