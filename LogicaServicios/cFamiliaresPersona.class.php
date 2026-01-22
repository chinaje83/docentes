<?php

class cFamiliaresPersona
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

    public function getCurl() {
        return 	$this->oCurl;
    }

    public function CodificarUtf8() {
        $this->Utf8 = true;
    }


    public function ObtenerFamiliarxId($datos) {

        $url = 'v1/familiares-persona';

        $dataEnviar = new stdClass();
        if (!FuncionesPHPLocal::isEmpty($datos['Id']))
            $dataEnviar->Id = (int)$datos['Id'];
        if (!FuncionesPHPLocal::isEmpty($datos['IdPersona']))
            $dataEnviar->IdPersona = (int)$datos['IdPersona'];

        $cuerpo = json_encode($dataEnviar);

        $header = ['Content-Type: application/json', "Authorization: Bearer {$_SESSION['token']}"];
        $this->oCurl->setUrl(APISSO . $url);
        $this->oCurl->setFunction(get_class($this) . '-' . __FUNCTION__);
        $this->oCurl->setHeader($header);
        $this->oCurl->setDebug(false);

        if (!$this->oCurl->sendGet('', $dataResult, $cuerpo)) {
            $this->setError($this->getError());
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

    public function modificarIdEstado($datos) {

        $dataEnviar = new stdClass();

        if (!FuncionesPHPLocal::isEmpty($datos['IdEstado']))
            $dataEnviar->IdEstado = $datos['IdEstado'];

        if (!FuncionesPHPLocal::isEmpty($datos['Id']))
            $dataEnviar->Id = $datos['Id'];

        $cuerpo = json_encode($dataEnviar);

        $url = 'v1/familiares-persona';

        $arrayHeader = ["Content-Type: application/json", "Authorization: Bearer {$_SESSION['token']}"];
        $this->oCurl->setUrl(APISSO.$url);
        $this->oCurl->setFunction(get_class($this)."-".__FUNCTION__);
        $this->oCurl->setHeader($arrayHeader);
        $this->oCurl->setDebug(false);

        if (!$this->oCurl->sendPut($cuerpo,$dataResult)) {
            $this->setError($dataResult);
            $this->setError("Error","Ocurri� un error al modificar al familiar");
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


    public function Eliminar($datos) {

        $url = 'v1/familiares-persona';

        $dataEnviar = new stdClass();
        if (!FuncionesPHPLocal::isEmpty($datos['Id']))
            $dataEnviar->IdFamiliar = (int)$datos['Id'];
        if (!FuncionesPHPLocal::isEmpty($datos['IdPersona']))
            $dataEnviar->IdPersona = (int)$datos['IdPersona'];

        $cuerpo = json_encode($dataEnviar);

        $header = ["Content-Type: application/json", "Authorization: Bearer {$_SESSION['token']}"];
        $this->oCurl->setUrl(APISSO.$url);
        $this->oCurl->setFunction(get_class($this)."-".__FUNCTION__);
        $this->oCurl->setHeader($header);
        $this->oCurl->setDebug(false);

        if (!$this->oCurl->sendDelete($cuerpo,$dataResult)) {

            $this->setError($dataResult['error'],$dataResult['error_description']);
            return false;
        }

        if (!$this->Utf8)
            $array = FuncionesPHPLocal::DecodificarUtf8($dataResult);
        else
            $array = $dataResult;

        /*if (isset($array['error'])) {
            $this->setError($array['error'],$array['error_description']);
            return false;
        }*/

        return $array;
    }
}
