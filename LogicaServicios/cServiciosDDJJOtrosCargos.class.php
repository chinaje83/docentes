<?php

class cServiciosDDJJOtrosCargos
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
        $this->Utf8 = false;
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

    public function ObtenerxId($datos)
    {
        $url = "v1/mis-otros-cargos/".$datos["Id"];

        $header = array("Authorization: Bearer {$_SESSION['token']}");
        $this->oCurl->setUrl(APISSO);
        $this->oCurl->setFunction(get_class($this)."-".__FUNCTION__);
        $this->oCurl->setHeader($header);
        $this->oCurl->setDebug(false);

        if(!$this->oCurl->sendGet($url,$dataResult))
        {
            $this->setError("Error","Ocurri� un error al buscar la ddjj");
            return false;
        }

        //$dataResult = json_decode('{"total":1,"filas":{"ddjj":{"Id":"3","IdEstado":"1","NombreEstado":"Pendiente"},"cargos":[{"Id":"7","Organismo":"Ministerio de Trabajo y Empleo- Tierra del Fuego","FuncionCargo":"Miembro comision evaluadora","Dependencia":null,"NombreEmpresa":"","TipoContrato":"Planta Permanente","TipoCargo":"estatal","FechaDesde":"2025-03-01 00:00:00","CantidadHoras":"8","horarios":[{"Dia":"2","HoraInicio":"08:00:00","HoraFin":"12:00:00"},{"Dia":"4","HoraInicio":"08:00:00","HoraFin":"12:00:00"}]},{"Id":"9","Organismo":"Universidad Blas Pascal (UBP)","FuncionCargo":"Veedor de concurso","Dependencia":"Concursos","NombreEmpresa":"","TipoContrato":"Titular","TipoCargo":"universitario","FechaDesde":"2025-05-01 15:36:40","CantidadHoras":"6","horarios":[{"Dia":"1","HoraInicio":"08:00:00","HoraFin":"11:00:00"},{"Dia":"5","HoraInicio":"08:00:00","HoraFin":"11:00:00"}]},{"Id":"8","Organismo":"Empresas Privadas","FuncionCargo":"Asesor comercial","Dependencia":null,"NombreEmpresa":"La Patagonica","TipoContrato":"Estable","TipoCargo":"privado","FechaDesde":"2025-04-08 15:34:43","CantidadHoras":"20","horarios":[{"Dia":"1","HoraInicio":"12:00:00","HoraFin":"16:00:00"},{"Dia":"3","HoraInicio":"12:00:00","HoraFin":"16:00:00"},{"Dia":"5","HoraInicio":"12:00:00","HoraFin":"18:00:00"}]}]}}', true);

        if (!$this->Utf8)
            $array = FuncionesPHPLocal::DecodificarUtf8($dataResult);
        else
            $array = $dataResult;

        return $array;
    }

    public function ObtenerListado(?array &$resultado)
    {
        $url = "v1/mis-otros-cargos";

        $header = ["Authorization: Bearer {$_SESSION['token']}"];
        //$header[] = 'Content-Type: application/json';
        $this->oCurl->setUrl(APISSO);
        $this->oCurl->setFunction(get_class($this) . "-" . __FUNCTION__);
        $this->oCurl->setHeader($header);
        $this->oCurl->setDebug(false);

        if(!$this->oCurl->sendGet($url,$dataResult))
        {
            $this->setError("Error","Ocurrio un error al buscar historial de ddjj");
            return false;
        }

        //$dataResult = json_decode('{"filas":[{"Id":"3","Numero":"3","FechaAprobacion":"2025-06-02 15:32:21","IdEstado":"2","NombreEstado":"activa"},{"Id":"1","Numero":"1","FechaAprobacion":"2025-03-01 00:00:00","IdEstado":"3","NombreEstado":"cerrada"},{"Id":"2","Numero":"2","FechaAprobacion":"2025-04-29 00:00:00","IdEstado":"3","NombreEstado":"cerrada"}],"total":3}',true);

        if (!$this->Utf8)
            $resultado = FuncionesPHPLocal::DecodificarUtf8($dataResult);
        else
            $resultado = $dataResult;

        return true;
    }

    public function Insertar($datos, &$codigoInsertado)
    {

        $url = "v1/mis-otros-cargos/nuevo";

        $arrayHeader = array("Authorization: Bearer {$_SESSION['token']}");

        $this->oCurl->setHeader($arrayHeader);

        $this->oCurl->setUrl(APISSO.$url);
        $this->oCurl->setFunction(get_class($this)."-".__FUNCTION__);
        $this->oCurl->setHttpBuildPost(false);
        $this->oCurl->setDebug(false);

        if(!$this->oCurl->sendPost([],$dataResult)) {
            $this->setError($dataResult['error'],$dataResult['error_description']);
            return false;
        }

        if (!$this->Utf8)
            $array = FuncionesPHPLocal::DecodificarUtf8($dataResult);
        else
            $array = $dataResult;

        if (isset($array['error'])) {
            $this->setError($array['error'],$array['error_description']);
            return false;
        }

        $codigoInsertado = $dataResult['DDJJ'];

        return $array;
    }

    public function Eliminar($datos)
    {
        $url = "v1/mis-otros-cargos/" . $datos['IdDDJJ'];

        $this->oCurl->setUrl(APISSO.$url);
        $this->oCurl->setFunction(get_class($this)."-".__FUNCTION__);
        $arrayHeader = array("Authorization: Bearer {$_SESSION['token']}");
        $this->oCurl->setHeader($arrayHeader);
        $this->oCurl->setDebug(false);

        $postData = [];

        if ($datos['IdPersona'] !== null) {
            $postData['IdPersona'] = $datos['IdPersona'];
        }

        if(!$this->oCurl->sendDelete($postData,$dataResult))
        {
            //$this->setError($dataResult);
            $this->setError("Error","Ocurrio un error al eliminar la ddjj");
            return false;
        }

        if (!$this->Utf8)
            $array = FuncionesPHPLocal::DecodificarUtf8($dataResult);
        else
            $array = $dataResult;

        if (isset($array['error'])) {
            $this->setError($array['error'],$array['error_description']);
            return false;
        }

        return $array;
    }

    public function ModificarEstadoAprobar($datos)
    {
        $url = "v1/mis-otros-cargos/" . $datos['IdDDJJ'] . "/aprobar";

        $arrayHeader = array("Content-Type: application/json", "Authorization: Bearer {$_SESSION['token']}");
        $this->oCurl->setUrl(APISSO.$url);
        $this->oCurl->setFunction(get_class($this)."-".__FUNCTION__);
        $this->oCurl->setHeader($arrayHeader);
        $this->oCurl->setDebug(false);


        if(!$this->oCurl->sendPut([],$dataResult))
        {
            $this->setError($dataResult);
            //$this->setError("Error","Ocurrio un error al modificar estado");
            return false;
        }

        if (!$this->Utf8)
            $array = FuncionesPHPLocal::DecodificarUtf8($dataResult);
        else
            $array = $dataResult;

        if (isset($array['error'])) {
            $this->setError($array['error'],$array['error_description']);
            return false;
        }

        return $array;
    }


    public function InsertarCargo($datos)
    {
        if (!$this->_ValidarInsertarCargo($datos))
            return false;

        $url = "v1/mis-otros-cargos/" . $datos['IdDDJJ'] . "/desempeno/nuevo";

        $dataEnviar = new stdClass();

        $dataEnviar->IdDDJJ = $datos['IdDDJJ'];

        if (isset($datos['IdTipoCargo']) && $datos['IdTipoCargo'] != "")
            $dataEnviar->IdTipoCargo = $datos['IdTipoCargo'];

        if (isset($datos['IdTipoContrato']) && $datos['IdTipoContrato'] != "")
            $dataEnviar->IdTipoContrato = $datos['IdTipoContrato'];

        $dataEnviar->FechaDesde = "";
        if (isset($datos['FechaDesde']) && $datos['FechaDesde'] != "")
            $dataEnviar->FechaDesde = $datos['FechaDesde'];

        if (isset($datos['IdOrganismo']) && $datos['IdOrganismo'] != "")
            $dataEnviar->IdOrganismo = $datos['IdOrganismo'];

        if (isset($datos['NombreEmpresa']) && $datos['NombreEmpresa'] != "")
            $dataEnviar->NombreEmpresa = $datos['NombreEmpresa'];

        if (isset($datos['Dependencia']) && $datos['Dependencia'] != "")
            $dataEnviar->Dependencia = $datos['Dependencia'];

        if (isset($datos['FuncionCargo']) && $datos['FuncionCargo'] != "")
            $dataEnviar->FuncionCargo = $datos['FuncionCargo'];

        if (isset($datos['IdPersona']) && $datos['IdPersona'] != "")
            $dataEnviar->FuncionCargo = $datos['IdPersona'];


        $desempenios = [];

        $i = 0;

        while ($i < count($datos['Dia'])) {
            $desempenios[] = [
                "Dia" => $datos['Dia'][$i],
                "HoraInicio" => $datos['HoraInicio'][$i],
                "HoraFin" => $datos['HoraFin'][$i]
            ];
            $i++;
        }

        $dataEnviar->Desempenos = $desempenios;

        $arrayHeader = array("Content-Type: application/json", "Authorization: Bearer {$_SESSION['token']}");
        $this->oCurl->setUrl(APISSO.$url);
        $this->oCurl->setFunction(get_class($this)."-".__FUNCTION__);
        $this->oCurl->setHttpBuildPost(false);
        $this->oCurl->setDebug(false);
        $this->oCurl->setHeader($arrayHeader);

        $cuerpo = json_encode(FuncionesPHPLocal::ConvertiraUtf8($dataEnviar));

        if(!$this->oCurl->sendPost($cuerpo,$dataResult)) {
            $this->setError($dataResult['error'],$dataResult['error_description']);
            return false;
        }

        if (!$this->Utf8)
            $array = FuncionesPHPLocal::DecodificarUtf8($dataResult);
        else
            $array = $dataResult;

        if (isset($array['error'])) {
            $this->setError($array['error'],$array['error_description']);
            return false;
        }

        return $array;
    }

    public function EliminarCargoxId($datos)
    {
        $url = "v1/mis-otros-cargos/". $datos['IdDDJJ'] . "/" . $datos['IdCargo'];

        $this->oCurl->setUrl(APISSO.$url);
        $this->oCurl->setFunction(get_class($this)."-".__FUNCTION__);
        $arrayHeader = array("Content-Type: application/json", "Authorization: Bearer {$_SESSION['token']}");
        $this->oCurl->setHeader($arrayHeader);
        $this->oCurl->setDebug(false);

        $postData = [];

        if ($datos['IdPersona'] !== null) {
            $postData['IdPersona'] = $datos['IdPersona'];
        }

        if(!$this->oCurl->sendDelete($postData,$dataResult))
        {
            $this->setError($dataResult);
            $this->setError("Error","Ocurrio un error al eliminar puesto");
            return false;
        }

        if (!$this->Utf8)
            $array = FuncionesPHPLocal::DecodificarUtf8($dataResult);
        else
            $array = $dataResult;

        if (isset($array['error'])) {
            $this->setError($array['error'],$array['error_description']);
            return false;
        }

        return $array;
    }

    public function ObtenerDatosFormCargo($datos){

        $url = "v1/mis-otros-cargos/".$datos["IdDDJJ"]."/puestos";

        $header = ["Authorization: Bearer {$_SESSION['token']}"];

        $this->oCurl->setUrl(APISSO);
        $this->oCurl->setFunction(get_class($this) . "-" . __FUNCTION__);
        $this->oCurl->setHeader($header);
        $this->oCurl->setDebug(false);

        if(!$this->oCurl->sendGet($url,$dataResult))
        {
            $this->setError("Error","Ocurrio un error al buscar los datos para cargar el puesto.");
            return false;
        }

        if (!$this->Utf8)
            $array = FuncionesPHPLocal::DecodificarUtf8($dataResult);
        else
            $array = $dataResult;

        if (isset($array['error'])) {
            $this->setError($array['error'],$array['error_description']);
            return false;
        }

        return $array;
    }


    public function ObtenerOrganismos($datos){

        $url = "v1/combos/organismos/tipo/" . $datos["IdTipoCargo"];

        $header = ["Authorization: Bearer {$_SESSION['token']}"];

        $this->oCurl->setUrl(APISSO);
        $this->oCurl->setFunction(get_class($this) . "-" . __FUNCTION__);
        $this->oCurl->setHeader($header);
        $this->oCurl->setDebug(false);

        if(!$this->oCurl->sendGet($url,$dataResult))
        {
            $this->setError("Error","Ocurrio un error al buscar historial de ddjj");
            return false;
        }

        if (!$this->Utf8)
            $array = FuncionesPHPLocal::DecodificarUtf8($dataResult);
        else
            $array = $dataResult;

        if (isset($array['error'])) {
            $this->setError($array['error'],$array['error_description']);
            return false;
        }

        return $array;
    }



    public function obtenerTodas($datos, ?array &$resultado) {
        $url = "v1/ddjj/otros-cargos";

        $params = [];

        if (!empty($datos['IdEstado'])) {
            $params['IdEstado'] = $datos['IdEstado'];
        }

        if (!empty($datos['NombreCompleto'])) {
            $params['NombreCompleto'] = $datos['NombreCompleto'];
        }

        if (!empty($datos['CuilDni'])) {
            $params['CuilDni'] = $datos['CuilDni'];
        }

        if (!empty($datos['FechaAprobacion'])) {
            $params['FechaAprobacion'] = FuncionesPHPLocal::ConvertirFecha($datos['FechaAprobacion'], "dd/mm/aaaa", 'aaaa-mm-dd');
        }

        if (!empty($datos['page'])) {
            $params['page'] = $datos['page'];
        }

        if (!empty($datos['rows'])) {
            $params['rows'] = $datos['rows'];
        }

        if (!empty($params)) {
            $url .= '?' . http_build_query($params);
        }

        $header = ["Authorization: Bearer {$_SESSION['token']}"];
        $this->oCurl->setUrl(APISSO);
        $this->oCurl->setFunction(get_class($this) . "-" . __FUNCTION__);
        $this->oCurl->setHeader($header);
        $this->oCurl->setDebug(false);

        if (!$this->oCurl->sendGet($url, $dataResult)) {
            $this->setError("Error", "Ocurrió un error al buscar las declaraciones de otros cargos activas");
            return false;
        }

        if (!$this->Utf8)
            $resultado = FuncionesPHPLocal::DecodificarUtf8($dataResult);
        else
            $resultado = $dataResult;

        return true;

    }



    public function obtenerHistorialPersona(array $datos, &$resultado)
    {
        $IdPersona = $datos['IdPersona'];
        $url = "v1/ddjj/otros-cargos/persona/".$IdPersona;

        $header = ["Authorization: Bearer {$_SESSION['token']}"];
        //$header[] = 'Content-Type: application/json';
        $this->oCurl->setUrl(APISSO);
        $this->oCurl->setFunction(get_class($this) . "-" . __FUNCTION__);
        $this->oCurl->setHeader($header);
        $this->oCurl->setDebug(false);

        if(!$this->oCurl->sendGet($url,$dataResult))
        {
            $this->setError("Error","Ocurrio un error al buscar historial de ddjj");
            return false;
        }

        if (!$this->Utf8)
            $resultado = FuncionesPHPLocal::DecodificarUtf8($dataResult);
        else
            $resultado = $dataResult;;

        return true;
    }

    public function Descargar($datos, &$dataResult) {

        $url = "v1/mis-otros-cargos/" . $datos["IdDDJJ"] . "/descargar";
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

    protected function _ValidarInsertarCargo($datos)
    {

        if (!isset($datos['IdTipoCargo']) || $datos['IdTipoCargo'] == "") {
            $this->setError(400, 'Debe seleccionar un tipo de cargo.');
            return false;
        }

        if (!isset($datos['IdDDJJ']) || $datos['IdDDJJ'] == "") {
            $this->setError(400, 'Faltan datos');
            return false;
        }

        if (!FuncionesPHPLocal::isEmpty($datos["IdTipoCargo"])) {
            if($datos["IdTipoCargo"] != DDJJ_OC_TIPO_PRIVADO) {

                if (!isset($datos['IdOrganismo']) || $datos['IdOrganismo'] == "") {
                    $this->setError(400, 'Debe seleccionar un organismo.');
                    return false;
                }

                if (!isset($datos['Dependencia']) || $datos['Dependencia'] == "") {
                    $this->setError(400, 'Debe ingresar una dependencia.');
                    return false;
                }

            } else {

                if (!isset($datos['NombreEmpresa']) || $datos['NombreEmpresa'] == "") {
                    $this->setError(400, 'Debe ingresar el nombre de la empresa.');
                    return false;
                }
            }
        }



        if (!isset($datos['HoraInicio']) || $datos['HoraInicio'] == "") {
            $this->setError(400, 'Debe ingresar un horario');
            return false;
        }
        if (!isset($datos['HoraFin']) || $datos['HoraFin'] == "") {
            $this->setError(400, 'Debe ingresar un horario');
            return false;
        }
        if (!isset($datos['Dia']) || $datos['Dia'] == "") {
            $this->setError(400, 'Debe ingresar un horario');
            return false;
        }

        return true;
    }

}
