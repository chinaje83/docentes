<?php

class cServiciosTiposDocumentos
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

    public function ObtenerTiposDocumentos($datos)
    {

        $spnombre="sel_TiposDocumentos";
        $sparam=array(
        );
        if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno) )
        {
            $this->setError("Error","Ocurrió un error al buscar los tipos de documentos");
            return false;
        }
        $array = [];
        $array['total'] = $numfilas;
        while($fila = $this->conexion->ObtenerSiguienteRegistro($resultado))
            $array['filas'][] = $fila;
        /*
        $url = "v1/tiposdocumentos";


        $header = array("Authorization: Bearer {$_SESSION['token']}");
        $this->oCurl->setUrl(APISSO);
        $this->oCurl->setFunction(get_class($this) . "-" . __FUNCTION__);
        $this->oCurl->setHeader($header);
        $this->oCurl->setDebug(false);
        if(!$this->oCurl->sendGet($url,$dataResult))
        {
            $this->setError("Error","Ocurrió un error al buscar los tipos de documentos");
            return false;
        }
        if (!$this->Utf8)
            $array = FuncionesPHPLocal::DecodificarUtf8($dataResult);
        else
            $array = $dataResult;
*/
        return $array;
    }

    public function ObtenerTiposDocumentosxId($datos)
    {

        $spnombre="sel_TiposDocumentos_xId";
        $sparam=array(
            "pId"=>$datos['IdTipoDocumento']
        );
        if(!$this->conexion->ejecutarStoredProcedure($spnombre,$sparam,$resultado,$numfilas,$errno) )
        {
            $this->setError("Error","Ocurrió un error al buscar el tipo de documento por Id");
            return false;
        }

        $array = [];
        if ($numfilas>0)
            $array = $this->conexion->ObtenerSiguienteRegistro($resultado);

        /*
        $url = "v1/tiposdocumentos/".$datos['IdTipoDocumento'];

        $header = array("Authorization: Bearer {$_SESSION['token']}");
        $this->oCurl->setUrl(APISSO);
        $this->oCurl->setFunction(get_class($this)."-".__FUNCTION__);
        $this->oCurl->setHeader($header);
        $this->oCurl->setDebug(false);

        if(!$this->oCurl->sendGet($url,$dataResult))
        {
            $this->setError("Error","Ocurrió un error al buscar el usuario por código");
            return false;
        }

        if (!$this->Utf8)
            $array = FuncionesPHPLocal::DecodificarUtf8($dataResult);
        else
            $array = $dataResult;*/
        return $array;
    }


}
