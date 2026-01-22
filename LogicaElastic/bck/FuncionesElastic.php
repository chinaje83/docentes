<?php 
//----------------------------------------------------------------------------------------- 
//----------------------------------------------------------------------------------------- 
// Clase con funciones generales de elasticsearch

class FuncionesElastic
{
	
	public static function MostrarError($data)
	{
		//echo "<pre>";print_r($data);echo "</pre>";die();
		
		if(isset($data['error']) && is_array($data['error']))
		{
			echo self::ProcesarErrores($data['error']);
		}
		else
			print_r($data);
	}
	
	private static function ProcesarErrores($datos)
	{
		$status = "";
		$tipo = "";
		$razon = "Error";
		if(isset($datos['status']) && $datos['status'] != "")
			$status = $datos['status'];
		if(isset($datos['type']) && $datos['type'] != "")
			$tipo = $datos['type'];

		switch($status)
		{
			case 500:
				if(isset($datos['caused_by']['caused_by']['caused_by']['reason']) && $datos['caused_by']['caused_by']['caused_by']['reason'] !="")
					return $datos['caused_by']['caused_by']['caused_by']['reason'];
			default:
				if(isset($datos['reason']) && $datos['reason'] != "")
					$razon = ucfirst($datos['reason']);
				if(isset($datos['line']) && $datos['line'] != "")
					$razon .= " en linea {$datos['line']}";
				if(isset($datos['col']) && $datos['col'] != "")
					$razon .= " columna {$datos['col']}";
				if($tipo != "")
					$razon .= " (".ucfirst($tipo).")";
				$razon .= ".".PHP_EOL;	
				if(isset($datos['caused_by']) && !empty($datos['caused_by']))
					$razon .= " Causa del errror: ".self::ProcesarErrores($datos['caused_by']);
				if(isset($datos['root_cause']) && !empty($datos['root_cause'])){
					foreach($datos['root_cause'] as $key=>$root_cause)
						$razon .= " Error - documento ($key): ".self::ProcesarErrores($root_cause);
				}

				return nl2br($razon);
			break;
		}
			
	}
	
	public static function AbrirIndice($indice)
	{
		$curl = curl_init();
		$urlBase = ELASTICSERVER."/".INDICE.$indice."/_open";
		$header = array("Content-Type: application/json");
		curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false); 
		curl_setopt($curl, CURLOPT_URL, $urlBase);
		curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
		curl_setopt($curl, CURLOPT_POSTFIELDS,"{}");
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); 
		
		//execute post
		$result = curl_exec($curl);
		curl_close ($curl);
		//echo $result."<br/>";
		$data = json_decode($result,true);
		if(!isset($data['acknowledged']) || $data['acknowledged']===false)
		{
			self::MostrarError($data);
			return false;
		}
		
		return true;	

	}
	
	public static function CerrarIndice($indice)
	{
		$curl = curl_init();
		$urlBase = ELASTICSERVER."/".INDICE.$indice."/_close";
		$header = array("Content-Type: application/json");
		curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false); 
		curl_setopt($curl, CURLOPT_URL, $urlBase);
		curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
		curl_setopt($curl, CURLOPT_POSTFIELDS,"{}");
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); 
		
		//execute post
		$result = curl_exec($curl);
		curl_close ($curl);
		//echo $result."<br/>";
		$data = json_decode($result,true);
		if(!isset($data['acknowledged']) || $data['acknowledged']===false)
		{
			self::MostrarError($data);
			return false;
		}
		
		return true;	

	}




    public static function Scroll($datos,&$dataResult,&$ch)
    {
        if(!empty($ch))
            $ch = curl_init();
        $urlBase = ELASTICSERVER."/_search/scroll";
        $datosEnviar = new StdClass;
        $datosEnviar->scroll = $datos['scroll'];
        $datosEnviar->scroll_id = $datos['scroll_id'];

        $dataEnvio = json_encode($datosEnviar);
        //echo $dataEnvio;
        //file_put_contents(PUBLICA."documentos.json",$dataEnvio);



        $header = array("Content-Type: application/json");
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_URL, $urlBase);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS,$dataEnvio);
        $result = curl_exec($ch);
        $dataResult = json_decode($result,1);

        if (isset($dataResult['error']))
        {
            self::MostrarError($dataResult);
            return false;
        }

        return true;
    }

    public static function clearScroll($scroll_id,&$ch)
    {
        if(!empty($ch))
            $ch = curl_init();
        $urlBase = ELASTICSERVER."/_search/scroll/$scroll_id";

        $dataEnvio = "";

        $header = array("Content-Type: application/json");
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_URL, $urlBase);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS,$dataEnvio);
        $result = curl_exec($ch);
        $dataResult = json_decode($result,1);

        if (isset($dataResult['error']))
        {
            self::MostrarError($dataResult);
            return false;
        }

        return true;
    }


    public static function armarQuerySimple($datos)
    {
        $ff = 0;
        $query = new StdClass;
        $query->query = new StdClass;
        $query->query->bool = new StdClass;
        $query->query->bool->filter = array();
        foreach($datos as $campo=>$valor)
        {
            $query->query->bool->filter[$ff] = new StdClass;
            $query->query->bool->filter[$ff]->term = new StdClass;
            $query->query->bool->filter[$ff]->term->$campo = utf8_encode($valor);
            $ff++;
        }
        return $query;
    }
    
    
    public static function VerificarTask($task, &$ch)
    {
    	$urlBase = ELASTICSERVER."/_tasks/$task";
	    $header = array("Content-Type: application/json");
	    curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
	    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	    curl_setopt($ch, CURLOPT_URL, $urlBase);
	    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	    $result = curl_exec($ch);
	    $dataResult = json_decode($result,1);
		return $dataResult['completed'];
	   
    }
}