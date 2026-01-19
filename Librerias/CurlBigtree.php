<?php

class CurlBigtree {

//-----------------------------------------------------------------------------------------
// Genera todas las constantes necesarias para el sistema
    private const SLOW_QUERY_LOG = true;
    private const SLOW_QUERY_SECONDS = 20;

    protected $debug;
    protected $conexion;
    protected $curl;
    protected $url;
    protected $headerCurl;
    protected $CallFunction;
    protected $utf8;
    protected $HttpBuildPost;
    protected $seguridad;

    // Constructor de la clase
    public function __construct() {
        $this->curl = curl_init();
        $this->debug = false;
        $this->headerCurl = [];
        $this->utf8 = false;
        $this->HttpBuildPost = true;
        $this->setSeguridad(false);
    }

    // Destructor de la clase
    public function __destruct() {}

    public function setUrl($url) {
        $this->url = $url;
    }

    public function setHttpBuildPost($valor) {
        $this->HttpBuildPost = $valor;
    }

    public function setUtf8($utf8) {
        $this->utf8 = $utf8;
    }

    public function setDebug($debug) {
        $this->debug = $debug;
    }

    public function CloseCurl() {
        curl_close($this->curl);
    }

    public function setHeader($header) {
        $this->headerCurl = $header;
    }

    public function setFunction($function) {
        $this->CallFunction = $function;
    }


    public function ResetCurl() {
        curl_reset($this->curl);
    }


