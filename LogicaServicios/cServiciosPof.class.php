<?php 
class cServiciosPof 
{

	protected $oCurl;
	protected $conexion;
	protected $error;
	protected $Utf8;
	
	protected $MemCache;

	const MemCacheExpire = 86400;// 1 dia
	
	
	function __construct($conexion){
		$this->conexion = &$conexion;
 		$this->error = array();
		$this->oCurl = new CurlBigtree();
		$this->Utf8 = false;
		
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


	public function CodificarUtf8()
	{
		$this->Utf8 = true;
	}
	
	
	public function TraerCargosCompletosxEscuela($claveEscuela, &$Personas, &$cuits="")
	{
		if (!$this->getPof($claveEscuela, $personas, $cuits))
			return false;
		
		if (!$this->getPofTemporal($claveEscuela, $resultadoTmp, $personasTMP,$cuits))
			return false;
		
		if (!$this->getPofPDD($claveEscuela, $Cargos))
			return false;
		
		$oServiciosEducacion = new cServiciosEducacion($this->conexion);
		$Personas = array();
		foreach ($personas as $data)
		{
			if ($data['fechaNacOut']!="")
				$data['fechaNacOut'] = FuncionesPHPLocal::ConvertirFecha($data['fechaNacOut'],"aaaa-mm-dd","dd/mm/aaaa");
			$Personas[$data['documentoOut']]['Cuit'] = $data['cuit'];
			$Personas[$data['documentoOut']]["muestrocuit"]=1;
			$Personas[$data['documentoOut']]['Nombre'] = $data['nombreOut'];
			$Personas[$data['documentoOut']]['Apellido'] = $data['apellidoOut'];
			$Personas[$data['documentoOut']]['nombreOut'] = $data['nombreOut'];
			$Personas[$data['documentoOut']]['apellidoOut'] = $data['apellidoOut'];
			$Personas[$data['documentoOut']]['Sexo'] = $data['sexoOut'];
			$Personas[$data['documentoOut']]['FechaNacimiento'] = $data['fechaNacOut'];
			$Personas[$data['documentoOut']]['pof'] = $data['cargos'];
			$Personas[$data['documentoOut']]['pdd'] = array();
		}
		
		$arrayTmp = explode(",",MOVIMIENTOSTMPHABILITADOS);
		foreach ($resultadoTmp as $dataTmp)
		{
			
			if (in_array(trim(strtoupper($dataTmp['codigoCambio'])),$arrayTmp))
			{
				//$cuil = "20".$dataTmp['documentoOut']."7";
				if (!isset($Personas[$dataTmp['documentoOut']]))
				{
					
					$Personas[$dataTmp['documentoOut']]['Cuit'] = $dataTmp['cuitCuil'];
					$Personas[$dataTmp['documentoOut']]["muestrocuit"]=1;
					$Personas[$dataTmp['documentoOut']]['Nombre'] = $dataTmp['nombreOut'];
					$Personas[$dataTmp['documentoOut']]['Apellido'] = $dataTmp['apellidoOut'];
					$Personas[$dataTmp['documentoOut']]['nombreOut'] = $dataTmp['nombreOut'];
					$Personas[$dataTmp['documentoOut']]['apellidoOut'] = $dataTmp['apellidoOut'];
					$Personas[$dataTmp['documentoOut']]['Sexo'] = "";
					$Personas[$dataTmp['documentoOut']]['FechaNacimiento'] = "";
					//$dataPof['']
					$Personas[$dataTmp['documentoOut']]['pdd'] = array();
					//print_r($Personas);
					
				}
				$dataTmp["origen"]="CARGOTMP";
				$dataTmp['codigoCambio'] = trim($dataTmp['codigoCambio']);
				$dataTmpTmp['secuenciaOut']=intval($dataTmp['secuenciaOut']);
				$dataTmp['subsecuenciaOut']=($dataTmp['subscuenciaOut']==""?0:intval($dataTmp['subscuenciaOut']));
				$dataTmp['hsDesigDetalle']=$dataTmp['hsDesigDetalle'];
				$dataTmp['realIntOut']=($dataTmp['realIntOut']=="R"?"REAL":"INT");
				$dataTmp['caractRevOut']=$dataInsert['RevistaVisualizacion']=$dataTmp['caractRevOut'];
				$dataTmp['numeroDistrito']="";
				$dataTmp['numeroEstablecimiento']="";
				$dataTmp['ModalidadCarrera'] = $dataTmp['carrera'];
				$dataTmp['claveEstab']=$dataTmp['estab'];
				$dataTmp['cargoCodigo']=$dataTmp['cargo'];
				$dataTmp['cargoDesc']="";
				$dataTmp['cargoEnse']="";
				$dataTmp['cargoHsMod']="";
				$dataTmp['anio']=intval($dataTmp['anio']);
				$dataTmp['letraSeccion']=$dataTmp['seccion'];
				$dataTmp['asignatura']="";
				$dataTmp['area']=$dataTmp['area'];
				$dataTmp['hsDesig'] = $dataInsert['hsDesigDetalle'] = $dataTmp['hsDesigDetalle'];
				$dataTmp['regHorario']="";
				$dataTmp['turno']=$dataTmp['turno'];
				$dataTmp['regEstatCodigo']=$dataTmp['regEstat'];
				$dataTmp['regEstatDesc']="";
				$dataTmp['subGrupoCodigo']=$dataTmp['subGrupoCodigo'];
				$dataTmp['subGrupoDesc']=$dataTmp['subGrupoCodigo'];
				$dataTmp['grupoCodigo']=$dataTmp['codigoGrupo'];
				$dataTmp['grupoDesc']=$dataTmp['codigoGrupo'];
				$Personas[$dataTmp['documentoOut']]['pof'][] = $dataTmp;
			}
		}
		//reviso los cargos de pdd que no están en HOST
		foreach ($Cargos as $dni=>$dataText)
		{
			foreach($dataText as $data)
			{
				if (!array_key_exists($dni,$Personas)){ // el DNI no estaen HOST
					//busco los datos personales
					
					$Personas[$dni]['Cuit'] = $dni;
					$Personas[$dni]['Nombre'] = $data['nombre'];
					$Personas[$dni]['Apellido'] = "";
					$Personas[$dni]['Sexo'] = "";
					$Personas[$dni]['FechaNacimiento'] = "";
					$Personas[$dni]['pof'] = array();
					$cuil = "20".$dni."7";
					$dataServicio = $oServiciosEducacion->getPersonaxCuil($cuil);
					$Personas[$dni]["muestrocuit"]=0;
					if ($dataServicio!==false)
					{
						if (isset($dataServicio['success']) && $dataServicio['success']==true)
						{
							if (isset($dataServicio['persona']['cuit']))
								$Personas[$dni]['Cuit'] = $dataServicio['persona']['cuit'];
							if (isset($dataServicio['persona']['nombre']))
								$Personas[$dni]['Nombre'] = $dataServicio['persona']['nombre'];
							$Personas[$dni]['Apellido'] = "";
							$Personas[$dni]['Sexo'] = "-";
							if (isset($dataServicio['persona']['fechanacimiento']))
							{	
								if ($dataServicio['persona']['fechanacimiento']!="")
									$dataServicio['persona']['fechanacimiento'] = FuncionesPHPLocal::ConvertirFecha($dataServicio['persona']['fechanacimiento'],"aaaa-mm-dd","dd/mm/aaaa");
								
								$Personas[$dni]['FechaNacimiento'] = $dataServicio['persona']['fechanacimiento'];
							}
						}	
					}
				}
				$Personas[$dni]['pdd'][] = $data;
			}
		}

		return true;
	}
	
	
	public function getPofTemporal($ClaveEscuela, &$resultadoTmp, &$personasOrdenadas,&$cuit=array())
	{
		$personas = array();
		
		$cargarServicio = true;
		if (MEMCACHED)
		{
			$personas = $this->MemCache->get("miPofHostTmp/clave/".$ClaveEscuela);
			if ($personas)
			{
				$cargarServicio = false;
			}
		}
		$cargarCuits = true;
		if (MEMCACHED)
		{
			$cuit = $this->MemCache->get("MiEscuela/cuits/".$ClaveEscuela);
			if ($cuit)
				$cargarCuits = false;
		}
		if ($cargarServicio)
		{
			$i=0;
		
			$url = $ClaveEscuela;
			$urlAnexa = "";
			$header = array();
			$this->oCurl->setUrl(WS_EDUCACION_POF_TMP_HOST);
			$this->oCurl->setFunction(get_class($this)."-".__FUNCTION__);
			$fields_string = "";

			//$this->oCurl->setDebug(true);
			
			//if (SITIOPRODUCTIVO==1)
			//	$this->oCurl->setUtf8(true);
			
			if(!$this->oCurl->sendGet($url,$resultados))
			{	
				$this->setError("Error","Error, ocurrio un error al buscar los cargos temporales por CUIL");
				return false;
			}
			//$cargosOrdenados=array();
			$i=0;
			$resultadoTmp=$resultados;
			if (is_array($resultadoTmp) && count($resultadoTmp)>0)
				ksort($resultadoTmp);
			if (MEMCACHED)
				$this->MemCache->set("miPofHostTmp/clave/".$ClaveEscuela, $personas, MEMCACHE_COMPRESSED, MEMCACHEDTIMELIFEDEFAULT);

		}
		if ($cargarCuits)
		{
			if (is_array($resultadoTmp) && count($resultadoTmp)>0)
			{
				foreach($resultadoTmp as $persona)
				{
					if (!isset($personasOrdenadas[$persona["cuitCuil"]]))
					{
						$personasOrdenadas[$persona["cuitCuil"]]=$persona;
						$cuit[$persona["cuitCuil"]] = $persona["cuitCuil"];
					}
				}
				if (MEMCACHED)
					$this->MemCache->set("MiEscuela/cuits/".$ClaveEscuela, $cuit, MEMCACHE_COMPRESSED, MEMCACHEDTIMELIFEDEFAULT);
			}
		}	
		
		return true;
	}

	
	public function getPof($ClaveEscuela, &$personasOrdenadas,&$cuit)
	{
		$personas = array();
		$cuit = array();
		
		$cargarServicio = true;
		if (MEMCACHED)
		{
			$personas = $this->MemCache->get("miPofHost/clave/".$ClaveEscuela);
			if ($personas)
			{
				$cargarServicio = false;
			}
		}
		$cargarCuits = true;
		if (MEMCACHED)
		{
			$cuit = $this->MemCache->get("MiEscuela/cuits/".$ClaveEscuela);
			if ($cuit)
				$cargarCuits = false;
		}
		if ($cargarServicio)
		{
			$i=0;
		
			$url = $ClaveEscuela;
			$urlAnexa = "";
			$header = array();
			$this->oCurl->setUrl(WS_EDUCACION_POF_HOST);
			$this->oCurl->setFunction(get_class($this)."-".__FUNCTION__);
			$fields_string = "";

			//$this->oCurl->setDebug(true);
			
			//if (SITIOPRODUCTIVO==1)
			//	$this->oCurl->setUtf8(true);
			
			if(!$this->oCurl->sendGet($url,$resultados))
			{	
				$this->setError("Error","Error, ocurrio un error al buscar los cargos por CUIL");
				return false;
			}
			
			$cargosOrdenados=array();
			$i=0;
			$personas=$resultados["revistas"];
			if (is_array($personas) && count($personas)>0)
				ksort($personas);
			if (MEMCACHED)
				$this->MemCache->set("miPofHost/clave/".$ClaveEscuela, $personas, MEMCACHE_COMPRESSED, MEMCACHEDTIMELIFEDEFAULT);

		}
		$personasOrdenadas=array();
		if ($cargarCuits)
		{
			if (is_array($personas) && count($personas)>0)
			{
				foreach($personas as $persona)
				{
					$cuit[$persona["cuit"]] = $persona["cuit"];
					$personasOrdenadas[$persona["cuit"]]=$persona;
				}
				if (MEMCACHED)
					$this->MemCache->set("MiEscuela/cuits/".$ClaveEscuela, $cuit, MEMCACHE_COMPRESSED, MEMCACHEDTIMELIFEDEFAULT);
			}
		}	
		
		return true;
	}
	
	
	
	public function getPofPDD($ClaveEscuela, &$cargosOrdenados)
	{

		$datosEnviar=array();
		$dataEnvio = json_encode($datosEnviar);
		
		$url = $ClaveEscuela."/all";
		$header = array();
		$this->oCurl->setUrl(WS_EDUCACION_PDD_POF);
		$this->oCurl->setFunction(get_class($this)."-".__FUNCTION__);
		$fields_string = "";
		//$this->oCurl->setDebug(true);

		if(!$this->oCurl->sendGet($url,$resultados))
		{	
			$this->setError("Error","Error, ocurrio un error al buscar los cargos por CUIL");
			return false;
		}
	

		$ClaveEscuelaDatos= $ClaveEscuela;
		$cargosOrdenados=array();
		$i=0;
		foreach($resultados as $cargo)
		{
			if (!isset($cargosOrdenados[$cargo["documento"]]) || count($cargosOrdenados[$cargo["documento"]])==0)
				$i=0;
			$cargosOrdenados[$cargo["documento"]][$i]=$cargo;
			$cargosOrdenados[$cargo["documento"]][$i]["toma_pocesion_efectiva"]=date("d/m/Y",strtotime($cargo['toma_pocesion_efectiva']));
			$i++;
		}
		//print_r($cargosOrdenados);
		ksort($cargosOrdenados);
		if (MEMCACHED)
		{
			$this->MemCache->set("miPofPdd/clave/".$ClaveEscuela, $cargosOrdenados, MEMCACHE_COMPRESSED, MEMCACHEDTIMELIFEDEFAULT);
		}
		
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
	
	private function SetMemcache($key,$data)
	{
		if (MEMCACHED)
			$this->MemCache->set($key, $data, MEMCACHE_COMPRESSED, self::MemCacheExpire);
	}
	
	public function TraerCargosCompletosxEscuelaxCuit($claveEscuela, &$Personas, &$cuits="")
	{
		if (!$this->getPof($claveEscuela, $personas, $cuits))
			return false;
		
		if (!$this->getPofTemporal($claveEscuela, $resultadoTmp, $personasTmp,$cuits))
			return false;
			
		$oServiciosEducacion = new cServiciosEducacion($this->conexion);
		$Personas = array();
		
		foreach ($personas as $data)
		{
			if ($data['fechaNacOut']!="")
				$data['fechaNacOut'] = FuncionesPHPLocal::ConvertirFecha($data['fechaNacOut'],"aaaa-mm-dd","dd/mm/aaaa");
			$Personas[$data['cuit']]['Cuit'] = $data['cuit'];
			$Personas[$data['cuit']]['documento'] = $data['documentoOut'];
			$Personas[$data['cuit']]['documentoOut'] = $data['documentoOut'];
			$Personas[$data['cuit']]["muestrocuit"]=1;
			$Personas[$data['cuit']]['Nombre'] = $data['nombreOut'];
			$Personas[$data['cuit']]['Apellido'] = $data['apellidoOut'];
			$Personas[$data['cuit']]['nombreOut'] = $data['nombreOut'];
			$Personas[$data['cuit']]['apellidoOut'] = $data['apellidoOut'];
			$Personas[$data['cuit']]['Sexo'] = $data['sexoOut'];
			$Personas[$data['cuit']]['sexoOut'] = $data['sexoOut'];
			$Personas[$data['cuit']]['FechaNacimiento'] = $data['fechaNacOut'];
			$Personas[$data['cuit']]['pof'] = $data['cargos'];
		}
		
		
		$arrayTmp = explode(",",MOVIMIENTOSTMPHABILITADOS);
		foreach ($resultadoTmp as $dataTmp)
		{
			
			if (in_array(trim(strtoupper($dataTmp['codigoCambio'])),$arrayTmp))
			{
				//$cuil = "20".$dataTmp['documentoOut']."7";
				if (!isset($Personas[$dataTmp['cuitCuil']]))
				{
					
					$Personas[$dataTmp['cuitCuil']]['Cuit'] = $dataTmp['cuitCuil'];
					$Personas[$dataTmp['cuitCuil']]['documento'] = $dataTmp['documentoOut'];
					$Personas[$dataTmp['cuitCuil']]['documentoOut'] = $dataTmp['documentoOut'];
					$Personas[$dataTmp['cuitCuil']]["muestrocuit"]=1;
					$Personas[$dataTmp['cuitCuil']]['Nombre'] = $dataTmp['nombreOut'];
					$Personas[$dataTmp['cuitCuil']]['Apellido'] = $dataTmp['apellidoOut'];
					$Personas[$dataTmp['cuitCuil']]['nombreOut'] = $dataTmp['nombreOut'];
					$Personas[$dataTmp['cuitCuil']]['apellidoOut'] = $dataTmp['apellidoOut'];
					$Personas[$dataTmp['cuitCuil']]['Sexo'] = $dataTmp['sexoOut'];
					$Personas[$dataTmp['cuitCuil']]['sexoOut'] = $dataTmp['sexoOut'];
					$Personas[$dataTmp['cuitCuil']]['FechaNacimiento'] = "";
					//$dataPof['']
										
				}
				
				$dataTmp["origen"]="CARGOTMP";
				$dataTmp['codigoCambio'] = trim($dataTmp['codigoCambio']);
				$dataTmpTmp['secuenciaOut']=intval($dataTmp['secuenciaOut']);
				$dataTmp['subsecuenciaOut']=($dataTmp['subscuenciaOut']==""?0:intval($dataTmp['subscuenciaOut']));
				$dataTmp['hsDesigDetalle']=$dataTmp['hsDesigDetalle'];
				$dataTmp['realIntOut']=($dataTmp['realIntOut']=="R"?"REAL":"INT");
				$dataTmp['caractRevOut']=$dataInsert['RevistaVisualizacion']=$dataTmp['caractRevOut'];
				$dataTmp['numeroDistrito']="";
				$dataTmp['numeroEstablecimiento']="";
				$dataTmp['ModalidadCarrera'] = $dataTmp['carrera'];
				$dataTmp['claveEstab']=$dataTmp['estab'];
				$dataTmp['cargoCodigo']=$dataTmp['cargo'];
				$dataTmp['cargoDesc']="";
				$dataTmp['cargoEnse']="";
				$dataTmp['cargoHsMod']="";
				$dataTmp['anio']=intval($dataTmp['anio']);
				$dataTmp['letraSeccion']=$dataTmp['seccion'];
				$dataTmp['asignatura']="";
				$dataTmp['area']=$dataTmp['area'];
				$dataTmp['hsDesig'] = $dataInsert['hsDesigDetalle'] = $dataTmp['hsDesigDetalle'];
				$dataTmp['regHorario']="";
				$dataTmp['turno']=$dataTmp['turno'];
				$dataTmp['regEstatCodigo']=$dataTmp['regEstat'];
				$dataTmp['regEstatDesc']="";
				$dataTmp['subGrupoCodigo']=$dataTmp['subGrupoCodigo'];
				$dataTmp['subGrupoDesc']=$dataTmp['subGrupoCodigo'];
				$dataTmp['grupoCodigo']=$dataTmp['codigoGrupo'];
				$dataTmp['grupoDesc']=$dataTmp['codigoGrupo'];
				$Personas[$dataTmp['cuitCuil']]['pof'][] = $dataTmp;
			}
		}


		return true;
	}
	
	
}//FIN CLASE

?>