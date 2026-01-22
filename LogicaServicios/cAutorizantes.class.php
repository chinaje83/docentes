<?php

use Bigtree\ExcepcionLogica;

class cAutorizantes
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

    public function BusquedaAvanzada($datos, &$resultado, &$numfilas)
    {
        $url = "medicos?";

        $dataEnviar = new stdClass();

        if (!FuncionesPHPLocal::isEmpty($datos['Id']))
            $dataEnviar->Id = $datos['Id'];

        if (!FuncionesPHPLocal::isEmpty($datos['IdTipoAutorizante']))
            $dataEnviar->IdTipoAutorizante = $datos['IdTipoAutorizante'];

        if (!FuncionesPHPLocal::isEmpty($datos['Nombre']))
            $dataEnviar->Nombre = $datos['Nombre'];

        if (!FuncionesPHPLocal::isEmpty($datos['Documento']))
            $dataEnviar->Documento = $datos['Documento'];

        if (!FuncionesPHPLocal::isEmpty($datos['IdTipoDocumento']))
            $dataEnviar->IdTipoDocumento = $datos['IdTipoDocumento'];

        if (!FuncionesPHPLocal::isEmpty($datos['Matricula']))
            $dataEnviar->Matricula = $datos['Matricula'];

        if (!FuncionesPHPLocal::isEmpty($datos['VencimientoMatricula']))
            $dataEnviar->VencimientoMatricula = $datos['VencimientoMatricula'];

        if (!FuncionesPHPLocal::isEmpty($datos['FechaRematricu']))
            $dataEnviar->FechaRematricu = $datos['FechaRematricu'];

        if (!FuncionesPHPLocal::isEmpty($datos['ObjetorDeConciencia']))
            $dataEnviar->ObjetorDeConciencia = $datos['ObjetorDeConciencia'];

        if (!FuncionesPHPLocal::isEmpty($datos['DispoBaja']))
            $dataEnviar->DispoBaja = $datos['DispoBaja'];

        if (!FuncionesPHPLocal::isEmpty($datos['FechaBaja']))
            $dataEnviar->FechaBaja = $datos['FechaBaja'];

        if (!FuncionesPHPLocal::isEmpty($datos['IdEspecialidad']))
            $dataEnviar->IdEspecialidad = $datos['IdEspecialidad'];

        if (!FuncionesPHPLocal::isEmpty($datos['EstadoBusqueda']))
            $dataEnviar->EstadoBusqueda = $datos['EstadoBusqueda'];

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
            $this->setError("Error", utf8_decode("Ocurrió un error al buscar el listado de autorizantes."));
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
        $url = "medicos/".$datos['IdAutorizante'];

        $header = array("Authorization: Bearer {$_SESSION['token']}");
        $this->oCurl->setHeader($header);
        $this->oCurl->setUrl(API_LICENCIAS);
        $this->oCurl->setFunction(get_class($this) . "-" . __FUNCTION__);
        $this->oCurl->setDebug(false);

        if (!$this->oCurl->sendGet($url, $dataResult)) {
            $this->setError("Error", utf8_decode("Ocurrió un error al buscar el autorizante."));
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

    public function autocompletarNombre($datos, &$resultado, &$numfilas) {

        $url = "medicos/busqueda/".$datos['Cadena'];

        $header = array("Authorization: Bearer {$_SESSION['token']}");
        $this->oCurl->setHeader($header);
        $this->oCurl->setUrl(API_LICENCIAS);
        $this->oCurl->setFunction(get_class($this) . "-" . __FUNCTION__);
        $this->oCurl->setDebug(false);

        if (!$this->oCurl->sendGet($url, $dataResult)) {
            $this->setError("Error", utf8_decode("Ocurrió un error al buscar el autorizante."));
            return false;
        }

        $array = $dataResult;

        $numfilas = $dataResult['total'];
        $resultado = $array;

        return $array;
    }

    public function Insertar($datos, &$codigoInsertado)
    {
        if (!self::_ValidarInsertar($datos))
            return false;

        $url = "medicos";
        $header = array("Content-Type: application/json", "Authorization: Bearer {$_SESSION['token']}");
        $this->oCurl->setHeader($header);
        $this->oCurl->setUrl(API_LICENCIAS.$url);
        $this->oCurl->setFunction(get_class($this)."-".__FUNCTION__);
        $this->oCurl->setHttpBuildPost(false);
        $this->oCurl->setDebug(false);

        $dataEnviar = new stdClass();

        $dataEnviar->IdTipoAutorizante = (int)$datos['IdTipoAutorizante'];

        if (!FuncionesPHPLocal::isEmpty($datos['Nombre']))
            $dataEnviar->Nombre = utf8_encode($datos['Nombre']);

        if (!FuncionesPHPLocal::isEmpty($datos['Documento']))
            $dataEnviar->Documento = $datos['Documento'];

        if (!FuncionesPHPLocal::isEmpty($datos['IdTipoDocumento']))
            $dataEnviar->IdTipoDocumento = $datos['IdTipoDocumento'];

        if (!FuncionesPHPLocal::isEmpty($datos['Matricula']))
            $dataEnviar->Matricula = $datos['Matricula'];

        if (!FuncionesPHPLocal::isEmpty($datos['VencimientoMatricula']))
            $dataEnviar->VencimientoMatricula = $datos['VencimientoMatricula'];

        if (!FuncionesPHPLocal::isEmpty($datos['FechaRematricu']))
            $dataEnviar->FechaRematricu = $datos['FechaRematricu'];

        if (!FuncionesPHPLocal::isEmpty($datos['ObjetorDeConciencia']))
            $dataEnviar->ObjetorDeConciencia = $datos['ObjetorDeConciencia'];

        if (!FuncionesPHPLocal::isEmpty($datos['DispoBaja']))
            $dataEnviar->DispoBaja = $datos['DispoBaja'];

        if (!FuncionesPHPLocal::isEmpty($datos['FechaBaja']))
            $dataEnviar->FechaBaja = $datos['FechaBaja'];

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

        $url = "medicos/".$datos['IdAutorizante'];
        $header = array("Content-Type: application/json", "Authorization: Bearer {$_SESSION['token']}");
        $this->oCurl->setHeader($header);
        $this->oCurl->setUrl(API_LICENCIAS.$url);
        $this->oCurl->setFunction(get_class($this)."-".__FUNCTION__);
        $this->oCurl->setHttpBuildPost(false);
        $this->oCurl->setDebug(false);

        $dataEnviar = new stdClass();

        if (!FuncionesPHPLocal::isEmpty($datos['IdAutorizante']))
            $dataEnviar->Id = (int)$datos['IdAutorizante'];

        $dataEnviar->IdTipoAutorizante = (int)$datos['IdTipoAutorizante'];

        if (!FuncionesPHPLocal::isEmpty($datos['Nombre']))
            $dataEnviar->Nombre = utf8_encode($datos['Nombre']);

        if (!FuncionesPHPLocal::isEmpty($datos['Documento']))
            $dataEnviar->Documento = $datos['Documento'];

        if (!FuncionesPHPLocal::isEmpty($datos['IdTipoDocumento']))
            $dataEnviar->IdTipoDocumento = (int)$datos['IdTipoDocumento'];

        if (!FuncionesPHPLocal::isEmpty($datos['Matricula']))
            $dataEnviar->Matricula = $datos['Matricula'];

        if (!FuncionesPHPLocal::isEmpty($datos['VencimientoMatricula']))
            $dataEnviar->VencimientoMatricula = $datos['VencimientoMatricula'];

        if (!FuncionesPHPLocal::isEmpty($datos['FechaRematricu']))
            $dataEnviar->FechaRematricu = $datos['FechaRematricu'];

        if (!FuncionesPHPLocal::isEmpty($datos['ObjetorDeConciencia']))
            $dataEnviar->ObjetorDeConciencia = $datos['ObjetorDeConciencia'];

        if (!FuncionesPHPLocal::isEmpty($datos['DispoBaja']))
            $dataEnviar->DispoBaja = $datos['DispoBaja'];

        if (!FuncionesPHPLocal::isEmpty($datos['FechaBaja']))
            $dataEnviar->FechaBaja = $datos['FechaBaja'];

        //print_r($dataEnviar);die;
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

    public function Eliminar($datos)
    {
        if (!self::_ValidarEliminar($datos))
            return false;

        $url = "medicos";

        $header = array("Content-Type: application/json", "Authorization: Bearer {$_SESSION['token']}");
        $this->oCurl->setHeader($header);
        $this->oCurl->setUrl(API_LICENCIAS.$url);
        $this->oCurl->setFunction(get_class($this)."-".__FUNCTION__);
        $this->oCurl->setDebug(false);

        $dataEnviar = new stdClass();

        if (!FuncionesPHPLocal::isEmpty($datos['IdAutorizante']))
            $dataEnviar->Id = $datos['IdAutorizante'];

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


    public function Activar(array $datos): bool
    {
        $url = "medicos/".$datos['IdAutorizante'];
        $header = array("Content-Type: application/json", "Authorization: Bearer {$_SESSION['token']}");
        $this->oCurl->setHeader($header);
        $this->oCurl->setUrl(API_LICENCIAS.$url);
        $this->oCurl->setFunction(get_class($this)."-".__FUNCTION__);
        $this->oCurl->setHttpBuildPost(false);
        $this->oCurl->setDebug(false);

        $dataEnviar = new stdClass();

        if (!FuncionesPHPLocal::isEmpty($datos['IdAutorizante']))
            $dataEnviar->Id = $datos['IdAutorizante'];

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
        $url = "medicos/".$datos['IdAutorizante'];
        $header = array("Content-Type: application/json", "Authorization: Bearer {$_SESSION['token']}");
        $this->oCurl->setHeader($header);
        $this->oCurl->setUrl(API_LICENCIAS.$url);
        $this->oCurl->setFunction(get_class($this)."-".__FUNCTION__);
        $this->oCurl->setHttpBuildPost(false);
        $this->oCurl->setDebug(false);

        $dataEnviar = new stdClass();

        if (!FuncionesPHPLocal::isEmpty($datos['IdAutorizante']))
            $dataEnviar->Id = $datos['IdAutorizante'];

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
    
    
    public static function preprocesarDatosElastic(array $datos): array {
	    $datos['Tabla'] = 'Autorizantes';
	    $datos['Identificadores'] = [$datos['Documento'], $datos['Matricula']];
		return $datos;
    }


    private function _ValidarInsertar($datos): bool {

        if (!self::_ValidarDatosVacios($datos))
            return false;

        return true;
    }

    private function _ValidarModificar($datos): bool {

        if (FuncionesPHPLocal::isEmpty($datos['IdAutorizante'])) {
            $this->setError(400, 'Error, registro inexistente.');
            return false;
        }

        if (!self::_ValidarDatosVacios($datos))
            return false;

        return true;
    }

    private function _ValidarEliminar($datos): bool {

        if (FuncionesPHPLocal::isEmpty($datos['IdAutorizante'])) {
            $this->setError(400, 'Error, registro inexistente.');
            return false;
        }

        return true;
    }

    private function _ValidarDatosVacios($datos): bool {

        if (FuncionesPHPLocal::isEmpty($datos['Nombre'])) {
            $this->setError(400, 'Error, debe ingresar un nombre');
            return false;
        }

        if (FuncionesPHPLocal::isEmpty($datos['IdTipoAutorizante'])) {
            $this->setError(400, 'Error, debe seleccionar un tipo de autorizante');
            return false;
        }

        /*if (FuncionesPHPLocal::isEmpty($datos['IdTipoDocumento'])) {
            $this->setError(400, 'Error, debe seleccionar un tipo de documento');
            return false;
        }

        if (FuncionesPHPLocal::isEmpty($datos['Documento'])) {
            $this->setError(400, 'Error, debe ingresar un número de documento');
            return false;
        }*/

        if (FuncionesPHPLocal::isEmpty($datos['Matricula'])) {
            $this->setError(400, 'Error, debe ingresar un número de matrícula');
            return false;
        }

        if (FuncionesPHPLocal::isEmpty($datos['ObjetorDeConciencia'])) {
            $this->setError(400, 'Error, debe seleccionar objetor de conciencia');
            return false;
        }

        return true;
    }

    /**
     * @return array
     * @throws ExcepcionLogica
     */
    public function obtenerTiposAutorizantes(): array {

        $url = 'combos/autorizantes-tipos';
        $header = ["Authorization: Bearer {$_SESSION['token']}"];
        $this->oCurl->setHeader($header);
        $this->oCurl->setUrl(API_LICENCIAS . $url);
        $this->oCurl->setFunction(get_class($this) . '-' . __FUNCTION__);
        $this->oCurl->setHttpBuildPost(false);
        $this->oCurl->setDebug(false);

        if (!$this->oCurl->sendGet('',$dataResult)) {
            throw new ExcepcionLogica('Error al buscar tipos de autorizante');
        }

        return FuncionesPHPLocal::DecodificarUtf8($dataResult['hits']);

    }
}


