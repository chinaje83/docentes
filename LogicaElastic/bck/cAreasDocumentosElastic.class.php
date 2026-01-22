<?php
//----------------------------------------------------------------------------------------- 
//----------------------------------------------------------------------------------------- 
// Clase con la lógica para las busquedas relacionadas a cajas

class cAreasDocumentosElastic
{


	protected $ch;
	const INDEX = INDICE.INDICEAREAS;
	
	// Constructor de la clase
	function __construct(){
		$this->ch = curl_init();
    } 
	
	// Destructor de la clase
	function __destruct() {	
		curl_close($this->ch);
    } 	
	
	
	public function ArmarMenu(&$conexion, $datos, &$menu, &$numfilas_hijos, &$breadcrumb, &$DocumentosArea)
	{
	
		
		$oElastic = new cClientesElastic($conexion,"");
		$datosBusqueda['IdDocumentoPadre'] = $datos['IdDocumento'];
		$datosBusqueda['TipoDocumento'] = $datos['TipoDocumento'];
		$datosBusqueda['TipoDocumentoArea'] = $datos['TipoDocumentoArea'];
		$datosBusqueda['Tipo'] = TIPODOC;
		if(!$oElastic->BuscarCantidadDocumentosHijosxId($datosBusqueda,$resultadoTotales))
			return false;
			
		$menu = array();
		$i=0;
		$numfilas_hijos = count($resultadoTotales['aggregations']['menu']['buckets']);
		foreach($resultadoTotales['aggregations']['menu']['buckets'] as $dataDoc)
		{
			if (array_key_exists($dataDoc['key'],$datos['TiposDocumentosAccedo']))
			{
				$menu[$i]['Id'] = $dataDoc['key'];
				$menu[$i]['Nombre'] = $datos['TiposDocumentosAccedo'][$dataDoc['key']]['Nombre'];
				$menu[$i]['Cantidad'] = $dataDoc['doc_count'];
				$i++;
			}
							
			
		}
		
		return true;
	}
	
	
	public function BusquedaMenu($datos,&$resultado,&$numfilas,&$breadcrumb)
	{
		$breadcrumb = array();
		$IdRegistroTipoDocumento = "";
		$IdTipoDocumento = "";
		$Vigencia = $_SESSION['Anio'].$_SESSION['Mes'].$_SESSION['Dia'];
		$f = $m = 0;
		$dataEnviar = array();
		$dataEnviar['size'] = 1;
		$dataEnviar['query'] = array();
		$dataEnviar['query']['bool'] = array();
		
		$dataEnviar['query']['bool']['filter'] = array();
		
		$dataEnviar['query']['bool']['filter'][$f]['term'] = array("Cliente.Id"=>$_SESSION['IdCliente']);
		$f++;
		$dataEnviar['query']['bool']['filter'][$f]['term'] = array("Proyecto.Id"=>PROYECTO);
		$f++;
		$dataEnviar['query']['bool']['filter'][$f]['range'] = array();
		$dataEnviar['query']['bool']['filter'][$f]['range']['Vigencia.Desde']['lte'] = $Vigencia;
		$f++;
		$dataEnviar['query']['bool']['filter'][$f]['range'] = array();
		$dataEnviar['query']['bool']['filter'][$f]['range']['Vigencia.Hasta']['gte'] = $Vigencia;
		$f++;
		if(isset($_SESSION['IdArea']) && $_SESSION['IdArea']!="")
		{
			if(!is_array($_SESSION['IdArea']))
				$dataEnviar['query']['bool']['filter'][$f]['terms'] = array("Area.Id"=>explode(",",$_SESSION['IdArea']));
			else
				$dataEnviar['query']['bool']['filter'][$f]['terms'] = array("Area.Id"=>$_SESSION['IdArea']);
			$f++;
		}
		
		$n = 0;
		if(isset($datos['IdRegistroTipoDocumento']) && $datos['IdRegistroTipoDocumento']!="")
		{
			$IdRegistroTipoDocumento = $datos['IdRegistroTipoDocumento'];
			$dataEnviar['query']['bool']['filter'][$f]['nested'] = array("path"=>"TipoDocumento","score_mode" => "sum");
			$dataEnviar['query']['bool']['filter'][$f]['nested']['query']['bool']['filter'][$n]['term'] = array("TipoDocumento.IdRegistro"=>$datos['IdRegistroTipoDocumento']);
			$f++;
			$n++;
		}
		if(isset($datos['IdTipoDocumento']) && $datos['IdTipoDocumento']!="")
		{
			$IdTipoDocumento = $datos['IdTipoDocumento'];
			$dataEnviar['query']['bool']['filter'][$f]['nested'] = array("path"=>"TipoDocumento","score_mode" => "sum");
			$dataEnviar['query']['bool']['filter'][$f]['nested']['query']['bool']['filter'][$n]['term'] = array("TipoDocumento.Id"=>$datos['IdTipoDocumento']);
			$f++;
			$n++;
		}
				
		//echo "<pre>";print_r($dataEnviar);die("</pre>");
		$datosEnviar = json_encode($dataEnviar);

		$urlBase = ELASTICSERVER."/".self::INDEX."/".TYPE."/_search";
		
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
		
		if (!isset($data['hits']))
		{
			FuncionesElastic::MostrarError($data);
			return false;
		}
		
		
		
		$array = array();
		$arregloHijos = array();
		$arregloNietos = array();
		$resultado = array();
		if(empty($data['hits']['hits'][0]['_source']))
			return true;
		$fila = $data['hits']['hits'][0]['_source'];
		
		if($IdRegistroTipoDocumento != "")
		{
			$resultado = self::_BuscarHijos($fila['TipoDocumento'],$IdRegistroTipoDocumento,'IdRegistro');
			self::_BuscarPadres($fila['TipoDocumento'],$IdRegistroTipoDocumento,'IdRegistro',$breadcrumb);
			$resultado['Asociados'] = self::_BuscarAsociados($fila['TipoDocumento'],$IdRegistroTipoDocumento,'IdRegistro');
		}
		elseif($IdTipoDocumento != "")
		{
			//$resultado = self::_BuscarHijos($fila['TipoDocumento'],$IdTipoDocumento,'Id');
			self::_BuscarPadres($fila['TipoDocumento'],$IdTipoDocumento,'Id',$breadcrumb);
			//$resultado['Asociados'] = self::_BuscarAsociados($fila['TipoDocumento'],$IdTipoDocumento,'Id');
			$resultado = $data['hits']['hits'];
		}
		else
			$resultado = $data['hits']['hits'];
		
		
		//$resultado = $data['hits']['hits'];	
		//echo "<pre>";print_r($breadcrumb);die("</pre>");
		$numfilas =  count($resultado);;//$data['hits']['total'];

		
		return true;
	}
	
	
	
