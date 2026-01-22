<?php

class cServiciosFamiliares {
    use ManejoErrores;

    protected $oCurl;
    protected $conexion;
    protected $error;
    protected $Utf8;

    protected $MemCache;

    const MemCacheExpire = 86400;// 1 dia

    function __construct($conexion) {
        $this->conexion = &$conexion;
        $this->error = [];
        $this->oCurl = new CurlBigtree();
        $this->Utf8 = false;

    }

    public function __destruct() {
        $this->oCurl->CloseCurl();
        unset($this->oCurl);
    }

    public function getCurl() {
        return $this->oCurl;
    }

    public function CodificarUtf8() {
        $this->Utf8 = true;
    }

    public function ObtenerFamiliarxId($datos) {
        $url = "v1/familiares/" . $datos['Id'];

        $header = ["Authorization: Bearer {$_SESSION['token']}"];
        $this->oCurl->setUrl(APISSO);
        $this->oCurl->setFunction(get_class($this) . "-" . __FUNCTION__);
        $this->oCurl->setHeader($header);
        $this->oCurl->setDebug(false);

        if (!$this->oCurl->sendGet($url, $dataResult)) {
            $this->setError("Error", "Ocurrió un error al buscar el usuario por código");
            return false;
        }

        if (!$this->Utf8)
            $array = FuncionesPHPLocal::DecodificarUtf8($dataResult);
        else
            $array = $dataResult;
        return $array;
    }

    public function buscarListado(array $datos, ?array &$resultado) {
        $url = 'v1/familiares?';

        $urlAnexa = [];
        $cuerpo = new stdClass();

        if (isset($datos['page']) && $datos['page'] != "")
            $urlAnexa['page'] = $datos['page'];

        if (isset($datos['rows']) && $datos['rows'] != "")
            $urlAnexa['rows'] = $datos['rows'];


        if (isset($datos['sidx']) && $datos['sidx'] != "")
            $urlAnexa['sidx'] = $datos['sidx'];


        if (isset($datos['sord']) && $datos['sord'] != "")
            $urlAnexa['sord'] = $datos['sord'];

        if (isset($datos['Id']) && $datos['Id'] != '')
            $cuerpo->Id = (int)$datos['Id'];

        if (isset($datos['IdPersona']) && $datos['IdPersona'] != '')
            $cuerpo->IdPersona = (int)$datos['IdPersona'];

        if (isset($datos['Nombre']) && $datos['Nombre'] != '')
            $cuerpo->Nombre = $datos['Nombre'];

        if (isset($datos['Apellido']) && $datos['Apellido'] != '')
            $cuerpo->Apellido = $datos['Apellido'];

        if (isset($datos['Dni']) && $datos['Dni'] != '')
            $cuerpo->Aplicacion = $datos['Dni'];

        if (isset($datos['IdParentesco']) && $datos['IdParentesco'] != '')
            $cuerpo->IdParentesco = (int)$datos['IdParentesco'];

        if (isset($datos['IdEstado']) && $datos['IdEstado'] != '')
            $cuerpo->IdEstado = $datos['IdEstado'];

        if (isset($datos['Estado']) && $datos['Estado'] != '')
            $cuerpo->Estado = $datos['Estado'];


        $url .= http_build_query($urlAnexa);

        $header = ["Authorization: Bearer {$_SESSION['token']}"];
        $header[] = 'Content-Type: application/json';
        $this->oCurl->setUrl(APISSO);
        $this->oCurl->setFunction(get_class($this) . "-" . __FUNCTION__);
        $this->oCurl->setHeader($header);
        $this->oCurl->setDebug(false);
        if (!$this->oCurl->sendGet($url, $dataResult, json_encode($cuerpo))) {
            $this->setError("Error", "Ocurrió un error al buscar familiares");
            return false;
        }

        if (!$this->Utf8)
            $resultado = FuncionesPHPLocal::DecodificarUtf8($dataResult);
        else
            $resultado = $dataResult;

        return true;
    }

    public function obtenerMisFamiliares(&$resultado) {
        $url = 'v1/mis-familiares';

        $header = ["Authorization: Bearer {$_SESSION['token']}"];
        $header[] = 'Content-Type: application/json';
        $this->oCurl->setUrl(APISSO);
        $this->oCurl->setFunction(get_class($this) . "-" . __FUNCTION__);
        $this->oCurl->setHeader($header);
        $this->oCurl->setDebug(false);
        if (!$this->oCurl->sendGet($url, $dataResult)) {
            $this->setError("Error", "Ocurrió un error al buscar familiares");
            return false;
        }
        if (!$this->Utf8)
            $resultado = FuncionesPHPLocal::DecodificarUtf8($dataResult);
        else
            $resultado = $dataResult;

        return true;
    }

