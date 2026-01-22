<?php

class cLicenciasCargos
{
    use ManejoErrores;
    protected $oCurl;
    protected $conexion;
    protected $error;
    protected $Utf8;
    protected $MemCache;

    const MemCacheExpire = 86400;// 1 dia

    function __construct($conexion) {

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
        return $this->oCurl;
    }

    public function CodificarUtf8() {
        $this->Utf8 = true;
    }

    public function ModificarxEscuela($datos) {

        if (!self::_ValidarModificarxEscuela($datos))
            return false;

        $url = 'licencias/cargos/escuela/';
        $this->oCurl->setHeader(["Content-Type: application/json", "Authorization: Bearer {$_SESSION['token']}"]);
        $this->oCurl->setUrl(API_LICENCIAS . $url);
        $this->oCurl->setFunction(get_class($this) . "-" . __FUNCTION__);
        $this->oCurl->setHttpBuildPost(false);
        $this->oCurl->setDebug(false);

        $dataEnviar = new stdClass();

        if (!FuncionesPHPLocal::isEmpty($datos['IdEscuela']))
            $dataEnviar->IdEscuela = $datos['IdEscuela'];

        if (!FuncionesPHPLocal::isEmpty($datos['IdRegion']))
            $dataEnviar->IdRegion = $datos['IdRegion'];

        $cuerpo = json_encode($dataEnviar);

        if (!$this->oCurl->sendPut($cuerpo, $dataResult)) {
            $this->setError($dataResult['error'], $dataResult['error_description']);
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

    public function ObtenerLicenciasxPersona($datos){

        $url = "licencias/{$datos['Id']}/cargos";

        $header = array("Authorization: Bearer {$_SESSION['token']}");

        $this->oCurl->setUrl(API_LICENCIAS);
        $this->oCurl->setFunction(get_class($this) . "-" . __FUNCTION__);
        $this->oCurl->setHeader($header);
        $this->oCurl->setDebug(false);


        if (!$this->oCurl->sendGet($url, $dataResult)) {
            $this->setError("Error","Ocurrió un error al buscar la licencia por id de persona");
            return false;
        }

        if (!$this->Utf8)
            $array = FuncionesPHPLocal::DecodificarUtf8($dataResult);
        else
            $array = $dataResult;
        return $array;

    }


    private function _ValidarModificarxEscuela($datos): bool {

        if (FuncionesPHPLocal::isEmpty($datos['IdEscuela'])) {
            $this->setError(400, 'Error, debe ingresar una escuela');
            return false;
        }

        if (FuncionesPHPLocal::isEmpty($datos['IdRegion'])) {
            $this->setError(400, 'Error, debe ingresar una region');
            return false;
        }

        return true;
    }



}