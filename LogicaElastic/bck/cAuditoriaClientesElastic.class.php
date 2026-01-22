<?php
//----------------------------------------------------------------------------------------- 
//----------------------------------------------------------------------------------------- 
// Clase con la lógica para las busquedas relacionadas a cajas

class cAuditoriaClientesElastic
{


	protected $ch;
	protected const INDEX = INDICE.INDICEAUDITORIA;
	
	// Constructor de la clase	
	public function __construct(){
		$this->ch = curl_init();
    } 
	
	// Destructor de la clase
	public function __destruct() {
		curl_close($this->ch);
    }
	
	
	/**
	 * @param mixed $datos
	 * @param mixed $datosDevueltos
	 * @return bool
	 */
	public function reporteErrores($datos, &$datosDevueltos) : bool
    {
	    $FechaInicio = '';
	    if(isset($datos['FechaInicio']) && $datos['FechaInicio'] !== ''){
		    $FechaInicio = trim($datos['FechaInicio']).'||/d';
	    }
	
	    $FechaFin = 'now/d';
	    if(isset($datos['FechaFin']) && $datos['FechaFin'] !== ''){
		    $FechaFin = trim($datos['FechaFin']).'||/d';
	    }
    	
    	
    	$datosEnviar = new stdClass();
    	$datosEnviar->size = 0;
    	$datosEnviar->query = new stdClass();
	    $datosEnviar->query->bool = new stdClass();
	    $datosEnviar->query->bool->filter = array();
	    $ff = 0; $var = 'Estado.Inicial.Id';
	    $datosEnviar->query->bool->filter[$ff] = new stdClass();
	    $datosEnviar->query->bool->filter[$ff]->term = new stdClass();
	    $datosEnviar->query->bool->filter[$ff]->term->$var = 32;
	    $ff++; $var = 'Estado.Final.Id';
	    $datosEnviar->query->bool->filter[$ff] = new stdClass();
	    $datosEnviar->query->bool->filter[$ff]->terms = new stdClass();
	    $datosEnviar->query->bool->filter[$ff]->terms->$var = [31, 116];
	    $ff++; $var = 'MovimientoFecha';
	    $datosEnviar->query->bool->filter[$ff] = new stdClass();
	    $datosEnviar->query->bool->filter[$ff]->range = new stdClass();
	    $datosEnviar->query->bool->filter[$ff]->range->$var = new stdClass();
	    $datosEnviar->query->bool->filter[$ff]->range->$var->lte = $FechaFin;
	    if('' !== $FechaInicio){
		    $datosEnviar->query->bool->filter[$ff]->range->$var->gte = $FechaInicio;
	    }
	
	
	    $datosEnviar->aggs = new stdClass();
	    $datosEnviar->aggs->Estados = new stdClass();
	    $datosEnviar->aggs->Estados->terms = new stdClass();
	    $datosEnviar->aggs->Estados->terms->field = 'Estado.Final.Id';
	    $datosEnviar->aggs->Estados->terms->size = 10;
	    $datosEnviar->aggs->Estados->aggs = new stdClass();
	    
	    $datosEnviar->aggs->Estados->aggs->Usuarios = new stdClass();
	    $datosEnviar->aggs->Estados->aggs->Usuarios->cardinality = new stdClass();
	    $datosEnviar->aggs->Estados->aggs->Usuarios->cardinality->field = 'UltimaModificacion.Cuil.raw';
	    
	    $datosEnviar->aggs->Estados->aggs->Nombre = new stdClass();
	    $datosEnviar->aggs->Estados->aggs->Nombre->top_hits = new stdClass();
	    $datosEnviar->aggs->Estados->aggs->Nombre->top_hits->size = 1;
	    $datosEnviar->aggs->Estados->aggs->Nombre->top_hits->{'_source'} = ['Estado.Final.Nombre'];
	    
	    if(true === $datos['grafico'])
	    {
		    $datosEnviar->aggs->Estados->aggs->Grafico = new stdClass();
		    $datosEnviar->aggs->Estados->aggs->Grafico->date_histogram = new stdClass();
		    $datosEnviar->aggs->Estados->aggs->Grafico->date_histogram->field = 'MovimientoFecha';
		    $datosEnviar->aggs->Estados->aggs->Grafico->date_histogram->interval = 'day';
		    $datosEnviar->aggs->Estados->aggs->Grafico->aggs = new stdClass();
		    $datosEnviar->aggs->Estados->aggs->Grafico->aggs->TipoInasistencia = new stdClass();
		    $datosEnviar->aggs->Estados->aggs->Grafico->aggs->TipoInasistencia->terms = new stdClass();
		    $datosEnviar->aggs->Estados->aggs->Grafico->aggs->TipoInasistencia->terms->field =  'TipoDocumento.Id';
		    $datosEnviar->aggs->Estados->aggs->Grafico->aggs->TipoInasistencia->terms->size = 100;
		
		
		    $datosEnviar->aggs->Grafico = new stdClass();
		    $datosEnviar->aggs->Grafico->date_histogram = new stdClass();
		    $datosEnviar->aggs->Grafico->date_histogram->field = 'MovimientoFecha';
		    $datosEnviar->aggs->Grafico->date_histogram->interval = 'day';
	    	
	    }
		
		
		
	
	    $dataEnvio = json_encode($datosEnviar);
	    
		$urlBase = ELASTICSERVER.'/'.self::INDEX.'/_search';
	    if (defined('RESTTOTALHITS') && true === RESTTOTALHITS){
		    $urlBase .= '?rest_total_hits_as_int=true';
	    }
	    if (defined('TOTALHISTTRACK') && true === TOTALHISTTRACK){
		    $urlBase .= '?track_total_hits=true';
	    }
	    //echo "<pre>curl -XPOST $urlBase -d '\n$dataEnvio'\n</pre>";
	    //echo "<pre>curl -XPOST $urlBase -d '\n$dataEnvio'\n</pre>";
	    //echo 'POST '.str_replace(ELASTICSERVER,'',$urlBase)."\n$dataEnvio\n";die;
	
	    $header = ['Content-Type: application/json'];
	    curl_setopt($this->ch, CURLOPT_HTTPHEADER, $header);
	    //curl_setopt($this->ch, CURLOPT_SSL_VERIFYPEER, false);
	    curl_setopt($this->ch, CURLOPT_URL, $urlBase);
	    curl_setopt($this->ch, CURLOPT_CUSTOMREQUEST, 'POST');
	    curl_setopt($this->ch, CURLOPT_POSTFIELDS,$dataEnvio);
	    curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, 1);
	
	    //execute post
	    $result = curl_exec($this->ch);
        //var_dump( $result);
	    $datosDevueltos = json_decode($result,true);
	   // var_dump($datosDevueltos);
	
	    if (!isset($datosDevueltos['hits']))
	    {
		    FuncionesElastic::MostrarError($datosDevueltos);
		    return false;
	    }
	    
	    return true;
	    
    }
    
		

	
}//FIN CLASE