    public function ObtenerParentescos() {
        $url = "v1/combos/parentescos";

        $header = ["Authorization: Bearer {$_SESSION['token']}"];
        $this->oCurl->setUrl(APISSO);
        $this->oCurl->setFunction(get_class($this) . "-" . __FUNCTION__);
        $this->oCurl->setHeader($header);
        $this->oCurl->setDebug(false);

        if (!$this->oCurl->sendGet($url, $dataResult)) {
            /*var_dump($dataResult);*/
            $this->setError("Error", "Ocurrió un error al buscar parentescos");
            return false;
        }

        if (!$this->Utf8)
            $array = FuncionesPHPLocal::DecodificarUtf8($dataResult);
        else
            $array = $dataResult;
        return $array;
    }

    public function ObtenerEstados() {
        $url = "v1/combos/familiaresestados";

        $header = ["Authorization: Bearer {$_SESSION['token']}"];
        $this->oCurl->setUrl(APISSO);
        $this->oCurl->setFunction(get_class($this) . "-" . __FUNCTION__);
        $this->oCurl->setHeader($header);
        $this->oCurl->setDebug(false);

        if (!$this->oCurl->sendGet($url, $dataResult)) {
            $this->setError("Error", "Ocurrió un error al buscar parentescos");
            return false;
        }

        if (!$this->Utf8)
            $array = FuncionesPHPLocal::DecodificarUtf8($dataResult);
        else
            $array = $dataResult;
        return $array;
    }

    public function Insertar($datos, &$codigoInsertado) {

        if (!$this->_ValidarDatosVacios($datos))
            return false;

        if (!$this->_ValidarInsertar($datos))
            return false;

        $url = "v1/familiares";

        $this->oCurl->setUrl(APISSO . $url);
        $this->oCurl->setFunction(get_class($this) . "-" . __FUNCTION__);
        $this->oCurl->setHttpBuildPost(false);
        $this->oCurl->setDebug(false);

        $arrayHeader = ["Content-Type: application/json", "Authorization: Bearer {$_SESSION['token']}"];

        $this->oCurl->setHeader($arrayHeader);

        $dataEnviar = new stdClass();
        $dataEnviar->IdFamiliarAgente = (int)$datos['IdFamiliarAgente'];
        $dataEnviar->Nombre = $datos['Nombre'];
        $dataEnviar->Apellido = $datos['Apellido'];
        $dataEnviar->Dni = $datos['Dni'];
        $dataEnviar->CUIL = $datos['CUIL'];
        $dataEnviar->IdParentesco = $datos['IdParentesco'];
        $dataEnviar->ACargo = $datos['ACargo'];
        $dataEnviar->Discapacidad = 0;
        if (isset($datos['FechaNacimiento']) && $datos['FechaNacimiento'] != "")
            $dataEnviar->FechaNacimiento = $datos['FechaNacimiento'];
        $dataEnviar->Sexo = $datos['Sexo'];
        if (isset($datos['IdEstado']) && $datos['IdEstado'] != "") {
            $dataEnviar->IdEstado = $datos['IdEstado'];
        } else {
            $dataEnviar->IdEstado = ESTADO_FAMILIAR_PENDIENTE;
        }

        $dataEnviar->Certificado = [];
        if (isset($datos['nombrearchivo']) && !empty($datos['nombrearchivo'])) {
            $dataEnviar->Certificado = new stdClass();
            $dataEnviar->Certificado->Nombre = $datos['nombrearchivo'];
            $dataEnviar->Certificado->Contenido = base64_encode(file_get_contents(PATH_STORAGE . "tmp/" . $datos['nombrearchivotmp']));
            $dataEnviar->Certificado->Size = $datos['size'];
        }

        $cuerpo = json_encode(FuncionesPHPLocal::ConvertiraUtf8($dataEnviar));

        if (!$this->oCurl->sendPost($cuerpo, $dataResult)) {
            $this->setError($dataResult);
            return false;
        }

        if (!$this->Utf8)
            $array = FuncionesPHPLocal::DecodificarUtf8($dataResult);
        else
            $array = $dataResult;

        if (isset($array['error'])) {
            $this->setError($array['error'], $array['error_description']);
            return false;
        }

        $codigoInsertado = $array['Id'];
        return $array;
    }


