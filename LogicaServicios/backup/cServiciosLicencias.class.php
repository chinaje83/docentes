<?php 
class cServiciosLicencias
{

	protected $formato;
	protected $conexion;
	protected $error;
	protected $Utf8;
	
	
	function __construct($conexion,$formato=FMT_TEXTO){
		$this->conexion = &$conexion;
		$this->formato = $formato;
 		$this->error = array();
		$this->ch = curl_init();
		$this->Utf8 = false;

	}
	public function __destruct() {	
		curl_close($this->ch);
    } 	

	public function getCurl()
	{
		return 	$this->ch;
	}

	public function CodificarUtf8()
	{
		$this->Utf8 = true;
	}




    public function getLicencias($datos,&$resultJson)
    {


        $urlBase = URLAPILICENCIAS."reallastactivities";

        $urlBase .= "?dateStart=".$datos['dateStart'];
        $urlBase .= "&dateEnd=".$datos['dateEnd'];
        $urlBase .= "&page=".$datos['page'];
        $urlBase .= "&pageSize=".$datos['pageLimit'];

        //$urlBase .= "?dateEnd=1645898600000&dateStart=12";

        $dataEnvio = array();
        $header = array();
        $header[] = 'x-api-key:'.APIKEY;

        curl_setopt($this->ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($this->ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($this->ch, CURLOPT_URL, $urlBase);
        curl_setopt($this->ch, CURLOPT_CUSTOMREQUEST, "GET");
        curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($this->ch, CURLOPT_POSTFIELDS,$dataEnvio);
        $result = curl_exec($this->ch);
        $resultJson = json_decode($result,1);
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



    public function getLicenciasFechas($datos,&$resultJson)
    {


        $urlBase = URLAPILICENCIAS."filter_creation";

        $urlBase .= "?dateEnd=".urlencode($datos['dateEnd']);
        $urlBase .= "&dateStart=".urlencode($datos['dateStart']);
        $urlBase .= "&page=".$datos['page'];
        $urlBase .= "&pageSize=".$datos['pageLimit'];

        //$urlBase .= "?dateEnd=1645898600000&dateStart=12";

        $dataEnvio = array();
        $header = array();
        $header[] = 'x-api-key: '.APIKEY;

        //echo APIKEY;

        curl_setopt($this->ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($this->ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($this->ch, CURLOPT_URL, $urlBase);
        curl_setopt($this->ch, CURLOPT_CUSTOMREQUEST, "GET");
        curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($this->ch, CURLOPT_POSTFIELDS,$dataEnvio);
        $result = curl_exec($this->ch);

        $resultJson = json_decode($result,1);
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


	public function getLicenciaByTaskID($taskID,&$resultJson)
	{

		
		$urlBase = URLAPILICENCIAS."find-tasks";
		
		$urlBase .= "?taskid=".$taskID;
		
		//$urlBase .= "?dateEnd=1645898600000&dateStart=12";
		
		$dataEnvio = array();
		$header = array();
		$header[] = 'x-api-key:'.APIKEY;
		
		curl_setopt($this->ch, CURLOPT_HTTPHEADER, $header);
		curl_setopt($this->ch, CURLOPT_SSL_VERIFYPEER, false); 
		curl_setopt($this->ch, CURLOPT_URL, $urlBase);
		curl_setopt($this->ch, CURLOPT_CUSTOMREQUEST, "GET");
		curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, 1); 
		curl_setopt($this->ch, CURLOPT_POSTFIELDS,$dataEnvio);
		$result = curl_exec($this->ch);
		$resultJson = json_decode($result,1);
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
		
		echo $resultJson;
		return true;
	}

    public function getLicenciaByCuil($cuil,&$resultJson)
    {


        $urlBase = URLAPILICENCIAS."tasks";

        $urlBase .= "?cuil=".$cuil;

        //$urlBase .= "?dateEnd=1645898600000&dateStart=12";


        $dataEnvio = array();
        $header = array();
        $header[] = 'x-api-key:'.APIKEY;

        curl_setopt($this->ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($this->ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($this->ch, CURLOPT_URL, $urlBase);
        curl_setopt($this->ch, CURLOPT_CUSTOMREQUEST, "GET");
        curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($this->ch, CURLOPT_POSTFIELDS,$dataEnvio);
        $result = curl_exec($this->ch);
        $resultJson = json_decode($result,1);
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

	public function GetError()
	{
		return $this->error;	
	}
	private function SetError($error,$errordesc="")
	{
		$this->error['error']=$error;	
		$this->error['errordesc']=$errordesc;	
	}

	
	
	
}//FIN CLASE

?>