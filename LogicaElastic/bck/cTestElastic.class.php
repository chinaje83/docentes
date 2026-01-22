<?php 
//----------------------------------------------------------------------------------------- 
//----------------------------------------------------------------------------------------- 
// Clase con la lógica para el manejo de las noticias relacionadas

class cTestElastic
{


	protected $ch;
	const TYPE = "auditoria_libros";
	
	// Constructor de la clase
	function __construct(){
		$this->ch = curl_init();
    } 
	
	// Destructor de la clase
	function __destruct() {	
    } 	


	public function Actualizar($DataActualizacion)
	{


		$datosEnviar = json_encode($DataActualizacion,1);
		
		$urlBase = ELASTICSERVER."/".INDICE."/".self::TYPE."/".$DataActualizacion['IdActa'];
		$header = array("Content-Type: application/json");
		curl_setopt($this->ch, CURLOPT_SSL_VERIFYPEER, false); 
		curl_setopt($this->ch, CURLOPT_URL, $urlBase);
		curl_setopt($this->ch, CURLOPT_CUSTOMREQUEST, "POST");
		curl_setopt($this->ch, CURLOPT_POSTFIELDS,$datosEnviar);
		curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, 1); 
		
		//execute post
		$result = curl_exec($this->ch);

		return true;
		
	}
	
	
	public function ReporteActas($datos = array())
	{
		$datosEnviar = array();
		$datosEnviar['query'] = array();
		$datosEnviar['query']['bool'] = array();
		$datosEnviar['query']['bool']['filter'] = array();
		$datosEnviar['query']['bool']['filter'][0] = array();
		$datosEnviar['query']['bool']['filter'][0]['term'] = array("Usuario.IdUsuario" => 8);
		$datosEnviar['query']['bool']['filter'][1] = array();
		$datosEnviar['query']['bool']['filter'][1]['term'] = array("IdLibro" => 9933327);
		$datosEnviar['query']['bool']['filter'][2] = array();
		$datosEnviar['query']['bool']['filter'][2]['bool'] = array();
		$datosEnviar['query']['bool']['filter'][2]['bool']['should'] = array();
		$datosEnviar['query']['bool']['filter'][2]['bool']['should'][0] = array();
		$datosEnviar['query']['bool']['filter'][2]['bool']['should'][0]['term'] = array("Accion.IdAccion" => 6);
		$datosEnviar['query']['bool']['filter'][2]['bool']['should'][1] = array();
		$datosEnviar['query']['bool']['filter'][2]['bool']['should'][1]['term'] = array("Accion.IdAccion" => 7);
		
		//$datosEnviar['size'] = 0;
		$datosEnviar['aggs'] = array();
		$datosEnviar['aggs']['reporte_actas'] = array();
		$datosEnviar['aggs']['reporte_actas']['date_histogram']=array();
		$datosEnviar['aggs']['reporte_actas']['date_histogram']['field'] = "Fecha";
		$datosEnviar['aggs']['reporte_actas']['date_histogram']['interval'] = "10m";
		$datosEnviar['aggs']['reporte_actas']['aggs'] = array();
		$datosEnviar['aggs']['reporte_actas']['aggs']['accion'] = array();
		$datosEnviar['aggs']['reporte_actas']['aggs']['accion']['terms'] = array();
		$datosEnviar['aggs']['reporte_actas']['aggs']['accion']['terms'] = array("field" => "Accion.IdAccion");

		$dataEnvio = json_encode($datosEnviar);		
		//echo $dataEnvio."<br/>";

		$urlBase = ELASTICSERVER."/".INDICE."/".self::TYPE."/_search";
		$header = array("Content-Type: application/json");
		curl_setopt($this->ch, CURLOPT_SSL_VERIFYPEER, false); 
		curl_setopt($this->ch, CURLOPT_URL, $urlBase);
		curl_setopt($this->ch, CURLOPT_CUSTOMREQUEST, "POST");
		curl_setopt($this->ch, CURLOPT_POSTFIELDS,$dataEnvio);
		curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, 1); 
		
		//execute post
		$result = curl_exec($this->ch);
		$data = json_decode($result,1);
		return $data;
	}
	
	
	public function ReporteVarios($datos = array())
	{
		/*$datosEnviar = array();
		$datosEnviar['query'] = array();
		$datosEnviar['query']['bool'] = array();
		$datosEnviar['query']['bool']['filter'] = array();
		$datosEnviar['query']['bool']['filter'][0] = array();
		$datosEnviar['query']['bool']['filter'][0]['term'] = array("Usuario.IdUsuario" => 8);
		$datosEnviar['query']['bool']['filter'][1] = array();
		$datosEnviar['query']['bool']['filter'][1]['term'] = array("IdLibro" => 9933327);
		$datosEnviar['query']['bool']['filter'][2] = array();
		$datosEnviar['query']['bool']['filter'][2]['bool'] = array();
		$datosEnviar['query']['bool']['filter'][2]['bool']['should'] = array();
		$datosEnviar['query']['bool']['filter'][2]['bool']['should'][0] = array();
		$datosEnviar['query']['bool']['filter'][2]['bool']['should'][0]['term'] = array("Accion.IdAccion" => 6);
		$datosEnviar['query']['bool']['filter'][2]['bool']['should'][1] = array();
		$datosEnviar['query']['bool']['filter'][2]['bool']['should'][1]['term'] = array("Accion.IdAccion" => 7);
		
		//$datosEnviar['size'] = 0;
		$datosEnviar['aggs'] = array();
		$datosEnviar['aggs']['reporte_actas'] = array();
		$datosEnviar['aggs']['reporte_actas']['date_histogram']=array();
		$datosEnviar['aggs']['reporte_actas']['date_histogram']['field'] = "Fecha";
		$datosEnviar['aggs']['reporte_actas']['date_histogram']['interval'] = "10m";
		$datosEnviar['aggs']['reporte_actas']['aggs'] = array();
		$datosEnviar['aggs']['reporte_actas']['aggs']['accion'] = array();
		$datosEnviar['aggs']['reporte_actas']['aggs']['accion']['terms'] = array();
		$datosEnviar['aggs']['reporte_actas']['aggs']['accion']['terms'] = array("field" => "Accion.IdAccion");
*/
		$dataEnvio = '{
  "query": {
    "bool": {
      "filter": [
        {
          "bool": {
            "should": [
              {
                "term": {
                  "Accion.IdAccion": 6
                }
              },
              {
                "term": {
                  "Accion.IdAccion": 7
                }
              }
            ]
          }
        }
      ]
    }
  },
  "size": 100,
  "aggs": {
    "usuarios": {
      "terms": {
        "field": "Usuario.IdUsuario"
      },
      "aggs": {
        "reporte_actas": {
          "date_histogram": {
            "field": "Fecha",
            "interval": "10m"
          },
          "aggs": {
            "accion": {
              "terms": {
                "field": "Accion.IdAccion"
              }
            }
          }
        }
      }
    }
  }
}';	
		//echo $dataEnvio."<br/>";

		$urlBase = ELASTICSERVER."/".INDICE."/".self::TYPE."/_search";
		$header = array("Content-Type: application/json");
		curl_setopt($this->ch, CURLOPT_SSL_VERIFYPEER, false); 
		curl_setopt($this->ch, CURLOPT_URL, $urlBase);
		curl_setopt($this->ch, CURLOPT_CUSTOMREQUEST, "POST");
		curl_setopt($this->ch, CURLOPT_POSTFIELDS,$dataEnvio);
		curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, 1); 
		
		//execute post
		$result = curl_exec($this->ch);
		$data = json_decode($result,1);
		return $data;
	}
	
	public function ReporteTest($datos = array())
	{
		$datosEnviar = array();
		/*$datosEnviar['query'] = array();
		$datosEnviar['query']['bool'] = array();
		$datosEnviar['query']['bool']['filter'] = array();
		$datosEnviar['query']['bool']['filter'][0] = array();
		$datosEnviar['query']['bool']['filter'][0]['term'] = array("Usuario.IdUsuario" => 8);
		$datosEnviar['query']['bool']['filter'][1] = array();
		$datosEnviar['query']['bool']['filter'][1]['term'] = array("IdLibro" => 9933327);
		$datosEnviar['query']['bool']['filter'][2] = array();
		$datosEnviar['query']['bool']['filter'][2]['bool'] = array();
		$datosEnviar['query']['bool']['filter'][2]['bool']['should'] = array();
		$datosEnviar['query']['bool']['filter'][2]['bool']['should'][0] = array();
		$datosEnviar['query']['bool']['filter'][2]['bool']['should'][0]['term'] = array("Accion.IdAccion" => 6);
		$datosEnviar['query']['bool']['filter'][2]['bool']['should'][1] = array();
		$datosEnviar['query']['bool']['filter'][2]['bool']['should'][1]['term'] = array("Accion.IdAccion" => 7);
		*/
		$datosEnviar['size'] = 0;
		$datosEnviar['aggs'] = array();
		$datosEnviar['aggs']['reporte_actas'] = array();
		$datosEnviar['aggs']['reporte_actas']['date_histogram']=array();
		$datosEnviar['aggs']['reporte_actas']['date_histogram']['field'] = "Fecha";
		$datosEnviar['aggs']['reporte_actas']['date_histogram']['interval'] = "10m";
		$datosEnviar['aggs']['reporte_actas']['aggs'] = array();
		$datosEnviar['aggs']['reporte_actas']['aggs']['accion'] = array();
		$datosEnviar['aggs']['reporte_actas']['aggs']['accion']['terms'] = array();
		$datosEnviar['aggs']['reporte_actas']['aggs']['accion']['terms'] = array("field" => "Accion.IdAccion");

		$dataEnvio = json_encode($datosEnviar);		
		//echo $dataEnvio."<br/>";

		$urlBase = ELASTICSERVER."/".INDICE."/".self::TYPE."/_search";
		$header = array("Content-Type: application/json");
		curl_setopt($this->ch, CURLOPT_SSL_VERIFYPEER, false); 
		curl_setopt($this->ch, CURLOPT_URL, $urlBase);
		curl_setopt($this->ch, CURLOPT_CUSTOMREQUEST, "POST");
		curl_setopt($this->ch, CURLOPT_POSTFIELDS,$dataEnvio);
		curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, 1); 
		
		//execute post
		$result = curl_exec($this->ch);
		$data = json_decode($result,1);
		return $data;
	}
	
	public function LibrosCompletos($datos = array())
	{
		//$$datosEnviar = $this->_queryAll();

		$dataEnvio = $this->_queryAll();		
		//echo $dataEnvio."<br/>";

		$urlBase = ELASTICSERVER."/renaper/libros/_search";
		$header = array("Content-Type: application/json");
		curl_setopt($this->ch, CURLOPT_SSL_VERIFYPEER, false); 
		curl_setopt($this->ch, CURLOPT_URL, $urlBase);
		curl_setopt($this->ch, CURLOPT_CUSTOMREQUEST, "POST");
		curl_setopt($this->ch, CURLOPT_POSTFIELDS,$dataEnvio);
		curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, 1); 
		
		//execute post
		$result = curl_exec($this->ch);
		$data = json_decode($result,1);
		//print_r($data);
		return $data;
	}
	
	
	public function BuscarxCodigoActa($id)
	{
		
		$datosEnviar = array();
		
		$urlBase = ELASTICSERVER."/".INDICE."/".self::TYPE."/".$id;
		$header = array("Content-Type: application/json");
		curl_setopt($this->ch, CURLOPT_SSL_VERIFYPEER, false); 
		curl_setopt($this->ch, CURLOPT_URL, $urlBase);
		curl_setopt($this->ch, CURLOPT_CUSTOMREQUEST, "GET");
		curl_setopt($this->ch, CURLOPT_POSTFIELDS,$datosEnviar);
		curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, 1); 
		
		//execute post
		$result = curl_exec($this->ch);
		$data = json_decode($result,1);
		if ($data['found']===false)
			return false;
		else
			return true;	
		
	}

	

	public function Mapping()
	{
		
		$jsonData['properties']['CodigoBarras']['type'] = "text";
		$jsonData['properties']['IdLibro']['type'] = "integer";
		$jsonData['properties']['IdActa']['type'] = "integer";

		$jsonData['properties']['NumeroActa']['type'] = "text";
		$jsonData['properties']['FechaNacimiento']['type'] = "date";
		$jsonData['properties']['FechaNacimiento']['format'] = "yyyy-MM-dd HH:mm:ss||yyyy-MM-dd||epoch_millis";
		$jsonData['properties']['FechaNacimiento']['null_value'] = "NULL";
	
	
		$jsonData['properties']['Orden']['type'] = "integer";
		
		$jsonData['properties']['Sexo']['type'] = "integer";
		$jsonData['properties']['Sexo']['null_value'] = "NULL";
		
		$jsonData['properties']['Documento']['type'] = "text";
		$jsonData['properties']['Documento']['null_value'] = "NULL";
		
		$jsonData['properties']['Nombre']['type'] = "text";
		$jsonData['properties']['Nombre']['null_value'] = "NULL";
		
		$jsonData['properties']['Apellido']['type'] = "text";
		$jsonData['properties']['Apellido']['null_value'] = "NULL";

		$jsonData['properties']['TipoDocumento']['properties']['Id']['type'] = "integer";
		$jsonData['properties']['TipoDocumento']['properties']['Id']['null_value'] = "NULL";
		$jsonData['properties']['TipoDocumento']['properties']['Nombre']['type'] = "text";
		$jsonData['properties']['TipoDocumento']['properties']['Nombre']['null_value'] = "NULL";

		$jsonData['properties']['TipoActa']['properties']['Id']['type'] = "integer";
		$jsonData['properties']['TipoActa']['properties']['Id']['null_value'] = "NULL";
		$jsonData['properties']['TipoActa']['properties']['Nombre']['type'] = "text";
		$jsonData['properties']['TipoActa']['properties']['Nombre']['null_value'] = "NULL";
		
		$jsonData['properties']['UtilizoSistemaRenaper']['type'] = "integer";
		$jsonData['properties']['UtilizoSistemaRenaper']['null_value'] = "NULL";
		$jsonData['properties']['DatosValidadosRenaper']['type'] = "integer";
		$jsonData['properties']['DatosValidadosRenaper']['null_value'] = "NULL";
		$jsonData['properties']['ExisteEnRenaper']['type'] = "integer";
		$jsonData['properties']['ExisteEnRenaper']['null_value'] = "NULL";

		$jsonData['properties']['UtilizoSistemaRenaperAutomatico']['type'] = "integer";
		
		$jsonData['properties']['Estado']['properties']['Id']['type'] = "integer";
		$jsonData['properties']['Estado']['properties']['Nombre']['type'] = "text";
		
		$jsonData['properties']['AltaFecha']['type'] = "date";
		$jsonData['properties']['AltaFecha']['format'] = "yyyy-MM-dd HH:mm:ss||yyyy-MM-dd||epoch_millis";

		$jsonData['properties']['UltimaModificacionUsuario']['type'] = "integer";
		$jsonData['properties']['UltimaModificacionFecha']['type'] = "date";
		$jsonData['properties']['UltimaModificacionFecha']['format'] = "yyyy-MM-dd HH:mm:ss||yyyy-MM-dd||epoch_millis";
	


		$datosEnviar = json_encode($jsonData);
		$urlBase = ELASTICSERVER."/".INDICE."/_mapping/".self::TYPE;
		$header = array("Content-Type: application/json");
		curl_setopt($this->ch, CURLOPT_SSL_VERIFYPEER, false); 
		curl_setopt($this->ch, CURLOPT_URL, $urlBase);
		curl_setopt($this->ch, CURLOPT_CUSTOMREQUEST, "PUT");
		curl_setopt($this->ch, CURLOPT_POSTFIELDS,$datosEnviar);
		curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, 1); 
		
		//execute post
		$result = curl_exec($this->ch);
		print_r($result);
		$data = json_decode($result,1);
		if ($data['acknowledged']===false)
			return false;
		else
			return true;	
	}
	
	

	

	private  function _queryAll()
	{
		$datosEnviar = array();
		$datosEnviar['query'] = array();
		$datosEnviar['query']['match_all'] = new stdClass();
		$dataEnvio = json_encode($datosEnviar);
		
		return $dataEnvio;
	}
		

	
}//FIN CLASE

?>