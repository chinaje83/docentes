<?php

class cServiciosUsuario
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


    public function getUsuario()
    {

        $array['Dni'] = $_SESSION['Dni'];
        $array['NombreCompleto'] = $_SESSION['usuarionombre']." ".$_SESSION['usuarioapellido'];
        $array['Avatar'] = $_SESSION['usuario_avatar'];
        $array['Cuil'] = $_SESSION['Cuil'];
        $array['Email'] = $_SESSION['email'];
        $array['Telefono'] = $_SESSION['telefono'];
        /*
        $url = "v1/usuario";

        $header = array("Authorization: Bearer {$_SESSION['token']}");
        $this->oCurl->setUrl(APISSO);
        $this->oCurl->setFunction(get_class($this)."-".__FUNCTION__);
        $this->oCurl->setHeader($header);
        //$this->oCurl->setDebug(true);

        if(!$this->oCurl->sendGet($url,$dataResult))
        {
            $this->setError("Error","Ocurrió un error al buscar usuario.");
            return false;
        }
        if (!$this->Utf8)
            $array = FuncionesPHPLocal::DecodificarUtf8($dataResult);
        else
            $array = $dataResult;*/

        return $array;
    }




    public function modificarPassword($datos)
    {
        $url = "v1/usuario/cambiar-password";

        $header = array("Authorization: Bearer {$_SESSION['token']}", 'Content-Type: application/json');
        $this->oCurl->setUrl(APISSO.$url);
        $this->oCurl->setFunction(get_class($this)."-".__FUNCTION__);
        $this->oCurl->setHeader($header);
        //$this->oCurl->setDebug(true);

        $dataEnviar = new stdClass();
        $dataEnviar->PassAnterior = $datos['PassAnterior'];
        $dataEnviar->Password = $datos['Password'];
        $cuerpo = json_encode($dataEnviar);

        if(!$this->oCurl->sendPatch($cuerpo,$dataResult))
        {
            $this->setError($dataResult['error'],$dataResult['error_description']);
            return false;
        }
        if (!$this->Utf8)
            $array = FuncionesPHPLocal::DecodificarUtf8($dataResult);
        else
            $array = $dataResult;

        return $array;
    }



}
