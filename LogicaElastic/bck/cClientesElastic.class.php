<?php
//-----------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------
// Clase con la lógica para las busquedas relacionadas a documentos de clientes
class cClientesElastic
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




	public function ListadoErroresHost(&$resultJson)
	{
		$datosEnviar = array();

		$urlBase = ELASTICSERVER."/".INDICE.INDICESUNA;
		if(INCLUDETYPE)
			$urlBase .= '/'.TYPE;
		$urlBase .= "/_search";
		
		if (RESTTOTALHITS==true)
			$urlBase .= "?rest_total_hits_as_int=true";
		if (TOTALHISTTRACK==true)
			$urlBase .= "?track_total_hits=true";


		$datosEnviar['from'] = 0;

		$i = 0;


		$datosEnviar["query"]["bool"]["filter"][$i]["bool"]["must_not"]["match"]["ObservacionesHOST"] ="BOT";
		$i++;
		$datosEnviar["query"]["bool"]["filter"][$i]["term"]["TipoDocumento.Categoria.Id"] = 6;
		$datosEnviar["aggs"]["ErroresHOST"]["terms"]["order"]['_key'] = "asc";
		$datosEnviar["aggs"]["ErroresHOST"]["terms"]["field"] = "ObservacionesHOST.raw";
		$datosEnviar["aggs"]["ErroresHOST"]["terms"]["size"] = 1000;
		$i++;


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

	public function BuscarCantidadDocumentos($datos,&$resultJson)
	{
		if(empty($datos['Tipo']) && isset($this->TipoDatos) && $this->TipoDatos!="")
			$datos['Tipo'] = $this->TipoDatos;
		$i=0;
		$urlBase = ELASTICSERVER."/".INDICECLIENTE;
		if(INCLUDETYPE)
			$urlBase .= '/'.TYPE;
		$urlBase .= "/_count";

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



	public function BuscarCantidadAdjuntos($datos,&$resultJson)
	{
		if(empty($datos['Tipo']) && isset($this->TipoDatos) && $this->TipoDatos!="")
			$datos['Tipo'] = $this->TipoDatos;
		$i=0;
		$urlBase = ELASTICSERVER."/".INDICECLIENTE;
		if(INCLUDETYPE)
			$urlBase .= '/'.TYPE;
		$urlBase .= "/_count";
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


	public function BuscarDescripcionDocumentoPorHijo($datos,&$resultJson)
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
		
		if (!isset($datos['from']))
			$datosEnviar['from'] = 0;
		else
			$datosEnviar['from'] = $datos['from'];
		if (!isset($datos['size']))
			$datosEnviar['size'] = PAGINAR;
		else
			$datosEnviar['size'] = $datos['size'];
			
		
		$datosEnviar['_source'] = ['IdDocumento','TipoDocumento.*'];


		$i = 0;
		$datosEnviar["query"] = array();
		$datosEnviar["query"]["bool"] = array();
		$datosEnviar["query"]["bool"]["filter"] = array();
		
		if(isset($datos['IdDocumento']) && $datos['IdDocumento']!="")
		{
			$datosEnviar["query"]["bool"]["filter"][$i]["term"]["IdDocumentoPadre"] = $datos['IdDocumento'];
			$i++;
		}
		
		$datosEnviar["query"]["bool"]["filter"][$i]["term"]["Tipo"] = TIPODOC;
		if(isset($datos['Tipo']) && $datos['Tipo']!="")
			$datosEnviar["query"]["bool"]["filter"][$i]["term"]["Tipo"] = $datos['Tipo'];
		$i++;
		

		$dataEnvio = json_encode($datosEnviar);
	/*	echo $dataEnvio;*/
		/*die;*/
	

		$header = array("Content-Type: application/json");

		$fields_string = "";
		$this->oCurl->setFunction(get_class($this)."-".__FUNCTION__);
		/*$this->oCurl->setDebug(true);*/
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


	public function BuscarDescripcionDocumentoPadres($datos,&$resultJson)
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
		
		if (!isset($datos['from']))
			$datosEnviar['from'] = 0;
		else
			$datosEnviar['from'] = $datos['from'];
		if (!isset($datos['size']))
			$datosEnviar['size'] = PAGINAR;
		else
			$datosEnviar['size'] = $datos['size'];
			
		
		$datosEnviar['_source'] = ['IdDocumento','TipoDocumento.*'];


		$i = 0;
		$datosEnviar["query"] = array();
		$datosEnviar["query"]["bool"] = array();
		$datosEnviar["query"]["bool"]["filter"] = array();
		
		if(isset($datos['IdDocumentoPadre']) && $datos['IdDocumentoPadre']!="")
		{
			$datosEnviar["query"]["bool"]["filter"][$i]["term"]["IdDocumento"] = $datos['IdDocumentoPadre'];
			$i++;
		}
		
		$datosEnviar["query"]["bool"]["filter"][$i]["term"]["Tipo"] = TIPODOC;
		if(isset($datos['Tipo']) && $datos['Tipo']!="")
			$datosEnviar["query"]["bool"]["filter"][$i]["term"]["Tipo"] = $datos['Tipo'];
		$i++;
		

		$dataEnvio = json_encode($datosEnviar);
		/*echo $dataEnvio;*/
		/*die;*/
	

		$header = array("Content-Type: application/json");

		$fields_string = "";
		$this->oCurl->setFunction(get_class($this)."-".__FUNCTION__);
		/*$this->oCurl->setDebug(true);*/
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
		$f=0;
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
		
				
		/* TODOS LOS ESTADOS MENOS: ELIMINADO y DESESTIMADO */
		$datosEnviar["query"]["bool"]["filter"][$i]["bool"]["must_not"]["terms"]["Estado.Id"] = [99, 115, 116, 117, 120];
		$i++;
		
		if(isset($datos['ClaveEscuela']) && $datos['ClaveEscuela']!="")
		{
			$datosEnviar["query"]["bool"]["filter"][$i]["term"]["ClaveEscuela"] = $datos['ClaveEscuela'];
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

		if(isset($datos['secuencia']) && $datos['secuencia']!="")
		{
			$datosEnviar["query"]["bool"]["filter"][$i]["nested"]["path"] = "Cargos";
			$datosEnviar["query"]["bool"]["filter"][$i]["nested"]["query"]["bool"]["filter"][$f]["term"]["Cargos.Secuencia"] = $datos['secuencia'];
			$i++;
		}

/*		if(isset($datos['subsecuencia']) && $datos['subsecuencia']!="")
		{
			$datosEnviar["query"]["bool"]["filter"][$i]["nested"]["path"] = "Cargos";
			$datosEnviar["query"]["bool"]["filter"][$i]["nested"]["query"]["bool"]["filter"][$f]["term"]["Cargos.SubSecuencia"] = $datos['subsecuencia'];
			$i++;
		}*/

		
		$iMust = 0;
		if(isset($datos['AgenteCuil']) && $datos['AgenteCuil']!="")
		{
			$datosEnviar["query"]["bool"]["must"][$iMust]["term"]["Agente.Cuil"] = $datos['AgenteCuil'];
			$iMust++;
		}

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



	public function BuscarDocumentos($datos,&$resultJson)
	{
		if(empty($datos['Tipo']) && isset($this->TipoDatos) && $this->TipoDatos!="")
			$datos['Tipo'] = $this->TipoDatos;
		$i=0;
		$f=0;
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

		if(isset($datos['IdEstado']) && $datos['IdEstado']!="" && !in_array('-1',$datos['IdEstado']))
		{
			$datosEnviar["query"]["bool"]["filter"][$i]["terms"]["Estado.Id"] = $datos['IdEstado'];
			$i++;
		}
		else{
			$datosEnviar["query"]["bool"]["filter"][$i]["bool"]["must_not"]["terms"]["Estado.Id"] =[99,109,115,116,117,120];
			$i++;
		}
		if(isset($datos['IdDesestimados']) && $datos['IdDesestimados']!="" && !in_array('-1',$datos['IdDesestimados']))
		{
			$datosEnviar["query"]["bool"]["filter"][$i]["bool"]["must_not"]["terms"]["Estado.Id"] = $datos['IdDesestimados'];
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

        if(isset($datos["TaskIds"]) && is_array($datos["TaskIds"]) && count($datos["TaskIds"])>0)
		{
			$datosEnviar["query"]["bool"]["filter"][$i]["terms"]["TaskId"] =$datos["TaskIds"];
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



		if(isset($datos['secuencia']) && $datos['secuencia']!="")
		{
			$datosEnviar["query"]["bool"]["filter"][$i]["nested"]["path"] = "Cargos";
			$datosEnviar["query"]["bool"]["filter"][$i]["nested"]["query"]["bool"]["filter"][$f]["term"]["Cargos.Secuencia"] = $datos['secuencia'];
			$i++;
		}

		if(isset($datos['subsecuencia']) && $datos['subsecuencia']!="")
		{
			$datosEnviar["query"]["bool"]["filter"][$i]["nested"]["path"] = "Cargos";
			$datosEnviar["query"]["bool"]["filter"][$i]["nested"]["query"]["bool"]["filter"][$f]["term"]["Cargos.SubSecuencia"] = $datos['subsecuencia'];
			$i++;
		}



        if(isset($datos['IdDocumentoPadre']) && is_array($datos['IdDocumentoPadre']) && count($datos['IdDocumentoPadre'])>0)
        {
            $datosEnviar["query"]["bool"]["filter"][$i]["terms"]["IdDocumentoPadre"] = $datos['IdDocumentoPadre'];
            $i++;
        }elseif(isset($datos['IdDocumentoPadre']) && is_numeric($datos['IdDocumentoPadre']))
        {
            $datosEnviar["query"]["bool"]["filter"][$i]["term"]["IdDocumentoPadre"] = $datos['IdDocumentoPadre'];
            $i++;
        }

        if(isset($datos['Adecuacion']) && $datos['Adecuacion']!="")
        {
            $valor = false;
            if($datos['Adecuacion']==1)
            {
                $valor = true;
                $datosEnviar["query"]["bool"]["filter"][$i]["term"]["Adecuacion"] = $valor;
                $i++;
            }
            else
            {
                $datosEnviar["query"]["bool"]["should"][0]["bool"]["must"][0]["term"]["Adecuacion"] = $valor;
                $datosEnviar["query"]["bool"]["should"][1]["bool"]["must_not"][0]["exists"]["field"] = "Adecuacion";
            }

        }

        if(isset($datos['ClaveEscuelaDestino']) && $datos['ClaveEscuelaDestino']!="")
        {
            $datosEnviar["query"]["bool"]["filter"][$i]["term"]["ClaveEscuelaDestino"] = $datos['ClaveEscuelaDestino'];
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

		if(isset($datos['AgenteCuils']) && $datos['AgenteCuils']!="")
		{
			$datosEnviar["query"]["bool"]["must"][$iMust]["terms"]["Agente.Cuil"] = $datos['AgenteCuils'];
			$iMust++;
		}

		if(isset($datos['filtroPdf']) && $datos['filtroPdf'] == true)
		{
			$datosEnviar["query"]["bool"]["must_not"][$iMust]["terms"]["TipoDocumento.Id"] = array("170","145","171");
			$iMust++;
		}
		
		/*
		$datosEnviar['aggs']['TiposDocumentos'] = array();
		$datosEnviar['aggs']['TiposDocumentos']['terms'] = array();
		$datosEnviar['aggs']['TiposDocumentos']['terms']['script']['source'] = "doc['TipoDocumento.Id'].value +'|'+doc['TipoDocumento.Nombre.raw'].value";
		$datosEnviar['aggs']['TiposDocumentos']['terms']['size'] = 150;
		*/
		
		$dataEnvio = json_encode($datosEnviar);
		//echo $dataEnvio;
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

	public function BuscarSolicitudesCoberturas($datos,&$resultJson)
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
		
		
		if(!is_array($_SESSION['IdArea']))
			$IdArea = explode(",",$_SESSION['IdArea']);
		else
			$IdArea = $_SESSION['IdArea'];
			
		$f = 0;

		if(isset($datos['ClaveEscuela']) && $datos['ClaveEscuela']!="")
		{
			$datosEnviar["query"]["bool"]["filter"][$i]["term"]["ClaveEscuela"] = $datos['ClaveEscuela'];
			$i++;
		}else
		{
		
			if(isset($datos['Distrito']) && $datos['Distrito']!="")
			{
				$datosEnviar["query"]["bool"]["filter"][$i]["term"]["Distrito.Id"] = $datos['Distrito'];
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

		if(isset($datos['IdRevista']) && $datos['IdRevista']!="")
		{
			$datosEnviar["query"]["bool"]["filter"][$i]["nested"]["path"] = "Cargos";
			$datosEnviar["query"]["bool"]["filter"][$i]["nested"]["query"]["bool"]["filter"][$f]["terms"]["Cargos.Revista"] = $datos['IdRevista'];
			$i++;
		}

		if(isset($datos['PID']) && $datos['PID']!="")
		{
			$datosEnviar["query"]["bool"]["filter"][$i]["match"]["PID"] = $datos['PID'];
			$i++;
		}


		if(isset($datos['Turno']) && $datos['Turno']!="")
		{
			$datosEnviar["query"]["bool"]["filter"][$i]["nested"]["path"] = "Cargos";
			$datosEnviar["query"]["bool"]["filter"][$i]["nested"]["query"]["bool"]["filter"][$f]["terms"]["Cargos.IdTurno"] = $datos['Turno'];
			$i++;
		}

		if(isset($datos['Asignatura']) && $datos['Asignatura']!="")
		{
			$datosEnviar["query"]["bool"]["filter"][$i]["nested"]["path"] = "Cargos";
			$datosEnviar["query"]["bool"]["filter"][$i]["nested"]["query"]["bool"]["filter"][$f]["terms"]["Cargos.Asignatura"] = $datos['Asignatura'];
			$i++;
		}

		if(isset($datos['Area']) && $datos['Area']!="")
		{
			$datosEnviar["query"]["bool"]["filter"][$i]["nested"]["path"] = "Cargos";
			$datosEnviar["query"]["bool"]["filter"][$i]["nested"]["query"]["bool"]["filter"][$f]["terms"]["Cargos.Area"] = $datos['Area'];
			$i++;
		}

		if(isset($datos['IdCargo']) && $datos['IdCargo']!="")
		{
			$datosEnviar["query"]["bool"]["filter"][$i]["nested"]["path"] = "Cargos";
			$datosEnviar["query"]["bool"]["filter"][$i]["nested"]["query"]["bool"]["filter"][$f]["terms"]["Cargos.CargoCodigo"] = $datos['IdCargo'];
			$i++;
		}
		


		if(isset($datos['IdClasificacion']) && $datos['IdClasificacion']!="")
		{
			$datosEnviar["query"]["bool"]["filter"][$i]["term"]["TipoDocumento.Clasificacion.Id"] = $datos['IdClasificacion'];
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

        if(isset($datos['IdEstado']) && is_array($datos['IdEstado']) && count($datos['IdEstado'])>0)
        {
            $datosEnviar["query"]["bool"]["filter"][$i]["terms"]["Estado.Id"] = $datos['IdEstado'];
            $i++;
        }
        else{
            $datosEnviar["query"]["bool"]["filter"][$i]["term"]["Estado.Id"] = $datos['IdEstado'];
            $i++;
        }
		

		if(isset($datos['IdDocumentoPadre']) && $datos['IdDocumentoPadre']!="")
		{
			$datosEnviar["query"]["bool"]["filter"][$i]["term"]["IdDocumento"] = $datos['IdDocumentoPadre'];
			$i++;
		}
		
		$datosEnviar["query"]["bool"]["filter"][$i]["term"]["Tipo"] = TIPODOC;
		if(isset($datos['Tipo']) && $datos['Tipo']!="")
			$datosEnviar["query"]["bool"]["filter"][$i]["term"]["Tipo"] = $datos['Tipo'];
		$i++;

		$Campo = "Alta.Fecha"; //$Campo = "FechaEnvio";//"FechaEnvio"
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

		/*$datosEnviar['aggs']['Cargos'] = array();
		$datosEnviar['aggs']['Cargos']['terms'] = array();
		$datosEnviar['aggs']['Cargos']['terms'] = $datos['Area'];*/
		
		
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



	public function BuscarDocumentosContralor($datos,&$resultJson)
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
		
		
		if(!is_array($_SESSION['IdArea']))
			$IdArea = explode(",",$_SESSION['IdArea']);
		else
			$IdArea = $_SESSION['IdArea'];
			
		$f = 0;
		if(isset($datos['ClaveEscuela']) && $datos['ClaveEscuela']!="" && $datos['IdEstado']!= -1)
		{

			$datosEnviar["query"]["bool"]["filter"][$i]["term"]["ClaveEscuela"] = $datos['ClaveEscuela'];
			$i++;
		}else
		{
		
			if(isset($datos['Distrito']) && $datos['Distrito']!="" && $datos['Distrito']!="000")
			{
				$datosEnviar["query"]["bool"]["filter"][$i]["term"]["Distrito.Id"] = $datos['Distrito'];
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

		if(isset($datos['IdDocumentoPadre']) && $datos['IdDocumentoPadre']!="")
		{
			$datosEnviar["query"]["bool"]["filter"][$i]["term"]["IdDocumentoPadre"] = $datos['IdDocumentoPadre'];
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
		
		if(isset($datos['AgenteDNI']) && $datos['AgenteDNI']!="")
		{
			$datosEnviar["query"]["bool"]["must"][$iMust]["term"]["Agente.Dni"] = $datos['AgenteDNI'];
			$iMust++;
		}
		
		/*
		$datosEnviar['aggs']['TiposDocumentos'] = array();
		$datosEnviar['aggs']['TiposDocumentos']['terms'] = array();
		$datosEnviar['aggs']['TiposDocumentos']['terms']['script']['source'] = "doc['TipoDocumento.Id'].value +'|'+doc['TipoDocumento.Nombre.raw'].value";
		$datosEnviar['aggs']['TiposDocumentos']['terms']['size'] = 150;
		*/
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


	public function BuscarDocumentosSAD($datos,&$resultJson)
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
		
		
		if(!is_array($_SESSION['IdArea']))
			$IdArea = explode(",",$_SESSION['IdArea']);
		else
			$IdArea = $_SESSION['IdArea'];
			
		$f = 0;

		if(isset($datos['ClaveEscuela']) && $datos['ClaveEscuela']!="")
		{
			$datosEnviar["query"]["bool"]["filter"][$i]["term"]["ClaveEscuela"] = $datos['ClaveEscuela'];
			$i++;
		}else
		{
		
			if(isset($datos['Distrito']) && $datos['Distrito']!="")
			{
				$datosEnviar["query"]["bool"]["filter"][$i]["term"]["Distrito.Id"] = $datos['Distrito'];
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
		
		if(isset($datos['TipoDocumento']) && is_array($datos['TipoDocumento']) && count($datos['TipoDocumento'])>0)
		{
			$datosEnviar["query"]["bool"]["filter"][$i]["terms"]["TipoDocumento.Id"] = $datos['TipoDocumento'];
			$i++;
		}elseif(isset($datos['TipoDocumento']) && is_numeric($datos['TipoDocumento']))
		{
			$datosEnviar["query"]["bool"]["filter"][$i]["term"]["TipoDocumento.Id"] = $datos['TipoDocumento'];
			$i++;
		}
		
	/*	if(isset($datos['IdEstado']) && $datos['IdEstado']!="")
		{
			$datosEnviar["query"]["bool"]["filter"][$i]["terms"]["Estado.Id"] = $datos['IdEstado'];
			$i++;
		}
		*/

		if(isset($datos['IdDocumentoPadre']) && $datos['IdDocumentoPadre']!="")
		{
			$datosEnviar["query"]["bool"]["filter"][$i]["term"]["IdDocumentoPadre"] = $datos['IdDocumentoPadre'];
			$i++;
		}

        if(isset($datos['Adecuacion']) && $datos['Adecuacion']!="")
        {
            $valor = false;
            if($datos['Adecuacion']==1)
            {
                $valor = true;
                $datosEnviar["query"]["bool"]["filter"][$i]["term"]["Adecuacion"] = $valor;
                $i++;
            }
            else
            {
                $datosEnviar["query"]["bool"]["should"][0]["bool"]["must"][0]["term"]["Adecuacion"] = $valor;
                $datosEnviar["query"]["bool"]["should"][1]["bool"]["must_not"][0]["exists"]["field"] = "Adecuacion";
            }

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
		
		
		if(isset($datos['IdEstado']) && $datos['IdEstado']!="" && !in_array('-1',$datos['IdEstado']))
		{
			$datosEnviar["query"]["bool"]["filter"][$i]["terms"]["Estado.Id"] = $datos['IdEstado'];
			$i++;
		}
		
		/*
		$datosEnviar['aggs']['TiposDocumentos'] = array();
		$datosEnviar['aggs']['TiposDocumentos']['terms'] = array();
		$datosEnviar['aggs']['TiposDocumentos']['terms']['script']['source'] = "doc['TipoDocumento.Id'].value +'|'+doc['TipoDocumento.Nombre.raw'].value";
		$datosEnviar['aggs']['TiposDocumentos']['terms']['size'] = 150;
		*/
		
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

	public function BuscarCantidadesDistritosPorEstados($datos,&$resultJson)
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
		/*if (isset($datos['SortField']) && is_array($datos['SortField']) && count($datos['SortField'])>0)
		{
			foreach($datos['SortField'] as $Order)
				$datosEnviar['sort'][][$Order['Field']] = array('order'=>$Order['Sort']);
		}else{
			if (isset($datos['SortField']) && $datos['SortField']!="")
				$SortField = $datos['SortField'];
			if (isset($datos['Sort']) && $datos['Sort']!="")
				$SortOrder = $datos['Sort'];
			
			$datosEnviar['sort'][][$SortField] = array('order'=>$SortOrder);
		}*/
		
		$datosEnviar['from'] = 0;
		$datosEnviar['size'] = 0;/*$datos['size']*/;
		
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
		
		
		if(!is_array($_SESSION['IdArea']))
			$IdArea = explode(",",$_SESSION['IdArea']);
		else
			$IdArea = $_SESSION['IdArea'];
			
		$f = 0;

		if(isset($datos['TipoOrganizacion']) && $datos['TipoOrganizacion']!="")
		{
			$datosEnviar["query"]["bool"]["filter"][$i]["term"]["TipoOrg.Id"] = strtoupper($datos['TipoOrganizacion']);
			$i++;
		}
		if(isset($datos['Distrito']) && $datos['Distrito']!="")
		{
			$datosEnviar["query"]["bool"]["filter"][$i]["term"]["Distrito.Id"] = $datos['Distrito'];
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
		
		if(isset($datos['TipoDocumento']) && is_array($datos['TipoDocumento']) && count($datos['TipoDocumento'])>0)
		{
			$datosEnviar["query"]["bool"]["filter"][$i]["terms"]["TipoDocumento.Id"] = $datos['TipoDocumento'];
			$i++;
		}elseif(isset($datos['TipoDocumento']) && is_numeric($datos['TipoDocumento']))
		{
			$datosEnviar["query"]["bool"]["filter"][$i]["term"]["TipoDocumento.Id"] = $datos['TipoDocumento'];
			$i++;
		}

		if(isset($datos['Estados']) && $datos['Estados']!="")
		{
			$arrayEstados=array();
			$datosEnviar["query"]["bool"]["filter"][$i]["terms"]["Estado.Id"] = array($datos['IdEstado']);
			$i++;
		}
		else
		{
			$datosEnviar["query"]["bool"]["filter"][$i]["terms"]["Estado.Id"] = array("30","39","43");
			$i++;
		}
		
		$datosEnviar["query"]["bool"]["filter"][$i]["term"]["Tipo"] = TIPODOC;
		if(isset($datos['Tipo']) && $datos['Tipo']!="")
			$datosEnviar["query"]["bool"]["filter"][$i]["term"]["Tipo"] = $datos['Tipo'];
		$i++;

		$Campo = "UltimaModificacion.Fecha"; //$Campo = "FechaEnvio";//"FechaEnvio"
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

		$datosEnviar['aggs']['Distritos']['terms']['field']= "Distrito.Id";
		$datosEnviar['aggs']['Distritos']['terms']['size'] = 100000;
		$datosEnviar['aggs']['Distritos']['aggs']['Distritos_sort']['bucket_sort']['size'] = $datos['size'];
		$datosEnviar['aggs']['Distritos']['aggs']['Distritos_sort']['bucket_sort']['from'] = $datos['from'];
		$datosEnviar['aggs']['Distritos']['aggs']['Estados']['terms']['field']= "Estado.Id";
		$datosEnviar['aggs']['Distritos']['aggs']["Estados"]["aggs"]["UltimoFecha"]["min"]["field"]="UltimaModificacion.Fecha";
		$datosEnviar['aggs']['Distritos']['aggs']["Estados"]["aggs"]["UltimoFecha"]["min"]["format"]="yyyy-MM-dd  HH:mm:ss";
		$datosEnviar['aggs']['Distritos_count']['cardinality']['field'] = "Distrito.Id";
		
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


	public function BuscarDocumentosConsejo($datos,&$resultJson)
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
		
		
		if(!is_array($_SESSION['IdArea']))
			$IdArea = explode(",",$_SESSION['IdArea']);
		else
			$IdArea = $_SESSION['IdArea'];
			
		$f = 0;

		if(isset($datos['ClaveEscuela']) && $datos['ClaveEscuela']!="")
		{
			$datosEnviar["query"]["bool"]["filter"][$i]["term"]["ClaveEscuela"] = $datos['ClaveEscuela'];
			$i++;
		}else
		{
			
			if(isset($datos['Distrito']) && $datos['Distrito']!="")
			{
				$datosEnviar["query"]["bool"]["filter"][$i]["term"]["Distrito.Id"] = $datos['Distrito'];
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
		
		if(isset($datos['TipoDocumento']) && is_array($datos['TipoDocumento']) && count($datos['TipoDocumento'])>0)
		{
			$datosEnviar["query"]["bool"]["filter"][$i]["terms"]["TipoDocumento.Id"] = $datos['TipoDocumento'];
			$i++;
		}elseif(isset($datos['TipoDocumento']) && is_numeric($datos['TipoDocumento']))
		{
			$datosEnviar["query"]["bool"]["filter"][$i]["term"]["TipoDocumento.Id"] = $datos['TipoDocumento'];
			$i++;
		}

		if(isset($datos['IdEstado']) && is_array($datos['IdEstado']) && !in_array('-1',$datos['IdEstado']))
		{
			$datosEnviar["query"]["bool"]["filter"][$i]["terms"]["Estado.Id"] = $datos['IdEstado'];
			$i++;
		}
		else{
			$datosEnviar["query"]["bool"]["filter"][$i]["bool"]["must_not"]["term"]["Estado.Id"] =99;
			$i++;
		}
		
		

		if(isset($datos['IdDocumentoPadre']) && $datos['IdDocumentoPadre']!="")
		{
			$datosEnviar["query"]["bool"]["filter"][$i]["term"]["IdDocumentoPadre"] = $datos['IdDocumentoPadre'];
			$i++;
		}
		
		$datosEnviar["query"]["bool"]["filter"][$i]["term"]["Tipo"] = TIPODOC;
		if(isset($datos['Tipo']) && $datos['Tipo']!="")
			$datosEnviar["query"]["bool"]["filter"][$i]["term"]["Tipo"] = $datos['Tipo'];
		$i++;

		
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
	

	
	public function BuscarDocumentosDashboard($datos,&$resultJson)
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
		

		$datosEnviar['from'] = 0;
		$datosEnviar['size'] = 0;

		$i = 0;
		$datosEnviar["query"] = array();
		$datosEnviar["query"]["bool"] = array();
		$datosEnviar["query"]["bool"]["filter"] = array();
		
		if(isset($datos['ClaveEscuela']) && $datos['ClaveEscuela']!="")
		{
			$datosEnviar["query"]["bool"]["filter"][$i]["term"]["ClaveEscuela"] = $datos['ClaveEscuela'];
			$i++;
		}
		if(isset($datos['TipoDocumento']) && $datos['TipoDocumento']!="")
		{
			$datosEnviar["query"]["bool"]["filter"][$i]["terms"]["TipoDocumento.Id"] = $datos['TipoDocumento'];
			$i++;
		}
		if(isset($datos['Estado']) && $datos['Estado']!="")
		{
			$datosEnviar["query"]["bool"]["filter"][$i]["terms"]["Estado.Id"] = $datos['Estado'];
			$i++;
		}
		if(isset($datos['Area']) && $datos['Area']!="")
		{
			$datosEnviar["query"]["bool"]["filter"][$i]["terms"]["Area.Id"] = $datos['Area'];
			$i++;
		}
		
		if(isset($datos['Distrito']) && $datos['Distrito']!="")
		{
			$datosEnviar["query"]["bool"]["filter"][$i]["term"]["Distrito.Id"] = $datos['Distrito'];
			$i++;
		}
		
		
		$f = 0;
		
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


	
	
	public function BuscarLogDocumentos($datos,&$resultJson)
	{
		if(empty($datos['Tipo']) && isset($this->TipoDatos) && $this->TipoDatos!="")
			$datos['Tipo'] = $this->TipoDatos;
		$i=0;
		$urlBase = ELASTICSERVER."/".INDICE.INDICEAUDITORIA;
		if(INCLUDETYPE)
			$urlBase .= '/'.TYPE;
		$urlBase .= "/_search";
		if (RESTTOTALHITS==true)
			$urlBase .= "?rest_total_hits_as_int=true";
		if (TOTALHISTTRACK==true)
			$urlBase .= "?track_total_hits=true";

		$datosEnviar = array();
		
		$SortField = "UltimaModificacion.Fecha";
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
		
		
		$datosEnviar["query"]["bool"]["filter"][$i]["term"]["IdDocumento"] = $datos['IdDocumento'];
		$i++;

		$Campo="UltimaModificacion.Fecha";
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
		$i++;
		
		$dataEnvio = json_encode($datosEnviar);

		//print_r($dataEnvio);
		
		
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
		
		if (isset($resultJson['hits']['total']['value']) && is_numeric($resultJson['hits']['total']['value']))
			$resultJson['hits']['total']=$resultJson['hits']['total']['value'];
			
		if (isset($resultJson['acknowledged']) && $resultJson['acknowledged']===true)
			return true;

		return true;
	}
	
	
	
	public function BuscarParosxFecha($datos, &$datosDevueltos, &$total)
	{
		$datosDevueltos = [];
		$total = 0;
		
		$urlBase = ELASTICSERVER."/".INDICE.INDICESUNA;
		if(INCLUDETYPE)
			$urlBase .= '/'.TYPE;
		$urlBase .= "/_search";
		if (RESTTOTALHITS==true)
			$urlBase .= "?rest_total_hits_as_int=true";
		if (TOTALHISTTRACK==true)
			$urlBase .= "?track_total_hits=true";

		$ff = 0;
		$var = "TipoDocumento.Id";
		$datosEnviar = new stdClass();
		$datosEnviar->size = 10000;
		$datosEnviar->query = new stdClass();
		$datosEnviar->query->bool = new stdClass();
		$datosEnviar->query->bool->filter = array();
		$datosEnviar->query->bool->filter[$ff] = new stdClass();
		$datosEnviar->query->bool->filter[$ff]->terms = new stdClass();
		$datosEnviar->query->bool->filter[$ff]->terms->$var = [DOCPARODOCENTEDOC, DOCPARODOCENTEAUX];
		
		$ff++;
		$var = "ClaveEscuela";
		$datosEnviar->query->bool->filter[$ff] = new stdClass();
		$datosEnviar->query->bool->filter[$ff]->term = new stdClass();
		$datosEnviar->query->bool->filter[$ff]->term->$var = $datos['ClaveEscuela'];
		$ff++;
		if(!empty($datos['FechaDesde']))
		{
			$var = "Periodo.FechaDesde";
			$datosEnviar->query->bool->filter[$ff] = new stdClass();
			$datosEnviar->query->bool->filter[$ff]->term = new stdClass();
			$datosEnviar->query->bool->filter[$ff]->term->$var = FuncionesPHPLocal::ConvertirFecha($datos['FechaDesde'],"dd/mm/aaaa","aaaa-mm-dd");
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
		
		if (!empty($resultJson['hits']['hits']))
		{
			foreach ($resultJson['hits']['hits'] as $dato)
			{
				$source = $dato['_source'];
				if (isset($source['Cargos']) && count($source['Cargos'])>0)
				{
					foreach($source['Cargos'] as $cargo)
					{
						$datosDevueltos[$source['Agente']['Cuil']."_".$cargo["Secuencia"]] = $source;
					}
				}
			}
		}

		return true;
	}

	
	

	public function BuscarAdjuntos($datos,&$resultado)
	{
		if(empty($datos['Tipo']) && isset($this->TipoDatos) && $this->TipoDatos!="")
			$datos['Tipo'] = $this->TipoDatos;
		$resultado = array();
		$i=0;
		$urlBase = ELASTICSERVER."/".INDICECLIENTE;
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
	
	

	
	public function BuscarxCodigo($datos,&$datosRegistro,&$numfilas)
	{
		if(empty($datos['Tipo']) && isset($this->TipoDatos) && $this->TipoDatos!="")
			$datos['Tipo'] = $this->TipoDatos;

		$datosRegistro = array();
		$numfilas = 0;
		$Id = "/".self::_ObtenerId($datos);
		
		$urlBase = ELASTICSERVER."/".INDICE.INDICESUNA;
		$urlBase .= '/'.(INCLUDETYPE ? TYPE : '_doc');
		$urlBase .= $Id;
		//echo '<br/>'.$urlBase.'<br/>';
		$header = array("Content-Type: application/json");
		
		/*
		curl_setopt($this->ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($this->ch, CURLOPT_URL, $urlBase);
		curl_setopt($this->ch, CURLOPT_CUSTOMREQUEST, "GET");
		curl_setopt($this->ch, CURLOPT_POSTFIELDS,"");
		curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, 1);
		//execute post
		$result = curl_exec($this->ch);
		$data = json_decode($result,true);
		*/
		$fields_string = "";
		$this->oCurl->setFunction(get_class($this)."-".__FUNCTION__);
		//$this->oCurl->setDebug(true);
		//$this->oCurl->setSeguridad(true);
		$this->oCurl->setHeader($header);
		$this->oCurl->setUrl($urlBase);
		$this->oCurl->setHttpBuildPost(false);
		$url="";
		//echo $urlBase;
		if(!$this->oCurl->sendGet($url,$resultJson))
		{
			//echo '<pre>'.print_r($resultJson,true).'</pre>';die;
			FuncionesPHPLocal::MostrarMensaje($this->conexion,MSG_ERRGRAVE,"Error, ocurrio un error al buscar los datos de las novedades",array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__),array("formato"=>$this->formato));
			//$this->setError("Error","Error, ocurrio un error al buscar los datos de las novedades");
			return false;
		}
		
		
		if (!isset($resultJson['found']))
		{
			FuncionesElastic::MostrarError($resultJson);
			return false;
		}
		elseif($resultJson['found']===false)
			return true;
		else
		{
			$numfilas = 1;
			$datosRegistro = FuncionesPHPLocal::DecodificarUtf8 ($resultJson['_source']);
			return true;
		}
		
	}
 
//-----------------------------------------------------------------------------------------
//FUNCIONES PRIVADAS
//-----------------------------------------------------------------------------------------
	
	
	
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
	public function BuscarCantidadInasistenciaParo($datos,&$resultJson)
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
		
		$datosEnviar['from'] = 0;
		$datosEnviar['size'] = 0;
		
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

		if(isset($datos['ClaveEscuela']) && $datos['ClaveEscuela']!="")
		{
			$datosEnviar["query"]["bool"]["filter"][$i]["term"]["ClaveEscuela"] = $datos['ClaveEscuela'];
			$i++;
		}else
		{
		
			if(isset($datos['Distrito']) && $datos['Distrito']!="" && $datos['Distrito']!="000")
			{
				$datosEnviar["query"]["bool"]["filter"][$i]["term"]["Distrito.Id"] = $datos['Distrito'];
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

		if(isset($datos['TiposOrganizaciones']) && count($datos['TiposOrganizaciones']>0))
		{
			$datosEnviar["query"]["bool"]["filter"][$i]["terms"]["TipoOrg.Id"] = $datos['TiposOrganizaciones'];
			$i++;
		}
		
	/*	if(isset($datos['IdCategoria']) && $datos['IdCategoria']!="")
		{
			$datosEnviar["query"]["bool"]["filter"][$i]["term"]["TipoDocumento.Categoria.Id"] = $datos['IdCategoria'];
			$i++;
		}
		*/
		if(isset($datos['IdClasificacion']) && $datos['IdClasificacion']!="")
		{
			$datosEnviar["query"]["bool"]["filter"][$i]["term"]["TipoDocumento.Clasificacion.Id"] = $datos['IdClasificacion'];
			$i++;
		}
		
		if(isset($datos['TipoDocumento']) && is_array($datos['TipoDocumento']) && count($datos['TipoDocumento'])>0)
		{
			$datosEnviar["query"]["bool"]["filter"][$i]["terms"]["TipoDocumento.Id"] = $datos['TipoDocumento'];
			$i++;
		}

		/*if(isset($datos['IdEstado']) && $datos['IdEstado']!="")
		{
			$datosEnviar["query"]["bool"]["filter"][$i]["term"]["Estado.Id"] = $datos['IdEstado'];
			$i++;
		}
		if(isset($datos['IdArea']) && $datos['IdArea']!="")
		{
			$datosEnviar["query"]["bool"]["filter"][$i]["term"]["Area.Id"] = $datos['IdArea'];
			$i++;
		}	*/
		
		

	/*	if(isset($datos['IdDocumentoPadre']) && $datos['IdDocumentoPadre']!="")
		{
			$datosEnviar["query"]["bool"]["filter"][$i]["term"]["IdDocumentoPadre"] = $datos['IdDocumentoPadre'];
			$i++;
		}
		*/
		$datosEnviar["query"]["bool"]["filter"][$i]["term"]["Tipo"] = TIPODOC;
		if(isset($datos['Tipo']) && $datos['Tipo']!="")
			$datosEnviar["query"]["bool"]["filter"][$i]["term"]["Tipo"] = $datos['Tipo'];
		$i++;

		
		//$Campo = "FechaEnvio";//"FechaEnvio"
		$Campo="Periodo.FechaDesde";
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
		

		if(isset($datos['IdEstado']) && $datos['IdEstado']!="" && !in_array('-1',$datos['IdEstado']))
		{
			$datosEnviar["query"]["bool"]["filter"][$i]["terms"]["Estado.Id"] = $datos['IdEstado'];
			$i++;
		}
		else{
			$datosEnviar["query"]["bool"]["filter"][$i]["bool"]["must_not"]["term"]["Estado.Id"] =99;
			$i++;
		}
		
		/*print_r($Campo);*/
		if(isset($datos['FechaDesde']) && $datos['FechaDesde']!="")
		{
			$datosEnviar["query"]["bool"]["filter"][$i]["range"][$Campo]['gte'] = FuncionesPHPLocal::ConvertirFecha($datos['FechaDesde'],"dd/mm/aaaa","aaaa-mm-dd");
			$datosEnviar["query"]["bool"]["filter"][$i]["range"][$Campo]['lte'] = FuncionesPHPLocal::ConvertirFecha($datos['FechaDesde'],"dd/mm/aaaa","aaaa-mm-dd");
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
		
		
		if(isset($datos['AgenteDNI']) && $datos['AgenteDNI']!="")
		{
			$datosEnviar["query"]["bool"]["must"][$iMust]["term"]["Agente.Dni"] = $datos['AgenteDNI'];
			$iMust++;
		}

		
		$datosEnviar['aggs']['Distritos']['terms']['field']= "Distrito.Id";
		$datosEnviar['aggs']['Distritos']['terms']['size'] = 100000;
		$datosEnviar['aggs']['Distritos']['aggs']['CantAgentes']['cardinality']['field']= "Agente.Cuil";
	/*	$datosEnviar['aggs']['Distritos']['aggs']["Estados"]["aggs"]["UltimoFecha"]["min"]["field"]="UltimaModificacion.Fecha";
		$datosEnviar['aggs']['Distritos']['aggs']["Estados"]["aggs"]["UltimoFecha"]["min"]["format"]="yyyy-MM-dd  HH:mm:ss";*/

		
		$dataEnvio = json_encode($datosEnviar);
		//echo $dataEnvio;

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


	public function BuscarDocumentosTodos($datos,&$resultJson)
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

        if(isset($datos['TaskId']) && $datos['TaskId']!="")
        {
            $datosEnviar["query"]["bool"]["filter"][$i]["term"]["TaskId"] = $datos['TaskId'];
            $i++;
        }
		
		if(isset($datos['IdDocumento']) && $datos['IdDocumento']!="")
		{
			$datosEnviar["query"]["bool"]["filter"][$i]["term"]["IdDocumento"] = $datos['IdDocumento'];
			$i++;
		}
		
		if(isset($datos['IdClasificacion']) && $datos['IdClasificacion']!="")
		{
			$datosEnviar["query"]["bool"]["filter"][$i]["term"]["TipoDocumento.Clasificacion.Id"] = $datos['IdClasificacion'];
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
            {
                $valor = true;
                $datosEnviar["query"]["bool"]["filter"][$i]["term"]["Adecuacion"] = $valor;
                $i++;
            }
            else
            {
                $datosEnviar["query"]["bool"]["should"][0]["bool"]["must"][0]["term"]["Adecuacion"] = $valor;
                $datosEnviar["query"]["bool"]["should"][1]["bool"]["must_not"][0]["exists"]["field"] = "Adecuacion";
            }

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
		$Campo="UltimaModificacion.Fecha";
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
		
		
		if(isset($datos['AgenteDNI']) && $datos['AgenteDNI']!="")
		{
			$datosEnviar["query"]["bool"]["must"][$iMust]["term"]["Agente.Dni"] = $datos['AgenteDNI'];
			$iMust++;
		}

		
		$dataEnvio = json_encode($datosEnviar);
		/*echo $dataEnvio;
*/
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
	
	
	public function BuscarDocumentosRelacionados($datos,&$resultJson)
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

		if(isset($datos['ClaveEscuela']) && $datos['ClaveEscuela']!="")
		{
			$datosEnviar["query"]["bool"]["filter"][$i]["term"]["ClaveEscuela"] = $datos['ClaveEscuela'];
			$i++;
		}else
		{
		
			if(isset($datos['Distrito']) && $datos['Distrito']!="" && $datos['Distrito']!="000")
			{
				$datosEnviar["query"]["bool"]["filter"][$i]["term"]["Distrito.Id"] = $datos['Distrito'];
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
		
		
		if(isset($datos['IdCategoria']) && is_array($datos['IdCategoria']) && count($datos['IdCategoria'])>0)
		{
			$datosEnviar["query"]["bool"]["filter"][$i]["terms"]["TipoDocumento.Categoria.Id"] = $datos['IdCategoria'];
			$i++;
		}elseif(isset($datos['IdCategoria']) && is_numeric($datos['IdCategoria']))
		{
			$datosEnviar["query"]["bool"]["filter"][$i]["term"]["TipoDocumento.Categoria.Id"] = $datos['IdCategoria'];
			$i++;
		}
		
		
		
		
		if(isset($datos['IdClasificacion']) && $datos['IdClasificacion']!="")
		{
			$datosEnviar["query"]["bool"]["filter"][$i]["term"]["TipoDocumento.Clasificacion.Id"] = $datos['IdClasificacion'];
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



        if(isset($datos['NotIdEstado']) && $datos['NotIdEstado']!="")
        {
            $datosEnviar['query']['bool']['must_not'][0]['terms']['Estado.Id'] = $datos['NotIdEstado'];
           // $i++;
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
		
		
		if(isset($datos['AgenteDNI']) && $datos['AgenteDNI']!="")
		{
			$datosEnviar["query"]["bool"]["must"][$iMust]["term"]["Agente.Dni"] = $datos['AgenteDNI'];
			$iMust++;
		}
		
		/*
		$datosEnviar['aggs']['TiposDocumentos'] = array();
		$datosEnviar['aggs']['TiposDocumentos']['terms'] = array();
		$datosEnviar['aggs']['TiposDocumentos']['terms']['script']['source'] = "doc['TipoDocumento.Id'].value +'|'+doc['TipoDocumento.Nombre.raw'].value";
		$datosEnviar['aggs']['TiposDocumentos']['terms']['size'] = 150;
		*/
		
		$dataEnvio = json_encode($datosEnviar);
		//echo $dataEnvio;die;

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


    public function BuscarInasistenciaxCuilxFecha($datos,&$resultJson)
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
        $datosEnviar["query"]["bool"]["must"] = array();

        $f = 0;

        if(isset($datos['AgenteCuil']) && $datos['AgenteCuil']!="")
        {
            $datosEnviar["query"]["bool"]["must"][$i]["term"]["Agente.Cuil"] = $datos['AgenteCuil'];
            $i++;
        }

        if(isset($datos['ClaveEscuela']) && $datos['ClaveEscuela']!="")
        {
            $datosEnviar["query"]["bool"]["must"][$i]["term"]["ClaveEscuela"] = $datos['ClaveEscuela'];
            $i++;
        }

        if(isset($datos['TipoDocumento']) && is_array($datos['TipoDocumento']) && count($datos['TipoDocumento'])>0)
        {
            $datosEnviar["query"]["bool"]["must"][$i]["terms"]["TipoDocumento.Id"] = $datos['TipoDocumento'];
            $i++;
        }elseif(isset($datos['TipoDocumento']) && is_numeric($datos['TipoDocumento']))
        {
            $datosEnviar["query"]["bool"]["must"][$i]["term"]["TipoDocumento.Id"] = $datos['TipoDocumento'];
            $i++;
        }

        if(isset($datos['NotIdEstado']) && $datos['NotIdEstado']!="")
        {
            $datosEnviar['query']['bool']['must_not'][0]['terms']['Estado.Id'] = $datos['NotIdEstado'];
            // $i++;
        }



        if(isset($datos['Fecha']) && $datos['Fecha']!="")
        {
            $datosEnviar["query"]["bool"]["must"][$i]["range"]["Periodo.FechaDesde"]['lte'] = $datos['Fecha']."||/d";
            $i++;
            $datosEnviar["query"]["bool"]["must"][$i]["range"]["Periodo.FechaHasta"]['gte'] = $datos['Fecha']."||/d";

            $i++;
        }


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