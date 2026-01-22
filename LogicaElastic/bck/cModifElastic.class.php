<?php 
//----------------------------------------------------------------------------------------- 
//----------------------------------------------------------------------------------------- 
// Clase genérica con la lógica para la insercion y modificacion de datos en elastic

class cModifElastic
{

	protected $indexsuffix;
	protected $ch;
	
	// Constructor de la clase
	function __construct($indexsuffix){
		$this->indexsuffix = $indexsuffix;
		$this->ch = curl_init();
    } 

	
	// Destructor de la clase
	function __destruct() {	
		curl_close($this->ch);
    }
	
	public function Actualizar($datos,$DataActualizacion)
	{
		$Id = $this->_ObtenerId($datos);
		$ExisteId = $this->_BuscarxCodigo($Id,$datosRegistro);
		$datosEnviar = "{}";
		if (!$ExisteId)
		{
			
			//mensaje de error
			if(!$this->Insertar($DataActualizacion))
				return false;
			
			return true;
		}
		$datosEnvio['doc'] = $DataActualizacion;
		$datosEnviar = json_encode($datosEnvio);
		//file_put_contents(PUBLICA."actualizar_{$this->indexsuffix}.txt",print_r($DataActualizacion,true)."\n");
		//file_put_contents(PUBLICA."actualizar_{$this->indexsuffix}.json",$datosEnviar."\n");
		
		$urlBase = ELASTICSERVER."/".INDICE.$this->indexsuffix."/".TYPE."/".$Id."/_update";
		$header = array("Content-Type: application/json");
		curl_setopt($this->ch, CURLOPT_HTTPHEADER, $header);
		curl_setopt($this->ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($this->ch, CURLOPT_URL, $urlBase);
		curl_setopt($this->ch, CURLOPT_CUSTOMREQUEST, "POST");
		/*if(defined('ELASTIC_AUTH'))
			curl_setopt($this->ch, CURLOPT_USERPWD, ELASTIC_AUTH);*/
		curl_setopt($this->ch, CURLOPT_POSTFIELDS,$datosEnviar);
		curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, 1);
		if (!UTILIZAPROXY)
		{
			curl_setopt($this->ch, CURLOPT_PROXY, "");
			curl_setopt($this->ch, CURLOPT_PROXYPORT, "");
		}
			$result = curl_exec($this->ch);
			$data = json_decode($result,true);
		//file_put_contents(PUBLICA."actualizar_{$this->indexsuffix}-result.txt",print_r($data,true)."\n");
		//file_put_contents(PUBLICA."actualizar_{$this->indexsuffix}-result.json",$result."\n");
		if(!isset($data['result']) || ($data['result'] != "updated" && $data['result'] != "noop") )
		{
			FuncionesElastic::MostrarError($data);
			return false;
		}
		
		return true;
		
	}

	public function ActualizarConScript($datos,$script)
	{
		$Id = $this->_ObtenerId($datos);
		$ExisteId = $this->_BuscarxCodigo($Id,$datosRegistro);
		$datosEnviar = "{}";
		if (!$ExisteId)
		{
			FuncionesPHPLocal::MostrarMensaje($this->ch, MSG_ERRGRAVE, 'Error al actualizar, el documento no existe', array("archivo" => __FILE__,"funcion" => __FUNCTION__, "linea" => __LINE__), array("formato"=>''));
			return false;
		}
		$datosEnvio['script'] = $script;
		$datosEnviar = json_encode($datosEnvio);
		//file_put_contents(PUBLICA."actualizar_{$this->indexsuffix}.txt",print_r($DataActualizacion,true)."\n");
		//file_put_contents(PUBLICA."actualizar_{$this->indexsuffix}.json",$datosEnviar."\n");

		$urlBase = ELASTICSERVER."/".INDICE.$this->indexsuffix."/".TYPE."/".$Id."/_update";
		$header = array("Content-Type: application/json");
		curl_setopt($this->ch, CURLOPT_HTTPHEADER, $header);
		curl_setopt($this->ch, CURLOPT_SSL_VERIFYPEER, false);
		/*if(defined('ELASTIC_AUTH'))
			curl_setopt($this->ch, CURLOPT_USERPWD, ELASTIC_AUTH);*/
		curl_setopt($this->ch, CURLOPT_URL, $urlBase);
		curl_setopt($this->ch, CURLOPT_CUSTOMREQUEST, "POST");
		curl_setopt($this->ch, CURLOPT_POSTFIELDS,$datosEnviar);
		curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, 1); 
		if (!UTILIZAPROXY)
		{
			$result = curl_exec($this->ch);
			$data = json_decode($result,true);
		}
		//file_put_contents(PUBLICA."actualizar_{$this->indexsuffix}-result.txt",print_r($data,true)."\n");
		//file_put_contents(PUBLICA."actualizar_{$this->indexsuffix}-result.json",$result."\n");
		if(!isset($data['result']) || ($data['result'] != "updated" && $data['result'] != "noop") )
		{
			FuncionesElastic::MostrarError($data);
			return false;
		}
		
