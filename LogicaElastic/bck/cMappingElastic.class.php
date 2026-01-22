<?php 
//----------------------------------------------------------------------------------------- 
//----------------------------------------------------------------------------------------- 
// Clase genérica con la lógica para el mapeo de indices en elastic

class cMappingElastic
{

	protected $indexsuffix;
	protected $ch;
	
	// Constructor de la clase
	public function __construct($indexsuffix){
		$this->indexsuffix = $indexsuffix;
		$this->ch = curl_init();
    } 
	
	// Destructor de la clase
	public function __destruct() {
		curl_close($this->ch);
    }
	
	
	/**
	 * @param int $shards
	 * @param int $replicas
	 * @return bool
	 */
	public function CrearIndice($shards=CANTIDAD_SHARDS, $replicas=CANTIDAD_REPLICAS) : bool
	{
		$urlBase = ELASTICSERVER.'/'.INDICE.$this->indexsuffix;
		
		$curl = curl_init();
		//curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true); 
		curl_setopt($curl, CURLOPT_URL, $urlBase);
		curl_setopt($curl, CURLOPT_NOBODY, true);
		curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'HEAD');
		
		curl_exec($curl);
		$returnCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);//status
		curl_close ($curl);
		//var_dump($returnCode);
		if( (int) $returnCode === 200){
			return true;
		}
		
		$jsonData['settings']['number_of_shards'] = $shards;
		$jsonData['settings']['number_of_replicas'] = $replicas;
		
		switch($this->indexsuffix)
		{
			case INDICESUNA:
				$jsonData['settings']['analysis']['filter']['spanish_stop']['type'] = 'stop';
				$jsonData['settings']['analysis']['filter']['spanish_stop']['stopwords'] = '_spanish_';
				$jsonData['settings']['analysis']['filter']['spanish_stemmer']['type'] = 'stemmer';
				$jsonData['settings']['analysis']['filter']['spanish_stemmer']['stopwords'] = 'light_spanish';
				$jsonData['settings']['analysis']['analyzer']['case_insensitive_sort']['tokenizer'] = 'keyword';
				$jsonData['settings']['analysis']['analyzer']['case_insensitive_sort']['filter'] = array('lowercase','asciifolding','spanish_stop','spanish_stemmer');
				$jsonData['settings']['analysis']['normalizer']['case_insensitive']['type'] = 'custom';
				$jsonData['settings']['analysis']['normalizer']['case_insensitive']['char_filter'] = array();
				$jsonData['settings']['analysis']['normalizer']['case_insensitive']['filter'] = array('lowercase','asciifolding');
				
				break;
		}
		
		$datosEnviar = json_encode($jsonData);
		
		$header = array('Content-Type: application/json');
		curl_setopt($this->ch, CURLOPT_HTTPHEADER, $header);
		//curl_setopt($this->ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($this->ch, CURLOPT_URL, $urlBase);
		curl_setopt($this->ch, CURLOPT_CUSTOMREQUEST, 'PUT');
		curl_setopt($this->ch, CURLOPT_POSTFIELDS,$datosEnviar);
		curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, true);
		
		$result = curl_exec($this->ch);
		$data = json_decode($result,true);
		if(!isset($data['acknowledged']) || $data['acknowledged']===false)
		{
			FuncionesElastic::MostrarError($data);
			return false;
		}
		
		return true;
			
	}
	
	
	/**
	 * @return bool
	 */
	public function GenerarPipelines() : bool
	{


		$jsonData['description'] = utf8_encode('Hace que el IdCliente sea obligatorio en los documentos');
		$jsonData['processors'][0]['script']['lang'] = 'painless';
		$jsonData['processors'][0]['script']['inline'] = utf8_encode('if (ctx.IdCliente == null) { throw new Exception("El identificador de cliente es obligatorio") }');

		
		$datosEnviar = json_encode($jsonData);

		$urlBase = ELASTICSERVER.'/_ingest/pipeline/obligatorio';
		$header = array('Content-Type: application/json');
		curl_setopt($this->ch, CURLOPT_HTTPHEADER, $header);
		//curl_setopt($this->ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($this->ch, CURLOPT_URL, $urlBase);
		curl_setopt($this->ch, CURLOPT_CUSTOMREQUEST, 'PUT');
		curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, 1); 
		curl_setopt($this->ch, CURLOPT_POSTFIELDS,$datosEnviar);
		$result = curl_exec($this->ch);

		$data = json_decode($result,true);
		if(!isset($data['acknowledged']) || $data['acknowledged']===false)
		{
			FuncionesElastic::MostrarError($data);
			return false;
		}
		
		
		return true;
	}
	
	
	/**
	 * @param array $datos
	 * @return bool
	 */
	public function GenerarAnalizadores($datos) : bool
	{
		$jsonData = [];
		FuncionesElastic::CerrarIndice($this->indexsuffix);
		//$datos['filter'] = array('spanish_stop','spanish_stemmer');
		//$datos['filter_type'] = array('stop','stemmer');
		//$datos['filter_stopwords'] = array('_spanish_','light_spanish');
		if(!empty($datos['filter']))
		{
			foreach($datos['filter'] as $key=>$filter)
			{
				$jsonData['settings']['analysis']['filter'][$filter]['type'] = $datos['filter_type'][$key];
				$jsonData['settings']['analysis']['filter'][$filter]['stopwords'] = $datos['filter_stopwords'][$key];
			}
		}

		//$datos['analyzer'] = array('case_insensitive_sort');
		//$datos['analyzer_tokenizer'] = array('keyword');
		//$datos['analyzer_filter'] = array(array('lowercase','asciifolding','spanish'));

		if(!empty($datos['analyzer']))
		{
			foreach($datos['analyzer'] as $key=>$analyzer)
			{
				$jsonData['analysis']['analyzer'][$analyzer]['tokenizer'] = $datos['analyzer_tokenizer'][$key];
				$jsonData['analysis']['analyzer'][$analyzer]['filter'] = $datos['analyzer_filter'][$key];
			}
		}


		//$datos['normalizer'] = array('case_insensitive');
		//$datos['normalizer_type'] = array('custom');
		//$datos['normalizer_char_filter'] = array(array());
		//$datos['normalizer_filter'] = array(array('lowercase','asciifolding'));

		if(!empty($datos['normalizer']))
		{
			foreach($datos['normalizer'] as $key=>$normalizer)
			{
				$jsonData['analysis']['normalizer'][$normalizer]['type'] = $datos['normalizer_type'][$key];
				$jsonData['analysis']['normalizer'][$normalizer]['char_filter'] = $datos['normalizer_char_filter'][$key];
				$jsonData['analysis']['normalizer'][$normalizer]['filter'] = $datos['normalizer_filter'][$key];
			}
		}
		
		
		$datosEnviar = json_encode($jsonData);

		$urlBase = ELASTICSERVER.'/'.INDICE."{$this->indexsuffix}/_settings";
		$header = array('Content-Type: application/json');
		curl_setopt($this->ch, CURLOPT_HTTPHEADER, $header);
		//curl_setopt($this->ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($this->ch, CURLOPT_URL, $urlBase);
		curl_setopt($this->ch, CURLOPT_CUSTOMREQUEST, 'PUT');
		curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, 1); 
		curl_setopt($this->ch, CURLOPT_POSTFIELDS,$datosEnviar);
		$result = curl_exec($this->ch);
		$data = json_decode($result,true);
		FuncionesElastic::AbrirIndice($this->indexsuffix);
		if(!isset($data['acknowledged']) || $data['acknowledged']===false)
		{
			FuncionesElastic::MostrarError($data);
			return false;
		}
		
		
		return true;

	}
	
	
	/**
	 * @param $alias
	 * @param $data
	 * @return bool
	 */
	public function GenerarAlias($alias, &$data)
	{
		$i=0;
		$datosAlias['actions'] = array();
		$datosAlias['actions'][$i]['add'] = array();
		$datosAlias['actions'][$i]['add']['index'] = INDICE.$this->indexsuffix;
		$datosAlias['actions'][$i]['add']['alias'] = $alias;
		$datosEnviar = json_encode($datosAlias);
		$urlBase = ELASTICSERVER.'/_aliases';
		
		$header = array('Content-Type: application/json');
		curl_setopt($this->ch, CURLOPT_HTTPHEADER, $header);
		//curl_setopt($this->ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($this->ch, CURLOPT_URL, $urlBase);
		curl_setopt($this->ch, CURLOPT_CUSTOMREQUEST, 'POST');
		curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, 1); 
		curl_setopt($this->ch, CURLOPT_POSTFIELDS,$datosEnviar);
		$result = curl_exec($this->ch);
		$data = json_decode($result,true);
		if(!isset($data['acknowledged']) || $data['acknowledged']===false)
		{
			FuncionesElastic::MostrarError($data);
			return false;
		}
		
		
		return true;
	}

	public function EliminarAlias($alias,&$data)
	{
		$i=0;
		$datosAlias['actions'] = array();
		$datosAlias['actions'][$i]['remove'] = array();
		$datosAlias['actions'][$i]['remove']['index'] = INDICE.$this->indexsuffix;;
		$datosAlias['actions'][$i]['remove']['alias'] = $alias;
		$datosEnviar = json_encode($datosAlias);
		$urlBase = ELASTICSERVER.'/_aliases';
		
		$header = array('Content-Type: application/json');
		curl_setopt($this->ch, CURLOPT_HTTPHEADER, $header);
		//curl_setopt($this->ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($this->ch, CURLOPT_URL, $urlBase);
		curl_setopt($this->ch, CURLOPT_CUSTOMREQUEST, 'POST');
		curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, 1); 
		curl_setopt($this->ch, CURLOPT_POSTFIELDS,$datosEnviar);
		$result = curl_exec($this->ch);
		$data = json_decode($result,true);
		if(!isset($data['acknowledged']) || $data['acknowledged']===false)
		{
			FuncionesElastic::MostrarError($data);
			return false;
		}
		
		
		return true;
	}

	

	public function Mapping()
	{
		$datosEnviar = $this->_ObtenerMapping();
		$urlBase = ELASTICSERVER.'/'.INDICE.$this->indexsuffix.'/_mapping';
		if(INCLUDETYPE){
			$urlBase .= '/'.TYPE;
			if (VERSIONELASTIC==="7c")
				$urlBase .= '?include_type_name=true';
		}
		$header = array('Content-Type: application/json');
		curl_setopt($this->ch, CURLOPT_HTTPHEADER, $header);
		//if(defined('ELASTIC_AUTH') && '' !== ELASTIC_AUTH)
		//	curl_setopt($this->ch, CURLOPT_USERPWD, ELASTIC_AUTH);
		//curl_setopt($this->ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($this->ch, CURLOPT_URL, $urlBase);
		curl_setopt($this->ch, CURLOPT_CUSTOMREQUEST, 'PUT');
		curl_setopt($this->ch, CURLOPT_POSTFIELDS,$datosEnviar);
		curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, 1); 
		
		//execute post
		$result = curl_exec($this->ch);
		//echo $result.'<br/>';
		$data = json_decode($result,true);
		if(!isset($data['acknowledged']) || $data['acknowledged']===false)
		{
			FuncionesElastic::MostrarError($data);
			return false;
		}
		
		return true;	
	}



	public function ChangeMapping()
	{
		$jsonData = array();
		
		$datosEnviar = json_encode($jsonData);
		$urlBase = ELASTICSERVER.'/'.INDICE."{$this->indexsuffix}/_mapping";
		if("6" === VERSIONELASTIC){
			$urlBase .= '/'.TYPE;
		}
		$header = array('Content-Type: application/json');
		curl_setopt($this->ch, CURLOPT_HTTPHEADER, $header);
		//curl_setopt($this->ch, CURLOPT_SSL_VERIFYPEER, false);
		if(defined('ELASTIC_AUTH') && '' !== ELASTIC_AUTH)
			curl_setopt($this->ch, CURLOPT_USERPWD, ELASTIC_AUTH);
		curl_setopt($this->ch, CURLOPT_URL, $urlBase);
		curl_setopt($this->ch, CURLOPT_CUSTOMREQUEST, 'PUT');
		curl_setopt($this->ch, CURLOPT_POSTFIELDS,$datosEnviar);
		curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, 1); 
		
		//execute post
		$result = curl_exec($this->ch);

		$data = json_decode($result,true);
		if(!isset($data['acknowledged']) || $data['acknowledged']===false)
		{
			FuncionesElastic::MostrarError($data);
			return false;
		}
		
		return true;	
	}
	
	/**
	 * @return bool
	 */
	public function EliminarIndice() : bool
	{
		$urlBase = ELASTICSERVER.'/'.INDICE.$this->indexsuffix;
		$header = array('Content-Type: application/json');
		curl_setopt($this->ch, CURLOPT_HTTPHEADER, $header);
		//curl_setopt($this->ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($this->ch, CURLOPT_URL, $urlBase);
		curl_setopt($this->ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
		curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, 1); 
		$result = curl_exec($this->ch);
		$data = json_decode($result,true);
		if(!isset($data['acknowledged']) || $data['acknowledged']===false)
		{
			FuncionesElastic::MostrarError($data);
			return false;
		}
		
		return true;	
	}
	
	private function _ObtenerMapping()
	{
		
		switch($this->indexsuffix)
		{
			case INDICESUNA:
				$jsonData = self::_EstructuraSuna();
			break;
			case INDICEAUDITORIA:
				$jsonData = self::_EstructuraAuditorias();
			break;
			case INDICETABLAS:
				$jsonData = self::_EstructuraTablasAnexas();
			break;
			case INDICELICENCIAS:
				$jsonData = self::_EstructuraLicencias();
			break;
			case INDICE_ESTASIT:
				$jsonData = self::_EstructuraEstasit();
				break;
			default:
				if(file_exists(PUBLICA."mappings/{$this->indexsuffix}.json"))
					$jsonData = file_get_contents(PUBLICA."mappings/{$this->indexsuffix}.json");
				else
					$jsonData = '{"properties":{"Texto":{"type":"text"}}}';
			break;
		}
		
		return $jsonData;
	}


	private static function _EstructuraAuditorias()
	{

		$jsonData = self::_EstructuraSuna(true);
		unset($jsonData['properties']['Estado'],$jsonData['properties']['Area']);
		$jsonData['properties']['AccionCambio']['type'] = 'keyword';
		$jsonData['properties']['IdFilaLog']['type'] = 'integer';
		
		$jsonData['properties']['Estado']['properties']['Inicial']['properties']['Id']['type'] = 'integer';
		$jsonData['properties']['Estado']['properties']['Inicial']['properties']['Nombre']['type'] = 'text';
		$jsonData['properties']['Estado']['properties']['Final']['properties']['Id']['type'] = 'integer';
		$jsonData['properties']['Estado']['properties']['Final']['properties']['Nombre']['type'] = 'text';

		$jsonData['properties']['Area']['properties']['Inicial']['properties']['Id']['type'] = 'integer';
		$jsonData['properties']['Area']['properties']['Inicial']['properties']['Nombre']['type'] = 'text';
		$jsonData['properties']['Area']['properties']['Final']['properties']['Id']['type'] = 'integer';
		$jsonData['properties']['Area']['properties']['Final']['properties']['Nombre']['type'] = 'text';

		return json_encode($jsonData);
	}
	
	
	/**
	 * @param bool $returnArray
	 * @return mixed
	 */
	private static function _EstructuraSuna($returnArray=false)
	{

		$jsonData['dynamic'] = 'strict';
		$jsonData['properties']['IdDocumento']['type'] = 'integer';
		$jsonData['properties']['IdDocumentoPadre']['type'] = 'integer';
		$jsonData['properties']['Tipo']['type'] = 'keyword';
		
		
		$jsonData['properties']['DocumentosHijos'] = [];
		$jsonData['properties']['DocumentosHijos']['type'] = 'nested';
		$jsonData['properties']['DocumentosHijos']['properties'] = [];
		$jsonData['properties']['DocumentosHijos']['properties']['IdDocumento'] = [];
		$jsonData['properties']['DocumentosHijos']['properties']['IdDocumento']['type'] = 'integer';
		$jsonData['properties']['DocumentosHijos']['properties']['IdTipoDocumento'] = [];
		$jsonData['properties']['DocumentosHijos']['properties']['IdTipoDocumento']['type'] = 'integer';
		$jsonData['properties']['DocumentosHijos']['properties']['NombreTipoDocumento'] = [];
		$jsonData['properties']['DocumentosHijos']['properties']['NombreTipoDocumento']['type'] = 'keyword';
		$jsonData['properties']['DocumentosHijos']['properties']['IdArea'] = [];
		$jsonData['properties']['DocumentosHijos']['properties']['IdArea']['type'] = 'integer';
		$jsonData['properties']['DocumentosHijos']['properties']['IdEstado'] = [];
		$jsonData['properties']['DocumentosHijos']['properties']['IdEstado']['type'] = 'integer';



        $jsonData['properties']['DocumentosRelacionados'] = [];
        $jsonData['properties']['DocumentosRelacionados']['type'] = 'nested';
        $jsonData['properties']['DocumentosRelacionados']['properties'] = [];
        $jsonData['properties']['DocumentosRelacionados']['properties']['IdDocumento'] = [];
        $jsonData['properties']['DocumentosRelacionados']['properties']['IdDocumento']['type'] = 'integer';
        $jsonData['properties']['DocumentosRelacionados']['properties']['IdTipoDocumento'] = [];
        $jsonData['properties']['DocumentosRelacionados']['properties']['IdTipoDocumento']['type'] = 'integer';
        $jsonData['properties']['DocumentosRelacionados']['properties']['NombreTipoDocumento'] = [];
        $jsonData['properties']['DocumentosRelacionados']['properties']['NombreTipoDocumento']['type'] = 'keyword';

        $jsonData['properties']['DocumentosRelacionados']['properties']['Cuil']['type'] = 'keyword';
        $jsonData['properties']['DocumentosRelacionados']['properties']['Nombre']['type'] = 'text';
        $jsonData['properties']['DocumentosRelacionados']['properties']['Nombre']['analyzer'] = 'spanish';
        $jsonData['properties']['DocumentosRelacionados']['properties']['Nombre']['fields']['raw']  = array('type' => 'keyword');
        $jsonData['properties']['DocumentosRelacionados']['properties']['Apellido']['type'] = 'text';
        $jsonData['properties']['DocumentosRelacionados']['properties']['Apellido']['analyzer'] = 'spanish';
        $jsonData['properties']['DocumentosRelacionados']['properties']['Apellido']['fields']['raw']  = array('type' => 'keyword');
        $jsonData['properties']['DocumentosRelacionados']['properties']['ClaveEscuela']['type'] = 'keyword';
        //$jsonData['properties']['DocumentosRelacionados']['properties']['IdArea'] = [];
        //$jsonData['properties']['DocumentosRelacionados']['properties']['IdArea']['type'] = 'integer';
        //$jsonData['properties']['DocumentosRelacionados']['properties']['IdEstado'] = [];
        //$jsonData['properties']['DocumentosRelacionados']['properties']['IdEstado']['type'] = 'integer';
		
		
		
		$jsonData['properties']['CDependencia']['type'] = 'integer';
		$jsonData['properties']['ClaveEscuela']['type'] = 'keyword';
        $jsonData['properties']['ClaveEscuelaDestino']['type'] = 'keyword';
        $jsonData['properties']['Adecuacion']['type'] = 'boolean';
		$jsonData['properties']['Ensenanza']['type'] = 'keyword';
		$jsonData['properties']['Distrito']['properties']['Id']['type'] = 'keyword';
		$jsonData['properties']['Distrito']['properties']['Nombre']['type'] = 'text';
		$jsonData['properties']['Distrito']['properties']['Nombre']['fields']['raw']['type'] = 'keyword';
		$jsonData['properties']['TipoOrg']['properties']['Id']['type'] = 'keyword';
		$jsonData['properties']['TipoOrg']['properties']['Nombre']['type'] = 'text';
		$jsonData['properties']['TipoOrg']['properties']['Nombre']['fields']['raw']['type'] = 'keyword';
		$jsonData['properties']['Escuela']['properties']['Id']['type'] = 'keyword';
		$jsonData['properties']['Escuela']['properties']['Nombre']['type'] = 'text';
		$jsonData['properties']['Escuela']['properties']['Nombre']['fields']['raw']['type'] = 'keyword';


		$jsonData['properties']['TipoArchivo']['properties']['Id']['type'] = 'integer';
		$jsonData['properties']['TipoArchivo']['properties']['Nombre']['type'] = 'text';
		$jsonData['properties']['TipoArchivo']['properties']['Nombre']['fields']['raw']['type'] = 'keyword';

		$jsonData['properties']['IdArchivo']['type'] = 'integer';
		$jsonData['properties']['NombreArchivo']['type'] = 'keyword';
		$jsonData['properties']['TamanioArchivo']['type'] = 'integer';
		$jsonData['properties']['UbicacionArchivo']['type'] = 'keyword';
		$jsonData['properties']['HashArchivo']['type'] = 'keyword';

		$jsonData['properties']['TipoDocumento']['properties']['Id']['type'] = 'keyword';
		$jsonData['properties']['TipoDocumento']['properties']['IdRegistro']['type'] = 'integer';
		$jsonData['properties']['TipoDocumento']['properties']['Nombre']['type'] = 'text';
		$jsonData['properties']['TipoDocumento']['properties']['Nombre']['fields']['raw']['type'] = 'keyword';
		$jsonData['properties']['TipoDocumento']['properties']['NombreCorto']['type'] = 'keyword';
		$jsonData['properties']['TipoDocumento']['properties']['Categoria']['properties']['Id']['type'] = 'integer';
		$jsonData['properties']['TipoDocumento']['properties']['Categoria']['properties']['Nombre']['type'] = 'text';
		$jsonData['properties']['TipoDocumento']['properties']['Categoria']['properties']['Nombre']['analyzer'] = 'spanish';
		$jsonData['properties']['TipoDocumento']['properties']['Categoria']['properties']['Nombre']['fields']['raw']  = array('type' => 'keyword');
		$jsonData['properties']['TipoDocumento']['properties']['Clasificacion']['properties']['Id']['type'] = 'integer';
		$jsonData['properties']['TipoDocumento']['properties']['Clasificacion']['properties']['Nombre']['type'] = 'text';
		$jsonData['properties']['TipoDocumento']['properties']['Clasificacion']['properties']['Nombre']['analyzer'] = 'spanish';
		$jsonData['properties']['TipoDocumento']['properties']['Clasificacion']['properties']['Nombre']['fields']['raw']  = array('type' => 'keyword');


		/*DATOS DEL AGENTE*/
		$jsonData['properties']['TieneCUPOF']['type'] = 'byte';
		$jsonData['properties']['ObservacionesCUPOF']['type'] = 'text';
		$jsonData['properties']['CUPOF']['type'] = 'keyword';
		$jsonData['properties']['Agente']['properties']['TipoSeleccion']['type'] = 'byte';
		$jsonData['properties']['Agente']['properties']['Cuil']['type'] = 'keyword';
		$jsonData['properties']['Agente']['properties']['Dni']['type'] = 'keyword';
		$jsonData['properties']['Agente']['properties']['Nombre']['type'] = 'text';
		$jsonData['properties']['Agente']['properties']['Nombre']['analyzer'] = 'spanish';
		$jsonData['properties']['Agente']['properties']['Nombre']['fields']['raw']  = array('type' => 'keyword');
		$jsonData['properties']['Agente']['properties']['Apellido']['type'] = 'text';
		$jsonData['properties']['Agente']['properties']['Apellido']['analyzer'] = 'spanish';
		$jsonData['properties']['Agente']['properties']['Apellido']['fields']['raw']  = array('type' => 'keyword');
		$jsonData['properties']['Agente']['properties']['FechaNacimiento']['type'] = 'date';
		$jsonData['properties']['Agente']['properties']['FechaNacimiento']['format'] = 'yyyy-MM-dd||epoch_millis';
		$jsonData['properties']['Agente']['properties']['Cuil']['type'] = 'keyword';
		$jsonData['properties']['Agente']['properties']['Sexo']['type'] = 'keyword';


		$jsonData['properties']['Cargo']['properties']['Revista']['type'] = 'keyword';
		$jsonData['properties']['Cargo']['properties']['ModalidadCarrera']['type'] = 'text';
		$jsonData['properties']['Cargo']['properties']['ModalidadCarrera']['analyzer'] = 'spanish';
		$jsonData['properties']['Cargo']['properties']['EspCursoAsig']['type'] = 'text';
		$jsonData['properties']['Cargo']['properties']['EspCursoAsig']['analyzer'] = 'spanish';
		$jsonData['properties']['Cargo']['properties']['Area']['type'] = 'text';
		$jsonData['properties']['Cargo']['properties']['Area']['analyzer'] = 'spanish';
		$jsonData['properties']['Cargo']['properties']['Asignacion']['type'] = 'text';
		$jsonData['properties']['Cargo']['properties']['Asignacion']['analyzer'] = 'spanish';
		$jsonData['properties']['Cargo']['properties']['HsModCar']['type'] = 'text';
		$jsonData['properties']['Cargo']['properties']['HsModCar']['analyzer'] = 'spanish';
		$jsonData['properties']['Cargo']['properties']['HsCargo']['type'] = 'text';
		$jsonData['properties']['Cargo']['properties']['HsCargo']['analyzer'] = 'spanish';
		$jsonData['properties']['Cargo']['properties']['HsTrabajadas']['type'] = 'text';
		$jsonData['properties']['Cargo']['properties']['HsTrabajadas']['analyzer'] = 'spanish';
		$jsonData['properties']['Cargo']['properties']['Funcion']['type'] = 'text';
		$jsonData['properties']['Cargo']['properties']['Funcion']['analyzer'] = 'spanish';
		$jsonData['properties']['Cargo']['properties']['Anio']['type'] = 'short';
		$jsonData['properties']['Cargo']['properties']['Seccion']['type'] = 'text';
		$jsonData['properties']['Cargo']['properties']['Seccion']['analyzer'] = 'spanish';
		$jsonData['properties']['Cargo']['properties']['IdTurno']['type'] = 'keyword';
		$jsonData['properties']['Cargo']['properties']['Secuencia']['type'] = 'keyword';
		$jsonData['properties']['Cargo']['properties']['SubSecuencia']['type'] = 'keyword';
		$jsonData['properties']['Cargo']['properties']['CodigoArea']['type'] = 'keyword';
		$jsonData['properties']['Cargo']['properties']['CodigoAsignatura']['type'] = 'keyword';
		$jsonData['properties']['Cargo']['properties']['NivelEnsenanza']['type'] = 'text';
		$jsonData['properties']['Cargo']['properties']['NivelEnsenanza']['analyzer'] = 'spanish';

		$jsonData['properties']['Cargo']['properties']['RegimenEstatutario']['type'] = 'keyword';
		$jsonData['properties']['Cargo']['properties']['Grupo']['type'] = 'keyword';
		$jsonData['properties']['Cargo']['properties']['SubGrupo']['type'] = 'keyword';


		$jsonData['properties']['Cargos']['type'] = 'nested';
		$jsonData['properties']['Cargos']['properties']['Secuencia']['type'] = 'keyword';
		$jsonData['properties']['Cargos']['properties']['SubSecuencia']['type'] = 'keyword';
		$jsonData['properties']['Cargos']['properties']['Revista']['type'] = 'keyword';
		$jsonData['properties']['Cargos']['properties']['RealIntOut']['type'] = 'keyword';
		$jsonData['properties']['Cargos']['properties']['Asignatura']['type'] = 'keyword';
		$jsonData['properties']['Cargos']['properties']['ModalidadCarrera']['type'] = 'text';
		$jsonData['properties']['Cargos']['properties']['ModalidadCarrera']['analyzer'] = 'spanish';
		$jsonData['properties']['Cargos']['properties']['Area']['type'] = 'keyword';
		$jsonData['properties']['Cargos']['properties']['CargoCodigo']['type'] = 'keyword';
		$jsonData['properties']['Cargos']['properties']['CargoDescripcion']['type'] = 'text';
		$jsonData['properties']['Cargos']['properties']['CargoDescripcion']['analyzer'] = 'spanish';


		$jsonData['properties']['Cargos']['properties']['GrupoCodigo']['type'] = 'keyword';
		$jsonData['properties']['Cargos']['properties']['GrupoDescripcion']['type'] = 'text';
		$jsonData['properties']['Cargos']['properties']['GrupoDescripcion']['analyzer'] = 'spanish';

		$jsonData['properties']['Cargos']['properties']['SubGrupoCodigo']['type'] = 'keyword';
		$jsonData['properties']['Cargos']['properties']['SubGrupoDescripcion']['type'] = 'text';
		$jsonData['properties']['Cargos']['properties']['SubGrupoDescripcion']['analyzer'] = 'spanish';


		$jsonData['properties']['Cargos']['properties']['RegimenEstatutarioCodigo']['type'] = 'keyword';
		$jsonData['properties']['Cargos']['properties']['RegimenEstatutarioDescripcion']['type'] = 'text';
		$jsonData['properties']['Cargos']['properties']['RegimenEstatutarioDescripcion']['analyzer'] = 'spanish';



		$jsonData['properties']['Cargos']['properties']['CargoHsMod']['type'] = 'text';
		$jsonData['properties']['Cargos']['properties']['CargoHsMod']['analyzer'] = 'spanish';
		$jsonData['properties']['Cargos']['properties']['CargoEnsenanza']['type'] = 'keyword';
		$jsonData['properties']['Cargos']['properties']['Anio']['type'] = 'short';
		$jsonData['properties']['Cargos']['properties']['Seccion']['type'] = 'keyword';
		$jsonData['properties']['Cargos']['properties']['IdTurno']['type'] = 'keyword';
		$jsonData['properties']['Cargos']['properties']['HsDesignacion']['type'] = 'keyword';
		$jsonData['properties']['Cargos']['properties']['HsDesignacionDescripcion']['type'] = 'text';
		$jsonData['properties']['Cargos']['properties']['HsDesignacionDescripcion']['analyzer'] = 'spanish';
        $jsonData['properties']['Cargos']['properties']['Tipo']['type'] = 'keyword';
        $jsonData['properties']['Cargos']['properties']['CodigoMovimiento']['type'] = 'keyword';



        /*DATOS CARGOS DIAS*/
        $jsonData['properties']['CargosDias']['type'] = 'nested';
        $jsonData['properties']['CargosDias']['properties']['Secuencia']['type'] = 'keyword';
        $jsonData['properties']['CargosDias']['properties']['SubSecuencia']['type'] = 'keyword';
        $jsonData['properties']['CargosDias']['properties']['Dia']['type'] = 'short';
        $jsonData['properties']['CargosDias']['properties']['DiaDescripcion']['type'] = 'keyword';
        $jsonData['properties']['CargosDias']['properties']['Turno']['type'] = 'keyword';
        $jsonData['properties']['CargosDias']['properties']['TurnoDescripcion']['type'] = 'keyword';
        $jsonData['properties']['CargosDias']['properties']['HoraInicio']['type'] = 'date';
        $jsonData['properties']['CargosDias']['properties']['HoraInicio']['format'] =  'HH:mm||yyyy-MM-dd HH:mm:ss||epoch_millis';
        $jsonData['properties']['CargosDias']['properties']['HoraFin']['type'] = 'date';
        $jsonData['properties']['CargosDias']['properties']['HoraFin']['format'] =  'HH:mm||yyyy-MM-dd HH:mm:ss||epoch_millis';


		/*DATOS DEL ALTA/MOVIMIENTO/CESE*/
		
		$jsonData['properties']['Periodo']['properties']['FechaDesde']['type'] = 'date';
		$jsonData['properties']['Periodo']['properties']['FechaDesde']['format'] = 'yy-MM-dd||yyyy-MM-dd||epoch_millis';

		$jsonData['properties']['Periodo']['properties']['FechaHasta']['type'] = 'date';
		$jsonData['properties']['Periodo']['properties']['FechaHasta']['format'] = 'yy-MM-dd||yyyy-MM-dd||epoch_millis';


		/*
		$jsonData['properties']['Accion']['properties']['TipoAccion']['type'] = 'keyword';
		$jsonData['properties']['Accion']['properties']['CDependencia']['type'] = 'keyword';
		$jsonData['properties']['Accion']['properties']['ClaveEscuela']['type'] = 'keyword';
		$jsonData['properties']['Accion']['properties']['Ensenianza']['type'] = 'keyword';

		$jsonData['properties']['Accion']['properties']['Distrito']['properties']['Id']['type'] = 'keyword';
		$jsonData['properties']['Accion']['properties']['Distrito']['properties']['Nombre']['type'] = 'text';
		$jsonData['properties']['Accion']['properties']['Distrito']['properties']['Nombre']['fields']['raw']['type'] = 'keyword';

		$jsonData['properties']['Accion']['properties']['TipoOrg']['properties']['Id']['type'] = 'keyword';
		$jsonData['properties']['Accion']['properties']['TipoOrg']['properties']['Nombre']['type'] = 'text';
		$jsonData['properties']['Accion']['properties']['TipoOrg']['properties']['Nombre']['fields']['raw']['type'] = 'keyword';

		$jsonData['properties']['Accion']['properties']['Escuela']['properties']['Id']['type'] = 'integer';
		$jsonData['properties']['Accion']['properties']['Escuela']['properties']['Nombre']['type'] = 'text';
		$jsonData['properties']['Accion']['properties']['Escuela']['properties']['Nombre']['fields']['raw']['type'] = 'keyword';


		$jsonData['properties']['Accion']['properties']['TipoSeleccion']['type'] = 'byte';
		$jsonData['properties']['Accion']['properties']['ModalidadCarrera']['type'] = 'text';
		$jsonData['properties']['Accion']['properties']['ModalidadCarrera']['analyzer'] = 'spanish';
		$jsonData['properties']['Accion']['properties']['EspCursoAsig']['type'] = 'text';
		$jsonData['properties']['Accion']['properties']['EspCursoAsig']['analyzer'] = 'spanish';
		$jsonData['properties']['Accion']['properties']['Area']['type'] = 'text';
		$jsonData['properties']['Accion']['properties']['Area']['analyzer'] = 'spanish';
		$jsonData['properties']['Accion']['properties']['Asignacion']['type'] = 'text';
		$jsonData['properties']['Accion']['properties']['Asignacion']['analyzer'] = 'spanish';
		$jsonData['properties']['Accion']['properties']['HsModCar']['type'] = 'text';
		$jsonData['properties']['Accion']['properties']['HsModCar']['analyzer'] = 'spanish';
		$jsonData['properties']['Accion']['properties']['HsCargo']['type'] = 'text';
		$jsonData['properties']['Accion']['properties']['HsCargo']['analyzer'] = 'spanish';

		$jsonData['properties']['Accion']['properties']['Cargo']['type'] = 'keyword';

		$jsonData['properties']['Accion']['properties']['Anio']['type'] = 'short';
		$jsonData['properties']['Accion']['properties']['Seccion']['type'] = 'text';
		$jsonData['properties']['Accion']['properties']['Seccion']['analyzer'] = 'spanish';
		$jsonData['properties']['Accion']['properties']['IdTurno']['type'] = 'keyword';
		*/


		/*DATOS DE INASISTENCIA*/
		
		$jsonData['properties']['Inasistencia']['properties']['FechaDesde']['type'] = 'date';
		$jsonData['properties']['Inasistencia']['properties']['FechaDesde']['format'] = 'yyyy-MM-dd||epoch_millis';
		$jsonData['properties']['Inasistencia']['properties']['FechaHasta']['type'] = 'date';
		$jsonData['properties']['Inasistencia']['properties']['FechaHasta']['format'] = 'yyyy-MM-dd||epoch_millis';
		

		$jsonData['properties']['Inasistencia']['properties']['ModSem']['type'] = 'integer';
		$jsonData['properties']['Inasistencia']['properties']['ModMen']['type'] = 'integer';
		$jsonData['properties']['Inasistencia']['properties']['HsDesignadas']['type'] = 'integer';
		$jsonData['properties']['Inasistencia']['properties']['HsTrabajadas']['type'] = 'integer';
		$jsonData['properties']['Inasistencia']['properties']['HsDescontadas']['type'] = 'integer';
		$jsonData['properties']['Inasistencia']['properties']['LicenciaEncuadreArticulo']['type'] = 'text';
		$jsonData['properties']['Inasistencia']['properties']['LicenciaEncuadreArticulo']['analyzer'] = 'spanish';
		$jsonData['properties']['Inasistencia']['properties']['LicenciaEncuadreInsiso']['type'] = 'text';
		$jsonData['properties']['Inasistencia']['properties']['LicenciaEncuadreInsiso']['analyzer'] = 'spanish';


		$jsonData['properties']['FechaTomaPosesion']['type'] = 'date';
		$jsonData['properties']['FechaTomaPosesion']['format'] = 'yyyy-MM-dd HH:mm:ss||yyyy-MM-dd||epoch_millis';

		$jsonData['properties']['FechaImpactoHost']['type'] = 'date';
		$jsonData['properties']['FechaImpactoHost']['format'] = 'yyyy-MM-dd HH:mm:ss||yyyy-MM-dd||epoch_millis';

		$jsonData['properties']['MovimientoFecha']['type'] = 'date';
		$jsonData['properties']['MovimientoFecha']['format'] = 'yyyy-MM-dd HH:mm:ss||yyyy-MM-dd||epoch_millis';

		$jsonData['properties']['NroResolucion']['type'] =  'text';
		$jsonData['properties']['OrganizacionApoyo']['type'] =  'text';
		$jsonData['properties']['TaskId']['type'] =  'keyword';

		$jsonData['properties']['DniReemplazo']['type'] = 'keyword';
		$jsonData['properties']['DniReemplazoSexo']['type'] = 'keyword';
		$jsonData['properties']['DniReemplazoSecuencia']['type'] = 'keyword';
		$jsonData['properties']['DniReemplazoSubSecuencia']['type'] = 'keyword';
		$jsonData['properties']['DniReemplazoRealIntOut']['type'] = 'keyword';
        $jsonData['properties']['DniReemplazoTipo']['type'] = 'keyword';
        $jsonData['properties']['DniReemplazoCodigoMovimiento']['type'] = 'keyword';

        $jsonData['properties']['NumeroOrden']['type'] = 'byte';

        $jsonData['properties']['Puntaje']['type'] = 'scaled_float';
        $jsonData['properties']['Puntaje']['scaling_factor'] = 100;



		$jsonData['properties']['CargosReemplazo']['type'] = 'nested';
		$jsonData['properties']['CargosReemplazo']['properties']['Secuencia']['type'] = 'keyword';
		$jsonData['properties']['CargosReemplazo']['properties']['SubSecuencia']['type'] = 'keyword';
		$jsonData['properties']['CargosReemplazo']['properties']['SecuenciaReemplazo']['type'] = 'keyword';
		$jsonData['properties']['CargosReemplazo']['properties']['SubSecuenciaReemplazo']['type'] = 'keyword';
		$jsonData['properties']['CargosReemplazo']['properties']['Revista']['type'] = 'keyword';
		$jsonData['properties']['CargosReemplazo']['properties']['RealIntOut']['type'] = 'keyword';
		$jsonData['properties']['CargosReemplazo']['properties']['Asignatura']['type'] = 'keyword';
		$jsonData['properties']['CargosReemplazo']['properties']['ModalidadCarrera']['type'] = 'text';
		$jsonData['properties']['CargosReemplazo']['properties']['ModalidadCarrera']['analyzer'] = 'spanish';
		$jsonData['properties']['CargosReemplazo']['properties']['Area']['type'] = 'keyword';
		$jsonData['properties']['CargosReemplazo']['properties']['CargoCodigo']['type'] = 'keyword';
		$jsonData['properties']['CargosReemplazo']['properties']['CargoDescripcion']['type'] = 'text';
		$jsonData['properties']['CargosReemplazo']['properties']['CargoDescripcion']['analyzer'] = 'spanish';
		$jsonData['properties']['CargosReemplazo']['properties']['CargoHsMod']['type'] = 'text';
		$jsonData['properties']['CargosReemplazo']['properties']['CargoHsMod']['analyzer'] = 'spanish';
		$jsonData['properties']['CargosReemplazo']['properties']['CargoEnsenanza']['type'] = 'keyword';
		$jsonData['properties']['CargosReemplazo']['properties']['Anio']['type'] = 'short';
		$jsonData['properties']['CargosReemplazo']['properties']['Seccion']['type'] = 'keyword';
		$jsonData['properties']['CargosReemplazo']['properties']['IdTurno']['type'] = 'keyword';
		$jsonData['properties']['CargosReemplazo']['properties']['HsDesignacion']['type'] = 'keyword';
		$jsonData['properties']['CargosReemplazo']['properties']['HsDesignacionDescripcion']['type'] = 'text';
		$jsonData['properties']['CargosReemplazo']['properties']['HsDesignacionDescripcion']['analyzer'] = 'spanish';
        $jsonData['properties']['CargosReemplazo']['properties']['Tipo']['type'] = 'keyword';
        $jsonData['properties']['CargosReemplazo']['properties']['CodigoMovimiento']['type'] = 'keyword';



		$jsonData['properties']['Observaciones']['type'] = 'text';
		$jsonData['properties']['Observaciones']['analyzer'] = 'spanish';

		$jsonData['properties']['ObservacionesHOST']['type'] = 'text';
		$jsonData['properties']['ObservacionesHOST']['analyzer'] = 'spanish';
		$jsonData['properties']['ObservacionesHOST']['fields']['raw']['type'] = 'keyword';


		$jsonData['properties']['Alta']['properties']['APP']['type'] = 'text';
		$jsonData['properties']['Alta']['properties']['ClaveEscuela']['type'] = 'text';
		$jsonData['properties']['Alta']['properties']['Escalafon']['type'] = 'text';
		$jsonData['properties']['Alta']['properties']['Cuil']['type'] = 'text';
		$jsonData['properties']['Alta']['properties']['Fecha']['type'] = 'date';
		$jsonData['properties']['Alta']['properties']['Fecha']['format'] = 'yyyy-MM-dd HH:mm:ss||yyyy-MM-dd||epoch_millis';


		$jsonData['properties']['UltimaModificacion']['properties']['APP']['type'] = 'text';
		$jsonData['properties']['UltimaModificacion']['properties']['ClaveEscuela']['type'] = 'text';
		$jsonData['properties']['UltimaModificacion']['properties']['Escalafon']['type'] = 'text';
		$jsonData['properties']['UltimaModificacion']['properties']['Cuil']['type'] = 'text';
		$jsonData['properties']['UltimaModificacion']['properties']['Cuil']['fields']['raw']  = array('type' => 'keyword');
		$jsonData['properties']['UltimaModificacion']['properties']['Fecha']['type'] = 'date';
		$jsonData['properties']['UltimaModificacion']['properties']['Fecha']['format'] = 'yyyy-MM-dd HH:mm:ss||yyyy-MM-dd||epoch_millis';


		$jsonData['properties']['Estado']['properties']['Id']['type'] = 'keyword';
		$jsonData['properties']['Estado']['properties']['Nombre']['type'] = 'text';
		$jsonData['properties']['Estado']['properties']['Nombre']['fields']['raw']  = array('type' => 'keyword');

		$jsonData['properties']['Area']['properties']['Id']['type'] = 'integer';
		$jsonData['properties']['Area']['properties']['Nombre']['type'] = 'text';
		$jsonData['properties']['Area']['properties']['Nombre']['fields']['raw']  = array('type' => 'keyword');

		$jsonData['properties']['FechaEnvio']['type'] = 'date';
		$jsonData['properties']['FechaEnvio']['format'] = 'yyyy-MM-dd HH:mm:ss||yyyy-MM-dd||epoch_millis';

		$jsonData['properties']['UltimaModificacionFecha']['type'] = 'date';
		$jsonData['properties']['UltimaModificacionFecha']['format'] = 'yyyy-MM-dd HH:mm:ss||yyyy-MM-dd||epoch_millis';

		$jsonData['properties']['FechaDesignacion']['type'] = "date";
		$jsonData['properties']['FechaDesignacion']['format'] = "yyyy-MM-dd HH:mm:ss||yyyy-MM-dd||epoch_millis";

		$jsonData['properties']['PID']['type'] = "text";
		//$jsonData['properties']['PID']['analyzer'] = 'spanish';


       	return $returnArray ? $jsonData : json_encode($jsonData);
	}

	
	private static function _EstructuraTablasAnexas()
	{

		$jsonData['properties']['Tipo']['type'] = 'keyword';
		$jsonData['properties']['IdRegistro']['type'] = 'integer';
		$jsonData['properties']['IdRegistroExterno']['type'] = 'integer';
		$jsonData['properties']['Codigo']['type'] = 'keyword';
		$jsonData['properties']['Nombre']['properties']['Id']['type'] = 'keyword';
		$jsonData['properties']['Orden']['type'] = 'integer';
		$jsonData['properties']['Descripcion']['type'] = 'text';
		$jsonData['properties']['Descripcion']['fields']['raw']['type'] = 'keyword';
		$jsonData['properties']['DescripcionLarga']['type'] = 'text';
		$jsonData['properties']['DescripcionLarga']['fields']['raw']['type'] = 'keyword';

		$jsonData['properties']['IdEnsenanzaExterno']['type'] = 'integer';
		$jsonData['properties']['IdNivelExterno']['type'] = 'integer';
		$jsonData['properties']['IdModalidadExterno']['type'] = 'integer';
		$jsonData['properties']['IdDependenciaFuncionalExterno']['type'] = 'integer';
		$jsonData['properties']['IdRegimenEstatutarioExterno']['type'] = 'integer';
		$jsonData['properties']['IdCarreraExterno']['type'] = 'integer';
		$jsonData['properties']['IdAsignaturaExterno']['type'] = 'integer';
		$jsonData['properties']['IdSeccionExterno']['type'] = 'integer';
		$jsonData['properties']['IdAreaEducacionExterno']['type'] = 'integer';
		
		$jsonData['properties']['AuditoriaTitulo']['type'] = 'integer';
		$jsonData['properties']['TipoEnsenanza']['type'] = 'keyword';
		$jsonData['properties']['Numero']['type'] = 'integer';
		$jsonData['properties']['IdRegionExterno']['type'] = 'integer';
		$jsonData['properties']['IdProvinciaExterno']['type'] = 'integer';
		$jsonData['properties']['PartidoCatastro']['type'] = 'keyword';
		$jsonData['properties']['Estado']['properties']['Id']['type'] = 'integer';
		$jsonData['properties']['Estado']['properties']['Nombre']['type'] = 'text';
		

		$jsonData['properties']['Carrera']['properties']['Id']['type'] = 'keyword';
		$jsonData['properties']['Carrera']['properties']['Codigo']['type'] = 'keyword';
		$jsonData['properties']['Carrera']['properties']['Nombre']['type'] = 'text';
		
		$jsonData['properties']['Area']['properties']['Id']['type'] = 'keyword';
		$jsonData['properties']['Area']['properties']['Codigo']['type'] = 'keyword';
		$jsonData['properties']['Area']['properties']['Nombre']['type'] = 'text';

		$jsonData['properties']['Asignatura']['properties']['Id']['type'] = 'keyword';
		$jsonData['properties']['Asignatura']['properties']['Codigo']['type'] = 'keyword';
		$jsonData['properties']['Asignatura']['properties']['Nombre']['type'] = 'text';

		$jsonData['properties']['MarcaModulo']['type'] = 'keyword';
		$jsonData['properties']['Auxiliar']['type'] = 'boolean';
		
		$jsonData['properties']['FechaDesde']['type'] = 'date';
		$jsonData['properties']['FechaDesde']['format'] = 'yyyy-MM-dd HH:mm:ss||yyyy-MM-dd||epoch_millis';
		$jsonData['properties']['FechaDesde']['null_value'] = 'NULL';
		$jsonData['properties']['FechaHasta']['type'] = 'date';
		$jsonData['properties']['FechaHasta']['format'] = 'yyyy-MM-dd HH:mm:ss||yyyy-MM-dd||epoch_millis';
		$jsonData['properties']['FechaHasta']['null_value'] = 'NULL';

		$jsonData['properties']['HsPlan']['type'] = 'text';

		
		return json_encode($jsonData);
	}
	

	
	private static function _EstructuraLicencias()
	{

		$jsonData['properties']['TaskId']['type'] = 'keyword';
		$jsonData['properties']['Tipo']['type'] = 'keyword';
		$jsonData['properties']['Cuil']['type'] = 'keyword';
		$jsonData['properties']['Tenant']['type'] = 'keyword';
		$jsonData['properties']['FechaCreacion']['type'] = 'date';
		$jsonData['properties']['FechaCreacion']['format'] = 'yyyy-MM-dd HH:mm:ss||yyyy-MM-dd||epoch_millis';
		$jsonData['properties']['FechaInicio']['type'] = 'date';
		$jsonData['properties']['FechaInicio']['format'] = 'yyyy-MM-dd HH:mm:ss||yyyy-MM-dd||epoch_millis';
		$jsonData['properties']['FechaFin']['type'] = 'date';
		$jsonData['properties']['FechaFin']['format'] = 'yyyy-MM-dd HH:mm:ss||yyyy-MM-dd||epoch_millis';
		$jsonData['properties']['FechaRevision']['type'] = 'date';
		$jsonData['properties']['FechaRevision']['format'] = 'yyyy-MM-dd HH:mm:ss||yyyy-MM-dd||epoch_millis';
		$jsonData['properties']['FechaModificacion']['type'] = 'date';
		$jsonData['properties']['FechaModificacion']['format'] = 'yyyy-MM-dd HH:mm:ss||yyyy-MM-dd||epoch_millis';

		$jsonData['properties']['Doc']['type'] = 'keyword';
		$jsonData['properties']['Aux']['type'] = 'keyword';
		$jsonData['properties']['DuracionLicencia']['type'] = 'integer';
		$jsonData['properties']['StatusLicencia']['type'] = 'keyword';
		$jsonData['properties']['StatusTarea']['type'] = 'keyword';
		$jsonData['properties']['Familia']['type'] = 'boolean';
		$jsonData['properties']['Adecuacion']['type'] = 'boolean';
		
		$jsonData['properties']['Establecimientos']['type'] = 'nested';
		$jsonData['properties']['Establecimientos']['properties']['ClaveEscuela']['type'] = 'keyword';

		$jsonData['properties']['CuilUsuarioModificacion']['type'] = 'keyword';
		$jsonData['properties']['CuilUsuarioOriginador']['type'] = 'keyword';
		$jsonData['properties']['CuilUsuarioReview']['type'] = 'keyword';
		$jsonData['properties']['IdEstadoProceso']['type'] = 'keyword';
        $jsonData['properties']['Creado']['type'] = "date";
        $jsonData['properties']['Creado']['format'] = "yyyy-MM-dd HH:mm:ss.SSS||epoch_millis";
        $jsonData['properties']['Actualizado']['type'] = "date";
        $jsonData['properties']['Actualizado']['format'] = "yyyy-MM-dd HH:mm:ss.SSS||epoch_millis";

		
		return json_encode($jsonData);
	}
	
	
	private function _EstructuraEstasit($returnJson=true) {
		$jsonData = new stdClass();
		$jsonData->dynamic = 'strict';
		$jsonData->properties = new stdClass();
		$jsonData->properties->IDCargoPagado = new stdClass();
		$jsonData->properties->IDCargoPagado->type = 'keyword';
		$jsonData->properties->Documento = new stdClass();
		$jsonData->properties->Documento->type = 'keyword';
		$jsonData->properties->Secuencia = new stdClass();
		$jsonData->properties->Secuencia->type = 'keyword';
		$jsonData->properties->Nombre = new stdClass();
		$jsonData->properties->Nombre->type = 'text';
		$jsonData->properties->Estado = new stdClass();
		$jsonData->properties->Estado->type = 'text';
		$jsonData->properties->Revista = new stdClass();
		$jsonData->properties->Revista->type = 'text';
		$jsonData->properties->Reg_Est = new stdClass();
		$jsonData->properties->Reg_Est->type = 'text';
		$jsonData->properties->Grupo_Ocup = new stdClass();
		$jsonData->properties->Grupo_Ocup->type = 'text';
		$jsonData->properties->Sub_Grupo_Ocup = new stdClass();
		$jsonData->properties->Sub_Grupo_Ocup->type = 'text';
		$jsonData->properties->Cod_Encasillamiento = new stdClass();
		$jsonData->properties->Cod_Encasillamiento->type = 'text';
		$jsonData->properties->Toma_Posesion_A = new stdClass();
		$jsonData->properties->Toma_Posesion_A->type = 'keyword';
		$jsonData->properties->Toma_Posesion_M = new stdClass();
		$jsonData->properties->Toma_Posesion_M->type = 'keyword';
		$jsonData->properties->Toma_Posesion_D = new stdClass();
		$jsonData->properties->Toma_Posesion_D->type = 'keyword';
		$jsonData->properties->Cod_Cambio = new stdClass();
		$jsonData->properties->Cod_Cambio->type = 'text';
		$jsonData->properties->Fecha_Desde_A = new stdClass();
		$jsonData->properties->Fecha_Desde_A->type = 'keyword';
		$jsonData->properties->Fecha_Desde_M = new stdClass();
		$jsonData->properties->Fecha_Desde_M->type = 'keyword';
		$jsonData->properties->Fecha_Desde_D = new stdClass();
		$jsonData->properties->Fecha_Desde_D->type = 'keyword';
		$jsonData->properties->Fecha_Hasta_A = new stdClass();
		$jsonData->properties->Fecha_Hasta_A->type = 'keyword';
		$jsonData->properties->Fecha_Hasta_M = new stdClass();
		$jsonData->properties->Fecha_Hasta_M->type = 'keyword';
		$jsonData->properties->Fecha_Hasta_D = new stdClass();
		$jsonData->properties->Fecha_Hasta_D->type = 'keyword';
		$jsonData->properties->Item = new stdClass();
		$jsonData->properties->Item->type = 'keyword';
		$jsonData->properties->Apartado = new stdClass();
		$jsonData->properties->Apartado->type = 'keyword';
		$jsonData->properties->Dependencia = new stdClass();
		$jsonData->properties->Dependencia->type = 'keyword';
		$jsonData->properties->Distrito = new stdClass();
		$jsonData->properties->Distrito->type = 'keyword';
		$jsonData->properties->Tipo_Org = new stdClass();
		$jsonData->properties->Tipo_Org->type = 'text';
		$jsonData->properties->Escuela = new stdClass();
		$jsonData->properties->Escuela->type = 'keyword';
		$jsonData->properties->Orga_Apoyo = new stdClass();
		$jsonData->properties->Orga_Apoyo->type = 'text';
		$jsonData->properties->Ensenanza = new stdClass();
		$jsonData->properties->Ensenanza->type = 'text';
		$jsonData->properties->Cargo_Categoria = new stdClass();
		$jsonData->properties->Cargo_Categoria->type = 'text';
		$jsonData->properties->Horas = new stdClass();
		$jsonData->properties->Horas->type = 'keyword';
		$jsonData->properties->Reg_Horario = new stdClass();
		$jsonData->properties->Reg_Horario->type = 'keyword';
		$jsonData->properties->Turno = new stdClass();
		$jsonData->properties->Turno->type = 'text';
		$jsonData->properties->Secc_Establec = new stdClass();
		$jsonData->properties->Secc_Establec->type = 'keyword';
		$jsonData->properties->Turno_Estab = new stdClass();
		$jsonData->properties->Turno_Estab->type = 'keyword';
		$jsonData->properties->Ruralidad_Estab = new stdClass();
		$jsonData->properties->Ruralidad_Estab->type = 'keyword';
		$jsonData->properties->Bonif_Carcel = new stdClass();
		$jsonData->properties->Bonif_Carcel->type = 'keyword';
		$jsonData->properties->Procedencia = new stdClass();
		$jsonData->properties->Procedencia->type = 'keyword';
		$jsonData->properties->Marca_Transfer = new stdClass();
		$jsonData->properties->Marca_Transfer->type = 'text';
		$jsonData->properties->Marca_EGB = new stdClass();
		$jsonData->properties->Marca_EGB->type = 'text';
		$jsonData->properties->Grupo = new stdClass();
		$jsonData->properties->Grupo->type = 'keyword';
		$jsonData->properties->Marca_Adicional = new stdClass();
		$jsonData->properties->Marca_Adicional->type = 'text';
		$jsonData->properties->Garantia = new stdClass();
		$jsonData->properties->Garantia->type = 'text';
		$jsonData->properties->Subvencion = new stdClass();
		$jsonData->properties->Subvencion->type = 'keyword';
		$jsonData->properties->Subsidio = new stdClass();
		$jsonData->properties->Subsidio->type = 'keyword';
		$jsonData->properties->Convenio = new stdClass();
		$jsonData->properties->Convenio->type = 'keyword';
		$jsonData->properties->Marca_Car_Hor_Mod = new stdClass();
		$jsonData->properties->Marca_Car_Hor_Mod->type = 'text';
		$jsonData->properties->Toma_Posesion_A_Int = new stdClass();
		$jsonData->properties->Toma_Posesion_A_Int->type = 'keyword';
		$jsonData->properties->Toma_Posesion_M_Int = new stdClass();
		$jsonData->properties->Toma_Posesion_M_Int->type = 'keyword';
		$jsonData->properties->Toma_Posesion_D_Int = new stdClass();
		$jsonData->properties->Toma_Posesion_D_Int->type = 'keyword';
		$jsonData->properties->Cod_Cambio_Int = new stdClass();
		$jsonData->properties->Cod_Cambio_Int->type = 'text';
		$jsonData->properties->Fecha_Desde_A_Int = new stdClass();
		$jsonData->properties->Fecha_Desde_A_Int->type = 'keyword';
		$jsonData->properties->Fecha_Desde_M_Int = new stdClass();
		$jsonData->properties->Fecha_Desde_M_Int->type = 'keyword';
		$jsonData->properties->Fecha_Desde_D_Int = new stdClass();
		$jsonData->properties->Fecha_Desde_D_Int->type = 'keyword';
		$jsonData->properties->Fecha_Hasta_A_Int = new stdClass();
		$jsonData->properties->Fecha_Hasta_A_Int->type = 'keyword';
		$jsonData->properties->Fecha_Hasta_M_Int = new stdClass();
		$jsonData->properties->Fecha_Hasta_M_Int->type = 'keyword';
		$jsonData->properties->Fecha_Hasta_D_Int = new stdClass();
		$jsonData->properties->Fecha_Hasta_D_Int->type = 'keyword';
		$jsonData->properties->Item_Int = new stdClass();
		$jsonData->properties->Item_Int->type = 'keyword';
		$jsonData->properties->Apartado_Int = new stdClass();
		$jsonData->properties->Apartado_Int->type = 'keyword';
		$jsonData->properties->Dependencia_Int = new stdClass();
		$jsonData->properties->Dependencia_Int->type = 'text';
		$jsonData->properties->Distrito_Int = new stdClass();
		$jsonData->properties->Distrito_Int->type = 'text';
		$jsonData->properties->Tipo_Org_Int = new stdClass();
		$jsonData->properties->Tipo_Org_Int->type = 'text';
		$jsonData->properties->Escuela_Int = new stdClass();
		$jsonData->properties->Escuela_Int->type = 'text';
		$jsonData->properties->Orga_Apoyo_Int = new stdClass();
		$jsonData->properties->Orga_Apoyo_Int->type = 'text';
		$jsonData->properties->Ensenanza_Int = new stdClass();
		$jsonData->properties->Ensenanza_Int->type = 'text';
		$jsonData->properties->Cargo_Categoria_Int = new stdClass();
		$jsonData->properties->Cargo_Categoria_Int->type = 'text';
		$jsonData->properties->Horas_Int = new stdClass();
		$jsonData->properties->Horas_Int->type = 'keyword';
		$jsonData->properties->Reg_Horario_Int = new stdClass();
		$jsonData->properties->Reg_Horario_Int->type = 'keyword';
		$jsonData->properties->Turno_Int = new stdClass();
		$jsonData->properties->Turno_Int->type = 'text';
		$jsonData->properties->Secc_Establec_Int = new stdClass();
		$jsonData->properties->Secc_Establec_Int->type = 'keyword';
		$jsonData->properties->Turno_Estab_Int = new stdClass();
		$jsonData->properties->Turno_Estab_Int->type = 'keyword';
		$jsonData->properties->Ruralidad_Estab_Int = new stdClass();
		$jsonData->properties->Ruralidad_Estab_Int->type = 'keyword';
		$jsonData->properties->Bonif_Carcel_Int = new stdClass();
		$jsonData->properties->Bonif_Carcel_Int->type = 'keyword';
		$jsonData->properties->Subsidio_Int = new stdClass();
		$jsonData->properties->Subsidio_Int->type = 'keyword';
		$jsonData->properties->Marca_Car_Hor_Mod_Int = new stdClass();
		$jsonData->properties->Marca_Car_Hor_Mod_Int->type = 'text';
		$jsonData->properties->Item_Imp = new stdClass();
		$jsonData->properties->Item_Imp->type = 'keyword';
		$jsonData->properties->Apartado_Imp = new stdClass();
		$jsonData->properties->Apartado_Imp->type = 'keyword';
		$jsonData->properties->Dependencia_Imp = new stdClass();
		$jsonData->properties->Dependencia_Imp->type = 'keyword';
		$jsonData->properties->Distrito_Imp = new stdClass();
		$jsonData->properties->Distrito_Imp->type = 'keyword';
		$jsonData->properties->Tipo_Org_Imp = new stdClass();
		$jsonData->properties->Tipo_Org_Imp->type = 'text';
		$jsonData->properties->Escuela_Imp = new stdClass();
		$jsonData->properties->Escuela_Imp->type = 'keyword';
		$jsonData->properties->Orga_Apoyo_Imp = new stdClass();
		$jsonData->properties->Orga_Apoyo_Imp->type = 'text';
		$jsonData->properties->Ensenanza_Imp = new stdClass();
		$jsonData->properties->Ensenanza_Imp->type = 'text';
		$jsonData->properties->Cargo_Categoria_Imp = new stdClass();
		$jsonData->properties->Cargo_Categoria_Imp->type = 'text';
		$jsonData->properties->Horas_Imp = new stdClass();
		$jsonData->properties->Horas_Imp->type = 'keyword';
		$jsonData->properties->Reg_Horario_Imp = new stdClass();
		$jsonData->properties->Reg_Horario_Imp->type = 'keyword';
		$jsonData->properties->Marca_Car_Hor_Mod_Imp = new stdClass();
		$jsonData->properties->Marca_Car_Hor_Mod_Imp->type = 'text';
		$jsonData->properties->Dependencia_Art = new stdClass();
		$jsonData->properties->Dependencia_Art->type = 'text';
		$jsonData->properties->Distrito_Art = new stdClass();
		$jsonData->properties->Distrito_Art->type = 'text';
		$jsonData->properties->Tipo_Org_Art = new stdClass();
		$jsonData->properties->Tipo_Org_Art->type = 'text';
		$jsonData->properties->Escuela_Art = new stdClass();
		$jsonData->properties->Escuela_Art->type = 'text';
		$jsonData->properties->Ruralidad_Estab_Art = new stdClass();
		$jsonData->properties->Ruralidad_Estab_Art->type = 'keyword';
		$jsonData->properties->Subvencion_Art = new stdClass();
		$jsonData->properties->Subvencion_Art->type = 'keyword';
		$jsonData->properties->Sexo = new stdClass();
		$jsonData->properties->Sexo->type = 'text';
		$jsonData->properties->Fecha_Nac_A = new stdClass();
		$jsonData->properties->Fecha_Nac_A->type = 'keyword';
		$jsonData->properties->Fecha_Nac_M = new stdClass();
		$jsonData->properties->Fecha_Nac_M->type = 'keyword';
		$jsonData->properties->Fecha_Nac_D = new stdClass();
		$jsonData->properties->Fecha_Nac_D->type = 'keyword';
		$jsonData->properties->Antig_Docente_A = new stdClass();
		$jsonData->properties->Antig_Docente_A->type = 'keyword';
		$jsonData->properties->Antig_Docente_M = new stdClass();
		$jsonData->properties->Antig_Docente_M->type = 'keyword';
		$jsonData->properties->Antig_Docente_D = new stdClass();
		$jsonData->properties->Antig_Docente_D->type = 'keyword';
		$jsonData->properties->Antig_Admin_A = new stdClass();
		$jsonData->properties->Antig_Admin_A->type = 'keyword';
		$jsonData->properties->Antig_Admin_M = new stdClass();
		$jsonData->properties->Antig_Admin_M->type = 'keyword';
		$jsonData->properties->Antig_Admin_D = new stdClass();
		$jsonData->properties->Antig_Admin_D->type = 'keyword';
		$jsonData->properties->Antig_Admin_Congelada_A = new stdClass();
		$jsonData->properties->Antig_Admin_Congelada_A->type = 'keyword';
		$jsonData->properties->Antig_Admin_Congelada_M = new stdClass();
		$jsonData->properties->Antig_Admin_Congelada_M->type = 'keyword';
		$jsonData->properties->Antig_Admin_Congelada_D = new stdClass();
		$jsonData->properties->Antig_Admin_Congelada_D->type = 'keyword';
		$jsonData->properties->Marca_Prim_Sec_Admin = new stdClass();
		$jsonData->properties->Marca_Prim_Sec_Admin->type = 'text';
		$jsonData->properties->Oficina = new stdClass();
		$jsonData->properties->Oficina->type = 'keyword';
		$jsonData->properties->Asistencia = new stdClass();
		$jsonData->properties->Asistencia->type = 'keyword';
		$jsonData->properties->Fecha_Liquidacion_A = new stdClass();
		$jsonData->properties->Fecha_Liquidacion_A->type = 'keyword';
		$jsonData->properties->Fecha_Liquidacion_M = new stdClass();
		$jsonData->properties->Fecha_Liquidacion_M->type = 'keyword';
		$jsonData->properties->GrupoOcup1 = new stdClass();
		$jsonData->properties->GrupoOcup1->type = 'text';
		$jsonData->properties->SubGrupOcup1 = new stdClass();
		$jsonData->properties->SubGrupOcup1->type = 'text';
		$jsonData->properties->AntigCoro = new stdClass();
		$jsonData->properties->AntigCoro->type = 'keyword';
		$jsonData->properties->AntDocAct = new stdClass();
		$jsonData->properties->AntDocAct->type = 'keyword';
		$jsonData->properties->AntAdmAct = new stdClass();
		$jsonData->properties->AntAdmAct->type = 'keyword';
		$jsonData->properties->AntDocNac = new stdClass();
		$jsonData->properties->AntDocNac->type = 'keyword';
		$jsonData->properties->AntDocNacCong = new stdClass();
		$jsonData->properties->AntDocNacCong->type = 'keyword';
		$jsonData->properties->Sucursal = new stdClass();
		$jsonData->properties->Sucursal->type = 'keyword';
		$jsonData->properties->Tit_Int = new stdClass();
		$jsonData->properties->Tit_Int->type = 'text';
		
		$jsonData->properties->Suplentes = new stdClass();
		$jsonData->properties->Suplentes->type = 'nested';
		$jsonData->properties->Suplentes->properties = new stdClass();
		$jsonData->properties->Suplentes->properties->Suple = new stdClass();
		$jsonData->properties->Suplentes->properties->Suple->type = 'keyword';
		$jsonData->properties->Suplentes->properties->Sec = new stdClass();
		$jsonData->properties->Suplentes->properties->Sec->type = 'keyword';
		$jsonData->properties->Suplentes->properties->Car = new stdClass();
		$jsonData->properties->Suplentes->properties->Car->type = 'keyword';
		
		$jsonData->properties->Delegado = new stdClass();
		$jsonData->properties->Delegado->type = 'text';
		$jsonData->properties->TO_Pago = new stdClass();
		$jsonData->properties->TO_Pago->type = 'text';
		$jsonData->properties->TO_Pago_Imp = new stdClass();
		$jsonData->properties->TO_Pago_Imp->type = 'text';
		$jsonData->properties->TCA = new stdClass();
		$jsonData->properties->TCA->type = 'text';
		$jsonData->properties->TSA = new stdClass();
		$jsonData->properties->TSA->type = 'text';
		$jsonData->properties->DL = new stdClass();
		$jsonData->properties->DL->type = 'text';
		$jsonData->properties->PAT = new stdClass();
		$jsonData->properties->PAT->type = 'text';
		$jsonData->properties->Cant_Cargos = new stdClass();
		$jsonData->properties->Cant_Cargos->type = 'keyword';
		$jsonData->properties->Cant_Horas = new stdClass();
		$jsonData->properties->Cant_Horas->type = 'keyword';
		$jsonData->properties->Cant_Modulos = new stdClass();
		$jsonData->properties->Cant_Modulos->type = 'keyword';
		$jsonData->properties->Casos_de_Horas = new stdClass();
		$jsonData->properties->Casos_de_Horas->type = 'keyword';
		$jsonData->properties->Casos_de_Modulos = new stdClass();
		$jsonData->properties->Casos_de_Modulos->type = 'keyword';
		$jsonData->properties->Cant_Cargos_Imp = new stdClass();
		$jsonData->properties->Cant_Cargos_Imp->type = 'keyword';
		$jsonData->properties->Cant_Horas_Imp = new stdClass();
		$jsonData->properties->Cant_Horas_Imp->type = 'keyword';
		$jsonData->properties->Cant_Modulos_Imp = new stdClass();
		$jsonData->properties->Cant_Modulos_Imp->type = 'keyword';
		$jsonData->properties->Casos_de_Horas_Imp = new stdClass();
		$jsonData->properties->Casos_de_Horas_Imp->type = 'keyword';
		$jsonData->properties->Casos_de_Modulos_Imp = new stdClass();
		$jsonData->properties->Casos_de_Modulos_Imp->type = 'keyword';
		$jsonData->properties->Casos = new stdClass();
		$jsonData->properties->Casos->type = 'keyword';
		$jsonData->properties->Importacion = new stdClass();
		$jsonData->properties->Importacion->type = 'object';
		$jsonData->properties->Anio = new stdClass();
		$jsonData->properties->Anio->type = 'keyword';
		$jsonData->properties->Casos = new stdClass();
		$jsonData->properties->Casos->type = 'keyword';
		
		$jsonData->properties->Importacion = new stdClass();
		$jsonData->properties->Importacion->type = 'object';
		$jsonData->properties->Importacion->properties = new stdClass();
		$jsonData->properties->Importacion->properties->Anio = new stdClass();
		$jsonData->properties->Importacion->properties->Anio->type = 'short';
		$jsonData->properties->Importacion->properties->Mes = new stdClass();
		$jsonData->properties->Importacion->properties->Mes->type = 'byte';
		$jsonData->properties->Importacion->properties->Fecha = new stdClass();
		$jsonData->properties->Importacion->properties->Fecha->type = 'date';
		$jsonData->properties->Importacion->properties->Fecha->format = 'strict_date';
		
		return $returnJson ? json_encode($jsonData) : $jsonData;
	}
	

	

