<?php

class cEspecialidadesAutorizantes
{
    use ManejoErrores;
    protected $oCurl;
    protected $conexion;
    protected $error;
    protected $Utf8;

    protected $MemCache;

    const MemCacheExpire = 86400;// 1 dia

    function __construct($conexion)
    {
        $this->conexion = &$conexion;
        $this->error = array();
        $this->oCurl = new CurlBigtree();
        $this->Utf8 = false;
    }

    public function __destruct()
    {
        $this->oCurl->CloseCurl();
        unset($this->oCurl);
    }

    public function getCurl()
    {
        return $this->oCurl;
    }

    public function CodificarUtf8()
    {
        $this->Utf8 = true;
    }


    public function BuscarxCodigoAutorizante($datos, &$resultado, &$numfilas)
    {
        $url = "medicos_especialidades/" . $datos['IdAutorizante'];

        $header = array("Authorization: Bearer {$_SESSION['token']}");
        $this->oCurl->setHeader($header);
        $this->oCurl->setUrl(API_LICENCIAS);
        $this->oCurl->setFunction(get_class($this) . "-" . __FUNCTION__);
        $this->oCurl->setDebug(false);

        if (!$this->oCurl->sendGet($url, $dataResult)) {
            $this->setError("Error", utf8_decode("Ocurrió un error al buscar el listado de autorizantes."));
            return false;
        }
        if (!$this->Utf8)
            $array = FuncionesPHPLocal::DecodificarUtf8($dataResult);
        else
            $array = $dataResult;

        $numfilas = count($array);
        $resultado = $array;

        return $array;
    }


    public function Insertar($datos, &$codigoInsertado)
    {
        if (!self::_ValidarInsertar($datos))
            return false;

        $url = "medicos_especialidades";
        $header = array("Content-Type: application/json", "Authorization: Bearer {$_SESSION['token']}");
        $this->oCurl->setHeader($header);
        $this->oCurl->setUrl(API_LICENCIAS.$url);
        $this->oCurl->setFunction(get_class($this)."-".__FUNCTION__);
        $this->oCurl->setHttpBuildPost(false);
        $this->oCurl->setDebug(false);

        $dataEnviar = new stdClass();

        if (!FuncionesPHPLocal::isEmpty($datos['IdAutorizante']))
            $dataEnviar->IdAutorizante = utf8_encode($datos['IdAutorizante']);

        if (!FuncionesPHPLocal::isEmpty($datos['IdEspecialidad']))
            $dataEnviar->IdEspecialidad = $datos['IdEspecialidad'];

        $cuerpo = json_encode($dataEnviar);

        if (!$this->oCurl->sendPost($cuerpo,$dataResult)) {
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

        $codigoInsertado = $array['Id'];
        return $array;
    }


    public function Eliminar($datos) {

        if (!self::_ValidarEliminar($datos))
            return false;

        $url = "medicos_especialidades";

        $header = array("Content-Type: application/json", "Authorization: Bearer {$_SESSION['token']}");
        $this->oCurl->setHeader($header);
        $this->oCurl->setUrl(API_LICENCIAS.$url);
        $this->oCurl->setFunction(get_class($this)."-".__FUNCTION__);
        $this->oCurl->setDebug(false);

        $dataEnviar = new stdClass();

        if (!FuncionesPHPLocal::isEmpty($datos['Id']))
            $dataEnviar->Id = $datos['Id'];

        $dataEnviar->Estado = 90;

        $cuerpo = json_encode($dataEnviar);

        if (!$this->oCurl->sendDelete($cuerpo,$dataResult)) {
            $this->setError("Error","Ocurrió un error al eliminar.");
            return false;
        }

        if (!$this->Utf8)
            $array = FuncionesPHPLocal::DecodificarUtf8($dataResult);
        else
            $array = $dataResult;

        return $array;
    }


    private function _ValidarInsertar($datos): bool {

        if (FuncionesPHPLocal::isEmpty($datos['IdAutorizante'])) {
            $this->setError(400, 'Error, registro inexistente.');
            return false;
        }

        if (FuncionesPHPLocal::isEmpty($datos['IdEspecialidad'])) {
            $this->setError(400, 'Error, registro inexistente.');
            return false;
        }

        return true;
    }


    private function _ValidarEliminar($datos): bool {

        if (FuncionesPHPLocal::isEmpty($datos['Id'])) {
            $this->setError(400, 'Error, registro inexistente.');
            return false;
        }

        return true;
    }
}