    public function Modificar($datos) {
        if (!$this->_ValidarDatosVacios($datos))
            return false;

        if (!$this->_ValidarModificar($datos))
            return false;

        $dataEnviar = new stdClass();
        $dataEnviar->IdPersona = (int)$datos['IdPersona'];
        $dataEnviar->Nombre = utf8_encode($datos['Nombre']);
        $dataEnviar->Apellido = utf8_encode($datos['Apellido']);
        $dataEnviar->Dni = utf8_encode($datos['Dni']);
        $dataEnviar->IdParentesco = (int)$datos['IdParentesco'];
        $dataEnviar->ACargo = $datos['ACargo'];
        $dataEnviar->Discapacidad = $datos['Discapacidad'];
        if (isset($datos['FechaNacimiento']) && $datos['FechaNacimiento'] != "")
            $dataEnviar->FechaNacimiento = utf8_encode($datos['FechaNacimiento']);
        $dataEnviar->Sexo = utf8_encode($datos['Sexo']);

        $cuerpo = json_encode($dataEnviar);

        $url = "v1/familiares/" . $datos['Id'];

        $arrayHeader = ["Content-Type: application/json", "Authorization: Bearer {$_SESSION['token']}"];
        $this->oCurl->setUrl(APISSO . $url);
        $this->oCurl->setFunction(get_class($this) . "-" . __FUNCTION__);
        $this->oCurl->setHeader($arrayHeader);
        $this->oCurl->setDebug(false);

        if (!$this->oCurl->sendPut($cuerpo, $dataResult)) {
            $this->setError($dataResult);
            //$this->setError("Error","Ocurrió un error al modificar al familiar");
            return false;
        }

        if (!$this->Utf8)
            $array = FuncionesPHPLocal::DecodificarUtf8($dataResult);
        else
            $array = $dataResult;

        if (isset($array['error'])) {
            $this->setError($array['error'], $array['error_description']);
            return false;
        }

        return $array;
    }


    public function Eliminar($datos) {

        $dataEnviar = new stdClass();

        if (!FuncionesPHPLocal::isEmpty($datos['Id']))
            $dataEnviar->Id = (int)$datos['Id'];
        // $dataEnviar->IdPersona = (int)$datos['IdPersona'];
        $cuerpo = json_encode($dataEnviar);

        $url = "v1/familiares/" . $datos['Id'];

        $this->oCurl->setUrl(APISSO . $url);
        $this->oCurl->setFunction(get_class($this) . "-" . __FUNCTION__);
        $arrayHeader = ["Content-Type: application/json", "Authorization: Bearer {$_SESSION['token']}"];
        $this->oCurl->setHeader($arrayHeader);
        $this->oCurl->setDebug(false);

        if (!$this->oCurl->sendDelete($cuerpo, $dataResult)) {
            $this->setError($dataResult);
            $this->setError("Error", "Ocurrió un error al modificar al familiar");
            return false;
        }

        if (!$this->Utf8)
            $array = FuncionesPHPLocal::DecodificarUtf8($dataResult);
        else
            $array = $dataResult;

        if (isset($array['error'])) {
            $this->setError($array['error'], $array['error_description']);
            return false;
        }

        return $array;
    }