/*
Numericos:
  Enteros:
  $jsonData['properties']['BBBBBBBBBBBBBBBBB']['type'] = 'byte';      (8 bits)
  
  $jsonData['properties']['SSSSSSSSSSSSSSSSS']['type'] = 'short';    (16 bits)
  
  $jsonData['properties']['iiiiiiiiiiiiiiiii']['type'] = 'integer';  (32 bits)
  
  $jsonData['properties']['lllllllllllllllll']['type'] = 'long';     (64 bits)
  

  Decimales:
  $jsonData['properties']['sssssssssssssssss']['type'] = 'scaled_float'; (recomendada)
  $jsonData['properties']['sssssssssssssssss']['scaling_factor'] = 100;
  
  $jsonData['properties']['hhhhhhhhhhhhhhhhh']['type'] = 'half_float';
  
  $jsonData['properties']['fffffffffffffffff']['type'] = 'float';
  
  $jsonData['properties']['ddddddddddddddddd']['type'] = 'double';
  
Strings:

  $jsonData['properties']['kkkkkkkkkkkkkkkkk']['type'] = 'keyword';
  
  $jsonData['properties']['ttttttttttttttttt']['type'] = 'text';
  $jsonData['properties']['ttttttttttttttttt']['analyzer'] = 'spanish';
  $jsonData['properties']['ttttttttttttttttt']['fields']['raw']['type'] = 'keyword';

Fecha:
  $jsonData['properties']['yyyyyyyyyyyyyyyyy']['type'] = 'date';
  $jsonData['properties']['yyyyyyyyyyyyyyyyy']['format'] = 'yyyy-MM-dd HH:mm:ss||yyyy-MM-dd||epoch_millis';
  $jsonData['properties']['yyyyyyyyyyyyyyyyy']['format'] = 'basic_date'; (equivalente a yyyyMMdd)

Otros:
  $jsonData['properties']['bbbbbbbbbbbbbbbbb']['type'] = 'boolean';
  
  $jsonData['properties']['IIIIIIIIIIIIIIIII']['type'] = 'ip';           (IPv4 o IPv6)
  
  $jsonData['properties']['ggggggggggggggggg']['type'] = 'geo_point';    (latitud y longitud)
     
	$jsonData['properties']['nnnnnnnnnnnnnnnnn']['type'] = 'nested';        (anidados)



$jsonData['properties']['CAMPO']['null_value'] = Contenido del misto tipo que el CAMPO;
*/
}
