<?php 
class cLicenciasElastic
{
	
	protected $conexion;
	protected $formato;
	protected $oCurl;

	// Constructor de la clase
	function __construct($conexion,$formato=FMT_TEXTO){
		$this->conexion = &$conexion;
		$this->formato = $formato;
		$this->oCurl = new CurlBigtree($this->conexion);  
		
    } 
	
	// Destructor de la clase
	function __destruct() 
	{	
		$this->oCurl->CloseCurl();
    } 	

//----------------------------------------------------------------------------------------- 
//----------------------------------------------------------------------------------------- 


	public function SubirLicenciasBulk($data)
	{
		
		$oModifElastic = new cModifElastic(INDICELICENCIAS);
		if(!$oModifElastic->ActualizarBulk($data))
			return false;

		return true;
	}
	
	public function CrearConexion(){$this->ch = curl_init();}
	
	public function CerrarConexion(){curl_close($this->ch);}
	

	
	public function BuscarLicencias($datos,&$resultJson)
	{
		$i=0;
		$urlBase = ELASTICSERVER."/".INDICE.INDICELICENCIAS."/".TYPE."/_search";
		$datosEnviar = array();
		
		$SortField = "FechaModificacion";
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
		
		$cuits=array();
		if(isset($datos["cuits"]) && is_array($datos["cuits"]) && count($datos["cuits"])>0)
		{
			$cuits=$datos["cuits"];
		}
		
		if (isset($cuits) && is_array($cuits) && count($cuits)>0)
		{
			$datosEnviar["query"]["bool"]["filter"][$i]["terms"]["Cuil"] = array_values($cuits);
			$i++;	
		}
		
		if(isset($datos['AgenteCuil']) && $datos['AgenteCuil']!="" && in_array($datos['AgenteCuil'],$cuits))
		{
			$datosEnviar["query"]["bool"]["filter"][$i]["term"]["Cuil"] = $datos['AgenteCuil'];
			$i++;	
		}
		
		if(isset($datos['Tipo']) && $datos['Tipo']!="")
		{
			$datosEnviar["query"]["bool"]["filter"][$i]["term"]["Tipo"] = $datos['Tipo'];
			$i++;	
		}
		
		if(isset($datos['IdEstadoProceso']) && $datos['IdEstadoProceso']!="")
		{
			$datosEnviar["query"]["bool"]["filter"][$i]["terms"]["IdEstadoProceso"] = $datos['IdEstadoProceso'];
			$i++;	
		}
		
		
		if(isset($datos['Encuadre']) && $datos['Encuadre']!="")
		{
			$datosEnviar["query"]["bool"]["should"][0]["term"]["Doc"] = $datos['Encuadre'];
			$datosEnviar["query"]["bool"]["should"][1]["term"]["Aux"] = $datos['Encuadre'];
		}


		$Campo="Actualizado";
		if(isset($datos['Actualizado']) && $datos['Actualizado']!="")
		{
			$datosEnviar["query"]["bool"]["filter"][$i]["range"][$Campo]['gte'] = date("Ymd");
			$datosEnviar["query"]["bool"]["filter"][$i]["range"][$Campo]['lte'] = date("Ymd");
			$datosEnviar["query"]["bool"]["filter"][$i]["range"][$Campo]['format'] = "yyyyMMdd";
			$i++;
		}

		
		$Campo="FechaInicio";
		if(isset($datos['FechaDesde']) && $datos['FechaDesde']!="")
		{
			$datosEnviar["query"]["bool"]["filter"][$i]["range"][$Campo]['gte']=FuncionesPHPLocal::ConvertirFecha($datos['FechaDesde'],"dd/mm/aaaa","aaaa-mm-dd")." 00:00:00";
			if (isset($datos['FechaHasta']) && $datos['FechaHasta']!="")
			{
				$FechaHastaTimeStamp=FuncionesPHPLocal::ConvertirFecha($datos['FechaHasta'],"dd/mm/aaaa","aaaa-mm-dd")." 23:59:59";
				$datosEnviar["query"]["bool"]["filter"][$i]["range"][$Campo]['lt'] =$FechaHastaTimeStamp;
			}
			else	
				$datosEnviar["query"]["bool"]["filter"][$i]["range"][$Campo]['lte'] = date("Y-m-d H:i:s");
			$datosEnviar["query"]["bool"]["filter"][$i]["range"][$Campo]['boost'] = "2.0";
			$i++;
		}

		$Campo="FechaFin";
		if(isset($datos['FechaDesde']) && $datos['FechaDesde']!="")
		{
			$datosEnviar["query"]["bool"]["filter"][$i]["range"][$Campo]['gte']=FuncionesPHPLocal::ConvertirFecha($datos['FechaDesde'],"dd/mm/aaaa","aaaa-mm-dd")." 00:00:00";
			if (isset($datos['FechaHasta']) && $datos['FechaHasta']!="")
			{
				$FechaHastaTimeStamp=FuncionesPHPLocal::ConvertirFecha($datos['FechaHasta'],"dd/mm/aaaa","aaaa-mm-dd")." 23:59:59";
				$datosEnviar["query"]["bool"]["filter"][$i]["range"][$Campo]['lt'] =$FechaHastaTimeStamp;
			}
			else	
				$datosEnviar["query"]["bool"]["filter"][$i]["range"][$Campo]['lte'] = date("Y-m-d H:i:s");
			$datosEnviar["query"]["bool"]["filter"][$i]["range"][$Campo]['boost'] = "2.0";
			$i++;
		}
		
		if(isset($datos["TaskIds"]) && is_array($datos["TaskIds"]) && count($datos["TaskIds"])>0)
		{
			$datosEnviar["query"]["bool"]["filter"][$i]["terms"]["TaskId"] =$datos["TaskIds"];
			$i++;	
		}
		
		if(isset($datos["StatusLicencia"]) && $datos["StatusLicencia"]!="")
		{
			if ($datos["StatusLicencia"]=="X")
			{
				$datos["StatusLicencia"]=array("XAPPROVED", "XJUNTA", "XVISITACMZ","XAUSENTE","XREVISION");
				$datosEnviar["query"]["bool"]["must"][0]["terms"]["StatusLicencia"] =$datos["StatusLicencia"];
			}
			else
			{
				$datosEnviar["query"]["bool"]["must"][0]["terms"]["StatusLicencia"] =array($datos["StatusLicencia"]);
			}
			//$i++;	
		}
		else	
		{
			if(isset($datos["StatusLicenciaNot"]) && is_array($datos["StatusLicenciaNot"]))
			{
				$datosEnviar["query"]["bool"]["must_not"][0]["terms"]["StatusLicencia"] =$datos["StatusLicenciaNot"];
				//$i++;	
			}
		}
		//print_r($datosEnviar);
		/*elseif(isset($datos['ClaveEscuela']) && $datos['ClaveEscuela']!="")
		{
			$datosEnviar["query"]["bool"]["filter"][$i]["term"]["Establecimientos.ClaveEscuela"] = $datos['ClaveEscuela'];
			$i++;	
		}*/
		
		$f = 0;	

		$dataEnvio = json_encode($datosEnviar,JSON_UNESCAPED_SLASHES);

		$header = array("Content-Type: application/json");

		$fields_string = "";
		$this->oCurl->setFunction(get_class($this)."-".__FUNCTION__);
		//$this->oCurl->setDebug(true);
		$this->oCurl->setHeader($header);
		$this->oCurl->setUrl($urlBase);
		$this->oCurl->setHttpBuildPost(false);
		if(!$this->oCurl->sendPost($dataEnvio,$resultJson))
		{	
			$this->setError("Error","Error, ocurrio un error al buscar los datos de las licencias");
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



	public function BuscarLicenciasProcesadas($datos,&$resultJson)
	{
		$i=0;
		$f=0;
		$urlBase = ELASTICSERVER."/".INDICE.INDICELICENCIAS."/".TYPE."/_search";
		$datosEnviar = array();
		
		$SortField = "FechaModificacion";
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
		
		$cuits=array();
		if(isset($datos["cuits"]) && is_array($datos["cuits"]) && count($datos["cuits"])>0)
		{
			$cuits=$datos["cuits"];
		}
		
		if (isset($cuits) && is_array($cuits) && count($cuits)>0)
		{
			$datosEnviar["query"]["bool"]["filter"][$i]["terms"]["Cuil"] =array_values($cuits);
			$i++;	
		}
		
		if(isset($datos['AgenteCuil']) && $datos['AgenteCuil']!="" && in_array($datos['AgenteCuil'],$cuits))
		{
			$datosEnviar["query"]["bool"]["filter"][$i]["term"]["Cuil"] = $datos['AgenteCuil'];
			$i++;	
		}
		
		if(isset($datos['Tipo']) && $datos['Tipo']!="")
		{
			$datosEnviar["query"]["bool"]["filter"][$i]["term"]["Tipo"] = $datos['Tipo'];
			$i++;	
		}
		
		if(isset($datos['IdEstadoProceso']) && $datos['IdEstadoProceso']!="")
		{
			$datosEnviar["query"]["bool"]["filter"][$i]["terms"]["IdEstadoProceso"] = $datos['IdEstadoProceso'];
			$i++;	
		}
		
		
		
		
		if(isset($datos['Encuadre']) && $datos['Encuadre']!="")
		{
			$datosEnviar["query"]["bool"]["should"][0]["term"]["Doc"] = $datos['Encuadre'];
			$datosEnviar["query"]["bool"]["should"][1]["term"]["Aux"] = $datos['Encuadre'];
		}


		// OPCION 
		// Trae la misma cantidad de registros
		/*$datosEnviar["query"]["bool"]["filter"][$i]["bool"]["must_not"][$f]["range"][$Campo]['lt'] =json_encode("now/d");*/

		$Campo="Actualizado";
		if(isset($datos['Actualizado']) && $datos['Actualizado']!="")
		{
			$datosEnviar["query"]["bool"]["filter"][$i]["bool"]["must_not"][$f]["range"][$Campo]['gte'] = date("Ymd");
			$datosEnviar["query"]["bool"]["filter"][$i]["bool"]["must_not"][$f]["range"][$Campo]['lte'] = date("Ymd");
			$datosEnviar["query"]["bool"]["filter"][$i]["bool"]["must_not"][$f]["range"][$Campo]['format'] = "yyyyMMdd";
			$i++;
		}

		
		
		$Campo="FechaInicio";
		
		if(isset($datos['FechaDesde']) && $datos['FechaDesde']!="")
		{
			$datosEnviar["query"]["bool"]["filter"][$i]["range"][$Campo]['gte']=FuncionesPHPLocal::ConvertirFecha($datos['FechaDesde'],"dd/mm/aaaa","aaaa-mm-dd")." 00:00:00";
			if (isset($datos['FechaHasta']) && $datos['FechaHasta']!="")
			{
				$FechaHastaTimeStamp=FuncionesPHPLocal::ConvertirFecha($datos['FechaHasta'],"dd/mm/aaaa","aaaa-mm-dd")." 23:59:59";
				$datosEnviar["query"]["bool"]["filter"][$i]["range"][$Campo]['lt'] =$FechaHastaTimeStamp;
			}
			else	
				$datosEnviar["query"]["bool"]["filter"][$i]["range"][$Campo]['lte'] = date("Y-m-d H:i:s");
			$datosEnviar["query"]["bool"]["filter"][$i]["range"][$Campo]['boost'] = "2.0";
			$i++;
		}

		$Campo="FechaFin";
		if(isset($datos['FechaDesde']) && $datos['FechaDesde']!="")
		{
			$datosEnviar["query"]["bool"]["filter"][$i]["range"][$Campo]['gte']=FuncionesPHPLocal::ConvertirFecha($datos['FechaDesde'],"dd/mm/aaaa","aaaa-mm-dd")." 00:00:00";
			if (isset($datos['FechaHasta']) && $datos['FechaHasta']!="")
			{
				$FechaHastaTimeStamp=FuncionesPHPLocal::ConvertirFecha($datos['FechaHasta'],"dd/mm/aaaa","aaaa-mm-dd")." 23:59:59";
				$datosEnviar["query"]["bool"]["filter"][$i]["range"][$Campo]['lt'] =$FechaHastaTimeStamp;
			}
			else	
				$datosEnviar["query"]["bool"]["filter"][$i]["range"][$Campo]['lte'] = date("Y-m-d H:i:s");
			$datosEnviar["query"]["bool"]["filter"][$i]["range"][$Campo]['boost'] = "2.0";
			$i++;
		}
		
		if(isset($datos["TaskIds"]) && is_array($datos["TaskIds"]) && count($datos["TaskIds"])>0)
		{
			$datosEnviar["query"]["bool"]["filter"][$i]["terms"]["TaskId"] =$datos["TaskIds"];
			$i++;	
		}
		
		if(isset($datos["StatusLicencia"]) && $datos["StatusLicencia"]!="")
		{
			if ($datos["StatusLicencia"]=="X")
			{
				$datos["StatusLicencia"]=array("XAPPROVED", "XJUNTA", "XVISITACMZ","XAUSENTE","XREVISION");
				$datosEnviar["query"]["bool"]["must"][0]["terms"]["StatusLicencia"] =$datos["StatusLicencia"];
			}
			else
			{
				$datosEnviar["query"]["bool"]["must"][0]["terms"]["StatusLicencia"] =array($datos["StatusLicencia"]);
			}
			//$i++;	
		}
		else	
		{
			if(isset($datos["StatusLicenciaNot"]) && is_array($datos["StatusLicenciaNot"]))
			{
				$datosEnviar["query"]["bool"]["must_not"][0]["terms"]["StatusLicencia"] =$datos["StatusLicenciaNot"];
				//$i++;	
			}
		}


	/*	if(isset($datos['ClaveEscuela']) && $datos['ClaveEscuela']!="")
		{
			$datosEnviar["query"]["bool"]["filter"][$i]["term"]["Establecimientos.ClaveEscuela"] = $datos['ClaveEscuela'];
			$i++;	
		}
		*/
		//print_r($datosEnviar);
		/*elseif(isset($datos['ClaveEscuela']) && $datos['ClaveEscuela']!="")
		{
			$datosEnviar["query"]["bool"]["filter"][$i]["term"]["Establecimientos.ClaveEscuela"] = $datos['ClaveEscuela'];
			$i++;	
		}*/
		
		$f = 0;	

		$dataEnvio = json_encode($datosEnviar, JSON_UNESCAPED_SLASHES);
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
			$this->setError("Error","Error, ocurrio un error al buscar los datos de las licencias");
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
	
	public function BuscarLicenciasTodas($datos,&$resultJson)
	{
		$i=0;
		$urlBase = ELASTICSERVER."/".INDICE.INDICELICENCIAS."/".TYPE."/_search";
		$datosEnviar = array();
		
		$SortField = "FechaModificacion";
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
		
		
		if(isset($datos['AgenteCuil']) && $datos['AgenteCuil']!="")
		{
			$datosEnviar["query"]["bool"]["filter"][$i]["term"]["Cuil"] = $datos['AgenteCuil'];
			$i++;
		}
        if(isset($datos['TaskId']) && $datos['TaskId']!="")
        {
            $datosEnviar["query"]["bool"]["filter"][$i]["term"]["TaskId"] = $datos['TaskId'];
            $i++;
        }

		if(isset($datos['ClaveEscuela']) && $datos['ClaveEscuela']!="")
		{
			$datosEnviar["query"]["bool"]["filter"][$i]["term"]["Establecimientos.ClaveEscuela"] = $datos['ClaveEscuela'];
			$i++;	
		}
		
		if(isset($datos['Encuadre']) && $datos['Encuadre']!="" )
		{
			$datosEnviar["query"]["bool"]["should"][0]["term"]["Doc"] = $datos['Encuadre'];
			$datosEnviar["query"]["bool"]["should"][1]["term"]["Aux"] = $datos['Encuadre'];
		}
		
		
		$Campo="FechaInicio";
		if(isset($datos['FechaDesde']) && $datos['FechaDesde']!="")
		{
			$datosEnviar["query"]["bool"]["filter"][$i]["range"][$Campo]['gte']=FuncionesPHPLocal::ConvertirFecha($datos['FechaDesde'],"dd/mm/aaaa","aaaa-mm-dd")." 00:00:00";
			if (isset($datos['FechaHasta']) && $datos['FechaHasta']!="")
			{
				$FechaHastaTimeStamp=FuncionesPHPLocal::ConvertirFecha($datos['FechaHasta'],"dd/mm/aaaa","aaaa-mm-dd")." 23:59:59";
				$datosEnviar["query"]["bool"]["filter"][$i]["range"][$Campo]['lt'] =$FechaHastaTimeStamp;
			}
			else	
				$datosEnviar["query"]["bool"]["filter"][$i]["range"][$Campo]['lte'] = date("Y-m-d H:i:s");
			$datosEnviar["query"]["bool"]["filter"][$i]["range"][$Campo]['boost'] = "2.0";
			$i++;
		}

		$Campo="FechaFin";
		if(isset($datos['FechaDesde']) && $datos['FechaDesde']!="")
		{
			$datosEnviar["query"]["bool"]["filter"][$i]["range"][$Campo]['gte']=FuncionesPHPLocal::ConvertirFecha($datos['FechaDesde'],"dd/mm/aaaa","aaaa-mm-dd")." 00:00:00";
			if (isset($datos['FechaHasta']) && $datos['FechaHasta']!="")
			{
				$FechaHastaTimeStamp=FuncionesPHPLocal::ConvertirFecha($datos['FechaHasta'],"dd/mm/aaaa","aaaa-mm-dd")." 23:59:59";
				$datosEnviar["query"]["bool"]["filter"][$i]["range"][$Campo]['lt'] =$FechaHastaTimeStamp;
			}
			else	
				$datosEnviar["query"]["bool"]["filter"][$i]["range"][$Campo]['lte'] = date("Y-m-d H:i:s");
			$datosEnviar["query"]["bool"]["filter"][$i]["range"][$Campo]['boost'] = "2.0";
			$i++;
		}
		/*
		if(isset($datos["StatusLicenciaNot"]) && is_array($datos["StatusLicenciaNot"]))
		{
			$datosEnviar["query"]["bool"]["must_not"][0]["terms"]["StatusLicencia"] =$datos["StatusLicenciaNot"];
			//$i++;	
		}*/
		if(isset($datos["StatusLicencia"]) && $datos["StatusLicencia"]!="")
		{
			if ($datos["StatusLicencia"]=="X")
			{
				$datos["StatusLicencia"]=array("XAPPROVED", "XJUNTA", "XVISITACMZ","XAUSENTE","XREVISION");
				$datosEnviar["query"]["bool"]["must"][0]["terms"]["StatusLicencia"] =$datos["StatusLicencia"];
			}
			else
			{
				$datosEnviar["query"]["bool"]["must"][0]["terms"]["StatusLicencia"] =array($datos["StatusLicencia"]);
			}
			//$i++;	
		}
		else	
		{
			if(isset($datos["StatusLicenciaNot"]) && is_array($datos["StatusLicenciaNot"]))
			{
				$datosEnviar["query"]["bool"]["must_not"][0]["terms"]["StatusLicencia"] =$datos["StatusLicenciaNot"];
				//$i++;	
			}
		}
		
		$f = 0;	
		//print_r($datosEnviar);
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
			$this->setError("Error","Error, ocurrio un error al buscar los datos de las licencias");
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