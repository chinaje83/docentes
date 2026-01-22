<?php

class cServiciosRoles
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


    public function getRoles()
    {
        $url = "v1/roles";

        $header = array("Authorization: Bearer {$_SESSION['token']}");
        $this->oCurl->setUrl(APISSO);
        $this->oCurl->setFunction(get_class($this)."-".__FUNCTION__);
        $this->oCurl->setHeader($header);
//        $this->oCurl->setDebug(true);
        if(!$this->oCurl->sendGet($url,$dataResult))
        {
            $this->setError("Error","Ocurrió un error al buscar los roles.");
            return false;
        }
        if (!$this->Utf8)
            $array = FuncionesPHPLocal::DecodificarUtf8($dataResult);
        else
            $array = $dataResult;

        return $array;
    }

    public function insertar($datos)
    {
        if (!$this->_ValidarDatosVacios($datos))
            return false;

        $url = "v1/roles";

        $header = array("Content-Type: application/json", "Authorization: Bearer {$_SESSION['token']}");
        $this->oCurl->setUrl(APISSO.$url);
        $this->oCurl->setFunction(get_class($this)."-".__FUNCTION__);
        $this->oCurl->setHeader($header);
        $this->oCurl->setDebug(false);

        $dataEnviar = new stdClass();
        $dataEnviar->Nombre = $datos['Nombre'];
        $dataEnviar->Descripcion = $datos['Descripcion'];
        $dataEnviar->Constante = $datos['Constante'];

        $cuerpo = json_encode($dataEnviar);

        if(!$this->oCurl->sendPost($cuerpo,$dataResult))
        {
            $this->setError("Error","Ocurrió un error al insertar el rol.");
            return false;
        }

        if (!$this->Utf8)
            $array = FuncionesPHPLocal::DecodificarUtf8($dataResult);
        else
            $array = $dataResult;

        return $array;
    }

    public function modificar($datos)
    {
        if (!$this->_ValidarDatosVacios($datos))
            return false;

        $url = "v1/roles";

        $header = array("Content-Type: application/json", "Authorization: Bearer {$_SESSION['token']}");
        $this->oCurl->setUrl(APISSO.$url);
        $this->oCurl->setFunction(get_class($this)."-".__FUNCTION__);
        $this->oCurl->setHeader($header);
        $this->oCurl->setDebug(false);

        $dataEnviar = new stdClass();

        if (isset($datos['Id']) && $datos['Id'] != "")
            $dataEnviar->Id = $datos['Id'];

        $dataEnviar->Nombre = utf8_encode($datos['Nombre']);
        $dataEnviar->Descripcion = (isset($datos['Descripcion']) && $datos['Descripcion'] != "" ? utf8_encode($datos['Descripcion']) : NULL);
        $dataEnviar->Constante = $datos['Constante'];

        $cuerpo = json_encode($dataEnviar);

        if(!$this->oCurl->sendPut($cuerpo,$dataResult))
        {
            $this->setError("Error","Ocurrió un error al modificar el rol.");
            return false;
        }

        if (!$this->Utf8)
            $array = FuncionesPHPLocal::DecodificarUtf8($dataResult);
        else
            $array = $dataResult;

        return $array;
    }


    public function eliminar($datos)
    {
        $url = "v1/roles";

        $header = array("Content-Type: application/json", "Authorization: Bearer {$_SESSION['token']}");
        $this->oCurl->setUrl(APISSO.$url);
        $this->oCurl->setFunction(get_class($this)."-".__FUNCTION__);
        $this->oCurl->setHeader($header);
        $this->oCurl->setDebug(true);

        $dataEnviar = new stdClass();

        if (isset($datos['Id']) && $datos['Id'] != "")
            $dataEnviar->Id = $datos['Id'];

        if (isset($datos['Constante']) && $datos['Constante'] != "")
            $dataEnviar->Constante = $datos['Constante'];

        $cuerpo = json_encode($dataEnviar);

        if (!$this->oCurl->sendDelete($cuerpo,$dataResult))
        {
            $this->setError("Error","Ocurrió un error al modificar el rol.");
            return false;
        }

        if (!$this->Utf8)
            $array = FuncionesPHPLocal::DecodificarUtf8($dataResult);
        else
            $array = $dataResult;

        return $array;
    }


    private function _ValidarDatosVacios($datos)
    {
        if (!isset($datos['Nombre']) || $datos['Nombre'] == "") {
            $this->setError(400,'Debe ingresar un nombre');
            return false;
        }

        return true;
    }
}