	public function Reporte($datos = array())
	{
		$datosEnviar = array();
		
		$datosEnviar['sort'] = array();
		$datosEnviar['sort'][0] = array();
		$datosEnviar['sort'][0]['UltimaModificacion.Fecha'] = array('order'=>'asc');
		
		$datosEnviar['size'] = PAGINAR;
		$datosEnviar['from'] = 0;
		$datosEnviar['aggs'] = array();
		$datosEnviar['aggs']['reporte_actas'] = array();
		$datosEnviar['aggs']['reporte_actas']['date_histogram']=array();
		$datosEnviar['aggs']['reporte_actas']['date_histogram']['field'] = "UltimaModificacion.Fecha";
		$datosEnviar['aggs']['reporte_actas']['date_histogram']['interval'] = "10m";
		$datosEnviar['aggs']['reporte_actas']['aggs'] = array();
		$datosEnviar['aggs']['reporte_actas']['aggs']['usuarios'] = array();
		$datosEnviar['aggs']['reporte_actas']['aggs']['usuarios']['terms'] = array();
		$datosEnviar['aggs']['reporte_actas']['aggs']['usuarios']['terms']['field'] = "Area.Id";

		$dataEnvio = json_encode($datosEnviar);
		//echo $dataEnvio."<br/>";die;

		$urlBase = ELASTICSERVER."/".self::INDEX."/".TYPE."/_search";
		$header = array("Content-Type: application/json");
		curl_setopt($this->ch, CURLOPT_HTTPHEADER, $header);
		curl_setopt($this->ch, CURLOPT_SSL_VERIFYPEER, false); 
		curl_setopt($this->ch, CURLOPT_URL, $urlBase);
		curl_setopt($this->ch, CURLOPT_CUSTOMREQUEST, "POST");
		curl_setopt($this->ch, CURLOPT_POSTFIELDS,$dataEnvio);
		curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, 1); 
		