    /**
     * @param $dataEnviar
     * @param $dataResult
     *
     * @return bool
     * @noinspection t
     */
    public function sendPost($dataEnviar, &$dataResult) {
        if (is_array($dataEnviar) || is_object($dataEnviar))
            $dataEnviar = http_build_query($dataEnviar);
        curl_setopt($this->curl, CURLOPT_URL, $this->url);
        if ($this->headerCurl != "")
            curl_setopt($this->curl, CURLOPT_HTTPHEADER, $this->headerCurl);

        curl_setopt($this->curl, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
        curl_setopt($this->curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($this->curl, CURLOPT_CUSTOMREQUEST, "POST");
        /*{
            curl_setopt($this->curl, CURLOPT_PROXY, "");
            curl_setopt($this->curl, CURLOPT_PROXYPORT, "");
        }*/
        if ($this->usaSeguridad() && defined('ELASTIC_AUTH')) {
            curl_setopt($this->curl, CURLOPT_USERPWD, ELASTIC_AUTH);
            $this->setSeguridad(false);
        }

        curl_setopt($this->curl, CURLOPT_POST, true);
        curl_setopt($this->curl, CURLOPT_VERBOSE, false);

        curl_setopt($this->curl, CURLOPT_TIMEOUT, 300); //times out after 10s

        if ($this->HttpBuildPost)
            curl_setopt($this->curl, CURLOPT_POSTFIELDS, $dataEnviar);
        else
            curl_setopt($this->curl, CURLOPT_POSTFIELDS, $dataEnviar);
        $start = microtime(true);
        // sleep(15);
        $oauthContent = curl_exec($this->curl);//execute the conection
        $oauthHttpcode = curl_getinfo($this->curl, CURLINFO_HTTP_CODE);//status
        $this->slowLog($start, $this->url, $dataEnviar, 'post');
        if ($this->debug) {
            $header = implode('\' -H\'', $this->headerCurl);
            echo "curl -H'$header' -XPOST '{$this->url}' -d'$dataEnviar'\n\n$oauthHttpcode\n";

            var_dump($oauthContent);
        }

        if ($oauthHttpcode != 200 && $oauthHttpcode != 201) {
            switch ($oauthHttpcode) {
                case 400:
                case 401:
                case 404:
                    $dataResult = json_decode($oauthContent, 1);
                    return false;
                    break;
                default:
                    $dataResult['error_description'] = "Ha ocurrido un error interno (cod. 0001-" . $oauthHttpcode . ").";
                    $dataResult['error'] = "json_convert";
                    return false;
                    break;
            }
        }

        $dataResult = json_decode($oauthContent, 1);

        if ($dataResult === false || $dataResult === null) {
            $dataResult['error_description'] = "Ha ocurrido un error interno.";
            $dataResult['error'] = "json_convert";
            $errorData = $this->ErrorJson();
            if ($this->debug)
                echo $errorData;
            $errorCarga = preg_replace("/[\r\n|\n|\r]+/", " ", $oauthContent);
            $log = "IP: " . $_SERVER['REMOTE_ADDR'] . ' - ' . date("d/m/yyyy H:i:s") . PHP_EOL .
                "Texto: " . ($errorCarga) . PHP_EOL .
                "ErrorJson: " . $errorData . PHP_EOL .
                "Funcion: " . (__FUNCTION__) . PHP_EOL;
            if ($this->CallFunction != "")
                $log .= "FuncionInstancia: " . $this->CallFunction . PHP_EOL;
            if (isset($_SESSION['usuariocod']))
                $log .= "Usuario: " . $_SESSION['usuariocod'] . PHP_EOL;
            $log .= "File: " . $_SERVER['PHP_SELF'] . PHP_EOL .
                "-------------------------" . PHP_EOL;
            //Save string to log, use FILE_APPEND to append.
            file_put_contents(DIR_ROOT . 'error_logs/log_servicios_' . date("Ymd") . '.txt', $log, FILE_APPEND);
            return false;
        }
        return true;

    }

    public function sendPostFiles($dataEnviar, &$dataResult) {
        curl_setopt($this->curl, CURLOPT_URL, $this->url);
        if ($this->headerCurl != "")
            curl_setopt($this->curl, CURLOPT_HTTPHEADER, $this->headerCurl);
        curl_setopt($this->curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($this->curl, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($this->curl, CURLOPT_POST, true);
        curl_setopt($this->curl, CURLOPT_VERBOSE, false);
        curl_setopt($this->curl, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
        curl_setopt($this->curl, CURLOPT_TIMEOUT, 50); //times out after 10s
        if (!UTILIZAPROXY) {
            curl_setopt($this->curl, CURLOPT_PROXY, PROXY);
            curl_setopt($this->curl, CURLOPT_PROXYPORT, PROXYPORT);
        }
        //curl_setopt($this->curl, CURLOPT_POSTFIELDS, http_build_query($dataEnviar));
        $datosFilesEnviar = $this->http_build_query_bigtree_file($dataEnviar);
        curl_setopt($this->curl, CURLOPT_POSTFIELDS, $datosFilesEnviar);
        $oauthContent = curl_exec($this->curl);//execute the conection
        if ($this->debug)
            echo $oauthContent;

        $oauthHttpcode = curl_getinfo($this->curl, CURLINFO_HTTP_CODE);//status
        if ($oauthHttpcode != 200) {
            $dataResult['error_description'] = "Ha ocurrido un error interno (cod. 0001-" . $oauthHttpcode . ").";
            $dataResult['error'] = "json_convert";
            return false;
        }
        $dataResult = json_decode($oauthContent, 1);
        if ($dataResult === false || $dataResult === null) {
            $dataResult['error_description'] = "Ha ocurrido un error interno.";
            $dataResult['error'] = "json_convert";
            $errorData = $this->ErrorJson();
            if ($this->debug)
                echo $errorData;
            $errorCarga = preg_replace("/[\r\n|\n|\r]+/", " ", $oauthContent);
            $log = "IP: " . $_SERVER['REMOTE_ADDR'] . ' - ' . date("d/m/yyyy H:i:s") . PHP_EOL .
                "Texto: " . ($errorCarga) . PHP_EOL .
                "ErrorJson: " . $errorData . PHP_EOL .
                "Funcion: " . (__FUNCTION__) . PHP_EOL;
            if ($this->CallFunction != "")
                $log .= "FuncionInstancia: " . $this->CallFunction . PHP_EOL;
            if (isset($_SESSION['usuariocod']))
                $log .= "Usuario: " . $_SESSION['usuariocod'] . PHP_EOL;
            $log .= "File: " . $_SERVER['PHP_SELF'] . PHP_EOL .
                "-------------------------" . PHP_EOL;
            //Save string to log, use FILE_APPEND to append.
            file_put_contents(DIR_ROOT . 'error_logs/log_servicios_' . date("Ymd") . '.txt', $log, FILE_APPEND);
            return false;
        }

        return true;

    }

    /**
     * @param $url
     * @param $dataResult
     * @param $filterData
     *
     * @return bool
     * @noinspection t
     */
    public function sendGet($url, &$dataResult, $filterData = null) {
        curl_setopt($this->curl, CURLOPT_URL, $this->url . $url);
        if ($this->headerCurl != "")
            curl_setopt($this->curl, CURLOPT_HTTPHEADER, $this->headerCurl);
        if ($this->debug) {
            $fullUrl = $this->url . $url;
            $header = implode('\' -H\'', $this->headerCurl);
            $d = empty($filterData) ? '' : "-d'$filterData'";
            echo "<br/>curl -H'$header' -XGET $fullUrl $d<br/>";
            unset($header, $fullUrl);
        }
        curl_setopt($this->curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($this->curl, CURLOPT_VERBOSE, false);
        curl_setopt($this->curl, CURLOPT_CUSTOMREQUEST, "GET");
        curl_setopt($this->curl, CURLOPT_TIMEOUT, 50); //times out after 10s
        curl_setopt($this->curl, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
        //curl_setopt($this->curl, CURLOPT_HTTPHEADER, $this->headerCurl);
        if (!empty($filterData))
            curl_setopt($this->curl, CURLOPT_POSTFIELDS, $filterData);
        if (!UTILIZAPROXY) {
            curl_setopt($this->curl, CURLOPT_PROXY, PROXY);
            curl_setopt($this->curl, CURLOPT_PROXYPORT, PROXYPORT);
        }
        if ($this->usaSeguridad() && defined('ELASTIC_AUTH')) {
            curl_setopt($this->curl, CURLOPT_USERPWD, ELASTIC_AUTH);
            $this->setSeguridad(false);
        }
        $start = microtime(true);
        $oauthContent = curl_exec($this->curl);//execute the conection
        $oauthHttpcode = curl_getinfo($this->curl, CURLINFO_HTTP_CODE);//status
        $this->slowLog($start, $this->url . $url, '', 'get');
        if ($this->debug) {
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
                "Url: " . $this->url . $url . PHP_EOL .
                "Funcion: " . (__FUNCTION__) . PHP_EOL;
            if ($this->CallFunction != "")
                $log .= "FuncionInstancia: " . $this->CallFunction . PHP_EOL;
            if (isset($_SESSION['usuariocod']))
                $log .= "Usuario: " . $_SESSION['usuariocod'] . PHP_EOL;
            $log .= "File: " . $_SERVER['PHP_SELF'] . PHP_EOL .
                "-------------------------" . PHP_EOL;
            //Save string to log, use FILE_APPEND to append.
            file_put_contents(DIR_ROOT . 'error_logs/log_servicios_' . date("Ymd") . '.txt', $log, FILE_APPEND);
            return false;
        }
        if ($this->utf8 == true)
            $oauthContent = utf8_encode($oauthContent);
        $dataResult = json_decode($oauthContent, 1);
        if ($dataResult === false || $dataResult === null) {
            $dataResult['error_description'] = "Ha ocurrido un error interno.";
            $dataResult['error'] = "json_convert";
            $errorData = $this->ErrorJson();
            if ($this->debug)
                echo $errorData;
            $errorCarga = preg_replace("/[\r\n|\n|\r]+/", " ", $oauthContent);
            $log = "IP: " . $_SERVER['REMOTE_ADDR'] . ' - ' . date("d/m/yyyy H:i:s") . PHP_EOL .
                "Texto: " . ($errorCarga) . PHP_EOL .
                "ErrorJson: " . $errorData . PHP_EOL .
                "Funcion: " . (__FUNCTION__) . PHP_EOL;
            if ($this->CallFunction != "")
                $log .= "FuncionInstancia: " . $this->CallFunction . PHP_EOL;
            if (isset($_SESSION['usuariocod']))
                $log .= "Usuario: " . $_SESSION['usuariocod'] . PHP_EOL;
            $log .= "File: " . $_SERVER['PHP_SELF'] . PHP_EOL .
                "-------------------------" . PHP_EOL;
            //Save string to log, use FILE_APPEND to append.
            file_put_contents(DIR_ROOT . 'error_logs/log_servicios_' . date("Ymd") . '.txt', $log, FILE_APPEND);
            return false;
        }

        return true;

    }

    /**
     * @param $dataEnviar
     * @param $dataResult
     *
     * @return bool
     * @noinspection t
     */
    public function sendPut($dataEnviar, &$dataResult) {

        if ($this->debug) {
            $header = implode('\' -H\'', $this->headerCurl);
            echo "<br/>curl -H'$header' -X PUT {$this->url} -d'{$dataEnviar}'<br/>";
            unset($header);
        }
        if (is_array($dataEnviar) || is_object($dataEnviar))
            $dataEnviar = http_build_query($dataEnviar);
        curl_setopt($this->curl, CURLOPT_URL, $this->url);
        curl_setopt($this->curl, CURLOPT_HTTPHEADER, $this->headerCurl);
        curl_setopt($this->curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($this->curl, CURLOPT_CUSTOMREQUEST, "PUT");
        curl_setopt($this->curl, CURLOPT_VERBOSE, false);
        curl_setopt($this->curl, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
        curl_setopt($this->curl, CURLOPT_TIMEOUT, 50); //times out after 10s
        curl_setopt($this->curl, CURLOPT_POSTFIELDS, $dataEnviar);
        curl_setopt($this->curl, CURLOPT_POST, 1);
        if (!UTILIZAPROXY) {
            curl_setopt($this->curl, CURLOPT_PROXY, PROXY);
            curl_setopt($this->curl, CURLOPT_PROXYPORT, PROXYPORT);
        }
        $start = microtime(true);
        $oauthContent = curl_exec($this->curl);//execute the conection
        if ($this->debug)
            echo $oauthContent;
        $oauthHttpcode = curl_getinfo($this->curl, CURLINFO_HTTP_CODE);//status
        $this->slowLog($start, $this->url, $dataEnviar, 'put');
        if ($oauthHttpcode != 200) {
            switch ($oauthHttpcode) {
                case 400:
                case 401:
                case 403:
                case 404:
                case 405:
                case 422:
                    if (!empty($oauthContent)) {
                        $dataResult = json_decode($oauthContent, 1);
                    } else {
                        $dataResult['error_description'] = "Ha ocurrido un error interno (cod. 0001-" . $oauthHttpcode . ").";
                        $dataResult['error'] = "json_convert";
                    }
                    return false;
                    break;
                default:
                    $dataResult['error_description'] = "Ha ocurrido un error interno (cod. 0001-" . $oauthHttpcode . ").";
                    $dataResult['error'] = "json_convert";
                    return false;
                    break;
            }
        }

        $dataResult = json_decode($oauthContent, 1);

        if ($dataResult === false || $dataResult === null) {
            $dataResult['error_description'] = "Ha ocurrido un error interno.";
            $dataResult['error'] = "json_convert";
            $errorData = $this->ErrorJson();
            if ($this->debug)
                echo $errorData;
            $errorCarga = preg_replace("/[\r\n|\n|\r]+/", " ", $oauthContent);
            $log = "IP: " . $_SERVER['REMOTE_ADDR'] . ' - ' . date("d/m/yyyy H:i:s") . PHP_EOL .
                "Texto: " . ($errorCarga) . PHP_EOL .
                "ErrorJson: " . $errorData . PHP_EOL .
                "Funcion: " . (__FUNCTION__) . PHP_EOL;
            if ($this->CallFunction != "")
                $log .= "FuncionInstancia: " . $this->CallFunction . PHP_EOL;
            if (isset($_SESSION['usuariocod']))
                $log .= "Usuario: " . $_SESSION['usuariocod'] . PHP_EOL;
            $log .= "File: " . $_SERVER['PHP_SELF'] . PHP_EOL .
                "-------------------------" . PHP_EOL;
            //Save string to log, use FILE_APPEND to append.
            file_put_contents(DIR_ROOT . 'error_logs/log_servicios_' . date("Ymd") . '.txt', $log, FILE_APPEND);
            return false;
        }

        return true;

    }

    /**
     * @param $dataEnviar
     * @param $dataResult
     *
     * @return bool
     * @noinspection t
     */
    public function sendPatch($dataEnviar, &$dataResult) {
        if (is_array($dataEnviar) || is_object($dataEnviar))
            $dataEnviar = http_build_query($dataEnviar);
        if ($this->debug) {
            $header = implode('\' -H\'', $this->headerCurl);
            echo "<br/>curl -H'$header' -X PATCH {$this->url} -d'{$dataEnviar}'<br/>";
            unset($header);
        }
        curl_setopt($this->curl, CURLOPT_URL, $this->url);
        curl_setopt($this->curl, CURLOPT_HTTPHEADER, $this->headerCurl);
        curl_setopt($this->curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($this->curl, CURLOPT_CUSTOMREQUEST, "PATCH");
        curl_setopt($this->curl, CURLOPT_VERBOSE, false);
        curl_setopt($this->curl, CURLOPT_TIMEOUT, 50); //times out after 10s
        curl_setopt($this->curl, CURLOPT_POSTFIELDS, $dataEnviar);
        curl_setopt($this->curl, CURLOPT_POST, 1);
        curl_setopt($this->curl, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
        if (!UTILIZAPROXY) {
            curl_setopt($this->curl, CURLOPT_PROXY, PROXY);
            curl_setopt($this->curl, CURLOPT_PROXYPORT, PROXYPORT);
        }
        $start = microtime(true);
        $oauthContent = curl_exec($this->curl);//execute the conection
        if ($this->debug)
            echo $oauthContent;
        $oauthHttpcode = curl_getinfo($this->curl, CURLINFO_HTTP_CODE);//status
        $this->slowLog($start, $this->url, $dataEnviar, 'patch');
        if ($oauthHttpcode != 200) {
            switch ($oauthHttpcode) {
                case 400:
                case 401:
                    $dataResult = json_decode($oauthContent, 1);
                    return false;
                    break;
                default:
                    $dataResult['error_description'] = "Ha ocurrido un error interno (cod. 0001-" . $oauthHttpcode . ").";
                    $dataResult['error'] = "json_convert";
                    return false;
                    break;
            }
        }

        $dataResult = json_decode($oauthContent, 1);
        if ($dataResult === false || $dataResult === null) {
            $dataResult['error_description'] = "Ha ocurrido un error interno.";
            $dataResult['error'] = "json_convert";
            $errorData = $this->ErrorJson();
            if ($this->debug)
                echo $errorData;
            $errorCarga = preg_replace("/[\r\n|\n|\r]+/", " ", $oauthContent);
            $log = "IP: " . $_SERVER['REMOTE_ADDR'] . ' - ' . date("d/m/yyyy H:i:s") . PHP_EOL .
                "Texto: " . ($errorCarga) . PHP_EOL .
                "ErrorJson: " . $errorData . PHP_EOL .
                "Funcion: " . (__FUNCTION__) . PHP_EOL;
            if ($this->CallFunction != "")
                $log .= "FuncionInstancia: " . $this->CallFunction . PHP_EOL;
            if (isset($_SESSION['usuariocod']))
                $log .= "Usuario: " . $_SESSION['usuariocod'] . PHP_EOL;
            $log .= "File: " . $_SERVER['PHP_SELF'] . PHP_EOL .
                "-------------------------" . PHP_EOL;
            //Save string to log, use FILE_APPEND to append.
            file_put_contents(DIR_ROOT . 'error_logs/log_servicios_' . date("Ymd") . '.txt', $log, FILE_APPEND);
            return false;
        }

        return true;

    }

    public function sendDelete($dataEnviar, &$dataResult) {
        if (is_array($dataEnviar) || is_object($dataEnviar))
            $dataEnviar = http_build_query($dataEnviar);

        $curlRequest = 'curl -H\'' .
            implode('\' -H\'', $this->headerCurl) .
            "' -XDELETE '{$this->url}' -d'$dataEnviar'";

        curl_setopt($this->curl, CURLOPT_URL, $this->url);
        curl_setopt($this->curl, CURLOPT_HTTPHEADER, $this->headerCurl);
        curl_setopt($this->curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($this->curl, CURLOPT_CUSTOMREQUEST, "DELETE");
        curl_setopt($this->curl, CURLOPT_VERBOSE, true);
        curl_setopt($this->curl, CURLOPT_TIMEOUT, 1000); //times out after 10s
        curl_setopt($this->curl, CURLOPT_POSTFIELDS, $dataEnviar);
        curl_setopt($this->curl, CURLOPT_POST, 1);
        $start = microtime(true);
        $oauthContent = curl_exec($this->curl);//execute the conection
        $oauthHttpcode = curl_getinfo($this->curl, CURLINFO_HTTP_CODE);//status
        $this->slowLog($start, $this->url, $dataEnviar, 'delete');
        if ($this->debug) {
            echo $curlRequest . PHP_EOL;
            var_dump($oauthContent);
        }
        $oauthHttpcode = curl_getinfo($this->curl, CURLINFO_HTTP_CODE);//status

        if (!self::processResponse($oauthHttpcode, $oauthContent, $dataResult, $curlRequest))
            return false;

        return true;

    }


    public function http_build_query_bigtree($array, $qs = '') {
        foreach ($array as $par => $val) {
            if (is_array($val)) {
                $this->http_build_query_bigtree($val, $qs);
            } else {
                $qs .= $par . '=' . $val . '&';
            }
        }
        return $qs;
    }


    private function http_build_query_bigtree_file($data) {
        if (!is_array($data)) {
            return $data;
        }
        foreach ($data as $key => $val) {
            if (is_array($val)) {
                foreach ($val as $k => $v) {
                    if (is_array($v)) {
                        $data = array_merge($data, $this->http_build_query_bigtree_file(["{$key}[{$k}]" => $v]));
                    } else {
                        $data["{$key}[{$k}]"] = $v;
                    }
                }
                unset($data[$key]);
            }
        }
        return $data;
    }


    private function ErrorJson() {
        $varError = "";

        switch (json_last_error()) {
            case JSON_ERROR_NONE:
                $varError = 'Sin errores';
                break;
            case JSON_ERROR_DEPTH:
                $varError = 'Excedido tama�o m�ximo de la pila';
                break;
            case JSON_ERROR_STATE_MISMATCH:
                $varError = 'Desbordamiento de buffer o los modos no coinciden';
                break;
            case JSON_ERROR_CTRL_CHAR:
                $varError = 'Encontrado car�cter de control no esperado';
                break;
            case JSON_ERROR_SYNTAX:
                $varError = 'Error de sintaxis, JSON mal formado';
                break;
            case JSON_ERROR_UTF8:
                $varError = 'Caracteres UTF-8 malformados, posiblemente est�n mal codificados';
                break;
            default:
                $varError = 'Error desconocido';
                break;
        }
        return $varError;
    }

    /**
     * @return bool
     */
    public function usaSeguridad(): bool {
        return $this->seguridad;
    }

    /**
     * @param bool $seguridad
     */
    public function setSeguridad($seguridad = true): void {
        $this->seguridad = $seguridad;
    }


    /**
     * @param int         $oauthHttpCode
     * @param string      $oauthContent
     * @param array|null  $dataResult
     * @param string|null $request
     *
     * @return bool
     */
    private static function processResponse(int $oauthHttpCode, string $oauthContent, ?array &$dataResult, ?string $request = ''): bool {
        switch ($oauthHttpCode) {
            case 200:
            case 201:
                $dataResult = json_decode($oauthContent, true);
                return true;
            case 400:
            case 401:
            case 402:
            case 404:
            case 405:
            case 409:
            case 415:
                $dataResult = json_decode($oauthContent, true);
                $dataResult['error_msg'] = $oauthContent;
                break;
            case 500:
            default:
                $dataResult['error_description'] = "Ha ocurrido un error con el servicio (cod. {$oauthHttpCode}).";
                $dataResult['error'] = $oauthHttpCode;
                $dataResult['error_msg'] = $oauthContent;
                self::logError($dataResult, $request);
                break;
        }
        return false;
    }

    /**
     * @param array  $errorMsg
     * @param string $request
     */
    private static function logError(array $errorMsg, string $request): void {

        $fecha = date('d/m/Y H:i:s');
        $log = "IP: {$_SERVER['REMOTE_ADDR']} - $fecha" . PHP_EOL .
            "Request: $request" . PHP_EOL .
            "Codigo de respuesta: " . ($errorMsg['error']) . PHP_EOL .
            "Texto: " . ($errorMsg['error_description']) . PHP_EOL .
            "ErrorJson: " . $errorMsg['error_msg'] . PHP_EOL .
            "Funcion: " . (__FUNCTION__) . PHP_EOL;
        $log .= "File: {$_SERVER['PHP_SELF']}" . PHP_EOL .
            "-------------------------" . PHP_EOL;
        //Save string to log, use FILE_APPEND to append.
        if (!file_exists(DIR_ROOT . 'error_logs'))
            mkdir(DIR_ROOT . 'error_logs');
        file_put_contents(DIR_ROOT . 'error_logs/log_servicios_' . date("Ymd") . '.txt', $log, FILE_APPEND);
    }

    /**
     * @param             $start
     * @param             $url
     * @param string|null $cuerpo
     * @param string      $lcMethod
     *
     * @return void
     */
    protected function slowLog($start, $url, ?string $cuerpo, string $lcMethod): void {
        if (!self::SLOW_QUERY_LOG) {
            return;
        }
        $segundos = microtime(true) - $start;
        if ($segundos > self::SLOW_QUERY_SECONDS) {
            if (!in_array($lcMethod, ['get', 'post', 'put', 'delete', 'head', 'patch'])) {
                $lcMethod = 'get';
            }
            $ucMethod = strtoupper($lcMethod);
            $dir = ltrim(PATH_STORAGE, '/') . '/log/docentes';
            if (!file_exists($dir)) {
                @mkdir($dir, 0755, true);
            }
            $file = $dir . "/curl_{$lcMethod}_" . date('Ymd') . '.log';
            $pathName = $url;
            file_put_contents(
                $file,
                "La siguiente consulta tard� $segundos segundos.\n$ucMethod $pathName\n" .
                ($cuerpo ?? '') .
                PHP_EOL .
                '------------------------------------------------------------------------------------------' .
                PHP_EOL,
                FILE_APPEND
            );
        }
    }


} // Fin clase FuncionesPHPLocal