		return true;
		
	}

	

	public function Insertar($datos)
	{

		$Id = $this->_ObtenerId($datos);
		if($Id === false)
			$id = "";
		else
			$id = "/".$Id;
	
		$datosEnviar = json_encode($datos);


		
		//file_put_contents(PUBLICA."insertar_{$this->indexsuffix}.txt",print_r($datos,true)."\n");
		//file_put_contents(PUBLICA."insertar_{$this->indexsuffix}.json",$datosEnviar."\n");
		
		$urlBase = ELASTICSERVER."/".INDICE.$this->indexsuffix."/".TYPE.$id;
		$header = array("Content-Type: application/json");
		curl_setopt($this->ch, CURLOPT_HTTPHEADER, $header);
		curl_setopt($this->ch, CURLOPT_SSL_VERIFYPEER, false);
		/*if(defined('ELASTIC_AUTH'))
			curl_setopt($this->ch, CURLOPT_USERPWD, ELASTIC_AUTH);*/
		curl_setopt($this->ch, CURLOPT_URL, $urlBase);
		curl_setopt($this->ch, CURLOPT_CUSTOMREQUEST, "POST");
		curl_setopt($this->ch, CURLOPT_POSTFIELDS,$datosEnviar);
		curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, 1); 
		if (!UTILIZAPROXY)
		{
			curl_setopt($this->ch, CURLOPT_PROXY, PROXY);
			curl_setopt($this->ch, CURLOPT_PROXYPORT, PROXYPORT);
		}
		$result = curl_exec($this->ch);
		$data = json_decode($result,true);
		
		//file_put_contents(PUBLICA."insertar_{$this->indexsuffix}-result.txt",print_r($data,true)."\n");
		//file_put_contents(PUBLICA."insertar_{$this->indexsuffix}-result.json",$result."\n");
		if(!isset($data['result']) || ($data['result'] != "created" && $data['result'] != "updated"  && $data['result']!="noop"))
		{
			FuncionesElastic::MostrarError($data);
			return false;
		}

		return true;
	}


    public function ActualizarBulk($datosEnviarJson)
    {
        $urlBase = ELASTICSERVER."/".INDICE.$this->indexsuffix."/".TYPE."/_bulk";
        $header = array("Content-Type: application/x-ndjson");
        curl_setopt($this->ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($this->ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($this->ch, CURLOPT_URL, $urlBase);
        curl_setopt($this->ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($this->ch, CURLOPT_POSTFIELDS,$datosEnviarJson);
        curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, 1);
        $result = curl_exec($this->ch);
        $data = json_decode($result,true);
        if(isset($data['errors']) && $data['errors'])
        {
            FuncionesElastic::MostrarError($data);
            return false;
        }

        return true;
    }


    public function ActualizarBulkSinIndex($datosEnviarJson)
	{
		$urlBase = ELASTICSERVER.'/_bulk';
		$header = array('Content-Type: application/x-ndjson');
		curl_setopt($this->ch, CURLOPT_HTTPHEADER, $header);
		curl_setopt($this->ch, CURLOPT_SSL_VERIFYPEER, false); 
		curl_setopt($this->ch, CURLOPT_URL, $urlBase);
		curl_setopt($this->ch, CURLOPT_CUSTOMREQUEST, "POST");
		curl_setopt($this->ch, CURLOPT_POSTFIELDS,$datosEnviarJson);
		curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, 1); 
		if (!UTILIZAPROXY)
		{
			curl_setopt($this->ch, CURLOPT_PROXY, PROXY);
			curl_setopt($this->ch, CURLOPT_PROXYPORT, PROXYPORT);
		}
		$result = curl_exec($this->ch);
		$data = json_decode($result,true);
		if(isset($data['errors']) && $data['errors'])
		{
			FuncionesElastic::MostrarError($data);
			return false;
		}
		
		
		return true;
	}


	public function Eliminar($datos)
	{
			
		$Id = $this->_ObtenerId($datos);
		$ExisteId = $this->_BuscarxCodigo($Id,$datosRegistro);
		if(!$ExisteId)
			return true;
		else
			$id = "/".$Id;
		$urlBase = ELASTICSERVER."/".INDICE.$this->indexsuffix."/".TYPE.$id;
		//file_put_contents(PUBLICA."eliminar_{$this->indexsuffix}.txt",$urlBase."\n");
		$header = array("Content-Type: application/json");
		curl_setopt($this->ch, CURLOPT_HTTPHEADER, $header);
		curl_setopt($this->ch, CURLOPT_SSL_VERIFYPEER, false); 
		curl_setopt($this->ch, CURLOPT_URL, $urlBase);
		curl_setopt($this->ch, CURLOPT_CUSTOMREQUEST, "DELETE");
		curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, 1); 
		if (!UTILIZAPROXY)
		{
			$result = curl_exec($this->ch);
			$data = json_decode($result,true);
		}
		if(isset($data['errors']) && $data['errors'])
		{
			FuncionesElastic::MostrarError($data);
			return false;
		}
		return true;
	}


	public function EliminarxQuery($datos,$usarTask=false)
	{
		$datosEnviar = json_encode($datos);
		$urlBase = ELASTICSERVER."/".INDICE.$this->indexsuffix."/".TYPE."/_delete_by_query";
		if($usarTask)
			$urlBase .= '?wait_for_completion=false';
		if(defined('DEBUGELASTIC') && DEBUGELASTIC)
			file_put_contents(PUBLICA."eliminarxquery_{$this->indexsuffix}.json",$datosEnviar."\n");
		$header = array("Content-Type: application/json");
		curl_setopt($this->ch, CURLOPT_HTTPHEADER, $header);
		curl_setopt($this->ch, CURLOPT_SSL_VERIFYPEER, false); 
		curl_setopt($this->ch, CURLOPT_URL, $urlBase);
		curl_setopt($this->ch, CURLOPT_CUSTOMREQUEST, "POST");
		curl_setopt($this->ch, CURLOPT_POSTFIELDS,$datosEnviar);
		curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, 1); 
		$result = curl_exec($this->ch);
		if(defined('DEBUGELASTIC') && DEBUGELASTIC)
			file_put_contents(PUBLICA."eliminarxquery_{$this->indexsuffix}-result.json",$result."\n");
		$data = json_decode($result,true);
		if($usarTask)
		{
			echo $data['task'];
			while(!FuncionesElastic::VerificarTask($data['task'],$this->ch))
				sleep(10);
		}
		if(isset($data['errors']) && $data['errors'])
		{
			FuncionesElastic::MostrarError($data);
			return false;
		}
		
		return true;
	}
	
	private function _ObtenerId($datos)
	{
		switch($this->indexsuffix)
		{
			case INDICESUNA:
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
			break;
			case INDICELICENCIAS:
				if(isset($datos['TaskId']) && $datos['TaskId']!="")
					return $datos['TaskId'];
				else
					return false;
			break;
			case INDICETABLAS:
			default:
				return false;
			break;
		}
	}
	
	private function _BuscarxCodigo($Id,&$datosRegistro)
	{
		
		$datosRegistro = $datosEnviar = array();
		
		$urlBase = ELASTICSERVER."/".INDICE.$this->indexsuffix."/".TYPE."/".$Id;
		$header = array("Content-Type: application/json");
		curl_setopt($this->ch, CURLOPT_HTTPHEADER, $header);
		curl_setopt($this->ch, CURLOPT_SSL_VERIFYPEER, false); 
		curl_setopt($this->ch, CURLOPT_URL, $urlBase);
		curl_setopt($this->ch, CURLOPT_CUSTOMREQUEST, "GET");
		curl_setopt($this->ch, CURLOPT_POSTFIELDS,$datosEnviar);
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
			return false;
		else
		{
			$datosRegistro = $data['_source'];
			return true;	
		}
		
	}
	
	function ActualizarEncuadreAntiguedad($datos)
	{
		$datosModif['UltimaModificacion']['Fecha'] = $datos['UltimaModificacionFecha'];
		$datosModif['Inasistencia']['LicenciaEncuadreArticulo'] = $datos['LicenciaEncuadreArticulo'];
		$datosModif['Periodo']['FechaDesde'] = $datos['PeriodoFechaDesde'];
		$datosModif['Periodo']['FechaHasta'] = $datos['PeriodoFechaHasta'];
		
		$datosEnvio['doc'] = $datosModif;
		$datosEnviar = json_encode($datosEnvio);
		
		//file_put_contents(PUBLICA."actualizar_{$this->indexsuffix}.txt",print_r($DataActualizacion,true)."\n");
		//file_put_contents(PUBLICA."actualizar_{$this->indexsuffix}.json",$datosEnviar."\n");
		$urlBase = ELASTICSERVER."/".INDICE.INDICESUNA."/".TYPE."/".PREFIJODOC.$datos['IdDocumento']."/_update";
		$header = array("Content-Type: application/json");
		curl_setopt($this->ch, CURLOPT_HTTPHEADER, $header);
		curl_setopt($this->ch, CURLOPT_SSL_VERIFYPEER, false); 
		curl_setopt($this->ch, CURLOPT_URL, $urlBase);
		curl_setopt($this->ch, CURLOPT_CUSTOMREQUEST, "POST");
		curl_setopt($this->ch, CURLOPT_POSTFIELDS,$datosEnviar);
		curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, 1); 
		if (!UTILIZAPROXY)
		{
			$result = curl_exec($this->ch);
			$data = json_decode($result,true);
		}
		//file_put_contents(PUBLICA."actualizar_{$this->indexsuffix}-result.txt",print_r($data,true)."\n");
		//file_put_contents(PUBLICA."actualizar_{$this->indexsuffix}-result.json",$result."\n");
		if(!isset($data['result']) || ($data['result'] != "updated" && $data['result'] != "noop") )
		{
			FuncionesElastic::MostrarError($data);
			return false;
		}
		return true;
	}

}