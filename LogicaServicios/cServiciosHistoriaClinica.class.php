<?php

class cServiciosHistoriaClinica
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

    public function setUtf8(bool $Utf8 = false): cServiciosHistoriaClinica {
        $this->Utf8 = $Utf8;
        return $this;
    }


    public function ObtenerListado(array $datos, ?array &$resultado): bool
    {
        // armamos los parámetros que van en el querystring
        $query = [];

        // obligatorios o con default
        $query['page'] = isset($datos['page']) ? (int)$datos['page'] : 1;
        $query['size'] = isset($datos['size']) ? (int)$datos['size'] : 20;

        // opcionales
        if (!empty($datos['estado_id']))
            $query['estado_id'] = $datos['estado_id'];

        if (!empty($datos['persona_id']))
            $query['persona_id'] = $datos['persona_id'];

        if (!empty($datos['cuil']))
            $query['cuil'] = $datos['cuil'];

        $url = 'v1/historia-clinica?' . http_build_query($query);
        $header = ["Authorization: Bearer {$_SESSION['token']}"];

        $this->oCurl->setUrl(APISSO);
        $this->oCurl->setFunction(get_class($this) . "-" . __FUNCTION__);
        $this->oCurl->setHeader($header);
        $this->oCurl->setDebug(false);

        if (!$this->oCurl->sendGet($url, $dataResult)) {
            $this->setError("Error", "Ocurrió un error al buscar historial de historia clínica");
            return false;
        }

        if (!$this->Utf8){
            $resultado = FuncionesPHPLocal::DecodificarUtf8($dataResult);
       } else{
            $resultado = $dataResult;
}
        return true;
    }


    public function ObtenerHistoriaClinica(array $datos, ?array &$resultado): bool
    {
        $id = isset($datos['IdHistoriaClinica']) ? trim($datos['IdHistoriaClinica']) : '';

        if ($id == '') {
            $this->setError("Error", "Falta IdHistoriaClinica");
            return false;
        }

        $url = "v1/historia-clinica/{$id}";

        $header = ["Authorization: Bearer {$_SESSION['token']}"];
        $this->oCurl->setUrl(APISSO);
        $this->oCurl->setFunction(get_class($this) . "-" . __FUNCTION__);
        $this->oCurl->setHeader($header);
        $this->oCurl->setDebug(false);

        if (!$this->oCurl->sendGet($url, $dataResult)) {
            $this->setError("Error", "Ocurrió un error al obtener la historia clínica");
            return false;
        }

        if (!$this->Utf8)
            $resultado = FuncionesPHPLocal::DecodificarUtf8($dataResult);
        else
            $resultado = $dataResult;

        return true;
    }


    public function ObtenerEstados(array &$resultado): bool
    {
        $url = 'v1/combos/historia-clinica/estados';

        $header = ["Authorization: Bearer {$_SESSION['token']}"];
        $this->oCurl->setUrl(APISSO);
        $this->oCurl->setFunction(get_class($this) . "-" . __FUNCTION__);
        $this->oCurl->setHeader($header);
        $this->oCurl->setDebug(false);

        if (!$this->oCurl->sendGet($url, $dataResult)) {
            $this->setError("Error", "Ocurrió un error al obtener los estados");
            return false;
        }

        if (!$this->Utf8)
            $resultado = FuncionesPHPLocal::DecodificarUtf8($dataResult);
        else
            $resultado = $dataResult;
        return true;
    }


    public function ObtenerDiagnosticos(?array &$resultado): bool
    {
        $url = "v1/combos/historia-clinica/diagnosticos";

        $header = ["Authorization: Bearer {$_SESSION['token']}"];
        $this->oCurl->setUrl(APISSO);
        $this->oCurl->setFunction(get_class($this) . "-" . __FUNCTION__);
        $this->oCurl->setHeader($header);
        $this->oCurl->setDebug(false);

        if (!$this->oCurl->sendGet($url, $dataResult)) {
            $this->setError("Error", "Ocurrió un error al obtener diagnósticos");
            return false;
        }

        if (!$this->Utf8)
            $resultado = FuncionesPHPLocal::DecodificarUtf8($dataResult);
        else
            $resultado = $dataResult;

        return true;
    }


    public function ObtenerComunicaciones(array $datos, ?array &$resultado): bool
    {
        $id = isset($datos['IdHistoriaClinica']) ? trim($datos['IdHistoriaClinica']) : '';
        if ($id == '') {
            $this->setError("Error", "Falta IdHistoriaClinica");
            return false;
        }

        $page = isset($datos['page']) ? (int)$datos['page'] : 1;
        $size = isset($datos['size']) ? (int)$datos['size'] : 10;

        $query = [
            'page' => $page,
            'size' => $size
        ];

        $url = "v1/historia-clinica/{$id}/comunicaciones?" . http_build_query($query);

        $header = ["Authorization: Bearer {$_SESSION['token']}"];
        $this->oCurl->setUrl(APISSO);
        $this->oCurl->setFunction(get_class($this) . "-" . __FUNCTION__);
        $this->oCurl->setHeader($header);
        $this->oCurl->setDebug(false);

        if (!$this->oCurl->sendGet($url, $dataResult)) {
            $this->setError("Error", "Ocurrió un error al obtener las comunicaciones");
            return false;
        }

        if (!$this->Utf8)
            $resultado = FuncionesPHPLocal::DecodificarUtf8($dataResult);
        else
            $resultado = $dataResult;

        return true;
    }


    public function EnviarHistoriaClinica(array $datos, ?array &$resultado): bool
    {
        $id = isset($datos['IdHistoriaClinica']) ? trim($datos['IdHistoriaClinica']) : '';
        if ($id == '') {
            $this->setError("Error", "Falta IdHistoriaClinica");
            return false;
        }

        $url = "v1/historia-clinica/{$id}";

        $dataEnviar = new stdClass();
        $dataEnviar->diagnosticos = isset($datos['diagnosticos']) ? $datos['diagnosticos'] : [];
        $dataEnviar->documentos   = isset($datos['documentos']) ? $datos['documentos'] : [];
        $dataEnviar->persona_id   = isset($datos['persona_id']) ? (int)$datos['persona_id'] : null;

        $cuerpo = json_encode(FuncionesPHPLocal::ConvertiraUtf8($dataEnviar));

        $this->oCurl->setHeader([
            "Accept: application/json",
            "Content-Type: application/json",
            "Authorization: Bearer {$_SESSION['token']}"
        ]);

        $this->oCurl->setUrl(APISSO . $url);
        $this->oCurl->setFunction(get_class($this) . "-" . __FUNCTION__);
        $this->oCurl->setHttpBuildPost(false);
        $this->oCurl->setDebug(false);

        if (!$this->oCurl->sendPut($cuerpo, $dataResult)) {
            $this->setError($dataResult['error']??'422', $dataResult['message']??$dataResult['error_description']);
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

        if (!isset($array['success']))
            return false;

        $resultado = $array;
        return $array['success'];
    }


    public function EnviarComunicacion(array $datos, ?array &$resultado): bool
    {
        $id = isset($datos['IdHistoriaClinica']) ? trim($datos['IdHistoriaClinica']) : '';
        if ($id == '') {
            $this->setError("Error", "Falta IdHistoriaClinica");
            return false;
        }

        $url = "v1/historia-clinica/{$id}/comunicaciones";

        $dataEnviar = new stdClass();
        $dataEnviar->mensaje = isset($datos['mensaje']) ? $datos['mensaje'] : '';

        $cuerpo = json_encode($dataEnviar);

        $this->oCurl->setHeader([
            "Content-Type: application/json",
            "Authorization: Bearer {$_SESSION['token']}"
        ]);

        $this->oCurl->setUrl(APISSO . $url);
        $this->oCurl->setFunction(get_class($this) . "-" . __FUNCTION__);
        $this->oCurl->setHttpBuildPost(false);
        $this->oCurl->setDebug(false);

        if (!$this->oCurl->sendPost($cuerpo, $dataResult)) {
            $this->setError($dataResult['error'], utf8_decode($dataResult['error_description']));
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

        if (!isset($array['success']))
            return false;

        $resultado = $array;
        return $array['success'];
    }


    public function HabilitarChatComunicaciones(array $datos, ?array &$resultado): bool
    {
        $id = isset($datos['IdHistoriaClinica']) ? trim($datos['IdHistoriaClinica']) : '';
        if ($id == '') {
            $this->setError("Error", "Falta IdHistoriaClinica");
            return false;
        }

        $url = "v1/historia-clinica/{$id}/comunicaciones";

        $dataEnviar = new stdClass();
        $dataEnviar->habilitar = !empty($datos['habilitar']) ? true : false;

        $cuerpo = json_encode(FuncionesPHPLocal::ConvertiraUtf8($dataEnviar));

        $this->oCurl->setHeader([
            "Content-Type: application/json",
            "Authorization: Bearer {$_SESSION['token']}"
        ]);

        $this->oCurl->setUrl(APISSO . $url);
        $this->oCurl->setFunction(get_class($this) . "-" . __FUNCTION__);
        $this->oCurl->setHttpBuildPost(false);
        $this->oCurl->setDebug(false);

        if (!$this->oCurl->sendPatch($cuerpo, $dataResult)) {
            $this->setError($dataResult['error'], utf8_decode($dataResult['error_description']));
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

        if (!isset($array['success']))
            return false;

        $resultado = $array;
        return $array['success'];
    }


    public function TomarHistoriaClinica(array $datos, ?array &$resultado): bool
    {
        $id = isset($datos['IdHistoriaClinica']) ? trim($datos['IdHistoriaClinica']) : '';
        if ($id == '') {
            $this->setError("Error", "Falta IdHistoriaClinica");
            return false;
        }

        $url = "v1/historia-clinica/{$id}";

        $dataEnviar = new stdClass();
        $cuerpo = json_encode(FuncionesPHPLocal::ConvertiraUtf8($dataEnviar));

        $this->oCurl->setHeader([
            "Content-Type: application/json",
            "Authorization: Bearer {$_SESSION['token']}"
        ]);

        $this->oCurl->setUrl(APISSO . $url);
        $this->oCurl->setFunction(get_class($this) . "-" . __FUNCTION__);
        $this->oCurl->setHttpBuildPost(false);
        $this->oCurl->setDebug(false);

        if (!$this->oCurl->sendPatch($cuerpo, $dataResult)) {
            $this->setError($dataResult['error'], utf8_decode($dataResult['error_description']));
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

        if (!isset($array['success']))
            return false;

        $resultado = $array;
        return $array['success'];
    }


    public function LiberarHistoriaClinica(array $datos, ?array &$resultado): bool
    {
        $id = isset($datos['IdHistoriaClinica']) ? trim($datos['IdHistoriaClinica']) : '';
        if ($id == '') {
            $this->setError("Error", "Falta IdHistoriaClinica");
            return false;
        }

        $url = "v1/historia-clinica/{$id}/liberar";

        $dataEnviar = new stdClass();
        $cuerpo = json_encode(FuncionesPHPLocal::ConvertiraUtf8($dataEnviar));

        $this->oCurl->setHeader([
            "Content-Type: application/json",
            "Authorization: Bearer {$_SESSION['token']}"
        ]);

        $this->oCurl->setUrl(APISSO . $url);
        $this->oCurl->setFunction(get_class($this) . "-" . __FUNCTION__);
        $this->oCurl->setHttpBuildPost(false);
        $this->oCurl->setDebug(false);

        if (!$this->oCurl->sendPatch($cuerpo, $dataResult)) {
            $this->setError($dataResult['error'], utf8_decode($dataResult['error_description']));
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

        if (!isset($array['success']))
            return false;

        $resultado = $array;
        return $array['success'];
    }


    public function AprobarHistoriaClinica(array $datos, ?array &$resultado): bool
    {
        $id = isset($datos['IdHistoriaClinica']) ? trim($datos['IdHistoriaClinica']) : '';
        if ($id == '') {
            $this->setError("Error", "Falta IdHistoriaClinica");
            return false;
        }

        $url = "v1/historia-clinica/{$id}/aprobar";

        $dataEnviar = new stdClass();
        $cuerpo = json_encode(FuncionesPHPLocal::ConvertiraUtf8($dataEnviar));

        $this->oCurl->setHeader([
            "Content-Type: application/json",
            "Authorization: Bearer {$_SESSION['token']}"
        ]);

        $this->oCurl->setUrl(APISSO . $url);
        $this->oCurl->setFunction(get_class($this) . "-" . __FUNCTION__);
        $this->oCurl->setHttpBuildPost(false);
        $this->oCurl->setDebug(false);

        if (!$this->oCurl->sendPatch($cuerpo, $dataResult)) {
            $this->setError($dataResult['error'], utf8_decode($dataResult['error_description']));
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

        if (!isset($array['success']))
            return false;

        $resultado = $array;
        return $array['success'];
    }


    public function RechazarHistoriaClinica(array $datos, ?array &$resultado): bool
    {
        $id = isset($datos['IdHistoriaClinica']) ? trim($datos['IdHistoriaClinica']) : '';
        if ($id == '') {
            $this->setError("Error", "Falta IdHistoriaClinica");
            return false;
        }

        $url = "v1/historia-clinica/{$id}/rechazar";

        $dataEnviar = new stdClass();
        $cuerpo = json_encode(FuncionesPHPLocal::ConvertiraUtf8($dataEnviar));

        $this->oCurl->setHeader([
            "Content-Type: application/json",
            "Authorization: Bearer {$_SESSION['token']}"
        ]);

        $this->oCurl->setUrl(APISSO . $url);
        $this->oCurl->setFunction(get_class($this) . "-" . __FUNCTION__);
        $this->oCurl->setHttpBuildPost(false);
        $this->oCurl->setDebug(false);

        if (!$this->oCurl->sendPatch($cuerpo, $dataResult)) {
            $this->setError($dataResult['error'], utf8_decode($dataResult['error_description']));
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

        if (!isset($array['success']))
            return false;

        $resultado = $array;
        return $array['success'];
    }


    public function ObtenerDocumentoContenido(array $datos, ?array &$resultado): bool
    {
        $idHistoriaClinica = isset($datos['IdHistoriaClinica']) ? trim($datos['IdHistoriaClinica']) : '';
        $idDocumento       = isset($datos['IdDocumento']) ? trim($datos['IdDocumento']) : '';
        $hash              = isset($datos['Hash']) ? trim($datos['Hash']) : '';

        if ($idHistoriaClinica == '' || $idDocumento == '' || $hash == '') {
            $this->setError("Error", "Faltan parámetros para obtener el documento");
            return false;
        }

        $url = "v1/historia-clinica/{$idHistoriaClinica}/documentos/{$idDocumento}/{$hash}";

        $header = ["Authorization: Bearer {$_SESSION['token']}"];

        $this->oCurl->setUrl(APISSO);
        $this->oCurl->setFunction(get_class($this) . "-" . __FUNCTION__);
        $this->oCurl->setHeader($header);
        $this->oCurl->setDebug(false);

        if (!$this->oCurl->sendGet($url, $dataResult)) {
            $this->setError("Error", "Ocurrió un error al obtener el documento");
            return false;
        }

        if (!$this->Utf8)
            $resultado = FuncionesPHPLocal::DecodificarUtf8($dataResult);
        else
            $resultado = $dataResult;

        return true;
    }


}