    public function DescargarDDJJ(&$dataResult)
    {
        $url = "v1/mis-familiares/descargar";
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

    private function _ValidarDatosVacios($datos) {

        if (!isset($datos['IdFamiliarAgente']) || $datos['IdFamiliarAgente'] == "") {
            $this->setError(400, 'Debe ingresar una persona');
            return false;
        }

        if (!isset($datos['Nombre']) || $datos['Nombre'] == "") {
            $this->setError(400, 'Debe ingresar un nombre');
            return false;
        }

        if (!isset($datos['Apellido']) || $datos['Apellido'] == "") {
            $this->setError(400, 'Debe ingresar un apellido');
            return false;
        }

        if (!isset($datos['Dni']) || $datos['Dni'] == "") {
            $this->setError(400, 'Debe ingresar un DNI');
            return false;
        }

        if (strlen($datos['Dni']) >= 9) {
            $this->setError(400, 'El DNI no puede tener mas de 8 digitos');
            return false;
        }

        if (isset($datos['Dni']) && $datos['Dni'] != "") {
            if (!FuncionesPHPLocal::ValidarContenido($this->conexion, $datos['Dni'], "NumericoEntero")) {
                $this->setError(400, 'Debe ingresar un numero para el DNI');
                return false;
            }

        }
        if (!isset($datos['CUIL']) || $datos['CUIL'] == "") {
            $this->setError(400, 'Debe ingresar un CUIL');
            return false;
        }

        if (strlen($datos['CUIL']) > 11) {
            $this->setError(400, 'El CUIL no puede tener mas de 11 digitos');
            return false;
        }

        if (isset($datos['CUIL']) && $datos['CUIL'] != "") {
            if (!FuncionesPHPLocal::ValidarContenido($this->conexion, $datos['CUIL'], "CUIT")) {
                $this->setError(400, 'Debe ingresar un CUIL valido');
                return false;
            }

        }

        if (!isset($datos['IdParentesco']) || $datos['IdParentesco'] == "") {
            $this->setError(400, 'Debe seleccionar un parentesco');
            return false;
        }


        if (!isset($datos['Sexo']) || $datos['Sexo'] == "") {
            $this->setError(400, 'Debe seleccionar un genero');
            return false;
        }

        if (isset($datos['IdParentesco']) && $datos['IdParentesco'] != "") {
            if (!FuncionesPHPLocal::ValidarContenido($this->conexion, $datos['IdParentesco'], "NumericoEntero")) {
                $this->setError(400, 'Error, el parentesco debe ser numerico');
                return false;
            }

        }


        if (VALIDA_CERTIFICADO_FAMILIAR) {
            if (!isset($datos['NoValidaCertificado']) || $datos['NoValidaCertificado'] == "") {
                if (!isset($datos['nombrearchivo']) || $datos['nombrearchivo'] == "") {
                    $this->setError(400, 'Debe seleccionar un archivo');
                    return false;
                }
            }
        }


        $dni = $datos['Dni'];
        $cuil = $datos['CUIL'];

        $cuilDigitosMedio = substr($cuil, 2, 8);

        if (strcmp($dni, $cuilDigitosMedio) !== 0) {
            $this->setError(400, 'El DNI no coincide con el CUIL ingresado');
            return false;
        }


        /* if (isset($datos['FechaNacimiento']) && $datos['FechaNacimiento']=="") {
          $this->setError(400, 'Debe ingresar una fecha de nacimiento');
          return false;
      }*/

        if (isset($datos['FechaNacimiento']) && $datos['FechaNacimiento'] == "") {

            $conexionES = new Elastic\Conexion();
            $oPersonas = new Elastic\Personas($conexionES);

            $datosbusqueda['Cuil'] = $datos['CUIL'];
            if (!$oPersonas->buscarxCuil($datosbusqueda, $resultadoPersona, $numfilasPersona)) {
                $error = $oPersonas->getError();
                echo utf8_decode($error['error_description']);
                die;
            }

            if ($numfilasPersona != 0) {

                $this->setError(400, 'El agente que esta intentando asignar como familiar tiene datos incompletos en su legajo. Por favor, completelos e intente nuevamente. ');
                return false;

            } else {

                $this->setError(400, 'Debe ingresar una fecha de nacimiento');
                return false;

            }
        }

        return true;
    }


    private function _ValidarModificar($datos) {
        if (isset($datos['FechaNacimiento'])) {


            $fechaNacimiento = strtotime(FuncionesPHPLocal::ConvertirFecha($datos['FechaNacimiento'], 'dd/mm/aaaa', 'aaaa-mm-dd'));


            $fechaActual = strtotime(date('Y-m-d'));

            if ($fechaNacimiento > $fechaActual) {
                $this->setError(400, 'La fecha de nacimiento no puede ser futura');
                return false;
            }
        }
        return true;
    }

    private function _ValidarInsertar($datos) {


        if (isset($datos['FechaNacimiento'])) {

            $fechaNacimiento = strtotime(FuncionesPHPLocal::ConvertirFecha($datos['FechaNacimiento'], 'dd/mm/aaaa', 'aaaa-mm-dd'));

            $fechaActual = strtotime(date('Y-m-d'));

            if ($fechaNacimiento > $fechaActual) {
                $this->setError(400, 'La fecha de nacimiento no puede ser futura');
                return false;
            }
        }
        return true;
    }

}