		//execute post
		$result = curl_exec($this->ch);
		$data = json_decode($result,true);
		if (!isset($data['hits']))
		{
			FuncionesElastic::MostrarError($data);
			return false;
		}
		return FuncionesPHPLocal::DecodificarUtf8($data);
	}
	
	public function BusquedaAvanzada($datos = array())
	{
		
		if (!isset($datos['from']))
			$datos['from'] = 0;
		if (!isset($datos['size']))
			$datos['size'] = PAGINAR;
		
		$f = $m = 0;
		$dataEnviar = array();
		$dataEnviar['query'] = array();
		$dataEnviar['query']['bool'] = array();
		
		$dataEnviar['query']['bool']['filter'] = array();
		if(isset($datos['IdCaja']) && $datos['IdCaja']!="")
		{
			if(!is_array($datos['IdCaja']))
				$dataEnviar['query']['bool']['filter'][$f]['terms'] = array("IdCaja"=>explode(",",$datos['IdCaja']));
			else
				$dataEnviar['query']['bool']['filter'][$f]['terms'] = array("IdCaja"=>$datos['IdCaja']);
			$f++;
		}
		if(isset($datos['CodigoBarras']) && $datos['CodigoBarras']!="")
		{
			$dataEnviar['query']['bool']['filter'][$f]['term'] = array("CodigoBarras"=>$datos['CodigoBarras']);
			$f++;
		}
		if(isset($datos['IdArea']) && $datos['IdArea']!="")
		{
			if(!is_array($datos['IdArea']))
				$dataEnviar['query']['bool']['filter'][$f]['terms'] = array("Area.Id"=>explode(",",$datos['IdArea']));
			else
				$dataEnviar['query']['bool']['filter'][$f]['terms'] = array("Area.Id"=>$datos['IdArea']);
			$f++;
		}
		if(isset($datos['IdEstado']) && $datos['IdEstado']!="")
		{
			$dataEnviar['query']['bool']['filter'][$f]['term'] = array("Estado.Id"=>$datos['IdEstado']);
			$f++;
		}
		if(isset($datos['IdCliente']) && $datos['IdCliente']!="")
		{
			$dataEnviar['query']['bool']['filter'][$f]['term'] = array("Cliente.Id"=>$datos['IdCliente']);
			$f++;
		}
		if(isset($datos['IdProyecto']) && $datos['IdProyecto']!="")
		{
			$dataEnviar['query']['bool']['filter'][$f]['term'] = array("Proyecto.Id"=>$datos['IdProyecto']);
			$f++;
		}
		if(isset($datos['IdPlanta']) && $datos['IdPlanta']!="")
		{
			$dataEnviar['query']['bool']['filter'][$f]['term'] = array("Ubicacion.Id"=>$datos['IdPlanta']);
			$f++;
		}
		if(isset($datos['IdUbicacion']) && $datos['IdUbicacion']!="")
		{
			$dataEnviar['query']['bool']['filter'][$f]['term'] = array("Ubicacion.Seccion.Id"=>$datos['IdUbicacion']);
			$f++;
		}
		if(isset($datos['IdEstanteria']) && $datos['IdEstanteria']!="")
		{
			$dataEnviar['query']['bool']['filter'][$f]['term'] = array("Ubicacion.Seccion.Estanteria.Id"=>$datos['IdEstanteria']);
			$f++;
		}
		if(isset($datos['IdCelda']) && $datos['IdCelda']!="")
		{
			$dataEnviar['query']['bool']['filter'][$f]['term'] = array("Ubicacion.Seccion.Estanteria.Celda.Id"=>$datos['IdCelda']);
			$f++;
		}
		if(isset($datos['CodigoCelda']) && $datos['CodigoCelda']!="")
		{
			$dataEnviar['query']['bool']['filter'][$f]['term'] = array("Ubicacion.Seccion.Estanteria.Celda.CodigoBarras"=>$datos['CodigoCelda']);
			$f++;
		}

		
		/*
		if(isset($datos['IdCliente']) && $datos['IdCliente']!="")
		{
			$dataEnviar['query']['bool']['filter'][$f]['term'] = array("Libro.Id"=>$datos['IdCliente']);
			$f++;
		}
		
		
		*/
		
		
		if(isset($datos['sort']['campo']) && isset($datos['sort']['orden']))
		{
			$dataEnviar['sort'] = array();
			$dataEnviar['sort'][$datos['sort']['campo']] = array('order'=>$datos['sort']['orden']);
		}
		
		$dataEnviar['size'] = $datos['size'];
		$dataEnviar['from'] = $datos['from'];
		//echo "<pre>";print_r($dataEnviar);echo "</pre>";die;
		$datosEnviar = json_encode($dataEnviar);
		
		//echo "<pre>$datosEnviar</pre>";die;
		
		$urlBase = ELASTICSERVER."/".self::INDEX."/".TYPE."/_search";
		
		
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
		if (!isset($data['hits']))
		{
			FuncionesElastic::MostrarError($data);
			return false;
		}
		return FuncionesPHPLocal::DecodificarUtf8($data);
		
	}
	
	public function _BuscarxCodigo($Id,&$datosRegistro)
	{
		
		$datosRegistro = $datosEnviar = array();
		
		$urlBase = ELASTICSERVER."/".self::INDEX."/".TYPE."/".$Id;
		$header = array("Content-Type: application/json");
		curl_setopt($this->ch, CURLOPT_SSL_VERIFYPEER, false); 
		curl_setopt($this->ch, CURLOPT_URL, $urlBase);
		curl_setopt($this->ch, CURLOPT_CUSTOMREQUEST, "GET");
		curl_setopt($this->ch, CURLOPT_POSTFIELDS,"");
		curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, 1); 
		
		//execute post
		$result = curl_exec($this->ch);
		echo $result;
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
	
	public static function ArmarHtmlHijos($filaHijo,$Cantidades,$IdPadre,$class="",$TieneCant=false)
	{
		$ulNietos = "";
		if(!empty($filaHijo['Hijos']))
		{
			$ulNietos = <<<UL

                        <ul class="nav nav-stacked nav-pills nietos $class" id="lstNietos_{$filaHijo['Id']}">

UL;
			$dataHijos = $tieneHijos = 1;
			foreach($filaHijo['Hijos'] as $filaNieto)
			{
				$cantidadNieto = 0;
				$dataNietos = 0;
				if(!empty($filaNieto['Hijos']))
					$dataNietos = 1;
				if(isset($Cantidades[$filaNieto['Id']]))
					$cantidadNieto = intval($Cantidades[$filaNieto['Id']]);
				$BadgeCantidad = "";
				if($TieneCant)
					$BadgeCantidad = ' <span class="badge badge-success pull-right">'.$cantidadNieto.'</span>';
				$NombreNieto = '<i class="fas fa-level-up-alt fa-rotate-90" aria-hidden="true"></i>&nbsp;'.FuncionesPHPLocal::HtmlspecialcharsSistema(utf8_decode($filaNieto['Nombre']),ENT_QUOTES);
				$ulNietos .= <<<LI
                            <li>
								<a href="javascript:void(0);" class="btnSubTipo" data-id="{$filaNieto['Id']}" data-hijos="$dataNietos" data-registro="$IdPadre">
									$NombreNieto$BadgeCantidad
								</a>
							</li>

LI;
			}
			$ulNietos .= <<<UL
                        </ul>
UL;
		}
		
		return $ulNietos;
		
	}
	
	public static function ArmarHtmlAsocHijos($filaHijo,$Cantidades,$IdPadre,$class="",$TieneCant=false)
	{
		$ulNietos = "";
		if(!empty($filaHijo['Hijos']))
		{
			$ulNietos = <<<UL

                        <ul class="nav nav-stacked nav-pills nietos $class" id="lstAsocHijos_{$filaHijo['Id']}">

UL;
			$dataHijos = $tieneHijos = 1;
			foreach($filaHijo['Hijos'] as $filaNieto)
			{
				$cantidadNieto = 0;
				$dataNietos = 0;
				if(!empty($filaNieto['Hijos']))
					$dataNietos = 1;
				if(isset($Cantidades[$filaNieto['Id']]))
					$cantidadNieto = intval($Cantidades[$filaNieto['Id']]);
				$BadgeCantidad = "";
				if($TieneCant)
					$BadgeCantidad = ' <span class="badge badge-success pull-right">'.$cantidadNieto.'</span>';
				$NombreNieto = '<i class="fas fa-level-up-alt fa-rotate-90" aria-hidden="true"></i>&nbsp;'.FuncionesPHPLocal::HtmlspecialcharsSistema(utf8_decode($filaNieto['Nombre']),ENT_QUOTES);
				$ulNietos .= <<<LI
                            <li>
								<a href="javascript:void(0);" class="btnSubTipoAsoc" data-id="{$filaNieto['Id']}" data-hijos="$dataNietos" data-registro="$IdPadre">
									$NombreNieto$BadgeCantidad
								</a>
							</li>

LI;
			}
			$ulNietos .= <<<UL
                        </ul>
UL;
		}
		
		return $ulNietos;
		
	}
	
	
	
	public static function calcularCompletadosHijos($filaHijo,$Cantidades,&$completados,&$total)
	{
		$total++;
		if(!empty($filaHijo['Hijos']))
		{
			foreach($filaHijo['Hijos'] as $filaNietos)
				self::calcularCompletadosHijos($filaNietos,$Cantidades,$completados,$total);
		}
	
		if(isset($Cantidades[$filaHijo['Id']]) && $Cantidades[$filaHijo['Id']]>0)
			$completados++;
	}
	
	
	private static function _BuscarHijos($datos,$Id,$tipo)
	{
		$i = 0;
		$resultado = array();
		$arregloHijos = array();
		foreach($datos as $TipoDocumento)
		{
			if(isset($TipoDocumento[$tipo]) && $TipoDocumento[$tipo] == $Id && isset($TipoDocumento[$tipo.'Hijos']))
				$arregloHijos = $TipoDocumento[$tipo.'Hijos'];
		}
		foreach($datos as $TipoDocumento)
		{
			if(isset($TipoDocumento[$tipo]) && in_array($TipoDocumento[$tipo],$arregloHijos))
			{
				$TipoDocumento[$tipo.'Padre'] = $Id;
				$Hijos = self::_BuscarHijos($datos,$TipoDocumento[$tipo],$tipo);
				if(!empty($Hijos))
					$TipoDocumento['Hijos'] = $Hijos;
				
				$resultado[$TipoDocumento['Id']] = $TipoDocumento;
			}
		}
		uksort($resultado, array("self", "_Ordenar"));
		return $resultado;
	}
	
	
	private static function _BuscarAsociados($datos,$Id,$tipo)
	{
		$i = 0;
		$resultado = array();
		$arregloAsociados = array();
		foreach($datos as $TipoDocumento)
		{
			if(isset($TipoDocumento[$tipo]) && $TipoDocumento[$tipo] == $Id && isset($TipoDocumento[$tipo.'Asociados']))
				$arregloAsociados = $TipoDocumento[$tipo.'Asociados'];
		}
		foreach($datos as $TipoDocumento)
		{
			if(isset($TipoDocumento[$tipo]) && in_array($TipoDocumento[$tipo],$arregloAsociados))
			{
				$TipoDocumento[$tipo.'Padre'] = $Id;
				$Asociados = self::_BuscarHijos($datos,$TipoDocumento[$tipo],$tipo);
				if(!empty($Asociados))
					$TipoDocumento['Hijos'] = $Asociados;
				
				$resultado[] = $TipoDocumento;
			}
		}
		uksort($resultado, array("self", "_Ordenar"));
		return $resultado;
	}
	
	
	private static function _BuscarPadres($datos,$Id,$tipo,&$resultado,$indice=0)
	{
		foreach($datos as $TipoDocumento)
		{
			if(isset($TipoDocumento[$tipo]) && $TipoDocumento[$tipo] == $Id && isset($TipoDocumento[$tipo.'Hijos']))
				$resultado[$indice] = $TipoDocumento;
			elseif(isset($TipoDocumento[$tipo.'Hijos']) && in_array($Id,$TipoDocumento[$tipo.'Hijos']))
			{
				$indicepadre = $indice - 1;
				self::_BuscarPadres($datos,$TipoDocumento[$tipo],$tipo,$resultado,$indicepadre);
			}
		}
		ksort($resultado);
	}
	
	private static function _Ordenar($a,$b)
	{
		if(isset($b['Orden']) && $b['Orden'] != "" && isset($a['Orden']) && $a['Orden'] != "")
			return intval($b['Orden']) - intval($a['Orden']);
		else
			return intval($b['IdRegistro']) - intval($a['IdRegistro']);
	}
		

	
}//FIN CLASE

?>