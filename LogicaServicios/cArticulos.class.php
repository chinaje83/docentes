<?php

class cArticulos
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

    public function BuscarCombo($datos, &$resultado, $completo = false)
    {
        if (isset($datos['IdMotivo']) && !empty($datos['IdMotivo'])) {
            $url = "combos/articulos/{$datos['IdMotivo']}";
        } else {
            $url = "combos/articulos";
        }


        $dataEnviar = new stdClass();
        if ($completo) {
            $dataEnviar->completo = true;
            $url .= '?';
        }

        $url .= http_build_query($dataEnviar);

        $header = array("Authorization: Bearer {$_SESSION['token']}");
        $this->oCurl->setHeader($header);
        $this->oCurl->setUrl(API_LICENCIAS);
        $this->oCurl->setFunction(get_class($this) . "-" . __FUNCTION__);
        $this->oCurl->setDebug(false);


        if (!$this->oCurl->sendGet($url, $dataResult)) {
            $this->setError("Error", "Ocurrió un error al buscar el listado de artículos");
            return false;
        }

        if (!$this->Utf8)
            $array = FuncionesPHPLocal::DecodificarUtf8($dataResult);
        else
            $array = $dataResult;

        $resultado = $array;

        return $array;
    }

    public function BusquedaAvanzada($datos, &$resultado, &$numfilas)
    {
        $url = "articulos?";

        $dataEnviar = new stdClass();

        if (!FuncionesPHPLocal::isEmpty($datos['IdMotivo']))
            $dataEnviar->IdMotivo = $datos['IdMotivo'];

        if (!FuncionesPHPLocal::isEmpty($datos['Codigo']))
            $dataEnviar->Codigo = $datos['Codigo'];

        if (!FuncionesPHPLocal::isEmpty($datos['CantidadMaximaDias']))
            $dataEnviar->CantidadMaximaDias = $datos['CantidadMaximaDias'];

        if (!FuncionesPHPLocal::isEmpty($datos['ConGoceSueldo']))
            $dataEnviar->ConGoceSueldo = $datos['ConGoceSueldo'];

        if (!FuncionesPHPLocal::isEmpty($datos['PermiteOtroOrganismo']))
            $dataEnviar->PermiteOtroOrganismo = $datos['PermiteOtroOrganismo'];

        if (!FuncionesPHPLocal::isEmpty($datos['EsAnual']))
            $dataEnviar->EsAnual = $datos['EsAnual'];

        if (!FuncionesPHPLocal::isEmpty($datos['EsAuxiliar']))
            $dataEnviar->EsAuxiliar = $datos['EsAuxiliar'];

        if (!FuncionesPHPLocal::isEmpty($datos['Estado']))
            $dataEnviar->Estado = $datos['Estado'];

        if (!FuncionesPHPLocal::isEmpty($datos['rows']))
            $dataEnviar->rows = $datos['rows'];

        if (!FuncionesPHPLocal::isEmpty($datos['page']))
            $dataEnviar->page = $datos['page'];

        if (!FuncionesPHPLocal::isEmpty($datos['sidx']))
            $dataEnviar->sidx = $datos['sidx'];

        if (!FuncionesPHPLocal::isEmpty($datos['sord']))
            $dataEnviar->sord = $datos['sord'];

        $cuerpo = http_build_query($dataEnviar);

        $header = array("Authorization: Bearer {$_SESSION['token']}");
        $this->oCurl->setHeader($header);
        $this->oCurl->setUrl(API_LICENCIAS.$url);
        $this->oCurl->setFunction(get_class($this) . "-" . __FUNCTION__);
        $this->oCurl->setDebug(false);

        if (!$this->oCurl->sendGet($cuerpo, $dataResult)) {
            $this->setError("Error", "Ocurrió un error al buscar el listado de artículos.");
            return false;
        }
        if (!$this->Utf8)
            $array = FuncionesPHPLocal::DecodificarUtf8($dataResult);
        else
            $array = $dataResult;

        $numfilas = count($array['filas']);
        $resultado = $array;

        return $array;
    }

    public function BuscarxCodigo($datos, &$resultado, &$numfilas)
    {
        $url = "articulos/".$datos['IdArticulo'];

        $header = array("Authorization: Bearer {$_SESSION['token']}");
        $this->oCurl->setHeader($header);
        $this->oCurl->setUrl(API_LICENCIAS);
        $this->oCurl->setFunction(get_class($this) . "-" . __FUNCTION__);
        $this->oCurl->setDebug(false);

        if (!$this->oCurl->sendGet($url, $dataResult)) {
            $this->setError("Error", "Ocurrió un error al buscar el artículo.");
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

        $url = 'articulos';
        $header = array("Content-Type: application/json", "Authorization: Bearer {$_SESSION['token']}");
        $this->oCurl->setHeader($header);
        $this->oCurl->setUrl(API_LICENCIAS.$url);
        $this->oCurl->setFunction(get_class($this)."-".__FUNCTION__);
        $this->oCurl->setHttpBuildPost(false);
        $this->oCurl->setDebug(false);

        $dataEnviar = new stdClass();

        if (!FuncionesPHPLocal::isEmpty($datos['IdMotivo']))
            $dataEnviar->IdMotivo = $datos['IdMotivo'];

        if (!FuncionesPHPLocal::isEmpty($datos['Codigo']))
            $dataEnviar->Codigo = $datos['Codigo'];

        if (!FuncionesPHPLocal::isEmpty($datos['Descripcion']))
            $dataEnviar->Descripcion = $datos['Descripcion'];

        if (!FuncionesPHPLocal::isEmpty($datos['CantidadMaximaDias']))
            $dataEnviar->CantidadMaximaDias = $datos['CantidadMaximaDias'];

        if (!FuncionesPHPLocal::isEmpty($datos['ConGoceSueldo']))
            $dataEnviar->ConGoceSueldo = $datos['ConGoceSueldo'];

        if (!FuncionesPHPLocal::isEmpty($datos['PermiteOtroOrganismo']))
            $dataEnviar->PermiteOtroOrganismo = $datos['PermiteOtroOrganismo'];

        if (!FuncionesPHPLocal::isEmpty($datos['EsAnual']))
            $dataEnviar->EsAnual = $datos['EsAnual'];

        if (!FuncionesPHPLocal::isEmpty($datos['EsAuxiliar']))
            $dataEnviar->EsAuxiliar = $datos['EsAuxiliar'];

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

    public function Modificar($datos)
    {
        if (!self::_ValidarModificar($datos))
            return false;

        $url = "articulos/".$datos['IdAutorizante'];
        $header = array("Content-Type: application/json", "Authorization: Bearer {$_SESSION['token']}");
        $this->oCurl->setHeader($header);
        $this->oCurl->setUrl(API_LICENCIAS.$url);
        $this->oCurl->setFunction(get_class($this)."-".__FUNCTION__);
        $this->oCurl->setHttpBuildPost(false);
        $this->oCurl->setDebug(false);

        $dataEnviar = new stdClass();

        if (!FuncionesPHPLocal::isEmpty($datos['IdArticulo']))
            $dataEnviar->IdArticulo = $datos['IdArticulo'];

        if (!FuncionesPHPLocal::isEmpty($datos['IdMotivo']))
            $dataEnviar->IdMotivo = $datos['IdMotivo'];

        if (!FuncionesPHPLocal::isEmpty($datos['Codigo']))
            $dataEnviar->Codigo = $datos['Codigo'];

        if (!FuncionesPHPLocal::isEmpty($datos['Descripcion']))
            $dataEnviar->Descripcion = $datos['Descripcion'];

        if (!FuncionesPHPLocal::isEmpty($datos['CantidadMaximaDias']))
            $dataEnviar->CantidadMaximaDias = $datos['CantidadMaximaDias'];

        if (!FuncionesPHPLocal::isEmpty($datos['ConGoceSueldo']))
            $dataEnviar->ConGoceSueldo = $datos['ConGoceSueldo'];

        if (!FuncionesPHPLocal::isEmpty($datos['PermiteOtroOrganismo']))
            $dataEnviar->PermiteOtroOrganismo = $datos['PermiteOtroOrganismo'];

        if (!FuncionesPHPLocal::isEmpty($datos['EsAnual']))
            $dataEnviar->EsAnual = $datos['EsAnual'];

        if (!FuncionesPHPLocal::isEmpty($datos['EsAuxiliar']))
            $dataEnviar->EsAuxiliar = $datos['EsAuxiliar'];

        $cuerpo = json_encode($dataEnviar);

        if (!$this->oCurl->sendPut($cuerpo,$dataResult)) {
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

    public function Activar(array $datos): bool
    {
        $url = 'articulo/'.$datos['IdArticulo'];
        $header = array("Content-Type: application/json", "Authorization: Bearer {$_SESSION['token']}");
        $this->oCurl->setHeader($header);
        $this->oCurl->setUrl(API_LICENCIAS.$url);
        $this->oCurl->setFunction(get_class($this)."-".__FUNCTION__);
        $this->oCurl->setHttpBuildPost(false);
        $this->oCurl->setDebug(false);

        $dataEnviar = new stdClass();

        if (!FuncionesPHPLocal::isEmpty($datos['IdArticulo']))
            $dataEnviar->IdArticulo = $datos['IdArticulo'];

        $dataEnviar->Estado = ACTIVO;

        $cuerpo = json_encode($dataEnviar);

        if (!$this->oCurl->sendPut($cuerpo,$dataResult)) {
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

        return true;
    }


    public function Desactivar(array $datos): bool
    {
        $url = 'articulo/'.$datos['IdArticulo'];
        $header = array("Content-Type: application/json", "Authorization: Bearer {$_SESSION['token']}");
        $this->oCurl->setHeader($header);
        $this->oCurl->setUrl(API_LICENCIAS.$url);
        $this->oCurl->setFunction(get_class($this)."-".__FUNCTION__);
        $this->oCurl->setHttpBuildPost(false);
        $this->oCurl->setDebug(false);

        $dataEnviar = new stdClass();

        if (!FuncionesPHPLocal::isEmpty($datos['IdArticulo']))
            $dataEnviar->IdArticulo = $datos['IdArticulo'];

        $dataEnviar->Estado = NOACTIVO;

        $cuerpo = json_encode($dataEnviar);

        if (!$this->oCurl->sendPut($cuerpo,$dataResult)) {
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

        return true;
    }

    private function _ValidarInsertar($datos): bool {

        if (!self::_ValidarDatosVacios($datos))
            return false;

        return true;
    }

    private function _ValidarModificar($datos): bool {

        if (!self::_ValidarDatosVacios($datos))
            return false;

        return true;
    }

    private function _ValidarDatosVacios($datos): bool {

        if (FuncionesPHPLocal::isEmpty($datos['Codigo'])) {
            $this->setError(400, 'Debe ingresar un código');
            return false;
        }

        if (FuncionesPHPLocal::isEmpty($datos['ConGoceSueldo'])) {
            $this->setError(400, 'Debe seleccionar una opción para "Goce de Sueldo"');
            return false;
        }

        if (FuncionesPHPLocal::isEmpty($datos['PermiteOtroOrganismo'])) {
            $this->setError(400, 'Debe seleccionar una opción para "Permite otro organismo"');
            return false;
        }

        if (FuncionesPHPLocal::isEmpty($datos['EsAnual'])) {
            $this->setError(400, 'Debe seleccionar una opción para "Anual"');
            return false;
        }

        if (FuncionesPHPLocal::isEmpty($datos['EsAuxiliar'])) {
            $this->setError(400, 'Debe seleccionar una opción para "Auxiliar"');
            return false;
        }

        return true;
    }

}


