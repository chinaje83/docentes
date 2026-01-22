<?php

class cServiciosDDJJCargosEducativos
{
    use ManejoErrores;
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
        $this->Utf8 = true;
    }

    public function __destruct() {
        $this->oCurl->CloseCurl();
        unset($this->oCurl);
    }

    public function getCurl()
    {
        return 	$this->oCurl;
    }

    public function CodificarUtf8()
    {
        $this->Utf8 = true;
    }

    public function buscarListado($datos, ?array &$resultado)
    {
        $url = 'v1/mis-cargos-educativos?';

        $urlAnexa = [];

        if (isset($datos['page']) && $datos['page'] != "")
            $urlAnexa['page'] = $datos['page'];

        if (isset($datos['rows']) && $datos['rows'] != "")
            $urlAnexa['rows'] = $datos['rows'];

        if (isset($datos['sidx']) && $datos['sidx'] != "")
            $urlAnexa['sidx'] = $datos['sidx'];

        if (isset($datos['sord']) && $datos['sord'] != "")
            $urlAnexa['sord'] = $datos['sord'];

        $url .= http_build_query($urlAnexa);
        $header = ["Authorization: Bearer {$_SESSION['token']}"];
        $header[] = 'Content-Type: application/json';
        $this->oCurl->setUrl(APISSO);
        $this->oCurl->setFunction(get_class($this) . "-" . __FUNCTION__);
        $this->oCurl->setHeader($header);
        $this->oCurl->setDebug(false);
        if (!$this->oCurl->sendGet($url, $dataResult)) {
            $this->setError("Error", "Ocurrió un error al buscar cargos educativos");
            return false;
        }
        if (!$this->Utf8)
            $resultado = FuncionesPHPLocal::DecodificarUtf8($dataResult);
        else
            $resultado = $dataResult;

        return true;
    }

    public function Descargar(&$dataResult)
    {
        $url = "v1/mis-cargos-educativos/descargar";
        $header = ["Authorization: Bearer {$_SESSION['token']}"];
        $urlBase = APISSO;
        $curl = curl_init();
        $debug = false;
        $callFunction = get_class($this) . "-" . __FUNCTION__;

        curl_setopt($curl, CURLOPT_URL, $urlBase . $url);
        if ($header != "")
            curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
        if ($debug) {
            $fullUrl = $urlBase . $url;
            $header = implode('\' -H\'', $header);
            $d = empty($filterData) ? '' : "-d'$filterData'";
            echo "<br/>curl -H'$header' -XGET $fullUrl $d<br/>";
            unset($header, $fullUrl);
        }

        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_VERBOSE, false);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "GET");
        curl_setopt($curl, CURLOPT_TIMEOUT, 50); //times out after 10s
        curl_setopt($curl, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);

        //curl_setopt($curl, CURLOPT_HTTPHEADER, $this->headerCurl);
        if (!empty($filterData))
            curl_setopt($curl, CURLOPT_POSTFIELDS, $filterData);
        if (!UTILIZAPROXY) {
            curl_setopt($curl, CURLOPT_PROXY, PROXY);
            curl_setopt($curl, CURLOPT_PROXYPORT, PROXYPORT);
        }

        $start = microtime(true);
        $oauthContent = curl_exec($curl);//execute the conection
        $oauthHttpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);//status
        //$this->oCurl->slowLog($start, $urlBase . $url, '', 'get');
        if ($debug) {
            var_dump($oauthHttpcode);
            echo '<br/>';
            var_dump($oauthContent);
        }
        if ($oauthHttpcode != 200) {

            $dataResult['error_description'] = "Ha ocurrido un error interno (cod. 0001-" . $oauthHttpcode . ").";
            $dataResult['error'] = "url_inaccesible";
            $errorCarga = preg_replace("/[\r\n]+/", " ", $oauthContent);
            $log = "IP: " . $_SERVER['REMOTE_ADDR'] . ' - ' . date("d/m/yyyy H:i:s") . PHP_EOL .
                "Texto: " . ($errorCarga) . PHP_EOL .
                "Url: " . $urlBase . $url . PHP_EOL .
                "Funcion: " . (__FUNCTION__) . PHP_EOL;
            if ($callFunction != "")
                $log .= "FuncionInstancia: " . $callFunction . PHP_EOL;
            if (isset($_SESSION['usuariocod']))
                $log .= "Usuario: " . $_SESSION['usuariocod'] . PHP_EOL;
            $log .= "File: " . $_SERVER['PHP_SELF'] . PHP_EOL .
                "-------------------------" . PHP_EOL;
            //Save string to log, use FILE_APPEND to append.
            file_put_contents(DIR_ROOT . 'error_logs/log_servicios_' . date("Ymd") . '.txt', $log, FILE_APPEND);

            return false;
        }

        $dataResult = $oauthContent;

        if ($dataResult === false || $dataResult === null) {

            $dataResult['error_description'] = "Ha ocurrido un error interno.";
            $dataResult['error'] = "json_convert";
            $errorData = $curl->ErrorJson();
            if ($debug)
                echo $errorData;
            $errorCarga = preg_replace("/[\r\n|\n|\r]+/", " ", $oauthContent);
            $log = "IP: " . $_SERVER['REMOTE_ADDR'] . ' - ' . date("d/m/yyyy H:i:s") . PHP_EOL .
                "Texto: " . ($errorCarga) . PHP_EOL .
                "ErrorJson: " . $errorData . PHP_EOL .
                "Funcion: " . (__FUNCTION__) . PHP_EOL;
            if ($callFunction != "")
                $log .= "FuncionInstancia: " . $callFunction . PHP_EOL;
            if (isset($_SESSION['usuariocod']))
                $log .= "Usuario: " . $_SESSION['usuariocod'] . PHP_EOL;
            $log .= "File: " . $_SERVER['PHP_SELF'] . PHP_EOL .
                "-------------------------" . PHP_EOL;

            //Save string to log, use FILE_APPEND to append.
            file_put_contents(DIR_ROOT . 'error_logs/log_servicios_' . date("Ymd") . '.txt', $log, FILE_APPEND);

            if ($this->utf8 == true)
                $oauthContent = utf8_encode($oauthContent);
            $dataResult = json_decode($oauthContent, 1);

            return false;
        }

        return true;
    }


}
