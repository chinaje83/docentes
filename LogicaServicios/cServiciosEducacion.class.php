<?php 
class cServiciosEducacion 
{

	protected $oCurl;
	protected $conexion;
	protected $error;
	protected $Utf8;
	protected $cargarCargosTemporales;
	protected $cargarCargosInactivos;
	
	protected $MemCache;
	protected $arrayCargosTmp;
	protected $arrayCargosInactivos;

	const MemCacheExpire = 86400;// 1 dia
	
	
	function __construct($conexion){
		$this->conexion = &$conexion;
 		$this->error = array();
		$this->oCurl = new CurlBigtree($this->conexion);
		$this->Utf8 = false;
		$this->cargarCargosTemporales = true;
		$this->cargarCargosInactivos = true;
		$this->CargosDefault();
		if (MEMCACHED)
		{
			if (MEMCACHEDLIBRARY=="memcache")
				$this->MemCache = new memcache();
			else
				$this->MemCache = new memcached();
			
			$this->MemCache->addServer(MEMCACHEDSERVER, MEMCACHEDPORT);		

			//$this->MemCache->flush();
		}
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


	public function CargosDefault()
	{
		$this->arrayCargosTmp = array();
		$array = explode(",",MOVIMIENTOSTMPHABILITADOS);
		foreach($array as $data)
			$this->AgregarCodigoCambio($data);
		
		$this->arrayCargosInactivos = array();
		$array = explode(",",MOVIMIENTOSINACTIVOSHABILITADOS);
		foreach($array as $data)
			$this->AgregarCodigoCambioInac($data);
		
		
	}
	
	public function CodificarUtf8()
	{
		$this->Utf8 = true;
	}
	
	
	public function getPersonaxCuil($cuil)
	{
		$url = $cuil;
		$urlAnexa = "";
		$header = array();
		$this->oCurl->setUrl(WS_EDUCACION_HOST_PERSONA2);
		$fields_string = "";
		$this->oCurl->setFunction(get_class($this)."-".__FUNCTION__);
		//$this->oCurl->setDebug(true);
		/*echo WS_EDUCACION_HOST_PERSONA2.$url; die;*/
		if(!$this->oCurl->sendGet($url,$dataResult))
		{	
			$this->setError("Error","Error, ocurrio un error al buscar los datos de persona por CUIL");
			return false;
		}
		if (!$this->Utf8)
			$array = FuncionesPHPLocal::DecodificarUtf8($dataResult);
		else
			$array = $dataResult;

		if (isset($array['success']) && $array['success']==true)
		{
			if (isset($array['persona']['fechanacimiento']) && $array['persona']['fechanacimiento']!="")
				$array['persona']['fechanacimientoFormateada'] = FuncionesPHPLocal::ConvertirFecha($array['persona']['fechanacimiento'],"aaaa-mm-dd","dd/mm/aaaa");
		}	
		//print_r($array);die;
		return $array;
	}
	
	public function getPersonaxCuilCompleto($cuil)
	{
		$url = $cuil;
		$urlAnexa = "";
		$header = array();
		$this->oCurl->setUrl(WS_EDUCACION_HOST_PERSONA);
		$fields_string = "";
		$this->oCurl->setFunction(get_class($this)."-".__FUNCTION__);

		//$this->oCurl->setDebug(true);
		//if (SITIOPRODUCTIVO<3)
			$this->oCurl->setUtf8(true);
		
		if(!$this->oCurl->sendGet($url,$dataResult))
		{	
			$this->setError("Error","Error, ocurrio un error al buscar los datos de persona por CUIL");
			return false;
		}
		$array = $dataResult;
		if (isset($array[0]))
			return $array[0];
		else 
			return false;
	}

	
	public function NoCargarTemporales(){$this->cargarCargosTemporales = false;}
	public function CargarTemporales(){$this->cargarCargosTemporales = false;}
		
	public function NoCargarCargosInactivos(){$this->cargarCargosInactivos = false;}
	public function CargarCargosInactivos(){$this->cargarCargosInactivos = false;}
		
	public function AgregarCodigoCambio($codigo){$this->arrayCargosTmp[$codigo] = $codigo;}
	public function EliminarCodigoCambio($codigo){$this->arrayCargosTmp[$codigo] = $codigo;}
		
	public function AgregarCodigoCambioInac($codigo){$this->arrayCargosInactivos[$codigo] = $codigo;}
	public function EliminarCodigoCambioInac($codigo){$this->arrayCargosInactivos[$codigo] = $codigo;}
		

	
	public function getCargosxCuil($cuil,$ClaveEscuelaDatos="")
	{
		$url = "getCargosXCUIL/".$cuil;
		$urlAnexa = "";
		$header = array();
		$this->oCurl->setUrl(WS_EDUCACION_HOST);
		$this->oCurl->setFunction(get_class($this)."-".__FUNCTION__);
		$fields_string = "";
		//$this->oCurl->setDebug(true);
		if(!$this->oCurl->sendGet($url,$dataResult))
		{	
			$this->setError("Error","Error, ocurrio un error al buscar los cargos por CUIL");
			return false;
		}
		if (!$this->Utf8)
			$array = FuncionesPHPLocal::DecodificarUtf8($dataResult);
		else
			$array = $dataResult;
		
		$arrayDevolver = $array;
		$arrayDevolver['cargos'] = array();
		if (isset($array['cargos']))
		{	
			foreach($array['cargos'] as $cargo)
			{
				$cargo['origen'] = "CARGOS";
				$cargo['RevistaVisualizacion'] = $cargo['caractRevOut'];
				if (strtoupper($cargo['realIntOut'])=="INT")
				{	
					$cargo['RevistaVisualizacion'] = "";
				}
				$hsSubsecuencia=$cargo["hsDesigDetalle"];
				if ($cargo["hsDesigDetalle"]==0 && $cargo["hsDesig"]>0)
					$hsSubsecuencia=$cargo["hsDesig"];
				$cargo['hsDesigDetalle'] = $hsSubsecuencia;
				$cargo['codigoCambio'] = "";
				$arrayDevolver['cargos'][] = $cargo;
			}
		
		}
		if ($this->cargarCargosTemporales)
		{
			
			$arrayData = $this->getCargosMovimientosxCuil($cuil);
			foreach($arrayData as $data)
			{
				if (array_key_exists(trim($data['codigoCambio']),$this->arrayCargosTmp))
				{
					$dataInsert['origen'] = "CARGOTMP";
					$dataInsert['codigoCambio'] = trim($data['codigoCambio']);
					$dataInsert['secuenciaOut']=intval($data['secuenciaOut']);
					$dataInsert['subsecuenciaOut']=($data['subscuenciaOut']==""?0:intval($data['subscuenciaOut']));
					$dataInsert['hsDesigDetalle']=$data['hsDesigDetalle'];
					$dataInsert['realIntOut']=($data['realIntOut']=="R"?"REAL":"INT");
					$dataInsert['caractRevOut']=$dataInsert['RevistaVisualizacion']=$data['caractRevOut'];
					$dataInsert['numeroDistrito']="";
					$dataInsert['numeroEstablecimiento']="";
					$dataInsert['ModalidadCarrera'] = $data['carrera'];
					$dataInsert['claveEstab']=$data['estab'];
					$dataInsert['cargoCodigo']=$data['cargo'];
					$dataInsert['cargoDesc']="";
					$dataInsert['cargoEnse']="";
					$dataInsert['cargoHsMod']="";
					$dataInsert['anio']=intval($data['anio']);
					$dataInsert['letraSeccion']=$data['seccion'];
					$dataInsert['asignatura']="";
					$dataInsert['area']=$data['area'];
					$dataInsert['hsDesig'] = $dataInsert['hsDesigDetalle'] = $data['hsDesigDetalle'];
					$dataInsert['regHorario']="";
					$dataInsert['turno']=$data['turno'];
					$dataInsert['regEstatCodigo']=$data['regEstat'];
					$dataInsert['regEstatDesc']="";
					$dataInsert['subGrupoCodigo']=$data['subGrupoCodigo'];
					$dataInsert['subGrupoDesc']=$data['subGrupoCodigo'];
					$dataInsert['grupoCodigo']=$data['codigoGrupo'];
					$dataInsert['grupoDesc']=$data['codigoGrupo'];
					$arrayDevolver['cargos'][]=$dataInsert;
				}
				
			}
		}
		
		if ($ClaveEscuelaDatos!="" && $this->cargarCargosInactivos==true)
		{
			
			$array = $this->getCargosInactivos($ClaveEscuelaDatos,$cuil);
			
			if (isset($array['cargos']))
			{	
				foreach($array['cargos'] as $cargo)
				{
					if (array_key_exists(trim($cargo['codigoCambio']),$this->arrayCargosInactivos))
					{
						$cargo['origen'] = "CARGOINACT";
						$cargo['RevistaVisualizacion'] = $cargo['caractRevOut'];
						if (strtoupper($cargo['realIntOut'])=="INT")
							$cargo['RevistaVisualizacion'] = "";

						$hsSubsecuencia=$cargo["hsDesigDetalle"];
						if ($cargo["hsDesigDetalle"]==0 && $cargo["hsDesig"]>0)
							$hsSubsecuencia=$cargo["hsDesig"];
						$cargo['hsDesigDetalle'] = $hsSubsecuencia;
						$cargo['codigoCambio'] = trim($cargo['codigoCambio']);
						$arrayDevolver['cargos'][] = $cargo;
					}
				}

			}
			
		}
		
		$arrayDevolver['cargos'] = $this->array_orderby($arrayDevolver['cargos'], 'secuenciaOut', SORT_ASC, 'subsecuenciaOut', SORT_ASC);
		
		
		
		return $arrayDevolver;
	}

	
	private function array_orderby()
	{
		$args = func_get_args();
		$data = array_shift($args);
		foreach ($args as $n => $field) {
			if (is_string($field)) {
				$tmp = array();
				foreach ($data as $key => $row)
					$tmp[$key] = $row[$field];
				$args[$n] = $tmp;
				}
		}
		$args[] = &$data;
		call_user_func_array('array_multisort', $args);
		return array_pop($args);
	}	


    public function getCargosInactivos($escuela,$cuil)
    {
        $url = "?clave=".$escuela."&cuil=".$cuil;
        $urlAnexa = "";
        $header = array();
        $this->oCurl->setUrl(WS_EDUCACION_INACTIVOS);
        $this->oCurl->setFunction(get_class($this)."-".__FUNCTION__);
        $fields_string = "";
        //$this->oCurl->setDebug(true);
        if(!$this->oCurl->sendGet($url,$dataResult))
        {
            $this->setError("Error","Error, ocurrio un error al buscar los cargos licenciados o relevados por escuela y CUIL");
            return false;
        }
        if (!$this->Utf8)
            $array = FuncionesPHPLocal::DecodificarUtf8($dataResult);
        else
            $array = $dataResult;

        //print_r($array);die;
        return $array;
    }
	
	
    public function getCargosMovimientosxCuil($cuil)
    {
        $url = $cuil;
        $urlAnexa = "";
        $header = array();
        $this->oCurl->setUrl(WS_EDUCACION_MOVIMIENTOS);
        $this->oCurl->setFunction(get_class($this)."-".__FUNCTION__);
        $fields_string = "";
        //$this->oCurl->setDebug(true);
        if(!$this->oCurl->sendGet($url,$dataResult))
        {
            $this->setError("Error","Error, ocurrio un error al buscar los cargos movimientos por CUIL");
            return false;
        }
        if (!$this->Utf8)
            $array = FuncionesPHPLocal::DecodificarUtf8($dataResult);
        else
            $array = $dataResult;

        //print_r($array);die;
        return $array;
    }
	
	
	public function getEstablecimientoxClave($clave)
	{
		
		if (MEMCACHED)
		{
			$response = $this->MemCache->get("establecimiento/clave/".$clave);
			if ($response)
				return $response;
		}
		//$url = "estabs/gral/clave/".$clave;
		$url = $clave;
		$urlAnexa = "";
		$header = array();
		$this->oCurl->setUrl(WS_EDUCACION_ESTABLECIMIENTO_HOST);
		$this->oCurl->setFunction(get_class($this)."-".__FUNCTION__);
		$fields_string = "";
		//echo WS_EDUCACION_ESTABLECIMIENTO_HOST.$url;
		//$this->oCurl->setDebug(true);
		if(!$this->oCurl->sendGet($url,$dataResult))
		{	
			$this->setError("Error","Error, ocurrio un error al buscar la escuela por clave");
			return false;
		}
		if (!$this->Utf8)
			$array = FuncionesPHPLocal::DecodificarUtf8($dataResult);
		else
			$array = $dataResult;

		if (MEMCACHED)
		{
			$this->SetMemcache("establecimiento/clave/".$clave,$array);
		}	
		//print_r($array);die;
		return $array;
	}

	
	public function getAgrupamientoEscuelas($claveEscuela)
	{
		$url = $claveEscuela;
		$urlAnexa = "";
		$header = array();
		$this->oCurl->setUrl(WS_EDUCACION_AGRUPAMIENTO_ESCUELAS);
		$fields_string = "";
		$this->oCurl->setFunction(get_class($this)."-".__FUNCTION__);
		//$this->oCurl->setDebug(true);
		/*echo WS_EDUCACION_HOST_PERSONA2.$url; die;*/
		if(!$this->oCurl->sendGet($url,$dataResult))
		{	
			$this->setError("Error","Error, ocurrio un error al buscar los datos de persona por CUIL");
			return false;
		}
		if (!$this->Utf8)
			$array = FuncionesPHPLocal::DecodificarUtf8($dataResult);
		else
			$array = $dataResult;

		//print_r($array);die;
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
	
	private function SetMemcache($key,$data)
	{
		if (MEMCACHED)
			$this->MemCache->set($key, $data, MEMCACHE_COMPRESSED, self::MemCacheExpire);
	}
	
	
	
}//FIN CLASE

?>