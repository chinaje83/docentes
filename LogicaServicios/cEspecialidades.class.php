<?php

class cEspecialidades
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

    public function BuscarListado(&$resultado, &$numfilas)
    {
        $url = "combos/especialidades";

        $header = array("Authorization: Bearer {$_SESSION['token']}");
        $this->oCurl->setHeader($header);
        $this->oCurl->setUrl(API_LICENCIAS);
        $this->oCurl->setFunction(get_class($this) . "-" . __FUNCTION__);
        $this->oCurl->setDebug(false);

        if (!$this->oCurl->sendGet($url, $dataResult)) {
            $this->setError("Error", utf8_decode("Ocurrió un error al buscar el listado de especialidades."));
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
}


